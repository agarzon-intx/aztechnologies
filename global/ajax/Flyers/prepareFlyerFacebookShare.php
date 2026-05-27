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
require_once dirname(__DIR__, 2) . '/include/flyer_facebook_share.php';
require_once dirname(__DIR__, 2) . '/include/flyer_facebook_prepare.php';

$retunData = array('status' => '0', 'message' => '');

try {
	@ini_set('memory_limit', '512M');
	set_time_limit(600);
	if (function_exists('ignore_user_abort')) {
		ignore_user_abort(true);
	}

	$fgmembersite->CheckLogin('cedulas.php');
	$lang = az_flyer_lang_load_for_config($Config);

	$type = isset($_POST['type']) ? strtolower(trim((string) $_POST['type'])) : '';
	$jornada = isset($_POST['Jornada_ID']) ? (int) $_POST['Jornada_ID'] : 0;
	$categoria = isset($_POST['Categoria_ID']) ? (int) $_POST['Categoria_ID'] : 0;
	$juego = isset($_POST['Juego_ID']) ? (int) $_POST['Juego_ID'] : 0;

	$share = az_flyer_facebook_prepare_share_data($Config, $type, $jornada, $categoria, $juego, $lang);
	$Config->Close();

	$retunData = array(
		'status' => '1',
		'message' => az_flyer_lang('jsfb12'),
		'image_count' => $share['image_count'],
		'share' => $share,
	);
} catch (Throwable $e) {
	if (isset($Config)) {
		$Config->Close();
	}
	$retunData = array(
		'status' => '0',
		'message' => $e->getMessage(),
	);
}

echo json_encode($retunData);
