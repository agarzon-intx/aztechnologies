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
	$sessionstat = $fgmembersite->CheckLogin('configAdmin.js.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	Header("content-type: application/x-javascript");

	echo "
		function validateInfo(){
			var error = \"" . $lang['js0000'] . "\";
			var count = 0;
			if($('#leagueName').val().length == 0){
				count++;
				error = error + \"<p>\" + count +  \".- " . $lang['js401'] . "\";
			}
			if($('#latitude').val().length == 0){
				count++;
				error = error + \"<p>\" + count +  \".- " . $lang['js402'] . "\";
			}
			if($('#longitude').val().length == 0){
				count++;
				error = error + \"<p>\" + count +  \".- " . $lang['js403'] . "\";
			}
			if(count>0){
				alert(error);
				return false;
			}
			configManagementInfoSave($('#leagueName').val(), $('#latitude').val(), $('#longitude').val(), 'LeagueLogo', $('#logox').val(), $('#logoy').val(), $('#myLogoFileName').val(), $('#colorHEdit').val(), $('#colorBEdit').val(), $('#colorFEdit').val());
		}
		
		function validateAlert(){
			$('#editor2').val(CKEDITOR.instances.editor2.getData());
			var error = \"" . $lang['js0000'] . "\";
			var count = 0;
			if(count>0){
				alert(error);
				return false;
			}
			configManagementAlertSave($('#editor2').val());
		}
		
		function validateGeneral(){
			var error = \"" . $lang['js0000'] . "\";
			var count = 0;
			var EmpatesPenales = 0;
			if ($('#EmpatesPenales').is(':checked')){
			  EmpatesPenales = 1;
			}
			var JugadorJugado = 0;
			if ($('#JugadorJugado').is(':checked')){
			  JugadorJugado = 1;
			}
			var JuegoCedulas = 0;
			if ($('#JuegoCedulas').is(':checked')){
			  JuegoCedulas = 1;
			}
			var MarcadorArbitro = 0;
			if ($('#MarcadorArbitro').is(':checked')){
			  MarcadorArbitro = 1;
			}
			var MarcadorFecha = 0;
			if ($('#MarcadorFecha').is(':checked')){
			  MarcadorFecha = 1;
			}
			var MarcadorDiaDefault = $('#MarcadorDiaDefault').val();
			var JornadaCedulas = 0;
			if ($('#JornadaCedulas').is(':checked')){
			  JornadaCedulas = 1;
			}
			var columnid = 0;
			if ($('#columnid').is(':checked')){
			  columnid = 1;
			}
			var ByeWeekPoints = 0;
			if ($('#ByeWeekPoints').is(':checked')){
			  ByeWeekPoints = 1;
			}
			var juegoSemanal = 0;
			if ($('#juegoSemanal').is(':checked')){
			  juegoSemanal = 1;
			}
			var tressets = 0;
			if ($('#tressets').is(':checked')){
			  tressets = 1;
			}
			var perfilJugador = 0;
			if ($('#perfilJugador').is(':checked')){
			  perfilJugador = 1;
			}
			var jugadoresApellidos1 = 0;
			if ($('#jugadoresApellidos1').is(':checked')){
			  jugadoresApellidos1 = 1;
			}
			var juegosxnombre = 0;
			if ($('#juegosxnombre').is(':checked')){
			  juegosxnombre = 1;
			}
			var coachjuegos = 0;
			if ($('#coachjuegos').is(':checked')){
			  coachjuegos = 1;
			}
			var coachjuegos = 0;
			if ($('#coachjuegos').is(':checked')){
			  coachjuegos = 1;
			}
			var tarjetaCambios = 0;
			if ($('#tarjetaCambios').is(':checked')){
			  tarjetaCambios = 1;
			}
			var horario = $('#MarcadorHoraDefault').val();
			var horario2 = $('#coachjuegoshorafinal').val();
			if(count>0){
				alert(error);
				return false;
			}
			configManagementGeneralSave($('#lenguaje').val(), EmpatesPenales, JugadorJugado, JuegoCedulas, MarcadorArbitro, MarcadorFecha, MarcadorDiaDefault, JornadaCedulas, columnid, ByeWeekPoints, 
			    $('#ByeWeekPointsGoals').val(), juegoSemanal, tressets, perfilJugador, jugadoresApellidos1, juegosxnombre, coachjuegos, $('#coachjuegosdiainicial').val(), $('#coachjuegosdiafinal').val(), horario, horario2, tarjetaCambios, $('#VBByeWeekSets').val(), $('#VBByeWeekPoints').val(), $('#VBByeWeekSetPoints').val());
		}"
		
?>