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
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'flyer_ci_page.php';
	require('membersite_config.php');
	$Config = new Configuration();
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('cedulas.php');
	$Config->connect();

	include('lang.' . $_COOKIE[$Config->getAlias() . 'language'] . '.php');

	$jornada = isset($_GET['Jornada_ID']) ? (int) $_GET['Jornada_ID'] : 0;
	if ($jornada < 1) {
		az_flyer_ci_fail(400, 'Jornada_ID is required.');
	}
	$categoria = az_flyer_ci_categoria_from_cookie($Config);
	if ($categoria < 1) {
		az_flyer_ci_fail(400, 'Select a category in the application before exporting flyers.');
	}

	$siteRoot = az_pdf_site_root($Config);
	$Config->LoadLogo();
	$Config->LoadFlags();

	$pdf = new FPDF('P','mm',array(210,210));
	$pdf->AddFont('Coluna','B','Coluna.php');

	$Config->query("SET lc_time_names = 'es_MX';");

	$sql = flyer_ci_juegos_sql($schema, $jornada, $categoria);
	$result1 = $Config->query($sql);
	$pageLabels = array();

	if ($result1 && $result1->num_rows > 0) {
		if ($result1->num_rows > 80) {
			az_flyer_ci_fail(400, 'Too many games in this jornada (' . $result1->num_rows . '). Maximum is 80.');
		}
		while ($row1 = $result1->fetch_assoc()) {
			$pageLabels[] = 'juego-' . $row1['Juego_ID'];
			flyer_ci_add_page($pdf, $siteRoot, $row1);
		}
	} else {
		$pageLabels[] = 'sin-juegos';
		$pdf->Cell(200 , 8, $lang['9998'], 0, 0 , 'C' , false);
	}
	$Config->close();

	header('X-Flyer-Pages: ' . count($pageLabels));
	az_flyer_ci_send_png_zip($pdf->Output('S'), $pageLabels, $jornada, '', $Config->getPath());
?>
