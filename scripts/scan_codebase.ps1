. (Join-Path $PSScriptRoot "common.ps1")

$ErrorActionPreference = "Stop"
Enter-RepoRoot

Write-Section "Codebase Scan"

$modulesPath = Join-RepoPath "app\Modules"
$expectedModules = @(
    "Admin",
    "Api",
    "Authentication",
    "Automation",
    "Dashboard",
    "Moderation",
    "ModerationEngine",
    "Posts",
    "Shared",
    "Users"
)

if (!(Test-Path -LiteralPath $modulesPath)) {
    Write-ErrorMsg "Missing modules path: app\Modules"
    exit 1
}

$modules = @(Get-ChildItem -LiteralPath $modulesPath -Directory | Sort-Object Name)

foreach ($module in $modules) {
    $moduleFile = Join-Path $module.FullName "module.php"
    $controllersPath = Join-Path $module.FullName "Controllers"
    $viewsPath = Join-Path $module.FullName "Views"
    $servicesPath = Join-Path $module.FullName "Services"

    if (Test-Path -LiteralPath $moduleFile) {
        Write-Success "Manifest present: $($module.Name)"
    }
    else {
        Write-WarnMsg "Missing manifest: $($module.Name)\module.php"
    }

    if (Test-Path -LiteralPath $controllersPath) {
        Write-Info "Controllers directory present: $($module.Name)"
    }

    if (Test-Path -LiteralPath $viewsPath) {
        Write-Info "Views directory present: $($module.Name)"
    }

    if (Test-Path -LiteralPath $servicesPath) {
        Write-Info "Services directory present: $($module.Name)"
    }
}

foreach ($moduleName in $expectedModules) {
    $expectedPath = Join-Path $modulesPath $moduleName

    if (Test-Path -LiteralPath $expectedPath) {
        Write-Success "Expected module present: $moduleName"
    }
    else {
        Write-WarnMsg "Expected module missing: $moduleName"
    }
}

Write-Section "Scan Complete"
