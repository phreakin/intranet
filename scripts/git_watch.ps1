. (Join-Path $PSScriptRoot "common.ps1")

param(
    [int]$IntervalSeconds = 10,
    [int]$CooldownSeconds = 30,
    [switch]$SkipValidation,
    [switch]$SkipPull,
    [switch]$SkipPush
)

$ErrorActionPreference = "Stop"
Enter-RepoRoot

if (!(Test-CommandExists "git")) {
    Write-ErrorMsg "git is not available on PATH."
    exit 1
}

Write-Section "Git Watch"
Write-Info "Polling git status every $IntervalSeconds second(s)."

$lastSignature = ""
$lastSyncAt = [datetime]::MinValue

while ($true) {
    $statusLines = @((& git status --porcelain --untracked-files=all) | Where-Object {
            -not [string]::IsNullOrWhiteSpace($_)
        })

    if ($LASTEXITCODE -ne 0) {
        Write-WarnMsg "git status failed. Retrying on next cycle."
        Start-Sleep -Seconds $IntervalSeconds
        continue
    }

    $signature = ($statusLines -join "`n").Trim()

    if (-not [string]::IsNullOrWhiteSpace($signature)) {
        $secondsSinceSync = (New-TimeSpan -Start $lastSyncAt -End (Get-Date)).TotalSeconds

        if ($signature -ne $lastSignature -and $secondsSinceSync -ge $CooldownSeconds) {
            Write-Info "Detected repository changes. Running git_sync.ps1."

            $syncArgs = @()
            if ($SkipValidation) {
                $syncArgs += "-SkipValidation"
            }
            if ($SkipPull) {
                $syncArgs += "-SkipPull"
            }
            if ($SkipPush) {
                $syncArgs += "-SkipPush"
            }

            try {
                Invoke-RepoScript -ScriptName "git_sync.ps1" -Arguments $syncArgs
                $lastSyncAt = Get-Date
                $lastSignature = ""
            }
            catch {
                Write-WarnMsg $_.Exception.Message
                $lastSignature = $signature
            }
        }
        else {
            $lastSignature = $signature
        }
    }
    else {
        $lastSignature = ""
    }

    Start-Sleep -Seconds $IntervalSeconds
}
