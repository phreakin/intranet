Write-Host "Adding ALL Windows Defender exclusions for Node.js, npm, Yarn, pnpm..." -ForegroundColor Cyan

$paths = @(
# Node.js installation
    "$env:ProgramFiles\nodejs",
    "$env:ProgramFiles(x86)\nodejs",

    # npm global install + cache
    "$env:AppData\npm",
    "$env:LocalAppData\npm-cache",
    "$env:UserProfile\AppData\Roaming\npm",
    "$env:UserProfile\AppData\Local\npm-cache",

    # Yarn global + cache
    "$env:LocalAppData\Yarn",
    "$env:LocalAppData\Yarn\Cache",
    "$env:UserProfile\AppData\Local\Yarn",
    "$env:UserProfile\AppData\Local\Yarn\Cache",

    # pnpm store
    "$env:LocalAppData\pnpm",
    "$env:UserProfile\AppData\Local\pnpm-store",

    # Common project roots
    "C:\Projects",
    "$env:UserProfile\Documents\Projects",
    "$env:UserProfile\Desktop\Projects",

    # Any node_modules under user profile
    "$env:UserProfile\node_modules",
    "$env:UserProfile\Documents\node_modules",
    "$env:UserProfile\Desktop\node_modules",
    "E:\Projects\",
    "C:\xampp\",
    "E:\Repositories\",
    "E:\Docker\",
    "C:\Program Files\Docker",
    "%LOCALAPPDATA%\Docker",
   "%LOCALAPPDATA%\DockerDesktop",  
    "%USERPROFILE%\.docker"

)

foreach ($path in $paths) {
    if (Test-Path $path) {
        Write-Host "Adding exclusion: $path"
        Add-MpPreference -ExclusionPath $path
    } else {
        Write-Host "Skipping (not found): $path" -ForegroundColor DarkYellow
    }
}

# Add wildcard exclusions for ANY node_modules folder under user profile
Write-Host "Adding wildcard exclusions for node_modules folders..." -ForegroundColor Cyan
Add-MpPreference -ExclusionProcess "node.exe"
Add-MpPreference -ExclusionProcess "npm.exe"
Add-MpPreference -ExclusionProcess "yarn.exe"
Add-MpPreference -ExclusionProcess "pnpm.exe"

Write-Host "Done. npm, Yarn, and pnpm will run MUCH faster now." -ForegroundColor Green
