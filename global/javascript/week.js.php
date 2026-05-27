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
	$sessionstat = $fgmembersite->CheckLogin('week.js.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	Header("content-type: application/x-javascript");

	echo "
	
		function DateChange(){
			var kk = $('#Fecha').val();
			$('#Inicio').val(moment(kk, 'YYYY-MM-DD').day(0).format('YYYY-MM-DD'));
			$('#Fin').val(moment(kk, 'YYYY-MM-DD').day(6).format('YYYY-MM-DD'));
		}
		
		function DateChange2(){
			var kk = $('#Fecha2').val();
			$('#Inicio2').val(moment(kk, 'YYYY-MM-DD').day(0).format('YYYY-MM-DD'));
			$('#Fin2').val(moment(kk, 'YYYY-MM-DD').day(6).format('YYYY-MM-DD'));
		}
		
		function validateWeekAdd(Calendar){
			var error = \"" . $lang['js0000'] . "\";
			var count = 0;
			if($('#DescCorta').val().length == 0){
				count++;
				error = error + '\\n' + count +  '.- " . $lang['866'] . "';
			}
			if($('#Desc').val() < 0 || $('#Desc').val().length == 0){
				count++;
				error = error + '\\n' + count +  '.- " . $lang['865'] . "';
			}
			if($('#Orden').val() < 0 || $('#Orden').val().length == 0){
				count++;
				error = error + '\\n' + count +  '.- " . $lang['867'] . "';
			}
			if($('#Fecha').val().length == 0){
				count++;
				error = error + '\\n' + count +  '.- " . $lang['86'] . "';
			}
			if(count>0){
				alert(error);
				return false;
			}
			weekManagementCreateSave($('#Desc').val(), $('#DescCorta').val(), $('#Orden').val(), $('#Fecha').val(), $('#Inicio').val(), $('#Fin').val(), Calendar, $('#tipo').val());
		}
		
		
		function validateWeekEdit(id, Calendar){
			var error = \"" . $lang['js0000'] . "\";
			var count = 0;
			if($('#DescCorta2').val().length == 0){
				count++;
				error = error + '\\n' + count +  '.- " . $lang['866'] . "';
			}
			if($('#Desc2').val() < 0 || $('#Desc2').val().length == 0){
				count++;
				error = error + '\\n' + count +  '.- " . $lang['865'] . "';
			}
			if($('#Orden2').val() < 0 || $('#Orden2').val().length == 0){
				count++;
				error = error + '\\n' + count +  '.- " . $lang['867'] . "';
			}
			if($('#Fecha2').val().length == 0){
				count++;
				error = error + '\\n' + count +  '.- " . $lang['86'] . "';
			}
			if(count>0){
				alert(error);
				return false;
			}
			weekManagementEditSave(id, $('#Desc2').val(), $('#DescCorta2').val(), $('#Orden2').val(), $('#Fecha2').val(), $('#Inicio2').val(), $('#Fin2').val(), Calendar, $('#tipo').val());
		}";
?>