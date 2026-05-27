<?php
/**
 * Shared flyerC-I PDF page builder and Imagick PNG/ZIP export.
 */
if (!function_exists('az_pdf_site_root')) {
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'pdf_image_helpers.php';
}
if (!function_exists('az_flyer_ci_send_png_zip')) {

	function az_flyer_ci_categoria_from_cookie($Config) {
		$key = $Config->getAlias() . 'category';
		if (!isset($_COOKIE[$key]) || $_COOKIE[$key] === '') {
			return 0;
		}
		return (int) $_COOKIE[$key];
	}

	function az_flyer_ci_juegos_from_clause($schema) {
		return "FROM $schema.Juegos j
            	join $schema.Equipos l on j.Local_ID = l.Equipo_ID
            	join $schema.Equipos v on j.Visitante_ID = v.Equipo_ID
                join $schema.Jornada jo on jo.Jornada_ID = j.Jornada_ID
                join $schema.Campos c on c.Campo_ID = j.Campo_ID
                join $schema.Categorias ca on ca.Categoria_ID = l.Fuerza and ca.Torneo_ID = j.Torneo_ID";
	}

	function az_flyer_ci_juegos_where($jornadaId, $categoriaId, $juegoId = null) {
		$where = 'where jo.Jornada_ID = ' . (int) $jornadaId;
		if ($categoriaId > 0) {
			$where .= ' and l.Fuerza = ' . (int) $categoriaId;
		}
		if ($juegoId !== null && $juegoId !== '') {
			$where .= ' and j.Juego_ID = ' . (int) $juegoId;
		}
		return $where;
	}

	function az_flyer_ci_count_juegos($Config, $schema, $jornadaId, $categoriaId, $juegoId = null) {
		$where = az_flyer_ci_juegos_where($jornadaId, $categoriaId, $juegoId);
		$sql = 'SELECT COUNT(DISTINCT j.Juego_ID) AS cnt ' . az_flyer_ci_juegos_from_clause($schema) . ' ' . $where;
		$result = $Config->query($sql);
		if (!$result || $result->num_rows < 1) {
			return 0;
		}
		$row = $result->fetch_assoc();
		return (int) $row['cnt'];
	}

	function az_flyer_ci_select_juegos_sql($schema, $jornadaId, $categoriaId, $juegoId = null) {
		$where = az_flyer_ci_juegos_where($jornadaId, $categoriaId, $juegoId);
		return "SELECT distinct j.Juego_ID,
                j.Torneo_ID,
                j.Jornada_ID,
                jo.Jornada_Desc,
                jo.Jornada_DescCorta,
                ca.Categoria_Desc,
                j.Local_ID,
                l.Equipo_FULLDESC,
                j.Visitante_ID,
                v.Equipo_FULLDESC,
                j.Campo_ID,
                c.Campo_DESC,
                TIME_FORMAT(j.Horario, '%H:%i HRS') Horario,
                DATE_FORMAT(j.Fecha, '%e de %M') Fecha
            " . az_flyer_ci_juegos_from_clause($schema) . "
            $where
            order by ca.Categoria_ID, j.Fecha, j.Horario, c.Campo_DESC, j.Juego_ID asc";
	}

	function az_flyer_ci_fail($code, $message) {
		http_response_code($code);
		header('Content-Type: text/plain; charset=UTF-8');
		echo $message;
		exit;
	}

	function az_flyer_ci_png_dpi($pngDpi) {
		$pngDpi = (int) $pngDpi;
		if ($pngDpi < 96) {
			return 96;
		}
		if ($pngDpi > 200) {
			return 200;
		}
		return $pngDpi;
	}

	function az_flyer_ci_shell_function_available($name) {
		if (!function_exists($name)) {
			return false;
		}
		$disabled = strtolower((string) ini_get('disable_functions'));
		if ($disabled === '') {
			return true;
		}
		$list = array_filter(array_map('trim', explode(',', $disabled)));
		return !in_array(strtolower($name), $list, true);
	}

	function az_flyer_ci_can_shell() {
		return az_flyer_ci_shell_function_available('exec')
			|| az_flyer_ci_shell_function_available('shell_exec');
	}

	/**
	 * @param array<int,string> $out
	 */
	function az_flyer_ci_shell_run($command, &$out, &$code) {
		$out = array();
		$code = 1;
		if (az_flyer_ci_shell_function_available('exec')) {
			@exec($command, $out, $code);
			return true;
		}
		if (az_flyer_ci_shell_function_available('shell_exec')) {
			$result = @shell_exec($command);
			if ($result !== null && $result !== '') {
				$out = preg_split('/\r\n|\r|\n/', trim($result));
				$code = 0;
				return true;
			}
		}
		return false;
	}

	function az_flyer_ci_probe_executable($run) {
		$run = trim((string) $run);
		if ($run === '') {
			return false;
		}
		if ((strpos($run, DIRECTORY_SEPARATOR) !== false || strpos($run, '/') !== false) && !is_file($run)) {
			return false;
		}
		if (!az_flyer_ci_can_shell()) {
			return false;
		}
		$runEsc = escapeshellarg($run);
		$flags = array('-version', '--version', '-v', '-h', '-help');
		foreach ($flags as $flag) {
			$probeOut = array();
			$probeCode = 1;
			az_flyer_ci_shell_run($runEsc . ' ' . $flag . ' 2>&1', $probeOut, $probeCode);
			if ($probeCode === 0 && !empty($probeOut)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @return string|null
	 */
	function az_flyer_ci_which($name) {
		if (!az_flyer_ci_shell_function_available('exec')) {
			return null;
		}
		$out = array();
		$code = 1;
		@exec('command -v ' . escapeshellarg($name) . ' 2>/dev/null', $out, $code);
		if ($code === 0 && !empty($out[0]) && is_executable($out[0])) {
			return $out[0];
		}
		return null;
	}

	/**
	 * @return array<string,string>
	 */
	function az_flyer_ci_repo_root_from_site($sitePath) {
		if (!function_exists('az_flyer_facebook_repo_root_from_site')) {
			$sitePath = rtrim(str_replace('\\', '/', $sitePath), '/');
			if ($sitePath === '') {
				return null;
			}
			$dir = $sitePath;
			for ($i = 0; $i < 8; $i++) {
				if (is_dir($dir . '/.local')) {
					return $dir;
				}
				$parent = dirname($dir);
				if ($parent === $dir) {
					break;
				}
				$dir = $parent;
			}
			return null;
		}
		return az_flyer_facebook_repo_root_from_site($sitePath);
	}

	/**
	 * @return array<string,string>
	 */
	function az_flyer_ci_read_local_export_env($sitePath = '') {
		$paths = array();
		$repoRoot = az_flyer_ci_repo_root_from_site($sitePath);
		if ($repoRoot === null) {
			return $paths;
		}
		$envPath = $repoRoot . '/.local/flyer-export.env';
		if (!is_readable($envPath)) {
			return $paths;
		}
		$parsed = @parse_ini_file($envPath, false, INI_SCANNER_RAW);
		if ($parsed === false) {
			return $paths;
		}
		foreach (array('magick_path', 'convert_path', 'gs_path', 'pdftoppm_path') as $key) {
			if (!empty($parsed[$key])) {
				$paths[$key] = trim((string) $parsed[$key]);
			}
		}
		return $paths;
	}

	function az_flyer_ci_read_export_paths_from_ini($sitePath = '') {
		$paths = az_flyer_ci_read_local_export_env($sitePath);
		if ($sitePath === '') {
			return $paths;
		}
		$iniPath = rtrim(str_replace('\\', '/', $sitePath), '/') . '/ini/config.ini';
		if (!is_readable($iniPath)) {
			return $paths;
		}
		$parsed = @parse_ini_file($iniPath, true, INI_SCANNER_RAW);
		if ($parsed === false) {
			return $paths;
		}
		$section = null;
		if (isset($parsed['flyer_export']) && is_array($parsed['flyer_export'])) {
			$section = $parsed['flyer_export'];
		} elseif (isset($parsed['flyer']) && is_array($parsed['flyer'])) {
			$section = $parsed['flyer'];
		}
		if ($section === null) {
			return $paths;
		}
		foreach (array('magick_path', 'convert_path', 'gs_path', 'pdftoppm_path') as $key) {
			if (!empty($section[$key])) {
				$paths[$key] = trim((string) $section[$key]);
			}
		}
		return $paths;
	}

	/**
	 * @return string|null absolute path or command name
	 */
	function az_flyer_ci_find_executable(array $candidates) {
		foreach ($candidates as $cmd) {
			$cmd = trim((string) $cmd);
			if ($cmd === '') {
				continue;
			}
			if (az_flyer_ci_probe_executable($cmd)) {
				return $cmd;
			}
		}
		return null;
	}

	/**
	 * @return string|null
	 */
	function az_flyer_ci_resolve_imagemagick_cli($sitePath = '') {
		static $cache = array();
		$key = $sitePath !== '' ? $sitePath : '_';
		if (array_key_exists($key, $cache)) {
			return $cache[$key];
		}
		$candidates = array();
		$ini = az_flyer_ci_read_export_paths_from_ini($sitePath);
		if (!empty($ini['magick_path'])) {
			$candidates[] = $ini['magick_path'];
		}
		if (!empty($ini['convert_path'])) {
			$candidates[] = $ini['convert_path'];
		}
		$env = getenv('MAGICK_BINARY');
		if ($env !== false && $env !== '') {
			$candidates[] = $env;
		}
		$env = getenv('IMAGEMAGICK_CONVERT');
		if ($env !== false && $env !== '') {
			$candidates[] = $env;
		}
		if (DIRECTORY_SEPARATOR === '\\') {
			foreach (glob('C:/Program Files/ImageMagick*/magick.exe') ?: array() as $path) {
				$candidates[] = $path;
			}
			foreach (glob('C:/Program Files (x86)/ImageMagick*/magick.exe') ?: array() as $path) {
				$candidates[] = $path;
			}
			foreach (glob('C:/laragon/bin/imagemagick/*/magick.exe') ?: array() as $path) {
				$candidates[] = $path;
			}
			foreach (glob('C:/laragon/bin/imagemagick/*/convert.exe') ?: array() as $path) {
				$candidates[] = $path;
			}
		}
		$whichMagick = az_flyer_ci_which('magick');
		if ($whichMagick !== null) {
			$candidates[] = $whichMagick;
		}
		$whichConvert = az_flyer_ci_which('convert');
		if ($whichConvert !== null) {
			$candidates[] = $whichConvert;
		}
		$candidates = array_merge($candidates, array(
			'magick',
			'convert',
			'/usr/local/bin/magick',
			'/usr/local/bin/convert',
			'/usr/bin/magick',
			'/usr/bin/convert',
			'/usr/local/magick/bin/magick',
			'/usr/local/magick/bin/convert',
		));
		$cache[$key] = az_flyer_ci_find_executable($candidates);
		return $cache[$key];
	}

	/**
	 * @return string|null
	 */
	function az_flyer_ci_resolve_ghostscript_cli($sitePath = '') {
		static $cache = array();
		$key = $sitePath !== '' ? $sitePath : '_';
		if (array_key_exists($key, $cache)) {
			return $cache[$key];
		}
		$candidates = array();
		$ini = az_flyer_ci_read_export_paths_from_ini($sitePath);
		if (!empty($ini['gs_path'])) {
			$candidates[] = $ini['gs_path'];
		}
		$env = getenv('GS_PROG');
		if ($env !== false && $env !== '') {
			$candidates[] = $env;
		}
		if (DIRECTORY_SEPARATOR === '\\') {
			foreach (glob('C:/Program Files/gs/*/bin/gswin64c.exe') ?: array() as $path) {
				$candidates[] = $path;
			}
			foreach (glob('C:/laragon/bin/gs*/bin/gswin64c.exe') ?: array() as $path) {
				$candidates[] = $path;
			}
			$candidates[] = 'gswin64c';
			$candidates[] = 'gswin32c';
		}
		$whichGs = az_flyer_ci_which('gs');
		if ($whichGs !== null) {
			$candidates[] = $whichGs;
		}
		$candidates = array_merge($candidates, array(
			'gs',
			'/usr/bin/gs',
			'/usr/local/bin/gs',
			'/opt/ghostscript/bin/gs',
		));
		$cache[$key] = az_flyer_ci_find_executable($candidates);
		return $cache[$key];
	}

	/**
	 * @return string|null
	 */
	function az_flyer_ci_resolve_pdftoppm_cli($sitePath = '') {
		static $cache = array();
		$key = $sitePath !== '' ? $sitePath : '_';
		if (array_key_exists($key, $cache)) {
			return $cache[$key];
		}
		$candidates = array();
		$ini = az_flyer_ci_read_export_paths_from_ini($sitePath);
		if (!empty($ini['pdftoppm_path'])) {
			$candidates[] = $ini['pdftoppm_path'];
		}
		$which = az_flyer_ci_which('pdftoppm');
		if ($which !== null) {
			$candidates[] = $which;
		}
		$candidates = array_merge($candidates, array(
			'pdftoppm',
			'/usr/bin/pdftoppm',
			'/usr/local/bin/pdftoppm',
		));
		$cache[$key] = az_flyer_ci_find_executable($candidates);
		return $cache[$key];
	}

	/**
	 * @return array<string,mixed>
	 */
	function az_flyer_ci_png_backend_status($sitePath = '') {
		return array(
			'php_imagick' => extension_loaded('imagick') && class_exists('Imagick'),
			'shell_available' => az_flyer_ci_can_shell(),
			'imagemagick_cli' => az_flyer_ci_resolve_imagemagick_cli($sitePath),
			'ghostscript_cli' => az_flyer_ci_resolve_ghostscript_cli($sitePath),
			'pdftoppm_cli' => az_flyer_ci_resolve_pdftoppm_cli($sitePath),
			'disable_functions' => (string) ini_get('disable_functions'),
		);
	}

	function az_flyer_ci_png_label_path($tmpDir, array $pageLabels, $pageIndex) {
		$label = isset($pageLabels[$pageIndex])
			? (string) $pageLabels[$pageIndex]
			: sprintf('page-%03d', $pageIndex + 1);
		$safeName = preg_replace('/[^\w\-]+/', '_', $label);
		if ($safeName === '') {
			$safeName = 'page';
		}
		// Page index keeps filenames unique when labels repeat (e.g. duplicate juego rows).
		$safeName .= '-' . ((int) $pageIndex + 1);
		return $tmpDir . DIRECTORY_SEPARATOR . $safeName . '.png';
	}

	/**
	 * @return array{tmpDir:string,paths:array<int,string>,labels:array<int,string>}
	 */
	function az_flyer_ci_write_pdf_temp($pdfBinary) {
		$tmpDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'flyer-png-' . uniqid('', true);
		if (!@mkdir($tmpDir, 0700, true)) {
			throw new RuntimeException('Could not create temporary directory.');
		}
		$pdfPath = $tmpDir . DIRECTORY_SEPARATOR . 'flyers.pdf';
		if (@file_put_contents($pdfPath, $pdfBinary) === false) {
			@rmdir($tmpDir);
			throw new RuntimeException('Could not write temporary PDF.');
		}
		return array('tmpDir' => $tmpDir, 'pdfPath' => $pdfPath);
	}

	/**
	 * @return array{tmpDir:string,paths:array<int,string>,labels:array<int,string>}
	 */
	function az_flyer_ci_generate_png_paths_imagick_ext($pdfPath, $tmpDir, array $pageLabels, $pngDpi) {
		$pageCount = count($pageLabels);
		$imagickTime = min(300, 90 + ($pageCount * 20));
		Imagick::setResourceLimit(Imagick::RESOURCETYPE_MEMORY, 384);
		Imagick::setResourceLimit(Imagick::RESOURCETYPE_MAP, 384);
		Imagick::setResourceLimit(Imagick::RESOURCETYPE_TIME, $imagickTime);
		Imagick::setResourceLimit(Imagick::RESOURCETYPE_AREA, 256 * 1024 * 1024);

		$imagickProbe = new Imagick();
		$pdfFormats = $imagickProbe->queryFormats('PDF');
		$imagickProbe->clear();
		$imagickProbe->destroy();
		if (empty($pdfFormats)) {
			throw new RuntimeException('ImageMagick cannot read PDF (Ghostscript delegate missing on server).');
		}

		$pngPaths = array();
		try {
			for ($pageIndex = 0; $pageIndex < $pageCount; $pageIndex++) {
				$pageImagick = new Imagick();
				$pageImagick->setResolution($pngDpi, $pngDpi);
				$pageImagick->readImage($pdfPath . '[' . $pageIndex . ']');
				$pageImagick->setImageBackgroundColor(new ImagickPixel('white'));
				$pageImagick->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
				$flat = $pageImagick->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
				$flat->setImageUnits(Imagick::RESOLUTION_PIXELSPERINCH);
				$flat->setImageResolution($pngDpi, $pngDpi);
				$flat->setImageFormat('png');
				$flat->setImageCompressionQuality(95);
				$flat->stripImage();

				$pngFile = az_flyer_ci_png_label_path($tmpDir, $pageLabels, $pageIndex);
				$flat->writeImage($pngFile);
				$flat->destroy();
				$pageImagick->clear();
				$pageImagick->destroy();
				unset($pageImagick, $flat);
				if (!is_file($pngFile) || filesize($pngFile) < 1) {
					throw new RuntimeException('ImageMagick did not create page ' . ($pageIndex + 1) . ' PNG.');
				}
				$pngPaths[] = $pngFile;
				if (function_exists('gc_collect_cycles')) {
					gc_collect_cycles();
				}
			}
		} catch (Throwable $e) {
			az_flyer_ci_cleanup_png_paths($tmpDir, $pngPaths);
			throw new RuntimeException('Error generating flyer images: ' . $e->getMessage(), 0, $e);
		}

		return array(
			'tmpDir' => $tmpDir,
			'paths' => $pngPaths,
			'labels' => $pageLabels,
		);
	}

	/**
	 * @return array{tmpDir:string,paths:array<int,string>,labels:array<int,string>}
	 */
	function az_flyer_ci_generate_png_paths_imagemagick_cli($cli, $pdfPath, $tmpDir, array $pageLabels, $pngDpi) {
		if (!az_flyer_ci_shell_function_available('exec')) {
			throw new RuntimeException('Shell exec is disabled; cannot run ImageMagick.');
		}
		$pageCount = count($pageLabels);
		$pngPaths = array();
		$cliEsc = escapeshellarg($cli);
		$isMagick7 = (stripos(basename($cli), 'magick') !== false);

		try {
			for ($pageIndex = 0; $pageIndex < $pageCount; $pageIndex++) {
				$pngFile = az_flyer_ci_png_label_path($tmpDir, $pageLabels, $pageIndex);
				$pngEsc = escapeshellarg($pngFile);
				$pageSel = escapeshellarg($pdfPath . '[' . $pageIndex . ']');
				if ($isMagick7) {
					$cmd = $cliEsc . ' -density ' . (int) $pngDpi . ' ' . $pageSel
						. ' -background white -alpha remove -flatten ' . $pngEsc . ' 2>&1';
				} else {
					$cmd = $cliEsc . ' -density ' . (int) $pngDpi . ' ' . $pageSel
						. ' -background white -alpha remove -flatten ' . $pngEsc . ' 2>&1';
				}
				$out = array();
				$code = 1;
				@exec($cmd, $out, $code);
				if ($code !== 0 || !is_readable($pngFile)) {
					$detail = trim(implode(' ', $out));
					throw new RuntimeException(
						'ImageMagick failed on page ' . ($pageIndex + 1)
						. ($detail !== '' ? ': ' . $detail : '')
					);
				}
				$pngPaths[] = $pngFile;
			}
		} catch (Throwable $e) {
			az_flyer_ci_cleanup_png_paths($tmpDir, $pngPaths);
			throw new RuntimeException('Error generating flyer images: ' . $e->getMessage(), 0, $e);
		}

		return array(
			'tmpDir' => $tmpDir,
			'paths' => $pngPaths,
			'labels' => $pageLabels,
		);
	}

	/**
	 * @return array{tmpDir:string,paths:array<int,string>,labels:array<int,string>}
	 */
	function az_flyer_ci_generate_png_paths_ghostscript_cli($gs, $pdfPath, $tmpDir, array $pageLabels, $pngDpi) {
		if (!az_flyer_ci_shell_function_available('exec')) {
			throw new RuntimeException('Shell exec is disabled; cannot run Ghostscript.');
		}
		$pageCount = count($pageLabels);
		$pngPaths = array();
		$gsEsc = escapeshellarg($gs);
		$pdfEsc = escapeshellarg($pdfPath);

		try {
			for ($pageIndex = 0; $pageIndex < $pageCount; $pageIndex++) {
				$pngFile = az_flyer_ci_png_label_path($tmpDir, $pageLabels, $pageIndex);
				$pngEsc = escapeshellarg($pngFile);
				$pageNum = $pageIndex + 1;
				$cmd = $gsEsc . ' -dNOPAUSE -dBATCH -dSAFER -sDEVICE=png16m -r' . (int) $pngDpi
					. ' -dFirstPage=' . $pageNum . ' -dLastPage=' . $pageNum
					. ' -sOutputFile=' . $pngEsc . ' ' . $pdfEsc . ' 2>&1';
				$out = array();
				$code = 1;
				@exec($cmd, $out, $code);
				if ($code !== 0 || !is_readable($pngFile)) {
					$detail = trim(implode(' ', $out));
					throw new RuntimeException(
						'Ghostscript failed on page ' . $pageNum
						. ($detail !== '' ? ': ' . $detail : '')
					);
				}
				$pngPaths[] = $pngFile;
			}
		} catch (Throwable $e) {
			az_flyer_ci_cleanup_png_paths($tmpDir, $pngPaths);
			throw new RuntimeException('Error generating flyer images: ' . $e->getMessage(), 0, $e);
		}

		return array(
			'tmpDir' => $tmpDir,
			'paths' => $pngPaths,
			'labels' => $pageLabels,
		);
	}

	/**
	 * @return array{tmpDir:string,paths:array<int,string>,labels:array<int,string>}
	 */
	function az_flyer_ci_generate_png_paths_pdftoppm_cli($pdftoppm, $pdfPath, $tmpDir, array $pageLabels, $pngDpi) {
		if (!az_flyer_ci_can_shell()) {
			throw new RuntimeException('Shell is disabled; cannot run pdftoppm.');
		}
		$pageCount = count($pageLabels);
		$prefix = $tmpDir . DIRECTORY_SEPARATOR . 'page';
		$cmd = escapeshellarg($pdftoppm) . ' -png -r ' . (int) $pngDpi
			. ' -f 1 -l ' . (int) $pageCount . ' '
			. escapeshellarg($pdfPath) . ' ' . escapeshellarg($prefix) . ' 2>&1';
		$out = array();
		$code = 1;
		az_flyer_ci_shell_run($cmd, $out, $code);
		$generated = glob($tmpDir . DIRECTORY_SEPARATOR . 'page-*.png') ?: array();
		if (empty($generated)) {
			$generated = glob($tmpDir . DIRECTORY_SEPARATOR . 'page*.png') ?: array();
		}
		natsort($generated);
		$generated = array_values($generated);
		if ($code !== 0 || count($generated) < $pageCount) {
			$detail = trim(implode(' ', $out));
			throw new RuntimeException(
				'pdftoppm failed' . ($detail !== '' ? ': ' . $detail : ' (missing output PNG files).')
			);
		}

		$pngPaths = array();
		for ($pageIndex = 0; $pageIndex < $pageCount; $pageIndex++) {
			$target = az_flyer_ci_png_label_path($tmpDir, $pageLabels, $pageIndex);
			if (!isset($generated[$pageIndex]) || !is_readable($generated[$pageIndex])) {
				throw new RuntimeException('pdftoppm did not create page ' . ($pageIndex + 1) . '.');
			}
			if ($generated[$pageIndex] !== $target) {
				if (!@rename($generated[$pageIndex], $target)) {
					if (!@copy($generated[$pageIndex], $target)) {
						throw new RuntimeException('Could not move generated PNG for page ' . ($pageIndex + 1) . '.');
					}
					@unlink($generated[$pageIndex]);
				}
			}
			$pngPaths[] = $target;
		}
		foreach ($generated as $extra) {
			if (is_file($extra) && !in_array($extra, $pngPaths, true)) {
				@unlink($extra);
			}
		}

		return array(
			'tmpDir' => $tmpDir,
			'paths' => $pngPaths,
			'labels' => $pageLabels,
		);
	}

	function az_flyer_ci_png_export_unavailable_message($sitePath = '') {
		$status = az_flyer_ci_png_backend_status($sitePath);
		$parts = array();
		if (!$status['php_imagick']) {
			$parts[] = 'enable PHP Imagick (cPanel → MultiPHP INI Editor → imagick)';
		}
		if (!$status['shell_available']) {
			$parts[] = 'shell functions (exec) are disabled on this server';
		}
		if ($status['shell_available']) {
			if ($status['imagemagick_cli'] === null && $status['ghostscript_cli'] === null && $status['pdftoppm_cli'] === null) {
				$parts[] = 'install ImageMagick, Ghostscript, or poppler-utils (pdftoppm)';
			}
		}
		$hint = 'Optional: set paths in ini/config.ini [flyer_export] magick_path, gs_path, pdftoppm_path.';
		if (empty($parts)) {
			return 'Flyer PNG export failed. ' . $hint;
		}
		return 'Flyer PNG export is not available: ' . implode('; ', $parts) . '. ' . $hint
			. ' Admin: open ajax/Flyers/pngBackendCheck.php while logged in.';
	}

	/**
	 * Convert PDF bytes to PNG files on disk.
	 *
	 * @param string $sitePath optional site root for ini paths and backend detection
	 * @return array{tmpDir:string,paths:array<int,string>,labels:array<int,string>}
	 */
	function az_flyer_ci_generate_png_paths($pdfBinary, array $pageLabels, $pngDpi = 200, $sitePath = '') {
		$pageCount = count($pageLabels);
		if ($pageCount < 1) {
			throw new RuntimeException('No flyer pages were generated.');
		}

		$pngDpi = az_flyer_ci_png_dpi($pngDpi);
		$temp = az_flyer_ci_write_pdf_temp($pdfBinary);
		$tmpDir = $temp['tmpDir'];
		$pdfPath = $temp['pdfPath'];
		unset($pdfBinary);

		try {
			if (extension_loaded('imagick') && class_exists('Imagick')) {
				$result = az_flyer_ci_generate_png_paths_imagick_ext($pdfPath, $tmpDir, $pageLabels, $pngDpi);
			} elseif (($pdftoppm = az_flyer_ci_resolve_pdftoppm_cli($sitePath)) !== null) {
				$result = az_flyer_ci_generate_png_paths_pdftoppm_cli($pdftoppm, $pdfPath, $tmpDir, $pageLabels, $pngDpi);
			} elseif (($magickCli = az_flyer_ci_resolve_imagemagick_cli($sitePath)) !== null) {
				$result = az_flyer_ci_generate_png_paths_imagemagick_cli($magickCli, $pdfPath, $tmpDir, $pageLabels, $pngDpi);
			} elseif (($gsCli = az_flyer_ci_resolve_ghostscript_cli($sitePath)) !== null) {
				$result = az_flyer_ci_generate_png_paths_ghostscript_cli($gsCli, $pdfPath, $tmpDir, $pageLabels, $pngDpi);
			} else {
				throw new RuntimeException(az_flyer_ci_png_export_unavailable_message($sitePath));
			}
		} finally {
			@unlink($pdfPath);
		}

		return $result;
	}

	/**
	 * @param array<int,string> $pngPaths
	 */
	function az_flyer_ci_cleanup_png_paths($tmpDir, array $pngPaths) {
		foreach ($pngPaths as $path) {
			if (is_string($path) && $path !== '') {
				@unlink($path);
			}
		}
		if (is_string($tmpDir) && $tmpDir !== '' && is_dir($tmpDir)) {
			@rmdir($tmpDir);
		}
	}

	/**
	 * @param array $pageLabels
	 */
	function az_flyer_ci_send_png_zip($pdfBinary, array $pageLabels, $jornadaId, $zipExtra = '', $sitePath = '') {
		if (!class_exists('ZipArchive')) {
			az_flyer_ci_fail(500, 'ZipArchive is required to download flyer PNGs.');
		}

		$pngDpi = 200;
		if (isset($_GET['dpi'])) {
			$pngDpi = (int) $_GET['dpi'];
		}

		try {
			$generated = az_flyer_ci_generate_png_paths($pdfBinary, $pageLabels, $pngDpi, $sitePath);
		} catch (Throwable $e) {
			az_flyer_ci_fail(500, $e->getMessage());
		}

		$tmpDir = $generated['tmpDir'];
		$pngPaths = $generated['paths'];

		if (count($pngPaths) === 1) {
			$pngFile = $pngPaths[0];
			$downloadName = 'flyers-jornada-' . preg_replace('/\D+/', '', (string) $jornadaId);
			if ($zipExtra === 'flyer') {
				$downloadName = 'flyer-juego-' . preg_replace('/\D+/', '', (string) $jornadaId);
			} elseif ($zipExtra !== '') {
				$downloadName .= '-' . preg_replace('/[^\w\-]+/', '_', (string) $zipExtra);
			}
			$downloadName .= '.png';
			header('Content-Type: image/png');
			header('Content-Disposition: attachment; filename="' . $downloadName . '"');
			header('Content-Length: ' . filesize($pngFile));
			header('Cache-Control: no-store, no-cache, must-revalidate');
			header('Pragma: no-cache');
			readfile($pngFile);
			az_flyer_ci_cleanup_png_paths($tmpDir, $pngPaths);
			exit;
		}

		$zipPath = $tmpDir . DIRECTORY_SEPARATOR . 'flyers.zip';
		$zip = new ZipArchive();
		if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
			foreach ($pngPaths as $path) {
				@unlink($path);
			}
			@rmdir($tmpDir);
			az_flyer_ci_fail(500, 'Could not create ZIP archive.');
		}
		foreach ($pngPaths as $path) {
			$zip->addFile($path, basename($path));
		}
		$zip->close();

		$zipDownloadName = 'flyers-jornada-' . preg_replace('/\D+/', '', (string) $jornadaId);
		if ($zipExtra !== '') {
			$zipDownloadName .= '-' . preg_replace('/[^\w\-]+/', '_', (string) $zipExtra);
		}
		$zipDownloadName .= '.zip';
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="' . $zipDownloadName . '"');
		header('Content-Length: ' . filesize($zipPath));
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Pragma: no-cache');
		readfile($zipPath);

		az_flyer_ci_cleanup_png_paths($tmpDir, array_merge($pngPaths, array($zipPath)));
		exit;
	}
}
