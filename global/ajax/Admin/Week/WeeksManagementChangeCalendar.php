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
	$sessionstat = $fgmembersite->CheckLogin('WeeksManagementChangeCalendar.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
	
	$Season = $_COOKIE[$Config->getAlias() . 'season'];
	$Calendar = 'null';
	$htmlCal = '<div class="container-fluid py-0">
					<div class="row">
						<div id="weeksAdminContent" class="" style="padding-top: 7px !important;padding-left: 0px !important;padding-right: 0px !important;padding-bottom: 0px !important;">
							<div class="container-fluid" style="padding: 0px;">
								<div class="row" id="weekAdminContentCalendar">
									<div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" id="calendarsContent" >
										<div class="container-fluid" style="padding: 0px;">
											<div class="row">
												<div class="col-5 col-sm-5 col-md-5 col-lg-5 col-xl-5 col-xxl-5 d-none d-sm-none d-md-block d-lg-block d-xl-block d-xxl-block">
													<div class="">' . $lang['62'] . ': </div>
												</div>
												<div class="col-8 col-sm-8 col-md-5 col-lg-5 col-xl-5 col-xxl-5" id="weekContentCalendarList">';
	
	$sql0 = "   select distinct a.Calendario_ID, b.Calendario_DESC
                from $schema.Categorias a
                	join $schema.Calendario b on a.Calendario_ID = b.Calendario_ID
                where a.Categoria_ID in ( select Fuerza
                						from $schema.Equipos
                						where Torneo_ID = $Season)
                	and a.Torneo_ID = $Season
                limit 1;";
	$result = $Config->query($sql0);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row2 = $result->fetch_assoc()) {
			$Calendar = $row2["Calendario_ID"];
		}
	}
	
	$sql1 = "select distinct a.Calendario_ID, b.Calendario_DESC
                from $schema.Categorias a
                	join $schema.Calendario b on a.Calendario_ID = b.Calendario_ID
                where Categoria_ID in ( select Fuerza
                						from $schema.Equipos
                						where Torneo_ID = $Season)
                	and a.Torneo_ID = $Season;";
	$result = $Config->query($sql1);
	if ($result->num_rows > 0) {
		$totCal = $result->num_rows;
	}
	
	$sql2 = "   select distinct a.Calendario_ID, b.Calendario_DESC
                from $schema.Categorias a
                	join $schema.Calendario b on a.Calendario_ID = b.Calendario_ID
                where Categoria_ID in ( select Fuerza
                						from $schema.Equipos
                						where Torneo_ID = $Season)
                	and a.Torneo_ID = $Season
                    and a.Calendario_ID = $Calendar;";
	$result = $Config->query($sql2);
	if ($result->num_rows > 0) {
		// output data of each row
		$selected = false;	
		while($row2 = $result->fetch_assoc()) {
			if($totCal > 1){
				$htmlCal .= '						<a class="btn bg-gradient-dark dropdown-toggle" data-bs-toggle="dropdown" id="navbarDropdownMenuLinkCat" style="padding-top: 0px; padding-bottom: 0px; margin-bottom: 0px;" aria-expanded="false">' . $row2["Calendario_DESC"] . '</a>';
			}else{
				$htmlCal .= '						<a class="btn bg-gradient-dark" data-bs-toggle="dropdown" id="navbarDropdownMenuLinkCat" style="padding-top: 0px; padding-bottom: 0px; margin-bottom: 0px;" aria-expanded="false">' . $row2["Calendario_DESC"] . '</a>';
			}
			$Calendar = (string) $row2["Calendario_ID"];
		}
	} else {
	   $htmlCal .= "";
	}
	
	$sql3 = "   select distinct a.Calendario_ID, b.Calendario_DESC
                from $schema.Categorias a
                	join $schema.Calendario b on a.Calendario_ID = b.Calendario_ID
                where Categoria_ID in ( select Fuerza
                						from $schema.Equipos
                						where Torneo_ID = $Season)
                	and a.Torneo_ID = $Season
                    and a.Calendario_ID <> $Calendar;";
	$result = $Config->query($sql3);
	if ($result->num_rows > 0) {
		// output data of each row
		$selected = false;
		$htmlCal .= '								<ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLinkCat">';
		while($row2 = $result->fetch_assoc()) {
			$htmlCal .= '								<li><a class="dropdown-item" onclick="weeksManagementAdminCalendarWeeksShow(' . $row2["Calendario_ID"] . ')">' . $row2["Calendario_DESC"] . '</a></li>';
		}
		$htmlCal .= '								</ul>';
	} else {
		$htmlCal .= "";
	}
	
	$htmlCal .= '									<input type="hidden" id="weeksManagementAdminSelectedCalendar" value="' . $Calendar . '">';
	$htmlCal .= '								</div>
												<div class="col-4 col-sm-4 col-md-2 col-lg-2 col-xl-2 col-xxl-2">
													<div class="dropdowni btn-tooltip" data-bs-toggle="tooltip" data-bs-placement="top">
														<img src="./imagenes/refresh.png" width="20" height="20" onclick="weeksManagementAdminCalendarWeeksShow(($(\'#weeksManagementAdminSelectedCalendar\').val()));" style="margin-left: 10;  margin-top: 2px;">
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
									</div>
								</div>
							</div>
						</div>';
	$htmlCal .= '</div>';
	$htmlCal .= '</div>';
	$htmlCal .= "<div style='width: 50%; float: left'>";
	$htmlCal .= '<div id="weeksContent" width="100%" style="height: 42"></div>';
	$htmlCal .= '</div>';
	$htmlCal .= '</div>';
	$htmlCal .= '</div>';
	$htmlCal .= '<div id="weekContent" width="100%" style="height: auto;"></div>';
	$htmlCal .= '</div>';
	
	$retunData = array('status' => '1', 'message' => 'Success.', 'dataCalendar' => $htmlCal, 'calendar' => $Calendar, 'sql0' => $sql0, 'sql1' => $sql1, 'sql2' => $sql2, 'sql3' => $sql3);
	$Config->Close();
	echo json_encode($retunData);
?>