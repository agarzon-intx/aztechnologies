# Deploy all new/edited git files that match Production rules (FTP).
# Usage: .\deploy-production-promote.ps1
param([switch]$Quiet)

$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot 'deploy-production-common.ps1')

$config = Get-AzProductionDeployConfig
$files = @(Get-AzProductionGitPromotableFiles -Config $config)

if ($files.Count -lt 1) {
	Write-Host 'Nothing to deploy (no promotable M/?? files in git).'
	exit 0
}

Write-Host "Deploying $($files.Count) file(s) to $($config.ProductionBase) ..."
$result = Invoke-AzProductionDeployFiles -Files $files -Quiet:$Quiet

$reportPath = Join-Path $config.Repo ".local\deploy-production-promote-$(Get-Date -Format 'yyyyMMdd-HHmmss').txt"
@(
	"Deploy to Production: $($config.ProductionBase)",
	"Uploaded: $($result.Uploaded.Count) | Failed: $($result.Failed.Count) | Skipped: $($result.Skipped.Count)",
	'',
	'=== UPLOADED ==='
) + $result.Uploaded + @('', '=== FAILED ===') + $result.Failed + @('', '=== SKIPPED ===') + $result.Skipped |
	Set-Content -Path $reportPath -Encoding UTF8

Write-Host ""
Write-Host "Report: $reportPath"
if ($result.Failed.Count -gt 0) { exit 1 }
