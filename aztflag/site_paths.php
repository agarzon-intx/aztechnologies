<?php
/**
 * Ensures include/config/languages/ini are on include_path when the host
 * does not set them (e.g. Laragon vs cPanel MultiPHP).
 * @see global/include/site_paths_bootstrap.php
 */
if (!defined('APP_SITE_BOOTSTRAP_ROOT')) {
	define('APP_SITE_BOOTSTRAP_ROOT', __DIR__);
}
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'global' . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'site_paths_bootstrap.php';
