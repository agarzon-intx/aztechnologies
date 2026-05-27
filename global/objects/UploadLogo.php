<?php 
    session_start();
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

	error_reporting(0);
if (!defined('APP_SITE_ROOT')) {
	$___d = __DIR__;
	while ($___d !== dirname($___d)) {
		$___p = $___d . DIRECTORY_SEPARATOR . 'site_paths.php';
		if (is_readable($___p)) {
			require_once $___p;
			break;
		}
		$___d = dirname($___d);
	}
}
	require("membersite_config.php");
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('UploadLogo.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	set_error_handler('exceptions_error_handler');


	//Linux
	//echo getcwd();
	$path = "../tmp/";
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    	$path = "..\\tmp\\";
	}
	$retunData = array('status' => '0', 'alert' => 'File Upload Error', 'action' => "");
	if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST"){
		$name = $_FILES['myLogo']['name'];
		if(strlen($name)) {
			list($txt, $ext) = explode(".", $name);
			$image_name = "logoPicture-" . session_id() .".".$ext;
			$tmp = $_FILES['myLogo']['tmp_name'];
			//echo $path;
			if(move_uploaded_file($tmp, $path.$image_name)){
				chmod($path.$image_name, 0755);
				$retunData = array('status' => '1', 'alert' => $lang['js910'], 'action' => $image_name);
			}else{
				$retunData = array('status' => '0', 'alert' => $lang['js911'], 'action' => "");
			}
			
		}else{
			$retunData = array('status' => '0', 'alert' => $lang['js912'], 'action' => "");
		}
	}
	echo json_encode($retunData);
	
	

function exceptions_error_handler($severity, $message, $filename, $lineno) {
  if (error_reporting() == 0) {
    return;
  }
  if (error_reporting() & $severity) {
    throw new ErrorException($message, 0, $severity, $filename, $lineno);
  }
}
