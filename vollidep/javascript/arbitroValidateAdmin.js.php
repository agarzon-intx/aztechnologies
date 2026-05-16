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
	$sessionstat = $fgmembersite->CheckLogin('ArbitroValidateAdmin.js.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	Header("content-type: application/x-javascript");

	echo "
	function printID1(){
				if($('#printRegisters1').val() != ''){ 
					$('#downloadPlayersIDBtn1').attr('href', $('#printRegisters1').val());
					$('#downloadPlayersIDBtn1')[0].click();
				}
			}
	
			function printID2(){
				if($('#printRegisters2').val() != ''){ 
					$('#downloadPlayersIDBtn2').attr('href', $('#printRegisters2').val());
					$('#downloadPlayersIDBtn2')[0].click();
				}
			}
			
			function printIDM1(){
				if($('#printRegistersM1').val() != ''){ 
					$('#downloadPlayersIDBtnM1').attr('href', $('#printRegistersM1').val());
					$('#downloadPlayersIDBtnM1')[0].click();
				}
			}
			
			function printIDM2(){
				if($('#printRegistersM2').val() != ''){ 
					$('#downloadPlayersIDBtnM2').attr('href', $('#printRegistersM2').val());
					$('#downloadPlayersIDBtnM2')[0].click();
				}
			}
			
			function validateEmail(email) {
				var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
				return re.test(email);
			}
			
			function validateReferee(){
				var error = \"" . $lang['js0000'] . "\";
				var count = 0;
				var curp = '';
				if($('#nombre').val().length == 0){
					count++;
					error = error + '\\n' + count +  '.- " . $lang['js900'] . "';
				}
				if($('#apellidop').val().length == 0){
					count++;
					error = error + '\\n' + count +  '.- " . $lang['js901'] . "';
				}
					if($('#apellidom').val().length == 0){
					count++;
					error = error + '\\n' + count +  '.- " . $lang['js901-1'] . "';
				}
				if($('#fechanac').val().length == 0){
					count++;
					error = error + '\\n' + count +  '.- " . $lang['js902'] . ": ' + fechanac.value;
				}
			
				if($('#telefono').val().length == 0){
					count++;
					error = error + '\\n' + count +  '.- " . $lang['js904'] . "';
				}
				if($('#correo').val().length == 0){
					count++;
					error = error + '\\n' + count +  '.- " . $lang['js905'] . "';
				}else{
					if(!validateEmail($('#correo').val())){
						count++;
						error = error + '\\n' + count +  '.- " . $lang['js906'] . "';			
					}
				}
				
				if(count>0){
					alert(error);
					return false;
				}else{
					var sexoSel = 'H';
					if($('#sexo').val() == '1'){
						sexoSel = 'M';
					}
					var b = $('#fechanac').val().split(/\D/);
					var dateEntered = new Date(b[0], --b[1], b[2]);
					var datearray = [dateEntered.getDate(), dateEntered.getMonth()+1, dateEntered.getFullYear()]
					curp = generaCurp({
					  nombre            : $('#nombre').val(),
					  apellido_paterno  : $('#apellidop').val(),
					  apellido_materno  : $('#apellidom').val(),
					  sexo              : sexoSel,
					  estado            : 'DF',
					  fecha_nacimiento  : datearray
					});
					if($('#curp').val().substring(0, 11) != curp.substring(0, 11)){
						count++;
						error = error + '\\n' + count +  '.- " . $lang['js909'] . "';
						alert(error);
						return false;
					}
				}
			    refereeManagementAdminCreateReferee($('#nombre').val(), $('#apellidop').val(), $('#apellidom').val(), $('#apodo').val(), $('#fechanac').val(), $('#telefono').val(), $('#sexo').val(), $('#correo').val(), $('#curp').val(), $('#comentarios').val(), $('#historial').val(), $('#cursos').val(), $('#Validado').val(), $('#Estatus2').val(), $('#myFotoFileName').val(), $('#myID11FileName').val(), $('#myID12FileName').val(), $('#myFirmaFileName').val(), $('#type').val());
			}

			function limpiarA(){
				$('#refereeManagementList').toggle();
				$('#refereeManagementCreate').toggle();
				$('#refereeManagementCreate').html('');
			}

			function limpiarAE(){
				$('#refereeManagementList').toggle();
				$('#refereeManagementEdit').toggle();
				$('#refereeManagementEdit').html('');
			}
			
			function validateRefereeE(refereeid){
				var error = \"" . $lang['js0000'] . "\";
				var count = 0;
				var curp = '';
				if($('#nombreE').val().length == 0){
					count++;
					error = error + '\\n' + count +  '.- " . $lang['js900'] . "';
				}
				if($('#apellidopE').val().length == 0){
					count++;
					error = error + '\\n' + count +  '.- " . $lang['js901'] . "';
				}
				if($('#apellidomE').val().length == 0){
					count++;
					error = error + '\\n' + count +  '.- " . $lang['js901-1'] . "';
				}
				if($('#fechanacE').val().length == 0){
					count++;
					error = error + '\\n' + count +  '.- " . $lang['js902'] . ": ' + fechanac.value;
				}
			
				if($('#telefonoE').val().length == 0){
					count++;
					error = error + '\\n' + count +  '.- " . $lang['js904'] . "';
				}
				if($('#correoE').val().length == 0){
					count++;
					error = error + '\\n' + count +  '.- " . $lang['js905'] . "';
				}else{
					if(!validateEmail($('#correoE').val())){
						count++;
						error = error + '\\n' + count +  '.- " . $lang['js906'] . "';
					}
				}
				
				if(count>0){
					alert(error);
					return false;
				}else{
					var sexoSel = 'H';
					if($('#sexoE').val() == '1'){
						sexoSel = 'M';
					}
					var b = $('#fechanacE').val().split(/\D/);
					var dateEntered = new Date(b[0], --b[1], b[2]);
					var datearray = [dateEntered.getDate(), dateEntered.getMonth()+1, dateEntered.getFullYear()]
					curp = generaCurp({
					  nombre            : $('#nombreE').val(),
					  apellido_paterno  : $('#apellidopE').val(),
					  apellido_materno  : $('#apellidomE').val(),
					  sexo              : sexoSel,
					  estado            : 'DF',
					  fecha_nacimiento  : datearray
					});
					if($('#curpE').val().substring(0, 11) != curp.substring(0, 11)){
						count++;
						error = error + '\\n' + count +  '.- " . $lang['js909'] . "';
						alert(error);
						return false;
					}
				}
				refereeManagementAdminEditReferee(refereeid,  $('#nombreE').val(), $('#apellidopE').val(), $('#apellidomE').val(), $('#apodoE').val(), $('#fechanacE').val(),  $('#telefonoE').val(), $('#sexoE').val(), $('#correoE').val(), $('#curpE').val(), $('#comentariosE').val(), $('#historialE').val(), $('#cursosE').val(), $('#ValidadoE').val(), $('#Estatus2E').val(), $('#myFotoFileNameE').val(), $('#myID11FileNameE').val(), $('#myID12FileNameE').val(), $('#myFirmaFileNameE').val());
			}
			
			
			
			
			
			function playersManagementAdminAddPrintList(){
				var count = 0;
				var insert = 0;
				var list = '';
				$('.playersManagementAdminImprimir').each(function(index ) {
					if ($( this ).is(':checked')){
						if(count === 0){
							list = $( this ).val();
						}else{
							list = list + ',' + $( this ).val();
						}
						count++;
					}
				});			
				var xmlhttp;
				if (window.XMLHttpRequest){
					// code for IE7+, Firefox, Chrome, Opera, Safari
					xmlhttp=new XMLHttpRequest();
				}else{// code for IE6, IE5
					xmlhttp=new ActiveXObject('Microsoft.XMLHTTP');
				}
				xmlhttp.onreadystatechange=function(){
					if (xmlhttp.readyState==4 && xmlhttp.status==200){
						insert--;
						if(insert === 0 ){
							alert('" . $lang['951'] . "');
						}
					}
				}
				//console.log('GeneratePrintList.php?list'+list+'&clear=0');
			
				xmlhttp.open('POST','ajax/Admin/Players/Admin/playersManagementGeneratePrintList.php',true);
				xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				insert++;
				xmlhttp.send('list='+list+'&clear=0');
			}
			
			function updatePrintList(){
				var list = '';
				var count = 0;
				$('.playersManagementAdminImprimirUpdate').each(function(index ) {
					if ($( this ).is(':checked')){
						if(count === 0){
							list = $( this ).val();
						}else{
							list = list + ',' + $( this ).val();
						}
						count++;
					}
				});			
				var xmlhttp;
				if (window.XMLHttpRequest){
					// code for IE7+, Firefox, Chrome, Opera, Safari
					xmlhttp=new XMLHttpRequest();
				}else{// code for IE6, IE5
					xmlhttp=new ActiveXObject('Microsoft.XMLHTTP');
				}
				xmlhttp.onreadystatechange=function(){
					if (xmlhttp.readyState==4 && xmlhttp.status==200){
						playersManagementAdminShowPrintList();
					}
				}
				xmlhttp.open('POST','ajax/Admin/Players/Admin/playersManagementGeneratePrintList.php',true);
				xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				xmlhttp.send('list='+list+'&clear=1');
			}
			
			function clearPrintList(){
				var xmlhttp;
				if (window.XMLHttpRequest){
					// code for IE7+, Firefox, Chrome, Opera, Safari
					xmlhttp=new XMLHttpRequest();
				}else{// code for IE6, IE5
					xmlhttp=new ActiveXObject('Microsoft.XMLHTTP');
				}
				xmlhttp.onreadystatechange=function(){
					if (xmlhttp.readyState==4 && xmlhttp.status==200){
						playersManagementAdminShowPrintList();
					}
				}				
				xmlhttp.open('POST','ajax/Admin/Players/Admin/playersManagementGeneratePrintList.php',true);
				xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				xmlhttp.send('list=&clear=1');
			}";
?>