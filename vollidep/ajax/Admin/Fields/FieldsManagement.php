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
	$sessionstat = $fgmembersite->CheckLogin('FieldsManagement.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');


    $retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
    
    $htmlFields = '';
	$Config->LoadFlags();
    $Config->LoadRegionalSettings();
	$fecha = new DateTime();

	$htmlFields .= '<div id="fieldsManagement" class="tabla active" style="display: block;padding-top: 10px;">
		<div id="alllist" class="datagridAdmin" style="display: block;width: 100%;height: auto;">
			<div class="tab-content">
				<div class="tablas" style="width:  100% !important;">
					<div class="nav-wrapper position-relative end-0">
						<!--<ul class="nav nav-pills nav-fill p-1" role="tablist" style="background: #cee6ff; flex-direction: unset !important;" id="config1">
							<li class="nav-item" id="infoli">
								<a class="nav-link mb-0 px-0 py-1 active" data-bs-toggle="tab" style="cursor: pointer;" callval="#info" role="tab" aria-controls="infoli" aria-selected="true">
									<img src="./imagenes/LogoLigaGeneric.png?tmp=' . $fecha->getTimestamp() . '" style="width: 20px; height: auto;" alt=""/>  ' . $lang['474'] . '
								</a>
							</li>
							<li class="nav-item" id="messageli">
								<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" style="cursor: pointer;" callval="#message" role="tab" aria-controls="messageli" aria-selected="false">
									<img src="./imagenes/stats.png?tmp=' . $fecha->getTimestamp() . '" style="width: 20px; height: auto;" alt=""/>  ' . $lang['451'] . '
								</a>
							</li>
							<li class="nav-item" id="generalli">
								<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" style="cursor: pointer;" callval="#general" role="tab" aria-controls="generalli" aria-selected="false">
									<img src="./imagenes/stats.png?tmp=' . $fecha->getTimestamp() . '" style="width: 20px; height: auto;" alt=""/>  ' . $lang['452'] . '
								</a>
							</li>
						</ul>-->
					</div>
					<!--<script>initNavs();</script>-->
					<div class="tabla-content">';
							
	/*-----------------------------------------------------------------------------------------------------------------------------
	-----------------------------------------------------------------------------------------------------------------------------*/
	$htmlFields .= '<div id="fieldsManagementList" class="tabla active" style="display: block; height: auto;">';
	require 'FieldsManagementActiveList.php';
	$htmlFields .= '</div>';
	/*-----------------------------------------------------------------------------------------------------------------------------
	-----------------------------------------------------------------------------------------------------------------------------*/

	/*-----------------------------------------------------------------------------------------------------------------------------
	-----------------------------------------------------------------------------------------------------------------------------*/
	$htmlFields .= '<div id="fieldsManagementCreate" class="tabla" style="display: none; height: auto;">';
	$htmlFields .= '</div>';
	/*-----------------------------------------------------------------------------------------------------------------------------
	-----------------------------------------------------------------------------------------------------------------------------*/
    
	/*-----------------------------------------------------------------------------------------------------------------------------
	-----------------------------------------------------------------------------------------------------------------------------*/
	$htmlFields .= '<div id="fieldsManagementEdit" class="tabla" style="display: none; height: auto;">';
	$htmlFields .= '</div>';
	/*-----------------------------------------------------------------------------------------------------------------------------
	-----------------------------------------------------------------------------------------------------------------------------*/
	$htmlFields .= "</div> 
            	</div>	
  			</div>  
		</div>
	</div>";
    $retunData = array('status' => '1', 'message' => 'Success.', 'dataField' => $htmlFields, 'sql' => $sql2);
    $Config->Close();
    echo json_encode($retunData);
?>