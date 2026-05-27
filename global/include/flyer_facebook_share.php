<?php
/**
 * Facebook Share Dialog: public token URLs with Open Graph preview (personal timeline).
 * No Page token or publish_actions required — user confirms each share in Facebook.
 */
if (!function_exists('az_flyer_lang')) {

	/**
	 * @param string $key lang array key (e.g. jsfb01)
	 * @param array<int|string> $replace optional %1, %2, … replacements
	 */
	function az_flyer_lang($key, array $replace = array()) {
		global $lang;
		$text = (is_array($lang) && isset($lang[$key])) ? (string) $lang[$key] : (string) $key;
		foreach ($replace as $i => $val) {
			$text = str_replace('%' . ($i + 1), (string) $val, $text);
		}
		return $text;
	}

	/**
	 * Load global $lang from global/languages (returns array for typed callers).
	 *
	 * @return array<string,string>
	 */
	function az_flyer_lang_load_for_config($Config) {
		global $lang;
		if (!is_array($lang)) {
			$lang = array();
		}
		if (!empty($lang)) {
			return $lang;
		}
		$alias = method_exists($Config, 'getAlias') ? $Config->getAlias() : '';
		$langCode = 'es';
		if ($alias !== '' && isset($_COOKIE[$alias . 'language']) && $_COOKIE[$alias . 'language'] !== '') {
			$langCode = preg_replace('/[^a-z]/i', '', (string) $_COOKIE[$alias . 'language']);
		}
		if ($langCode !== 'en') {
			$langCode = 'es';
		}
		$langFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . 'lang.' . $langCode . '.php';
		if (is_readable($langFile)) {
			include $langFile;
		}
		if (!is_array($lang)) {
			$lang = array();
		}
		return $lang;
	}
}

