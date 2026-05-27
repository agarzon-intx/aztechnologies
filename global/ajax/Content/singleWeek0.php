<?php 
				$htmlWeek .= "<div id='Jornada" . $row["Jornada"]. "' >";
				$htmlWeek .= '<div class="tablasMainJornada">';
				$htmlWeek .= '<div class="tabla-content">';


				/*-----------------------------------------------------------------------------------------------------------------------------
				-----------------------------------------------------------------------------------------------------------------------------*/
				$htmlWeek .= '<div id="estadisticas' . $row["Jornada"] . '" class="tabla" style="display: block">';
				if($Config->getSport() == 0){
				    //require 'week-Stats.php'; 
				}else{
				    if($Config->getSport() == 1){
    				    require 'week-StatsVolleyBall0.php'; 
    				}else{
    				    
    				}
				}                   
				$htmlWeek .= '</div>';

?>