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
	$sessionstat = $fgmembersite->CheckLogin('FieldsManagementUpdateSave.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
	$id = SanitizeText($_POST['id']);
	$descripcion = SanitizeText($_POST['descripcion']);
	$lat = $_POST['lat'];
	$long = $_POST['long'];
	$zoom = SanitizeInteger($_POST['zoom']);
	$google = $_POST['google'];
		
	$retunData = array('status' => '0', 'message' => 'No insert.', 'dataFieldAnswer' => 'Error');
		
	$sql1 = "CALL $schema.FieldUpdate('" . $_SESSION[$Config->getAlias() . 'username'] . "', $id, '$descripcion', $lat, $long, $zoom, '$google', @out);";

	$Connection = $Config->connectAdmin();
	$result = $Connection->query($sql1);

	$sql2 = "Select @out as 'count'";
	$result = $Connection->query($sql2);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row2 = $result->fetch_assoc()) {
			$retunData = array('status' => '1', 'message' => 'Success.', 'dataFieldAnswer' => $lang['430'], 'sql1' => $sql1, 'sql2' => $sql2);
		}
	}
	$Connection->Close();
    echo json_encode($retunData);
?>