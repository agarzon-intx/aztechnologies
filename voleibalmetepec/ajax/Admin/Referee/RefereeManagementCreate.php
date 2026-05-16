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
	$sessionstat = $fgmembersite->CheckLogin('RefereeManagementCreate.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
    
  	$referee = SanitizeInteger($_POST['referee']);

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');

	$htmlPlayer = "";
	$fecha = new DateTime();	



	$htmlReferee = '';

	




    $htmlReferee .='<div class="container-fluid py-2">
					<input type="hidden" name="fotostr" id="fotostr" value=""/>
					<input type="hidden" name="idstr" id="idstr" value=""/>
					<input type="hidden" name="firmastr" id="firmastr" value=""/>
					<div class="row">
						<div class="col-xl-12" style="text-align: center;">
							<h3>' . $lang['10700-1'] . '</h3>
						</div>
					</div>
					<div class="row">
						<div class="col-xl-12">
							<Div id="error2"></Div>
						</div>
					</div>
					<div class="row">
						<div class="col-xl-4" style="text-align: center;">
							<h4>' . $lang['946'] . '</h4>
							<div class="row" style="width: 100%;">
								<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
									<div class="row">
										<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
											<span style="text-align: center; ">
												<div style="width: 161px; height: 225px; background-color: #BFBFBF; margin: auto;">
													<img id="foto" src="" alt="Foto" width="161" height="220" style="display: none"/>
												</div>
											</span>
										</div>
									</div>
									<div class="row">
										<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12" style="width: 100%;">
											<span style="text-align: left; ">
												<button style="margin: 0; width: 120px;" class="btn btn-secondary" type="button" onclick="fireEvent($(\'#myFoto\'), \'click\');" id="subirfoto">' . $lang['930'] . '</button>' . $lang['931'] . '
												<form>
													<div style="text-align: center;">
														<div>
															<input style="display: none; visibility: hidden;" type="file" accept="image/png" name="myFoto" id="myFoto" onchange="readURL(this, \'foto\');">
															<input type="hidden" name="myFotoFileName" id="myFotoFileName" value="">
														</div>
														<div id=\'previewMyFoto\' style="text-align: center;"></div>
													</div>
												</form>
											</span>
										</div>
									</div>
									
									
									</div>
								</div>
							</div>
							<div class="col-xl-4" style="text-align: center;">
								<h4>' . $lang['10747'] . '</h4>
								<div class="row">
									<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xx-l2">
										<div class="row">
											<div class="">
												<form>
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['10707'] . '</label>                                                                
																<input type="text" class="form-control" name="nombre" id="nombre" value=""/>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['10719'] . '</label>
																<input type="text" class="form-control" name="apellidop" id="apellidop" value=""/>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['10720'] . '</label>
																<input type="text" class="form-control" name="apellidom" id="apellidom" value=""/>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['10714'] . '</label>
																<select class="form-control" name="sexo" id="sexo">
																	<option value="0">' . $lang['10744'] . '</option>
																	<option value="1" selected>' . $lang['10745'] . '</option>
																</select>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['10707-1'] . '</label>
																<input type="text" class="form-control" name="apodo" id="apodo" value="">
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['10721'] . '</label>
																<input type="date" class="form-control" name="fechanac" id="fechanac"  value="">
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['10722'] . '</label>
																<input type="text" class="form-control" name="curp" id="curp" value="">
															</div>
														</div>
													</div>
													
									
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['10725'] . '</label>
																<input type="number" class="form-control" name="telefono" id="telefono" value="">
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['10732'] . '</label>
																<input type="email" class="form-control" name="correo" id="correo" value="">
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['10726'] . '</label>
																<select class="form-control" name="Estatus2" id="Estatus2">
																	<option value="A" selected>' . $lang['10727'] . '</option>
																	<option value="B">' . $lang['10728'] . '</option>
																	<option value="S">' . $lang['10729'] . '</option>
																</select>
															</div>
														</div>
													</div>
													
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['10739'] . '</label>
																<textarea class="form-control" cols="20" rows="3" spellcheck="false" name="comentarios" id="comentarios"></textarea> 
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['10702'] . '</label>
																<textarea class="form-control" cols="20" rows="3" spellcheck="false" name="historial" id="historial"></textarea> 
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['10703'] . '</label>
																<textarea class="form-control" cols="20" rows="3" spellcheck="false" name="cursos" id="cursos"></textarea> 
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['10712'] . '</label>
																<select class="form-control" name="Validado" id="Validado">
																	<option value="1">' . $lang['10740'] . '</option>
																	<option value="0" selected>' . $lang['10741'] . '</option>
																</select>
															</div>
														</div>
													</div>
												</form>
											</div
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xl-4" style="text-align: center;">
							<h4>' . $lang['948'] . '</h4>
							<div class="row" style="width: 100%;" >
								<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
									<div id="myIDBackground">
										<div class="row">
											<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
												<span style="text-align: center; ">
													<div style="width: 150px; height: 187px; background-color: #BFBFBF; margin: auto;">
														<img id="identificacion" src="" width="150" height="187" style="display: none"/>
													</div>
												</span>
											</div>
										</div>
										<div class="row">
											<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12" style="width: 100%;">
												<span style="text-align: left; ">
													<button style="margin: 0; width: 180px;" class="btn btn-secondary" type="button" onclick="$(\'#myIDBackground\').toggle();$(\'#myIDBackground2\').toggle();" id="subiridentificacion">' . $lang['933'] . '</button>
												</span>
											</div>
										</div>
									</div>
									<div id="myIDBackground2" style="display: none;">
										<div class="row">
											<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
												<span style="text-align: center; ">
													<div style="width: 150px; height: 98px; background-color: #BFBFBF; margin: auto;">
														<img id="identificacion11" src=""  width="150px" height="98px" style="display: none"/>
													</div>
												</span>
											</div>
										</div>
										<div class="row">
											<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12" style="width: 100%;">
												<span style="text-align: left; ">
													<button style="margin: 0; width: 135px;" class="btn btn-secondary" type="button" onclick="fireEvent($(\'#myID11\'), \'click\');" id="subiridentificacion11">' . $lang['933-1'] . '</button>' . $lang['931'] . '
													<form>
														<div style="text-align: center;">
															<div>
																<input style="display: none; visibility: hidden;" type="file" accept="image/png" name="myID11" id="myID11" onchange="readIDURL11(this, \'identificacion11\');">
																<input type="hidden" name="myID11FileName" id="myID11FileName" value="">
															</div>
															<div id=\'previewMyID11\' style="display: inline-block; vertical-align:middle;"></div>
														</div>
													</form>
												</span>
											</div>
										</div>
										<div class="row">
											<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
												<span style="text-align: center; ">
													<div style="width: 150px; height: 98px; background-color: #BFBFBF; margin: auto;">
														<img id="identificacion12" src="" width="150px" height="98px" style="display: none"/>
													</div>
												</span>
											</div>
										</div>
										<div class="row">
											<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12" style="width: 100%;">
												<span style="text-align: left; ">
													<button style="margin: 0; width: 135px;" class="btn btn-secondary" type="button" onclick="fireEvent($(\'#myID12\'), \'click\');" id="subiridentificacion12">' . $lang['933-2'] . '</button>' . $lang['931'] . '
													<form>
														<div style="text-align: center;">
															<div>
																<input style="display: none; visibility: hidden;" type="file" accept="image/png" name="myID12" id="myID12" onchange="readIDURL12(this, \'identificacion12\');">
																<input type="hidden" name="myID12FileName" id="myID12FileName" value="">
															</div>
															<div id=\'previewMyID12\' style="display: inline-block; vertical-align:middle;"></div>
														</div>
													</form>
												</span>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<h4>' . $lang['949'] . '</h4>
								<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
									<span style="text-align: center; ">
										<div style="width: 150px; height: 194px; background-color: #BFBFBF; margin: auto;">
											<img width="150" height="194" id="firma" src="" alt="" style="display: none"/>
										</div>
									</span>
								</div>
							</div>
							<div class="row">
								<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
									<span style="text-align: left; ">
										<button style="margin: 0; width: 135px;" class="btn btn-secondary" type="button" onclick="fireEvent($(\'#myFirma\'), \'click\');" id="subirfirma">' . $lang['935'] . '</button>' . $lang['936'] . '
										<form>
											<div style="text-align: center;">
												<div>
													<input style="display: none; visibility: hidden;" type="file" accept="image/png" name="myFirma" id="myFirma" onchange="readFirmaURL(this, \'firma\');">
													<input type="hidden" name="myFirmaFileName" id="myFirmaFileName" value="">
												</div>
												<div id=\'previewMyFirma\' style="display: inline-block; vertical-align:middle;"></div>
											</div>
										</form>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-6 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" style="text-align: right;">
						<button type="button" class="btn btn-primary" onClick="validateReferee();" >' . $lang['0000'] . '</button>
					</div>
					<div class="col-6 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
						<button type="button" class="btn btn-primary" onClick="limpiarA();" >' . $lang['0001'] . '</button>
					</div>
				</div>
			</div>
			<script>
				var inputs = document.querySelectorAll(\'input\');
				var selects = document.querySelectorAll(\'select\');
				var textareas = document.querySelectorAll(\'textarea\');
				var as = document.querySelectorAll(\'a\');
				
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
				
				for (var i = 0; i < textareas.length; i++) {
					textareas[i].addEventListener(\'focus\', function(e) {
					  this.parentElement.classList.add(\'is-focused\');
					}, false);

					textareas[i].onkeyup = function(e) {
					  if (this.value != "") {
						this.parentElement.classList.add(\'is-filled\');
					  } else {
						this.parentElement.classList.remove(\'is-filled\');
					  }
					};

					textareas[i].addEventListener(\'focusout\', function(e) {
						if (this.value != "") {
							this.parentElement.classList.add(\'is-filled\');
						}
						this.parentElement.classList.remove(\'is-focused\');
					}, false);
					
					textareas[i].parentElement.classList.add(\'is-filled\');
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
			</script>';

						

	$retunData = array('status' => '1', 'message' => 'Success.', 'refereeAdd' => $htmlReferee);
    echo json_encode($retunData);
?>