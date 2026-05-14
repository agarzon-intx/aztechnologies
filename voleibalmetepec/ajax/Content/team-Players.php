            <?php
				$htmlTeam .= '<div class="container-fluid py-0 px-0">
								<div class="row">
									<div class="justify-content-left d-flex px-0 py-0 col-6 col-sm-6 col-md-7 col-lg-8 col-xl-8 col-xxl-8">
										<div class="container-fluid py-0">
											<div class="px-0 py-0">
												<div class="nav-wrapper position-relative end-0">
													<ul class="nav nav-pills nav-fill p-1" role="tablist" style="background: #cee6ff; flex-direction: unset !important;" id="teamplayersl">
														<li class="nav-item" id="jugadores1li">
															<a class="nav-link mb-0 px-0 py-1 active" data-bs-toggle="tab" style="cursor: pointer;" callval="#jugadores1" role="tab" aria-controls="jugadores1li" aria-selected="true">
																<div class="d-none d-xs-none d-md-block d-lg-block d-xl-block"><img src="./imagenes/playerslist.png" style="width: 20px; height: auto;" alt=""/>  ' . $lang['385'] . '</div><div class="d-block d-xs-block d-md-none d-lg-none d-xl-none"><img src="./imagenes/playerslist.png" style="width: 20px; height: auto;" alt=""/></div>
															</a>
														</li>';
				if($Config->perfiljugador == 1){
	                $htmlTeam .=  '                     <li class="nav-item" id="jugadores2li">
															<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" style="cursor: pointer;" callval="#jugadores2" role="tab" aria-controls="jugadores2li" aria-selected="false">
																<div class="d-none d-xs-none d-md-block d-lg-block d-xl-block"><img src="./imagenes/profile.png" style="width: 20px; height: auto;" alt=""/>  ' . $lang['386'] . '</div><div class="d-block d-xs-block d-md-none d-lg-none d-xl-none"><img src="./imagenes/profile.png" style="width: 20px; height: auto;" alt=""/></div>
															</a>
														</li>';
	            }
														
				$htmlTeam .=  '                     </ul>
												</div>
												<script>initNavs("teamplayersl");</script>
											</div>
										</div>
									</div>	
									<div class="align-self-right col-5 col-sm-5 col-md-4 col-lg-3 col-xl-3 col-xxl-3" style="text-align: right;">
										<h4>' . $lang['384'] . '</h4>
									</div>
								</div>';
								

				$htmlTeam .= '<div class="tabla-content">';
				$htmlTeam .= '<div id="jugadores1" class="tabla active" style="display: block; width: 100% !important;">';
				if($Config->getSport() == 0){
            		require 'team-PlayersPlayerList.php';
            	}
            	if($Config->getSport() == 1){
        		    require 'team-PlayersPlayerListVoleibol.php';
        		}
				$htmlTeam .= '</div>';
				$htmlTeam .= '<div id="jugadores2" class="tabla" style="display: none; width: 100% !important;">';
				if($Config->getSport() == 0){
            		require 'team-PlayersPlayerProfileList.php';
            	}
            	if($Config->getSport() == 1){
        		    require 'team-PlayersPlayerProfileListVoleibol.php';
        		}
				$htmlTeam .= '</div>';
				$htmlTeam .= '</div>';
				$htmlTeam .= '</div>';
				?>