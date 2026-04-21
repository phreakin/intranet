Write-Host "=== PHP LINT START ==="

$files = Get-ChildItem -Recurse -Filter *.php

foreach ($file in $files) {
    php -l $file.FullName

    if ($LASTEXITCODE -ne 0) {
        Write-Host "❌ Syntax error in $($file.FullName)"
        exit 1
    }
}

Write-Host "=== PHP LINT COMPLETE ==="