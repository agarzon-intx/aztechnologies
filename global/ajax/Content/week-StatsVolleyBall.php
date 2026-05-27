            <?php
				$htmlWeek .= '<div class="container-fluid py-0" style="padding-left: 0px; padding-right: 0px; padding-top: 2px !important;">
								<div class="nav-wrapper position-relative end-0">
									<ul class="nav nav-pills nav-fill p-1" role="tablist" style="background: #cee6ff; flex-direction: unset !important;" id="statS">
										<li class="nav-item" id="primeraTabla' . $row["Jornada"] . 'li">
											<a class="nav-link mb-0 px-0 py-1 active" data-bs-toggle="tab" style="cursor: pointer;" callval="#primeraTabla' . $row["Jornada"] . '" role="tab" aria-controls="primeraTabla' . $row["Jornada"] . 'li" aria-selected="true">
												<div class="d-none d-xs-none d-md-block d-lg-block d-xl-block"><img src="./imagenes/tabla.png" style="width: 20px; height: auto;" alt=""/>' . $lang['612'] . '</div><div class="d-block d-xs-block d-md-none d-lg-none d-xl-none"><img src="./imagenes/tabla.png" style="width: 20px; height: auto;" alt=""/></div>
											</a>
										</li>
									</ul>
								</div><script>initNavs();</script>
							</div>';

                
				$htmlWeek .= '<div class="primeratabla-content">';

                $htmlWeek .= '<div id="primeraTabla' . $row["Jornada"] . '" class="tablaMainJornada active" style="display: block">';
				require 'week-StatsTableVolleyBall.php';
				$htmlWeek .= '</div>';
                $htmlWeek .= '</div>';

				?>