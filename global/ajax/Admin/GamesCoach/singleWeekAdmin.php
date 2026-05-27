<?php 
		$sql0 = "select distinct j.Jornada_ID as Jornada, 
						jk.Fecha, 
						DATE_FORMAT(jk.Fecha_Inicio, '" . $Config->DateFormat3 . "') Fecha_Inicio, 
						DATE_FORMAT(jk.Fecha_Fin, '" . $Config->DateFormat3 . "') Fecha_Fin
				from  $schema.Juegos as j 
					join $schema.Jornada jk on (j.Fecha between jk.Fecha_Inicio and jk.Fecha_Fin) and jk.Torneo_ID = $Season
					join $schema.Equipos e on j.Local_ID = e.Equipo_ID
				where j.Torneo_ID = $Season and jk.Jornada_ID = $Week $sqlcat
				order by j.Jornada_ID
				limit 1;";
		//echo $sql0;
		$result0 = $Config->query($sql0);
		if ($result0->num_rows > 0) {
			// output data of each row
			while($row = $result0->fetch_assoc()) {
				$htmlWeek .= "<div id='Jornada" . $row0["Jornada"]. "' >";
				$htmlWeek .= '<div class="tablasMainJornada">';

				$htmlWeek .= '<div class="tabla-content">';

				/*-----------------------------------------------------------------------------------------------------------------------------
				-----------------------------------------------------------------------------------------------------------------------------*/
				$htmlWeek .= '<div id="1resultados' . $row0["Jornada"] . '" class="tabla active" style="display: block">';
				if($Config->getSport() == 0){
				    require 'weekAdmin-ScheduleScores.php';
				}
			    if($Config->getSport() == 1){
				    require 'weekAdmin-ScheduleScoresVoleibol.php';
				} 
			    if($Config->getSport() == 2){
				    require 'weekAdmin-ScheduleScoresBasket.php';
				} 
				                    
				$htmlWeek .= '</div>';

				$htmlWeek .= '</div>';
				$htmlWeek .= "</div>";
			}
		}else{
			$sql1 = "select distinct j.Jornada_ID as Jornada, 
						j.Fecha, 
						DATE_FORMAT(j.Fecha_Inicio, '" . $Config->DateFormat3 . "') Fecha_Inicio, 
						DATE_FORMAT(j.Fecha_Fin, '" . $Config->DateFormat3 . "') Fecha_Fin, 
						case when k.Jornada_ID is null then 0 else 1 end as Activo
					from  $schema.Jornada as j 
						left outer join (select ifnull((SELECT 
														  Jornada_ID
														FROM 
														  $schema.Jornada
														where Torneo_ID = $Season and Fecha >= DATE_ADD(date(now()) , INTERVAL-3 DAY)
														LIMIT 1), (select max(Jornada_ID) from $schema.Jornada where Torneo_ID = $Season)) Jornada_ID) k on j.Jornada_ID = k.Jornada_ID and j.Torneo_ID = $Season
					where j.Torneo_ID = $Season and j.Jornada_ID = $Week
					order by j.Jornada_ID;";
			//echo sql1;
			$result1 = $Config->query($sql1);
			if ($result1->num_rows > 0) {
				// output data of each row
				while($row1 = $result1->fetch_assoc()) {
					$htmlWeek .= "<div id='Jornada" . $row1["Jornada"]. "' >";
					$htmlWeek .= '<div class="tablasMainJornada">';


					$htmlWeek .= '<div class="tabla-content">';

					/*-----------------------------------------------------------------------------------------------------------------------------
					-----------------------------------------------------------------------------------------------------------------------------*/
					$htmlWeek .= '<div id="2resultados' . $row1["Jornada"] . '" class="tabla active" style="display: block">';
			
			
			
			
			
					$htmlWeek .= '<div class="d-block  d-xs-block d-md-block d-lg-block d-xl-block"><div class="card">
								<div class="table-responsive">
									<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
										<thead class="">
											<th scope="col" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">' . $lang['609']  . ' ' . $row1["Fecha_Inicio"] . ' ' . $lang['610'] . ' ' . $row1["Fecha_Fin"] . '</th>';
					$htmlWeek .= '		</thead>';
					$htmlWeek .= '		<tbody>';
					$htmlWeek .= '<tr>
									<td scope="row">
										<div class="d-flex px-2 py-1">
											<div style="width: 100%;text-align: left;padding-right: 3px;padding-top: 6px;">
												<div style="float: left;padding-top: 6px;padding-left: 10px;">
													<div style="float: left;width: 67px;">' . $lang['652'] . '</div>
													<div style="float: right;padding-left: 10px;">
														<select name="localAgregar" id="localAgregar" onChange="loadVisitanteAgregar()">';
					
					$sqlcat = "and Fuerza = $Category";
					if($vs == 1){
						$sqlcat = "";
					}
					
			    $sqloneperweek = "";				
			    $sqloneperweekcond = "";				
				if($Config->unjuegosemanal == 1){
    			    $sqloneperweek = "left outer join (
    									select j.Local_ID as Equipo_ID
    									from  $schema.Juegos as j 
    										join $schema.Jornada as jo on j.Fecha between jo.Fecha_Inicio and jo.Fecha_Fin
    									where jo.Jornada_ID = $Week
    									UNION
    									select j.Visitante_ID as Equipo_ID
    									from  $schema.Juegos as j 
    										join $schema.Jornada as jo on j.Fecha between jo.Fecha_Inicio and jo.Fecha_Fin
    									where jo.Jornada_ID = $Week) b on a.Equipo_ID = b.Equipo_ID";				
    				$sqloneperweekcond = "and b.Equipo_ID is null";
				}
				$sql33 = "SELECT a.Equipo_ID, 
								Equipo_DESC 
						 FROM $schema.Equipos a
						$sqloneperweek
						 where Torneo_ID = $Season 
							and Equipo_Desc <> 'NA' 
							and Activo = 1 $sqlcat
							$sqloneperweekcon
						 order by 2 asc;";
				$result3 = $Config->query($sql33);
					if ($result3->num_rows > 0) {
						// output data of each row
						while($row3 = $result3->fetch_assoc()) {
								$htmlWeek .= "<option value='" . $row3["Equipo_ID"] . "'>" . $row3["Equipo_DESC"] . "</option>";
						}
					}
					
						$htmlWeek .= '					</select>
													</div>
												</div>
												<div style="float: left;padding-top: 6px;padding-left: 10px;">
													<div style="float: left;width: 67px;">' . $lang['653'] . '</div>
													<div style="float: right;padding-left: 10px;">
														<select name="visitanteAgregar" id="visitanteAgregar">
															<option value="NULL">' . $lang['654'] . '</option>
														</select>
													</div>
												</div>
												<div style="float: left;padding-left: 10px;">
													<button type="button" class="btn btn-primary" onClick="agregarJuego(\'' . $row1["Fecha"] . '\', ' . $Season . ', ' . $Week . ', $(\'#localAgregar\').val(), $(\'#visitanteAgregar\').val());" >' . $lang['664'] . '</button>
												</div>
											</div>
										</div>
									</td>
								</tr>';
					$htmlWeek .= '</tbody>';
					$htmlWeek .= '</table><script>loadVisitanteAgregar();</script>';
					$htmlWeek .= '</div>';
					$htmlWeek .= '</div>';
				
					$htmlWeek .= '</div>';

					$htmlWeek .= '</div>';
					$htmlWeek .= "</div>";
				}
			}
		}
?>