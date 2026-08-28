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
	$sessionstat = $fgmembersite->CheckLogin('week-ScheduleScoresGameDetailS.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');


	$Season = $_COOKIE[$Config->getAlias() . 'season'];
	$Week = htmlspecialchars($_POST["week"]);
	$Game = htmlspecialchars($_POST["game"]);
	$Game_DESC = htmlspecialchars($_POST["gamedesc"]);
	$lgoal = htmlspecialchars($_POST["lgoals"]);
	$vgoal = htmlspecialchars($_POST["vgoals"]);

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
	} else {
		echo "";
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
	} else {
		echo "";
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
	} else {
		echo "";
	}
	$Config->LoadFlags();


    $retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');

	$docGames = false;
	if(file_exists ('../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo1-.png') || 
	   file_exists ('../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo2.png') || 
	   file_exists ('../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo3.png') || 
	   file_exists ('../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo4.png')){
		$docGames = true;
	}
	
	if($jugado == 1){
		$htmlWeekGameDetail = '<div class="container-fluid py-0" style="padding-left: 0px; padding-right: 0px;">
									<div class="nav-wrapper position-relative end-0">
										<ul class="nav nav-pills nav-fill p-1" role="tablist" style="background: #cee6ff; flex-direction: unset !important;" id="scheduleS">
											<li class="nav-item" id="local' . $Game . 'li">
												<a class="nav-link mb-0 px-0 py-1 active" data-bs-toggle="tab" style="cursor: pointer;" callval="#local' . $Game . '" role="tab" aria-controls="local' . $Game . 'li" aria-selected="true">
													<img src="./imagenes/Original/' . $llogo . '.png" style="width: 20px; height: auto;" alt=""/>
												</a>
											</li>
											<li class="nav-item" id="visitante' . $Game . 'li">
												<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" style="cursor: pointer;" callval="#visitante' . $Game . '" role="tab" aria-controls="visitante' . $Game . 'li" aria-selected="false">
													<img src="./imagenes/Original/' . $vlogo . '.png" style="width: 20px; height: auto;" alt=""/>
												</a>
											</li>';
		if($docGames){
			$htmlWeekGameDetail .= '<li class="nav-item" id="docs' . $Game . 'li">
												<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" style="cursor: pointer;" callval="#docs' . $Game . '" role="tab" aria-controls="docs' . $Game . 'li" aria-selected="false">
													<img src="./imagenes/documents.png" style="width: 20px; height: auto;" alt=""/>
												</a>
											</li>';
		}
		$htmlWeekGameDetail .= '		</ul>
									</div><script>initNavs(\'scheduleS\');</script>
								</div>';

		$htmlWeekGameDetail .= '<div class="container-fluid py-0" style="padding-left: 0px; padding-right: 0px;">
									<div id="local' . $Game . '" class="tabla active" style="display: block">
										<div class="d-block  d-xs-block d-md-block d-lg-block d-xl-none">
											<div class="card">
												<div class="table-responsive">
													<table id="local" class=" table align-items-center mb-0" style="border-color: #136aeb;">';
	
		$htmlWeekGameDetail .= '';
		$JGsql = "";
		
	$apellidos = 'a.Apellido_P, 
				  a.Apellido_M,';
	if($Config->jugadoresApellidos1){
	    $apellidos = '  SUBSTRING(a.Apellido_P, 1, 1) Apellido_P, 
				        SUBSTRING(a.Apellido_M, 1, 1) Apellido_M,';
	}
	if ($Config->JugadorJugado <> "hidden"){
			$JGsql = "UNION
						SELECT a.Jugador_ID,
							a.Numero,
							a.Nombre,
							$apellidos
							0 as Amarillas,
							0 as Rojas,
							0 as Goles,
							'' Comentario,
							0 Dias_Castigo,
							0 Multa,
							d.Jugado
						FROM $schema.Jugadores a
							join $schema.JugadorJugado d on a.Jugador_ID = d.Jugador_ID and d.Torneo_ID = $Season and d.Juego_ID = $Game and d.Jugado > 0
						where a.Equipo_ID = $lequipoid and a.Estatus in ('A','D')";
		}
		$sql2 = "select a.Jugador_ID,
					a.Numero,
					a.Nombre,
					$apellidos
					sum(a.Amarillas) Amarillas,
					sum(a.Rojas) Rojas,
					sum(a.Goles) Goles,
					Max(a.Comentario) Comentario,
					Max(a.Dias_Castigo) Dias_Castigo,
					Max(a.Multa) Multa,
					Max(a.Jugado) Jugado
				from 	(SELECT a.Jugador_ID,
							a.Numero,
							a.Nombre,
							a.Apellido_P,
							a.Apellido_M,
							ifnull(sum(b.Cantidad),0) as Amarillas,
							0 as Rojas,
							0 as Goles,
							'' Comentario,
							0 Dias_Castigo,
							0 Multa,
							0 Jugado
						FROM $schema.Jugadores a
							join $schema.Amonestados b on a.Jugador_ID = b.Jugador_ID and b.Torneo_ID = $Season and Juego_ID = $Game and b.Cantidad > 0
						where a.Equipo_ID = $lequipoid and a.Estatus in ('A','D')
						group by a.Numero,
							a.Nombre,
							a.Apellido_P,
							a.Apellido_M
						UNION
						SELECT a.Jugador_ID,
							a.Numero,
							a.Nombre,
							a.Apellido_P,
							a.Apellido_M,
							0 as Amarillas,
							ifnull(sum(c.Cantidad),0) as Rojas,
							0 as Goles,
							c.Comentario,
							c.Dias_Castigo,
							c.Multa,
							0 Jugado
						FROM $schema.Jugadores a
							join $schema.Expulsados c on a.Jugador_ID = c.Jugador_ID and c.Torneo_ID = $Season and c.Juego_ID = $Game and c.Cantidad > 0
						where a.Equipo_ID = $lequipoid and a.Estatus in ('A','D')
						group by a.Numero,
							a.Nombre,
							a.Apellido_P,
							a.Apellido_M,
							c.Comentario,
							c.Dias_Castigo,
							c.Multa
						UNION
						SELECT a.Jugador_ID,
							a.Numero,
							a.Nombre,
							a.Apellido_P,
							a.Apellido_M,
							0 as Amarillas,
							0 as Rojas,
							d.Goles as Goles,
							'' Comentario,
							0 Dias_Castigo,
							0 Multa,
							0 Jugado
						FROM $schema.Jugadores a
							join $schema.Goles d on a.Jugador_ID = d.Jugador_ID and d.Torneo_ID = $Season and d.Juego_ID = $Game and d.Goles > 0
						where a.Equipo_ID = $lequipoid and a.Estatus in ('A','D')
						 " . $JGsql . " ) a
				group by a.Jugador_ID, a.Numero, a.Nombre, a.Apellido_P, a.Apellido_M
				order by cast(a.Numero as decimal) asc;";
		$result2 = $Config->query($sql2);
		$htmlWeekGameDetail .= '<thead class="">
									  <tr>
										  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding-left: 10px;padding-right: 0px;">' . $lang['312'] . '</th>
										  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding-left: 10px;padding-right: 0px;">' . $lang['313'] . '</th>
										  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding-left: 10px;padding-right: 0px;">' . $lang['314'] . '</th>
										  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding-left: 10px;padding-right: 0px;" ' . $Config->JugadorJugado . '><img src="./imagenes/gamePlayed.png" width="20" height="25" alt=""/></th>
										  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding-left: 10px;padding-right: 0px;"><img src="./imagenes/amarilla.png" width="20" height="25" alt=""/></th>
										  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding-left: 10px;padding-right: 0px;"><img src="./imagenes/roja.png" width="20" height="25" alt=""/></th>
										  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding-left: 10px;padding-right: 0px;"><img src="./imagenes/goal.png" width="20" height="20" alt=""/></th>
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
					$htmlWeekGameDetail .= '
					<td scope="row"><div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Numero"] . '</span></div></div></td>
					<td scope="row"><div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: left;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Nombre"] . '</span></div></div></td>
					<td scope="row"><div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: left;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Apellido_P"] . ' ' . $row2["Apellido_M"] . '</span></div></div></td>
					<td scope="row" ' .  $Config->JugadorJugado . '><div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">';
					if($row2["Jugado"] > 0){   
							$htmlWeekGameDetail .= "<input type='checkbox' checked disabled>";
					 }else{
							$htmlWeekGameDetail .= "<input type='checkbox' disabled>";
					 }
					$htmlWeekGameDetail .= '</span></div></div></td>
					<td scope="row"><div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">';
					if($row2["Amarillas"] > 0){   
						 if($row2["Amarillas"] == 1){   
							$htmlWeekGameDetail .= "<img src='./imagenes/amarilla.png' width='15' height='18' alt=''/>";
						 }else{
							$htmlWeekGameDetail .= "<img src='./imagenes/damarilla.png' width='15' height='18' alt=''/>";
						 }
					 }else{
						$htmlWeekGameDetail .= "";
					 }
					$htmlWeekGameDetail .= '</span></div></div></td>
					<td scope="row"><div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">';
					 if($row2["Rojas"] > 0){   
						$htmlWeekGameDetail .= "<img src='./imagenes/roja.png' width='15' height='18' alt=''
						title='" . $lang['655'] . " " . $row2["Comentario"] . ", " . $lang['656'] . " " . $row2["Multa"] . ", " . $lang['657'] . " " . $row2["Dias_Castigo"] . "'/>";
					 }else{
						$htmlWeekGameDetail .= "";
					 }
					$htmlWeekGameDetail .= '</span></div></div></td>
					<td scope="row"><div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">';
					if($row2["Goles"] > 0){   
						$htmlWeekGameDetail .= "" . $row2["Goles"] . "";
					 }else{
						$htmlWeekGameDetail .= "";
					 }
					$htmlWeekGameDetail .= "</span></div></div></td>
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
		
		<div id="visitante' . $Game . '" class="tabla active" style="display: none">
			<div class="d-block  d-xs-block d-md-block d-lg-block d-xl-none">
				<div class="card">
					<div class="table-responsive">
						<table id="local" class=" table align-items-center mb-0" style="border-color: #136aeb;">';
		$htmlWeekGameDetail .= '';
		$JGsql = "";
		if ($Config->JugadorJugado <> "hidden"){
			$JGsql = "UNION
						SELECT a.Jugador_ID,
							a.Numero,
							a.Nombre,
							$apellidos
							0 as Amarillas,
							0 as Rojas,
							0 as Goles,
							'' Comentario,
							0 Dias_Castigo,
							0 Multa,
							d.Jugado
						FROM $schema.Jugadores a
							join $schema.JugadorJugado d on a.Jugador_ID = d.Jugador_ID and d.Torneo_ID = $Season and d.Juego_ID = $Game and d.Jugado > 0
						where a.Equipo_ID = $vequipoid and a.Estatus in ('A','D')";
		}
		$sql2 = "select a.Jugador_ID,
					a.Numero,
					a.Nombre,
					$apellidos
					sum(a.Amarillas) Amarillas,
					sum(a.Rojas) Rojas,
					sum(a.Goles) Goles,
					Max(a.Comentario) Comentario,
					Max(a.Dias_Castigo) Dias_Castigo,
					Max(a.Multa) Multa,
					Max(a.Jugado) Jugado
				from 	(SELECT a.Jugador_ID,
							a.Numero,
							a.Nombre,
							a.Apellido_P,
							a.Apellido_M,
							ifnull(sum(b.Cantidad),0) as Amarillas,
							0 as Rojas,
							0 as Goles,
							'' Comentario,
							0 Dias_Castigo,
							0 Multa,
							0 Jugado
						FROM $schema.Jugadores a
							join $schema.Amonestados b on a.Jugador_ID = b.Jugador_ID and b.Torneo_ID = $Season and Juego_ID = $Game and b.Cantidad > 0
						where a.Equipo_ID = $vequipoid and a.Estatus in ('A','D')
						group by a.Numero,
							a.Nombre,
							a.Apellido_P,
							a.Apellido_M
						UNION
						SELECT a.Jugador_ID,
							a.Numero,
							a.Nombre,
							a.Apellido_P,
							a.Apellido_M,
							0 as Amarillas,
							ifnull(sum(c.Cantidad),0) as Rojas,
							0 as Goles,
							c.Comentario,
							c.Dias_Castigo,
							c.Multa,
							0 Jugado
						FROM $schema.Jugadores a
							join $schema.Expulsados c on a.Jugador_ID = c.Jugador_ID and c.Torneo_ID = $Season and c.Juego_ID = $Game and c.Cantidad > 0
						where a.Equipo_ID = $vequipoid and a.Estatus in ('A','D')
						group by a.Numero,
							a.Nombre,
							a.Apellido_P,
							a.Apellido_M,
							c.Comentario,
							c.Dias_Castigo,
							c.Multa
						UNION
						SELECT a.Jugador_ID,
							a.Numero,
							a.Nombre,
							a.Apellido_P,
							a.Apellido_M,
							0 as Amarillas,
							0 as Rojas,
							d.Goles as Goles,
							'' Comentario,
							0 Dias_Castigo,
							0 Multa,
							0 Jugado
						FROM $schema.Jugadores a
							join $schema.Goles d on a.Jugador_ID = d.Jugador_ID and d.Torneo_ID = $Season and d.Juego_ID = $Game and d.Goles > 0
						where a.Equipo_ID = $vequipoid and a.Estatus in ('A','D')
						 " . $JGsql . " ) a
				group by a.Jugador_ID, a.Numero, a.Nombre, a.Apellido_P, a.Apellido_M
				order by cast(a.Numero as decimal) asc;";
		$result2 = $Config->query($sql2);
		$htmlWeekGameDetail .= '<thead class="">
									  <tr>
										  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding-left: 10px;padding-right: 0px;">' . $lang['312'] . '</th>
										  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding-left: 10px;padding-right: 0px;">' . $lang['313'] . '</th>
										  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding-left: 10px;padding-right: 0px;">' . $lang['314'] . '</th>
										  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding-left: 10px;padding-right: 0px;" ' . $Config->JugadorJugado . '><img src="./imagenes/gamePlayed.png" width="20" height="25" alt=""/></th>
										  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding-left: 10px;padding-right: 0px;"><img src="./imagenes/amarilla.png" width="20" height="25" alt=""/></th>
										  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding-left: 10px;padding-right: 0px;"><img src="./imagenes/roja.png" width="20" height="25" alt=""/></th>
										  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding-left: 10px;padding-right: 0px;"><img src="./imagenes/goal.png" width="20" height="20" alt=""/></th>
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
					$htmlWeekGameDetail .= '
					<td scope="row"><div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Numero"] . '</span></div></div></td>
					<td scope="row"><div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: left;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Nombre"] . '</span></div></div></td>
					<td scope="row"><div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: left;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Apellido_P"] . ' ' . $row2["Apellido_M"] . '</span></div></div></td>
					<td scope="row" ' .  $Config->JugadorJugado . '><div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">';
					if($row2["Jugado"] > 0){   
							$htmlWeekGameDetail .= "<input type='checkbox' checked disabled>";
					 }else{
							$htmlWeekGameDetail .= "<input type='checkbox' disabled>";
					 }
					$htmlWeekGameDetail .= '</span></div></div></td>
					<td scope="row"><div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">';
					if($row2["Amarillas"] > 0){   
						 if($row2["Amarillas"] == 1){   
							$htmlWeekGameDetail .= "<img src='./imagenes/amarilla.png' width='15' height='18' alt=''/>";
						 }else{
							$htmlWeekGameDetail .= "<img src='./imagenes/damarilla.png' width='15' height='18' alt=''/>";
						 }
					 }else{
						$htmlWeekGameDetail .= "";
					 }
					$htmlWeekGameDetail .= '</span></div></div></td>
					<td scope="row"><div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">';
					 if($row2["Rojas"] > 0){   
						$htmlWeekGameDetail .= "<img src='./imagenes/roja.png' width='15' height='18' alt=''
						title='" . $lang['655'] . " " . $row2["Comentario"] . ", " . $lang['656'] . " " . $row2["Multa"] . ", " . $lang['657'] . " " . $row2["Dias_Castigo"] . "'/>";
					 }else{
						$htmlWeekGameDetail .= "";
					 }
					$htmlWeekGameDetail .= '</span></div></div></td>
					<td scope="row"><div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">';
					if($row2["Goles"] > 0){   
						$htmlWeekGameDetail .= "" . $row2["Goles"] . "";
					 }else{
						$htmlWeekGameDetail .= "";
					 }
					$htmlWeekGameDetail .= "</span></div></div></td>
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
		
		if($Config->JuegoCedulas == 1 && $docGames){
			$htmlWeekGameDetail .= '<div id="docs' . $Game . '" class="tabla" style="display: none">';
			if(file_exists ('../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo1.png')){
				$htmlWeekGameDetail .= '<div class="justify-content-center d-flex px-0 py-1">';
				$htmlWeekGameDetail .= '<div class="align-self-center" style="width: 30%; text-align: right;padding-right: 30px; font-size:3vw;width: 10px;word-wrap: break-word;white-space:pre-wrap;">' . $lang['317'] . '</div>
										<img id="anexo1' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo1.png" style="width: 100%; height: auto;">';
				$htmlWeekGameDetail .= '</div>';
			}
			if(file_exists ('../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo2.png')){
				$htmlWeekGameDetail .= '<div class="justify-content-center d-flex px-0 py-1">';
				$htmlWeekGameDetail .= '<div class="align-self-center" style="width: 30%; text-align: right;padding-right: 30px; font-size:3vw;width: 10px;word-wrap: break-word;white-space:pre-wrap;">' . $lang['318'] . '</div>
										<img id="anexo2' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo2.png" style="width: 100%; height: auto;">';
				$htmlWeekGameDetail .= '</div>';
			}
			if(file_exists ('../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo3.png')){
				$htmlWeekGameDetail .= '<div class="justify-content-center d-flex px-0 py-1">';
				$htmlWeekGameDetail .= '<div class="align-self-center" style="width: 30%; text-align: right;padding-right: 30px; font-size:3vw;width: 10px;word-wrap: break-word;white-space:pre-wrap;">' . $lang['319'] . '</div>
										<img id="anexo3' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo3.png" style="width: 100%; height: auto;">';
				$htmlWeekGameDetail .= '</div>';
			}
			if(file_exists ('../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo4.png')){
				$htmlWeekGameDetail .= '<div class="justify-content-center d-flex px-0 py-1">';
				$htmlWeekGameDetail .= '<div class="align-self-center" style="width: 30%; text-align: right;padding-right: 30px; font-size:3vw;width: 10px;word-wrap: break-word;white-space:pre-wrap;">' . $lang['320'] . '</div>
										<img id="anexo4' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo4.png" style="width: 100%; height: auto;">';
				$htmlWeekGameDetail .= '</div>';
			}
			$htmlWeekGameDetail .= '</div>';
		}
		$htmlWeekGameDetail .= '</div>';
	}
	
	$retunData = array('status' => '1', 'message' => 'Success.', 'dataWeekGameDetail' => $htmlWeekGameDetail);
	$Config->Close();
	echo json_encode($retunData);
?>