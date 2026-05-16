# Upload imagen.png + general.png (default) to each site's Production imagenes/
# (the target of Development's imagenes -> Production symlink).
# Source: voleibalmetepec/imagenes (canonical icons).
# Optional args (order-free): site name, and/or a .png filename (e.g. general.png only).
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

# Optional args (order-free): site name (e.g. vollidep) and/or a filename (e.g. general.png).
foreach ($a in $args) {
	if ($a -match '\.(png|PNG)$') {
		$files = @($a)
	} elseif ($a) {
		$sites = @($a)
	}
}
foreach ($f in $files) {
	$p = Join-Path $localDir $f
	if (-not (Test-Path $p)) { throw "Missing local file: $p" }
}

$fail = @()
foreach ($s in $sites) {
	foreach ($f in $files) {
		$local = Join-Path $localDir $f
		$remote = "${u}@${h}:$productionBase/$s/imagenes/$f"
		$ok = $false
		for ($attempt = 1; $attempt -le 3; $attempt++) {
			if ($attempt -gt 1) { Start-Sleep -Seconds 6 }
			& $pscp -batch -pw $pw $local $remote
			if ($LASTEXITCODE -eq 0) {
				$ok = $true
				Write-Host "OK $s $f"
				break
			}
		}
		if (-not $ok) { $fail += "${s}/${f}" }
		Start-Sleep -Seconds 2
	}
}
if ($fail.Count -gt 0) {
	Write-Host "Failed: $($fail -join ', ')"
	exit 1
}
Write-Host 'Done.'
