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
	$sessionstat = $fgmembersite->CheckLogin('weekAdmin-ScheduleScoresGameDetailS.php');

	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	header('Content-Type: application/json; charset=utf-8');

	$LTeam = SanitizeInteger($_POST['LTeam']);
	$VTeam = SanitizeInteger($_POST['VTeam']);
	$Game = SanitizeInteger($_POST['Game']);
	$Week = SanitizeInteger($_POST['Week']);
	$Season = SanitizeInteger($_POST['Season']);
	$scoresTeam = isset($_POST['gamedata']) ? trim((string) $_POST['gamedata']) : '';
	$userName = $_SESSION[$Config->getAlias() . 'username'];

	if ($scoresTeam === '') {
		echo json_encode(array('status' => '1', 'message' => 'No data.', 'error' => '', 'errorct' => 0));
		exit;
	}

	$Connection = $Config->connectAdmin();
	$userEsc = $Connection->real_escape_string($userName);

	/* The posted teams must be the ones playing this game. */
	$gameOk = false;
	$sql = "SELECT Local_ID, Visitante_ID
			FROM $schema.Juegos
			WHERE Juego_ID = $Game and Jornada_ID = $Week and Torneo_ID = $Season;";
	$result = $Connection->query($sql);
	if ($result && $row = $result->fetch_assoc()) {
		$gameOk = ((int) $row['Local_ID'] === (int) $LTeam && (int) $row['Visitante_ID'] === (int) $VTeam);
	}
	if (!$gameOk) {
		$Connection->Close();
		echo json_encode(array('status' => '0', 'message' => 'Juego no encontrado.', 'error' => 'Juego no encontrado.', 'errorct' => 1));
		exit;
	}

	$teamDesc = array('l' => '', 'v' => '');
	foreach (array('l' => $LTeam, 'v' => $VTeam) as $side => $teamId) {
		$sql = "SELECT Equipo_DESC FROM $schema.Equipos WHERE Equipo_ID = $teamId and Torneo_ID = $Season;";
		$result = $Connection->query($sql);
		if ($result && $row = $result->fetch_assoc()) {
			$teamDesc[$side] = $row['Equipo_DESC'];
		}
	}

	/*
	 * One line per basket: team (l|v), quarter (1-5), player number, team score after the basket.
	 * Example: l,1,12,3 -> local team, 1st quarter, player #12, local score is now 3.
	 */
	$errors = array();
	$formatErrors = array();
	$lastScore = array('l' => 0, 'v' => 0);
	$quarterScore = array('l' => array(), 'v' => array());
	$playerBaskets = array('l' => array(), 'v' => array());

	$lineNo = 0;
	foreach (preg_split("/\r\n|\r|\n/", $scoresTeam) as $line) {
		$lineNo++;
		$line = trim($line);
		if ($line === '') {
			continue;
		}
		$data = array_map('trim', str_getcsv($line, ','));
		$side = isset($data[0]) ? strtolower($data[0]) : '';
		if (count($data) < 4 || ($side !== 'l' && $side !== 'v')
			|| !preg_match('/^[1-5]$/', $data[1])
			|| !preg_match('/^\d{1,3}$/', $data[2])
			|| !preg_match('/^\d{1,4}$/', $data[3])) {
			$formatErrors[] = "Linea $lineNo con formato invalido: $line";
			continue;
		}
		$quarter = (int) $data[1];
		$number = (int) $data[2];
		$score = (int) $data[3];

		$points = $score - $lastScore[$side];
		$lastScore[$side] = $score;
		$quarterScore[$side][$quarter] = $score;

		if ($points >= 1 && $points <= 3) {
			if (!isset($playerBaskets[$side][$number])) {
				$playerBaskets[$side][$number] = array(1 => 0, 2 => 0, 3 => 0);
			}
			$playerBaskets[$side][$number][$points]++;
		} elseif ($points !== 0) {
			$errors[] = "Linea $lineNo: el marcador cambia $points puntos ($line)";
		}
	}

	if (count($formatErrors) > 0) {
		$Connection->Close();
		$msg = implode("\n", $formatErrors);
		echo json_encode(array('status' => '1', 'message' => 'Format error.', 'error' => $msg, 'errorct' => count($formatErrors)));
		exit;
	}

	/* Quarter points from the cumulative score; a quarter with no lines keeps the previous total. */
	$sets = array('l' => array(), 'v' => array());
	foreach (array('l', 'v') as $side) {
		$prevCum = 0;
		for ($q = 1; $q <= 5; $q++) {
			$cum = isset($quarterScore[$side][$q]) ? max($quarterScore[$side][$q], $prevCum) : $prevCum;
			$sets[$side][$q] = $cum - $prevCum;
			$prevCum = $cum;
		}
	}

	$sql = "CALL $schema.GameUpdateSets('$userEsc', "
		. implode(', ', $sets['l']) . ", " . implode(', ', $sets['v']) . ", $Game, @out);";
	$Connection->query($sql);
	while ($Connection->more_results() && $Connection->next_result()) {
	}

	$sql = "DELETE FROM $schema.PuntosB
			WHERE Equipo_ID in ($LTeam, $VTeam)
				and Juego_ID = $Game
				and Jornada_ID = $Week
				and Torneo_ID = $Season;";
	$Connection->query($sql);

	$sideLabel = array('l' => 'Local', 'v' => 'Visitante');
	foreach (array('l' => $LTeam, 'v' => $VTeam) as $side => $teamId) {
		foreach ($playerBaskets[$side] as $number => $baskets) {
			$sql = "SELECT Jugador_ID
					FROM $schema.Jugadores
					WHERE Equipo_ID = $teamId
						and Estatus in ('A', 'D')
						and cast(Numero as decimal) = $number;";
			$result = $Connection->query($sql);
			$ct = $result ? $result->num_rows : 0;
			if ($ct === 1) {
				$row = $result->fetch_assoc();
				$playerId = (int) $row['Jugador_ID'];
				$sql = "CALL $schema.GameUpdateDetailGoalsBasket('$userEsc', $playerId, $teamId, $Game, $Week, $Season, "
					. $baskets[1] . ", " . $baskets[2] . ", " . $baskets[3] . ", @out);";
				$Connection->query($sql);
				while ($Connection->more_results() && $Connection->next_result()) {
				}
			} elseif ($ct > 1) {
				$errors[] = "Hay mas de 1 jugador con el numero $number registrados en el Equipo " . $sideLabel[$side] . " " . $teamDesc[$side];
			} else {
				$errors[] = "No hay jugadores registrados con el numero $number en el Equipo " . $sideLabel[$side] . " " . $teamDesc[$side];
			}
		}
	}

	if ($Config->juegosHasColumn('LoadInfo')) {
		$sql = "UPDATE $schema.Juegos
				SET LoadInfo = '" . $Connection->real_escape_string($scoresTeam) . "'
				WHERE Juego_ID = $Game and Jornada_ID = $Week and Torneo_ID = $Season;";
		$Connection->query($sql);
	}

	$Connection->Close();
	echo json_encode(array(
		'status' => '1',
		'message' => 'Success.',
		'error' => implode("\n", $errors),
		'errorct' => count($errors),
		'local' => $lastScore['l'],
		'visitante' => $lastScore['v'],
	));
?>
