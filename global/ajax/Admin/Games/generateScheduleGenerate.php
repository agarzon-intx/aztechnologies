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
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'generate_schedule_rr.inc.php';

	if (!function_exists('az_gs_preview_cache_dir')) {
		function az_gs_preview_cache_dir() {
			$dir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'az_gs_preview';
			if (!is_dir($dir)) {
				@mkdir($dir, 0700, true);
			}
			return $dir;
		}
		function az_gs_preview_cache_file($alias) {
			$sid = session_id();
			if ($sid === '') {
				$sid = 'nosession';
			}
			$safeAlias = preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $alias);
			$safeSid = preg_replace('/[^a-zA-Z0-9,-]/', '', $sid);
			return az_gs_preview_cache_dir() . DIRECTORY_SEPARATOR . $safeAlias . '_' . $safeSid . '.json';
		}
		function az_gs_preview_save($alias, array $plan) {
			$_SESSION[$alias . 'gsPreview'] = $plan;
			$path = az_gs_preview_cache_file($alias);
			$json = json_encode($plan);
			if ($json !== false) {
				@file_put_contents($path, $json, LOCK_EX);
			}
			// Flush session before large JSON response so confirm can read it.
			if (session_status() === PHP_SESSION_ACTIVE) {
				@session_write_close();
			}
			return true;
		}
		function az_gs_preview_load($alias) {
			$key = $alias . 'gsPreview';
			if (isset($_SESSION[$key]) && is_array($_SESSION[$key]) && !empty($_SESSION[$key]['categories'])) {
				return $_SESSION[$key];
			}
			$path = az_gs_preview_cache_file($alias);
			if (is_readable($path)) {
				$raw = @file_get_contents($path);
				if (is_string($raw) && $raw !== '') {
					$decoded = json_decode($raw, true);
					if (is_array($decoded) && !empty($decoded['categories'])) {
						$_SESSION[$key] = $decoded;
						return $decoded;
					}
				}
			}
			return null;
		}
		function az_gs_preview_clear($alias) {
			unset($_SESSION[$alias . 'gsPreview']);
			$path = az_gs_preview_cache_file($alias);
			if (is_file($path)) {
				@unlink($path);
			}
		}
	}

	$retunData = array('status' => '0', 'message' => $lang['js0002']);
	$action = isset($_POST['action']) ? (string) $_POST['action'] : 'preview';
	$alias = $Config->getAlias();
	$previewKey = $alias . 'gsPreview';

	$Season = 0;
	if (isset($_COOKIE[$alias . 'season']) && $_COOKIE[$alias . 'season'] !== '') {
		$Season = SanitizeInteger($_COOKIE[$alias . 'season']);
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

	$username = isset($_SESSION[$alias . 'username']) ? $_SESSION[$alias . 'username'] : '';

	// ---------- CONFIRM: save preview stored in session / cache ----------
	if ($action === 'confirm') {
		$plan = az_gs_preview_load($alias);
		if (!is_array($plan) || empty($plan['categories']) || !is_array($plan['categories'])) {
			$retunData = array('status' => '0', 'message' => (isset($lang['101-26']) ? $lang['101-26'] : 'Preview expired. Generate again.'));
			header('Content-Type: application/json');
			echo json_encode($retunData);
			exit();
		}
		// Trust the season from the preview plan (cookie can drift between generate and confirm).
		$planSeason = isset($plan['season']) ? (int) $plan['season'] : 0;
		if ($planSeason > 0) {
			$Season = $planSeason;
		} elseif ($Season <= 0) {
			$retunData = array('status' => '0', 'message' => (isset($lang['101-26']) ? $lang['101-26'] : 'Preview expired. Generate again.'));
			header('Content-Type: application/json');
			echo json_encode($retunData);
			exit();
		}

		$confirmWeeks = 0;
		if (isset($_POST['confirmWeeks'])) {
			$confirmWeeks = SanitizeInteger($_POST['confirmWeeks']);
		}
		$maxWeeksPlanned = isset($plan['weeksPlanned']) ? (int) $plan['weeksPlanned'] : 0;
		if ($maxWeeksPlanned < 1) {
			foreach ($plan['categories'] as $catPlan) {
				$cnt = isset($catPlan['weeks']) && is_array($catPlan['weeks']) ? count($catPlan['weeks']) : 0;
				if ($cnt > $maxWeeksPlanned) {
					$maxWeeksPlanned = $cnt;
				}
			}
		}
		if ($confirmWeeks < 1 || $confirmWeeks > $maxWeeksPlanned) {
			$confirmWeeks = $maxWeeksPlanned;
		}

		$created = 0;
		$skipped = 0;
		$weeksCreated = 0;
		$errors = array();
		$Connection = $Config->connectAdmin();
		if (!$Connection) {
			$retunData = array('status' => '0', 'message' => $lang['js0002']);
			header('Content-Type: application/json');
			echo json_encode($retunData);
			exit();
		}

		// Create simulated weeks once per calendar (shared across categories).
		$createdWeekIdsByKey = array();
		foreach ($plan['categories'] as $catPlan) {
			$weekIdx = 0;
			foreach ($catPlan['weeks'] as $week) {
				if ($weekIdx >= $confirmWeeks) {
					break;
				}
				$weekIdx++;
				if (empty($week['createWeek'])) {
					continue;
				}
				$calId = (int) ($week['calendarioId'] ?? 0);
				$orden = (int) ($week['orden'] ?? 0);
				$key = $calId . ':' . $orden;
				if (isset($createdWeekIdsByKey[$key])) {
					continue;
				}
				if ($calId <= 0 || $orden <= 0) {
					$errors[] = isset($lang['101-33']) ? $lang['101-33'] : 'Cannot create week without calendar.';
					continue;
				}
				$fecha = (string) ($week['fecha'] ?? '');
				$inicio = (string) ($week['fechaInicio'] ?? $fecha);
				$fin = (string) ($week['fechaFin'] ?? $fecha);
				$desc = (string) ($week['jornadaDesc'] ?? ('Jornada ' . $orden));
				$descEsc = $Connection->real_escape_string($desc);
				$fechaEsc = $Connection->real_escape_string($fecha);
				$inicioEsc = $Connection->real_escape_string($inicio);
				$finEsc = $Connection->real_escape_string($fin);
				$userEsc = $Connection->real_escape_string($username);
				// WeekCreate(user, season, weekNum→DescCorta, desc→Desc, orden, fecha, inicio, fin, cal, type, @out)
				$sqlW = "CALL $schema.WeekCreate('$userEsc', $Season, $orden, '$descEsc', $orden, '$fechaEsc', '$inicioEsc', '$finEsc', $calId, 1, @out);";
				$okW = $Connection->query($sqlW);
				$newId = 0;
				if ($okW) {
					while ($Connection->more_results() && $Connection->next_result()) {
						$extraRes = $Connection->use_result();
						if ($extraRes instanceof mysqli_result) {
							$extraRes->free();
						}
					}
					$Connection->query("SELECT @out AS 'count'");
					$resId = $Connection->query("SELECT LAST_INSERT_ID() AS id");
					if ($resId && $resId->num_rows > 0) {
						$rowId = $resId->fetch_assoc();
						$newId = (int) $rowId['id'];
					}
					if ($newId <= 0) {
						$resFind = $Connection->query("SELECT Jornada_ID FROM $schema.Jornada
								WHERE Torneo_ID = $Season AND Calendario_ID = $calId AND Jornada_Orden = $orden
								ORDER BY Jornada_ID DESC LIMIT 1");
						if ($resFind && $resFind->num_rows > 0) {
							$rowF = $resFind->fetch_assoc();
							$newId = (int) $rowF['Jornada_ID'];
						}
					}
				}
				if ($newId > 0) {
					$createdWeekIdsByKey[$key] = $newId;
					$weeksCreated++;
				} else {
					$errors[] = (isset($lang['101-34']) ? $lang['101-34'] : 'Failed to create week') . ' #' . $orden;
				}
			}
		}

		// Then create games (including bye rows with NULL on the open side).
		foreach ($plan['categories'] as $catPlan) {
			$weekIdx = 0;
			foreach ($catPlan['weeks'] as $week) {
				if ($weekIdx >= $confirmWeeks) {
					break;
				}
				$weekIdx++;
				if (!empty($week['skip'])) {
					$skipN = 0;
					if (isset($week['games']) && is_array($week['games'])) {
						foreach ($week['games'] as $g) {
							$h = (int) ($g['homeId'] ?? 0);
							$a = (int) ($g['awayId'] ?? 0);
							if ($h > 0 || $a > 0) {
								$skipN++;
							}
						}
					}
					$skipped += $skipN;
					continue;
				}
				$jornadaId = (int) $week['jornadaId'];
				if (!empty($week['createWeek'])) {
					$calId = (int) ($week['calendarioId'] ?? 0);
					$orden = (int) ($week['orden'] ?? 0);
					$key = $calId . ':' . $orden;
					if (isset($createdWeekIdsByKey[$key])) {
						$jornadaId = (int) $createdWeekIdsByKey[$key];
					}
				}
				if ($jornadaId <= 0) {
					$errors[] = isset($lang['101-34']) ? $lang['101-34'] : 'Missing week id';
					continue;
				}
				$userEsc = $Connection->real_escape_string($username);
				foreach ($week['games'] as $game) {
					$home = (int) ($game['homeId'] ?? 0);
					$away = (int) ($game['awayId'] ?? 0);
					if ($home <= 0 && $away <= 0) {
						continue;
					}
					// Bye open slot → SQL NULL (future team). Real match → both IDs.
					$localSql = ($home > 0) ? (string) $home : 'NULL';
					$awaySql = ($away > 0) ? (string) $away : 'NULL';

					$resMax = $Connection->query("SELECT IFNULL(MAX(Juego_ID), 0) + 1 AS nextId FROM $schema.Juegos");
					$nextId = 0;
					if ($resMax && $resMax->num_rows > 0) {
						$rowMax = $resMax->fetch_assoc();
						$nextId = (int) $rowMax['nextId'];
					}
					if ($nextId <= 0) {
						$errors[] = $lang['js0002'];
						continue;
					}

					// Same INSERT shape as GameCreate; Fecha/Horario from Configuration + Jornada.
					$sql = "INSERT INTO $schema.Juegos
						(Juego_ID,
						Visitante_ID,
						Gol_Local,
						Gol_Visitante,
						Arbitro,
						Comentarios,
						Jornada_ID,
						Torneo_ID,
						Local_ID,
						Jugado,
						Penal_Local,
						Penal_Visitante,
						Estatus,
						Extra_Local,
						Extra_Visitante,
						Campo_ID,
						Horario,
						Fecha)
					VALUES (
						$nextId,
						$awaySql,
						0,
						0,
						'',
						'',
						$jornadaId,
						$Season,
						$localSql,
						0,
						0,
						0,
						'',
						0,
						0,
						0,
						(SELECT MarcadorHoraDefault FROM $schema.Configuration),
						(SELECT DATE_ADD(Fecha_Inicio, INTERVAL (SELECT MarcadorDiaDefault FROM $schema.Configuration) DAY)
							FROM $schema.Jornada WHERE Jornada_ID = $jornadaId)
					)";
					$ok = $Connection->query($sql);
					if ($ok) {
						$created++;
						// Best-effort audit (same as GameCreate); ignore failures.
						$localAudit = ($home > 0) ? (string) $home : 'NULL';
						$awayAudit = ($away > 0) ? (string) $away : 'NULL';
						$ctrlDetail = $Connection->real_escape_string(
							"INSERT INTO Juegos (Juego_ID,Visitante_ID,...,Jornada_ID,Torneo_ID,Local_ID,...) VALUES ($nextId,$awayAudit,...,$jornadaId,$Season,$localAudit,...)"
						);
						@$Connection->query("CALL $schema.insertIntoControlTable('$userEsc', 'CREATE', 'GAME', '$ctrlDetail', 'OK, total inserts: 1')");
						while ($Connection->more_results() && $Connection->next_result()) {
							$extraRes = $Connection->use_result();
							if ($extraRes instanceof mysqli_result) {
								$extraRes->free();
							}
						}
					} else {
						$errors[] = $lang['js0002'] . (isset($Connection->error) && $Connection->error !== '' ? (': ' . $Connection->error) : '');
					}
				}
			}
		}
		$Connection->Close();
		az_gs_preview_clear($alias);

		if ($created > 0 && count($errors) === 0) {
			$msg = str_replace(
				array('%1', '%2'),
				array((string) $created, (string) $skipped),
				$lang['101-12']
			);
			if ($weeksCreated > 0) {
				$msg .= ' ' . str_replace('%1', (string) $weeksCreated, isset($lang['101-35']) ? $lang['101-35'] : 'Weeks created: %1.');
			}
			$retunData = array('status' => '1', 'message' => $msg, 'created' => $created, 'skipped' => $skipped, 'weeksCreated' => $weeksCreated);
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
				'weeksCreated' => $weeksCreated,
			);
		} else {
			$retunData = array(
				'status' => '0',
				'message' => count($errors) ? implode("\n", $errors) : $lang['101-13'],
				'created' => 0,
				'skipped' => $skipped,
				'weeksCreated' => $weeksCreated,
			);
		}
		header('Content-Type: application/json');
		echo json_encode($retunData);
		exit();
	}

	// ---------- PREVIEW: build plan, do not save ----------
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

	$startDateByCalendar = array();
	if (isset($_POST['startDates']) && is_array($_POST['startDates'])) {
		foreach ($_POST['startDates'] as $calRaw => $dateRaw) {
			$calId = SanitizeInteger($calRaw);
			$date = preg_replace('/[^0-9\-]/', '', (string) $dateRaw);
			if ($calId > 0 && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
				$startDateByCalendar[$calId] = $date;
			}
		}
	}

	$calByCat = array();
	$weeksByCal = array();
	$catIdsList = implode(',', array_map('intval', array_keys($weeksByCategory)));
	$catNames = array();
	if ($catIdsList !== '') {
		$resCal = $Config->query("SELECT Categoria_ID, Categoria_Desc, Calendario_ID
				FROM $schema.Categorias
				WHERE Torneo_ID = $Season
					AND Categoria_ID IN ($catIdsList)");
		if ($resCal && $resCal->num_rows > 0) {
			while ($row = $resCal->fetch_assoc()) {
				$cid = (int) $row['Categoria_ID'];
				$calId = (int) $row['Calendario_ID'];
				$calByCat[$cid] = $calId;
				$catNames[$cid] = (string) $row['Categoria_Desc'];
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

	$teamOrderByCategory = array();
	$orderRaw = '';
	if (isset($_POST['teamOrderJson']) && is_string($_POST['teamOrderJson']) && $_POST['teamOrderJson'] !== '') {
		$orderRaw = $_POST['teamOrderJson'];
	} elseif (isset($_POST['teamOrder']) && is_string($_POST['teamOrder']) && $_POST['teamOrder'] !== '') {
		$orderRaw = $_POST['teamOrder'];
	}
	if ($orderRaw !== '') {
		$decodedOrder = json_decode($orderRaw, true);
		if (is_array($decodedOrder)) {
			foreach ($decodedOrder as $catIdRaw => $ids) {
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
	} elseif (isset($_POST['teamOrder']) && is_array($_POST['teamOrder'])) {
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
	// Remember last UI order so each category keeps its own seed list across preview/confirm.
	$_SESSION[$alias . 'gsTeamOrder'] = $teamOrderByCategory;

	$teamNameCache = array();
	$resolveTeamName = function ($teamId) use ($Config, $schema, $Season, &$teamNameCache) {
		$teamId = (int) $teamId;
		if (isset($teamNameCache[$teamId])) {
			return $teamNameCache[$teamId];
		}
		$res = $Config->query("SELECT Equipo_DESC FROM $schema.Equipos WHERE Equipo_ID = $teamId AND Torneo_ID = $Season LIMIT 1");
		$name = '#' . $teamId;
		if ($res && $res->num_rows > 0) {
			$row = $res->fetch_assoc();
			$name = (string) $row['Equipo_DESC'];
		}
		$teamNameCache[$teamId] = $name;
		return $name;
	};

	$errors = array();
	$planCategories = array();
	$totalGames = 0;
	$totalSkipWeeks = 0;

	foreach ($weeksByCategory as $catId => $weeksRequested) {
		if ($weeksRequested < 1) {
			$errors[] = str_replace('%1', (string) $catId, $lang['101-9']);
			continue;
		}

		$teamIds = array();
		$usedCustomOrder = false;
		if (isset($teamOrderByCategory[$catId]) && count($teamOrderByCategory[$catId]) > 0) {
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
			// Keep UI seed order first; append any active team missing from the posted list.
			foreach (array_keys($allowed) as $tid) {
				$teamIds[] = (int) $tid;
			}
			if (count($teamIds) >= 2) {
				$usedCustomOrder = true;
			} else {
				$teamIds = array();
			}
		}
		if (count($teamIds) < 2) {
			$teamIds = az_generate_schedule_seeded_team_ids_for_category($Config, $schema, $Season, $catId, $institutionSeeds);
		}

		if (count($teamIds) < 2) {
			$errors[] = str_replace('%1', (string) (isset($catNames[$catId]) ? $catNames[$catId] : $catId), $lang['101-10']);
			continue;
		}

		// Seed map for this category only (#1 = first in UI order).
		$seedRankByTeam = array();
		foreach ($teamIds as $idx => $tid) {
			$seedRankByTeam[(int) $tid] = $idx + 1;
		}
		$formatSeedName = function ($teamId) use ($resolveTeamName, $seedRankByTeam) {
			$teamId = (int) $teamId;
			$name = $resolveTeamName($teamId);
			$rank = isset($seedRankByTeam[$teamId]) ? (int) $seedRankByTeam[$teamId] : 0;
			if ($rank > 0) {
				return '#' . $rank . ' ' . $name;
			}
			return $name;
		};

		$jornadas = array();
		$willCreateWeeks = false;
		$calId = isset($calByCat[$catId]) ? (int) $calByCat[$catId] : 0;
		if ($calId <= 0) {
			$resCalOne = $Config->query("SELECT Calendario_ID FROM $schema.Categorias WHERE Torneo_ID = $Season AND Categoria_ID = $catId LIMIT 1");
			if ($resCalOne && $resCalOne->num_rows > 0) {
				$rowCal = $resCalOne->fetch_assoc();
				$calId = (int) $rowCal['Calendario_ID'];
			}
		}
		if ($calId > 0) {
			$sqlJ = "SELECT j.Jornada_ID, j.Jornada_Desc, j.Fecha_Inicio, j.Fecha, j.Fecha_Fin, j.Jornada_Orden
					FROM $schema.Jornada j
					WHERE j.Torneo_ID = $Season
						AND j.Calendario_ID = $calId
					ORDER BY j.Jornada_Orden ASC, j.Jornada_ID ASC";
			$resJ = $Config->query($sqlJ);
			if ($resJ && $resJ->num_rows > 0) {
				while ($j = $resJ->fetch_assoc()) {
					$j['createWeek'] = false;
					$jornadas[] = $j;
				}
			}
		}

		$catLabel = isset($catNames[$catId]) ? $catNames[$catId] : (string) $catId;
		if (count($jornadas) === 0) {
			$startDate = isset($startDateByCalendar[$calId]) ? $startDateByCalendar[$calId] : '';
			if ($calId <= 0) {
				$errors[] = $catLabel . ': ' . (isset($lang['101-33']) ? $lang['101-33'] : 'Cannot create weeks without a calendar.');
				continue;
			}
			if ($startDate === '') {
				$errors[] = $catLabel . ': ' . (isset($lang['101-32']) ? $lang['101-32'] : 'Enter a start week date.');
				continue;
			}
			$jornadas = az_gs_simulate_jornadas($startDate, $weeksRequested, $calId);
			$willCreateWeeks = true;
		} elseif (count($jornadas) < $weeksRequested) {
			$beforeExtend = count($jornadas);
			$jornadas = az_gs_extend_jornadas($jornadas, $weeksRequested, $calId);
			if (count($jornadas) < $weeksRequested) {
				$errors[] = str_replace(
					array('%1', '%2', '%3'),
					array($catLabel, (string) $weeksRequested, (string) $beforeExtend),
					isset($lang['101-11']) ? $lang['101-11'] : 'Not enough weeks to extend.'
				);
				continue;
			}
			$willCreateWeeks = true;
		}

		$rr = az_rr_balanced_rounds($teamIds, 2);
		if (count($rr) === 0) {
			$errors[] = str_replace('%1', $catLabel, $lang['101-10']);
			continue;
		}
		$scheduleRounds = az_rr_expand_weeks($rr, $weeksRequested, 2);

		$teamIdList = implode(',', array_map('intval', $teamIds));
		$weekPlans = array();
		for ($wi = 0; $wi < $weeksRequested; $wi++) {
			$jornadaId = (int) $jornadas[$wi]['Jornada_ID'];
			$createWeek = !empty($jornadas[$wi]['createWeek']);
			$fecha = isset($jornadas[$wi]['Fecha']) ? $jornadas[$wi]['Fecha'] : null;
			if ($fecha === null || $fecha === '') {
				$fecha = $jornadas[$wi]['Fecha_Inicio'];
			}
			if ($fecha === null || $fecha === '') {
				$fecha = date('Y-m-d');
			}
			$fechaInicio = isset($jornadas[$wi]['Fecha_Inicio']) ? (string) $jornadas[$wi]['Fecha_Inicio'] : (string) $fecha;
			$fechaFin = isset($jornadas[$wi]['Fecha_Fin']) ? (string) $jornadas[$wi]['Fecha_Fin'] : (string) $fecha;
			$orden = isset($jornadas[$wi]['Jornada_Orden']) ? (int) $jornadas[$wi]['Jornada_Orden'] : ($wi + 1);
			$jornadaDesc = '';
			if (isset($jornadas[$wi]['Jornada_Desc']) && $jornadas[$wi]['Jornada_Desc'] !== '') {
				$jornadaDesc = (string) $jornadas[$wi]['Jornada_Desc'];
			} elseif (isset($jornadas[$wi]['Jornada_DESC']) && $jornadas[$wi]['Jornada_DESC'] !== '') {
				$jornadaDesc = (string) $jornadas[$wi]['Jornada_DESC'];
			} else {
				$jornadaDesc = 'Jornada ' . ($wi + 1);
			}
			$skip = false;
			if (!$createWeek && $jornadaId > 0) {
				$sqlExist = "SELECT COUNT(*) AS cnt
						FROM $schema.Juegos j
						WHERE j.Torneo_ID = $Season
							AND j.Jornada_ID = $jornadaId
							AND (j.Local_ID IN ($teamIdList) OR j.Visitante_ID IN ($teamIdList))";
				$resExist = $Config->query($sqlExist);
				if ($resExist && $resExist->num_rows > 0) {
					$rowE = $resExist->fetch_assoc();
					if ((int) $rowE['cnt'] > 0) {
						$skip = true;
						$totalSkipWeeks++;
					}
				}
			}

			$round = isset($scheduleRounds[$wi]) ? $scheduleRounds[$wi] : array('games' => array(), 'byeGame' => null);
			$roundGames = az_rr_round_games($round);
			$byeGame = az_rr_round_bye_game($round);
			$games = array();
			foreach ($roundGames as $pair) {
				$home = (int) $pair[0];
				$away = (int) $pair[1];
				$games[] = array(
					'homeId' => $home,
					'awayId' => $away,
					'homeName' => $formatSeedName($home),
					'awayName' => $formatSeedName($away),
					'isBye' => false,
				);
				if (!$skip) {
					$totalGames++;
				}
			}
			if ($byeGame !== null) {
				$home = (int) $byeGame[0];
				$away = (int) $byeGame[1];
				$games[] = array(
					'homeId' => $home,
					'awayId' => $away,
					'homeName' => ($home > 0) ? $formatSeedName($home) : '',
					'awayName' => ($away > 0) ? $formatSeedName($away) : '',
					'isBye' => true,
				);
				if (!$skip && ($home > 0 || $away > 0)) {
					$totalGames++;
				}
			}

			$weekPlans[] = array(
				'jornadaId' => $jornadaId,
				'jornadaDesc' => $jornadaDesc,
				'fecha' => (string) $fecha,
				'fechaInicio' => $fechaInicio,
				'fechaFin' => $fechaFin,
				'orden' => $orden,
				'calendarioId' => $calId,
				'createWeek' => $createWeek,
				'skip' => $skip,
				'games' => $games,
			);
		}

		$seedOrderLabels = array();
		foreach ($teamIds as $tid) {
			$seedOrderLabels[] = $formatSeedName((int) $tid);
		}

		$planCategories[] = array(
			'Categoria_ID' => $catId,
			'Categoria_Desc' => $catLabel,
			'teamIds' => $teamIds,
			'seedOrder' => $seedOrderLabels,
			'usedCustomOrder' => $usedCustomOrder,
			'willCreateWeeks' => $willCreateWeeks,
			'weeks' => $weekPlans,
		);
	}

	if (count($planCategories) === 0) {
		$retunData = array(
			'status' => '0',
			'message' => count($errors) ? implode("\n", $errors) : $lang['101-13'],
		);
		header('Content-Type: application/json');
		echo json_encode($retunData);
		exit();
	}

	$maxWeeksPlanned = 0;
	foreach ($planCategories as $catPlan) {
		$cnt = isset($catPlan['weeks']) ? count($catPlan['weeks']) : 0;
		if ($cnt > $maxWeeksPlanned) {
			$maxWeeksPlanned = $cnt;
		}
	}

	$planPayload = array(
		'season' => $Season,
		'categories' => $planCategories,
		'weeksPlanned' => $maxWeeksPlanned,
		'createdAt' => time(),
	);
	az_gs_preview_save($alias, $planPayload);

	// Build preview HTML
	$previewTitle = isset($lang['101-25']) ? $lang['101-25'] : 'Schedule preview';
	$homeLbl = isset($lang['363']) ? $lang['363'] : 'Home';
	$awayLbl = isset($lang['364']) ? $lang['364'] : 'Away';
	$skipLbl = isset($lang['101-27']) ? $lang['101-27'] : 'Skipped (games already exist)';
	$newWeekLbl = isset($lang['101-36']) ? $lang['101-36'] : 'New week (will be created)';
	$byeLbl = isset($lang['101-39']) ? $lang['101-39'] : 'BYE';
	$confirmLbl = isset($lang['101-28']) ? $lang['101-28'] : 'Confirm & save';
	$weeksSaveLbl = isset($lang['101-41']) ? $lang['101-41'] : 'Weeks to save';
	$backLbl = isset($lang['0001']) ? $lang['0001'] : 'Cancel';
	$noteLbl = isset($lang['101-29']) ? $lang['101-29'] : 'Balanced round-robin (max 2 consecutive home or away).';
	$createNote = isset($lang['101-37']) ? $lang['101-37'] : 'Confirm will create any missing weeks, then the matches.';

	$html = '<div id="generateSchedulePreview" class="tabla active" style="display: block;padding-top: 10px;">
		<div class="datagridAdmin" style="display: block;width: 100%;height: auto;">
			<div style="float: left;width: 100%;padding-top: 8px;padding-bottom: 8px;">
				<legend style="font-size: 25px; font-weight: bold; border-bottom: 0px">' . htmlspecialchars($previewTitle, ENT_QUOTES, 'UTF-8') . '</legend>
				<div class="text-muted">' . htmlspecialchars($noteLbl, ENT_QUOTES, 'UTF-8') . '</div>
				<div class="text-muted">' . htmlspecialchars($createNote, ENT_QUOTES, 'UTF-8') . '</div>
			</div>';

	if (count($errors) > 0) {
		$html .= '<div class="alert alert-warning">' . htmlspecialchars(implode("\n", $errors), ENT_QUOTES, 'UTF-8') . '</div>';
	}

	$html .= '<div class="nav-wrapper position-relative end-0">
			<ul class="nav nav-pills nav-fill p-1" role="tablist" style="background: #cee6ff; flex-direction: unset !important; flex-wrap: wrap;" id="generateSchedulePreviewNavTabs">';

	foreach ($planCategories as $idx => $catPlan) {
		$panelId = 'gsPrevCat' . (int) $catPlan['Categoria_ID'];
		$active = ($idx === 0) ? ' active' : '';
		$selected = ($idx === 0) ? 'true' : 'false';
		$html .= '<li class="nav-item" id="' . $panelId . 'li">
				<a class="nav-link mb-0 px-2 py-1' . $active . '" data-bs-toggle="tab" style="cursor: pointer;" callval="#' . $panelId . '" role="tab" aria-controls="' . $panelId . 'li" aria-selected="' . $selected . '">'
					. htmlspecialchars((string) $catPlan['Categoria_Desc'], ENT_QUOTES, 'UTF-8') .
				'</a>
			</li>';
	}

	$html .= '</ul>
		</div>
		<script>initNavs("generateSchedulePreviewNavTabs");</script>
		<div class="tabla-content" style="padding-top: 12px;">';

	foreach ($planCategories as $idx => $catPlan) {
		$panelId = 'gsPrevCat' . (int) $catPlan['Categoria_ID'];
		$display = ($idx === 0) ? 'block' : 'none';
		$activeClass = ($idx === 0) ? ' active' : '';
		$html .= '<div id="' . $panelId . '" class="tabla' . $activeClass . '" style="display: ' . $display . '; height: auto;">';

		if (!empty($catPlan['seedOrder']) && is_array($catPlan['seedOrder'])) {
			$seedLbl = isset($lang['101-40']) ? $lang['101-40'] : 'Seed order used for this category';
			$html .= '<div class="mb-2 text-muted"><strong>' . htmlspecialchars($seedLbl, ENT_QUOTES, 'UTF-8') . ':</strong> '
				. htmlspecialchars(implode(' · ', $catPlan['seedOrder']), ENT_QUOTES, 'UTF-8') . '</div>';
		}

		foreach ($catPlan['weeks'] as $wIdx => $week) {
			$weekTitle = (string) $week['jornadaDesc'];
			if (!empty($week['fecha'])) {
				$weekTitle .= ' — ' . (string) $week['fecha'];
			}
			$html .= '<div class="mb-3">
				<div class="mb-1"><strong>' . htmlspecialchars($weekTitle, ENT_QUOTES, 'UTF-8') . '</strong>';
			if (!empty($week['skip'])) {
				$html .= ' <span class="badge bg-warning text-dark">' . htmlspecialchars($skipLbl, ENT_QUOTES, 'UTF-8') . '</span>';
			} elseif (!empty($week['createWeek'])) {
				$html .= ' <span class="badge bg-info text-dark">' . htmlspecialchars($newWeekLbl, ENT_QUOTES, 'UTF-8') . '</span>';
			}
			$html .= '</div>
				<div class="table-responsive">
					<table class="table table-sm table-striped align-middle mb-0">
						<thead>
							<tr>
								<th>' . htmlspecialchars($homeLbl, ENT_QUOTES, 'UTF-8') . '</th>
								<th style="width: 40px;" class="text-center">vs</th>
								<th>' . htmlspecialchars($awayLbl, ENT_QUOTES, 'UTF-8') . '</th>
							</tr>
						</thead>
						<tbody>';
			foreach ($week['games'] as $game) {
				$isBye = !empty($game['isBye']) || (int) $game['homeId'] <= 0 || (int) $game['awayId'] <= 0;
				$homeCell = ((int) $game['homeId'] > 0)
					? htmlspecialchars((string) $game['homeName'], ENT_QUOTES, 'UTF-8')
					: '<em>' . htmlspecialchars($byeLbl, ENT_QUOTES, 'UTF-8') . '</em>';
				$awayCell = ((int) $game['awayId'] > 0)
					? htmlspecialchars((string) $game['awayName'], ENT_QUOTES, 'UTF-8')
					: '<em>' . htmlspecialchars($byeLbl, ENT_QUOTES, 'UTF-8') . '</em>';
				$rowClass = $isBye ? ' class="table-secondary"' : '';
				$html .= '<tr' . $rowClass . '>
						<td>' . $homeCell . '</td>
						<td class="text-center">vs</td>
						<td>' . $awayCell . '</td>
					</tr>';
			}
			$html .= '</tbody>
					</table>
				</div>
			</div>';
		}

		$html .= '</div>';
	}

	$html .= '</div>
			<div class="mt-4 mb-2" style="clear: both;">
				<label class="me-2" for="gsConfirmWeeks">' . htmlspecialchars($weeksSaveLbl, ENT_QUOTES, 'UTF-8') . '</label>
				<select id="gsConfirmWeeks" class="form-select form-select-sm d-inline-block me-3" style="width: auto; min-width: 4.5rem; vertical-align: middle;">';
	if ($maxWeeksPlanned < 1) {
		$maxWeeksPlanned = 1;
	}
	for ($wOpt = 1; $wOpt <= $maxWeeksPlanned; $wOpt++) {
		$sel = ($wOpt === $maxWeeksPlanned) ? ' selected' : '';
		$html .= '<option value="' . $wOpt . '"' . $sel . '>' . $wOpt . '</option>';
	}
	$html .= '</select>
				<button type="button" class="btn btn-primary me-2" onClick="generateScheduleConfirm();">' . htmlspecialchars($confirmLbl, ENT_QUOTES, 'UTF-8') . '</button>
				<button type="button" class="btn btn-outline-secondary" onClick="generateScheduleShow();">' . htmlspecialchars($backLbl, ENT_QUOTES, 'UTF-8') . '</button>
			</div>
		</div>
	</div>';

	$retunData = array(
		'status' => '1',
		'message' => '',
		'dataGenerateSchedulePreview' => $html,
		'gameCount' => $totalGames,
		'skipWeeks' => $totalSkipWeeks,
	);
	header('Content-Type: application/json');
	echo json_encode($retunData);
	exit();
?>
