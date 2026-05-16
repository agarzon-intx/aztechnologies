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
	$sessionstat = $fgmembersite->CheckLogin('TeamsManagement.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');


    $retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
	
	$Season = $_COOKIE[$Config->getAlias() . 'season'];
	$Category = SanitizeInteger($_POST["Category"]);
    
    $htmlTeams = '';
	$Config->LoadFlags();
    $Config->LoadRegionalSettings();

	$htmlTeams .= '<div id="teamssManagement" class="tabla active" style="display: block;padding-top: 10px;">
		<div id="alllist" class="datagridAdmin" style="display: block;width: 100%;height: auto;">
			<div class="tab-content">
				<div class="tablas" style="width:  100% !important;">
					<div id="teamsManagementList" class="tabla active" style="display: block;padding-top: 10px;">
						<div class="container-fluid py-0 px-0">
							<div class="row">
								<div class="justify-content-left d-flex px-0 py-0 col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
									<div class="container-fluid py-0">
										<div class="px-0 py-0">
											<div class="nav-wrapper position-relative end-0">
												<ul class="nav nav-pills nav-fill p-1" role="tablist" style="background: #cee6ff; flex-direction: unset !important;" id="teamsManagementNavTabs">
													<li class="nav-item" id="teamsTabActiveli">
														<a class="nav-link mb-0 px-0 py-1 active" data-bs-toggle="tab" style="cursor: pointer;" callval="#teamsTabActive" role="tab" aria-controls="teamsTabActiveli" aria-selected="true">
															' . $lang['js907'] . '
														</a>
													</li>
													<li class="nav-item" id="teamsTabInactiveli">
														<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" style="cursor: pointer;" callval="#teamsTabInactive" role="tab" aria-controls="teamsTabInactiveli" aria-selected="false">
															' . $lang['js908'] . '
														</a>
													</li>
												</ul>
											</div>
											<script>initNavs("teamsManagementNavTabs");</script>
										</div>
									</div>
								</div>
								<div class="align-self-right col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 col-xxl-6" style="text-align: right;">
									<h4>' . $lang['514'] . '</h4>
								</div>
							</div>
						</div>
						<div class="tabla-content">';
	$htmlTeams .= '<div id="teamsTabActive" class="tabla active" style="display: block; height: auto;">';
	require 'TeamsManagementActiveList.php';
	$htmlTeams .= '</div>';
	$htmlTeams .= '<div id="teamsTabInactive" class="tabla" style="display: none; height: auto;">';
	require 'TeamsManagementInactiveList.php';
	$htmlTeams .= '</div>';
	$htmlTeams .= '</div>
					</div>';
	$htmlTeams .= '<div id="teamsManagementCreate" class="tabla" style="display: none; height: auto;">';
	$htmlTeams .= '</div>';
	$htmlTeams .= '<div id="teamsManagementEdit" class="tabla" style="display: none; height: auto;">';
	$htmlTeams .= '</div>';
	$htmlTeams .= "</div>
            	</div>
  			</div>
	</div>";
    $retunData = array('status' => '1', 'message' => 'Success.', 'dataTeam' => $htmlTeams);
    $Config->Close();
    echo json_encode($retunData);
?>