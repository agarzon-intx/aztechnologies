			<?php
			$fecha = new DateTime();
			$sqlcat = "and l.Fuerza = $Category";
			if($vs == 1){
				$sqlcat = "";
			}
				
			$sql2 = "select * from (
								select 0 as VisitanteS, 
										j.Torneo_ID as Torneo, 
										jo.Jornada_ID as Jornada, 
										Juego_ID as juego, 
										jugado, 
										case when j.Visitante_Id is null then '' else concat(l.equipo_desc,'') end  as 'Local',
										case when j.Visitante_ID is null then null when jugado = 0 then '' else CONCAT(Gol_Local,'') end as 'Goles Local', 
										case when j.Visitante_ID is null then null else Penal_local end as 'Penalties Local', 
										concat(case when j.Visitante_ID is null then null else v.equipo_desc end,'') as 'Visitante', 
										case when j.Visitante_ID is null then null when jugado = 0 then '' else CONCAT(Gol_Visitante,'') end as 'Goles Visitante', 
										case when j.Visitante_ID is null then null else Penal_Visitante end as 'Penalties Visitante', 
										case when j.Visitante_ID is null then null else Arbitro end as Arbitro, 
										case 
											when j.Visitante_Id is null then CONCAT('<img src=\"imagenes/',concat(l.Torneo_ID,'-', l.Equipo_ID),'.png?tmp=" . $fecha->getTimestamp() . "\" class=\"avatar avatar-sm me-0\" style=\"border-radius: 0rem !important;\"/>',l.equipo_desc,' " . $lang['654'] . "') 
											else Comentarios 
										end as Comentarios, 
										Estatus, 
										case when j.Visitante_ID is null then null else ifnull(jc.Campo_DESC, lc.Campo_DESC) end as Campo, 
										ifnull(jc.Google, lc.Google) as Google, 
										case 
											when j.Visitante_Id is null then ''
											else
												CONCAT('<img src=\"imagenes/',concat(l.Torneo_ID,'-', l.Equipo_ID),'.png?tmp=" . $fecha->getTimestamp() . "\" class=\"avatar avatar-sm me-0\" style=\"border-radius: 0rem !important;\"/>') 
										end as Logol, 
										case 
											when j.Visitante_Id is null then ''
											else
												CONCAT('<img src=\"imagenes/',concat(v.Torneo_ID,'-', v.Equipo_ID),'.png?tmp=" . $fecha->getTimestamp() . "\" class=\"avatar avatar-sm me-0\" style=\"border-radius: 0rem !important;\"/>') 
										end  as Logov,
										case 
											when jugado = 0 then '' 
											else '-'
										end as marcador,
										case 
											when jugado = 0 then '' 
											else case when Penal_local > Penal_Visitante then '*' else '' end
										end as marcadorpl,
										case 
											when jugado = 0 then '' 
											else case when Penal_local < Penal_Visitante then '*' else '' end
										end as marcadorpv,
										TIME_FORMAT(Horario, '%H:%i%p') horario,
										case when j.Visitante_Id is null then DATE_ADD(j.Fecha, INTERVAL 30 DAY) else j.Fecha end  as Fecha,
										case when (weekday(j.Fecha) = (SELECT MarcadorDiaDefault-1 FROM $schema.Configuration) and j.Horario = (SELECT MarcadorHoraDefault FROM $schema.Configuration)) then 'No Programado' else '' end Pendiente,
										case when (weekday(j.Fecha) = (SELECT MarcadorDiaDefault-1 FROM $schema.Configuration) and j.Horario = (SELECT MarcadorHoraDefault FROM $schema.Configuration)) then 1 else 0 end PendienteFlag
									from  $schema.Juegos as j 
										left outer join $schema.Campos jc on j.Campo_ID = jc.Campo_ID
										join $schema.Equipos as l on j.local_ID = l.Equipo_ID and j.Torneo_ID = $Season and l.Torneo_ID = $Season
										left outer join $schema.Campos lc on l.Campo_ID = lc.Campo_ID
										left outer join $schema.Equipos as v on j.Visitante_ID = v.Equipo_ID and v.Torneo_ID = $Season
										join $schema.Jornada as jo on j.Fecha between jo.Fecha_Inicio and jo.Fecha_Fin
									where jo.Jornada_ID = $Week and Visitante_ID is not null $sqlcat
									UNION
									select 1 as VisitanteS, j.Torneo_ID as Torneo, jo.Jornada_ID as Jornada, Juego_ID as juego, jugado, case when j.Visitante_Id is null then '' else concat(l.equipo_desc,'') end  as 'Local', 
										case when j.Visitante_ID is null then null when jugado = 0 then '' else CONCAT(Gol_Local,'') end as 'Goles Local', case when j.Visitante_ID is null then null else Penal_local end as 'Penalties Local', 
										concat(case when j.Visitante_ID is null then null else v.equipo_desc end,'') as 'Visitante', case when j.Visitante_ID is null then null when jugado = 0 then '' else CONCAT(Gol_Visitante,'') end as 'Goles Visitante', 
										case when j.Visitante_ID is null then null else Penal_Visitante end as 'Penalties Visitante', case when j.Visitante_ID is null then null else Arbitro end as Arbitro, 
										case 
											when j.Visitante_Id is null then CONCAT('<img src=\"imagenes/', concat(l.Torneo_ID,'-', l.Equipo_ID), '.png?tmp=" . $fecha->getTimestamp() . "\" class=\"avatar avatar-sm me-0\" style=\"border-radius: 0rem !important;\"/>',l.equipo_desc,' " . $lang['654'] . "') 
											else Comentarios 
										end as Comentarios, 
										Estatus, case when j.Visitante_ID is null then null else ifnull(jc.Campo_DESC, lc.Campo_DESC) end as Campo, ifnull(jc.Google, lc.Google) as Google, 
										case 
											when j.Visitante_Id is null then ''
											else
												CONCAT('<img src=\"imagenes/',concat(v.Torneo_ID,'-', v.Equipo_ID),'.png?tmp=" . $fecha->getTimestamp() . "\" class=\"avatar avatar-sm me-0\" style=\"border-radius: 0rem !important;\"/>') 
										end as Logol, 
										case 
											when j.Visitante_Id is null then ''
											else
												CONCAT('<img src=\"imagenes/',concat(v.Torneo_ID,'-', v.Equipo_ID),'.png?tmp=" . $fecha->getTimestamp() . "\" class=\"avatar avatar-sm me-0\" style=\"border-radius: 0rem !important;\"/>') 
										end  as Logov,
										case 
											when jugado = 0 then '' 
											else '-'
										end as marcador,
										case 
											when jugado = 0 then '' 
											else case when Penal_local > Penal_Visitante then '*' else '' end
										end as marcadorpl,
										case 
											when jugado = 0 then '' 
											else case when Penal_local < Penal_Visitante then '*' else '' end
										end as marcadorpv,
										TIME_FORMAT(Horario, '%H:%i%p') horario,
										case when j.Visitante_Id is null then DATE_ADD(j.Fecha, INTERVAL 30 DAY) else j.Fecha end  as Fecha,
										case when (weekday(j.Fecha) = (SELECT MarcadorDiaDefault-1 FROM $schema.Configuration) and j.Horario = (SELECT MarcadorHoraDefault FROM $schema.Configuration)) then 'No Programado' else '' end Pendiente,
										case when (weekday(j.Fecha) = (SELECT MarcadorDiaDefault-1 FROM $schema.Configuration) and j.Horario = (SELECT MarcadorHoraDefault FROM $schema.Configuration)) then 1 else 0 end PendienteFlag
									from  $schema.Juegos as j 
										left outer join $schema.Campos jc on j.Campo_ID = jc.Campo_ID
										join $schema.Equipos as l on j.local_ID = l.Equipo_ID and j.Torneo_ID = $Season and l.Torneo_ID = $Season
										left outer join $schema.Campos lc on l.Campo_ID = lc.Campo_ID
										left outer join $schema.Equipos as v on j.Visitante_ID = v.Equipo_ID and v.Torneo_ID = $Season
										join $schema.Jornada as jo on j.Fecha between jo.Fecha_Inicio and jo.Fecha_Fin and jo.Torneo_ID = $Season
									where jo.Jornada_ID = $Week and Visitante_ID is null $sqlcat) a
						order by Fecha asc, VisitanteS, Torneo, Jornada, Juego;";
				//echo $sql2;
				$result2 = $Config->query($sql2);
				$htmlWeek .= '<div class="d-none  d-xs-none d-md-none d-lg-none d-xl-block"><div class="card">
								<div class="table-responsive">
									<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
										<thead class="">
											<th scope="col" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">' . $lang['609']  . ' ' . $row["Fecha_Inicio"] . ' ' . $lang['610'] . ' ' . $row["Fecha_Fin"] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">' . $lang['604'] . ' / ' . $lang['605'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">' . $lang['606'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" ' . $Config->referee . '>' . $lang['607'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">' . $lang['608'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">' . $lang['659'] . '</th>';
				$htmlWeek .= '</thead>';
				$htmlWeek .= '<tbody>';
				$count = 0;
				if ($result2->num_rows > 0) {
					// output data of each row
					while($row2 = $result2->fetch_assoc()) {
						if (($count % 2) == 1){
							$htmlWeek .= "<tr>";
						}else{
							$htmlWeek .= "<tr class='alt'>";
						}
						$statusText = '';
						$htmlWeek .= '
							<td scope="row"><div class="d-flex px-2 py-1"><div style="text-wrap: balance; width: 240px;text-align: right;padding-right: 3px;padding-top: 6px;">' . $row2["Local"] . '</div>
							<div>' . $row2["Logol"] . '</div>';
						$sql20 = "   SELECT * FROM $schema.Juego_Estatus
                                    where Juego_Estatus_ID = " . $row2["jugado"] . ";";
        				//echo $sql2;
        				$result20 = $Config->query($sql20);
        				if ($result20->num_rows > 0) {
        					// output data of each row
        					while($row20 = $result20->fetch_assoc()) {
    							$statusText = $lang[$row20["Juego_Estatus_DESC_ID"]];
        					}
        				}
						if($row2["jugado"] > 1){
							$htmlWeek .= '<div style="text-wrap: wrap; width: 80px;text-align: center;padding-top: 6px;"><span style="font-size: small;">' . $statusText . '</span></div>';
						}
						if($row2["jugado"] == 1 || $row2["jugado"] == 0){
							$htmlWeek .= '
							<div ' .  $Config->EmpatesPenales . ' style="width: 15px;text-align: center;padding-top: 6px;">' . $row2["marcadorpl"] . '</div>
							<div style="width: 20px;text-align: right;padding-top: 6px;">' . $row2["Goles Local"] . '</div>
							<div style="width: 10px;text-align: center;padding-top: 6px;">' . $row2["marcador"] . '</div>
							<div style="width: 20px;text-align: left;padding-top: 6px;">' . $row2["Goles Visitante"] . '</div>
							<div ' .  $Config->EmpatesPenales . ' style="width: 15px;text-align: center;padding-top: 6px;">' . $row2["marcadorpv"] . '</div>';
						}
						$htmlWeek .= '<div>' . $row2["Logov"] . '</div>
							<div style="text-wrap: balance; width: 240px;text-left: right;padding-left: 3px;padding-top: 6px;">' . $row2["Visitante"] .'</div></div></td>
							<td  scope="row" class="align-middle text-center">';
								if (strpos($row2["Comentarios"],$lang['654']) !== false){
									$htmlWeek .= "";
								}else{
								    if($row2["PendienteFlag"]){
								        $htmlWeek .= '<span class="text-secondary text-xs font-weight-normal">' . $row2["Pendiente"] . '</span>';
								    }else{
									    $htmlWeek .= '<span class="text-secondary text-xs font-weight-normal">' . $row2["Fecha"]. ' / ' . $row2["horario"]. '</span>';
								    }
								}
							$htmlWeek .= '</td>';
							if (strpos($row2["Comentarios"],$lang['654']) !== false){
								$htmlWeek .= '<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["Comentarios"]. '</span>';
								$htmlWeek .= '<td scope="row" class="align-middle text-center"></td>';
							}else{
								$htmlWeek .= '<td  scope="row" class="align-middle text-center"><a target="_blank" href="' . $row2["Google"] . '"><span class="text-secondary text-xs font-weight-normal">' . $row2["Campo"]. '</span></a></td>';
								$htmlWeek .= '<td  scope="row" class="align-middle text-center"' .  $Config->referee . '><span class="text-secondary text-xs font-weight-normal">' . $row2["Arbitro"]. '</span>';
							}

							$htmlWeek .= '</td>';
							if (strpos($row2["Comentarios"],$lang['654']) !== false){
								$htmlWeek .= '<td scope="row" class="align-middle text-center"></td>';
							}else{
								if($row2["jugado"] == 1){
									if(strcmp($row2["Comentarios"],"")==0){
										$htmlWeek .= '<td  scope="row" class="align-middle text-center">';
									    $htmlWeek .= '</td>';
									}else{
										$htmlWeek .= '<td  scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal"><img src="imagenes/comments.png" class="avatar avatar-sm me-3" style="border-radius: 0rem !important;"title="' . $row2["Comentarios"]. '"/></span>';
									    $htmlWeek .= '</td>';
									}
								}else{
									if(strcmp($row2["Comentarios"],"")==0){
										$htmlWeek .= '<td  scope="row" class="align-middle text-center">';
									    $htmlWeek .= '</td>';
									}else{
										$htmlWeek .= '<td  scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal"><img src="imagenes/comments.png" class="avatar avatar-sm me-3" style="border-radius: 0rem !important;"title="' . $row2["Comentarios"]. '"/></span>';
									    $htmlWeek .= '</td>';
									}
								}
							}
						    $htmlWeek .= '<td  scope="row" class="align-middle text-center">';
							if (strpos($row2["Comentarios"],$lang['654']) !== false){
								$htmlWeek .= ' ';
							}else{
								if($row2["jugado"] != 0){
									$htmlWeek .= '<img class="expandirButton" id="expandir' . $row2["juego"] . '" src="./imagenes/expandir.png" height="25" width="25" onClick="abrirFicha(' . $row2["juego"] . ', ' . $row2["Jornada"] . ', ' . $row2["juego"] . ', \'' . $row2["Local"] . ' vs ' . $row2["Visitante"] . '\', \'' . $row2["Goles Local"] . '\', \'' . $row2["Goles Visitante"] . '\'); ">';
								}
							}
							
    						if (strpos($row2["Comentarios"],$lang['654']) !== false){
    							$htmlWeek .= "";
    						}else{
    							$htmlWeek .= '<a href="pdf/flyer.php?Juego_ID=' . $row2["juego"] . '" target="_blank" download=""><img src="imagenes/flyer.png" width="20" height="20"></a></td>';
    							
    						}
							$htmlWeek .= '</td>';
						$htmlWeek .= '</tr>';
						if (strpos($row2["Comentarios"],$lang['654']) == false){
							$htmlWeek .= '<tr id="' . $row2["juego"] . '" class="juego" style="display: none">
									<td  scope="row" colspan="14" style="width: 1183px; padding-left: 0px; padding-right: 0px;">
										<div class="contentEditFicha" width="100%" id="content' . $row2["juego"] . '" height="400"></div>
									</td>
								  </tr>';
						}
						$count++;
					}
				}
				
				$htmlWeek .= '<tr>';
				$htmlWeek .= '<td colspan="12">' . $lang['611'] . '</td>';
				$htmlWeek .= '</tr>';
				$htmlWeek .= '</tbody>';
				$htmlWeek .= '</table>';
				$htmlWeek .= '</div>';
				$htmlWeek .= '</div>';
				$htmlWeek .= '</div>';
				
				
				//echo $sql2;
				$result2 = $Config->query($sql2);
				$htmlWeek .= '<div class="d-md-block d-lg-block d-xl-none"><div class="card">
								<div class="table-responsive">
									<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
										<thead class="">
											<th scope="col" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">' . $lang['609']  . ' ' . $row["Fecha_Inicio"] . ' ' . $lang['610'] . ' ' . $row["Fecha_Fin"] . '</th>';
				$htmlWeek .= '</thead>';
				$htmlWeek .= '<tbody>';
				$count = 0;
				if ($result2->num_rows > 0) {
					// output data of each row
					while($row2 = $result2->fetch_assoc()) {
						if (($count % 2) == 1){
							$htmlWeek .= '<tr >';
						}else{
							$htmlWeek .= '<tr class="alt" >';
						}
						$htmlWeek .= '
							<td scope="row"><div class="justify-content-center d-flex px-0 py-1"><div class="align-self-center" style="text-wrap: balance; width: 30%; text-align: right;padding-right: 10px; font-size:3vw;">' . $row2["Local"] . '</div>
							<div class="align-self-center">' . $row2["Logol"] . '</div>';
						if($row2["jugado"] == 2){
							$htmlWeek .= '<div style="width: 80px;text-align: center;" class="align-self-center"><span style="font-size: small;">' . $lang['663'] . '</span></div>';
						}else{
							$htmlWeek .= '
							<div ' .  $Config->EmpatesPenales . ' style="width: 15px;text-align: center;" class="align-self-center">' . $row2["marcadorpl"] . '</div>
							<div style="width: 20px;text-align: right;" class="align-self-center">' . $row2["Goles Local"] . '</div>
							<div style="width: 10px;text-align: center;" class="align-self-center">' . $row2["marcador"] . '</div>
							<div style="width: 20px;text-align: left;" class="align-self-center">' . $row2["Goles Visitante"] . '</div>
							<div ' .  $Config->EmpatesPenales . ' style="width: 15px;text-align: center;" class="align-self-center">' . $row2["marcadorpv"] . '</div>';
						}
						$htmlWeek .= '<div class="align-self-center">' . $row2["Logov"] . '</div>
							<div style="text-wrap: balance; width: 30%; text-align: left; padding-left: 10px; font-size:3vw;" class="align-self-center">' . $row2["Visitante"] .'</div></div><div class="d-flex px-0 py-1">';
						if (strpos($row2["Comentarios"],$lang['654']) !== false){
							$htmlWeek .= "";
						}else{
							$htmlWeek .= '<div style="width: 30%;text-align: center;padding-top: 6px;">
							<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['604'] . ' / ' . $lang['605'] . '</span></p>
							<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Fecha"]. '  ' . $row2["horario"]. '</span></div></div>';
						}
						$htmlWeek .= '</div>';
						if (strpos($row2["Comentarios"],$lang['654']) !== false){
							$htmlWeek .= '<div style="width: 30%;text-align: center;padding-top: 6px;">
							<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $row2["Comentarios"]. '</span></p>';
						}else{
							$htmlWeek .= '<div style="width: 30%;text-align: center;padding-top: 6px;">
							<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['606']. '</span></a></p>
							<p style="margin-bottom: 0rem !important;" class="lh-1"><a target="_blank" href="' . $row2["Google"] . '"><span class="text-secondary text-xs font-weight-normal  text-wrap">' . $row2["Campo"]. '</span></a></p></div>';
							$htmlWeek .= '<div style="width: 30%;text-align: center;padding-top: 6px;" ' .  $Config->referee . '>
							<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['607'] . '</span></p>
							<p style="margin-bottom: 0rem !important;" class="lh-1"><span class="text-secondary text-xs font-weight-normal  text-wrap">' . $row2["Arbitro"] . '</span></p>';
						}

						$htmlWeek .= '</div>';
						if (strpos($row2["Comentarios"],$lang['654']) !== false){
							$htmlWeek .= '';
						}else{
							if($row2["jugado"] == 1){
								if(strcmp($row2["Comentarios"],"")==0){
									$htmlWeek .= '<div style="width: 30%;text-align: center;padding-top: 6px;"><p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['608'] . '</span></p>';
								    $htmlWeek .= '</div>';
								}else{
									$htmlWeek .= '<div style="width: 30%;text-align: center;padding-top: 6px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['608'] . '</span></p>
									<span class="text-secondary text-xs font-weight-normal"><img src="imagenes/comments.png" class="avatar avatar-sm me-3" style="border-radius: 0rem !important;"title="' . $row2["Comentarios"]. '"/></span>';
								    $htmlWeek .= '</div>';
								}
							}else{
								if(strcmp($row2["Comentarios"],"")==0){
									$htmlWeek .= '<div style="width: 30%;text-align: center;padding-top: 6px;"><p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['608'] . '</span></p>';
								    $htmlWeek .= '</div>';
								}else{
									$htmlWeek .= '<div style="width: 30%;text-align: center;padding-top: 6px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['608'] . '</span></p>
									<span class="text-secondary text-xs font-weight-normal"><img src="imagenes/comments.png" class="avatar avatar-sm me-3" style="border-radius: 0rem !important;"title="' . $row2["Comentarios"]. '"/></span>';
								    $htmlWeek .= '</div>';
								}
							}
						}
						$htmlWeek .= '';
							if (strpos($row2["Comentarios"],$lang['654']) !== false){
								$htmlWeek .= '';
							}else{
								if($row2["jugado"] == 1){
									$htmlWeek .= '<div style="width: 10%;text-align: right;padding-top: 6px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['659'] . '</span></p>
									<img class="expandirButton" id="expandir' . $row2["juego"] . 'S" src="./imagenes/expandir.png" height="25" width="25" onClick="abrirFichaS(' . $row2["juego"] . ', ' . $row2["Jornada"] . ', ' . $row2["juego"] . ', \'' . $row2["Local"] . ' vs ' . $row2["Visitante"] . '\', \'' . $row2["Goles Local"] . '\', \'' . $row2["Goles Visitante"] . '\'); "></div>';
								}
							}
							if (strpos($row2["Comentarios"],$lang['654']) !== false){
    							$htmlWeek .= "";
    						}else{
    							$htmlWeek .= '<a href="pdf/flyer.php?Juego_ID=' . $row2["juego"] . '" target="_blank" download=""><img src="imagenes/flyer.png" width="20" height="20"></a></td>';
    							
    						}
						$htmlWeek .= '</div></td>';
						$htmlWeek .= '</tr>';
						if (strpos($row2["Comentarios"],$lang['654']) == false){
							$htmlWeek .= '<tr id="' . $row2["juego"] . 'S" class="juego" style="display: none">
									<td scope="row">
										<div id="content' . $row2["juego"] . 'S"></div>
									</td>
								  </tr>';
						}
						$count++;
					}
				}
				
				$htmlWeek .= '<tr>';
				$htmlWeek .= '<td colspan="12">' . $lang['611'] . '</td>';
				$htmlWeek .= '</tr>';
				$htmlWeek .= '</tbody>';
				$htmlWeek .= '</table>';
				$htmlWeek .= '</div>';
				$htmlWeek .= '</div>';
				
				
				$htmlWeek .= '</div>';
				?>