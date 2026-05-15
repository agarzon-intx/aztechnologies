<?php
                $fecha = new DateTime();
				$htmlWeek .= '<div class="d-none d-xs-none d-md-block d-lg-block d-xl-block">
								<div class="card">
									<div class="table-responsive">
										<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
											<thead class="">';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['617'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-left text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['618'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['619'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['620'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['621'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['622'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['623'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['624'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['625'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['626'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7 lh-1" style="width: 85px;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['627'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7 lh-1" style="width: 50px;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['628'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;"><span>' . $lang['616'] . '  ' . $lang['629'] . '</span></th>';
				$htmlWeek .= '</thead>';
				$htmlWeek .= '<tbody>';
				
				
				$sqlPenales1 = "when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1  then 1";
				$sqlPenales12 = "when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 then 1";
				$sqlPenales2 = "0";
				$sqlPenales3 = "when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 then 1";
				$sqlPenales4 = "0";
				$sqlPenales5 = "when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 then 1 ";
				$sqlByeWeekPoints1 = "";
				$sqlByeWeekPoints2 = "";
				$sqlByeWeekPoints3 = "0 ";
				if($Config->EmpatesPenalesFlag == 1){
					$sqlPenales1 = "when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado =1 then 1 ";
					$sqlPenales12 = "when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 then 1 ";
					$sqlPenales2 = "case 
								when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local > l.Penal_Visitante and l.Jugado = 1 then 1
								else 0
							end as ";
					$sqlPenales3 = "when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local > l.Penal_Visitante and l.Jugado =1 then 2
								when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local < l.Penal_Visitante and l.Jugado = 1 then 1 ";
					$sqlPenales4 = "case 
								when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local < v.Penal_Visitante and v.Jugado = 1 then 1
								else 0
							end as ";
					$sqlPenales5 = "when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local < v.Penal_Visitante and v.Jugado =1 then 2 
								when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local > v.Penal_Visitante and v.Jugado = 1 then 1 ";
				}
				if($Config->ByeWeekPoints == 1){
					$sqlByeWeekPoints1 = "when Equipo_ID = l.Local_ID and l.Visitante_ID is null then 3 ";
					$sqlByeWeekPoints2 = "when Equipo_ID = l.Local_ID and l.Visitante_ID is null then 1 ";
					$sqlByeWeekPoints3 = "" . $Config->ByeWeekPointsGoals . " ";
				}
				$sqlPTSL = "";
				$sqlJGL = "";
				$sqlJEL = "";
				$sqlJPL = "";
				$sqlJJL = "";
				$sqlPTSV = "";
				$sqlJGV = "";
				$sqlJEV = "";
				$sqlJPV = "";
				$sqlJJV = "";
				
                $sql20 = "  SELECT * 
                            FROM (
                            		SELECT * FROM $schema.Juego_Estatus) a
                            order by Juego_Estatus_ID;";
    			$result20 = $Config->query($sql20);
                if ($result20->num_rows > 0) {
    				// output data of each row
    				while($row20 = $result20->fetch_assoc()) {
    					$sqlPTSL .= " when l.Jugado = " . $row20["Juego_Estatus_ID"] . " then " . $row20["PTSL"] . " ";
        				$sqlJGL .=  " when l.Jugado = " . $row20["Juego_Estatus_ID"] . " then " . $row20["JGL"] . " ";
        				$sqlJEL .=  " when l.Jugado = " . $row20["Juego_Estatus_ID"] . " then " . $row20["JEL"] . " ";
        				$sqlJPL .=  " when l.Jugado = " . $row20["Juego_Estatus_ID"] . " then " . $row20["JPL"] . " ";
        				$sqlJJL .=  " when l.Jugado = " . $row20["Juego_Estatus_ID"] . " then " . $row20["JJL"] . " ";
        				$sqlPTSV .= " when v.Jugado = " . $row20["Juego_Estatus_ID"] . " then " . $row20["PTSV"] . " ";
        				$sqlJGV .=  " when v.Jugado = " . $row20["Juego_Estatus_ID"] . " then " . $row20["JGV"] . " ";
        				$sqlJEV .=  " when v.Jugado = " . $row20["Juego_Estatus_ID"] . " then " . $row20["JEV"] . " ";
        				$sqlJPV .=  " when v.Jugado = " . $row20["Juego_Estatus_ID"] . " then " . $row20["JPV"] . " ";
        				$sqlJJV .=  " when v.Jugado = " . $row20["Juego_Estatus_ID"] . " then " . $row20["JJV"] . " ";
    				}
                }
                /*
                echo $sqlPTSL;
                echo $sqlJGL;
                echo $sqlJEL;
                echo $sqlJPL;
                echo $sqlJJL;
                echo $sqlPTSV;
                echo $sqlJGV;
                echo $sqlJEV;
                echo $sqlJPV;
                echo $sqlJJV;
                */
                
				$sql2 = "SET @rank:=0;";
				$Config->query($sql2);
				$sql2 = "SELECT @rank:=@rank+1 AS rank, Logo, Equipo_DESC, JJ, JG, JE, JP, GF, GC, DIFF, Puntos, Reales, last5, Extra
						from (
								Select 	Logo, 
										j.Equipo_ID, 
										Equipo_DESC, 
										fuerza, 
										ifnull(sum(Juegos), 0) as JJ, 
										ifnull(sum(JG), 0) as JG, 
										ifnull(sum(JE), 0) as JE, 
										ifnull(sum(JP), 0) as JP, 
										ifnull(sum(Puntos), 0) as Puntos, 
										ifnull(sum(Puntos), 0)+ifnull(Sum(Extra), 0)+ifnull(sum(ExtraEquipo), 0) as Reales, 
										ifnull(Sum(GF), 0) as GF, 
										ifnull(Sum(GC), 0) as GC, 
										ifnull(Sum(GF), 0) - ifnull(Sum(GC), 0) as DIFF, 
										ifnull(Sum(Extra), 0) Extra, 
										ifnull(last5,' - - ') last5, 
										j.Juego_ID
								from (
										select distinct concat(e.Torneo_ID,'-', e.Equipo_ID) Logo, 
												l.Jornada_ID, 
												Equipo_ID, 
												Equipo_DESC, 
												Fuerza, 
												Juego_ID,
												case  
													" . $sqlByeWeekPoints1 . "
													when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 then 3 
													" . $sqlPenales1 . "
													" . $sqlPTSL . "
													else 0
												end as Puntos,
												" . $sqlPenales2 . " Extra,
												case 
													" . $sqlByeWeekPoints1 . "
													when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante then 3 
													" . $sqlPenales3 . "
													" . $sqlPTSL . "
													else 0
												end + l.Extra_Local as Reales, 
												case 
													when l.Visitante_ID is not null then Gol_Local
													else " . $sqlByeWeekPoints3 . "
												end as GF, 
												Gol_Visitante as GC,
												case 
													" . $sqlByeWeekPoints2 . "
													when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 then 1
													when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 then 1
													when Equipo_ID = l.Local_ID and l.Gol_Local < l.Gol_Visitante and l.Jugado = 1 then 1
													" . $sqlJJL . "
													else 
														case when l.Estatus like '5' then 1 else 0 end
												end as Juegos,
												case 
													" . $sqlByeWeekPoints2 . "
													when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 then 1
													" . $sqlJGL . "
													else 0
												end as JG,
												case
													when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 then 1
												    " . $sqlJEL . "
													else 0
												end as JE,
												case 
													when Equipo_ID = l.Local_ID and l.Gol_Local < l.Gol_Visitante and l.Jugado = 1 then 1
													" . $sqlJPL . "
													else 0
												end as JP, 
												l.Extra_Local ExtraEquipo
										from $schema.Equipos e
											left outer join $schema.Juegos l on e.Equipo_ID = l.Local_ID  
																					and e.Torneo_ID = $Season 
																					and l.Torneo_ID = $Season
																					and Equipo_ID > 0
																					and l.Jugado <> 10
                                            join $schema.Categorias lc on e.Fuerza = lc.Categoria_ID
                                            join $schema.Jornada lj on l.Jornada_ID = lj.Jornada_ID 
                                                                                        and lj.Jornada_Type = 1 
                                                                                        and lc.Calendario_ID = lj.Calendario_ID
										where e.Fuerza = $Category and e.Torneo_ID = $Season and l.Fecha between ( SELECT min(Fecha_Inicio)
                                                                                                                   FROM   $schema.Jornada
                                                                                                                   WHERE  Fecha_Inicio <= CURDATE()
                                                                                                                   AND    Torneo_ID = $Season) and (SELECT min(Fecha_Fin) Fecha_Fin
                                                                                                                                                   FROM   $schema.Jornada
                                                                                                                                                   WHERE  CURDATE() < Fecha_Fin
                                                                                                                                                    AND    Torneo_ID = $Season)
										UNION
										select distinct concat(e.Torneo_ID,'-', e.Equipo_ID) Logo, 
												v.Jornada_ID, 
												Equipo_ID, 
												Equipo_DESC, 
												Fuerza, 
												Juego_ID,
												case 
													when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 3 
													" . $sqlPenales12 . "
													" . $sqlPTSV  . "
													else 0
												end as Puntos, 
												" . $sqlPenales4 . " Extra, 
												case 
													when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 3 
													" . $sqlPenales5 . "
													" . $sqlPTSV . "
													else 0
												end + v.Extra_Visitante as Reales, 
												Gol_Visitante as GF, 
												Gol_Local as GC,
												case 
													when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 1
													when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 then 1
													when Equipo_ID = v.Visitante_ID and v.Gol_Local > v.Gol_Visitante and v.Jugado = 1 then 1 
													" . $sqlJJV . "
													else 
														case when v.Estatus like '5' then 1 else 0 end
												end as Juegos ,
												case 
													when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 1
													" . $sqlJGV . "
													else 0
												end as JG,
												case 
													when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 then 1 
													" . $sqlJEV . "
													else 0
												end as JE,
												case 
													when Equipo_ID = v.Visitante_ID and v.Gol_Local > v.Gol_Visitante and v.Jugado = 1 then 1 
													" . $sqlJPV . "
													else 0
												end
											as JP, v.Extra_Visitante ExtraEquipo
										from $schema.Equipos e
											left outer join $schema.Juegos v on e.Equipo_ID = v.Visitante_ID 
																						and e.Torneo_ID = $Season 
																						and v.Torneo_ID = $Season
																						and Equipo_ID > 0
																						and v.Jugado <> 10
                                            join $schema.Categorias vc on e.Fuerza = vc.Categoria_ID
                                            join $schema.Jornada vj on v.Jornada_ID = vj.Jornada_ID 
                                                                                        and vj.Jornada_Type = 1 
                                                                                        and vc.Calendario_ID = vj.Calendario_ID
										where e.Fuerza = $Category and e.Torneo_ID = $Season and v.Fecha between ( SELECT min(Fecha_Inicio)
                                                                                                                   FROM   $schema.Jornada
                                                                                                                   WHERE  Fecha_Inicio <= CURDATE()
                                                                                                                   AND    Torneo_ID = $Season) and (SELECT min(Fecha_Fin) Fecha_Fin
                                                                                                                                                   FROM   $schema.Jornada
                                                                                                                                                   WHERE  CURDATE() < Fecha_Fin
                                                                                                                                                    AND    Torneo_ID = $Season)) j
									left outer join (
										select Equipo_ID, 
												concat(CAST(sum(JG) AS char(20)),'-',CAST(sum(JP) AS char(20)),'-', CAST(sum(JE) AS char(20))) 'last5', 
												Juego_ID 
										from (	select l.Jornada_ID, 
													Equipo_ID, 
													Juego_ID,
													case 
														when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 then 1
														else 0
													end as JG,
													case 
														when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 then 1
														else 0
													end as JE,
													case 
														when l.Jugado = 2 then 1
														when Equipo_ID = l.Local_ID and l.Gol_Local < l.Gol_Visitante and l.Jugado = 1 then 1
														else 0
													end as JP
												from $schema.Equipos e
													left outer join $schema.Juegos l on e.Equipo_ID = l.Local_ID and e.Torneo_ID = $Season and l.Torneo_ID = $Season
													JOIN (		SELECT fecha FROM $schema.Jornada
																		where Torneo_ID = $Season
																			and Fecha <= (select Fecha FROM $schema.Jornada where Jornada_ID = $Week and Torneo_ID = $Season)
																			and Jornada_Desc not like '%inales%'
																		order by Fecha desc 
																		limit 5) l5 on l.fecha = l5.fecha
												UNION
												select distinct v.Jornada_ID, Equipo_ID, Juego_ID,
													case 
														when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 1
														else 0
													end as JG,
													case 
														when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 then 1 
														else 0
													end as JE,
													case 
														when v.Jugado = 2 then 1
														when Equipo_ID = v.Visitante_ID and v.Gol_Local > v.Gol_Visitante and v.Jugado = 1 then 1 
														else 0
													end as JP
												from $schema.Equipos e
													left outer join $schema.Juegos v on e.Equipo_ID = v.Visitante_ID and e.Torneo_ID = $Season and v.Torneo_ID = $Season
													JOIN (		SELECT fecha FROM $schema.Jornada
																		where Torneo_ID = $Season
																			and Fecha <= (select Fecha FROM $schema.Jornada where Jornada_ID = $Week and Torneo_ID = $Season)
																			and Jornada_Desc not like '%inales%'
																		order by Fecha desc 
																		limit 5) l5 on v.fecha = l5.fecha
												) j
										group by Equipo_ID
										) f on j.Equipo_ID = f.Equipo_ID
								where Fuerza = $Category
								Group by j.Equipo_ID, Equipo_DESC, Fuerza
								order by sum(Puntos)+Sum(Extra)+sum(ExtraEquipo) desc, Sum(GF) - Sum(GC) desc, Sum(GF) desc, Equipo_DESC) jj";
				//$htmlWeek .= $sql2;
				$result2 = $Config->query($sql2);
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
							
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["rank"]. '</span></td>
										<td scope="row" class="align-middle text-right"><div class="d-flex px-0 py-1"><div style="width: 40px;text-align: right;padding-right: 3px;"><img src="imagenes/' . $row2["Logo"]. '.png?tmp=' . $fecha->getTimestamp() . '" class="avatar avatar-sm me-0" style="border-radius: 0rem !important;"></div><div style="width: 100px;text-align: left;padding-right: 3px;padding-top: 6px;"><span class="text-secondary text-xs font-weight-normal">' . $row2["Equipo_DESC"]. '</span></div></div></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["JJ"]. '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["JG"]. '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["JE"]. '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["JP"]. '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["GF"]. '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["GC"]. '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["DIFF"]. '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["Puntos"]. '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["Extra"]. '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["Reales"]. '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["last5"]. '</span></td>';
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
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.15rem 0.1rem;">' . $lang['620'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.15rem 0.1rem;">' . $lang['621'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.15rem 0.1rem;">' . $lang['622'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.15rem 0.1rem;">' . $lang['623'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.15rem 0.1rem;">' . $lang['624'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.15rem 0.1rem;">' . $lang['625'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.15rem 0.1rem;">' . $lang['626'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.15rem 0.1rem;">' . $lang['628-1'] . '</th>';
				$htmlWeek .= '</thead>';
				$htmlWeek .= '<tbody>';

				$sql3 = "SET @rank:=0;";
				$Config->query($sql3);
				
				$result2 = $Config->query($sql2);
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
										<td scope="row" class="align-middle text-right" style="padding: 0.15rem 0.1rem;"><div class="d-flex px-0 py-1"><div style="width: 40px;text-align: right;padding-right: 3px;"><img src="imagenes/' . $row2["Logo"]. '.png?tmp=' . $fecha->getTimestamp() . '" class="avatar avatar-sm me-0" style="border-radius: 0rem !important;"></div></div></td>
										<td scope="row" class="align-middle text-center" style="padding: 0.15rem 0.1rem;"><span class="text-secondary text-xs font-weight-normal">' . $row2["JJ"]. '</span></td>
										<td scope="row" class="align-middle text-center" style="padding: 0.15rem 0.1rem;"><span class="text-secondary text-xs font-weight-normal">' . $row2["JG"]. '</span></td>
										<td scope="row" class="align-middle text-center" style="padding: 0.15rem 0.1rem;"><span class="text-secondary text-xs font-weight-normal">' . $row2["JE"]. '</span></td>
										<td scope="row" class="align-middle text-center" style="padding: 0.15rem 0.1rem;"><span class="text-secondary text-xs font-weight-normal">' . $row2["JP"]. '</span></td>
										<td scope="row" class="align-middle text-center" style="padding: 0.15rem 0.1rem;"><span class="text-secondary text-xs font-weight-normal">' . $row2["GF"]. '</span></td>
										<td scope="row" class="align-middle text-center" style="padding: 0.15rem 0.1rem;"><span class="text-secondary text-xs font-weight-normal">' . $row2["GC"]. '</span></td>
										<td scope="row" class="align-middle text-center" style="padding: 0.15rem 0.1rem;"><span class="text-secondary text-xs font-weight-normal">' . $row2["DIFF"]. '</span></td>
										<td scope="row" class="align-middle text-center" style="padding: 0.15rem 0.1rem;"><span class="text-secondary text-xs font-weight-normal">' . $row2["Puntos"]. '</span></td>
										<td scope="row" class="align-middle text-center" style="padding: 0.15rem 0.1rem;"><span class="text-secondary text-xs font-weight-normal">' . $row2["Reales"]. '</span></td>';
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