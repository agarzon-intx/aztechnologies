<?php
	session_start();

	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
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

	require("membersite_config.php");
	$schema = $Config->getSchema();
	$fgmembersite->CheckLogin('playerDuplicateTeams.php');
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	$Config->LoadFlags();

	if ((int) $Config->duplicatePlayer !== 1) {
		echo json_encode(array('status' => '0', 'message' => $lang['539-14'], 'teams' => array()));
		exit;
	}

	$Season = SanitizeInteger($_COOKIE[$Config->getAlias() . 'season'] ?? 0);
	$currentTeam = SanitizeInteger($_POST['team'] ?? 0);
	$equipoIds = $fgmembersite->UserEquipo();
	$isAdmin = ($equipoIds === '0' || $equipoIds === '-1');

	$teamFilter = '';
	if (!$isAdmin) {
		$ids = array_filter(array_map('intval', explode(',', (string) $equipoIds)));
		if (count($ids) === 0) {
			echo json_encode(array('status' => '0', 'message' => $lang['539-14'], 'teams' => array()));
			exit;
		}
		$teamFilter = ' AND b.Equipo_ID IN (' . implode(',', $ids) . ')';
	}
	if ($currentTeam > 0) {
		$teamFilter .= ' AND b.Equipo_ID <> ' . $currentTeam;
	}

	$sql = "SELECT b.Equipo_ID, CONCAT(c.categoria_DESC, ' - ', b.Equipo_FULLDESC) Equipo_FULLDESC
		FROM $schema.Equipos b
			JOIN $schema.Categorias c ON b.Fuerza = c.Categoria_ID AND c.Torneo_ID = $Season
		WHERE b.Torneo_ID = $Season AND b.Equipo_ID > 0 $teamFilter
		ORDER BY c.Categoria_ID ASC, b.Equipo_FULLDESC ASC";
	$result = $Config->query($sql);
	$teams = array();
	if ($result && $result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$teams[] = array(
				'id' => (int) $row['Equipo_ID'],
				'label' => $row['Equipo_FULLDESC'],
			);
		}
	}

	if (count($teams) === 0) {
		echo json_encode(array('status' => '0', 'message' => $lang['539-14'], 'teams' => array()));
		exit;
	}

	echo json_encode(array(
		'status' => '1',
		'message' => $lang['539-10'],
		'prompt' => $lang['539-10'],
		'cancel' => $lang['0001'],
		'ok' => $lang['0000'],
		'teams' => $teams,
	));
