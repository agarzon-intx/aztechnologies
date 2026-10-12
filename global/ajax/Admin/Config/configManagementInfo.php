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
	$locHasValue = is_numeric($latitude ?? null) && is_numeric($longitude ?? null) && ((float) $latitude != 0 || (float) $longitude != 0);
	$locLat = $locHasValue ? round((float) $latitude, 6) : 19.281454;
	$locLon = $locHasValue ? round((float) $longitude, 6) : -99.656296;
	$locLatValue = $locHasValue ? number_format($locLat, 6, '.', '') : '';
	$locLonValue = $locHasValue ? number_format($locLon, 6, '.', '') : '';
	$locLang = isset($_COOKIE[$Config->getAlias() . 'language']) ? preg_replace('/[^a-z]/', '', strtolower($_COOKIE[$Config->getAlias() . 'language'])) : 'es';
	
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
									</div>
								</form>
							</div>
						</div>
						<div class="row mb-4">
							<div class="col-12 col-lg-10 col-xxl-8">
								<label class="ms-0" style="font-size: 0.875rem;">' . $lang['452-100'] . '</label>
								<div class="d-flex flex-wrap align-items-center mb-2" style="gap: 8px;">
									<input type="text" class="form-control border px-2" id="cfgLocSearch" placeholder="' . htmlspecialchars($lang['452-101'], ENT_QUOTES) . '" style="flex: 1 1 240px; height: 38px;">
									<button type="button" class="btn btn-secondary mb-0" id="cfgLocSearchBtn" title="' . htmlspecialchars($lang['452-101'], ENT_QUOTES) . '"><img src="imagenes/lupa.png" width="18" height="18" alt=""></button>
									<button type="button" class="btn btn-outline-secondary mb-0" id="cfgLocMine">' . $lang['452-103'] . '</button>
								</div>
								<div id="cfgLocMap" style="height: 360px; border-radius: 8px; border: 1px solid #d2d6da;"></div>
								<div class="d-flex flex-wrap justify-content-between mt-2" style="gap: 8px; font-size: 0.8rem;">
									<span class="text-muted">' . $lang['452-102'] . '</span>
									<span>' . $lang['477'] . '/' . $lang['478'] . ': <a id="cfgLocText" href="#" target="_blank" rel="noopener"></a></span>
								</div>
								<input type="hidden" name="latitude" id="latitude" value="' . $locLatValue . '">
								<input type="hidden" name="longitude" id="longitude" value="' . $locLonValue . '">
							</div>
						</div>
						<script>
						(function () {
							var tries = 0;
							var start = { lat: ' . json_encode($locLat) . ', lng: ' . json_encode($locLon) . ' };
							var hasValue = ' . ($locHasValue ? 'true' : 'false') . ';
							var mapId = ' . json_encode($googleMapsMapId ?? 'DEMO_MAP_ID', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';
							var mapsLang = ' . json_encode($locLang) . ';
							function showCoords(lat, lng) {
								var a = document.getElementById("cfgLocText");
								if (!a) return;
								if (lat === null) { a.textContent = "-"; a.removeAttribute("href"); return; }
								a.textContent = lat.toFixed(6) + ", " + lng.toFixed(6);
								a.href = "https://www.google.com/maps/search/?api=1&query=" + lat.toFixed(6) + "," + lng.toFixed(6) + "&hl=" + mapsLang;
							}
							function initCfgLocMap() {
								var el = document.getElementById("cfgLocMap");
								if (!el) return;
								if (typeof google === "undefined" || !google.maps || !google.maps.Map) {
									if (++tries < 200) setTimeout(initCfgLocMap, 100);
									return;
								}
								if (!(google.maps.marker && google.maps.marker.AdvancedMarkerElement) && ++tries < 40) {
									setTimeout(initCfgLocMap, 100);
									return;
								}
								var map = new google.maps.Map(el, {
									zoom: hasValue ? 15 : 11,
									center: start,
									mapTypeId: google.maps.MapTypeId.HYBRID,
									gestureHandling: "greedy",
									streetViewControl: false,
									mapId: mapId
								});
								var advanced = !!(google.maps.marker && google.maps.marker.AdvancedMarkerElement);
								var marker = advanced
									? new google.maps.marker.AdvancedMarkerElement({ map: map, position: start, gmpDraggable: true })
									: new google.maps.Marker({ map: map, position: start, draggable: true });
								function setPoint(pos, pan) {
									var lat = typeof pos.lat === "function" ? pos.lat() : pos.lat;
									var lng = typeof pos.lng === "function" ? pos.lng() : pos.lng;
									if (advanced) { marker.position = { lat: lat, lng: lng }; } else { marker.setPosition({ lat: lat, lng: lng }); }
									document.getElementById("latitude").value = lat.toFixed(6);
									document.getElementById("longitude").value = lng.toFixed(6);
									showCoords(lat, lng);
									if (pan) map.panTo({ lat: lat, lng: lng });
								}
								map.addListener("click", function (e) { if (e.latLng) setPoint(e.latLng, false); });
								marker.addListener("dragend", function (e) { setPoint(e && e.latLng ? e.latLng : marker.position, false); });

								var geocoder = new google.maps.Geocoder();
								var search = document.getElementById("cfgLocSearch");
								function doSearch() {
									var q = search.value.trim();
									if (q === "") return;
									geocoder.geocode({ address: q }, function (results, status) {
										if (status !== "OK" || !results || !results[0]) return;
										var g = results[0].geometry;
										setPoint(g.location, true);
										if (g.viewport) { map.fitBounds(g.viewport); } else { map.setZoom(16); }
									});
								}
								document.getElementById("cfgLocSearchBtn").addEventListener("click", doSearch);
								search.addEventListener("keydown", function (e) {
									if (e.key === "Enter") { e.preventDefault(); doSearch(); }
								});
								document.getElementById("cfgLocMine").addEventListener("click", function () {
									if (!navigator.geolocation) return;
									navigator.geolocation.getCurrentPosition(function (p) {
										setPoint({ lat: p.coords.latitude, lng: p.coords.longitude }, true);
										map.setZoom(16);
									});
								});
							}
							showCoords(hasValue ? start.lat : null, hasValue ? start.lng : null);
							initCfgLocMap();
						})();
						</script>
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
					</div>';
?>