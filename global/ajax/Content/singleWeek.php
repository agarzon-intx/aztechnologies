<?php 
		$sql = "select distinct j.Jornada_ID as Jornada, 
						jk.Fecha, 
						DATE_FORMAT(jk.Fecha_Inicio, '" . $Config->DateFormat3 . "') Fecha_Inicio, 
						DATE_FORMAT(jk.Fecha_Fin, '" . $Config->DateFormat3 . "') Fecha_Fin
				from  $schema.Juegos as j 
					join $schema.Jornada jk on (j.Fecha between jk.Fecha_Inicio and jk.Fecha_Fin) and jk.Torneo_ID = $Season
					join $schema.Equipos e on j.Local_ID = e.Equipo_ID
				where j.Torneo_ID = $Season and jk.Jornada_ID = $Week $sqlcat
				order by j.Jornada_ID
				limit 1;";
		//echo $sql;
		$result1 = $Config->query($sql);
		if ($result1->num_rows > 0) {
			// output data of each row
			while($row = $result1->fetch_assoc()) {
				$htmlWeek .= "<div id='Jornada" . $row["Jornada"]. "' >";
				$htmlWeek .= '<div class="tablasMainJornada">';
				$htmlWeekTab .= '<div class="nav-wrapper position-relative end-0">
									<ul class="nav nav-pills nav-fill p-1" role="tablist" style="background: #cee6ff; flex-direction: unset !important;" id="singleWeekS">
										<li class="nav-item" id="resultados' . $row["Jornada"] . 'li">
											<a class="nav-link mb-0 px-0 py-1 active" data-bs-toggle="tab" style="cursor: pointer;" callval="#resultados' . $row["Jornada"] . '" role="tab" aria-controls="resultados' . $row["Jornada"] . 'li" aria-selected="true">
												<img src="./imagenes/Calendar.png" style="width: 20px; height: auto;" alt=""/>' . $lang['600'] . '
											</a>
										</li>
										<li class="nav-item" id="estadisticas' . $row["Jornada"] . 'li">
											<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" style="cursor: pointer;" callval="#estadisticas' . $row["Jornada"] . '" role="tab" aria-controls="estadisticas' . $row["Jornada"] . 'li" aria-selected="false">';
				if($Config->getSport() == 0){
            		    $htmlWeekTab .= '<img src="./imagenes/stats.png" style="width: 20px; height: auto;" alt=""/>' . $lang['601'];
            	}
            	if($Config->getSport() == 1){
        		    $htmlWeekTab .= '<img src="./imagenes/statsVoleibol.png" style="width: 20px; height: auto;" alt=""/>' . $lang['601'];
        		}
            	if($Config->getSport() == 2){
        		    $htmlWeekTab .= '<img src="./imagenes/statsBasket.png" style="width: 20px; height: auto;" alt=""/>' . $lang['601'];
        		}
				$htmlWeekTab .= '	        </a>
										</li>';
				if($vs == 1){ 
					$htmlWeekTab .= '	<li class="nav-item" id="all' . $row["Jornada"] . 'li">
											<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" style="cursor: pointer;" callval="#all' . $row["Jornada"] . '" role="tab" aria-controls="all' . $row["Jornada"] . 'li" aria-selected="false">
												<img src="./imagenes/stats.png" style="width: 20px; height: auto;" alt=""/>' . $lang['601-1'] . '
											</a>
										</li>';
				}

				$htmlWeekTab .= '</ul>';
							$htmlWeekTab .= "</div><script>initNavs('singleWeekS');</script>";
				$htmlWeek .= '<div class="tabla-content">';

				/*-----------------------------------------------------------------------------------------------------------------------------
				-----------------------------------------------------------------------------------------------------------------------------*/
				$htmlWeek .= '<div id="resultados' . $row["Jornada"] . '" class="tabla active" style="display: block">';
				if($Config->getSport() == 0){
				    require 'week-ScheduleScores.php';
				}
				if($Config->getSport() == 1){
				    require 'week-ScheduleScoresVolleyBall.php';
				}
				if($Config->getSport() == 2){
				    require 'week-ScheduleScoresBasket.php';
				}
				$htmlWeek .= '</div>';

				/*-----------------------------------------------------------------------------------------------------------------------------
				-----------------------------------------------------------------------------------------------------------------------------*/
				$htmlWeek .= '<div id="estadisticas' . $row["Jornada"] . '" class="tabla" style="display: block">';
				if($Config->getSport() == 0){
				    require 'week-Stats.php'; 
				}
				if($Config->getSport() == 1){
				    require 'week-StatsVolleyBall.php'; 
				} 
				if($Config->getSport() == 2){
				    require 'week-StatsBasket.php'; 
				}               
				$htmlWeek .= '</div>';

				/*-----------------------------------------------------------------------------------------------------------------------------
				-----------------------------------------------------------------------------------------------------------------------------*/
				if($vs == 1){ 
					$htmlWeek .= '<div id="all' . $row["Jornada"] . '" class="tabla" style="display: block">';
					require 'week-All.php';                    
					$htmlWeek .= '</div>';
				}

				/*-----------------------------------------------------------------------------------------------------------------------------
				-----------------------------------------------------------------------------------------------------------------------------*/

				$htmlWeek .= '</div>';
				$htmlWeek .= "</div>";
				$htmlWeek .= "<script> 	
							 
							 $('#estadisticas" . $row["Jornada"] . "').toggle();
							 $('#all" . $row["Jornada"] . "').toggle();
			 
			 			</script>";
			}
		}
?>