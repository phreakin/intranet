. (Join-Path $PSScriptRoot "common.ps1")

$ErrorActionPreference = "Stop"
Enter-RepoRoot

Write-Section "PHP Lint"

if (!(Test-CommandExists "php")) {
    Write-ErrorMsg "php is not available on PATH."
    exit 1
}

$targetDirs = @(
    "app",
    "config",
    "public",
    "resources\views",
    "tests"
)

$files = @()

foreach ($dir in $targetDirs) {
    $fullPath = Join-RepoPath $dir

    if (Test-Path -LiteralPath $fullPath) {
        $files += Get-ChildItem -LiteralPath $fullPath -Recurse -Filter "*.php" -File
    }
    else {
        Write-WarnMsg "Missing directory: $dir"
    }
}

$bootstrapFile = Join-RepoPath "bootstrap.php"
if (Test-Path -LiteralPath $bootstrapFile) {
    $files += Get-Item -LiteralPath $bootstrapFile
}

$files = @($files | Sort-Object FullName -Unique)

if ($files.Count -eq 0) {
    Write-WarnMsg "No PHP files found to lint."
    exit 0
}

foreach ($file in $files) {
    & php -l $file.FullName | Out-Null

    if ($LASTEXITCODE -ne 0) {
        Write-ErrorMsg "Syntax error in $($file.FullName)"
        exit 1
    }
}

Write-Success "All PHP files are valid."
