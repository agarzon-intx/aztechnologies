<?php
/**
 * Replacements for deprecated utf8_decode / utf8_encode (PHP 8.2+).
 * Decoding targets Windows-1252, the encoding of FPDF's core fonts.
 */
declare(strict_types=1);

if (!function_exists('az_utf8_decode')) {
	/**
	 * UTF-8 string → Windows-1252 (for legacy FPDF / single-byte output).
	 * Input that is not valid UTF-8 is assumed to be single-byte already and returned unchanged,
	 * so decoding twice does not turn accented letters into "?".
	 */
	function az_utf8_decode(mixed $string): string
	{
		if ($string === null || $string === '') {
			return '';
		}
		$string = (string) $string;
		if (!mb_check_encoding($string, 'UTF-8')) {
			return $string;
		}
		return mb_convert_encoding($string, 'Windows-1252', 'UTF-8');
	}
}

if (!function_exists('az_pdf_upper')) {
	/**
	 * Upper-case text for FPDF output (Windows-1252), accented letters included.
	 * Accepts UTF-8 or single-byte input; the optional encoding argument is ignored.
	 */
	function az_pdf_upper(mixed $string, ?string $encoding = null): string
	{
		if ($string === null || $string === '') {
			return '';
		}
		$string = (string) $string;
		if (!mb_check_encoding($string, 'UTF-8')) {
			$string = mb_convert_encoding($string, 'UTF-8', 'Windows-1252');
		}
		return mb_convert_encoding(mb_strtoupper($string, 'UTF-8'), 'Windows-1252', 'UTF-8');
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
