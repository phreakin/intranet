Set-StrictMode -Version Latest

$script:RepoRoot = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path

function Get-RepoRoot {
    return $script:RepoRoot
}

function Join-RepoPath {
    param(
        [Parameter(Mandatory = $true)]
        [string]$RelativePath
    )

    return Join-Path $script:RepoRoot $RelativePath
}

function Enter-RepoRoot {
    Set-Location $script:RepoRoot
}

function Test-CommandExists {
    param(
        [Parameter(Mandatory = $true)]
        [string]$Name
    )

    return $null -ne (Get-Command $Name -ErrorAction SilentlyContinue)
}

function Read-EnvFile {
    param(
        [string]$Path = (Join-RepoPath ".env")
    )

    $values = @{}

    if (!(Test-Path -LiteralPath $Path)) {
        return $values
    }

    foreach ($line in Get-Content -LiteralPath $Path) {
        if ([string]::IsNullOrWhiteSpace($line)) {
            continue
        }

        $trimmed = $line.Trim()
        if ($trimmed.StartsWith("#")) {
            continue
        }

        $parts = $trimmed -split "=", 2
        if ($parts.Count -ne 2) {
            continue
        }

        $values[$parts[0].Trim()] = $parts[1]
    }

    return $values
}

function Ensure-Directory {
    param(
        [Parameter(Mandatory = $true)]
        [string]$Path
    )

    if (!(Test-Path -LiteralPath $Path)) {
        New-Item -ItemType Directory -Path $Path -Force | Out-Null
    }
}

function Clear-DirectoryContents {
    param(
        [Parameter(Mandatory = $true)]
        [string]$Path
    )

    Ensure-Directory -Path $Path

    Get-ChildItem -LiteralPath $Path -Force -ErrorAction SilentlyContinue | ForEach-Object {
        Remove-Item -LiteralPath $_.FullName -Recurse -Force -ErrorAction Stop
    }
}

function Invoke-RepoScript {
    param(
        [Parameter(Mandatory = $true)]
        [string]$ScriptName,
        [string[]]$Arguments = @()
    )

    $scriptPath = Join-Path $PSScriptRoot $ScriptName

    if (!(Test-Path -LiteralPath $scriptPath)) {
        throw "Missing script: $scriptPath"
    }

    & powershell -NoProfile -ExecutionPolicy Bypass -File $scriptPath @Arguments

    if ($LASTEXITCODE -ne 0) {
        throw "Script failed: $ScriptName"
    }
}

function Write-Section {
    param([string]$Message)

    Write-Host ""
    Write-Host "=== $Message ==="
}

function Write-Info {
    param([string]$Message)

    Write-Host "[INFO] $Message"
}

function Write-WarnMsg {
    param([string]$Message)

    Write-Host "[WARN] $Message"
}

function Write-ErrorMsg {
    param([string]$Message)

    Write-Host "[ERROR] $Message"
}

function Write-Success {
    param([string]$Message)

    Write-Host "[OK] $Message"
}
