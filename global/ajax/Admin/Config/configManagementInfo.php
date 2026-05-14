<?php
	$fecha = new DateTime();
			
	$sql2 = "SELECT Logo,
				LogoX,
				LogoY,
				ColorHeader,
				ColorBody,
				ColorFooter,
				TorneoCopaLiga,
				Categorias,
				EmpatesPenales,
				JugadorJugado,
				JuegoCedulas,
				MarcadorArbitro,
				MarcadorFecha,
				MarcadorDiaDefault,
				AvisosTemplete,
				Idioma,
				id,
				JornadaCedulas,
				LeagueName,
				ShowIDColumn,
				Latitude,
				Longitude, 
				ByeWeekPoints,
				ByeWeekPointsGoals
			FROM $schema.Configuration
			where id = 0;";
	$result2 = $Config->query($sql2);
	if ($result2->num_rows > 0) {
	// output data of each row
		while($row2 = $result2->fetch_assoc()) {
			$logo = $row2["Logo"]; 
			$logox = $row2["LogoX"]; 
			$logoy = $row2["LogoY"]; 
			$CHeader = $row2["ColorHeader"]; 
			$CFooter = $row2["ColorFooter"]; 
			$CBody = $row2["ColorBody"]; 
			$leagueName = $row2["LeagueName"];
			$latitude = $row2["Latitude"];
			$longitude = $row2["Longitude"];
		}
	} 
	
	$htmlConfig .= '<div class="container-fluid py-2">
						<div class="row">
							<div class="">
								<Div id="error2"></Div>
							</div>
						</div>
						<div class="row">
							<div class="">
								<form>
									<div class="row">
										<div class="col-12 col-xs-5 col-sm-5 col-md-5 col-lg-5 col-xl-5 col-xxl-5">
											<div class="input-group input-group-static mb-4">
												<label>' . $lang['475'] . '</label>
												<input type="text" class="form-control" name="leagueName" id="leagueName" value="' . $leagueName . '">
											</div>
										</div>
										<div class="col-6 col-xs-3 col-sm-3 col-md-3 col-lg-3 col-xl-3 col-xxl-3">
											<div class="input-group input-group-static mb-4">
												<label>' . $lang['477'] . '</label>
												<input type="text" class="form-control" name="latitude" id="latitude" value="' . $latitude . '">
											</div>
										</div>
										<div class="col-6 col-xs-3 col-sm-3 col-md-3 col-lg-3 col-xl-3 col-xxl-3">
											<div class="input-group input-group-static mb-4">
												<label>' . $lang['478'] . '</label>
												<input type="text" class="form-control" name="longitude" id="longitude" value="' . $longitude . '">
											</div>
										</div>
									</div>
								</form>
							</div
						</div>
						<div class="row">
							<div class="">
								<h2>' . $lang['442'] . '</h2>
								<form>
									<div class="row">
										<div class="col-4 col-xs-4 col-sm-4 col-md-3 col-lg-3 col-xl-3 col-xxl-3">
											<div class="row">
												<div class="col-xl-12">
													<div class="input-group input-group-static mb-4">
														<label>' . $lang['443'] . '</label>
														<input type="number" name="logox" id="logox" value="' . $logox . '" class="form-control"> 
														<script>
																$(function () {
																   $( "#logox" ).change(function() {
																	  var max = 110;
																	  var min = 0;
																	  if ($(this).val() > max)
																	  {
																		  $(this).val(max);
																	  }
																	  if ($(this).val() < min)
																	  {
																		  $(this).val(min);
																	  }
																	}); 
																});
														</script>
														<input style="width:200px" type="hidden" name="logo" id="logo" value="' . $logo . '" onkeypress="return isNumberKey(event)">
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-xl-12">
													<div class="input-group input-group-static mb-4">
														<label>' . $lang['444'] . '</label>
														<input type="number" name="logoy" id="logoy" value="' . $logoy . '" class="form-control" onkeypress="return isNumberKey(event)"> 
														<script>
																$(function () {
																	$( "#logoy" ).change(function() {
																		var max = 110;
																		var min = 0;
																		if ($(this).val() > max){
																			$(this).val(max);
																		}
																		if ($(this).val() < min){
																			$(this).val(min);
																		}
																	}); 
																});
														</script>
													</div>
												</div>
											</div>
										</div>
										<div class="col-4 col-xs-4 col-sm-4 col-md-3 col-lg-3 col-xl-3 col-xxl-3">
											<div class="d-none d-xs-none d-md-block d-lg-block d-xl-block d-xxl-block">
												<button type="button" class="btn btn-secondary" onClick="document.getElementById(\'logoImage\').width = document.getElementById(\'logox\').value; document.getElementById(\'logoImage\').height = document.getElementById(\'logoy\').value; return false;">' . $lang['446'] . '</button>
											</div>
											<div class="d-block d-xs-block d-md-none d-lg-none d-xl-none d-xxl-none">
												<button style="font-size: 0.55rem;" type="button" class="btn btn-secondary" onClick="document.getElementById(\'logoImage\').width = document.getElementById(\'logox\').value; document.getElementById(\'logoImage\').height = document.getElementById(\'logoy\').value; return false;">' . $lang['446'] . '</button>
											</div>
										</div>
										<div class="col-4 col-xs-4 col-sm-4 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
											<div class="row">
												<div class="">
													<span style="text-align: center; ">
														<div style="width: 110px; height: 110px; background-color: #BFBFBF;">
															<img id="logoImage" src="imagenes/' . $logo . '.png?tmp=' . $fecha->getTimestamp() . '" alt="Foto" width="' . $logox . '" height="' . $logoy . '"/>
														</div>
													</span>
												</div>
												<div class="py-1">
													<span style="text-align: left; ">
														<button style="margin: 0; width: 110px;" class="btn btn-secondary" type="button" onclick="fireEvent($(\'#myLogo\'), \'click\');" id="subirfoto">' . $lang['445'] . '</button>
														<form style="margin-block-end: 0.5em;">
															<div style="text-align: left;">
																<div style="display: inline-block;">
																	<input style="display: none; visibility: hidden;" type="file" accept="image/png" name="myLogo" id="myLogo" onchange="readURLLogo(this, \'logoImage\');">
																	<input type="hidden" name="myLogoFileName" id="myLogoFileName" value="">
																</div>
																<div id=\'previewMyLogo\' style="display: inline-block; vertical-align:middle;"></div>
															</div>
														</form>
													</span>
												</div>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
						<div class="row" style="display: none;">
							<div class="" >
								<h2>' . $lang['447'] . '</h2>
								<form>
									<div class="row">
										<div class="col-4 col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4">
											<div class="input-group input-group-static mb-4">
												<label>' . $lang['448'] . '</label>
												<td id="HeaderMuestra" style="border: 1px double black !important; text-align:left; padding-left: 5px; width: 405px; background-color: ' . $CHeader . '">
													<input type="color" name="colorHEdit" id="colorHEdit" value="' . $CHeader . '" onChange="document.getElementById(\'HeaderMuestra\').style.backgroundColor = document.getElementById(\'colorHEdit\').value;" class="form-control">
												</td>
											</div>
										</div>
										<div class="col-4 col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4">
											<div class="input-group input-group-static mb-4">
												<label>' . $lang['449'] . '</label>
												<td id="BodyMuestra" style="border: 1px double black!important; text-align:left; padding-left: 5px; width: 405px; background-color: ' . $CBody . '">
													<input type="color" name="colorBEdit" id="colorBEdit" value="' . $CBody . '" onChange="document.getElementById(\'BodyMuestra\').style.backgroundColor = document.getElementById(\'colorBEdit\').value;" class="form-control">
												</td>
											</div>
										</div>
										<div class="col-4 col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4">
											<div class="input-group input-group-static mb-4">
												<label>' . $lang['450'] . '</label>
												<td id="FooterMuestra" style="border: 1px double black!important; text-align:left; padding-left: 5px; width: 405px; background-color: ' . $CFooter . '">
													<input type="color" name="colorFEdit" id="colorFEdit" value="' . $CFooter . '" onChange="document.getElementById(\'FooterMuestra\').style.backgroundColor = document.getElementById(\'colorFEdit\').value;" class="form-control">
												</td>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
						<div class="row">
							<div class="" >
								<button type="button" class="btn btn-primary" onClick="validateInfo()" >' . $lang['0000'] . '</button>
							</div>
						</div>
					</div>
				</div>';
?>