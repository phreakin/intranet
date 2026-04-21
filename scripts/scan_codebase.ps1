Write-Host "=== CODEBASE SCAN ==="

# Modules missing structure
$modules = Get-ChildItem app/Modules -Directory

foreach ($module in $modules) {
    $path = $module.FullName

    if (!(Test-Path "$path/Controllers")) {
        Write-Host "⚠️ Missing Controllers: $($module.Name)"
    }

    if (!(Test-Path "$path/Views")) {
        Write-Host "⚠️ Missing Views: $($module.Name)"
    }

    if (!(Test-Path "$path/Services")) {
        Write-Host "⚠️ Missing Services: $($module.Name)"
    }
}

# Detect missing core systems
$expected = @(
    "app/Modules/ModerationEngine",
    "app/Modules/API",
    "app/Modules/Analytics",
    "app/Modules/Automation"
)

foreach ($e in $expected) {
    if (!(Test-Path $e)) {
        Write-Host "🚨 Missing future module: $e"
    }
}

Write-Host "=== SCAN COMPLETE ==="