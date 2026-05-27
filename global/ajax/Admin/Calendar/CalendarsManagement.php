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
	$sessionstat = $fgmembersite->CheckLogin('CalendarsManagement.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');


    $retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
    
    $htmlCalendar = '';
	$Config->LoadFlags();
    $Config->LoadRegionalSettings();
	$fecha = new DateTime();

	$htmlCalendar .= '<div id="calendarsManagement" class="tabla active" style="display: block;padding-top: 10px;">
		<div id="alllist" class="datagridAdmin" style="display: block;width: 100%;height: auto;">
			<div class="tab-content">
				<div class="tablas" style="width:  100% !important;">
					<div class="tabla-content">';
							
	/*-----------------------------------------------------------------------------------------------------------------------------
	-----------------------------------------------------------------------------------------------------------------------------*/
	$htmlCalendar .= '<div id="calendarsManagementList" class="tabla active" style="display: block; height: auto;">';
	require 'CalendarsManagementActiveList.php';
	$htmlCalendar .= '</div>';
	/*-----------------------------------------------------------------------------------------------------------------------------
	-----------------------------------------------------------------------------------------------------------------------------*/

	/*-----------------------------------------------------------------------------------------------------------------------------
	-----------------------------------------------------------------------------------------------------------------------------*/
	$htmlCalendar .= '<div id="calendarsManagementCreate" class="tabla" style="display: none; height: auto;">';
	$htmlCalendar .= '</div>';
	/*-----------------------------------------------------------------------------------------------------------------------------
	-----------------------------------------------------------------------------------------------------------------------------*/
    
	/*-----------------------------------------------------------------------------------------------------------------------------
	-----------------------------------------------------------------------------------------------------------------------------*/
	$htmlCalendar .= '<div id="calendarsManagementEdit" class="tabla" style="display: none; height: auto;">';
	$htmlCalendar .= '</div>';
	/*-----------------------------------------------------------------------------------------------------------------------------
	-----------------------------------------------------------------------------------------------------------------------------*/
	$htmlCalendar .= "</div> 
            	</div>	
  			</div>  
		</div>
	</div>";
    $retunData = array('status' => '1', 'message' => 'Success.', 'dataCalendar' => $htmlCalendar);
    $Config->Close();
    echo json_encode($retunData);
?>