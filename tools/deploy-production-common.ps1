# Shared Production FTP upload (curl). Rules enforced in Test-AzProductionPromotePath.
# Dot-source from deploy-production-files.ps1, deploy-production-watch.ps1, deploy-production-promote.ps1

function Get-AzProductionDeployConfig {
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
		throw 'SFTP_HOST, SFTP_USER, SFTP_PASSWORD, SFTP_PRODUCTION_BASE required in .local/sftp-development.env'
	}
	$ftpRoot = if ($productionBase -match '/public_html/(.+)$') { "public_html/$($matches[1])" } else { $productionBase.TrimStart('/') }
	return @{
		Repo           = $repo
		Host           = $h
		User           = $u
		Password       = $pw
		FtpRoot        = $ftpRoot
		ProductionBase = $productionBase
		Sites          = @('elite', 'huskies', 'lidep', 'nuestrodeporte', 'vollidep', 'voleibalmetepec')
		# Repo paths never deployed
		ExcludePattern = '\\\.git\\|\\\.local\\|\\\.cursor\\|\\tools\\|\\logs\\|\\tmp\\|\.swp$|php_imagick\.dll$'
	}
}

function ConvertTo-AzProductionRelativePath {
	param([string]$Path, [hashtable]$Config)
	$repo = $Config.Repo
	$p = $Path
	if (-not [System.IO.Path]::IsPathRooted($p)) {
		$p = Join-Path $repo ($p -replace '/', [IO.Path]::DirectorySeparatorChar)
	}
	$p = [System.IO.Path]::GetFullPath($p)
	$root = [System.IO.Path]::GetFullPath($repo)
	if (-not $p.StartsWith($root, [StringComparison]::OrdinalIgnoreCase)) {
		return $null
	}
	return $p.Substring($root.Length).TrimStart('\').Replace('\', '/')
}

<#
	Production deploy rules:
	- global/**  new or edited files (except ExcludePattern)
	- {site}/** new or edited EXCEPT {site}/ini/** and {site}/imagenes/**
#>
function Test-AzProductionPromotePath {
	param([string]$Rel, [hashtable]$Config)
	if ([string]::IsNullOrWhiteSpace($Rel)) { return $false }
	$rel = $Rel.Replace('\', '/').TrimStart('/')
	$full = Join-Path $Config.Repo ($rel -replace '/', [IO.Path]::DirectorySeparatorChar)
	if ($full -match $Config.ExcludePattern) { return $false }

	if ($rel -like 'global/*') {
		return $true
	}

	foreach ($s in $Config.Sites) {
		if ($rel -like "$s/*" -or $rel -eq $s) {
			if ($rel -like "$s/ini/*" -or $rel -eq "$s/ini") {
				return $false
			}
			if ($rel -like "$s/imagenes/*" -or $rel -eq "$s/imagenes") {
				return $false
			}
			return $true
		}
	}
	return $false
}

function Send-AzProductionFile {
	param(
		[string]$RelativePath,
		[hashtable]$Config,
		[switch]$Quiet
	)
	$rel = $RelativePath.Replace('\', '/').TrimStart('/')
	if (-not (Test-AzProductionPromotePath -Rel $rel -Config $Config)) {
		return @{ Ok = $false; Skipped = $true; Rel = $rel; Reason = 'excluded' }
	}
	$local = Join-Path $Config.Repo ($rel -replace '/', [IO.Path]::DirectorySeparatorChar)
	if (-not (Test-Path -LiteralPath $local -PathType Leaf)) {
		return @{ Ok = $false; Skipped = $true; Rel = $rel; Missing = $true }
	}
	$remotePath = "$($Config.FtpRoot)/$rel".Replace('\', '/')
	$url = "ftp://$($Config.User):$($Config.Password)@$($Config.Host)/$remotePath"
	$ok = $false
	for ($attempt = 1; $attempt -le 3; $attempt++) {
		if ($attempt -gt 1) { Start-Sleep -Seconds 5 }
		$prevEap = $ErrorActionPreference
		$ErrorActionPreference = 'Continue'
		& curl.exe -sS --ftp-pasv --ftp-create-dirs -T $local $url 2>$null
		$code = $LASTEXITCODE
		$ErrorActionPreference = $prevEap
		if ($code -eq 0) { $ok = $true; break }
	}
	if (-not $Quiet) {
		if ($ok) { Write-Host "OK $rel" } else { Write-Host "FAIL $rel" }
	}
	return @{ Ok = $ok; Skipped = $false; Rel = $rel }
}

function Invoke-AzProductionDeployFiles {
	param(
		[string[]]$Files,
		[switch]$Quiet
	)
	$config = Get-AzProductionDeployConfig
	$uploaded = [System.Collections.Generic.List[string]]::new()
	$failed = [System.Collections.Generic.List[string]]::new()
	$skipped = [System.Collections.Generic.List[string]]::new()
	$seen = @{}
	foreach ($f in $Files) {
		$rel = ConvertTo-AzProductionRelativePath -Path $f -Config $config
		if ($null -eq $rel -or $seen.ContainsKey($rel)) { continue }
		$seen[$rel] = $true
		$result = Send-AzProductionFile -RelativePath $rel -Config $config -Quiet:$Quiet
		if ($result.Skipped) {
			$skipped.Add($rel) | Out-Null
		} elseif ($result.Ok) {
			$uploaded.Add($rel) | Out-Null
		} else {
			$failed.Add($rel) | Out-Null
		}
	}
	return @{
		Config   = $config
		Uploaded = $uploaded
		Failed   = $failed
		Skipped  = $skipped
	}
}

function Get-AzProductionGitPromotableFiles {
	param([hashtable]$Config)
	$repo = $Config.Repo
	$paths = [System.Collections.Generic.List[string]]::new()
	Push-Location $repo
	try {
		$lines = @(git status --porcelain 2>$null)
	} finally {
		Pop-Location
	}
	foreach ($line in $lines) {
		if ([string]::IsNullOrWhiteSpace($line)) { continue }
		$st = $line.Substring(0, 2).Trim()
		if ($st -ne 'M' -and $st -ne '??') { continue }
		$p = $line.Substring(3).Trim().Trim('"')
		if (-not (Test-AzProductionPromotePath -Rel $p -Config $Config)) {
			if (Test-Path -LiteralPath (Join-Path $repo $p) -PathType Container) {
				Get-ChildItem -LiteralPath (Join-Path $repo $p) -Recurse -File -ErrorAction SilentlyContinue | ForEach-Object {
					$rel = ConvertTo-AzProductionRelativePath -Path $_.FullName -Config $Config
					if ($null -ne $rel -and (Test-AzProductionPromotePath -Rel $rel -Config $Config)) {
						[void]$paths.Add($rel)
					}
				}
			}
			continue
		}
		if (Test-Path -LiteralPath (Join-Path $repo $p) -PathType Leaf) {
			[void]$paths.Add($p.Replace('\', '/'))
		} elseif (Test-Path -LiteralPath (Join-Path $repo $p) -PathType Container) {
			Get-ChildItem -LiteralPath (Join-Path $repo $p) -Recurse -File -ErrorAction SilentlyContinue | ForEach-Object {
				$rel = ConvertTo-AzProductionRelativePath -Path $_.FullName -Config $Config
				if ($null -ne $rel) { [void]$paths.Add($rel) }
			}
		}
	}
	return $paths | Sort-Object -Unique
}
