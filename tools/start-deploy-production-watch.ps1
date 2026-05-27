# Start deploy-production-watch.ps1 (auto FTP on every file create/edit).
$watch = Join-Path $PSScriptRoot 'deploy-production-watch.ps1'
$pidFile = Join-Path (Split-Path $PSScriptRoot -Parent) '.local\deploy-production-watch.pid'

if (Test-Path $pidFile) {
	$oldPid = Get-Content -LiteralPath $pidFile -ErrorAction SilentlyContinue | Select-Object -First 1
	if ($oldPid -match '^\d+$') {
		$proc = Get-Process -Id ([int]$oldPid) -ErrorAction SilentlyContinue
		if ($proc) {
			Write-Host "Watch already running (PID $oldPid). Stop: Stop-Process -Id $oldPid"
			exit 0
		}
	}
}

$p = Start-Process -FilePath 'powershell.exe' -ArgumentList @(
	'-NoProfile', '-ExecutionPolicy', 'Bypass', '-NoExit', '-File', $watch
) -WindowStyle Minimized -PassThru

$p.Id | Set-Content -LiteralPath $pidFile -Encoding ASCII
Write-Host "Production auto-deploy started (PID $($p.Id))."
Write-Host 'Rules: global/** + site/** except ini/ and imagenes/'
