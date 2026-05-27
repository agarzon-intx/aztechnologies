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
				$htmlTeam .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;"><span>' . $lang['359'] . '  ' . $lang['366'] . '</span></th>';
				$htmlTeam .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="padding: 0.75rem 0.5rem;">' . $lang['367'] . '</th>';
				$htmlTeam .= '<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;"><span>' . $lang['360'] . '  ' . $lang['368'] . '</span></th>';
				$htmlTeam .= '</thead>';
				$htmlTeam .= '<tbody>';
				$sql2 = "select j.Torneo_ID as Torneo, h.Jornada_DescCorta as Jornada, j.Fecha,
									case when j.Visitante_Id is null then 'Descansa' else concat(l.equipo_desc,'') end  as 'Local', 
									case when j.Visitante_ID is null then null when jugado = 0 then '' else CONCAT(Gol_Local,'') end as 'Goles Local', 
									case when j.Visitante_ID is null then null else Penal_local end as 'Penalties Local', 
									case 
										when jugado = 0 then '' 
										else '-'
									end as marcador,
									case 
										when jugado = 0 then '' 
										else case when Penal_local > Penal_Visitante then '*' else '' end
									end as marcadorpl,
									case 
										when jugado = 0 then '' 
										else case when Penal_local < Penal_Visitante then '*' else '' end
									end as marcadorpv,
									concat(case when j.Visitante_ID is null then null else v.equipo_desc end,'') as 'Visitante', 
									case when j.Visitante_ID is null then null when jugado = 0 then '' else CONCAT(Gol_Visitante,'') end as 'Goles Visitante', 
									case when j.Visitante_ID is null then null else Penal_Visitante end as 'Penalties Visitante', 
									case when j.Visitante_ID is null then null else ifnull(jc.Campo_DESC, lc.Campo_DESC) end as Campo,
									case 
										when j.Visitante_Id is null then ''
										else
											concat(l.Torneo_ID,'-', l.Equipo_ID) 
									end as Logol, 
									case 
										when j.Visitante_Id is null then ''
										else
											concat(v.Torneo_ID,'-', v.Equipo_ID)
									end  as Logov, ifnull(jc.Google, lc.Google) as Google, 
									case when Jugado = 0 and j.Visitante_ID is not null then '' else CONCAT('',i.Pts) end as Pts, 
									case when Jugado = 0 and j.Visitante_ID is not null  then '' else CONCAT('',i.Reales) end as Reales, 
									case when Jugado = 0 and j.Visitante_ID is not null  then '' else CONCAT('',i.Last5) end as Last5, 
									case when Jugado = 0 and j.Visitante_ID is not null  then '' else CONCAT('',i.PosGrupo) end as PosGru, 
									case when Jugado = 0 and j.Visitante_ID is not null  then '' else CONCAT('',i.PosGeneral) end as PosGen,
									j.jugado
								from  $schema.Juegos as j 
									left outer join $schema.Campos jc on j.Campo_ID = jc.Campo_ID
									join $schema.Equipos as l on j.local_ID = l.Equipo_ID and l.Torneo_ID = $Season and j.Torneo_ID = $Season
									left outer join $schema.Campos lc on l.Campo_ID = lc.Campo_ID
									left outer join $schema.Equipos as v on j.Visitante_ID = v.Equipo_ID and v.Torneo_ID = $Season and v.Torneo_ID = $Season
									left outer join $schema.Jornada as h on j.Jornada_ID = h.Jornada_ID
									left outer join $schema.Equipo_Stats i 
										on j.Torneo_ID = i.Torneo_ID and
											j.Jornada_ID = i.Jornada_ID and
											i.Equipo_ID = $Team
								where (j.Local_ID = $Team or j.Visitante_ID = $Team) and j.Torneo_ID = $Season
								order by j.Torneo_ID, j.Fecha asc;";
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
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["Fecha"]. '</span></td>
										<td scope="row" class="align-middle text-right"><div class="justify-content-center d-flex px-0 py-1"><div class="align-self-center" style="width: 30%; text-align: right;padding-right: 3px; ">' . $row2["Local"] . '</div>
							<div class="align-self-center"><img src="imagenes/' . $row2["Logol"] . '.png?tmp=' . $fecha->getTimestamp() . '" class="avatar avatar-sm me-0" style="border-radius: 0rem !important;"></div>
							<div ' .  $Config->EmpatesPenales . ' style="width: 15px;text-align: center;" class="align-self-center">' . $row2["marcadorpl"] . '</div>';
						if($row2["jugado"] == 2){
							$htmlTeam .= '<div style="width: 15px;text-align: center;" class="align-self-center">' . $lang['663'] . '</div>';
						}else{
							$htmlTeam .= '<div style="width: 20px;text-align: right;" class="align-self-center">' . $row2["Goles Local"] . '</div>
							<div style="width: 10px;text-align: center;" class="align-self-center">' . $row2["marcador"] . '</div>
							<div style="width: 20px;text-align: left;" class="align-self-center">' . $row2["Goles Visitante"] . '</div>';
						}
						$htmlTeam .= '<div ' .  $Config->EmpatesPenales . ' style="width: 15px;text-align: center;" class="align-self-center">' . $row2["marcadorpv"] . '</div>
							<div class="align-self-center"><img src="imagenes/' . $row2["Logov"] . '.png?tmp=' . $fecha->getTimestamp() . '" class="avatar avatar-sm me-0" style="border-radius: 0rem !important;"></div>
							<div style="width: 30%; text-align: left; padding-left: 3px; " class="align-self-center">' . $row2["Visitante"] .'</div></div></td>
										<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal"><a target="_blank" href="' . $row2["Google"] . '">' . $row2["Campo"]. '</a></span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["Last5"]. '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["Reales"]. '</span></td>
										<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["PosGru"]. '</span></td>';
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
				$sql2 = "select j.Torneo_ID as Torneo, h.Jornada_DescCorta as Jornada, j.Fecha,
									case when j.Visitante_Id is null then 'Descansa' else concat(l.equipo_desc,'') end  as 'Local', 
									case when j.Visitante_ID is null then null when jugado = 0 then '' else CONCAT(Gol_Local,'') end as 'Goles Local', 
									case when j.Visitante_ID is null then null else Penal_local end as 'Penalties Local', 
									case 
										when jugado = 0 then '' 
										else '-'
									end as marcador,
									case 
										when jugado = 0 then '' 
										else case when Penal_local > Penal_Visitante then '*' else '' end
									end as marcadorpl,
									case 
										when jugado = 0 then '' 
										else case when Penal_local < Penal_Visitante then '*' else '' end
									end as marcadorpv,
									concat(case when j.Visitante_ID is null then null else v.equipo_desc end,'') as 'Visitante', 
									case when j.Visitante_ID is null then null when jugado = 0 then '' else CONCAT(Gol_Visitante,'') end as 'Goles Visitante', 
									case when j.Visitante_ID is null then null else Penal_Visitante end as 'Penalties Visitante', 
									case when j.Visitante_ID is null then null else ifnull(jc.Campo_DESC, lc.Campo_DESC) end as Campo,
									case 
										when j.Visitante_Id is null then ''
										else
											concat(l.Torneo_ID,'-', l.Equipo_ID) 
									end as Logol, 
									case 
										when j.Visitante_Id is null then ''
										else
											concat(v.Torneo_ID,'-', v.Equipo_ID)
									end  as Logov, ifnull(jc.Google, lc.Google) as Google, 
									case when Jugado = 0 and j.Visitante_ID is not null then '' else CONCAT('',i.Pts) end as Pts, 
									case when Jugado = 0 and j.Visitante_ID is not null  then '' else CONCAT('',i.Reales) end as Reales, 
									case when Jugado = 0 and j.Visitante_ID is not null  then '' else CONCAT('',i.Last5) end as Last5, 
									case when Jugado = 0 and j.Visitante_ID is not null  then '' else CONCAT('',i.PosGrupo) end as PosGru, 
									case when Jugado = 0 and j.Visitante_ID is not null  then '' else CONCAT('',i.PosGeneral) end as PosGen,
									j.jugado
								from  $schema.Juegos as j 
									left outer join $schema.Campos jc on j.Campo_ID = jc.Campo_ID
									join $schema.Equipos as l on j.local_ID = l.Equipo_ID and l.Torneo_ID = $Season and j.Torneo_ID = $Season
									left outer join $schema.Campos lc on l.Campo_ID = lc.Campo_ID
									left outer join $schema.Equipos as v on j.Visitante_ID = v.Equipo_ID and v.Torneo_ID = $Season and v.Torneo_ID = $Season
									left outer join $schema.Jornada as h on j.Fecha between h.Fecha_Inicio and h.Fecha_Fin and h.Torneo_ID = $Season
									left outer join $schema.Equipo_Stats i 
										on j.Torneo_ID = i.Torneo_ID and
											j.Jornada_ID = i.Jornada_ID and
											i.Equipo_ID = $Team
								where (j.Local_ID = $Team or j.Visitante_ID = $Team) and j.Torneo_ID = $Season
								order by j.Torneo_ID, j.Fecha asc;";
				$result2 = $Config->query($sql2);

				$count = 0;
				if ($result2->num_rows > 0) {
						
					while($row2 = $result2->fetch_assoc()) {
						if (($count % 2) == 1){
							$htmlTeam .= "<tr>";
						}else{
							$htmlTeam .= "<tr class='alt'>";
						}
						
						$htmlTeam .=  '<td scope="row"><div class="justify-content-center d-flex px-0 py-1"><div class="align-self-center" style="width: 30%; text-align: right;padding-right: 3px; font-size:3vw;">' . $row2["Local"] . '</div>
							<div class="align-self-center"><img src="imagenes/' . $row2["Logol"] . '.png?tmp=' . $fecha->getTimestamp() . '" class="avatar avatar-sm me-0" style="border-radius: 0rem !important;"></div>
							<div ' .  $Config->EmpatesPenales . ' style="width: 15px;text-align: center;" class="align-self-center">' . $row2["marcadorpl"] . '</div>';
						if($row2["jugado"] == 2){
							$htmlTeam .= '<div style="width: 40px;text-align: center;" class="align-self-center">' . $lang['663'] . '</div>';
						}else{
							$htmlTeam .= '<div style="width: 15px;text-align: right;" class="align-self-center">' . $row2["Goles Local"] . '</div>
							<div style="width: 10px;text-align: center;" class="align-self-center">-</div>
							<div style="width: 15px;text-align: left;" class="align-self-center">' . $row2["Goles Visitante"] . '</div>';
						}
						$htmlTeam .= '<div ' .  $Config->EmpatesPenales . ' style="width: 15px;text-align: center;" class="align-self-center">' . $row2["marcadorpv"] . '</div>
							<div class="align-self-center"><img src="imagenes/' . $row2["Logov"] . '.png?tmp=' . $fecha->getTimestamp() . '" class="avatar avatar-sm me-0" style="border-radius: 0rem !important;"></div>
							<div style="width: 30%; text-align: left; padding-left: 3px; font-size:3vw;" class="align-self-center">' . $row2["Visitante"] .'</div></div>
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
									<div style="width: 30%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold" style="word-wrap: break-word; width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['359'] . '  ' . $lang['366'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Last5"] . '</span></div></div></div>
									<div style="width: 10%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['367'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Reales"] . '</span></div></div></div>
									
									</div></div></td>';
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
				$sql2 = "select j.Torneo_ID as Torneo, h.Jornada_DescCorta as Jornada, j.Fecha,
									case when j.Visitante_Id is null then 'Descansa' else concat(l.equipo_desc,'') end  as 'Local', 
									case when j.Visitante_ID is null then null when jugado = 0 then '' else CONCAT(Gol_Local,'') end as 'Goles Local', 
									case when j.Visitante_ID is null then null else Penal_local end as 'Penalties Local', 
									case 
										when jugado = 0 then '' 
										else '-'
									end as marcador,
									case 
										when jugado = 0 then '' 
										else case when Penal_local > Penal_Visitante then '*' else '' end
									end as marcadorpl,
									case 
										when jugado = 0 then '' 
										else case when Penal_local < Penal_Visitante then '*' else '' end
									end as marcadorpv,
									concat(case when j.Visitante_ID is null then null else v.equipo_desc end,'') as 'Visitante', 
									case when j.Visitante_ID is null then null when jugado = 0 then '' else CONCAT(Gol_Visitante,'') end as 'Goles Visitante', 
									case when j.Visitante_ID is null then null else Penal_Visitante end as 'Penalties Visitante', 
									case when j.Visitante_ID is null then null else ifnull(jc.Campo_DESC, lc.Campo_DESC) end as Campo,
									case 
										when j.Visitante_Id is null then ''
										else
											concat(l.Torneo_ID,'-', l.Equipo_ID) 
									end as Logol, 
									case 
										when j.Visitante_Id is null then ''
										else
											concat(v.Torneo_ID,'-', v.Equipo_ID)
									end  as Logov, ifnull(jc.Google, lc.Google) as Google, 
									case when Jugado = 0 and j.Visitante_ID is not null then '' else CONCAT('',i.Pts) end as Pts, 
									case when Jugado = 0 and j.Visitante_ID is not null  then '' else CONCAT('',i.Reales) end as Reales, 
									case when Jugado = 0 and j.Visitante_ID is not null  then '' else CONCAT('',i.Last5) end as Last5, 
									case when Jugado = 0 and j.Visitante_ID is not null  then '' else CONCAT('',i.PosGrupo) end as PosGru, 
									case when Jugado = 0 and j.Visitante_ID is not null  then '' else CONCAT('',i.PosGeneral) end as PosGen,
									j.jugado
								from  $schema.Juegos as j 
									left outer join $schema.Campos jc on j.Campo_ID = jc.Campo_ID
									join $schema.Equipos as l on j.local_ID = l.Equipo_ID and l.Torneo_ID = $Season and j.Torneo_ID = $Season
									left outer join $schema.Campos lc on l.Campo_ID = lc.Campo_ID
									left outer join $schema.Equipos as v on j.Visitante_ID = v.Equipo_ID and v.Torneo_ID = $Season and v.Torneo_ID = $Season
									left outer join $schema.Jornada as h on j.Jornada_ID = h.Jornada_ID and h.Torneo_ID = $Season
									left outer join $schema.Equipo_Stats i 
										on j.Torneo_ID = i.Torneo_ID and
											j.Jornada_ID = i.Jornada_ID and
											i.Equipo_ID = $Team
								where (j.Local_ID = $Team or j.Visitante_ID = $Team) and j.Torneo_ID = $Season
								order by j.Torneo_ID, j.Fecha asc;";
				$result2 = $Config->query($sql2);

				$count = 0;
				if ($result2->num_rows > 0) {
						
					while($row2 = $result2->fetch_assoc()) {
						if (($count % 2) == 1){
							$htmlTeam .= "<tr>";
						}else{
							$htmlTeam .= "<tr class='alt'>";
						}
						$htmlTeam .=  '<td scope="row" class="align-middle text-center"><div class="justify-content-center d-flex px-0 py-1"><div class="align-self-center" style="width: 30%; text-align: right;padding-right: 3px; font-size:3vw;">' . $row2["Local"] . '</div>
							<div class="align-self-center"><img src="imagenes/' . $row2["Logol"] . '.png?tmp=' . $fecha->getTimestamp() . '" class="avatar avatar-sm me-0" style="border-radius: 0rem !important;"></div>
							<div' .  $Config->EmpatesPenales . ' style="width: 15px;text-align: center;" class="align-self-center">' . $row2["marcadorpl"] . '</div>';
						if($row2["jugado"] == 2){
							$htmlTeam .= '<div style="width: 15px;text-align: center;" class="align-self-center">' . $lang['663'] . '</div>';
						}else{
							$htmlTeam .= '<div style="width: 20px;text-align: right;" class="align-self-center">' . $row2["Goles Local"] . '</div>
							<div style="width: 10px;text-align: center;" class="align-self-center">' . $row2["marcador"] . '</div>
							<div style="width: 20px;text-align: left;" class="align-self-center">' . $row2["Goles Visitante"] . '</div>';
						}
						$htmlTeam .= '<div ' .  $Config->EmpatesPenales . ' style="width: 15px;text-align: center;" class="align-self-center">' . $row2["marcadorpv"] . '</div>
							<div class="align-self-center"><img src="imagenes/' . $row2["Logov"] . '.png?tmp=' . $fecha->getTimestamp() . '" class="avatar avatar-sm me-0" style="border-radius: 0rem !important;"></div>
							<div style="width: 30%; text-align: left; padding-left: 3px; font-size:3vw;" class="align-self-center">' . $row2["Visitante"] .'</div></div>
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