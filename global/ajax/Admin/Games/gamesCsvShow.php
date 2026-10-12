<?php
	session_start();
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

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
	require_once __DIR__ . '/gamesCsv.inc.php';
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('gamesCsvShow.php');

	include('lang.' . $_COOKIE[$Config->getAlias() . 'language'] . '.php');

	header('Content-Type: application/json; charset=utf-8');
	if (!games_csv_is_admin($Config)) {
		echo json_encode(array('status' => '0', 'message' => 'Not allowed.'));
		exit;
	}

	$h = function ($s) {
		return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
	};

	list($Season, $tournamentName) = games_csv_current_season($Config, $schema);
	$types = ($Season > 0) ? games_csv_week_types($Config, $schema, $Season, $lang) : array();

	$html = '<div id="gamesCsv" class="tabla active" style="display: block;padding-top: 10px;">
		<div class="datagridAdmin" style="display: block;width: 100%;height: auto;">
			<div style="float: left;width: 100%;padding-top: 8px;padding-bottom: 8px;">
				<legend style="font-size: 25px; font-weight: bold; border-bottom: 0px">' . $h($lang['101-52']) . '</legend>
				<div class="text-muted">' . $h($lang['105']) . ': <strong>' . $h($tournamentName) . '</strong></div>
			</div>
			<div style="clear: both;"></div>';

	if (count($types) === 0) {
		$html .= '<div class="alert alert-warning">' . $h($lang['101-55']) . '</div>';
	} else {
		$options = '';
		foreach ($types as $t => $label) {
			$options .= '<option value="' . (int) $t . '">' . $h($label) . '</option>';
		}
		$html .= '<div class="row g-3 mt-1" style="padding: 8px 12px 16px; clear: both;">
				<div class="col-12 col-md-6 col-lg-3">
					<div class="input-group input-group-outline my-3 is-filled" style="margin-top: 5px !important;margin-bottom: 0px !important;">
						<label class="form-label">' . $h($lang['101-53']) . '</label>
						<select class="form-control" id="gcType" name="gcType">' . $options . '</select>
					</div>
				</div>
				<div class="col-12 col-md-6 col-lg-3">
					<button type="button" class="btn btn-primary" style="margin-top: 5px;" onClick="gamesExportCsvDownload();">' . $h($lang['101-54']) . '</button>
				</div>
			</div>';
	}
	$html .= '</div>
	</div>';

	$Config->Close();
	echo json_encode(array('status' => '1', 'message' => 'Success.', 'dataGamesCsv' => $html), JSON_UNESCAPED_UNICODE);
