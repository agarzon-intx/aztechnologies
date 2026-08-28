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
	$sessionstat = $fgmembersite->CheckLogin('playersManagementEdit.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	$signatureStyle = $Config->playerSignatureEnabled() ? '' : 'style="display: none;"';
	$idPdfEnabled = $Config->playerIDPDFEnabled();
	$idImageStyle = $idPdfEnabled ? 'style="display: none;"' : '';
	$idPdfStyle = $idPdfEnabled ? '' : 'style="display: none;"';
	$idPdfExists = false;
	if ($idPdfEnabled && $Config->jugadoresHasColumn('IdentificacionPDF')) {
		$pdfCheck = $Config->query("SELECT OCTET_LENGTH(IdentificacionPDF) len FROM $schema.Jugadores WHERE Jugador_ID = " . SanitizeInteger($_POST['player']));
		if ($pdfCheck && $pdfRow = $pdfCheck->fetch_assoc()) {
			$idPdfExists = (int) $pdfRow["len"] > 100;
		}
	}
	
	$Season = $_COOKIE[$Config->getAlias() . 'season'];
    $Category = $_COOKIE[$Config->getAlias() . 'category'];
	$player = SanitizeInteger($_POST['player']);

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');

	$htmlPlayer = "";
	$fecha = new DateTime();
	$Config->LoadFlags();

	$sql0="	SELECT Jugador_ID,
				Clave,
				Nombre,
				Apellido_P,
				Apellido_M,
				Apodo,
				Fecha_Nacimiento,
				Curp,
				Numero,
				Estatus,
				a.Equipo_ID,
				b.Equipo_FULLDESC,
				Comentarios,
				Foto,
				Identificacion,
				Firma, 
				Telefono,
				correo,
				Validado,
				Sexo,
				Jugador_tipo,
				case when FechaValidacionCurp is null then 'hidden' else '' end curplink
			FROM $schema.Jugadores a
				join $schema.Equipos b on a.Equipo_ID = b.Equipo_ID
					 and b.Torneo_ID in ($Season)
			where Jugador_ID = $player;";
	$result = $Config->query($sql0);
	if ($result->num_rows > 0) {
		while($row2 = $result->fetch_assoc()) {
			$curplinkA = $row2["curplink"];
			$nombreA = $row2["Nombre"]; 
			$apellidopA = $row2["Apellido_P"]; 
			$apellidomA = $row2["Apellido_M"]; 
			$apodoA = $row2["Apodo"]; 
			$fecnaciA = $row2["Fecha_Nacimiento"]; 
			$curpA = $row2["Curp"]; 
			$numeroA = $row2["Numero"];  
			$equipoidA = $row2["Equipo_ID"]; 
			$equipoA = $row2["Equipo_FULLDESC"]; 
			$telefonoA = $row2["Telefono"]; 
			$estatusA = $row2["Estatus"]; 
			$estatusAA = "";
			$estatusAB = "";
			$estatusAS = "";
			if($estatusA == 'A') $estatusAA = "selected";
			if($estatusA == 'B') $estatusAB = "selected";
			if($estatusA == 'S') $estatusAS = "selected";
			$tipoA = $row2["Jugador_tipo"];
			$sexoA = $row2["Sexo"];
			$sexoA0 = "";
			$sexoA1 = "";
			if($sexoA == "0") $sexoA0 = "selected";
			if($sexoA == "1") $sexoA1 = "selected";
			$correoA = $row2["correo"]; 
			$fotoA = $row2["Foto"]; 
			$firmaA = $row2["Firma"]; 
			$idA = $row2["Identificacion"]; 
			$claveA=strtoupper(substr($equipoA,0,1) . "" . substr($equipoA,strlen($equipoA)-1,1) . "" . substr($nombreA,0,1) . "" . 
				substr($apellidopA,0,1) . "" . substr($fecnaciA,2,2) . "" . 
				substr($fecnaciA,5,2) . "" . substr($fecnaciA,8,2) . "-" . 
				$numeroA);
			$fotostrA = $row2["Foto"]; 
			$fotostrAN = 'style="display: block"';
			if($fotostrA == "#") $fotostrAN = 'style="display: none"';
			$idstrA = $row2["Identificacion"]; 
			$idstrAN = 'style="display: block"';
			if($idstrA == "#") $idstrAN = 'style="display: none"';
			$firmastrA = $row2["Firma"]; 
			$firmastrAN = 'style="display: block"';
			if($firmastrA == "#") $firmastrAN = 'style="display: none"';
			$comentariosA = $row2["Comentarios"]; 
			$validadoA = $row2["Validado"];
			$validadoA1 = "";
			$validadoA0 = "";
			if($validadoA == '1') $validadoA1 = "selected";
			if($validadoA == '0') $validadoA0 = "selected";
			
		}		
	}
	
	$htmlPlayer .= '<div class="container-fluid py-2">
						<input type="hidden" name="fotostrE" id="fotostrE" value=""/>
						<input type="hidden" name="idstrE" id="idstrE" value=""/>
						<input type="hidden" name="firmastrE" id="firmastrE" value=""/>
						<div class="row">
							<div class="col-xl-12" style="text-align: center;">
								<h3>' . $lang['918-1'] . '</h3>
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
														<img id="fotoE" src="Form/fetch_image.php?Jugador_ID=' . $player . '&Imagen=Foto" alt="Foto" width="161" height="220" ' . $fotostrAN . '/>
													</div>
												</span>
											</div>
										</div>
										<div class="row">
											<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12" style="width: 100%;">
												<span style="text-align: left; ">
													<button style="margin: 0; width: 120px;" class="btn btn-secondary" type="button" onclick="fireEvent($(\'#myFotoE\'), \'click\');" id="subirfoto">' . $lang['930'] . '</button>' . $lang['931'] . '
													<form>
														<div style="text-align: center;">
															<div>
																<input style="display: none; visibility: hidden;" type="file" accept="image/png" name="myFotoE" id="myFotoE" onchange="readURLE(this, \'fotoE\');">
																<input type="hidden" name="myFotoFileNameE" id="myFotoFileNameE" value="">
															</div>
															<div id=\'previewMyFotoE\' style="text-align: center;"></div>
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
			where b.Equipo_ID = $equipoidA
			order by c.categoria_ID asc, b.Equipo_FULLDESC";
			//echo $sql;
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row2 = $result->fetch_assoc()) {
			if($equipoidA == $row2["Equipo_ID"]){
				$TeamLogo = $row2["Logo"];
			}
		}
	}
    $apodoenable = '';
    if($Config->Apodo == 0){
        $apodoenable = 'disabled';
    }
	$htmlPlayer .= '								<img width="120" height="120" id="logoEE" src="imagenes/' . $TeamLogo . '.png?tmp=' . $fecha->getTimestamp() . '"/>
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
																	<input disabled type="text" class="form-control" name="nombreE" id="nombreE" value="' . $nombreA . '"/>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
																<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																	<label class="form-label">' . $lang['919'] . '</label>
																	<input disabled type="text" class="form-control" name="apellidopE" id="apellidopE" value="' . $apellidopA . '"/>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
																<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																	<label class="form-label">' . $lang['920'] . '</label>
																	<input disabled type="text" class="form-control" name="apellidomE" id="apellidomE" value="' . $apellidomA . '"/>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
																<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																	<label class="form-label">' . $lang['914'] . '</label>
																	<select disabled class="form-control" name="sexoE" id="sexoE">
																		<option value="0" ' . $sexoA0 . '>' . $lang['944'] . '</option>
																		<option value="1" ' . $sexoA1 . '>' . $lang['945'] . '</option>
																	</select>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
																<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																	<label class="form-label">' . $lang['906'] . '</label>
																	<input type="text" class="form-control" name="apodoE" id="apodoE" value="' . $apodoA . '" ' . $apodoenable . '>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
																<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																	<label class="form-label">' . $lang['921'] . '</label>
																	<input disabled type="date" class="form-control" name="fechanacE" id="fechanacE"  value="' . $fecnaciA . '">
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
																<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																	<label class="form-label">' . $lang['922'] . '</label>
																	<input disabled type="text" class="form-control" name="curpE" id="curpE" value="' . $curpA . '">
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
																<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																	<label class="form-label">' . $lang['923'] . '</label>
																	<input type="number" class="form-control" name="numeroE" id="numeroE" value="' . $numeroA . '">
																</div>
															</div>
														</div>
														<div class="row">
    														<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
    															<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
    																<label class="form-label">' . $lang['924-1'] . '</label>
    																<select class="form-control" name="typeE" id="typeE" >';
