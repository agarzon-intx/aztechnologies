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
	$sessionstat = $fgmembersite->CheckLogin('configManagementFontUpload.php');

	include('lang.' . $_COOKIE[$Config->getAlias() . 'language'] . '.php');

	$includeDir = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'include';
	require_once $includeDir . DIRECTORY_SEPARATOR . 'flyer_text_style.inc.php';

	$t = function ($key, $fallback) use ($lang) {
		return isset($lang[$key]) ? $lang[$key] : $fallback;
	};
	$fail = function ($msg) {
		echo json_encode(array('status' => '0', 'dataConfigAnswer' => $msg));
		exit;
	};

	$basePath = rtrim((string) $Config->getPath(), '/\\');
	$fontDir = $basePath . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . 'font';
	if ($basePath === '') {
		$fail($t('452-46', 'Could not save the font on the server.'));
	}

	$uploads = array();
	if (isset($_FILES['fontFiles']) && is_array($_FILES['fontFiles']['name'])) {
		foreach ($_FILES['fontFiles']['name'] as $i => $n) {
			$uploads[] = array(
				'name' => (string) $n,
				'tmp' => (string) $_FILES['fontFiles']['tmp_name'][$i],
				'error' => (int) $_FILES['fontFiles']['error'][$i],
				'size' => (int) $_FILES['fontFiles']['size'][$i],
			);
		}
	}
	$uploads = array_values(array_filter($uploads, function ($u) {
		return $u['error'] !== UPLOAD_ERR_NO_FILE;
	}));
	if (count($uploads) === 0) {
		$fail($t('452-40', 'Choose a font file (.ttf).'));
	}

	require_once $includeDir . DIRECTORY_SEPARATOR . 'flyer_font_install.php';

	$added = array();
	$errors = array();
	foreach ($uploads as $u) {
		$label = basename($u['name']);
		if ($u['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($u['tmp'])) {
			$errors[] = $label . ': upload error ' . $u['error'];
			continue;
		}
		$r = az_flyer_font_install($u['tmp'], $label, $fontDir, $t);
		if ($r['ok']) {
			$added[] = array('family' => $r['family'], 'style' => $r['style'], 'name' => $r['name']);
		} else {
			$errors[] = $label . ': ' . $r['error'];
		}
	}

	if (count($added) === 0) {
		$fail(implode("\n", $errors));
	}

	$styleLabels = array(
		'' => $t('452-32', 'Regular'),
		'B' => $t('452-33', 'Bold'),
		'I' => $t('452-34', 'Italic'),
		'BI' => $t('452-35', 'Bold italic'),
	);
	$fonts = array();
	foreach (az_flyer_available_fonts($fontDir) as $family => $info) {
		if ($info['symbolic'] || empty($info['styles'])) {
			continue;
		}
		$fonts[$family] = array();
		foreach ($info['styles'] as $code => $unused) {
			$fonts[$family][] = array($code, $styleLabels[$code]);
		}
	}
	$addedFamilies = array_values(array_unique(array_map(function ($a) {
		return $a['family'];
	}, $added)));
	$web = az_flyer_font_web_assets($fontDir, $addedFamilies);

	$msg = $t('452-39', 'Font added. Select it and press Save to use it on the flyers.')
		. ' (' . implode(', ', array_map(function ($a) { return $a['name']; }, $added)) . ')';
	if (count($errors) > 0) {
		$msg .= "\n" . implode("\n", $errors);
	}
	echo json_encode(array(
		'status' => '1',
		'dataConfigAnswer' => $msg,
		'added' => $added,
		'fonts' => $fonts,
		'fontCss' => $web['css'],
		'fontFaces' => $web['faces'],
	));
