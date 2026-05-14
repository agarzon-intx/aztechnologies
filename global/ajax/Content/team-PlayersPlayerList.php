            <?php
				$htmlTeam .= '<div class="d-none  d-xs-none d-md-none d-lg-block d-xl-block"><div class="card">
								<div class="table-responsive">
									<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
										<thead class="">
											<th scope="col" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"></th>';
				$htmlTeam .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">' . $lang['372'] . '</th>';
				$htmlTeam .= '<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">' . $lang['373'] . '</th>';
				$htmlTeam .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No.</th>';
				$htmlTeam .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['376'] . '</th>';
				$htmlTeam .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['377'] . '</th>';
				$htmlTeam .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['379'] . '</th>';
				$htmlTeam .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['380'] . '</th>';
				$htmlTeam .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;"><img src="imagenes/goal.png" width="20" height="20" alt=""/></th>';
				$htmlTeam .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;"><img src="imagenes/amarilla.png" width="20" height="20" alt=""/></th>';
				$htmlTeam .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;"><img src="imagenes/roja.png" width="20" height="20" alt=""/></th>';
				$htmlTeam .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;"><img src="imagenes/gamePlayed.png" width="20" height="20" alt=""/></th>';
				$htmlTeam .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">%</th>';
				$htmlTeam .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;"></th>';
				$htmlTeam .= '</thead>';
				$htmlTeam .= '<tbody>';
				
				
				$apellidos = 'Apellido_P, 
							  Apellido_M,';
				if($Config->jugadoresApellidos1){
				    $apellidos = '  SUBSTRING(Apellido_P, 1, 1) Apellido_P, 
							        SUBSTRING(Apellido_M, 1, 1) Apellido_M,';
				}
				
				$sql2 = "SET @rank:=0;";
				$Config->query($sql2);
				$sql2 = "select @rank:=@rank+1 AS rank, a.* 
						 from (SELECT distinct a.Jugador_ID,
										Numero,
										Clave,
										Apodo,
										Nombre,
										$apellidos
										year(a.Fecha_Nacimiento) Fecha_Nacimiento,
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
										Sexo,
										ifnull(Goles,0) Goles, 
										ifnull(Amarillas,0) Amarillas, 
										ifnull(Rojas,0) Rojas,
										ifnull(Juegos,0) Juegos, 
										floor(ifnull((Juegos/(select Jornadas from $schema.Torneos where Torneo_ID = $Season))*100,0)) '%',
										case when ISNULL(g.Color_HEX) then '' ELSE concat('background: ', g.Color_HEX, ';') END Color_HEX,
										year(now())-year(a.Fecha_Nacimiento) Edad, 
										CASE WHEN j.Categoria_ID <> ifnull(h.Categoria_ID,-1) and case 
																									when month(a.Fecha_Nacimiento) < 8 then 
																										year(now())-year(a.Fecha_Nacimiento)+1 
																									else 
																										year(now())-year(a.Fecha_Nacimiento) 
																									end > j.Edad_Final 
												THEN '' 
											ELSE '' END strikei, 
										CASE WHEN j.Categoria_ID <> ifnull(h.Categoria_ID,-1) and case 
																									when month(a.Fecha_Nacimiento) < 8 then 
																										year(now())-year(a.Fecha_Nacimiento)+1 
																									else 
																										year(now())-year(a.Fecha_Nacimiento) 
																									end > j.Edad_Final 
												THEN '' ELSE '' END strikef 
									FROM $schema.Jugadores a
										LEFT OUTER JOIN (select Jugador_ID, sum(Goles) Goles from $schema.Goles where Torneo_ID = $Season group by Jugador_ID) b on a.Jugador_ID = b.Jugador_ID
										LEFT OUTER JOIN (select Jugador_ID, sum(Cantidad) Amarillas from $schema.Amonestados where Torneo_ID = $Season group by Jugador_ID) c on a.Jugador_ID = c.Jugador_ID
										LEFT OUTER JOIN (select Jugador_ID, sum(Cantidad) Rojas from $schema.Expulsados where Torneo_ID = $Season group by Jugador_ID) d on a.Jugador_ID = d.Jugador_ID
										LEFT OUTER JOIN (select Jugador_ID, sum(Jugado) Juegos from $schema.JugadorJugado where Torneo_ID = $Season group by Jugador_ID) e on a.Jugador_ID = e.Jugador_ID 
										LEFT OUTER JOIN $schema.Range_Age f on f.Range_Active = 1 and f.Range_Id <> 1 and year(now())-year(a.Fecha_Nacimiento) between f.Range_Start and f.Range_End
										LEFT OUTER JOIN $schema.Colores g on f.Range_Color_ID = g.Color_ID
										LEFT OUTER JOIN $schema.Categorias h on year(now())-year(a.Fecha_Nacimiento) between h.Edad_Inicial and h.Edad_Final
										JOIN $schema.Equipos i ON a.Equipo_ID = i.Equipo_ID and i.Torneo_ID = $Season
										JOIN $schema.Categorias j ON i.Fuerza = j.Categoria_ID 
									where a.Equipo_ID = $Team
										and Estatus = 'A' 
									order by Jugador_tipo, cast(Numero as decimal) asc, Nombre, Apellido_P, Apellido_M) a;";
				//$htmlTeam .= $sql2;
				$result2 = $Config->query($sql2);
				
				 $count = 0;
				 if ($result2->num_rows > 0) {
					// output data of each row
					while($row2 = $result2->fetch_assoc()) {
						if (($count % 2) == 1){
							$htmlTeam .= "<tr>";
						}else{
							$htmlTeam .= "<tr class='alt'>";
						}
						$htmlTeam .=  '<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["rank"] . $row2["strikef"] . '</span></td>
										<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["Apodo"] . $row2["strikef"] . '</div></td>
										<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["Nombre"] . " " . $row2["Apellido_P"] . " " . $row2["Apellido_M"] . $row2["strikef"] . '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["Numero"] . $row2["strikef"] . '</span></td>
										<td scope="row" class="align-middle text-center" style="' . $row2["Color_HEX"] . '"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["Fecha_Nacimiento"] . $row2["strikef"] . '</span></td>';
						$color = "";
						if($row2["Sexo"] == 0){ 
							$color = "#4BE1DA";
						}else{
							$color = "#F95693";
						}
						$htmlTeam .=  '<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["Edad"] . $row2["strikef"] . '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["Validado"] . $row2["strikef"] . '</span></td>
										<td scope="row" class="align-middle text-center" style="text-align:center; background: ' . $color . '"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["SexoT"] . $row2["strikef"] .'</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["Goles"] . $row2["strikef"] . '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["Amarillas"] . $row2["strikef"] . '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["Rojas"] . $row2["strikef"] . '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["Juegos"] . $row2["strikef"] . '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["%"] . $row2["strikef"] . '</span></td>';
						if($Config->perfiljugador == 1){
        	                $htmlTeam .=  ' <td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal"><img src="imagenes/lupa.png" width="15" height="15" alt="" onclick="previewPlayerShow(' . $row2["Jugador_ID"] . ')"></span></td>';
        	            }else{
        	                $htmlTeam .=  ' <td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal"></span></td>';
        	            }
						$htmlTeam .=  '</tr>';
						$count++;
					}
				}
				$htmlTeam .= '</tbody>';
				$htmlTeam .= '</table>';
				$htmlTeam .= '</div>';

				$htmlTeam .= '</div>';

				$htmlTeam .= '</div>';
				
				
				
				$htmlTeam .= '<div class="d-none d-xs-none d-md-block d-lg-none d-xl-none"><div class="card">
								<div class="table-responsive">
									<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
										<thead class="">';
				$htmlTeam .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">' . $lang['372'] . '</th>';
				$htmlTeam .= '<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">' . $lang['373'] . '</th>';
				$htmlTeam .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding: 0.45rem 0.2rem;">' . $lang['376'] . '</th>';
				$htmlTeam .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding: 0.45rem 0.2rem;">' . $lang['377'] . '</th>';
				$htmlTeam .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding: 0.45rem 0.2rem;">' . $lang['380'] . '</th>';
				$htmlTeam .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding: 0.45rem 0.2rem;"><img src="imagenes/goal.png" width="20" height="20" alt=""/></th>';
				$htmlTeam .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding: 0.45rem 0.2rem;"><img src="imagenes/amarilla.png" width="20" height="20" alt=""/></th>';
				$htmlTeam .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding: 0.45rem 0.2rem;"><img src="imagenes/roja.png" width="20" height="20" alt=""/></th>';
				$htmlTeam .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding: 0.45rem 0.2rem;"><img src="imagenes/gamePlayed.png" width="20" height="20" alt=""/></th>';
				$htmlTeam .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding: 0.45rem 0.2rem;">%</th>';
				$htmlTeam .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding: 0.45rem 0.2rem;"></th>';
				$htmlTeam .= '</thead>';
				$htmlTeam .= '<tbody>';
				
				$sql2 = "SET @rank:=0;";
				$Config->query($sql2);
				$sql2 = "select @rank:=@rank+1 AS rank, a.* 
						 from (SELECT distinct a.Jugador_ID,
										Numero,
										Clave,
										Apodo,
										Nombre,
										$apellidos
										year(a.Fecha_Nacimiento) Fecha_Nacimiento,
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
										Sexo,
										ifnull(Goles,0) Goles, 
										ifnull(Amarillas,0) Amarillas, 
										ifnull(Rojas,0) Rojas,
										ifnull(Juegos,0) Juegos, 
										floor(ifnull((Juegos/(select Jornadas from $schema.Torneos where Torneo_ID = $Season))*100,0)) '%',
										case when ISNULL(g.Color_HEX) then '' ELSE concat('background: ', g.Color_HEX, ';') END Color_HEX,
										year(now())-year(a.Fecha_Nacimiento) Edad, 
										CASE WHEN j.Categoria_ID <> ifnull(h.Categoria_ID,-1) and case 
																									when month(a.Fecha_Nacimiento) < 8 then 
																										year(now())-year(a.Fecha_Nacimiento)+1 
																									else 
																										year(now())-year(a.Fecha_Nacimiento) 
																									end > j.Edad_Final 
												THEN '' 
											ELSE '' END strikei, 
										CASE WHEN j.Categoria_ID <> ifnull(h.Categoria_ID,-1) and case 
																									when month(a.Fecha_Nacimiento) < 8 then 
																										year(now())-year(a.Fecha_Nacimiento)+1 
																									else 
																										year(now())-year(a.Fecha_Nacimiento) 
																									end > j.Edad_Final 
												THEN '' ELSE '' END strikef 
									FROM $schema.Jugadores a
										LEFT OUTER JOIN (select Jugador_ID, sum(Goles) Goles from $schema.Goles where Torneo_ID = $Season group by Jugador_ID) b on a.Jugador_ID = b.Jugador_ID
										LEFT OUTER JOIN (select Jugador_ID, sum(Cantidad) Amarillas from $schema.Amonestados where Torneo_ID = $Season group by Jugador_ID) c on a.Jugador_ID = c.Jugador_ID
										LEFT OUTER JOIN (select Jugador_ID, sum(Cantidad) Rojas from $schema.Expulsados where Torneo_ID = $Season group by Jugador_ID) d on a.Jugador_ID = d.Jugador_ID
										LEFT OUTER JOIN (select Jugador_ID, sum(Jugado) Juegos from $schema.JugadorJugado where Torneo_ID = $Season group by Jugador_ID) e on a.Jugador_ID = e.Jugador_ID 
										LEFT OUTER JOIN $schema.Range_Age f on f.Range_Active = 1 and f.Range_Id <> 1 and year(now())-year(a.Fecha_Nacimiento) between f.Range_Start and f.Range_End
										LEFT OUTER JOIN $schema.Colores g on f.Range_Color_ID = g.Color_ID
										LEFT OUTER JOIN $schema.Categorias h on year(now())-year(a.Fecha_Nacimiento) between h.Edad_Inicial and h.Edad_Final
										JOIN $schema.Equipos i ON a.Equipo_ID = i.Equipo_ID and i.Torneo_ID = $Season
										JOIN $schema.Categorias j ON i.Fuerza = j.Categoria_ID 
									where a.Equipo_ID = $Team
										and Estatus = 'A' 
									order by Jugador_tipo, cast(Numero as decimal) asc, Nombre, Apellido_P, Apellido_M) a;";
				//$htmlTeam .= $sql2;
				$result2 = $Config->query($sql2);
				
				 $count = 0;
				 if ($result2->num_rows > 0) {
					// output data of each row
					while($row2 = $result2->fetch_assoc()) {
						if (($count % 2) == 1){
							$htmlTeam .= "<tr>";
						}else{
							$htmlTeam .= "<tr class='alt'>";
						}
						$htmlTeam .=  '<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["Apodo"] . $row2["strikef"] . '</div></td>
										<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["Nombre"] . " " . $row2["Apellido_P"] . " " . $row2["Apellido_M"] . '(' . $row2["Numero"] . ')' . $row2["strikef"] . '</span></td>
										<td scope="row" class="align-middle text-center" style="' . $row2["Color_HEX"] . '"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["Fecha_Nacimiento"] . $row2["strikef"] . '</span></td>';
						$color = "";
						if($row2["Sexo"] == 0){ 
							$color = "#4BE1DA";
						}else{
							$color = "#F95693";
						}
						$htmlTeam .=  '<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["Edad"] . $row2["strikef"] . '</span></td>
										<td scope="row" class="align-middle text-center" style="text-align:center; background: ' . $color . '"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["SexoT"] . $row2["strikef"] .'</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["Goles"] . $row2["strikef"] . '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["Amarillas"] . $row2["strikef"] . '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["Rojas"] . $row2["strikef"] . '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["Juegos"] . $row2["strikef"] . '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["%"] . $row2["strikef"] . '</span></td>';
                        if($Config->perfiljugador == 1){
        	                $htmlTeam .=  '<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal"><img src="imagenes/lupa.png" width="15" height="15" alt="" onclick="previewPlayerShow(' . $row2["Jugador_ID"] . ')"></span></td>';
        	            }else{
        	                $htmlTeam .=  '<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal"></span></td>';
        	            }
						$htmlTeam .=  '</tr>';
						$count++;
					}
				}
				$htmlTeam .= '</tbody>';
				$htmlTeam .= '</table>';
				$htmlTeam .= '</div>
								</div>';

				$htmlTeam .= '</div>';
				
				$htmlTeam .= '<div class="d-block d-xs-block d-md-none d-lg-none d-xl-none"><div class="card">
								<div class="table-responsive">
									<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
										<thead class=""></thead>
										<tbody>';
				
				$sql2 = "SET @rank:=0;";
				$Config->query($sql2);
				$sql2 = "select @rank:=@rank+1 AS rank, a.* 
						 from (SELECT distinct a.Jugador_ID,
										Numero,
										Clave,
										Apodo,
										Nombre,
										$apellidos
										year(a.Fecha_Nacimiento) Fecha_Nacimiento,
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
										Sexo,
										ifnull(Goles,0) Goles, 
										ifnull(Amarillas,0) Amarillas, 
										ifnull(Rojas,0) Rojas,
										ifnull(Juegos,0) Juegos, 
										floor(ifnull((Juegos/(select Jornadas from $schema.Torneos where Torneo_ID = $Season))*100,0)) '%',
										case when ISNULL(g.Color_HEX) then '' ELSE concat('background: ', g.Color_HEX, ';') END Color_HEX,
										year(now())-year(a.Fecha_Nacimiento) Edad, 
										CASE WHEN j.Categoria_ID <> ifnull(h.Categoria_ID,-1) and case 
																									when month(a.Fecha_Nacimiento) < 8 then 
																										year(now())-year(a.Fecha_Nacimiento)+1 
																									else 
																										year(now())-year(a.Fecha_Nacimiento) 
																									end > j.Edad_Final 
												THEN '<strike>' 
											ELSE '' END strikei, 
										CASE WHEN j.Categoria_ID <> ifnull(h.Categoria_ID,-1) and case 
																									when month(a.Fecha_Nacimiento) < 8 then 
																										year(now())-year(a.Fecha_Nacimiento)+1 
																									else 
																										year(now())-year(a.Fecha_Nacimiento) 
																									end > j.Edad_Final 
												THEN '</strike>' ELSE '' END strikef 
									FROM $schema.Jugadores a
										LEFT OUTER JOIN (select Jugador_ID, sum(Goles) Goles from $schema.Goles where Torneo_ID = $Season group by Jugador_ID) b on a.Jugador_ID = b.Jugador_ID
										LEFT OUTER JOIN (select Jugador_ID, sum(Cantidad) Amarillas from $schema.Amonestados where Torneo_ID = $Season group by Jugador_ID) c on a.Jugador_ID = c.Jugador_ID
										LEFT OUTER JOIN (select Jugador_ID, sum(Cantidad) Rojas from $schema.Expulsados where Torneo_ID = $Season group by Jugador_ID) d on a.Jugador_ID = d.Jugador_ID
										LEFT OUTER JOIN (select Jugador_ID, sum(Jugado) Juegos from $schema.JugadorJugado where Torneo_ID = $Season group by Jugador_ID) e on a.Jugador_ID = e.Jugador_ID 
										LEFT OUTER JOIN $schema.Range_Age f on f.Range_Active = 1 and f.Range_Id <> 1 and year(now())-year(a.Fecha_Nacimiento) between f.Range_Start and f.Range_End
										LEFT OUTER JOIN $schema.Colores g on f.Range_Color_ID = g.Color_ID
										LEFT OUTER JOIN $schema.Categorias h on year(now())-year(a.Fecha_Nacimiento) between h.Edad_Inicial and h.Edad_Final
										JOIN $schema.Equipos i ON a.Equipo_ID = i.Equipo_ID and i.Torneo_ID = $Season
										JOIN $schema.Categorias j ON i.Fuerza = j.Categoria_ID 
									where a.Equipo_ID = $Team
										and Estatus = 'A' 
									order by Jugador_tipo, cast(Numero as decimal) asc, Nombre, Apellido_P, Apellido_M) a;";
				//$htmlTeam .= $sql2;
				$result2 = $Config->query($sql2);
				
				 $count = 0;
				 if ($result2->num_rows > 0) {
					// output data of each row
					while($row2 = $result2->fetch_assoc()) {
						if (($count % 2) == 1){
							$htmlTeam .= "<tr>";
						}else{
							$htmlTeam .= "<tr class='alt'>";
						}
						$color = "";
						if($row2["Sexo"] == 0){ 
							$color = "#4BE1DA";
						}else{
							$color = "#F95693";
						}
						
						$htmlTeam .=  '<td scope="row" class="align-middle text-center">
											<div class="d-flex px-0 py-1" style="justify-content: space-between">
												<div style="width: 60px;text-align: center;padding-top: 0px;">
													<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xxs font-weight-bold">' . $lang['372'] . '</span></p>
													<div class="d-flex px-0 py-0 lh-1">
														<div style="width: 100%;text-align: center;"><span class="text-secondary text-xxs font-weight-normal text-wrap" >' . $row2["strikei"] . $row2["Apodo"] . $row2["strikef"] . '</span></div>
													</div>
												</div>
												<div style="width: 45%;text-align: center;padding-top: 0px;">
													<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xxs font-weight-bold">' . $lang['373'] . '</span></p>
													<div class="d-flex px-0 py-0 lh-1">
														<div style="width: 100%;text-align: center;"><span class="text-secondary text-xxs font-weight-normal text-wrap" >' . $row2["strikei"] . $row2["Nombre"] . " " . $row2["Apellido_P"] . " " . $row2["Apellido_M"] . $row2["strikef"] . '</span></div>
													</div>
												</div>
												<div style="width: 25px;text-align: center;padding-top: 0px;">
													<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xxs font-weight-bold">No.</span></p>
													<div class="d-flex px-0 py-0 lh-1">
														<div style="width: 100%;text-align: center;"><span class="text-secondary text-xxs font-weight-normal text-wrap" >' . $row2["strikei"] . $row2["Numero"] . $row2["strikef"] . '</span></div>
													</div>
												</div>
												<div style="width: 45px;text-align: center;padding-top: 0px;">
													<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xxs font-weight-bold">' . $lang['379'] . '</span></p>
													<div class="d-flex px-0 py-0 lh-1">
														<div style="width: 100%;text-align: center;"><span class="text-secondary text-xxs font-weight-normal text-wrap">' . $row2["strikei"] . $row2["Validado"] . $row2["strikef"] . '</span></div>
													</div>
												</div>
											</div>
											<div class="d-flex px-0 py-1" style="justify-content: space-between">
												<div style="width: 60px;text-align: center;padding-top: 0px;">
													<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xxs font-weight-bold">' . $lang['376'] . '</span></p>
													<div class="d-flex px-0 py-0 lh-1">
														<div style="width: 100%;text-align: center; ' . $row2["Color_HEX"] . '"><span class="text-secondary text-xxs font-weight-normal text-wrap" >' . $row2["strikei"] . $row2["Fecha_Nacimiento"] . $row2["strikef"] . '</span></div>
													</div>
												</div>
												<div style="width: 30px;text-align: center;padding-top: 0px;">
													<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xxs font-weight-bold">' . $lang['377'] . '</span></p>
													<div class="d-flex px-0 py-0 lh-1">
														<div style="width: 100%;text-align: center;"><span class="text-secondary text-xxs font-weight-normal text-wrap" >' . $row2["strikei"] . $row2["Edad"] . $row2["strikef"] . '</span></div>
													</div>
												</div>
												<div style="width: 30px;text-align: center;padding-top: 0px;">
													<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xxs font-weight-bold">' . $lang['380'] . '</span></p>
													<div class="d-flex px-0 py-0 lh-1">
														<div style="width: 100%;text-align: center; background: ' . $color . '"><span class="text-secondary text-xxs font-weight-normal text-wrap" >' . $row2["strikei"] . $row2["SexoT"] . $row2["strikef"] . '</span></div>
													</div>
												</div>
												<div style="width: 22px;text-align: center;padding-top: 0px;">
													<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xxs font-weight-bold"><img src="imagenes/goal.png" width="20" height="20" alt=""/></span></p>
													<div class="d-flex px-0 py-0 lh-1">
														<div style="width: 100%;text-align: center;"><span class="text-secondary text-xxs font-weight-normal text-wrap">' . $row2["strikei"] . $row2["Goles"] . $row2["strikef"] . '</span></div>
													</div>
												</div>
												<div style="width: 22px;text-align: center;padding-top: 0px;">
													<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xxs font-weight-bold"><img src="imagenes/amarilla.png" width="20" height="20" alt=""/></span></p>
													<div class="d-flex px-0 py-0 lh-1">
														<div style="width: 100%;text-align: center;"><span class="text-secondary text-xxs font-weight-normal text-wrap">' . $row2["strikei"] . $row2["Amarillas"] . $row2["strikef"] . '</span></div>
													</div>
												</div>
												<div style="width: 22px;text-align: center;padding-top: 0px;">
													<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xxs font-weight-bold"><img src="imagenes/roja.png" width="20" height="20" alt=""/></span></p>
													<div class="d-flex px-0 py-0 lh-1">
														<div style="width: 100%;text-align: center;"><span class="text-secondary text-xxs font-weight-normal text-wrap">' . $row2["strikei"] . $row2["Rojas"] . $row2["strikef"] . '</span></div>
													</div>
												</div>
												<div style="width: 22px;text-align: center;padding-top: 0px;">
													<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xxs font-weight-bold"><img src="imagenes/gamePlayed.png" width="20" height="20" alt=""/></span></p>
													<div class="d-flex px-0 py-0 lh-1">
														<div style="width: 100%;text-align: center;"><span class="text-secondary text-xxs font-weight-normal text-wrap">' . $row2["strikei"] . $row2["Juegos"] . $row2["strikef"] . '</span></div>
													</div>
												</div>
												<div style="width: 22px;text-align: center;padding-top: 0px;">
													<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xxs font-weight-bold">%</span></p>
													<div class="d-flex px-0 py-0 lh-1">
														<div style="width: 100%;text-align: center;"><span class="text-secondary text-xxs font-weight-normal text-wrap">' . $row2["strikei"] . $row2["%"] . $row2["strikef"] . '</span></div>
													</div>
												</div>
												<div style="width: 22px;text-align: center;padding-top: 0px;">
													<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xxs font-weight-bold"></span></p>
													<div class="d-flex px-0 py-0 lh-1">
													    <div style="width: 100%;text-align: center;"><span class="text-secondary text-xxs font-weight-normal text-wrap">';
				                                        if($Config->perfiljugador == 1){
                                        	                $htmlTeam .=  '<img src="imagenes/lupa.png" width="15" height="15" alt="" onclick="previewPlayerShow(' . $row2["Jugador_ID"] . ')">';
                                        	            }
						$htmlTeam .=  '                     </span>
					                                    </div>
													</div>
												</div>
											</div>
										</div></td>';
						$htmlTeam .= '</tr>';
						$count++;
					}
				}
				$htmlTeam .= '</tbody>';
				$htmlTeam .= '</table>';
				$htmlTeam .= '</div>
								</div>';

				$htmlTeam .= '</div>';

				$htmlTeam .= '<div class="swal2-container swal2-center swal2-backdrop-show" style="overflow-y: auto; display: none;" id="jugador">
					<div aria-labelledby="swal2-title" aria-describedby="swal2-html-container" class="swal2-popup swal2-modal swal2-icon-info swal2-show" tabindex="-1" role="dialog" aria-live="assertive" aria-modal="true" style="display: grid; width: 41em;">
						<button type="button" class="swal2-close" aria-label="Close this dialog" style="display: flex;"  onclick="previewPlayerHide()">×</button>
						<div class="swal2-html-container" style="display: block; padding-top: 35px;" id="teamPlayerPreview">
						</div>
					</div>
				</div>';
				?>