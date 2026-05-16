<?php
    session_start();
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

	error_reporting(0);
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
	$sessionstat = $fgmembersite->CheckLogin('changeWeekAdmin.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');


    $retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
    
    $Season = $_COOKIE[$Config->getAlias() . 'season'];
    $Category = $_COOKIE[$Config->getAlias() . 'category'];
    $Week = SanitizeInteger($_POST['Week']);
    $Team = SanitizeText($_POST['Team']);
    $Type = SanitizeText($_POST['Type']);
    
    $teamdesc = $Team;
    if($teamdesc == ''){
        $teamdesc = $_COOKIE[$Config->getAlias() . "selteam"];
    }
    //echo $teamdesc;
    
	$htmlWeek = '<div class="tab-content" style="padding: 0px; width: 100%">';
	$Config->LoadFlags();
    $Config->LoadRegionalSettings();
    // Create connection
    $sql = "SELECT * FROM $schema.Torneos where Torneo_ID = $Season;";
	//echo $sql;
	$result1 = $Config->query($sql);
    if ($result1->num_rows > 0) {
		while($row = $result1->fetch_assoc()) {
			$vs = $row["TodosVsTodos"];
		}
	}
	$sqlcat = "and e.Fuerza = $Category";
	if($vs == 1){
		$sqlcat = "";
	}
	require 'singleWeekAdmin.php';
	$htmlWeek .= '</div>';
    $retunData = array('status' => '1', 'message' => 'Success.', 'dataWeekAdmin' => $htmlWeek, 'sql' => $sql, 'sql0' => $sql0, 'sql1' => $sql1, 'sql33' => $sql33);
    $Config->Close();
    echo json_encode($retunData);
?>