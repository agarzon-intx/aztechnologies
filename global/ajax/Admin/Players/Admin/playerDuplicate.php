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
	$fgmembersite->CheckLogin('playerDuplicate.php');
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	$Config->LoadFlags();

	$retunData = array('status' => '0', 'message' => $lang['539-12']);
	if ((int) $Config->duplicatePlayer !== 1) {
		echo json_encode($retunData);
		exit;
	}

	$Season = SanitizeInteger($_COOKIE[$Config->getAlias() . 'season'] ?? 0);
	$player = SanitizeInteger($_POST['player'] ?? 0);
	$team = SanitizeInteger($_POST['team'] ?? 0);
	if ($player <= 0 || $team <= 0) {
		echo json_encode($retunData);
		exit;
	}

	$equipoIds = $fgmembersite->UserEquipo();
	$isAdmin = ($equipoIds === '0' || $equipoIds === '-1');
	if (!$isAdmin) {
		$allowed = array_map('intval', explode(',', (string) $equipoIds));
		if (!in_array($team, $allowed, true)) {
			echo json_encode($retunData);
			exit;
		}
	}

	$src = $Config->query("SELECT Curp, Equipo_ID FROM $schema.Jugadores WHERE Jugador_ID = $player LIMIT 1");
	if (!$src || $src->num_rows === 0) {
		echo json_encode($retunData);
		exit;
	}
	$row = $src->fetch_assoc();
	$curp = (string) $row['Curp'];
	if ((int) $row['Equipo_ID'] === $team) {
		echo json_encode(array('status' => '0', 'message' => $lang['539-15']));
		exit;
	}

	$conn = $Config->connect();
	$curpEsc = $conn ? $conn->real_escape_string($curp) : addslashes($curp);
	$exists = $Config->query("SELECT Jugador_ID FROM $schema.Jugadores WHERE Curp = '$curpEsc' AND Equipo_ID = $team LIMIT 1");
	if ($exists && $exists->num_rows > 0) {
		echo json_encode(array('status' => '0', 'message' => $lang['539-15']));
		exit;
	}

	$username = SanitizeUsername($_SESSION[$Config->getAlias() . 'username'] ?? '');
	$sql0 = "CALL $schema.PlayerCopy('" . $username . "', '$curpEsc', $team, @out);";
	$Connection = $Config->connectAdmin();
	if (!$Connection) {
		echo json_encode($retunData);
		exit;
	}
	$Connection->query($sql0);
	$out = $Connection->query("SELECT @out AS count");
	$ok = $out && $out->num_rows > 0;
	$categoria = 0;
	$catRes = $Config->query("SELECT Fuerza FROM $schema.Equipos WHERE Equipo_ID = $team AND Torneo_ID = $Season LIMIT 1");
	if ($catRes && $catRow = $catRes->fetch_assoc()) {
		$categoria = (int) $catRow['Fuerza'];
	}
	$Connection->Close();

	if ($ok) {
		echo json_encode(array('status' => '1', 'message' => $lang['539-11'], 'categoria' => $categoria, 'equipo' => $team));
		exit;
	}
	echo json_encode($retunData);
