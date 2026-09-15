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
