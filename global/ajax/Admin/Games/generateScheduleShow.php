<?php
	session_start();
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

	// Keep fatals from becoming blank 500s — return JSON instead.
	error_reporting(E_ALL);
	ini_set('display_errors', '0');
	ini_set('log_errors', '1');

	function az_gs_json_exit($payload) {
		header('Content-Type: application/json; charset=utf-8');
		$flags = JSON_UNESCAPED_UNICODE;
		if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
			$flags |= JSON_INVALID_UTF8_SUBSTITUTE;
		}
		$json = json_encode($payload, $flags);
		echo ($json !== false) ? $json : '{"status":"0","message":"JSON encode failed"}';
		exit();
	}

	set_exception_handler(function ($e) {
		@file_put_contents(__DIR__ . '/generate_schedule_error.log', date('c') . ' EX ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine() . "\n", FILE_APPEND);
		az_gs_json_exit(array(
			'status' => '0',
			'message' => 'Generate Schedule error: ' . $e->getMessage(),
			'dataGenerateSchedule' => '',
		));
	});
	register_shutdown_function(function () {
		$err = error_get_last();
		if (!$err) {
			return;
		}
		$fatalTypes = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR);
		if (!in_array($err['type'], $fatalTypes, true)) {
			return;
		}
		@file_put_contents(__DIR__ . '/generate_schedule_error.log', date('c') . ' FATAL ' . $err['message'] . ' @ ' . $err['file'] . ':' . $err['line'] . "\n", FILE_APPEND);
		if (!headers_sent()) {
			header('Content-Type: application/json; charset=utf-8');
		}
		echo json_encode(array(
			'status' => '0',
			'message' => 'Generate Schedule fatal: ' . $err['message'],
			'dataGenerateSchedule' => '',
		));
	});

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
	$sessionstat = $fgmembersite->CheckLogin('generateScheduleShow.php');

	$langFile = 'lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php';
	include($langFile);
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'generate_schedule_seeds.inc.php';

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

	$tournamentName = '';
	if ($Season > 0) {
		$resTorneo = $Config->query("SELECT Torneo_Desc FROM $schema.Torneos WHERE Torneo_ID = $Season LIMIT 1");
		if ($resTorneo && $resTorneo->num_rows > 0) {
			$rowTorneo = $resTorneo->fetch_assoc();
			$tournamentName = (string) $rowTorneo['Torneo_Desc'];
		} else {
			$Season = 0;
		}
	}

	if ($Season <= 0) {
		$html = '<div id="generateSchedule" class="tabla active" style="display: block;padding-top: 10px;">
			<div class="alert alert-warning">' . htmlspecialchars((string) (isset($lang['101-14']) ? $lang['101-14'] : 'Select a tournament first.'), ENT_QUOTES, 'UTF-8') . '</div>
		</div>';
		az_gs_json_exit(array(
			'status' => '1',
			'message' => '',
			'dataGenerateSchedule' => $html,
		));
	}

	$institutionSeeds = az_generate_schedule_institution_seeds($Config, $schema, $Season);

	$categories = array();
	$sqlCat = "SELECT DISTINCT c.Categoria_ID, c.Categoria_Desc, c.Categoria_Orden, c.Calendario_ID
			FROM $schema.Categorias c
				INNER JOIN $schema.Equipos e
					ON e.Fuerza = c.Categoria_ID
					AND e.Torneo_ID = c.Torneo_ID
					AND IFNULL(e.Activo, 0) = 1
			WHERE c.Torneo_ID = $Season
			ORDER BY c.Categoria_Orden ASC, c.Categoria_Desc ASC";
	$resCat = $Config->query($sqlCat);
	$weekCountByCalendar = array();
	if ($resCat && $resCat->num_rows > 0) {
		while ($row = $resCat->fetch_assoc()) {
			$catId = (int) $row['Categoria_ID'];
			$calId = (int) $row['Calendario_ID'];
			$weekCount = 0;
			if ($calId > 0) {
				if (!isset($weekCountByCalendar[$calId])) {
					$sqlWeeks = "SELECT COUNT(*) AS cnt
							FROM $schema.Jornada j
							WHERE j.Torneo_ID = $Season
								AND j.Calendario_ID = $calId";
					$resWeeks = $Config->query($sqlWeeks);
					$cnt = 0;
					if ($resWeeks && $resWeeks->num_rows > 0) {
						$w = $resWeeks->fetch_assoc();
						$cnt = (int) $w['cnt'];
					}
					$weekCountByCalendar[$calId] = $cnt;
				}
				$weekCount = (int) $weekCountByCalendar[$calId];
			}

			$seeds = array();
			foreach ($institutionSeeds as $seed) {
				$teams = az_generate_schedule_teams_for_seed_category($Config, $schema, $Season, $catId, $seed);
				if (count($teams) === 0) {
					continue;
				}
				$seeds[] = array(
					'seed' => (int) $seed['rank'],
					'Institucion_ID' => (int) $seed['Institucion_ID'],
					'Institucion_DESC' => (string) $seed['Institucion_DESC'],
					'TeamCount' => count($teams),
					'teams' => $teams,
				);
			}

			$categories[] = array(
				'Categoria_ID' => $catId,
				'Categoria_Desc' => (string) $row['Categoria_Desc'],
				'Calendario_ID' => $calId,
				'weekCount' => $weekCount,
				'seeds' => $seeds,
			);
		}
	}

	$html = '<div id="generateSchedule" class="tabla active" style="display: block;padding-top: 10px;" data-tournament-id="' . (int) $Season . '">
		<div class="datagridAdmin" style="display: block;width: 100%;height: auto;">
			<div style="float: left;width: 100%;padding-top: 8px;padding-bottom: 8px;">
				<legend style="font-size: 25px; font-weight: bold; border-bottom: 0px">' . htmlspecialchars((string) (isset($lang['101-1']) ? $lang['101-1'] : 'Generate Schedule'), ENT_QUOTES, 'UTF-8') . '</legend>
				<div class="text-muted">' . htmlspecialchars((string) (isset($lang['105']) ? $lang['105'] : 'Season'), ENT_QUOTES, 'UTF-8') . ': <strong>' . htmlspecialchars($tournamentName, ENT_QUOTES, 'UTF-8') . '</strong></div>
			</div>';

	if (count($categories) === 0) {
		$html .= '<div class="alert alert-warning">' . htmlspecialchars((string) (isset($lang['101-6']) ? $lang['101-6'] : 'No categories'), ENT_QUOTES, 'UTF-8') . '</div>';
	} else {
		$rankingLabel = (string) (isset($lang['101-15']) ? $lang['101-15'] : 'Ranking');
		$html .= '<div class="nav-wrapper position-relative end-0">
				<ul class="nav nav-pills nav-fill p-1" role="tablist" style="background: #cee6ff; flex-direction: unset !important; flex-wrap: wrap;" id="generateScheduleNavTabs">
				<li class="nav-item" id="gsRankingAllli">
					<a class="nav-link mb-0 px-2 py-1 active" data-bs-toggle="tab" style="cursor: pointer;" callval="#gsRankingAll" role="tab" aria-controls="gsRankingAllli" aria-selected="true">'
						. htmlspecialchars($rankingLabel, ENT_QUOTES, 'UTF-8') .
					'</a>
				</li>';

		foreach ($categories as $cat) {
			$panelId = 'gsCat' . (int) $cat['Categoria_ID'];
			$html .= '<li class="nav-item" id="' . $panelId . 'li">
					<a class="nav-link mb-0 px-2 py-1" data-bs-toggle="tab" style="cursor: pointer;" callval="#' . $panelId . '" role="tab" aria-controls="' . $panelId . 'li" aria-selected="false">'
						. htmlspecialchars((string) $cat['Categoria_Desc'], ENT_QUOTES, 'UTF-8') .
					'</a>
				</li>';
		}

		$html .= '</ul>
			</div>
			<script>initNavs("generateScheduleNavTabs");</script>
			<div class="tabla-content" style="padding-top: 12px;">';

		// First tab: tournament-wide institution ranking (position + name).
		$html .= '<div id="gsRankingAll" class="tabla active" style="display: block; height: auto;">
			<div class="table-responsive">
				<table class="table table-sm table-striped align-middle mb-0">
					<thead>
						<tr>
							<th style="width: 70px;">#</th>
							<th>' . htmlspecialchars((string) (isset($lang['113-2']) ? $lang['113-2'] : 'Institution'), ENT_QUOTES, 'UTF-8') . '</th>
						</tr>
					</thead>
					<tbody>';

		if (count($institutionSeeds) === 0) {
			$html .= '<tr><td colspan="2">' . htmlspecialchars((string) (isset($lang['101-7']) ? $lang['101-7'] : 'No teams'), ENT_QUOTES, 'UTF-8') . '</td></tr>';
		} else {
			foreach ($institutionSeeds as $seed) {
				$html .= '<tr>
						<td>' . (int) $seed['rank'] . '</td>
						<td>' . htmlspecialchars((string) $seed['Institucion_DESC'], ENT_QUOTES, 'UTF-8') . '</td>
					</tr>';
			}
		}

		$html .= '</tbody>
				</table>
			</div>
		</div>';

		foreach ($categories as $cat) {
			$panelId = 'gsCat' . (int) $cat['Categoria_ID'];
			$calId = (int) $cat['Calendario_ID'];
			$weeks = (int) $cat['weekCount'];
			$weeksValue = ($weeks > 0) ? (string) $weeks : '';
			$weeksHint = ($weeks > 0)
				? (string) (isset($lang['101-2']) ? $lang['101-2'] : 'Calendar has %1 week(s).')
				: (string) (isset($lang['101-3']) ? $lang['101-3'] : 'Enter weeks.');
			$weeksHint = str_replace('%1', (string) $weeks, $weeksHint);
			if ($calId > 0) {
				$shareHint = (string) (isset($lang['101-17']) ? $lang['101-17'] : 'Shared with other categories on this calendar.');
				$weeksHint .= ' ' . $shareHint;
			}

			$html .= '<div id="' . $panelId . '" class="tabla" style="display: none; height: auto;" data-category-id="' . (int) $cat['Categoria_ID'] . '" data-calendario-id="' . $calId . '">
				<div class="row align-items-end mb-3">
					<div class="col-12 col-md-6 col-lg-4">
						<label class="form-label" for="gsWeeks_' . (int) $cat['Categoria_ID'] . '">' . htmlspecialchars((string) (isset($lang['108']) ? $lang['108'] : 'Weeks'), ENT_QUOTES, 'UTF-8') . '</label>
						<input type="number" min="1" step="1" class="form-control gs-weeks-input" id="gsWeeks_' . (int) $cat['Categoria_ID'] . '" data-category-id="' . (int) $cat['Categoria_ID'] . '" data-calendario-id="' . $calId . '" data-default-weeks="' . $weeks . '" value="' . htmlspecialchars($weeksValue, ENT_QUOTES, 'UTF-8') . '" oninput="generateScheduleSyncWeeks(this);" />
						<small class="text-muted">' . htmlspecialchars($weeksHint, ENT_QUOTES, 'UTF-8') . '</small>
					</div>
				</div>
				<div class="mb-2"><strong>' . htmlspecialchars((string) (isset($lang['101-16']) ? $lang['101-16'] : 'Category ranking'), ENT_QUOTES, 'UTF-8') . '</strong></div>
				<div class="table-responsive">
					<table class="table table-sm table-striped align-middle mb-0">
						<thead>
							<tr>
								<th style="width: 70px;">#</th>
								<th>' . htmlspecialchars((string) (isset($lang['112']) ? $lang['112'] : 'Equipo'), ENT_QUOTES, 'UTF-8') . '</th>
							</tr>
						</thead>
						<tbody>';

			$catTeams = array();
			foreach ($cat['seeds'] as $seed) {
				foreach ($seed['teams'] as $t) {
					$catTeams[] = array(
						'seed' => (int) $seed['seed'],
						'Equipo_DESC' => (string) $t['Equipo_DESC'],
					);
				}
			}
			usort($catTeams, function ($a, $b) {
				if ($a['seed'] !== $b['seed']) {
					return $a['seed'] - $b['seed'];
				}
				return strcasecmp($a['Equipo_DESC'], $b['Equipo_DESC']);
			});

			if (count($catTeams) === 0) {
				$html .= '<tr><td colspan="2">' . htmlspecialchars((string) (isset($lang['101-7']) ? $lang['101-7'] : 'No teams'), ENT_QUOTES, 'UTF-8') . '</td></tr>';
			} else {
				$catRank = 0;
				foreach ($catTeams as $t) {
					$catRank++;
					$html .= '<tr>
							<td>' . $catRank . '</td>
							<td>' . htmlspecialchars((string) $t['Equipo_DESC'], ENT_QUOTES, 'UTF-8') . '</td>
						</tr>';
				}
			}

			$html .= '</tbody>
					</table>
				</div>
			</div>';
		}

		$html .= '</div>
			<div class="mt-4 mb-2" style="clear: both;">
				<button type="button" class="btn btn-primary" id="gsGenerateBtn" data-msg-weeks="' . htmlspecialchars((string) (isset($lang['101-8']) ? $lang['101-8'] : 'Enter weeks'), ENT_QUOTES, 'UTF-8') . '" onClick="generateScheduleRun();">' . htmlspecialchars((string) (isset($lang['826']) ? $lang['826'] : 'Generate'), ENT_QUOTES, 'UTF-8') . '</button>
			</div>';
	}

	$html .= '</div>
	</div>';

	az_gs_json_exit(array(
		'status' => '1',
		'message' => '',
		'dataGenerateSchedule' => $html,
	));
?>
