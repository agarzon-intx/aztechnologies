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
	
	$sessionstat = $fgmembersite->CheckLogin('userManagementResetPassword.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
    $retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');

    $htmlUsers = '';
	
	$htmlUsers .= '<main class="main-content mt-0">
							<div class="page-header align-items-start min-height-150 m-3 border-radius-xl" style="">
								<span></span>
							</div>
							<div class="container mb-4">
								<div class="row mt-lg-n12 mt-md-n12 mt-n12 justify-content-center">
									<div class="col-xl-4 col-lg-5 col-md-7 mx-auto">
										<div class="card mt-8">
											<div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
												<div class="bg-gradient-success shadow-success border-radius-lg py-3 pe-1 text-center py-4">
													<h4 class="font-weight-bolder text-white mt-1">' . $lang['838'] . '</h4>
													<p class="mb-1 text-sm text-white"></p>
												</div>
											</div>
										<div class="card-body">
											<div id="getSalt">
												<fieldset>
													<form role="form" class="text-start" onsubmit="return false;">
														<div class="input-group input-group-static mb-4">
															<input type="hidden" name="submitted" id="submitted" value="1"/>
															<div class="short_explanation"></div>
															<div><span class="error" id="errorLoginL"></span></div>
														</div>
														<div class="input-group input-group-static mb-4">
															<label>' . $lang['811'] . '</label>
															<input type="text" name="email" id="email" value="" maxlength="50" class="form-control" placeholder="john@email.com" onkeydown="handleResetPasswordRequestEmailKey(event);"/><br/>
															<span id="resetreq_email_errorloc" class="error"></span>
														</div>
														<div class="input-group input-group-static mb-4">
															<div class="short_explanation">' . $lang['840'] . '</div>
														</div>
														<div class="text-center">
															<button id="getSaltSubmit" type="button" class="btn bg-gradient-dark w-100 mt-3 mb-0" onclick="userManagementShowResetPasswordEnterConfirmCode($(\'#email\').val());">' . $lang['211'] . '</button>
														</div>
													</form>
												</fieldset>
											</div>
										</div>
									</div>
								</div>
							</div>
						</main>
						<script type="text/javascript">
							function handleResetPasswordRequestEmailKey(event) {
								if (event.key === \'Enter\' || event.keyCode === 13) {
									event.preventDefault();
									event.stopPropagation();
									userManagementShowResetPasswordEnterConfirmCode($(\'#email\').val());
								}
							}
						</script>';

	$retunData = array('status' => '1', 'message' => 'Success.', 'dataUser' => $htmlUsers);
    echo json_encode($retunData);
?>