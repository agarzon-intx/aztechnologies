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
	$sessionstat = $fgmembersite->CheckLogin('gamesCsvDownload.php');

	if (!games_csv_is_admin($Config)) {
		http_response_code(403);
		echo 'Not allowed.';
		exit;
	}

	include('lang.' . $_COOKIE[$Config->getAlias() . 'language'] . '.php');

	list($Season, $tournamentName) = games_csv_current_season($Config, $schema);
	$type = (int) ($_GET['type'] ?? 0);

	$sql = "SELECT IFNULL(cl.Categoria_Orden, cv.Categoria_Orden) AS Categoria_Orden,
				IFNULL(cl.Categoria_Desc, cv.Categoria_Desc) AS Categoria,
				jo.Jornada_ID, jo.Jornada_Orden, jo.Jornada_Desc, j.Juego_ID,
				IFNULL(l.Equipo_DESC, 'DESCANSA') AS Local,
				IFNULL(v.Equipo_DESC, 'DESCANSA') AS Visitante
			FROM $schema.Juegos j
				LEFT OUTER JOIN $schema.Equipos l ON j.Local_ID = l.Equipo_ID AND j.Torneo_ID = l.Torneo_ID
				LEFT OUTER JOIN $schema.Equipos v ON j.Visitante_ID = v.Equipo_ID AND j.Torneo_ID = v.Torneo_ID
				LEFT OUTER JOIN $schema.Categorias cl ON l.Fuerza = cl.Categoria_ID AND j.Torneo_ID = cl.Torneo_ID
				LEFT OUTER JOIN $schema.Categorias cv ON v.Fuerza = cv.Categoria_ID AND j.Torneo_ID = cv.Torneo_ID
				LEFT OUTER JOIN $schema.Jornada jo ON j.Jornada_ID = jo.Jornada_ID AND j.Torneo_ID = jo.Torneo_ID
			WHERE j.Torneo_ID = $Season AND jo.Jornada_Type = $type
			ORDER BY 1, 2, 6";
	$result = ($Season > 0) ? $Config->query($sql) : null;

	$clean = function ($v) {
		return trim(str_replace(array("\r", "\n", '"'), array(' ', ' ', ''), (string) $v));
	};

	$filename = trim(preg_replace('/[\\\\\/:*?"<>|\x00-\x1F]+/', '', $tournamentName . '-' . $lang['100'])) . '.csv';
	$filenameAscii = preg_replace('/[^A-Za-z0-9 _.\-]/', '_', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $filename));
	header('Content-Type: text/csv; charset=UTF-8');
	header('Content-Disposition: attachment; filename="' . $filenameAscii . '"; filename*=UTF-8\'\'' . rawurlencode($filename));

	// UTF-8 BOM: without it Excel opens the file as ANSI and garbles accented characters.
	echo "\xEF\xBB\xBF";
	echo $clean($lang['356']) . ',' . $clean($lang['358']) . ',' . $clean($lang['363']) . ',' . $clean($lang['364']) . "\r\n";
	if ($result) {
		while ($row = $result->fetch_assoc()) {
			echo $clean($row['Categoria']) . ',' . $clean($row['Jornada_Desc']) . ',' . $clean($row['Local']) . ',' . $clean($row['Visitante']) . "\r\n";
		}
	}
	$Config->Close();
