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
	$sessionstat = $fgmembersite->CheckLogin('playersManagementChangeTeamsReloadList.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
	
	$Season = $_COOKIE[$Config->getAlias() . "season"];
    $Category = SanitizeInteger($_POST["Category"]);
	$team = SanitizeInteger($_POST["Team"]);
	
	$htmlCat = '';
	
	$sql0 = "SELECT Equipo_ID, Equipo_FULLDESC 
			FROM $schema.Equipos
			where Torneo_ID = $Season 
				and Fuerza = $Category 
				and Equipo_ID > 0
			order by Equipo_FULLDESC asc";
	$result = $Config->query($sql0);
	if ($result->num_rows > 0) {
		$totTeam = $result->num_rows;
	}
	
	$sql1 = "SELECT Equipo_ID, Equipo_FULLDESC
			FROM $schema.Equipos
			where Torneo_ID = $Season 
				and Fuerza = $Category 
				and Equipo_ID = $team
			order by Equipo_FULLDESC asc";
	$result = $Config->query($sql1);
	if ($result->num_rows > 0) {
		// output data of each row
		$selected = false;	
		while($row2 = $result->fetch_assoc()) {
			if($totTeam > 1){
				$htmlCat .= '						<a class="btn bg-gradient-dark dropdown-toggle" data-bs-toggle="dropdown" id="navbarDropdownMenuLinkCat" style="padding-top: 0px; padding-bottom: 0px; margin-bottom: 0px;" aria-expanded="false">' . $row2["Equipo_FULLDESC"] . '</a>';
			}else{
				$htmlCat .= '						<a class="btn bg-gradient-dark" data-bs-toggle="dropdown" id="navbarDropdownMenuLinkCat" style="padding-top: 0px; padding-bottom: 0px; margin-bottom: 0px;" aria-expanded="false">' . $row2["Equipo_FULLDESC"] . '</a>';
			}
		}
	} else {
	   $htmlCat .= "";
	}
	
	$sql2 = "SELECT Equipo_ID, Equipo_FULLDESC, Activo
			FROM $schema.Equipos
			where Torneo_ID = $Season 
				and Fuerza = $Category 
				and Equipo_ID <> $team
				and Equipo_ID > 0
			order by Activo desc, Equipo_FULLDESC asc";
	$result = $Config->query($sql2);
	if ($result->num_rows > 0) {
		// output data of each row
		$selected = false;
		$htmlCat .= '								<ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLinkCat">';
		while($row2 = $result->fetch_assoc()) {
		    if($row2["Activo"] == '1'){
			    $htmlCat .= '								<li><a class="dropdown-item" onclick="playersManagementAdminCategoryTeamShowReloadList(' . $Category . ',' . $row2["Equipo_ID"] . ')">' . $row2["Equipo_FULLDESC"] . '</a></li>';
		    }else{
		        $htmlCat .= '								<li><a class="dropdown-item" onclick="playersManagementAdminCategoryTeamShowReloadList(' . $Category . ',' . $row2["Equipo_ID"] . ')"><strike>' . $row2["Equipo_FULLDESC"] . '</strike></a></li>';
		    }
		}
		$htmlCat .= '								</ul>';
	} else {
		$htmlCat .= "";
	}
	
	$htmlCat .= '									<input type="hidden" id="playersManagementAdminSelectedTeam" value="' . $team . '">';
	
	$retunData = array('status' => '1', 'message' => 'Success.', 'dataCategories' => $htmlCat, 'team' => $team, 'sql0' => $sql0, 'sql1' => $sql1, 'sql2' => $sql2);
	$Config->Close();
	echo json_encode($retunData);
?>