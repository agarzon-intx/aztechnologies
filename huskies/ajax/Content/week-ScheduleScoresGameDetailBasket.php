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
            <tr style="background: url(./imagenes/marcadorbasket.png?tmp=' . $fecha->getTimestamp() . ') no-repeat; background-size:100% 100%;">
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
            </tr>';
    $retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');

    

	if($jugado == 1){
		$htmlWeekGameDetail .= '<tr>
			<td style="vertical-align:text-top; width: 49%">
				<div id="lista">';
		$JGsql = "";
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
		$result2 = $Config->query($sql2);
		$htmlWeekGameDetail .= '<div id="all" class="datagridTeamSchedule">
          			<h2 style="margin-top: 10;">' . $lequipodesc . '</h2>
						<table id="local" width="100%">
							<thead>
								<tr>
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
					<td style='text-align:left'>
						" . $row2["Numero"] . "
					</td>
					<td style='text-align:left'><span class='text-secondary text-xs font-weight-normal'>
						" . $row2["Nombre"] . " " . $row2["Apellido_P"] . " " . $row2["Apellido_M"] . "
					</span></td>
					<td style='text-align:center' " .  $Config->JugadorJugado . ">";
					 if($row2["Jugado"] > 0){   
							$htmlWeekGameDetail .= "<input type='checkbox' checked disabled>";
					 }else{
							$htmlWeekGameDetail .= "<input type='checkbox' disabled>";
					 }
					 $htmlWeekGameDetail .= "</td>
        				<td style='text-align:center'>" . $row2["AmarillasP"] . "</td>
        				<td style='text-align:center'>" . $row2["AmarillasT"] . "</td>
				        <td style='text-align:center'>";
    				 if($row2["Rojas"] > 0){   
    					$htmlWeekGameDetail .= "<input type='checkbox' name='rojaS' id='rojaS" . $row2["Jugador_ID"] . "' checked disabled>";
    				 }else{
    					$htmlWeekGameDetail .= "<input type='checkbox' name='rojaS' id='rojaS" . $row2["Jugador_ID"] . "' disabled>";
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
        <td width="20">
        </td>
          <td style="vertical-align:text-top; width: 49%">
	       	<div id="lista">';   
		$JGsql = "";
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
		$result2 = $Config->query($sql2);
		$htmlWeekGameDetail .= '<div id="all" class="datagridTeamSchedule">
                 <h2 style="margin-top: 10;">' . $vequipodesc . '</h2>
                  <table id="visitante" width="100%">
                      <thead>
                          <tr>
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
					<td style='text-align:left'>
						" . $row2["Numero"] . "
					</td>
					<td style='text-align:left'><span class='text-secondary text-xs font-weight-normal'>
						" . $row2["Nombre"] . " " . $row2["Apellido_P"] . " " . $row2["Apellido_M"] . "
					</span></td>
					<td style='text-align:center' " .  $Config->JugadorJugado . ">";
					 if($row2["Jugado"] > 0){   
							$htmlWeekGameDetail .= "<input type='checkbox' checked disabled>";
					 }else{
							$htmlWeekGameDetail .= "<input type='checkbox' disabled>";
					 }
					 $htmlWeekGameDetail .= "</td>
        				<td style='text-align:center'>" . $row2["AmarillasP"] . "</td>
        				<td style='text-align:center'>" . $row2["AmarillasT"] . "</td>
				        <td style='text-align:center'>";
    				 if($row2["Rojas"] > 0){   
    					$htmlWeekGameDetail .= "<input type='checkbox' name='rojaS' id='rojaS" . $row2["Jugador_ID"] . "' checked disabled>";
    				 }else{
    					$htmlWeekGameDetail .= "<input type='checkbox' name='rojaS' id='rojaS" . $row2["Jugador_ID"] . "' disabled>";
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