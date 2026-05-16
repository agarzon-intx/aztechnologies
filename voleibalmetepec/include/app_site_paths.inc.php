<?php
/**
 * Walk up from $__APP_SITE_PATHS_START__ and require the first readable site_paths.php.
 * Callers must set $__APP_SITE_PATHS_START__ = __DIR__ before loading this file.
 */
if (defined('APP_SITE_ROOT')) {
	return;
}
if (!isset($__APP_SITE_PATHS_START__) || !is_string($__APP_SITE_PATHS_START__)) {
	return;
}
$d = $__APP_SITE_PATHS_START__;
while ($d !== dirname($d)) {
	$p = $d . DIRECTORY_SEPARATOR . 'site_paths.php';
	if (is_readable($p)) {
		require_once $p;
		break;
	}
	$d = dirname($d);
}
unset($__APP_SITE_PATHS_START__);
?>