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
	$sessionstat = $fgmembersite->CheckLogin('jorndaAmin.js.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	Header("content-type: application/x-javascript");
	$__msg_ajax_generic = json_encode($lang['js0002'] ?? '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

	echo "
		var MSG_AJAX_GENERIC = " . $__msg_ajax_generic . ";
		function loadVisitanteAgregar(Team){
			//console.log('loadVisitanteAgregar ' + Team);
			mainLoadingOn();
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: 'ajax/Admin/Games/weekAdmin-ScheduleScoresGetVs.php',
				data: {Team: Team},
				success: function (res) {
					mainLoadingOff();
					if (res.status === '1') {
						$('#visitanteAgregar').empty().append(res.dataVs);
					}
				},
				error: function(jqxhr, status, exception) {
					mainLoadingOff();
					alert(MSG_AJAX_GENERIC);
					console.log('Exception:' + exception);
				}
			});
		}
		
		
		function aplicarFiltroLocalAgregarA(){
			var fil = document.getElementById('filtroCategoriaLocalAgregarA');
			var src = document.getElementById('localAgregarA_source');
			var dst = document.getElementById('localAgregarA');
			if (!fil || !src || !dst) {
				loadVisitanteAgregarA();
				return;
			}
			var cat = fil.value;
			dst.innerHTML = '';
			for (var i = 0; i < src.options.length; i++) {
				var o = src.options[i];
				if (String(o.getAttribute('data-categoria')) === String(cat)) {
					dst.appendChild(o.cloneNode(true));
				}
			}
			loadVisitanteAgregarA();
		}
		
		function aplicarFiltroVisitanteAgregarA(){
			loadVisitanteAgregarA();
		}
		
		function loadVisitanteAgregarA(){
			var filV = document.getElementById('filtroCategoriaVisitanteAgregarA');
			var src = document.getElementById('localAgregarA_source');
			var first = document.getElementById('localAgregarA');
			var second = document.getElementById('visitanteAgregarA');
			if (!filV || !src || !first || !second) {
				if (first && second) {
					var options = first.innerHTML;
					second.innerHTML = options;
					second.remove(first.selectedIndex);
					second.selectedIndex = 0;
				}
				return;
			}
			var catV = filV.value;
			var localId = String(first.value);
			second.innerHTML = '';
			for (var i = 0; i < src.options.length; i++) {
				var o = src.options[i];
				if (String(o.value) === localId) {
					continue;
				}
				if (String(o.getAttribute('data-categoria')) !== String(catV)) {
					continue;
				}
				second.appendChild(o.cloneNode(true));
			}
			if (second.options.length > 0) {
				second.selectedIndex = 0;
			}
		}
		
		function inicializarAgregarA(){
			if (document.getElementById('filtroCategoriaLocalAgregarA')) {
				aplicarFiltroLocalAgregarA();
			} else {
				loadVisitanteAgregarA();
			}
		}
		
		
		function loadVisitanteAgregarS(){
			var first = document.getElementById('localAgregarS');
			var options = first.innerHTML;
			
			var second = document.getElementById('visitanteAgregarS');
			
			second.innerHTML = options;
			second.remove(first.selectedIndex);
			var option = document.createElement('option');
			option.value = 'NULL';
			option.text = '" . $lang['654'] . "';
			second.add(option, second[0]);
			second.selectedIndex = 0;
		}
		
		function agregarJuego(fecha, Season, weekid, Local, Visitante){
			//console.log('createGame');
			mainLoadingOn();
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: 'ajax/Admin/Games/gameManagementAddGame.php',
				data: {Date: fecha, Season: Season, Week: weekid, Home: Local, Away: Visitante},
				success: function (res) {
					mainLoadingOff();
					if (res.status === '1') {
						alert(res.dataGameAnswer);
						loadWeekAdmin(weekid);
					}
				},
				error: function(jqxhr, status, exception) {
					mainLoadingOff();
					alert(MSG_AJAX_GENERIC);
					console.log('Exception:' + exception);
				}
			});
		}
		
		function agregarJuegoA(fecha, Season, weekid, Local, Visitante){
			//console.log('createGame');
			mainLoadingOn();
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: 'ajax/Admin/Games/gameManagementAddGameA.php',
				data: {Date: fecha, Season: Season, Week: weekid, Home: Local, Away: Visitante},
				success: function (res) {
					mainLoadingOff();
					if (res.status === '1') {
						alert(res.dataGameAnswer);
						loadWeekAdmin(weekid);
					}
				},
				error: function(jqxhr, status, exception) {
					mainLoadingOff();
					alert(MSG_AJAX_GENERIC);
					console.log('Exception:' + exception);
				}
			});
		}
		
		function borrarJuego(gameID, weekid){
			//console.log('borrarJuego');
			mainLoadingOn();
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: 'ajax/Admin/Games/gameManagementDeleteGame.php',
				data: {GameID: gameID, Week: weekid},
				success: function (res) {
					mainLoadingOff()
					if (res.status === '1') {
						alert(res.dataGameAnswer);
						loadWeekAdmin(weekid);
					}
				},
				error: function(jqxhr, status, exception) {
					mainLoadingOff();
					alert(MSG_AJAX_GENERIC);
					console.log('Exception:' + exception);
				}
			});
		}

		var gameupdates = 0;
		function actualizarJuego(GoalsHome, GoalsAway, Date, Played, PenaltiesHome, PenaltiesAway, Time, Field, GameID, weekid){
			// Debug example: actualizarJuego GoalsHome = 0, GoalsAway = 0
			//console.log('actualizarJuego GoalsHome = ' + GoalsHome + ', GoalsAway = ' + GoalsAway);
			gameupdates++;
			mainLoadingOn();
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: 'ajax/Admin/Games/gameManagementUpdateGame.php',
				data: {GoalsHome: GoalsHome, GoalsAway: GoalsAway, Date: Date, Played: Played, PenaltiesHome: PenaltiesHome, PenaltiesAway: PenaltiesAway, Time: Time, Field: Field, GameID: GameID},
				success: function (res) {
					mainLoadingOff();
					if (res.status === '1') {
						gameupdates--;
						if(gameupdates === 0){
							alert('" . $lang['658'] . "');
							actualizarJuegoAfter(weekid);
							loadWeekAdmin(weekid);
						}
					}
				},
				error: function(jqxhr, status, exception) {
					mainLoadingOff();
					alert(MSG_AJAX_GENERIC);
					console.log('Exception:' + exception);
				}
			});
		}
		
		function actualizarJuegoC(Date, Time, Field, GameID, weekid){
			//console.log('actualizarJuego GoalsHome = ' + GoalsHome + ', GoalsAway = ' + GoalsAway);
			gameupdates++;
			mainLoadingOn();
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: 'ajax/Admin/GamesCoach/gameManagementUpdateGame.php',
				data: {Date: Date, Time: Time, Field: Field, GameID: GameID},
				success: function (res) {
					mainLoadingOff();
					if (res.status === '1') {
						gameupdates--;
						if(gameupdates === 0){
							alert('" . $lang['658'] . "');
							loadWeekAdminC(weekid);
						}
					}
				},
				error: function(jqxhr, status, exception) {
					mainLoadingOff();
					alert(MSG_AJAX_GENERIC);
					console.log('Exception:' + exception);
				}
			});
		}
		
		function saveChanges(season, week){
			var torneo, jornada, juego, local, visitante, fecha, horario;
			var golesl, golesv, penaltiesl, penaltiesv, arbitro, comentarios;
			var jugado, estatus, extral, extrav, campo;
			
			//console.log('scores');
			//var table = document.getElementById('scores');
			
			//console.log('tabla = ' + table);
			$('#scores tr.mainValues').each(function() {
					juego = $(this).attr('id');
					torneo = $(this).find('#torneo' + juego).val();
					jornada = $(this).find('#jornada' + juego).val();
					campo = $(this).find('#campo' + juego).val();
					local = $(this).find('#local' + juego).val();
					visitante = $(this).find('#visitante' + juego).val();
					penaltiesl = +$(this).find('#penall' + juego).is( ':checked' );
					penaltiesv = +$(this).find('#penalv' + juego).is( ':checked' );
					fecha = $(this).find('#fecha' + juego).val();
					horario = $(this).find('#horario' + juego).val();
					jugado = $(this).find('#jugado'+juego).val();
					golesl = 0;
					golesv = 0;
					if($('#golesl' + juego).length){
					    golesl = $(this).find('#golesl' + juego).val();
    					if($(this).find('#golesl' + juego).val().localeCompare('') == 0){
    							golesl = 0;
    					}
					}
					if($('#golesv' + juego).length){
					    golesv = $(this).find('#golesv' + juego).val();
    					if($(this).find('#golesv' + juego).val().localeCompare('') == 0){
    						golesv = 0;
    					}
					}
					if (typeof campo !== \"undefined\") {
						actualizarJuego(golesl, golesv, fecha, jugado, penaltiesl, penaltiesv, horario, campo, juego, week);
					}
			});
		}
		
		function saveChangesC(season, week){
			var torneo, jornada, juego, local, visitante, fecha, horario;
			var golesl, golesv, penaltiesl, penaltiesv, arbitro, comentarios;
			var jugado, estatus, extral, extrav, campo;
			
			//console.log('scores');
			//var table = document.getElementById('scores');
			
			//console.log('tabla = ' + table);
			$('#scores tr.mainValues').each(function() {
					juego = $(this).attr('id');
					torneo = $(this).find('#torneo' + juego).val();
					jornada = $(this).find('#jornada' + juego).val();
					campo = $(this).find('#campo' + juego).val();
					local = $(this).find('#local' + juego).val();
					visitante = $(this).find('#visitante' + juego).val();
					fecha = $(this).find('#fecha' + juego).val();
					horario = $(this).find('#horario' + juego).val();
					actualizarJuegoC(fecha, horario, campo, juego, week);
			});
		}
		
		function actualizarJuegoAfter(weekid){
			//console.log('actualizarJuegoAfter');
			mainLoadingOn();
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: 'ajax/Admin/Games/gameManagementUpdateGameAfter.php',
				data: {Week: weekid},
				success: function (res) {
					mainLoadingOff();
				},
				error: function(jqxhr, status, exception) {
					mainLoadingOff();
					alert(MSG_AJAX_GENERIC);
					console.log('Exception:' + exception);
				}
			});
		}
		
		function saveChangesS(season, week){
			var torneo, jornada, juego, local, visitante, fecha, horario;
			var golesl, golesv, penaltiesl, penaltiesv, arbitro, comentarios;
			var jugado, estatus, extral, extrav, campo;
			
			//console.log('scores');
			//var table = document.getElementById('scoresS');
			
			//console.log('tabla = ' + table);
			$('#scoresS tr.mainValues').each(function() {
					juego = $(this).attr('id');
					torneo = $(this).find('#torneo' + juego).val();
					jornada = $(this).find('#jornada' + juego).val();
					campo = $(this).find('#campo' + juego).val();
					local = $(this).find('#local' + juego).val();
					visitante = $(this).find('#visitante' + juego).val();
					penaltiesl = +$(this).find('#penall' + juego).is( ':checked' );
					penaltiesv = +$(this).find('#penalv' + juego).is( ':checked' );
					fecha = $(this).find('#fecha' + juego).val();
					horario = $(this).find('#horario' + juego).val();
					jugado = $(this).find('#jugado'+juego).val();
					golesl = 0;
					golesv = 0;
					if($('#golesl' + juego).length){
					    golesl = $(this).find('#golesl' + juego).val();
    					if($(this).find('#golesl' + juego).val().localeCompare('') == 0){
    							golesl = 0;
    					}
					}
					if($('#golesv' + juego).length){
					    golesv = $(this).find('#golesv' + juego).val();
    					if($(this).find('#golesv' + juego).val().localeCompare('') == 0){
    						golesv = 0;
    					}
					}
				   	actualizarJuego(golesl, golesv, fecha, jugado, penaltiesl, penaltiesv, horario, campo, juego, week);
			});
		}
		
		function saveChangesSC(season, week){
			var torneo, jornada, juego, local, visitante, fecha, horario;
			var golesl, golesv, penaltiesl, penaltiesv, arbitro, comentarios;
			var jugado, estatus, extral, extrav, campo;
			
			//console.log('scores');
			//var table = document.getElementById('scoresS');
			
			//console.log('tabla = ' + table);
			$('#scoresS tr.mainValues').each(function() {
					juego = $(this).attr('id');
					torneo = $(this).find('#torneo' + juego).val();
					jornada = $(this).find('#jornada' + juego).val();
					campo = $(this).find('#campo' + juego).val();
					local = $(this).find('#local' + juego).val();
					visitante = $(this).find('#visitante' + juego).val();
					fecha = $(this).find('#fecha' + juego).val();
					horario = $(this).find('#horario' + juego).val();
				   	actualizarJuegoC(fecha, horario, campo, juego, week);
			});
		}
		
		function saveGameDocs(Season,Week,Game){
			mainLoadingOn();
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: 'ajax/Admin/Games/gameManagementUploadGameDocs.php',
				data: {Season: Season,Week: Week,Game: Game, Anexo1:  $('#myAnexo1FileName').val(), Anexo2:  $('#myAnexo2FileName').val(), Anexo3:  $('#myAnexo3FileName').val(), Anexo4:  $('#myAnexo4FileName').val()},
				success: function (res) {
					mainLoadingOff();
					if (res.status === '1') {
						$('#gameDocInputDiv').css('z-index', '-1'); 
						$('#gameDocInput').html('');
						refreshGameDocs(Season,Week,Game);
					}
				},
				error: function(jqxhr, status, exception) {
					mainLoadingOff();
					alert(MSG_AJAX_GENERIC);
					console.log('Exception:' + exception);
				}
			});
		}
		
		function refreshGameDocs(Season,Week,Game){
			mainLoadingOn();
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: 'ajax/Admin/Games/weekAdmin-ScheduleScoresGameDetailDocsContainer.php',
				data: {Season: Season,Week: Week,Game: Game},
				success: function (res) {
					mainLoadingOff();
					if (res.status === '1') {
						$('#docs' + Game).html(res.dataWeekGameDetailDocContainer);
						$('#docs' + Game + 'S').html(res.dataWeekGameDetailDocContainerS);
					}
				},
				error: function(jqxhr, status, exception) {
					mainLoadingOff();
					alert(MSG_AJAX_GENERIC);
					console.log('Exception:' + exception);
				}
			});
		}
		
		var updates = 0;
		var tmprojadias = 0;
		var tmprojamulta = 0;
		var tmprojacomentario = '';
		var tmprojapagado = 0;
	
		var gameplayerupdates = 0;
		
		function actualizarJuegoDetalleSets(Game, Week, Season, s1l, s2l, s3l, s4l, s5l, s1v, s2v, s3v, s4v, s5v){
		    //console.log('actualizarJuegoDetalleSets');
			mainLoadingOn();
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: 'ajax/Admin/Games/gameManagementUpdateGamePlayerStatSets.php',
				data: {Game: Game, Week: Week, Season: Season, s1l:s1l, s2l:s2l, s3l:s3l, s4l:s4l, s5l:s5l, s1v:s1v, s2v:s2v, s3v:s3v, s4v:s4v, s5v:s5v},
				success: function (res) {
					mainLoadingOff();
					if (res.status === '1') {
					    loadWeekAdmin(Week);
						//alert('" . $lang['310'] . "');
					}
				},
				error: function(jqxhr, status, exception) {
					mainLoadingOff();
					alert(MSG_AJAX_GENERIC);
					console.log('Exception:' + exception);
				}
			});
		}
		
		function actualizarJuegoDetalleRefereeComentarios(Game, Week, Season, Referee, Comments, Extral, Extrav){
			//console.log('actualizarJuegoDetalleRefereeComentarios');
			mainLoadingOn();
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: 'ajax/Admin/Games/gameManagementUpdateGamePlayerStatRefereeComm.php',
				data: {Game: Game, Week: Week, Season: Season, Referee: Referee, Comments: Comments, Extral: Extral, Extrav: Extrav},
				success: function (res) {
					mainLoadingOff();
					if (res.status === '1') {
						alert('" . $lang['310'] . "');
					}
				},
				error: function(jqxhr, status, exception) {
					mainLoadingOff();
					alert(MSG_AJAX_GENERIC);
					console.log('Exception:' + exception);
				}
			});
		}
		
		function actualizarJuegoDetalleJugadorJugado(Player, Team, Game, Week, Season, Played){
			//console.log('actualizarJuegoDetalleJugadorJugado');
			gameplayerupdates++;
			mainLoadingOn();
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: 'ajax/Admin/Games/gameManagementUpdateGamePlayerStatPlayed.php',
				data: {Player: Player, Team: Team, Game: Game, Week: Week, Season: Season, Played: Played},
				success: function (res) {
					mainLoadingOff();
					if (res.status === '1') {
						gameplayerupdates--;
						/*if(gameplayerupdates === 0){
							alert('" . $lang['310'] . "');
							//actualizarJuegoAfter(weekid);
							//loadWeekAdmin(weekid);
						}*/
					}
				},
				error: function(jqxhr, status, exception) {
					mainLoadingOff();
					alert(MSG_AJAX_GENERIC);
					console.log('Exception:' + exception);
				}
			});
		}
		
		function actualizarJuegoDetalleJugadorAmarillas(Player, Team, Game, Week, Season, Yellow){
			//console.log('actualizarJuegoDetalleJugadorAmarillas');
			gameplayerupdates++;
			mainLoadingOn();
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: 'ajax/Admin/Games/gameManagementUpdateGamePlayerStatYellow.php',
				data: {Player: Player, Team: Team, Game: Game, Week: Week, Season: Season, Yellow: Yellow},
				success: function (res) {
					mainLoadingOff();
					if (res.status === '1') {
						gameplayerupdates--;
						/*if(gameplayerupdates === 0){
							alert('" . $lang['310'] . "');
							//actualizarJuegoAfter(weekid);
							//loadWeekAdmin(weekid);
						}*/
					}
				},
				error: function(jqxhr, status, exception) {
					mainLoadingOff();
					alert(MSG_AJAX_GENERIC);
					console.log('Exception:' + exception);
				}
			});
		}
		
		function actualizarJuegoDetalleJugadorRojas(Player, Team, Game, Week, Season, Red, RedDays, RedFee, YellowN, RedComm){
			//console.log('actualizarJuegoDetalleJugadorAmarillas');
			gameplayerupdates++;
			mainLoadingOn();
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: 'ajax/Admin/Games/gameManagementUpdateGamePlayerStatRed.php',
				data: {Player: Player, Team: Team, Game: Game, Week: Week, Season: Season, Red: Red, RedDays: RedDays, RedFee: RedFee, YellowN: YellowN, RedComm: RedComm},
				success: function (res) {
					mainLoadingOff();
					if (res.status === '1') {
						gameplayerupdates--;
						/*if(gameplayerupdates === 0){
							alert('" . $lang['310'] . "');
							//actualizarJuegoAfter(weekid);
							//loadWeekAdmin(weekid);
						}*/
					}
				},
				error: function(jqxhr, status, exception) {
					mainLoadingOff();
					alert(MSG_AJAX_GENERIC);
					console.log('Exception:' + exception);
				}
			});
		}
		
		function actualizarJuegoDetalleJugadorGoles(Player, Team, Game, Week, Season, Goals){
			//console.log('actualizarJuegoDetalleJugadorGoles');
			gameplayerupdates++;
			mainLoadingOn();
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: 'ajax/Admin/Games/gameManagementUpdateGamePlayerStatGoal.php',
				data: {Player: Player, Team: Team, Game: Game, Week: Week, Season: Season, Goals: Goals},
				success: function (res) {
					mainLoadingOff();
					if (res.status === '1') {
						gameplayerupdates--;
						/*if(gameplayerupdates === 0){
							alert('" . $lang['310'] . "');
							//actualizarJuegoAfter(weekid);
							//loadWeekAdmin(weekid);
						}*/
					}
				},
				error: function(jqxhr, status, exception) {
					mainLoadingOff();
					alert(MSG_AJAX_GENERIC);
					console.log('Exception:' + exception);
				}
			});
		}
		
		function SaveGameDetailPlayerStats(Season,Week,Game,lequipoid,vequipoid, referee, comments, extral, extrav){
			gameplayerupdates = 0;
			var jugadorid;
			var tjugado, tamarillas, trojas, tgoles;
			var jugado, amarillas, rojas, goles;
			var rojasd, rojasm, rojasc;
			var table = document.getElementById('localList');
			actualizarJuegoDetalleRefereeComentarios(Game, Week, Season, referee, comments, extral, extrav);
			for (var i = 1, row; row = table.rows[i]; i++) {
				try{
				   //iterate through rows
				   //rows would be accessed using the 'row' variable assigned in the for loop
				   for (var j = 0, col; col = row.cells[j]; j++) {
						 //iterate through columns
						 //columns would be accessed using the 'col' variable assigned in the for loop
						 if (j == 0) jugadorid = col.innerText;
						 if (j == 1) tjugado = col.innerText;
						 if (j == 2) tamarillas = col.innerText;
						 if (j == 3) trojas = col.innerText;
						 if (j == 4) tgoles = col.innerText;
						 if (j == 5) rojasd = col.children[0].value;
						 if (j == 6) rojasm = col.children[0].value;
						 if (j == 7) rojasc = col.children[0].value.replace('\"', '\"\"');
						 if (j == 11){
							 if(col.children[0].checked){
									jugado = '1';
							 }else{
									jugado = '0';
							 }
						 }
						 if (j == 12) amarillas = col.children[0].value;
						 if (j == 13){
							 if(col.children[0].checked){
									rojas = '1';
							 }else{
									rojas = '0';
							 }
						 }
						 if (j == 14) goles = col.children[0].value;
				   	}
				 if(tgoles != goles || tamarillas != amarillas || tjugado != jugado || trojas != rojas){
					 
					if(tjugado != jugado){
							actualizarJuegoDetalleJugadorJugado(jugadorid, lequipoid, Game, Week, Season, jugado);
					}if(tamarillas != amarillas){
							actualizarJuegoDetalleJugadorAmarillas(jugadorid, lequipoid, Game, Week, Season, amarillas);
					}if(tgoles != goles){
							actualizarJuegoDetalleJugadorGoles(jugadorid, lequipoid, Game, Week, Season, goles);
					}
					if(trojas != rojas){
							actualizarJuegoDetalleJugadorRojas(jugadorid, lequipoid, Game, Week, Season, rojas, rojasd, rojasm, amarillas, rojasc);
					}
				 }
				 
				}catch (e) {
					console.log('Error = ' + e.message);
				}
			}		
			var table = document.getElementById('visitanteList');
			
			for (var i = 1, row; row = table.rows[i]; i++) {
				try{
				   //iterate through rows
				   //rows would be accessed using the 'row' variable assigned in the for loop
				   for (var j = 0, col; col = row.cells[j]; j++) {
					 //iterate through columns
					 //columns would be accessed using the 'col' variable assigned in the for loop
					 if (j == 0) jugadorid = col.innerText;
					 if (j == 1) tjugado = col.innerText;
					 if (j == 2) tamarillas = col.innerText;
					 if (j == 3) trojas = col.innerText;
					 if (j == 4) tgoles = col.innerText;
					 if (j == 5) rojasd = col.children[0].value;
					 if (j == 6) rojasm = col.children[0].value;
					 if (j == 7) rojasc = col.children[0].value.replace('\"', '\"\"');
					 if (j == 11){
						 if(col.children[0].checked){
								jugado = '1';
						 }else{
								jugado = '0';
						 }
					 }
					 if (j == 12) amarillas = col.children[0].value;
					 if (j == 13){
						 if(col.children[0].checked){
								rojas = '1';
						 }else{
								rojas = '0';
						 }
					 }
					 if (j == 14) goles = col.children[0].value;
				   }  
				 if(tgoles != goles || tamarillas != amarillas || tjugado != jugado){
					if(tjugado != jugado){
							actualizarJuegoDetalleJugadorJugado(jugadorid, vequipoid, Game, Week, Season, jugado);
					}if(tamarillas != amarillas){
							actualizarJuegoDetalleJugadorAmarillas(jugadorid, vequipoid, Game, Week, Season, amarillas);
					}if(tgoles != goles){
							actualizarJuegoDetalleJugadorGoles(jugadorid, vequipoid, Game, Week, Season, goles);
					}if(trojas != rojas){
							actualizarJuegoDetalleJugadorRojas(jugadorid, vequipoid, Game, Week, Season, rojas, rojasd, rojasm, amarillas, rojasc);
					}
				 }
				 
				}catch (e) {
					console.log('Error = ' + e.message);
				}
			}		
		}
		
		function SaveGameDetailPlayerStatsC(Season,Week,Game,lequipoid,vequipoid, referee, comments, extral, extrav){
			gameplayerupdates = 0;
			var jugadorid;
			var tjugado, tamarillas, trojas, tgoles;
			var jugado, amarillas, rojas, goles;
			var rojasd, rojasm, rojasc;
			actualizarJuegoDetalleRefereeComentarios(Game, Week, Season, referee, comments, extral, extrav);
		}
		
		function SaveGameDetailPlayerStatsVoleibol(Season,Week,Game,lequipoid,vequipoid, referee, comments, extral, extrav){
			gameplayerupdates = 0;
			var jugadorid;
			var tjugado;
			var jugado;
			var table = document.getElementById('localList');
			actualizarJuegoDetalleRefereeComentarios(Game, Week, Season, referee, comments, extral, extrav);
			actualizarJuegoDetalleSets(Game, Week, Season, $('#S1L').val(), $('#S2L').val(), $('#S3L').val(), $('#S4L').val(), $('#S5L').val(), $('#S1V').val(), $('#S2V').val(), $('#S3V').val(), $('#S4V').val(), $('#S5V').val());
			for (var i = 1, row; row = table.rows[i]; i++) {
				try{
				   //iterate through rows
				   //rows would be accessed using the 'row' variable assigned in the for loop
				   for (var j = 0, col; col = row.cells[j]; j++) {
						 //iterate through columns
						 //columns would be accessed using the 'col' variable assigned in the for loop
						 if (j == 0) jugadorid = col.innerText;
						 if (j == 1) tjugado = col.innerText;
						 if (j == 4){
							 if(col.children[0].checked){
									jugado = '1';
							 }else{
									jugado = '0';
							 }
						 }
				   	}
				 if(tjugado != jugado){
					 
					if(tjugado != jugado){
							actualizarJuegoDetalleJugadorJugado(jugadorid, lequipoid, Game, Week, Season, jugado);
					}
				 }
				}catch (e) {
					console.log('Error = ' + e.message);
				}
			}		
			var table = document.getElementById('visitanteList');
			
			for (var i = 1, row; row = table.rows[i]; i++) {
				try{
				   //iterate through rows
				   //rows would be accessed using the 'row' variable assigned in the for loop
				   for (var j = 0, col; col = row.cells[j]; j++) {
					 //iterate through columns
					 //columns would be accessed using the 'col' variable assigned in the for loop
					 if (j == 0) jugadorid = col.innerText;
					 if (j == 1) tjugado = col.innerText;
					 if (j == 4){
						 if(col.children[0].checked){
								jugado = '1';
						 }else{
								jugado = '0';
						 }
					 }
				   }  
				 if(tjugado != jugado){
					if(tjugado != jugado){
							actualizarJuegoDetalleJugadorJugado(jugadorid, vequipoid, Game, Week, Season, jugado);
					}
				 }
				}catch (e) {
					console.log('Error = ' + e.message);
				}
			}		
		}
		
		function SaveGameDetailPlayerStatsS(Season,Week,Game,lequipoid,vequipoid, referee, comments, extral, extrav){
			gameplayerupdates = 0;
			var jugadorid;
			var tjugado, tamarillas, trojas, tgoles;
			var jugado, amarillas, rojas, goles;
			var rojasd, rojasm, rojasc;
			var table = document.getElementById('local' + Game + 'T');
			actualizarJuegoDetalleRefereeComentarios(Game, Week, Season, referee, comments, extral, extrav);
			for (var i = 1, row; row = table.rows[i]; i++) {
				try{
				   //iterate through rows
				   //rows would be accessed using the 'row' variable assigned in the for loop
				   for (var j = 0, col; col = row.cells[j]; j++) {
						 //iterate through columns
						 //columns would be accessed using the 'col' variable assigned in the for loop
						 if (j == 0) jugadorid = col.innerText;
						 if (j == 1) tjugado = col.innerText;
						 if (j == 2) tamarillas = col.innerText;
						 if (j == 3) trojas = col.innerText;
						 if (j == 4) tgoles = col.innerText;
						 if (j == 5) rojasd = col.children[0].value;
						 if (j == 6) rojasm = col.children[0].value;
						 if (j == 7) rojasc = col.children[0].value.replace('\"', '\"\"');
						 if (j == 11){
							 if(col.children[0].checked){
									jugado = '1';
							 }else{
									jugado = '0';
							 }
						 }
						 if (j == 12) amarillas = col.children[0].value;
						 if (j == 13){
							 if(col.children[0].checked){
									rojas = '1';
							 }else{
									rojas = '0';
							 }
						 }
						 if (j == 14) goles = col.children[0].value;
				   	}
				 if(tgoles != goles || tamarillas != amarillas || trojas != rojas || tjugado != jugado){
					 
					if(tjugado != jugado){
							actualizarJuegoDetalleJugadorJugado(jugadorid, lequipoid, Game, Week, Season, jugado);
					}if(tamarillas != amarillas){
							actualizarJuegoDetalleJugadorAmarillas(jugadorid, lequipoid, Game, Week, Season, amarillas);
					}if(trojas != rojas){
							actualizarJuegoDetalleJugadorRojas(jugadorid, lequipoid, Game, Week, Season, rojas, rojasd, rojasm, amarillas, rojasc);
					}if(tgoles != goles){
							actualizarJuegoDetalleJugadorGoles(jugadorid, lequipoid, Game, Week, Season, goles);
					}
				 }
				}catch (e) {
					console.log('Error = ' + e.message);
				}
			}		
			var table = document.getElementById('visitante' + Game + 'T');
			
			for (var i = 1, row; row = table.rows[i]; i++) {
				try{
				   //iterate through rows
				   //rows would be accessed using the 'row' variable assigned in the for loop
				   for (var j = 0, col; col = row.cells[j]; j++) {
					 //iterate through columns
					 //columns would be accessed using the 'col' variable assigned in the for loop
					 if (j == 0) jugadorid = col.innerText;
					 if (j == 1) tjugado = col.innerText;
					 if (j == 2) tamarillas = col.innerText;
					 if (j == 3) trojas = col.innerText;
					 if (j == 4) tgoles = col.innerText;
					 if (j == 5) rojasd = col.children[0].value;
					 if (j == 6) rojasm = col.children[0].value;
					 if (j == 7) rojasc = col.children[0].value.replace('\"', '\"\"');
					 if (j == 11){
						 if(col.children[0].checked){
								jugado = '1';
						 }else{
								jugado = '0';
						 }
					 }
					 if (j == 12) amarillas = col.children[0].value;
					 if (j == 13){
						 if(col.children[0].checked){
								rojas = '1';
						 }else{
								rojas = '0';
						 }
					 }
					 if (j == 14) goles = col.children[0].value;
				   }  
				 if(tgoles != goles || tamarillas != amarillas || trojas != rojas || tjugado != jugado){
					if(tjugado != jugado){
							actualizarJuegoDetalleJugadorJugado(jugadorid, vequipoid, Game, Week, Season, jugado);
					}if(tamarillas != amarillas){
							actualizarJuegoDetalleJugadorAmarillas(jugadorid, vequipoid, Game, Week, Season, amarillas);
					}if(trojas != rojas){
							actualizarJuegoDetalleJugadorRojas(jugadorid, vequipoid, Game, Week, Season, rojas, rojasd, rojasm, amarillas, rojasc);
					}if(tgoles != goles){
							actualizarJuegoDetalleJugadorGoles(jugadorid, vequipoid, Game, Week, Season, goles);
					}
				 }
				}catch (e) {
					console.log('Error = ' + e.message);
				}
			}		
		}
		
		function SaveGameDetailPlayerStatsSVoleibol(Season,Week,Game,lequipoid,vequipoid, referee, comments, extral, extrav){
			gameplayerupdates = 0;
			var jugadorid;
			var tjugado, tamarillas, trojas, tgoles;
			var jugado, amarillas, rojas, goles;
			var rojasd, rojasm, rojasc;
			var table = document.getElementById('local' + Game + 'T');
			actualizarJuegoDetalleRefereeComentarios(Game, Week, Season, referee, comments, extral, extrav);
			actualizarJuegoDetalleSets(Game, Week, Season, $('#S1L').val(), $('#S2L').val(), $('#S3L').val(), $('#S4L').val(), $('#S5L').val(), $('#S1V').val(), $('#S2V').val(), $('#S3V').val(), $('#S4V').val(), $('#S5V').val());
			for (var i = 1, row; row = table.rows[i]; i++) {
				try{
				   //iterate through rows
				   //rows would be accessed using the 'row' variable assigned in the for loop
				   for (var j = 0, col; col = row.cells[j]; j++) {
						 //iterate through columns
						 //columns would be accessed using the 'col' variable assigned in the for loop
						 if (j == 0) jugadorid = col.innerText;
						 if (j == 1) tjugado = col.innerText;
						 if (j == 4){
							 if(col.children[0].checked){
									jugado = '1';
							 }else{
									jugado = '0';
							 }
						 }
				   	}
				 if(tjugado != jugado){
					 
					if(tjugado != jugado){
							actualizarJuegoDetalleJugadorJugado(jugadorid, lequipoid, Game, Week, Season, jugado);
					}
				 }
				}catch (e) {
					console.log('Error = ' + e.message);
				}
			}		
			var table = document.getElementById('visitante' + Game + 'T');
			
			for (var i = 1, row; row = table.rows[i]; i++) {
				try{
				   //iterate through rows
				   //rows would be accessed using the 'row' variable assigned in the for loop
				   for (var j = 0, col; col = row.cells[j]; j++) {
					 //iterate through columns
					 //columns would be accessed using the 'col' variable assigned in the for loop
					 if (j == 0) jugadorid = col.innerText;
					 if (j == 1) tjugado = col.innerText;
					 if (j == 4){
						 if(col.children[0].checked){
								jugado = '1';
						 }else{
								jugado = '0';
						 }
					 }
				   }  
				 if(tjugado != jugado){
					if(tjugado != jugado){
							actualizarJuegoDetalleJugadorJugado(jugadorid, vequipoid, Game, Week, Season, jugado);
					}
				 }
				}catch (e) {
					console.log('Error = ' + e.message);
				}
			}		
		}	"
		
?>