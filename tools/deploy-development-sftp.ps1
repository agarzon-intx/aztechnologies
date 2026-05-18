# Deploy tracked tree to Development over SFTP while preserving Git symlinks.
# Uses: git archive (excludes each site's imagenes/ and ini/) -> pscp -> remote tar -xzf
#       then recreates imagenes -> Production and ini -> Production symlinks on the server.
# Requires: PuTTY pscp/plink, .local/sftp-development.env
#
# .local/sftp-development.env must include:
#   SFTP_HOST, SFTP_USER, SFTP_PASSWORD
#   SFTP_REMOTE_PATH   = Development root (e.g. /home1/.../public_html/Development)
#   SFTP_PRODUCTION_BASE = Production parent (e.g. /home1/.../public_html/Production)
#     so each site links: $SFTP_REMOTE_PATH/<site>/imagenes -> $SFTP_PRODUCTION_BASE/<site>/imagenes
#     and:                 $SFTP_REMOTE_PATH/<site>/ini      -> $SFTP_PRODUCTION_BASE/<site>/ini
#
# See: tools/IMAGENES-SYMLINK-REQUIRED.txt
$ErrorActionPreference = 'Stop'
$repo = Split-Path $PSScriptRoot -Parent
Set-Location $repo
$envFile = Join-Path $repo '.local\sftp-development.env'
if (-not (Test-Path $envFile)) { throw "Missing $envFile" }
$cfg = @{}
Get-Content $envFile | ForEach-Object {
	if ($_ -match '^\s*#' -or $_ -match '^\s*$') { return }
	if ($_ -match '^([^=]+)=(.*)$') { $cfg[$matches[1].Trim()] = $matches[2].Trim() }
}
$h = $cfg['SFTP_HOST']; $u = $cfg['SFTP_USER']; $pw = $cfg['SFTP_PASSWORD']
$remoteBase = $cfg['SFTP_REMOTE_PATH'].TrimEnd('/').Replace('\', '/')
$productionBase = ([string]$cfg['SFTP_PRODUCTION_BASE']).TrimEnd('/').Replace('\', '/')
if (-not $h -or -not $u -or -not $pw -or -not $remoteBase) { throw 'SFTP_HOST, SFTP_USER, SFTP_PASSWORD, SFTP_REMOTE_PATH required in env file' }
if (-not $productionBase) { throw 'SFTP_PRODUCTION_BASE is required (parent path of Production site roots, same layout as Development). Example: /home1/aztechn1/public_html/Production' }
$pscp = 'C:\Program Files\PuTTY\pscp.exe'
$plink = 'C:\Program Files\PuTTY\plink.exe'
if (-not (Test-Path $pscp)) { throw "Install PuTTY or set path to pscp.exe: $pscp" }
if (-not (Test-Path $plink)) { throw "Install PuTTY or set path to plink.exe: $plink" }

$sites = @('elite', 'huskies', 'lidep', 'nuestrodeporte', 'vollidep', 'voleibalmetepec')

$ref = if ($args[0]) { $args[0] } else { 'HEAD' }
$tarName = 'aztechnologies-Development-deploy.tgz'
$localTar = Join-Path ([System.IO.Path]::GetTempPath()) $tarName
Write-Host "git archive $ref -> $localTar (excluding each site's imagenes/ and ini/)"
$archiveArgs = @('--format=tar.gz', '-o', $localTar, $ref, '--', '.')
foreach ($s in $sites) {
	$archiveArgs += ":(exclude)$s/imagenes"
	$archiveArgs += ":(exclude)$s/ini"
}
& git archive @archiveArgs
if ($LASTEXITCODE -ne 0) { throw 'git archive failed' }

$remoteTar = "$remoteBase/$tarName"
Write-Host "Uploading to ${u}@${h}:$remoteTar ..."
& $pscp -batch -pw $pw $localTar "${u}@${h}:$remoteTar"
if ($LASTEXITCODE -ne 0) { throw 'pscp failed' }

$remoteCmd = "cd $remoteBase && tar -xzf $tarName && rm -f $tarName"
Write-Host 'Extracting on server (symlinks preserved; imagenes/ and ini/ omitted from archive)...'
& $plink -batch -pw $pw "${u}@${h}" $remoteCmd
if ($LASTEXITCODE -ne 0) { throw 'remote tar failed' }

$linkParts = @()
foreach ($s in $sites) {
	$prodI = "$productionBase/$s/imagenes"
	$devI = "$remoteBase/$s/imagenes"
	$linkParts += "if [ -d '$prodI' ]; then rm -rf '$devI' && ln -s '$prodI' '$devI'; else echo 'WARN: Production imagenes missing, skip symlink: $prodI'; fi"
}
$remoteLink = $linkParts -join '; '
Write-Host "Creating imagenes -> Production symlinks under each site..."
& $plink -batch -pw $pw "${u}@${h}" $remoteLink
if ($LASTEXITCODE -ne 0) { throw 'remote imagenes symlink step failed' }

$iniParts = @()
foreach ($s in $sites) {
	$prodIni = "$productionBase/$s/ini"
	$devIni = "$remoteBase/$s/ini"
	$iniParts += "if [ -d '$prodIni' ]; then rm -rf '$devIni' && ln -s '$prodIni' '$devIni'; else echo 'WARN: Production ini missing, skip symlink: $prodIni'; fi"
}
$remoteIni = $iniParts -join '; '
Write-Host "Creating ini -> Production symlinks under each site..."
& $plink -batch -pw $pw "${u}@${h}" $remoteIni
if ($LASTEXITCODE -ne 0) { throw 'remote ini symlink step failed' }

Remove-Item $localTar -Force -ErrorAction SilentlyContinue
Write-Host 'Done.'
