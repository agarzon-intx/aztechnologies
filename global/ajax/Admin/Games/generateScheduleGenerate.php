<?php
	session_start();
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

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
	$sessionstat = $fgmembersite->CheckLogin('generateScheduleGenerate.php');

	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'generate_schedule_seeds.inc.php';

	$retunData = array('status' => '0', 'message' => $lang['js0002']);

	$Season = 0;
	if (isset($_COOKIE[$Config->getAlias() . 'season']) && $_COOKIE[$Config->getAlias() . 'season'] !== '') {
		$Season = SanitizeInteger($_COOKIE[$Config->getAlias() . 'season']);
	}
	if ($Season <= 0) {
		$resActual = $Config->query("SELECT Torneo_ID FROM $schema.Torneos WHERE Actual = 'S' ORDER BY Torneo_ID DESC LIMIT 1");
		if ($resActual && $resActual->num_rows > 0) {
			$rowActual = $resActual->fetch_assoc();
			$Season = (int) $rowActual['Torneo_ID'];
		}
	}
	if ($Season > 0) {
		$resTorneo = $Config->query("SELECT Torneo_ID FROM $schema.Torneos WHERE Torneo_ID = $Season LIMIT 1");
		if (!$resTorneo || $resTorneo->num_rows === 0) {
			$Season = 0;
		}
	}
	if ($Season <= 0) {
		$retunData = array('status' => '0', 'message' => $lang['101-14']);
		header('Content-Type: application/json');
		echo json_encode($retunData);
		exit();
	}

	$username = $_SESSION[$Config->getAlias() . 'username'];

	/**
	 * Circle-method single round-robin with home/away balance.
	 * Returns array of rounds => list of [homeId, awayId] (null = bye).
	 */
	function az_rr_balanced_rounds(array $teamIds) {
		$teams = array_values($teamIds);
		$n = count($teams);
		if ($n < 2) {
			return array();
		}
		$bye = false;
		if ($n % 2 === 1) {
			$teams[] = null;
			$n++;
			$bye = true;
		}
		$rounds = $n - 1;
		$half = (int) ($n / 2);
		$result = array();
		for ($r = 0; $r < $rounds; $r++) {
			$games = array();
			for ($i = 0; $i < $half; $i++) {
				$a = $teams[$i];
				$b = $teams[$n - 1 - $i];
				if ($a === null || $b === null) {
					continue;
				}
				// Alternate home/away by round and slot for balance.
				if (($r + $i) % 2 === 0) {
					$games[] = array($a, $b);
				} else {
					$games[] = array($b, $a);
				}
			}
			$result[] = $games;
			// Rotate all but first.
			$fixed = array_shift($teams);
			$last = array_pop($teams);
			array_unshift($teams, $last);
			array_unshift($teams, $fixed);
		}
		return $result;
	}

	$weeksByCategory = array();
	if (isset($_POST['weeks']) && is_array($_POST['weeks'])) {
		foreach ($_POST['weeks'] as $catId => $weeks) {
			$cid = SanitizeInteger($catId);
			$w = SanitizeInteger($weeks);
			if ($cid > 0) {
				$weeksByCategory[$cid] = $w;
			}
		}
	}

	if (count($weeksByCategory) === 0) {
		$retunData = array('status' => '0', 'message' => $lang['101-8']);
		header('Content-Type: application/json');
		echo json_encode($retunData);
		exit();
	}

	// Categories that share Calendario_ID must use the same weeks count.
	$calByCat = array();
	$weeksByCal = array();
	$catIdsList = implode(',', array_map('intval', array_keys($weeksByCategory)));
	if ($catIdsList !== '') {
		$resCal = $Config->query("SELECT Categoria_ID, Calendario_ID
				FROM $schema.Categorias
				WHERE Torneo_ID = $Season
					AND Categoria_ID IN ($catIdsList)");
		if ($resCal && $resCal->num_rows > 0) {
			while ($row = $resCal->fetch_assoc()) {
				$cid = (int) $row['Categoria_ID'];
				$calId = (int) $row['Calendario_ID'];
				$calByCat[$cid] = $calId;
				if ($calId > 0 && isset($weeksByCategory[$cid])) {
					if (!isset($weeksByCal[$calId]) || (int) $weeksByCategory[$cid] > (int) $weeksByCal[$calId]) {
						$weeksByCal[$calId] = (int) $weeksByCategory[$cid];
					}
				}
			}
		}
		foreach ($weeksByCategory as $cid => $w) {
			$calId = isset($calByCat[$cid]) ? (int) $calByCat[$cid] : 0;
			if ($calId > 0 && isset($weeksByCal[$calId])) {
				$weeksByCategory[$cid] = (int) $weeksByCal[$calId];
			}
		}
	}

	$institutionSeeds = az_generate_schedule_institution_seeds($Config, $schema, $Season);

	// Optional per-category team order from the UI (after manual reordering).
	$teamOrderByCategory = array();
	if (isset($_POST['teamOrder']) && is_array($_POST['teamOrder'])) {
		foreach ($_POST['teamOrder'] as $catIdRaw => $ids) {
			$cid = SanitizeInteger($catIdRaw);
			if ($cid <= 0 || !is_array($ids)) {
				continue;
			}
			$ordered = array();
			$seen = array();
			foreach ($ids as $idRaw) {
				$tid = SanitizeInteger($idRaw);
				if ($tid > 0 && !isset($seen[$tid])) {
					$seen[$tid] = true;
					$ordered[] = $tid;
				}
			}
			if (count($ordered) > 0) {
				$teamOrderByCategory[$cid] = $ordered;
			}
		}
	}

	$created = 0;
	$skipped = 0;
	$errors = array();

	foreach ($weeksByCategory as $catId => $weeksRequested) {
		if ($weeksRequested < 1) {
			$errors[] = str_replace('%1', (string) $catId, $lang['101-9']);
			continue;
		}

		// Prefer UI order when provided; otherwise tournament-wide institution seeds.
		$teamIds = array();
		if (isset($teamOrderByCategory[$catId]) && count($teamOrderByCategory[$catId]) >= 2) {
			$allowed = array();
			$sqlTeams = "SELECT Equipo_ID FROM $schema.Equipos
					WHERE Torneo_ID = $Season
						AND Fuerza = $catId
						AND IFNULL(Activo, 0) = 1";
			$resTeams = $Config->query($sqlTeams);
			if ($resTeams && $resTeams->num_rows > 0) {
				while ($tr = $resTeams->fetch_assoc()) {
					$allowed[(int) $tr['Equipo_ID']] = true;
				}
			}
			foreach ($teamOrderByCategory[$catId] as $tid) {
				if (isset($allowed[$tid])) {
					$teamIds[] = $tid;
					unset($allowed[$tid]);
				}
			}
			// Append any active teams missing from the posted order (stable fallback).
			foreach (array_keys($allowed) as $tid) {
				$teamIds[] = (int) $tid;
			}
		}
		if (count($teamIds) < 2) {
			$teamIds = az_generate_schedule_seeded_team_ids_for_category($Config, $schema, $Season, $catId, $institutionSeeds);
		}

		if (count($teamIds) < 2) {
			$errors[] = str_replace('%1', (string) $catId, $lang['101-10']);
			continue;
		}

		// Existing weeks for this category's calendar, ordered.
		$jornadas = array();
		$calId = isset($calByCat[$catId]) ? (int) $calByCat[$catId] : 0;
		if ($calId <= 0) {
			$resCalOne = $Config->query("SELECT Calendario_ID FROM $schema.Categorias WHERE Torneo_ID = $Season AND Categoria_ID = $catId LIMIT 1");
			if ($resCalOne && $resCalOne->num_rows > 0) {
				$rowCal = $resCalOne->fetch_assoc();
				$calId = (int) $rowCal['Calendario_ID'];
			}
		}
		if ($calId > 0) {
			$sqlJ = "SELECT j.Jornada_ID, j.Fecha_Inicio, j.Fecha
					FROM $schema.Jornada j
					WHERE j.Torneo_ID = $Season
						AND j.Calendario_ID = $calId
					ORDER BY j.Jornada_Orden ASC, j.Jornada_ID ASC";
			$resJ = $Config->query($sqlJ);
			if ($resJ && $resJ->num_rows > 0) {
				while ($j = $resJ->fetch_assoc()) {
					$jornadas[] = $j;
				}
			}
		}

		if (count($jornadas) < $weeksRequested) {
			$errors[] = str_replace(
				array('%1', '%2', '%3'),
				array((string) $catId, (string) $weeksRequested, (string) count($jornadas)),
				$lang['101-11']
			);
			continue;
		}

		$rounds = az_rr_balanced_rounds($teamIds);
		if (count($rounds) === 0) {
			$errors[] = str_replace('%1', (string) $catId, $lang['101-10']);
			continue;
		}

		// Repeat RR cycles if more weeks than a single RR needs.
		$scheduleRounds = array();
		while (count($scheduleRounds) < $weeksRequested) {
			foreach ($rounds as $roundGames) {
				$scheduleRounds[] = $roundGames;
				if (count($scheduleRounds) >= $weeksRequested) {
					break;
				}
			}
		}

		$teamIdList = implode(',', array_map('intval', $teamIds));
		$Connection = $Config->connectAdmin();

		for ($wi = 0; $wi < $weeksRequested; $wi++) {
			$jornadaId = (int) $jornadas[$wi]['Jornada_ID'];
			$fecha = $jornadas[$wi]['Fecha_Inicio'];
			if ($fecha === null || $fecha === '') {
				$fecha = $jornadas[$wi]['Fecha'];
			}
			if ($fecha === null || $fecha === '') {
				$fecha = date('Y-m-d');
			}
			$fechaEsc = $Connection->real_escape_string($fecha);

			// Skip creating duplicates if games already exist this week for these teams.
			$sqlExist = "SELECT COUNT(*) AS cnt
					FROM $schema.Juegos j
					WHERE j.Torneo_ID = $Season
						AND j.Jornada_ID = $jornadaId
						AND (j.Local_ID IN ($teamIdList) OR j.Visitante_ID IN ($teamIdList))";
			$resExist = $Connection->query($sqlExist);
			$existCnt = 0;
			if ($resExist && $resExist->num_rows > 0) {
				$rowE = $resExist->fetch_assoc();
				$existCnt = (int) $rowE['cnt'];
			}
			if ($existCnt > 0) {
				$skipped += count($scheduleRounds[$wi]);
				continue;
			}

			foreach ($scheduleRounds[$wi] as $pair) {
				$home = (int) $pair[0];
				$away = (int) $pair[1];
				$userEsc = $Connection->real_escape_string($username);
				$sql = "CALL $schema.GameCreate('$userEsc', $home, $away, $jornadaId, $Season, '$fechaEsc', 0, @out);";
				$ok = $Connection->query($sql);
				if ($ok) {
					$Connection->query("SELECT @out AS 'count'");
					$created++;
				} else {
					$errors[] = $lang['js0002'];
				}
			}
		}
		$Connection->Close();
	}

	if ($created > 0 && count($errors) === 0) {
		$msg = str_replace(
			array('%1', '%2'),
			array((string) $created, (string) $skipped),
			$lang['101-12']
		);
		$retunData = array('status' => '1', 'message' => $msg, 'created' => $created, 'skipped' => $skipped);
	} elseif ($created > 0) {
		$msg = str_replace(
			array('%1', '%2'),
			array((string) $created, (string) $skipped),
			$lang['101-12']
		);
		$retunData = array(
			'status' => '1',
			'message' => $msg . "\n" . implode("\n", $errors),
			'created' => $created,
			'skipped' => $skipped,
		);
	} else {
		$retunData = array(
			'status' => '0',
			'message' => count($errors) ? implode("\n", $errors) : $lang['101-13'],
			'created' => 0,
			'skipped' => $skipped,
		);
	}

	header('Content-Type: application/json');
	echo json_encode($retunData);
	exit();
?>
