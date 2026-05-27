            <?php
				$sql2 = "SET @rank:=0";
				$Config->query($sql2);
				$sql2 = "select @rank:=@rank+1 AS rank, a.* 
								 from (SELECT a.Jugador_ID,
												Numero,
												Clave,
												Apodo,
												Nombre,
												Apellido_P,
												Apellido_M,
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
												ifnull(Juegos,0) Juegos, 
												floor(ifnull((Juegos /(k.Jornadas))* 100, 0)) '%',
                                                ifnull(b.AmarillasP, 0) FP, 
                                                ifnull(b.AmarillasT, 0) FT,
                                                ifnull(c.Rojas, 0) EX,
                                                ifnull(d.Puntos1, 0) Puntos1,
                                                ifnull(d.Puntos2, 0) Puntos2,
                                                ifnull(d.Puntos3, 0) Puntos3
											FROM $schema.Jugadores a
                                			    LEFT OUTER JOIN (
                                                				select Jugador_ID, ifnull(sum(CantidadP),0) as AmarillasP, ifnull(sum(CantidadT),0) as AmarillasT 
                                                				from $schema.AmonestadosB 
                                                				where Torneo_ID = $Season 
                                                				group by Jugador_ID
                                                				) b on a.Jugador_ID = b.Jugador_ID 
                                                LEFT OUTER JOIN (
                                                				select Jugador_ID, ifnull(Cantidad,0) as Rojas
                                                				from $schema.Expulsados 
                                                				where Torneo_ID = $Season  
                                                				group by Jugador_ID
                                                				) c on a.Jugador_ID = c.Jugador_ID 
                                                LEFT OUTER JOIN (
                                                				select Jugador_ID, ifnull(sum(Puntos1),0) as Puntos1, ifnull(sum(Puntos2),0) as Puntos2, ifnull(sum(Puntos3),0) as Puntos3
                                                				from $schema.PuntosB 
                                                				where Torneo_ID = $Season 
                                                				group by Jugador_ID
                                                				) d on a.Jugador_ID = d.Jugador_ID 
                                				LEFT OUTER JOIN (
                                				                select Jugador_ID, sum(Jugado) Juegos 
                                				                from $schema.JugadorJugado 
                                				                where Torneo_ID = $Season 
                                				                group by Jugador_ID
                                				                ) e on a.Jugador_ID = e.Jugador_ID 
                                				LEFT OUTER JOIN $schema.Range_Age f on f.Range_Active = 1 and f.Range_Id <> 1 and year(now())-year(a.Fecha_Nacimiento) between f.Range_Start and f.Range_End
                                				LEFT OUTER JOIN $schema.Colores g on f.Range_Color_ID = g.Color_ID
                                				LEFT OUTER JOIN $schema.Categorias h on year(now())-year(a.Fecha_Nacimiento) between h.Edad_Inicial and h.Edad_Final
                                				JOIN $schema.Equipos i ON a.Equipo_ID = i.Equipo_ID and i.Torneo_ID = $Season
                                				JOIN $schema.Categorias j ON i.Fuerza = j.Categoria_ID 
                                                LEFT OUTER JOIN (
                                        						SELECT a.Torneo_ID, Categoria_ID, count(*) as Jornadas
                                        						FROM aztechn1_jubal.Jornada a
                                        							join aztechn1_jubal.Calendario b on a.Calendario_ID = b.Calendario_ID
                                        							join aztechn1_jubal.Categorias c on b.Calendario_ID = c.Calendario_ID
                                        						WHERE a.Torneo_ID = 1
                                        							and a.Jornada_Type = 1
                                        						) k on j.Categoria_ID = k.Categoria_ID and k.Torneo_ID = 1
											where a.Equipo_ID = $Team
												and Estatus = 'A' 
											order by cast(Numero as decimal) asc, Nombre, Apellido_P, Apellido_M) a;";
				//$htmlTeam .= $sql2;
				$result2 = $Config->query($sql2);
				$htmlTeam .= '<div class="containerN d-block  d-xs-block d-md-block d-lg-block d-xl-block">
								<div class="row">';
						

				$count = 0;
				if ($result2->num_rows > 0) {
					// output data of each row
					while ($row2 = $result2->fetch_assoc()) {
					//	$Edad = $row2["Edad"];
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
																<td style="border-right: 1px solid #f9b500;">FP</td>
																<td style="padding-left: 10px;">' . $row2["FP"] . '</td>
															</tr>
															<tr>
																<td style="border-right: 1px solid #f9b500;">FT</td>
																<td style="padding-left: 10px;">' . $row2["FT"] . '</td>
															</tr>
															<tr>
																<td style="border-right: 1px solid #f9b500;">EX</td>
																<td style="padding-left: 10px;">' . $row2["EX"] . '</td>
															</tr>
															<tr>
																<td style="border-right: 1px solid #f9b500;"><img src="imagenes/Pointb1.png" width="20" height="20" alt=""/></td>
																<td style="padding-left: 10px;">' . $row2["Puntos1"] . '</td>
															</tr>
															<tr>
																<td style="border-right: 1px solid #f9b500;"><img src="imagenes/Pointb2.png" width="20" height="20" alt=""/></td>
																<td style="padding-left: 10px;">' . $row2["Puntos2"] . '</td>
															</tr>
															<tr>
																<td style="border-right: 1px solid #f9b500;"><img src="imagenes/Pointb3.png" width="20" height="20" alt=""/></td>
																<td style="padding-left: 10px;">' . $row2["Puntos3"] . '</td>
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