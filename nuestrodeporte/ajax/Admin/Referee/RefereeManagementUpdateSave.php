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
	$sessionstat = $fgmembersite->CheckLogin('RefereeManagementUpdateSave.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
	$id = SanitizeText($_POST['id']);
	$Titulo = SanitizeText(str_replace("'","''",htmlspecialchars($_POST["Titulo"])));
	$Fecha = $_POST["Fecha"]; 
	$minuta = str_replace("'","''",htmlspecialchars($_POST['editor']));
	$minutaFileName = $_POST["file"];
	
	$Season = $_COOKIE[$Config->getAlias() . 'season'];
		
	$retunData = array('status' => '0', 'message' => 'No insert.', 'dataRefereeAnswer' => 'Error');
		
	$sql1 = "CALL $schema.RefereeUpdate('" . $_SESSION[$Config->getAlias() . 'username'] . "', $id, '$Titulo', '$Fecha', '$minuta', @out);";

	$Connection = $Config->connectAdmin();
	$result = $Connection->query($sql1);

	$sql2 = "Select @out as 'count'";
	$result = $Connection->query($sql2);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row2 = $result->fetch_assoc()) {
			$retunData = array('status' => '1', 'message' => 'Success.', 'dataRefereeAnswer' => $lang['720'], 'sql1' => $sql1, 'sql2' => $sql2);
		}
	}
	
	$target_dir = ".";
	
	$target_dir = "../imagenes/Original/";
	$target_dir1 = "../imagenes/";
	
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    	chdir('..\\..\\..\\tmp');
		$target_dir = "..\\imagenes\\Minutas\\";
	}else{
		chdir('../../../tmp');
		$target_dir = "../imagenes/Minutas/";
	}
	$found = 0;
	
	
	if (strlen($minutaFileName) > 0){
		$handle = new Upload($minutaFileName);
		
		if ($handle->uploaded) {
			$handle->image_resize            = true;
			$handle->image_ratio_pixels      = 400000;
			$handle->file_auto_rename 		 = false;
			$handle->file_overwrite 		 = true;
			$handle->file_new_name_body      = $id;
			$handle->file_new_name_ext       = "png";
			$handle->Process($target_dir);
	
			$handle-> Clean();
		} 		
	
	}
	
	$Connection->Close();
    echo json_encode($retunData);
?>