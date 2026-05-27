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
	$sessionstat = $fgmembersite->CheckLogin('changeWeeksAdmin.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');


    $retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
    //print_r($_COOKIE);
    $Season = $_COOKIE[$Config->getAlias() . 'season'];
    $Category = $_COOKIE[$Config->getAlias() . 'category'];
    $Type = SanitizeInteger($_POST['Type'] ?? 0);
    
    $Config->LoadFlags();
    $hideJuegosXNombre = "";
    if($Config->juegosxnombre == 0){
        $hideJuegosXNombre = "visibility: hidden;";
    }
    
    $hideTeamS = "";
    if($Type === 0){
        $hideTeamS = "visibility: hidden;";
    }
    $htmlWeeks = '';
    
	
	$currentWeek = 0;
	// Create connection
	$sql0 = "select distinct j.Jornada_ID as Jornada, j.Jornada_Desc
			from $schema.Jornada as j 
			    join $schema.Categorias ca on ca.Categoria_ID = $Category and ca.Torneo_Id = $Season and ca.Calendario_Id = j.Calendario_ID
			    join ( 
                        select ifnull( 
                                (   SELECT Jornada_ID 
                                    FROM $schema.Jornada j 
                                        join $schema.Categorias ca on ca.Categoria_ID = $Category and ca.Torneo_Id = $Season and ca.Calendario_Id = j.Calendario_ID 
                                    where j.Torneo_ID = $Season and DATE_ADD(date(now()) , INTERVAL-3 DAY) between Fecha_Inicio and Fecha_Fin LIMIT 1), 
                                (   select  case when now() > max(fecha) 
                                            then (	select Jornada_ID from (select Jornada_ID, fecha, now()  
													from $schema.Jornada j 
                                                        join $schema.Categorias ca on ca.Categoria_ID = $Category and ca.Torneo_Id = $Season and ca.Calendario_Id = j.Calendario_ID
													where j.Torneo_ID = $Season and fecha < now()
													order by fecha desc
													limit 1) a ) 
                                        	else (	select Jornada_ID from (select Jornada_ID, fecha, now()  
													from $schema.Jornada j 
                                                        join $schema.Categorias ca on ca.Categoria_ID = $Category and ca.Torneo_Id = $Season and ca.Calendario_Id = j.Calendario_ID
													where j.Torneo_ID = $Season and fecha > now()
													order by fecha asc
													limit 1) a ) end Jornada_ID 
                                	from $schema.Jornada j 
	                                    join $schema.Categorias ca on ca.Categoria_ID = $Category and ca.Torneo_Id = $Season and ca.Calendario_Id = j.Calendario_ID
	                                where j.Torneo_ID = $Season)
								) Jornada_ID) k on j.Jornada_ID = k.Jornada_ID 
				left outer join $schema.Juegos l on j.Jornada_ID = l.Jornada_ID and l.Torneo_ID = $Season 
			where j.Torneo_ID = $Season
			order by j.Jornada_ID
			limit 1;";
			//echo $sql;
	$result = $Config->query($sql0);
    $count = 0;
    if($result){
         if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$currentWeek = $row["Jornada"];
			}
		}
	}
    $currentTeam = -1;
	$sql00 = "  SELECT distinct Equipo_FULLDESC 
	            FROM $schema.Equipos
                where Torneo_ID = $Season
                    and Equipo_ID in (select Local_ID from $schema.Juegos where Jornada_Id = $currentWeek and Torneo_Id = $Season union select Visitante_ID from $schema.Juegos where Jornada_Id = $currentWeek and Torneo_Id = $Season )
                order by 1 asc
                limit 1;";
			//echo $sql00;
	$result00 = $Config->query($sql00);
    $count = 0;
    if($result00){
         if ($result00->num_rows > 0) {
			while($row00 = $result00->fetch_assoc()) {
				$currentTeam = $row00["Equipo_FULLDESC"];
			}
		}
	}
	$count = 0;
	$WeekRows = 0;
    $htmlWeeks .= '<div class="container-fluid py-0">';
    $htmlWeeks .= '<div class="row">';
	$htmlWeeks .= '<div id="JornadasContent" class="col-12 col-sm-12 col-md-6 col-lg-7 col-xl-7 col-xxl-7" style="padding-top: 7px !important;padding-left: 0px !important;padding-right: 0px !important;padding-bottom: 0px !important;">';
	$htmlWeeks .= '<div class="container-fluid" style="padding: 0px;">';
	$htmlWeeks .= '<div class="row" id="weekadminselectorsection">';
	$htmlWeeks .= '<div class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2 col-xxl-2">';
	$htmlWeeks .= '<div class="">' . $lang['690'] . ': </div>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '<div class="col-5 col-sm-5 col-md-6 col-lg-5 col-xl-5 col-xxl-5">';
	$htmlWeeks .= '<div class="dropdown btn-tooltip" data-bs-toggle="tooltip" data-bs-placement="top">';
	$selectedWeek = '';
	// Create connection
	$sql1 = "select distinct j.Jornada_ID as Jornada, j.Jornada_Desc
			from $schema.Jornada as j 
			    join $schema.Categorias ca on ca.Categoria_ID = $Category and ca.Torneo_Id = $Season and ca.Calendario_Id = j.Calendario_ID
			where j.Torneo_ID = $Season and j.Jornada_ID = $currentWeek
			order by j.Jornada_ID;";
    //echo $sql;
	$result = $Config->query($sql1);
	if($result){
		$totJor = $result->num_rows;
		if ($result->num_rows > 0) {
			// output data of each row
			//$htmlWeeks .= "         <option value='All'>" . $lang['692'] . "</option>";
			while($row = $result->fetch_assoc()) {
				$htmlWeeks .= '<a class="btn bg-gradient-dark dropdown-toggle " data-bs-toggle="dropdown" id="navbarDropdownJornada" style="padding-top: 0px; padding-bottom: 0px; margin-bottom: 0px;">' . $row["Jornada_Desc"] . '</a>';
				$selectedWeek = $row["Jornada"];
			}
		}
	}
	
	$sql2 = "select distinct j.Jornada_ID as Jornada, j.Jornada_Desc
			from $schema.Jornada as j 
			    join $schema.Categorias ca on ca.Categoria_ID = $Category and ca.Torneo_Id = $Season and ca.Calendario_Id = j.Calendario_ID
			where j.Torneo_ID = $Season and j.Jornada_ID <> $currentWeek
			order by j.Jornada_ID;";
    //echo $sql;
	$result = $Config->query($sql2);
	if($result){
		$totJor = $result->num_rows;
		if ($result->num_rows > 0) {
			// output data of each row
			$htmlWeeks .= '<ul class="dropdown-menu" aria-labelledby="navbarDropdownJornada">';
			//$htmlWeeks .= "         <option value='All'>" . $lang['692'] . "</option>";
			while($row = $result->fetch_assoc()) {
				$htmlWeeks .= '<li><a class="dropdown-item" onclick="loadWeekAdmin(' . $row["Jornada"] . ', \'\', $(\'#byTeam\').prop(\'checked\') ? 1 : 0);">' . $row["Jornada_Desc"] . '</a></li>';
			}
			$htmlWeeks .= '</ul>';
		}
	}
	
	
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '<div class="col-1 col-sm-1 col-md-1 col-lg-1 col-xl-1 col-xxl-1">';
	$htmlWeeks .= '<div >';
	$htmlWeeks .= '<img src="./imagenes/refresh.png" width="20" height="20" onclick="loadWeekAdmin(' . $selectedWeek . ', \'\', $(\'#byTeam\').prop(\'checked\') ? 1 : 0);" style="margin-left: 10;  margin-top: 2px;">';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '</div>
	                    <div class="form-check mb-2 col-4 col-sm-4 col-md-3 col-lg-4 col-xl-4 col-xxl-4" style="' . $hideJuegosXNombre . '">
							<input class="form-check-input" type="checkbox" name="byTeam" id="byTeam" onclick="$(\'#weekAdminTabContent\').toggle(); loadWeekAdmin(' . $currentWeek . ', \'' . $currentTeam . '\', $(\'#byTeam\').prop(\'checked\') ? 1 : 0);">
							<label class="custom-control-label" for="byTeam">' . $lang['120000'] . '</label>
						</div>';
	$htmlWeeks .= '<script type="text/javascript">';
	$htmlWeeks .= 'loadWeekAdmin(' . $selectedWeek . ', \'\', $(\'#byTeam\').prop(\'checked\') ? 1 : 0);';
	$htmlWeeks .= '</script>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '<div class="col-12 col-sm-12 col-md-6 col-lg-5 col-xl-5 col-xxl-5" style="padding-top: 7px !important;padding-left: 0px !important;padding-right: 0px !important;padding-bottom: 0px !important;">';
	$htmlWeeks .= '<div id="weekAdminTabContent" style="display: none;">';

	// Create connection


	$count = 0;
	$WeekRows = 0;
	$htmlWeeks .= '<div class="row" id="teamadminselectorsection">';
	$htmlWeeks .= '<div class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2 col-xxl-2">';
	$htmlWeeks .= '<div class="">' . $lang['540'] . ': </div>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '<div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-8 col-xxl-8">';
	$htmlWeeks .= '<div class="dropdown btn-tooltip" data-bs-toggle="tooltip" data-bs-placement="top">';
	$selectedTeam = '';
	// Create connection
	$sql01 = "  SELECT distinct Equipo_FULLDESC 
	            FROM $schema.Equipos
                where Torneo_ID = $Season and Equipo_FULLDESC = '$currentTeam'
                order by 1 asc;";
    //echo $sql01;
	$result01 = $Config->query($sql01);
	if($result01){
		if ($result01->num_rows > 0) {
			// output data of each row
			//$htmlWeeks .= "         <option value='All'>" . $lang['692'] . "</option>";
			while($row01 = $result01->fetch_assoc()) {
				$htmlWeeks .= '<a class="btn bg-gradient-dark dropdown-toggle " data-bs-toggle="dropdown" id="navbarDropdownJornada" style="padding-top: 0px; padding-bottom: 0px; margin-bottom: 0px;">' . $row01["Equipo_FULLDESC"] . '</a>';
				$selectedTeam = $row01["Equipo_FULLDESC"];
				setcookie($Config->getAlias() . "selteam",$selectedTeam,0,'/');
			}
		}
	}
	
	$sql02 = "  SELECT distinct Equipo_FULLDESC
	            FROM $schema.Equipos
                where Torneo_ID = $Season and Equipo_FULLDESC <> '$currentTeam'
                    and Equipo_ID in (select Local_ID from $schema.Juegos where Torneo_Id = $Season union select Visitante_ID from $schema.Juegos where Torneo_Id = $Season )
                order by 1 asc;";
                //echo $sql02;
	$result02 = $Config->query($sql02);
	if($result02){
		$totJor = $result02->num_rows;
		if ($result02->num_rows > 0) {
			// output data of each row
			$htmlWeeks .= '<ul class="dropdown-menu" aria-labelledby="navbarDropdownJornada">';
			//$htmlWeeks .= "         <option value='All'>" . $lang['692'] . "</option>";
			while($row02 = $result02->fetch_assoc()) {
				$htmlWeeks .= '<li><a class="dropdown-item" onclick="loadWeekAdmin(' . $currentWeek . ',\'' . $row02["Equipo_FULLDESC"] . '\', $(\'#byTeam\').prop(\'checked\') ? 1 : 0);">' . $row02["Equipo_FULLDESC"] . '</a></li>';
			}
			$htmlWeeks .= '</ul>';
		}
	}
		$htmlWeeks .= '</div>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '<div class="col-1 col-sm-1 col-md-1 col-lg-1 col-xl-1 col-xxl-1">';
	$htmlWeeks .= '<div >';
	$htmlWeeks .= '<img src="./imagenes/refresh.png" width="20" height="20" onclick="loadWeekAdmin(' . $selectedWeek . ', \'' . $selectedTeam . '\', $(\'#byTeam\').prop(\'checked\') ? 1 : 0);" style="margin-left: 10;  margin-top: 2px;">';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '<div class="container-fluid">
						<div class="row">
							<div class="col-lg-12 position-relative z-index-2" style="padding-left: 0; padding-right: 0;">
								<div class="card mb-4 ">
									<div id="weekAdminContent" width="100%" style="height: 550"></div>
								</div>
							</div>
						</div>
					</div>';	
	$htmlWeeks .= '<div class="container-fluid margin-left" id="rojaInputDiv" style="height: 100%;position: fixed;top: 0px;z-index: -1;background-color: rgb(240 240 240 / 96%);width: 100%;">
						<div class="row">
							<div class="col-lg-12 position-relative z-index-2" style="padding-left: 0; padding-right: 0;">
								<div class="card mb-4 ">
									<div id="rojaInput" style="height: 100%;width: 100%;position: absolute;margin: auto;top: 0; background-color: #f0f0f0c7;"></div>
								</div>
							</div>
						</div>
					</div>';
	$htmlWeeks .= '<div class="container-fluid margin-left" id="gameDocInputDiv" style="height: 100%;position: fixed;top: 0px;z-index: -1;background-color: rgb(240 240 240 / 96%);width: 100%;">
						<div class="row">
							<div class="col-lg-12 position-relative z-index-2" style="padding-left: 0; padding-right: 0;">
								<div class="card mb-4 ">
									<div id="gameDocInput" style="height: 100%;width: 100%;position: absolute;margin: auto;top: 0; background-color: #f0f0f0c7;"></div>
								</div>
							</div>
						</div>
					</div>';

	$retunData = array('status' => '1', 'message' => 'Success.', 'dataWeeksAdmin' => $htmlWeeks, 'sql0' => $sql0, 'sql1' => $sql1, 'sql2' => $sql2);
    $Config->Close();
    echo json_encode($retunData);
?>