<?php
/**
 * Configuration → Images: flyer letter colors and font sizes.
 */
if (!function_exists('az_flyer_normalize_hex_color')) {
	function az_flyer_normalize_hex_color($value, $fallback = '#0098AF') {
		$hex = strtoupper(trim((string) $value));
		if ($hex !== '' && $hex[0] !== '#') {
			$hex = '#' . $hex;
		}
		if (!preg_match('/^#[0-9A-F]{6}$/', $hex)) {
			$fallback = strtoupper(trim((string) $fallback));
			if ($fallback !== '' && $fallback[0] !== '#') {
				$fallback = '#' . $fallback;
			}
			return preg_match('/^#[0-9A-F]{6}$/', $fallback) ? $fallback : '#0098AF';
		}
		return $hex;
	}
}

if (!function_exists('az_flyer_normalize_font_size')) {
	function az_flyer_normalize_font_size($value, $fallback) {
		$n = (int) $value;
		if ($n < 8 || $n > 150) {
			return (int) $fallback;
		}
		return $n;
	}
}

if (!function_exists('az_flyer_hex_to_rgb')) {
	function az_flyer_hex_to_rgb($hex) {
		$hex = az_flyer_normalize_hex_color($hex);
		return array(
			hexdec(substr($hex, 1, 2)),
			hexdec(substr($hex, 3, 2)),
			hexdec(substr($hex, 5, 2)),
		);
	}
}

if (!function_exists('az_flyer_pdf_set_text_color')) {
	function az_flyer_pdf_set_text_color($pdf, $rgbOrHex) {
		if (is_string($rgbOrHex)) {
			$rgb = az_flyer_hex_to_rgb($rgbOrHex);
		} elseif (is_array($rgbOrHex) && count($rgbOrHex) >= 3) {
			$rgb = array((int) $rgbOrHex[0], (int) $rgbOrHex[1], (int) $rgbOrHex[2]);
		} else {
			$rgb = array(0, 152, 175);
		}
		$pdf->SetTextColor($rgb[0], $rgb[1], $rgb[2]);
	}
}

if (!function_exists('az_flyer_pdf_fonts')) {
	/**
	 * Font definitions in an FPDF font folder, grouped by family.
	 * Style codes follow FPDF: '' regular, 'B', 'I', 'BI'.
	 *
	 * @return array<string, array{type:string, symbolic:bool, styles:array<string, array{name:string, file:string}>}>
	 */
	function az_flyer_pdf_fonts($fontDir, $refresh = false) {
		static $cache = array();
		$fontDir = rtrim((string) $fontDir, '/\\');
		if (!$refresh && isset($cache[$fontDir])) {
			return $cache[$fontDir];
		}
		$fonts = array();
		if ($fontDir !== '' && is_dir($fontDir)) {
			foreach (glob($fontDir . '/*.php') ?: array() as $fontFile) {
				$src = (string) @file_get_contents($fontFile, false, null, 0, 4096);
				if (!preg_match('/\$name\s*=\s*\'([^\']+)\'/', $src, $mName)) {
					continue;
				}
				$fontType = preg_match('/\$type\s*=\s*\'([^\']+)\'/', $src, $mType) ? $mType[1] : '';
				$parts = explode('-', $mName[1], 2);
				$family = $parts[0];
				$suffix = strtolower(isset($parts[1]) ? $parts[1] : '');
				$code = (preg_match('/bold|black|heavy/', $suffix) ? 'B' : '') . (preg_match('/italic|oblique/', $suffix) ? 'I' : '');
				if (!isset($fonts[$family])) {
					$fonts[$family] = array(
						'type' => $fontType,
						'symbolic' => in_array(strtolower($family), array('symbol', 'zapfdingbats'), true),
						'styles' => array(),
					);
				}
				if (!isset($fonts[$family]['styles'][$code])) {
					$fonts[$family]['styles'][$code] = array(
						'name' => isset($parts[1]) ? $parts[1] : 'Regular',
						'file' => basename($fontFile),
						'dir' => $fontDir,
					);
				}
			}
			ksort($fonts, SORT_NATURAL | SORT_FLAG_CASE);
			$order = array('' => 0, 'B' => 1, 'I' => 2, 'BI' => 3);
			foreach ($fonts as &$f) {
				uksort($f['styles'], function ($a, $b) use ($order) {
					return $order[$a] - $order[$b];
				});
			}
			unset($f);
		}
		$cache[$fontDir] = $fonts;
		return $fonts;
	}
}

