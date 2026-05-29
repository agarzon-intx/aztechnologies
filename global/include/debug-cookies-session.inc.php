<?php
/**
 * Debug view: $_COOKIE and $_SESSION (HTML).
 * Included from each site’s debug-cookies-session.php in the site root.
 */

if (!function_exists('debug_cookies_session_allowed')) {
	function debug_cookies_session_allowed(): bool
	{
		if (getenv('ALLOW_DEBUG_COOKIES_SESSION') === '1') {
			return true;
		}
		$host = (string) ($_SERVER['HTTP_HOST'] ?? '');
		if ($host !== '' && preg_match('/\.test$/i', $host)) {
			return true;
		}
		$ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
		return in_array($ip, ['127.0.0.1', '::1'], true);
	}
}

if (!function_exists('debug_cookies_session_h')) {
	function debug_cookies_session_h($value): string
	{
		if ($value === null) {
			return '';
		}
		if (is_bool($value)) {
			return $value ? 'true' : 'false';
		}
		if (is_scalar($value)) {
			return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
		}
		$json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
		if ($json === false) {
			return htmlspecialchars(print_r($value, true), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
		}
		return htmlspecialchars($json, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
	}
}

if (!function_exists('debug_cookies_session_render_table')) {
	function debug_cookies_session_render_table(string $title, array $data): void
	{
		echo '<section class="card">';
		echo '<h2>' . debug_cookies_session_h($title) . ' <span class="count">(' . count($data) . ')</span></h2>';
		if ($data === []) {
			echo '<p class="empty">(empty)</p>';
		} else {
			echo '<table><thead><tr><th>Key</th><th>Value</th></tr></thead><tbody>';
			foreach ($data as $key => $value) {
				echo '<tr><td class="key">' . debug_cookies_session_h($key) . '</td>';
				echo '<td class="val"><pre>' . debug_cookies_session_h($value) . '</pre></td></tr>';
			}
			echo '</tbody></table>';
		}
		echo '</section>';
	}
}

if (!debug_cookies_session_allowed()) {
	http_response_code(403);
	header('Content-Type: text/plain; charset=UTF-8');
	echo "Forbidden. Use *.test, localhost, or set ALLOW_DEBUG_COOKIES_SESSION=1 on the server.\n";
	return;
}

header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

$siteRoot = defined('APP_SITE_BOOTSTRAP_ROOT') ? APP_SITE_BOOTSTRAP_ROOT : (__DIR__ . '/../..');
$siteName = basename((string) realpath($siteRoot));

?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Cookies &amp; Session — <?php echo debug_cookies_session_h($siteName); ?></title>
	<style>
		body { font-family: system-ui, sans-serif; margin: 1rem; background: #f4f6f8; color: #1a1a1a; }
		h1 { font-size: 1.25rem; margin: 0 0 0.5rem; }
		.meta { color: #555; font-size: 0.9rem; margin-bottom: 1rem; }
		.card { background: #fff; border: 1px solid #d0d7de; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; }
		.card h2 { font-size: 1rem; margin: 0 0 0.75rem; }
		.count { font-weight: normal; color: #666; font-size: 0.85rem; }
		table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
		th, td { border: 1px solid #e1e4e8; padding: 0.4rem 0.6rem; vertical-align: top; text-align: left; }
		th { background: #f6f8fa; }
		td.key { width: 28%; font-family: ui-monospace, monospace; word-break: break-all; }
		td.val pre { margin: 0; white-space: pre-wrap; word-break: break-word; font-family: ui-monospace, monospace; font-size: 0.8rem; }
		.empty { color: #666; margin: 0; }
	</style>
</head>
<body>
	<h1>Cookies &amp; Session</h1>
	<p class="meta">
		Site: <strong><?php echo debug_cookies_session_h($siteName); ?></strong>
		· PHP session id: <code><?php echo debug_cookies_session_h(session_id()); ?></code>
		· <?php echo debug_cookies_session_h(date('Y-m-d H:i:s T')); ?>
	</p>
<?php
debug_cookies_session_render_table('$_COOKIE', $_COOKIE);
debug_cookies_session_render_table('$_SESSION', $_SESSION);

$sessionMeta = [
	'session_status' => session_status(),
	'session_name' => session_name(),
	'session_save_path' => session_save_path(),
	'session_module' => session_module_name(),
];
if (function_exists('session_get_cookie_params')) {
	foreach (session_get_cookie_params() as $k => $v) {
		$sessionMeta['cookie_' . $k] = $v;
	}
}
debug_cookies_session_render_table('Session metadata', $sessionMeta);
?>
</body>
</html>
