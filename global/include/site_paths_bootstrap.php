<?php
/**
 * Shared bootstrap for every site: include_path, then encoding_compat.
 * Each site's site_paths.php sets APP_SITE_BOOTSTRAP_ROOT to that site's directory
 * and requires this file once.
 */
if (defined('APP_SITE_ROOT')) {
	// Another bootstrap may have defined APP_SITE_ROOT without loading helpers (stale deploy).
	if (!function_exists('az_utf8_decode')) {
		require_once dirname(APP_SITE_ROOT) . DIRECTORY_SEPARATOR . 'global' . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'encoding_compat.php';
	}
	return;
}
if (!defined('APP_SITE_BOOTSTRAP_ROOT') || !is_dir(APP_SITE_BOOTSTRAP_ROOT)) {
	return;
}

define('APP_SITE_ROOT', APP_SITE_BOOTSTRAP_ROOT);
define(
	'APP_INI_FILE',
	APP_SITE_ROOT . DIRECTORY_SEPARATOR . 'ini' . DIRECTORY_SEPARATOR . 'config.ini'
);
$sep = PATH_SEPARATOR;
$root = APP_SITE_ROOT;
$globalInclude = dirname($root) . DIRECTORY_SEPARATOR . 'global' . DIRECTORY_SEPARATOR . 'include';
$globalMembersite = $globalInclude . DIRECTORY_SEPARATOR . 'membersite_config.php';
$globalSeg = (is_dir($globalInclude) || is_file($globalMembersite)) ? ($globalInclude . $sep) : '';
set_include_path(
	$root . $sep
	. $root . DIRECTORY_SEPARATOR . 'include' . $sep
	. $globalSeg
	. $root . DIRECTORY_SEPARATOR . 'config' . $sep
	. $root . DIRECTORY_SEPARATOR . 'languages' . $sep
	. $root . DIRECTORY_SEPARATOR . 'ini' . $sep
	. get_include_path()
);
// UTF-8 helpers for PDFs; require (do not skip) so a missing file fails loudly on deploy.
if (is_dir($globalInclude)) {
	require_once $globalInclude . DIRECTORY_SEPARATOR . 'encoding_compat.php';
}
