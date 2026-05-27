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
	$sessionstat = $fgmembersite->CheckLogin('TournamentsManagementNewSave.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	$tournamentName = SanitizeText($_POST['tournamentName']);
	$tournamentActual = SanitizeText($_POST['tournamentActual']);
	$tournamentInscr = SanitizeText($_POST['tournamentInscr']);
	$tournamentVs = SanitizeText($_POST['tournamentVs']);
	$tournamentWeeks = SanitizeText($_POST['tournamentWeeks']);
	$prevTourID = 0;
	$newTourID = 0;
	
	$sql0="	SELECT max(Torneo_ID) Torneo_ID
			FROM $schema.Torneos;";
	$result = $Config->query($sql0);
	if ($result->num_rows > 0) {
		while($row2 = $result->fetch_assoc()) {
			$prevTourID = $row2["Torneo_ID"];
		}		
	}
	
	$actual = 'N';
	
	if($tournamentActual == 1){
		$actual = 'S';
	}
		
	$retunData = array('status' => '0', 'message' => 'No insert.', 'dataTournamentAnswer' => 'Error');
		
	$sql1 = "CALL $schema.TournamentCreate('" . $_SESSION[$Config->getAlias() . 'username'] . "', '$tournamentName', '$actual', $tournamentInscr, $tournamentVs, $tournamentWeeks, @out);";

	$Connection = $Config->connectAdmin();
	$result = $Connection->query($sql1);
	
	$sql01="	SELECT max(Torneo_ID) Torneo_ID
			FROM $schema.Torneos;";
	$result = $Config->query($sql01);
	if ($result->num_rows > 0) {
		while($row2 = $result->fetch_assoc()) {
			$newTourID = $row2["Torneo_ID"];
		}		
	}
	chdir('../../../imagenes/');
	$salida = shell_exec('./copy-rename.sh ' . $prevTourID . ' ' . $newTourID . '');
	
	$sql2 = "Select @out as 'count'";
	$result = $Connection->query($sql2);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row2 = $result->fetch_assoc()) {
			$retunData = array('status' => '1', 'message' => 'Success.', 'dataTournamentAnswer' => $lang['756'], 'sql1' => $sql1, 'sql2' => $sql2, 'out' => $salida);
		}
	}
	$Connection->Close();
    echo json_encode($retunData);
?>