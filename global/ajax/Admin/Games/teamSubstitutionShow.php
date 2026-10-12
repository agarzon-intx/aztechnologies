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
	$sessionstat = $fgmembersite->CheckLogin('teamSubstitutionShow.php');

	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	$h = function ($s) {
		return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
	};

	$Season = 0;
	if (isset($_COOKIE[$Config->getAlias() . 'season']) && $_COOKIE[$Config->getAlias() . 'season'] !== '') {
		$Season = (int) SanitizeInteger($_COOKIE[$Config->getAlias() . 'season']);
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

	$title = $h(isset($lang['101-44']) ? $lang['101-44'] : 'Team substitution');
	$seasonLbl = $h(isset($lang['105']) ? $lang['105'] : 'Season');

	if ($Season <= 0) {
		$html = '<div id="teamSubstitution" class="tabla active" style="display: block;padding-top: 10px;">
			<div class="alert alert-warning">' . $h(isset($lang['101-14']) ? $lang['101-14'] : 'Select a tournament first.') . '</div>
		</div>';
		$Config->Close();
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode(array(
			'status' => '1',
			'message' => '',
			'dataTeamSubstitution' => $html,
			'tsData' => array('categories' => array(), 'teams' => array(), 'weeks' => array()),
		));
		exit;
	}

	$categories = array();
	$sqlCat = "SELECT Categoria_ID, Categoria_DESC, Calendario_ID
			FROM $schema.Categorias
			WHERE Torneo_ID = $Season
			ORDER BY Categoria_Orden ASC, Categoria_DESC ASC";
	$resCat = $Config->query($sqlCat);
	if ($resCat && $resCat->num_rows > 0) {
		while ($row = $resCat->fetch_assoc()) {
			$categories[] = array(
				'id' => (int) $row['Categoria_ID'],
				'name' => (string) $row['Categoria_DESC'],
				'calendarId' => (int) $row['Calendario_ID'],
			);
		}
	}

	$teams = array();
	$sqlTeams = "SELECT Equipo_ID, Equipo_DESC, Fuerza
			FROM $schema.Equipos
			WHERE Torneo_ID = $Season
				AND IFNULL(Activo, 0) = 1
				AND Equipo_DESC <> 'NA'
			ORDER BY Equipo_DESC ASC";
	$resTeams = $Config->query($sqlTeams);
	if ($resTeams && $resTeams->num_rows > 0) {
		while ($row = $resTeams->fetch_assoc()) {
			$teams[] = array(
				'id' => (int) $row['Equipo_ID'],
				'name' => (string) $row['Equipo_DESC'],
				'categoryId' => (int) $row['Fuerza'],
			);
		}
	}

	$weeks = array();
	$sqlWeeks = "SELECT Jornada_ID, Jornada_Desc, Jornada_DescCorta, Fecha_Inicio, Calendario_ID
			FROM $schema.Jornada
			WHERE Torneo_ID = $Season
			ORDER BY Fecha_Inicio ASC, Jornada_ID ASC";
	$resWeeks = $Config->query($sqlWeeks);
	if ($resWeeks && $resWeeks->num_rows > 0) {
		while ($row = $resWeeks->fetch_assoc()) {
			$label = trim((string) $row['Jornada_Desc']);
			if ($label === '') {
				$label = trim((string) $row['Jornada_DescCorta']);
			}
			$weeks[] = array(
				'id' => (int) $row['Jornada_ID'],
				'name' => $label,
				'calendarId' => (int) $row['Calendario_ID'],
			);
		}
	}

	$occupancy = array();
	$sqlOcc = "SELECT Jornada_ID, Local_ID AS Equipo_ID
			FROM $schema.Juegos
			WHERE Torneo_ID = $Season AND IFNULL(Local_ID, 0) > 0
			UNION
			SELECT Jornada_ID, Visitante_ID AS Equipo_ID
			FROM $schema.Juegos
			WHERE Torneo_ID = $Season AND IFNULL(Visitante_ID, 0) > 0";
	$resOcc = $Config->query($sqlOcc);
	if ($resOcc && $resOcc->num_rows > 0) {
		while ($row = $resOcc->fetch_assoc()) {
			$tid = (int) $row['Equipo_ID'];
			$jid = (int) $row['Jornada_ID'];
			if ($tid > 0 && $jid > 0) {
				$occupancy[] = array(
					'jornadaId' => $jid,
					'teamId' => $tid,
				);
			}
		}
	}

	$lblCat = $h(isset($lang['101-21']) ? $lang['101-21'] : 'Category');
	$lblSource = $h(isset($lang['101-45']) ? $lang['101-45'] : 'Source team');
	$lblTarget = $h(isset($lang['101-46']) ? $lang['101-46'] : 'Target team');
	$lblWeek = $h(isset($lang['101-47']) ? $lang['101-47'] : 'From week');
	$chooseTeam = $h(isset($lang['112-1']) ? $lang['112-1'] : 'Choose a team');
	$chooseWeek = $h(isset($lang['101-47']) ? $lang['101-47'] : 'From week');
	$noCats = $h(isset($lang['101-6']) ? $lang['101-6'] : 'No categories');

	$html = '<div id="teamSubstitution" class="tabla active" style="display: block;padding-top: 10px;">
		<div class="datagridAdmin" style="display: block;width: 100%;height: auto;">
			<div style="float: left;width: 100%;padding-top: 8px;padding-bottom: 8px;">
				<legend style="font-size: 25px; font-weight: bold; border-bottom: 0px">' . $title . '</legend>
				<div class="text-muted">' . $seasonLbl . ': <strong>' . $h($tournamentName) . '</strong></div>
			</div>
			<div style="clear: both;"></div>';

	if (count($categories) === 0) {
		$html .= '<div class="alert alert-warning">' . $noCats . '</div>';
	} else {
		$html .= '<div class="row g-3 mt-1" style="padding: 8px 12px 16px; clear: both;">
				<div class="col-12 col-md-6 col-lg-3">
					<div class="input-group input-group-outline my-3 is-filled" style="margin-top: 5px !important;margin-bottom: 0px !important;">
						<label class="form-label">' . $lblCat . '</label>
						<select class="form-control" id="tsCategory" name="tsCategory" onChange="teamSubstitutionOnCategoryChange();">
						</select>
					</div>
				</div>
				<div class="col-12 col-md-6 col-lg-3">
					<div class="input-group input-group-outline my-3 is-filled" style="margin-top: 5px !important;margin-bottom: 0px !important;">
						<label class="form-label">' . $lblSource . '</label>
						<select class="form-control" id="tsSource" name="tsSource" onChange="teamSubstitutionOnSourceChange();"></select>
					</div>
				</div>
				<div class="col-12 col-md-6 col-lg-3">
					<div class="input-group input-group-outline my-3 is-filled" style="margin-top: 5px !important;margin-bottom: 0px !important;">
						<label class="form-label">' . $lblTarget . '</label>
						<select class="form-control" id="tsTarget" name="tsTarget"></select>
					</div>
				</div>
				<div class="col-12 col-md-6 col-lg-3">
					<div class="input-group input-group-outline my-3 is-filled" style="margin-top: 5px !important;margin-bottom: 0px !important;">
						<label class="form-label">' . $lblWeek . '</label>
						<select class="form-control" id="tsWeek" name="tsWeek" onChange="teamSubstitutionFillTarget();"></select>
					</div>
				</div>
				<div class="col-12 col-md-6 col-lg-3">
					<button type="button" class="btn btn-primary" id="tsSaveBtn" style="margin-top: 5px;" onClick="teamSubstitutionRun();">' . $h(isset($lang['0000']) ? $lang['0000'] : 'Save') . '</button>
				</div>
			</div>';
	}

	$html .= '</div>
	</div>';

	$jsonFlags = JSON_UNESCAPED_UNICODE;
	if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
		$jsonFlags |= JSON_INVALID_UTF8_SUBSTITUTE;
	}

	$Config->Close();
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode(array(
		'status' => '1',
		'message' => 'Success.',
		'dataTeamSubstitution' => $html,
		'tsData' => array(
			'categories' => $categories,
			'teams' => $teams,
			'weeks' => $weeks,
			'occupancy' => $occupancy,
			'chooseTeam' => isset($lang['112-1']) ? $lang['112-1'] : 'Choose a team',
			'chooseWeek' => isset($lang['101-47']) ? $lang['101-47'] : 'From week',
			'msgMissing' => isset($lang['101-48']) ? $lang['101-48'] : 'Select source team, target team and week.',
			'msgConfirm' => isset($lang['101-49']) ? $lang['101-49'] : 'Replace the source team from this week on?',
		),
	), $jsonFlags);
?>
