Write-Host "=== PHP LINT START ==="

$targetDirs = @(
    "app",
    "public",
    "config"
)

$files = @()

foreach ($dir in $targetDirs) {
    if (Test-Path $dir) {
        $files += Get-ChildItem -Path $dir -Recurse -Filter *.php -File
    }
}

foreach ($file in $files) {
    php -l $file.FullName | Out-Null

    if ($LASTEXITCODE -ne 0) {
        Write-Host "[ERROR] Syntax error in $($file.FullName)"
        exit 1
    }
}

Write-Host "[OK] All PHP files valid"