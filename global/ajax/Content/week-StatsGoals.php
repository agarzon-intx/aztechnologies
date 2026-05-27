<?php
				$fecha = new DateTime();
				$htmlWeek .= '<div class="d-none d-xs-none d-md-block d-lg-block d-xl-block">
								<div class="card">
									<div class="table-responsive">
										<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
											<thead class="">';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['630'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-left text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['631'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-left text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['632'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;"><img src="./imagenes/goal.png" width="20" height="20" alt=""></th>';
				$htmlWeek .= '</thead>';
				$htmlWeek .= '<tbody>';
				
				
				$Config->query("SET @rank:=0;");
				$sql2 = "SELECT @rank:=@rank+1 AS rank, a.* from (select Numero, concat(b.Nombre,' ', b.Apellido_P, ' ', b.Apellido_M) Jugador, c.Equipo_DESC, concat(c.Torneo_ID,'-', c.Equipo_ID) Logo, Sum(Goles) Goles 
						from $schema.Goles a 
							join $schema.Juegos d on a.Juego_ID = d.Juego_ID 
								and (a.Equipo_ID = d.Local_ID or a.Equipo_ID = d.Visitante_ID)
								and a.Jornada_ID = d.Jornada_ID
							join $schema.Jugadores b on a.Jugador_ID = b.Jugador_ID
							join $schema.Equipos c on a.Equipo_ID = c.Equipo_ID and c.Torneo_ID = a.Torneo_ID and c.Fuerza = $Category
                            join $schema.Categorias vc on c.Fuerza = vc.Categoria_ID
                            join $schema.Jornada vj on a.Jornada_ID = vj.Jornada_ID 
                                                                        and vj.Jornada_Type = 1 
                                                                        and vc.Calendario_ID = vj.Calendario_ID
							JOIN (		SELECT jornada_id FROM $schema.Jornada
												where Torneo_ID = $Season
													and Fecha <= (		SELECT max(fecha) FROM $schema.Jornada
																			where Torneo_ID = $Season
																				and Fecha <= (select Fecha FROM $schema.Jornada where Jornada_ID = $Week and Torneo_ID = $Season)
																				and Jornada_Desc not like '%inales%'
																			order by Fecha desc 
																			limit 5)
													and Jornada_Desc not like '%inales%') l5 on d.jornada_ID = l5.Jornada_ID
						where a.Torneo_ID = $Season
						group by Numero, concat(b.Nombre,' ', b.Apellido_P, ' ', b.Apellido_M), c.Equipo_DESC
						order by 5 desc, 1 asc, 2 asc) a";
						//echo $sql2;
				$result2 = $Config->query($sql2); 
                $count = 0;
				if($result2){
					if ($result2->num_rows > 0) {
						// output data of each row
						while($row2 = $result2->fetch_assoc()) {
							if (($count % 2) == 1){
								$htmlWeek .= "<tr>";
							}else{
								$htmlWeek .= "<tr class='alt'>";
							}
							$htmlWeek .= '<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["rank"]. '</span></td>
											<td scope="row" class="align-middle text-right"><div class="d-flex px-0 py-1"><div style="width: 40px;text-align: right;padding-right: 3px;"><img src="imagenes/' . $row2["Logo"]. '.png?tmp=' . $fecha->getTimestamp() . '" class="avatar avatar-sm me-0" style="border-radius: 0rem !important;"></div><div style="width: 100px;text-align: left;padding-right: 3px;padding-top: 6px;"><span class="text-secondary text-xs font-weight-normal">' . $row2["Equipo_DESC"]. '</span></div></div></td>
											<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Jugador"]. '</span></td>
											<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["Goles"]. '</span></td>';
							$htmlWeek .= '</tr>';
							$count++;
						}
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
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.15rem 0.1rem;">' . $lang['630'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-left text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.15rem 0.1rem;"></th>';
				$htmlWeek .= '<th scope="col" class="text-left text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.15rem 0.1rem;">' . $lang['632'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.15rem 0.1rem;"><img src="./imagenes/goal.png" width="20" height="20" alt=""></th>';
				$htmlWeek .= '</thead>';
				$htmlWeek .= '<tbody>';
				
				
				$Config->query("SET @rank:=0;");
				$result2 = $Config->query($sql2); 
                $count = 0;
				if($result2){
					if ($result2->num_rows > 0) {
						// output data of each row
						while($row2 = $result2->fetch_assoc()) {
							if (($count % 2) == 1){
								$htmlWeek .= "<tr>";
							}else{
								$htmlWeek .= "<tr class='alt'>";
							}
							$htmlWeek .= '<td scope="row" class="align-middle text-center" style="padding: 0.15rem 0.1rem;"><span class="text-secondary text-xs font-weight-normal">' . $row2["rank"]. '</span></td>
											<td scope="row" class="align-middle text-right" style="padding: 0.15rem 0.1rem;"><div class="d-flex px-0 py-1"><div style="width: 40px;text-align: right;padding-right: 3px;"><img src="imagenes/' . $row2["Logo"]. '.png?tmp=' . $fecha->getTimestamp() . '" class="avatar avatar-sm me-0" style="border-radius: 0rem !important;"></div></div></td>
											<td scope="row" class="align-middle text-left" style="padding: 0.15rem 0.1rem;">
												<div style="width: 100%;text-align: left;padding-top: 6px;">
													<div class="d-flex px-0 py-0 lh-1">
														<div style="width: 100%;text-align: left;">
															<span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Jugador"]. '</span>
														</div>
													</div>
												</div>
											</td>
											<td scope="row" class="align-middle text-center" style="padding: 0.15rem 0.1rem;"><span class="text-secondary text-xs font-weight-normal">' . $row2["Goles"]. '</span></td>';
							$htmlWeek .= '</tr>';
							$count++;
						}
					}
				 }
                 $htmlWeek .= '</tbody>';
                $htmlWeek .= '</table>';
				$htmlWeek .= '</div>';
				$htmlWeek .= '</div>';
				$htmlWeek .= '</div>';
?>