if ($tipoA == 0) {
$htmlPlayer .= '													    <option value="0" selected="selected">' . $lang['924-10'] . '</option>';
}else{
$htmlPlayer .= '													    <option value="0"">' . $lang['924-10'] . '</option>';
}
if ($tipoA == 1) {
$htmlPlayer .= '													    <option value="1" selected="selected">' . $lang['924-11'] . '</option>';
}else{
$htmlPlayer .= '													    <option value="1"">' . $lang['924-11'] . '</option>';
}
if ($tipoA == 2) {
$htmlPlayer .= '													    <option value="2" selected="selected">' . $lang['924-12'] . '</option>';
}else{
$htmlPlayer .= '													    <option value="2"">' . $lang['924-12'] . '</option>';
}
$htmlPlayer .= '													</select>
    															</div>
    														</div>
    													</div>
														<div class="row">
															<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
																<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																	<label class="form-label">' . $lang['924'] . '</label>
																	<select disabled class="form-control" onChange="playerManagementLoadImage(\'equipoE\', \'logoEE\')" name="equipoE" id="equipoE" >';
$Config->query($sql);
$sql = "SELECT c.Categoria_ID, b.Equipo_ID, concat(c.categoria_DESC,' - ',b.Equipo_FULLDESC) Equipo_FULLDESC, concat(b.Torneo_ID,'-', b.Equipo_ID) Logo
		from (	select a.* 
				from $schema.Equipos a
				where Equipo_ID > 0 
					and Torneo_ID = $Season) b
			join $schema.Categorias c on b.Fuerza = c.Categoria_ID 
		order by c.categoria_ID asc, b.Equipo_FULLDESC";	
