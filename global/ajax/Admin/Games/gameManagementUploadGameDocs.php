<?php
	namespace Verot\Upload;
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
	$sessionstat = $fgmembersite->CheckLogin('gameManagementUploadGameDocs.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
    
    $Season = $_COOKIE[$Config->getAlias() . 'season'];
    $Category = $_COOKIE[$Config->getAlias() . 'category'];
	
    $Season = SanitizeText($_POST['Season']); 
    $Week = SanitizeText($_POST['Week']); 
    $Game = SanitizeInteger($_POST['Game']);
	$Doc1 = $_POST['Anexo1'];
	$Doc2 = $_POST['Anexo2'];
	$Doc3 = $_POST['Anexo3'];
	$Doc4 = $_POST['Anexo4'];
	
	$htmlPlayer = '';
	$Config->LoadFlags();
    $Config->LoadRegionalSettings();

	$htmlPlayer .= '';   
	
	$target_dir = ".";
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    	chdir('..\\..\\..\\tmp');
	}else{
		chdir('../../../tmp');
	}
	
	$target_dir = "../imagenes/Cedulas";

	if(strlen($Doc1) > 0){

		$handle = new Upload($Doc1);

		if ($handle->uploaded) {

			$handle->image_resize            = true;
			$handle->image_ratio_pixels      = 400000;
			$handle->file_auto_rename 		 = false;
			$handle->file_overwrite 		 = true;
			$handle->file_new_name_body      = $Season . "-" . $Week . "-" . $Game . "-Anexo1";
			$handle->file_new_name_ext       = "png";
			$handle->Process($target_dir);
	
			$handle-> Clean();
		}
	}

	if(strlen($Doc2) > 0){
		$handle = new Upload($Doc2);

		if ($handle->uploaded) {

			$handle->image_resize            = true;
			$handle->image_ratio_pixels      = 400000;
			$handle->file_auto_rename 		 = false;
			$handle->file_overwrite 		 = true;
			$handle->file_new_name_body      = $Season . "-" . $Week . "-" . $Game . "-Anexo2";
			$handle->file_new_name_ext       = "png";
			$handle->Process($target_dir);
	
			$handle-> Clean();
		}
	}


	if(strlen($Doc3) > 0){
		$handle = new Upload($Doc3);

		if ($handle->uploaded) {

			$handle->image_resize            = true;
			$handle->image_ratio_pixels      = 400000;
			$handle->file_auto_rename 		 = false;
			$handle->file_overwrite 		 = true;
			$handle->file_new_name_body      = $Season . "-" . $Week . "-" . $Game . "-Anexo3";
			$handle->file_new_name_ext       = "png";
			$handle->Process($target_dir);
	
			$handle-> Clean();
		}
	}


	if(strlen($Doc4) > 0){
		$handle = new Upload($Doc4);

		if ($handle->uploaded) {

			$handle->image_resize            = true;
			$handle->image_ratio_pixels      = 400000;
			$handle->file_auto_rename 		 = false;
			$handle->file_overwrite 		 = true;
			$handle->file_new_name_body      = $Season . "-" . $Week . "-" . $Game . "-Anexo4";
			$handle->file_new_name_ext       = "png";
			$handle->Process($target_dir);
	
			$handle-> Clean();
		}
	}

	
	$retunData = array('status' => '1', 'message' => 'Insert.', 'dataPlayerMessage' => $lang['668']);

    $Config->Close();
    echo json_encode($retunData);
?>