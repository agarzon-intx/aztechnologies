<?php
    session_start();
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
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
	$sessionstat = $fgmembersite->CheckLogin('weekAdmin-ScheduleScoresGameDetail.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');


	$Season = $_COOKIE[$Config->getAlias() . 'season'];
	$Week = htmlspecialchars($_POST["week"]);
	$Game = htmlspecialchars($_POST["game"]);
	$Game_DESC = htmlspecialchars($_POST["gamedesc"]);
	$lgoal = htmlspecialchars($_POST["lgoals"]);
	$vgoal = htmlspecialchars($_POST["vgoals"]);
	$comentario = htmlspecialchars($_POST["Comentarios"]);
	$arbitro = htmlspecialchars($_POST["Arbitro"]);
	$extral = htmlspecialchars($_POST["Extral"]);
	$extrav = htmlspecialchars($_POST["Extrav"]);
	$sqlcat = htmlspecialchars($_POST["SQL"]);

	$lequipoid = "";
	$lequipo = "";
	$lequipodesc = "";
	$llogo = "";

	$vequipoid = "";
	$vequipo = "";
	$vequipodesc = "";
	$vlogo = "";
	$gameData = "";
	$fecha = new DateTime();

	$sql = "select Equipo_ID, Equipo_FULLDESC, concat(Torneo_ID,'-', Equipo_ID) Logo 
			from $schema.Equipos
			where Equipo_ID = (select Local_ID from $schema.Juegos
								where Torneo_ID = $Season and Juego_ID = $Game) 
				and Torneo_ID = $Season;";
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			$lequipoid = $row["Equipo_ID"];
			$lequipo = $row["Equipo_FULLDESC"];
			$lequipodesc = $row["Equipo_FULLDESC"];
			$llogo = $row["Logo"];
		}
	}

	$sql = "select Equipo_ID, Equipo_FULLDESC, concat(Torneo_ID,'-', Equipo_ID) Logo 
			from $schema.Equipos
			where Equipo_ID = (select Visitante_ID from $schema.Juegos
								where Torneo_ID = $Season and Juego_ID = $Game) 
				and Torneo_ID = $Season;";
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			$vequipoid = $row["Equipo_ID"];
			$vequipo = $row["Equipo_FULLDESC"];
			$vequipodesc = $row["Equipo_FULLDESC"];
			$vlogo = $row["Logo"];
		}
	}

	$sql = "select 0 as VisitanteS, 
				j.Torneo_ID as Torneo, 
				jo.Jornada_ID as Jornada, 
				Juego_ID as juego,
				Extra_Local,
				Extra_Visitante, 
				case when j.Visitante_ID is null then null else Arbitro end as Arbitro, 
				Comentarios,
				LoadInfo
			from  $schema.Juegos as j 
				join $schema.Jornada as jo on j.Fecha between jo.Fecha_Inicio and jo.Fecha_Fin
			where jo.Jornada_ID = $Week and j.Juego_ID = $Game;";
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			$comentario = $row["Comentarios"];
			$arbitro = $row["Arbitro"];
			$extral = $row["Extra_Local"];
			$extrav = $row["Extra_Visitante"];
	        $gameData = $row["LoadInfo"];
		}
	}
	$Config->LoadFlags();


    $retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');

	$htmlWeekGameDetail = '<table width="99%" id="fichaTecnicaEdit" >
							<tr style="background: url(./imagenes/marcador.png?tmp=' . $fecha->getTimestamp() . ') no-repeat; background-size:100% 100%;">
                        	    <td colspan="3" height="200">
                        	        <div style="width:auto; margin: auto;">';
                                	
                    $sql = "SELECT ifnull(s.Set1_L,0) Set1_L,
                            		ifnull(s.Set1_V,0) Set1_V,
                            		case when ifnull(s.Set1_L,0) <> ifnull(s.Set1_V,0) then case when ifnull(s.Set1_L,0) > ifnull(s.Set1_V,0) then '00800026' else '80000026' end else '80000000' end colorS1L,
                            		case when ifnull(s.Set1_L,0) <> ifnull(s.Set1_V,0) then case when ifnull(s.Set1_V,0) > ifnull(s.Set1_L,0) then '00800026' else '80000026' end else '80000000' end colorS1V,
                                    ifnull(s.Set2_L,0) Set2_L,
                                    ifnull(s.Set2_V,0) Set2_V,
                            		case when ifnull(s.Set2_L,0) <> ifnull(s.Set2_V,0) then case when ifnull(s.Set2_L,0) > ifnull(s.Set2_V,0) then '00800026' else '80000026' end else '80000000' end colorS2L,
                            		case when ifnull(s.Set2_L,0) <> ifnull(s.Set2_V,0) then case when ifnull(s.Set2_V,0) > ifnull(s.Set2_L,0) then '00800026' else '80000026' end else '80000000' end colorS2V,
                                    ifnull(s.Set3_L,'') Set3_L,
                                    ifnull(s.Set3_V,'') Set3_V,
                            		case when ifnull(s.Set3_L,0) <> ifnull(s.Set3_V,0) then case when ifnull(s.Set3_L,0) > ifnull(s.Set3_V,0) then '00800026' else '80000026' end else '80000000' end colorS3L,
                            		case when ifnull(s.Set3_L,0) <> ifnull(s.Set3_V,0) then case when ifnull(s.Set3_V,0) > ifnull(s.Set3_L,0) then '00800026' else '80000026' end else '80000000' end colorS3V,
                                    ifnull(s.Set4_L,'') Set4_L,
                                    ifnull(s.Set4_V,'') Set4_V,
                            		case when ifnull(s.Set4_L,0) <> ifnull(s.Set4_V,0) then case when ifnull(s.Set4_L,0) > ifnull(s.Set4_V,0) then '00800026' else '80000026' end else '80000000' end colorS4L,
                            		case when ifnull(s.Set4_L,0) <> ifnull(s.Set4_V,0) then case when ifnull(s.Set4_V,0) > ifnull(s.Set4_L,0) then '00800026' else '80000026' end else '80000000' end colorS4V,
                                    ifnull(s.Set5_L,'') Set5_L,
                                    ifnull(s.Set5_V,'') Set5_V,
                            		case when ifnull(s.Set5_L,0) <> ifnull(s.Set5_V,0) then case when ifnull(s.Set5_L,0) > ifnull(s.Set5_V,0) then '00800026' else '80000026' end else '80000000' end colorS5L,
                            		case when ifnull(s.Set5_L,0) <> ifnull(s.Set5_V,0) then case when ifnull(s.Set5_V,0) > ifnull(s.Set5_L,0) then '00800026' else '80000026' end else '80000000' end colorS5V
                            FROM $schema.Juegos_Set s
                            WHERE Juego_ID = $Game;";
                	$result = $Config->query($sql);
                	if ($result->num_rows > 0) {
                		// output data of each row
                		while($row = $result->fetch_assoc()) {
                		    $q1l = $row["Set1_L"];
                		    $q2l = $row["Set2_L"];
                		    $q3l = $row["Set3_L"];
                		    $q4l = $row["Set4_L"];
                		    $q5l = $row["Set5_L"];
                		    $q1v = $row["Set1_V"];
                		    $q2v = $row["Set2_V"];
                		    $q3v = $row["Set3_V"];
                		    $q4v = $row["Set4_V"];
                		    $q5v = $row["Set5_V"];
                		}
                	} else {
                		echo "";
                	}
        			$htmlWeekGameDetail .= '<table style="margin: 0 auto;background: white;color: black;">
                        	    <tr>
                        	        <th style="border: 1px solid #000000;text-align: center;">' . $lang['669'] . '</th>
                        	        <th style="border: 1px solid #000000;text-align: center;">' . $lang['670'] . '</th>
                        	        <th style="border: 1px solid #000000;text-align: center;">' . $lang['671'] . '</th>
                        	        <th style="border: 1px solid #000000;text-align: center;">' . $lang['672'] . '</th>
                        	        <th style="border: 1px solid #000000;text-align: center;">' . $lang['673'] . '</th>
                                    <th style="border: 1px solid #000000;text-align: center;">' . $lang['674'] . '</th>';
                    $htmlWeekGameDetail .= '</tr>
                	            <tr>
                                	<td style="text-align:left; border: none;border: 1px solid #000000;" width="180px"><img src="./imagenes/Original/' . $llogo . '.png?tmp=' . $fecha->getTimestamp() . '" width="70" height="70" alt=""/>' . $lequipo . '</td>
                                	<td style="text-align:center; border: none;border: 1px solid #000000;text-align: center;" width="90px">' . $q1l . '</td>
                                	<td style="text-align:center; border: none;border: 1px solid #000000;text-align: center;" width="90px">' . $q2l . '</td>
                                	<td style="text-align:center; border: none;border: 1px solid #000000;text-align: center;" width="90px">' . $q3l . '</td>
                                    <td style="text-align:left; border: none;border: 1px solid #000000;text-align: center;" width="90px">' . $q4l . '</td>
                                    <td style="text-align:left; border: none;border: 1px solid #000000;text-align: center;" width="90px">' . $q5l . '</td>';
                    $htmlWeekGameDetail .= '</tr>
                	            <tr>
                                	<td style="text-align:left; border: none;border: 1px solid #000000;" width="180px"><img src="./imagenes/Original/' . $vlogo . '.png?tmp=' . $fecha->getTimestamp() . '" width="70" height="70" alt=""/>' . $vequipo . '</td>
                                	<td style="text-align:center; border: none;border: 1px solid #000000;text-align: center;" width="90px">' . $q1v . '</td>
                                	<td style="text-align:center; border: none;border: 1px solid #000000;text-align: center;" width="90px">' . $q2v . '</td>
                                	<td style="text-align:center; border: none;border: 1px solid #000000;text-align: center;" width="90px">' . $q3v . '</td>
                                    <td style="text-align:left; border: none;border: 1px solid #000000;text-align: center;" width="90px">' . $q4v . '</td>
                                    <td style="text-align:left; border: none;border: 1px solid #000000;text-align: center;" width="90px">' . $q5v . '</td>';
                    $htmlWeekGameDetail .= '</tr>
                            </table>';
                    $htmlWeekGameDetail .= '</div>
                                </td>
                            </tr>
							<tr >
								<td colspan=3" style="text-align:left; border: none; width: 100%">
									<div class="row">
										<div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">
											<div style="text-align:left;border: none;margin-top: 10px;">
												<div class="input-group input-group-outline my-3" style="margin-top: -5px !important;margin-bottom: 0px !important;">
													<label class="form-label">' . $lang['607'] . '</label>
													<input type="text" class="form-control" name="arbitro" id="arbitro" value="' . $arbitro . '"/>
												</div>
											</div>
										</div>
										<div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">
											<div style="text-align:left;border: none;margin-top: 10px;">
												<div class="input-group input-group-outline my-3" style="margin-top: -5px !important;margin-bottom: 0px !important;">
													<label class="form-label">' . $lang['645'] . '</label>
													<input type="text" class="form-control" name="comentario" id="comentario" value="' . $comentario . '"/>
												</div>
											</div>
										</div>
										<div class="col-1 col-sm-1 col-md-1 col-lg-1 col-xl-1">
											<div style="text-align:left;border: none;margin-top: 10px;">
												<div class="input-group input-group-outline my-3" style="margin-top: -5px !important;margin-bottom: 0px !important;">
													<label class="form-label">' . $lang['648'] . ' ' . $lang['650'] . '</label>
													<input type="text" class="form-control" name="extral" id="extral" value="' . $extral . '"/>
												</div>
											</div>
										</div>
										<div class="col-1 col-sm-1 col-md-1 col-lg-1 col-xl-1">
											<div style="text-align:left;border: none;margin-top: 10px;">
												<div class="input-group input-group-outline my-3" style="margin-top: -5px !important;margin-bottom: 0px !important;">
													<label class="form-label">' . $lang['648'] . ' ' . $lang['651'] . '</label>
													<input type="text" class="form-control" name="extrav" id="extrav" value="' . $extrav . '"/>
												</div>
											</div>
										</div>
										<div class="col-1 col-sm-1 col-md-1 col-lg-1 col-xl-1">
											<div style="text-align:left;border: none;margin-top: 10px;">
												<div class="input-group input-group-outline my-3" style="margin-top: -5px !important;margin-bottom: 0px !important;">
													<label class="form-label">Game Data</label>
													<textarea class="form-control" spellcheck="false" name="gameData" id="gameData">' . $gameData . '</textarea>
												</div>
											</div>
										</div>
										<div class="col-1 col-sm-1 col-md-1 col-lg-1 col-xl-1">
											<div style="text-align:left; border: none; margin-top: 10px !important">
												<button style="margin-top: -5px; margin-bottom: 15px;" type="button" class="btn btn-primary" onclick="SaveGameDetailPlayerStatsBasketR(' . $Season . ',' . $Week . ',' . $Game . ',' . $lequipoid . ',' . $vequipoid . ', $(\'#arbitro\').val(), $(\'#comentario\').val(), $(\'#extral\').val(), $(\'#extrav\').val());">' . $lang['0000'] . '</button>
											</div>
										</div>
									</div>
								</td>
							</tr>
							<script>
								var inputs = document.querySelectorAll(\'input\');
								var textareas = document.querySelectorAll(\'textarea\');
								
								for (var i = 0; i < inputs.length; i++) {
									inputs[i].addEventListener(\'focus\', function(e) {
									  this.parentElement.classList.add(\'is-focused\');
									}, false);

									inputs[i].onkeyup = function(e) {
									  if (this.value != "") {
										this.parentElement.classList.add(\'is-filled\');
									  } else {
										this.parentElement.classList.remove(\'is-filled\');
									  }
									};

									inputs[i].addEventListener(\'focusout\', function(e) {
										if (this.value != "") {
											this.parentElement.classList.add(\'is-filled\');
										}
										this.parentElement.classList.remove(\'is-focused\');
									}, false);
									
									if(inputs[i].hasAttribute(\'value\')){
										inputs[i].parentElement.classList.add(\'is-filled\');
									}
								  }
								  
								for (var i = 0; i < textareas.length; i++) {
            						textareas[i].addEventListener(\'focus\', function(e) {
            						  this.parentElement.classList.add(\'is-focused\');
            						}, false);
            
            						textareas[i].onkeyup = function(e) {
            						  if (this.value != "") {
            							this.parentElement.classList.add(\'is-filled\');
            						  } else {
            							this.parentElement.classList.remove(\'is-filled\');
            						  }
            						};
            
            						textareas[i].addEventListener(\'focusout\', function(e) {
            							if (this.value != "") {
            								this.parentElement.classList.add(\'is-filled\');
            							}
            							this.parentElement.classList.remove(\'is-focused\');
            						}, false);
            						
            						textareas[i].parentElement.classList.add(\'is-filled\');
            					  }
								
							</script>';

	$htmlWeekGameDetail .= '<tr>
		<td style="vertical-align:text-top;width: 50%; border-right: gray thin solid; ">
			<div id="lista">';
	$sql2 = "SELECT a.Jugador_ID,
				a.Numero,
				a.Nombre,
				a.Apellido_P,
				a.Apellido_M,
				ifnull(sum(b.CantidadP),0) as AmarillasP,
				ifnull(sum(b.CantidadT),0) as AmarillasT,
				ifnull(c.Cantidad,0) as Rojas,
				ifnull(sum(d.Puntos1),0) as Puntos1,
				ifnull(sum(d.Puntos2),0) as Puntos2,
				ifnull(sum(d.Puntos3),0) as Puntos3,
				ifnull(c.Comentario, '') Comentario,
				ifnull(c.Dias_Castigo,0) Dias_Castigo,
				ifnull(c.Multa, 0) Multa,
				ifnull(c.Pagado, 0) Pagado,
				ifnull(jj.Jugado, 0) Jugado
			FROM $schema.Jugadores a
				left outer join $schema.AmonestadosB b on a.Jugador_ID = b.Jugador_ID and b.Torneo_ID = $Season and Juego_ID = $Game
				left outer join $schema.Expulsados c on a.Jugador_ID = c.Jugador_ID and c.Torneo_ID = $Season and c.Juego_ID = $Game
				left outer join $schema.PuntosB d on a.Jugador_ID = d.Jugador_ID and d.Torneo_ID = $Season and d.Juego_ID = $Game
				left outer join $schema.JugadorJugado jj on a.Jugador_ID = jj.Jugador_ID and jj.Torneo_ID = $Season and jj.Juego_ID = $Game
			where a.Equipo_ID = $lequipoid and a.Estatus in ('A', 'D')
			group by a.Numero,
				a.Nombre,
				a.Apellido_P,
				a.Apellido_M,
				a.Fecha_Nacimiento
			order by a.Estatus desc, cast(a.Numero as decimal) asc;";
			//echo $sql2;
	$result2 = $Config->query($sql2);
	$htmlWeekGameDetail .= '<div id="all" class="datagridTeamSchedule">
					<h2 style="margin-top: 10;">' . $lequipodesc . '</h2>
					<table id="localList" width="100%">
						<thead>
							<tr>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Jugador_ID</th>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Jugado</th>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">FP</th>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">FT</th>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">EX</th>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">P1</th>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">P2</th>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">P3</th>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Roja Dias</th>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Roja Multa</th>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Roja Comentarios</th>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Roja Pago</th>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1">' . $lang['312'] . '</th>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1">' . $lang['313'] . ' ' . $lang['314'] . '</th>
								<th style="text-align:center; padding: 0rem 0rem;" ' . $Config->JugadorJugado . '><img src="imagenes/gamePlayedBasket.png" width="20" height="25" alt=""/></th>
								<th style="text-align:center; padding: 0rem 0rem;"><span style="text-align: center">FP</span></th>
								<th style="text-align:center; padding: 0rem 0rem;"><span style="text-align: center">FT</span></th>
								<th style="text-align:center; padding: 0rem 0rem;"><span style="text-align: center">EX</span></th>
								<th style="text-align:center; padding: 0rem 0rem;"><span style="text-align: center"><img src="imagenes/Pointb1.png" width="20" height="20" alt=""/></span></th>
								<th style="text-align:center; padding: 0rem 0rem;"><span style="text-align: center"><img src="imagenes/Pointb2.png" width="20" height="20" alt=""/></span></th>
								<th style="text-align:center; padding: 0rem 0rem;"><span style="text-align: center"><img src="imagenes/Pointb3.png" width="20" height="20" alt=""/></span></th>
						</tr>
					</thead>
					<tbody>';
	 $count = 0;
	 if($result2){
		 if ($result2->num_rows > 0) {
			// output data of each row
			while($row2 = $result2->fetch_assoc()) {
				if (($count % 2) == 1){
					$htmlWeekGameDetail .= "<tr>";
				}else{
					$htmlWeekGameDetail .= "<tr class='alt'>";
				}
				$htmlWeekGameDetail .= "
				<td style='text-align:left' hidden='true'>" . $row2["Jugador_ID"] . "</td>
				<td style='text-align:left' hidden='true'>" . $row2["Jugado"] . "</td>
				<td style='text-align:left' hidden='true'>" . $row2["AmarillasP"] . "</td>
				<td style='text-align:left' hidden='true'>" . $row2["AmarillasT"] . "</td>
				<td style='text-align:left' id='rojasource" . $row2["Jugador_ID"] . "' hidden='true'>" . $row2["Rojas"] . "</td>
				<td style='text-align:left' hidden='true'>" . $row2["Puntos1"] . "</td>
				<td style='text-align:left' hidden='true'>" . $row2["Puntos2"] . "</td>
				<td style='text-align:left' hidden='true'>" . $row2["Puntos3"] . "</td>				
				<td style='text-align:left' hidden='true'><input type='text' id='rojaDias" . $row2["Jugador_ID"] . "' value='" . $row2["Dias_Castigo"] . "'></td>
				<td style='text-align:left' hidden='true'><input type='text' id='rojaMulta" . $row2["Jugador_ID"] . "' value='" . $row2["Multa"] . "'></td>
				<td style='text-align:left' hidden='true'><input type='text' id='rojaComentario" . $row2["Jugador_ID"] . "' value=' " . $row2["Comentario"] . "'></td>
				<td style='text-align:left' hidden='true'><input type='text' id='rojaPagado" . $row2["Jugador_ID"] . "' value=' " . $row2["Pagado"] . "'></td>
				<td style='text-align:left'><span class='text-secondary text-xs font-weight-normal'>" . $row2["Numero"] . "</span></td>
				<td style='text-align:left'><span class='text-secondary text-xs font-weight-normal'>" . $row2["Nombre"] . " " . $row2["Apellido_P"] . " " . $row2["Apellido_M"] . "</span></td>
				<td style='text-align:center' " .  $Config->JugadorJugado . ">";
				 if($row2["Jugado"] == 1){   
					$htmlWeekGameDetail .= "<input type='checkbox' checked name='jugado' id='jugado" . $row2["Jugador_ID"] . "'>";
				 }else{
					$htmlWeekGameDetail .= "<input type='checkbox' name='jugado' id='jugado" . $row2["Jugador_ID"] . "'>";
				 }
				$htmlWeekGameDetail .= "</td>
				<td style='text-align:center'>
					<input maxlength='1' size='1' type='text' name='amarilla' id='amarilla" . $row2["Jugador_ID"] . "' value='" . $row2["AmarillasP"] . "' style='width:28px'>
				</td>
				<td style='text-align:center'>
					<input maxlength='1' size='1' type='text' name='amarilla' id='amarilla" . $row2["Jugador_ID"] . "' value='" . $row2["AmarillasT"] . "' style='width:28px'>
				</td>
				<td style='text-align:center'>";
				 if($row2["Rojas"] > 0){   
					$htmlWeekGameDetail .= "<input type='checkbox' name='roja' id='roja" . $row2["Jugador_ID"] . "' checked onClick='if(this.checked){
																																				$(\"#rojaS" . $row2["Jugador_ID"] . "\").prop(\"checked\", true);
																																				$(\"#rojaInputDiv\").css(\"z-index\", \"2\");
																																				loadWeekAdminGameDetailRoja($(\"#rojaComentario" . $row2["Jugador_ID"] . "\").val(), " . $row2["Jugador_ID"] . ", $(\"#rojaDias" . $row2["Jugador_ID"] . "\").val(), $(\"#rojaMulta" . $row2["Jugador_ID"] . "\").val(),$(\"#rojaPagado" . $row2["Jugador_ID"] . "\").val(),\"\");
																																			}else{
																																				$(\"#rojaInputDiv\").css(\"z-index\", \"2\");
																																				$(\"#roja" . $row2["Jugador_ID"] . "\").prop(\"checked\", true);
																																				$(\"#rojaS" . $row2["Jugador_ID"] . "\").prop(\"checked\", true);
																																				loadWeekAdminGameDetailRoja($(\"#rojaComentario" . $row2["Jugador_ID"] . "\").val(), " . $row2["Jugador_ID"] . ", $(\"#rojaDias" . $row2["Jugador_ID"] . "\").val(), $(\"#rojaMulta" . $row2["Jugador_ID"] . "\").val(),$(\"#rojaPagado" . $row2["Jugador_ID"] . "\").val(),\"\");
																																			}' title='" . $lang['655'] . " " . $row2["Comentario"] . ", " . $lang['656'] . " " . $row2["Multa"] . ", " . $lang['657'] . " " . $row2["Dias_Castigo"] . "'>";
				 }else{
					$htmlWeekGameDetail .= "<input type='checkbox' name='roja' id='roja" . $row2["Jugador_ID"] . "' onClick='if(this.checked){
																																		$(\"#rojaS" . $row2["Jugador_ID"] . "\").prop(\"checked\", true);
																																		$(\"#rojaInputDiv\").css(\"z-index\", \"2\");
																																		loadWeekAdminGameDetailRoja($(\"#rojaComentario" . $row2["Jugador_ID"] . "\").val(), " . $row2["Jugador_ID"] . ", $(\"#rojaDias" . $row2["Jugador_ID"] . "\").val(), $(\"#rojaMulta" . $row2["Jugador_ID"] . "\").val(),$(\"#rojaPagado" . $row2["Jugador_ID"] . "\").val(),\"\");
																																	}else{
																																		$(\"#rojaInputDiv\").css(\"z-index\", \"2\");
																																		$(\"#roja" . $row2["Jugador_ID"] . "\").prop(\"checked\", true);
																																		$(\"#rojaS" . $row2["Jugador_ID"] . "\").prop(\"checked\", true);
																																	loadWeekAdminGameDetailRoja($(\"#rojaComentario" . $row2["Jugador_ID"] . "\").val(), " . $row2["Jugador_ID"] . ", $(\"#rojaDias" . $row2["Jugador_ID"] . "\").val(), $(\"#rojaMulta" . $row2["Jugador_ID"] . "\").val(),$(\"#rojaPagado" . $row2["Jugador_ID"] . "\").val(),\"\");
																																	}' title='" . $lang['655'] . " " . $row2["Comentario"] . ", " . $lang['656'] . " " . $row2["Multa"] . ", " . $lang['657'] . " " . $row2["Dias_Castigo"] . "'>";
				 }
				$htmlWeekGameDetail .= "</td>
				<td style='text-align:center'>" . $row2["Puntos1"] . "</td>
				<td style='text-align:center'>" . $row2["Puntos2"] . "</td>
				<td style='text-align:center'>" . $row2["Puntos3"] . "</td>

			</tr>";
			$count++;
			}
		 }
	}
	$htmlWeekGameDetail .= '</tbody>
				</table>
			</div>
		</div>
	</td>
	  <td style="vertical-align:text-top; width: 50%">
		<div id="lista">';   
		$sql2 = "SELECT a.Jugador_ID,
				a.Numero,
				a.Nombre,
				a.Apellido_P,
				a.Apellido_M,
				ifnull(sum(b.CantidadP),0) as AmarillasP,
				ifnull(sum(b.CantidadT),0) as AmarillasT,
				ifnull(c.Cantidad,0) as Rojas,
				ifnull(sum(d.Puntos1),0) as Puntos1,
				ifnull(sum(d.Puntos2),0) as Puntos2,
				ifnull(sum(d.Puntos3),0) as Puntos3,
				ifnull(c.Comentario, '') Comentario,
				ifnull(c.Dias_Castigo,0) Dias_Castigo,
				ifnull(c.Multa, 0) Multa,
				ifnull(c.Pagado, 0) Pagado,
				ifnull(jj.Jugado, 0) Jugado
			FROM $schema.Jugadores a
				left outer join $schema.AmonestadosB b on a.Jugador_ID = b.Jugador_ID and b.Torneo_ID = $Season and Juego_ID = $Game
				left outer join $schema.Expulsados c on a.Jugador_ID = c.Jugador_ID and c.Torneo_ID = $Season and c.Juego_ID = $Game
				left outer join $schema.PuntosB d on a.Jugador_ID = d.Jugador_ID and d.Torneo_ID = $Season and d.Juego_ID = $Game
				left outer join $schema.JugadorJugado jj on a.Jugador_ID = jj.Jugador_ID and jj.Torneo_ID = $Season and jj.Juego_ID = $Game
			where a.Equipo_ID = $vequipoid and a.Estatus in ('A', 'D')
			group by a.Numero,
				a.Nombre,
				a.Apellido_P,
				a.Apellido_M,
				a.Fecha_Nacimiento
			order by a.Estatus desc, cast(a.Numero as decimal) asc;";
			//echo $sql2;
	$result2 = $Config->query($sql2);
	$htmlWeekGameDetail .= '<div id="all" class="datagridTeamSchedule">
			 <h2 style="margin-top: 10;">' . $vequipodesc . '</h2>
			  <table id="visitanteList" width="100%">
				  <thead>
					  <tr>
                            <th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Jugador_ID</th>
                            <th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Jugado</th>
                            <th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">FP</th>
                            <th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">FT</th>
                            <th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">EX</th>
                            <th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">P1</th>
                            <th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">P2</th>
                            <th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">P3</th>
                            <th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Roja Dias</th>
                            <th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Roja Multa</th>
                            <th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Roja Comentarios</th>
                            <th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Roja Pago</th>
                            <th style="text-align:left; padding: 0rem 0rem;" colspan="1">' . $lang['312'] . '</th>
                            <th style="text-align:left; padding: 0rem 0rem;" colspan="1">' . $lang['313'] . ' ' . $lang['314'] . '</th>
                            <th style="text-align: center; padding: 0rem 0rem;" ' . $Config->JugadorJugado . '><img src="imagenes/gamePlayedVoleibol.png" width="20" height="25" alt=""/></th>
                            <th style="text-align:center; padding: 0rem 0rem;"><span style="text-align: center">FP</span></th>
                            <th style="text-align:center; padding: 0rem 0rem;"><span style="text-align: center">FT</span></th>
                            <th style="text-align:center; padding: 0rem 0rem;"><span style="text-align: center">EX</span></th>
                            <th style="text-align:center; padding: 0rem 0rem;"><span style="text-align: center"><img src="imagenes/Pointb1.png" width="20" height="20" alt=""/></span></th>
                            <th style="text-align:center; padding: 0rem 0rem;"><span style="text-align: center"><img src="imagenes/Pointb2.png" width="20" height="20" alt=""/></span></th>
                            <th style="text-align:center; padding: 0rem 0rem;"><span style="text-align: center"><img src="imagenes/Pointb3.png" width="20" height="20" alt=""/></span></th>
					  </tr>
				  </thead>
				  <tbody>';
	 $count = 0;
	 if($result2){
		 if ($result2->num_rows > 0) {
			// output data of each row
			while($row2 = $result2->fetch_assoc()) {
				if (($count % 2) == 1){
					$htmlWeekGameDetail .= "<tr>";
				}else{
					$htmlWeekGameDetail .= "<tr class='alt'>";
				}
				$htmlWeekGameDetail .= "
				<td style='text-align:left' hidden='true'>" . $row2["Jugador_ID"] . "</td>
				<td style='text-align:left' hidden='true'>" . $row2["Jugado"] . "</td>
				<td style='text-align:left' hidden='true'>" . $row2["AmarillasP"] . "</td>
				<td style='text-align:left' hidden='true'>" . $row2["AmarillasT"] . "</td>
				<td style='text-align:left' id='rojasource" . $row2["Jugador_ID"] . "' hidden='true'>" . $row2["Rojas"] . "</td>
				<td style='text-align:left' hidden='true'>" . $row2["Puntos1"] . "</td>
				<td style='text-align:left' hidden='true'>" . $row2["Puntos2"] . "</td>
				<td style='text-align:left' hidden='true'>" . $row2["Puntos3"] . "</td>				
				<td style='text-align:left' hidden='true'><input type='text' id='rojaDias" . $row2["Jugador_ID"] . "' value='" . $row2["Dias_Castigo"] . "'></td>
				<td style='text-align:left' hidden='true'><input type='text' id='rojaMulta" . $row2["Jugador_ID"] . "' value='" . $row2["Multa"] . "'></td>
				<td style='text-align:left' hidden='true'><input type='text' id='rojaComentario" . $row2["Jugador_ID"] . "' value=' " . $row2["Comentario"] . "'></td>
				<td style='text-align:left' hidden='true'><input type='text' id='rojaPagado" . $row2["Jugador_ID"] . "' value=' " . $row2["Pagado"] . "'></td>
				<td style='text-align:left'><span class='text-secondary text-xs font-weight-normal'>" . $row2["Numero"] . "</span></td>
				<td style='text-align:left'><span class='text-secondary text-xs font-weight-normal'>" . $row2["Nombre"] . " " . $row2["Apellido_P"] . " " . $row2["Apellido_M"] . "</span></td>
				<td style='text-align:center' " .  $Config->JugadorJugado . ">";
				 if($row2["Jugado"] > 0){   
						$htmlWeekGameDetail .= "<input type='checkbox' checked name='jugado' id='jugado" . $row2["Jugador_ID"] . "'>";
				 }else{
						$htmlWeekGameDetail .= "<input type='checkbox' name='jugado' id='jugado" . $row2["Jugador_ID"] . "'>";
				 }
				$htmlWeekGameDetail .= "</td>
				<td style='text-align:center'>
					<input maxlength='1' size='1' type='text' name='amarilla' id='amarilla" . $row2["Jugador_ID"] . "' value='" . $row2["AmarillasP"] . "' style='width:28px'>
				</td>
				<td style='text-align:center'>
					<input maxlength='1' size='1' type='text' name='amarilla' id='amarilla" . $row2["Jugador_ID"] . "' value='" . $row2["AmarillasT"] . "' style='width:28px'>
				</td>
				<td style='text-align:center'>";
				 if($row2["Rojas"] > 0){   
					$htmlWeekGameDetail .= "<input type='checkbox' name='roja' id='roja" . $row2["Jugador_ID"] . "' checked onClick='if(this.checked){
																																				$(\"#rojaS" . $row2["Jugador_ID"] . "\").prop(\"checked\", true);
																																				$(\"#rojaInputDiv\").css(\"z-index\", \"2\");
																																				loadWeekAdminGameDetailRoja($(\"#rojaComentario" . $row2["Jugador_ID"] . "\").val(), " . $row2["Jugador_ID"] . ", $(\"#rojaDias" . $row2["Jugador_ID"] . "\").val(), $(\"#rojaMulta" . $row2["Jugador_ID"] . "\").val(),$(\"#rojaPagado" . $row2["Jugador_ID"] . "\").val(),\"\");
																																			}else{
																																				$(\"#rojaInputDiv\").css(\"z-index\", \"2\");
																																				$(\"#roja" . $row2["Jugador_ID"] . "\").prop(\"checked\", true);
																																				$(\"#rojaS" . $row2["Jugador_ID"] . "\").prop(\"checked\", true);
																																				loadWeekAdminGameDetailRoja($(\"#rojaComentario" . $row2["Jugador_ID"] . "\").val(), " . $row2["Jugador_ID"] . ", $(\"#rojaDias" . $row2["Jugador_ID"] . "\").val(), $(\"#rojaMulta" . $row2["Jugador_ID"] . "\").val(),$(\"#rojaPagado" . $row2["Jugador_ID"] . "\").val(),\"\");
																																			}' title='" . $lang['655'] . " " . $row2["Comentario"] . ", " . $lang['656'] . " " . $row2["Multa"] . ", " . $lang['657'] . " " . $row2["Dias_Castigo"] . "'>";
				 }else{
					$htmlWeekGameDetail .= "<input type='checkbox' name='roja' id='roja" . $row2["Jugador_ID"] . "' onClick='if(this.checked){
																																		$(\"#rojaS" . $row2["Jugador_ID"] . "\").prop(\"checked\", true);
																																		$(\"#rojaInputDiv\").css(\"z-index\", \"2\");
																																		loadWeekAdminGameDetailRoja($(\"#rojaComentario" . $row2["Jugador_ID"] . "\").val(), " . $row2["Jugador_ID"] . ", $(\"#rojaDias" . $row2["Jugador_ID"] . "\").val(), $(\"#rojaMulta" . $row2["Jugador_ID"] . "\").val(),$(\"#rojaPagado" . $row2["Jugador_ID"] . "\").val(),\"\");
																																	}else{
																																		$(\"#rojaInputDiv\").css(\"z-index\", \"2\");
																																		$(\"#roja" . $row2["Jugador_ID"] . "\").prop(\"checked\", true);
																																		$(\"#rojaS" . $row2["Jugador_ID"] . "\").prop(\"checked\", true);
																																	loadWeekAdminGameDetailRoja($(\"#rojaComentario" . $row2["Jugador_ID"] . "\").val(), " . $row2["Jugador_ID"] . ", $(\"#rojaDias" . $row2["Jugador_ID"] . "\").val(), $(\"#rojaMulta" . $row2["Jugador_ID"] . "\").val(),$(\"#rojaPagado" . $row2["Jugador_ID"] . "\").val(),\"\");
																																	}' title='" . $lang['655'] . " " . $row2["Comentario"] . ", " . $lang['656'] . " " . $row2["Multa"] . ", " . $lang['657'] . " " . $row2["Dias_Castigo"] . "'>";
				 }
				$htmlWeekGameDetail .= "</td>
				<td style='text-align:center'>" . $row2["Puntos1"] . "</td>
				<td style='text-align:center'>" . $row2["Puntos2"] . "</td>
				<td style='text-align:center'>" . $row2["Puntos3"] . "</td>

			</tr>";
			$count++;
			}
		} 
	}
	$htmlWeekGameDetail .= '</tbody>
					</table>
				</div>
			</div>
		</td>
	  </tr>';
	
	if($Config->JuegoCedulas == 0 ){
		$htmlWeekGameDetail .= "<!--";
	}
	$htmlWeekGameDetail .= '<tr>
								<td colspan="3" style="align-content:center; text-align:center"  id="docs' . $Game . '">';
 	$htmlWeekGameDetail .= '<table width="100%" border="1" cellspacing="0" cellpadding="0">
              <tr>
                <th width="225">' . $lang['317'] . '</th>
                <th width="225">' . $lang['318'] . '</th>
                <th width="225">' . $lang['319'] . '</th>
                <th width="225">' . $lang['320'] . '</th>
                <th width="90">' . $lang['316'] . '</th>
              </tr>
              <tr style="height:30px;">
                <td style="height:30px; text-align: center;">
                    <span style="text-align: center;">';
	if(file_exists ('../../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo1.png')){
		$htmlWeekGameDetail .= '<img id="anexo1' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo1.png?tmp=' . $fecha->getTimestamp() . '" alt="Foto" width="250" height="187">';
	}
    $htmlWeekGameDetail .= '</span>
                </td>
                <td style="height:30px; text-align: center;">
                    <span style="text-align: center; ">';
	if(file_exists ('../../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo2.png')){
		$htmlWeekGameDetail .= '<img id="anexo2' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo2.png?tmp=' . $fecha->getTimestamp() . '" alt="Foto" width="250" height="187">';
	}
    $htmlWeekGameDetail .= '</span>
                 </td>
                <td style="height:30px; text-align: center;">
                    <span style="text-align: center; ">';
	if(file_exists ('../../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo3.png')){
		$htmlWeekGameDetail .= '<img id="anexo3' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo3.png?tmp=' . $fecha->getTimestamp() . '" alt="Foto" width="250" height="187">';
	}
    $htmlWeekGameDetail .= '</span>
                 </td>
                <td style="height:30px; text-align: center;">
                    <span style="text-align: center; ">';
	if(file_exists ('../../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo4.png')){
		$htmlWeekGameDetail .= '<img id="anexo4' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo4.png?tmp=' . $fecha->getTimestamp() . '" alt="Foto" width="250" height="187">';
	}
    $htmlWeekGameDetail .= '</span>
                 </td>
                <td style="height:30px; text-align: center;">
                    <span style="text-align: center; ">
                        <img id="" src="imagenes/edit.png" width="30" height="30" onclick="$(\'#gameDocInputDiv\').css(\'z-index\', \'2\'); loadWeekAdminGameDetailDocs(' . $Season . ', ' . $Week . ', ' . $Game . ')"/>
                    </span>
                 </td>
              </tr>
            </table>
		</td>
	</tr>';
	if($Config->JuegoCedulas == 0){
		$htmlWeekGameDetail .= "-->";
	}
	$htmlWeekGameDetail .= '<tr><td style="height: 30;"></td></tr>';
	$htmlWeekGameDetail .= '</table>';
    $retunData = array('status' => '1', 'message' => 'Success.', 'dataWeekGameDetail' => $htmlWeekGameDetail);
    $Config->Close();
    echo json_encode($retunData);
?>