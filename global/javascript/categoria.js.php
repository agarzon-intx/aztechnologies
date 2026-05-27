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
	$sessionstat = $fgmembersite->CheckLogin('categoria.js.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	Header("content-type: application/x-javascript");

	echo "
		function validateCategoryAdd(){
			var error = \"" . $lang['js0000'] . "\";
			var count = 0;
			if($('#descripcion').val().length == 0){
				count++;
				error = error + '\\n' + count +  '.- " . $lang['js50'] . "';
			}
			if($('#orden').val() < 0 || $('#orden').val().length == 0){
				count++;
				error = error + '\\n' + count +  '.- " . $lang['js51'] . "';
			}
			if($('#Inicial').val() < 0 || $('#Inicial').val().length == 0){
				count++;
				error = error + '\\n' + count +  '.- " . $lang['js52'] . "';
			}
			if($('#Final').val() < 0 || $('#Final').val().length == 0){
				count++;
				error = error + '\\n' + count +  '.- " . $lang['js53'] . "';
			}
			if(count>0){
				alert(error);
				return false;
			}
			categoryManagementCreateSave($('#descripcion').val(), $('#orden').val(), $('#Inicial').val(), $('#Final').val(), $('#Color').val(), $('#calendario').val(), $('#rondas').val());
		}
		
		
		function validateCategoryEdit(id){
			var error = \"" . $lang['js0000'] . "\";
			var count = 0;
			if($('#descripcion2').val().length == 0){
				count++;
				error = error + '\\n' + count +  '.- " . $lang['js50'] . "';
			}
			if($('#orden2').val() < 0 || $('#orden2').val().length == 0){
				count++;
				error = error + '\\n' + count +  '.- " . $lang['js51'] . "';
			}
			if($('#Inicial2').val() < 0 || $('#Inicial2').val().length == 0){
				count++;
				error = error + '\\n' + count +  '.- " . $lang['js52'] . "';
			}
			if($('#Final2').val() < 0 || $('#Final2').val().length == 0){
				count++;
				error = error + '\\n' + count +  '.- " . $lang['js53'] . "';
			}
			if(count>0){
				alert(error);
				return false;
			}
			categoryManagementEditSave(id, $('#descripcion2').val(), $('#orden2').val(), $('#Inicial2').val(), $('#Final2').val(), $('#Color2').val(), $('#calendario2').val(), $('#rondas2').val())
		}";
?>