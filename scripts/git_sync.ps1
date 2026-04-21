param(
    [string]$Branch = "",
    [string]$Remote = "origin",
    [string]$CommitMessage = "",
    [switch]$SkipValidation,
    [switch]$SkipPull,
    [switch]$SkipPush,
    [switch]$DryRun,
    [switch]$VerboseOutput
)

$ErrorActionPreference = "Stop"

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
        [string]$Arguments,
        [switch]$AllowFailure
    )

    if ($DryRun) {
        Write-Host "[DRY-RUN] git $Arguments" -ForegroundColor Magenta
        return ""
    }

    if ($VerboseOutput) {
        Write-Info "Running: git $Arguments"
    }

    $output = & git $Arguments.Split(" ") 2>&1
    $exitCode = $LASTEXITCODE

    if (-not $AllowFailure -and $exitCode -ne 0) {
        throw "Git command failed: git $Arguments`n$output"
    }

    return ($output | Out-String).Trim()
}

function Test-GitRepo {
    if (!(Test-Path ".git")) {
        throw "Current directory is not a git repository."
    }
}

function Get-CurrentBranch {
    $current = Invoke-Git -Arguments "rev-parse --abbrev-ref HEAD"
    if ([string]::IsNullOrWhiteSpace($current)) {
        throw "Unable to determine current git branch."
    }
    return $current.Trim()
}

function Get-ChangedFiles {
    $status = Invoke-Git -Arguments "status --porcelain"
    if ([string]::IsNullOrWhiteSpace($status)) {
        return @()
    }

    return ($status -split "`r?`n" | Where-Object { -not [string]::IsNullOrWhiteSpace($_) })
}

function Run-ValidationIfPresent {
    if ($SkipValidation) {
        Write-WarnMsg "Validation skipped by option."
        return
    }

    $validateScript = Join-Path $PSScriptRoot "validate.ps1"

    if (Test-Path $validateScript) {
        Write-Section "Running Validation"

        if ($DryRun) {
            Write-Host "[DRY-RUN] powershell -ExecutionPolicy Bypass -File `"$validateScript`"" -ForegroundColor Magenta
            return
        }

        & powershell -ExecutionPolicy Bypass -File $validateScript
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

    $topFiles = $ChangedFiles |
            ForEach-Object {
                if ($_.Length -ge 4) { $_.Substring(3).Trim() } else { $_.Trim() }
            } |
            Select-Object -First 5

    $fileSummary = if ($topFiles.Count -gt 0) {
        ($topFiles -join ", ")
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

    $result = Invoke-Git -Arguments "rev-list --left-right --count $RemoteName/$BranchName...HEAD"
    if ([string]::IsNullOrWhiteSpace($result)) {
        return @{
            Behind = 0
            Ahead  = 0
        }
    }

    $parts = $result -split "\s+"
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

    Write-Info "Repository: $(Get-Location)"
    Write-Info "Remote: $Remote"
    Write-Info "Branch: $Branch"

    Run-ValidationIfPresent

    Write-Section "Stage All Changes"
    Invoke-Git -Arguments "add ."
    Write-Success "All local changes staged with git add ."

    $changedFiles = Get-ChangedFiles
    Show-ChangedFiles -ChangedFiles $changedFiles

    if ($changedFiles.Count -gt 0) {
        Write-Section "Commit Changes"

        $finalCommitMessage = Build-CommitMessage -ManualMessage $CommitMessage -ChangedFiles $changedFiles
        Write-Info "Commit message:"
        Write-Host "  $finalCommitMessage" -ForegroundColor White

        Invoke-Git -Arguments "commit -m `"$finalCommitMessage`""
        Write-Success "Commit created."
    }
    else {
        Write-WarnMsg "Nothing new to commit."
    }

    Write-Section "Fetch Remote"
    Invoke-Git -Arguments "fetch $Remote"
    Write-Success "Fetch complete."

    $aheadBehind = Get-AheadBehind -RemoteName $Remote -BranchName $Branch
    Write-Info "Remote status -> Behind: $($aheadBehind.Behind), Ahead: $($aheadBehind.Ahead)"

    if (-not $SkipPull -and $aheadBehind.Behind -gt 0) {
        Write-Section "Pull Remote Changes"
        Write-WarnMsg "Local branch is behind by $($aheadBehind.Behind) commit(s). Pulling with rebase."

        Invoke-Git -Arguments "pull $Remote $Branch --rebase"
        Write-Success "Pull with rebase completed."
    }
    elseif ($SkipPull) {
        Write-WarnMsg "Pull skipped by option."
    }
    else {
        Write-Info "No pull needed."
    }

    if (-not $SkipPush) {
        Write-Section "Push Changes"
        Invoke-Git -Arguments "push $Remote $Branch"
        Write-Success "Push successful."
    }
    else {
        Write-WarnMsg "Push skipped by option."
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
        Write-Host "  2. If rebase conflict happened: resolve files, then run git rebase --continue"
        Write-Host "  3. Re-run this script after conflicts are resolved"
    }

    exit 1
}