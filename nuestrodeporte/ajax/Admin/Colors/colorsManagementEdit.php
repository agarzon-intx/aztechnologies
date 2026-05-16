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
	$sessionstat = $fgmembersite->CheckLogin('colorsManagementEdit.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
	$color = SanitizeInteger($_POST['color']);

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');

	$htmlColor = "";

	$sql="	SELECT Color_ID,
						Color_Desc,
						Color_HEX
					FROM $schema.Colores
					where Color_ID = $color;";
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		while($row2 = $result->fetch_assoc()) {
			$colorname = $row2["Color_Desc"];
			$colorHEX = $row2["Color_HEX"]; 
		}		
	}
	
	$htmlColor .= '<div class="container-fluid py-2">
						<div class="row">
							<div class="col-xl-12">
								<h3>' . $lang['90'] . '</h3>
							</div>
						</div>
						<div class="row">
							<div class="col-xl-12">
								<Div id="errorColorE" style="color: red; text-align: justify;"></Div>
							</div>
						</div>
						<div class="row">
							<div class="">
								<form>
									<div class="row">
										<div class="col-4 col-xs-4 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
											<div class="row">
												<div class="col-xl-6">
													<div class="input-group input-group-static mb-4">
														<label>' . $lang['83'] . '</label>
														<input type="text" name="colorDescripcionE" id="colorDescripcionE" value="' . $colorname . '" class="form-control">
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-xl-6">
													<div class="input-group input-group-static mb-4" id="colorTextoE">
														<label>' . $lang['84'] . '</label>
														<input type="text" id="colorEditTextE" name="colorEditTextE" value="' . $colorHEX . '" class="form-control"> 
													</div>
												</div>
											</div>
										</div>
										<div class="col-4 col-xs-4 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
											<div class="row">
												<div class="col-xl-6">
													<h6>' . $lang['87'] . '</h6>
												</div>
											</div>
											<div class="row">
												<div class="col-xl-6">
													<div id="colorEditPickerE"></div>
												</div>
											</div>
										</div>
									</div>
								</form>
							</div>
							<script>
								$(\'#colorEditTextE\').val(\'' . $colorHEX . '\');
								$.farbtastic(\'#colorEditPickerE\').setColor(\'' . $colorHEX . '\');
								$.farbtastic(\'#colorEditPickerE\').linkTo($(\'#colorEditTextE\'));
							</script>
						</div>
						<div class="row">
							<div class="" >
								<button type="button" class="btn btn-primary" onClick="validateColorActualizar(' . $color . ');" >' . $lang['0000'] . '</button>
								<button type="button" class="btn btn-primary" onClick="limpiarColorActualizar(); return false;" >' . $lang['0001'] . '</button>
							</div>
						</div>
					</div>';
					
	$retunData = array('status' => '1', 'message' => 'Success.', 'dataColorEdit' => $htmlColor);
    $Config->Close();
    echo json_encode($retunData);
?>