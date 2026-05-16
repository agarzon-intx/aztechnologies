# Deploy tracked tree to Development over SFTP while preserving Git symlinks.
# Uses: git archive (symlinks + content as in HEAD) -> pscp -> remote tar -xzf
# Requires: PuTTY pscp/plink, .local/sftp-development.env (see google_maps key example pattern)
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
if (-not $h -or -not $u -or -not $pw -or -not $remoteBase) { throw 'SFTP_HOST, SFTP_USER, SFTP_PASSWORD, SFTP_REMOTE_PATH required in env file' }
$pscp = 'C:\Program Files\PuTTY\pscp.exe'
$plink = 'C:\Program Files\PuTTY\plink.exe'
if (-not (Test-Path $pscp)) { throw "Install PuTTY or set path to pscp.exe: $pscp" }
if (-not (Test-Path $plink)) { throw "Install PuTTY or set path to plink.exe: $plink" }

$ref = if ($args[0]) { $args[0] } else { 'HEAD' }
$tarName = 'aztechnologies-Development-deploy.tgz'
$localTar = Join-Path ([System.IO.Path]::GetTempPath()) $tarName
Write-Host "git archive $ref -> $localTar"
& git archive --format=tar.gz -o $localTar $ref
if ($LASTEXITCODE -ne 0) { throw 'git archive failed' }

$remoteTar = "$remoteBase/$tarName"
Write-Host "Uploading to ${u}@${h}:$remoteTar ..."
& $pscp -batch -pw $pw $localTar "${u}@${h}:$remoteTar"
if ($LASTEXITCODE -ne 0) { throw 'pscp failed' }

$remoteCmd = "cd $remoteBase && tar -xzf $tarName && rm -f $tarName"
Write-Host 'Extracting on server (symlinks preserved)...'
& $plink -batch -pw $pw "${u}@${h}" $remoteCmd
if ($LASTEXITCODE -ne 0) { throw 'remote tar failed' }

Remove-Item $localTar -Force -ErrorAction SilentlyContinue
Write-Host 'Done.'