$result = $Config->query($sql);
if ($result->num_rows > 0) {
	// output data of each row
	while($row2 = $result->fetch_assoc()) {
		if($equipoidA == $row2["Equipo_ID"]){
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
																	<input type="number" class="form-control" name="telefonoE" id="telefonoE" value="' . $telefonoA . '">
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
																<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																	<label class="form-label">' . $lang['926'] . '</label>
																	<select class="form-control" name="Estatus2E" id="Estatus2E">
																		<option value="A" ' . $estatusAA . '>' . $lang['927'] . '</option>
																		<option value="B" ' . $estatusAB . '>' . $lang['928'] . '</option>
																		<option value="S" ' . $estatusAS . '>' . $lang['929'] . '</option>
																	</select>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
																<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																	<label class="form-label">' . $lang['932'] . '</label>
																	<input type="email" class="form-control" name="correoE" id="correoE" value="' . $correoA . '">
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
																<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																	<label class="form-label">' . $lang['939'] . '</label>
																	<textarea class="form-control" cols="20" rows="3" spellcheck="false" name="comentariosE" id="comentariosE">' . $comentariosA . '</textarea> 
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
																<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																	<label class="form-label">' . $lang['912'] . '</label>
																	<select disabled class="form-control" name="ValidadoE" id="ValidadoE">
																		<option value="1" ' . $validadoA1 . '>' . $lang['940'] . '</option>
																		<option value="0" ' . $validadoA0 . '>' . $lang['941'] . '</option>
																	</select>
																</div>
															</div>
														</div>
														<div class="row" ' . $curplinkA . '>
															<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
																<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
																	<label class="form-label">' . $lang['959'] . '</label>
																	<a class="form-control" href="Form/fetch_pdf.php?curp=' . $curpA . '" target="_blank">' . $curpA . '</a>
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
								<div ' . $idImageStyle . '>
								<h4>' . $lang['948'] . '</h4>
								<div class="row" style="width: 100%;" >
									<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
										<div id="myIDBackgroundE">
											<div class="row">
												<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
													<span style="text-align: center; ">
														<div style="width: 150px; height: 187px; background-color: #BFBFBF; margin: auto;">
															<img id="identificacionE" src="Form/fetch_image.php?Jugador_ID=' . $player . '&Imagen=Identificacion" width="150" height="187" ' . $idstrAN . '/>
														</div>
													</span>
												</div>
											</div>
											<div class="row">
												<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12" style="width: 100%;">
													<span style="text-align: left; ">
														<button style="margin: 0; width: 180px;" class="btn btn-secondary" type="button" onclick="$(\'#myIDBackgroundE\').toggle();$(\'#myIDBackground2E\').toggle();" id="subiridentificacion">' . $lang['933'] . '</button>
													</span>
												</div>
											</div>
										</div>
										<div id="myIDBackground2E" style="display: none;">
											<div class="row">
												<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
													<span style="text-align: center; ">
														<div style="width: 150px; height: 98px; background-color: #BFBFBF; margin: auto;">
															<img id="identificacion11E" src=""  width="150px" height="98px" style="display: none"/>
														</div>
													</span>
												</div>
											</div>
											<div class="row">
												<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12" style="width: 100%;">
													<span style="text-align: left; ">
														<button style="margin: 0; width: 135px;" class="btn btn-secondary" type="button" onclick="fireEvent($(\'#myID11E\'), \'click\');" id="subiridentificacion11E">' . $lang['933-1'] . '</button>' . $lang['931'] . '
														<form>
															<div style="text-align: center;">
																<div>
																	<input style="display: none; visibility: hidden;" type="file" accept="image/png" name="myID11E" id="myID11E" onchange="readIDURL11E(this, \'identificacion11E\');">
																	<input type="hidden" name="myID11FileNameE" id="myID11FileNameE" value="">
																</div>
																<div id=\'previewMyID11E\' style="display: inline-block; vertical-align:middle;"></div>
															</div>
														</form>
													</span>
												</div>
											</div>
											<div class="row">
												<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
													<span style="text-align: center; ">
														<div style="width: 150px; height: 98px; background-color: #BFBFBF; margin: auto;">
															<img id="identificacion12E" src="" width="150px" height="98px" style="display: none"/>
														</div>
													</span>
												</div>
											</div>
											<div class="row">
												<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12" style="width: 100%;">
													<span style="text-align: left; ">
														<button style="margin: 0; width: 135px;" class="btn btn-secondary" type="button" onclick="fireEvent($(\'#myID12E\'), \'click\');" id="subiridentificacion12E">' . $lang['933-2'] . '</button>' . $lang['931'] . '
														<form>
															<div style="text-align: center;">
																<div>
																	<input style="display: none; visibility: hidden;" type="file" accept="image/png" name="myID12E" id="myID12E" onchange="readIDURL12E(this, \'identificacion12E\');">
																	<input type="hidden" name="myID12FileNameE" id="myID12FileNameE" value="">
																</div>
																<div id=\'previewMyID12E\' style="display: inline-block; vertical-align:middle;"></div>
															</div>
														</form>
													</span>
												</div>
											</div>
										</div>
									</div>
								</div>
								</div>
								<div ' . $idPdfStyle . '>
									<h4>' . $lang['539'] . '</h4>
									<div class="row">
										<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
											<span style="text-align: center; ">
												<div style="width: 100%; height: 320px; background-color: #BFBFBF; margin: auto;">
													<iframe id="identificacionPDFE" src="' . ($idPdfExists ? 'Form/fetch_pdf.php?Jugador_ID=' . $player . '&tmp=' . $fecha->getTimestamp() : '') . '" title="' . $lang['539'] . '" style="width: 100%; height: 320px; border: 0; ' . ($idPdfExists ? '' : 'display: none;') . '"></iframe>
												</div>
											</span>
										</div>
									</div>
									<div class="row">
										<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12" style="width: 100%;">
											<span style="text-align: left; ">
												<button style="margin: 0; width: 135px;" class="btn btn-secondary" type="button" onclick="fireEvent($(\'#myIDPDFE\'), \'click\');" id="subiridentificacionpdfE">' . $lang['933-1'] . '</button>
												<form>
													<div style="text-align: center;">
														<div>
															<input style="display: none; visibility: hidden;" type="file" accept="application/pdf" name="myIDPDFE" id="myIDPDFE" onchange="readIDPDFURLE(this, \'identificacionPDFE\');">
															<input type="hidden" name="myIDPDFFileNameE" id="myIDPDFFileNameE" value="">
														</div>
														<div id=\'previewMyIDPDFE\' style="display: inline-block; vertical-align:middle;"></div>
													</div>
												</form>
											</span>
										</div>
									</div>
								</div>
								<div class="row" ' . $signatureStyle . '>
									<h4>' . $lang['949'] . '</h4>
									<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
										<span style="text-align: center; ">
											<div style="width: 150px; height: 60px; background-color: #BFBFBF; margin: auto;">
												<img width="150" height="60" id="firmaE" src="Form/fetch_image.php?Jugador_ID=' . $player . '&Imagen=Firma" alt="" ' . $firmastrAN . '/>
											</div>
										</span>
									</div>
								</div>
								<div class="row" ' . $signatureStyle . '>
									<div class="form-check col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
										<span style="text-align: left; ">
											<button style="margin: 0; width: 135px;" class="btn btn-secondary" type="button" onclick="fireEvent($(\'#myFirmaE\'), \'click\');" id="subirfirmaE">' . $lang['935'] . '</button>' . $lang['936'] . '
											<form>
												<div style="text-align: center;">
													<div>
														<input style="display: none; visibility: hidden;" type="file" accept="image/png" name="myFirmaE" id="myFirmaE" onchange="readFirmaURLE(this, \'firmaE\');">
														<input type="hidden" name="myFirmaFileNameE" id="myFirmaFileNameE" value="">
													</div>
													<div id=\'previewMyFirmaE\' style="display: inline-block; vertical-align:middle;"></div>
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
							<button type="button" class="btn btn-primary" onClick="validateETeam(' . $player . ', ' . $equipoidA . ');" >' . $lang['0000'] . '</button>
						</div>
						<div class="col-6 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<button type="button" class="btn btn-primary" onClick="limpiarE();" >' . $lang['0001'] . '</button>
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

	$retunData = array('status' => '1', 'message' => 'Success.', 'dataPlayerEdit' => $htmlPlayer, 'sql' => $sql0);
    $Config->Close();
    echo json_encode($retunData);
?>