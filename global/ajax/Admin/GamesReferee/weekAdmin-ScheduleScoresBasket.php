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
										j.Juego_ID as juego, 
										jugado jugado1, 
										case 
											when jugado = 0 then 
												concat('<option value=''0'' selected>" . $lang['660'] . "</option><option value=''1''>" . $lang['661'] . "</option><option value=''2''>" . $lang['662'] . "</option>')
											when jugado = 1 then 
												concat('<option value=''0''>" . $lang['660'] . "</option><option value=''1'' selected>" . $lang['661'] . "</option><option value=''2''>" . $lang['662'] . "</option>')
											when jugado = 2 then 
												concat('<option value=''0''>" . $lang['660'] . "</option><option value=''1''>" . $lang['661'] . "</option><option value=''2'' selected>" . $lang['662'] . "</option>')
										end as jugado,
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
											else ':'
										end as marcador,
										TIME_FORMAT(Horario, '%H:%i') horario,
										case when j.Visitante_Id is null then DATE_ADD(j.Fecha, INTERVAL 30 DAY) else j.Fecha end  as Fecha,
										case when ifnull(s.Set1_L,0) > ifnull(s.Set1_V,0) then 1 else 0 end +
                                		case when ifnull(s.Set2_L,0) > ifnull(s.Set2_V,0) then 1 else 0 end +
                                        case when ifnull(s.Set3_L,0) > ifnull(s.Set3_V,0) then 1 else 0 end +
                                        case when ifnull(s.Set4_L,0) > ifnull(s.Set4_V,0) then 1 else 0 end +
                                        case when ifnull(s.Set5_L,0) > ifnull(s.Set5_V,0) then 1 else 0 end GL,
                                        case when ifnull(s.Set1_V,0) > ifnull(s.Set1_L,0) then 1 else 0 end +
                                		case when ifnull(s.Set2_V,0) > ifnull(s.Set2_L,0) then 1 else 0 end +
                                        case when ifnull(s.Set3_V,0) > ifnull(s.Set3_L,0) then 1 else 0 end +
                                        case when ifnull(s.Set4_V,0) > ifnull(s.Set4_L,0) then 1 else 0 end +
                                        case when ifnull(s.Set5_V,0) > ifnull(s.Set5_L,0) then 1 else 0 end GV,
                                        ifnull(s.Set1_L,0) + ifnull(s.Set2_L,0) + ifnull(s.Set3_L,0) + ifnull(s.Set4_L,0) + ifnull(s.Set5_L,0) PL,
                                        ifnull(s.Set1_V,0) + ifnull(s.Set2_V,0) + ifnull(s.Set3_V,0) + ifnull(s.Set4_V,0) + ifnull(s.Set5_V,0) PV
									from  $schema.Juegos as j 
										left outer join $schema.Campos jc on j.Campo_ID = jc.Campo_ID
										join $schema.Equipos as l on j.local_ID = l.Equipo_ID and j.Torneo_ID = $Season and l.Torneo_ID = $Season
										left outer join $schema.Campos lc on l.Campo_ID = lc.Campo_ID
										left outer join $schema.Equipos as v on j.Visitante_ID = v.Equipo_ID and v.Torneo_ID = $Season
										join $schema.Jornada as jo on j.Fecha between jo.Fecha_Inicio and jo.Fecha_Fin
										left outer join $schema.Juegos_Set s on j.Juego_ID = s.Juego_ID
									where jo.Jornada_ID = $Week and Visitante_ID is not null $sqlcat
									UNION
									select 1 as VisitanteS, 
									    j.Torneo_ID as Torneo, 
									    jo.Jornada_ID as Jornada, 
									    j.Juego_ID as juego, 
										jugado jugado1, 
									    case 
											when jugado = 0 then 
												concat('<option value=''0'' selected>" . $lang['660'] . "</option><option value=''1''>" . $lang['661'] . "</option><option value=''2''>" . $lang['662'] . "</option>')
											when jugado = 1 then 
												concat('<option value=''0''>" . $lang['660'] . "</option><option value=''1'' selected>" . $lang['661'] . "</option><option value=''2''>" . $lang['662'] . "</option>')
											when jugado = 2 then 
												concat('<option value=''0''>" . $lang['660'] . "</option><option value=''1''>" . $lang['661'] . "</option><option value=''2'' selected>" . $lang['662'] . "</option>')
										end as jugado,
									    case when j.Visitante_Id is null then '' else concat(l.equipo_desc,'') end  as 'Local', 
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
											else ':'
										end as marcador,
										TIME_FORMAT(Horario, '%H:%i') horario,
										case when j.Visitante_Id is null then DATE_ADD(j.Fecha, INTERVAL 30 DAY) else j.Fecha end  as Fecha,
										case when ifnull(s.Set1_L,0) > ifnull(s.Set1_V,0) then 1 else 0 end +
                                		case when ifnull(s.Set2_L,0) > ifnull(s.Set2_V,0) then 1 else 0 end +
                                        case when ifnull(s.Set3_L,0) > ifnull(s.Set3_V,0) then 1 else 0 end +
                                        case when ifnull(s.Set4_L,0) > ifnull(s.Set4_V,0) then 1 else 0 end +
                                        case when ifnull(s.Set5_L,0) > ifnull(s.Set5_V,0) then 1 else 0 end GL,
                                        case when ifnull(s.Set1_V,0) > ifnull(s.Set1_L,0) then 1 else 0 end +
                                		case when ifnull(s.Set2_V,0) > ifnull(s.Set2_L,0) then 1 else 0 end +
                                        case when ifnull(s.Set3_V,0) > ifnull(s.Set3_L,0) then 1 else 0 end +
                                        case when ifnull(s.Set4_V,0) > ifnull(s.Set4_L,0) then 1 else 0 end +
                                        case when ifnull(s.Set5_V,0) > ifnull(s.Set5_L,0) then 1 else 0 end GV,
                                        ifnull(s.Set1_L,0) + ifnull(s.Set2_L,0) + ifnull(s.Set3_L,0) + ifnull(s.Set4_L,0) + ifnull(s.Set5_L,0) PL,
                                        ifnull(s.Set1_V,0) + ifnull(s.Set2_V,0) + ifnull(s.Set3_V,0) + ifnull(s.Set4_V,0) + ifnull(s.Set5_V,0) PV
									from  $schema.Juegos as j 
										left outer join $schema.Campos jc on j.Campo_ID = jc.Campo_ID
										join $schema.Equipos as l on j.local_ID = l.Equipo_ID and j.Torneo_ID = $Season and l.Torneo_ID = $Season
										left outer join $schema.Campos lc on l.Campo_ID = lc.Campo_ID
										left outer join $schema.Equipos as v on j.Visitante_ID = v.Equipo_ID and v.Torneo_ID = $Season
										join $schema.Jornada as jo on j.Fecha between jo.Fecha_Inicio and jo.Fecha_Fin and jo.Torneo_ID = $Season
										left outer join $schema.Juegos_Set s on j.Juego_ID = s.Juego_ID
									where jo.Jornada_ID = $Week and Visitante_ID is null $sqlcat) a
						order by Fecha asc, VisitanteS, Torneo, Jornada, Juego;";
				//echo $sql2;
				$result2 = $Config->query($sql2);
                $htmlWeek .= '<div class="d-none  d-xs-none d-md-none d-lg-none d-xl-block"><div class="card">
								<div class="table-responsive">
									<table class=" table align-items-center mb-0" style="border-color: #136aeb;" id="scores">
										<thead class="">
											<th scope="col" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding-right: .25rem; padding-left: .25rem;"></th>
											<th scope="col" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding-right: .25rem; padding-left: .25rem;">' . $lang['609']  . ' ' . $row["Fecha_Inicio"] . ' ' . $lang['610'] . ' ' . $row["Fecha_Fin"] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding-right: .25rem; padding-left: .25rem;">' . $lang['604'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" ' . $Config->time . ' style="padding-right: .25rem; padding-left: .25rem;">' . $lang['605'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding-right: .25rem; padding-left: .25rem;">' . $lang['606'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding-right: .25rem; padding-left: .25rem;">' . $lang['649'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"></th>';
				$htmlWeek .= '</thead>';
				$htmlWeek .= '<tbody>';
				$count = 0;
				if ($result2->num_rows > 0) {
					// output data of each row
					while($row2 = $result2->fetch_assoc()) {
						if (($count % 2) == 1){
							$htmlWeek .= '<tr class="mainValues" id="' . $row2["juego"] . '">';
						}else{
							$htmlWeek .= '<tr class="alt mainValues" id="' . $row2["juego"] . '">';
						}
						$htmlWeek .= '<input name="torneo' . $row2["juego"] . '" type="hidden" id="torneo' . $row2["juego"] . '" value="' . $row2["Torneo"] . '">
								<input name="jornada' . $row2["juego"] . '" type="hidden" id="jornada' . $row2["juego"] . '" value="' . $row2["Jornada"] . '">
								<input name="juego' . $row2["juego"] . '" type="hidden" id="juego' . $row2["juego"] . '" value="' . $row2["juego"] . '">
								<input name="local' . $row2["juego"] . '" type="hidden" id="local' . $row2["juego"] . '" value="' . $row2["Local_ID"] . '">
								<input name="visitante' . $row2["juego"] . '" type="hidden" id="visitante' . $row2["juego"] . '" value="' . $row2["Visitante_ID"] . '">
							<td scope="row">
							</td>';
						if (strpos($row2["Comentarios"],$lang['654']) !== false){
								$htmlWeek .= '<td scope="row"><div class="d-flex px-2 py-1">
								<div style="width: 200px;text-align: center;padding-top: 6px;">' . $row2["Comentarios"] . '</div></div></td>';
						}else{
						    if($row2["jugado1"] == 1){
						    	if($row2["PL"] > $row2["PV"]){
    						        $colorSL = 'green';
    						        $colorSV = 'red';
    						    }
    						    if($row2["PL"] < $row2["PV"]){
    						        $colorSV = 'green';
    						        $colorSL = 'red';
    						    }
    						    if($row2["PL"] == $row2["PV"]){
    						        $colorSV = 'inherit';
    						        $colorSL = 'inherit';
    						    }
    							
    							$htmlWeek .= '<td scope="row">
    							                <div class="d-flex px-2 py-1"><div style="width: 120px;text-align: right;padding-right: 3px;padding-top: 6px;">' . $row2["Local"] . '</div>
								                <div>' . $row2["Logol"] . '</div>
								                <div style="width: 30px;text-align: right;padding-top: 6px;color: ' . $colorSL . ';">' . $row2["PL"] . '</div>
                    							<div style="width: 10px;text-align: center;padding-top: 6px;">' . $row2["marcador"] . '</div>
                    							<div style="width: 30px;text-align: left;padding-top: 6px;color: ' . $colorSV . ';">' . $row2["PV"] . '</div>
                    							<div>' . $row2["Logov"] . '</div>
								                <div style="width: 120px;text-left: right;padding-left: 3px;padding-top: 6px;">' . $row2["Visitante"] .'</div></div>
								            </td>';
                				
    				        }
						    if($row2["jugado1"] == 0){
						    	$colorSV = 'inherit';
						    	$colorSL = 'inherit';
    							
    							$htmlWeek .= '<td scope="row">
    							                <div class="d-flex px-2 py-1"><div style="width: 120px;text-align: right;padding-right: 3px;padding-top: 6px;">' . $row2["Local"] . '</div>
								                <div>' . $row2["Logol"] . '</div>
								                <div style="width: 30px;text-align: right;padding-top: 6px;color: ' . $colorSL . ';"></div>
                    							<div style="width: 10px;text-align: center;padding-top: 6px;"></div>
                    							<div style="width: 30px;text-align: left;padding-top: 6px;color: ' . $colorSV . ';"></div>
                    							<div>' . $row2["Logov"] . '</div>
								                <div style="width: 120px;text-left: right;padding-left: 3px;padding-top: 6px;">' . $row2["Visitante"] .'</div></div>
								            </td>';
                				
    				        }
						}
						if (strpos($row2["Comentarios"],$lang['654']) !== false){
							$htmlWeek .= '<td  scope="row" class="align-middle text-center"></td>';
						}else{
							$htmlWeek .= '<td  scope="row" class="align-middle text-center">
											<div class="input-group input-group-sm input-group-outline my-0">
												<input type="date" style="width: 110px;text-align: center;padding-right: 0px !important;padding-left: 0px !important;" class="form-control form-control-sm" value="' . $row2["Fecha"] . '" name="fecha' . $row2["juego"] . '" id="fecha' . $row2["juego"] . '">
											</div>
										</td>';
						}
						if (strpos($row2["Comentarios"],$lang['654']) !== false){
							$htmlWeek .= '<td  scope="row" class="align-middle text-center" ' .  $Config->time . ' ></td>';
						}else{
							$htmlWeek .= '<td  scope="row" class="align-middle text-center" ' .  $Config->time . ' >
											<div class="input-group input-group-sm input-group-outline my-0">
												<input type="time" style="width: 92px;text-align: center;padding-right: 0px !important;padding-left: 0px !important;" class="form-control form-control-sm" value="' . $row2["horario"] . '" name="horario' . $row2["juego"] . '" id="horario' . $row2["juego"] . '">
											</div>
										</td>';
						}
						
						if (strpos($row2["Comentarios"],$lang['654']) !== false){
							$htmlWeek .= "<td scope='row' class='align-middle text-center'></td>";
						}else{
							$htmlWeek .= '<td scope="row" class="align-middle text-center">
								<div class="input-group input-group-static mb-0">
									<select class="form-control" style="width : 90px;" name="campo' . $row2["juego"] . '" id="campo' . $row2["juego"] . '">';
							$sql3 = "SELECT Campo_ID, Campo_DESC FROM $schema.Campos
										order by Campo_DESC asc;";
							$result3 = $Config->query($sql3);
							if ($result3->num_rows > 0) {
								// output data of each row
								while($row3 = $result3->fetch_assoc()) {
									if($row3["Campo_DESC"] == $row2["Campo"]){
										$htmlWeek .= "<option value='" . $row3["Campo_ID"] . "' selected>" . $row3["Campo_DESC"] . "</option>";
									}else{
										$htmlWeek .= "<option value='" . $row3["Campo_ID"] . "'>" . $row3["Campo_DESC"] . "</option>";
									}
								}
							}
							$htmlWeek .= '
								 </select>
							   </div>';
							
						}
						if (strpos($row2["Comentarios"],$lang['654']) !== false){
							$htmlWeek .= '<td  scope="row" class="align-middle text-center"></td>';
						}else{
							$htmlWeek .= '<td scope="row" class="align-middle text-center">
											<div class="input-group input-group-static mb-0">
												<select class="form-control" style="width : 40px;" name="jugado' . $row2["juego"] . '" id="jugado' . $row2["juego"] . '">' . $row2["jugado"] . '</select>
											</div>';
						}
						if (strpos($row2["Comentarios"],$lang['654']) !== false){
							$htmlWeek .= '<td  scope="row" class="align-middle text-center"></td>';;
						}else{
							//if($row2["jugadoStat"] == 1){
								$htmlWeek .= '<td  scope="row" class="align-middle text-center">' . '<img class="expandirButton" id="expandir' . $row2["juego"] . '" src="./imagenes/expandir.png" height="25" width="25" onClick="abrirFichaEditBasketR(' . $row2["juego"] . ', ' . $row2["Jornada"] . ', ' . $row2["juego"] . ', \'' . $row2["Local"] . ' vs ' . $row2["Visitante"] . '\', \'' . $row2["Goles Local"] . '\', \'' . $row2["Goles Visitante"] . '\', \'' . $row2["Arbitro"] . '\', \'' . $row2["Comentarios"] . '\', 0, 0, \'' . $sqlcat . '\'); "></td>';
							//}else{
							//	$htmlWeek .= '<td  scope="row" class="align-middle text-center"></td>';
							//}
						}
						$htmlWeek .= '</tr>';
						if (strpos($row2["Comentarios"],$lang['654']) == false){
							$htmlWeek .= '<tr id="edit' . $row2["juego"] . '" class="juego" style="display: none">
									<td  scope="row" colspan="14" style="width: 1183px; padding-left: 0px; padding-right: 0px;">
										<div class="contentEditFicha" width="100%" id="content' . $row2["juego"] . '" height="400"></div>
									</td>
								  </tr>';
						}
						$count++;
					}
				}
				$htmlWeek .= '	<tr>
									<td style="border-bottom: 0;" colspan="2">
										<button type="button" class="btn btn-primary" onclick="saveChangesR(' . $Season . ',' . $Week . ');">' . $lang['0000'] . '</button>
									</td>
								</tr>';
				
					
				$sqlcat = "and a.Fuerza = $Category";
				if($vs == 1){
					$sqlcat = "";
				}
			    $sqloneperweek = "";				
			    $sqloneperweekcond = "";				
				if($Config->unjuegosemanal == 1){
    			    $sqloneperweek = "left outer join (
    									select j.Local_ID as Equipo_ID
    									from  $schema.Juegos as j 
    										join $schema.Jornada as jo on j.Fecha between jo.Fecha_Inicio and jo.Fecha_Fin
    									where jo.Jornada_ID = $Week
    									UNION
    									select j.Visitante_ID as Equipo_ID
    									from  $schema.Juegos as j 
    										join $schema.Jornada as jo on j.Fecha between jo.Fecha_Inicio and jo.Fecha_Fin
    									where jo.Jornada_ID = $Week) b on a.Equipo_ID = b.Equipo_ID";				
    				$sqloneperweekcond = "and b.Equipo_ID is null";
				}
				$sql33 = "SELECT a.Equipo_ID, 
								Equipo_DESC 
						 FROM $schema.Equipos a
						$sqloneperweek
						 where Torneo_ID = $Season 
							and Equipo_Desc <> 'NA' 
							and Activo = 1 $sqlcat
							$sqloneperweekcond
						 order by 2 asc;";
				$result3 = $Config->query($sql33);
				
					
						
                $htmlWeek .= '</tbody>';
                $htmlWeek .= '</table>';
				$htmlWeek .= '</div>';
				$htmlWeek .= '</div>';
                $htmlWeek .= '</div>';
				
				$result2 = $Config->query($sql2);
                $htmlWeek .= '<div class="d-block  d-xs-block d-md-block d-lg-block d-xl-none"><div class="card">
								<div class="table-responsive">
									<table class=" table align-items-center mb-0" style="border-color: #136aeb;width: 100%;" id="scoresS">
										<thead class="">
											<th scope="col" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding-right: .25rem; padding-left: .25rem;">' . $lang['609']  . ' ' . $row["Fecha_Inicio"] . ' ' . $lang['610'] . ' ' . $row["Fecha_Fin"] . '</th>';
				$htmlWeek .= '</thead>';
				$htmlWeek .= '<tbody>';
				$count = 0;
				if ($result2->num_rows > 0) {
					// output data of each row
					while($row2 = $result2->fetch_assoc()) {
						if (($count % 2) == 1){
							$htmlWeek .= '<tr class="mainValues" id="' . $row2["juego"] . '">';
						}else{
							$htmlWeek .= '<tr class="alt mainValues" id="' . $row2["juego"] . '">';
						}
						$htmlWeek .= '
							<td scope="row">
								<input name="torneo' . $row2["juego"] . '" type="hidden" id="torneo' . $row2["juego"] . '" value="' . $row2["Torneo"] . '">
								<input name="jornada' . $row2["juego"] . '" type="hidden" id="jornada' . $row2["juego"] . '" value="' . $row2["Jornada"] . '">
								<input name="juego' . $row2["juego"] . '" type="hidden" id="juego' . $row2["juego"] . '" value="' . $row2["juego"] . '">
								<input name="local' . $row2["juego"] . '" type="hidden" id="local' . $row2["juego"] . '" value="' . $row2["Local_ID"] . '">
								<input name="visitante' . $row2["juego"] . '" type="hidden" id="visitante' . $row2["juego"] . '" value="' . $row2["Visitante_ID"] . '">
								<div class="d-flex px-0 py-1">
									<div class="align-self-center" style="width: 5%; text-align: left;padding-right: 3px; font-size:3vw;"></div>';
						if (strpos($row2["Comentarios"],$lang['654']) !== false){
								$htmlWeek .= '<div class="align-self-center" style="width: 60%; text-align: right;padding-right: 3px; font-size:3vw;">' . $row2["Comentarios"] . '</div>
											<div class="align-self-center" style="width: 150px; text-align: right;padding-right: 3px; font-size:3vw;"></div>';
						}else{
						    
						    if($row2["jugado1"] == 1){
						    	if($row2["PL"] > $row2["PV"]){
    						        $colorSL = 'green';
    						        $colorSV = 'red';
    						    }
    						    if($row2["PL"] < $row2["PV"]){
    						        $colorSV = 'green';
    						        $colorSL = 'red';
    						    }
    						    if($row2["PL"] == $row2["PV"]){
    						        $colorSV = 'inherit';
    						        $colorSL = 'inherit';
    						    }
    							
        						$htmlWeek .= '	<div class="align-self-center" style="width: 20%; text-align: center;padding-right: 3px; font-size:3vw;">' . $row2["Local"] . '</div>
    											<div class="justify-content-center d-flex px-0 py-1" style="width: 35%; text-align: right;padding-right: 3px; font-size:3vw;">
    												<div>' . $row2["Logol"] . '</div>

    												<div style="width: 30px;text-align: right;color: ' . $colorSL . ';" class="align-self-center">
    												    <p style="margin-top: -4px;color: ' . $colorSL . ';font-size: large;font-weight: 500;margin-bottom: 0px !important;">' . $row2["PL"] . '</p>
    												</div>
    												<div style="width: 10px;text-align: center;" class="align-self-center"><p style="margin-top: -4px;font-size: large;font-weight: 500;margin-bottom: 0px !important;">' . $row2["marcador"] . '</p></div>
    												<div style="width: 30px;text-align: left;color: ' . $colorSV . ';" class="align-self-center">
    												    <p style="margin-top: -4px;color: ' . $colorSV . ';font-size: large;font-weight: 500;margin-bottom: 0px !important;">' . $row2["PV"] . '</p>
    							                    </div>

    												<div>' . $row2["Logov"] . '</div>
    											</div>
    											<div style="width: 20%; text-align: center; padding-left: 3px; font-size:3vw;" class="align-self-center">' . $row2["Visitante"] .'</div>';
    				        }
						    if($row2["jugado1"] == 0){
						    	$colorSV = 'inherit';
						    	$colorSL = 'inherit';
    							
    							$htmlWeek .= '	<div class="align-self-center" style="width: 20%; text-align: center;padding-right: 3px; font-size:3vw;">' . $row2["Local"] . '</div>
    											<div class="justify-content-center d-flex px-0 py-1" style="width: 30%; text-align: right;padding-right: 3px; font-size:3vw;">
    												<div>' . $row2["Logol"] . '</div>
    												<div style="width: 30px;text-align: right;" class="align-self-center"></div>
    												<div style="width: 10px;text-align: center;" class="align-self-center"></div>
    												<div style="width: 30px;text-align: left;" class="align-self-center"></div>
    												<div>' . $row2["Logov"] . '</div>
    											</div>
    											<div style="width: 20%; text-align: center; padding-left: 3px; font-size:3vw;" class="align-self-center">' . $row2["Visitante"] .'</div>';
                				
    				        }
    				        $htmlWeek .= '<div style="width: 8%;text-align: right;padding-top: 6px;">
									<p style="margin-bottom: 0rem !important;"><img class="expandirButtonS" id="expandirS' . $row2["juego"] . 'SA" src="./imagenes/expandir.png" height="25" width="25" onClick="abrirFichaEditSBasketR(' . $row2["juego"] . ', ' . $row2["Jornada"] . ', ' . $row2["juego"] . ', \'' . $row2["Local"] . ' vs ' . $row2["Visitante"] . '\', \'' . $row2["Goles Local"] . '\', \'' . $row2["Goles Visitante"] . '\', \'' . $row2["Arbitro"] . '\', \'' . $row2["Comentarios"] . '\', 0, 0, \'' . $sqlcat . '\'); "></p>
									</div></div>';

						}
						if (strpos($row2["Comentarios"],$lang['654']) !== false){
							$htmlWeek .= "";
						}else{
							$htmlWeek .= '<div class="d-flex px-0 py-1">';
							$htmlWeek .= '<div style="width: 20%;text-align: center;padding-top: 6px;">
											<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['604'] . '</span></p>
											<div class="d-flex px-1 py-0 lh-1">
												<div style="width: 100%;text-align: center;">
													<div class="input-group input-group-sm input-group-outline my-0">
														<input type="date" style="width: 90px;text-align: center;padding-right: 0px !important;padding-left: 0px !important;" class="form-control form-control-sm" value="' . $row2["Fecha"] . '" name="fecha' . $row2["juego"] . '" id="fecha' . $row2["juego"] . '">
													</div>
												</div>
											</div>
										</div>';
							$htmlWeek .= '<div style="width: 20%;text-align: center;padding-top: 6px;" ' .  $Config->time . '>
											<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['605'] . '</span></p>
											<div class="d-flex px-1 py-0 lh-1"><div style="width: 100%;text-align: center;">
												<div class="input-group input-group-sm input-group-outline my-0">
													<input type="time" style="width: 90px;text-align: center;padding-right: 0px !important;padding-left: 0px !important;" class="form-control form-control-sm" value="' . $row2["horario"] . '" name="horario' . $row2["juego"] . '" id="horario' . $row2["juego"] . '">
												</div>
											</div>
										</div>';
							$htmlWeek .= '</div>';
						}
						if (strpos($row2["Comentarios"],$lang['654']) !== false){
							$htmlWeek .= '';
						}else{
							$htmlWeek .= '<div style="width: 23%;text-align: center;padding-top: 6px;">
											<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['606']. '</span></a></p>
											<p style="margin-bottom: 0rem !important;" class="lh-1">
												<div class="input-group input-group-static mb-0" style=" padding-left: 0.25rem; padding-right: 0.25rem;">
													<select class="form-control" style="width : 90px;padding-top: 0px;padding-bottom: 0px;" name="campo' . $row2["juego"] . '" id="campo' . $row2["juego"] . '">';
							$sql3 = "SELECT Campo_ID, Campo_DESC FROM $schema.Campos
										order by Campo_DESC asc;";
							$result3 = $Config->query($sql3);
							if ($result3->num_rows > 0) {
								// output data of each row
								while($row3 = $result3->fetch_assoc()) {
									if($row3["Campo_DESC"] == $row2["Campo"]){
										$htmlWeek .= "<option value='" . $row3["Campo_ID"] . "' selected>" . $row3["Campo_DESC"] . "</option>";
									}else{
										$htmlWeek .= "<option value='" . $row3["Campo_ID"] . "'>" . $row3["Campo_DESC"] . "</option>";
									}
								}
							}
							$htmlWeek .= '
								 </select>
							   </div>
							   </p>
							   </div>';
						}
						if (strpos($row2["Comentarios"],$lang['654']) !== false){
							$htmlWeek .= '';
						}else{
							$htmlWeek .= '<div style="width: 23%;text-align: center;padding-top: 6px;">
											<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['649'] . '</span></p>
											<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;">
												<div class="input-group input-group-static mb-0" style=" padding-left: 0.25rem; padding-right: 0.25rem;">
													<select class="form-control" style="width : 90px;padding-top: 0px;padding-bottom: 0px;" name="jugado' . $row2["juego"] . '" id="jugado' . $row2["juego"] . '">' . $row2["jugado"] . '</select>
												</div>
											</div>
										</div>';
						}

						$htmlWeek .= '</div>';
						$htmlWeek .= '';
						if (strpos($row2["Comentarios"],$lang['654']) !== false){
							$htmlWeek .= '<div style="width: 8%;text-align: right;padding-top: 6px;">
								<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold"></span></p>
								</div>';
						}else{
							//if($row2["jugadoStat"] == 1){
								
							//}else{
							//	$htmlWeek .= '<div style="width: 8%;text-align: right;padding-top: 6px;">
							//		<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold"></span></p>
							//		</div>';
							//}
						}
						$htmlWeek .= '</div></td>';
						$htmlWeek .= '</tr>';
						if (strpos($row2["Comentarios"],$lang['654']) == false){
							$htmlWeek .= '<tr id="editS' . $row2["juego"] . '" class="juegoS" style="display: none">
									<td  scope="row" colspan="14" style="width: 1183px; padding-left: 0px; padding-right: 0px;">
										<div class="contentEditFichaS" width="100%" id="contentS' . $row2["juego"] . '" height="400"></div>
									</td>
								  </tr>';
						}
						$count++;
					}
				}
				$htmlWeek .= '	<tr>
									<td style="border-bottom: 0;">
										<button type="button" class="btn btn-primary" onclick="saveChangesSR(' . $Season . ',' . $Week . ');">' . $lang['0000'] . '</button>
									</td>
								</tr>';
				$sqlcat = "and a.Fuerza = $Category";
				if($vs == 1){
					$sqlcat = "";
				}
			    $sqloneperweek = "";				
			    $sqloneperweekcond = "";				
				if($Config->unjuegosemanal == 1){
    			    $sqloneperweek = "left outer join (
    									select j.Local_ID as Equipo_ID
    									from  $schema.Juegos as j 
    										join $schema.Jornada as jo on j.Fecha between jo.Fecha_Inicio and jo.Fecha_Fin
    									where jo.Jornada_ID = $Week
    									UNION
    									select j.Visitante_ID as Equipo_ID
    									from  $schema.Juegos as j 
    										join $schema.Jornada as jo on j.Fecha between jo.Fecha_Inicio and jo.Fecha_Fin
    									where jo.Jornada_ID = $Week) b on a.Equipo_ID = b.Equipo_ID";				
    				$sqloneperweekcond = "and b.Equipo_ID is null";
				}
				$sql33 = "SELECT a.Equipo_ID, 
								Equipo_DESC 
						 FROM $schema.Equipos a
						$sqloneperweek
						 where Torneo_ID = $Season 
							and Equipo_Desc <> 'NA' 
							and Activo = 1 $sqlcat
							$sqloneperweekcond
						 order by 2 asc;";
				$result3 = $Config->query($sql33);
				
				
                $htmlWeek .= '</tbody>';
                $htmlWeek .= '</table>';
				$htmlWeek .= '</div>';
				$htmlWeek .= '</div>';
                $htmlWeek .= '</div>';
                ?>