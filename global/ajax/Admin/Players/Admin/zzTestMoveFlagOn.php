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
	require_once __DIR__ . '/player_team_move.inc.php';
	$schema = $Config->getSchema();
	$fgmembersite->CheckLogin('playerMoveToTeam.php');
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	$Config->LoadFlags(); $Config->allowCopyDuplicate = 1;

	$retunData = array('status' => '0', 'message' => $lang['539-12']);
	if ((int) $Config->searchPlayer !== 1) {
		echo json_encode($retunData);
		exit;
	}

	$Season = (int) SanitizeInteger($_COOKIE[$Config->getAlias() . 'season'] ?? 0);
	$player = (int) SanitizeInteger($_POST['player'] ?? 0);
	$team = (int) SanitizeInteger($_POST['team'] ?? 0);
	if ($player <= 0 || $team <= 0) {
		echo json_encode($retunData);
		exit;
	}

	$isAdmin = az_user_is_league_admin($Config, $schema, $fgmembersite);
	$allowed = az_user_allowed_equipo_ids($Config, $schema, $fgmembersite, $isAdmin);
	$src = $Config->query("SELECT Equipo_ID FROM $schema.Jugadores WHERE Jugador_ID = $player LIMIT 1");
	$srcTeam = 0;
	if ($src && $srcRow = $src->fetch_assoc()) {
		$srcTeam = (int) $srcRow['Equipo_ID'];
	}
	if ((int) $Config->allowCopyDuplicate === 1) {
		$action = az_player_found_action($Config, $schema, $player, $Season);
		if ($action === 'suspended' || $action !== 'move' || (!$isAdmin && !in_array($team, $allowed, true))) {
			echo json_encode(array('status' => '0', 'message' => $action === 'suspended' ? $lang['539-35'] : $lang['539-16']));
			exit;
		}
	} elseif (!az_player_coach_can_move_to_team($Config, $schema, $srcTeam, $team, $allowed, $isAdmin)) {
		echo json_encode(array('status' => '0', 'message' => $lang['539-16']));
		exit;
	}

	$already = $Config->query("SELECT Jugador_ID FROM $schema.Jugadores WHERE Equipo_ID = $team AND Jugador_ID = $player LIMIT 1");
	if ($already && $already->num_rows > 0) {
		echo json_encode(array('status' => '1', 'message' => $lang['539-7'], 'categoria' => 0));
		exit;
	}

	$dup = $Config->query("SELECT a.Jugador_ID FROM $schema.Jugadores a
		JOIN $schema.Jugadores b ON a.Curp = b.Curp
		WHERE b.Jugador_ID = $player AND a.Equipo_ID = $team AND a.Jugador_ID <> $player LIMIT 1");
	if ($dup && $dup->num_rows > 0) {
		echo json_encode(array('status' => '0', 'message' => $lang['539-15']));
		exit;
	}

	echo json_encode(array("status" => "WOULD_MOVE")); exit;
	$ok = $Connection && $Connection->query("UPDATE $schema.Jugadores SET Equipo_ID = $team WHERE Jugador_ID = $player");
	$categoria = 0;
	$catRes = $Config->query("SELECT Fuerza FROM $schema.Equipos WHERE Equipo_ID = $team AND Torneo_ID = $Season LIMIT 1");
	if ($catRes && $catRow = $catRes->fetch_assoc()) {
		$categoria = (int) $catRow['Fuerza'];
	}
	if ($Connection) {
		$Connection->Close();
	}

	if ($ok) {
		echo json_encode(array('status' => '1', 'message' => $lang['539-9'], 'categoria' => $categoria, 'equipo' => $team));
		exit;
	}
	echo json_encode($retunData);