if (!function_exists('az_flyer_share_storage_dir')) {

	function az_flyer_share_storage_dir($sitePath) {
		$dir = rtrim(str_replace('\\', '/', $sitePath), '/') . '/tmp/flyer-share';
		$dir = str_replace('/', DIRECTORY_SEPARATOR, $dir);
		if (!is_dir($dir)) {
			@mkdir($dir, 0755, true);
		}
		if (!is_dir($dir) || !is_writable($dir)) {
			throw new RuntimeException('Cannot write flyer share directory: tmp/flyer-share');
		}
		return $dir;
	}

	function az_flyer_share_cleanup_old($sitePath, $maxAgeSeconds = 172800) {
		try {
			$dir = az_flyer_share_storage_dir($sitePath);
		} catch (Throwable $e) {
			return;
		}
		$cutoff = time() - (int) $maxAgeSeconds;
		$files = glob($dir . DIRECTORY_SEPARATOR . '*.json');
		if ($files === false) {
			return;
		}
		foreach ($files as $metaFile) {
			$raw = @file_get_contents($metaFile);
			$meta = is_string($raw) ? json_decode($raw, true) : null;
			$expires = is_array($meta) && isset($meta['expires']) ? (int) $meta['expires'] : 0;
			$created = is_array($meta) && isset($meta['created']) ? (int) $meta['created'] : 0;
			if ($expires >= time() && $created >= $cutoff) {
				continue;
			}
			if (is_array($meta) && !empty($meta['images']) && is_array($meta['images'])) {
				foreach ($meta['images'] as $file) {
					$img = $dir . DIRECTORY_SEPARATOR . basename((string) $file);
					if (is_file($img)) {
						@unlink($img);
					}
				}
			} elseif (is_array($meta) && !empty($meta['image'])) {
				$img = $dir . DIRECTORY_SEPARATOR . basename((string) $meta['image']);
				if (is_file($img)) {
					@unlink($img);
				}
			}
			@unlink($metaFile);
		}
	}

	function az_flyer_share_normalize_token($token) {
		$token = strtolower(trim((string) $token));
		if (!preg_match('/^[a-f0-9]{32}$/', $token)) {
			return '';
		}
		return $token;
	}

	/**
	 * @return array<string,mixed>|null
	 */
	function az_flyer_share_load($sitePath, $token) {
		$token = az_flyer_share_normalize_token($token);
		if ($token === '') {
			return null;
		}
		$dir = az_flyer_share_storage_dir($sitePath);
		$metaFile = $dir . DIRECTORY_SEPARATOR . $token . '.json';
		if (!is_readable($metaFile)) {
			return null;
		}
		$raw = @file_get_contents($metaFile);
		$meta = is_string($raw) ? json_decode($raw, true) : null;
		if (!is_array($meta)) {
			return null;
		}
		$expires = isset($meta['expires']) ? (int) $meta['expires'] : 0;
		if ($expires > 0 && $expires < time()) {
			return null;
		}
		$imageFiles = array();
		if (!empty($meta['images']) && is_array($meta['images'])) {
			foreach ($meta['images'] as $file) {
				$name = basename((string) $file);
				if (preg_match('/^[a-f0-9]{32}(?:-\d+)?\.png$/', $name)) {
					$imageFiles[] = $name;
				}
			}
		}
		if (empty($imageFiles) && !empty($meta['image'])) {
			$name = basename((string) $meta['image']);
			if (preg_match('/^[a-f0-9]{32}(?:-\d+)?\.png$/', $name)) {
				$imageFiles[] = $name;
			}
		}
		if (empty($imageFiles)) {
			return null;
		}
		$paths = array();
		foreach ($imageFiles as $name) {
			$path = $dir . DIRECTORY_SEPARATOR . $name;
			if (!is_readable($path)) {
				return null;
			}
			$paths[] = $path;
		}
		$meta['token'] = $token;
		$meta['images'] = $imageFiles;
		$meta['image'] = $imageFiles[0];
		$meta['image_path'] = $paths[0];
		$meta['image_paths'] = $paths;
		return $meta;
	}

	/**
	 * @param array<int,string> $pngPaths
	 */
	function az_flyer_share_register_bundle($sitePath, $title, $description, $userMessage, array $pngPaths) {
		if (count($pngPaths) < 1) {
			throw new RuntimeException('No flyer pages were generated.');
		}
		$token = bin2hex(random_bytes(16));
		$dir = az_flyer_share_storage_dir($sitePath);
		$imageFiles = array();
		foreach ($pngPaths as $idx => $pngPath) {
			$pngPath = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, (string) $pngPath);
			if ($pngPath === '' || !is_file($pngPath) || filesize($pngPath) < 1) {
				$msg = function_exists('az_flyer_lang') ? az_flyer_lang('jsfb24') : 'Flyer PNG is missing.';
				throw new RuntimeException($msg);
			}
			$n = (int) $idx + 1;
			$imageFile = $token . '-' . $n . '.png';
			$dest = $dir . DIRECTORY_SEPARATOR . $imageFile;
			if (!@copy($pngPath, $dest)) {
				foreach ($imageFiles as $prev) {
					@unlink($dir . DIRECTORY_SEPARATOR . $prev);
				}
				$msg = function_exists('az_flyer_lang') ? az_flyer_lang('jsfb25') : 'Could not store flyer image for sharing.';
				throw new RuntimeException($msg);
			}
			$imageFiles[] = $imageFile;
		}
		$userMessage = trim((string) $userMessage);
		$meta = array(
			'created' => time(),
			'expires' => time() + (48 * 3600),
			'title' => trim((string) $title),
			'description' => trim((string) $description),
			'user_message' => $userMessage,
			'bundle' => count($imageFiles) > 1,
			'images' => $imageFiles,
			'image' => $imageFiles[0],
		);
		$written = @file_put_contents($dir . DIRECTORY_SEPARATOR . $token . '.json', json_encode($meta, JSON_UNESCAPED_UNICODE));
		if ($written === false) {
			foreach ($imageFiles as $file) {
				@unlink($dir . DIRECTORY_SEPARATOR . $file);
			}
			throw new RuntimeException('Could not save share metadata.');
		}
		return $token;
	}

	function az_flyer_share_website_base($website) {
		$website = trim((string) $website);
		if ($website === '') {
			return '';
		}
		return rtrim($website, '/') . '/';
	}

	function az_flyer_share_website_base_resolved($website) {
		$base = az_flyer_share_website_base($website);
		if ($base === '' && !empty($_SERVER['HTTP_HOST'])) {
			$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
			$base = $scheme . '://' . $_SERVER['HTTP_HOST'] . '/';
		}
		if (preg_match('#^http://#i', $base) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
			$base = 'https://' . substr($base, 7);
		}
		return $base;
	}

	/**
	 * @return array<int,string>
	 */
	function az_flyer_share_image_urls($website, $token, $imageCount) {
		$token = az_flyer_share_normalize_token($token);
		$base = az_flyer_share_website_base_resolved($website);
		$urls = array();
		$imageCount = (int) $imageCount;
		if ($imageCount < 1) {
			$imageCount = 1;
		}
		for ($n = 1; $n <= $imageCount; $n++) {
			$urls[] = $base . 'pdf/flyerShareImage.php?t=' . rawurlencode($token) . '&n=' . $n;
		}
		return $urls;
	}

	/**
	 * Same-directory URLs for flyerFacebookShareLaunch.php fetch() (must match page origin).
	 *
	 * @return array<int,string>
	 */
	function az_flyer_share_image_urls_for_launch($token, $imageCount) {
		$token = az_flyer_share_normalize_token($token);
		if ($token === '') {
			return array();
		}
		$urls = array();
		$imageCount = (int) $imageCount;
		if ($imageCount < 1) {
			$imageCount = 1;
		}
		for ($n = 1; $n <= $imageCount; $n++) {
			$urls[] = 'flyerShareImage.php?t=' . rawurlencode($token) . '&n=' . $n;
		}
		return $urls;
	}

	/**
	 * @return array{share_page_url:string,image_url:string,sharer_url:string}
	 */
	function az_flyer_share_urls($website, $token, $quote = '') {
		$token = az_flyer_share_normalize_token($token);
		$base = az_flyer_share_website_base_resolved($website);
		$sharePage = $base . 'pdf/flyerShare.php?t=' . rawurlencode($token);
		$imageUrl = $base . 'pdf/flyerShareImage.php?t=' . rawurlencode($token) . '&n=1';
		$sharer = 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($sharePage);
		$quote = trim((string) $quote);
		if ($quote !== '') {
			$sharer .= '&quote=' . rawurlencode($quote);
		}
		return array(
			'share_page_url' => $sharePage,
			'image_url' => $imageUrl,
			'sharer_url' => $sharer,
		);
	}

	function az_flyer_share_escape_html($value) {
		return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
	}

	/** CORP for subresource loads when host sends COEP (Chrome shows blocked:origin without this). */
	function az_flyer_share_send_resource_policy_headers() {
		if (!headers_sent()) {
			header('Cross-Origin-Resource-Policy: same-site');
		}
	}

	/**
	 * PNG bytes as base64 for launch popup (no client fetch — avoids CORP/CORS/origin blocks).
	 *
	 * @return array<int,string>
	 */
	function az_flyer_share_launch_embedded_png_base64($sitePath, $token) {
		$meta = az_flyer_share_load($sitePath, $token);
		if ($meta === null || empty($meta['image_paths'])) {
			return array();
		}
		$out = array();
		foreach ($meta['image_paths'] as $path) {
			$raw = @file_get_contents($path);
			if (!is_string($raw) || $raw === '') {
				$msg = function_exists('az_flyer_lang') ? az_flyer_lang('jsfb24') : 'Flyer PNG is missing.';
				throw new RuntimeException($msg);
			}
			$out[] = base64_encode($raw);
		}
		return $out;
	}

	function az_flyer_facebook_share_http_page() {
		$token = isset($_GET['t']) ? $_GET['t'] : '';
		if (!defined('APP_SITE_BOOTSTRAP_ROOT')) {
			header('HTTP/1.1 500 Internal Server Error');
			echo 'Site bootstrap missing.';
			exit;
		}
		require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'global' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'Configuration.php';
		$Config = new Configuration();
		$sitePath = $Config->getPath();
		az_flyer_lang_load_for_config($Config);
		$meta = az_flyer_share_load($sitePath, $token);
		if ($meta === null) {
			header('HTTP/1.1 404 Not Found');
			header('Content-Type: text/plain; charset=UTF-8');
			echo az_flyer_lang('jsfb23');
			exit;
		}
		$quote = !empty($meta['user_message']) ? (string) $meta['user_message'] : '';
		$urls = az_flyer_share_urls($Config->getWebSite(), $meta['token'], $quote);
		$title = $meta['title'] !== '' ? $meta['title'] : az_flyer_lang('jsfb22');
		$desc = $quote !== '' ? $quote : ($meta['description'] !== '' ? $meta['description'] : $title);
		$base = az_flyer_share_website_base_resolved($Config->getWebSite());
		az_flyer_share_send_resource_policy_headers();
		header('Content-Type: text/html; charset=UTF-8');
		header('Cache-Control: public, max-age=300');
		echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">';
		echo '<meta property="og:type" content="website">';
		echo '<meta property="og:title" content="' . az_flyer_share_escape_html($title) . '">';
		echo '<meta property="og:description" content="' . az_flyer_share_escape_html($desc) . '">';
		echo '<meta property="og:image" content="' . az_flyer_share_escape_html($urls['image_url']) . '">';
		echo '<meta property="og:url" content="' . az_flyer_share_escape_html($urls['share_page_url']) . '">';
		echo '<meta name="twitter:card" content="summary_large_image">';
		echo '<title>' . az_flyer_share_escape_html($title) . '</title></head>';
		echo '<body style="margin:0;background:#111;color:#eee;font-family:sans-serif;text-align:center;">';
		echo '<p style="padding:1rem;">' . az_flyer_share_escape_html($title) . '</p>';
		if ($quote !== '') {
			echo '<p style="padding:0 1rem 1rem;white-space:pre-wrap;">' . az_flyer_share_escape_html($quote) . '</p>';
		}
		$images = isset($meta['images']) && is_array($meta['images']) ? $meta['images'] : array();
		foreach ($images as $idx => $file) {
			$n = (int) $idx + 1;
			$imgUrl = $base . 'pdf/flyerShareImage.php?t=' . rawurlencode($meta['token']) . '&n=' . $n;
			echo '<div style="margin:0 auto 1.5rem;max-width:520px;">';
			echo '<img src="' . az_flyer_share_escape_html($imgUrl) . '" alt="" style="max-width:100%;height:auto;display:block;margin:0 auto;">';
			echo '</div>';
		}
		echo '</body></html>';
		exit;
	}

	function az_flyer_facebook_share_http_image() {
		$token = isset($_GET['t']) ? $_GET['t'] : '';
		$index = isset($_GET['n']) ? (int) $_GET['n'] : 1;
		if ($index < 1) {
			$index = 1;
		}
		if (!defined('APP_SITE_BOOTSTRAP_ROOT')) {
			header('HTTP/1.1 500 Internal Server Error');
			exit;
		}
		require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'global' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'Configuration.php';
		$Config = new Configuration();
		$sitePath = $Config->getPath();
		$meta = az_flyer_share_load($sitePath, $token);
		if ($meta === null || empty($meta['image_paths'])) {
			header('HTTP/1.1 404 Not Found');
			exit;
		}
		$paths = $meta['image_paths'];
		$imagePath = $paths[$index - 1] ?? $paths[0];
		if (!is_readable($imagePath)) {
			header('HTTP/1.1 404 Not Found');
			exit;
		}
		az_flyer_share_send_resource_policy_headers();
		header('Content-Type: image/png');
		if (!empty($_GET['download'])) {
			$name = basename($imagePath);
			header('Content-Disposition: attachment; filename="' . $name . '"');
			header('Cache-Control: no-store, no-cache, must-revalidate');
		} else {
			header('Cache-Control: public, max-age=86400');
		}
		readfile($imagePath);
		exit;
	}

	/**
	 * ZIP download of all flyer PNGs for a share token.
	 */
	function az_flyer_facebook_share_http_download() {
		$token = isset($_GET['t']) ? $_GET['t'] : '';
		if (!defined('APP_SITE_BOOTSTRAP_ROOT')) {
			header('HTTP/1.1 500 Internal Server Error');
			exit;
		}
		if (!class_exists('ZipArchive')) {
			header('HTTP/1.1 500 Internal Server Error');
			header('Content-Type: text/plain; charset=UTF-8');
			echo 'ZipArchive is required.';
			exit;
		}
		require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'global' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'Configuration.php';
		$Config = new Configuration();
		$sitePath = $Config->getPath();
		$meta = az_flyer_share_load($sitePath, $token);
		if ($meta === null || empty($meta['image_paths'])) {
			header('HTTP/1.1 404 Not Found');
			exit;
		}
		$paths = $meta['image_paths'];
		if (count($paths) === 1) {
			$imagePath = $paths[0];
			if (!is_readable($imagePath)) {
				header('HTTP/1.1 404 Not Found');
				exit;
			}
			az_flyer_share_send_resource_policy_headers();
			header('Content-Type: image/png');
			header('Content-Disposition: attachment; filename="' . basename($imagePath) . '"');
			header('Cache-Control: no-store, no-cache, must-revalidate');
			readfile($imagePath);
			exit;
		}
		$label = isset($_GET['label']) ? preg_replace('/[^\w\-]+/', '_', (string) $_GET['label']) : 'flyers';
		$zipPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'az-flyer-fb-' . $meta['token'] . '.zip';
		$zip = new ZipArchive();
		if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
			header('HTTP/1.1 500 Internal Server Error');
			exit;
		}
		foreach ($paths as $path) {
			if (is_readable($path)) {
				$zip->addFile($path, basename($path));
			}
		}
		$zip->close();
		if (!is_readable($zipPath)) {
			header('HTTP/1.1 500 Internal Server Error');
			exit;
		}
		az_flyer_share_send_resource_policy_headers();
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="' . $label . '.zip"');
		header('Content-Length: ' . filesize($zipPath));
		header('Cache-Control: no-store, no-cache, must-revalidate');
		readfile($zipPath);
		@unlink($zipPath);
		exit;
	}

	function az_flyer_facebook_share_launch_http() {
		if (!defined('APP_SITE_BOOTSTRAP_ROOT')) {
			header('HTTP/1.1 500 Internal Server Error');
			echo 'Bootstrap missing.';
			exit;
		}
		@ini_set('memory_limit', '512M');
		if (function_exists('ignore_user_abort')) {
			ignore_user_abort(true);
		}
		set_time_limit(600);
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		// __DIR__ is global/include (not global/ — dirname(__DIR__) broke production with HTTP 500).
		$azGlobalInclude = __DIR__;
		require_once $azGlobalInclude . DIRECTORY_SEPARATOR . 'membersite_config.php';
		require_once $azGlobalInclude . DIRECTORY_SEPARATOR . 'pdf_image_helpers.php';
		require_once $azGlobalInclude . DIRECTORY_SEPARATOR . 'flyer_ci_export.php';
		require_once $azGlobalInclude . DIRECTORY_SEPARATOR . 'flyer_sc_pdf.php';
		require_once $azGlobalInclude . DIRECTORY_SEPARATOR . 'flyer_game_pdf.php';
		require_once $azGlobalInclude . DIRECTORY_SEPARATOR . 'flyer_facebook_prepare.php';

		$lang = az_flyer_lang_load_for_config($Config);

		$errorMsg = '';
		$downloadUrls = array();
		$zipDownloadUrl = '';
		$fbOpenUrl = 'https://www.facebook.com/';
		$fbOpenUrlMobile = 'https://m.facebook.com/';
		$launchUiJson = '{}';
		try {
			$fgmembersite->CheckLogin('cedulas.php');

			$type = isset($_GET['type']) ? strtolower(trim((string) $_GET['type'])) : '';
			$jornada = isset($_GET['Jornada_ID']) ? (int) $_GET['Jornada_ID'] : 0;
			$categoria = isset($_GET['Categoria_ID']) ? (int) $_GET['Categoria_ID'] : 0;
			$juego = isset($_GET['Juego_ID']) ? (int) $_GET['Juego_ID'] : 0;

			$share = az_flyer_facebook_prepare_share_data($Config, $type, $jornada, $categoria, $juego, $lang);
			$token = $share['token'];
			$imageCount = (int) $share['image_count'];
			if ($imageCount < 1) {
				$imageCount = 1;
			}
			for ($n = 1; $n <= $imageCount; $n++) {
				$downloadUrls[] = 'flyerShareImage.php?t=' . rawurlencode($token) . '&n=' . $n . '&download=1';
			}
			if ($imageCount > 1) {
				$zipDownloadUrl = 'flyerFacebookShareDownload.php?t=' . rawurlencode($token) . '&label=flyers';
			}
			$launchUiJson = json_encode(
				array(
					'urls' => $downloadUrls,
					'zipUrl' => $zipDownloadUrl,
					'msg' => az_flyer_lang('jsfb39'),
					'msgZip' => az_flyer_lang('jsfb40'),
					'msgProgress' => az_flyer_lang('jsfb41'),
				),
				JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
			);
			if ($launchUiJson === false) {
				$launchUiJson = '{}';
			}
			$Config->Close();
		} catch (Throwable $e) {
			if (isset($Config)) {
				$Config->Close();
			}
			$errorMsg = $e->getMessage();
		}

		az_flyer_share_send_resource_policy_headers();
		header('Content-Type: text/html; charset=UTF-8');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
		echo '<title></title><style>';
		echo 'html,body{margin:0;height:100%;background:#1877F2;}';
		echo '.spinner{position:fixed;inset:0;display:flex;align-items:center;justify-content:center;}';
		echo '.spinner::after{content:"";width:40px;height:40px;border:4px solid rgba(255,255,255,.35);border-top-color:#fff;border-radius:50%;animation:spin .8s linear infinite;}';
		echo '@keyframes spin{to{transform:rotate(360deg);}}';
		echo '.err,.msg{position:fixed;inset:0;display:flex;align-items:center;justify-content:center;padding:20px;color:#fff;font:15px/1.45 sans-serif;text-align:center;box-sizing:border-box;white-space:pre-wrap;}';
		echo '.msg{display:none;max-width:360px;margin:auto;}';
		echo '</style></head><body>';
		if ($errorMsg !== '') {
			echo '<div class="err">' . az_flyer_share_escape_html($errorMsg) . '</div></body></html>';
			exit;
		}
		echo '<div class="spinner" id="azFbSpin"></div>';
		echo '<div class="msg" id="azFbMsg" style="display:none"></div>';
		echo '<script id="azFbUi" type="application/json">' . $launchUiJson . '</script>';
		echo '<script>(function(){var ui={};try{ui=JSON.parse(document.getElementById("azFbUi").textContent||"{}");}catch(e){}';
		echo 'var urls=Array.isArray(ui.urls)?ui.urls:[],zipUrl=ui.zipUrl||"",msg=ui.msg||"",msgZip=ui.msgZip||"",msgProg=ui.msgProgress||"";';
		echo 'var fb=' . json_encode($fbOpenUrl, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) . ';';
		echo 'var fbM=' . json_encode($fbOpenUrlMobile, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) . ';';
		echo 'function isMobile(){return /Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent||"")||((navigator.maxTouchPoints||0)>1&&window.matchMedia("(max-width:900px)").matches);}';
		echo 'function hideSpin(){var s=document.getElementById("azFbSpin");if(s)s.style.display="none";}';
		echo 'function setMsg(t){hideSpin();var m=document.getElementById("azFbMsg");if(m){m.textContent=t||"";m.style.display="flex";}}';
		echo 'function progMsg(cur,tot){return (msgProg||"").replace("%1",String(cur)).replace("%2",String(tot));}';
		echo 'function dlOne(u){var a=document.createElement("a");a.href=u;a.style.display="none";document.body.appendChild(a);a.click();setTimeout(function(){a.remove();},300);}';
		echo 'function openFb(){var u=isMobile()&&fbM?fbM:fb;var w=null;try{w=window.open(u,"azFbNewPost","noopener,noreferrer");}catch(e){}';
		echo 'if(!w||w.closed){try{window.location.href=u;}catch(e2){}}}';
		echo 'function dlSequential(list,gapMs,onDone){var i=0;function step(){if(i>=list.length){onDone();return;}';
		echo 'if(list.length>1)setMsg(progMsg(i+1,list.length));dlOne(list[i]);i+=1;setTimeout(step,gapMs);}step();}';
		echo 'function go(){if(!urls.length){hideSpin();return;}var mob=isMobile();';
		echo 'if(mob&&zipUrl&&urls.length>1){dlOne(zipUrl);setTimeout(function(){openFb();setMsg(msgZip);},2400);return;}';
		echo 'var gap=mob?4000:1000;if(urls.length===1){dlOne(urls[0]);setTimeout(function(){openFb();setMsg(msg);},mob?1800:1100);return;}';
		echo 'dlSequential(urls,gap,function(){openFb();setMsg(msg);});}go();})();</script>';
		echo '</body></html>';
		exit;
	}
}
