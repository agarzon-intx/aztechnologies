<?php
    // JSON endpoint only (no HTML). Enter-key / form behavior is handled in showBrowserReg.php.
    session_start();
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

	error_reporting(0);

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

	$sessionstat = $fgmembersite->CheckLogin('browserReg.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

    $retunData = array('status' => '0', 'dataError' => 'Something went wrong,please try again.');

	if($fgmembersite->RegisterBrowser()){
		$retunData = array('status' => '1');
	}
	else{
		$retunData = array('status' => '0', 'dataError' => $fgmembersite->GetErrorMessage());
	}

	echo json_encode($retunData);
?>