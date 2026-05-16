# Recreate NTFS junctions for each site -> global (Windows / Laragon).
# After clone/pull, Git may check out ../global/* as symlinks; if checkout fails or you prefer
# junctions, run: powershell -ExecutionPolicy Bypass -File tools\recreate-site-junctions.ps1
#
# PERMANENT (Linux / FTP / cPanel): each site's imagenes/ must be a symbolic link
# to that site's Production imagenes/ — not a duplicated directory. This script
# does not create imagenes links; tools/deploy-development-sftp.ps1 does (requires
# SFTP_PRODUCTION_BASE in .local/sftp-development.env). See tools/IMAGENES-SYMLINK-REQUIRED.txt
$ErrorActionPreference = 'Stop'
$root = Split-Path $PSScriptRoot -Parent
$globalRoot = Join-Path $root 'global'
if (-not (Test-Path (Join-Path $globalRoot 'ajax'))) {
	throw "global/ajax not found; run from repo root (expected under: $root)"
}
$sites = @('elite', 'huskies', 'lidep', 'nuestrodeporte', 'vollidep', 'voleibalmetepec')
$links = @('ajax', 'assets', 'config', 'css', 'Form', 'include', 'javascript', 'languages', 'objects')
foreach ($s in $sites) {
	foreach ($l in $links) {
		$p = Join-Path (Join-Path $root $s) $l
		$t = Join-Path $globalRoot $l
		if (-not (Test-Path $t)) { Write-Warning "Skip missing target: $t"; continue }
		if (Test-Path -LiteralPath $p) {
			$it = Get-Item -LiteralPath $p -Force
			if ($it.LinkType) { cmd /c "rmdir `"$p`"" 2>$null }
			else { Remove-Item -LiteralPath $p -Recurse -Force }
		}
		cmd /c "mklink /J `"$p`" `"$t`"" | Out-Null
	}
}
Write-Host "Junctions OK under: $root"
