<?php 
				$htmlWeek .= "<div id='Jornada" . $row["Jornada"]. "' >";
				$htmlWeek .= '<div class="tablasMainJornada">';
				$htmlWeek .= '<div class="tabla-content">';


				/*-----------------------------------------------------------------------------------------------------------------------------
				-----------------------------------------------------------------------------------------------------------------------------*/
				$htmlWeek .= '<div id="estadisticas' . $row["Jornada"] . '" class="tabla" style="display: block">';
				if(app_sport_uses_soccer((int) $Config->getSport())){
				    //require 'week-Stats.php'; 
				}else{
				    if(app_sport_uses_voleibol((int) $Config->getSport())){
    				    require 'week-StatsVolleyBall0.php'; 
    				}else{
    				    
    				}
				}                   
				$htmlWeek .= '</div>';

?>