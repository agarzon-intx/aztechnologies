<?php
	$fecha = new DateTime();
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
	$sessionstat = $fgmembersite->CheckLogin('week-ScheduleScoresGameDetail.php');
	
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
    $htmlWeekGameDetail = '<table width="100%">
            <tr style="background: url(./imagenes/marcador.png?tmp=' . $fecha->getTimestamp() . ') no-repeat; background-size:100% 100%;">
        	    <td colspan="3" height="200">
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
			$htmlWeekGameDetail .= '<table style="margin: 0 auto;background: white;color: black;">
                	    <tr>
                	        <th style="border: 1px solid #000000;text-align: center;">Equipos</th>
                	        <th style="border: 1px solid #000000;text-align: center;">S1</th>
                	        <th style="border: 1px solid #000000;text-align: center;">S2</th>
                	        <th style="border: 1px solid #000000;text-align: center;">S3</th>';
            if($Config->tressets == 0){
            $htmlWeekGameDetail .= '<th style="border: 1px solid #000000;text-align: center;">S4</th>
                            <th style="border: 1px solid #000000;text-align: center;">S5</th>';
            }
            $htmlWeekGameDetail .= '</tr>
        	            <tr>
                        	<td style="text-align:left; border: none;border: 1px solid #000000;" width="180px"><img src="./imagenes/Original/' . $llogo . '.png?tmp=' . $fecha->getTimestamp() . '" width="70" height="70" alt=""/>' . $lequipo . '</td>
                        	<td style="text-align:center; border: none;border: 1px solid #000000;text-align: center;background-color: #' . $row["colorS1L"] . ';" width="90px"><h3>' . $row["Set1_L"] . '</h3></td>
                        	<td style="text-align:center; border: none;border: 1px solid #000000;text-align: center;background-color: #' . $row["colorS2L"] . ';" width="90px"><h3>' . $row["Set2_L"] . '</h3></td>
                        	<td style="text-align:center; border: none;border: 1px solid #000000;text-align: center;background-color: #' . $row["colorS3L"] . ';" width="90px"><h3>' . $row["Set3_L"] . '</h3></td>';
            if($Config->tressets == 0){
            $htmlWeekGameDetail .= '<td style="text-align:left; border: none;border: 1px solid #000000;text-align: center;background-color: #' . $row["colorS4L"] . ';" width="90px"><h3>' . $row["Set4_L"] . '</h3></td>
                            <td style="text-align:left; border: none;border: 1px solid #000000;text-align: center;background-color: #' . $row["colorS5L"] . ';" width="90px"><h3>' . $row["Set5_L"] . '</h3></td>';
            }
            $htmlWeekGameDetail .= '</tr>
        	            <tr>
                        	<td style="text-align:left; border: none;border: 1px solid #000000;" width="180px"><img src="./imagenes/Original/' . $vlogo . '.png?tmp=' . $fecha->getTimestamp() . '" width="70" height="70" alt=""/>' . $vequipo . '</td>
                        	<td style="text-align:center; border: none;border: 1px solid #000000;text-align: center;background-color: #' . $row["colorS1V"] . ';" width="90px"><h3>' . $row["Set1_V"] . '</h3></td>
                        	<td style="text-align:center; border: none;border: 1px solid #000000;text-align: center;background-color: #' . $row["colorS2V"] . ';" width="90px"><h3>' . $row["Set2_V"] . '</h3></td>
                        	<td style="text-align:center; border: none;border: 1px solid #000000;text-align: center;background-color: #' . $row["colorS3V"] . ';" width="90px"><h3>' . $row["Set3_V"] . '</h3></td>';
            if($Config->tressets == 0){
            $htmlWeekGameDetail .= '<td style="text-align:left; border: none;border: 1px solid #000000;text-align: center;background-color: #' . $row["colorS4V"] . ';" width="90px"><h3>' . $row["Set4_V"] . '</h3></td>
                            <td style="text-align:left; border: none;border: 1px solid #000000;text-align: center;background-color: #' . $row["colorS5V"] . ';" width="90px"><h3>' . $row["Set5_V"] . '</h3></td>';
            }
            $htmlWeekGameDetail .= '</tr>
                    </table>';
		}
	} else {
		echo "";
	}
    $htmlWeekGameDetail .= '</div>
                </td>
            </tr>';
    $retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');

    
	$apellidos = 'a.Apellido_P, 
				  a.Apellido_M,';
	if($Config->jugadoresApellidos1){
	    $apellidos = '  SUBSTRING(a.Apellido_P, 1, 1) Apellido_P, 
				        SUBSTRING(a.Apellido_M, 1, 1) Apellido_M,';
	}
	if($jugado == 1){
		$htmlWeekGameDetail .= '<tr>
			<td style="vertical-align:text-top; width: 49%">
				<div id="lista">';
		$JGsql = "";
		if ($Config->JugadorJugado <> "hidden"){
			$JGsql = "UNION
						SELECT a.Jugador_ID,
							a.Numero,
							a.Nombre,
							$apellidos
							d.Jugado
						FROM $schema.Jugadores a
							join $schema.JugadorJugado d on a.Jugador_ID = d.Jugador_ID and d.Torneo_ID = $Season and d.Juego_ID = $Game and d.Jugado > 0
						where a.Equipo_ID = $lequipoid and a.Estatus in ('A','D')";
		}
		$sql2 = "select a.Jugador_ID,
					a.Numero,
					a.Nombre,
					$apellidos
					Max(a.Jugado) Jugado
				from 	(SELECT a.Jugador_ID,
							a.Numero,
							a.Nombre,
							a.Apellido_P,
							a.Apellido_M,
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
							0 Jugado
						FROM $schema.Jugadores a
							join $schema.Expulsados c on a.Jugador_ID = c.Jugador_ID and c.Torneo_ID = $Season and c.Juego_ID = $Game and c.Cantidad > 0
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
							0 Jugado
						FROM $schema.Jugadores a
							join $schema.Goles d on a.Jugador_ID = d.Jugador_ID and d.Torneo_ID = $Season and d.Juego_ID = $Game and d.Goles > 0
						where a.Equipo_ID = $lequipoid and a.Estatus in ('A','D')
						 " . $JGsql . " ) a
				group by a.Jugador_ID, a.Numero, a.Nombre, a.Apellido_P, a.Apellido_M
				order by cast(a.Numero as decimal) asc;";
		$result2 = $Config->query($sql2);
		$htmlWeekGameDetail .= '<div id="all" class="datagridTeamSchedule">
          			<h2 style="margin-top: 10;">' . $lequipodesc . '</h2>
						<table id="local" width="100%">
							<thead>
								<tr>
									<th style="text-align:left; padding: 0rem 0rem;" colspan="1">' . $lang['312'] . '</th>
									<th style="text-align:left; padding: 0rem 0rem;" colspan="1">' . $lang['313'] . '</th>
									<th style="text-align:left; padding: 0rem 0rem;" colspan="1">' . $lang['314'] . '</th>
									<th style="text-align: center; padding: 0rem 0rem;" ' . $Config->JugadorJugado . '><img src="./imagenes/gamePlayedVoleibol.png" width="20" height="25" alt=""/></th>
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
					<td style='text-align:left'>
						" . $row2["Numero"] . "
					</td>
					<td style='text-align:left'><span class='text-secondary text-xs font-weight-normal'>
						" . $row2["Nombre"] . "</span></td>
					<td style='text-align:left'><span class='text-secondary text-xs font-weight-normal'>
						" . $row2["Apellido_P"] . "
						" . $row2["Apellido_M"] . "
					</span></td>
					<td style='text-align:center' " .  $Config->JugadorJugado . ">";
					 if($row2["Jugado"] > 0){   
							$htmlWeekGameDetail .= "<input type='checkbox' checked disabled>";
					 }else{
							$htmlWeekGameDetail .= "<input type='checkbox' disabled>";
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
        </td>
        <td width="20">
        </td>
          <td style="vertical-align:text-top; width: 49%">
	       	<div id="lista">';   
		$JGsql = "";
		if ($Config->JugadorJugado <> "hidden"){
			$JGsql = "UNION
						SELECT a.Jugador_ID,
							a.Numero,
							a.Nombre,
							$apellidos
							d.Jugado
						FROM $schema.Jugadores a
							join $schema.JugadorJugado d on a.Jugador_ID = d.Jugador_ID and d.Torneo_ID = $Season and d.Juego_ID = $Game and d.Jugado > 0
						where a.Equipo_ID = $vequipoid and a.Estatus in ('A','D')";
		}
		$sql2 = "select a.Jugador_ID,
					a.Numero,
					a.Nombre,
					$apellidos
					Max(a.Jugado) Jugado
				from 	(SELECT a.Jugador_ID,
							a.Numero,
							a.Nombre,
							a.Apellido_P,
							a.Apellido_M,
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
							0 Jugado
						FROM $schema.Jugadores a
							join $schema.Expulsados c on a.Jugador_ID = c.Jugador_ID and c.Torneo_ID = $Season and c.Juego_ID = $Game and c.Cantidad > 0
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
							0 Jugado
						FROM $schema.Jugadores a
							join $schema.Goles d on a.Jugador_ID = d.Jugador_ID and d.Torneo_ID = $Season and d.Juego_ID = $Game and d.Goles > 0
						where a.Equipo_ID = $vequipoid and a.Estatus in ('A','D')
						 " . $JGsql . " ) a
				group by a.Jugador_ID, a.Numero, a.Nombre, a.Apellido_P, a.Apellido_M
				order by cast(a.Numero as decimal) asc;";
		$result2 = $Config->query($sql2);
		$htmlWeekGameDetail .= '<div id="all" class="datagridTeamSchedule">
                 <h2 style="margin-top: 10;">' . $vequipodesc . '</h2>
                  <table id="visitante" width="100%">
                      <thead>
                          <tr>
                              <th style="text-align:left;; padding: 0rem 0rem;" colspan="1">' . $lang['312'] . '</th>
                              <th style="text-align:left; padding: 0rem 0rem;" colspan="1">' . $lang['313'] . '</th>
                              <th style="text-align:left; padding: 0rem 0rem;" colspan="1">' . $lang['314'] . '</th>
                              <th style="text-align: center; padding: 0rem 0rem;" ' . $Config->JugadorJugado . '><img src="./imagenes/gamePlayedVoleibol.png" width="20" height="25" alt=""/></th>
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
					<td style='text-align:left'>
						" . $row2["Numero"] . "
					</td>
					<td style='text-align:left'><span class='text-secondary text-xs font-weight-normal'>" . $row2["Nombre"] . "</span>
					</td>
					<td style='text-align:left'><span class='text-secondary text-xs font-weight-normal'>
						" . $row2["Apellido_P"] . "
						" . $row2["Apellido_M"] . "
					</span></td>
					<td style='text-align:center' " .  $Config->JugadorJugado . ">";
					 if($row2["Jugado"] > 0){   
							$htmlWeekGameDetail .= "<input type='checkbox' checked disabled>";
					 }else{
							$htmlWeekGameDetail .= "<input type='checkbox' disabled>";
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
        </td>
	  </tr>
	  <tr>
		<td height="20">
		</td>
	  </tr>';
	$docGames = false;
	if(file_exists ('../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo1.png') || 
	   file_exists ('../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo2.png') || 
	   file_exists ('../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo3.png') || 
	   file_exists ('../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo4.png')){
		$docGames = true;
	}
	if($Config->JuegoCedulas == 0 || !$docGames){
		$htmlWeekGameDetail .= "<!--";
	}
	$htmlWeekGameDetail .= '<tr><td colspan="3" style="align-content:center; text-align:center">';
 	$htmlWeekGameDetail .= '<table width="100%" border="1" cellspacing="0" cellpadding="0">
              <tr>
                <th width="25%">' . $lang['317'] . '</th>
                <th width="25%">' . $lang['318'] . '</th>
                <th width="25%">' . $lang['319'] . '</th>
                <th width="25%">' . $lang['320'] . '</th>
              </tr>
              <tr style="height:30px;">
                <td style="height:30px; text-align: center;">
                    <span style="text-align: center;">';
	if(file_exists ('../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo1.png')){
		$htmlWeekGameDetail .= '<img id="Anexo1' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo1.png?tmp=' . $fecha->getTimestamp() . '" alt="Foto" style="width: 100%; height: auto;">';
	}
    $htmlWeekGameDetail .= '</span>
                </td>
                <td style="height:30px; text-align: center;">
                    <span style="text-align: center; ">';
	if(file_exists ('../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo2.png')){
		$htmlWeekGameDetail .= '<img id="Anexo2' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo2.png?tmp=' . $fecha->getTimestamp() . '" alt="Foto" style="width: 100%; height: auto;">';
	}
    $htmlWeekGameDetail .= '</span>
                 </td>
                <td style="height:30px; text-align: center;">
                    <span style="text-align: center; ">';
	if(file_exists ('../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo3.png')){
		$htmlWeekGameDetail .= '<img id="Anexo3' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo3.png?tmp=' . $fecha->getTimestamp() . '" alt="Foto" style="width: 100%; height: auto;">';
	}
    $htmlWeekGameDetail .= '</span>
                 </td>
                <td style="height:30px; text-align: center;">
                    <span style="text-align: center; ">';
	if(file_exists ('../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo4.png')){
		$htmlWeekGameDetail .= '<img id="Anexo4' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo4.png?tmp=' . $fecha->getTimestamp() . '" alt="Foto" style="width: 100%; height: auto;">';
	}
    $htmlWeekGameDetail .= '</span>
                 </td>
              </tr>
            </table>';
		$htmlWeekGameDetail .= '</td></tr>';
		if($Config->JuegoCedulas == 0){
			$htmlWeekGameDetail .= "-->";
		}
	}
	$htmlWeekGameDetail .= '<tr><td style="height: 30;"></td></tr>';
	$htmlWeekGameDetail .= '</table>';
    $retunData = array('status' => '1', 'message' => 'Success.', 'dataWeekGameDetail' => $htmlWeekGameDetail);
    $Config->Close();
    echo json_encode($retunData);
?>