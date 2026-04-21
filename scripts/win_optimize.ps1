Write-Host "=== WINDOWS DEV PERFORMANCE OPTIMIZER ===" -ForegroundColor Cyan

# ------------------------------------------------------------
# 1. CLEAN TEMP, LOG, AND DEV JUNK
# ------------------------------------------------------------

Write-Host "`n[1/5] Cleaning temp and dev junk..." -ForegroundColor Yellow

$cleanupPaths = @(
    "$env:TEMP\*",
    "$env:WINDIR\Temp\*",
    "$env:LOCALAPPDATA\Temp\*",
    "$env:USERPROFILE\AppData\Local\CrashDumps\*"
)

foreach ($p in $cleanupPaths) {
    Write-Host "Cleaning: $p"
    Remove-Item -Recurse -Force $p -ErrorAction SilentlyContinue
}

Write-Host "Temp cleanup complete." -ForegroundColor Green


# ------------------------------------------------------------
# 2. RESET NODE/YARN/PNPM CACHES
# ------------------------------------------------------------

Write-Host "`n[2/5] Resetting Node/npm/Yarn/pnpm caches..." -ForegroundColor Yellow

$nodePaths = @(
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

foreach ($p in $nodePaths) {
    Write-Host "Removing: $p"
    Remove-Item -Recurse -Force $p -ErrorAction SilentlyContinue
}

Write-Host "Node ecosystem reset complete." -ForegroundColor Green


# ------------------------------------------------------------
# 3. ADD DEFENDER EXCLUSIONS FOR DEV STACK (RESILIENT)
# ------------------------------------------------------------

Write-Host "`n[3/5] Adding Windows Defender exclusions for dev tools..." -ForegroundColor Yellow

function Try-AddExclusionPath {
    param([string]$Path)

    if (-not (Test-Path $Path)) {
        Write-Host "Skipping (not found): $Path" -ForegroundColor DarkYellow
        return
    }

    try {
        Add-MpPreference -ExclusionPath $Path -ErrorAction Stop
        Write-Host "Excluding path: $Path"
    } catch {
        Write-Host "FAILED to exclude path (permissions/policy): $Path" -ForegroundColor Red
    }
}

function Try-AddExclusionProcess {
    param([string]$Proc)

    try {
        Add-MpPreference -ExclusionProcess $Proc -ErrorAction Stop
        Write-Host "Excluding process: $Proc"
    } catch {
        Write-Host "FAILED to exclude process (permissions/policy): $Proc" -ForegroundColor Red
    }
}

$defenderPaths = @(
    "$env:ProgramFiles\nodejs",
    "$env:ProgramFiles(x86)\nodejs",

    "$env:APPDATA\npm",
    "$env:LOCALAPPDATA\npm-cache",
    "$env:LOCALAPPDATA\Yarn",
    "$env:LOCALAPPDATA\Yarn\Cache",
    "$env:LOCALAPPDATA\pnpm",
    "$env:USERPROFILE\AppData\Local\pnpm-store",

    "C:\Program Files\Docker\Docker",
    "C:\Program Files\Docker\Docker\resources",
    "C:\ProgramData\Docker",
    "C:\ProgramData\DockerDesktop",
    "$env:LOCALAPPDATA\Docker",
    "$env:LOCALAPPDATA\DockerDesktop",
    "$env:USERPROFILE\.docker",

    "$env:LOCALAPPDATA\Docker\wsl\data\ext4.vhdx",

    "E:\Projects",
    "$env:USERPROFILE\Projects",
    "$env:USERPROFILE\Documents\Projects",
    "$env:USERPROFILE\Desktop\Projects"
)

foreach ($p in $defenderPaths) {
    Try-AddExclusionPath -Path $p
}

$defenderProcs = @(
    "node.exe", "npm.exe", "yarn.exe", "pnpm.exe",
    "docker.exe", "dockerd.exe",
    "com.docker.backend.exe", "com.docker.service.exe"
)

foreach ($proc in $defenderProcs) {
    Try-AddExclusionProcess -Proc $proc
}

Write-Host "Defender exclusion phase complete (with graceful handling)." -ForegroundColor Green


# ------------------------------------------------------------
# 4. CLEAN AND NORMALIZE PATH
# ------------------------------------------------------------

Write-Host "`n[4/5] Cleaning PATH (removing dead entries)..." -ForegroundColor Yellow

$originalPath = $env:PATH
$pathParts = $originalPath.Split(";") | Where-Object { $_ -and ($_ -ne "") }

$validParts = @()
foreach ($part in $pathParts) {
    if (Test-Path $part) {
        $validParts += $part
    } else {
        Write-Host "Removing dead PATH entry: $part" -ForegroundColor DarkYellow
    }
}

$cleanPath = ($validParts | Select-Object -Unique) -join ";"
setx PATH "$cleanPath" | Out-Null

Write-Host "PATH cleaned and normalized." -ForegroundColor Green


# ------------------------------------------------------------
# 5. WSL2 + DOCKER DEV TUNING
# ------------------------------------------------------------

Write-Host "`n[5/5] Tuning WSL2 for dev performance..." -ForegroundColor Yellow

$wslConfig = @"
[wsl2]
memory=8GB
processors=4
swap=0
localhostForwarding=true
"@

$wslConfigPath = "$env:USERPROFILE\.wslconfig"
$wslConfig | Out-File -FilePath $wslConfigPath -Encoding ASCII

Write-Host "WSL2 config written to $wslConfigPath" -ForegroundColor Cyan
Write-Host "  • memory = 8GB"
Write-Host "  • processors = 4"
Write-Host "  • swap = 0"
Write-Host "  • localhostForwarding = true"

Write-Host "`nShutting down WSL to apply settings..." -ForegroundColor Yellow
wsl --shutdown 2>$null

Write-Host "`n=== WINDOWS DEV PERFORMANCE OPTIMIZATION COMPLETE ===" -ForegroundColor Cyan
Write-Host "Reboot recommended to fully apply PATH and Defender changes." -ForegroundColor Yellow
