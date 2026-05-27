            <?php
				$htmlWeek .= '<div class="container-fluid py-0" style="padding-left: 0px; padding-right: 0px; padding-top: 2px !important;">
								<div class="nav-wrapper position-relative end-0">
									<ul class="nav nav-pills nav-fill p-1" role="tablist" style="background: #cee6ff; flex-direction: unset !important;" id="statS">
										<li class="nav-item" id="primeraTabla' . $row["Jornada"] . 'li">
											<a class="nav-link mb-0 px-0 py-1 active" data-bs-toggle="tab" style="cursor: pointer;" callval="#primeraTabla' . $row["Jornada"] . '" role="tab" aria-controls="primeraTabla' . $row["Jornada"] . 'li" aria-selected="true">
												<div class="d-none d-xs-none d-md-block d-lg-block d-xl-block"><img src="./imagenes/tabla.png" style="width: 20px; height: auto;" alt=""/>' . $lang['612'] . '</div><div class="d-block d-xs-block d-md-none d-lg-none d-xl-none"><img src="./imagenes/tabla.png" style="width: 20px; height: auto;" alt=""/></div>
											</a>
										</li>
										<li class="nav-item" id="primeraGoles' . $row["Jornada"] . 'li">
											<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" style="cursor: pointer;" callval="#primeraGoles' . $row["Jornada"] . '" role="tab" aria-controls="primeraGoles' . $row["Jornada"] . 'li" aria-selected="false">
												<div class="d-none d-xs-none d-md-block d-lg-block d-xl-block"><img src="./imagenes/goal.png" style="width: 20px; height: auto;" alt=""/>' . $lang['613'] . '</div><div class="d-block d-xs-block d-md-none d-lg-none d-xl-none"><img src="./imagenes/goal.png" style="width: 20px; height: auto;" alt=""/></div>
											</a>
										</li>
										<li class="nav-item" id="primeraAmonestados' . $row["Jornada"] . 'li">
											<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" style="cursor: pointer;" callval="#primeraAmonestados' . $row["Jornada"] . '" role="tab" aria-controls="primeraAmonestados' . $row["Jornada"] . 'li" aria-selected="false">
												<div class="d-none d-xs-none d-md-block d-lg-block d-xl-block"><img src="./imagenes/amarilla.png" style="width: 20px; height: auto;" alt=""/>' . $lang['614'] . '</div><div class="d-block d-xs-block d-md-none d-lg-none d-xl-none"><img src="./imagenes/amarilla.png" style="width: 20px; height: auto;" alt=""/></div>
											</a>
										</li>
										<li class="nav-item" id="primeraExpulsados' . $row["Jornada"] . 'li">
											<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" style="cursor: pointer;" callval="#primeraExpulsados' . $row["Jornada"] . '" role="tab" aria-controls="primeraExpulsados' . $row["Jornada"] . 'li" aria-selected="false">
												<div class="d-none d-xs-none d-md-block d-lg-block d-xl-block"><img src="./imagenes/roja.png" style="width: 20px; height: auto;" alt=""/>' . $lang['615'] . '</div><div class="d-block d-xs-block d-md-none d-lg-none d-xl-none"><img src="./imagenes/roja.png" style="width: 20px; height: auto;" alt=""/></div>
											</a>
										</li>
									</ul>
								</div><script>initNavs("statS");</script>
							</div>';

                
				$htmlWeek .= '<div class="primeratabla-content">';

                $htmlWeek .= '<div id="primeraTabla' . $row["Jornada"] . '" class="tablaMainJornada active" style="display: block">';
				require 'week-StatsTable.php';
				$htmlWeek .= '</div>';

                $htmlWeek .= '<div id="primeraGoles' . $row["Jornada"] . '" class="tablaMainJornada" style="display: none">';
				require 'week-StatsGoals.php';
				$htmlWeek .= "</div>";

                $htmlWeek .= '<div id="primeraAmonestados' . $row["Jornada"] . '" class="tablaMainJornada"  style="display: none">';
				require 'week-StatsYellow.php';
				$htmlWeek .= '</div>';

                $htmlWeek .= '<div id="primeraExpulsados' . $row["Jornada"] . '" class="tablaMainJornada"  style="display: none">'; 
				require 'week-StatsRed.php';             
                $htmlWeek .= '</div>';                     

                $htmlWeek .= '</div>';

				?>