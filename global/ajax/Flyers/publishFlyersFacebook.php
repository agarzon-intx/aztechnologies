<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

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

require_once dirname(__DIR__, 2) . '/include/membersite_config.php';

require_once dirname(__DIR__, 2) . '/include/pdf_image_helpers.php';
require_once dirname(__DIR__, 2) . '/include/flyer_ci_export.php';
require_once dirname(__DIR__, 2) . '/include/flyer_sc_pdf.php';
require_once dirname(__DIR__, 2) . '/include/flyer_game_pdf.php';
require_once dirname(__DIR__, 2) . '/include/flyer_facebook_publish.php';
require_once dirname(__DIR__, 2) . '/include/flyer_facebook_share.php';

$retunData = array('status' => '0', 'message' => '');

try {
	@ini_set('memory_limit', '512M');
	set_time_limit(600);
	if (function_exists('ignore_user_abort')) {
		ignore_user_abort(true);
	}

	$sessionstat = $fgmembersite->CheckLogin('publishFlyersFacebook.php');
	include 'lang.' . $_COOKIE[$Config->getAlias() . 'language'] . '.php';
	$retunData['message'] = az_flyer_lang('js0002');

	$type = isset($_POST['type']) ? strtolower(trim((string) $_POST['type'])) : '';
	$jornada = isset($_POST['Jornada_ID']) ? (int) $_POST['Jornada_ID'] : 0;
	$categoria = isset($_POST['Categoria_ID']) ? (int) $_POST['Categoria_ID'] : 0;
	$juego = isset($_POST['Juego_ID']) ? (int) $_POST['Juego_ID'] : 0;

	$schema = $Config->getSchema();
	$sitePath = $Config->getPath();
	$fbConfig = az_flyer_facebook_read_config($sitePath);
	if ($fbConfig === null) {
		throw new RuntimeException('Facebook is not configured for this site. Add [facebook] section to ini/config.ini.');
	}

	$sitePdf = $sitePath . DIRECTORY_SEPARATOR . 'pdf';
	$flyerCiPage = $sitePdf . DIRECTORY_SEPARATOR . 'flyer_ci_page.php';
	if (!is_readable($flyerCiPage)) {
		throw new RuntimeException('Missing pdf/flyer_ci_page.php on this site.');
	}
	require_once $flyerCiPage;

	$alphapdfPath = $sitePdf . DIRECTORY_SEPARATOR . 'alphapdf.php';
	if (!is_readable($alphapdfPath)) {
		throw new RuntimeException('Missing pdf/alphapdf.php on this site.');
	}
	require_once $alphapdfPath;

	$siteRoot = az_pdf_site_root($Config);
	$Config->connect();
	$Config->LoadLogo();
	$Config->LoadFlags();

	$pdf = new FPDF('P', 'mm', array(210, 210));
	$pdf->AddFont('Coluna', 'B', 'Coluna.php');
	$pageLabels = array();
	$albumName = az_flyer_lang('jsfb22');
	$albumMessage = '';

	if ($type === 'jornada') {
		if ($jornada < 1) {
			throw new RuntimeException('Jornada_ID is required.');
		}
		$categoria = az_flyer_ci_categoria_from_cookie($Config);
		if ($categoria < 1) {
			throw new RuntimeException('Select a category before publishing flyers.');
		}
		$Config->query("SET lc_time_names = 'es_MX';");
		$sql = flyer_ci_juegos_sql($schema, $jornada, $categoria);
		$result1 = $Config->query($sql);
		if (!$result1 || $result1->num_rows < 1) {
			throw new RuntimeException('No games found for this week.');
		}
		if ($result1->num_rows > 80) {
			throw new RuntimeException('Too many games (' . $result1->num_rows . '). Maximum is 80.');
		}
		while ($row1 = $result1->fetch_assoc()) {
			$pageLabels[] = 'juego-' . $row1['Juego_ID'];
			flyer_ci_add_page($pdf, $siteRoot, $row1);
		}
		$albumName = az_flyer_lang('jsfb15', array($jornada));
		$albumMessage = az_flyer_lang('jsfb16', array($jornada));
	} elseif ($type === 'categoria') {
		if ($jornada < 1 || $categoria < 1) {
			throw new RuntimeException('Jornada_ID and Categoria_ID are required.');
		}
		flyer_sc_build_pdf($pdf, $Config, $schema, $jornada, $categoria, $siteRoot, $lang, $pageLabels);
		$albumName = az_flyer_lang('jsfb17', array($jornada, $categoria));
		$albumMessage = az_flyer_lang('jsfb18', array($categoria, $jornada));
	} elseif ($type === 'juego') {
		if ($juego < 1) {
			throw new RuntimeException('Juego_ID is required.');
		}
		flyer_game_build_pdf($pdf, $Config, $schema, $juego, $siteRoot, $lang, $pageLabels);
		$albumName = az_flyer_lang('jsfb19', array($juego));
		$albumMessage = az_flyer_lang('jsfb20', array($juego));
	} else {
		throw new RuntimeException('Invalid flyer type.');
	}

	if (count($pageLabels) < 1) {
		throw new RuntimeException('No flyer pages were generated.');
	}

	$generated = az_flyer_ci_generate_png_paths($pdf->Output('S'), $pageLabels, 200, $sitePath);
	$tmpDir = $generated['tmpDir'];
	$pngPaths = $generated['paths'];

	try {
		$publish = az_flyer_facebook_publish_page_photos(
			$fbConfig['page_id'],
			$fbConfig['access_token'],
			$pngPaths,
			$albumMessage !== '' ? $albumMessage : $albumName
		);
	} finally {
		az_flyer_ci_cleanup_png_paths($tmpDir, $pngPaths);
	}

	$Config->Close();

	$retunData = array(
		'status' => '1',
		'message' => az_flyer_lang('jsfb14', array($publish['photo_count'])),
		'photo_count' => $publish['photo_count'],
		'post_ids' => $publish['post_ids'],
		'page_url' => $publish['page_url'],
		'album_url' => $publish['first_post_url'],
		'first_post_url' => $publish['first_post_url'],
	);
} catch (Throwable $e) {
	if (isset($Config)) {
		$Config->Close();
	}
	$retunData = array('status' => '0', 'message' => $e->getMessage());
}

echo json_encode($retunData);
