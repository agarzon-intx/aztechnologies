<?php
	require_once dirname(__DIR__) . '/site_paths.php';
	@ini_set('memory_limit', '512M');
	set_time_limit(300);
	if (function_exists('ignore_user_abort')) {
		ignore_user_abort(true);
	}

	require('alphapdf.php');
	$__azPdfHelpers = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'global' . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'pdf_image_helpers.php';
	if (!is_readable($__azPdfHelpers)) {
		header('HTTP/1.1 500 Internal Server Error');
		header('Content-Type: text/plain; charset=UTF-8');
		echo 'Missing pdf_image_helpers.php on server.';
		exit;
	}
	require_once $__azPdfHelpers;
	unset($__azPdfHelpers);
	require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'global' . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'flyer_ci_export.php';
	require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'global' . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'flyer_sc_pdf.php';
	require('membersite_config.php');
	$Config = new Configuration();
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('cedulas.php');
	$Config->connect();

	include('lang.' . $_COOKIE[$Config->getAlias() . 'language'] . '.php');

	$jornada = isset($_GET['Jornada_ID']) ? (int) $_GET['Jornada_ID'] : 0;
	$categoria = isset($_GET['Categoria_ID']) ? (int) $_GET['Categoria_ID'] : 0;
	if ($jornada < 1 || $categoria < 1) {
		az_flyer_ci_fail(400, 'Jornada_ID and Categoria_ID are required.');
	}

	$siteRoot = az_pdf_site_root($Config);
	$Config->LoadLogo();
	$Config->LoadFlags();

	$pdf = new AlphaPDF('P', 'mm', array(210, 210));
	$pdf->AddFont('Coluna', 'B', 'Coluna.php');

	$pageLabels = array();
	flyer_sc_build_pdf($pdf, $Config, $schema, $jornada, $categoria, $siteRoot, $lang, $pageLabels);

	$Config->close();

	if (count($pageLabels) > 80) {
		az_flyer_ci_fail(400, 'Too many flyer pages (' . count($pageLabels) . '). Maximum is 80.');
	}

	header('X-Flyer-Pages: ' . count($pageLabels));
	az_flyer_ci_send_png_zip($pdf->Output('S'), $pageLabels, $jornada, 'cat-' . $categoria, $Config->getPath());
