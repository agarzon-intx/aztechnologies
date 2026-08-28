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
	$sessionstat = $fgmembersite->CheckLogin('InstitutionsManagementEdit.php');

	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	$Season = $_COOKIE[$Config->getAlias() . 'season'];
	$institution_id = SanitizeInteger($_POST['institution']);

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');

	$fecha = new DateTime();
	$institucionidA = '';
	$descripcionA = '';
	$estatusA = '';
	$descripcionLargaA = '';
	$descripcion5A = '';

	$sql="	SELECT a.*
			FROM $schema.Instituciones a
			WHERE a.Torneo_ID = $Season
				AND a.Institucion_ID = $institution_id";
	$result = $Config->query($sql);
	if ($result && $result->num_rows > 0) {
		while($row2 = $result->fetch_assoc()) {
			$institucionidA = $row2["Institucion_ID"];
			$descripcionA = $row2["Institucion_DESC"];
			if($row2["Activo"] == 1) {
				$estatusA = "checked";
			}else{
				$estatusA = "";
			}
			$descripcionLargaA = $row2["Institucion_FULLDESC"];
			$descripcion5A = $row2["Institucion_DESC5"];
		}
	}

	$htmlInstitutions = "";
	$htmlInstitutions .= '<div class="container-fluid py-2">
					<div class="row">
						<div class="col-xl-12">
							<h3>' . $lang['inst009'] . '</h3>
						</div>
					</div>
					<div class="row">
						<div class="col-xl-12">
							<div id="errorColor" style="color: red; text-align: justify;"></div>
						</div>
					</div>
					<div class="row">
						<div class="col-12 col-sm-12 col-md-12 col-lg-4 col-xl-4">
							<h2>' . $lang['529'] . '</h2>
							<form>
								<div class="row">
									<div class="col-xl-12" hidden>
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['inst002'] . '</label>
											<input type="text" class="form-control" name="institutionid" id="institutionid" value="' . htmlspecialchars((string)$institucionidA, ENT_QUOTES, 'UTF-8') . '"/>
										</div>
									</div>
									<div class="col-xl-12">
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['inst004'] . '</label>
											<input type="text" class="form-control" name="descripcion" id="descripcion" value="' . htmlspecialchars((string)$descripcionA, ENT_QUOTES, 'UTF-8') . '"/>
										</div>
									</div>
									<div class="col-xl-12">
										<div class="form-check col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3" style="padding-left: 0px;">
											<input class="form-check-input" type="checkbox" name="estatus" id="estatus" ' . $estatusA . '>
											<label class="custom-control-label" for="estatus">' . $lang['inst005'] . '</label>
										</div>
									</div>
									<div class="col-xl-12">
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['inst006'] . '</label>
											<input type="text" class="form-control" name="descripcionLarga" id="descripcionLarga" value="' . htmlspecialchars((string)$descripcionLargaA, ENT_QUOTES, 'UTF-8') . '"/>
										</div>
									</div>
									<div class="col-xl-12">
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['inst007'] . '</label>
											<input type="text" class="form-control" name="descripcion5" id="descripcion5" value="' . htmlspecialchars((string)$descripcion5A, ENT_QUOTES, 'UTF-8') . '" maxlength="5"/>
										</div>
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
												<img id="logo" src="imagenes/Original/I-' . $Season . '-' . $institucionidA. '.png?tmp=' . $fecha->getTimestamp() . '" alt="Logo" width="350" height="350"/>
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
							<button type="button" class="btn btn-primary" onClick="validateInstitutionEdit(' . (int)$institucionidA . ');" >' . $lang['0000'] . '</button>
							<button type="button" class="btn btn-primary" onClick="institutionManagementHideEdit();" >' . $lang['0001'] . '</button>
						</div>
					</div>
				</div>
				<script>
					var inputs = document.querySelectorAll(\'input\');
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
				</script>';

	$retunData = array('status' => '1', 'message' => 'Success.', 'dataInstitutionEdit' => $htmlInstitutions);
    $Config->Close();
    echo json_encode($retunData);
?>
