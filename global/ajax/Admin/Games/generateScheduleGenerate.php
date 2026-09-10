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

	$retunData = array('status' => '0', 'message' => $lang['js0002']);
	$Season = SanitizeInteger($_COOKIE[$Config->getAlias() . 'season']);
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

	$created = 0;
	$skipped = 0;
	$errors = array();

	foreach ($weeksByCategory as $catId => $weeksRequested) {
		if ($weeksRequested < 1) {
			$errors[] = str_replace('%1', (string) $catId, $lang['101-9']);
			continue;
		}

		// Seeded team order: institutions by team count DESC, Institucion_DESC ASC; teams by Equipo_DESC.
		$teamIds = array();
		$sqlSeeds = "SELECT IFNULL(e.Institucion_ID, 0) AS Institucion_ID,
					IFNULL(NULLIF(TRIM(i.Institucion_DESC), ''), CONCAT('#', IFNULL(e.Institucion_ID, 0))) AS Institucion_DESC,
					COUNT(e.Equipo_ID) AS TeamCount
				FROM $schema.Equipos e
					LEFT JOIN $schema.Instituciones i
						ON i.Institucion_ID = e.Institucion_ID
						AND i.Torneo_ID = e.Torneo_ID
				WHERE e.Torneo_ID = $Season
					AND e.Fuerza = $catId
					AND IFNULL(e.Activo, 0) = 1
				GROUP BY IFNULL(e.Institucion_ID, 0), Institucion_DESC
				ORDER BY TeamCount DESC, Institucion_DESC ASC";
		$resSeeds = $Config->query($sqlSeeds);
		if ($resSeeds && $resSeeds->num_rows > 0) {
			while ($seed = $resSeeds->fetch_assoc()) {
				$instId = (int) $seed['Institucion_ID'];
				$sqlTeams = "SELECT e.Equipo_ID
						FROM $schema.Equipos e
						WHERE e.Torneo_ID = $Season
							AND e.Fuerza = $catId
							AND IFNULL(e.Activo, 0) = 1
							AND IFNULL(e.Institucion_ID, 0) = $instId
						ORDER BY e.Equipo_DESC ASC";
				$resTeams = $Config->query($sqlTeams);
				if ($resTeams && $resTeams->num_rows > 0) {
					while ($t = $resTeams->fetch_assoc()) {
						$teamIds[] = (int) $t['Equipo_ID'];
					}
				}
			}
		}

		if (count($teamIds) < 2) {
			$errors[] = str_replace('%1', (string) $catId, $lang['101-10']);
			continue;
		}

		// Existing weeks for this category calendar, ordered.
		$jornadas = array();
		$sqlJ = "SELECT j.Jornada_ID, j.Fecha_Inicio, j.Fecha
				FROM $schema.Jornada j
					INNER JOIN $schema.Categorias c
						ON c.Calendario_ID = j.Calendario_ID
						AND c.Categoria_ID = $catId
						AND c.Torneo_ID = $Season
				WHERE j.Torneo_ID = $Season
				ORDER BY j.Jornada_Orden ASC, j.Jornada_ID ASC";
		$resJ = $Config->query($sqlJ);
		if ($resJ && $resJ->num_rows > 0) {
			while ($j = $resJ->fetch_assoc()) {
				$jornadas[] = $j;
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
