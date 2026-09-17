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

	$Season = (int) SanitizeInteger($_COOKIE[$Config->getAlias() . 'season'] ?? 0);
	$player = (int) SanitizeInteger($_POST['player'] ?? 0);
	$team = (int) SanitizeInteger($_POST['team'] ?? 0);
	if ($player <= 0 || $team <= 0) {
		echo json_encode($retunData);
		exit;
	}

	$username = SanitizeUsername($_SESSION[$Config->getAlias() . 'username'] ?? '');
	$isAdmin = false;
	$allowed = array();
	$ue = $Config->query("SELECT DISTINCT Equipo_ID FROM $schema.usuarios_equipo WHERE username = '" . $username . "'");
	if ($ue && $ue->num_rows > 0) {
		while ($r = $ue->fetch_assoc()) {
			$id = (int) $r['Equipo_ID'];
			if ($id === 0 || $id === -1) {
				$isAdmin = true;
			} elseif ($id > 0) {
				$allowed[] = $id;
			}
		}
	}
	$equipoIds = $fgmembersite->UserEquipo();
	if ($equipoIds == 0 || $equipoIds == -1 || $equipoIds === '0' || $equipoIds === '-1') {
		$isAdmin = true;
	}
	if (!$isAdmin && count($allowed) === 0) {
		foreach (explode(',', (string) $equipoIds) as $part) {
			$id = (int) $part;
			if ($id > 0) {
				$allowed[] = $id;
			}
		}
	}
	if (!$isAdmin && !in_array($team, $allowed, true)) {
		echo json_encode($retunData);
		exit;
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

	$Connection = $Config->connectAdmin();
	if (!$Connection) {
		echo json_encode($retunData);
		exit;
	}
	$Connection->set_charset('utf8');

	$nextNum = 1;
	$numRes = $Config->queryAdmin("SELECT IFNULL(MAX(CAST(Numero AS UNSIGNED)), 0) + 1 AS n
		FROM $schema.Jugadores
		WHERE Equipo_ID = $team AND Numero REGEXP '^[0-9]+$'");
	if ($numRes && $numRow = $numRes->fetch_assoc()) {
		$n = (int) $numRow['n'];
		if ($n > 0) {
			$nextNum = $n;
		}
	}

	$cols = array(
		'Clave', 'Nombre', 'Apellido_P', 'Apellido_M', 'Fecha_Nacimiento', 'Estatus',
		'Equipo_ID', 'Validado', 'Comentarios', 'FechaAlta', 'FechaCambio', 'Curp',
		'Numero', 'Telefono', 'correo', 'Apodo', 'Foto', 'Identificacion', 'Firma',
		'FechaValidacionCurp', 'IntentosValidacionCurp', 'Actualizado',
		'ValidacionCurpComentario', 'Sexo',
	);
	$selects = array(
		'Clave', 'Nombre', 'Apellido_P', 'Apellido_M', 'Fecha_Nacimiento', 'Estatus',
		(string) $team, '0', 'Comentarios', 'NOW()', 'NOW()', 'Curp',
		"'" . $nextNum . "'", 'Telefono', 'correo', 'Apodo', 'Foto', 'Identificacion', 'Firma',
		'FechaValidacionCurp', 'IntentosValidacionCurp', '0',
		'ValidacionCurpComentario', 'Sexo',
	);
	if ($Config->jugadoresHasColumn('Jugador_tipo')) {
		$cols[] = 'Jugador_tipo';
		$selects[] = 'Jugador_tipo';
	}
	if ($Config->jugadoresHasColumn('Fecha_Validacion')) {
		$cols[] = 'Fecha_Validacion';
		$selects[] = 'NULL';
	}
	if ($Config->jugadoresHasColumn('Fecha_Alta')) {
		$cols[] = 'Fecha_Alta';
		$selects[] = 'NOW()';
	}
	if ($Config->jugadoresHasColumn('Fecha_Baja')) {
		$cols[] = 'Fecha_Baja';
		$selects[] = 'NULL';
	}
	if ($Config->jugadoresHasColumn('IdentificacionPDF')) {
		$cols[] = 'IdentificacionPDF';
		$selects[] = 'IdentificacionPDF';
	}

	$sqlInsert = 'INSERT INTO ' . $schema . '.Jugadores (' . implode(', ', $cols) . ') SELECT '
		. implode(', ', $selects) . ' FROM (SELECT * FROM ' . $schema . '.Jugadores WHERE Jugador_ID = ' . $player . ' LIMIT 1) src';
	$okIns = $Connection->query($sqlInsert);
	$newId = $okIns ? (int) $Connection->insert_id : 0;
	$insertErr = (!$okIns && !empty($Connection->error)) ? (string) $Connection->error : '';
	if ($newId <= 0) {
		$found = $Config->query("SELECT Jugador_ID FROM $schema.Jugadores WHERE Curp = '$curpEsc' AND Equipo_ID = $team AND Jugador_ID <> $player ORDER BY Jugador_ID DESC LIMIT 1");
		if ($found && $foundRow = $found->fetch_assoc()) {
			$newId = (int) $foundRow['Jugador_ID'];
		}
	}

	$categoria = 0;
	$catRes = $Config->query("SELECT Fuerza FROM $schema.Equipos WHERE Equipo_ID = $team AND Torneo_ID = $Season LIMIT 1");
	if ($catRes && $catRow = $catRes->fetch_assoc()) {
		$categoria = (int) $catRow['Fuerza'];
	}
	$Connection->Close();

	if ($newId > 0) {
		echo json_encode(array('status' => '1', 'message' => $lang['539-11'], 'categoria' => $categoria, 'equipo' => $team));
		exit;
	}
	if ($insertErr !== '') {
		$retunData['message'] = $lang['539-12'] . ' (' . $insertErr . ')';
	}
	echo json_encode($retunData);
