<?php
	session_start();

	header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');
	header('Content-Type: application/json; charset=UTF-8');

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

	require('membersite_config.php');
	$sessionstat = $fgmembersite->CheckLogin('configManagementPdfLayoutImage.php');
	if (!$sessionstat) {
		http_response_code(401);
		echo json_encode(array('status' => '0'));
		exit;
	}

	include('lang.' . $_COOKIE[$Config->getAlias() . 'language'] . '.php');

	require_once dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'pdf_layout.php';

	$t = function ($key, $fallback) use ($lang) {
		return isset($lang[$key]) ? $lang[$key] : $fallback;
	};
	$fail = function ($msg) {
		echo json_encode(array('status' => '0', 'dataConfigAnswer' => $msg));
		exit;
	};

	$siteRoot = rtrim((string) $Config->getPath(), '/\\');
	$f = isset($_FILES['image']) ? $_FILES['image'] : null;
	if ($siteRoot === '' || !$f || !is_array($f) || (int) $f['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($f['tmp_name'])) {
		$fail($t('452-87', 'Choose an image (PNG, JPG, GIF or WebP).'));
	}
	if ((int) $f['size'] > 8 * 1024 * 1024) {
		$fail($t('452-88', 'The image is too large (maximum 8 MB).'));
	}
	$info = @getimagesize($f['tmp_name']);
	$exts = array(IMAGETYPE_PNG => 'png', IMAGETYPE_JPEG => 'jpg', IMAGETYPE_GIF => 'gif');
	if (defined('IMAGETYPE_WEBP')) {
		$exts[IMAGETYPE_WEBP] = 'webp';
	}
	if (!$info || !isset($exts[$info[2]]) || $info[0] < 1 || $info[1] < 1) {
		$fail($t('452-87', 'Choose an image (PNG, JPG, GIF or WebP).'));
	}

	$rel = az_pdf_layout_image_dir() . '/img-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $exts[$info[2]];
	$dir = $siteRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, az_pdf_layout_image_dir());
	if ((!is_dir($dir) && !@mkdir($dir, 0755, true)) || !@move_uploaded_file($f['tmp_name'], $siteRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel))) {
		$fail($t('452-89', 'The image could not be saved.'));
	}
	@chmod($siteRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel), 0644);

	echo json_encode(array('status' => '1', 'src' => $rel, 'w' => (int) $info[0], 'h' => (int) $info[1]));
?>
