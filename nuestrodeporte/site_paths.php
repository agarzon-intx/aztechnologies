<?php
/**
 * Ensures include/config/languages/ini are on include_path when the host
 * does not set them (e.g. Laragon vs cPanel MultiPHP).
 */
if (!defined('APP_SITE_ROOT')) {
	define('APP_SITE_ROOT', __DIR__);
	define(
		'APP_INI_FILE',
		APP_SITE_ROOT . DIRECTORY_SEPARATOR . 'ini' . DIRECTORY_SEPARATOR . 'config.ini'
	);
	$sep = PATH_SEPARATOR;
	$root = APP_SITE_ROOT;
	set_include_path(
		$root . $sep
		. $root . DIRECTORY_SEPARATOR . 'include' . $sep
		. $root . DIRECTORY_SEPARATOR . 'config' . $sep
		. $root . DIRECTORY_SEPARATOR . 'languages' . $sep
		. $root . DIRECTORY_SEPARATOR . 'ini' . $sep
		. get_include_path()
	);
}
?>
