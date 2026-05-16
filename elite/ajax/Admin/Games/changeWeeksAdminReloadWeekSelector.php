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
	$sessionstat = $fgmembersite->CheckLogin('changeWeeksReloadWeekSelector.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
	//print_r($_COOKIE);
	$Season = $_COOKIE[$Config->getAlias() . 'season'];
	$Category = $_COOKIE[$Config->getAlias() . 'category'];
	$Week = SanitizeInteger($_POST['Week']);
    $Type = SanitizeInteger($_POST['Type']);
    
    $Config->LoadFlags();
    $hideJuegosXNombre = "";
    if($Config->juegosxnombre == 0){
        $hideJuegosXNombre = "visibility: hidden;";
    }
    

	$htmlWeeks = '';
	$currentWeek = $Week;
	// Create connection
	$count = 0;
	$WeekRows = 0;

	$htmlWeeks .= '<div class="row" id="weekselectorsection">';
	$htmlWeeks .= '<div class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2 col-xxl-2">';
	$htmlWeeks .= '<div class="">' . $lang['690'] . ': </div>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '<div class="col-5 col-sm-5 col-md-6 col-lg-5 col-xl-5 col-xxl-5">';
	$htmlWeeks .= '<div class="dropdown btn-tooltip" data-bs-toggle="tooltip" data-bs-placement="top">';
	$selectedWeek = '';
	// Create connection
	$sql = "select distinct j.Jornada_ID as Jornada, j.Jornada_Desc
			from $schema.Jornada as j 
			    join $schema.Categorias ca on ca.Categoria_ID = $Category and ca.Torneo_Id = $Season and ca.Calendario_Id = j.Calendario_ID
			where j.Torneo_ID = $Season and j.Jornada_ID = $currentWeek
			order by j.Jornada_ID;";
    //echo $sql;
	$result = $Config->query($sql);
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
	
	$htmlWeeks .= '<ul class="dropdown-menu" aria-labelledby="navbarDropdownJornada">';
	$sql1 = "select distinct j.Jornada_ID as Jornada, j.Jornada_Desc
			from $schema.Jornada as j 
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
				$htmlWeeks .= '<li><a class="dropdown-item" onclick="loadWeekAdmin(' . $row["Jornada"] . ', \'\', $(\'#byTeam\').prop(\'checked\') ? 1 : 0);">' . $row["Jornada_Desc"] . '</a></li>';
			}
		}
	}
	
	$htmlWeeks .= '</ul>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '<div class="col-1 col-sm-1 col-md-1 col-lg-1 col-xl-1 col-xxl-1">';
	$htmlWeeks .= '<div >';
	$htmlWeeks .= '<img src="./imagenes/refresh.png" width="20" height="20" onclick="loadWeekAdmin(' . $selectedWeek . ', \'\', $(\'#byTeam\').prop(\'checked\') ? 1 : 0);" style="margin-left: 10;  margin-top: 2px;">';
	$htmlWeeks .= '</div>';
	$checkStat = "checked";
    if($Type == 0){
        $checkStat = "";
    }
    $htmlWeeks .= '</div>
	                    <div class="form-check mb-2 col-4 col-sm-4 col-md-3 col-lg-4 col-xl-4 col-xxl-4" style="' . $hideJuegosXNombre . '">
							<input class="form-check-input" type="checkbox" name="byTeam" id="byTeam" onclick="$(\'#weekAdminTabContent\').toggle(); loadWeekAdmin(' . $currentWeek . ', \'\', $(\'#byTeam\').prop(\'checked\') ? 1 : 0);" ' . $checkStat . '>
							<label class="custom-control-label" for="byTeam">' . $lang['120000'] . '</label>
						</div>';
	$htmlWeeks .= '</div>';
	
	
	$retunData = array('status' => '1', 'message' => 'Success.', 'dataWeeks' => $htmlWeeks, 'sql' => $sql, 'sql1' => $sql1);
    $Config->Close();
    echo json_encode($retunData);
?>