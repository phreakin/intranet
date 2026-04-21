param(
    [string]$Branch = "",
    [string]$Remote = "origin",
    [string]$CommitMessage = "",
    [switch]$SkipValidation,
    [switch]$SkipPull,
    [switch]$SkipPush,
    [switch]$DryRun,
    [switch]$VerboseOutput,
    [switch]$AllowPushFailure
)

$ErrorActionPreference = "Stop"
Set-StrictMode -Version Latest

$repoRoot = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path
Set-Location $repoRoot

function Write-Section {
    param([string]$Message)

    Write-Host ""
    Write-Host "=== $Message ===" -ForegroundColor Cyan
}

function Write-Info {
    param([string]$Message)

    Write-Host "[INFO] $Message" -ForegroundColor Gray
}

function Write-Success {
    param([string]$Message)

    Write-Host "[OK] $Message" -ForegroundColor Green
}

function Write-WarnMsg {
    param([string]$Message)

    Write-Host "[WARN] $Message" -ForegroundColor Yellow
}

function Write-ErrMsg {
    param([string]$Message)

    Write-Host "[ERROR] $Message" -ForegroundColor Red
}

function Invoke-Git {
    param(
        [Parameter(Mandatory = $true)]
        [string[]]$Arguments,
        [switch]$AllowFailure
    )

    $display = $Arguments -join " "

    if ($DryRun) {
        Write-Host "[DRY-RUN] git $display" -ForegroundColor Magenta
        return @{
            Output   = ""
            ExitCode = 0
        }
    }

    if ($VerboseOutput) {
        Write-Info "Running: git $display"
    }

    $output = & git @Arguments 2>&1
    $exitCode = $LASTEXITCODE
    $text = ($output | Out-String).Trim()

    if (-not $AllowFailure -and $exitCode -ne 0) {
        throw "Git command failed: git $display`n$text"
    }

    return @{
        Output   = $text
        ExitCode = $exitCode
    }
}

function Test-GitRepo {
    if (!(Test-Path -LiteralPath (Join-Path $repoRoot ".git"))) {
        throw "Current directory is not a git repository."
    }
}

function Get-CurrentBranch {
    $output = & git rev-parse --abbrev-ref HEAD 2>&1
    $exitCode = $LASTEXITCODE
    $current = ($output | Out-String).Trim()

    if ($exitCode -ne 0 -or [string]::IsNullOrWhiteSpace($current)) {
        throw "Unable to determine current git branch."
    }

    return $current
}

function Get-ChangedFiles {
    $result = Invoke-Git -Arguments @("status", "--porcelain")
    if ([string]::IsNullOrWhiteSpace($result.Output)) {
        return ,@()
    }

    $lines = @($result.Output -split "`r?`n" | Where-Object { -not [string]::IsNullOrWhiteSpace($_) })
    return ,$lines
}

function Has-WorkingTreeChanges {
    $changedFiles = @(Get-ChangedFiles)
    return $changedFiles.Count -gt 0
}

