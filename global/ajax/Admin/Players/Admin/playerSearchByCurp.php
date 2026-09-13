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
	$fgmembersite->CheckLogin('playerSearchByCurp.php');
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	$Config->LoadFlags();

	$retunData = array('status' => '0', 'message' => $lang['539-8']);
	if ((int) $Config->searchPlayer !== 1) {
		echo json_encode($retunData);
		exit;
	}

	$Season = SanitizeInteger($_COOKIE[$Config->getAlias() . 'season'] ?? 0);
	$team = SanitizeInteger($_POST['team'] ?? 0);
	$curp = trim((string) SanitizeText($_POST['curp'] ?? ''));
	if ($curp === '' || $team <= 0) {
		$retunData['message'] = $lang['539-5'];
		echo json_encode($retunData);
		exit;
	}

	$conn = $Config->connect();
	$curpEsc = $conn ? $conn->real_escape_string($curp) : addslashes($curp);

	$sqlSame = "SELECT a.Jugador_ID, a.Nombre, a.Apellido_P, a.Apellido_M, a.Equipo_ID,
			b.Equipo_FULLDESC, c.Categoria_Desc, c.Categoria_ID
		FROM $schema.Jugadores a
			JOIN $schema.Equipos b ON a.Equipo_ID = b.Equipo_ID AND b.Torneo_ID = $Season
			JOIN $schema.Categorias c ON b.Fuerza = c.Categoria_ID AND c.Torneo_ID = $Season
		WHERE a.Curp = '$curpEsc' AND a.Equipo_ID = $team
		LIMIT 1";
	$result = $Config->query($sqlSame);
	if ($result && $result->num_rows > 0) {
		$row = $result->fetch_assoc();
		$playerName = trim($row['Nombre'] . ' ' . $row['Apellido_P'] . ' ' . $row['Apellido_M']);
		echo json_encode(array(
			'status' => '1',
			'found' => 1,
			'sameTeam' => 1,
			'player' => (int) $row['Jugador_ID'],
			'categoria' => (int) $row['Categoria_ID'],
			'equipo' => (int) $row['Equipo_ID'],
			'message' => $lang['539-7'] . ': ' . $playerName . ' — ' . $row['Categoria_Desc'] . ' / ' . $row['Equipo_FULLDESC'],
		));
		exit;
	}

	$sqlOther = "SELECT a.Jugador_ID, a.Nombre, a.Apellido_P, a.Apellido_M, a.Equipo_ID,
			b.Equipo_FULLDESC, c.Categoria_Desc, c.Categoria_ID
		FROM $schema.Jugadores a
			JOIN $schema.Equipos b ON a.Equipo_ID = b.Equipo_ID
			JOIN $schema.Categorias c ON b.Fuerza = c.Categoria_ID
		WHERE a.Curp = '$curpEsc' AND a.Equipo_ID <> $team
		ORDER BY b.Torneo_ID DESC, a.Jugador_ID DESC
		LIMIT 1";
	$result = $Config->query($sqlOther);
	if ($result && $result->num_rows > 0) {
		$row = $result->fetch_assoc();
		$playerName = trim($row['Nombre'] . ' ' . $row['Apellido_P'] . ' ' . $row['Apellido_M']);
		$playerTeam = (int) $row['Equipo_ID'];
		$equipoIds = $fgmembersite->UserEquipo();
		$isAdmin = ($equipoIds === '0' || $equipoIds === '-1');
		$allowed = array_map('intval', explode(',', (string) $equipoIds));
		$canMove = $isAdmin || in_array($playerTeam, $allowed, true);
		$msgKey = $canMove ? '539-6' : '539-16';
		$confirm = str_replace(
			array('%1', '%2', '%3'),
			array($row['Categoria_Desc'], $row['Equipo_FULLDESC'], $playerName),
			$lang[$msgKey]
		);
		echo json_encode(array(
			'status' => '1',
			'found' => 1,
			'sameTeam' => 0,
			'canMove' => $canMove ? 1 : 0,
			'player' => (int) $row['Jugador_ID'],
			'categoria' => (int) $row['Categoria_ID'],
			'equipo' => $playerTeam,
			'playerName' => $playerName,
			'categoriaDesc' => $row['Categoria_Desc'],
			'equipoDesc' => $row['Equipo_FULLDESC'],
			'message' => $confirm,
		), JSON_UNESCAPED_UNICODE);
		exit;
	}

	echo json_encode(array(
		'status' => '1',
		'found' => 0,
		'sameTeam' => 0,
		'message' => $lang['539-8'],
	));
