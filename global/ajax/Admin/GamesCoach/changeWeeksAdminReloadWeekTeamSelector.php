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
    $Team = SanitizeText($_POST['Team']);
    $Type = SanitizeInteger($_POST['Type']);


    if($Team == ''){
        $Team = $_COOKIE[$Config->getAlias() . "selteam"];
    }
    setcookie($Config->getAlias() . "selteam",$Team,0,'/');
	$htmlWeeks = '';
	$currentWeek = $Week;
	// Create connection
	$count = 0;
	$WeekRows = 0;


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
                where Torneo_ID = $Season and Equipo_FULLDESC = '$Team'
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
                where Torneo_ID = $Season and Equipo_FULLDESC <> '$Team' and Equipo_ID in (" . $_SESSION[$Config->getAlias() . 'equipo'] . ")
                    and Equipo_ID in (select Local_ID from $schema.Juegos where Torneo_Id = $Season union select Visitante_ID from $schema.Juegos where Torneo_Id = $Season )
                order by 1 asc;";
	//$htmlWeeks .= $sql02;
	$result02 = $Config->query($sql02);
	if($result02){
		$totJor = $result02->num_rows;
		if ($result02->num_rows > 0) {
			// output data of each row
			$htmlWeeks .= '<ul class="dropdown-menu" aria-labelledby="navbarDropdownJornada">';
			//$htmlWeeks .= "         <option value='All'>" . $lang['692'] . "</option>";
			while($row02 = $result02->fetch_assoc()) {
				$htmlWeeks .= '<li><a class="dropdown-item" onclick="loadWeekAdminC(' . $currentWeek . ',\'' . $row02["Equipo_FULLDESC"] . '\', $(\'#byTeam\').prop(\'checked\') ? 1 : 0);">' . $row02["Equipo_FULLDESC"] . '</a></li>';
			}
			$htmlWeeks .= '</ul>';
		}
	}
		$htmlWeeks .= '</div>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '<div class="col-1 col-sm-1 col-md-1 col-lg-1 col-xl-1 col-xxl-1">';
	$htmlWeeks .= '<div >';
	$htmlWeeks .= '<img src="./imagenes/refresh.png" width="20" height="20" onclick="loadWeekAdmin(' . $currentWeek . ', \'' . $selectedTeam . '\', $(\'#byTeam\').prop(\'checked\') ? 1 : 0);" style="margin-left: 10;  margin-top: 2px;">';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '</div>';
	$htmlWeeks .= '</div>';
	
	
	$retunData = array('status' => '1', 'message' => 'Success.', 'dataWeeks' => $htmlWeeks, 'sql' => $sql01, 'sql1' => $sql02);
    $Config->Close();
    echo json_encode($retunData);
?>