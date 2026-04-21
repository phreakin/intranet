Write-Host "=== WSL2 PERFORMANCE OPTIMIZATION ===" -ForegroundColor Cyan

# ------------------------------------------------------------
# 1. APPLY WSL2 PERFORMANCE CONFIG (.wslconfig)
# ------------------------------------------------------------

Write-Host "`n[1/5] Applying WSL2 performance settings..." -ForegroundColor Yellow

$wslConfig = @"
[wsl2]
memory=8GB
processors=4
swap=0
localhostForwarding=true
nestedVirtualization=true
"@

$wslConfigPath = "$env:USERPROFILE\.wslconfig"
$wslConfig | Out-File -FilePath $wslConfigPath -Encoding ASCII

Write-Host "WSL2 config written to $wslConfigPath" -ForegroundColor Green
Write-Host "  • memory = 8GB"
Write-Host "  • processors = 4"
Write-Host "  • swap = 0"
Write-Host "  • localhostForwarding = true"
Write-Host "  • nestedVirtualization = true"


# ------------------------------------------------------------
# 2. SHUT DOWN WSL COMPLETELY
# ------------------------------------------------------------

Write-Host "`n[2/5] Shutting down WSL..." -ForegroundColor Yellow
wsl --shutdown 2>$null
Start-Sleep -Seconds 2
Write-Host "WSL stopped." -ForegroundColor Green


# ------------------------------------------------------------
# 3. OPTIMIZE WSL VIRTUAL DISKS (COMPACT VHDX)
# ------------------------------------------------------------

Write-Host "`n[3/5] Optimizing WSL2 virtual disks..." -ForegroundColor Yellow

$distros = wsl --list --quiet

foreach ($d in $distros) {
    Write-Host "Optimizing: $d" -ForegroundColor Cyan
    try {
        wsl --terminate $d 2>$null
        wsl --shutdown 2>$null
        Optimize-VHD -Path "$env:LOCALAPPDATA\Packages\*\LocalState\ext4.vhdx" -Mode Full -ErrorAction SilentlyContinue
    } catch {
        Write-Host "Skipping: $d (no VHDX found or access denied)" -ForegroundColor DarkYellow
    }
}

Write-Host "VHDX optimization complete." -ForegroundColor Green


# ------------------------------------------------------------
# 4. ENABLE WSL2 FAST I/O + DISK PERFORMANCE
# ------------------------------------------------------------

Write-Host "`n[4/5] Enabling WSL2 fast I/O tweaks..." -ForegroundColor Yellow

# Disable NTFS last-access time (huge perf boost)
fsutil behavior set disablelastaccess 1 | Out-Null

# Enable Windows I/O priority for WSL
reg add "HKLM\SYSTEM\CurrentControlSet\Services\HvHost\Parameters" /v EnableIdleYield /t REG_DWORD /d 1 /f | Out-Null

# Enable WSL2 I/O coalescing
reg add "HKLM\SYSTEM\CurrentControlSet\Services\wsl\Parameters" /v IoRingEnabled /t REG_DWORD /d 1 /f | Out-Null

Write-Host "Fast I/O tweaks applied." -ForegroundColor Green


# ------------------------------------------------------------
# 5. CLEAN WSL TEMP + LOGS
# ------------------------------------------------------------

Write-Host "`n[5/5] Cleaning WSL temp + logs..." -ForegroundColor Yellow

$wslTempPaths = @(
    "$env:LOCALAPPDATA\Packages\*\LocalState\temp",
    "$env:LOCALAPPDATA\Packages\*\LocalState\logs"
)

foreach ($p in $wslTempPaths) {
    Remove-Item -Recurse -Force $p -ErrorAction SilentlyContinue
}

Write-Host "WSL temp cleanup complete." -ForegroundColor Green


# ------------------------------------------------------------
# DONE
# ------------------------------------------------------------

Write-Host "`n=== WSL2 PERFORMANCE OPTIMIZATION COMPLETE ===" -ForegroundColor Cyan
Write-Host "Restart recommended to fully apply kernel + registry changes." -ForegroundColor Yellow
