<?php
/**
 * Image normalization for FPDF::Image() — every PDF image passes through az_pdf_normalize_image().
 *
 * The extension (or the type a caller passes) is not trusted: the real format is read from the file
 * bytes. JPEG, PNG and GIF go to FPDF as they are; WebP, BMP and PNGs FPDF cannot parse (16-bit,
 * interlaced) are converted with GD to a cached temporary PNG. A missing file is looked up with the
 * other common extensions, and URLs that point at this site are read from disk instead of over HTTP.
 */
if (!function_exists('az_pdf_normalize_image')) {

	/**
	 * @return array|null array('file' => path FPDF can read, 'type' => 'jpg'|'png'|'gif'), or null when unusable
	 */
	function az_pdf_normalize_image($file) {
		static $cache = array();
		$file = (string) $file;
		if ($file === '') {
			return null;
		}
		if (array_key_exists($file, $cache)) {
			return $cache[$file];
		}
		$cache[$file] = null;

		$path = az_pdf_image_locate($file);
		if ($path === null) {
			error_log('PDF image not found: ' . $file);
			return null;
		}

		$format = az_pdf_image_detect_format($path);
		if ($format === 'jpg' || $format === 'gif') {
			return $cache[$file] = array('file' => $path, 'type' => $format);
		}
		if ($format === 'png' && az_pdf_image_png_is_fpdf_safe($path)) {
			return $cache[$file] = array('file' => $path, 'type' => 'png');
		}

		$converted = az_pdf_image_convert_to_png($path, $format);
		if ($converted === null) {
			error_log('PDF image format not supported (' . ($format === null ? 'unknown' : $format) . '): ' . $file);
			return null;
		}
		return $cache[$file] = array('file' => $converted, 'type' => 'png');
	}

	/** Readable local path for a file or URL, trying the other common extensions when it is missing. */
	function az_pdf_image_locate($file) {
		if (preg_match('#^https?://#i', $file)) {
			$local = az_pdf_image_local_path_for_url($file);
			if ($local !== null) {
				$file = $local;
			} else {
				return az_pdf_image_download($file);
			}
		}
		if (is_file($file) && is_readable($file)) {
			return $file;
		}
		$base = preg_replace('/\.[A-Za-z0-9]{2,5}$/', '', $file);
		foreach (array('png', 'jpg', 'jpeg', 'gif', 'webp', 'bmp') as $ext) {
			foreach (array($ext, strtoupper($ext)) as $try) {
				$candidate = $base . '.' . $try;
				if (is_file($candidate) && is_readable($candidate)) {
					return $candidate;
				}
			}
		}
		return null;
	}

	/** URL on this same site → file under DOCUMENT_ROOT (avoids an HTTP request to ourselves). */
	function az_pdf_image_local_path_for_url($url) {
		$parts = parse_url($url);
		if (empty($parts['host']) || empty($parts['path']) || empty($_SERVER['DOCUMENT_ROOT'])) {
			return null;
		}
		$hosts = array();
		foreach (array('HTTP_HOST', 'SERVER_NAME') as $key) {
			if (!empty($_SERVER[$key])) {
				$hosts[] = strtolower(preg_replace('/:\d+$/', '', (string) $_SERVER[$key]));
			}
		}
		if (!in_array(strtolower($parts['host']), $hosts, true)) {
			return null;
		}
		$relative = rawurldecode($parts['path']);
		if (strpos($relative, '..') !== false) {
			return null;
		}
		return rtrim((string) $_SERVER['DOCUMENT_ROOT'], '/\\') . '/' . ltrim($relative, '/');
	}

	function az_pdf_image_download($url) {
		$path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'az-pdf-img-' . md5($url);
		if (is_file($path) && filesize($path) > 0 && time() - filemtime($path) < 300) {
			return $path;
		}
		$context = stream_context_create(array(
			'http' => array('timeout' => 10, 'follow_location' => 1),
			'ssl' => array('verify_peer' => false, 'verify_peer_name' => false),
		));
		$data = @file_get_contents($url, false, $context);
		if ($data === false || $data === '' || @file_put_contents($path, $data) === false) {
			error_log('PDF image download failed: ' . $url);
			return null;
		}
		return $path;
	}

	/** Real format from the file signature: jpg, png, gif, webp, bmp, or another GD-readable type name. */
	function az_pdf_image_detect_format($path) {
		$head = (string) @file_get_contents($path, false, null, 0, 16);
		if (strncmp($head, "\xFF\xD8\xFF", 3) === 0) {
			return 'jpg';
		}
		if (strncmp($head, "\x89PNG\r\n\x1A\n", 8) === 0) {
			return 'png';
		}
		if (strncmp($head, 'GIF87a', 6) === 0 || strncmp($head, 'GIF89a', 6) === 0) {
			return 'gif';
		}
		if (strncmp($head, 'RIFF', 4) === 0 && substr($head, 8, 4) === 'WEBP') {
			return 'webp';
		}
		if (strncmp($head, 'BM', 2) === 0) {
			return 'bmp';
		}
		$info = @getimagesize($path);
		if ($info && isset($info[2])) {
			$ext = image_type_to_extension($info[2], false);
			return $ext === 'jpeg' ? 'jpg' : $ext;
		}
		return null;
	}

	/** FPDF reads 8-bit, non-interlaced PNGs only. */
	function az_pdf_image_png_is_fpdf_safe($path) {
		$ihdr = (string) @file_get_contents($path, false, null, 0, 29);
		if (strlen($ihdr) < 29 || substr($ihdr, 12, 4) !== 'IHDR') {
			return false;
		}
		return ord($ihdr[24]) <= 8 && ord($ihdr[28]) === 0;
	}

	function az_pdf_image_convert_to_png($path, $format) {
		if (!function_exists('imagepng')) {
			return null;
		}
		$target = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'az-pdf-img-' . md5($path . '|' . @filemtime($path) . '|' . @filesize($path)) . '.png';
		if (is_file($target) && filesize($target) > 0) {
			return $target;
		}
		$readers = array('png' => 'imagecreatefrompng', 'webp' => 'imagecreatefromwebp', 'bmp' => 'imagecreatefrombmp', 'gif' => 'imagecreatefromgif', 'jpg' => 'imagecreatefromjpeg');
		$img = false;
		if ($format !== null && isset($readers[$format]) && function_exists($readers[$format])) {
			$img = @call_user_func($readers[$format], $path);
		}
		if (!$img && function_exists('imagecreatefromstring')) {
			$data = @file_get_contents($path);
			if ($data !== false && $data !== '') {
				$img = @imagecreatefromstring($data);
			}
		}
		if (!$img) {
			return null;
		}
		if (function_exists('imageistruecolor') && !imageistruecolor($img)) {
			imagepalettetotruecolor($img);
		}
		imagealphablending($img, false);
		imagesavealpha($img, true);
		imageinterlace($img, false);
		$ok = @imagepng($img, $target);
		imagedestroy($img);
		return ($ok && is_file($target)) ? $target : null;
	}
}
