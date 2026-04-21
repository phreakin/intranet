. (Join-Path $PSScriptRoot "common.ps1")

$ErrorActionPreference = "Stop"
Enter-RepoRoot

Write-Section "Validating Intranet"

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

$missingRequired = @()

foreach ($relativePath in $requiredPaths) {
    $resolvedPath = Join-RepoPath $relativePath

    if (!(Test-Path -LiteralPath $resolvedPath)) {
        Write-ErrorMsg "Missing required path: $relativePath"
        $missingRequired += $relativePath
    }
    else {
        Write-Success "Found required path: $relativePath"
    }
}

foreach ($relativePath in $optionalPaths) {
    $resolvedPath = Join-RepoPath $relativePath

    if (Test-Path -LiteralPath $resolvedPath) {
        Write-Success "Found optional path: $relativePath"
    }
    else {
        Write-Info "Optional path not present: $relativePath"
    }
}

$modules = @(Get-ChildItem -LiteralPath (Join-RepoPath "app\Modules") -Directory)
$controllers = @(Get-ChildItem -LiteralPath (Join-RepoPath "app") -Recurse -Filter "*Controller.php" -File)
$views = @(Get-ChildItem -LiteralPath (Join-RepoPath "resources\views") -Recurse -Filter "*.php" -File)

Write-Info "Modules detected: $($modules.Count)"
Write-Info "Controllers detected: $($controllers.Count)"
Write-Info "Views detected: $($views.Count)"
Write-Info "Repo root: $(Get-RepoRoot)"

if ($missingRequired.Count -gt 0) {
    Write-Section "Validation Failed"
    exit 1
}

Write-Section "Validation Complete"
