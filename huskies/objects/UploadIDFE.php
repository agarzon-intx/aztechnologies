<?php 
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
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('UploadIDFE.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	set_error_handler('exceptions_error_handler');


	//Linux
	$path = "../tmp/";
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    	//$path = "..\\tmp\\";
	}
	$retunData = array('status' => '0', 'alert' => 'File Upload Error', 'action' => "");
	if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST"){
		$name = $_FILES['myID11E']['name'];
		if(strlen($name)) {
			list($txt, $ext) = explode(".", $name);
			$image_name = "IDFPicture-" . session_id() .".".$ext;
			$tmp = $_FILES['myID11E']['tmp_name'];
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
?>