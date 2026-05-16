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

	require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'membersite_config.php';
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('changeWeeksReloadWeekSelector.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
	//print_r($_COOKIE);
	$Season = $_COOKIE[$Config->getAlias() . 'season'];
	$Category = $_COOKIE[$Config->getAlias() . 'category'];
	$Week = SanitizeInteger($_POST['Week']);

	$htmlWeeks = '';
	$currentWeek = $Week;
	// Create connection
	$count = 0;
	$WeekRows = 0;

	$htmlWeeks .= '<div class="row" id="weekselectorsection">';
	$htmlWeeks .= '<div class="col-4 col-sm-2 col-md-2 col-lg-2 col-xl-4 col-xxl-4">';
	$htmlWeeks .= '<div class="">' . $lang['690'] . ': </div>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '<div class="col-6 col-sm-3 col-md-2 col-lg-2 col-xl-4 col-xxl-4">';
	$htmlWeeks .= '<div class="dropdown btn-tooltip" data-bs-toggle="tooltip" data-bs-placement="top">';
	$selectedWeek = '';
	// Create connection
	$sql1 = "select distinct j.Jornada_ID as Jornada, j.Jornada_Desc
			from $schema.Jornada as j 
				join $schema.Juegos ju on j.Jornada_ID = ju.Jornada_ID
			    join $schema.Categorias ca on ca.Categoria_ID = $Category and ca.Torneo_Id = $Season and ca.Calendario_Id = j.Calendario_ID
			where j.Torneo_ID = $Season
			order by j.Jornada_ID;";
    //echo $sql1;
	$result = $Config->query($sql1);
	if($result){
		$totJor = $result->num_rows;
		
	}
	
	$sql0 = "select distinct j.Jornada_ID as Jornada, j.Jornada_Desc
			from $schema.Jornada as j 
				join $schema.Juegos ju on j.Jornada_ID = ju.Jornada_ID
			    join $schema.Categorias ca on ca.Categoria_ID = $Category and ca.Torneo_Id = $Season and ca.Calendario_Id = j.Calendario_ID
			where j.Torneo_ID = $Season and j.Jornada_ID = $currentWeek
			order by j.Jornada_ID;";
    //echo $sql;
	$result = $Config->query($sql0);
	if($result){
		$totJor = $result->num_rows;
		if ($result->num_rows > 0) {
			// output data of each row
			//$htmlWeeks .= "         <option value='All'>" . $lang['692'] . "</option>";
			while($row = $result->fetch_assoc()) {
			    if($totJor > 1){
    				$htmlWeeks .= '<a class="btn bg-gradient-dark dropdown-toggle " data-bs-toggle="dropdown" id="navbarDropdownJornada" style="padding-top: 0px; padding-bottom: 0px; margin-bottom: 0px;">' . $row["Jornada_Desc"] . '</a>';
    			}else{
    				$htmlWeeks .= '<a class="btn bg-gradient-dark" data-bs-toggle="dropdown" id="navbarDropdownJornada" style="padding-top: 0px; padding-bottom: 0px; margin-bottom: 0px;" aria-expanded="false">' . $row["Jornada_Desc"] . '</a>';
    			}
				$selectedWeek = $row["Jornada"];
			}
		}
	}
	
	$htmlWeeks .= '<ul class="dropdown-menu" aria-labelledby="navbarDropdownJornada">';
	$sql1 = "select distinct j.Jornada_ID as Jornada, j.Jornada_Desc
			from $schema.Jornada as j 
				join $schema.Juegos ju on ju.Fecha between j.Fecha_Inicio and j.Fecha_Fin
			    join $schema.Categorias ca on ca.Categoria_ID = $Category and ca.Torneo_Id = $Season and ca.Calendario_Id = j.Calendario_ID
			where j.Torneo_ID = $Season and j.Jornada_ID <> $currentWeek
			order by j.Jornada_ID;";
    //echo $sql;
	$result = $Config->query($sql1);
	if($result){
		$totJor = $result->num_rows;
		if ($result->num_rows > 0) {
			// output data of each row
			//$htmlWeeks .= "         <option value='All'>" . $lang['692'] . "</option>";
			while($row = $result->fetch_assoc()) {
				$htmlWeeks .= '<li><a class="dropdown-item" onclick="onChangeWeek(' . $row["Jornada"] . ');">' . $row["Jornada_Desc"] . '</a></li>';
			}
		}
	}
	
	$htmlWeeks .= '</ul>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '<div class="col-1 col-sm-2 col-md-2 col-lg-2 col-xl-4 col-xxl-4">';
	$htmlWeeks .= '<div >';
	$htmlWeeks .= '<img src="./imagenes/refresh.png" width="20" height="20" onclick="loadWeek(' . $selectedWeek . ');" style="margin-left: 10;  margin-top: 2px;">';
	$htmlWeeks .= '<a href="pdf/flyerC.php?Jornada_ID=' . $selectedWeek . '" target="_blank" download=""><img src="imagenes/flyer.png" width="20" height="20"></a>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '</div>';
	
	
	$retunData = array('status' => '1', 'message' => 'Success.', 'dataWeeks' => $htmlWeeks, 'sql0' => $sql0, 'sql1' => $sql1);
    $Config->Close();
    echo json_encode($retunData);
?>