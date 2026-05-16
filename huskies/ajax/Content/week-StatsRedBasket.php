<?php
				$fecha = new DateTime();
				$htmlWeek .= '<div class="d-none d-xs-none d-md-block d-lg-block d-xl-block">
								<div class="card">
									<div class="table-responsive">
										<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
											<thead class="">';
				$htmlWeek .= '<th scope="col" class="text-left text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['639'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['641'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-left text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['642'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['643'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['644'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-left text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['645'] . '</th>';
				$htmlWeek .= '</thead>';
				$htmlWeek .= '<tbody>';
				
				
				$sql2 = "select distinct a.* from (select Jornada_DescCorta, a.Jornada_ID, concat(b.Nombre,' ', b.Apellido_P, ' ', b.Apellido_M) Jugador, c.Equipo_DESC, concat(c.Torneo_ID,'-', c.Equipo_ID) Logo, Cantidad Rojas, Comentario, Dias_Castigo, Multa, d.Fecha
						from $schema.Expulsados a 
							join $schema.Jugadores b on a.Jugador_ID = b.Jugador_ID
							join $schema.Equipos c on b.Equipo_ID = c.Equipo_ID and c.Torneo_ID = a.Torneo_ID and c.Fuerza = $Category
                            join $schema.Categorias vc on c.Fuerza = vc.Categoria_ID
							join $schema.Jornada d on a.Jornada_ID = d.Jornada_ID
                                                                        and d.Jornada_Type = 1 
                                                                        and vc.Calendario_ID = d.Calendario_ID
							JOIN (		SELECT jornada_id FROM $schema.Jornada
												where Torneo_ID = $Season
													and Fecha <= (		SELECT max(fecha) FROM $schema.Jornada
																			where Torneo_ID = $Season
																				and Fecha <= (select Fecha FROM $schema.Jornada where Jornada_ID = $Week and Torneo_ID = $Season)
																				and Jornada_Desc not like '%inales%'
																			order by Fecha desc 
																			limit 5)
													and Jornada_Desc not like '%inales%') l5 on a.jornada_ID = l5.Jornada_ID
						where a.Torneo_ID = $Season
						order by 10 asc, 4 asc,3 asc) a;";
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
							$htmlWeek .= '<td scope="row" class="align-middle text-left"><div class="d-flex px-0 py-1"><div style="width: 40px;text-align: right;padding-right: 3px;"><img src="imagenes/' . $row2["Logo"]. '.png?tmp=' . $fecha->getTimestamp() . '" class="avatar avatar-sm me-0" style="border-radius: 0rem !important;"></div><div style="width: 100px;text-align: left;padding-right: 3px;padding-top: 6px;"><span class="text-secondary text-xs font-weight-normal">' . $row2["Equipo_DESC"]. '</span></div></div></td>
											<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["Jornada_DescCorta"]. '</span></td>
											<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Jugador"]. '</span></td>
											<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["Dias_Castigo"]. '</span></td>
											<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["Multa"]. '</span></td>
											<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Comentario"]. '</span></td>';
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
				$htmlWeek .= '<th scope="col" class="text-left text-uppercase text-secondary text-xs font-weight-bolder opacity-7" style="padding: 0.15rem 0.1rem;"></th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7" style="padding: 0.15rem 0.1rem;">' . $lang['641'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-left text-uppercase text-secondary text-xs font-weight-bolder opacity-7" style="padding: 0.15rem 0.1rem;">' . $lang['642'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7" style="padding: 0.15rem 0.1rem;">' . $lang['643'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7" style="padding: 0.15rem 0.1rem;">' . $lang['644'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-left text-uppercase text-secondary text-xs font-weight-bolder opacity-7" style="padding: 0.15rem 0.1rem;">' . $lang['645'] . '</th>';
				$htmlWeek .= '</thead>';
				$htmlWeek .= '<tbody>';

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
							$htmlWeek .= '<td scope="row" class="align-middle text-left" style="padding: 0.15rem 0.1rem;"><div class="d-flex px-0 py-1"><div style="width: 40px;text-align: right;padding-right: 3px;"><img src="imagenes/' . $row2["Logo"]. '.png?tmp=' . $fecha->getTimestamp() . '" class="avatar avatar-sm me-0" style="border-radius: 0rem !important;"></div></div></td>
											<td scope="row" class="align-middle text-center" style="padding: 0.15rem 0.1rem;"><span class="text-secondary text-xs font-weight-normal">' . $row2["Jornada_DescCorta"]. '</span></td>
											<td scope="row" class="align-middle text-left" style="padding: 0.15rem 0.1rem;">
												<div style="width: 100%;text-align: left;padding-top: 6px;">
													<div class="d-flex px-0 py-0 lh-1">
														<div style="width: 100%;text-align: left;">
															<span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Jugador"]. '</span>
														</div>
													</div>
												</div>
											</td>
											<td scope="row" class="align-middle text-center" style="padding: 0.15rem 0.1rem;"><span class="text-secondary text-xs font-weight-normal">' . $row2["Dias_Castigo"]. '</span></td>
											<td scope="row" class="align-middle text-center" style="padding: 0.15rem 0.1rem;"><span class="text-secondary text-xs font-weight-normal">' . $row2["Multa"]. '</span></td>
											<td scope="row" class="align-middle text-left" style="padding: 0.15rem 0.1rem;">
												<div style="width: 100%;text-align: left;padding-top: 3px;">
													<div class="d-flex px-0 py-0 lh-1">
														<div style="width: 100%;text-align: left;">
															<span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Comentario"]. '</span>
														</div>
													</div>
												</div>
											</td>';
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