<?php
    session_start();
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

	error_reporting(0);

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
	$sessionstat = $fgmembersite->CheckLogin('weekAdmin-ScheduleScores-GameDocuments.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');


    $retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
    
    $Season = $_COOKIE[$Config->getAlias() . 'season'];
    $Week = SanitizeText($_POST['Week']); 
    $Game = SanitizeInteger($_POST['Game']);
	$fecha = new DateTime();
    
	$htmlWeekAdminGameComment = '<div style="height: 70px;"></div>
									<div class="container-fluid py-2">
										<div class="row">
											<div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="border: 1px black; border-style: solid;">
												<div class="row">
													<span style="text-align: center;">' . $lang['317'] . '</span>
												</div>
												<div class="row">
													<span style="text-align: center;">';
	if(file_exists ('../../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo1.png')){
		$htmlWeekAdminGameComment .= '	<img id="Anexo1-' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo1.png?tmp=' . $fecha->getTimestamp() . '" alt="" style="width: 100%; height: auto;"/>';
	}else{
		$htmlWeekAdminGameComment .= '	<img id="Anexo1-' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/error.png" alt="" width="30" height="30"/>';
	}
	$htmlWeekAdminGameComment .= '					</span>
													<form id="anexo1_upload_form" method="post" enctype="multipart/form-data" action="objects/UploadAnexo1.php" autocomplete="off style="margin-block-end: 0.5em;">
														<div>
															<div style="display: inline-block;">
																<input style="display: none; visibility: hidden;" type="file" accept="image/png" name="myanexo1" id="myanexo1" onchange="readURLA1(this, \'Anexo1-' . $Season . '-' . $Week . '-' . $Game . '\');">
																<input type="hidden" name="myAnexo1FileName" id="myAnexo1FileName" value="">
															</div>
															<div id="previewMyAnexo1" style="display: inline-block; vertical-align:middle;"></div>
														</div>
													</form>
													<script>


														function showResponseMyAnexo1(data)  { 
															if(data.status !== \'1\'){ 
																$(\'#Anexo1' . $Season . '-' . $Week . '-' . $Game . '\').src(\'\'); 
																$(\'#previewMyAnexo1\').html(data.alert);
															}
															if(data.status === \'1\'){ 
																$(\'#previewMyAnexo1\').html(data.alert); 
																$(\'#myAnexo1FileName\').val(data.action);
															} 
														} 
													</script>
												</div>
												<div class="row">
													<span style="text-align: center;">
														<div class="my-3" >
															<button id="subirfoto" type="button" class="btn btn-primary" onClick="fireEvent($(\'#myanexo1\'), \'click\');" >' . $lang['0010'] . '</button>
														</div>
													</span>
												</div>
											</div>
											<div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="border: 1px black; border-style: solid;">
												<div class="row">
													<span style="text-align: center;">' . $lang['318'] . '</span>
												</div>
												<div class="row">
													<span style="text-align: center;">';
	if(file_exists ('../../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo2.png')){
		$htmlWeekAdminGameComment .= '	<img id="Anexo2-' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo2.png?tmp=' . $fecha->getTimestamp() . '" alt="" style="width: 100%; height: auto;"/>';
	}else{
		$htmlWeekAdminGameComment .= '	<img id="Anexo2-' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/error.png" alt="" width="30" height="30"/>';
	}
	$htmlWeekAdminGameComment .= '					</span>
													<form id="anexo2_upload_form" method="post" enctype="multipart/form-data" action="objects/UploadAnexo2.php" autocomplete="off style="margin-block-end: 0.5em;">
														<div>
															<div style="display: inline-block;">
																<input style="display: none; visibility: hidden;" type="file" accept="image/png" name="myanexo2" id="myanexo2" onchange="readURLA2(this, \'Anexo2-' . $Season . '-' . $Week . '-' . $Game . '\');">
																<input type="hidden" name="myAnexo2FileName" id="myAnexo2FileName" value="">
															</div>
															<div id="previewMyAnexo2" style="display: inline-block; vertical-align:middle;"></div>
														</div>
													</form>
													<script>


														function showResponseMyAnexo2(data)  { 
															if(data.status !== \'1\'){ 
																$(\'#Anexo2' . $Season . '-' . $Week . '-' . $Game . '\').src(\'\'); 
																$(\'#previewMyAnexo2\').html(data.alert);
															}
															if(data.status === \'1\'){ 
																$(\'#previewMyAnexo2\').html(data.alert); 
																$(\'#myAnexo2FileName\').val(data.action);
															} 
														} 
													</script>
												</div>
												<div class="row">
													<span style="text-align: center;">
														<div class="my-3" >
															<button id="subirfoto" type="button" class="btn btn-primary" onClick="fireEvent($(\'#myanexo2\'), \'click\');" >' . $lang['0010'] . '</button>
														</div>
													</span>
												</div>
											</div>
											<div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="border: 1px black; border-style: solid;">
												<div class="row">
													<span style="text-align: center;">' . $lang['319'] . '</span>
												</div>
												<div class="row">
													<span style="text-align: center;">';
	if(file_exists ('../../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo3.png')){
		$htmlWeekAdminGameComment .= '	<img id="Anexo3-' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo3.png?tmp=' . $fecha->getTimestamp() . '" alt="" style="width: 100%; height: auto;"/>';
	}else{
		$htmlWeekAdminGameComment .= '	<img id="Anexo3-' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/error.png" alt="" width="30" height="30"/>';
	}
	$htmlWeekAdminGameComment .= '					</span>
													<form id="anexo3_upload_form" method="post" enctype="multipart/form-data" action="objects/UploadAnexo3.php" autocomplete="off style="margin-block-end: 0.5em;">
														<div>
															<div style="display: inline-block;">
																<input style="display: none; visibility: hidden;" type="file" accept="image/png" name="myanexo3" id="myanexo3" onchange="readURLA3(this, \'Anexo3-' . $Season . '-' . $Week . '-' . $Game . '\');">
																<input type="hidden" name="myAnexo3FileName" id="myAnexo3FileName" value="">
															</div>
															<div id="previewMyAnexo3" style="display: inline-block; vertical-align:middle;"></div>
														</div>
													</form>
													<script>


														function showResponseMyAnexo3(data)  { 
															if(data.status !== \'1\'){ 
																$(\'#Anexo3' . $Season . '-' . $Week . '-' . $Game . '\').src(\'\'); 
																$(\'#previewMyAnexo3\').html(data.alert);
															}
															if(data.status === \'1\'){ 
																$(\'#previewMyAnexo3\').html(data.alert); 
																$(\'#myAnexo3FileName\').val(data.action);
															} 
														} 
													</script>
												</div>
												<div class="row">
													<span style="text-align: center;">
														<div class="my-3" >
															<button id="subirfoto" type="button" class="btn btn-primary" onClick="fireEvent($(\'#myanexo3\'), \'click\');" >' . $lang['0010'] . '</button>
														</div>
													</span>
												</div>
											</div>
											<div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="border: 1px black; border-style: solid;">
												<div class="row">
													<span style="text-align: center;">' . $lang['320'] . '</span>
												</div>
												<div class="row">
													<span style="text-align: center;">';
	if(file_exists ('../../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo4.png')){
		$htmlWeekAdminGameComment .= '	<img id="Anexo4-' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo4.png?tmp=' . $fecha->getTimestamp() . '" alt="" style="width: 100%; height: auto;"/>';
	}else{
		$htmlWeekAdminGameComment .= '	<img id="Anexo4-' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/error.png" alt="" width="30" height="30"/>';
	}
	$htmlWeekAdminGameComment .= '					</span>
													<form id="anexo4_upload_form" method="post" enctype="multipart/form-data" action="objects/UploadAnexo4.php" autocomplete="off style="margin-block-end: 0.5em;">
														<div>
															<div style="display: inline-block;">
																<input style="display: none; visibility: hidden;" type="file" accept="image/png" name="myanexo4" id="myanexo4" onchange="readURLA4(this, \'Anexo4-' . $Season . '-' . $Week . '-' . $Game . '\');">
																<input type="hidden" name="myAnexo4FileName" id="myAnexo4FileName" value="">
															</div>
															<div id="previewMyAnexo4" style="display: inline-block; vertical-align:middle;"></div>
														</div>
													</form>
													<script>


														function showResponseMyAnexo4(data)  { 
															if(data.status !== \'1\'){ 
																$(\'#Anexo4' . $Season . '-' . $Week . '-' . $Game . '\').src(\'\'); 
																$(\'#previewMyAnexo4\').html(data.alert);
															}
															if(data.status === \'1\'){ 
																$(\'#previewMyAnexo4\').html(data.alert); 
																$(\'#myAnexo4FileName\').val(data.action);
															} 
														} 
													</script>
												</div>
												<div class="row">
													<span style="text-align: center;">
														<div class="my-3" >
															<button id="subirfoto" type="button" class="btn btn-primary" onClick="fireEvent($(\'#myanexo4\'), \'click\');" >' . $lang['0010'] . '</button>
														</div>
													</span>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
												<div class="row">
													<div class="my-3" style="text-align: center;">
														<span>
															<button type="button" class="btn btn-primary" onClick="saveGameDocs(' . $Season . ',' . $Week . ',' . $Game . ');" >' . $lang['0000'] . '</button>
															<button type="button" class="btn btn-primary" onClick="$(\'#gameDocInput\').html(\'\'); $(\'#gameDocInputDiv\').css(\'z-index\', \'-1\');" >' . $lang['0001'] . '</button>
														</span>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>';
    $retunData = array('status' => '1', 'message' => 'Success.', 'dataWeekAdminGameDocs' => $htmlWeekAdminGameComment);
    echo json_encode($retunData);
?>