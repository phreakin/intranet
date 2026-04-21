. (Join-Path $PSScriptRoot "common.ps1")

param(
    [switch]$IncludeSeeds
)

$ErrorActionPreference = "Stop"
Enter-RepoRoot

Write-Section "Dev Reset"

$pathsToClear = @(
    "storage\cache",
    "storage\logs"
)

foreach ($relativePath in $pathsToClear) {
    Clear-DirectoryContents -Path (Join-RepoPath $relativePath)
    Write-Success "Cleared $relativePath"
}

$migrateArgs = @()
if ($IncludeSeeds) {
    $migrateArgs += "-IncludeSeeds"
}

Invoke-RepoScript -ScriptName "migrate.ps1" -Arguments $migrateArgs

Write-Section "Reset Complete"
