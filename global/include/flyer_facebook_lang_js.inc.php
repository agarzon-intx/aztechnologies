<?php
/**
 * Expose flyer Facebook UI strings to main.js (include after $lang is loaded).
 */
if (!isset($lang) || !is_array($lang)) {
	$lang = array();
}
$__az_fb_js_keys = array(
	'jsfb28', 'jsfb30', 'jsfb39',
);
$__az_fb_js = array();
foreach ($__az_fb_js_keys as $__k) {
	$__az_fb_js[$__k] = isset($lang[$__k]) ? (string) $lang[$__k] : '';
}
unset($__az_fb_js_keys, $__k);
?>
<script type="text/javascript">var LANG_FLYER_FB = <?php echo json_encode($__az_fb_js, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;</script>
