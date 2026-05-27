            <?php
				$apellidos = 'Apellido_P, 
                              Apellido_M,';
            	if($Config->jugadoresApellidos1){
            	    $apellidos = '  SUBSTRING(Apellido_P, 1, 1) Apellido_P, 
            				        SUBSTRING(Apellido_M, 1, 1) Apellido_M,';
            	}
            
            	$sql2 = "SET @rank:=0";
				$Config->query($sql2);
				$sql2 = "select @rank:=@rank+1 AS rank, a.* 
								 from (SELECT a.Jugador_ID,
												Numero,
												Clave,
												Apodo,
												Nombre,
												$apellidos
												Fecha_Nacimiento,
												Case when Estatus = 'A' then '" . $lang['927'] . "'
													when Estatus = 'B' then '" . $lang['928'] . "'
													when Estatus = 'S' then '" . $lang['929'] . "'
													end Estatus,
												case when Validado = 0 then '" . $lang["941"] . "'
													when Validado = 1 then '" . $lang["940"] . "'
													end Validado,
												case when Sexo = 0 then '" . $lang["942"] . "'
													when Sexo = 1 then '" . $lang["943"] . "'
													end SexoT,
												year(now())-year(a.Fecha_Nacimiento) Edad,
												Sexo,
												ifnull(Goles,0) Goles, 
												ifnull(Amarillas,0) Amarillas, 
												ifnull(Rojas,0) Rojas,
												ifnull(Juegos,0) Juegos, 
												floor(ifnull((Juegos/(select Jornadas from $schema.Torneos where Torneo_ID = $Season))*100,0)) '%' 
											FROM $schema.Jugadores a
												left outer join (select Jugador_ID, sum(Goles) Goles from $schema.Goles where Torneo_ID = $Season group by Jugador_ID) b on a.Jugador_ID = b.Jugador_ID
												left outer join (select Jugador_ID, sum(Cantidad) Amarillas from $schema.Amonestados where Torneo_ID = $Season group by Jugador_ID) c on a.Jugador_ID = c.Jugador_ID
												left outer join (select Jugador_ID, sum(Cantidad) Rojas from $schema.Expulsados where Torneo_ID = $Season group by Jugador_ID) d on a.Jugador_ID = d.Jugador_ID
												left outer join (select Jugador_ID, sum(Jugado) Juegos from $schema.JugadorJugado where Torneo_ID = $Season group by Jugador_ID) e on a.Jugador_ID = e.Jugador_ID
											where Equipo_ID = $Team
												and Estatus = 'A' 
											order by cast(Numero as decimal) asc, Nombre, Apellido_P, Apellido_M) a;";
				$result2 = $Config->query($sql2);
				$htmlTeam .= '<div class="containerN d-block  d-xs-block d-md-block d-lg-block d-xl-block">
								<div class="row">';
						

				$count = 0;
				if ($result2->num_rows > 0) {
					// output data of each row
					while ($row2 = $result2->fetch_assoc()) {
						$Edad = $row2["Edad"];
						//$birthDate = date_format($date, "m-d-Y");
						$htmlTeam .= '<div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-4 col-xxl-3" style="margin: 20px 0px;">
										<div class="card p-0"> 
											<span class="player-number">#' . $row2["Numero"] . '</span>
											<div class="card-image">
												<img src="./Form/fetch_image.php?Jugador_ID=' . $row2["Jugador_ID"] . '&Imagen=Foto" alt="">
											</div>
											<div class="card-content d-flex flex-column align-items-center">
												<h4 class="pt-2" style="height: 30px;">' . $row2["Apodo"] . '</h4>
												<h5 style="height: 50px;opacity: 1;">' . $row2["Nombre"] . ' ' . $row2["Apellido_P"] . ' ' . $row2["Apellido_M"] . '</h5>
												<ul class="social-icons d-flex justify-content-center" style="width: 100%">
													<table id="perfil" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; font-size: 12px; line-height: 1; color: #333; background-color: transparent; width: 90%">
														<tbody>
															<tr>
																<td style="border-right: 1px solid #f9b500;">' . $lang['921-1'] . '</td>
																<td style="padding-left: 10px;">' . $row2["Fecha_Nacimiento"] . '</td>
															</tr>
															<tr>
																<td style="border-right: 1px solid #f9b500;">' . $lang['377'] . '</td>
																<td style="padding-left: 10px;">' . $Edad . '</td>
															</tr>
															<tr>
																<td style="border-right: 1px solid #f9b500;">' . $lang['379'] . '</td>
																<td style="padding-left: 10px;">' . $row2["Validado"] . '</td>
															</tr>
															<tr>
																<td style="border-right: 1px solid #f9b500;">' . $lang['380'] . '</td>
																<td style="padding-left: 10px;">' . $row2["SexoT"] . '</td>
															</tr>
															<tr>
																<td style="border-right: 1px solid #f9b500;"><img src="imagenes/goal.png" width="10" height="10" alt="" /></td>
																<td style="padding-left: 10px;">' . $row2["Goles"] . '</td>
															</tr>
															<tr>
																<td style="border-right: 1px solid #f9b500;"><img src="imagenes/amarilla.png" width="10" height="10" alt="" /></td>
																<td style="padding-left: 10px;">' . $row2["Amarillas"] . '</td>
															</tr>
															<tr>
																<td style="border-right: 1px solid #f9b500;"><img src="imagenes/roja.png" width="10" height="10" alt="" /></td>
																<td style="padding-left: 10px;">' . $row2["Rojas"] . '</td>
															</tr>
															<tr>
																<td style="border-right: 1px solid #f9b500;">Juegos</td>
																<td style="padding-left: 10px;">' . $row2["Juegos"] . '</td>
															</tr>
															<tr>
																<td style="border-right: 1px solid #f9b500;">%</td>
																<td style="padding-left: 10px;">' . $row2["%"] . '</td>
															</tr>
														</tbody>
													</table>
												</ul>
											</div>
										</div>
									</div>';
						$count++;
					}
				}
				
				$htmlTeam .= '</div>
							</div>';
				?>