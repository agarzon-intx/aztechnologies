<?php
    ob_start();
    session_start();
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Content-Type: application/json; charset=utf-8");

	ini_set('display_errors', '0');
	
	//ok
	$retunData = array('status' => '1');

$__APP_SITE_PATHS_START__ = __DIR__;
$__app_here = __DIR__;
for ($__i = 0, $__prev = null; $__i < 24; $__i++) {
	$__base = ($__i === 0) ? $__app_here : dirname($__app_here, $__i);
	if ($__base === $__prev) {
		break;
	}
	$__prev = $__base;
	$__inc = $__base . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'app_site_paths.inc.php';
	if (is_readable($__inc)) {
		require_once $__inc;
		break;
	}
}
unset($__i, $__prev, $__base, $__inc, $__app_here);

	require("membersite_config.php");
	
	$__alias = $Config->getAlias();
	$__langCk = $__alias . 'language';
	if (!isset($_COOKIE[$__langCk]) || $_COOKIE[$__langCk] === '') {
		$Config->LoadLanguage();
		$__lang = (string) $Config->lan;
	} else {
		$__lang = $_COOKIE[$__langCk];
	}
	$__langFile = 'lang.' . $__lang . '.php';
	if (!@include($__langFile)) {
		include('lang.es.php');
	}
	
	$__activityKey = $__alias . 'LAST_ACTIVITY';
	$session_lifetime = $fgmembersite->sessionLifeTime;
	
	if(empty($_SESSION[$__alias . 'username'])){
		$retunData = array('status' => '2', 'message' => 'No hay sesion');
		$fgmembersite->LogOut();
	}else{
		if (!isset($_SESSION[$__activityKey])) {
			$_SESSION[$__activityKey] = time();
		}
		$last_activity = $_SESSION[$__activityKey];
		$time_lapsed = time() - $last_activity;
		// They were properly logged in, but that was too long ago (sessionLifeTime) so they need to login again
		if ((time() - $_SESSION[$__activityKey]) > $fgmembersite->sessionLifeTime) {
			//http_response_code(401);
			$expiredMessage = isset($lang['230']) ? $lang['230'] : 'Your session has expired. Please login again.';
			$retunData = array('status' => '0', 'message' => $expiredMessage, 'last_activity' => $last_activity, 'time' => time(), 'time_lapsed' => $time_lapsed, 'session_lifetime' => $session_lifetime);
			$fgmembersite->LogOut();
		}else{
			$secondsO = (time() - $_SESSION[$__activityKey]);
			$minutes = intdiv($secondsO, 60) % 60;
			$seconds = $secondsO % 60;
			$out = (time() - $_SESSION[$__activityKey]) . "	Missing minutes $minutes, seconds $seconds";

			$retunData = array('status' => '1', 'message' => $out, 'last_activity' => $last_activity, 'time' => time(), 'time_lapsed' => $time_lapsed, 'session_lifetime' => $session_lifetime);
		}
	}

	
	
	ob_end_clean();
	echo json_encode($retunData);
