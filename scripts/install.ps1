Write-Host "=== INTRANET INSTALL ==="

# Ensure storage dirs exist
$dirs = @(
    "storage/cache",
    "storage/logs",
    "storage/uploads"
)

foreach ($dir in $dirs) {
    if (!(Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir | Out-Null
        Write-Host "Created: $dir"
    }
}

# Ensure config files
if (!(Test-Path ".env") -and (Test-Path ".env.example")) {
    Copy-Item ".env.example" ".env"
    Write-Host ".env created"
}

# Composer install
if (Test-Path "composer.json") {
    composer install
}

Write-Host "=== INSTALL COMPLETE ==="