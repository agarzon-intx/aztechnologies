# Auto-deploy: on every create/edit under global/ or site folders, FTP upload after debounce.
# Excludes site ini/, site imagenes/, tools, logs, tmp.
# Usage: .\deploy-production-watch.ps1  |  .\start-deploy-production-watch.ps1 (background)
param([int]$DebounceSeconds = 3)

$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot 'deploy-production-common.ps1')

$config = Get-AzProductionDeployConfig
$repo = $config.Repo
$deployScript = Join-Path $PSScriptRoot 'deploy-production-files.ps1'

$global:AzDeployPending = [System.Collections.Concurrent.ConcurrentDictionary[string, byte]]::new()
$global:AzDeployTimer = New-Object System.Timers.Timer
$global:AzDeployTimer.Interval = [Math]::Max(1000, $DebounceSeconds * 1000)
$global:AzDeployTimer.AutoReset = $false

Register-ObjectEvent -InputObject $global:AzDeployTimer -EventName Elapsed -SourceIdentifier 'AzDeployFlush' -Action {
	$batch = @($global:AzDeployPending.Keys)
	if ($batch.Count -lt 1) { return }
	$global:AzDeployPending.Clear()
	$script = $using:deployScript
	Write-Host ("[{0}] Deploy {1} file(s)..." -f (Get-Date -Format 'HH:mm:ss'), $batch.Count)
	& powershell.exe -NoProfile -ExecutionPolicy Bypass -File $script -Files $batch -Quiet
} | Out-Null

$onFileEvent = {
	$path = $Event.SourceEventArgs.FullPath
	if (-not (Test-Path -LiteralPath $path -PathType Leaf)) { return }
	$cfg = $using:config
	$root = $cfg.Repo
	$full = [System.IO.Path]::GetFullPath($path)
	$rootFull = [System.IO.Path]::GetFullPath($root)
	if (-not $full.StartsWith($rootFull, [StringComparison]::OrdinalIgnoreCase)) { return }
	$rel = $full.Substring($rootFull.Length).TrimStart('\').Replace('\', '/')
	if (-not (Test-AzProductionPromotePath -Rel $rel -Config $cfg)) { return }
	[void]$global:AzDeployPending.TryAdd($rel, 0)
	$global:AzDeployTimer.Stop()
	$global:AzDeployTimer.Start()
}

$watchRoots = @(
	(Join-Path $repo 'global')
) + @($config.Sites | ForEach-Object { Join-Path $repo $_ })

Write-Host 'Production auto-deploy (active rules)'
Write-Host "  Repo:   $repo"
Write-Host "  Target: $($config.ProductionBase)"
Write-Host '  Deploy: global/** (all new/edited)'
Write-Host '  Deploy: {site}/** except ini/ and imagenes/'
Write-Host "  Debounce: ${DebounceSeconds}s"
Write-Host '  Press Ctrl+C to stop.'
Write-Host ''

$watchers = @()
foreach ($root in $watchRoots) {
	if (-not (Test-Path -LiteralPath $root)) {
		Write-Host "SKIP missing $root"
		continue
	}
	$w = New-Object System.IO.FileSystemWatcher
	$w.Path = $root
	$w.IncludeSubdirectories = $true
	$w.EnableRaisingEvents = $true
	$w.NotifyFilter = [IO.NotifyFilters]'FileName, LastWrite, CreationTime'
	Register-ObjectEvent -InputObject $w -EventName Changed -SourceIdentifier ("AzDeployCh_" + (Get-Random)) -Action $onFileEvent | Out-Null
	Register-ObjectEvent -InputObject $w -EventName Created -SourceIdentifier ("AzDeployCr_" + (Get-Random)) -Action $onFileEvent | Out-Null
	Register-ObjectEvent -InputObject $w -EventName Renamed -SourceIdentifier ("AzDeployRn_" + (Get-Random)) -Action $onFileEvent | Out-Null
	$watchers += $w
	Write-Host "Watching $root"
}

if ($watchers.Count -lt 1) {
	throw 'No watch folders found.'
}

try {
	while ($true) { Start-Sleep -Seconds 3600 }
} finally {
	foreach ($w in $watchers) {
		$w.EnableRaisingEvents = $false
		$w.Dispose()
	}
	$global:AzDeployTimer.Stop()
	$global:AzDeployTimer.Dispose()
	Get-EventSubscriber | Where-Object { $_.SourceIdentifier -like 'AzDeploy*' } | ForEach-Object {
		Unregister-Event -SubscriptionId $_.SubscriptionId -ErrorAction SilentlyContinue
	}
}
