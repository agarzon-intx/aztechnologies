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
	
	$sessionstat = $fgmembersite->CheckLogin('FieldsManagementCreate.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');

	$htmlFields = "";
	$htmlFields .= '<div class="container-fluid py-2">
					<div class="row">
						<div class="col-xl-12">
							<h3>' . $lang['431'] . '</h3>
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
											<input type="text" class="form-control" name="fieldid" id="fieldid" value=""/>
										</div>
									</div>
									<div class="col-xl-12">
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['422'] . '</label>
											<input type="text" class="form-control" name="descripcion" id="descripcion" value=""/>
										</div>
									</div>
									<div class="col-xl-12">
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['426'] . '</label>
											<a id="URLA" class="form-control" href="" target="_blank"></a>
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
													<input id="address" type="textbox" value="" style="width: 90%;">
													<div style="float:right;width: 30px;text-align: right;">
														<img id="buscar" src="imagenes/lupa.png" width="20" height="20" alt=""/>
													</div>
												</div>
												<div id="latlongmap" style="height:400px;">
												</div>
											</div>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
					
					<input style="width:200px" type="hidden" name="latitud" id="latitud"  value="">
					<input style="width:200px" type="hidden" name="longitud" id="longitud" value="">
					<input style="width:200px" type="hidden" name="zoom" id="zoom" value="">
					<input style="width:200px" type="hidden" name="google" id="google" value="">
					<div class="row">
						<div class="my-3" >
							<button type="button" class="btn btn-primary" onClick="validateFieldAdd();" >' . $lang['0000'] . '</button>
							<button type="button" class="btn btn-primary" onClick="fieldManagementHideAdd();" >' . $lang['0001'] . '</button>
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
					function setMapMarkerPosition(marker, latLng) {
						if (!marker || !latLng) return;
						if (typeof marker.setPosition === "function") {
							marker.setPosition(latLng);
						} else {
							marker.position = latLng;
						}
					}
					function createMapMarker(map, position) {
						if (google.maps.marker && google.maps.marker.AdvancedMarkerElement) {
							return new google.maps.marker.AdvancedMarkerElement({
								position: position,
								map: map
							});
						}
						return new google.maps.Marker({
							position: position,
							map: map
						});
					}
					function initFieldMapCreate(){
					if (typeof google === "undefined" || !google.maps || !document.getElementById("latlongmap")) {
						tries++;
						if (tries < 200) {
							setTimeout(initFieldMapCreate, 100);
						}
						return;
					}
					// Advanced markers need libraries=marker; wait briefly, then fall back to classic Marker.
					if (!(google.maps.marker && google.maps.marker.AdvancedMarkerElement) && tries < 40) {
						tries++;
						setTimeout(initFieldMapCreate, 100);
						return;
					}
					
					var map1;
					var geocoder1;
					var marker1;
					var infowindow1;
					
					var latlng1 = new google.maps.LatLng(' . $Config->Latitude . '.toFixed(6),' . $Config->Longitude . '.toFixed(6));
					var latlng2 = new google.maps.LatLng(' . $Config->Latitude . '+(0.021172),' . $Config->Longitude . '+(-0.025589));
					
					var myOptions1 = {
						zoom: 12,
						center: latlng2,
						panControl: true,
						scrollwheel: true,
						gestureHandling: "greedy",
						scaleControl: true,
						overviewMapControl: true,
						overviewMapControlOptions: { opened: true },
						mapTypeId: google.maps.MapTypeId.HYBRID,
						streetViewControl: false,
						mapId: ' . json_encode($googleMapsMapId ?? 'DEMO_MAP_ID', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . '
					};
					map1 = new google.maps.Map(document.getElementById("latlongmap"),
							myOptions1);
					geocoder1 = new google.maps.Geocoder();
					document.getElementById(\'buscar\').addEventListener(\'click\', function() {
						geocodeAddress(geocoder1, map1, \'address\');
					});
					try {
						marker1 = createMapMarker(map1, latlng1);
					} catch (e) {
						marker1 = new google.maps.Marker({ position: latlng1, map: map1 });
					}
					infowindow1 = new google.maps.InfoWindow({
						content: "(1.10, 1.10)"
					});
			
					google.maps.event.addListener(map1, \'click\', function(event) {
						setMapMarkerPosition(marker1, event.latLng);
			
						var yeri1 = event.latLng;
			
						var latlongi1 = "(" + yeri1.lat().toFixed(6) + ", " +yeri1.lng().toFixed(6) + ")";
			
						infowindow1.setContent(latlongi1);
			
						document.getElementById(\'latitud\').value = yeri1.lat().toFixed(6);
						document.getElementById(\'longitud\').value = yeri1.lng().toFixed(6);
						document.getElementById(\'zoom\').value =  map1.getZoom();
						document.getElementById("google").value =  	"https://www.google.com/maps/@" + yeri1.lat().toFixed(6) + "," + yeri1.lng().toFixed(6) + "," + map1.getZoom() + "z/data=!3m1!1e3?language=' . $_COOKIE[$Config->getAlias() . 'language'] . '";
						$("#URLA").attr("href", "https://www.google.com/maps/@" + yeri1.lat().toFixed(6) + "," + yeri1.lng().toFixed(6) + "," + map1.getZoom() + "z/data=!3m1!1e3?language=' . $_COOKIE[$Config->getAlias() . 'language'] . '");
						$("#URLA").html("Google Maps Link");
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
					initFieldMapCreate();
				})();
				</script>';

	$retunData = array('status' => '1', 'message' => 'Success.', 'fieldAdd' => $htmlFields);
    echo json_encode($retunData);
?>