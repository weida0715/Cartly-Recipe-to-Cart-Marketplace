[CmdletBinding()]
param(
    [Parameter(Mandatory = $true)]
    [string]$PrNumber,
    [string]$RepoRoot = (Split-Path -Parent $PSScriptRoot),
    [string]$Ref = 'HEAD',
    [string]$PreviewRoot = 'C:\xampp\htdocs\cartly\previews',
    [string]$ApacheServiceName = '',
    [string]$DbHost = '127.0.0.1',
    [string]$DbPort = '3306',
    [string]$DbUser = 'root',
    [string]$DbPassword = '',
    [string]$AppEnv = 'preview'
)

. (Join-Path $PSScriptRoot 'Cartly.Deploy.Common.ps1')

Assert-NumericValue -Value $PrNumber -Label 'PR number'
Write-DeployLog "Starting preview deployment for PR $PrNumber."
Assert-CommandAvailable -CommandName 'git'
Assert-CommandAvailable -CommandName 'php'

$previewPath = Join-Path $PreviewRoot "pr-$PrNumber"
$currentPath = Join-Path $previewPath 'current'
$dbName = "cartly_preview_pr_$PrNumber"
$appBasePath = "/pr-$PrNumber"

Push-Location $RepoRoot
try {
    & git fetch --all --prune
    $targetCommit = (git rev-parse $Ref).Trim()
}
finally {
    Pop-Location
}

Export-GitRelease -RepoRoot $RepoRoot -CommitSha $targetCommit -DestinationPath $currentPath

$envValues = @{
    APP_BASE_PATH = $appBasePath
    APP_ENV = $AppEnv
    APP_NAME = 'Cartly'
    DB_HOST = $DbHost
    DB_NAME = $dbName
    DB_PASSWORD = $DbPassword
    DB_PORT = $DbPort
    DB_USER = $DbUser
}

Write-EnvFile -Path (Join-Path $currentPath '.env') -Values $envValues
Install-ComposerDependencies -ReleasePath $currentPath
Invoke-PreviewDatabaseRefresh -ReleasePath $currentPath -EnvValues $envValues
Test-ReleaseLayout -ReleasePath $currentPath
Restart-ApacheIfRequested -ServiceName $ApacheServiceName

Write-DeployLog "Preview deployment completed for PR $PrNumber at commit $targetCommit."
