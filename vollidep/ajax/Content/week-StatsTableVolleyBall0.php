<?php
				$fecha = new DateTime();
				$htmlWeek .= '<div class="d-none d-xs-none d-md-block d-lg-block d-xl-block">
								<div class="card">
									<div class="table-responsive">
										<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
											<thead class="">';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['617'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-left text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['618'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-left text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['618-1'] . '</th>';
				$htmlWeek .= '</thead>';
				$htmlWeek .= '<tbody>';
				
				
				$sqlPenales1 = "when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 and l.Jugado =1 then 1";
				$sqlPenales2 = "0";
				$sqlPenales3 = "when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 and l.Jugado =1 then 1";
				$sqlPenales4 = "0";
				$sqlPenales5 = "when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 and v.Jugado =1 then 1 ";
				$sqlByeWeekPoints1 = "";
				$sqlByeWeekPoints2 = "";
				$sqlByeWeekPoints3 = "0 ";
				if($Config->ByeWeekPoints == 1){
					$sqlByeWeekPoints1 = "when Equipo_ID = l.Local_ID and l.Visitante_ID is null then 3 ";
					$sqlByeWeekPoints2 = "when Equipo_ID = l.Local_ID and l.Visitante_ID is null then 1 ";
					$sqlByeWeekPoints3 = "" . $Config->ByeWeekPointsGoals . " ";
				}

				$sql2 = "SET @rank:=0;";
				$Config->query($sql2);
				$sql21 = "SELECT @rank:=@rank+1 AS rank, 
				                Logo, 
				                Equipo_DESC, 
				                Equipo_DESC3
						   from (
								Select  Logo, 
        								j.Equipo_ID,
        								Equipo_DESC,
        								Equipo_DESC3,
        								fuerza
        						from (
										select distinct concat(e.Torneo_ID, '-', e.Equipo_ID) Logo, 
                                                  Equipo_ID, 
                                                  Equipo_DESC, 
                                                  Equipo_DESC3, 
                                                  Fuerza
                                        from $schema.Equipos e 
										where e.Fuerza = $Category and e.Torneo_ID = $Season and Activo = 1
										UNION
										select distinct concat(e.Torneo_ID,'-', e.Equipo_ID) Logo, 
												Equipo_ID, 
												Equipo_DESC, 
												Equipo_DESC3, 
												Fuerza
										from $schema.Equipos e
										where e.Fuerza = $Category and e.Torneo_ID = $Season and Activo = 1) j
								where Fuerza = $Category
								Group by j.Equipo_ID, Equipo_DESC, Fuerza
								) jj
						order by Equipo_DESC";
				//$htmlWeek .= $sql21;
				$result2 = $Config->query($sql21);
				$count = 0;
				if ($result2->num_rows > 0) {
					// output data of each row
					while($row2 = $result2->fetch_assoc()) {
						if (($count % 2) == 1){
							$htmlWeek .= "<tr>";
						}else{
							$htmlWeek .= "<tr class='alt'>";
						}
						$htmlWeek .=  ' <td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["rank"]. '</span></td>
										<td scope="row" class="align-middle text-right"><div class="d-flex px-0 py-1"><div style="width: 40px;text-align: right;padding-right: 3px;"><img src="imagenes/' . $row2["Logo"]. '.png?tmp=' . $fecha->getTimestamp() . '" class="avatar avatar-sm me-0" style="border-radius: 0rem !important;"></div><div style="width: 100px;text-align: left;padding-right: 3px;padding-top: 6px;"><span class="text-secondary text-xs font-weight-normal">' . $row2["Equipo_DESC"]. '</span></div></div></td>
										<td scope="row" class="align-middle text-right"><div class="d-flex px-0 py-1"><div style="width: 100px;text-align: left;padding-right: 3px;padding-top: 6px;"><span class="text-secondary text-xs font-weight-normal">' . $row2["Equipo_DESC3"]. '</span></div></div></td>';
						$htmlWeek .= '</tr>';
						$count++;
					}
				}
                $htmlWeek .= '</tbody>';
                $htmlWeek .= '</table>';
				$htmlWeek .= '</div>';
				$htmlWeek .= '</div>';
				$htmlWeek .= '</div>';
				
								$htmlWeek .= '<div class="d-block d-xs-block d-md-none d-lg-none d-xl-none">
								<div class="card">
									<div class="table-responsive">
										<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
											<thead class="">';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.15rem 0.1rem;">' . $lang['617'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-left text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.15rem 0.1rem;"></th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.15rem 0.1rem;">' . $lang['619'] . '</th>';
				$htmlWeek .= '</thead>';
				$htmlWeek .= '<tbody>';
				
				
				$sql3 = "SET @rank:=0;";
				$Config->query($sql3);
				
				//$htmlWeek .= $sql2;
				$result2 = $Config->query($sql21);
				$count = 0;
				if ($result2->num_rows > 0) {
					// output data of each row
					while($row2 = $result2->fetch_assoc()) {
						if (($count % 2) == 1){
							$htmlWeek .= "<tr>";
						}else{
							$htmlWeek .= "<tr class='alt'>";
						}
						$htmlWeek .=  '
							
										<td scope="row" class="align-middle text-center" style="padding: 0.15rem 0.1rem;"><span class="text-secondary text-xs font-weight-normal">' . $row2["rank"]. '</span></td>
										<td scope="row" class="align-middle text-right" style="padding: 0.15rem 0.1rem;"><div class="d-flex px-0 py-1"><div style="width: 40px;text-align: right;padding-right: 3px;"><img src="imagenes/' . $row2["Logo"]. '.png?tmp=' . $fecha->getTimestamp() . '" class="avatar avatar-sm me-0" style="border-radius: 0rem !important;"></div><div style="width: 20px;text-align: left;padding-right: 3px;padding-top: 6px;"><span class="text-secondary text-xs font-weight-normal">' . $row2["Equipo_DESC"]. '</span></div></div></td>
										<td scope="row" class="align-middle text-right" style="padding: 0.15rem 0.1rem;"><div style="width: 20px;text-align: left;padding-right: 3px;padding-top: 6px;"><span class="text-secondary text-xs font-weight-normal">' . $row2["Equipo_DESC3"]. '</span></div></div></td>';
						$htmlWeek .= '</tr>';
						$count++;
					}
				}
                $htmlWeek .= '</tbody>';
                $htmlWeek .= '</table>';
				$htmlWeek .= '</div>';
				$htmlWeek .= '</div>';
				$htmlWeek .= '</div>';
                
?>