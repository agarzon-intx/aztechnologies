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
	$sessionstat = $fgmembersite->CheckLogin('CategoriesManagementEdit.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
    
    $Season = $_COOKIE[$Config->getAlias() . 'season'];
	$category_id = SanitizeInteger($_POST['category']);

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');

	$htmlCategory = "";

	$sql="	SELECT Categoria_ID,
					Categoria_Desc,
					Categoria_Orden,
					Edad_Inicial,
					Edad_Final,
					Color,
				    ca.Calendario_DESC,
					Rondas
			FROM $schema.Categorias c
				join $schema.Calendario ca on c.Calendario_Id = ca.Calendario_ID
			where Categoria_ID = $category_id
			    and Torneo_Id = $Season;";
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		while($row2 = $result->fetch_assoc()) {
			$descripcion = $row2["Categoria_Desc"]; 
			$orden = $row2["Categoria_Orden"]; 
			$EdadInicial = $row2["Edad_Inicial"]; 
			$EdadFinal = $row2["Edad_Final"]; 
			$Color = $row2["Color"]; 
			$calendario = $row2["Calendario_DESC"]; 
			$rondas = $row2["Rondas"]; 
		}		
	}
	
	$htmlCategory = "";
	$htmlCategory .= '<div class="container-fluid py-2">
					<div class="row">
						<div class="col-xl-12">
							<h3>' . $lang['61'] . '</h3>
						</div>
					</div>
					<div class="row">
						<div class="col-xl-12">
							<Div id="errorColor" style="color: red; text-align: justify;"></Div>
						</div>
					</div>
					<div class="row">
						<div class="">
							<form>
								<div class="row">
									<div class="col-xl-12" hidden>
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['51'] . '</label>
											<input type="text" class="form-control" name="categoryid2" id="categoryid2" value=""/>
										</div>
									</div>
									<div class="col-xl-12">
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['52'] . '</label>
											<input type="text" class="form-control" name="descripcion2" id="descripcion2" value="' . $descripcion . '"/>
										</div>
									</div>
									<div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3">
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['53'] . '</label>
											<input type="number" class="form-control" name="orden2" id="orden2" value="' . $orden . '"/>
										</div>
									</div>
									<div class="col-12 ccol-sm-6 col-md-4 col-lg-4 col-xl-3">
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['54'] . '</label>
											<input type="number" class="form-control" name="Inicial2" id="Inicial2" value="' . $EdadInicial . '"/>
										</div>
									</div>
									<div class="col-12 ccol-sm-6 col-md-4 col-lg-4 col-xl-3">
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['55'] . '</label>
											<input type="number" class="form-control" name="Final2" id="Final2" value="' . $EdadFinal . '"/>
										</div>
									</div>
									<div class="col-12 ccol-sm-6 col-md-4 col-lg-4 col-xl-3">
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['56'] . '</label>
											<select class="form-control" onChange="document.getElementById(\'Color2\').style.backgroundColor = document.getElementById(\'Color2\').value;" name="Color2" id="Color2" >';
$sql = "SELECT Color_ID, Color_DESC, Color_HEX  
		FROM $schema.Colores;";
$result = $Config->query($sql);
if ($result->num_rows > 0) {
// output data of each row
	while($row3 = $result->fetch_assoc()) {
		if($row3["Color_HEX"] == $Color){
			$htmlCategory .= "<option style='background-color:" . $row3["Color_HEX"] . "' value='" . $row3["Color_HEX"] . "' selected>" . $row3["Color_DESC"] . "</option>";
		}else{
			$htmlCategory .= "<option style='background-color:" . $row3["Color_HEX"] . "' value='" . $row3["Color_HEX"] . "'>" . $row3["Color_DESC"] . "</option>";
		}
	}
}
$htmlCategory .= '							</select>
											<script>
												document.getElementById(\'Color2\').style.backgroundColor = document.getElementById(\'Color2\').value;
											</script>
										</div>
									</div>
									<div class="col-12 ccol-sm-6 col-md-4 col-lg-4 col-xl-3">
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['62'] . '</label>
											<select class="form-control" name="calendario2" id="calendario2" >';
$sql = "SELECT Calendario_ID, Calendario_DESC
		FROM $schema.Calendario;";
$result = $Config->query($sql);
if ($result->num_rows > 0) {
// output data of each row
	while($row3 = $result->fetch_assoc()) {
		if($row3["Calendario_DESC"] == $calendario){
			$htmlCategory .= "<option value='" . $row3["Calendario_ID"] . "' selected>" . $row3["Calendario_DESC"] . "</option>";
		}else{
			$htmlCategory .= "<option value='" . $row3["Calendario_ID"] . "'>" . $row3["Calendario_DESC"] . "</option>";
		}
	}
}
$htmlCategory .= '							</select>
											<script>
												document.getElementById(\'Color2\').style.backgroundColor = document.getElementById(\'Color2\').value;
											</script>
										</div>
									</div>
									<div class="col-12 ccol-sm-6 col-md-4 col-lg-4 col-xl-3">
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['63'] . '</label>
											<input type="number" class="form-control" name="rondas2" id="rondas2" value="' . $rondas . '"/>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
					<div class="row">
						<div class="my-3" >
							<button type="button" class="btn btn-primary" onClick="validateCategoryEdit(' . $category_id . ');" >' . $lang['0000'] . '</button>
							<button type="button" class="btn btn-primary" onClick="categoryManagementHideEdit();" >' . $lang['0001'] . '</button>
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
					
	$retunData = array('status' => '1', 'message' => 'Success.', 'dataCategoryEdit' => $htmlCategory);
    $Config->Close();
    echo json_encode($retunData);
?>