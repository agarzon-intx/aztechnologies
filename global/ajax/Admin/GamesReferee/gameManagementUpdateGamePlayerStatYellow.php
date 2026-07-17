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
	$sessionstat = $fgmembersite->CheckLogin('gameManagementUpdateGamePlayerStatYellow.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	$Player = SanitizeInteger($_POST['Player']);
    $Team = SanitizeInteger($_POST['Team']);
    $Game = SanitizeInteger($_POST['Game']);
    $Week = SanitizeInteger($_POST['Week']);
    $Season = SanitizeInteger($_POST['Season']);
    $Yellow = SanitizeInteger($_POST['Yellow']);

	$retunData = array('status' => '0', 'message' => 'No insert.', 'dataColorAnswer' => 'Error');
		
	$sql = "CALL $schema.GameUpdateDetailYellow('" . $_SESSION[$Config->getAlias() . 'username'] . "', $Player, $Team, $Game, $Week, $Season, $Yellow, @out);";

	//echo $sql;

	$Connection = $Config->connectAdmin();
	$result = $Connection->query($sql);

	$sql = "Select @out as 'count'";
	$result = $Connection->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row2 = $result->fetch_assoc()) {
			$retunData = array('status' => '1', 'message' => 'Success.');
		}
	}
	$Connection->Close();
    echo json_encode($retunData);
?>