<?php
/**
 * Server capability check for flyerC-I PNG export (no heavy work).
 */
require_once dirname(__DIR__) . '/site_paths.php';
header('Content-Type: text/plain; charset=UTF-8');

$lines = array();
$lines[] = 'flyerC-I server check';
$lines[] = 'PHP ' . PHP_VERSION;
$lines[] = 'memory_limit=' . ini_get('memory_limit');
$lines[] = 'max_execution_time=' . ini_get('max_execution_time');
$lines[] = 'imagick=' . (extension_loaded('imagick') ? 'yes' : 'NO');
$lines[] = 'zip=' . (class_exists('ZipArchive') ? 'yes' : 'NO');
$lines[] = 'temp_writable=' . (is_writable(sys_get_temp_dir()) ? 'yes' : 'NO');

if (extension_loaded('imagick') && class_exists('Imagick')) {
	$im = new Imagick();
	$pdf = $im->queryFormats('PDF');
	$lines[] = 'imagick_pdf=' . (empty($pdf) ? 'NO (install Ghostscript)' : 'yes (' . implode(',', $pdf) . ')');
	$lines[] = 'imagick_version=' . Imagick::getVersion()['versionString'];
	$im->clear();
	$im->destroy();
}

$lines[] = '';
$lines[] = 'Export: flyerC-I.php?Jornada_ID=1 (one ZIP; default 200 DPI PNG, optional &dpi=96-200).';
$lines[] = 'Optional: &Juego_ID=123 for a single game only.';
$lines[] = '503 Service Unavailable usually means timeout or memory on shared hosting.';

echo implode("\n", $lines);
