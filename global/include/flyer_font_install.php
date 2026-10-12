<?php
/**
 * Installs a TrueType font into the shared global font folder (definition .php + compressed .z) using FPDF makefont,
 * so it is available to every site.
 */
require_once __DIR__ . DIRECTORY_SEPARATOR . 'flyer_text_style.inc.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'fpdf_makefont' . DIRECTORY_SEPARATOR . 'makefont.php';

if (!function_exists('az_flyer_font_install')) {
	/**
	 * @param callable $t function ($langKey, $fallback): string
	 * @return array{ok:bool, error?:string, family?:string, style?:string, name?:string}
	 */
	function az_flyer_font_install($srcPath, $label, $siteFontDir, $t) {
		$err = function ($key, $fallback) use ($t) {
			return array('ok' => false, 'error' => $t($key, $fallback));
		};
		if (!preg_match('/\.(ttf|otf)$/i', (string) $label) || !is_file($srcPath) || filesize($srcPath) > 5 * 1024 * 1024) {
			return $err('452-41', 'Invalid file: use a TrueType font (.ttf).');
		}
		$magic = (string) file_get_contents($srcPath, false, null, 0, 4);
		if ($magic === 'OTTO') {
			return $err('452-42', 'This font uses PostScript (CFF) outlines and is not supported; use the .ttf version.');
		}
		if ($magic !== "\x00\x01\x00\x00" && $magic !== 'true') {
			return $err('452-41', 'Invalid file: use a TrueType font (.ttf).');
		}
		try {
			$ttf = new TTFParser($srcPath);
			$ttf->Parse();
		} catch (Throwable $e) {
			return $err('452-41', 'Invalid file: use a TrueType font (.ttf).');
		}
		$psName = (string) $ttf->postScriptName;
		$embeddable = (bool) $ttf->embeddable;
		unset($ttf);
		// The PostScript name is written verbatim into the generated PHP definition file.
		if (!preg_match('/^[A-Za-z0-9][A-Za-z0-9_.+-]{0,63}$/', $psName)) {
			return $err('452-45', 'Invalid font name.');
		}
		if (!$embeddable) {
			return $err('452-43', 'The font license does not allow embedding it in PDFs.');
		}
		$parts = explode('-', $psName, 2);
		$family = $parts[0];
		$suffix = strtolower(isset($parts[1]) ? $parts[1] : '');
		$code = (preg_match('/bold|black|heavy/', $suffix) ? 'B' : '') . (preg_match('/italic|oblique/', $suffix) ? 'I' : '');
		if (in_array(strtolower($family), array('courier', 'helvetica', 'arial', 'times', 'symbol', 'zapfdingbats'), true)) {
			return $err('452-44', 'That font already exists.');
		}
		foreach (az_flyer_available_fonts($siteFontDir, true) as $exFamily => $exInfo) {
			if (strcasecmp($exFamily, $family) === 0 && isset($exInfo['styles'][$code])) {
				return $err('452-44', 'That font already exists.');
			}
		}
		$fontDir = az_flyer_global_font_dir();
		if (!is_dir($fontDir) && !@mkdir($fontDir, 0755, true)) {
			return $err('452-46', 'Could not save the font on the server.');
		}
		$destPhp = $fontDir . DIRECTORY_SEPARATOR . $psName . '.php';
		$destZ = $fontDir . DIRECTORY_SEPARATOR . $psName . '.z';
		if (is_file($destPhp) || is_file($destZ)) {
			return $err('452-44', 'That font already exists.');
		}

		$work = rtrim(sys_get_temp_dir(), '/\\') . DIRECTORY_SEPARATOR . 'azfont-' . bin2hex(random_bytes(6));
		if (!@mkdir($work, 0700) || !@copy($srcPath, $work . DIRECTORY_SEPARATOR . $psName . '.ttf')) {
			return $err('452-46', 'Could not save the font on the server.');
		}
		$cleanup = function () use ($work) {
			foreach (glob($work . DIRECTORY_SEPARATOR . '*') ?: array() as $f) {
				@unlink($f);
			}
			@rmdir($work);
		};
		// makefont's Error() calls exit: clean up and still answer with JSON.
		$active = true;
		register_shutdown_function(function () use (&$active, $cleanup, $t) {
			if (!$active) {
				return;
			}
			$cleanup();
			$log = '';
			while (ob_get_level() > 0) {
				$log .= ob_get_clean();
			}
			echo json_encode(array('status' => '0', 'dataConfigAnswer' => $t('452-41', 'Invalid file: use a TrueType font (.ttf).') . ' ' . trim(strip_tags($log))));
		});
		$cwd = getcwd();
		chdir($work);
		ob_start();
		MakeFont($psName . '.ttf', 'cp1252', true, true);
		ob_end_clean();
		chdir($cwd);
		$active = false;

		$def = is_file($work . DIRECTORY_SEPARATOR . $psName . '.php') ? (string) file_get_contents($work . DIRECTORY_SEPARATOR . $psName . '.php') : '';
		$ok = $def !== '' && strpos($def, "\$name = '" . $psName . "';") !== false
			&& is_file($work . DIRECTORY_SEPARATOR . $psName . '.z')
			&& @copy($work . DIRECTORY_SEPARATOR . $psName . '.z', $destZ)
			&& @copy($work . DIRECTORY_SEPARATOR . $psName . '.php', $destPhp);
		$cleanup();
		if (!$ok) {
			@unlink($destZ);
			@unlink($destPhp);
			return $err('452-46', 'Could not save the font on the server.');
		}
		@chmod($destPhp, 0644);
		@chmod($destZ, 0644);
		clearstatcache();
		az_flyer_available_fonts($siteFontDir, true);
		return array('ok' => true, 'family' => $family, 'style' => $code, 'name' => $psName);
	}
}
