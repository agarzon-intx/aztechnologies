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
	$sessionstat = $fgmembersite->CheckLogin('AlertsManagementEdit.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
	$alert_id = SanitizeInteger($_POST['alert']);

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');

	$avisoid = ''; 
	$Inicio = ''; 
	$Fin = ''; 
	$Contenido = ''; 
	$Titulo = ''; 
	$Tipo = ''; 
	$Status = '';
	$htmlAlerts = "";

	$sql="	SELECT Aviso_ID, 
				cast(Aviso_Fecha_Inicio as Date) Aviso_Fecha_Inicio,
				cast(Aviso_Fecha_Fin as Date) Aviso_Fecha_Fin,
				Aviso_Contenido,
				Aviso_Titulo,
				Aviso_Tipo,
				case when Aviso_Estatus = 1 then 'Checked' else '' end Aviso_Estatus_check,
				Aviso_Estatus,
				case when Aviso_Mostrar = 1 then 'Checked' else '' end Aviso_Mostrar_check
			FROM $schema.Avisos
			where Aviso_ID = $alert_id;";
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		while($row2 = $result->fetch_assoc()) {
			$avisoid = $row2["Aviso_ID"]; 
			$Inicio = $row2["Aviso_Fecha_Inicio"]; 
			$Fin = $row2["Aviso_Fecha_Fin"]; 
			$Contenido = $row2["Aviso_Contenido"]; 
			$Titulo = $row2["Aviso_Titulo"]; 
			$Tipo = $row2["Aviso_Tipo"]; 
			$Status = $row2["Aviso_Estatus_check"];
			$Mostrar = $row2["Aviso_Mostrar_check"];
		}
	}
	
	$htmlAlerts .= '<div class="container-fluid py-2">
					<div class="row">
						<div class="col-xl-12">
							<h3>' . $lang['412'] . '</h3>
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
											<label class="form-label">' . $lang['402'] . '</label>
											<input type="text" class="form-control" name="avisoid" id="avisoid" value="' . $avisoid . '"/>
										</div>
									</div>
									<div class="col-xl-8">
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['405'] . '</label>
											<input type="text" class="form-control" maxlength="50" size="50" name="Titulo" id="Titulo" value="' . $Titulo . '"/>
										</div>
									</div>
									<div class="col-xl-4">
										<div class="form-check col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3" style="padding-left: 0px;">
											<input class="form-check-input" type="checkbox" name="Mostrar" id="Mostrar" ' . $Mostrar . '>
											<label class="custom-control-label" for="Status">' . $lang['417'] . '</label>
										</div>
									</div>
									<div class="col-xl-4">
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['407'] . '</label>
											<input type="date" class="form-control" name="Inicio" id="Inicio" value="' . $Inicio . '"/>
										</div>
									</div>
									<div class="col-xl-4">
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['408'] . '</label>
											<input class="form-control" type="date" name="Fin" id="Fin" value="' . $Fin . '">
										</div>
									</div>
									<div class="col-xl-4">
										<div class="form-check col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3" style="padding-left: 0px;">
											<input class="form-check-input" type="checkbox" name="Status" id="Status" ' . $Status . '>
											<label class="custom-control-label" for="Status">' . $lang['518'] . '</label>
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
								</div>
							</form>
						</div>
					</div>
					<div class="row">
						<div class="my-3" >
							<button type="button" class="btn btn-primary" onClick="validateAlertEdit(' . $avisoid . ');" >' . $lang['0000'] . '</button>
							<button type="button" class="btn btn-primary" onClick="alertManagementHideEdit();" >' . $lang['0001'] . '</button>
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
					
	$retunData = array('status' => '1', 'message' => 'Success.', 'dataAlertEdit' => $htmlAlerts);
    $Config->Close();
    echo json_encode($retunData);
?>