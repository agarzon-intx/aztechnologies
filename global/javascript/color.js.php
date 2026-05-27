<?php
	session_start();
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

	error_reporting(0);
$__APP_SITE_PATHS_START__ = __DIR__;
$__app_here = __DIR__;
for ($__i = 0, $__prev = null; $__i < 24; $__i++) {
	$__base = ($__i === 0) ? $__app_here : dirname($__app_here, $__i);
	if ($__base === $__prev) {
		break;
	}
	$__prev = $__base;
	$__inc = $__base . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'app_site_paths.inc.php';
	if (is_readable($__inc)) {
		require_once $__inc;
		break;
	}
}
unset($__i, $__prev, $__base, $__inc, $__app_here);

	require("membersite_config.php");
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('colors.js.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	Header("content-type: application/x-javascript");

	echo "
		function validateColorAgregar(){
			var error = \"" . $lang['js0000'] . "\";
			var count = 0;
			if($(\"#colorDescripcion\").val().length == 0){
				count++;
				error = error + \"<p>\" + count +  \".- " . $lang['js80'] . "\";
			}
			if(!(/^#[0-9A-F]{6}$/i.test($(\"#colorEditText\").val()))){
				count++;
				error = error + \"<p>\" + count +  \".- " . $lang['js81'] . "\";
			}
			if(count>0){
				$(\"#errorColor\").html(error);
				return false;
			}
			colorsManagementCreateSave($(\"#colorDescripcion\").val(), $(\"#colorEditText\").val().toLowerCase());
		}
		
		function validateColorActualizar(colorid){
			var error = \"" . $lang['js0000'] . "\";
			var count = 0;
			if($(\"#colorDescripcionE\").val().length == 0){
				count++;
				error = error + \"<p>\" + count +  \".- " . $lang['js80'] . "\";
			}
			if(!(/^#[0-9A-F]{6}$/i.test($(\"#colorEditTextE\").val()))){
				count++;
				error = error + \"<p>\" + count +  \".- " . $lang['js81'] . "\";
			}
			if(count>0){
				$(\"#errorColorE\").html(error);
				return false;
			}
			colorsManagementUpdateSave(colorid, $(\"#colorDescripcionE\").val(), $(\"#colorEditTextE\").val().toLowerCase());
		}
		
		function limpiarColorActualizar(){
			$(\"#colorsManagementEdit\").css('display', 'none');
			$(\"#colorsManagementList\").css('display', 'block');
			$(\"#colorsManagementEdit\").html('');
		}
		
		function limpiarColorAgregar(){
			$(\"#colorsManagementCreate\").css('display', 'none');
			$(\"#colorsManagementList\").css('display', 'block');
			$(\"#colorsManagementCreate\").html('');
		}";
?>