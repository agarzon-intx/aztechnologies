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
	$sessionstat = $fgmembersite->CheckLogin('avisos.js.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	Header("content-type: application/x-javascript");

	echo "  function searchCURP(cat, team) {
 
                var curp = $('#curp').val();
                var team = $('select[name=equipo] option').filter(':selected').val().split(',')[0];
            
                if (curp != ''){
                
                    mainLoadingOn();
                    $.ajax({
                    	type: 'POST',
                    	dataType: 'json',
                    	url: 'ajax/Admin/Players/Admin/playerValidation.php',
                    	data: {curp: curp, 'team':team},
                    	success: function (res1) {
                    		mainLoadingOff();
                    		if (res1.status === '1') {
                    	        alert( res1.mensaje1 );
                    	       ;
                    	        if(res1.JugadorEXiste === 1){
                        	        if (window.confirm('Deseas registrar el Jugador(a)..?')) {
                                        /*$.post('ajax/Admin/Players/Admin/playerManagementNewAutomatic.php',{'curp':curp, 'team':team} , 
                                            function( data ) {
                                            alert(data);
                                            playersManagementAdminCategoryShow();
                                            console.log(data);
                                        });
                                        */
                                        $.ajax({
                                        	type: 'POST',
                                        	dataType: 'json',
                                        	url: 'ajax/Admin/Players/Admin/playerManagementNewAutomatic.php',
                                        	data: {curp: curp, team: team},
                                        	success: function (res) {
                                        		mainLoadingOff();
                                        		if (res.status === '1') {
                                        	        alert(res.dataPlayerMessage);
                                                    playersManagementAdminShow(res1.categoria,team);
                                                };
                                        	},
                                        	error: function(jqxhr, status, exception) {
                                        		console.log('Exception:' + exception);
                                        	}
                                        });
                                    } else {
                                     
                                    }
                    	        }
                    			console.log(res.JugadorEXiste);
                    			console.log(res.mensaje1);
                    			console.log(res.equipo);
                    			console.log(res.categoria);
                    			console.log(res.sql);
                    		}else{
                    		 alert ('Continuar con el registro');
                    	    }
                    	},
                    	error: function(jqxhr, status, exception) {
                    		console.log('Exception:' + exception);
                    	}
                    });
                }else{
                  alert ('Ingresa CURP');
                }
            
            }
            
            function searchCURPT(cat, team) {
 
                var curp = $('#curp').val();
                var team = $('select[name=equipo] option').filter(':selected').val().split(',')[0];
            
                if (curp != ''){
                
                    mainLoadingOn();
                    $.ajax({
                    	type: 'POST',
                    	dataType: 'json',
                    	url: 'ajax/Admin/Players/Admin/playerValidation.php',
                    	data: {curp: curp, 'team':team},
                    	success: function (res1) {
                    		mainLoadingOff();
                    		if (res1.status === '1') {
                    	        alert( res1.mensaje1 );
                    	       ;
                    	        if(res1.JugadorEXiste === 1){
                        	        if (window.confirm('Deseas registrar el Jugador(a)..?')) {
                                        /*$.post('ajax/Admin/Players/Admin/playerManagementNewAutomatic.php',{'curp':curp, 'team':team} , 
                                            function( data ) {
                                            alert(data);
                                            playersManagementTeamCategoryShow();
                                            console.log(data);
                                        });
                                        */
                                        $.ajax({
                                        	type: 'POST',
                                        	dataType: 'json',
                                        	url: 'ajax/Admin/Players/Admin/playerManagementNewAutomatic.php',
                                        	data: {curp: curp, team: team},
                                        	success: function (res) {
                                        		mainLoadingOff();
                                        		if (res.status === '1') {
                                        	        alert(res.dataPlayerMessage);
                                                    playersManagementTeamShow(res1.categoria,team);
                                                };
                                        	},
                                        	error: function(jqxhr, status, exception) {
                                        		console.log('Exception:' + exception);
                                        	}
                                        });
                                    } else {
                                     
                                    }
                    	        }
                    			console.log(res1.JugadorEXiste);
                    			console.log(res1.mensaje1);
                    			console.log(res1.equipo);
                    			console.log(res1.categoria);
                    			console.log(res1.sql);
                    		}else{
                    		 alert ('Continuar con el registro');
                    	    }
                    	},
                    	error: function(jqxhr, status, exception) {
                    		console.log('Exception:' + exception);
                    	}
                    });
                }else{
                  alert ('Ingresa CURP');
                }
            
            }";
?>