<?php
    /**
     * Replacement for filter_var(..., FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)
     * (deprecated/removed in PHP 8.1+). Strips HTML tags then removes C0 controls, DEL, and high bytes.
     */
    function filterSanitizeStringCompat($value) {
        $value = strip_tags((string) $value);
        return preg_replace('/[\x00-\x1F\x7F\x80-\xFF]/', '', $value);
    }

    function SanitizeUsername($username) {
        return preg_replace('/[^a-z0-9]/s', "", filterSanitizeStringCompat(strtolower((string) $username)));
    }

    function SanitizeHex($key) {
        return preg_replace('/[^a-f0-9]/s', "", filterSanitizeStringCompat($key));
    }

    function SanitizeInteger($number) {
        return preg_replace('/[^0-9-]/s', "", filter_var($number,FILTER_SANITIZE_NUMBER_INT));
    }

    function SanitizeFloat($value) {
        return preg_replace('/[^0-9.]/s', "", filter_var($value,FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
    }

    /*
    function SanitizeText($text) {
            return preg_replace('/[^a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ_\. ]/s', "", filterSanitizeStringCompat($text));
    }
    */

    function SanitizeText($text) {
            return preg_replace('/[^a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ_\.\- ]/s', "", $text);
    }

    /*
    function SanitizeTextComa($text) {
            return preg_replace('/[^a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ_\., ]/s', "", filterSanitizeStringCompat($text));
    }
    */

    function SanitizeTextComa($text) {
            return preg_replace('/[^a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ_\., \-]/s', "",$text);
    }
    
    function SanitizeTime($text) {
            return preg_replace('/[^a-zA-Z0-9_\. :]/s', "", filterSanitizeStringCompat($text));
    }
    
    function SanitizeLang($text) {
            return preg_replace('/[^a-z-]/s', "", filterSanitizeStringCompat($text));
    }
    
    function SanitizeFileName($text) {
            return preg_replace('/[^a-zA-Z0-9\.-]/s', "", filterSanitizeStringCompat($text));
    }
    
    function SanitizeNonNumericText($text) {
        return preg_replace('/[^a-zA-Z]/s', "", filterSanitizeStringCompat($text));
    }

    function SanitizeRealName($text) {
        return preg_replace('/[^a-zA-Z\s+]/s', "", filterSanitizeStringCompat($text));
    }

    function SanitizeBrowserName($text) {
        return preg_replace('/[^a-zA-Z0-9_\. -]/s', "", filterSanitizeStringCompat($text));
    }

    function SanitizeEmail($email) {
        return filter_var(strtolower((string) $email), FILTER_SANITIZE_EMAIL);
    }

	function sanitizeHexColor($color = '#FFFFFF', $hash = true) {

		// Remove any spaces and special characters before and after the string
		$color = trim( $color );

		// Remove any trailing '#' symbols from the color value
		$color = str_replace( '#', '', $color );

		// If the string is 6 characters long then use it in pairs.
		if ( 3 == strlen( $color ) ) {
			$color = substr( $color, 0, 1 ) . substr( $color, 0, 1 ) . substr( $color, 1, 1 ) . substr( $color, 1, 1 ) . substr( $color, 2, 1 ) . substr( $color, 2, 1 );
		}

		$substr = array();
		for ( $i = 0; $i <= 5; $i++ ) {
			$default    = ( 0 == $i ) ? 'F' : ( $substr[$i-1] );
			$substr[$i] = substr( $color, $i, 1 );
			$substr[$i] = ( false === $substr[$i] || ! ctype_xdigit( $substr[$i] ) ) ? $default : $substr[$i];
		}
		$hex = implode( '', $substr );

		return ( ! $hash ) ? $hex : '#' . $hex;

	}
?>