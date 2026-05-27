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
	$sessionstat = $fgmembersite->CheckLogin('playersManagementChangeTeams.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

    $retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
    //print_r($_COOKIE);
    $Season = $_COOKIE[$Config->getAlias() . 'season'];
    $Category = SanitizeInteger($_POST["Category"]);
    
	$htmlTeam = "";
    
    $team = '';
	$totTeam = 0;
	$sql = "SELECT Equipo_ID, Equipo_FULLDESC 
			FROM $schema.Equipos
			where Torneo_ID = $Season 
				and Fuerza = $Category 
				and Equipo_ID > 0 and Equipo_ID in (" . $_SESSION[$Config->getAlias() . 'equipo'] . ")
			order by Equipo_FULLDESC asc
			limit 1;";
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row2 = $result->fetch_assoc()) {
			$team = $row2["Equipo_ID"];
		}
	}
	
	$sql = "SELECT Equipo_ID, Equipo_FULLDESC 
			FROM $schema.Equipos
			where Torneo_ID = $Season 
				and Fuerza = $Category 
				and Equipo_ID > 0 and Equipo_ID in (" . $_SESSION[$Config->getAlias() . 'equipo'] . ")
			order by Equipo_FULLDESC asc";
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		$totTeam = $result->num_rows;
	}
	
	$sql = "SELECT Equipo_ID, Equipo_FULLDESC 
			FROM $schema.Equipos
			where Torneo_ID = $Season 
				and Fuerza = $Category 
				and Equipo_ID = $team
			order by Equipo_FULLDESC asc";
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		$selected = false;	
		while($row2 = $result->fetch_assoc()) {
			if($totTeam > 1){
				$htmlTeam .= '						<a class="btn bg-gradient-dark dropdown-toggle" data-bs-toggle="dropdown" id="navbarDropdownMenuLinkCat" style="padding-top: 0px; padding-bottom: 0px; margin-bottom: 0px;" aria-expanded="false">' . $row2["Equipo_FULLDESC"] . '</a>';
			}else{
				$htmlTeam .= '						<a class="btn bg-gradient-dark" data-bs-toggle="dropdown" id="navbarDropdownMenuLinkCat" style="padding-top: 0px; padding-bottom: 0px; margin-bottom: 0px;" aria-expanded="false">' . $row2["Equipo_FULLDESC"] . '</a>';
			}
		}
	} else {
	   $htmlTeam .= "";
	}
	
	$sql = "SELECT Equipo_ID, Equipo_FULLDESC 
			FROM $schema.Equipos
			where Torneo_ID = $Season 
				and Fuerza = $Category 
				and Equipo_ID <> $team
				and Equipo_ID > 0
				and Equipo_ID in (" . $_SESSION[$Config->getAlias() . 'equipo'] . ")
			order by Equipo_FULLDESC asc";
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		$selected = false;
		$htmlTeam .= '								<ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLinkCat">';
		while($row2 = $result->fetch_assoc()) {
			$htmlTeam .= '								<li><a class="dropdown-item" onclick="playersManagementTeamCategoryTeamShowReloadList(' . $Category . ',' . $row2["Equipo_ID"] . ')">' . $row2["Equipo_FULLDESC"] . '</a></li>';
		}
		$htmlTeam .= '								</ul>';
	} else {
		$htmlTeam .= "";
	}
	
	$htmlTeam .= '									<input type="hidden" id="playersManagementTeamSelectedTeam" value="' . $team . '">';
    
    $retunData = array('status' => '1', 'message' => 'Success.', 'dataCatTeam' => $htmlTeam, 'team' => $team);
    $Config->Close();
    echo json_encode($retunData);
?>