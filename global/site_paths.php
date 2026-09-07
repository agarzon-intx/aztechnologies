<?php
/**
 * Bridge for code under global/: loads the real site bootstrap.
 *
 * Resolution order:
 * 1) DOCUMENT_ROOT/site_paths.php (addon-domain docroots)
 * 2) APP_SITE_ROOT env
 * 3) Walk up from SCRIPT_FILENAME (keeps symlink path under /Production/<site>/...)
 * 4) First sibling of global/ that has site_paths.php (last-resort fallback)
 *
 * Do not define APP_* constants here.
 */
if (defined('APP_SITE_ROOT')) {
	return;
}

$bridgeFile = __FILE__;
$bridgeReal = @realpath($bridgeFile);

$trySitePaths = static function (string $path) use ($bridgeReal): bool {
	if ($path === '' || !is_readable($path)) {
		return false;
	}
	if ($bridgeReal !== false) {
		$rp = @realpath($path);
		if ($rp !== false && $rp === $bridgeReal) {
			return false;
		}
	}
	require_once $path;
	return true;
};

$doc = !empty($_SERVER['DOCUMENT_ROOT']) ? rtrim((string) $_SERVER['DOCUMENT_ROOT'], '/\\') : '';
$candidate = ($doc !== '') ? $doc . DIRECTORY_SEPARATOR . 'site_paths.php' : '';
if ($candidate !== '' && $trySitePaths($candidate)) {
	return;
}

$envRoot = getenv('APP_SITE_ROOT');
if (is_string($envRoot) && $envRoot !== '') {
	$envRoot = rtrim($envRoot, '/\\');
	if ($trySitePaths($envRoot . DIRECTORY_SEPARATOR . 'site_paths.php')) {
		return;
	}
}

// Prefer the request path (symlink-aware) so /Production/voleyMVP/javascript/*.php
// resolves to voleyMVP/, not the first alphabetical sibling of global/ (e.g. aztflag).
$script = !empty($_SERVER['SCRIPT_FILENAME']) ? (string) $_SERVER['SCRIPT_FILENAME'] : '';
if ($script !== '') {
	$d = dirname($script);
	$prev = null;
	for ($i = 0; $i < 24 && $d !== '' && $d !== $prev; $i++) {
		if ($trySitePaths($d . DIRECTORY_SEPARATOR . 'site_paths.php')) {
			return;
		}
		$prev = $d;
		$d = dirname($d);
	}
}

$repoRoot = dirname(__DIR__);
$dirs = @scandir($repoRoot);
if (is_array($dirs)) {
	sort($dirs, SORT_STRING);
	foreach ($dirs as $entry) {
		if ($entry === '.' || $entry === '..' || $entry === 'global') {
			continue;
		}
		$subdir = $repoRoot . DIRECTORY_SEPARATOR . $entry;
		if (!is_dir($subdir)) {
			continue;
		}
		$p = $subdir . DIRECTORY_SEPARATOR . 'site_paths.php';
		if ($trySitePaths($p)) {
			return;
		}
	}
}
