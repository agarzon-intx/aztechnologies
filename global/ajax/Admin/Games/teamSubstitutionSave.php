<?php
	session_start();
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	header('Content-Type: application/json; charset=utf-8');

	error_reporting(0);

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
	$sessionstat = $fgmembersite->CheckLogin('teamSubstitutionSave.php');
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	$jsonFlags = JSON_UNESCAPED_UNICODE;
	if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
		$jsonFlags |= JSON_INVALID_UTF8_SUBSTITUTE;
	}

	$fail = function ($msg) use ($jsonFlags) {
		echo json_encode(array(
			'status' => '0',
			'message' => $msg,
		), $jsonFlags);
		exit;
	};

	$Season = 0;
	if (isset($_COOKIE[$Config->getAlias() . 'season']) && $_COOKIE[$Config->getAlias() . 'season'] !== '') {
		$Season = (int) SanitizeInteger($_COOKIE[$Config->getAlias() . 'season']);
	}
	$categoryId = isset($_POST['categoryId']) ? (int) SanitizeInteger($_POST['categoryId']) : 0;
	$sourceId = isset($_POST['sourceId']) ? (int) SanitizeInteger($_POST['sourceId']) : 0;
	$targetId = isset($_POST['targetId']) ? (int) SanitizeInteger($_POST['targetId']) : 0;
	$weekId = isset($_POST['weekId']) ? (int) SanitizeInteger($_POST['weekId']) : 0;

	$missing = isset($lang['101-48']) ? $lang['101-48'] : 'Select source team, target team and week.';
	if ($Season <= 0 || $categoryId <= 0 || $sourceId <= 0 || $targetId <= 0 || $weekId <= 0) {
		$fail($missing);
	}
	if ($sourceId === $targetId) {
		$fail($missing);
	}

	$catCheck = $Config->query(
		"SELECT Categoria_ID, Calendario_ID FROM $schema.Categorias
		 WHERE Torneo_ID = $Season AND Categoria_ID = $categoryId LIMIT 1"
	);
	if (!$catCheck || $catCheck->num_rows === 0) {
		$fail($missing);
	}
	$catRow = $catCheck->fetch_assoc();
	$calId = (int) $catRow['Calendario_ID'];

	$teamCheck = $Config->query(
		"SELECT Equipo_ID, Fuerza, IFNULL(Campo_ID, 0) AS Campo_ID FROM $schema.Equipos
		 WHERE Torneo_ID = $Season
		   AND Equipo_ID IN ($sourceId, $targetId)
		   AND IFNULL(Activo, 0) = 1"
	);
	$found = array();
	$teamField = array();
	if ($teamCheck && $teamCheck->num_rows > 0) {
		while ($t = $teamCheck->fetch_assoc()) {
			$found[(int) $t['Equipo_ID']] = (int) $t['Fuerza'];
			$teamField[(int) $t['Equipo_ID']] = (int) $t['Campo_ID'];
		}
	}
	if (!isset($found[$sourceId], $found[$targetId])
		|| $found[$sourceId] !== $categoryId
		|| $found[$targetId] !== $categoryId) {
		$fail($missing);
	}

	$weekCheck = $Config->query(
		"SELECT Jornada_ID, Fecha_Inicio, Calendario_ID
		 FROM $schema.Jornada
		 WHERE Torneo_ID = $Season AND Jornada_ID = $weekId LIMIT 1"
	);
	if (!$weekCheck || $weekCheck->num_rows === 0) {
		$fail($missing);
	}
	$weekRow = $weekCheck->fetch_assoc();
	if ($calId > 0 && (int) $weekRow['Calendario_ID'] !== $calId) {
		$fail($missing);
	}

	$weekStart = $Config->quote($weekRow['Fecha_Inicio']);
	$calFilter = ($calId > 0) ? " AND jo.Calendario_ID = $calId" : '';
	$weekFilter = "Jornada_ID IN (
			SELECT jo.Jornada_ID
			FROM $schema.Jornada jo
			WHERE jo.Torneo_ID = $Season
			  AND jo.Fecha_Inicio >= $weekStart
			  $calFilter
		)";

	$occCheck = $Config->query(
		"SELECT Juego_ID FROM $schema.Juegos
		 WHERE Torneo_ID = $Season
		   AND (Local_ID = $targetId OR Visitante_ID = $targetId)
		   AND $weekFilter
		 LIMIT 1"
	);
	if ($occCheck && $occCheck->num_rows > 0) {
		$fail(isset($lang['101-51']) ? $lang['101-51'] : 'The target team already has games from the selected week.');
	}

	$Connection = $Config->connectAdmin();
	if (!$Connection) {
		$fail(isset($lang['js0002']) ? $lang['js0002'] : 'Error');
	}

	// Games still on the replaced team's field (or none) move to the new team's field; manual choices stay.
	$srcField = (int) $teamField[$sourceId];
	$tgtField = (int) $teamField[$targetId];
	$sqlHome = "UPDATE $schema.Juegos
		SET Campo_ID = IF(IFNULL(Campo_ID, 0) IN (0, $srcField), $tgtField, Campo_ID),
			Local_ID = $targetId
		WHERE Torneo_ID = $Season
		  AND Local_ID = $sourceId
		  AND IFNULL(Visitante_ID, 0) <> $targetId
		  AND $weekFilter";
	$okHome = $Connection->query($sqlHome);
	$homeCount = $okHome ? (int) $Connection->affected_rows : 0;

	$sqlAway = "UPDATE $schema.Juegos
		SET Campo_ID = IF(Local_ID = -1 AND IFNULL(Campo_ID, 0) IN (0, $srcField), $tgtField, Campo_ID),
			Visitante_ID = $targetId
		WHERE Torneo_ID = $Season
		  AND Visitante_ID = $sourceId
		  AND IFNULL(Local_ID, 0) <> $targetId
		  AND $weekFilter";
	$okAway = $Connection->query($sqlAway);
	$awayCount = $okAway ? (int) $Connection->affected_rows : 0;
	$Connection->Close();

	if (!$okHome || !$okAway) {
		$fail(isset($lang['js0002']) ? $lang['js0002'] : 'Error');
	}

	$msg = isset($lang['101-50']) ? $lang['101-50'] : 'Updated %1 home game(s) and %2 away game(s).';
	$msg = str_replace(array('%1', '%2'), array((string) $homeCount, (string) $awayCount), $msg);

	echo json_encode(array(
		'status' => '1',
		'message' => $msg,
		'homeCount' => $homeCount,
		'awayCount' => $awayCount,
	), $jsonFlags);
?>
