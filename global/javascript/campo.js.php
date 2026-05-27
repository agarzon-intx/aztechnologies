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
	$sessionstat = $fgmembersite->CheckLogin('campo.js.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	Header("content-type: application/x-javascript");
	
	echo "
		function validateFieldAdd(){
			var error = \"" . $lang['js0000'] . "\";
			var count = 0;
			if($('#descripcion').val().length == 0){
				count++;
				error = error + '\\n' + count +  '.- " . $lang['js420'] . "';
			}
			if($('#latitud').val().length == 0){
				count++;
				error = error + '\\n' + count +  '.- " . $lang['js421'] . "';
			}
			if(count>0){
				alert(error);
				return false;
			}
			fieldManagementCreateSave($('#descripcion').val(), $('#latitud').val(), $('#longitud').val(), $('#zoom').val(), $('#google').val());
		}
		
		function validateFieldEdit(fieldid){
			var error = \"" . $lang['js0000'] . "\";
			var count = 0;
			if($('#descripcion2').val().length == 0){
				count++;
				error = error + '\\n' + count +  '.- " . $lang['js420'] . "';
			}
			if($('#latitud2').val().length == 0){
				count++;
				error = error + '\\n' + count +  '.- " . $lang['js421'] . "';
			}
			if(count>0){
				alert(error);
				return false;
			}
			fieldManagementEditSave(fieldid, $('#descripcion2').val(), $('#latitud2').val(), $('#longitud2').val(), $('#zoom2').val(), $('#google2').val());
		}";
?>