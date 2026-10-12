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
	$sessionstat = $fgmembersite->CheckLogin('configManagementPdfLayoutSave.php');

	include('lang.' . $_COOKIE[$Config->getAlias() . 'language'] . '.php');

	require_once dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'pdf_layout.php';

	$t = function ($key, $fallback) use ($lang) {
		return isset($lang[$key]) ? $lang[$key] : $fallback;
	};

	$data = isset($_POST['layout']) ? json_decode((string) $_POST['layout'], true) : null;
	$siteRoot = rtrim((string) $Config->getPath(), '/\\');
	if (!is_array($data) || $siteRoot === '' || !az_pdf_layout_save($data, $siteRoot)) {
		echo json_encode(array('status' => '0', 'dataConfigAnswer' => $t('452-81', 'The layout could not be saved.')));
		exit;
	}

	$saved = az_pdf_layout_load($siteRoot);

	// Uploaded replacement images no longer used (kept one day: an editor may still be previewing them).
	$used = array();
	foreach ($saved as $o) {
		if (isset($o['src'])) {
			$used[basename($o['src'])] = true;
		}
	}
	$imgDir = $siteRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, az_pdf_layout_image_dir());
	foreach (glob($imgDir . DIRECTORY_SEPARATOR . 'img-*') ?: array() as $img) {
		if (!isset($used[basename($img)]) && is_file($img) && filemtime($img) < time() - 86400) {
			@unlink($img);
		}
	}

	echo json_encode(array(
		'status' => '1',
		'dataConfigAnswer' => $t('441', 'Settings Updated'),
		'layout' => (object) $saved,
	), JSON_UNESCAPED_UNICODE);
?>
