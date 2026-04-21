Write-Host "Clearing ALL npm caches, temp files, and global installs..." -ForegroundColor Cyan

# npm cache
Remove-Item -Recurse -Force "$env:LOCALAPPDATA\npm-cache" -ErrorAction SilentlyContinue
Remove-Item -Recurse -Force "$env:APPDATA\npm-cache" -ErrorAction SilentlyContinue

# npm global installs
Remove-Item -Recurse -Force "$env:APPDATA\npm" -ErrorAction SilentlyContinue

# npm temp files
Remove-Item -Recurse -Force "$env:TEMP\npm-*" -ErrorAction SilentlyContinue

# node-gyp cache
Remove-Item -Recurse -Force "$env:USERPROFILE\.node-gyp" -ErrorAction SilentlyContinue

Write-Host "npm cache and related data fully cleared." -ForegroundColor Green
