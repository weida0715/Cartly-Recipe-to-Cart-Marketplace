[CmdletBinding()]
param(
    [string]$RepoRoot = (Split-Path -Parent $PSScriptRoot),
    [string]$TargetRef = 'origin/main',
    [string]$BranchName = 'main',
    [string]$ProductionRoot = 'C:\xampp\htdocs\cartly\prod',
    [string]$ApacheServiceName = '',
    [string]$DbHost = '127.0.0.1',
    [string]$DbPort = '3306',
    [string]$DbName = 'cartly_prod',
    [string]$DbUser = 'root',
    [string]$DbPassword = '',
    [string]$AppBasePath = '',
    [string]$AppEnv = 'production'
)

. (Join-Path $PSScriptRoot 'Cartly.Deploy.Common.ps1')

Write-DeployLog 'Starting production deployment.'
Assert-CommandAvailable -CommandName 'git'
Assert-CommandAvailable -CommandName 'php'

$releasesPath = Join-Path $ProductionRoot 'releases'
$currentPath = Join-Path $ProductionRoot 'current'
$versioningPath = Join-Path $ProductionRoot 'VERSIONING'

Push-Location $RepoRoot
try {
    & git fetch --all --prune
    $targetCommit = (git rev-parse $TargetRef).Trim()
}
finally {
    Pop-Location
}

$releaseId = Get-ReleaseId -CommitSha $targetCommit
$releasePath = Join-Path $releasesPath $releaseId
$versioning = Get-CurrentVersionMetadata -VersioningPath $versioningPath
$previousCommit = $versioning['CURRENT_COMMIT']
$databaseChanged = Test-DatabaseChanges -RepoRoot $RepoRoot -PreviousCommit $previousCommit -TargetCommit $targetCommit

if (-not (Test-Path $releasesPath)) {
    New-Item -ItemType Directory -Path $releasesPath -Force | Out-Null
}

Export-GitRelease -RepoRoot $RepoRoot -CommitSha $targetCommit -DestinationPath $releasePath

Write-EnvFile -Path (Join-Path $releasePath '.env') -Values @{
    APP_BASE_PATH = $AppBasePath
    APP_ENV = $AppEnv
    APP_NAME = 'Cartly'
    DB_HOST = $DbHost
    DB_NAME = $DbName
    DB_PASSWORD = $DbPassword
    DB_PORT = $DbPort
    DB_USER = $DbUser
}

Install-ComposerDependencies -ReleasePath $releasePath
Test-ReleaseLayout -ReleasePath $releasePath

if ($databaseChanged) {
    Write-DeployLog 'Database files changed. Production deploy preserves live data, so no destructive schema or seed reset will run automatically.'
}
else {
    Write-DeployLog 'No database file changes detected.'
}

Update-CurrentCopy -ReleasePath $releasePath -CurrentPath $currentPath
Write-VersioningFile -Path $versioningPath -ReleaseId $releaseId -CommitSha $targetCommit -Branch $BranchName -PreviousVersions (Get-SafePreviousVersions -ReleasesPath $releasesPath -CurrentRelease $releaseId)
Restart-ApacheIfRequested -ServiceName $ApacheServiceName

Write-DeployLog "Production deployment completed. Active release: $releaseId"
