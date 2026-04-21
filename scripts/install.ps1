. (Join-Path $PSScriptRoot "common.ps1")

param(
    [switch]$SkipComposer,
    [switch]$SkipNpm
)

$ErrorActionPreference = "Stop"
Enter-RepoRoot

Write-Section "Intranet Install"

$dirs = @(
    "storage",
    "storage\cache",
    "storage\logs",
    "storage\uploads"
)

foreach ($dir in $dirs) {
    Ensure-Directory -Path (Join-RepoPath $dir)
    Write-Success "Ready: $dir"
}

$envPath = Join-RepoPath ".env"
$envExamplePath = Join-RepoPath ".env.example"

if (!(Test-Path -LiteralPath $envPath) -and (Test-Path -LiteralPath $envExamplePath)) {
    Copy-Item -LiteralPath $envExamplePath -Destination $envPath
    Write-Success "Created .env from .env.example"
}

if ((Test-Path -LiteralPath (Join-RepoPath "composer.json")) -and -not $SkipComposer) {
    if (!(Test-CommandExists "composer")) {
        Write-ErrorMsg "composer is not available on PATH."
        exit 1
    }

    & composer install --no-interaction
    if ($LASTEXITCODE -ne 0) {
        Write-ErrorMsg "composer install failed."
        exit 1
    }
}

if ((Test-Path -LiteralPath (Join-RepoPath "package.json")) -and -not $SkipNpm) {
    if (!(Test-CommandExists "npm")) {
        Write-WarnMsg "npm is not available on PATH. Skipping npm install."
    }
    else {
        & npm install
        if ($LASTEXITCODE -ne 0) {
            Write-ErrorMsg "npm install failed."
            exit 1
        }
    }
}

Write-Section "Install Complete"
