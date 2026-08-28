<?php
	session_start();

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

	//error_reporting(0);

	session_start();

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

	//error_reporting(0);
if (!defined('APP_SITE_ROOT')) {
	$___d = __DIR__;
	while ($___d !== dirname($___d)) {
		$___p = $___d . DIRECTORY_SEPARATOR . 'site_paths.php';
		if (is_readable($___p)) {
			require_once $___p;
			break;
		}
		$___d = dirname($___d);
	}
}
	require("membersite_config.php");
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('playersManagementEdit.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	$signatureStyle = $Config->playerSignatureEnabled() ? '' : 'style="display: none;"';
	$idPdfEnabled = $Config->playerIDPDFEnabled();
	$idImageStyle = $idPdfEnabled ? 'style="display: none;"' : '';
	$idPdfStyle = $idPdfEnabled ? '' : 'style="display: none;"';
	
	$Season = $_COOKIE[$Config->getAlias() . 'season'];
    $Category = SanitizeInteger($_POST['Category']);
	$Team = SanitizeInteger($_POST['Team']);

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');

	$htmlPlayer = "";
	$fecha = new DateTime();
	
	$htmlPlayer .= '<div class="container-fluid py-2">
					<input type="hidden" name="fotostr" id="fotostr" value=""/>
					<input type="hidden" name="idstr" id="idstr" value=""/>
					<input type="hidden" name="firmastr" id="firmastr" value=""/>
					<div class="row">
						<div class="col-xl-12" style="text-align: center;">
							<h3>' . $lang['918'] . '</h3>
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
									<div class="row">
										<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">';
$sql = "SELECT c.Categoria_ID, b.Equipo_ID, concat(c.categoria_DESC,' - ',b.Equipo_FULLDESC) Equipo_FULLDESC, concat(b.Torneo_ID,'-', b.Equipo_ID) Logo 
		from (	select a.* 
				from $schema.Equipos a
				where Equipo_ID > 0 
					and Torneo_ID = $Season) b
			join $schema.Categorias c on b.Fuerza = c.Categoria_ID and c.Torneo_ID = $Season
		where b.Equipo_ID = $Team
		order by c.categoria_ID asc, b.Equipo_FULLDESC";
		//echo $sql;
$result = $Config->query($sql);
if ($result->num_rows > 0) {
	// output data of each row
	while($row2 = $result->fetch_assoc()) {
		if($Team == $row2["Equipo_ID"]){
			$TeamLogo = $row2["Logo"];
		}
	}
}
$htmlPlayer .= '							<img width="120" height="120" id="logoE" src="imagenes/' . $TeamLogo . '.png?tmp=' . $fecha->getTimestamp() . '" alt="Firma"/>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xl-4" style="text-align: center;">
								<h4>' . $lang['947'] . '</h4>
								<div class="row">
									<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xx-l2">
										<div class="row">
											<div class="">
												<form>
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['907'] . '</label>
																<input type="text" class="form-control" name="nombre" id="nombre" value=""/>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['919'] . '</label>
																<input type="text" class="form-control" name="apellidop" id="apellidop" value=""/>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['920'] . '</label>
																<input type="text" class="form-control" name="apellidom" id="apellidom" value=""/>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['914'] . '</label>
																<select class="form-control" name="sexo" id="sexo">
																	<option value="0">' . $lang['944'] . '</option>
																	<option value="1" selected>' . $lang['945'] . '</option>
																</select>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['906'] . '</label>
																<input type="text" class="form-control" name="apodo" id="apodo" value="">
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['921'] . '</label>
																<input type="date" class="form-control" name="fechanac" id="fechanac"  value="">
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-11 col-xs-11 col-sm-11 col-md-11 col-lg-11 col-xl-11 col-xxl-11">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['922'] . '</label>
																<input type="text" class="form-control" name="curp" id="curp" value="">
															</div>
														</div>
														<div class="col-1 col-xs-1 col-sm-1 col-md-1 col-lg-1 col-xl-1 col-xxl-1" style="padding-left: 0;">
														    <button class="btn btn-outline-secundary" type="button" id="btn-search" onclick="searchCURP(' . $Category . ', ' . $Team . ')" style="margin-bottom: 0rem !important; padding: 0.625rem 0rem;"><i class="fas fa-search"></i></button>
															<script src="ajax/Admin/Players/Admin/playerValidation.js.php"></script>
														</div>
													</div>
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['923'] . '</label>
																<input type="number" class="form-control" name="numero" id="numero" value="">
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['924-1'] . '</label>
																<select class="form-control" name="type" id="type" >
                                                                    <option value="0" selected="selected">' . $lang['924-10'] . '</option>
                                                                    <option value="1">' . $lang['924-11'] . '</option>
                                                                    <option value="2">' . $lang['924-12'] . '</option>
                                                                </select>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['924'] . '</label>
																<select class="form-control" onChange="playerManagementLoadImage(\'equipo\', \'logoE\')" name="equipo" id="equipo" >';
$Config->query($sql);
$sql = "SELECT c.Categoria_ID, b.Equipo_ID, concat(c.categoria_DESC,' - ',b.Equipo_FULLDESC) Equipo_FULLDESC, concat(b.Torneo_ID,'-', b.Equipo_ID) Logo
	from (	select a.* 
			from $schema.Equipos a
			where Equipo_ID > 0 
				and Torneo_ID = $Season) b
		join $schema.Categorias c on b.Fuerza = c.Categoria_ID and c.Torneo_ID = $Season
	order by c.categoria_ID asc, b.Equipo_FULLDESC";	
$result = $Config->query($sql);
if ($result->num_rows > 0) {
// output data of each row
while($row2 = $result->fetch_assoc()) {
	if($Team == $row2["Equipo_ID"]){
		$htmlPlayer .= '						<option value="' . $row2["Equipo_ID"] . ',' . $row2["Logo"] . '" selected="selected">' . $row2["Equipo_FULLDESC"]. '</option>';
	}else{
	   $htmlPlayer .= '							<option value="' . $row2["Equipo_ID"] . ',' . $row2["Logo"] . '">' . $row2["Equipo_FULLDESC"] . '</option>';
	}
}
}
$htmlPlayer .= '													</select>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['925'] . '</label>
																<input type="number" class="form-control" name="telefono" id="telefono" value="">
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['926'] . '</label>
																<select class="form-control" name="Estatus2" id="Estatus2">
																	<option value="A" selected>' . $lang['927'] . '</option>
																	<option value="B">' . $lang['928'] . '</option>
																	<option value="S">' . $lang['929'] . '</option>
																</select>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['932'] . '</label>
																<input type="email" class="form-control" name="correo" id="correo" value="">
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['939'] . '</label>
																<textarea class="form-control" cols="20" rows="3" spellcheck="false" name="comentarios" id="comentarios"></textarea> 
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																<label class="form-label">' . $lang['912'] . '</label>
																<select class="form-control" name="Validado" id="Validado">
																	<option value="1">' . $lang['940'] . '</option>
																	<option value="0" selected>' . $lang['941'] . '</option>
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
							<div class="row" ' . $signatureStyle . '>
								<h4>' . $lang['949'] . '</h4>
								<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
									<span style="text-align: center; ">
										<div style="width: 150px; height: 194px; background-color: #BFBFBF; margin: auto;">
											<img width="150" height="194" id="firma" src="" alt="" style="display: none"/>
										</div>
									</span>
								</div>
							</div>
							<div class="row" ' . $signatureStyle . '>
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
						<button type="button" class="btn btn-primary" onClick="validate(' . $Category . ', ' . $Team . ');" >' . $lang['0000'] . '</button>
					</div>
					<div class="col-6 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
						<button type="button" class="btn btn-primary" onClick="limpiar();" >' . $lang['0001'] . '</button>
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

	$retunData = array('status' => '1', 'message' => 'Success.', 'dataPlayerAdd' => $htmlPlayer);
    $Config->Close();
    echo json_encode($retunData);
