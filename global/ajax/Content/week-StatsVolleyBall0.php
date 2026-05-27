            <?php
				$htmlWeek .= '<div class="container-fluid py-0" style="padding-left: 0px; padding-right: 0px; padding-top: 2px !important;">
								</div>';

                
				$htmlWeek .= '<div class="primeratabla-content">';

                $htmlWeek .= '<div id="primeraTabla' . $row["Jornada"] . '" class="tablaMainJornada active" style="display: block">';
				require 'week-StatsTableVolleyBall0.php';
				$htmlWeek .= '</div>';
                $htmlWeek .= '</div>';

				?>