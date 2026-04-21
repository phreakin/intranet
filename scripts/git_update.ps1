. (Join-Path $PSScriptRoot "common.ps1")

param(
    [string]$Branch = "",
    [string]$Remote = "origin",
    [switch]$SkipComposer,
    [switch]$SkipNpm,
    [switch]$BuildAssets
)

$ErrorActionPreference = "Stop"
Enter-RepoRoot

Write-Section "Git Update"

if (!(Test-CommandExists "git")) {
    Write-ErrorMsg "git is not available on PATH."
    exit 1
}

if ([string]::IsNullOrWhiteSpace($Branch)) {
    $Branch = (& git rev-parse --abbrev-ref HEAD).Trim()
    if ($LASTEXITCODE -ne 0 -or [string]::IsNullOrWhiteSpace($Branch)) {
        Write-ErrorMsg "Unable to determine current branch."
        exit 1
    }
}

& git fetch $Remote
if ($LASTEXITCODE -ne 0) {
    Write-ErrorMsg "git fetch failed."
    exit 1
}

& git pull --rebase $Remote $Branch
if ($LASTEXITCODE -ne 0) {
    Write-ErrorMsg "git pull --rebase failed."
    exit 1
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

        if ($BuildAssets) {
            & npm run build
            if ($LASTEXITCODE -ne 0) {
                Write-ErrorMsg "npm run build failed."
                exit 1
            }
        }
    }
}

Write-Section "Update Complete"
