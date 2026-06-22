[CmdletBinding()]
param()

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

function Write-DeployLog {
    param([string]$Message)
    Write-Host "[Cartly] $Message"
}

function Assert-CommandAvailable {
    param([string]$CommandName)
    if (-not (Get-Command $CommandName -ErrorAction SilentlyContinue)) {
        throw "Required command not found: $CommandName"
    }
}

function Assert-NumericValue {
    param(
        [string]$Value,
        [string]$Label
    )
    if ($Value -notmatch '^\d+$') {
        throw "$Label must be numeric."
    }
}

function Get-RepoRoot {
    param([string]$ScriptPath)
    return Split-Path -Parent $ScriptPath
}

function Get-ReleaseId {
    param([string]$CommitSha)
    $shortSha = $CommitSha.Substring(0, [Math]::Min(7, $CommitSha.Length))
    $timestamp = Get-Date -Format 'yyyy-MM-dd-HHmm'
    return "$timestamp-$shortSha"
}

function Get-TrackedDatabasePaths {
    return @(
        'src/database'
        'cartly/database'
    )
}

function Test-DatabaseChanges {
    param(
        [string]$RepoRoot,
        [string]$PreviousCommit,
        [string]$TargetCommit
    )

    if ([string]::IsNullOrWhiteSpace($PreviousCommit)) {
        return $true
    }

    Push-Location $RepoRoot
    try {
        $paths = Get-TrackedDatabasePaths
        $output = & git diff --name-only $PreviousCommit $TargetCommit -- $paths
        return -not [string]::IsNullOrWhiteSpace(($output | Out-String).Trim())
    }
    finally {
        Pop-Location
    }
}

function Get-CurrentVersionMetadata {
    param([string]$VersioningPath)
    $map = @{}
    if (-not (Test-Path $VersioningPath)) {
        return $map
    }

    foreach ($line in Get-Content $VersioningPath) {
        if ($line -match '^\s*([^=]+)=(.*)$') {
            $map[$matches[1].Trim()] = $matches[2].Trim()
        }
    }

    return $map
}

function Write-VersioningFile {
    param(
        [string]$Path,
        [string]$ReleaseId,
        [string]$CommitSha,
        [string]$Branch,
        [string[]]$PreviousVersions = @()
    )

    $lines = @(
        "CURRENT_VERSION=$ReleaseId"
        "CURRENT_COMMIT=$CommitSha"
        "DEPLOYED_AT=$((Get-Date).ToString('o'))"
        "BRANCH=$Branch"
    )

    if ($PreviousVersions.Count -gt 0) {
        $lines += "PREVIOUS_VERSIONS=$($PreviousVersions -join ',')"
    }

    $parent = Split-Path -Parent $Path
    if ($parent -and -not (Test-Path $parent)) {
        New-Item -ItemType Directory -Path $parent | Out-Null
    }

    Set-Content -Path $Path -Value $lines -Encoding UTF8
}

function Reset-Directory {
    param([string]$Path)
    if (Test-Path $Path) {
        Remove-Item -Path $Path -Recurse -Force
    }
    New-Item -ItemType Directory -Path $Path | Out-Null
}

function Copy-ProjectTree {
    param(
        [string]$SourcePath,
        [string]$DestinationPath
    )

    Reset-Directory -Path $DestinationPath
    $sourceItems = Join-Path $SourcePath '*'
    Copy-Item -Path $sourceItems -Destination $DestinationPath -Recurse -Force
}

function Export-GitRelease {
    param(
        [string]$RepoRoot,
        [string]$CommitSha,
        [string]$DestinationPath
    )

    Reset-Directory -Path $DestinationPath

    $tempArchive = Join-Path ([System.IO.Path]::GetTempPath()) ("cartly-release-$([guid]::NewGuid()).zip")
    Push-Location $RepoRoot
    try {
        & git archive --format=zip --output=$tempArchive $CommitSha
    }
    finally {
        Pop-Location
    }

    try {
        Expand-Archive -Path $tempArchive -DestinationPath $DestinationPath -Force
    }
    finally {
        if (Test-Path $tempArchive) {
            Remove-Item $tempArchive -Force
        }
    }
}

function Write-EnvFile {
    param(
        [string]$Path,
        [hashtable]$Values
    )

    $lines = foreach ($key in ($Values.Keys | Sort-Object)) {
        "$key=$($Values[$key])"
    }

    Set-Content -Path $Path -Value $lines -Encoding UTF8
}

function Install-ComposerDependencies {
    param([string]$ReleasePath)
    Push-Location $ReleasePath
    try {
        Assert-CommandAvailable -CommandName 'composer'
        & composer install --no-interaction --prefer-dist --no-progress --optimize-autoloader
    }
    finally {
        Pop-Location
    }
}

