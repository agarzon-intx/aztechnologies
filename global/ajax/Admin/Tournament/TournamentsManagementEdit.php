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
	$sessionstat = $fgmembersite->CheckLogin('TournamentsManagementEdit.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
	$tournament_id = SanitizeInteger($_POST['tournament']);

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');

	$name = '';
	$actual = 0; 
	$insc = 0; 
	$vs = 0; 
	$weeks = 0; 
	
	$htmlTournament = "";

	$sql="	SELECT Torneo_ID,
				Torneo_Desc,
				case when Actual = 'S' then 'checked' else '' end Actual,
				case when Inscripciones = 1 then 'checked' else '' end Inscripciones,
				case when TodosVsTodos = 1 then 'checked' else '' end TodosVsTodos,
				Jornadas, Rondas
			FROM $schema.Torneos
			WHERE Torneo_ID = $tournament_id
			ORDER BY Torneo_ID desc;";
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		while($row2 = $result->fetch_assoc()) {
			$name = $row2["Torneo_Desc"];
			$actual = $row2["Actual"]; 
			$insc = $row2["Inscripciones"]; 
			$vs = $row2["TodosVsTodos"]; 
			$weeks = $row2["Jornadas"]; 
			$rounds = $row2["Rondas"]; 
		}		
	}
	
	$htmlTournament = "";
	$htmlTournament .= '<div class="container-fluid py-2">
					<div class="row">
						<div class="col-xl-12">
							<h3>' . $lang['761'] . '</h3>
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
											<label class="form-label">' . $lang['751'] . '</label>
											<input type="text" class="form-control" name="torneoid2" id="torneoid2" value="' . $tournament_id . '"/>
										</div>
									</div>
									<div class="col-xl-12">
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['752'] . '</label>
											<input type="text" class="form-control" name="descripcion2" id="descripcion2" value="' . $name . '"/>
										</div>
									</div>
									<div class="form-check col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3">
										<label class="custom-control-label" for="actual2">' . $lang['753'] . '</label>
										<input class="form-check-input" type="checkbox" name="actual2" id="actual2" ' . $actual . '>
									</div>
									<div class="form-check col-12 ccol-sm-6 col-md-4 col-lg-4 col-xl-3">
										<label class="custom-control-label" for="inscripciones2">' . $lang['754'] . '</label>
										<input class="form-check-input" type="checkbox" name="inscripciones2" id="inscripciones2" ' . $insc . '>
									</div>
									<div class="form-check col-12 ccol-sm-6 col-md-4 col-lg-4 col-xl-3">
										<label class="custom-control-label" for="vs2">' . $lang['762'] . '</label>
										<input class="form-check-input" type="checkbox" name="vs2" id="vs2" ' . $vs . '>
									</div>
									<div class="col-xl-12">
										<div class="input-group input-group-outline my-3 mb-4">
											<label class="form-label">' . $lang['763'] . '</label>
											<input type="number" id="jornadas2" name="jornadas2" value="' . $weeks . '" class="form-control"> 
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
					<div class="row">
						<div class="" >
							<button type="button" class="btn btn-primary" onClick="validateTournamentEdit(' . $tournament_id . ');" >' . $lang['0000'] . '</button>
							<button type="button" class="btn btn-primary" onClick="tournamentsManagementHideEdit();" >' . $lang['0001'] . '</button>
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
					
	$retunData = array('status' => '1', 'message' => 'Success.', 'dataTournamentEdit' => $htmlTournament);
    $Config->Close();
    echo json_encode($retunData);
?>