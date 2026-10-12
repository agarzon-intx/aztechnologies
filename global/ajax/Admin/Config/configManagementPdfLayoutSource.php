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
	$sessionstat = $fgmembersite->CheckLogin('configManagementPdfLayoutSource.php');
	if (!$sessionstat) {
		http_response_code(401);
		echo json_encode(array('status' => '0'));
		exit;
	}

	// Source lines of layout keys ("pdf/flyer_ci_page.php:58"), only .php files of this site or of global/.
	$siteRoot = realpath(rtrim((string) $Config->getPath(), '/\\'));
	$globalRoot = realpath(dirname(__DIR__, 3));
	$roots = array_filter(array($siteRoot, $globalRoot));

	$keys = isset($_POST['keys']) ? json_decode((string) $_POST['keys'], true) : null;
	$out = array();
	foreach (is_array($keys) ? array_slice($keys, 0, 20) : array() as $key) {
		if (!is_string($key) || !preg_match('#^([A-Za-z0-9_./\-]{1,200}\.php):(\d{1,6})$#', $key, $m) || strpos($m[1], '..') !== false) {
			continue;
		}
		$rel = $m[1];
		$line = (int) $m[2];
		$candidates = array();
		if ($siteRoot) {
			$candidates[] = $siteRoot . DIRECTORY_SEPARATOR . $rel;
		}
		if ($globalRoot && strpos($rel, 'global/') === 0) {
			$candidates[] = dirname($globalRoot) . DIRECTORY_SEPARATOR . $rel;
		}
		foreach ($candidates as $cand) {
			$real = realpath($cand);
			if (!$real || !is_file($real)) {
				continue;
			}
			$inRoot = false;
			foreach ($roots as $r) {
				if (strpos($real, $r . DIRECTORY_SEPARATOR) === 0) {
					$inRoot = true;
					break;
				}
			}
			if (!$inRoot) {
				continue;
			}
			$file = new SplFileObject($real);
			$lines = array();
			for ($n = max(1, $line - 2); $n <= $line + 2; $n++) {
				$file->seek($n - 1);
				if ($file->eof() && $file->current() === '') {
					break;
				}
				$lines[] = array('n' => $n, 't' => rtrim((string) $file->current(), "\r\n"));
			}
			$out[$key] = $lines;
			break;
		}
	}

	echo json_encode(array('status' => '1', 'lines' => (object) $out), JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
?>
