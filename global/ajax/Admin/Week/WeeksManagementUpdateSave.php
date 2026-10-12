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
	$sessionstat = $fgmembersite->CheckLogin('TournamentsManagementUpdateSave.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	$id = SanitizeText($_POST['id']);
	$Season = $_COOKIE[$Config->getAlias() . "season"];
	$Desc = SanitizeText($_POST['Desc']);
	$DescCorta = SanitizeText($_POST['DescCorta']);
	$Orden = SanitizeText($_POST['Orden']);
	$Fecha = SanitizeText($_POST['Fecha']);
	$Inicio = sanitizeText($_POST['Inicio']);
	$Fin = sanitizeText($_POST['Fin']);
	$Type = SanitizeInteger($_POST["Type"]);
	
	$retunData = array('status' => '0', 'message' => 'No insert.', 'dataWeekAnswer' => 'Error');
		
	$sql1 = "CALL $schema.WeekUpdate('" . $_SESSION[$Config->getAlias() . 'username'] . "', $id, $Season, '$Desc', '$DescCorta', $Orden, '$Fecha', '$Inicio', '$Fin', $Type, @out);";

	$Connection = $Config->connectAdmin();

	$weekId = (int) $id;
	$seasonId = (int) $Season;
	$oldInicio = '';
	$oldFin = '';
	$resOld = $Connection->query("SELECT DATE_FORMAT(Fecha_Inicio, '%Y-%m-%d') AS ini, DATE_FORMAT(Fecha_Fin, '%Y-%m-%d') AS fin
		FROM $schema.Jornada WHERE Jornada_ID = $weekId AND Torneo_ID = $seasonId LIMIT 1");
	if ($resOld && $rowOld = $resOld->fetch_assoc()) {
		$oldInicio = (string) $rowOld['ini'];
		$oldFin = (string) $rowOld['fin'];
	}

	$result = $Connection->query($sql1);
	while ($Connection->more_results() && $Connection->next_result()) {
	}

	$sql2 = "Select @out as 'count'";
	$result = $Connection->query($sql2);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row2 = $result->fetch_assoc()) {
			$retunData = array('status' => '1', 'message' => 'Success.', 'dataWeekAnswer' => $lang['872'], 'sql1' => $sql1, 'sql2' => $sql2);
		}
	}

	/*
	 * Games of this week dated inside the old range follow the week to the same weekday
	 * of the new range (same week number when the range spans several weeks);
	 * if the new range has no such day, they land on its last day.
	 */
	$isDate = function ($d) {
		return is_string($d) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) && strtotime($d) !== false;
	};
	$newInicio = substr(trim((string) $Inicio), 0, 10);
	$newFin = substr(trim((string) $Fin), 0, 10);
	if ($retunData['status'] === '1' && $weekId > 0 && $isDate($oldInicio) && $isDate($oldFin)
		&& $isDate($newInicio) && $isDate($newFin) && $newFin >= $newInicio
		&& ($oldInicio !== $newInicio || $oldFin !== $newFin)) {
		$sqlMove = "UPDATE $schema.Juegos
			SET Fecha = LEAST(
				DATE_ADD('$newInicio', INTERVAL (FLOOR(DATEDIFF(Fecha, '$oldInicio') / 7) * 7
					+ MOD(DAYOFWEEK(Fecha) - DAYOFWEEK('$newInicio') + 7, 7)) DAY),
				'$newFin')
			WHERE Torneo_ID = $seasonId
				AND Jornada_ID = $weekId
				AND Fecha BETWEEN '$oldInicio' AND '$oldFin'";
		if ($Connection->query($sqlMove)) {
			$moved = (int) $Connection->affected_rows;
			if ($moved > 0) {
				$userEsc = $Connection->real_escape_string($_SESSION[$Config->getAlias() . 'username']);
				$logSql = $Connection->real_escape_string(preg_replace('/\s+/', ' ', $sqlMove));
				$Connection->query("CALL $schema.insertIntoControlTable('$userEsc', 'UPDATE', 'GAME', '$logSql', 'OK, total updates: $moved')");
				while ($Connection->more_results() && $Connection->next_result()) {
				}
				$movedMsg = isset($lang['872-1']) ? $lang['872-1'] : 'Games moved to the new dates: %1';
				$retunData['dataWeekAnswer'] .= "\n" . str_replace('%1', (string) $moved, $movedMsg);
			}
		}
	}
	$Connection->Close();
    echo json_encode($retunData);
?>