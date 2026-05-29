<?php
/**
 * Cache-bust query param for local script tags in index.php (new value each request).
 */
if (!isset($JS_CACHE_BUST)) {
	$JS_CACHE_BUST = time();
}

if (!function_exists('index_js_src')) {
	function index_js_src(string $path): string
	{
		global $JS_CACHE_BUST;
		if ($path === '' || preg_match('#^(https?:)?//#i', $path)) {
			return $path;
		}
		$sep = (strpos($path, '?') !== false) ? '&' : '?';
		return $path . $sep . 'v=' . $JS_CACHE_BUST;
	}
}
