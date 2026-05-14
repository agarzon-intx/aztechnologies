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
	$sessionstat = $fgmembersite->CheckLogin('configManagementInfoSave.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	$leagueName = SanitizeText($_POST['leagueName']);
	$latitude = $_POST['latitude'];
	$longitude = $_POST['longitude'];
	$logo = SanitizeText($_POST['logo']);
	$logox = SanitizeInteger($_POST['logox']);
	$logoy = SanitizeInteger($_POST['logoy']);
	$logoFileName = SanitizeFileName($_POST['logoFileName']);
	$colorHeader = sanitizeHexColor($_POST['colorHeader']);
	$colorBody = sanitizeHexColor($_POST['colorBody']);
	$colorFooter = sanitizeHexColor($_POST['colorFooter']);

	//echo getcwd();
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    		chdir('..\\..\\..\\tmp');
	}else{
		chdir('../../../tmp');
	}
	//echo getcwd();
	if(strlen($logoFileName) > 0){
		rename("$logoFileName", $Config->getPath() ."/imagenes/$logo.png");
	}

	$retunData = array('status' => '0', 'message' => 'No insert.', 'dataConfigAnswer' => 'Error');
		
	$sql = "CALL $schema.ConfigInfoUpdate('" . $_SESSION[$Config->getAlias() . 'username'] . "', '$leagueName', $latitude, $longitude, '$logo', $logox, $logoy, '$colorHeader', '$colorBody', '$colorFooter', @out);";
	//echo $sql;
	$Connection = $Config->connectAdmin();
	$result = $Connection->query($sql);

	$sql = "Select @out as 'count'";
	$result = $Connection->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row2 = $result->fetch_assoc()) {
			$retunData = array('status' => '1', 'message' => 'Success.', 'dataConfigAnswer' => $lang['441']);
		}
	}
	$Connection->Close();
    echo json_encode($retunData);
?>