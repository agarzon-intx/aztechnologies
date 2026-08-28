<?php
	namespace Verot\Upload;
    session_start();

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
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
	$sessionstat = $fgmembersite->CheckLogin('playersManagementEditSave.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
    
    $Season = $_COOKIE[$Config->getAlias() . 'season'];
    $Category = $_COOKIE[$Config->getAlias() . 'category'];
	
    $playerid = SanitizeText($_POST['playerid']); 
    $name = SanitizeText($_POST['name']); 
    $lastname = SanitizeText($_POST['lastname']);
    $lastname2 = SanitizeText($_POST['lastname2']); 
    $nickname = SanitizeText($_POST['nickname']); 
    $birthdate = $_POST['birthdate'];
    $playernumber = SanitizeInteger($_POST['playernumber']);
    $phone = SanitizeInteger($_POST['phone']);
    $sex = SanitizeInteger($_POST['sex']);
    $email = SanitizeEmail($_POST['email']); 
    $id = SanitizeText($_POST['id']);
    $comments = SanitizeText($_POST['comments']);
    $valid = SanitizeInteger($_POST['valid']);
    $status = SanitizeNonNumericText($_POST['status']);
    $team = SanitizeInteger($_POST['team']);
    $picture = $_POST['picture'];
    $type = $_POST['type'];
	$foto = "";
    $idf = $_POST['idf'];
    $idpdf = isset($_POST['idpdf']) ? $_POST['idpdf'] : '';
    $idb = $_POST['idb'];
    $signature = $_POST['signature'];
	$idfull = '';
	$foto = '';
	$firma = '';

	$htmlPlayer = '';
	$Config->LoadFlags();
    $Config->LoadRegionalSettings();

	$htmlPlayer .= '';   
	
	$target_dir = ".";
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    	chdir('..\\..\\..\\..\\tmp');
	}else{
		chdir('../../../../tmp');
	}
	

	if(strlen($picture) > 0){

		list($width, $height) = getimagesize($picture);
		$percentFoto = 0;
		if($width < $height){
			$percentFoto = $width/$height;
		}else{
			$percentFoto = $height/$width;
		}
		if(($percentFoto) > 0.731707){
			$ratio_crop = 'L';
		}else{
			$ratio_crop = 'B';
		}


		$handle = new Upload($picture);

		if ($handle->uploaded) {

			$handle->image_convert         = 'png';
			$handle->image_resize          = true;
			$handle->image_ratio_crop      = $ratio_crop;
			$handle->image_y               = 683;
			$handle->image_x               = 500;
			$handle->file_auto_rename 		 = false;
			$handle->file_overwrite 		 = true;
			$handle->file_new_name_body      = "playerPictureNew-" . session_id();
			$handle->file_new_name_ext       = "png";
			$handle->Process($target_dir);

			//$handle-> Clean();
		}
		$foto=addslashes(file_get_contents("playerPictureNew-" . session_id() . ".png"));

	}else{
		$foto="";
	}
	if(strlen($signature) > 0){
		$handle = new Upload($signature);

		if ($handle->uploaded) {

			$handle->image_convert         = 'png';
			$handle->image_resize          = true;
			$handle->image_ratio_crop      = 'BL';
			$handle->image_y               = 970;
			$handle->image_x               = 750;

			$handle->file_auto_rename 		 = false;
			$handle->file_overwrite 		 = true;
			$handle->file_new_name_body      = "signaturePictureNew-" . session_id();
			$handle->file_new_name_ext       = "png";
			$handle->Process($target_dir);

			//$handle-> Clean();
		}
		$firma=addslashes(file_get_contents("signaturePictureNew-" . session_id() . ".png"));
	}else{
		$firma="";
	}
	if(strlen($idf) > 0){
		if(strlen($idf) > 0){

			$handle = new Upload($idf);
			if ($handle->uploaded) {
				$handle->image_convert         = 'png';
				$handle->image_resize          = true;
				$handle->image_ratio_crop      = 'B';
				$handle->image_y               = 438;
				$handle->image_x               = 700;

				$handle->file_auto_rename 		 = false;
				$handle->file_overwrite 		 = true;
				$handle->file_new_name_body      = "IDFPictureNew-" . session_id();
				$handle->file_new_name_ext       = "png";
				$handle->Process($target_dir);
				//$handle-> Clean();
			}
			$handle = new Upload("IDFPictureNew-" . session_id() . ".png");

			if ($handle->uploaded) {

				$handle->image_resize          = true;
				$handle->image_ratio_fill      = 't';
				$handle->image_y               = 876;
				$handle->image_x               = 700;

				$handle->file_auto_rename 		 = false;
				$handle->file_overwrite 		 = true;
				$handle->file_new_name_body      = "IDFFPictureNew-" . session_id();
				$handle->file_new_name_ext       = "png";
				$handle->Process($target_dir);

				//$handle-> Clean();
			}

			if(strlen($idb) > 0){
				$handle = new Upload($idb);
			}else{
				$img = imagecreatetruecolor(700, 438);
				imagesavealpha($img, true);
				$color = imagecolorallocatealpha($img, 0, 0, 0, 127);
				imagefill($img, 0, 0, $color);
				imagepng($img, "IDBPictureNew-" . session_id() . ".png");
				$handle = new Upload("IDBPictureNew-" . session_id() . ".png");
			}


			if ($handle->uploaded) {

				$handle->image_convert         = 'png';
				$handle->image_resize          = true;
				$handle->image_ratio_crop      = 'B';
				$handle->image_y               = 438;
				$handle->image_x               = 700;

				$handle->file_auto_rename 		 = false;
				$handle->file_overwrite 		 = true;
				$handle->file_new_name_body      = "IDBBPictureNew-" . session_id();
				$handle->file_new_name_ext       = "png";
				$handle->Process($target_dir);

				//$handle-> Clean();
			}


			$image_1 = imagecreatefrompng("IDFFPictureNew-" . session_id() . ".png");
			$image_2 = imagecreatefrompng("IDBBPictureNew-" . session_id() . ".png");
			imagealphablending($image_1, true);
			imagesavealpha($image_1, true);
			imagecopy($image_1, $image_2, 0, 438, 0, 0, 700, 438);
			imagepng($image_1, "IDPictureNew-" . session_id() . ".png");
			$idfull=addslashes (file_get_contents("IDPictureNew-" . session_id() . ".png"));


			if (file_exists("IDFPictureNew-" . session_id() . ".png")) { unlink ("IDFPictureNew-" . session_id() . ".png"); }
			if (file_exists("IDBPictureNew-" . session_id() . ".png")) { unlink ("IDBPictureNew-" . session_id() . ".png"); }
			if (file_exists("IDPictureNew-" . session_id() . ".png")) { unlink ("IDPictureNew-" . session_id() . ".png"); }
			if (file_exists("playerPictureNew-" . session_id() . ".png")) { unlink ("playerPictureNew-" . session_id() . ".png"); }
			if (file_exists("signaturePictureNew-" . session_id() . ".png")) { unlink ("signaturePictureNew-" . session_id() . ".png"); }
			if (file_exists("IDBBPictureNew-" . session_id() . ".png")) { unlink ("IDBBPictureNew-" . session_id() . ".png"); }
			if (file_exists("IDFFPictureNew-" . session_id() . ".png")) { unlink ("IDFFPictureNew-" . session_id() . ".png"); }


		}
	}else{
		$idfull="";
	}
	$retunData = array('status' => '0', 'message' => 'No insert.', 'dataPlayerMessage' => 'Error');

	$sql = "CALL $schema.PlayerUpdate('" . $_SESSION[$Config->getAlias() . 'username'] . "', $playerid, '$name', '$lastname', '$lastname2', '$nickname', '$birthdate', '$status', '$id', '$playernumber', $team, '$valid', '$comments', '$phone', '$email','$foto','$idfull','$firma',$sex, $type, NOW(), @out);";
	//echo $sql;
	$Connection = $Config->connectAdmin();
	$result = $Connection->query($sql);

	$sql = "Select @out as 'count'";
	$result = $Connection->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row2 = $result->fetch_assoc()) {
			$retunData = array('status' => '1', 'message' => 'Success.', 'dataPlayerMessage' => $lang['938']);
		}
	}

	$playerIdForPdf = $playerid;
	require(dirname(__DIR__) . '/player_id_pdf_save.inc.php');

	$Connection->Close();
	
    
    $Config->Close();
    echo json_encode($retunData);
?>