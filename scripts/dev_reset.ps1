Write-Host "=== DEV RESET ==="

# Clear logs
Remove-Item storage/logs/* -Recurse -Force -ErrorAction SilentlyContinue

# Clear cache
Remove-Item storage/cache/* -Recurse -Force -ErrorAction SilentlyContinue

# Re-run migrations
.\scripts\migrate.ps1

Write-Host "=== RESET COMPLETE ==="