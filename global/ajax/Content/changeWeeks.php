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
	require_once("membersite_config.php");
	require_once dirname(dirname(__DIR__)) . '/include/flyer_download_menu.php';
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('changeWeeks.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');


    $retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
    //print_r($_COOKIE);
    $Season = $_COOKIE[$Config->getAlias() . 'season'];
    $Category = $_COOKIE[$Config->getAlias() . 'category'];
    $htmlWeeks = '';
    
	
	$currentWeek = 0;
	// Create connection
	$sql0 = "select distinct j.Jornada_ID as Jornada, j.Jornada_Desc, 
                    case when k.Jornada_ID is null then 0 else 1 end as Activo 
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
			order by j.Jornada_ID;";
			//echo $sql0;
	$result = $Config->query($sql0);
    $count = 0;
    if($result){
         if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$currentWeek = $row["Jornada"];
			}
		}
	}

	$count = 0;
	$WeekRows = 0;
    $htmlWeeks .= '<div class="container-fluid py-0">';
    $htmlWeeks .= '<div class="row">';
	$htmlWeeks .= '<div id="JornadasContent" class="col-xl-4" style="padding-top: 7px !important;padding-left: 0px !important;padding-right: 0px !important;padding-bottom: 0px !important;">';
	$htmlWeeks .= '<div class="container-fluid" style="padding: 0px;">';
	$htmlWeeks .= '<div class="row align-items-center g-1" id="weekselectorsection">';
	$htmlWeeks .= '<div class="col-4 col-sm-2 col-md-2 col-lg-2 col-xl-4 col-xxl-4">';
	$htmlWeeks .= '<div class="">' . $lang['690'] . ': </div>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '<div class="col-5 col-sm-3 col-md-2 col-lg-2 col-xl-4 col-xxl-4">';
	$htmlWeeks .= '<div class="dropdown btn-tooltip" data-bs-toggle="tooltip" data-bs-placement="top">';
	$selectedWeek = '';
	// Create connection
	$sql1 = "select distinct j.Jornada_ID as Jornada, j.Jornada_Desc
			from $schema.Jornada as j 
				join $schema.Juegos ju on j.Jornada_ID = ju.Jornada_ID
			    join $schema.Categorias ca on ca.Categoria_ID = $Category and ca.Torneo_Id = $Season and ca.Calendario_Id = j.Calendario_ID
			where j.Torneo_ID = $Season and j.Jornada_ID = $currentWeek
			order by j.Jornada_ID;";
    //echo $sql1;
    $totJor = 0;
	$result = $Config->query($sql1);
	if($result){
		$totJor = $result->num_rows;
		//echo $totJor;
		if ($result->num_rows > 0) {
			// output data of each row
			//$htmlWeeks .= "         <option value='All'>" . $lang['692'] . "</option>";
			while($row = $result->fetch_assoc()) {
    				$htmlWeeks .= '<a class="btn bg-gradient-dark dropdown-toggle " data-bs-toggle="dropdown" id="navbarDropdownJornada" style="padding-top: 0px; padding-bottom: 0px; margin-bottom: 0px;">' . $row["Jornada_Desc"] . '</a>';
				$selectedWeek = $row["Jornada"];
			}
		}
	}
	if($totJor == 0){
        $htmlWeeks .= '<a class="btn bg-gradient-dark dropdown-toggle " data-bs-toggle="dropdown" id="navbarDropdownJornada" style="padding-top: 0px; padding-bottom: 0px; margin-bottom: 0px;">0</a>';
        $selectedWeek = -1;
	}
	
	$sql2 = "select distinct j.Jornada_ID as Jornada, j.Jornada_Desc, Jornada_Orden
			from $schema.Jornada as j 
				join $schema.Juegos ju on ju.Fecha between j.Fecha_Inicio and j.Fecha_Fin
			    join $schema.Categorias ca on ca.Categoria_ID = $Category and ca.Torneo_Id = $Season and ca.Calendario_Id = j.Calendario_ID
			where j.Torneo_ID = $Season and j.Jornada_ID <> $currentWeek
			order by Jornada_Orden desc, j.Jornada_ID;";
    //echo $sql;
	$result = $Config->query($sql2);
	if($result){
		$totJor = $result->num_rows;
		if ($result->num_rows > 0) {
			// output data of each row
			$htmlWeeks .= '<ul class="dropdown-menu" aria-labelledby="navbarDropdownJornada">';
			//$htmlWeeks .= "         <option value='All'>" . $lang['692'] . "</option>";
			while($row = $result->fetch_assoc()) {
				$htmlWeeks .= '<li><a class="dropdown-item" onclick="onChangeWeek(' . $row["Jornada"] . ');">' . $row["Jornada_Desc"] . '</a></li>';
			}
			$htmlWeeks .= '</ul>';
		}
	}
	
	
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '<div class="col-3 col-sm-2 col-md-2 col-lg-2 col-xl-4 col-xxl-4">';
	$htmlWeeks .= az_flyer_week_actions_toolbar_html($selectedWeek, $Config->getPath());
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '<script type="text/javascript">';
	$htmlWeeks .= 'loadWeek(' . $selectedWeek . ');';
	$htmlWeeks .= '</script>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '<div class="col-xl-8" style="padding: 0px;">';
	$htmlWeeks .= '<div id="weekTabContent"></div>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '<div class="container-fluid">
						<div class="row">
							<div class="col-lg-12 position-relative z-index-2" style="padding-left: 0; padding-right: 0;">
								<div class="card mb-4 ">
									<div id="weekContent" width="100%" style="height: 550"></div>
								</div>
							</div>
						</div>';
	$htmlWeeks .= '</div>';
	
	$retunData = array('status' => '1', 'message' => 'Success.', 'dataWeeks' => $htmlWeeks, 'sql0' => $sql0, 'sql1' => $sql1, 'sql2' => $sql2);
    $Config->Close();
    echo json_encode($retunData);
