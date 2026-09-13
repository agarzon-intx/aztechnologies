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
	$__msg_ajax_generic = json_encode($lang['js0002'] ?? '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

	echo "
	var MSG_AJAX_GENERIC = " . $__msg_ajax_generic . ";
	var MSG_PLAYER_CURP_EMPTY = " . json_encode($lang['539-5'] ?? '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) . ";

	function searchPlayerCurpTeam() {
		if ($('#equipo').length && $('#equipo').val()) {
			return $('#equipo').val().toString().split(',')[0];
		}
		return '';
	}

	function searchPlayerCurp(defaultTeam) {
		var curp = $('#curp').val();
		var team = searchPlayerCurpTeam();
		if (!team) {
			team = defaultTeam;
		}
		if (!curp) {
			alert(MSG_PLAYER_CURP_EMPTY);
			return;
		}
		mainLoadingOn();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: 'ajax/Admin/Players/Admin/playerSearchByCurp.php',
			data: {curp: curp, team: team},
			success: function (res) {
				mainLoadingOff();
				if (!res || res.status !== '1') {
					alert((res && res.message) ? res.message : MSG_AJAX_GENERIC);
					return;
				}
				if (res.found === 1 && res.sameTeam === 1) {
					alert(res.message);
					return;
				}
				if (res.found === 1 && res.sameTeam === 0) {
					if (window.confirm(res.message)) {
						mainLoadingOn();
						$.ajax({
							type: 'POST',
							dataType: 'json',
							url: 'ajax/Admin/Players/Admin/playerMoveToTeam.php',
							data: {player: res.player, team: team},
							success: function (moveRes) {
								mainLoadingOff();
								alert((moveRes && moveRes.message) ? moveRes.message : MSG_AJAX_GENERIC);
								if (moveRes && moveRes.status === '1') {
									var cat = moveRes.categoria || res.categoria;
									if (typeof playersManagementAdminShow === 'function' && $('#equipo').length) {
										playersManagementAdminShow(cat, team);
									} else if (typeof playersManagementTeamShow === 'function') {
										playersManagementTeamShow(cat, team);
									}
								}
							},
							error: function() {
								mainLoadingOff();
								alert(MSG_AJAX_GENERIC);
							}
						});
					}
					return;
				}
				alert(res.message);
			},
			error: function() {
				mainLoadingOff();
				alert(MSG_AJAX_GENERIC);
			}
		});
	}

  function searchCURP(cat, team) {
 
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
                                        		mainLoadingOff();
                                        		alert(MSG_AJAX_GENERIC);
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
                    		mainLoadingOff();
                    		alert(MSG_AJAX_GENERIC);
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
                                        		mainLoadingOff();
                                        		alert(MSG_AJAX_GENERIC);
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
                    		mainLoadingOff();
                    		alert(MSG_AJAX_GENERIC);
                    		console.log('Exception:' + exception);
                    	}
                    });
                }else{
                  alert ('Ingresa CURP');
                }
            
            }";
?>