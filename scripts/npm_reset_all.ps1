Write-Host "=== FULL NODE/YARN/NPM RESET + WINDOWS OPTIMIZATION + DEFENDER EXCLUSIONS ===" -ForegroundColor Cyan

# -----------------------------
# 1. RESET ALL NODE ENVIRONMENTS
# -----------------------------

Write-Host "`n[1/3] Resetting npm, Yarn, pnpm, node-gyp, caches..." -ForegroundColor Yellow

# npm cache
Remove-Item -Recurse -Force "$env:LOCALAPPDATA\npm-cache" -ErrorAction SilentlyContinue
Remove-Item -Recurse -Force "$env:APPDATA\npm-cache" -ErrorAction SilentlyContinue

# npm global installs
Remove-Item -Recurse -Force "$env:APPDATA\npm" -ErrorAction SilentlyContinue

# Yarn cache
Remove-Item -Recurse -Force "$env:LOCALAPPDATA\Yarn" -ErrorAction SilentlyContinue
Remove-Item -Recurse -Force "$env:LOCALAPPDATA\Yarn\Cache" -ErrorAction SilentlyContinue

# pnpm store
Remove-Item -Recurse -Force "$env:LOCALAPPDATA\pnpm" -ErrorAction SilentlyContinue
Remove-Item -Recurse -Force "$env:USERPROFILE\AppData\Local\pnpm-store" -ErrorAction SilentlyContinue

# node-gyp
Remove-Item -Recurse -Force "$env:USERPROFILE\.node-gyp" -ErrorAction SilentlyContinue

# Temp npm/yarn files
Remove-Item -Recurse -Force "$env:TEMP\npm-*" -ErrorAction SilentlyContinue
Remove-Item -Recurse -Force "$env:TEMP\yarn-*" -ErrorAction SilentlyContinue

Write-Host "Node ecosystem reset complete." -ForegroundColor Green


# -----------------------------
# 2. WINDOWS DEV OPTIMIZATION
# -----------------------------

Write-Host "`n[2/3] Optimizing Windows for Node/Yarn performance..." -ForegroundColor Yellow

# Clean Windows temp
Remove-Item -Recurse -Force "$env:TEMP\*" -ErrorAction SilentlyContinue

# Clean stale symlinks in user profile
Get-ChildItem -Path $env:USERPROFILE -Recurse -ErrorAction SilentlyContinue |
        Where-Object { $_.LinkType -ne $null -and -not (Test-Path $_.FullName) } |
        Remove-Item -Force -ErrorAction SilentlyContinue

# Optional: Clean PATH of broken entries
$cleanPath = ($env:PATH.Split(";") | Where-Object { Test-Path $_ }) -join ";"
setx PATH "$cleanPath" | Out-Null

Write-Host "Windows optimization complete." -ForegroundColor Green


# -----------------------------
# 3. WINDOWS DEFENDER EXCLUSIONS
# -----------------------------

Write-Host "`n[3/3] Adding ALL Defender exclusions for Node, npm, Yarn, pnpm, Docker, WSL2..." -ForegroundColor Yellow

$paths = @(
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

    # WSL2 virtual disk
    "$env:LOCALAPPDATA\Docker\wsl\data\ext4.vhdx",

    # Common project roots
    "C:\Projects",
    "$env:USERPROFILE\Documents\Projects",
    "$env:USERPROFILE\Desktop\Projects",

    # Any node_modules under user profile
    "$env:USERPROFILE\node_modules",
    "$env:USERPROFILE\Documents\node_modules",
    "$env:USERPROFILE\Desktop\node_modules"
)

foreach ($path in $paths) {
    if (Test-Path $path) {
        Write-Host "Excluding: $path"
        Add-MpPreference -ExclusionPath $path
    } else {
        Write-Host "Skipping (not found): $path" -ForegroundColor DarkYellow
    }
}

# Process exclusions
Add-MpPreference -ExclusionProcess "node.exe"
Add-MpPreference -ExclusionProcess "npm.exe"
Add-MpPreference -ExclusionProcess "yarn.exe"
Add-MpPreference -ExclusionProcess "pnpm.exe"
Add-MpPreference -ExclusionProcess "docker.exe"
Add-MpPreference -ExclusionProcess "dockerd.exe"
Add-MpPreference -ExclusionProcess "com.docker.backend.exe"
Add-MpPreference -ExclusionProcess "com.docker.service.exe"

Write-Host "`nAll exclusions added successfully." -ForegroundColor Green

Write-Host "`n=== COMPLETE: Your Windows dev environment is now fully reset, optimized, and accelerated ===" -ForegroundColor Cyan
