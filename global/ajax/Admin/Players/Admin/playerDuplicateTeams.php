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
	$username = SanitizeUsername($_SESSION[$Config->getAlias() . 'username'] ?? '');
	$isAdmin = false;
	$ids = array();
	$ue = $Config->query("SELECT DISTINCT Equipo_ID FROM $schema.usuarios_equipo WHERE username = '" . $username . "'");
	if ($ue && $ue->num_rows > 0) {
		while ($r = $ue->fetch_assoc()) {
			$id = (int) $r['Equipo_ID'];
			if ($id === 0 || $id === -1) {
				$isAdmin = true;
			} elseif ($id > 0) {
				$ids[] = $id;
			}
		}
	}
	$sessionEq = $fgmembersite->UserEquipo();
	if ($sessionEq == 0 || $sessionEq == -1 || $sessionEq === '0' || $sessionEq === '-1') {
		$isAdmin = true;
	}
	if (!$isAdmin && count($ids) === 0) {
		foreach (explode(',', (string) $sessionEq) as $part) {
			$id = (int) $part;
			if ($id > 0) {
				$ids[] = $id;
			}
		}
	}

	$teamFilter = '';
	if (!$isAdmin) {
		$ids = array_values(array_unique($ids));
		if (count($ids) === 0) {
			echo json_encode(array('status' => '0', 'message' => $lang['539-14'], 'teams' => array()));
			exit;
		}
		$teamFilter = ' AND b.Equipo_ID IN (' . implode(',', $ids) . ')';
	}
	if ($currentTeam > 0) {
		$teamFilter .= ' AND b.Equipo_ID <> ' . $currentTeam;
	}

	$sql = "SELECT b.Equipo_ID, c.categoria_DESC, b.Equipo_FULLDESC
		FROM (
			SELECT a.*
			FROM $schema.Equipos a
			WHERE a.Equipo_ID > 0 AND a.Torneo_ID = $Season
		) b
			JOIN $schema.Categorias c ON b.Fuerza = c.Categoria_ID AND c.Torneo_ID = $Season
		WHERE b.Equipo_ID > 0 $teamFilter
		ORDER BY c.categoria_ID ASC, b.Equipo_FULLDESC ASC";
	$result = $Config->query($sql);
	$teams = array();
	if ($result && $result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$categoria = mb_convert_encoding((string) $row['categoria_DESC'], 'UTF-8', 'ISO-8859-1');
			$equipo = mb_convert_encoding((string) $row['Equipo_FULLDESC'], 'UTF-8', 'ISO-8859-1');
			$teams[] = array(
				'id' => (int) $row['Equipo_ID'],
				'label' => $categoria . ' - ' . $equipo,
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
	), JSON_UNESCAPED_UNICODE);
