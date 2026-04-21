Write-Host "=== UPDATE START ==="

git pull origin main

if (Test-Path "composer.json") {
    composer install
}

Write-Host "=== UPDATE COMPLETE ==="