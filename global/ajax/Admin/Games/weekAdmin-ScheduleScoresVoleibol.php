	            <?php
			$fecha = new DateTime();
            $sqlcat = "and l.Fuerza = $Category";
			if($vs == 1){
				$sqlcat = "";
			}
			$sqlJuegosXNombre = "";
            if($Type == 1){
                $sqlJuegosXNombre = " and (Local_Id in (SELECT Equipo_ID FROM $schema.Equipos where Equipo_FULLDESC like '$teamdesc' and Torneo_ID = $Season) or Visitante_ID in (SELECT Equipo_ID FROM $schema.Equipos where Equipo_FULLDESC like '$teamdesc' and Torneo_ID = $Season))";
                $sqlcat = "";
            }
            
	        //$htmlWeek .= $sqlJuegosXNombre;
			$game_status_opt = "case ";
			$sql20 = "  SELECT * 
                        FROM (
                        		SELECT * FROM $schema.Juego_Estatus
                        		UNION
                                SELECT 0,'660','660',0,0,0,0,0,0,0,0,0,0
                        		UNION
                                SELECT 1,'661','661',0,0,0,0,0,0,0,0,0,0) a
                        order by Juego_Estatus_ID;";
			$result20 = $Config->query($sql20);
            if ($result20->num_rows > 0) {
				// output data of each row
				while($row20 = $result20->fetch_assoc()) {
					$game_status_opt .= "when jugado = " . $row20["Juego_Estatus_ID"] . " then concat(";
					$sql21 = "  SELECT * 
                                FROM (
                                		SELECT * FROM $schema.Juego_Estatus
                                		UNION
                                        SELECT 0,'660','660',0,0,0,0,0,0,0,0,0,0
                                		UNION
                                        SELECT 1,'661','661',0,0,0,0,0,0,0,0,0,0) a
                                order by Juego_Estatus_ID;";
        			$result21 = $Config->query($sql21);
                    if ($result21->num_rows > 0) {
        				// output data of each row
        				while($row21 = $result21->fetch_assoc()) {
        				    $selectesStr = '';
        				    if($row20["Juego_Estatus_ID"] === $row21["Juego_Estatus_ID"]){
        				        $selectesStr = "selected";
        				    }
        					$game_status_opt .= "'<option value=''" . $row21["Juego_Estatus_ID"] . "'' " . $selectesStr . ">" . $lang[$row21["Juego_Estatus_DESC_ID"]] . "</option>'";
        				}
                    }
                    $game_status_opt .= ") ";
				}
            }
            $game_status_opt .= "end";
            
            $sql2 = "select * from (
								select 0 as VisitanteS, 
										j.Torneo_ID as Torneo, 
										jo.Jornada_ID as Jornada,  
            							jor.Jornada_DescCorta,
            							cat.Categoria_DESC,
            							cat.Categoria_Orden,
										j.Juego_ID as juego, 
										Extra_Local,
										Extra_Visitante, 
										jugado as jugadoStat,
										$game_status_opt as jugadoopt,
										jugado jugado1, 
										case 
											when jugado = 0 then 
												concat('<option value=''0'' selected>" . $lang['660'] . "</option><option value=''1''>" . $lang['661'] . "</option><option value=''2''>" . $lang['662'] . "</option>')
											when jugado = 1 then 
												concat('<option value=''0''>" . $lang['660'] . "</option><option value=''1'' selected>" . $lang['661'] . "</option><option value=''2''>" . $lang['662'] . "</option>')
											when jugado = 2 then 
												concat('<option value=''0''>" . $lang['660'] . "</option><option value=''1''>" . $lang['661'] . "</option><option value=''2'' selected>" . $lang['662'] . "</option>')
											when jugado = 10 then 
												concat('<option value=''10'' selected>" . $lang['675'] . "</option>')
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
										Horario as hora,
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
            							join $schema.Categorias as cat on l.Fuerza = cat.Categoria_ID and cat.Torneo_ID = $Season
										left outer join $schema.Campos lc on l.Campo_ID = lc.Campo_ID
										left outer join $schema.Equipos as v on j.Visitante_ID = v.Equipo_ID and v.Torneo_ID = $Season
										join $schema.Jornada as jo on j.Fecha between jo.Fecha_Inicio and jo.Fecha_Fin and jo.Torneo_ID = $Season
            							join $schema.Jornada as jor on j.Jornada_ID = jor.Jornada_ID
										left outer join $schema.Juegos_Set s on j.Juego_ID = s.Juego_ID
									where jo.Jornada_ID = $Week and Visitante_ID is not null $sqlcat $sqlJuegosXNombre
									UNION
									select 1 as VisitanteS, 
									    j.Torneo_ID as Torneo, 
									    jo.Jornada_ID as Jornada, 
            							jor.Jornada_DescCorta,
            							cat.Categoria_DESC,
            							cat.Categoria_Orden,
										j.Juego_ID as juego, 
										Extra_Local,
										Extra_Visitante, 
										jugado as jugadoStat,
										$game_status_opt as jugadoopt,
										jugado jugado1, 
									    case 
											when jugado = 0 then 
												concat('<option value=''0'' selected>" . $lang['660'] . "</option><option value=''1''>" . $lang['661'] . "</option><option value=''2''>" . $lang['662'] . "</option>')
											when jugado = 1 then 
												concat('<option value=''0''>" . $lang['660'] . "</option><option value=''1'' selected>" . $lang['661'] . "</option><option value=''2''>" . $lang['662'] . "</option>')
											when jugado = 2 then 
												concat('<option value=''0''>" . $lang['660'] . "</option><option value=''1''>" . $lang['661'] . "</option><option value=''2'' selected>" . $lang['662'] . "</option>')
											when jugado = 10 then 
												concat('<option value=''10'' selected>" . $lang['675'] . "</option>')
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
										Horario as hora,
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
            							join $schema.Categorias as cat on l.Fuerza = cat.Categoria_ID and cat.Torneo_ID = $Season
										left outer join $schema.Campos lc on l.Campo_ID = lc.Campo_ID
										left outer join $schema.Equipos as v on j.Visitante_ID = v.Equipo_ID and v.Torneo_ID = $Season
										join $schema.Jornada as jo on j.Fecha between jo.Fecha_Inicio and jo.Fecha_Fin and jo.Torneo_ID = $Season
            							join $schema.Jornada as jor on j.Jornada_ID = jor.Jornada_ID
										left outer join $schema.Juegos_Set s on j.Juego_ID = s.Juego_ID
									where jo.Jornada_ID = $Week and Visitante_ID is null $sqlcat $sqlJuegosXNombre) a
						order by Fecha asc, hora asc, VisitanteS, Torneo, Jornada, Juego;";
				//$htmlWeek .= $sql2;
				$result2 = $Config->query($sql2);
                $htmlWeek .= '<div class="d-none  d-xs-none d-md-none d-lg-none d-xl-block d-xxl-block"><div class="card">
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
							<td scope="row"><img src="imagenes/eliminar.png" height="18" width="18" onClick="borrarJuego(' . $row2["juego"] . ', ' . $Week . ')">';
						if($Type == 1){
						    $htmlWeek .= '[' . $row2["Jornada_DescCorta"] . ' - ' .  $row2["Categoria_DESC"] . '] ';
						}
						$htmlWeek .= '</td>';
						$colorSV = 'inherit';
						$colorSL = 'inherit';
						$GL = '-';
						$PL = '-';
						$PV = '-';
						$GV = '-';
						if($row2["jugado1"] == 1){
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
						}
						if($row2["jugado1"] == 0){
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
						}
						if($row2["jugado1"] > 1){
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
						}
						if (strpos($row2["Comentarios"],$lang['654']) !== false){
								$htmlWeek .= '<td scope="row"><div class="d-flex px-2 py-1">
								<div style="width: 200px;text-align: center;padding-top: 6px;">' . $row2["Comentarios"] . '</div></div></td>';
						}else{
							$htmlWeek .= '<td scope="row"><div class="d-flex px-2 py-1"><div style="text-wrap: balance; width: 240px;text-align: right;padding-right: 3px;padding-top: 6px;">' . $row2["Local"] . '</div>
								<div>' . $row2["Logol"] . '</div>
								<div style="width: 20px;text-align: center">';
							if (strpos($row2["Comentarios"],$lang['654']) !== false){
									$htmlWeek .= "";
							}else{
								$htmlWeek .= '<div style="justify-content: right;align-items: center;display: flex;margin-top: 0px;"><span style="color: ' . $colorSL . ';font-size: xx-large;font-weight: 500;max-height: 36px;display: flex;align-items: center;">' . $row2["GL"] . '</span></div>';
							}
							$htmlWeek .= '</div>
								<div style="width: 20px;text-align: right;">';
							if (strpos($row2["Comentarios"],$lang['654']) !== false){
								$htmlWeek .= "";
							}else{
								$htmlWeek .= '<div style="justify-content: right;color: ' . $colorSL . ';height: 36px;display: flex;align-items: center;">' . $row2["PL"] . '</div>';
							}
							$htmlWeek .= '</div>
								<div style="width: 10px;text-align: center;">' . $row2["marcador"] . '</div>
								<div style="width: 20px;text-align: left;">';
							if (strpos($row2["Comentarios"],$lang['654']) !== false){
								$htmlWeek .= "";
							}else{
								$htmlWeek .= '<div style="justify-content: left;color: ' . $colorSV . ';height: 36px;display: flex;align-items: center;">' . $row2["PV"] . '</div>';
							}
							$htmlWeek .= '</div>
								<div style="width: 20px;text-align: center;">';
							if (strpos($row2["Comentarios"],$lang['654']) !== false){
								$htmlWeek .= "";
							}else{
								$htmlWeek .= '<div style="justify-content: left;align-items: center;display: flex;margin-top: 0px;"><span style="color: ' . $colorSV . ';font-size: xx-large;font-weight: 500;max-height: 36px;display: flex;align-items: center;">' . $row2["GV"] . '</span></div>';
							}
							$htmlWeek .= '</div>
								<div>' . $row2["Logov"] . '</div>
								<div style="text-wrap: balance; width: 240px;text-left: right;padding-left: 3px;">' . $row2["Visitante"] .'</div></div></td>';
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
								$htmlWeek .= '<td  scope="row" class="align-middle text-center">' . '<img class="expandirButton" id="expandir' . $row2["juego"] . '" src="./imagenes/expandir.png" height="25" width="25" onClick="abrirFichaEditVoleibol(' . $row2["juego"] . ', ' . $row2["Jornada"] . ', ' . $row2["juego"] . ', \'' . $row2["Local"] . ' vs ' . $row2["Visitante"] . '\', \'' . $row2["Goles Local"] . '\', \'' . $row2["Goles Visitante"] . '\', \'' . $row2["Arbitro"] . '\', \'' . $row2["Comentarios"] . '\', ' . $row2["Extra_Local"] . ', ' . $row2["Extra_Visitante"] . ', \'\'); "></td>';
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
										<button type="button" class="btn btn-primary" onclick="saveChanges(' . $Season . ',' . $Week . ');">' . $lang['0000'] . '</button>
									</td>
									<td style="border-bottom: 0;text-align: end;" colspan="5">
										<a href="pdf/flyerSC.php?Jornada_ID=' . $Week . '&Categoria_ID=' . $Category . '" target="_blank" download class="btn btn-primary" role="button" aria-pressed="true">Flyer Categoria</a>
										<a href="pdf/reportePendientes.php?Torneo_ID=' . $Season . '&Jornada_ID=' . $Week . '&Categoria_ID=' . $Category . '" target="_blank" download class="btn btn-primary" role="button" aria-pressed="true">Reporte Pendientes</a>
										<a href="pdf/reporteArbitros.php?Torneo_ID=' . $Season . '&Jornada_ID=' . $Week . '&Categoria_ID=' . $Category . '" target="_blank" download class="btn btn-primary" role="button" aria-pressed="true">Reporte Partidos</a>
										<a href="pdf/cedulas.php?Torneo_ID=' . $Season . '&Jornada_ID=' . $Week . '&Categoria_ID=' . $Category . '" target="_blank" download class="btn btn-primary" role="button" aria-pressed="true">' . $lang['647'] . '</a>
									</td>
								</tr>';
				
					
				$sqlcata = "and a.Fuerza = $Category";
				if($vs == 1){
					$sqlcata = "";
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
				$sql33 = "	SELECT a.Equipo_ID, 
								Equipo_DESC 
							FROM $schema.Equipos a
							$sqloneperweek
							where Torneo_ID = $Season 
								and Equipo_Desc <> 'NA' 
								and Activo = 1 $sqlcata
								$sqloneperweekcond
							order by 2 asc;";
						 
				$sql34 = "	SELECT c.Categoria_ID, c.Categoria_DESC, e.Equipo_ID, 
								e.Equipo_DESC 
							FROM $schema.Equipos e
								join $schema.Categorias c on e.Fuerza = c.Categoria_ID and c.Torneo_ID = $Season
							where e.Torneo_ID = $Season 
							and e.Equipo_Desc <> 'NA' 
							and e.Activo = 1
							order by c.Categoria_DESC, e.Equipo_DESC asc;";
				$sql35 = "SELECT Categoria_ID, Categoria_DESC FROM $schema.Categorias WHERE Torneo_ID = $Season ORDER BY Categoria_Orden, Categoria_DESC";
				$filtroCatDefaultAllA = ($vs == 1 || $Type == 1);
				$optsCatFilterHtml = '<option value="0"' . ($filtroCatDefaultAllA ? ' selected' : '') . '>' . $lang['762'] . '</option>';
				$resultCatA = $Config->query($sql35);
				if ($resultCatA && $resultCatA->num_rows > 0) {
					while ($rowCatA = $resultCatA->fetch_assoc()) {
						$selCatA = (!$filtroCatDefaultAllA && (int)$rowCatA['Categoria_ID'] === (int)$Category) ? ' selected' : '';
						$optsCatFilterHtml .= "<option value='" . $rowCatA["Categoria_ID"] . "'" . $selCatA . ">" . $rowCatA["Categoria_DESC"] . "</option>";
					}
				}
				
					$htmlWeek .= '<tr>
									<td scope="row" colspan="7">
										<div class="d-flex px-2 py-1">
											<div style="width: 100%;text-align: left;padding-right: 3px;padding-top: 6px;">
												<div style="float: left;padding-top: 6px;padding-left: 10px;">
													<div class="form-check input-group input-group-outline">
														<label class="custom-control-label">' . $lang['675'] . '</label>
														<input type="checkbox" id="amistoso" name="amistoso" value="0" class="form-check-input" style="border-radius: 0.35rem" onClick="$(\'#amistosos\').toggle();$(\'#normal\').toggle();"> 
													</div>
												</div>	
												<div id="normal">
													<div style="float: left;padding-top: 6px;padding-left: 10px;">
														<div style="float: left;width: 67px;">' . $lang['652'] . '</div>
														<div style="float: right;padding-left: 10px;">
															<select name="localAgregar" id="localAgregar" onChange="loadVisitanteAgregar($(\'#localAgregar  option:selected\').val())">';
				$result3 = $Config->query($sql33);
				if ($result3->num_rows > 0) {// output data of each row
					while($row3 = $result3->fetch_assoc()) {
							$htmlWeek .= "<option value='" . $row3["Equipo_ID"] . "'>" . $row3["Equipo_DESC"] . "</option>";
					}
				}
						$htmlWeek .= '						</select>
														</div>
													</div>
													<div style="float: left;padding-top: 6px;padding-left: 10px;">
														<div style="float: left;width: 67px;">' . $lang['653'] . '</div>
														<div style="float: right;padding-left: 10px;">
															<select name="visitanteAgregar" id="visitanteAgregar">
																<option value="NULL">' . $lang['654'] . '</option>
															</select>
														</div>
													</div>
													<div style="float: left;padding-left: 10px;">
														<button type="button" class="btn btn-primary" onClick="agregarJuego(\'' . $row["Fecha"] . '\', ' . $Season . ', ' . $Week . ', $(\'#localAgregar\').val(), $(\'#visitanteAgregar\').val());" >' . $lang['664'] . '</button>
													</div>
												</div>
												<div id="amistosos" style="display: none;">
													<select id="localAgregarA_source" style="display:none">';
				$result3 = $Config->query($sql34);
				$optsAgregarA = '';
				if ($result3->num_rows > 0) {
					while ($row3 = $result3->fetch_assoc()) {
						$optsAgregarA .= "<option value='" . $row3["Equipo_ID"] . "' data-categoria='" . $row3["Categoria_ID"] . "'>" . $row3["Categoria_DESC"] . "-" . $row3["Equipo_DESC"] . "</option>";
					}
				}
				$htmlWeek .= $optsAgregarA . '</select>
													<div style="float: left;padding-top: 6px;padding-left: 10px;clear: both;">
														<div style="float: left;width: 67px;">' . $lang['652'] . '</div>
														<div style="float: left;padding-left: 10px;">
															<div style="font-size: 11px;margin-bottom: 2px;">' . $lang['953'] . '</div>
															<select id="filtroCategoriaLocalAgregarA" onChange="aplicarFiltroLocalAgregarA();">' . $optsCatFilterHtml . '</select>
															<div style="margin-top: 6px;">
																<select name="localAgregarA" id="localAgregarA" onChange="loadVisitanteAgregarA()"></select>
															</div>
														</div>
													</div>
													<div style="float: left;padding-top: 6px;padding-left: 10px;">
														<div style="float: left;width: 67px;">' . $lang['653'] . '</div>
														<div style="float: left;padding-left: 10px;">
															<div style="font-size: 11px;margin-bottom: 2px;">' . $lang['953'] . '</div>
															<select id="filtroCategoriaVisitanteAgregarA" onChange="aplicarFiltroVisitanteAgregarA();">' . $optsCatFilterHtml . '</select>
															<div style="margin-top: 6px;">
																<select name="visitanteAgregarA" id="visitanteAgregarA">
																	<option value="NULL">' . $lang['654'] . '</option>
																</select>
															</div>
														</div>
													</div>
													<div style="float: left;padding-left: 10px;">
														<button type="button" class="btn btn-primary" onClick="agregarJuegoA(\'' . $row["Fecha"] . '\', ' . $Season . ', ' . $Week . ', $(\'#localAgregarA\').val(), $(\'#visitanteAgregarA\').val());" >' . $lang['664'] . '</button>
													</div>
												</div>
											</div>
										</div>
									</td>
								</tr>
								<script>loadVisitanteAgregar($(\'#localAgregar  option:selected\').val());</script>
								<script>inicializarAgregarA();</script>';

					
						
                $htmlWeek .= '</tbody>';
                $htmlWeek .= '</table>';
				$htmlWeek .= '</div>';
				$htmlWeek .= '</div>';
                $htmlWeek .= '</div>';
				
				$result2 = $Config->query($sql2);
                $htmlWeek .= '<div class="d-block d-xs-block d-md-block d-lg-block d-xl-none d-xxl-none"><div class="card">
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
								<div class="container" style="padding-left: 0px; padding-right: 0px; margin-left: 0px; margin-right: 0px; width: 100%; max-width: 100%;">
							        <div class="row">';
						if($Type == 1){
						    $htmlWeek .= '  <div class="col-12 col-sm-12 col-md-12 col-lg-12" style="padding-top: 0px;padding-left: 10;padding-right: 0;padding-bottom: 0;font-size: 11px;height: 2px;">
					                            [' . $row2["Jornada_DescCorta"] . ' - ' .  $row2["Categoria_DESC"] . '] 
					                        </div>';
						}
						$htmlWeek .= '  <div class="col-12 col-sm-12 col-md-12 col-lg-12" style="padding-top: 0px;padding-left: 0;padding-right: 0;padding-bottom: 0;">
							                <div class="container" style="padding-left: 0px; padding-right: 0px; margin-left: 0px; margin-right: 0px; width: 100%; max-width: 100%;">
            							        <div class="row">
            							            <div class="col-1 col-sm-1 col-md-1 col-lg-1" style="padding-top: 0px;padding-left: 0;padding-right: 0;padding-bottom: 0;">
                        							    <div class="container" style="height: 100%;">
                        							        <div class="row" style="height: 100%;">
            							                        <div class="col-12 col-sm-12 col-md-12 col-lg-12" style="padding-top: 6px;padding-left: 0;padding-right: 0;padding-bottom: 0;text-wrap: pretty;align-items: center;display: flex;justify-content: center;">
            							                            <img src="imagenes/eliminar.png" height="18" width="18" onClick="borrarJuego(' . $row2["juego"] . ', ' . $Week . ')">
            							                        </div>
            							                    </div>
            							                </div>
            							            </div>
            							            <div class="col-4 col-sm-4 col-md-4 col-lg-4" style="padding-top: 0px;padding-left: 0;padding-right: 0;padding-bottom: 0;">
                        							    <div class="container" style="height: 100%;">
                        							        <div class="row" style="height: 100%;">
            							                        <div class="col-9 col-sm-9 col-md-9 col-lg-9" style="padding-top: 6px;padding-left: 0;padding-right: 0;padding-bottom: 0;text-wrap: pretty;align-items: center;display: flex;justify-content: right;">
            							                            <div style="text-align: right;overflow-wrap: break-word;padding-right: 2px;">' . $row2["Local"] . '</div>
            							                        </div>
            							                        <div class="col-3 col-sm-3 col-md-3 col-lg-3" style="padding-top: 6px;padding-left: 0;padding-right: 0;padding-bottom: 0;justify-content: center;align-items: center;display: flex;">' . $row2["Logol"] . '</div>
            							                    </div>
            							                </div>
            							            </div>';
						if($row2["jugado1"] == 1){
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
							<div class="col-3 col-sm-3 col-md-3 col-lg-3" style="padding-top: 0px;padding-left: 0;padding-right: 0;padding-bottom: 0;">
							    <div class="container" style="height: 100%;">
							        <div class="row" style="height: 100%;justify-content: center;align-items: center;display: flex;">
            							<div class="col-3 col-sm-3 col-md-3 col-lg-3" style="justify-content: right;padding: 0;align-items: center;display: flex;"><span style="margin-top: 0px;color: ' . $colorSL . ';font-size: xx-large;font-weight: 500;">' . $row2["GL"] . '</span></div>
            							<div class="col-2 col-sm-2 col-md-2 col-lg-2" style="justify-content: right;color: ' . $colorSL . ';padding: 0;align-items: center;display: flex;">' . $row2["PL"] . '</div>
            							<div class="col-1 col-sm-1 col-md-1 col-lg-1" style="justify-content: center;padding: 0;align-items: center;display: flex;">' . $row2["marcador"] . '</div>
            							<div class="col-2 col-sm-2 col-md-2 col-lg-2" style="justify-content: left;color: ' . $colorSV . ';padding: 0;align-items: center;display: flex;">' . $row2["PV"] . '</div>
            							<div class="col-3 col-sm-3 col-md-3 col-lg-3" style="justify-content: left;padding: 0;align-items: center;display: flex;"><span style="margin-top: 0px;color: ' . $colorSV . ';font-size: xx-large;font-weight: 500;">' . $row2["GV"] . '</span></div>
            						</div>
            					</div>
							</div>';
            				
				        }
					    if($row2["jugado1"] == 0){
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
							<div class="col-3 col-sm-3 col-md-3 col-lg-3" style="padding-top: 0px;padding-left: 0;padding-right: 0;padding-bottom: 0;">
							    <div class="container" style="height: 100%;">
							        <div class="row" style="height: 100%;justify-content: center;align-items: center;display: flex;">
            							<div class="col-3 col-sm-3 col-md-3 col-lg-3" style="justify-content: right;padding: 0;align-items: center;display: flex;"><span style="margin-top: 0px;color: ' . $colorSL . ';font-size: xx-large;font-weight: 500;">' . $row2["GL"] . '</span></div>
            							<div class="col-2 col-sm-2 col-md-2 col-lg-2" style="justify-content: right;color: ' . $colorSL . ';padding: 0;align-items: center;display: flex;">' . $row2["PL"] . '</div>
            							<div class="col-1 col-sm-1 col-md-1 col-lg-1" style="justify-content: center;padding: 0;align-items: center;display: flex;">' . $row2["marcador"] . '</div>
            							<div class="col-2 col-sm-2 col-md-2 col-lg-2" style="justify-content: left;color: ' . $colorSV . ';padding: 0;align-items: center;display: flex;">' . $row2["PV"] . '</div>
            							<div class="col-3 col-sm-3 col-md-3 col-lg-3" style="justify-content: left;padding: 0;align-items: center;display: flex;"><span style="margin-top: 0px;color: ' . $colorSV . ';font-size: xx-large;font-weight: 500;">' . $row2["GV"] . '</span></div>
            						</div>
            					</div>
							</div>';
            				
				        }						    
				        if($row2["jugado1"] > 1){
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
							<div class="col-3 col-sm-3 col-md-3 col-lg-3" style="padding-top: 0px;padding-left: 0;padding-right: 0;padding-bottom: 0;">
							    <div class="container" style="height: 100%;">
							        <div class="row" style="height: 100%;justify-content: center;align-items: center;display: flex;">
            							<div class="col-3 col-sm-3 col-md-3 col-lg-3" style="justify-content: right;padding: 0;align-items: center;display: flex;"><span style="margin-top: 0px;color: ' . $colorSL . ';font-size: xx-large;font-weight: 500;">' . $row2["GL"] . '</span></div>
            							<div class="col-2 col-sm-2 col-md-2 col-lg-2" style="justify-content: right;color: ' . $colorSL . ';padding: 0;align-items: center;display: flex;">' . $row2["PL"] . '</div>
            							<div class="col-1 col-sm-1 col-md-1 col-lg-1" style="justify-content: center;padding: 0;align-items: center;display: flex;">' . $row2["marcador"] . '</div>
            							<div class="col-2 col-sm-2 col-md-2 col-lg-2" style="justify-content: left;color: ' . $colorSV . ';padding: 0;align-items: center;display: flex;">' . $row2["PV"] . '</div>
            							<div class="col-3 col-sm-3 col-md-3 col-lg-3" style="justify-content: left;padding: 0;align-items: center;display: flex;"><span style="margin-top: 0px;color: ' . $colorSV . ';font-size: xx-large;font-weight: 500;">' . $row2["GV"] . '</span></div>
            						</div>
            					</div>
							</div>';
            				
				        }
						$htmlWeek .= '<div class="col-4 col-sm-4 col-md-4 col-lg-4" style="padding-top: 0px;padding-left: 0;padding-right: 0;padding-bottom: 0;">
							            <div class="container" style="height: 100%;">
            							    <div class="row" style="height: 100%;">
							                    <div class="col-3 col-sm-3 col-md-3 col-lg-3" style="padding-top: 6px;padding-left: 0;padding-right: 0;padding-bottom: 0;justify-content: center;align-items: center;display: flex;">
							                        <div style="text-left: left;">' . $row2["Logov"] . ' </div>
							                    </div>
							                    <div class="col-9 col-sm-9 col-md-9 col-lg-9" style="padding-top: 6px;padding-left: 0;padding-right: 0;padding-bottom: 0;text-wrap: pretty;align-items: center;display: flex;justify-content: left;">
							                        <div style="text-align: left;overflow-wrap: break-word;padding-left: 2px;">' . $row2["Visitante"] .'</div>
							                    </div>
							                </div>
							            </div>
							        </div>';

						$htmlWeek .= '</div></div></div>';
						$htmlWeek .= '<div class="col-12 col-sm-12 col-md-12 col-lg-12" style="padding-top: 0px;padding-left: 0;padding-right: 0;padding-bottom: 0;">
						                <div class="container" style="padding-left: 0px; padding-right: 0px; margin-left: 0px; margin-right: 0px; width: 100%; max-width: 100%;">
        							        <div class="row">
        							            <div class="col-6 col-sm-6 col-md-6 col-lg-6" style="padding-top: 0px;padding-left: 0;padding-right: 0;padding-bottom: 0; align-items: center;display: flex;justify-content: center;">
        							                <div class="container" style="padding-left: 0px; padding-right: 0px; margin-left: 0px; margin-right: 0px; width: 100%; max-width: 100%;">
        							                        <div class="row">
        							                            <div class="col-12 col-sm-12 col-md-12 col-lg-12" style="padding-top: 0px;padding-left: 0;padding-right: 0;padding-bottom: 0; align-items: center;display: flex;justify-content: center;">
                    							                    <p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['604'] . '/' . $lang['605'] . '</span></p>
                    							                </div>
                        						                <div class="col-12 col-sm-12 col-md-12 col-lg-12" style="padding-top: 0px;padding-left: 0;padding-right: 0;padding-bottom: 0; align-items: center;display: flex;justify-content: center;">
                    							                    <div class="container" style="padding-left: 0px; padding-right: 0px; margin-left: 0px; margin-right: 0px; width: 100%; max-width: 100%;">
                    							        <div class="row">';
						if (strpos($row2["Comentarios"],$lang['654']) !== false){
							$htmlWeek .= "";
						}else{
							$htmlWeek .= '  <div class="col-6 col-sm-6 col-md-6 col-lg-6" style="padding-top: 0px;padding-left: 0;padding-right: 1;padding-bottom: 0; align-items: center;display: flex;justify-content: right;">
						                        <input type="date" style="width: 90px;text-align: center;padding-right: 0px !important;padding-left: 0px !important;" class="form-control form-control-sm" value="' . $row2["Fecha"] . '" name="fecha' . $row2["juego"] . '" id="fecha' . $row2["juego"] . '">
										    </div>
										    <div class="col-6 col-sm-6 col-md-6 col-lg-6" style="padding-top: 0px;padding-left: 1;padding-right: 0;padding-bottom: 0; align-items: center;display: flex;justify-content: left;">
						                        <input type="time" style="width: 95px;text-align: center;padding-right: 0px !important;padding-left: 0px !important;" class="form-control form-control-sm" value="' . $row2["horario"] . '" name="horario' . $row2["juego"] . '" id="horario' . $row2["juego"] . '">
											</div>
										</div></div></div></div></div></div>';
						}
						if (strpos($row2["Comentarios"],$lang['654']) !== false){
							$htmlWeek .= '';
						}else{
							$htmlWeek .= '<div class="col-3 col-sm-3 col-md-3 col-lg-3" style="padding-top: 0px;padding-left: 0;padding-right: 0;padding-bottom: 0;">
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
							$htmlWeek .= '<div class="col-2 col-sm-2 col-md-2 col-lg-2" style="padding-top: 0px;padding-left: 0;padding-right: 0;padding-bottom: 0;">
											<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['649'] . '</span></p>
											<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;">
												<div class="input-group input-group-static mb-0" style=" padding-left: 0.25rem; padding-right: 0.25rem;">
													<select class="form-control" style="width : 90px;padding-top: 0px;padding-bottom: 0px;" name="jugado' . $row2["juego"] . '" id="jugado' . $row2["juego"] . '">' . $row2["jugado"] . '</select>
												</div>
											</div>
										</div>';
						}

						$htmlWeek .= '</div>';
						$htmlWeek .= '<div class="col-1 col-sm-1 col-md-1 col-lg-1" style="padding-top: 0px;padding-left: 0;padding-right: 0;padding-bottom: 0;">
        							    <div class="container" style="height: 100%;">
        							        <div class="row" style="height: 100%;">
        				                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12" style="padding-top: 6px;padding-left: 0;padding-right: 0;padding-bottom: 0;text-wrap: pretty;align-items: center;display: flex;justify-content: center;">
        				                            <img class="expandirButtonS" id="expandirS' . $row2["juego"] . 'SA" src="./imagenes/expandir.png" height="25" width="25" onClick="abrirFichaEditSVoleibol(' . $row2["juego"] . ', ' . $row2["Jornada"] . ', ' . $row2["juego"] . ', \'' . $row2["Local"] . ' vs ' . $row2["Visitante"] . '\', \'' . $row2["Goles Local"] . '\', \'' . $row2["Goles Visitante"] . '\', \'' . $row2["Arbitro"] . '\', \'' . $row2["Comentarios"] . '\', 0, 0, \'' . $sqlcat . '\'); ">
        				                        </div>
        				                    </div>
        				                </div>
        				            </div>';
        				$htmlWeek .= '</div></td>';
						$htmlWeek .= '</tr>';
						if (strpos($row2["Comentarios"],$lang['654']) == false){
							$htmlWeek .= '<tr id="editS' . $row2["juego"] . '" class="juegoS" style="display: none">
									<td  scope="row" colspan="14" style="width: 1183px; padding-left: 0px; padding-right: 0px;">
										<div class="contentEditFichaS" width="100%" id="contentS' . $row2["juego"] . '" height="400"></div>
									</td>
								  </td></td></td></tr>';
						}

						$count++;
					}
				}
				$htmlWeek .= '	<tr>
									<td style="border-bottom: 0;">
										<button type="button" class="btn btn-primary" onclick="saveChangesS(' . $Season . ',' . $Week . ');">' . $lang['0000'] . '</button>
										<a href="pdf/flyerSC.php?Jornada_ID=' . $Week . '&Categoria_ID=' . $Category . '" target="_blank" download class="btn btn-primary" role="button" aria-pressed="true">Flyer Categoria</a>
										<a href="pdf/reportePendientes.php?Torneo_ID=' . $Season . '&Jornada_ID=' . $Week . '&Categoria_ID=' . $Category . '" target="_blank" download class="btn btn-primary" role="button" aria-pressed="true">Reporte Pendientes</a>
										<a href="pdf/reporteArbitros.php?Torneo_ID=' . $Season . '&Jornada_ID=' . $Week . '&Categoria_ID=' . $Category . '" target="_blank" download class="btn btn-primary" role="button" aria-pressed="true">Reporte Partidos</a>
										<a href="pdf/cedulas.php?Torneo_ID=' . $Season . '&Jornada_ID=' . $Week . '&Categoria_ID=' . $Category . '" target="_blank" download class="btn btn-primary" role="button" aria-pressed="true">' . $lang['647'] . '</a>
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
				if ($result3->num_rows > 0) {
					$htmlWeek .= '<tr>
									<td scope="row" colspan="1">
										<div class="d-flex px-2 py-1">
											<div style="width: 100%;text-align: left;padding-right: 3px;padding-top: 6px;">
												<div style="float: left;padding-top: 6px;padding-left: 10px;">
													<div style="float: left;width: 67px;">' . $lang['652'] . '</div>
													<div style="float: right;padding-left: 10px;">
														<select name="localAgregarS" id="localAgregarS" onChange="loadVisitanteAgregarS()">';
					// output data of each row
					while($row3 = $result3->fetch_assoc()) {
							$htmlWeek .= "<option value='" . $row3["Equipo_ID"] . "'>" . $row3["Equipo_DESC"] . "</option>";
					}
					$htmlWeek .= '						</select>
													</div>
												</div>
												<div style="float: left;padding-top: 6px;padding-left: 10px;">
													<div style="float: left;width: 67px;">' . $lang['653'] . '</div>
													<div style="float: right;padding-left: 10px;">
														<select name="visitanteAgregarS" id="visitanteAgregarS">
															<option value="NULL">' . $lang['654'] . '</option>
														</select>
													</div>
												</div>
												<div style="float: left;padding-left: 10px;width: 100%;">
													<button type="button" class="btn btn-primary" onClick="agregarJuego(\'' . $row["Fecha"] . '\', ' . $Season . ', ' . $Week . ', $(\'#localAgregarS\').val(), $(\'#visitanteAgregarS\').val());" >' . $lang['664'] . '</button>
												</div>
											</div>
										</div>
									</td>
								</tr><script>loadVisitanteAgregarS();</script>';
				}
				
                $htmlWeek .= '</tbody>';
                $htmlWeek .= '</table>';
				$htmlWeek .= '</div>';
				$htmlWeek .= '</div>';
				
                $htmlWeek .= '</div>';
                ?>