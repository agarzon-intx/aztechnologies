<?php
// Persists the uploaded player document PDF into Jugadores.IdentificacionPDF.
// Kept outside PlayerCreate/PlayerUpdate so those procedures do not need a
// signature change on every schema.
//
// Expects: $Config, $Connection, $schema, $idpdf (temp file name), $playerIdForPdf.
// Runs with the working directory already changed to the site tmp folder.

if (!empty($idpdf) && !empty($playerIdForPdf) && $Config->jugadoresHasColumn('IdentificacionPDF')) {
	$pdfPath = basename($idpdf);
	if (is_readable($pdfPath)) {
		$pdfBytes = file_get_contents($pdfPath);
		if ($pdfBytes !== false && strncmp($pdfBytes, '%PDF-', 5) === 0) {
			$pdfEscaped = $Connection->real_escape_string($pdfBytes);
			$pdfSql = "UPDATE $schema.Jugadores SET IdentificacionPDF = '$pdfEscaped' WHERE Jugador_ID = " . (int) $playerIdForPdf;
			if ($Connection->query($pdfSql) === false) {
				error_log('player id pdf save failed: ' . $Connection->error);
			}
		}
		@unlink($pdfPath);
	}
}
