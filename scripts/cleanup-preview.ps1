[CmdletBinding()]
param(
    [Parameter(Mandatory = $true)]
    [string]$PrNumber,
    [string]$PreviewRoot = 'C:\xampp\htdocs\cartly\previews',
    [string]$DbHost = '127.0.0.1',
    [string]$DbPort = '3306',
    [string]$DbUser = 'root',
    [string]$DbPassword = ''
)

. (Join-Path $PSScriptRoot 'Cartly.Deploy.Common.ps1')

Write-DeployLog "Cleaning preview deployment for PR $PrNumber."
Remove-PreviewArtifacts -PreviewRoot $PreviewRoot -PrNumber $PrNumber -DbConfig @{
    DB_HOST = $DbHost
    DB_PORT = $DbPort
    DB_USER = $DbUser
    DB_PASSWORD = $DbPassword
}
Write-DeployLog "Preview cleanup completed for PR $PrNumber."
