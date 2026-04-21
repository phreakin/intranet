Write-Host "=== RUNNING MIGRATIONS ==="

$migrations = Get-ChildItem database/migrations/*.sql | Sort-Object Name

foreach ($file in $migrations) {
    Write-Host "Applying: $($file.Name)"
    mysql -u root -p intranet < $file.FullName
}

Write-Host "=== MIGRATIONS COMPLETE ==="