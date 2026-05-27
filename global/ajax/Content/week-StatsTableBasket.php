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
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['622'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['623-V'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['624-V'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['625'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['626'] . '</th>';
				//$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7 lh-1" style="width: 85px;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['627'] . '</th>';
				//$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7 lh-1" style="width: 50px;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['628'] . '</th>';
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
				if($Config->EmpatesPenales == ""){
					$sqlPenales1 = "when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local <> l.Penal_Visitante and l.Jugado =1 then 1 ";
					$sqlPenales2 = "case 
								when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local > l.Penal_Visitante and l.Jugado =1 then 1
								else 0
							end as ";
					$sqlPenales3 = "when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local > l.Penal_Visitante and l.Jugado =1 then 2
								when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local < l.Penal_Visitante and l.Jugado =1 then 1 ";
					$sqlPenales4 = "case 
								when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local < v.Penal_Visitante and v.Jugado =1 then 1
								else 0
							end as ";
					$sqlPenales5 = "when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local < v.Penal_Visitante and v.Jugado =1 then 2 
								when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local > v.Penal_Visitante and v.Jugado =1 then 1 ";
				}
				if($Config->ByeWeekPoints == 1){
					$sqlByeWeekPoints1 = "when Equipo_ID = l.Local_ID and l.Visitante_ID is null then 3 ";
					$sqlByeWeekPoints2 = "when Equipo_ID = l.Local_ID and l.Visitante_ID is null then 1 ";
					$sqlByeWeekPoints3 = "" . $Config->ByeWeekPointsGoals . " ";
				}

				$sql2 = "SET @rank:=0;";
				$Config->query($sql2);
				$sql21 = "SELECT 
                  @rank := @rank + 1 AS rank, 
                  Equipo_ID, 
                  Logo, 
                  Equipo_DESC, 
                  JJ, 
                  JG, 
                  JP, 
                  GF, 
                  GC, 
                  ROUND(PF - PC, 0) as DIFF, 
                  Puntos, 
                  ROUND(PF, 0) as PF, 
                  ROUND(PC, 0) as PC, 
                  CASE WHEN PF = 0 THEN ROUND(0.000, 3) WHEN PC = 0 THEN ROUND(PF / 1, 3) ELSE ROUND(PF / PC, 3) END AS CP, 
                  ROUND(SF, 0) as SF, 
                  ROUND(SC, 0) as SC, 
                  CASE WHEN SF = 0 THEN ROUND(0.000, 3) WHEN SC = 0 THEN ROUND(SF / 1, 3) ELSE ROUND(SF / SC, 3) END AS CS 
                from 
                  (
                    Select Logo, j.Equipo_ID, Equipo_DESC, fuerza, 
                		ifnull(sum(Juegos), 0) as JJ, 
                		ifnull(sum(JG), 0) as JG, ifnull(sum(JP), 0) as JP, ifnull(sum(Puntos), 0) + ifnull(sum(Extra), 0) as Puntos, 
                		ifnull(Sum(GF), 0) as GF, ifnull(Sum(GC), 0) as GC, ifnull(Sum(GF), 0) - ifnull(Sum(GC), 0) as DIFF, 
                		IFNULL(Sum(PF), 0.001) AS PF, IFNULL(Sum(PC), 0.001) AS PC, IFNULL(Sum(SF), 0.001) AS SF, IFNULL(Sum(SC), 0.001) AS SC, 
                		j.Juego_ID 
                    from 
                      (
                		select 
                			distinct concat(e.Torneo_ID, '-', e.Equipo_ID) Logo, 
                			s.Jornada_ID, Equipo_ID, Equipo_DESC, Fuerza, s.Juego_ID, 
                			s.SF, s.SC, s.PF, s.PC, 
                			case when Equipo_ID = s.Local_ID and s.PL > s.PV  and s.Jugado = 1 then 2 
                				when Equipo_ID = s.Local_ID and s.PL < s.PV and s.Jugado = 1 then 1 
                			end as Puntos, s.Extra, 
                			case when s.Visitante_ID is not null then s.PL else 0 end as GF, 
                			s.PV as GC, s.Juegos, 
                			case when Equipo_ID = s.Local_ID and s.PL > s.PV and s.Jugado = 1 then 1 else 0 end as JG, 
                			case when s.Jugado = 2 then 1 when Equipo_ID = s.Local_ID and s.PL < s.PV and s.Jugado = 1 then 1 else 0 end as JP, 
                			s.ExtraEquipo,
                			s.Local_ID, s.PL, s.PV, s.Jugado
                		from 
                			$schema.Equipos e 
                			left outer join (
                						select distinct l.Jornada_ID, l.Juego_ID, 
                							s.SL SF, s.SV SC, s.PL PF, s.PV PC, l.Local_ID, s.PL, s.PV, l.Jugado, l.Visitante_ID, 
                							l.Extra_Local Extra, s.PV as GC, 
                							case when l.Jugado = 1 then 1 when l.Jugado = 2 then 1 when l.Jugado = 0 then 0 end as Juegos, 
                							l.Extra_Local ExtraEquipo 
                						from $schema.Juegos l 
                							left outer join (
                									select 
                										  Juego_ID, 
                										  case when ifnull(s1.Set1_L, 0) > ifnull(s1.Set1_V, 0) then 1 else 0 end + case when ifnull(s1.Set2_L, 0) > ifnull(s1.Set2_V, 0) then 1 else 0 end + case when ifnull(s1.Set3_L, 0) > ifnull(s1.Set3_V, 0) then 1 else 0 end + case when ifnull(s1.Set4_L, 0) > ifnull(s1.Set4_V, 0) then 1 else 0 end + case when ifnull(s1.Set5_L, 0) > ifnull(s1.Set5_V, 0) then 1 else 0 end SL, 
                										  case when ifnull(s1.Set1_V, 0) > ifnull(s1.Set1_L, 0) then 1 else 0 end + case when ifnull(s1.Set2_V, 0) > ifnull(s1.Set2_L, 0) then 1 else 0 end + case when ifnull(s1.Set3_V, 0) > ifnull(s1.Set3_L, 0) then 1 else 0 end + case when ifnull(s1.Set4_V, 0) > ifnull(s1.Set4_L, 0) then 1 else 0 end + case when ifnull(s1.Set5_V, 0) > ifnull(s1.Set5_L, 0) then 1 else 0 end SV, 
                										  ifnull(s1.Set1_L, 0) + ifnull(s1.Set2_L, 0) + ifnull(s1.Set3_L, 0) + ifnull(s1.Set4_L, 0) + ifnull(s1.Set5_L, 0) PL, 
                										  ifnull(s1.Set1_V, 0) + ifnull(s1.Set2_V, 0) + ifnull(s1.Set3_V, 0) + ifnull(s1.Set4_V, 0) + ifnull(s1.Set5_V, 0) PV 
                									from 
                									  $schema.Juegos_Set s1
                								  ) s on l.Juego_ID = s.Juego_ID 
                						where l.Fecha between (
                									SELECT min(Fecha_Inicio) 
                									FROM $schema.Jornada 
                									WHERE Torneo_ID = $Season AND Jornada_Type = 1
                								  ) 
                								  and (
                									SELECT max(Fecha_Fin) 
                									FROM $schema.Jornada 
                									WHERE Torneo_ID = $Season AND Jornada_Type = 1
                								  )
                				  ) s on e.Equipo_ID = s.Local_ID 
                			join $schema.Categorias lc on e.Fuerza = lc.Categoria_ID 
                			left outer join $schema.Jornada lj on s.Jornada_ID = lj.Jornada_ID and lj.Jornada_Type = 1 and lc.Calendario_ID = lj.Calendario_ID 
                		where 
                			e.Fuerza = $Category and e.Torneo_ID = $Season and e.Activo = 1
                		UNION 
                		select 
                			distinct concat(e.Torneo_ID, '-', e.Equipo_ID) Logo, 
                			s.Jornada_ID, Equipo_ID, Equipo_DESC, Fuerza, s.Juego_ID, 
                			s.SF, s.SC, s.PF, s.PC, 
                			case when Equipo_ID = s.Visitante_ID and s.PL < s.PV and s.Jugado = 1 then 2
                				when Equipo_ID = s.Visitante_ID  and s.PL > s.PV and s.Jugado = 1 then 1 end as Puntos, s.Extra, 
                			case when s.Visitante_ID is not null then s.PL else 0 end as GF, 
                			s.PV as GC, s.Juegos, 
                			case when Equipo_ID = s.Visitante_ID and s.PL < s.PV and s.Jugado = 1 then 1 else 0 end as JG, 
                            case when Equipo_ID = s.Visitante_ID and s.PL > s.PV and s.Jugado = 1 then 1 when s.Jugado = 2 then 1 else 0 end as JP, 
                			s.ExtraEquipo,
                			s.Local_ID, s.PL, s.PV, s.Jugado
                		from 
                			$schema.Equipos e 
                			left outer join (
                						select distinct v.Jornada_ID, v.Juego_ID, 
                							s.SL SF, s.SV SC, s.PL PF, s.PV PC, v.Local_ID, s.PL, s.PV, v.Jugado, v.Visitante_ID, 
                							v.Extra_Visitante Extra, s.PV as GC, 
                							case when v.Jugado = 1 then 1 when v.Jugado = 2 then 1 when v.Jugado = 0 then 0 end as Juegos, 
                							v.Extra_Visitante ExtraEquipo 
                						from $schema.Juegos v 
                							left outer join (
                									select 
                										  Juego_ID, 
                										  case when ifnull(s1.Set1_L, 0) > ifnull(s1.Set1_V, 0) then 1 else 0 end + case when ifnull(s1.Set2_L, 0) > ifnull(s1.Set2_V, 0) then 1 else 0 end + case when ifnull(s1.Set3_L, 0) > ifnull(s1.Set3_V, 0) then 1 else 0 end + case when ifnull(s1.Set4_L, 0) > ifnull(s1.Set4_V, 0) then 1 else 0 end + case when ifnull(s1.Set5_L, 0) > ifnull(s1.Set5_V, 0) then 1 else 0 end SL, 
                										  case when ifnull(s1.Set1_V, 0) > ifnull(s1.Set1_L, 0) then 1 else 0 end + case when ifnull(s1.Set2_V, 0) > ifnull(s1.Set2_L, 0) then 1 else 0 end + case when ifnull(s1.Set3_V, 0) > ifnull(s1.Set3_L, 0) then 1 else 0 end + case when ifnull(s1.Set4_V, 0) > ifnull(s1.Set4_L, 0) then 1 else 0 end + case when ifnull(s1.Set5_V, 0) > ifnull(s1.Set5_L, 0) then 1 else 0 end SV, 
                										  ifnull(s1.Set1_L, 0) + ifnull(s1.Set2_L, 0) + ifnull(s1.Set3_L, 0) + ifnull(s1.Set4_L, 0) + ifnull(s1.Set5_L, 0) PL, 
                										  ifnull(s1.Set1_V, 0) + ifnull(s1.Set2_V, 0) + ifnull(s1.Set3_V, 0) + ifnull(s1.Set4_V, 0) + ifnull(s1.Set5_V, 0) PV 
                									from 
                									  $schema.Juegos_Set s1
                								  ) s on v.Juego_ID = s.Juego_ID 
                						where v.Fecha between (
                									SELECT min(Fecha_Inicio) 
                									FROM $schema.Jornada 
                									WHERE Torneo_ID = $Season AND Jornada_Type = 1
                								  ) 
                								  and (
                									SELECT max(Fecha_Fin) 
                									FROM $schema.Jornada 
                									WHERE Torneo_ID = $Season AND Jornada_Type = 1
                								  )
                				  ) s on e.Equipo_ID = s.Visitante_ID 
                			join $schema.Categorias lc on e.Fuerza = lc.Categoria_ID 
                			left outer join $schema.Jornada lj on s.Jornada_ID = lj.Jornada_ID and lj.Jornada_Type = 1 and lc.Calendario_ID = lj.Calendario_ID 
                		where 
                			e.Fuerza = $Category and e.Torneo_ID = $Season and e.Activo = 1) j 
                    where 
                      Fuerza = $Category 
                    Group by 
                      j.Equipo_ID, 
                      Equipo_DESC, 
                      Fuerza
                  ) jj 
                order by 
                    Puntos desc, 
                    CS desc, 
                    CP desc, 
                    Equipo_DESC";
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
						$htmlWeek .=  '
							
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["rank"]. '</span></td>
										<td scope="row" class="align-middle text-right"><div class="d-flex px-0 py-1"><div style="width: 40px;text-align: right;padding-right: 3px;"><img src="imagenes/' . $row2["Logo"]. '.png?tmp=' . $fecha->getTimestamp() . '" class="avatar avatar-sm me-0" style="border-radius: 0rem !important;"></div><div style="width: 100px;text-align: left;padding-right: 3px;padding-top: 6px;"><span class="text-secondary text-xs font-weight-normal">' . $row2["Equipo_DESC"]. '</span></div></div></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["JJ"]. '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["JG"]. '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["JP"]. '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["PF"]. '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["PC"]. '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["DIFF"]. '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["Puntos"]. '</span></td>
										<!--<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["Extra"]. '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["Reales"]. '</span></td>-->';
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
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.15rem 0.1rem;">' . $lang['622'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.15rem 0.1rem;">' . $lang['623-V'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.15rem 0.1rem;">' . $lang['624-V'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.15rem 0.1rem;">' . $lang['625'] . '</th>';
				$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.15rem 0.1rem;">' . $lang['626'] . '</th>';
				//$htmlWeek .= '<th scope="col" class="text-center text-uppercase text-secondary text-s font-weight-bolder opacity-7" style="padding: 0.15rem 0.1rem;">' . $lang['628-1'] . '</th>';
				$htmlWeek .= '</thead>';
				$htmlWeek .= '<tbody>';

				$sql3 = "SET @rank:=0;";
				$Config->query($sql3);
				
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
										<td scope="row" class="align-middle text-right" style="padding: 0.15rem 0.1rem;"><div class="d-flex px-0 py-1"><div style="width: 40px;text-align: right;padding-right: 3px;"><img src="imagenes/' . $row2["Logo"]. '.png?tmp=' . $fecha->getTimestamp() . '" class="avatar avatar-sm me-0" style="border-radius: 0rem !important;"></div></div></td>
										<td scope="row" class="align-middle text-center" style="padding: 0.15rem 0.1rem;"><span class="text-secondary text-xs font-weight-normal">' . $row2["JJ"]. '</span></td>
										<td scope="row" class="align-middle text-center" style="padding: 0.15rem 0.1rem;"><span class="text-secondary text-xs font-weight-normal">' . $row2["JG"]. '</span></td>
										<td scope="row" class="align-middle text-center" style="padding: 0.15rem 0.1rem;"><span class="text-secondary text-xs font-weight-normal">' . $row2["JP"]. '</span></td>
										<td scope="row" class="align-middle text-center" style="padding: 0.15rem 0.1rem;"><span class="text-secondary text-xs font-weight-normal">' . $row2["GF"]. '</span></td>
										<td scope="row" class="align-middle text-center" style="padding: 0.15rem 0.1rem;"><span class="text-secondary text-xs font-weight-normal">' . $row2["GC"]. '</span></td>
										<td scope="row" class="align-middle text-center" style="padding: 0.15rem 0.1rem;"><span class="text-secondary text-xs font-weight-normal">' . $row2["DIFF"]. '</span></td>
										<td scope="row" class="align-middle text-center" style="padding: 0.15rem 0.1rem;"><span class="text-secondary text-xs font-weight-normal">' . $row2["Puntos"]. '</span></td>
										<!--<td scope="row" class="align-middle text-center" style="padding: 0.15rem 0.1rem;"><span class="text-secondary text-xs font-weight-normal">' . $row2["Reales"]. '</span></td>-->';
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