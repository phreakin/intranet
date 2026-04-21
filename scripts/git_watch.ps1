# ============================================
# GIT WATCHER (FIXED + STABLE)
# ============================================

$repoPath = Get-Location

Write-Host "=== WATCHING FOR FILE CHANGES ==="

$watcher = New-Object System.IO.FileSystemWatcher
$watcher.Path = $repoPath
$watcher.IncludeSubdirectories = $true
$watcher.EnableRaisingEvents = $true

# Prevent spam commits (cooldown)
$global:lastRun = Get-Date

$action = {
    $now = Get-Date

    # Throttle commits (5 sec cooldown)
    if (($now - $global:lastRun).TotalSeconds -lt 5) {
        return
    }

    $global:lastRun = $now

    Write-Host "Change detected..."

    Start-Sleep -Seconds 2

    git add .

    $status = git status --porcelain

    if ($status) {
        $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
        $msg = "Auto Commit [$timestamp]"

        git commit -m "$msg"
        git push

        Write-Host "[OK] Auto committed and pushed"
    }
}

Register-ObjectEvent $watcher Changed -Action $action | Out-Null
Register-ObjectEvent $watcher Created -Action $action | Out-Null
Register-ObjectEvent $watcher Deleted -Action $action | Out-Null
Register-ObjectEvent $watcher Renamed -Action $action | Out-Null

while ($true) {
    Start-Sleep 5
}