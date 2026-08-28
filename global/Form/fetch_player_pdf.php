<?php
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

	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin($Config,'fetchPlayerPdf.php');

	$jugador = SanitizeInteger($_GET['Jugador_ID']);
	$pdf_content = '';

	if ($jugador !== '' && $Config->jugadoresHasColumn('IdentificacionPDF')) {
		$Config->connect();
		$select_pdf = "select IdentificacionPDF from $schema.Jugadores where Jugador_ID = $jugador and OCTET_LENGTH(IdentificacionPDF) > 100";
		$result = $Config->query($select_pdf);
		if ($result && $result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$pdf_content = $row["IdentificacionPDF"];
			}
		}
		$Config->close();
	}

	if ($pdf_content === '' || $pdf_content === null) {
		http_response_code(404);
		exit;
	}

	header("Content-Type: application/pdf");
	header("Content-Disposition: inline; filename=\"player-" . $jugador . ".pdf\"");
	header("Content-Length: " . strlen($pdf_content));
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	echo $pdf_content;
?>
