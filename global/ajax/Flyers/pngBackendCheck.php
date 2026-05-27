<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

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
require_once dirname(__DIR__, 2) . '/include/flyer_ci_export.php';

$retunData = array('status' => '0', 'message' => 'Unauthorized');

try {
	$fgmembersite->CheckLogin('pngBackendCheck.php');
	$sitePath = $Config->getPath();
	$status = az_flyer_ci_png_backend_status($sitePath);
	$ok = $status['php_imagick']
		|| $status['imagemagick_cli'] !== null
		|| $status['ghostscript_cli'] !== null
		|| $status['pdftoppm_cli'] !== null;
	$retunData = array(
		'status' => $ok ? '1' : '0',
		'message' => $ok ? 'At least one PNG backend is available.' : az_flyer_ci_png_export_unavailable_message($sitePath),
		'backends' => $status,
	);
} catch (Throwable $e) {
	$retunData = array('status' => '0', 'message' => $e->getMessage());
}

echo json_encode($retunData);
