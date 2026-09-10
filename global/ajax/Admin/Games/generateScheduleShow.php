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
	$sessionstat = $fgmembersite->CheckLogin('generateScheduleShow.php');

	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'generate_schedule_seeds.inc.php';

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');

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
			$tournamentName = $rowTorneo['Torneo_Desc'];
		} else {
			$Season = 0;
		}
	}

	if ($Season <= 0) {
		$html = '<div id="generateSchedule" class="tabla active" style="display: block;padding-top: 10px;">
			<div class="alert alert-warning">' . htmlspecialchars($lang['101-14'], ENT_QUOTES, 'UTF-8') . '</div>
		</div>';
		$retunData = array(
			'status' => '1',
			'message' => '',
			'dataGenerateSchedule' => $html,
		);
		header('Content-Type: application/json');
		echo json_encode($retunData);
		exit();
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
	if ($resCat && $resCat->num_rows > 0) {
		while ($row = $resCat->fetch_assoc()) {
			$catId = (int) $row['Categoria_ID'];
			$weekCount = 0;
			$sqlWeeks = "SELECT COUNT(*) AS cnt
					FROM $schema.Jornada j
						INNER JOIN $schema.Categorias c
							ON c.Calendario_ID = j.Calendario_ID
							AND c.Categoria_ID = $catId
							AND c.Torneo_ID = $Season
					WHERE j.Torneo_ID = $Season";
			$resWeeks = $Config->query($sqlWeeks);
			if ($resWeeks && $resWeeks->num_rows > 0) {
				$w = $resWeeks->fetch_assoc();
				$weekCount = (int) $w['cnt'];
			}

			$seeds = array();
			foreach ($institutionSeeds as $seed) {
				$teams = az_generate_schedule_teams_for_seed_category($Config, $schema, $Season, $catId, $seed);
				if (count($teams) === 0) {
					continue;
				}
				$seeds[] = array(
					'seed' => (int) $seed['seed'],
					'Institucion_ID' => (int) $seed['Institucion_ID'],
					'Institucion_DESC' => $seed['Institucion_DESC'],
					'TeamCount' => count($teams),
					'teams' => $teams,
				);
			}

			$categories[] = array(
				'Categoria_ID' => $catId,
				'Categoria_Desc' => $row['Categoria_Desc'],
				'weekCount' => $weekCount,
				'seeds' => $seeds,
			);
		}
	}

	$html = '<div id="generateSchedule" class="tabla active" style="display: block;padding-top: 10px;" data-tournament-id="' . (int) $Season . '">
		<div class="datagridAdmin" style="display: block;width: 100%;height: auto;">
			<div style="float: left;width: 100%;padding-top: 8px;padding-bottom: 8px;">
				<legend style="font-size: 25px; font-weight: bold; border-bottom: 0px">' . htmlspecialchars($lang['101-1'], ENT_QUOTES, 'UTF-8') . '</legend>
				<div class="text-muted">' . htmlspecialchars($lang['105'], ENT_QUOTES, 'UTF-8') . ': <strong>' . htmlspecialchars($tournamentName, ENT_QUOTES, 'UTF-8') . '</strong></div>
			</div>';

	if (count($categories) === 0) {
		$html .= '<div class="alert alert-warning">' . htmlspecialchars($lang['101-6'], ENT_QUOTES, 'UTF-8') . '</div>';
	} else {
		$html .= '<div class="nav-wrapper position-relative end-0">
				<ul class="nav nav-pills nav-fill p-1" role="tablist" style="background: #cee6ff; flex-direction: unset !important; flex-wrap: wrap;" id="generateScheduleNavTabs">';

		foreach ($categories as $idx => $cat) {
			$active = ($idx === 0) ? ' active' : '';
			$selected = ($idx === 0) ? 'true' : 'false';
			$panelId = 'gsCat' . (int) $cat['Categoria_ID'];
			$html .= '<li class="nav-item" id="' . $panelId . 'li">
					<a class="nav-link mb-0 px-2 py-1' . $active . '" data-bs-toggle="tab" style="cursor: pointer;" callval="#' . $panelId . '" role="tab" aria-controls="' . $panelId . 'li" aria-selected="' . $selected . '">'
						. htmlspecialchars($cat['Categoria_Desc'], ENT_QUOTES, 'UTF-8') .
					'</a>
				</li>';
		}

		$html .= '</ul>
			</div>
			<script>initNavs("generateScheduleNavTabs");</script>
			<div class="tabla-content" style="padding-top: 12px;">';

		foreach ($categories as $idx => $cat) {
			$panelId = 'gsCat' . (int) $cat['Categoria_ID'];
			$display = ($idx === 0) ? 'block' : 'none';
			$activeClass = ($idx === 0) ? ' active' : '';
			$weeks = (int) $cat['weekCount'];
			$weeksValue = ($weeks > 0) ? (string) $weeks : '';
			$weeksHint = ($weeks > 0) ? $lang['101-2'] : $lang['101-3'];
			$weeksHint = str_replace('%1', (string) $weeks, $weeksHint);

			$html .= '<div id="' . $panelId . '" class="tabla' . $activeClass . '" style="display: ' . $display . '; height: auto;" data-category-id="' . (int) $cat['Categoria_ID'] . '">
				<div class="row align-items-end mb-3">
					<div class="col-12 col-md-6 col-lg-4">
						<label class="form-label" for="gsWeeks_' . (int) $cat['Categoria_ID'] . '">' . htmlspecialchars($lang['108'], ENT_QUOTES, 'UTF-8') . '</label>
						<input type="number" min="1" step="1" class="form-control gs-weeks-input" id="gsWeeks_' . (int) $cat['Categoria_ID'] . '" data-category-id="' . (int) $cat['Categoria_ID'] . '" data-default-weeks="' . $weeks . '" value="' . htmlspecialchars($weeksValue, ENT_QUOTES, 'UTF-8') . '" />
						<small class="text-muted">' . htmlspecialchars($weeksHint, ENT_QUOTES, 'UTF-8') . '</small>
					</div>
				</div>
				<div class="mb-2"><strong>' . htmlspecialchars($lang['101-4'], ENT_QUOTES, 'UTF-8') . '</strong></div>
				<div class="table-responsive">
					<table class="table table-sm table-striped align-middle mb-0">
						<thead>
							<tr>
								<th style="width: 70px;">#</th>
								<th>' . htmlspecialchars($lang['113-2'], ENT_QUOTES, 'UTF-8') . '</th>
								<th style="width: 100px;">' . htmlspecialchars($lang['101-5'], ENT_QUOTES, 'UTF-8') . '</th>
								<th>' . htmlspecialchars($lang['112'], ENT_QUOTES, 'UTF-8') . '</th>
							</tr>
						</thead>
						<tbody>';

			if (count($cat['seeds']) === 0) {
				$html .= '<tr><td colspan="4">' . htmlspecialchars($lang['101-7'], ENT_QUOTES, 'UTF-8') . '</td></tr>';
			} else {
				foreach ($cat['seeds'] as $seed) {
					$teamNames = array();
					foreach ($seed['teams'] as $t) {
						$teamNames[] = htmlspecialchars($t['Equipo_DESC'], ENT_QUOTES, 'UTF-8');
					}
					$html .= '<tr>
							<td>' . (int) $seed['seed'] . '</td>
							<td>' . htmlspecialchars($seed['Institucion_DESC'], ENT_QUOTES, 'UTF-8') . '</td>
							<td>' . (int) $seed['TeamCount'] . '</td>
							<td>' . implode(', ', $teamNames) . '</td>
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
				<button type="button" class="btn btn-primary" id="gsGenerateBtn" data-msg-weeks="' . htmlspecialchars($lang['101-8'], ENT_QUOTES, 'UTF-8') . '" onClick="generateScheduleRun();">' . htmlspecialchars($lang['826'], ENT_QUOTES, 'UTF-8') . '</button>
			</div>';
	}

	$html .= '</div>
	</div>';

	$retunData = array(
		'status' => '1',
		'message' => '',
		'dataGenerateSchedule' => $html,
	);

	header('Content-Type: application/json');
	echo json_encode($retunData);
	exit();
?>
