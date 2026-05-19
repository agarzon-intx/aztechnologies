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
	$sessionstat = $fgmembersite->CheckLogin('showLogin.php');

	$__langCk = $Config->getAlias() . 'language';
	if (!isset($_COOKIE[$__langCk]) || $_COOKIE[$__langCk] === '') {
		$Config->LoadLanguage();
		$__lang = $Config->lan;
	} else {
		$__lang = $_COOKIE[$__langCk];
	}
	include 'lang.' . $__lang . '.php';

	$key = $Config->getAlias() . 'CSRFtoken';
	$CSRFtoken = (isset($_SESSION[$key]) && is_string($_SESSION[$key])) ? $_SESSION[$key] : '';

    $retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
    
    $htmlLogin = '';

    $htmlLogin .= '	<main class="main-content mt-0">
						<div class="page-header align-items-start min-height-150 m-3 border-radius-xl" style="">
							<span></span>
						</div>
						<div class="container mb-4">
							<div class="row mt-lg-n12 mt-md-n12 mt-n12 justify-content-center">
								<div class="col-xl-4 col-lg-5 col-md-7 mx-auto">
									<div class="card mt-8">
										<div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
											<div class="bg-gradient-success shadow-success border-radius-lg py-3 pe-1 text-center py-4">
												<h4 class="font-weight-bolder text-white mt-1">' . $lang['212'] . '</h4>
												<p class="mb-1 text-sm text-white"></p>
											</div>
										</div>
									<div class="card-body">
										<div id="getSalt">
											<fieldset>
												<form role="form" class="text-start" method="post" action="#" onsubmit="return false;">
													<div class="input-group input-group-static mb-4">
														<input type="hidden" name="submitted" id="submitted" value="1"/>
														<div class="short_explanation">' . $lang['209'] . '</div>
														<div><span class="error" id="errorLogin"></span></div>
													</div>
													<div class="input-group input-group-static mb-4">
														<label>' . $lang['207'] . '</label>
														<input type="text" name="username" id="usernamePre" value="" maxlength="64" onkeydown="handleUsername(event);" class="form-control" placeholder="bayermunich"/><br/>
														<span id="login_username_errorloc" class="error"></span>
													</div>
													<div class="text-center">
														<button id="getSaltSubmit" type="button" class="btn bg-gradient-dark w-100 mt-3 mb-0" onclick="getTheirSalt();">' . $lang['211'] . '</button>
													</div>
												</form>
											</fieldset>
										</div>
										
										<div id="login" style="display:none;">
											<fieldset>
												<legend>' . $lang['212'] . '</legend>
												<form role="form" class="text-start" method="post" action="#" onsubmit="return false;">
												<input type="hidden" name="submitted" id="submitted" value="1"/>
												<div class="short_explanation">' . $lang['209'] . '</div>
												<div><span class="error" id="errorLoginL"></span></div>
												<div class="input-group input-group-static mb-4">
													<label>' . $lang['207'] . '</label><br/>
													<input type="text" name="username" id="username" value="" maxlength="64" oninput="returnToUsername();" onkeydown="handleLoginUsernameKey(event);" class="form-control" placeholder="bayermunich"/><br/>
													<span id="login_username_errorloc" class="error"></span>
												</div>
												<div class="input-group input-group-static mb-4">
													<label>' . $lang['208'] . '</label>
													<input type="password" class="form-control" placeholder="•••••••••••••" name="password" id="password" onkeydown="handlePassword(event)">
													<span id="login_password_errorloc" class="error"></span>
												</div>
												<div class="text-center">
													<button id="getLoginSubmit" type="button" class="btn bg-gradient-dark w-100 mt-3 mb-0" onclick="submitLoginForm()">' . $lang['211'] . '</button>
												</div>
												</form>
											</fieldset>
										</div>
									</div>
									<div class="card-footer text-center pt-0 px-lg-2 px-1">
										<p class="mb-4 text-sm mx-auto"><a style="cursor: pointer;" onclick="userManagementShowResetPassword();" class="text-success text-gradient font-weight-bold">' . $lang['210'] . '</a></p>
									</div>
								</div>
							</div>
						</div>
					</main>


					<script type="text/javascript">

						
						var salt;
						document.getElementById(\'usernamePre\').select();
						function getTheirSalt() {
							if($(\'#usernamePre\').val().length === 0){
								$(\'#errorLogin\').html(\'' . $lang['js0001'] . '\');
							}else{
								var userid = document.getElementById(\'usernamePre\').value.toLowerCase();
								$.ajax({
									type: \'POST\',
									dataType: \'json\',
									url: \'ajax/Login/get_salt.php\',
									data: {username : userid},
									success: function (res) {
										if (res.status === \'1\') {
											document.getElementById(\'getSalt\').style.display = \'none\';
											document.getElementById(\'username\').value = userid;
											document.getElementById(\'password\').value = \'\';
											document.getElementById(\'login\').style.display = \'block\';
											document.getElementById(\'password\').select();
											document.getElementById(\'password\').focus();
											salt = res.salt;
										}
										if (res.status === \'0\') {
											showBrowserRegister();
										}
									},
									error: function(jqxhr, status, exception) {
										if (typeof mainLoadingOff === \'function\') { mainLoadingOff(); }
										alert(' . json_encode($lang['js0002'], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG) . ');
										console.log(\'Exception:\' + exception);
									}
								});
							}
								
						}
						

						function returnToUsername() {
							document.getElementById(\'getSalt\').style.display = \'block\';
							document.getElementById(\'login\').style.display = \'none\';
							document.getElementById(\'usernamePre\').value = document.getElementById(\'username\').value;
							document.getElementById(\'usernamePre\').focus();
						}

						function handleLoginUsernameKey(event) {
							if (event.key === \'Enter\' || event.keyCode === 13) {
								event.preventDefault();
								event.stopPropagation();
								submitLoginForm();
							}
						}

						function submitLoginForm() {
							if(document.getElementById(\'password\').value.length === 0 || $(\'#username\').val().length  === 0){
								$(\'#errorLoginL\').html(\'' . $lang['js0001'] . '\');
							}else{
								login($(\'#username\').val(), CryptoJS.PBKDF2(document.getElementById(\'password\').value, salt, { keySize: 160/32, iterations: 1000 }).toString(),\'' . $CSRFtoken . '\');
							}
						}

						function handleUsername(event){
							if (event.key === \'Enter\' || event.keyCode === 13) {
								event.preventDefault();
								event.stopPropagation();
								getTheirSalt();
							}
						}

						function handlePassword(event){
							if (event.key === \'Enter\' || event.keyCode === 13) {
								event.preventDefault();
								event.stopPropagation();
								submitLoginForm();
							}
						}

					</script>
					</div>';
    
    $retunData = array('status' => '1', 'message' => 'Success.', 'dataLogin' => $htmlLogin);
    echo json_encode($retunData);
?>