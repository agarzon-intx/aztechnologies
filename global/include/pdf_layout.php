<?php
/**
 * User-editable PDF layout for every FPDF document.
 *
 * Each drawing call (Cell, MultiCell, Write, Text, Image, Rect, Line) is identified by the
 * file:line that issued it, relative to the site root (e.g. "pdf/flyer_ci_page.php:42").
 * Overrides per element (they win over whatever the document code set, e.g. the flyer font/colors):
 *  - dx / dy   move, in document units
 *  - s         scale (legacy; text size / image or box size around its center)
 *  - font / fs / pt / color / al   text font family, style ('', B, I, BI), size in points, #RRGGBB, alignment (Cell)
 *  - w / h     cell width/height, image or box size
 *  - src       replacement image (imagenes/pdf_layout/...)
 *  - hide
 * They are stored per site in {site}/ini/pdf_layout.json and edited in Configuration → PDF layout.
 *
 * Request switches (used by the editor):
 *  - azLayoutDraft   JSON overrides used instead of the saved ones (preview before saving)
 *  - azLayoutCapture "1": instead of the PDF, output JSON with every drawn element and its box; "2": same plus the PDF (base64)
 */

if (!function_exists('az_pdf_layout_file')) {
	function az_pdf_layout_site_root() {
		$root = isset($_SERVER['DOCUMENT_ROOT']) ? realpath((string) $_SERVER['DOCUMENT_ROOT']) : false;
		return $root !== false ? $root : '';
	}

	function az_pdf_layout_file($siteRoot = null) {
		$siteRoot = $siteRoot === null ? az_pdf_layout_site_root() : rtrim((string) $siteRoot, '/\\');
		return $siteRoot === '' ? '' : $siteRoot . DIRECTORY_SEPARATOR . 'ini' . DIRECTORY_SEPARATOR . 'pdf_layout.json';
	}

	/** Folder (relative to the site root) of images uploaded to replace PDF images. */
	function az_pdf_layout_image_dir() {
		return 'imagenes/pdf_layout';
	}

	/** @return array<string, array> */
	function az_pdf_layout_load($siteRoot = null) {
		$file = az_pdf_layout_file($siteRoot);
		if ($file === '' || !is_readable($file)) {
			return array();
		}
		$data = json_decode((string) file_get_contents($file), true);
		return is_array($data) ? az_pdf_layout_sanitize($data) : array();
	}

	function az_pdf_layout_sanitize(array $data) {
		$out = array();
		foreach ($data as $key => $o) {
			if (!is_string($key) || !preg_match('/^[A-Za-z0-9_\/.\-]+\.php:\d+$/', $key) || !is_array($o)) {
				continue;
			}
			$e = array();
			if (isset($o['m']) && preg_match('/^[A-Za-z]{1,20}$/', (string) $o['m'])) {
				$e['m'] = (string) $o['m'];
			}
			if (isset($o['sig']) && is_string($o['sig'])) {
				$e['sig'] = substr($o['sig'], 0, 120);
			}
			foreach (array('dx', 'dy') as $k) {
				if (isset($o[$k]) && is_numeric($o[$k]) && abs((float) $o[$k]) <= 5000) {
					$e[$k] = round((float) $o[$k], 3);
				}
			}
			if (isset($o['s']) && is_numeric($o['s']) && (float) $o['s'] >= 0.1 && (float) $o['s'] <= 10) {
				$e['s'] = round((float) $o['s'], 3);
			}
			if (!empty($o['hide'])) {
				$e['hide'] = true;
			}
			if (isset($o['font']) && preg_match('/^[A-Za-z0-9_\-]{1,40}$/', (string) $o['font'])) {
				$e['font'] = (string) $o['font'];
			}
			if (isset($o['fs']) && in_array((string) $o['fs'], array('', 'B', 'I', 'BI'), true)) {
				$e['fs'] = (string) $o['fs'];
			}
			if (isset($o['pt']) && is_numeric($o['pt']) && (float) $o['pt'] >= 1 && (float) $o['pt'] <= 400) {
				$e['pt'] = round((float) $o['pt'], 2);
			}
			if (isset($o['color']) && preg_match('/^#[0-9A-Fa-f]{6}$/', (string) $o['color'])) {
				$e['color'] = strtoupper((string) $o['color']);
			}
			if (isset($o['al']) && in_array((string) $o['al'], array('L', 'C', 'R'), true)) {
				$e['al'] = (string) $o['al'];
			}
			foreach (array('w', 'h') as $k) {
				if (isset($o[$k]) && is_numeric($o[$k]) && (float) $o[$k] > 0 && (float) $o[$k] <= 5000) {
					$e[$k] = round((float) $o[$k], 3);
				}
			}
			if (isset($o['src']) && preg_match('#^imagenes/pdf_layout/[A-Za-z0-9_.\-]+\.(png|jpe?g|gif|webp)$#i', (string) $o['src'])) {
				$e['src'] = (string) $o['src'];
			}
			if (isset($o['label']) && is_string($o['label'])) {
				$e['label'] = mb_substr($o['label'], 0, 80);
			}
			$isNoop = empty($e['hide']) && empty($e['dx']) && empty($e['dy']) && (!isset($e['s']) || (float) $e['s'] === 1.0)
				&& !isset($e['font']) && !isset($e['fs']) && !isset($e['pt']) && !isset($e['color']) && !isset($e['al'])
				&& !isset($e['w']) && !isset($e['h']) && !isset($e['src']);
			if (!$isNoop) {
				$out[$key] = $e;
			}
		}
		return $out;
	}

	function az_pdf_layout_save(array $data, $siteRoot = null) {
		$file = az_pdf_layout_file($siteRoot);
		if ($file === '') {
			return false;
		}
		$dir = dirname($file);
		if (!is_dir($dir) && !@mkdir($dir, 0755, true)) {
			return false;
		}
		$json = json_encode(az_pdf_layout_sanitize($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		return @file_put_contents($file, $json, LOCK_EX) !== false;
	}

	/** Image signature: file name with numbers replaced, so per-team/per-player images still match. */
	function az_pdf_layout_image_sig($file) {
		$base = is_string($file) ? basename(str_replace('\\', '/', $file)) : '';
		$base = preg_replace('/\?.*$/', '', $base);
		return (string) preg_replace('/\d+/', '#', $base);
	}

	/** Team logos ("<Season_ID>-<TEAM_ID>.png") can be moved/resized but not replaced (one upload would replace every team's logo). */
	function az_pdf_layout_is_team_logo($file) {
		$base = is_string($file) ? basename(str_replace('\\', '/', preg_replace('/\?.*$/', '', $file))) : '';
		return (bool) preg_match('/^\d+-\d+\.(png|jpe?g|gif|webp)$/i', $base);
	}

	/** "#RRGGBB" from FPDF's color operator string ("0.000 g" or "r g b rg"). */
	function az_pdf_layout_color_hex($op) {
		$p = preg_split('/\s+/', trim((string) $op));
		if (count($p) >= 4) {
			$rgb = array((float) $p[0], (float) $p[1], (float) $p[2]);
		} elseif (count($p) >= 2) {
			$rgb = array((float) $p[0], (float) $p[0], (float) $p[0]);
		} else {
			return '#000000';
		}
		return sprintf('#%02X%02X%02X', round($rgb[0] * 255), round($rgb[1] * 255), round($rgb[2] * 255));
	}
}

if (!trait_exists('AzPdfLayoutTrait')) {
	trait AzPdfLayoutTrait {
		private static $azLayoutData = null;
		private static $azLayoutCapture = null;
		private $azLayoutElements = array();

		private static function azLayoutInit() {
			if (self::$azLayoutData !== null) {
				return;
			}
			$capture = isset($_REQUEST['azLayoutCapture']) ? (string) $_REQUEST['azLayoutCapture'] : '';
			self::$azLayoutCapture = ($capture === '1' || $capture === '2') ? (int) $capture : false;
			$data = null;
			if (isset($_POST['azLayoutDraft'])) {
				$draft = json_decode((string) $_POST['azLayoutDraft'], true);
				if (is_array($draft)) {
					$data = az_pdf_layout_sanitize($draft);
				}
			}
			self::$azLayoutData = $data !== null ? $data : az_pdf_layout_load();
		}

		private function azLayoutActive() {
			self::azLayoutInit();
			return self::$azLayoutCapture || count(self::$azLayoutData) > 0;
		}

		/** file:line of the drawing call in the document code (skips FPDF and generic PDF helpers). */
		private function azLayoutKey() {
			static $root = null;
			if ($root === null) {
				$root = az_pdf_layout_site_root();
			}
			foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 12) as $frame) {
				if (!isset($frame['file'])) {
					continue;
				}
				$file = $frame['file'];
				$base = basename($file);
				if ($base === 'fpdf.php' || $base === 'alphapdf.php' || $base === 'pdf_layout.php'
					|| $base === 'pdf_image_helpers.php' || $base === 'flyer_text_style.inc.php' || $base === 'pdf_image_normalize.php') {
					continue;
				}
				$real = realpath($file) ?: $file;
				$real = str_replace('\\', '/', $real);
				$rootN = str_replace('\\', '/', $root);
				if ($rootN !== '' && strpos($real, $rootN . '/') === 0) {
					$rel = substr($real, strlen($rootN) + 1);
				} elseif (($p = strpos($real, '/global/')) !== false) {
					$rel = substr($real, $p + 1);
				} else {
					$rel = $base;
				}
				return $rel . ':' . (int) $frame['line'];
			}
			return null;
		}

		/** Override of the current drawing call, with defaults filled in. */
		private function azLayoutFor($method, $sig = null) {
			$key = $this->azLayoutKey();
			$o = ($key !== null && isset(self::$azLayoutData[$key])) ? self::$azLayoutData[$key] : null;
			if ($o !== null && isset($o['m']) && $o['m'] !== $method) {
				$o = null;
			}
			if ($o !== null && $sig !== null && isset($o['sig']) && $o['sig'] !== '' && $o['sig'] !== $sig) {
				$o = null;
			}
			$o = is_array($o) ? $o : array();
			return array(
				'key' => $key,
				'dx' => isset($o['dx']) ? (float) $o['dx'] : 0.0,
				'dy' => isset($o['dy']) ? (float) $o['dy'] : 0.0,
				's' => isset($o['s']) ? (float) $o['s'] : 1.0,
				'hide' => !empty($o['hide']),
				'font' => isset($o['font']) ? (string) $o['font'] : null,
				'fs' => isset($o['fs']) ? (string) $o['fs'] : null,
				'pt' => isset($o['pt']) ? (float) $o['pt'] : null,
				'color' => isset($o['color']) ? (string) $o['color'] : null,
				'al' => isset($o['al']) ? (string) $o['al'] : null,
				'w' => isset($o['w']) ? (float) $o['w'] : null,
				'h' => isset($o['h']) ? (float) $o['h'] : null,
				'src' => isset($o['src']) ? (string) $o['src'] : null,
			);
		}

		/** Makes $family/$style usable (core or TrueType from the site/shared font folders); returns [family, style] or null. */
		private function azLayoutUseFont($family, $style) {
			if (!function_exists('az_flyer_available_fonts')) {
				$inc = __DIR__ . DIRECTORY_SEPARATOR . 'flyer_text_style.inc.php';
				if (is_readable($inc)) {
					require_once $inc;
				}
			}
			if (!function_exists('az_flyer_available_fonts')) {
				return null;
			}
			foreach (az_flyer_available_fonts($this->fontpath) as $name => $info) {
				if (strcasecmp($name, $family) !== 0 || empty($info['styles'])) {
					continue;
				}
				if (strcasecmp($info['type'], 'Core') === 0) {
					return array($name, $style);
				}
				$candidates = array($style, str_replace('I', '', $style), str_replace('B', '', $style), 'B', '');
				$pick = null;
				foreach ($candidates as $c) {
					if (isset($info['styles'][$c])) {
						$pick = $c;
						break;
					}
				}
				if ($pick === null) {
					$pick = (string) key($info['styles']);
				}
				$f = array('family' => $name, 'style' => $pick, 'file' => $info['styles'][$pick]['file'], 'dir' => $info['styles'][$pick]['dir']);
				if (!isset($this->fonts[strtolower($name) . $pick])) {
					if (!az_flyer_font_ensure_local($f, $this->fontpath)) {
						return null;
					}
					$this->AddFont($name, $pick, $f['file']);
				}
				return array($name, $pick);
			}
			return null;
		}

		/** Applies the text overrides (font, size, color); returns what is needed to restore the document state. */
		private function azLayoutTextBegin(array $o) {
			$saved = array(
				'family' => $this->FontFamily,
				'style' => $this->FontStyle,
				'size' => $this->FontSizePt,
				'color' => $this->TextColor,
				'flag' => $this->ColorFlag,
				'font' => false,
				'resized' => false,
			);
			$size = $o['pt'] !== null ? $o['pt'] : ($o['s'] !== 1.0 ? $this->FontSizePt * $o['s'] : $this->FontSizePt);
			if ($o['font'] !== null || $o['fs'] !== null) {
				$use = $this->azLayoutUseFont($o['font'] !== null ? $o['font'] : $this->FontFamily, $o['fs'] !== null ? $o['fs'] : $this->FontStyle);
				if ($use !== null) {
					$this->SetFont($use[0], $use[1] . ($this->underline ? 'U' : ''), $size);
					$saved['font'] = true;
				}
			}
			if (!$saved['font'] && $size != $this->FontSizePt) {
				$this->SetFontSize($size);
				$saved['resized'] = true;
			}
			if ($o['color'] !== null) {
				$this->SetTextColor(hexdec(substr($o['color'], 1, 2)), hexdec(substr($o['color'], 3, 2)), hexdec(substr($o['color'], 5, 2)));
			}
			return $saved;
		}

		private function azLayoutTextEnd(array $saved) {
			if ($saved['font']) {
				$this->SetFont($saved['family'], $saved['style'] . ($this->underline ? 'U' : ''), $saved['size']);
			} elseif ($saved['resized']) {
				$this->SetFontSize($saved['size']);
			}
			$this->TextColor = $saved['color'];
			$this->ColorFlag = $saved['flag'];
		}

		private function azLayoutRecord($key, $method, $x, $y, $w, $h, $text = '', $sig = '', $hidden = false, array $extra = array()) {
			if (!self::$azLayoutCapture || $key === null || count($this->azLayoutElements) >= 5000) {
				return;
			}
			$text = (string) $text;
			if ($text !== '' && !mb_check_encoding($text, 'UTF-8')) {
				$text = mb_convert_encoding($text, 'UTF-8', 'Windows-1252');
			}
			$this->azLayoutElements[] = array(
				'k' => $key,
				'm' => $method,
				'p' => (int) $this->page,
				'x' => round((float) $x, 2),
				'y' => round((float) $y, 2),
				'w' => round((float) $w, 2),
				'h' => round((float) $h, 2),
				't' => mb_substr(trim($text), 0, 60),
				'sig' => $sig,
				'hidden' => $hidden,
			) + $extra;
		}

		/** Font/color of the current state, for the editor. */
		private function azLayoutTextInfo() {
			return array(
				'f' => (string) $this->FontFamily,
				'fs' => str_replace('U', '', (string) $this->FontStyle),
				'pt' => round((float) $this->FontSizePt, 2),
				'c' => az_pdf_layout_color_hex($this->TextColor),
			);
		}

		function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='') {
			if (!$this->azLayoutActive()) {
				parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
				return;
			}
			$o = $this->azLayoutFor('Cell');
			$visible = (string) $txt !== '' || $border || $fill;
			if ($o['w'] !== null) {
				$w = $o['w'];
			}
			if ($o['h'] !== null) {
				$h = $o['h'];
			}
			if ($o['al'] !== null && (string) $txt !== '') {
				$align = $o['al'];
			}
			$this->x += $o['dx'];
			$this->y += $o['dy'];
			$saved = $visible ? $this->azLayoutTextBegin($o) : null;
			if ($visible) {
				$bw = ($w == 0) ? $this->w - $this->rMargin - $this->x : $w;
				$extra = (string) $txt !== '' ? $this->azLayoutTextInfo() + array('al' => $align === '' ? 'L' : strtoupper((string) $align)) : array();
				$this->azLayoutRecord($o['key'], 'Cell', $this->x, $this->y, $bw, $h, $txt, '', $o['hide'], $extra);
			}
			if ($o['hide']) {
				parent::Cell($w, $h, '', 0, $ln, '', false, '');
			} else {
				parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
			}
			if ($saved !== null) {
				$this->azLayoutTextEnd($saved);
			}
			if ($ln > 0) {
				$this->x -= ($ln == 1) ? 0 : $o['dx'];
			} else {
				$this->x -= $o['dx'];
			}
			$this->y -= $o['dy'];
		}

		function Text($x, $y, $txt) {
			if (!$this->azLayoutActive()) {
				parent::Text($x, $y, $txt);
				return;
			}
			$o = $this->azLayoutFor('Text');
			$x += $o['dx'];
			$y += $o['dy'];
			$saved = $this->azLayoutTextBegin($o);
			$this->azLayoutRecord($o['key'], 'Text', $x, $y - $this->FontSize, $this->GetStringWidth($txt), $this->FontSize * 1.2, $txt, '', $o['hide'], $this->azLayoutTextInfo());
			if (!$o['hide']) {
				parent::Text($x, $y, $txt);
			}
			$this->azLayoutTextEnd($saved);
		}

		/** Pixel size of a local image file, or null. */
		private static function azLayoutImageSize($file) {
			if (!is_string($file) || $file === '' || preg_match('#^[a-z]+://#i', $file) || !is_readable($file)) {
				return null;
			}
			$info = @getimagesize($file);
			return ($info && $info[0] > 0 && $info[1] > 0) ? array((float) $info[0], (float) $info[1]) : null;
		}

		function Image($file, $x=null, $y=null, $w=0, $h=0, $type='', $link='') {
			if (!$this->azLayoutActive()) {
				parent::Image($file, $x, $y, $w, $h, $type, $link);
				return;
			}
			$sig = az_pdf_layout_image_sig($file);
			$o = $this->azLayoutFor('Image', $sig);
			if ($x === null || $y === null) {
				if (!$o['hide']) {
					parent::Image($file, $x, $y, $w, $h, $type, $link);
				}
				return;
			}
			// Real box size (FPDF derives missing sides from the image ratio, 96 dpi when both are 0).
			$bw = (float) $w;
			$bh = (float) $h;
			$px = self::azLayoutImageSize($file);
			if (($bw <= 0 || $bh <= 0) && $px !== null) {
				if ($bw <= 0 && $bh <= 0) {
					$bw = $px[0] * 72 / 96 / $this->k;
					$bh = $px[1] * 72 / 96 / $this->k;
				} elseif ($bw <= 0) {
					$bw = $bh * $px[0] / $px[1];
				} else {
					$bh = $bw * $px[1] / $px[0];
				}
			}
			// Replacement image: fitted (keeping its proportions) and centered in the original box.
			$srcName = '';
			if ($o['src'] !== null && !az_pdf_layout_is_team_logo($file)) {
				$root = az_pdf_layout_site_root();
				$alt = $root !== '' ? $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $o['src']) : '';
				$altPx = $alt !== '' ? self::azLayoutImageSize($alt) : null;
				if ($altPx !== null) {
					if ($bw > 0 && $bh > 0) {
						$r = min($bw / $altPx[0], $bh / $altPx[1]);
						$x += ($bw - $altPx[0] * $r) / 2;
						$y += ($bh - $altPx[1] * $r) / 2;
						$bw = $altPx[0] * $r;
						$bh = $altPx[1] * $r;
					} else {
						$bw = $altPx[0] * 72 / 96 / $this->k;
						$bh = $altPx[1] * 72 / 96 / $this->k;
					}
					$file = $alt;
					$type = '';
					$w = $bw;
					$h = $bh;
					$srcName = basename($alt);
				}
			}
			$x += $o['dx'];
			$y += $o['dy'];
			if (($o['w'] !== null || $o['h'] !== null) && $bw > 0 && $bh > 0) {
				$nw = $o['w'] !== null ? $o['w'] : $o['h'] * $bw / $bh;
				$nh = $o['h'] !== null ? $o['h'] : $o['w'] * $bh / $bw;
				$bw = $w = $nw;
				$bh = $h = $nh;
			} elseif ($o['s'] !== 1.0 && $bw > 0 && $bh > 0) {
				$x += ($bw - $bw * $o['s']) / 2;
				$y += ($bh - $bh * $o['s']) / 2;
				$bw *= $o['s'];
				$bh *= $o['s'];
				$w = $bw;
				$h = $bh;
			}
			$this->azLayoutRecord($o['key'], 'Image', $x, $y, $bw, $bh, '', $sig, $o['hide'], array('src' => $srcName));
			if (!$o['hide']) {
				parent::Image($file, $x, $y, $w, $h, $type, $link);
			}
		}

		function Rect($x, $y, $w, $h, $style='') {
			if (!$this->azLayoutActive()) {
				parent::Rect($x, $y, $w, $h, $style);
				return;
			}
			$o = $this->azLayoutFor('Rect');
			$x += $o['dx'];
			$y += $o['dy'];
			if ($o['w'] !== null || $o['h'] !== null) {
				$w = $o['w'] !== null ? $o['w'] : $w;
				$h = $o['h'] !== null ? $o['h'] : $h;
			} elseif ($o['s'] !== 1.0) {
				$w *= $o['s'];
				$h *= $o['s'];
			}
			$this->azLayoutRecord($o['key'], 'Rect', $x, $y, $w, $h, '', '', $o['hide']);
			if (!$o['hide']) {
				parent::Rect($x, $y, $w, $h, $style);
			}
		}

		function Line($x1, $y1, $x2, $y2) {
			if (!$this->azLayoutActive()) {
				parent::Line($x1, $y1, $x2, $y2);
				return;
			}
			$o = $this->azLayoutFor('Line');
			$x1 += $o['dx'];
			$x2 += $o['dx'];
			$y1 += $o['dy'];
			$y2 += $o['dy'];
			$this->azLayoutRecord($o['key'], 'Line', min($x1, $x2), min($y1, $y2), max(abs($x2 - $x1), 0.5), max(abs($y2 - $y1), 0.5), '', '', $o['hide']);
			if (!$o['hide']) {
				parent::Line($x1, $y1, $x2, $y2);
			}
		}

		function Output($dest='', $name='', $isUTF8=false) {
			self::azLayoutInit();
			$destU = strtoupper((string) $dest);
			if (self::$azLayoutCapture && ($destU === '' || $destU === 'I' || $destU === 'D')) {
				if ($this->state < 3) {
					$this->Close();
				}
				$pages = array();
				foreach ($this->PageInfo as $n => $info) {
					$size = isset($info['size']) ? $info['size'] : array($this->DefPageSize[0] * $this->k, $this->DefPageSize[1] * $this->k);
					$pages[$n] = array('w' => round($size[0] / $this->k, 2), 'h' => round($size[1] / $this->k, 2));
				}
				$out = array(
					'k' => $this->k,
					'pages' => $pages,
					'elements' => $this->azLayoutElements,
				);
				if (self::$azLayoutCapture === 2) {
					$out['pdf'] = base64_encode(parent::Output('S'));
				}
				while (ob_get_level() > 0) {
					ob_end_clean();
				}
				header('Content-Type: application/json; charset=UTF-8');
				header('Cache-Control: no-store');
				header('Content-Disposition: inline');
				echo json_encode($out, JSON_INVALID_UTF8_SUBSTITUTE);
				return '';
			}
			return parent::Output($dest, $name, $isUTF8);
		}
	}
}
