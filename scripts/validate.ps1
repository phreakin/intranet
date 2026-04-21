Write-Host "=== VALIDATING INTRANET ==="

$paths = @(
    "app/Core",
    "app/Modules",
    "config",
    "public/index.php",
    "resources/views",
    "database/migrations"
)

foreach ($path in $paths) {
    if (!(Test-Path $path)) {
        Write-Host "❌ Missing: $path"
    } else {
        Write-Host "✅ Found: $path"
    }
}

# Check modules
$modules = Get-ChildItem app/Modules -Directory
Write-Host "Modules detected: $($modules.Count)"

# Check controllers
$controllers = Get-ChildItem -Recurse -Filter "*Controller.php" app/
Write-Host "Controllers: $($controllers.Count)"

# Check views
$views = Get-ChildItem -Recurse -Filter "*.php" resources/views
Write-Host "Views: $($views.Count)"

Write-Host "=== VALIDATION COMPLETE ==="    