# Upload imagen.png + general.png to each site's Production imagenes/
# (the target of Development's imagenes -> Production symlink).
# Source: voleibalmetepec/imagenes (canonical icons).
# Requires: PuTTY pscp, .local/sftp-development.env with SFTP_* + SFTP_PRODUCTION_BASE
$ErrorActionPreference = 'Stop'
$repo = Split-Path $PSScriptRoot -Parent
$envFile = Join-Path $repo '.local\sftp-development.env'
if (-not (Test-Path $envFile)) { throw "Missing $envFile" }
$cfg = @{}
Get-Content $envFile | ForEach-Object {
	if ($_ -match '^\s*#' -or $_ -match '^\s*$') { return }
	if ($_ -match '^([^=]+)=(.*)$') { $cfg[$matches[1].Trim()] = $matches[2].Trim() }
}
$h = $cfg['SFTP_HOST']; $u = $cfg['SFTP_USER']; $pw = $cfg['SFTP_PASSWORD']
$productionBase = ([string]$cfg['SFTP_PRODUCTION_BASE']).TrimEnd('/').Replace('\', '/')
if (-not $h -or -not $u -or -not $pw -or -not $productionBase) {
	throw 'SFTP_HOST, SFTP_USER, SFTP_PASSWORD, SFTP_PRODUCTION_BASE required in env file'
}
$pscp = 'C:\Program Files\PuTTY\pscp.exe'
if (-not (Test-Path $pscp)) { throw "Install PuTTY or adjust path: $pscp" }

$sites = @('elite', 'huskies', 'lidep', 'nuestrodeporte', 'vollidep', 'voleibalmetepec')
$localDir = Join-Path $repo 'voleibalmetepec\imagenes'
$files = @('imagen.png', 'general.png')
foreach ($f in $files) {
	$p = Join-Path $localDir $f
	if (-not (Test-Path $p)) { throw "Missing local file: $p" }
}

$sitesArg = $args[0]
if ($sitesArg) {
	$sites = @($sitesArg)
}

$fail = @()
foreach ($s in $sites) {
	foreach ($f in $files) {
		$local = Join-Path $localDir $f
		$remote = "${u}@${h}:$productionBase/$s/imagenes/$f"
		& $pscp -batch -pw $pw $local $remote
		if ($LASTEXITCODE -ne 0) { $fail += "${s}/${f}" }
		else { Write-Host "OK $s $f" }
	}
}
if ($fail.Count -gt 0) {
	Write-Host "Failed: $($fail -join ', ')"
	exit 1
}
Write-Host 'Done.'
