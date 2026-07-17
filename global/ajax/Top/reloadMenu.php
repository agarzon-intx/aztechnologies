<?php
    session_start();
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
if (!defined('APP_SITE_ROOT')) {
	$___d = __DIR__;
	while ($___d !== dirname($___d)) {
		$___p = $___d . DIRECTORY_SEPARATOR . 'site_paths.php';
		if (is_readable($___p)) {
			require_once $___p;
			break;
		}
		$___d = dirname($___d);
	}
}
	require("membersite_config.php");
$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('reloadMenu.php');

	$__langCk = $Config->getAlias() . 'language';
	if (!isset($_COOKIE[$__langCk]) || $_COOKIE[$__langCk] === '') {
		$Config->LoadLanguage();
		$__lang = $Config->lan;
	} else {
		$__lang = $_COOKIE[$__langCk];
	}
	include 'lang.' . $__lang . '.php';

    $retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');

	$__a = $Config->getAlias();
	$Season = (isset($_COOKIE[$__a . 'season']) && $_COOKIE[$__a . 'season'] !== '') ? $_COOKIE[$__a . 'season'] : '0';
	$Category = (isset($_COOKIE[$__a . 'category']) && $_COOKIE[$__a . 'category'] !== '') ? $_COOKIE[$__a . 'category'] : '0';
	$Config->LoadFlags();

    $htmlMenu = '';

    //$htmlMenu .= '""""' . $_COOKIE[$Config->getAlias() . 'language'] . '""""';
	$htmlMenu .= '<ul class="navbar-nav">';

    //echo $Config->coachjuegos . ' -- ' . date('N') . ' vs ' . $Config->coachjuegosdiainicial . ' -- ' . date('N') . ' vs ' . $Config->coachjuegosdiafinal . ' -- ' . date('H:i:s') . ' vs ' . $Config->coachjuegoshorafinal;
    //echo ($Config->coachjuegos == 1) . ' && (' . (date('N')>=$Config->coachjuegosdiainicial) . ' && ' . (date('N')<=$Config->coachjuegosdiafinal) . ' && ' . var_dump((date('H:i:s') < $Config->coachjuegoshorafinal)) . ')';
	if (session_status() == PHP_SESSION_ACTIVE and isset($_SESSION[$Config->getAlias() . "equipo"])) {
        if($_SESSION[$Config->getAlias() . 'equipo'] == 0 || $_SESSION[$Config->getAlias() . 'equipo'] == -1){
            if($_SESSION[$Config->getAlias() . 'equipo'] == 0){
                $htmlMenu .= '<li class="nav-item">
    							<a data-bs-toggle="collapse" href="#menu1" class="nav-link text-white " aria-controls="menu1" role="button" aria-expanded="false">
    								<i class="material-symbols-rounded {% if page.brand == \'RTL\' %}ms-2{% else %} me-2{% endif %}">settings</i>
    									<span class="nav-link-text ms-2 ps-1">' . $lang['121'] . '</span>
    							</a>
    							<div class="collapse " id="menu1" style="margin-left: 5px;">
    								<ul class="nav ">
    
    									<li class="nav-item ">
    										<a class="nav-link text-white " aria-expanded="false" onClick="configManagementShow(); toggleSidenav();">
    											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 16px;">settings</i> </span>
    											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['121'] . ' </span>
    										</a>
    									</li>
    
    
    									<li class="nav-item ">
    										<a class="nav-link text-white " aria-expanded="false" onclick="colorManagementShow(); toggleSidenav();">
    											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 16px;">palette</i> </span>
    											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['114'] . '  </span>
    										</a>
    									</li>
    
    									<li class="nav-item ">
    										<a class="nav-link text-white " aria-expanded="false" onclick="userManagementShow(); toggleSidenav();">
    											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 16px;">emoji_events</i> </span>
    											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['103'] . '  </span>
    										</a>
    									</li>
    									
    								</ul>
    							</div>
    						</li>';
                $htmlMenu .= '<li class="nav-item">
    							<a data-bs-toggle="collapse" href="#menu2" class="nav-link text-white " aria-controls="menu2" role="button" aria-expanded="false">
    								<i class="material-symbols-rounded {% if page.brand == \'RTL\' %}ms-2{% else %} me-2{% endif %}">emoji_events</i>
    									<span class="nav-link-text ms-2 ps-1">' . $lang['104'] . '</span>
    							</a>
    							<div class="collapse " id="menu2" style="margin-left: 5px;">
    								<ul class="nav ">
    
    									<li class="nav-item ">
    										<a class="nav-link text-white " aria-expanded="false" onClick="tournamentManagementShow(); toggleSidenav();">
    											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 16px;">emoji_events</i> </span>
    											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['105'] . ' </span>
    										</a>    
    									</li>
                                        					<li class="nav-item">
                                        						<a data-bs-toggle="collapse" href="#menu2-1" class="nav-link text-white " aria-controls="menu2-1" role="button" aria-expanded="false">
                                        							<i class="material-symbols-rounded {% if page.brand == \'RTL\' %}ms-2{% else %} me-2{% endif %}" style="font-size: 16px;">date_range</i>
                                        							<span class="nav-link-text ms-2 ps-1">' . $lang['108'] . '</span>
                                        						</a>
                                        						<div class="collapse " id="menu2-1" style="margin-left: 5px;">
                                        							<ul class="nav ">
                                        
                                        							<li class="nav-item ">
                										<a class="nav-link text-white " aria-expanded="false" onclick="calendarManagementShow(); toggleSidenav();">
                											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 14px;">date_range</i> </span>
                											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['133'] . '  </span>
                										</a>
                									</li>
                
                
                									<li class="nav-item ">
                										<a class="nav-link text-white " aria-expanded="false" onclick="weekManagementShow(); toggleSidenav();">
                											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 14px;">date_range</i> </span>
                											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['108'] . '  </span>
                										</a>
                									</li>
                                        			
                                        		</ul>
                                        	</div>
                                        </li>
    								</ul>
    							</div>
    				</li>';
    						
    			$htmlMenu .= '<li class="nav-item">
    							<a data-bs-toggle="collapse" href="#menu3" class="nav-link text-white " aria-controls="menu3" role="button" aria-expanded="false">
    								<i class="material-symbols-rounded {% if page.brand == \'RTL\' %}ms-2{% else %} me-2{% endif %}">category</i>
    									<span class="nav-link-text ms-2 ps-1">' . $lang['109'] . '</span>
    							</a>
    							<div class="collapse " id="menu3" style="margin-left: 5px;">
    								<ul class="nav ">
    
    									<li class="nav-item ">
    										<a class="nav-link text-white " aria-expanded="false" onClick="categoryManagementShow(); toggleSidenav();">
    											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 16px;">category</i> </span>
    											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['109'] . ' </span>
    										</a>
    									</li>
    									
    								</ul>
    							</div>
    						</li>';
    						
    			$htmlMenu .= '<li class="nav-item">
    							<a data-bs-toggle="collapse" href="#menu4" class="nav-link text-white " aria-controls="menu4" role="button" aria-expanded="false">
    								<i class="material-symbols-rounded {% if page.brand == \'RTL\' %}ms-2{% else %} me-2{% endif %}">public</i>
    									<span class="nav-link-text ms-2 ps-1">' . $lang['110'] . '</span>
    							</a>
    							<div class="collapse " id="menu4" style="margin-left: 5px;">
    								<ul class="nav ">
    
    									<li class="nav-item ">
    										<a class="nav-link text-white " aria-expanded="false" onClick="fieldManagementShow(); toggleSidenav();">
    											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 16px;">public</i> </span>
    											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['110'] . ' <b class="caret"></b></span>
    										</a>
    									</li>
    									
    								</ul>
    							</div>
    						</li>';
    						
    			$htmlMenu .= '<li class="nav-item">
    							<a data-bs-toggle="collapse" href="#menu5" class="nav-link text-white " aria-controls="menu5" role="button" aria-expanded="false">
    								<i class="material-symbols-rounded {% if page.brand == \'RTL\' %}ms-2{% else %} me-2{% endif %}">folder_shared</i>
    									<span class="nav-link-text ms-2 ps-1">' . $lang['112'] . '</span>
    							</a>
    							<div class="collapse " id="menu5" style="margin-left: 5px;">
    								<ul class="nav ">
    
    									<li class="nav-item ">
    										<a class="nav-link text-white " aria-expanded="false" onClick="teamsManagementAdminCategoryShow(); toggleSidenav();">
    											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 16px;">groups</i> </span>
    											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['113'] . ' <b class="caret"></b></span>
    										</a>
    									</li>
    
    									<li class="nav-item ">
    										<a class="nav-link text-white " aria-expanded="false" onClick="playersManagementAdminCategoryShow(); toggleSidenav();">
    											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 16px;">account_circle</i> </span>
    											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['115'] . ' <b class="caret"></b></span>
    										</a>
    									</li>
    
    									<li class="nav-item ">
    										<a class="nav-link text-white " aria-expanded="false" onClick="playersManagementAdminShowPrintList(); toggleSidenav();">
    											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 16px;">badge</i> </span>
    											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['123'] . ' <b class="caret"></b></span>
    										</a>
    									</li>
    									
    								</ul>
    							</div>
    						</li>';
    						
    			$htmlMenu .= '<li class="nav-item">
    							<a data-bs-toggle="collapse" href="#menu6" class="nav-link text-white " aria-controls="menu6" role="button" aria-expanded="false">
    								<i class="material-symbols-rounded {% if page.brand == \'RTL\' %}ms-2{% else %} me-2{% endif %}">scoreboard</i>
    									<span class="nav-link-text ms-2 ps-1">' . $lang['100'] . '</span>
    							</a>
    							<div class="collapse " id="menu6" style="margin-left: 5px;">
    								<ul class="nav ">
    
    									<li class="nav-item ">
    										<a class="nav-link text-white " aria-expanded="false" onClick="loadWeeksAdmin(0); toggleSidenav();">
    											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 16px;">scoreboard</i> </span>
    											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['101'] . ' <b class="caret"></b></span>
    										</a>
    									</li>';
    			$htmlMenu .= '      </ul>
    							</div>';
    			$htmlMenu .= '</li>';
    			
    			$htmlMenu .= '<li class="nav-item">
    							<a data-bs-toggle="collapse" href="#menu7" class="nav-link text-white " aria-controls="menu7" role="button" aria-expanded="false">
    								<i class="material-symbols-rounded {% if page.brand == \'RTL\' %}ms-2{% else %} me-2{% endif %}">notifications</i>
    									<span class="nav-link-text ms-2 ps-1">' . $lang['116'] . '</span>
    							</a>
    							<div class="collapse " id="menu7" style="margin-left: 5px;">
    								<ul class="nav ">
    
    									<li class="nav-item ">
    										<a class="nav-link text-white " aria-expanded="false" onClick="alertManagementShow(); toggleSidenav();">
    											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 16px;">notifications</i> </span>
    											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['117'] . ' <b class="caret"></b></span>
    										</a>
    									</li>
    									
    								</ul>
    							</div>
    						</li>';
    						
    			$htmlMenu .= '<li class="nav-item">
    							<a data-bs-toggle="collapse" href="#menu8" class="nav-link text-white " aria-controls="menu8" role="button" aria-expanded="false">
    								<i class="material-symbols-rounded {% if page.brand == \'RTL\' %}ms-2{% else %} me-2{% endif %}">description</i>
    									<span class="nav-link-text ms-2 ps-1">' . $lang['118'] . '</span>
    							</a>
    							<div class="collapse " id="menu8" style="margin-left: 5px;">
    								<ul class="nav ">
    
    									<li class="nav-item ">
    										<a class="nav-link text-white " aria-expanded="false" onClick="memoManagementShow(); toggleSidenav();">
    											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 16px;">description</i> </span>
    											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['118'] . ' <b class="caret"></b></span>
    										</a>
    									</li>
    									
    								</ul>
    							</div>
    						</li>';
    						
    			/*
    			$htmlMenu .= '<li class="nav-item">
    							<a data-bs-toggle="collapse" href="#menu9" class="nav-link text-white " aria-controls="menu9" role="button" aria-expanded="false">
    							 <i class="material-symbols-rounded">group</i> 
    									<span class="nav-link-text ms-2 ps-1">' . $lang['134'] . '</span>
    							</a>
    							<div class="collapse " id="menu9" style="margin-left: 5px;">
    								<ul class="nav ">
    
    									<li class="nav-item ">
    										<a class="nav-link text-white " aria-expanded="false" onClick="refereeManagementShow(); toggleSidenav();">
    											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 16px;">group</i> </span>
    											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['135'] . ' <b class="caret"></b></span>
    										</a>
    									</li>
    									
    								</ul>
    							</div>
    						</li>';
    			*/
    			/*
    			$htmlMenu .= '<li class="nav-item">
    							<a data-bs-toggle="collapse" href="#Manual" class="nav-link text-white " aria-controls="Manual" role="button" aria-expanded="false">
    								<i class="material-symbols-rounded {% if page.brand == \'RTL\' %}ms-2{% else %} me-2{% endif %}">video_library</i>
    									<span class="nav-link-text ms-2 ps-1">' . $lang['124'] . '</span>
    							</a>
    							<div class="collapse " id="Manual" style="margin-left: 5px;">
    								<ul class="nav ">
    									<li class="nav-item ">
    										<a class="nav-link text-white " data-bs-toggle="collapse" aria-expanded="false" href="#createPlayer">
    											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 16px;">featured_play_list</i> </span>
    											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['125'] . ' <b class="caret"></b></span>
    										</a>
    										<div class="collapse " id="createPlayer" style="margin-left: 5px;">
    											<ul class="nav nav-sm flex-column">
    												<li class="nav-item">
    													<a class="nav-link text-white " href="https://youtu.be/I1mHReQQQjQ" target=\"_blank\">
    														<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 14px;">play_arrow</i> </span>
    														<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['126'] . ' </span>
    													</a>
    												</li>
    												<li class="nav-item">
    													<a class="nav-link text-white " href="https://youtu.be/tIAcs8NT31w" target=\"_blank\">
    														<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 14px;">play_arrow</i> </span>
    														<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['127'] . ' </span>
    													</a>
    												</li>
    												<li class="nav-item">
    													<a class="nav-link text-white " href="https://youtu.be/E-9TJc3NAY8" target=\"_blank\">
    														<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 14px;">play_arrow</i> </span>
    														<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['128'] . ' </span>
    													</a>
    												</li>
    												<li class="nav-item">
    													<a class="nav-link text-white " href="https://youtu.be/W3oInpSnixc" target=\"_blank\">
    														<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 14px;">play_arrow</i> </span>
    														<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['129'] . ' </span>
    													</a>
    												</li>
    											</ul>
    										</div>
    									</li>
    								</ul>
    							</div>
    						</li>';
    			*/
    			/*
    			$htmlMenu .= '<li class="nav-item">
    							<a data-bs-toggle="collapse" href="#Reglamento" class="nav-link text-white " aria-controls="Reglamento" role="button" aria-expanded="false">
    								<i class="material-symbols-rounded {% if page.brand == \'RTL\' %}ms-2{% else %} me-2{% endif %}">library_books</i>
    									<span class="nav-link-text ms-2 ps-1">' . $lang['304'] . '</span>
    							</a>
    							<div class="collapse " id="Reglamento" style="margin-left: 5px;">
    								<ul class="nav ">
    									<li class="nav-item ">
    										<a class="nav-link text-white " href="imagenes/reglamentoLigaPremierDeVeteranos2017.docx" target="_blank">
    											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 16px;">menu_book</i> </span>
    											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['301'] . ' </span>
    										</a>
    									</li>
    									<li class="nav-item ">
    										<a class="nav-link text-white " href="imagenes/Estatutos Generales2017.docx" target="_blank">
    											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 16px;">menu_book</i> </span>
    											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['302'] . ' </span>
    										</a>
    									</li>
    								</ul>
    							</div>';
    			$htmlMenu .= '</li>';
    			*/
            }
            if($_SESSION[$Config->getAlias() . 'equipo'] == -1){
    			$htmlMenu .= '<li class="nav-item">
    							<a data-bs-toggle="collapse" href="#menu6" class="nav-link text-white " aria-controls="menu6" role="button" aria-expanded="false">
    								<i class="material-symbols-rounded {% if page.brand == \'RTL\' %}ms-2{% else %} me-2{% endif %}">scoreboard</i>
    									<span class="nav-link-text ms-2 ps-1">' . $lang['100'] . '</span>
    							</a>
    							<div class="collapse " id="menu6" style="margin-left: 5px;">
    								<ul class="nav ">
    
    									<li class="nav-item ">
    										<a class="nav-link text-white " aria-expanded="false" onClick="loadWeeksAdmin(0); toggleSidenav();">
    											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 16px;">scoreboard</i> </span>
    											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['101'] . ' <b class="caret"></b></span>
    										</a>
    									</li>
    									
    								</ul>
    							</div>
    						</li>';
            }
		}else{
            $htmlMenu .= '<li class="nav-item">
							<a data-bs-toggle="collapse" href="#menu1" class="nav-link text-white " aria-controls="menu1" role="button" aria-expanded="false">
								<i class="material-symbols-rounded {% if page.brand == \'RTL\' %}ms-2{% else %} me-2{% endif %}">account_circle</i>
									<span class="nav-link-text ms-2 ps-1">' . $lang['115'] . '</span>
							</a>
							<div class="collapse " id="menu1" style="margin-left: 5px;">
								<ul class="nav ">

									<li class="nav-item ">
										<a class="nav-link text-white " aria-expanded="false" onClick="playersManagementTeamCategoryShow(' . $_SESSION[$Config->getAlias() . 'equipo'] . '); toggleSidenav();">
											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 16px;">account_circle</i> </span>
											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['115'] . ' </span>
										</a>
									</li>
									
								</ul>
							</div>
						</li>';
			$diafinal = $Config->coachjuegosdiafinal;
			if( ($Config->coachjuegos == 1)&&
			    ((date('N')>=$Config->coachjuegosdiainicial))&&
			    (   (date('N')<$diafinal)||
			        (((date('N')==$diafinal)
			        &&(date('H:i:s') < $Config->coachjuegoshorafinal))))){
                $htmlMenu .= '<li class="nav-item">
    							<a data-bs-toggle="collapse" href="#menu2" class="nav-link text-white " aria-controls="menu2" role="button" aria-expanded="false">
    								<i class="material-symbols-rounded {% if page.brand == \'RTL\' %}ms-2{% else %} me-2{% endif %}">account_circle</i>
    									<span class="nav-link-text ms-2 ps-1">' . $lang['100'] . '</span>
    							</a>
    							<div class="collapse " id="menu2" style="margin-left: 5px;">
    								<ul class="nav ">
    
    									<li class="nav-item ">
    										<a class="nav-link text-white " aria-expanded="false" onClick="loadWeeksAdminC(0); toggleSidenav();">
    											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 16px;">account_circle</i> </span>
    											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['101'] . ' </span>
    										</a>
    									</li>
    									
    								</ul>
    							</div>
    						</li>';
			}
			// GamesReferee: same coach-style UI for users whose email matches an active Arbitro
			$isGamesReferee = false;
			$sessionEmail = isset($_SESSION[$Config->getAlias() . 'email']) ? trim((string) $_SESSION[$Config->getAlias() . 'email']) : '';
			if ($sessionEmail !== '') {
				$sqlRef = "SELECT Arbitro_ID FROM $schema.Arbitro WHERE Correo = " . $Config->quote($sessionEmail) . " AND Estatus = 1 LIMIT 1";
				$resultRef = $Config->query($sqlRef);
				if ($resultRef && $resultRef->num_rows > 0) {
					$isGamesReferee = true;
				}
			}
			if ($isGamesReferee) {
                $htmlMenu .= '<li class="nav-item">
    							<a data-bs-toggle="collapse" href="#menuGamesReferee" class="nav-link text-white " aria-controls="menuGamesReferee" role="button" aria-expanded="false">
    								<i class="material-symbols-rounded {% if page.brand == \'RTL\' %}ms-2{% else %} me-2{% endif %}">sports</i>
    									<span class="nav-link-text ms-2 ps-1">' . $lang['135'] . '</span>
    							</a>
    							<div class="collapse " id="menuGamesReferee" style="margin-left: 5px;">
    								<ul class="nav ">
    									<li class="nav-item ">
    										<a class="nav-link text-white " aria-expanded="false" onClick="loadWeeksAdminR(0); toggleSidenav();">
    											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 16px;">scoreboard</i> </span>
    											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['101'] . ' </span>
    										</a>
    									</li>
    								</ul>
    							</div>
    						</li>';
			}
			/*
			$htmlMenu .= '<li class="nav-item">
							<a data-bs-toggle="collapse" href="#Manual" class="nav-link text-white " aria-controls="Manual" role="button" aria-expanded="false">
								<i class="material-symbols-rounded {% if page.brand == \'RTL\' %}ms-2{% else %} me-2{% endif %}">video_library</i>
									<span class="nav-link-text ms-2 ps-1">' . $lang['124'] . '</span>
							</a>
							<div class="collapse " id="Manual" style="margin-left: 5px;">
								<ul class="nav ">
									<li class="nav-item ">
										<a class="nav-link text-white " data-bs-toggle="collapse" aria-expanded="false" href="#createPlayer">
											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 16px;">featured_play_list</i> </span>
											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['125'] . ' <b class="caret"></b></span>
										</a>
										<div class="collapse " id="createPlayer" style="margin-left: 5px;">
											<ul class="nav nav-sm flex-column">
												<li class="nav-item">
													<a class="nav-link text-white " href="https://youtu.be/I1mHReQQQjQ" target=\"_blank\">
														<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 14px;">play_arrow</i> </span>
														<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['126'] . ' </span>
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link text-white " href="https://youtu.be/tIAcs8NT31w" target=\"_blank\">
														<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 14px;">play_arrow</i> </span>
														<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['127'] . ' </span>
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link text-white " href="https://youtu.be/E-9TJc3NAY8" target=\"_blank\">
														<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 14px;">play_arrow</i> </span>
														<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['128'] . ' </span>
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link text-white " href="https://youtu.be/W3oInpSnixc" target=\"_blank\">
														<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 14px;">play_arrow</i> </span>
														<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['129'] . ' </span>
													</a>
												</li>
											</ul>
										</div>
									</li>
								</ul>
							</div>
						</li>';
			*/
			/*
			$htmlMenu .= '<li class="nav-item">
							<a data-bs-toggle="collapse" href="#Reglamento" class="nav-link text-white " aria-controls="Reglamento" role="button" aria-expanded="false">
								<i class="material-symbols-rounded {% if page.brand == \'RTL\' %}ms-2{% else %} me-2{% endif %}">library_books</i>
									<span class="nav-link-text ms-2 ps-1">' . $lang['304'] . '</span>
							</a>
							<div class="collapse " id="Reglamento" style="margin-left: 5px;">
								<ul class="nav ">
									<li class="nav-item ">
										<a class="nav-link text-white " href="imagenes/reglamentoLigaPremierDeVeteranos2017.docx" target="_blank">
											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 16px;">menu_book</i> </span>
											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['301'] . ' </span>
										</a>
									</li>
									<li class="nav-item ">
										<a class="nav-link text-white " href="imagenes/Estatutos Generales2017.docx" target="_blank">
											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 16px;">menu_book</i> </span>
											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['302'] . ' </span>
										</a>
									</li>
								</ul>
							</div>';
			$htmlMenu .= '</li>';
			*/
        }
        if (session_status() == PHP_SESSION_ACTIVE and isset($_SESSION[$Config->getAlias() . "equipo"])) {
		$htmlMenu .='<li class="nav-item">
						<hr class="horizontal light">
						<a class="nav-link text-white" onclick="logout(); toggleSidenav();">
							<span class="sidenav-mini-icon"> <i class="material-symbols-rounded">logout</i> </span>
							<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['119'] . ' </span>
						</a>
					</li>';
    	}else{
    		$htmlMenu .= '<li><a onclick="showLogin();" style="padding-top: 10;padding-right: 0px;"><font color="#3498db" style="font-size: 12px;">' . $lang['120'] . '</font></a></li>';
    	}
    }else{
        /*
        $htmlMenu .= '<li class="nav-item">
							<a data-bs-toggle="collapse" href="#Minuta" class="nav-link text-white " aria-controls="Minuta" role="button" aria-expanded="false">
								<i class="material-symbols-rounded {% if page.brand == \'RTL\' %}ms-2{% else %} me-2{% endif %}">image</i>
									<span class="nav-link-text ms-2 ps-1">' . $lang['118'] . '</span>
							</a>
							<div class="collapse " id="Minuta">
								<ul class="nav">
									<li class="nav-item ">
										<a class="nav-link text-white " aria-expanded="false" onClick="memoManagementShow(); toggleSidenav();">
											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded">mail</i> </span>
											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['118'] . ' <b class="caret"></b></span>
										</a>
									</li>
								</ul>
							</div>';
		$htmlMenu .= "</li>";
		*/
		/*
		$htmlMenu .= '<li class="nav-item">
							<a data-bs-toggle="collapse" href="#Manual" class="nav-link text-white " aria-controls="Manual" role="button" aria-expanded="false">
								<i class="material-symbols-rounded {% if page.brand == \'RTL\' %}ms-2{% else %} me-2{% endif %}">video_library</i>
									<span class="nav-link-text ms-2 ps-1">' . $lang['124'] . '</span>
							</a>
							<div class="collapse " id="Manual" style="margin-left: 5px;">
								<ul class="nav ">
									<li class="nav-item ">
										<a class="nav-link text-white " data-bs-toggle="collapse" aria-expanded="false" href="#createPlayer">
											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 16px;">featured_play_list</i> </span>
											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['125'] . ' <b class="caret"></b></span>
										</a>
										<div class="collapse " id="createPlayer" style="margin-left: 5px;">
											<ul class="nav nav-sm flex-column">
												<li class="nav-item">
													<a class="nav-link text-white " href="https://youtu.be/I1mHReQQQjQ" target=\"_blank\">
														<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 14px;">play_arrow</i> </span>
														<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['126'] . ' </span>
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link text-white " href="https://youtu.be/tIAcs8NT31w" target=\"_blank\">
														<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 14px;">play_arrow</i> </span>
														<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['127'] . ' </span>
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link text-white " href="https://youtu.be/E-9TJc3NAY8" target=\"_blank\">
														<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 14px;">play_arrow</i> </span>
														<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['128'] . ' </span>
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link text-white " href="https://youtu.be/W3oInpSnixc" target=\"_blank\">
														<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 14px;">play_arrow</i> </span>
														<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['129'] . ' </span>
													</a>
												</li>
											</ul>
										</div>
									</li>
								</ul>
							</div>
						</li>';
		*/
	    /*
	    $htmlMenu .= '<li class="nav-item">
							<a data-bs-toggle="collapse" href="#Reglamento" class="nav-link text-white " aria-controls="Reglamento" role="button" aria-expanded="false">
								<i class="material-symbols-rounded {% if page.brand == \'RTL\' %}ms-2{% else %} me-2{% endif %}">library_books</i>
									<span class="nav-link-text ms-2 ps-1">' . $lang['304'] . '</span>
							</a>
							<div class="collapse " id="Reglamento" style="margin-left: 5px;">
								<ul class="nav ">
									<li class="nav-item ">
										<a class="nav-link text-white " href="imagenes/reglamentoLigaPremierDeVeteranos2017.docx" target="_blank">
											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 16px;">menu_book</i> </span>
											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['301'] . ' </span>
										</a>
									</li>
									<li class="nav-item ">
										<a class="nav-link text-white " href="imagenes/Estatutos Generales2017.docx" target="_blank">
											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: 16px;">menu_book</i> </span>
											<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['302'] . ' </span>
										</a>
									</li>
								</ul>
							</div>';
		$htmlMenu .= '</li>';
		*/
		if (session_status() == PHP_SESSION_ACTIVE and isset($_SESSION[$Config->getAlias() . "equipo"])) {
		$htmlMenu .='<li class="nav-item">
						<hr class="horizontal light">
						<a class="nav-link text-white" onclick="logout(); toggleSidenav();">
							<span class="sidenav-mini-icon"> <i class="material-symbols-rounded">logout</i> </span>
							<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['119'] . ' </span>
						</a>
					</li>';
    	}else{
    		$htmlMenu .= '<li class="nav-item">
						<hr class="horizontal light">
						<a class="nav-link text-white" onclick="showLogin(); toggleSidenav();">
							<span class="sidenav-mini-icon"> <i class="material-symbols-rounded">login</i> </span>
							<span class="sidenav-normal  ms-2  ps-1"> ' . $lang['120'] . ' </span>
						</a>
					</li>';
		}
	}
	$htmlMenu .= '<li class="nav-item mt-3 d-md-block d-lg-block d-xl-none">
          <hr class="horizontal light"><h6 class="ps-4  ms-2 text-uppercase text-xs font-weight-bolder text-white">' . $lang['112'] . '</h6>
        </li>
		<li class="nav-item d-md-block d-lg-block d-xl-none" id="menuteams">
						
							<span>';
	$sqlPenales1 = "when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 and l.Jugado then 1";
	$sqlPenales2 = "0";
	$sqlPenales3 = "when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 and l.Jugado then 1";
	$sqlPenales4 = "0";
	$sqlPenales5 = "when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 and v.Jugado then 1 ";
	$sqlByeWeekPoints1 = "";
	$sqlByeWeekPoints2 = "";
	$sqlByeWeekPoints3 = "0 ";
	if($Config->EmpatesPenales == ""){
		$sqlPenales1 = "when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local <> l.Penal_Visitante and l.Jugado then 1 ";
		$sqlPenales2 = "case 
					when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local > l.Penal_Visitante and l.Jugado then 1
					else 0
				end as ";
		$sqlPenales3 = "when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local > l.Penal_Visitante and l.Jugado then 2
					when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local < l.Penal_Visitante and l.Jugado then 1 ";
		$sqlPenales4 = "case 
					when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local < v.Penal_Visitante and v.Jugado then 1
					else 0
				end as ";
		$sqlPenales5 = "when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local < v.Penal_Visitante and v.Jugado then 2 
					when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local > v.Penal_Visitante and v.Jugado then 1 ";
	}
	if($Config->ByeWeekPoints == 1){
		$sqlByeWeekPoints1 = "when Equipo_ID = l.Local_ID and l.Visitante_ID is null then 3 ";
		$sqlByeWeekPoints2 = "when Equipo_ID = l.Local_ID and l.Visitante_ID is null then 1 ";
		$sqlByeWeekPoints3 = "" . $Config->ByeWeekPointsGoals . " ";
	}
	
	$sql = "SET @rank:=0;";
	$Config->query($sql);
	$sql = "SELECT @rank:=@rank+1 AS rank, Logo, Equipo_ID, Equipo_DESC, Equipo_FULLDESC, JJ, JG, JE, JP, GF, GC, DIFF, Puntos, Reales, Extra
		from (
		Select 	Logo, 
				j.Equipo_ID, 
				Equipo_DESC,
				Equipo_FULLDESC, 
				fuerza, ifnull(sum(Juegos),0) as JJ, 
				ifnull(sum(JG),0) as JG, 
				ifnull(sum(JE),0) as JE, 
				ifnull(sum(JP),0) as JP, 
				ifnull(sum(Puntos),0) as Puntos, 
				ifnull(sum(Puntos),0)+ifnull(Sum(Extra),0)+ifnull(sum(ExtraEquipo),0) as Reales, 
				ifnull(Sum(GF),0) as GF, 
				ifnull(Sum(GC),0) as GC, 
				ifnull(Sum(GF),0) - ifnull(Sum(GC),0) as DIFF, 
				ifnull(Sum(Extra),0) Extra, 
				j.Juego_ID
		from (
				select distinct concat(e.Torneo_ID,'-', e.Equipo_ID) Logo, 
						l.Jornada_ID, 
						Equipo_ID, 
						Equipo_DESC, 
						Equipo_FULLDESC,
						Fuerza, 
						Juego_ID,
						case  
							" . $sqlByeWeekPoints1 . "
							when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 then 3 
							" . $sqlPenales1 . "
							else 0
						end as Puntos,
						" . $sqlPenales2 . " Extra,
						case 
							" . $sqlByeWeekPoints1 . "
							when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante then 3 
							" . $sqlPenales3 . "
							else 0
						end + l.Extra_Local as Reales, 
						case 
							when l.Visitante_ID is not null then Gol_Local
							else " . $sqlByeWeekPoints3 . "
						end as GF, 
						Gol_Visitante as GC,
						case 
							" . $sqlByeWeekPoints2 . "
							when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 then 1
							when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 then 1
							when Equipo_ID = l.Local_ID and l.Gol_Local < l.Gol_Visitante and l.Jugado = 1 then 1
							else 
								case when l.Estatus like '5' then 1 else 0 end
						end as Juegos,
						case 
							" . $sqlByeWeekPoints2 . "
							when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 then 1
							else 0
						end as JG,
						case 
							when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 then 1
							else 0
						end as JE,
						case 
							when Equipo_ID = l.Local_ID and l.Gol_Local < l.Gol_Visitante and l.Jugado = 1 then 1
							else 0
						end as JP, 
						l.Extra_Local ExtraEquipo
				from $schema.Equipos e
					left outer join $schema.Juegos l on e.Equipo_ID = l.Local_ID and l.Torneo_ID = $Season
														  and l.Jugado <> 10
				where e.Fuerza = $Category and e.Torneo_ID = $Season and e.Equipo_ID > 0 and Activo = 1
				UNION
				select distinct concat(e.Torneo_ID,'-', e.Equipo_ID) Logo, 
						v.Jornada_ID, 
						Equipo_ID, 
						Equipo_DESC, 
						Equipo_FULLDESC,
						Fuerza, 
						Juego_ID,
						case 
							when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 3 
							when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 then 1 
							else 0
						end as Puntos, 
						" . $sqlPenales4 . " Extra, 
						case 
							when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 3 
							" . $sqlPenales5 . "
							else 0
						end + v.Extra_Visitante as Reales, 
						Gol_Visitante as GF, 
						Gol_Local as GC,
						case 
							when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 1
							when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 then 1
							when Equipo_ID = v.Visitante_ID and v.Gol_Local > v.Gol_Visitante and v.Jugado = 1 then 1 
							else 
								case when v.Estatus like '5' then 1 else 0 end
						end as Juegos ,
						case 
							when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 1
							else 0
						end as JG,
						case 
							when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 then 1 
							else 0
						end as JE,
						case 
							when Equipo_ID = v.Visitante_ID and v.Gol_Local > v.Gol_Visitante and v.Jugado = 1 then 1 
							else 0
						end
					as JP, v.Extra_Visitante ExtraEquipo
				from $schema.Equipos e
					left outer join $schema.Juegos v on e.Equipo_ID = v.Visitante_ID and v.Torneo_ID = $Season
														  and v.Jugado <> 10
				where e.Fuerza = $Category and e.Torneo_ID = $Season  and e.Equipo_ID > 0 and Activo = 1) j
		where ifnull(Jornada_ID, -2) <= (select ifnull(max(Jornada_ID),0)-2 from $schema.Jornada where Torneo_ID = $Season)
			Group by j.Equipo_ID, Equipo_DESC, Fuerza) jj
		order by Reales desc, DIFF desc, GF desc, Equipo_FULLDESC";
	$htmlMenu .= '<div class="container-fluid py-1 px-3 d-md-block d-lg-block d-xl-none" style="width: 87%;"><div class="dropdown">';
    $result = $Config->query($sql);
    $totLogos = $result->num_rows;
    $htmlMenu .= '<a class="btn bg-gradient-dark dropdown-toggle " data-bs-toggle="dropdown" id="navbarDropdownMenuLink0" style="margin-bottom: 0rem;">-- ' . $lang['112-1'] . '</a>';
	$htmlMenu .= '<ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink0">';
	$count = 1;
    if($result){
		if ($result->num_rows > 0) {
			$width = round(860/($result->num_rows));
			$radius = round((860/($result->num_rows))/5);
			if($width > 60){
				 $width = 60;
			}	 $radius = 60/5;
			while($row2 = $result->fetch_assoc()) {
				$htmlMenu .= '<li><a class="dropdown-item"  onclick="loadTeam(' . mb_convert_encoding((string)$row2["Equipo_ID"], 'UTF-8', 'ISO-8859-1') . "," . $_COOKIE[$Config->getAlias() . "season"] . '); toggleSidenav();"><img src="./imagenes/' . mb_convert_encoding((string)$row2["Logo"], 'UTF-8', 'ISO-8859-1') . '.png" style="width: 17px;"/> ' . mb_convert_encoding((string)$row2["Equipo_FULLDESC"], 'UTF-8', 'ISO-8859-1') . '</a></li>';
				
				$count = $count + 1;
			}
		}
    }
    $htmlMenu .= '</ul>';
	$htmlMenu .= '</div></div>';
    $htmlMenu .= "</span></li>";
	
    
	
    $htmlMenu .= '<li class="nav-item">
						<hr class="horizontal light">
							<span style="padding-left: 2rem !important;">';
	// Create connection
    $sql = "SELECT Lenguaje_ID, Lenguaje_DESC, case when Lenguaje_ID = '" . $_COOKIE[$Config->getAlias() . 'language'] . "' then 1 else 0 end as active FROM $schema.Lenguaje
            order by active desc, `order` asc;";
    $result = $Config->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        $selected = false;
        while($row2 = $result->fetch_assoc()) {
			if($row2["active"] == 1){
				$htmlMenu .= '<button class="btn bg-gradient-primary dropdown-toggle " data-bs-toggle="dropdown" id="navbarDropdownLanguage" style="padding-top: 0px; padding-bottom: 0px; margin-bottom: 0px;"><i class="material-symbols-rounded" style="font-size: 13px !important;">language</i> ' . $row2["Lenguaje_DESC"] . '</button>';
				$htmlMenu .= '<ul class="dropdown-menu" aria-labelledby="navbarDropdownLanguage" style="margin-top: 0 !important;padding: 0.0rem 0;margin-left: 31px;margin-right: 44px;">';
			}else{
				$htmlMenu .= '<li><a class="dropdown-item" onclick="console.log(\'' . $row2["Lenguaje_ID"] . '\'); loadLanguage(\'' . $row2["Lenguaje_ID"] . '\', \'' . $Config->getWebSite() . '\');"><i class="material-symbols-rounded" style="font-size: 12px !important;">language</i> ' . $row2["Lenguaje_DESC"] . '</a></li>';
			}
       }
	   $htmlMenu .= '</ul></span>';
    }
    $htmlMenu .= "</li>";
    
    $htmlMenu .= '<li class="nav-item" style="text-align: center;">
						<hr class="horizontal light">
							<span style="padding-left: .5rem !important;">
							<img src="imagenes/ws.png" width="30"> <img src="imagenes/aztechnologies.png" width="126"> <img src="imagenes/LeagueLogo.png" width="90">
				    </li>';    
    $htmlMenu .= '</ul>';
	
    $retunData = array('status' => '1', 'message' => 'Success.', 'dataMenu' => $htmlMenu);
    $Config->Close();
    echo json_encode($retunData);
?>