function Run-ValidationIfPresent {
    if ($SkipValidation) {
        Write-WarnMsg "Validation skipped by option."
        return
    }

    $validateScript = Join-Path $PSScriptRoot "validate.ps1"

    if (Test-Path -LiteralPath $validateScript) {
        Write-Section "Running Validation"

        if ($DryRun) {
            Write-Host "[DRY-RUN] powershell -NoProfile -ExecutionPolicy Bypass -File `"$validateScript`"" -ForegroundColor Magenta
            return
        }

        & powershell -NoProfile -ExecutionPolicy Bypass -File $validateScript
        if ($LASTEXITCODE -ne 0) {
            throw "Validation script failed."
        }

        Write-Success "Validation completed."
    }
    else {
        Write-WarnMsg "No validate.ps1 script found. Continuing."
    }
}

function Build-CommitMessage {
    param(
        [string]$ManualMessage,
        [array]$ChangedFiles
    )

    if (-not [string]::IsNullOrWhiteSpace($ManualMessage)) {
        return $ManualMessage.Trim()
    }

    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $count = $ChangedFiles.Count

    $topFiles = @(
        $ChangedFiles |
            ForEach-Object {
                if ($_.Length -ge 4) {
                    $_.Substring(3).Trim()
                }
                else {
                    $_.Trim()
                }
            } |
            Select-Object -First 5
    )

    $fileSummary = if ($topFiles.Count -gt 0) {
        $topFiles -join ", "
    }
    else {
        "repo updates"
    }

    return "Auto Sync [$timestamp] - $count file(s) updated - $fileSummary"
}

function Show-ChangedFiles {
    param([array]$ChangedFiles)

    if ($ChangedFiles.Count -eq 0) {
        Write-Info "No local changes detected."
        return
    }

    Write-Info "Changed files:"
    foreach ($line in $ChangedFiles) {
        Write-Host "  $line"
    }
}

function Get-AheadBehind {
    param(
        [string]$RemoteName,
        [string]$BranchName
    )

    $result = Invoke-Git -Arguments @("rev-list", "--left-right", "--count", "$RemoteName/$BranchName...HEAD")
    if ([string]::IsNullOrWhiteSpace($result.Output)) {
        return @{
            Behind = 0
            Ahead  = 0
        }
    }

    $parts = $result.Output -split "\s+"
    if ($parts.Count -lt 2) {
        return @{
            Behind = 0
            Ahead  = 0
        }
    }

    return @{
        Behind = [int]$parts[0]
        Ahead  = [int]$parts[1]
    }
}

try {
    Write-Section "Git Sync Start"

    Test-GitRepo

    if ([string]::IsNullOrWhiteSpace($Branch)) {
        $Branch = Get-CurrentBranch
    }

    Write-Info "Repository: $repoRoot"
    Write-Info "Remote: $Remote"
    Write-Info "Branch: $Branch"

    Run-ValidationIfPresent

    if (Has-WorkingTreeChanges) {
        Write-Section "Stage All Changes"
        Invoke-Git -Arguments @("add", ".") | Out-Null
        Write-Success "All local changes staged with git add ."

        $changedFiles = Get-ChangedFiles
        Show-ChangedFiles -ChangedFiles $changedFiles

        if ($changedFiles.Count -gt 0) {
            Write-Section "Commit Changes"

            $finalCommitMessage = Build-CommitMessage -ManualMessage $CommitMessage -ChangedFiles $changedFiles
            Write-Info "Commit message:"
            Write-Host "  $finalCommitMessage" -ForegroundColor White

            Invoke-Git -Arguments @("commit", "-m", $finalCommitMessage) | Out-Null
            Write-Success "Commit created."
        }
        else {
            Write-WarnMsg "No staged changes were detected after git add ."
        }
    }
    else {
        Write-Info "No local changes detected. Skipping stage and commit."
    }

    Write-Section "Fetch Remote"
    Invoke-Git -Arguments @("fetch", $Remote) | Out-Null
    Write-Success "Fetch complete."

    $aheadBehind = Get-AheadBehind -RemoteName $Remote -BranchName $Branch
    Write-Info "Remote status -> Behind: $($aheadBehind.Behind), Ahead: $($aheadBehind.Ahead)"

    if (-not $SkipPull -and $aheadBehind.Behind -gt 0) {
        Write-Section "Pull Remote Changes"
        Write-WarnMsg "Local branch is behind by $($aheadBehind.Behind) commit(s). Pulling with rebase."

        $pullResult = Invoke-Git -Arguments @("pull", "--rebase", $Remote, $Branch) -AllowFailure
        if ($pullResult.ExitCode -ne 0) {
            throw "Pull failed:`n$($pullResult.Output)"
        }

        Write-Success "Pull with rebase completed."
    }
    elseif ($SkipPull) {
        Write-WarnMsg "Pull skipped by option."
    }
    else {
        Write-Info "No pull needed."
    }

    $aheadBehind = Get-AheadBehind -RemoteName $Remote -BranchName $Branch
    Write-Info "Post-pull remote status -> Behind: $($aheadBehind.Behind), Ahead: $($aheadBehind.Ahead)"

    if (-not $SkipPush -and $aheadBehind.Ahead -gt 0) {
        Write-Section "Push Changes"

        $pushResult = Invoke-Git -Arguments @("push", $Remote, $Branch) -AllowFailure

        if ($pushResult.ExitCode -ne 0) {
            Write-ErrMsg "Push failed."
            if (-not [string]::IsNullOrWhiteSpace($pushResult.Output)) {
                Write-Host $pushResult.Output -ForegroundColor Red
            }

            if (-not $AllowPushFailure) {
                throw "Push failed."
            }
        }
        else {
            Write-Success "Push successful."
        }
    }
    elseif ($SkipPush) {
        Write-WarnMsg "Push skipped by option."
    }
    else {
        Write-Info "No push needed."
    }

    Write-Section "Git Sync Complete"
}
catch {
    Write-ErrMsg $_.Exception.Message
    Write-WarnMsg "Sync stopped before completion."

    if (-not $DryRun) {
        Write-Host ""
        Write-Host "Suggested next steps:" -ForegroundColor Yellow
        Write-Host "  1. Run: git status"
        Write-Host "  2. Run: git remote -v"
        Write-Host "  3. Run: git push origin main"
        Write-Host "  4. If auth failed, refresh GitHub credentials/token"
        Write-Host "  5. If branch is protected, push to a feature branch instead"
    }

    exit 1
}
