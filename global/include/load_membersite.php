<?php
/**
 * Loads membersite_config.php from this directory (always global/include).
 * From a site root index.php use:
 *   require_once dirname(__DIR__) . '/global/include/load_membersite.php';
 */
$p = __DIR__ . DIRECTORY_SEPARATOR . 'membersite_config.php';
if (!is_file($p)) {
	http_response_code(500);
	header('Content-Type: text/plain; charset=utf-8');
	$msg = "membersite_config.php is missing at:\n  {$p}\n\n"
		. "Restore from Git:\n  git checkout HEAD -- global/include/membersite_config.php\n\n"
		. "Or recreate site links to global:\n  macOS/Linux: bash tools/recreate-site-symlinks.sh\n  Windows: tools\\recreate-site-junctions.ps1\n";
	die($msg);
}
require $p;
