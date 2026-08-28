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
	$sessionstat = $fgmembersite->CheckLogin('weekAdmin-ScheduleScoresGameDetailS.php');
	
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
	$fecha = new DateTime();

	$lequipoid = "";
	$lequipo = "";
	$lequipodesc = "";
	$llogo = "";

	$vequipoid = "";
	$vequipo = "";
	$vequipodesc = "";
	$vlogo = "";

	$jugado = 0;

	$sql = "select Local_ID from $schema.Juegos
			where Torneo_ID = $Season and Juego_ID = $Game and Jugado = 1;";
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			$jugado = 1;
		}
	}

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
				Comentarios
			from  $schema.Juegos as j 
				join $schema.Jornada as jo on j.Fecha between jo.Fecha_Inicio and jo.Fecha_Fin
			where jo.Jornada_ID = $Week and j.Juego_ID = $Game $sqlcat;";
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			$comentario = $row["Comentarios"];
			$arbitro = $row["Arbitro"];
			$extral = $row["Extra_Local"];
			$extrav = $row["Extra_Visitante"];
		}
	}
	$Config->LoadFlags();


    $retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');

	$htmlWeekGameDetail = '<table width="98%" id="fichaTecnicaEdit" >
							<tr >
								<td style="text-align:left; border: none; width: 100%">
									<div class="row">
										<div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
											<div style="text-align:left;border: none;margin-top: 10px;">
												<div class="input-group input-group-outline my-3" style="margin-top: -5px !important;margin-bottom: 0px !important;">
													<label class="form-label">' . $lang['607'] . '</label>
													<input type="text" class="form-control" name="arbitroS' . $Game . '" id="arbitroS' . $Game . '" value="' . $arbitro . '"/>
												</div>
											</div>
										</div>
									</div>
								</td>
							</tr>
							<tr>
								<td style="text-align:left; border: none; width: 100%">
									<div class="row">
										<div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
											<div style="text-align:left;border: none;margin-top: 10px;">
												<div class="input-group input-group-outline my-3" style="margin-top: -5px !important;margin-bottom: 0px !important;">
													<label class="form-label">' . $lang['645'] . '</label>
													<input type="text" class="form-control" name="comentarioS' . $Game . '" id="comentarioS' . $Game . '" value="' . $comentario . '"/>
												</div>
											</div>
										</div>
									</div>
								</td>
							</tr>
							<tr>
								<td style="text-align:left; border: none; width: 100%">
									<div class="row">
										<div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">
											<div style="text-align:left;border: none;margin-top: 10px;">
												<div class="input-group input-group-outline my-3" style="margin-top: -5px !important;margin-bottom: 0px !important;">
													<label class="form-label">' . $lang['648'] . ' ' . $lang['650'] . '</label>
													<input type="text" class="form-control" name="extralS' . $Game . '" id="extralS' . $Game . '" value="' . $extral . '"/>
												</div>
											</div>
										</div>
										<div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">
											<div style="text-align:left;border: none;margin-top: 10px;">
												<div class="input-group input-group-outline my-3" style="margin-top: -5px !important;margin-bottom: 0px !important;">
													<label class="form-label">' . $lang['648'] . ' ' . $lang['651'] . '</label>
													<input type="text" class="form-control" name="extravS' . $Game . '" id="extravS' . $Game . '" value="' . $extrav . '"/>
												</div>
											</div>
										</div>
										<div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">
											<div style="text-align:left; border: none; margin-top: 10px !important">
												<button style="margin-top: -5px; margin-bottom: 15px;" type="button" class="btn btn-primary" onclick="SaveGameDetailPlayerStatsSR(' . $Season . ',' . $Week . ',' . $Game . ',' . $lequipoid . ',' . $vequipoid . ', $(\'#arbitroS' . $Game . '\').val(), $(\'#comentarioS' . $Game . '\').val(), $(\'#extralS' . $Game . '\').val(), $(\'#extravS' . $Game . '\').val());">' . $lang['0000'] . '</button>
											</div>
										</div>
									</div>
								</td>
							</tr>
							<script>
								var inputs = document.querySelectorAll(\'input\');
								
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
								
							</script>
						</table>';

	$htmlWeekGameDetail .= '<div class="container-fluid py-0" style="padding-left: 0px; padding-right: 0px;">
								<div class="nav-wrapper position-relative end-0">
									<ul class="nav nav-pills nav-fill p-1" role="tablist" style="background: #cee6ff; flex-direction: unset !important;" id="scheduleS">
										<li class="nav-item" id="local' . $Game . 'Sli">
											<a class="nav-link mb-0 px-0 py-1 active" data-bs-toggle="tab" style="cursor: pointer;" callval="#local' . $Game . 'S" role="tab" aria-controls="local' . $Game . 'li" aria-selected="true">
												<img src="./imagenes/Original/' . $llogo . '.png?tmp=' . $fecha->getTimestamp() . '" style="width: 20px; height: auto;" alt=""/>
											</a>
										</li>
										<li class="nav-item" id="visitante' . $Game . 'Sli">
											<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" style="cursor: pointer;" callval="#visitante' . $Game . 'S" role="tab" aria-controls="visitante' . $Game . 'li" aria-selected="false">
												<img src="./imagenes/Original/' . $vlogo . '.png?tmp=' . $fecha->getTimestamp() . '" style="width: 20px; height: auto;" alt=""/>
											</a>
										</li>';
	if($Config->JuegoCedulas == 1 ){
		$htmlWeekGameDetail .= '<li class="nav-item" id="docs' . $Game . 'Sli">
											<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" style="cursor: pointer;" callval="#docs' . $Game . 'S" role="tab" aria-controls="docs' . $Game . 'li" aria-selected="false">
												<img src="./imagenes/documents.png" style="width: 20px; height: auto;" alt=""/>
											</a>
										</li>';
	}
	$htmlWeekGameDetail .= '		</ul>
								</div>
							</div><script>initNavs(\'scheduleS\');</script>';
							
	$htmlWeekGameDetail .= '<div class="container-fluid py-0" style="padding-left: 0px; padding-right: 0px;">
								<div id="local' . $Game . 'S" class="tabla active" style="display: block">
									<div class="d-block  d-xs-block d-md-block d-lg-block d-xl-none">
										<div class="card">
											<div class="table-responsive">
												<table id="local' . $Game . 'T" class=" table align-items-center mb-0" style="border-color: #136aeb;">';
	$htmlWeekGameDetail .= '
						<thead>
							<tr>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Jugador_ID</th>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Jugado</th>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Amarillas</th>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Rojas</th>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Goles</th>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Roja Dias</th>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Roja Multa</th>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Roja Comentarios</th>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Roja Pago</th>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1">' . $lang['312'] . '</th>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1">' . $lang['313'] . ' ' . $lang['314'] . '</th>
								<th style="text-align: center; padding: 0rem 0rem;" ' . $Config->JugadorJugado . '><img src="imagenes/gamePlayed.png" width="20" height="25" alt=""/></th>
								<th style="text-align: center; padding: 0rem 0rem;"><img src="imagenes/amarilla.png" width="20" height="25" alt=""/></th>
								<th style="text-align:center; padding: 0rem 0rem;"><span style="text-align: center"><img src="imagenes/roja.png" width="20" height="25" alt=""/></span></th>
								<th style="text-align:center; padding: 0rem 0rem;"><span style="text-align: center"><img src="imagenes/goal.png" width="20" height="20" alt=""/></span></th>
						</tr>
					</thead>
					<tbody>';
	
	$sql2 = "SELECT a.Jugador_ID,
				a.Numero,
				a.Nombre,
				a.Apellido_P,
				a.Apellido_M,
				ifnull(sum(b.Cantidad),0) as Amarillas,
				ifnull(c.Cantidad,0) as Rojas,
				ifnull(sum(d.Goles),0) as Goles,
				ifnull(c.Comentario, '') Comentario,
				ifnull(c.Dias_Castigo,0) Dias_Castigo,
				ifnull(c.Multa, 0) Multa,
				ifnull(c.Pagado, 0) Pagado,
				ifnull(jj.Jugado, 0) Jugado
			FROM $schema.Jugadores a
				left outer join $schema.Amonestados b on a.Jugador_ID = b.Jugador_ID and b.Torneo_ID = $Season and Juego_ID = $Game
				left outer join $schema.Expulsados c on a.Jugador_ID = c.Jugador_ID and c.Torneo_ID = $Season and c.Juego_ID = $Game
				left outer join $schema.Goles d on a.Jugador_ID = d.Jugador_ID and d.Torneo_ID = $Season and d.Juego_ID = $Game
				left outer join $schema.JugadorJugado jj on a.Jugador_ID = jj.Jugador_ID and jj.Torneo_ID = $Season and jj.Juego_ID = $Game
			where a.Equipo_ID = $lequipoid and a.Estatus in ('A', 'D')
			group by a.Numero,
				a.Nombre,
				a.Apellido_P,
				a.Apellido_M,
				a.Fecha_Nacimiento,
				ifnull(c.Cantidad,0),
				c.Comentario,
				c.Dias_Castigo,
				c.Multa
			order by a.Estatus desc, cast(a.Numero as decimal) asc;";
	$result2 = $Config->query($sql2);

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
				<td style='text-align:left' hidden='true'>" . $row2["Amarillas"] . "</td>
				<td style='text-align:left' id='rojasource" . $row2["Jugador_ID"] . "' hidden='true'>" . $row2["Rojas"] . "</td>
				<td style='text-align:left' hidden='true'>" . $row2["Goles"] . "</td>
				<td style='text-align:left' hidden='true'><input type='text' id='rojaDiasS" . $row2["Jugador_ID"] . "' value='" . $row2["Dias_Castigo"] . "'></td>
				<td style='text-align:left' hidden='true'><input type='text' id='rojaMultaS" . $row2["Jugador_ID"] . "' value='" . $row2["Multa"] . "'></td>
				<td style='text-align:left' hidden='true'><input type='text' id='rojaComentarioS" . $row2["Jugador_ID"] . "' value=' " . $row2["Comentario"] . "'></td>
				<td style='text-align:left' hidden='true'><input type='text' id='rojaPagadoS" . $row2["Jugador_ID"] . "' value=' " . $row2["Pagado"] . "'></td>

				<td style='text-align:left'><span class='text-secondary text-xs font-weight-normal'>" . $row2["Numero"] . "</span></td>
				<td style='text-align:left'><span class='text-secondary text-xs font-weight-normal'>" . $row2["Nombre"] . " " . $row2["Apellido_P"] . " " . $row2["Apellido_M"] . "</span></td>
				<td style='text-align:center' " .  $Config->JugadorJugado . ">";
				 if($row2["Jugado"] == 1){   
					$htmlWeekGameDetail .= "<input type='checkbox' checked name='jugadoS' id='jugadoS" . $row2["Jugador_ID"] . "'>";
				 }else{
					$htmlWeekGameDetail .= "<input type='checkbox' name='jugadoS' id='jugadoS" . $row2["Jugador_ID"] . "'>";
				 }
				$htmlWeekGameDetail .= "</td>
				<td style='text-align:center'>
					<input maxlength='1' size='1' type='text' name='amarillaS' id='amarillaS" . $row2["Jugador_ID"] . "' value='" . $row2["Amarillas"] . "' style='width:28px'>
				</td>
				<td style='text-align:center'>";
				 if($row2["Rojas"] > 0){   
					$htmlWeekGameDetail .= "<input type='checkbox' name='rojaS' id='rojaS" . $row2["Jugador_ID"] . "' checked onClick='if(this.checked){
																																				$(\"#roja" . $row2["Jugador_ID"] . "\").prop(\"checked\", true);
																																				$(\"#rojaInputDiv\").css(\"z-index\", \"2\");
																																				loadWeekAdminGameDetailRoja($(\"#rojaComentarioS" . $row2["Jugador_ID"] . "\").val(), " . $row2["Jugador_ID"] . ", $(\"#rojaDiasS" . $row2["Jugador_ID"] . "\").val(), $(\"#rojaMultaS" . $row2["Jugador_ID"] . "\").val(),$(\"#rojaPagadoS" . $row2["Jugador_ID"] . "\").val(),\"S\");
																																			}else{
																																				$(\"#rojaInputDiv\").css(\"z-index\", \"2\");
																																				$(\"#roja" . $row2["Jugador_ID"] . "\").prop(\"checked\", true);
																																				$(\"#rojaS" . $row2["Jugador_ID"] . "\").prop(\"checked\", true);
																																				loadWeekAdminGameDetailRoja($(\"#rojaComentarioS" . $row2["Jugador_ID"] . "\").val(), " . $row2["Jugador_ID"] . ", $(\"#rojaDiasS" . $row2["Jugador_ID"] . "\").val(), $(\"#rojaMultaS" . $row2["Jugador_ID"] . "\").val(),$(\"#rojaPagadoS" . $row2["Jugador_ID"] . "\").val(),\"S\");
																																			}' title='" . $lang['655'] . " " . $row2["Comentario"] . ", " . $lang['656'] . " " . $row2["Multa"] . ", " . $lang['657'] . " " . $row2["Dias_Castigo"] . "'>";
				 }else{
					$htmlWeekGameDetail .= "<input type='checkbox' name='rojaS' id='rojaS" . $row2["Jugador_ID"] . "' onClick='if(this.checked){
																																		$(\"#roja" . $row2["Jugador_ID"] . "\").prop(\"checked\", true);
																																		$(\"#rojaInputDiv\").css(\"z-index\", \"2\");
																																		loadWeekAdminGameDetailRoja($(\"#rojaComentarioS" . $row2["Jugador_ID"] . "\").val(), " . $row2["Jugador_ID"] . ", $(\"#rojaDiasS" . $row2["Jugador_ID"] . "\").val(), $(\"#rojaMultaS" . $row2["Jugador_ID"] . "\").val(),$(\"#rojaPagadoS" . $row2["Jugador_ID"] . "\").val(),\"S\");
																																	}else{
																																		$(\"#rojaInputDiv\").css(\"z-index\", \"2\");
																																		$(\"#roja" . $row2["Jugador_ID"] . "\").prop(\"checked\", true);
																																		$(\"#rojaS" . $row2["Jugador_ID"] . "\").prop(\"checked\", true);
																																		loadWeekAdminGameDetailRoja($(\"#rojaComentarioS" . $row2["Jugador_ID"] . "\").val(), " . $row2["Jugador_ID"] . ", $(\"#rojaDiasS" . $row2["Jugador_ID"] . "\").val(), $(\"#rojaMultaS" . $row2["Jugador_ID"] . "\").val(),$(\"#rojaPagadoS" . $row2["Jugador_ID"] . "\").val(),\"S\");
																																	}' title='" . $lang['655'] . " " . $row2["Comentario"] . ", " . $lang['656'] . " " . $row2["Multa"] . ", " . $lang['657'] . " " . $row2["Dias_Castigo"] . "'>";
				 }
				$htmlWeekGameDetail .= "</td>
				<td style='text-align:center'>
					<input maxlength='2' size='2' type='text' name='lgolesS' id='lgoleS" . $row2["Jugador_ID"] . "' value='" . $row2["Goles"] . "' style='width:28px'>
				</td>

			</tr>";
			$count++;
			}
		 }
	}
	
	$htmlWeekGameDetail .= '</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		
		<div id="visitante' . $Game . 'S" class="tabla active" style="display: none">
			<div class="d-block  d-xs-block d-md-block d-lg-block d-xl-none">
				<div class="card">
					<div class="table-responsive">
						<table id="visitante' . $Game . 'T" class=" table align-items-center mb-0" style="border-color: #136aeb;">';
	$htmlWeekGameDetail .= '
					<thead>
					  <tr>
						  <th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Jugador_ID</th>
						  <th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Jugado</th>
						  <th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Amarillas</th>
						  <th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Rojas</th>
						  <th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Goles</th>
						  <th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Roja Dias</th>
						  <th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Roja Multa</th>
						  <th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Roja Comentarios</th>
						  <th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Roja Pago</th>
						  <th style="text-align:left; padding: 0rem 0rem;" colspan="1">' . $lang['312'] . '</th>
						  <th style="text-align:left; padding: 0rem 0rem;" colspan="1">' . $lang['313'] . ' ' . $lang['314'] . '</th>
						  <th style="text-align: center; padding: 0rem 0rem;" ' . $Config->JugadorJugado . '><img src="imagenes/gamePlayed.png" width="20" height="25" alt=""/></th>
						  <th style="text-align: center; padding: 0rem 0rem;"><img src="imagenes/amarilla.png" width="20" height="25" alt=""/></th>
						  <th style="text-align:center; padding: 0rem 0rem;"><span style="text-align: center"><img src="imagenes/roja.png" width="20" height="25" alt=""/></span></th>
						  <th style="text-align:center; padding: 0rem 0rem;"><span style="text-align: center"><img src="imagenes/goal.png" width="20" height="20" alt=""/></span></th>
					  </tr>
				  </thead>
				  <tbody>';
				  
	$sql2 = "SELECT a.Jugador_ID,
				a.Numero,
				a.Nombre,
				a.Apellido_P,
				a.Apellido_M,
				ifnull(sum(b.Cantidad),0) as Amarillas,
				ifnull(c.Cantidad,0) as Rojas,
				ifnull(sum(d.Goles),0) as Goles,
				ifnull(c.Comentario, '') Comentario,
				ifnull(c.Dias_Castigo,0) Dias_Castigo,
				ifnull(c.Multa, 0) Multa,
				ifnull(c.Pagado, 0) Pagado,
				ifnull(jj.Jugado, 0) Jugado
			FROM $schema.Jugadores a
				left outer join $schema.Amonestados b on a.Jugador_ID = b.Jugador_ID and b.Torneo_ID = $Season and Juego_ID = $Game
				left outer join $schema.Expulsados c on a.Jugador_ID = c.Jugador_ID and c.Torneo_ID = $Season and c.Juego_ID = $Game
				left outer join $schema.Goles d on a.Jugador_ID = d.Jugador_ID and d.Torneo_ID = $Season and d.Juego_ID = $Game
				left outer join $schema.JugadorJugado jj on a.Jugador_ID = jj.Jugador_ID and jj.Torneo_ID = $Season and jj.Juego_ID = $Game
			where a.Equipo_ID = $vequipoid and a.Estatus in ('A', 'D')
			group by a.Numero,
				a.Nombre,
				a.Apellido_P,
				a.Apellido_M,
				a.Fecha_Nacimiento,
				ifnull(c.Cantidad,0),
				c.Comentario,
				c.Dias_Castigo,
				c.Multa
			order by a.Estatus desc, cast(a.Numero as decimal) asc;";
	$result2 = $Config->query($sql2);
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
				<td style='text-align:left' hidden='true'>" . $row2["Amarillas"] . "</td>
				<td style='text-align:left' id='rojasource" . $row2["Jugador_ID"] . "' hidden='true'>" . $row2["Rojas"] . "</td>
				<td style='text-align:left' hidden='true'>" . $row2["Goles"] . "</td>
				<td style='text-align:left' hidden='true'><input type='text' id='rojaDiasS" . $row2["Jugador_ID"] . "' value='" . $row2["Dias_Castigo"] . "'></td>
				<td style='text-align:left' hidden='true'><input type='text' id='rojaMultaS" . $row2["Jugador_ID"] . "' value='" . $row2["Multa"] . "'></td>
				<td style='text-align:left' hidden='true'><input type='text' id='rojaComentarioS" . $row2["Jugador_ID"] . "' value='" . $row2["Comentario"] . "'></td>
				<td style='text-align:left' hidden='true'><input type='text' id='rojaPagadoS" . $row2["Jugador_ID"] . "' value=' " . $row2["Pagado"] . "'></td>
				<td style='text-align:left'><span class='text-secondary text-xs font-weight-normal'>" . $row2["Numero"] . "</span></td>
				<td style='text-align:left'><span class='text-secondary text-xs font-weight-normal'>" . $row2["Nombre"] . " " . $row2["Apellido_P"] . " " . $row2["Apellido_M"] . "</span></td>
				<td style='text-align:center' " .  $Config->JugadorJugado . ">";
				 if($row2["Jugado"] > 0){   
						$htmlWeekGameDetail .= "<input type='checkbox' checked name='jugadoS' id='jugadoS" . $row2["Jugador_ID"] . "'>";
				 }else{
						$htmlWeekGameDetail .= "<input type='checkbox' name='jugadoS' id='jugadoS" . $row2["Jugador_ID"] . "'>";
				 }
				$htmlWeekGameDetail .= "</td>
				<td style='text-align:center'>
					<input maxlength='1' size='1' type='text' name='amarillaS' id='amarillaS" . $row2["Jugador_ID"] . "' value='" . $row2["Amarillas"] . "' style='width:28px'>
				</td>
				<td style='text-align:center'>";
				 if($row2["Rojas"] > 0){   
					$htmlWeekGameDetail .= "<input type='checkbox' name='rojaS' id='rojaS" . $row2["Jugador_ID"] . "' checked onClick='if(this.checked){
																																				$(\"#roja" . $row2["Jugador_ID"] . "\").prop(\"checked\", true);
																																				$(\"#rojaInputDiv\").css(\"z-index\", \"2\");
																																				loadWeekAdminGameDetailRoja($(\"#rojaComentarioS" . $row2["Jugador_ID"] . "\").val(), " . $row2["Jugador_ID"] . ", $(\"#rojaDiasS" . $row2["Jugador_ID"] . "\").val(), $(\"#rojaMultaS" . $row2["Jugador_ID"] . "\").val(),$(\"#rojaPagadoS" . $row2["Jugador_ID"] . "\").val(),\"S\");
																																			}else{
																																				$(\"#rojaInputDiv\").css(\"z-index\", \"2\");
																																				$(\"#roja" . $row2["Jugador_ID"] . "\").prop(\"checked\", true);
																																				$(\"#rojaS" . $row2["Jugador_ID"] . "\").prop(\"checked\", true);
																																				loadWeekAdminGameDetailRoja($(\"#rojaComentarioS" . $row2["Jugador_ID"] . "\").val(), " . $row2["Jugador_ID"] . ", $(\"#rojaDiasS" . $row2["Jugador_ID"] . "\").val(), $(\"#rojaMultaS" . $row2["Jugador_ID"] . "\").val(),$(\"#rojaPagadoS" . $row2["Jugador_ID"] . "\").val(),\"S\");
																																			}' title='" . $lang['655'] . " " . $row2["Comentario"] . ", " . $lang['656'] . " " . $row2["Multa"] . ", " . $lang['657'] . " " . $row2["Dias_Castigo"] . "'>";
				 }else{
					$htmlWeekGameDetail .= "<input type='checkbox' name='rojaS' id='rojaS" . $row2["Jugador_ID"] . "' onClick='if(this.checked){
																																		$(\"#roja" . $row2["Jugador_ID"] . "\").prop(\"checked\", true);
																																		$(\"#rojaInputDiv\").css(\"z-index\", \"2\");
																																		loadWeekAdminGameDetailRoja($(\"#rojaComentarioS" . $row2["Jugador_ID"] . "\").val(), " . $row2["Jugador_ID"] . ", $(\"#rojaDiasS" . $row2["Jugador_ID"] . "\").val(), $(\"#rojaMultaS" . $row2["Jugador_ID"] . "\").val(),$(\"#rojaPagadoS" . $row2["Jugador_ID"] . "\").val(),\"S\");
																																	}else{
																																		$(\"#rojaInputDiv\").css(\"z-index\", \"2\");
																																		$(\"#roja" . $row2["Jugador_ID"] . "\").prop(\"checked\", true);
																																		$(\"#rojaS" . $row2["Jugador_ID"] . "\").prop(\"checked\", true);
																																		loadWeekAdminGameDetailRoja($(\"#rojaComentarioS" . $row2["Jugador_ID"] . "\").val(), " . $row2["Jugador_ID"] . ", $(\"#rojaDiasS" . $row2["Jugador_ID"] . "\").val(), $(\"#rojaMultaS" . $row2["Jugador_ID"] . "\").val(),$(\"#rojaPagadoS" . $row2["Jugador_ID"] . "\").val(),\"S\");
																																	}'>";
				 }
				$htmlWeekGameDetail .= "</td>
				<td style='text-align:center'>
					<input maxlength='2' size='2' type='text' name='lgolesS' id='lgolesS" . $row2["Jugador_ID"] . "' value='" . $row2["Goles"] . "' style='width:28px'>
				</td>
			</tr>";
			$count++;
			}
		} 
	}
	
	$htmlWeekGameDetail .= '</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>';
		
		
	if($Config->JuegoCedulas == 1 ){
		$htmlWeekGameDetail .= '<div id="docs' . $Game . 'S" class="tabla active" style="display: none">
				
				<div class="justify-content-center d-flex px-0 py-1">
					<div class="align-self-center" style="width: 30%; text-align: right;padding-right: 30px; font-size:3vw;word-wrap: break-word;white-space:pre-wrap;">' . $lang['316'] . '</div>
					<img id="" src="imagenes/edit.png" width="30" height="30" onclick="$(\'#gameDocInputDiv\').css(\'z-index\', \'2\'); loadWeekAdminGameDetailDocs(' . $Season . ', ' . $Week . ', ' . $Game . ')"/>
				</div>
				<div class="justify-content-center d-flex px-0 py-1">
					<div class="align-self-center" style="width: 30%; text-align: right;padding-right: 30px; font-size:3vw;width: 10px;word-wrap: break-word;white-space:pre-wrap;">' . $lang['317'] . '</div>';
		if(file_exists ('../../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo1.png')){
			$htmlWeekGameDetail .= '<img id="anexo1' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo1.png?tmp=' . $fecha->getTimestamp() . '" alt="Foto" style="width: 91%; height: auto;">';
		}
		$htmlWeekGameDetail .= '</div>
				<div class="justify-content-center d-flex px-0 py-1">
					<div class="align-self-center" style="width: 30%; text-align: right;padding-right: 30px; font-size:3vw;width: 10px;word-wrap: break-word;white-space:pre-wrap;">' . $lang['318'] . '</div>';
		if(file_exists ('../../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo2.png')){
			$htmlWeekGameDetail .= '<img id="anexo2' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo2.png?tmp=' . $fecha->getTimestamp() . '" alt="Foto" style="width: 91%; height: auto;">';
		}
		$htmlWeekGameDetail .= '</div>
				<div class="justify-content-center d-flex px-0 py-1">
					<div class="align-self-center" style="width: 30%; text-align: right;padding-right: 30px; font-size:3vw;width: 10px;word-wrap: break-word;white-space:pre-wrap;">' . $lang['319'] . '</div>';
		if(file_exists ('../../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo3.png')){
			$htmlWeekGameDetail .= '<img id="anexo3' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo2.png?tmp=' . $fecha->getTimestamp() . '" alt="Foto" style="width: 91%; height: auto;">';
		}
		$htmlWeekGameDetail .= '</div>
				<div class="justify-content-center d-flex px-0 py-1">
					<div class="align-self-center" style="width: 30%; text-align: right;padding-right: 30px; font-size:3vw;width: 10px;word-wrap: break-word;white-space:pre-wrap;">' . $lang['320'] . '</div>';
		if(file_exists ('../../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo4.png')){
			$htmlWeekGameDetail .= '<img id="anexo4' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo4.png?tmp=' . $fecha->getTimestamp() . '" alt="Foto" style="width: 91%; height: auto;">';
		}
		$htmlWeekGameDetail .= '</div>
			</div>';
	}
	$htmlWeekGameDetail .= '</div>';
    $retunData = array('status' => '1', 'message' => 'Success.', 'dataWeekGameDetail' => $htmlWeekGameDetail);
    $Config->Close();
    echo json_encode($retunData);
?>