if (!function_exists('az_flyer_global_font_dir')) {
	/** Shared font folder: fonts added from Configuration → Images are available to every site. */
	function az_flyer_global_font_dir() {
		return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'pdf_fonts';
	}
}

if (!function_exists('az_flyer_available_fonts')) {
	/**
	 * Fonts of the site FPDF font folder plus the shared global folder (site copy wins).
	 * Each style entry carries the folder it lives in ('dir').
	 */
	function az_flyer_available_fonts($siteFontDir, $refresh = false) {
		$fonts = az_flyer_pdf_fonts($siteFontDir, $refresh);
		foreach (az_flyer_pdf_fonts(az_flyer_global_font_dir(), $refresh) as $family => $info) {
			$key = null;
			foreach ($fonts as $existing => $unused) {
				if (strcasecmp($existing, $family) === 0) {
					$key = $existing;
					break;
				}
			}
			if ($key === null) {
				$fonts[$family] = $info;
				continue;
			}
			foreach ($info['styles'] as $code => $styleInfo) {
				if (!isset($fonts[$key]['styles'][$code])) {
					$fonts[$key]['styles'][$code] = $styleInfo;
				}
			}
			$order = array('' => 0, 'B' => 1, 'I' => 2, 'BI' => 3);
			uksort($fonts[$key]['styles'], function ($a, $b) use ($order) {
				return $order[$a] - $order[$b];
			});
		}
		ksort($fonts, SORT_NATURAL | SORT_FLAG_CASE);
		return $fonts;
	}
}

if (!function_exists('az_flyer_font_dir')) {
	function az_flyer_font_dir($Config = null) {
		if (defined('FPDF_FONTPATH')) {
			return rtrim(FPDF_FONTPATH, '/\\');
		}
		if (class_exists('FPDF', false)) {
			$ref = new ReflectionClass('FPDF');
			return dirname($ref->getFileName()) . DIRECTORY_SEPARATOR . 'font';
		}
		if (is_object($Config) && method_exists($Config, 'getPath')) {
			$root = rtrim((string) $Config->getPath(), '/\\');
			if ($root !== '') {
				return $root . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . 'font';
			}
		}
		return '';
	}
}

if (!function_exists('az_flyer_font_choice')) {
	/**
	 * Configured flyer font, validated against the font folder; falls back to Coluna Bold.
	 *
	 * @return array{family:string, style:string, file:?string, dir:string}
	 */
	function az_flyer_font_choice($Config = null, $fontDir = null) {
		if ($Config === null && isset($GLOBALS['Config'])) {
			$Config = $GLOBALS['Config'];
		}
		$family = (is_object($Config) && isset($Config->flyerFontFamily) && (string) $Config->flyerFontFamily !== '')
			? (string) $Config->flyerFontFamily : 'Coluna';
		$style = (is_object($Config) && isset($Config->flyerFontStyle)) ? strtoupper((string) $Config->flyerFontStyle) : 'B';
		$fonts = az_flyer_available_fonts($fontDir === null ? az_flyer_font_dir($Config) : $fontDir);

		$pick = function ($fam, $sty) use ($fonts) {
			foreach ($fonts as $name => $info) {
				if (strcasecmp($name, $fam) !== 0 || $info['symbolic'] || empty($info['styles'])) {
					continue;
				}
				if (!isset($info['styles'][$sty])) {
					$sty = isset($info['styles']['B']) ? 'B' : (string) key($info['styles']);
				}
				return array(
					'family' => $name,
					'style' => $sty,
					'file' => strcasecmp($info['type'], 'Core') === 0 ? null : $info['styles'][$sty]['file'],
					'dir' => $info['styles'][$sty]['dir'],
				);
			}
			return null;
		};
		$choice = $pick($family, $style);
		if ($choice === null) {
			$choice = $pick('Coluna', 'B');
		}
		if ($choice === null) {
			$choice = array('family' => 'Helvetica', 'style' => 'B', 'file' => null, 'dir' => '');
		}
		return $choice;
	}
}

