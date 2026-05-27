<?php
	$fecha = new DateTime();
	$sport = (int)$Config->getSport();
	$ts = (int)$fecha->getTimestamp();

	$targetsFn = require __DIR__ . '/configManagementImagesTargets.php';
	$pack = $targetsFn();
	$rows = $pack['rows'];
	$siteRoot = rtrim((string) $Config->getPath(), '/\\');
	$configImgPreviewRel = function ($rel) use ($siteRoot) {
		$rel = ltrim(str_replace('\\', '/', (string) $rel), '/');
		if ($siteRoot === '' || is_readable($siteRoot . '/' . $rel)) {
			return $rel;
		}
		if (preg_match('/\.png$/i', $rel)) {
			$base = preg_replace('/\.png$/i', '', $rel);
			foreach (array('jpeg', 'jpg', 'JPEG', 'JPG') as $ext) {
				$try = $base . '.' . $ext;
				if (is_readable($siteRoot . '/' . $try)) {
					return $try;
				}
			}
		}
		return $rel;
	};

	$htmlConfig .= '<div class="container-fluid py-2" id="configImagesRoot" data-sport="' . $sport . '">
						<div class="row g-3">';

	foreach ($rows as $r) {
		$post = $r['post'];
		$rel = $r['rel'];
		$accept = htmlspecialchars($r['accept'], ENT_QUOTES, 'UTF-8');
		$langKey = isset($r['lang']) ? $r['lang'] : '';
		$label = ($langKey !== '' && isset($lang[$langKey]))
			? htmlspecialchars($lang[$langKey], ENT_QUOTES, 'UTF-8')
			: htmlspecialchars($rel, ENT_QUOTES, 'UTF-8');
		$previewRel = $configImgPreviewRel($rel);
		$previewUrl = htmlspecialchars($previewRel, ENT_QUOTES, 'UTF-8') . '?tmp=' . $ts;
		$htmlConfig .= '
			<div class="col-12 col-md-6 col-xl-4">
				<div class="card h-100">
					<div class="card-body py-2">
						<label class="form-label text-sm mb-1">' . $label . '</label>
						<div class="d-flex align-items-start gap-2">
							<div style="min-width: 96px; min-height: 72px; background-color: #e9ecef; display:flex; align-items:center; justify-content:center; overflow:hidden;">
								<img class="cfg-img-preview" data-post="' . htmlspecialchars($post, ENT_QUOTES, 'UTF-8') . '" src="' . $previewUrl . '" alt="" style="max-width:120px; max-height:90px; object-fit:contain;" onerror="this.style.display=\'none\'"/>
							</div>
							<div class="flex-grow-1">
								<input type="file" class="form-control form-control-sm cfg-img-file" name="' . htmlspecialchars($post, ENT_QUOTES, 'UTF-8') . '" id="' . htmlspecialchars($post, ENT_QUOTES, 'UTF-8') . '" accept="' . $accept . '"/>
							</div>
						</div>
					</div>
				</div>
			</div>';
	}

	$msgSave = htmlspecialchars($lang['0000'], ENT_QUOTES, 'UTF-8');
	$msgOk = htmlspecialchars($lang['441'], ENT_QUOTES, 'UTF-8');
	$msgErr = htmlspecialchars($lang['452-9'], ENT_QUOTES, 'UTF-8');
	$msgAjaxGenericJs = json_encode($lang['js0002'] ?? '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

	$htmlConfig .= '
						</div>
						<div class="row mt-3">
							<div class="col-12">
								<button type="button" class="btn btn-primary" id="btnSaveConfigImages">' . $msgSave . '</button>
							</div>
						</div>
					</div>
					<script>
					(function () {
						var MSG_AJAX_GENERIC = ';
	$htmlConfig .= $msgAjaxGenericJs;
	$htmlConfig .= ';
						var saveUrl = "./ajax/Admin/Config/configManagementImagesSave.php";
						function readURLConfigImage(input) {
							if (!input || !input.files || !input.files[0]) { return; }
							var post = input.name;
							var img = document.querySelector("img.cfg-img-preview[data-post=\"" + post + "\"]");
							if (!img) { return; }
							var reader = new FileReader();
							reader.onload = function (e) { img.src = e.target.result; img.style.display = ""; };
							reader.readAsDataURL(input.files[0]);
						}
						document.querySelectorAll("#configimages .cfg-img-file").forEach(function (inp) {
							inp.addEventListener("change", function () { readURLConfigImage(inp); });
						});
						function validateConfigImages() {
							var fd = new FormData();
							var any = false;
							document.querySelectorAll("#configimages .cfg-img-file").forEach(function (inp) {
								if (inp.files && inp.files.length > 0) {
									fd.append(inp.name, inp.files[0]);
									any = true;
								}
							});
							if (!any) {
								if (typeof Swal !== "undefined") { Swal.fire({ icon: "info", title: "' . $msgErr . '" }); }
								else { alert("' . $msgErr . '"); }
								return;
							}
							if (typeof $ !== "undefined") {
								$.ajax({
									url: saveUrl,
									type: "POST",
									data: fd,
									processData: false,
									contentType: false,
									dataType: "json",
									success: function (j) {
										if (j && j.status === "1") {
											if (typeof Swal !== "undefined") { Swal.fire({ icon: "success", title: j.dataConfigAnswer || "' . $msgOk . '" }); }
											else { alert(j.dataConfigAnswer || "' . $msgOk . '"); }
											var t = Math.floor(Date.now() / 1000);
											document.querySelectorAll("#configimages .cfg-img-preview").forEach(function (im) {
												var post = im.getAttribute("data-post");
												var inp = post ? document.getElementById(post) : null;
												var u = im.getAttribute("src");
												if (inp && inp.files && inp.files.length > 0) {
													return;
												}
												if (u && u.indexOf("?") > -1) { im.src = u.split("?")[0] + "?tmp=" + t; }
												else if (u) { im.src = u + (u.indexOf("?") > -1 ? "" : ("?tmp=" + t)); }
												im.style.display = "";
											});
											document.querySelectorAll("#configimages .cfg-img-file").forEach(function (inp) { inp.value = ""; });
										} else {
											var err = (j && (j.dataConfigAnswer || j.message)) ? (j.dataConfigAnswer || j.message) : "Error";
											if (typeof Swal !== "undefined") { Swal.fire({ icon: "error", title: err }); }
											else { alert(err); }
										}
									},
									error: function (jqxhr, status, exception) {
										if (typeof mainLoadingOff === "function") { mainLoadingOff(); }
										if (typeof Swal !== "undefined") { Swal.fire({ icon: "error", title: MSG_AJAX_GENERIC }); }
										else { alert(MSG_AJAX_GENERIC); }
									}
								});
							}
						}
						var b = document.getElementById("btnSaveConfigImages");
						if (b) { b.addEventListener("click", validateConfigImages); }
					})();
					</script>';
?>
