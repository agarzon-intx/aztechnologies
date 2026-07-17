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
	$comentario = htmlspecialchars($_POST["Comentarios"]);
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
									<div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">
											<div style="text-align:left; border: none; margin-top: 10px !important">
												<button style="margin-top: -5px; margin-bottom: 15px;" type="button" class="btn btn-primary" onclick="SaveGameDetailPlayerStatsS(' . $Season . ',' . $Week . ',' . $Game . ',' . $lequipoid . ',' . $vequipoid . ', \'\', $(\'#comentarioS' . $Game . '\').val(), 0, 0);">' . $lang['0000'] . '</button>
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


							
   $retunData = array('status' => '1', 'message' => 'Success.', 'dataWeekGameDetail' => $htmlWeekGameDetail);
    $Config->Close();
    echo json_encode($retunData);
?>