if (!function_exists('az_flyer_font_ensure_local')) {
	/**
	 * FPDF only loads fonts from its own font folder (and re-reads the .z on Output),
	 * so a shared font is copied there on first use.
	 */
	function az_flyer_font_ensure_local(array $f, $pdfFontDir) {
		$pdfFontDir = rtrim((string) $pdfFontDir, '/\\');
		$srcDir = rtrim((string) $f['dir'], '/\\');
		if ($f['file'] === null || $pdfFontDir === '' || $srcDir === '' || realpath($srcDir) === realpath($pdfFontDir)) {
			return true;
		}
		$def = (string) @file_get_contents($srcDir . DIRECTORY_SEPARATOR . $f['file']);
		if (!preg_match('/\$file\s*=\s*\'([^\']+)\'/', $def, $m)) {
			return false;
		}
		foreach (array(basename($m[1]), $f['file']) as $name) {
			$src = $srcDir . DIRECTORY_SEPARATOR . $name;
			$dst = $pdfFontDir . DIRECTORY_SEPARATOR . $name;
			if (is_file($dst) && filesize($dst) === filesize($src)) {
				continue;
			}
			if (!is_file($src) || !@copy($src, $dst)) {
				return false;
			}
			@chmod($dst, 0644);
		}
		return true;
	}
}

if (!function_exists('az_flyer_pdf_set_font')) {
	function az_flyer_pdf_set_font($pdf, $size, $Config = null) {
		$f = az_flyer_font_choice($Config);
		if ($f['file'] !== null && !az_flyer_font_ensure_local($f, az_flyer_font_dir($Config))) {
			$f = array('family' => 'Coluna', 'style' => 'B', 'file' => 'Coluna.php');
		}
		if ($f['file'] !== null) {
			$pdf->AddFont($f['family'], $f['style'], $f['file']);
		}
		$pdf->SetFont($f['family'], $f['style'], $size);
	}
}

