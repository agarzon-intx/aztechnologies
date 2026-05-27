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

	require_once("membersite_config.php");
$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('changeTournamentReloadList.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
	
	$Season = SanitizeInteger($_POST["Season"]);
	
	$htmlSeason = '';
	
	$sql = "SELECT Torneo_ID, Torneo_Desc, Actual 
			FROM $schema.Torneos 
			WHERE Torneo_ID = $Season
			order by Actual desc";
	$result = $Config->query($sql);
	$totTorneos = $result->num_rows;
	if ($result->num_rows > 0) {
		// output data of each row
		$selected = false;
		while($row2 = $result->fetch_assoc()) {
			if(strlen($row2["Torneo_Desc"]) > 33){
				$htmlSeason .= '<a class="btn bg-gradient-dark dropdown-toggle " data-bs-toggle="dropdown" id="navbarDropdownMenuLinkTor" style="padding-top: 0px; padding-bottom: 0px; margin-bottom: 0px;">' . substr($row2["Torneo_Desc"],0,30) . '...</a>';
			}else{
				$htmlSeason .= '<a class="btn bg-gradient-dark dropdown-toggle " data-bs-toggle="dropdown" id="navbarDropdownMenuLinkTor" style="padding-top: 0px; padding-bottom: 0px; margin-bottom: 0px;">' . $row2["Torneo_Desc"] . '...</a>';
			}
		}
	} else {
		echo "";
	}
	$sql = "SELECT Torneo_ID, Torneo_Desc, Actual 
			FROM $schema.Torneos 
			WHERE Torneo_ID <> $Season
			order by Torneo_ID desc";
	$result = $Config->query($sql);
	$totTorneos = $result->num_rows;
	if ($result->num_rows > 0) {
		// output data of each row
		$htmlSeason .= '<ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLinkTor">';
		$selected = false;
		while($row2 = $result->fetch_assoc()) {
			if(strlen($row2["Torneo_Desc"]) > 33){
				$htmlSeason .= '<li><a class="dropdown-item" onclick="loadTournament(' . $row2["Torneo_ID"] . ');">' . substr($row2["Torneo_Desc"],0,30) . '...</a></li>';
			}else{
				$htmlSeason .= '<li><a class="dropdown-item" onclick="loadTournament(' . $row2["Torneo_ID"] . ');">' . $row2["Torneo_Desc"] . '</a></li>';
			}
			
		}
		$htmlSeason .= '</ul>';
	} else {
		echo "";
	}
	
	$htmlSeason .= '<input type="hidden" id="selectedTournament" value="' . $Season . '">';
	
	$retunData = array('status' => '1', 'message' => 'Success.', 'dataSeason' => $htmlSeason);
	$Config->Close();
	echo json_encode($retunData);
?>