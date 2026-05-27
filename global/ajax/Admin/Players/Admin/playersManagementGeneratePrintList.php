<?php
	session_start();

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

	//error_reporting(0);

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
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('playersManagementGeneratePrintList.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	echo "Entro<br>";
	$afected = 0;
	
	$cookieName = $Config->getAlias() . 'printList';
	echo '"' . $cookieName . '"<br>';
	echo 'lista "' . htmlspecialchars($_POST["list"]) . '"<br>';
	
	if (isset($_SESSION[$cookieName])){
		echo "Existe<br>";
		print_r ($_SESSION[$cookieName]);
	}else{
		echo "No Existe<br>";
	}
	
	if(htmlspecialchars($_POST["clear"]) == "1"){
		echo "borro<br>";
		$str = htmlspecialchars($_POST["list"]);
		$_SESSION[$cookieName] = $str;
	}else{
		echo "No borro<br>";
		$str = htmlspecialchars($_POST["list"]);
		if(strlen($_SESSION[$cookieName]) > 0){
			$_SESSION[$cookieName] = $_SESSION[$cookieName] . ',' . $str;
		}else{
			$_SESSION[$cookieName] = $str;
		}
	}

	echo "Lista<br>";
	print_r ($_SESSION[$cookieName]);
	
	http_response_code(200);
?>