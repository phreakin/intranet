Write-Host "=== NODE + DOCKER + WSL2 OPTIMIZED ENVIRONMENT SETUP ===" -ForegroundColor Cyan

# ------------------------------------------------------------
# 1. RESET NODE ENVIRONMENT (npm, Yarn, pnpm, node-gyp, caches)
# ------------------------------------------------------------

Write-Host "`n[1/4] Resetting Node/npm/Yarn/pnpm caches..." -ForegroundColor Yellow

$resetPaths = @(
    "$env:LOCALAPPDATA\npm-cache",
    "$env:APPDATA\npm-cache",
    "$env:APPDATA\npm",
    "$env:LOCALAPPDATA\Yarn",
    "$env:LOCALAPPDATA\Yarn\Cache",
    "$env:LOCALAPPDATA\pnpm",
    "$env:USERPROFILE\AppData\Local\pnpm-store",
    "$env:USERPROFILE\.node-gyp",
    "$env:TEMP\npm-*",
    "$env:TEMP\yarn-*"
)

foreach ($p in $resetPaths) {
    Remove-Item -Recurse -Force $p -ErrorAction SilentlyContinue
}

Write-Host "Node ecosystem reset complete." -ForegroundColor Green


# ------------------------------------------------------------
# 2. WINDOWS OPTIMIZATION (temp cleanup, symlink cleanup, PATH)
# ------------------------------------------------------------

Write-Host "`n[2/4] Optimizing Windows for dev workloads..." -ForegroundColor Yellow

# Clean Windows temp
Remove-Item -Recurse -Force "$env:TEMP\*" -ErrorAction SilentlyContinue

# Clean broken symlinks
Get-ChildItem -Path $env:USERPROFILE -Recurse -ErrorAction SilentlyContinue |
        Where-Object { $_.LinkType -ne $null -and -not (Test-Path $_.FullName) } |
        Remove-Item -Force -ErrorAction SilentlyContinue

# Clean PATH of invalid entries
$cleanPath = ($env:PATH.Split(";") | Where-Object { Test-Path $_ }) -join ";"
setx PATH "$cleanPath" | Out-Null

Write-Host "Windows optimization complete." -ForegroundColor Green


# ------------------------------------------------------------
# 3. DEFENDER EXCLUSIONS (Node, npm, Yarn, pnpm, Docker, WSL2)
# ------------------------------------------------------------

Write-Host "`n[3/4] Adding Defender exclusions for Node, Docker, WSL2..." -ForegroundColor Yellow

$defenderPaths = @(
# Node.js
    "$env:ProgramFiles\nodejs",
    "$env:ProgramFiles(x86)\nodejs",

    # npm
    "$env:APPDATA\npm",
    "$env:LOCALAPPDATA\npm-cache",

    # Yarn
    "$env:LOCALAPPDATA\Yarn",
    "$env:LOCALAPPDATA\Yarn\Cache",

    # pnpm
    "$env:LOCALAPPDATA\pnpm",
    "$env:USERPROFILE\AppData\Local\pnpm-store",

    # Docker Desktop
    "C:\Program Files\Docker\Docker",
    "C:\Program Files\Docker\Docker\resources",
    "C:\ProgramData\Docker",
    "C:\ProgramData\DockerDesktop",
    "$env:LOCALAPPDATA\Docker",
    "$env:LOCALAPPDATA\DockerDesktop",
    "$env:USERPROFILE\.docker",

    # WSL2 virtual disk (critical)
    "$env:LOCALAPPDATA\Docker\wsl\data\ext4.vhdx",

    # Common project roots
    "C:\Projects",
    "$env:USERPROFILE\Documents\Projects",
    "$env:USERPROFILE\Desktop\Projects"
)

foreach ($p in $defenderPaths) {
    if (Test-Path $p) {
        Add-MpPreference -ExclusionPath $p
        Write-Host "Excluded: $p"
    } else {
        Write-Host "Skipping (not found): $p" -ForegroundColor DarkYellow
    }
}

# Process exclusions
$processes = @(
    "node.exe", "npm.exe", "yarn.exe", "pnpm.exe",
    "docker.exe", "dockerd.exe",
    "com.docker.backend.exe", "com.docker.service.exe"
)

foreach ($proc in $processes) {
    Add-MpPreference -ExclusionProcess $proc
}

Write-Host "Defender exclusions added." -ForegroundColor Green


# ------------------------------------------------------------
# 4. WSL2 OPTIMIZATION (memory, CPU, I/O, swap)
# ------------------------------------------------------------

Write-Host "`n[4/4] Optimizing WSL2 performance..." -ForegroundColor Yellow

$wslConfig = @"
[wsl2]
memory=8GB
processors=4
swap=0
localhostForwarding=true
"@

$wslConfigPath = "$env:USERPROFILE\.wslconfig"
$wslConfig | Out-File -FilePath $wslConfigPath -Encoding ASCII

Write-Host "WSL2 optimized with:" -ForegroundColor Cyan
Write-Host "  • 8GB RAM"
Write-Host "  • 4 CPU cores"
Write-Host "  • Swap disabled"
Write-Host "  • Localhost forwarding enabled"

Write-Host "`nRestarting WSL..." -ForegroundColor Yellow
wsl --shutdown

Write-Host "`n=== OPTIMIZED ENVIRONMENT COMPLETE ===" -ForegroundColor Green
Write-Host "Your Node + Docker + WSL2 stack is now running at maximum speed." -ForegroundColor Cyan
