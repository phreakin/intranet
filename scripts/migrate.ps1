. (Join-Path $PSScriptRoot "common.ps1")

param(
    [switch]$IncludeSeeds,
    [switch]$SeedsOnly
)

$ErrorActionPreference = "Stop"
Enter-RepoRoot

Write-Section "Database Migration"

if (!(Test-CommandExists "mysql")) {
    Write-ErrorMsg "mysql is not available on PATH."
    exit 1
}

$env = Read-EnvFile
$dbHost = if ($env.ContainsKey("DB_HOST")) { $env["DB_HOST"] } else { "127.0.0.1" }
$dbPort = if ($env.ContainsKey("DB_PORT")) { $env["DB_PORT"] } else { "3306" }
$dbName = if ($env.ContainsKey("DB_NAME")) { $env["DB_NAME"] } else { "intranet" }
$dbUser = if ($env.ContainsKey("DB_USER")) { $env["DB_USER"] } else { "root" }
$dbPass = if ($env.ContainsKey("DB_PASS")) { $env["DB_PASS"] } else { "" }

$mysqlArgs = @(
    "--host=$dbHost",
    "--port=$dbPort",
    "--user=$dbUser",
    "--database=$dbName"
)

if (-not [string]::IsNullOrEmpty($dbPass)) {
    $mysqlArgs += "--password=$dbPass"
}

function Invoke-SqlFiles {
    param(
        [Parameter(Mandatory = $true)]
        [string]$Directory
    )

    $fullDirectory = Join-RepoPath $Directory

    if (!(Test-Path -LiteralPath $fullDirectory)) {
        Write-WarnMsg "Missing SQL directory: $Directory"
        return
    }

    $sqlFiles = @(Get-ChildItem -LiteralPath $fullDirectory -Filter "*.sql" -File | Sort-Object Name)

    foreach ($file in $sqlFiles) {
        Write-Info "Applying $Directory/$($file.Name)"
        Get-Content -LiteralPath $file.FullName -Raw | & mysql @mysqlArgs

        if ($LASTEXITCODE -ne 0) {
            Write-ErrorMsg "mysql failed while applying $($file.Name)"
            exit 1
        }
    }
}

if (-not $SeedsOnly) {
    Invoke-SqlFiles -Directory "database\migrations"
}

if ($IncludeSeeds -or $SeedsOnly) {
    Invoke-SqlFiles -Directory "database\seeds"
}

Write-Section "Migrations Complete"
