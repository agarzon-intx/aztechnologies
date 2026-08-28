<?php
ob_start();

session_start();

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: application/json; charset=utf-8');

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

require 'membersite_config.php';
$schema = $Config->getSchema();
$sessionstat = $fgmembersite->CheckLogin('playersManagementEditSave.php');

require_once dirname(__DIR__, 4) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'class.upload.php';

use Verot\Upload\Upload;

include 'lang.' . $_COOKIE[$Config->getAlias() . 'language'] . '.php';

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
$idf = $_POST['idf'];
$idpdf = isset($_POST['idpdf']) ? $_POST['idpdf'] : '';
$idb = $_POST['idb'];
$signature = $_POST['signature'];
$idfull = '';
$foto = '';
$firma = '';

$Config->LoadFlags();
$Config->LoadRegionalSettings();

$target_dir = '.';
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
	chdir('..\\..\\..\\..\\tmp');
} else {
	chdir('../../../../tmp');
}

$sessionPng = session_id();

if (strlen($picture) > 0) {
	$valid = '0';

	$ratio_crop = 'B';
	$imageSize = @getimagesize($picture);
	if ($imageSize !== false) {
		$width = (int) $imageSize[0];
		$height = (int) $imageSize[1];
		if ($width > 0 && $height > 0) {
			if ($width < $height) {
				$percentFoto = $width / $height;
			} else {
				$percentFoto = $height / $width;
			}
			$ratio_crop = ($percentFoto > 0.731707) ? 'L' : 'B';
		}
	}

	$handle = new Upload($picture);
	if ($handle->uploaded) {
		$handle->image_convert = 'png';
		$handle->image_resize = true;
		$handle->image_ratio_crop = $ratio_crop;
		$handle->image_y = 683;
		$handle->image_x = 500;
		$handle->file_auto_rename = false;
		$handle->file_overwrite = true;
		$handle->file_new_name_body = 'playerPictureNew-' . $sessionPng;
		$handle->file_new_name_ext = 'png';
		$handle->Process($target_dir);
		$handle->Clean();
	}
	$playerPictureFile = 'playerPictureNew-' . $sessionPng . '.png';
	if (file_exists($playerPictureFile)) {
		$foto = addslashes(file_get_contents($playerPictureFile));
	}
}

if (strlen($signature) > 0) {
	$valid = '0';
	$handle = new Upload($signature);
	if ($handle->uploaded) {
		$handle->image_convert = 'png';
		$handle->image_resize = true;
		$handle->image_ratio_crop = 'BL';
		$handle->image_y = 80;
		$handle->image_x = 200;
		$handle->file_auto_rename = false;
		$handle->file_overwrite = true;
		$handle->file_new_name_body = 'signaturePictureNew-' . $sessionPng;
		$handle->file_new_name_ext = 'png';
		$handle->Process($target_dir);
		$handle->Clean();
	}
	$signaturePictureFile = 'signaturePictureNew-' . $sessionPng . '.png';
	if (file_exists($signaturePictureFile)) {
		$firma = addslashes(file_get_contents($signaturePictureFile));
	}
}

