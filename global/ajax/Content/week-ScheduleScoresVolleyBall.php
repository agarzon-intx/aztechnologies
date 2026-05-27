<?php
			if (!function_exists('az_flyer_game_download_menu_html')) {
				require_once dirname(__DIR__, 2) . '/include/flyer_download_menu.php';
			}
			//echo '123456';
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
										jugado, 
										case when j.Visitante_Id is null then '' else concat(l.equipo_desc,'') end  as 'Local',
										case when j.Visitante_Id is null then '' else concat(l.equipo_desc3,'') end  as 'Local3',
										case when j.Visitante_ID is null then null when jugado = 0 then '' else CONCAT(Gol_Local,'') end as 'Goles Local', 
										case when j.Visitante_ID is null then null else Penal_local end as 'Penalties Local', 
										concat(case when j.Visitante_ID is null then null else v.equipo_desc end,'') as 'Visitante', 
										concat(case when j.Visitante_ID is null then null else v.equipo_desc3 end,'') as 'Visitante3', 
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
										Horario as hora,
										TIME_FORMAT(Horario, '%H:%i%p') horario,
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
									select 1 as VisitanteS, j.Torneo_ID as Torneo, jo.Jornada_ID as Jornada, j.Juego_ID as juego, jugado, 
									    case when j.Visitante_Id is null then '' else concat(l.equipo_desc,'') end  as 'Local', 
									    case when j.Visitante_Id is null then '' else concat(l.equipo_desc3,'') end  as 'Local3', 
										case when j.Visitante_ID is null then null when jugado = 0 then '' else CONCAT(Gol_Local,'') end as 'Goles Local', 
										case when j.Visitante_ID is null then null else Penal_local end as 'Penalties Local', 
										concat(case when j.Visitante_ID is null then null else v.equipo_desc end,'') as 'Visitante', 
										concat(case when j.Visitante_ID is null then null else v.equipo_desc3 end,'') as 'Visitante3', 
										case when j.Visitante_ID is null then null when jugado = 0 then '' else CONCAT(Gol_Visitante,'') end as 'Goles Visitante', 
										case when j.Visitante_ID is null then null else Penal_Visitante end as 'Penalties Visitante', 
										case when j.Visitante_ID is null then null else Arbitro end as Arbitro, 
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
										Horario as hora,
										TIME_FORMAT(Horario, '%H:%i%p') horario,
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
						order by Fecha asc,hora asc, VisitanteS, Torneo, Jornada, Juego;";
				//echo $sql2;
				$result2 = $Config->query($sql2);
				$htmlWeek .= '<div class="d-none  d-xs-none d-md-none d-lg-none d-xl-block"><div class="card">
								<div class="table-responsive">
									<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
										<thead class="">
											<th scope="col" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" width="50%">' . $lang['609']  . ' ' . $row["Fecha_Inicio"] . ' ' . $lang['610'] . ' ' . $row["Fecha_Fin"] . '</th>';
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
						$htmlWeek .= '
							<td scope="row">
							    <div class="container">
							        <div class="row">
							            <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4" style="padding-top: 0px;padding-left: 0;padding-right: 0;padding-bottom: 0;">
            							    <div class="container" style="height: 100%;">
            							        <div class="row" style="height: 100%;">
							                        <div class="col-12 col-sm-12 col-md-6 col-lg-7 col-xl-9 col-xxl-10" style="padding-top: 6px;padding-left: 0;padding-right: 0;padding-bottom: 0;text-wrap: pretty;align-items: center;display: flex;justify-content: right;">
							                            <div style="text-align: right;overflow-wrap: break-word;padding-right: 2px;">' . $row2["Local"] . '</div>
							                        </div>
							                        <div class="col-12 col-sm-12 col-md-6 col-lg-7 col-xl-3 col-xxl-2" style="padding-top: 6px;padding-left: 0;padding-right: 0;padding-bottom: 0;justify-content: center;align-items: center;display: flex;">' . $row2["Logol"] . '</div>
							                    </div>
							                </div>
							            </div>';
						$sql20 = "   SELECT * FROM $schema.Juego_Estatus
                                    where Juego_Estatus_ID = " . $row2["jugado"] . ";";
        				//echo $sql20;
        				$result20 = $Config->query($sql20);
        				if ($result20->num_rows > 0) {
        					// output data of each row
        					while($row20 = $result20->fetch_assoc()) {
    							$statusText = $lang[$row20["Juego_Estatus_DESC_ID"]];
        					}
        				}
						if($row2["jugado"] > 1 ){
						    if($row2["GL"] > $row2["GV"]){
						        $colorSL = 'red';
						        $colorSV = 'inherit';
						    }
						    if($row2["GL"] < $row2["GV"]){
						        $colorSV = 'red';
						        $colorSL = 'inherit';
						    }
						    if($row2["GL"] == $row2["GV"]){
						        $colorSV = 'inherit';
						        $colorSL = 'inherit';
						    }
							$htmlWeek .= '
							<div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4" style="padding-top: 0px;padding-left: 0;padding-right: 0;padding-bottom: 0;">
							    <div class="container" style="height: 100%;">
							        <div class="row" style="height: 100%;justify-content: center;align-items: center;display: flex;">' . $statusText . '</div>
            					</div>
							</div>';
						}
						if($row2["jugado"] == 1 ){
						    if($row2["GL"] > $row2["GV"]){
						        $colorSL = 'red';
						        $colorSV = 'inherit';
						    }
						    if($row2["GL"] < $row2["GV"]){
						        $colorSV = 'red';
						        $colorSL = 'inherit';
						    }
						    if($row2["GL"] == $row2["GV"]){
						        $colorSV = 'inherit';
						        $colorSL = 'inherit';
						    }
							$htmlWeek .= '
							<div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4" style="padding-top: 0px;padding-left: 0;padding-right: 0;padding-bottom: 0;">
							    <div class="container" style="height: 100%;">
							        <div class="row" style="height: 100%;justify-content: center;align-items: center;display: flex;">
            							<div class="col-12 col-sm-12 col-md-6 col-lg-7 col-xl-3 col-xxl-3" style="justify-content: right;padding: 0;align-items: center;display: flex;"><span style="margin-top: 0px;color: ' . $colorSL . ';font-size: xx-large;font-weight: 500;">' . $row2["GL"] . '</span></div>
            							<div class="col-12 col-sm-12 col-md-6 col-lg-7 col-xl-2 col-xxl-2" style="justify-content: right;color: ' . $colorSL . ';padding: 0;align-items: center;display: flex;">' . $row2["PL"] . '</div>
            							<div class="col-12 col-sm-12 col-md-6 col-lg-7 col-xl-1 col-xxl-1" style="justify-content: center;padding: 0;align-items: center;display: flex;">' . $row2["marcador"] . '</div>
            							<div class="col-12 col-sm-12 col-md-6 col-lg-7 col-xl-2 col-xxl-2" style="justify-content: left;color: ' . $colorSV . ';padding: 0;align-items: center;display: flex;">' . $row2["PV"] . '</div>
            							<div class="col-12 col-sm-12 col-md-6 col-lg-7 col-xl-3 col-xxl-3" style="justify-content: left;padding: 0;align-items: center;display: flex;"><span style="margin-top: 0px;color: ' . $colorSV . ';font-size: xx-large;font-weight: 500;">' . $row2["GV"] . '</span></div>
            						</div>
            					</div>
							</div>';
						}
						if($row2["jugado"] == 0){
						    if($row2["GL"] > $row2["GV"]){
						        $colorSL = 'red';
						        $colorSV = 'inherit';
						    }
						    if($row2["GL"] < $row2["GV"]){
						        $colorSV = 'red';
						        $colorSL = 'inherit';
						    }
						    if($row2["GL"] == $row2["GV"]){
						        $colorSV = 'inherit';
						        $colorSL = 'inherit';
						    }
							$htmlWeek .= '
							<div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4" style="padding-top: 0px;padding-left: 0;padding-right: 0;padding-bottom: 0;">
							    <div class="container" style="height: 100%;">
							        <div class="row" style="height: 100%;justify-content: center;align-items: center;display: flex;">
            							<div class="col-12 col-sm-12 col-md-6 col-lg-7 col-xl-3 col-xxl-3" style="justify-content: right;padding: 0;align-items: center;display: flex;"></div>
            							<div class="col-12 col-sm-12 col-md-6 col-lg-7 col-xl-2 col-xxl-2" style="justify-content: right;color: ' . $colorSL . ';padding: 0;align-items: center;display: flex;"></div>
            							<div class="col-12 col-sm-12 col-md-6 col-lg-7 col-xl-1 col-xxl-1" style="justify-content: center;padding: 0;align-items: center;display: flex;"></div>
            							<div class="col-12 col-sm-12 col-md-6 col-lg-7 col-xl-2 col-xxl-2" style="justify-content: left;color: ' . $colorSV . ';padding: 0;align-items: center;display: flex;"></div>
            							<div class="col-12 col-sm-12 col-md-6 col-lg-7 col-xl-3 col-xxl-3" style="justify-content: left;padding: 0;align-items: center;display: flex;"></div>
									</div>
            					</div>
							</div>';
						}
						$htmlWeek .= '<div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4" style="padding-top: 0px;padding-left: 0;padding-right: 0;padding-bottom: 0;">
							            <div class="container" style="height: 100%;">
            							    <div class="row" style="height: 100%;">
							                    <div class="col-12 col-sm-12 col-md-6 col-lg-7 col-xl-3 col-xxl-2" style="padding-top: 6px;padding-left: 0;padding-right: 0;padding-bottom: 0;justify-content: center;align-items: center;display: flex;">
							                        <div style="text-left: left;">' . $row2["Logov"] . ' </div>
							                    </div>
							                    <div class="col-12 col-sm-12 col-md-6 col-lg-7 col-xl-9 col-xxl-10" style="padding-top: 6px;padding-left: 0;padding-right: 0;padding-bottom: 0;text-wrap: pretty;align-items: center;display: flex;justify-content: left;">
							                        <div style="text-align: left;overflow-wrap: break-word;padding-left: 2px;">' . $row2["Visitante"] .'</div>
							                    </div>
							                </div>
							            </div>
							        </div></div></div></td>
							<td  scope="row" class="align-middle text-center">';
							if (strpos($row2["Comentarios"],$lang['654']) !== false){
								$htmlWeek .= "";
							}else{
								$htmlWeek .= '<span class="text-secondary text-xs font-weight-normal">' . $row2["Fecha"]. ' / ' . $row2["horario"]. '</span>';
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
								if($row2["jugado"] == 1){
									$htmlWeek .= '<img class="expandirButton" id="expandir' . $row2["juego"] . '" src="./imagenes/expandir.png" height="25" width="25" onClick="abrirFichaVoleibol(' . $row2["juego"] . ', ' . $row2["Jornada"] . ', ' . $row2["juego"] . ', \'' . $row2["Local"] . ' vs ' . $row2["Visitante"] . '\', \'' . $row2["Goles Local"] . '\', \'' . $row2["Goles Visitante"] . '\'); ">';
								}
							}
							
    						if (strpos($row2["Comentarios"],$lang['654']) !== false){
    							$htmlWeek .= "";
    						}else{
    							$htmlWeek .= az_flyer_game_download_menu_html($row2["juego"], $Config->getPath()) . '</td>';
    							
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
				
				$htmlWeek .= '</tbody>';
				$htmlWeek .= '</table>';
				$htmlWeek .= '</div>';
				$htmlWeek .= '</div>';
				$htmlWeek .= '</div>';
				
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
							<td scope="row"><div class="justify-content-center d-flex px-0 py-1"><div class="align-self-center" style="width: 25%; text-align: right;padding-right: 3px; font-size:3vw;">' . $row2["Local"] . '</div>
							<div class="align-self-center">' . $row2["Logol"] . '</div>';
							
							
							
						if($row2["jugado"] == 2){
							$htmlWeek .= '<div style="width: 80px;text-align: center;" class="align-self-center"><span style="font-size: small;">' . $lang['663'] . '</span></div>';
						}
						if($row2["jugado"] == 1){
						    if($row2["GL"] > $row2["GV"]){
						        $colorSL = 'red';
						        $colorSV = 'inherit';
						    }
						    if($row2["GL"] < $row2["GV"]){
						        $colorSV = 'red';
						        $colorSL = 'inherit';
						    }
						    if($row2["GL"] == $row2["GV"]){
						        $colorSV = 'inherit';
						        $colorSL = 'inherit';
						    }
							$htmlWeek .= '
							<div style="width: 17px;text-align: center;" class="align-self-center"><p style="color: ' . $colorSL . ';font-size: xx-large;font-weight: 500; margin-bottom: 0px !important;">' . $row2["GL"] . '</p></div>
							<div style="width: 20px;text-align: right;color: ' . $colorSL . ';" class="align-self-center">' . $row2["PL"] . '</div>
							<div style="width: 10px;text-align: center;" class="align-self-center">' . $row2["marcador"] . '</div>
							<div style="width: 20px;text-align: left;color: ' . $colorSV . ';" class="align-self-center">' . $row2["PV"] . '</div>
							<div style="width: 17px;text-align: center;" class="align-self-center"><p style="color: ' . $colorSV . ';font-size: xx-large;font-weight: 500; margin-bottom: 0px !important;">' . $row2["GV"] . '</p></div>';
						}
						if($row2["jugado"] == 0){

						    $colorSV = 'inherit';
                            $colorSL = 'inherit';

							$htmlWeek .= '
							<div style="width: 17px;text-align: center;" class="align-self-center"></div>
							<div style="width: 20px;text-align: right;color: ' . $colorSL . ';" class="align-self-center"></div>
							<div style="width: 10px;text-align: center;" class="align-self-center"></div>
							<div style="width: 20px;text-align: left;color: ' . $colorSV . ';" class="align-self-center"></div>
							<div style="width: 17px;text-align: center;" class="align-self-center"></div>';
						}
						
						
						$htmlWeek .= '<div class="align-self-center">' . $row2["Logov"] . '</div>
							<div style="width: 25%; text-align: left; padding-left: 3px; font-size:3vw;" class="align-self-center">' . $row2["Visitante"] .'</div></div><div class="d-flex px-0 py-1">';
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
								$htmlWeek .= '<div style="width: 30%;text-align: center;padding-top: 6px;">';
								$htmlWeek .= '</div>';
							}
						}
						$htmlWeek .= '';
							if (strpos($row2["Comentarios"],$lang['654']) !== false){
								$htmlWeek .= '';
							}else{
								if($row2["jugado"] == 1){
									$htmlWeek .= '<div style="width: 10%;text-align: right;padding-top: 6px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['659'] . '</span></p>
									<img class="expandirButton" id="expandir' . $row2["juego"] . 'S" src="./imagenes/expandir.png" height="25" width="25" onClick="abrirFichaSVoleibol(' . $row2["juego"] . ', ' . $row2["Jornada"] . ', ' . $row2["juego"] . ', \'' . $row2["Local"] . ' vs ' . $row2["Visitante"] . '\', \'' . $row2["Goles Local"] . '\', \'' . $row2["Goles Visitante"] . '\'); "></div>';
								}
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
				
				$htmlWeek .= '</tbody>';
				$htmlWeek .= '</table>';
				$htmlWeek .= '</div>';
				$htmlWeek .= '</div>';
				
				
				$htmlWeek .= '</div>';
				?>