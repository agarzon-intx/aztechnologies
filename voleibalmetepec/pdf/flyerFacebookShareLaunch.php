<?php
require_once dirname(__DIR__) . '/site_paths.php';
@ini_set('memory_limit', '512M');
if (function_exists('ignore_user_abort')) {
	ignore_user_abort(true);
}
set_time_limit(600);
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'global' . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'flyer_facebook_share.php';
az_flyer_facebook_share_launch_http();