function Invoke-MySqlFile {
    param(
        [string]$MySqlExe,
        [string]$Host,
        [string]$Port,
        [string]$Database,
        [string]$User,
        [string]$Password,
        [string]$FilePath
    )

    $arguments = @(
        "--host=$Host"
        "--port=$Port"
        "--user=$User"
        "--database=$Database"
        '--default-character-set=utf8mb4'
    )

    if (-not [string]::IsNullOrEmpty($Password)) {
        $arguments += "--password=$Password"
    }

    Get-Content -Path $FilePath -Raw | & $MySqlExe @arguments
}

function Invoke-SeedAssets {
    param([string]$ReleasePath)
    Push-Location $ReleasePath
    try {
        & php 'src/database/seed_assets.php'
    }
    finally {
        Pop-Location
    }
}

function Invoke-PreviewDatabaseRefresh {
    param(
        [string]$ReleasePath,
        [hashtable]$EnvValues,
        [string]$MySqlExe = 'mysql'
    )

    Assert-CommandAvailable -CommandName $MySqlExe
    $dbName = $EnvValues['DB_NAME']
    $dbHost = $EnvValues['DB_HOST']
    $dbPort = $EnvValues['DB_PORT']
    $dbUser = $EnvValues['DB_USER']
    $dbPassword = $EnvValues['DB_PASSWORD']

    $dropAndCreate = @"
DROP DATABASE IF EXISTS `$dbName`;
CREATE DATABASE `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
"@

    $arguments = @(
        "--host=$dbHost"
        "--port=$dbPort"
        "--user=$dbUser"
        '--default-character-set=utf8mb4'
    )
    if (-not [string]::IsNullOrEmpty($dbPassword)) {
        $arguments += "--password=$dbPassword"
    }

    $dropAndCreate | & $MySqlExe @arguments
    Invoke-MySqlFile -MySqlExe $MySqlExe -Host $dbHost -Port $dbPort -Database $dbName -User $dbUser -Password $dbPassword -FilePath (Join-Path $ReleasePath 'src/database/schema.sql')
    Invoke-MySqlFile -MySqlExe $MySqlExe -Host $dbHost -Port $dbPort -Database $dbName -User $dbUser -Password $dbPassword -FilePath (Join-Path $ReleasePath 'src/database/seed.sql')
    Invoke-SeedAssets -ReleasePath $ReleasePath
}

function Test-ReleaseLayout {
    param([string]$ReleasePath)

    $requiredPaths = @(
        'src/public/index.php'
        'src/config/config.php'
        '.env'
    )

    foreach ($relativePath in $requiredPaths) {
        $fullPath = Join-Path $ReleasePath $relativePath
        if (-not (Test-Path $fullPath)) {
            throw "Release validation failed. Missing: $relativePath"
        }
    }
}

function Update-CurrentCopy {
    param(
        [string]$ReleasePath,
        [string]$CurrentPath
    )

    Reset-Directory -Path $CurrentPath
    Get-ChildItem -Path $ReleasePath -Force | ForEach-Object {
        Copy-Item -Path $_.FullName -Destination $CurrentPath -Recurse -Force
    }
}

function Restart-ApacheIfRequested {
    param([string]$ServiceName)
    if ([string]::IsNullOrWhiteSpace($ServiceName)) {
        Write-DeployLog 'Skipping Apache restart because no service name was provided.'
        return
    }

    Restart-Service -Name $ServiceName -ErrorAction Stop
}

function Get-SafePreviousVersions {
    param(
        [string]$ReleasesPath,
        [string]$CurrentRelease
    )

    if (-not (Test-Path $ReleasesPath)) {
        return @()
    }

    return Get-ChildItem -Path $ReleasesPath -Directory |
        Sort-Object Name -Descending |
        Where-Object { $_.Name -ne $CurrentRelease } |
        Select-Object -ExpandProperty Name
}

function Remove-PreviewArtifacts {
    param(
        [string]$PreviewRoot,
        [string]$PrNumber,
        [hashtable]$DbConfig,
        [string]$MySqlExe = 'mysql'
    )

    Assert-NumericValue -Value $PrNumber -Label 'PR number'

    $previewPath = Join-Path $PreviewRoot "pr-$PrNumber"
    if ($previewPath -notmatch 'previews[\\/]pr-\d+$') {
        throw "Refusing to remove unexpected preview path: $previewPath"
    }

    if (Test-Path $previewPath) {
        Remove-Item -Path $previewPath -Recurse -Force
    }

    $dbName = "cartly_preview_pr_$PrNumber"
    if ($dbName -notmatch '^cartly_preview_pr_\d+$') {
        throw "Refusing to drop unexpected database: $dbName"
    }

    Assert-CommandAvailable -CommandName $MySqlExe
    $arguments = @(
        "--host=$($DbConfig['DB_HOST'])"
        "--port=$($DbConfig['DB_PORT'])"
        "--user=$($DbConfig['DB_USER'])"
        '--default-character-set=utf8mb4'
    )
    if (-not [string]::IsNullOrEmpty($DbConfig['DB_PASSWORD'])) {
        $arguments += "--password=$($DbConfig['DB_PASSWORD'])"
    }

    "DROP DATABASE IF EXISTS `$dbName`;" | & $MySqlExe @arguments
}
