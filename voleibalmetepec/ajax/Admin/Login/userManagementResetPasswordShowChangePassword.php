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
	
	$sessionstat = $fgmembersite->CheckLogin('userManagementResetPasswordShowChangePassword.php');
	
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
												<h4 class="font-weight-bolder text-white mt-1">' . $lang['838-2'] . '</h4>
												<p class="mb-1 text-sm text-white"></p>
											</div>
										</div>
									<div class="card-body">
										<div id="getSalt">
											<fieldset>
												<form role="form" class="text-start" onsubmit="return false;">
													<div class="input-group input-group-static mb-4">
														<input type="hidden" name="submitted" id="submitted" value="1"/>
														<div class="short_explanation">' . $lang['222'] . '</div>
														<div><span class="error" id="errorLoginL"></span></div>
													</div>
													<div class="input-group input-group-static mb-4">
														<label>' . $lang['844'] . '</label>
														<div class="pwdwidgetdiv" id="thepwddiv" style="width: 100%;"></div>
														<noscript>
															<input class="form-control" type="password" name="password" id="password" placeholder="•••••••••••••" onkeydown="handlePassword(event);"/>
														</noscript> 

														<div id="register_code_error_reset_pwd" class="error" style="clear:both"></div>
													</div>
													<div style="display:none;">
														<input type="password" name="salt" id="salt" value="' . bin2hex(random_bytes(16)) . '"/>
													</div>
													<div style="display:none;">
																<input type="password" name="code" id="code" value="' . $_POST['code'] . '"/>
															</div>
													<div class="input-group input-group-static mb-4">
														<div class="short_explanation"></div>
													</div>
													<div class="text-center">
														<button type="button" class="btn bg-gradient-dark w-100 mt-3 mb-0" onclick="submitForm();">' . $lang['211'] . '</button>
													</div>
												</form>
											</fieldset>
										</div>
									</div>
								</div>
							</div>
						</div>
					</main>
					<script>

						function submitForm() {
							userManagementResetPasswordChangePassword($(\'#code\').val(),CryptoJS.PBKDF2($(\'#password_id\').val(), $(\'#salt\').val(), { keySize: 160/32, iterations: 1000 }).toString(),$(\'#salt\').val());
						}

						function handlePassword(event){
							if (event.key === \'Enter\' || event.keyCode === 13) {
								event.preventDefault();
								event.stopPropagation();
								submitForm();
							}
						}
						
						var pwdwidget = new PasswordWidget(\'thepwddiv\',\'password\', \'' . $lang['824'] . '\', \'' . $lang['825'] . '\', \'' . $lang['826'] . '\', \'' . $lang['827'] . '\', \'' . $lang['828'] . '\', \'' . $lang['829'] . '\');
						pwdwidget.MakePWDWidget();
						(function attachResetPwdEnter() {
							var p = document.getElementById(\'password_id\');
							var t = document.getElementById(\'password_text_id\');
							function onEnter(e) {
								if (e.key === \'Enter\' || e.keyCode === 13) {
									e.preventDefault();
									e.stopPropagation();
									submitForm();
								}
							}
							if (p) { p.addEventListener(\'keydown\', onEnter); }
							if (t) { t.addEventListener(\'keydown\', onEnter); }
						})();
            
				  	</script>';

	$retunData = array('status' => '1', 'message' => 'Success.', 'dataUser' => $htmlUsers);

	
	if(!$fgmembersite->validateConfirmation(SanitizeHex(strtolower($_POST['code'])))){
		$retunData = array('status' => '0', 'message' => 'Success.', 'dataUser' => $fgmembersite->GetErrorMessage());
	}
	

    echo json_encode($retunData);
?>