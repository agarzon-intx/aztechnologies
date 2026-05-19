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

	$lequipoid = "";
	$lequipo = "";
	$lequipodesc = "";
	$llogo = "";

	$vequipoid = "";
	$vequipo = "";
	$vequipodesc = "";
	$vlogo = "";
	$fecha = new DateTime();

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
			where jo.Jornada_ID = $Week and j.Juego_ID = $Game;";
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

	$htmlWeekGameDetail = '<table width="100%" id="fichaTecnicaEdit" >
            <tr style="background: url(./imagenes/marcador.png?tmp=' . $fecha->getTimestamp() . ') no-repeat; background-size:100% 100%;">
        	    <td colspan="3" height="120">
        	        <div style="width:100%; margin: auto;">';
                	
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
                $s1l = $row["Set1_L"];
                $s2l = $row["Set2_L"];
                $s3l = $row["Set3_L"];
                $s4l = $row["Set4_L"];
                $s5l = $row["Set5_L"];
                $s1v = $row["Set1_V"];
                $s2v = $row["Set2_V"];
                $s3v = $row["Set3_V"];
                $s4v = $row["Set4_V"];
                $s5v = $row["Set5_V"];
    		}
    	}
    	$htmlWeekGameDetail .= '<table style="margin: 0 auto;background: white;color: black;">
                                	    <tr>
                                	        <th style="border: 1px solid #000000;text-align: center;">Equipos</th>
                                	        <th style="border: 1px solid #000000;text-align: center;">S1</th>
                                	        <th style="border: 1px solid #000000;text-align: center;">S2</th>
                                	        <th style="border: 1px solid #000000;text-align: center;">S3</th>';
        $display = '';
        if($Config->tressets == 1){
            $display = 'display: none';
        }
        $htmlWeekGameDetail .= '<th style="border: 1px solid #000000;text-align: center;' . $display . ';">S4</th>
                        <th style="border: 1px solid #000000;text-align: center;' . $display . ';">S5</th>';
        $htmlWeekGameDetail .= '</tr>
    	            <tr>
                    	<td style="text-align:left; border: none;border: 1px solid #000000;" width="90px"><img src="./imagenes/Original/' . $llogo . '.png?tmp=' . $fecha->getTimestamp() . '" width="35" height="35" alt=""/>' . $lequipo . '</td>
                    	<td style="text-align:center; border: none;border: 1px solid #000000;text-align: center;" width="45px"><input id="S1L" type="number" value="' . $s1l . '" style="width: 45px;"></td>
                    	<td style="text-align:center; border: none;border: 1px solid #000000;text-align: center;" width="45px"><input id="S2L" type="number" value="' . $s2l . '" style="width: 45px;"></td>
                    	<td style="text-align:center; border: none;border: 1px solid #000000;text-align: center;" width="45px"><input id="S3L" type="number" value="' . $s3l . '" style="width: 45px;"></td>';
        $htmlWeekGameDetail .= '<td style="text-align:left; border: none;border: 1px solid #000000;text-align: center;' . $display . ';" width="45px"><input id="S4L" type="number" value="' . $s4l . '" style="width: 37px;"></td>
                        <td style="text-align:left; border: none;border: 1px solid #000000;text-align: center;' . $display . ';" width="45px"><input id="S5L" type="number" value="' . $s5l . '" style="width: 37px;"></td>';
        $htmlWeekGameDetail .= '</tr>
    	            <tr>
                    	<td style="text-align:left; border: none;border: 1px solid #000000;" width="90px"><img src="./imagenes/Original/' . $vlogo . '.png?tmp=' . $fecha->getTimestamp() . '" width="35" height="35" alt=""/>' . $vequipo . '</td>
                    	<td style="text-align:center; border: none;border: 1px solid #000000;text-align: center;" width="45px"><input id="S1V" type="number" value="' . $s1v . '" style="width: 45px;"></td>
                    	<td style="text-align:center; border: none;border: 1px solid #000000;text-align: center;" width="45px"><input id="S2V" type="number" value="' . $s2v . '" style="width: 45px;"></td>
                    	<td style="text-align:center; border: none;border: 1px solid #000000;text-align: center;" width="45px"><input id="S3V" type="number" value="' . $s3v . '" style="width: 45px;"></td>';
        $htmlWeekGameDetail .= '<td style="text-align:left; border: none;border: 1px solid #000000;text-align: center;' . $display . ';" width="45px"><input id="S4V" type="number" value="' . $s4v . '" style="width: 37px;"></td>
                        <td style="text-align:left; border: none;border: 1px solid #000000;text-align: center;' . $display . ';" width="45px"><input id="S5V" type="number" value="' . $s5v . '" style="width: 37px;"></td>';
        $htmlWeekGameDetail .= '</tr>
                </table>';
        $htmlWeekGameDetail .= '</div>
                    </td>
                </tr>
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
												<button style="margin-top: -5px; margin-bottom: 15px;" type="button" class="btn btn-primary" onclick="SaveGameDetailPlayerStatsSVoleibol(' . $Season . ',' . $Week . ',' . $Game . ',' . $lequipoid . ',' . $vequipoid . ', $(\'#arbitroS' . $Game . '\').val(), $(\'#comentarioS' . $Game . '\').val(), $(\'#extralS' . $Game . '\').val(), $(\'#extravS' . $Game . '\').val());">' . $lang['0000'] . '</button>
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
	$script = "if ($('#editS" . $Game . " #local" . $Game . "T input:checkbox:checked').length > 0) { $('#editS" . $Game . "').find('#local" . $Game . "T').find('input:checkbox').prop('checked', false); } else { $('#editS" . $Game . "').find('#local" . $Game . "T').find('input:checkbox').prop('checked', true); }";
	$htmlWeekGameDetail .= '
						<thead>
							<tr>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Jugador_ID</th>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Jugado</th>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1">' . $lang['312'] . '</th>
								<th style="text-align:left; padding: 0rem 0rem;" colspan="1">' . $lang['313'] . ' ' . $lang['314'] . '</th>
								<th style="text-align: center; padding: 0rem 0rem;" ' . $Config->JugadorJugado . '><img src="imagenes/gamePlayedVoleibol.png" width="20" height="25" alt="" onclick="' . $script . '"/></th>
						</tr>
					</thead>
					<tbody>';
	
	$sql2 = "SELECT a.Jugador_ID,
				a.Numero,
				a.Nombre,
				a.Apellido_P,
				a.Apellido_M,
				ifnull(jj.Jugado, 0) Jugado
			FROM $schema.Jugadores a
				left outer join $schema.JugadorJugado jj on a.Jugador_ID = jj.Jugador_ID and jj.Torneo_ID = $Season and jj.Juego_ID = $Game
			where a.Equipo_ID = $lequipoid and a.Estatus in ('A', 'D')
			group by a.Numero,
				a.Nombre,
				a.Apellido_P,
				a.Apellido_M
			order by a.Estatus desc, cast(a.Numero as decimal) asc;";
			//echo $sql2;
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
				<td style='text-align:left'><span class='text-secondary text-xs font-weight-normal'>" . $row2["Numero"] . "</span></td>
				<td style='text-align:left'><span class='text-secondary text-xs font-weight-normal'>" . $row2["Nombre"] . " " . $row2["Apellido_P"] . " " . $row2["Apellido_M"] . "</span></td>
				<td style='text-align:center' " .  $Config->JugadorJugado . ">";
				 if($row2["Jugado"] == 1){   
					$htmlWeekGameDetail .= "<input type='checkbox' checked name='jugadoS' id='jugadoS" . $row2["Jugador_ID"] . "'>";
				 }else{
					$htmlWeekGameDetail .= "<input type='checkbox' name='jugadoS' id='jugadoS" . $row2["Jugador_ID"] . "'>";
				 }
				$htmlWeekGameDetail .= "</td>
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
	$script = "if ($('#editS" . $Game . " #visitante" . $Game . "T input:checkbox:checked').length > 0) { $('#editS" . $Game . "').find('#visitante" . $Game . "T').find('input:checkbox').prop('checked', false); } else { $('#editS" . $Game . "').find('#visitante" . $Game . "T').find('input:checkbox').prop('checked', true); }";
	$htmlWeekGameDetail .= '
					<thead>
					  <tr>
						  <th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Jugador_ID</th>
						  <th style="text-align:left; padding: 0rem 0rem;" colspan="1" hidden="true">Jugado</th>
						  <th style="text-align:left; padding: 0rem 0rem;" colspan="1">' . $lang['312'] . '</th>
						  <th style="text-align:left; padding: 0rem 0rem;" colspan="1">' . $lang['313'] . ' ' . $lang['314'] . '</th>
						  <th style="text-align: center; padding: 0rem 0rem;" ' . $Config->JugadorJugado . '><img src="imagenes/gamePlayedVoleibol.png" width="20" height="25" alt="" onclick="' . $script . '"/></th>
					  </tr>
				  </thead>
				  <tbody>';
				  
	$sql2 = "SELECT a.Jugador_ID,
				a.Numero,
				a.Nombre,
				a.Apellido_P,
				a.Apellido_M,
				ifnull(jj.Jugado, 0) Jugado
			FROM $schema.Jugadores a
				left outer join $schema.JugadorJugado jj on a.Jugador_ID = jj.Jugador_ID and jj.Torneo_ID = $Season and jj.Juego_ID = $Game
			where a.Equipo_ID = $vequipoid and a.Estatus in ('A', 'D')
			group by a.Numero,
				a.Nombre,
				a.Apellido_P,
				a.Apellido_M
			order by a.Estatus desc, cast(a.Numero as decimal) asc;";
			//echo $sql2;
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
				<td style='text-align:left'><span class='text-secondary text-xs font-weight-normal'>" . $row2["Numero"] . "</span></td>
				<td style='text-align:left'><span class='text-secondary text-xs font-weight-normal'>" . $row2["Nombre"] . " " . $row2["Apellido_P"] . " " . $row2["Apellido_M"] . "</span></td>
				<td style='text-align:center' " .  $Config->JugadorJugado . ">";
				 if($row2["Jugado"] > 0){   
						$htmlWeekGameDetail .= "<input type='checkbox' checked name='jugadoS' id='jugadoS" . $row2["Jugador_ID"] . "'>";
				 }else{
						$htmlWeekGameDetail .= "<input type='checkbox' name='jugadoS' id='jugadoS" . $row2["Jugador_ID"] . "'>";
				 }
				$htmlWeekGameDetail .= "</td>
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