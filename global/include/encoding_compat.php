<?php
/**
 * Replacements for deprecated utf8_decode / utf8_encode (PHP 8.2+).
 * Same byte semantics for valid UTF-8 / ISO-8859-1 via mbstring.
 */
declare(strict_types=1);

if (!function_exists('az_utf8_decode')) {
	/**
	 * UTF-8 string → ISO-8859-1 (for legacy FPDF / single-byte output).
	 */
	function az_utf8_decode(mixed $string): string
	{
		if ($string === null || $string === '') {
			return '';
		}
		return mb_convert_encoding((string) $string, 'ISO-8859-1', 'UTF-8');
	}
}

if (!function_exists('az_utf8_encode')) {
	/**
	 * ISO-8859-1 string → UTF-8 (inverse of az_utf8_decode).
	 */
	function az_utf8_encode(mixed $string): string
	{
		if ($string === null || $string === '') {
			return '';
		}
		return mb_convert_encoding((string) $string, 'UTF-8', 'ISO-8859-1');
	}
}
