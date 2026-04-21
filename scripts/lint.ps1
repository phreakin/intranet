Write-Host "=== PHP LINT START ==="

# Resolve repo root (script location → parent)
$repoRoot = Split-Path -Parent $PSScriptRoot

# Only scan THESE directories (your codebase)
$targetDirs = @(
    "app",
    "public",
    "config"
)

$files = @()

foreach ($dir in $targetDirs) {
    $fullPath = Join-Path $repoRoot $dir

    if (Test-Path $fullPath) {
        $files += Get-ChildItem -Path $fullPath -Recurse -Filter *.php -File
    }
    else {
        Write-Host "[WARN] Missing directory: $fullPath"
    }
}

if ($files.Count -eq 0) {
    Write-Host "[WARN] No PHP files found to lint"
    exit 0
}

foreach ($file in $files) {
    php -l $file.FullName | Out-Null

    if ($LASTEXITCODE -ne 0) {
        Write-Host "[ERROR] Syntax error in $($file.FullName)"
        exit 1
    }
}

Write-Host "[OK] All PHP files valid"