if (strlen($idf) > 0) {
	$valid = '0';

	$handle = new Upload($idf);
	if ($handle->uploaded) {
		$handle->image_convert = 'png';
		$handle->image_resize = true;
		$handle->image_ratio_crop = 'B';
		$handle->image_y = 438;
		$handle->image_x = 700;
		$handle->file_auto_rename = false;
		$handle->file_overwrite = true;
		$handle->file_new_name_body = 'IDFPictureNew-' . $sessionPng;
		$handle->file_new_name_ext = 'png';
		$handle->Process($target_dir);
		$handle->Clean();
	}

	$idfProcessed = 'IDFPictureNew-' . $sessionPng . '.png';
	if (file_exists($idfProcessed)) {
		$handle = new Upload($idfProcessed);
		if ($handle->uploaded) {
			$handle->image_resize = true;
			$handle->image_ratio_fill = 't';
			$handle->image_y = 876;
			$handle->image_x = 700;
			$handle->file_auto_rename = false;
			$handle->file_overwrite = true;
			$handle->file_new_name_body = 'IDFFPictureNew-' . $sessionPng;
			$handle->file_new_name_ext = 'png';
			$handle->Process($target_dir);
			$handle->Clean();
		}
	}

	if (strlen($idb) > 0) {
		$handle = new Upload($idb);
	} else {
		$img = imagecreatetruecolor(700, 438);
		imagesavealpha($img, true);
		$color = imagecolorallocatealpha($img, 0, 0, 0, 127);
		imagefill($img, 0, 0, $color);
		imagepng($img, 'IDBPictureNew-' . $sessionPng . '.png');
		imagedestroy($img);
		$handle = new Upload('IDBPictureNew-' . $sessionPng . '.png');
	}

	if ($handle->uploaded) {
		$handle->image_convert = 'png';
		$handle->image_resize = true;
		$handle->image_ratio_crop = 'B';
		$handle->image_y = 438;
		$handle->image_x = 700;
		$handle->file_auto_rename = false;
		$handle->file_overwrite = true;
		$handle->file_new_name_body = 'IDBBPictureNew-' . $sessionPng;
		$handle->file_new_name_ext = 'png';
		$handle->Process($target_dir);
		$handle->Clean();
	}

	$idffFile = 'IDFFPictureNew-' . $sessionPng . '.png';
	$idbbFile = 'IDBBPictureNew-' . $sessionPng . '.png';
	if (file_exists($idffFile) && file_exists($idbbFile)) {
		$image_1 = imagecreatefrompng($idffFile);
		$image_2 = imagecreatefrompng($idbbFile);
		imagealphablending($image_1, true);
		imagesavealpha($image_1, true);
		imagecopy($image_1, $image_2, 0, 438, 0, 0, 700, 438);
		imagepng($image_1, 'IDPictureNew-' . $sessionPng . '.png');
		imagedestroy($image_1);
		imagedestroy($image_2);
		$idPictureFile = 'IDPictureNew-' . $sessionPng . '.png';
		if (file_exists($idPictureFile)) {
			$idfull = addslashes(file_get_contents($idPictureFile));
		}
	}

	$tempFiles = array(
		'IDFPictureNew-' . $sessionPng . '.png',
		'IDBPictureNew-' . $sessionPng . '.png',
		'IDPictureNew-' . $sessionPng . '.png',
		'playerPictureNew-' . $sessionPng . '.png',
		'signaturePictureNew-' . $sessionPng . '.png',
		'IDBBPictureNew-' . $sessionPng . '.png',
		'IDFFPictureNew-' . $sessionPng . '.png',
	);
	foreach ($tempFiles as $tempFile) {
		if (file_exists($tempFile)) {
			unlink($tempFile);
		}
	}
}

$retunData = array('status' => '0', 'message' => 'No insert.', 'dataPlayerMessage' => 'Error');

$sqlt = "CALL $schema.PlayerTeamUpdate('" . $_SESSION[$Config->getAlias() . 'username'] . "', $playerid, '$name', '$lastname', '$lastname2', '$nickname', '$birthdate', '$status', '$id', '$playernumber', $team, '$valid', '$comments', '$phone', '$email','$foto','$idfull','$firma',$sex, $type, NOW(), @out);";

$Connection = $Config->connectAdmin();
$result = $Connection->query($sqlt);
if ($result === false) {
	$retunData = array(
		'status' => '0',
		'message' => 'Database error.',
		'dataPlayerMessage' => $Connection->error,
	);
} else {
	$sql = "Select @out as 'count'";
	$result = $Connection->query($sql);
	if ($result && $result->num_rows > 0) {
		while ($row2 = $result->fetch_assoc()) {
			$retunData = array(
				'status' => '1',
				'message' => 'Success.',
				'dataPlayerMessage' => $lang['938'],
			);
		}
	}
}

$playerIdForPdf = $playerid;
require(dirname(__DIR__) . '/player_id_pdf_save.inc.php');

$Connection->Close();

$Config->Close();
ob_end_clean();
echo json_encode($retunData);
