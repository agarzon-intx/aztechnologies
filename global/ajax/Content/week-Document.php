                <?php
                $htmlWeek .= '<div class="cedulatablas" style="text-align: left; padding-top: 30px">';

				$htmlWeek .= '<div class="cedulatabla-content">';
                $htmlWeek .= '<div id="cedulaTabla' . $Week . '" class="tabla " style="text-align: center;">';
				$sql2 = "SELECT Jornada_DescCorta FROM $schema.Jornada
						 where Jornada_ID = " . $Week . " and Torneo_Id = " . $Season . ";";
                $result2 = $Config->query($sql2);
				 if ($result2->num_rows > 0) {
					// output data of each row
					while($row2 = $result2->fetch_assoc()) {
						$weekShortDesk = $row2["Jornada_DescCorta"];
					}
				}
        		$htmlWeek .= '<a href="pdf/cedulas.php?Torneo_ID=' . $Season . '&Jornada_ID=' . $Week . '&Categoria_ID=' . $Category . '" target="_blank" download class="btn btn-primary btn-lg active" role="button" aria-pressed="true">' . $lang['647'] . '</a>';
                $htmlWeek .= '</div>';      
                $htmlWeek .= '</div>';	
                $htmlWeek .= '</div>';
                ?>