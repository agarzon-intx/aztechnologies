<?php 
		$fecha = new DateTime();
		// Create connection
        $sql = "select distinct jk.Jornada_Desc, j.Jornada_ID as Jornada, DATE_FORMAT(jk.Fecha_Inicio, '" . $Config->DateFormat3 . "') Fecha_Inicio, jk.Fecha_Inicio Fecha_InicioS, DATE_FORMAT(jk.Fecha_Fin, '" . $Config->DateFormat3 . "') Fecha_Fin, jk.Fecha_Fin Fecha_FinS, 
					case when k.Jornada_ID is null then 0 else 1 end as Activo
				from  $schema.Juegos as j 
					left outer join (select ifnull((SELECT 
				  Jornada_ID
				FROM 
				  $schema.Jornada
				where Torneo_ID = $Season and Fecha >= DATE_ADD(date(now()) , INTERVAL-3 DAY)
				LIMIT 1), (select max(Jornada_ID) from $schema.Jornada where Torneo_ID = $Season)) Jornada_ID) k on j.Jornada_ID = k.Jornada_ID
					join $schema.Jornada jk on j.Jornada_ID = jk.Jornada_ID and jk.Torneo_ID = $Season
					join $schema.Equipos e on j.Local_ID = e.Equipo_ID
				where j.Torneo_ID = $Season $sqlcat and j.Visitante_ID is not null
				order by jk.Fecha_Inicio desc;";

		//$htmlWeek .= $sql;
        $result1 = $Config->query($sql);
         if ($result1->num_rows > 0) {
            // output data of each row
            while($row = $result1->fetch_assoc()) {
				$sqlcat = "and l.Fuerza = $Category";
				if($vs == 1){
					$sqlcat = "";
				}
				
			
                $sql2 = "select * from (select 0 as VisitanteS, j.Torneo_ID as Torneo, Jornada_ID as Jornada, Juego_ID as juego, jugado, case when j.Visitante_Id is null then '' else concat(l.equipo_desc,'') end  as 'Local', 
                    case when j.Visitante_ID is null then null when jugado = 0 then '' else CONCAT(Gol_Local,'') end as 'Goles Local', case when j.Visitante_ID is null then null else Penal_local end as 'Penalties Local', 
                    concat(case when j.Visitante_ID is null then null else v.equipo_desc end,'') as 'Visitante', case when j.Visitante_ID is null then null when jugado = 0 then '' else CONCAT(Gol_Visitante,'') end as 'Goles Visitante', 
                    case when j.Visitante_ID is null then null else Penal_Visitante end as 'Penalties Visitante', case when j.Visitante_ID is null then null else Arbitro end as Arbitro, 
                    case 
                        when j.Visitante_Id is null then CONCAT('<div class=''circularsmall'' style=''height: 30px;width: 30px;border-radius:7px; text-align: left;''><img src=''imagenes/', concat(l.Torneo_ID,'-', l.Equipo_ID), '.png?tmp=" . $fecha->getTimestamp() . "'' width=''30'' height=''30'' alt='''' style=''vertical-align:middle !important;''/></div>','&nbsp;&nbsp;&nbsp;',l.equipo_desc,' " . $lang['654'] . "') 
                        else Comentarios 
                    end as Comentarios, Estatus, case when j.Visitante_ID is null then null else ifnull(jc.Campo_DESC, lc.Campo_DESC) end as Campo, ifnull(jc.Google, lc.Google) as Google, 
                    case 
                        when j.Visitante_Id is null then ''
                        else
                            CONCAT('<div class=''circularsmall'' style=''height: 30px;width: 30px;border-radius:7px;''><img src=''imagenes/',concat(l.Torneo_ID,'-', l.Equipo_ID),'.png?tmp=" . $fecha->getTimestamp() . "'' width=''30'' height=''30'' alt=''''/></div>') 
                    end as Logol, 
                    case 
                        when j.Visitante_Id is null then ''
                        else
                            CONCAT('<div class=''circularsmall'' style=''height: 30px;width: 30px;border-radius: 7px; text-align: left;''><img src=''imagenes/',concat(v.Torneo_ID,'-', v.Equipo_ID),'.png?tmp=" . $fecha->getTimestamp() . "'' width=''30'' height=''30'' alt=''''/></div>') 
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
					Fecha
                from  $schema.Juegos as j 
					left outer join $schema.Campos jc on j.Campo_ID = jc.Campo_ID
                    join $schema.Equipos as l on j.local_ID = l.Equipo_ID and j.Torneo_ID = $Season and l.Torneo_ID = $Season
                    left outer join $schema.Campos lc on l.Campo_ID = lc.Campo_ID
                    left outer join $schema.Equipos as v on j.Visitante_ID = v.Equipo_ID and v.Torneo_ID = $Season
                where j.Fecha between '".$row["Fecha_InicioS"]."' and '".$row["Fecha_FinS"]."' and Visitante_ID is not null $sqlcat
				UNION
				select 1 as VisitanteS, j.Torneo_ID as Torneo, Jornada_ID as Jornada, Juego_ID as juego, jugado, case when j.Visitante_Id is null then '' else concat(l.equipo_desc,'') end  as 'Local', 
                    case when j.Visitante_ID is null then null when jugado = 0 then '' else CONCAT(Gol_Local,'') end as 'Goles Local', case when j.Visitante_ID is null then null else Penal_local end as 'Penalties Local', 
                    concat(case when j.Visitante_ID is null then null else v.equipo_desc end,'') as 'Visitante', case when j.Visitante_ID is null then null when jugado = 0 then '' else CONCAT(Gol_Visitante,'') end as 'Goles Visitante', 
                    case when j.Visitante_ID is null then null else Penal_Visitante end as 'Penalties Visitante', case when j.Visitante_ID is null then null else Arbitro end as Arbitro, 
                    case 
                        when j.Visitante_Id is null then CONCAT('<div class=''circularsmall'' style=''height: 30px;width: 30px;border-radius:7px; text-align: left;''><img src=''imagenes/', concat(l.Torneo_ID,'-', l.Equipo_ID), '.png?tmp=" . $fecha->getTimestamp() . "'' width=''30'' height=''30'' alt='''' style=''vertical-align:middle !important;''/></div>','&nbsp;&nbsp;&nbsp;',l.equipo_desc,' " . $lang['654'] . "') 
                        else Comentarios 
                    end as Comentarios, Estatus, case when j.Visitante_ID is null then null else ifnull(jc.Campo_DESC, lc.Campo_DESC) end as Campo, ifnull(jc.Google, lc.Google) as Google, 
                    case 
                        when j.Visitante_Id is null then ''
                        else
                            CONCAT('<div class=''circularsmall'' style=''height: 30px;width: 30px;border-radius:7px;''><img src=''imagenes/',concat(l.Torneo_ID,'-', l.Equipo_ID),'.png?tmp=" . $fecha->getTimestamp() . "'' width=''30'' height=''30'' alt=''''/></div>') 
                    end as Logol, 
                    case 
                        when j.Visitante_Id is null then ''
                        else
                            CONCAT('<div class=''circularsmall'' style=''height: 30px;width: 30px;border-radius: 7px; text-align: left;''><img src=''imagenes/',concat(v.Torneo_ID,'-', v.Equipo_ID),'.png?tmp=" . $fecha->getTimestamp() . "'' width=''30'' height=''30'' alt=''''/></div>') 
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
					Fecha
                from  $schema.Juegos as j 
					left outer join $schema.Campos jc on j.Campo_ID = jc.Campo_ID
                    join $schema.Equipos as l on j.local_ID = l.Equipo_ID and j.Torneo_ID = $Season and l.Torneo_ID = $Season
                    left outer join $schema.Campos lc on l.Campo_ID = lc.Campo_ID
                    left outer join $schema.Equipos as v on j.Visitante_ID = v.Equipo_ID and v.Torneo_ID = $Season
                where j.Fecha between '".$row["Fecha_InicioS"]."' and '".$row["Fecha_FinS"]."' and Visitante_ID is null $sqlcat) a
                order by VisitanteS, Torneo, Jornada, Juego;";
				//$htmlWeek .= $sql2;
                $result2 = $Config->query($sql2);
			
                $htmlWeek .= '<div id="all" class="datagridWeek" style="width: 100%">';
				$htmlWeek .= '<div>';
				$htmlWeek .= '<div class="tWrap" id="weekScheduleAll"' . $row["Jornada"] . '" style="overflow-x: none;">';
				$htmlWeek .= '<div class="tWrap__body" style="overflow-y: auto; width: 1200;">';
				$htmlWeek .= '<table style="font: normal 12px/150% Montserrat !important; width: 1183px">';
                $htmlWeek .= '<thead>';
                $htmlWeek .= '<tr>';
                if (is_numeric($row["Jornada_Desc"])){
					$htmlWeek .= '<th width="481" colspan="' . (9 - $Config->countHidden2) . '">' . $lang['609']  . ' ' . $row["Fecha_Inicio"] . ' ' . $lang['610'] . ' ' . $row["Fecha_Fin"] . ' --- ' . $lang['690'] . ' ' . $row["Jornada_Desc"] . '</th>';
				}else{
					$htmlWeek .= '<th width="481" colspan="' . (9 - $Config->countHidden2) . '">' . $lang['609']  . ' ' . $row["Fecha_Inicio"] . ' ' . $lang['610'] . ' ' . $row["Fecha_Fin"] . ' --- ' . $row["Jornada_Desc"] . '</th>';
				}
                $htmlWeek .= '<th width="77">' . $lang['604'] . '</th>';
                $htmlWeek .= '<th width="70" ' . $Config->time . '>' . $lang['605'] . '</th>';
                $htmlWeek .= '<th width="117">' . $lang['606'] . '</th>';
                $htmlWeek .= '<th width="320" ' . $Config->referee . '>' . $lang['607'] . '</th>';
                $htmlWeek .= '<th width="69">' . $lang['608'] . '</th>';
                $htmlWeek .= '<th width="48">' . $lang['659'] . '</th>';
                $htmlWeek .= '</tr>';
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
                        $htmlWeek .= "
                            <td style='width: 187;text-align:right;'>" . $row2["Local"] . "</td>
                            <td style='width: 30px; text-align:left;border:none;padding-right: 0px;padding-left: 0px; '>" . $row2["Logol"] . "</td>
                            <td style='width: 9px; padding-top: 0px;padding-right: 0px;padding-bottom: 0px;padding-left: 0px;text-align:right' " .  $Config->EmpatesPenales . ">" . $row2["marcadorpl"] . "</td>";
						if($row2["jugado"] == 2){
                            $htmlWeek .= "<td colspan='3' style='width: 21px; border:none;padding-right: 0px;padding-left: 0px;text-align:center;'>" . $lang['663'] . "</td>";
						}else{
                            $htmlWeek .= "<td style='width: 12px; border:none;padding-right: 0px;padding-left: 0px;text-align:right;'>" . $row2["Goles Local"] . "</td>
                            <td style='width: 5px; text-align:center;border:none;padding-right: 0px;padding-left: 0px;text-align:center;'>" . $row2["marcador"] . "</td>
                            <td style='width: 12px; text-align:left;border:none;padding-right: 0px;padding-left: 0px;text-align:center;'>" . $row2["Goles Visitante"] . "</td>";
						}
                        $htmlWeek .= "<td style='width: 9px; border:none;padding-top: 0px;padding-right: 0px;padding-bottom: 0px;padding-left: 0px;text-align:left' " .  $Config->EmpatesPenales . ">" . $row2["marcadorpv"] . "</td>
                            <td style='width: 30px; text-align:right;padding-right: 0px;padding-left: 0px;'>" . $row2["Logov"] . "</td>
                            <td style='width: 187px; border:none;text-align:left'>" . $row2["Visitante"] ."</td>
                            <td style='width: 77px; padding-top: 0px; padding-left: 10px;padding-right: 0px;text-align: left;vertical-align: middle;border-left-width: 0px;padding-bottom: 0px;'>";
								if (strpos($row2["Comentarios"],$lang['654']) !== false){
									$htmlWeek .= "";
								}else{
									$htmlWeek .= "" . $row2["Fecha"]. "";
								}
                            $htmlWeek .= "</td>
                            <td " .  $Config->time . " style='width: 70px; padding-top: 0px; padding-left: 10px;padding-right: 0px;text-align: left;vertical-align: middle;border-left-width: 0px;padding-bottom: 0px;'>";
								if (strpos($row2["Comentarios"],$lang['654']) !== false){
									$htmlWeek .= "";
								}else{
									$htmlWeek .= "" . $row2["horario"]. "";
								}
                            $htmlWeek .= "</td>";
                            if (strpos($row2["Comentarios"],$lang['654']) !== false){
								$htmlWeek .= "<td style='width: 389px; padding-top: 0px; padding-left: 0px;padding-right: 0px;text-align: center;vertical-align: middle;border-left-width: 0px;padding-bottom: 0px;'colspan='2'>" . $row2["Comentarios"]. "";
							}else{
                            	$htmlWeek .= "<td style='width: 117px; padding-top: 0px; padding-left: 0px;padding-right: 0px;text-align: left;vertical-align: middle;border-left-width: 0px;padding-bottom: 0px;'><a target='_blank' href='" . $row2["Google"] . "'>" . $row2["Campo"]. "</a></td>";
								$htmlWeek .= "<td style='width: 320px; padding-top: 0px; padding-left: 0px;padding-right: 0px;text-align: left;vertical-align: middle;border-left-width: 0px;padding-bottom: 0px;'" .  $Config->referee . ">" . $row2["Arbitro"]. "";
							}

                            $htmlWeek .= "</td>";
                                if (strpos($row2["Comentarios"],$lang['654']) !== false){
                                    $htmlWeek .= "";
                                }else{
                                    if($row2["jugado"] == 1){
                                        if(strcmp($row2["Comentarios"],"")==0){
                                            $htmlWeek .= "<td style='width: 69px; padding-top: 0px; padding-left: 0px;padding-right: 0px;text-align: center;vertical-align: middle;border-left-width: 0px;padding-bottom: 0px;'>";
		                                $htmlWeek .= "</td>";
                                        }else{
                                            $htmlWeek .= "<td style='width: 69px; padding-top: 0px; padding-left: 0px;padding-right: 0px;text-align: center;vertical-align: middle;border-left-width: 0px;padding-bottom: 0px;'>......<img src='imagenes/comments.png' width='30' height='30' title='" . $row2["Comentarios"]. "' alt=''/>";
		                                $htmlWeek .= "</td>";
                                        }
                                    }else{
                                        $htmlWeek .= "<td style='width: 69px; padding-top: 0px; padding-left: 0px;padding-right: 0px;text-align: center;vertical-align: middle;border-left-width: 0px;padding-bottom: 0px;'>";
		                                $htmlWeek .= "</td>";
                                    }
                                }
							$htmlWeek .= "<td style='width: 48px; padding-top: 0px; padding-left: 0px;padding-right: 0px;text-align: center;vertical-align: middle;border-left-width: 0px;padding-bottom: 0px;'>";
								if (strpos($row2["Comentarios"],$lang['654']) !== false){
									$htmlWeek .= "";
								}else{
									if($row2["jugado"] == 1){
										$htmlWeek .= '<img class="expandirButton" id="expandir' . $row2["juego"] . '" src="./imagenes/expandir.png" height="25" width="25" onClick="abrirFicha(' . $row2["juego"] . ', ' . $row2["Jornada"] . ', ' . $row2["juego"] . ', \'' . $row2["Local"] . ' vs ' . $row2["Visitante"] . '\', \'' . $row2["Goles Local"] . '\', \'' . $row2["Goles Visitante"] . '\'); ">';
									}
								}
							$htmlWeek .= "</td>";
                        $htmlWeek .= "</tr>";
						if (strpos($row2["Comentarios"],$lang['654']) == false){
							$htmlWeek .= "<tr id='" . $row2["juego"] . "' class='juego' style='display: none'>
									<td colspan='15' style='width: 1183px; padding-left: 0px; padding-right: 0px;'>
										<div class='contentEditFicha' width='100%' id='content" . $row2["juego"] . "' height='400'></div>
									</td>
								  </tr>";
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
                $htmlWeek .= '</div>';
                
			}
		 }

?>