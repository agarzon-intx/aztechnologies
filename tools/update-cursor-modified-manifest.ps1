# Refresh .cursor/modified-files.json from git status (files changed outside Cursor / IDE).
# Run from repo root:  .\tools\update-cursor-modified-manifest.ps1
$ErrorActionPreference = 'Stop'
$repo = Split-Path $PSScriptRoot -Parent
Set-Location $repo

$excludePatterns = @(
	'logs/',
	'elite/tmp/',
	'tools/output_',
	'tools/.promote',
	'global/include/imagick/',
	'.git/'
)

function Test-ExcludePath {
	param([string]$Path)
	foreach ($e in $excludePatterns) {
		if ($Path -like "*$e*") { return $true }
	}
	return $false
}

$status = git status --porcelain 2>$null
if (-not $status) {
	throw 'Not a git repository or git failed.'
}

$items = [System.Collections.Generic.List[object]]::new()
foreach ($line in $status) {
	if ([string]::IsNullOrWhiteSpace($line) -or $line.Length -lt 4) { continue }
	$st = $line.Substring(0, 2).Trim()
	$path = ($line.Substring(3).Trim() -replace '\\', '/')
	if (Test-ExcludePath $path) { continue }
	$items.Add([ordered]@{ status = $st; path = $path })
}

$manifest = [ordered]@{
	generatedAt = (Get-Date).ToString('o')
	repoRoot    = $repo
	branch      = (git rev-parse --abbrev-ref HEAD 2>$null)
	fileCount   = $items.Count
	files       = $items
}

$cursorDir = Join-Path $repo '.cursor'
New-Item -ItemType Directory -Force -Path $cursorDir | Out-Null
$outPath = Join-Path $cursorDir 'modified-files.json'
$manifest | ConvertTo-Json -Depth 6 | Set-Content -LiteralPath $outPath -Encoding UTF8
Write-Host "Wrote $($items.Count) entries to $outPath"