if (!function_exists('az_flyer_font_web_bytes')) {
	/**
	 * Makes an FPDF-embedded TrueType font loadable by browsers (@font-face).
	 * FPDF subsets drop the OS/2 table, which browser font sanitizers require.
	 */
	function az_flyer_font_web_bytes($bin, $bold = true) {
		$bin = (string) $bin;
		if (strlen($bin) < 12 || substr($bin, 0, 4) !== "\x00\x01\x00\x00") {
			return $bin;
		}
		$numTables = unpack('n', substr($bin, 4, 2))[1];
		$tables = array();
		for ($i = 0; $i < $numTables; $i++) {
			$rec = substr($bin, 12 + 16 * $i, 16);
			if (strlen($rec) < 16) {
				return $bin;
			}
			$t = unpack('Nsum/Noff/Nlen', substr($rec, 4));
			$tables[substr($rec, 0, 4)] = substr($bin, $t['off'], $t['len']);
		}
		if (isset($tables['OS/2']) || !isset($tables['head'], $tables['hhea'], $tables['hmtx'])) {
			return $bin;
		}
		$s16 = function ($data, $off) {
			$v = unpack('n', substr($data, $off, 2))[1];
			return $v >= 0x8000 ? $v - 0x10000 : $v;
		};
		$head = $tables['head'];
		$hhea = $tables['hhea'];
		$upm = unpack('n', substr($head, 18, 2))[1] ?: 1000;
		$yMin = $s16($head, 38);
		$yMax = $s16($head, 42);
		$ascender = $s16($hhea, 4);
		$descender = $s16($hhea, 6);
		$lineGap = $s16($hhea, 8);
		$numHMetrics = unpack('n', substr($hhea, 34, 2))[1];
		$sum = 0;
		$cnt = 0;
		for ($i = 0; $i < $numHMetrics && 4 * $i + 2 <= strlen($tables['hmtx']); $i++) {
			$adv = unpack('n', substr($tables['hmtx'], 4 * $i, 2))[1];
			if ($adv > 0) {
				$sum += $adv;
				$cnt++;
			}
		}
		$avg = $cnt ? (int) round($sum / $cnt) : (int) round($upm / 2);
		$sc = function ($f) use ($upm) {
			return (int) round($upm * $f);
		};
		$os2 = pack('nnnnn', 4, $avg, $bold ? 700 : 400, 5, 0)
			. pack('n8', $sc(0.65), $sc(0.6), 0, $sc(0.075), $sc(0.65), $sc(0.6), 0, $sc(0.35))
			. pack('nn', $sc(0.05), $sc(0.3))
			. pack('n', 0)
			. str_repeat("\x00", 10)
			. pack('NNNN', 3, 0, 0, 0)
			. 'NONE'
			. pack('n', $bold ? 0x0020 : 0x0040)
			. pack('nn', 0x20, 0xFFFF)
			. pack('nnn', $ascender & 0xFFFF, $descender & 0xFFFF, $lineGap & 0xFFFF)
			. pack('nn', max(0, $yMax), max(0, -$yMin))
			. pack('NN', 1, 0)
			. pack('nnnnn', $sc(0.5), $sc(0.7), 0, 0x20, 1);
		$tables['OS/2'] = $os2;
		$tables['head'] = substr($head, 0, 8) . "\x00\x00\x00\x00" . substr($head, 12);
		ksort($tables, SORT_STRING);

		$checksum = function ($data) {
			$data .= str_repeat("\x00", (4 - strlen($data) % 4) % 4);
			$s = 0;
			foreach (unpack('N*', $data) as $w) {
				$s = ($s + $w) & 0xFFFFFFFF;
			}
			return $s;
		};
		$n = count($tables);
		$entrySelector = (int) floor(log($n, 2));
		$searchRange = (1 << $entrySelector) * 16;
		$dir = pack('Nnnnn', 0x00010000, $n, $searchRange, $entrySelector, $n * 16 - $searchRange);
		$body = '';
		$offset = 12 + 16 * $n;
		$headOffset = 0;
		foreach ($tables as $tag => $data) {
			if ($tag === 'head') {
				$headOffset = $offset;
			}
			$dir .= $tag . pack('NNN', $checksum($data), $offset, strlen($data));
			$padded = $data . str_repeat("\x00", (4 - strlen($data) % 4) % 4);
			$body .= $padded;
			$offset += strlen($padded);
		}
		$font = $dir . $body;
		$adjust = (0xB1B0AFBA - $checksum($font)) & 0xFFFFFFFF;
		return substr($font, 0, $headOffset + 8) . pack('N', $adjust) . substr($font, $headOffset + 12);
	}
}

