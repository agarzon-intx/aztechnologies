<?php
	session_start();

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

	//error_reporting(0);

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
	$sessionstat = $fgmembersite->CheckLogin('MemosManagementEdit.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
	$htmlMemos = '';

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');

	$memoid = ''; 
	$Fecha = ''; 
	$Contenido = ''; 
	$Titulo = ''; 
	
	$htmlMemos .= '<div class="container-fluid py-2">
					<div class="row">
						<div class="col-xl-12">
							<h3>' . $lang['722'] . '</h3>
						</div>
					</div>
					<div class="row">
						<div class="col-xl-12">
							<Div id="errorColor" style="color: red; text-align: justify;"></Div>
						</div>
					</div>
					<div class="row">
						<div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
							<form>
								<div class="row">
									<div class="col-xl-12" hidden>
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['712'] . '</label>
											<input type="text" class="form-control" name="memoid" id="memoid" value="' . $memoid . '"/>
										</div>
									</div>
									<div class="col-xl-6">
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['714'] . '</label>
											<input type="text" class="form-control" maxlength="50" size="50" name="Titulo" id="Titulo" value="' . $Titulo . '"/>
										</div>
									</div>
									<div class="col-xl-6">
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['713'] . '</label>
											<input type="date" class="form-control" name="Fecha" id="Fecha" value="' . $Fecha . '"/>
										</div>
									</div>
									<div class="col-xl-12">
										<textarea cols="80" rows="20" id="editor" name="editor">' . $Contenido . '</textarea>
										<script>
											CKEDITOR.replace(\'editor\', {
												width: \'auto\',
												height: \'293px\',
												language: \'' . $_COOKIE[$Config->getAlias() . "language"] . '\',
												extraPlugins: \'autogrow\',
												autoGrow_minHeight: 293,
												autoGrow_maxHeight: 293,
												autoGrow_bottomSpace: 50,
												removePlugins: \'resize\'
											});
										</script>
									</div>
									<div class="col-xl-12">
										<form>
											<div class="row">
												<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
													<span style="text-align: center; ">
														<div style="width: 300px; height: 400px; background-color: #BFBFBF; margin: auto;">
															<img id="Minuta" src="imagenes/fondoTransparente.png" alt="Logo" width="300" height="400" onerror="if (this.src != \'imagenes/fondoTransparente.png\'){ this.src = \'imagenes/fondoTransparente.png\'; this.height = \'400\'; this.width = \'300\'; }"/>
														</div>
													</span>
												</div>
											</div>
											<div class="row">
												<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
													<span style="text-align: center;">
														<div style="margin: auto;">
															<button style="margin: 0; width: 235px;" class="btn btn-secondary" type="button" onclick="fireEvent($(\'#MyMinuta\'), \'click\');" id="subirmemo">' . $lang['719'] . '</button>
															<form>
																<div style="text-align: center;">
																	<div>
																		<input style="display: none; visibility: hidden;" type="file" accept="image/png" name="MyMinuta" id="MyMinuta" onchange="readMinutaURL(this, \'Minuta\');">
																		<input type="hidden" name="myMinutaFileName" id="myMinutaFileName" value="">
																	</div>
																	<div id=\'previewMyMinuta\' style="display: inline-block; vertical-align:middle;"></div>
																</div>
															</form>
														</div>
													</span>
												</div>
											</div>
										</form>
									</div>
								</div>
							</form>
						</div>
					</div>
					<div class="row">
						<div class="my-3" >
							<button type="button" class="btn btn-primary" onClick="validateMemoCreate();" >' . $lang['0000'] . '</button>
							<button type="button" class="btn btn-primary" onClick="memoManagementHideAdd();" >' . $lang['0001'] . '</button>
						</div>
					</div>
				</div>
				<script>
					var inputs = document.querySelectorAll(\'input\');
					var as = document.querySelectorAll(\'a\');
					var selects = document.querySelectorAll(\'select\');
					var divs = document.querySelectorAll(\'div.form-control\');
					
					for (var i = 0; i < inputs.length; i++) {
						inputs[i].addEventListener(\'focus\', function(e) {
						  this.parentElement.classList.add(\'is-focused\');
						}, false);

						inputs[i].onkeyup = function(e) {
						  if (this.value != "") {
							this.parentElement.classList.add(\'is-filled\');
						  } else {
							this.parentElement.classList.remove(\'is-filled\');
						  }
						};

						inputs[i].addEventListener(\'focusout\', function(e) {
							if (this.value != "") {
								this.parentElement.classList.add(\'is-filled\');
							}
							this.parentElement.classList.remove(\'is-focused\');
						}, false);
						
						if(inputs[i].hasAttribute(\'value\')){
							inputs[i].parentElement.classList.add(\'is-filled\');
						}
					  }
					
					for (var i = 0; i < as.length; i++) {
						as[i].addEventListener(\'focus\', function(e) {
						  this.parentElement.classList.add(\'is-focused\');
						}, false);

						as[i].onkeyup = function(e) {
						  if (this.value != "") {
							this.parentElement.classList.add(\'is-filled\');
						  } else {
							this.parentElement.classList.remove(\'is-filled\');
						  }
						};

						as[i].addEventListener(\'focusout\', function(e) {
							if (this.value != "") {
								this.parentElement.classList.add(\'is-filled\');
							}
							this.parentElement.classList.remove(\'is-focused\');
						}, false);
						
						as[i].parentElement.classList.add(\'is-filled\');
					  }
				
					for (var i = 0; i < selects.length; i++) {
						selects[i].addEventListener(\'focus\', function(e) {
						  this.parentElement.classList.add(\'is-focused\');
						}, false);

						selects[i].onkeyup = function(e) {
						  if (this.value != "") {
							this.parentElement.classList.add(\'is-filled\');
						  } else {
							this.parentElement.classList.remove(\'is-filled\');
						  }
						};

						selects[i].addEventListener(\'focusout\', function(e) {
							if (this.value != "") {
								this.parentElement.classList.add(\'is-filled\');
							}
							this.parentElement.classList.remove(\'is-focused\');
						}, false);
						
						selects[i].parentElement.classList.add(\'is-filled\');
					  }
					
					for (var i = 0; i < divs.length; i++) {
						divs[i].addEventListener(\'focus\', function(e) {
						  this.parentElement.classList.add(\'is-focused\');
						}, false);

						divs[i].onkeyup = function(e) {
						  if (this.value != "") {
							this.parentElement.classList.add(\'is-filled\');
						  } else {
							this.parentElement.classList.remove(\'is-filled\');
						  }
						};

						divs[i].addEventListener(\'focusout\', function(e) {
							if (this.value != "") {
								this.parentElement.classList.add(\'is-filled\');
							}
							this.parentElement.classList.remove(\'is-focused\');
						}, false);
						
						divs[i].parentElement.classList.add(\'is-filled\');
					  }
				</script>';
					
	$retunData = array('status' => '1', 'message' => 'Success.', 'memoAdd' => $htmlMemos);
    echo json_encode($retunData);
?>