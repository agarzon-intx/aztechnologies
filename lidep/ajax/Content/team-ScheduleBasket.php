            <?php
				$fecha = new DateTime();
				$htmlTeam .= '<div class="d-none  d-xs-none d-md-none d-lg-none d-xl-block"><div class="card">
								<div class="table-responsive">
									<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
										<thead class="">
											<th scope="col" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['358'] . '  ' . $lang['361'] . '</span></th>';
				$htmlTeam .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['362'] . '</th>';
				$htmlTeam .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['363'] . ' - ' . $lang['364'] . '</th>';
				$htmlTeam .= '<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['365'] . '</th>';
				$htmlTeam .= '</thead>';
				$htmlTeam .= '<tbody>';
				$sql2 = "select * from (
								select 0 as VisitanteS, 
										j.Torneo_ID as Torneo, 
										jo.Jornada_ID as Jornada, 
										j.Juego_ID as juego, 
										jugado, 
										concat(l.equipo_desc,'') 'Local',
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
										concat(l.Torneo_ID,'-', l.Equipo_ID) Logol, 
										case 
											when j.Visitante_Id is null then ''
											else
												concat(v.Torneo_ID,'-', v.Equipo_ID)
										end  as Logov,
										case 
											when jugado = 0 then '' 
											else ':'
										end as marcador,
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
									where Local_ID = $Team
									UNION
									select 1 as VisitanteS, j.Torneo_ID as Torneo, jo.Jornada_ID as Jornada, j.Juego_ID as juego, jugado, case when j.Visitante_Id is null then '' else concat(l.equipo_desc,'') end  as 'Local', 
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
												concat(l.Torneo_ID,'-', l.Equipo_ID) 
										end as Logol, 
										case 
											when j.Visitante_Id is null then ''
											else
												concat(v.Torneo_ID,'-', v.Equipo_ID)
										end  as Logov,
										case 
											when jugado = 0 then '' 
											else ':'
										end as marcador,
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
									where Visitante_ID = $Team) a
						order by Fecha asc, VisitanteS, Torneo, Jornada, Juego;";
				//$htmlTeam .= $sql2;
				$result2 = $Config->query($sql2);

				$count = 0;
				if ($result2->num_rows > 0) {
						
					while($row2 = $result2->fetch_assoc()) {
						if (($count % 2) == 1){
							$htmlTeam .= "<tr>";
						}else{
							$htmlTeam .= "<tr class='alt'>";
						}
						$htmlTeam .=  '<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["Jornada"]. '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["Fecha"]. '</span></td>';
						$htmlTeam .=  '<td scope="row" class="align-middle text-right"><div class="justify-content-center d-flex px-0 py-1"><div class="align-self-center" style="width: 30%; text-align: right;padding-right: 3px; ">' . $row2["Local"] . '</div>
							<div class="align-self-center"><img src="imagenes/' . $row2["Logol"] . '.png?tmp=' . $fecha->getTimestamp() . '" class="avatar avatar-sm me-0" style="border-radius: 0rem !important;"></div>';
						if($row2["jugado"] == 2){
							$htmlTeam .= '<div style="width: 15px;text-align: center;" class="align-self-center">' . $lang['663'] . '</div>';
						}
						if($row2["jugado"] == 1){
						    if($row2["GL"] > $row2["GV"]){
						        $colorSL = 'green';
						        $colorSV = 'red';
						    }
						    if($row2["GL"] < $row2["GV"]){
						        $colorSV = 'green';
						        $colorSL = 'red';
						    }
						    if($row2["GL"] == $row2["GV"]){
						        $colorSV = 'inherit';
						        $colorSL = 'inherit';
						    }
							$htmlTeam .= '<div style="width: 30px;text-align: right;margin-top: 12px;margin-bottom: 0px;color: ' . $colorSL . ';">' . $row2["PL"] . '</div>
            							<div style="width: 10px;text-align: center;margin-top: 12px;margin-bottom: 0px;">' . $row2["marcador"] . '</div>
            							<div style="width: 30px;text-align: left;margin-top: 12px;margin-bottom: 0px;color: ' . $colorSV . ';">' . $row2["PV"] . '</div>';
						}
						if($row2["jugado"] == 0){
						    if($row2["GL"] > $row2["GV"]){
						        $colorSL = 'green';
						        $colorSV = 'red';
						    }
						    if($row2["GL"] < $row2["GV"]){
						        $colorSV = 'green';
						        $colorSL = 'red';
						    }
						    if($row2["GL"] == $row2["GV"]){
						        $colorSV = 'inherit';
						        $colorSL = 'inherit';
						    }
							$htmlTeam .= '<div style="width: 30px;text-align: right;padding-top: 6px;color: ' . $colorSL . ';"></div>
            							<div style="width: 10px;text-align: center;padding-top: 6px;"></div>
            							<div style="width: 30px;text-align: left;padding-top: 6px;color: ' . $colorSV . ';"></div>';
						}
						if($row2["Logov"] != ''){
    						$htmlTeam .= '<div class="align-self-center"><img src="imagenes/' . $row2["Logov"] . '.png?tmp=' . $fecha->getTimestamp() . '" class="avatar avatar-sm me-0" style="border-radius: 0rem !important;"></div>
    							<div style="width: 30%; text-align: left; padding-left: 3px; " class="align-self-center">' . $row2["Visitante"] .'</div></div></td>
    						        <td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal"><a target="_blank" href="' . $row2["Google"] . '">' . $row2["Campo"]. '</a></span></td>';
						}else{
						    $htmlTeam .= '<div class="align-self-center">' . $lang['654'] . '</div>
    							<div style="width: 30%; text-align: left; padding-left: 3px; " class="align-self-center"></div></div></td>
    						        <td scope="row" class="align-middle text-left"></td>';
						}
						$htmlTeam .= '</tr>';
						$count++;
                	}
				}
                $htmlTeam .= '</tbody>';
                $htmlTeam .= '</table>';
				$htmlTeam .= '</div>';
				$htmlTeam .= '</div>';
				$htmlTeam .= '</div>';
				
				$htmlTeam .= '<div class="d-none  d-xs-block d-md-block d-lg-block d-xl-none"><div class="card">
								<div class="table-responsive">
									<table class=" table align-items-center mb-0" style="border-color: #136aeb;">';
				$htmlTeam .= '			<tbody>';
				$result2 = $Config->query($sql2);

				$count = 0;
				if ($result2->num_rows > 0) {
						
					while($row2 = $result2->fetch_assoc()) {
						if (($count % 2) == 1){
							$htmlTeam .= "<tr>";
						}else{
							$htmlTeam .= "<tr class='alt'>";
						}
						
						$htmlTeam .=  '<td scope="row"><div class="justify-content-center d-flex px-0 py-1">';
						if($row2["jugado"] == 2){
							$htmlTeam .= '<div style="width: 40px;text-align: center;" class="align-self-center">' . $lang['663'] . '</div>';
						}else{
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
								$htmlTeam .= '	<div class="align-self-center" style="width: 20%; text-align: center;padding-right: 3px; font-size:3vw;">' . $row2["Local"] . '</div>
											<div class="justify-content-center d-flex px-0 py-1" style="width: 35%; text-align: right;padding-right: 3px; font-size:3vw;">
												<div><img src="imagenes/' . $row2["Logol"] . '.png?tmp=' . $fecha->getTimestamp() . '" class="avatar avatar-sm me-0" style="border-radius: 0rem !important;"></div>
												<div style="width: 20px;text-align: center;">
													<p style="margin-top: -4px;color: ' . $colorSL . ';font-size: xx-large;font-weight: 500;margin-bottom: 0px !important;">' . $row2["GL"] . '</p>
												</div>
												<div style="width: 20px;text-align: right;color: ' . $colorSL . ';" class="align-self-center">
												    <p style="margin-top: -4px;color: ' . $colorSL . ';font-size: large;font-weight: 500;margin-bottom: 0px !important;">' . $row2["PL"] . '</p>
												</div>
												<div style="width: 10px;text-align: center;" class="align-self-center"><p style="margin-top: -4px;font-size: large;font-weight: 500;margin-bottom: 0px !important;">' . $row2["marcador"] . '</p></div>
												<div style="width: 20px;text-align: left;color: ' . $colorSV . ';" class="align-self-center">
												    <p style="margin-top: -4px;color: ' . $colorSV . ';font-size: large;font-weight: 500;margin-bottom: 0px !important;">' . $row2["PV"] . '</p>
							                    </div>
							                    <div style="width: 20px;text-align: center;">
							                        <p style="margin-top: -4px;color: ' . $colorSV . ';font-size: xx-large;font-weight: 500;margin-bottom: 0px !important;">' . $row2["GV"] . '</p>
							                    </div>
												<div><img src="imagenes/' . $row2["Logov"] . '.png?tmp=' . $fecha->getTimestamp() . '" class="avatar avatar-sm me-0" style="border-radius: 0rem !important;"></div>
											</div>
											<div style="width: 20%; text-align: center; padding-left: 3px; font-size:3vw;" class="align-self-center">' . $row2["Visitante"] .'</div>';
						     }
						     if($row2["jugado"] == 0){
    							$colorSV = 'inherit';
    						    $colorSL = 'inherit';
								$htmlTeam .= '	<div class="align-self-center" style="width: 20%; text-align: center;padding-right: 3px; font-size:3vw;">' . $row2["Local"] . '</div>
											<div class="justify-content-center d-flex px-0 py-1" style="width: 35%; text-align: right;padding-right: 3px; font-size:3vw;">
												<div><img src="imagenes/' . $row2["Logol"] . '.png?tmp=' . $fecha->getTimestamp() . '" class="avatar avatar-sm me-0" style="border-radius: 0rem !important;"></div>
												<div style="width: 20px;text-align: center;"></div>
												<div style="width: 20px;text-align: right;color: ' . $colorSL . ';" class="align-self-center"></div>
												<div style="width: 10px;text-align: center;" class="align-self-center"></div>
												<div style="width: 20px;text-align: left;color: ' . $colorSV . ';" class="align-self-center"></div>
							                    <div style="width: 20px;text-align: center;"></div>';
        						if($row2["Logov"] != ''){
            						$htmlTeam .= '<div><img src="imagenes/' . $row2["Logov"] . '.png?tmp=' . $fecha->getTimestamp() . '" class="avatar avatar-sm me-0" style="border-radius: 0rem !important;"></div>
											</div>
											<div style="width: 20%; text-align: center; padding-left: 3px; font-size:3vw;" class="align-self-center">' . $row2["Visitante"] .'</div>';
        						}else{
        						    $htmlTeam .= '<div>' . $lang['654'] . '</div>
											</div>
											<div style="width: 20%; text-align: center; padding-left: 3px; font-size:3vw;" class="align-self-center"></div>';
        						}
						     }
						}
						if($row2["Logov"] != ''){
        					$htmlTeam .= '</div>
    							<div class="d-flex px-0 py-1">
    							<div style="width: 20%;text-align: center;padding-top: 0px;">
    									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">#</span></p>
    									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Jornada"]. '</span></div></div></div>
    									<div style="width: 40%;text-align: center;padding-top: 0px;">
    									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['362'] . '</span></p>
    									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Fecha"]. '</span></div></div></div>
    									<div style="width: 40%;text-align: center;padding-top: 0px;">
    									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['365'] . '</span></p>
    									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap"><a target="_blank" href="' . $row2["Google"] . '">' . $row2["Campo"]. '</a></span></div></div></div>
    									
    									</div></div></td>';
						}else{
						   $htmlTeam .= '</div>
    							<div class="d-flex px-0 py-1">
    							<div style="width: 20%;text-align: center;padding-top: 0px;">
    									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">#</span></p>
    									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Jornada"]. '</span></div></div></div>
    									<div style="width: 40%;text-align: center;padding-top: 0px;">
    									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['362'] . '</span></p>
    									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Fecha"]. '</span></div></div></div>
    									<div style="width: 40%;text-align: center;padding-top: 0px;">
    									<p style="margin-bottom: 0rem !important;"></p>
    									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"></div></div></div>
    									
    									</div></div></td>';
						}
						
						$htmlTeam .= '</tr>';
						$count++;
                	}
				}
                $htmlTeam .= '</tbody>';
                $htmlTeam .= '</table>';
				$htmlTeam .= '</div>';
				$htmlTeam .= '</div>';
				$htmlTeam .= '</div>';
				
				$htmlTeam .= '<div class="d-block  d-xs-none d-md-none d-lg-none d-xl-none"><div class="card">
								<div class="table-responsive">
									<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
										<thead class="">
										</thead>';
				$htmlTeam .= '			<tbody>';
				$result2 = $Config->query($sql2);

				$count = 0;
				if ($result2->num_rows > 0) {
						
					while($row2 = $result2->fetch_assoc()) {
						if (($count % 2) == 1){
							$htmlTeam .= "<tr>";
						}else{
							$htmlTeam .= "<tr class='alt'>";
						}
						$htmlTeam .=  '<td scope="row" class="align-middle text-center"><div class="justify-content-center d-flex px-0 py-1">';
						if($row2["jugado"] == 2){
							$htmlTeam .= '<div style="width: 15px;text-align: center;" class="align-self-center">' . $lang['663'] . '</div>';
						}else{
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
								$htmlTeam .= '	<div class="align-self-center" style="width: 30%; text-align: right;padding-right: 3px; font-size:3vw;">' . $row2["Local"] . '</div>
											<div class="justify-content-center d-flex px-0 py-1" style="width: 40%; text-align: right;padding-right: 3px; font-size:3vw;">
												<div><img src="imagenes/' . $row2["Logol"] . '.png?tmp=' . $fecha->getTimestamp() . '" class="avatar avatar-sm me-0" style="border-radius: 0rem !important;"></div>
												<div style="width: 20px;text-align: center;">
													<p style="margin-top: -4px;color: ' . $colorSL . ';font-size: xx-large;font-weight: 500;margin-bottom: 0px !important;">' . $row2["GL"] . '</p>
												</div>
												<div style="width: 20px;text-align: right;color: ' . $colorSL . ';" class="align-self-center">
												    <p style="margin-top: -4px;color: ' . $colorSL . ';font-size: large;font-weight: 500;margin-bottom: 0px !important;">' . $row2["PL"] . '</p>
												</div>
												<div style="width: 10px;text-align: center;" class="align-self-center"><p style="margin-top: -4px;font-size: large;font-weight: 500;margin-bottom: 0px !important;">' . $row2["marcador"] . '</p></div>
												<div style="width: 20px;text-align: left;color: ' . $colorSV . ';" class="align-self-center">
												    <p style="margin-top: -4px;color: ' . $colorSV . ';font-size: large;font-weight: 500;margin-bottom: 0px !important;">' . $row2["PV"] . '</p>
							                    </div>
							                    <div style="width: 20px;text-align: center;">
							                        <p style="margin-top: -4px;color: ' . $colorSV . ';font-size: xx-large;font-weight: 500;margin-bottom: 0px !important;">' . $row2["GV"] . '</p>
							                    </div>
												<div><img src="imagenes/' . $row2["Logov"] . '.png?tmp=' . $fecha->getTimestamp() . '" class="avatar avatar-sm me-0" style="border-radius: 0rem !important;"></div>
											</div>
											<div style="width: 30%; text-align: left; padding-left: 3px; font-size:3vw;" class="align-self-center">' . $row2["Visitante"] .'</div>';
						     }
						     if($row2["jugado"] == 0){
    							$colorSV = 'inherit';
    						    $colorSL = 'inherit';
								$htmlTeam .= '	<div class="align-self-center" style="width: 20%; text-align: center;padding-right: 3px; font-size:3vw;">' . $row2["Local"] . '</div>
											<div class="justify-content-center d-flex px-0 py-1" style="width: 35%; text-align: right;padding-right: 3px; font-size:3vw;">
												<div><img src="imagenes/' . $row2["Logol"] . '.png?tmp=' . $fecha->getTimestamp() . '" class="avatar avatar-sm me-0" style="border-radius: 0rem !important;"></div>
												<div style="width: 20px;text-align: center;"></div>
												<div style="width: 20px;text-align: right;color: ' . $colorSL . ';" class="align-self-center"></div>
												<div style="width: 10px;text-align: center;" class="align-self-center"></div>
												<div style="width: 20px;text-align: left;color: ' . $colorSV . ';" class="align-self-center"></div>
							                    <div style="width: 20px;text-align: center;"></div>';
							    
        						if($row2["Logov"] != ''){
            						$htmlTeam .= '<div><img src="imagenes/' . $row2["Logov"] . '.png?tmp=' . $fecha->getTimestamp() . '" class="avatar avatar-sm me-0" style="border-radius: 0rem !important;"></div>
											</div>
											<div style="width: 20%; text-align: center; padding-left: 3px; font-size:3vw;" class="align-self-center">' . $row2["Visitante"] .'</div>';
        						}else{
        						    $htmlTeam .= '<div>' . $lang['654'] . '</div>
											</div>
											<div style="width: 20%; text-align: center; padding-left: 3px; font-size:3vw;" class="align-self-center"></div>';
        						}
												
						     }
							$htmlTeam .= '';
						}
						if($row2["Logov"] != ''){
    						$htmlTeam .= '</div>
    							<div class="d-flex px-0 py-1">
    							<div style="width: 10%;text-align: center;padding-top: 0px;">
    									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">#</span></p>
    									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Jornada"]. '</span></div></div></div>
    									<div style="width: 30%;text-align: center;padding-top: 0px;">
    									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['362'] . '</span></p>
    									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Fecha"]. '</span></div></div></div>
    									<div style="width: 30%;text-align: center;padding-top: 0px;">
    									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['365'] . '</span></p>
    									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap"><a target="_blank" href="' . $row2["Google"] . '">' . $row2["Campo"]. '</a></span></div></div></div>
    									<div style="width: 10%;text-align: center;padding-top: 0px;">
    									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['367'] . '</span></p>
    									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Reales"] . '</span></div></div></div>
    									
    									</div></div></td>';
						}else{
    						$htmlTeam .= '</div>
    							<div class="d-flex px-0 py-1">
    							<div style="width: 10%;text-align: center;padding-top: 0px;">
    									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">#</span></p>
    									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Jornada"]. '</span></div></div></div>
    									<div style="width: 30%;text-align: center;padding-top: 0px;">
    									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['362'] . '</span></p>
    									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Fecha"]. '</span></div></div></div>
    									<div style="width: 30%;text-align: center;padding-top: 0px;">
    									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold"></span></p>
    									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"></div></div></div>
    									<div style="width: 10%;text-align: center;padding-top: 0px;">
    									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold"></span></p>
    									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"></div></div></div>
    									
    									</div></div></td>';
						}
						
						$htmlTeam .= '</tr>';
						$count++;
                	}
				}
                $htmlTeam .= '</tbody>';
                $htmlTeam .= '</table>';
				$htmlTeam .= '</div>';
				$htmlTeam .= '</div>';
				$htmlTeam .= '</div>';
			?>