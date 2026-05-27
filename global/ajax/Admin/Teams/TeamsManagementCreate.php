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
	$Config->LoadFlags();
	
	$sessionstat = $fgmembersite->CheckLogin('TeamsManagementCreate.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
	$Category = SanitizeInteger($_POST["Category"]);
	$Season = $_COOKIE[$Config->getAlias() . 'season'];

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');

	$htmlTeams = "";
	$htmlTeams .= '<div class="container-fluid py-2">
					<div class="row">
						<div class="col-xl-12">
							<h3>' . $lang['527'] . '</h3>
						</div>
					</div>
					<div class="row">
						<div class="col-xl-12">
							<Div id="errorColor" style="color: red; text-align: justify;"></Div>
						</div>
					</div>
					<div class="row">
						<div class="col-12 col-sm-12 col-md-12 col-lg-4 col-xl-4">
							<h2>' . $lang['529'] . '</h2>
							<form>
								<div class="row">
									<div class="col-xl-12" hidden>
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['515'] . '</label>
											<input type="text" class="form-control" name="teamid" id="teamid" value=""/>
										</div>
									</div>
									<div class="col-xl-12">
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['517'] . '</label>
											<input type="text" class="form-control" name="descripcion" id="descripcion" value=""/>
										</div>
									</div>
									<div class="col-xl-12">
										<div class="form-check col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3" style="padding-left: 0px;">
											<input class="form-check-input" type="checkbox" name="estatus" id="estatus" checked>
											<label class="custom-control-label" for="estatus">' . $lang['518'] . '</label>
										</div>
									</div>
									<div class="col-xl-12">
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['519'] . '</label>
											<select class="form-control" name="fuerza" id="fuerza" multiple>';
	$sql3 = 	"SELECT Categoria_ID, Categoria_Desc 
				FROM $schema.Categorias
				where Torneo_Id = $Season
				order by Categoria_Orden;";
	$result3 = $Config->query($sql3);
	if ($result3->num_rows > 0) {
		// output data of each row
		while($row3 = $result3->fetch_assoc()) {
			if($row3["Categoria_ID"] == $Category){
				$htmlTeams .= "<option value='" . $row3["Categoria_ID"] . "' selected>" . $row3["Categoria_Desc"] . "</option>";
			}else{
				$htmlTeams .= "<option value='" . $row3["Categoria_ID"] . "'>" . $row3["Categoria_Desc"] . "</option>";
			}
		}
	} else {
		echo "";
	}
	$htmlTeams .= '							</select>
										</div>
									</div>
									<div class="col-xl-12">
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['520'] . '</label>
											<input type="text" class="form-control" name="descripcionLarga" id="descripcionLarga" value=""/>
										</div>
									</div>
									<div class="col-xl-12">
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['520-1'] . '</label>
											<input type="text" class="form-control" name="descripcion3" id="descripcion3" value="" maxlength="6"/>
										</div>
									</div>
									<div class="col-xl-12">
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['521'] . '</label>
											<select class="form-control" name="campo" id="campo">';
	$sql3 = 	"SELECT Campo_ID, Campo_DESC 
				FROM $schema.Campos
				order by Campo_DESC asc;";
	$result3 = $Config->query($sql3);
	if ($result3->num_rows > 0) {
		// output data of each row
		while($row3 = $result3->fetch_assoc()) {
			$htmlTeams .= "<option value='" . $row3["Campo_ID"] . "'>" . $row3["Campo_DESC"] . "</option>";
		}
	} else {
		echo "";
	}
	$htmlTeams .= '							</select>
										</div>
									</div>
								</div>
							</form>
							<h2>' . $lang['530'] . '</h2>
							<form>
								<div class="row">
									<div class="col-3 col-sm-3 col-md-3 col-xl-3 col-xxl-3" style="text-align: right;">
										<span style="text-align: center;">
											<img style=" background-color: transparent;" id="playera" src="imagenes/uniforme/playera.png" alt="Foto" width="60" height="60"/><span style="text-align: left"></span>
										</span>
									</div>
									<div class="col-9 col-sm-9 col-md-9 col-xl-9 col-xxl-9" style="text-align: left; padding-top: 25px;">
										<span>
											<select id="playeraA" name="playeraA" onChange="$(\'#playera\').css(\'backgroundColor\', $(\'#playeraA\').val());">
													<option style="background-color: #FFFFFF" value="#FFFFFF" selected>----</option>';
													
	$sql3 = "SELECT Color_ID, Color_DESC, Color_HEX FROM $schema.Colores;";
	$result3 = $Config->query($sql3);
	if ($result3->num_rows > 0) {
		// output data of each row
		while($row3 = $result3->fetch_assoc()) {
			$htmlTeams .= "<option style='background-color:" . $row3["Color_HEX"] . "' value='" . $row3["Color_HEX"] . "'>" . $row3["Color_DESC"] . "</option>";
		}
	}
	
$htmlTeams .= '								</select>
										</span>
									</div>
								</div>
								<div class="row">
									<div class="col-3 col-sm-3 col-md-3 col-xl-3 col-xxl-3" style="text-align: right;">
										<span style="text-align: center;">
											<img style=" background-color: transparent;" id="short" src="imagenes/uniforme/short.png" alt="Foto" width="60" height="40"/><span style="text-align: left"></span>
										</span>
									</div>
									<div class="col-9 col-sm-9 col-md-9 col-xl-9 col-xxl-9" style="text-align: left;  padding-top: 7px;">
										<span>
											<select id="shortA" name="shortA" onChange="$(\'#short\').css(\'backgroundColor\', $(\'#shortA\').val());">
													<option style="background-color: #FFFFFF" value="#FFFFFF" selected>----</option>';
													
	$sql3 = "SELECT Color_ID, Color_DESC, Color_HEX FROM $schema.Colores;";
	$result3 = $Config->query($sql3);
	if ($result3->num_rows > 0) {
		// output data of each row
		while($row3 = $result3->fetch_assoc()) {
			$htmlTeams .= "<option style='background-color:" . $row3["Color_HEX"] . "' value='" . $row3["Color_HEX"] . "'>" . $row3["Color_DESC"] . "</option>";
		}
	}
	
$htmlTeams .= '								</select>
										</span>
									</div>
								</div>
								<div class="row">
									<div class="col-3 col-sm-3 col-md-3 col-xl-3 col-xxl-3" style="text-align: right;">
										<span style="text-align: center;">
											<img style=" background-color: transparent;" id="calceta" src="imagenes/uniforme/calcetas.png" alt="Foto" width="60" height="40"/><span style="text-align: left"></span>
										</span>
									</div>
									<div class="col-9 col-sm-9 col-md-9 col-xl-9 col-xxl-9" style="text-align: left; padding-top: 7px;">
										<span>
											<select id="calcetasA" name="calcetasA" onChange="$(\'#calceta\').css(\'backgroundColor\', $(\'#calcetasA\').val());">
													<option style="background-color: #FFFFFF" value="#FFFFFF" selected>----</option>';
													
	$sql3 = "SELECT Color_ID, Color_DESC, Color_HEX FROM $schema.Colores;";
	$result3 = $Config->query($sql3);
	if ($result3->num_rows > 0) {
		// output data of each row
		while($row3 = $result3->fetch_assoc()) {
			$htmlTeams .= "<option style='background-color:" . $row3["Color_HEX"] . "' value='" . $row3["Color_HEX"] . "'>" . $row3["Color_DESC"] . "</option>";
		}
	}
	
$htmlTeams .= '								</select>
										</span>
									</div>
								</div>
							</form>
						</div>
						<div class="col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8">
							<h2>' . $lang['531'] . '</h2>
							<form>
								<div class="row">
									<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
										<span style="text-align: center; ">
											<div style="width: 345px; height: 345px; background-color: #BFBFBF; margin: auto;">
												<img id="logo" src="imagenes/Original/.png" alt="Logo" width="350" height="350" style="display: none"/>
											</div>
										</span>
									</div>
								</div>
								<div class="row">
									<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
										<span style="text-align: center;">
											<div style="margin: auto;">
												<button style="margin: 0; width: 235px;" class="btn btn-secondary" type="button" onclick="fireEvent($(\'#myLogo\'), \'click\');" id="subirfirma">' . $lang['525'] . '</button>' . $lang['936'] . '
												<form>
													<div style="text-align: center;">
														<div>
															<input style="display: none; visibility: hidden;" type="file" accept="image/png" name="myLogo" id="myLogo" onchange="readTeamLogoURL(this, \'logo\');">
															<input type="hidden" name="myLogoFileName" id="myLogoFileName" value="">
														</div>
														<div id=\'previewMyLogo\' style="display: inline-block; vertical-align:middle;"></div>
													</div>
												</form>
											</div>
										</span>
									</div>
								</div>
							</form>
						</div>
					</div>
					<div class="row">
						<div class="my-3" >
							<button type="button" class="btn btn-primary" onClick="validateTeamAdd(' . $Category . ');" >' . $lang['0000'] . '</button>
							<button type="button" class="btn btn-primary" onClick="teamManagementHideAdd();" >' . $lang['0001'] . '</button>
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

	$retunData = array('status' => '1', 'message' => 'Success.', 'teamAdd' => $htmlTeams);
    echo json_encode($retunData);
?>