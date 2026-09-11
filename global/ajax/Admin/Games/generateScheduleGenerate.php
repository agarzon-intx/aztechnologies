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

	$retunData = array('status' => '0', 'message' => $lang['js0002']);
	$action = isset($_POST['action']) ? (string) $_POST['action'] : 'preview';
	$previewKey = $Config->getAlias() . 'gsPreview';

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

	// ---------- CONFIRM: save preview stored in session ----------
	if ($action === 'confirm') {
		$plan = isset($_SESSION[$previewKey]) ? $_SESSION[$previewKey] : null;
		if (!is_array($plan) || (int) ($plan['season'] ?? 0) !== $Season || empty($plan['categories']) || !is_array($plan['categories'])) {
			$retunData = array('status' => '0', 'message' => (isset($lang['101-26']) ? $lang['101-26'] : 'Preview expired. Generate again.'));
			header('Content-Type: application/json');
			echo json_encode($retunData);
			exit();
		}

		$created = 0;
		$skipped = 0;
		$errors = array();
		$Connection = $Config->connectAdmin();
		if (!$Connection) {
			$retunData = array('status' => '0', 'message' => $lang['js0002']);
			header('Content-Type: application/json');
			echo json_encode($retunData);
			exit();
		}

		foreach ($plan['categories'] as $catPlan) {
			foreach ($catPlan['weeks'] as $week) {
				if (!empty($week['skip'])) {
					$skipped += isset($week['games']) ? count($week['games']) : 0;
					continue;
				}
				$jornadaId = (int) $week['jornadaId'];
				$fecha = (string) $week['fecha'];
				$fechaEsc = $Connection->real_escape_string($fecha);
				$userEsc = $Connection->real_escape_string($username);
				foreach ($week['games'] as $game) {
					$home = (int) $game['homeId'];
					$away = (int) $game['awayId'];
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
		}
		$Connection->Close();
		unset($_SESSION[$previewKey]);

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
			foreach (array_keys($allowed) as $tid) {
				$teamIds[] = (int) $tid;
			}
		}
		if (count($teamIds) < 2) {
			$teamIds = az_generate_schedule_seeded_team_ids_for_category($Config, $schema, $Season, $catId, $institutionSeeds);
		}

		if (count($teamIds) < 2) {
			$errors[] = str_replace('%1', (string) (isset($catNames[$catId]) ? $catNames[$catId] : $catId), $lang['101-10']);
			continue;
		}

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
			$sqlJ = "SELECT j.Jornada_ID, j.Jornada_DESC, j.Fecha_Inicio, j.Fecha, j.Jornada_Orden
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

		$catLabel = isset($catNames[$catId]) ? $catNames[$catId] : (string) $catId;
		if (count($jornadas) < $weeksRequested) {
			$errors[] = str_replace(
				array('%1', '%2', '%3'),
				array($catLabel, (string) $weeksRequested, (string) count($jornadas)),
				$lang['101-11']
			);
			continue;
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
			$fecha = $jornadas[$wi]['Fecha_Inicio'];
			if ($fecha === null || $fecha === '') {
				$fecha = $jornadas[$wi]['Fecha'];
			}
			if ($fecha === null || $fecha === '') {
				$fecha = date('Y-m-d');
			}
			$jornadaDesc = isset($jornadas[$wi]['Jornada_DESC']) ? (string) $jornadas[$wi]['Jornada_DESC'] : ('#' . ($wi + 1));

			$skip = false;
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

			$games = array();
			foreach ($scheduleRounds[$wi] as $pair) {
				$home = (int) $pair[0];
				$away = (int) $pair[1];
				$games[] = array(
					'homeId' => $home,
					'awayId' => $away,
					'homeName' => $resolveTeamName($home),
					'awayName' => $resolveTeamName($away),
				);
				if (!$skip) {
					$totalGames++;
				}
			}

			$weekPlans[] = array(
				'jornadaId' => $jornadaId,
				'jornadaDesc' => $jornadaDesc,
				'fecha' => (string) $fecha,
				'skip' => $skip,
				'games' => $games,
			);
		}

		$planCategories[] = array(
			'Categoria_ID' => $catId,
			'Categoria_Desc' => $catLabel,
			'teamIds' => $teamIds,
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

	$_SESSION[$previewKey] = array(
		'season' => $Season,
		'categories' => $planCategories,
		'createdAt' => time(),
	);

	// Build preview HTML
	$previewTitle = isset($lang['101-25']) ? $lang['101-25'] : 'Schedule preview';
	$homeLbl = isset($lang['363']) ? $lang['363'] : 'Home';
	$awayLbl = isset($lang['364']) ? $lang['364'] : 'Away';
	$skipLbl = isset($lang['101-27']) ? $lang['101-27'] : 'Skipped (games already exist)';
	$confirmLbl = isset($lang['101-28']) ? $lang['101-28'] : 'Confirm & save';
	$backLbl = isset($lang['0001']) ? $lang['0001'] : 'Cancel';
	$noteLbl = isset($lang['101-29']) ? $lang['101-29'] : 'Balanced round-robin (max 2 consecutive home or away).';

	$html = '<div id="generateSchedulePreview" class="tabla active" style="display: block;padding-top: 10px;">
		<div class="datagridAdmin" style="display: block;width: 100%;height: auto;">
			<div style="float: left;width: 100%;padding-top: 8px;padding-bottom: 8px;">
				<legend style="font-size: 25px; font-weight: bold; border-bottom: 0px">' . htmlspecialchars($previewTitle, ENT_QUOTES, 'UTF-8') . '</legend>
				<div class="text-muted">' . htmlspecialchars($noteLbl, ENT_QUOTES, 'UTF-8') . '</div>
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

		foreach ($catPlan['weeks'] as $wIdx => $week) {
			$weekTitle = (string) $week['jornadaDesc'];
			if (!empty($week['fecha'])) {
				$weekTitle .= ' — ' . (string) $week['fecha'];
			}
			$html .= '<div class="mb-3">
				<div class="mb-1"><strong>' . htmlspecialchars($weekTitle, ENT_QUOTES, 'UTF-8') . '</strong>';
			if (!empty($week['skip'])) {
				$html .= ' <span class="badge bg-warning text-dark">' . htmlspecialchars($skipLbl, ENT_QUOTES, 'UTF-8') . '</span>';
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
				$html .= '<tr>
						<td>' . htmlspecialchars((string) $game['homeName'], ENT_QUOTES, 'UTF-8') . '</td>
						<td class="text-center">vs</td>
						<td>' . htmlspecialchars((string) $game['awayName'], ENT_QUOTES, 'UTF-8') . '</td>
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
