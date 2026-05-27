<?php
/**
 * Bridge for code under global/: loads the real site bootstrap from
 * DOCUMENT_ROOT/site_paths.php, or APP_SITE_ROOT/site_paths.php, or the first
 * sibling directory of global/ that contains a readable site_paths.php (not this file).
 * Multiple site folders (including via symlinks) are supported; set APP_SITE_ROOT to pick one.
 * Do not define APP_* constants here.
 */
if (defined('APP_SITE_ROOT')) {
	return;
}

$bridgeFile = __FILE__;
$bridgeReal = @realpath($bridgeFile);

$doc = !empty($_SERVER['DOCUMENT_ROOT']) ? rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') : '';
$candidate = ($doc !== '') ? $doc . DIRECTORY_SEPARATOR . 'site_paths.php' : '';
$here = @realpath($bridgeFile);
if ($candidate !== '' && is_readable($candidate)) {
	$target = @realpath($candidate);
	if ($target !== false && $here !== false && $target !== $here) {
		require_once $candidate;
		return;
	}
}

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

$envRoot = getenv('APP_SITE_ROOT');
if (is_string($envRoot) && $envRoot !== '') {
	$envRoot = rtrim($envRoot, '/\\');
	if ($trySitePaths($envRoot . DIRECTORY_SEPARATOR . 'site_paths.php')) {
		return;
	}
}

$repoRoot = dirname(__DIR__);
$dirs = @scandir($repoRoot);
if (is_array($dirs)) {
	sort($dirs, SORT_STRING);
	foreach ($dirs as $entry) {
		if ($entry === '.' || $entry === '..') {
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
