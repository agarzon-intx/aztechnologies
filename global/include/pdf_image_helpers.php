<?php
/**
 * Local filesystem image loading for FPDF scripts (no HTTP self-requests).
 */
if (!function_exists('az_pdf_site_root')) {

	function az_pdf_site_root($Config) {
		return rtrim((string) $Config->getPath(), '/\\');
	}

	/** Create AlphaPDF after require alphapdf.php; fails fast with a clear message. */
	function az_pdf_create_alphapdf($orientation = 'P', $unit = 'mm', $size = 'Letter') {
		if (!class_exists('AlphaPDF', false)) {
			throw new RuntimeException('AlphaPDF is not loaded. Require alphapdf.php before generating PDFs.');
		}
		return new AlphaPDF($orientation, $unit, $size);
	}

	function az_pdf_require_login($fgmembersite, $page = 'registrosEquipo.php') {
		if ($fgmembersite->CheckLogin($page) === false) {
			http_response_code(401);
			header('Content-Type: text/plain; charset=UTF-8');
			exit('Session expired or not logged in.');
		}
	}

	function az_pdf_path_readable($siteRoot, $relativePath) {
		if ($siteRoot === '') {
			return null;
		}
		$relativePath = ltrim(str_replace('\\', '/', (string) $relativePath), '/');
		$full = $siteRoot . '/' . $relativePath;
		if (is_readable($full)) {
			return $full;
		}
		// Extension case variants (e.g. .PNG on Linux)
		if (preg_match('/\.([a-zA-Z0-9]+)$/', $relativePath, $m)) {
			$base = substr($relativePath, 0, -strlen($m[0]));
			foreach (array(strtolower($m[1]), strtoupper($m[1])) as $ext) {
				$try = $siteRoot . '/' . $base . '.' . $ext;
				if (is_readable($try)) {
					return $try;
				}
			}
		}
		return null;
	}

	function az_pdf_resolve_extensions($siteRoot, $pathWithoutExt, array $exts = array('png', 'jpg', 'jpeg', 'gif')) {
		foreach ($exts as $ext) {
			$path = az_pdf_path_readable($siteRoot, $pathWithoutExt . '.' . $ext);
			if ($path !== null) {
				return $path;
			}
		}
		return null;
	}

	function az_pdf_team_logo_path($siteRoot, $torneoId, $equipoId) {
		return az_pdf_resolve_extensions($siteRoot, 'imagenes/Original/' . $torneoId . '-' . $equipoId);
	}

	function az_pdf_fpdf_type($path) {
		$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
		if ($ext === 'jpg') {
			return 'JPEG';
		}
		return strtoupper($ext);
	}

	function az_pdf_image($pdf, $path, $x, $y, $w = 0, $h = 0, $type = '') {
		if ($path === null || !is_readable($path)) {
			return false;
		}
		if ($type === '') {
			$type = az_pdf_fpdf_type($path);
		}
		try {
			$pdf->Image($path, $x, $y, $w, $h, $type);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	/** Relative path under site root; skips load if file does not exist. */
	function az_pdf_image_file($pdf, $siteRoot, $relativePath, $x, $y, $w = 0, $h = 0) {
		if ($siteRoot === '' || $relativePath === null || $relativePath === '') {
			return false;
		}
		$relativePath = ltrim(str_replace('\\', '/', (string) $relativePath), '/');
		if (preg_match('/\.(png|jpe?g|gif)$/i', $relativePath)) {
			return az_pdf_image($pdf, az_pdf_path_readable($siteRoot, $relativePath), $x, $y, $w, $h);
		}
		return az_pdf_image($pdf, az_pdf_resolve_extensions($siteRoot, $relativePath), $x, $y, $w, $h);
	}

	function az_pdf_player_photo_path($Config, $schema, $jugadorId, $column = 'Foto') {
		static $cache = array();
		$column = preg_replace('/[^A-Za-z0-9_]/', '', $column);
		$jugadorId = (int) $jugadorId;
		$key = $schema . ':' . $jugadorId . ':' . $column;
		if (array_key_exists($key, $cache)) {
			return $cache[$key];
		}
		$cache[$key] = null;
		$sql = "SELECT $column FROM $schema.Jugadores WHERE Jugador_ID = $jugadorId AND OCTET_LENGTH($column) > 100";
		$result = $Config->query($sql);
		if (!$result || $result->num_rows === 0) {
			return null;
		}
		$row = $result->fetch_assoc();
		$bytes = $row[$column];
		if ($bytes === null || $bytes === '') {
			return null;
		}
		$ext = 'png';
		if (strncmp($bytes, "\xFF\xD8", 2) === 0) {
			$ext = 'jpg';
		} elseif (strncmp($bytes, 'GIF8', 4) === 0) {
			$ext = 'gif';
		}
		$path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'az-pdf-' . md5($key) . '.' . $ext;
		if (file_put_contents($path, $bytes) === false) {
			return null;
		}
		$cache[$key] = $path;
		return $path;
	}

	function az_pdf_player_photo($pdf, $Config, $schema, $jugadorId, $column, $x, $y, $w, $h) {
		return az_pdf_image($pdf, az_pdf_player_photo_path($Config, $schema, $jugadorId, $column), $x, $y, $w, $h);
	}

	function az_pdf_qrcode_path($qrMsg) {
		static $cache = array();
		$key = md5((string) $qrMsg);
		if (array_key_exists($key, $cache)) {
			return $cache[$key];
		}
		$cache[$key] = null;
		if (!class_exists('QRcode', false)) {
			$candidates = array(
				__DIR__ . DIRECTORY_SEPARATOR . 'qrcode' . DIRECTORY_SEPARATOR . 'qrcode.class.php',
				dirname(__DIR__) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'qrcode' . DIRECTORY_SEPARATOR . 'qrcode.class.php',
			);
			$loaded = false;
			foreach ($candidates as $qrcodeClass) {
				if (is_readable($qrcodeClass)) {
					require_once $qrcodeClass;
					$loaded = true;
					break;
				}
			}
			if (!$loaded || !class_exists('QRcode', false)) {
				return null;
			}
		}
		if (!function_exists('imagecreatetruecolor') || !function_exists('imagepng')) {
			return null;
		}
		$path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'az-qr-' . $key . '.png';
		// Write to a file — displayPNG() calls header() when $filename is null, which
		// corrupts output (and our PNG) when a PDF is already being streamed.
		try {
			$qrcode = new QRcode(az_utf8_encode((string) $qrMsg), 'L');
			$qrcode->disableBorder();
			$qrcode->displayPNG(200, array(255, 255, 255), array(0, 0, 0), $path, 0);
		} catch (Throwable $e) {
			return null;
		}
		if (!is_readable($path) || filesize($path) < 32) {
			return null;
		}
		$cache[$key] = $path;
		return $path;
	}

	function az_pdf_qrcode($pdf, $fgmembersite, $jugadorId, $x, $y, $w, $h) {
		$site = '';
		if (is_object($fgmembersite) && method_exists($fgmembersite, 'getSitename')) {
			$site = rtrim((string) $fgmembersite->getSitename(), '/');
		}
		$msg = $site . '/ajax/QR.php?Jugador_ID=' . (int) $jugadorId;
		$path = az_pdf_qrcode_path($msg);
		if ($path === null) {
			return false;
		}
		if (method_exists($pdf, 'SetFillColor') && method_exists($pdf, 'Rect')) {
			$pdf->SetFillColor(255, 255, 255);
			$pdf->Rect($x - 0.5, $y - 0.5, $w + 1, $h + 1, 'F');
		}
		if (method_exists($pdf, 'SetAlpha')) {
			$pdf->SetAlpha(1);
		}
		return az_pdf_image($pdf, $path, $x, $y, $w, $h);
	}

}
