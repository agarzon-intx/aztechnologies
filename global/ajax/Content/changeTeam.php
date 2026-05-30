<?php
    session_start();
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
$__APP_SITE_PATHS_START__ = __DIR__;
$__app_here = __DIR__;
for ($__i = 0, $__prev = null; $__i < 24; $__i++) {
	$__base = ($__i === 0) ? $__app_here : dirname($__app_here, $__i);
	if ($__base === $__prev) {
		break;
	}
	$__prev = $__base;
	$__inc = $__base . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'app_site_paths.inc.php';
	if (is_readable($__inc)) {
		require_once $__inc;
		break;
	}
}
unset($__i, $__prev, $__base, $__inc, $__app_here);

	require("membersite_config.php");
	$schema = $Config->getSchema();
	
	$sessionstat = $fgmembersite->CheckLogin('changeTeam.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
    $retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
    
    $Season = $_COOKIE[$Config->getAlias() . 'season'];
    $Team = SanitizeInteger($_POST['team']);
	$Category = $_COOKIE[$Config->getAlias() . 'category'];
    $htmlTeam = '';
	
	$equipodesc = "";
	$logo = "";
	$Config->LoadFlags();
	$fecha = new DateTime();

	$sql = "select replace(Equipo_FULLDESC, 'ñ', '&ntilde;') Equipo_FULLDESC, concat(Torneo_ID,'-', Equipo_ID) Logo from $schema.Equipos
			where Equipo_ID = $Team and Torneo_ID = $Season;";
	$result = $Config->query($sql);
    

	 if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			$equipodesc = $row["Equipo_FULLDESC"];
			$logo = $row["Logo"];
		}
	}

    $htmlTeam .= '<div class="container-fluid py-0">
					<div class="row">
						<div class="justify-content-left d-flex px-0 py-0 col-8 col-sm-10 col-md-10 col-lg-10 col-xl-10 col-xxl-11">
							<div class="container-fluid py-0">
								<div class="align-self-left" text-align: left;">
									<h5>' . $equipodesc . '</h5>
								</div>
								<div class="px-0 py-0">
									
									<div class="nav-wrapper position-relative end-0">
										<ul class="nav nav-pills nav-fill p-1" role="tablist" style="background: #cee6ff; flex-direction: unset !important;" id="team">
											<li class="nav-item" id="equipoli">
												<a class="nav-link mb-0 px-0 py-1 active" data-bs-toggle="tab" style="cursor: pointer;" callval="#equipo" role="tab" aria-controls="equipoli" aria-selected="true">
													<div class="d-none d-xs-none d-md-block d-lg-block d-xl-block"><img src="./imagenes/' . $logo . '.png?tmp=' . $fecha->getTimestamp() . '" style="width: 20px; height: auto;" alt=""/>  ' . $lang['350'] . '</div><div class="d-block d-xs-block d-md-none d-lg-none d-xl-none"><img src="./imagenes/' . $logo . '.png?tmp=' . $fecha->getTimestamp() . '" style="width: 20px; height: auto;" alt=""/></div>
												</a>
											</li>
	                                        <li class="nav-item" id="calendarioli">
												<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" style="cursor: pointer;" callval="#calendario" role="tab" aria-controls="calendarioli" aria-selected="false">
													<div class="d-none d-xs-none d-md-block d-lg-block d-xl-block"><img src="./imagenes/Calendar.png" style="width: 20px; height: auto;" alt=""/>  ' . $lang['351'] . '</div><div class="d-block d-xs-block d-md-none d-lg-none d-xl-none"><img src="./imagenes/Calendar.png" style="width: 20px; height: auto;" alt=""/></div>
												</a>
											</li>';
											if(app_sport_uses_soccer((int) $Config->getSport())){
											    $htmlTeam .= '  <li class="nav-item" id="graph1li">
                    												<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" style="cursor: pointer;" callval="#graph1" role="tab" aria-controls="graph1li" aria-selected="false">
                    													<div class="d-none d-xs-none d-md-block d-lg-block d-xl-block"><img src="./imagenes/graph.png" style="width: 20px; height: auto;" alt=""/>  ' . $lang['352'] . '</div><div class="d-block d-xs-block d-md-none d-lg-none d-xl-none"><img src="./imagenes/graph.png" style="width: 20px; height: auto;" alt=""/></div>
                    												</a>
                    											</li>';
	                                        }
											$htmlTeam .= '<li class="nav-item" id="jugadoresli">';
											if(app_sport_uses_soccer((int) $Config->getSport())){
                                        		    $htmlTeam .= '<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" style="cursor: pointer;" callval="#jugadores" role="tab" aria-controls="jugadoresli" aria-selected="false">
                													<div class="d-none d-xs-none d-md-block d-lg-block d-xl-block"><img src="./imagenes/jugador.png" style="width: 20px; height: auto;" alt=""/>  ' . $lang['353'] . '</div><div class="d-block d-xs-block d-md-none d-lg-none d-xl-none"><img src="./imagenes/jugador.png" style="width: 20px; height: auto;" alt=""/></div>
                												</a>';
                                        	}
                                        	if(app_sport_uses_voleibol((int) $Config->getSport())){
                                    		    $htmlTeam .= '<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" style="cursor: pointer;" callval="#jugadores" role="tab" aria-controls="jugadoresli" aria-selected="false">
            													<div class="d-none d-xs-none d-md-block d-lg-block d-xl-block"><img src="./imagenes/jugadorVoleibol.png" style="width: 20px; height: auto;" alt=""/>  ' . $lang['353'] . '</div><div class="d-block d-xs-block d-md-none d-lg-none d-xl-none"><img src="./imagenes/jugadorVoleibol.png" style="width: 20px; height: auto;" alt=""/></div>
            												</a>';
                                    		}
                                        	if(app_sport_uses_basket((int) $Config->getSport())){
                                    		    $htmlTeam .= '<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" style="cursor: pointer;" callval="#jugadores" role="tab" aria-controls="jugadoresli" aria-selected="false">
            													<div class="d-none d-xs-none d-md-block d-lg-block d-xl-block"><img src="./imagenes/jugadorBasket.png" style="width: 20px; height: auto;" alt=""/>  ' . $lang['353'] . '</div><div class="d-block d-xs-block d-md-none d-lg-none d-xl-none"><img src="./imagenes/jugadorVoleibol.png" style="width: 20px; height: auto;" alt=""/></div>
            												</a>';
                                    		}
												
	$htmlTeam .=                            '</li>';
	                                        if($Config->tarjetacambios == 1){
	$htmlTeam .=                            '<li class="nav-item" id="documentosli">
												<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" style="cursor: pointer;" callval="#documentos" role="tab" aria-controls="documentosli" aria-selected="false">
													<div class="d-none d-xs-none d-md-block d-lg-block d-xl-block"><img src="./imagenes/documents.png" style="width: 20px; height: auto;" alt=""/>  ' . $lang['382'] . '</div><div class="d-block d-xs-block d-md-none d-lg-none d-xl-none"><img src="./imagenes/documents.png" style="width: 20px; height: auto;" alt=""/></div>
													
	                                           </a>
											</li>';
	                                        }
	$htmlTeam .=                       '</ul>
									</div>
									<script>initNavs("team");</script>
								</div>
							</div>
						</div>	
						<div class="align-self-right col-1 col-sm-2 col-md-2 col-lg-1 col-xl-1 col-xxl-1" style="text-align: right;">
							<img src="imagenes/Original/' . $logo . '.png?tmp=' . $fecha->getTimestamp() . '" style="width: auto; height:85px" alt=""/>
						</div>
					  </div>';
		  
   	$htmlTeam .= '<div class="tab-content" style="background: transparent; padding: 0px;">';
    $htmlTeam .= '<div class="tablas" style=" width: 100% !important;">';
    
    $htmlTeam .= '<div class="tabla-content" style=" width: 100% !important;">';
            
	/*-----------------------------------------------------------------------------------------------------------------------------
	-----------------------------------------------------------------------------------------------------------------------------*/
	$htmlTeam .= '<div id="equipo" class="tabla active" style="display: block;width: 100% !important;">';
	require 'team-Info.php';	
	$htmlTeam .= '</div>';
            
	/*-----------------------------------------------------------------------------------------------------------------------------
	-----------------------------------------------------------------------------------------------------------------------------*/
	$htmlTeam .= '<div id="calendario" class="tabla" style="display: none; width: 100% !important;">';
	if(app_sport_uses_soccer((int) $Config->getSport())){
	    require 'team-Schedule.php';
	}
    if(app_sport_uses_voleibol((int) $Config->getSport())){
	    require 'team-ScheduleVoleibol.php';
	}
    if(app_sport_uses_basket((int) $Config->getSport())){
	    require 'team-ScheduleBasket.php';
	}

   	$htmlTeam .= '</div>';
            
	/*-----------------------------------------------------------------------------------------------------------------------------
	-----------------------------------------------------------------------------------------------------------------------------*/

	
	if(app_sport_uses_soccer((int) $Config->getSport())){
	    	$htmlTeam .= '<div id="graph1" class="tabla" style="display: none; width: 100% !important;">';
	        require 'team-Graph.php';
	        $htmlTeam .= '</div>';
	}else{
	    if(app_sport_uses_voleibol((int) $Config->getSport())){
	   	    $htmlTeam .= '<div id="graph1" class="tabla" style="display: none; width: 100% !important;">';
		    require 'team-GraphVoleibol.php';
		    $htmlTeam .= '</div>';
		}else{
		    
		}
	}
            
	/*-----------------------------------------------------------------------------------------------------------------------------
	-----------------------------------------------------------------------------------------------------------------------------*/
	$htmlTeam .= '<div id="jugadores" class="tabla" style="display: none; width: 100% !important;">';
	if(app_sport_uses_soccer((int) $Config->getSport())){
	    require 'team-Players.php';
	}
	if(app_sport_uses_voleibol((int) $Config->getSport())){
	    require 'team-PlayersVoleibol.php';
	}
	if(app_sport_uses_basket((int) $Config->getSport())){
	    require 'team-PlayersBasket.php';
	}
    $htmlTeam .= '</div>';
            
	/*-----------------------------------------------------------------------------------------------------------------------------
	-----------------------------------------------------------------------------------------------------------------------------*/
	if($Config->tarjetacambios == 1){
	    $htmlTeam .= '<div id="documentos" class="tabla" style="display: none; width: 100% !important;">';
    	require 'team-Docs.php';
        $htmlTeam .= '</div>';	
	}
    
	
	$htmlTeam .= '</div>';
    $htmlTeam .= '</div>';
    $htmlTeam .= '</div>';
    $htmlTeam .= '</div>';
    $retunData = array('status' => '1', 'message' => 'Success.', 'dataTeam' => $htmlTeam);
    $Config->Close();
    echo json_encode($retunData);
?>