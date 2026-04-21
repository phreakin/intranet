Write-Host "=== VALIDATING INTRANET ==="

$repoRoot = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path

$requiredPaths = @(
    "app\Core",
    "app\Modules",
    "config",
    "public\index.php",
    "resources\views",
    "resources\assets",
    "database\migrations",
    "database\seeds",
    ".env",
    "composer.json"
)

$optionalPaths = @(
    "routes",
    "database\factories"
)

foreach ($relativePath in $requiredPaths) {
    $resolvedPath = Join-Path $repoRoot $relativePath

    if (!(Test-Path -LiteralPath $resolvedPath)) {
        Write-Host "[MISSING] Required path: $relativePath"
    } else {
        Write-Host "[FOUND] Required path: $relativePath"
    }
}

foreach ($relativePath in $optionalPaths) {
    $resolvedPath = Join-Path $repoRoot $relativePath

    if (Test-Path -LiteralPath $resolvedPath) {
        Write-Host "[FOUND] Optional path: $relativePath"
    } else {
        Write-Host "[OPTIONAL] Path not present: $relativePath"
    }
}

# Check modules
$modulesPath = Join-Path $repoRoot "app\Modules"
$modules = @(Get-ChildItem -LiteralPath $modulesPath -Directory)
Write-Host "Modules detected: $($modules.Count)"

# Check controllers
$appPath = Join-Path $repoRoot "app"
$controllers = @(Get-ChildItem -LiteralPath $appPath -Recurse -Filter "*Controller.php" -File)
Write-Host "Controllers: $($controllers.Count)"

# Check views
$viewsPath = Join-Path $repoRoot "resources\views"
$views = @(Get-ChildItem -LiteralPath $viewsPath -Recurse -Filter "*.php" -File)
Write-Host "Views: $($views.Count)"

Write-Host "Repo root: $repoRoot"
Write-Host "=== VALIDATION COMPLETE ==="