if (!function_exists('az_flyer_font_web_assets')) {
	/**
	 * Browser assets for the flyer font preview: CSS font-family per selectable family
	 * and @font-face rules (data URIs) for embedded TrueType fonts.
	 *
	 * @return array{css: array<string, string>, faces: string}
	 */
	function az_flyer_font_web_assets($fontDir, $onlyFamilies = null) {
		$coreFontCss = array(
			'courier' => '"Courier New", Courier, monospace',
			'helvetica' => 'Helvetica, Arial, sans-serif',
			'times' => '"Times New Roman", Times, serif',
		);
		$css = array();
		$faces = '';
		foreach (az_flyer_available_fonts($fontDir) as $family => $info) {
			if ($info['symbolic'] || empty($info['styles'])) {
				continue;
			}
			if (is_array($onlyFamilies) && !in_array($family, $onlyFamilies, true)) {
				continue;
			}
			$key = strtolower($family);
			if (strcasecmp($info['type'], 'Core') === 0) {
				$css[$family] = isset($coreFontCss[$key]) ? $coreFontCss[$key] : 'sans-serif';
				continue;
			}
			$cssFamily = 'azflyer-' . preg_replace('/[^a-z0-9_-]/', '', $key);
			$css[$family] = '"' . $cssFamily . '", sans-serif';
			foreach ($info['styles'] as $code => $styleInfo) {
				$styleDir = $styleInfo['dir'];
				$def = (string) @file_get_contents($styleDir . DIRECTORY_SEPARATOR . $styleInfo['file'], false, null, 0, 65536);
				if (!preg_match('/\$file\s*=\s*\'([^\']+)\'/', $def, $mFile)) {
					continue;
				}
				$fontPath = $styleDir . DIRECTORY_SEPARATOR . basename($mFile[1]);
				if (!is_readable($fontPath) || filesize($fontPath) > 2097152) {
					continue;
				}
				$bin = (string) file_get_contents($fontPath);
				if (preg_match('/\.z$/i', $fontPath)) {
					$bin = (string) @gzuncompress($bin);
				}
				$magic = substr($bin, 0, 4);
				if ($magic !== "\x00\x01\x00\x00" && $magic !== 'OTTO' && $magic !== 'true') {
					continue;
				}
				$bin = az_flyer_font_web_bytes($bin, strpos($code, 'B') !== false);
				$isOtf = $magic === 'OTTO';
				$faces .= '@font-face{font-family:"' . $cssFamily . '";src:url(data:' . ($isOtf ? 'font/otf' : 'font/ttf') . ';base64,' . base64_encode($bin) . ') format("' . ($isOtf ? 'opentype' : 'truetype') . '");'
					. 'font-weight:' . (strpos($code, 'B') !== false ? '700' : '400') . ';font-style:' . (strpos($code, 'I') !== false ? 'italic' : 'normal') . ';}';
			}
		}
		return array('css' => $css, 'faces' => $faces);
	}
}

if (!function_exists('az_flyer_text_style')) {
	/**
	 * @return array{color1:array,color2:array,color1Hex:string,color2Hex:string,week:int,category:int,date:int,hour:int,field:int}
	 */
	function az_flyer_text_style($Config = null) {
		$color1Hex = '#0098AF';
		$color2Hex = '#FFFFFF';
		$week = 75;
		$category = 60;
		$date = 35;
		$hour = 35;
		$field = 35;
		if ($Config === null && isset($GLOBALS['Config'])) {
			$Config = $GLOBALS['Config'];
		}
		if (is_object($Config)) {
			if (isset($Config->flyerTextColor1) && (string) $Config->flyerTextColor1 !== '') {
				$color1Hex = az_flyer_normalize_hex_color($Config->flyerTextColor1, $color1Hex);
			}
			if (isset($Config->flyerTextColor2) && (string) $Config->flyerTextColor2 !== '') {
				$color2Hex = az_flyer_normalize_hex_color($Config->flyerTextColor2, $color2Hex);
			}
			if (isset($Config->flyerFontWeek)) {
				$week = az_flyer_normalize_font_size($Config->flyerFontWeek, $week);
			}
			if (isset($Config->flyerFontCategory)) {
				$category = az_flyer_normalize_font_size($Config->flyerFontCategory, $category);
			}
			if (isset($Config->flyerFontDate)) {
				$date = az_flyer_normalize_font_size($Config->flyerFontDate, $date);
			}
			if (isset($Config->flyerFontHour)) {
				$hour = az_flyer_normalize_font_size($Config->flyerFontHour, $hour);
			}
			if (isset($Config->flyerFontField)) {
				$field = az_flyer_normalize_font_size($Config->flyerFontField, $field);
			}
		}
		return array(
			'color1Hex' => $color1Hex,
			'color2Hex' => $color2Hex,
			'color1' => az_flyer_hex_to_rgb($color1Hex),
			'color2' => az_flyer_hex_to_rgb($color2Hex),
			'week' => $week,
			'category' => $category,
			'date' => $date,
			'hour' => $hour,
			'field' => $field,
		);
	}
}
