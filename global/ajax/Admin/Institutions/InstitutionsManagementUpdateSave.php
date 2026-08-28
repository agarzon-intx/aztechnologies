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
	$sessionstat = $fgmembersite->CheckLogin('InstitutionsManagementUpdateSave.php');

	require_once("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	$id = SanitizeInteger($_POST['id']);
	$descripcion = SanitizeText(str_replace("'","''",htmlspecialchars($_POST["descripcion"])));
	$estatus = SanitizeInteger($_POST["estatus"]);
	$descripcionLarga = SanitizeText(str_replace("'","''",htmlspecialchars($_POST["descripcionlarga"])));
	$desc5 = SanitizeText(str_replace("'","''",htmlspecialchars($_POST["desc5"])));
	$desc5 = substr($desc5, 0, 5);
	$logoFileName = isset($_POST["file"]) ? $_POST["file"] : '';

	$Season = (int) $_COOKIE[$Config->getAlias() . 'season'];

	$retunData = array('status' => '0', 'message' => 'No update.', 'dataInstitutionAnswer' => 'Error');

	$Connection = $Config->connectAdmin();
	if (!$Connection) {
		echo json_encode($retunData);
		exit;
	}

	$logoKey = 'I-' . $Season . '-' . $id;
	$sql1 = "UPDATE $schema.Instituciones SET
				Institucion_DESC = '$descripcion',
				Activo = $estatus,
				Institucion_FULLDESC = '$descripcionLarga',
				Institucion_DESC5 = '$desc5',
				Logo = '" . $Connection->real_escape_string($logoKey) . "'
			 WHERE Institucion_ID = $id AND Torneo_ID = $Season";
	$result = $Connection->query($sql1);
	if ($result) {
		$retunData = array('status' => '1', 'message' => 'Success.', 'dataInstitutionAnswer' => $lang['inst011']);

		if (strlen($logoFileName) > 0) {
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
				chdir('..\\..\\..\\tmp');
			} else {
				chdir('../../../tmp');
			}
			$handle = new Upload($logoFileName);
			if ($handle->uploaded) {
				$handle->image_resize            = false;
				$handle->file_auto_rename 		 = false;
				$handle->file_overwrite 		 = true;
				$handle->file_new_name_body      = $logoKey;
				$handle->file_new_name_ext       = "png";
				$handle->Process($Config->getPath() ."/imagenes/Original/");

				$handle->image_resize            = true;
				$handle->image_ratio_pixels      = 22500;
				$handle->file_auto_rename 		 = false;
				$handle->file_overwrite 		 = true;
				$handle->file_new_name_body      = $logoKey;
				$handle->file_new_name_ext       = "png";
				$handle->Process($Config->getPath() ."/imagenes/");
				$handle->Clean();
			}
		}
	}

	$Connection->close();
	echo json_encode($retunData);
?>
