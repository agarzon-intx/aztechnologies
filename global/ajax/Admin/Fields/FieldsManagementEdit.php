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
	$sessionstat = $fgmembersite->CheckLogin('FieldsManagementEdit.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
	$field_id = SanitizeInteger($_POST['field']);

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');

	$htmlFields = "";

	$sql="	SELECT Campos.Campo_ID,
					Campos.Campo_Desc,
					Campos.Google,
					ifnull(Campos.Lat, 19.281454) Lat,
					ifnull(Campos.Lon, -99.656296) Lon,
					ifnull(Campos.Zoom, 11) Zoom
				FROM $schema.Campos
					where Campo_ID = $field_id";
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		while($row2 = $result->fetch_assoc()) {
			$campoid = $row2["Campo_ID"]; 
			$descripcion = $row2["Campo_Desc"]; 
			$latitud = $row2["Lat"]; 
			$longitud = $row2["Lon"]; 
			$zoom = $row2["Zoom"]; 
			$google = $row2["Google"]; 
		}		
	}
	
	$htmlFields = "";
	$htmlFields .= '<div class="container-fluid py-2">
					<div class="row">
						<div class="col-xl-12">
							<h3>' . $lang['432'] . '</h3>
						</div>
					</div>
					<div class="row">
						<div class="col-xl-12">
							<Div id="errorColor" style="color: red; text-align: justify;"></Div>
						</div>
					</div>
					<div class="row">
						<div class="col-12 col-sm-12 col-md-12 col-lg-4 col-xl-4">
							<h2>' . $lang['433'] . '</h2>
							<form>
								<div class="row">
									<div class="col-xl-12" hidden>
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['421'] . '</label>
											<input type="text" class="form-control" name="fieldid2" id="fieldid2" value="' . $campoid . '"/>
										</div>
									</div>
									<div class="col-xl-12">
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['422'] . '</label>
											<input type="text" class="form-control" name="descripcion2" id="descripcion2" value="' . $descripcion . '"/>
										</div>
									</div>
									<div class="col-xl-12">
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['426'] . '</label>
											<a id="URLA" class="form-control" href="https://www.google.com/maps/@' . $latitud . ',' . $longitud . ',' . $zoom . 'z/data=!3m1!1e3?language=' . $_COOKIE[$Config->getAlias() . 'language'] . '" target="_blank">Google Maps Link</a>
										</div>
									</div>
								</div>
							</form>
						</div>
						<div class="col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8">
							<h2>' . $lang['429'] . '</h2>
							<form>
								<div class="row">
									<div class="col-xl-12">
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">Map</label>
											<div class="form-control">
												<div id="floating-panel" style="width: 95%; position: absolute;top: 65px;left: 12px;z-index: 5;background-color: transparent;padding: 5px;border: 1px solid #999;text-align: center;font-family: \'Roboto\',\'sans-serif\';line-height: 30px;padding-left: 10px;">
													<input id="address2" type="textbox" value="" style="width: 90%;">
													<div style="float:right;width: 30px;text-align: right;">
														<img id="buscar2" src="imagenes/lupa.png" width="20" height="20" alt=""/>
													</div>
												</div>
												<div id="latlongmap2" style="height:400px;">
												</div>
											</div>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
					
					<input style="width:200px" type="hidden" name="latitud2" id="latitud2"  value="' . $latitud . '">
					<input style="width:200px" type="hidden" name="longitud2" id="longitud2" value="' . $longitud . '">
					<input style="width:200px" type="hidden" name="zoom2" id="zoom2" value="' . $zoom . '">
					<input style="width:200px" type="hidden" name="google2" id="google2" value="' . $google . '">
					<div class="row">
						<div class="my-3" >
							<button type="button" class="btn btn-primary" onClick="validateFieldEdit(' . $field_id . ');" >' . $lang['0000'] . '</button>
							<button type="button" class="btn btn-primary" onClick="fieldManagementHideEdit();" >' . $lang['0001'] . '</button>
						</div>
					</div>
				</div>
				<script>
					var inputs = document.querySelectorAll(\'input\');
					var as = document.querySelectorAll(\'a\');
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
				</script>
				
				
				<script type="text/javascript">
				(function(){
					var tries = 0;
					function initFieldMapEdit(){
					if (typeof google === "undefined" || !google.maps) {
						tries++;
						if (typeof window !== "undefined" && window.__GOOGLE_MAPS_API_KEY && tries < 200) {
							setTimeout(initFieldMapEdit, 100);
						}
						return;
					}
					var map;
					var geocoder;
					var marker;
					var infowindow;
				
					var map1;
					var geocoder1;
					var marker1;
					var infowindow1;
					
					var latlng = new google.maps.LatLng(' . $latitud . ',' . $longitud . ');
					var myOptions = {
						zoom: ' . $zoom . ',
						center: latlng,
						panControl: true,
						scrollwheel: true,
						scaleControl: true,
						overviewMapControl: true,
						overviewMapControlOptions: { opened: true },
						mapTypeId: google.maps.MapTypeId.HYBRID,
						streetViewControl: false
					};
					map = new google.maps.Map(document.getElementById("latlongmap2"),
							myOptions);
					geocoder = new google.maps.Geocoder();
					document.getElementById(\'buscar2\').addEventListener(\'click\', function() {
						geocodeAddress(geocoder, map, \'address2\');
					});
					marker = new google.maps.Marker({
						position: latlng,
						map: map
					});
					infowindow = new google.maps.InfoWindow({
						content: "(1.10, 1.10)"
					});
			
					google.maps.event.addListener(map, \'click\', function(event) {
						marker.setPosition(event.latLng);
			
						var yeri = event.latLng;
			
						var latlongi = "(" + yeri.lat().toFixed(6) + ", " +yeri.lng().toFixed(6) + ")";
			
						infowindow.setContent(latlongi);
			
						document.getElementById(\'latitud2\').value = yeri.lat().toFixed(6);
						document.getElementById(\'longitud2\').value = yeri.lng().toFixed(6);
						document.getElementById(\'zoom2\').value =  map.getZoom();
						document.getElementById("google2").value =  "https://www.google.com/maps/@" + yeri.lat().toFixed(6) + "," + yeri.lng().toFixed(6) + "," + map.getZoom() + "z/data=!3m1!1e3?language=' . $_COOKIE[$Config->getAlias() . 'language'] . '";
						$("#URLA").attr("href", "https://www.google.com/maps/@" + yeri.lat().toFixed(6) + "," + yeri.lng().toFixed(6) + "," + map.getZoom() + "z/data=!3m1!1e3?language=' . $_COOKIE[$Config->getAlias() . 'language'] . '");
					});
					
					
					function geocodeAddress(geocoder, resultsMap, adressid) {
						var address = document.getElementById(adressid).value;
						geocoder.geocode({\'address\': address}, function(results, status) {
							if (status === \'OK\') {
								resultsMap.setCenter(results[0].geometry.location);
							} else {
								//alert(\'Geocode was not successful for the following reason: \' + status);
							}
						});
					}
					}
					initFieldMapEdit();
				})();
				</script>';
					
	$retunData = array('status' => '1', 'message' => 'Success.', 'dataFieldEdit' => $htmlFields);
    $Config->Close();
    echo json_encode($retunData);
?>