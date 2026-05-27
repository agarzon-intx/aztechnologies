# Upload specific repo files to Production (FTP). Used by the watcher and Cursor auto-deploy.
# Usage:
#   .\deploy-production-files.ps1 -Files global/include/flyer_ci_export.php
#   .\deploy-production-files.ps1 -Files elite/pdf/flyer-I.php,global/javascript/main.js.php
param(
	[Parameter(Mandatory = $true)]
	[string[]]$Files,
	[switch]$Quiet
)

$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot 'deploy-production-common.ps1')

$result = Invoke-AzProductionDeployFiles -Files $Files -Quiet:$Quiet
if ($result.Failed.Count -gt 0) { exit 1 }
