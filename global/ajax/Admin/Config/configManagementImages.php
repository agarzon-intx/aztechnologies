<?php
	$fecha = new DateTime();
	$sport = (int)$Config->getSport();
	$ts = (int)$fecha->getTimestamp();

	$targetsFn = require __DIR__ . '/configManagementImagesTargets.php';
	$pack = $targetsFn();
	$rows = $pack['rows'];
	$siteRoot = rtrim((string) $Config->getPath(), '/\\');
	require_once dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'flyer_text_style.inc.php';
	$flyerStyle = az_flyer_text_style($Config);
	// Keep Configuration.credencialFrontImage / credencialBackImage in sync with disk.
	$Config->syncCredencialImageFlags();
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
	$configImgExists = function ($rel) use ($siteRoot, $configImgPreviewRel) {
		if ($siteRoot === '') {
			return false;
		}
		$previewRel = $configImgPreviewRel($rel);
		return is_readable($siteRoot . '/' . ltrim(str_replace('\\', '/', (string) $previewRel), '/'));
	};

	$htmlConfig .= '<div class="container-fluid py-2" id="configImagesRoot" data-sport="' . $sport . '">
						<div class="row g-3">';

	$noImageLabel = htmlspecialchars(isset($lang['452-18']) ? $lang['452-18'] : 'No image', ENT_QUOTES, 'UTF-8');

	foreach ($rows as $r) {
		$post = $r['post'];
		$rel = $r['rel'];
		$accept = htmlspecialchars($r['accept'], ENT_QUOTES, 'UTF-8');
		$langKey = isset($r['lang']) ? $r['lang'] : '';
		$label = ($langKey !== '' && isset($lang[$langKey]))
			? htmlspecialchars($lang[$langKey], ENT_QUOTES, 'UTF-8')
			: htmlspecialchars($rel, ENT_QUOTES, 'UTF-8');
		$clearable = !empty($r['clearable']);
		$exists = $configImgExists($rel);
		$previewRel = $configImgPreviewRel($rel);
		$previewUrl = htmlspecialchars($previewRel, ENT_QUOTES, 'UTF-8') . '?tmp=' . $ts;
		$imgStyle = $exists ? '' : 'display: none;';
		$clearChecked = ($clearable && !$exists) ? ' checked' : '';
		$fileDisabled = ($clearable && !$exists) ? ' disabled' : '';
		$postEsc = htmlspecialchars($post, ENT_QUOTES, 'UTF-8');

		$htmlConfig .= '
			<div class="col-12 col-md-6 col-xl-4">
				<div class="card h-100">
					<div class="card-body py-2">
						<label class="form-label text-sm mb-1">' . $label . '</label>
						<div class="d-flex align-items-start gap-2">
							<div style="min-width: 96px; min-height: 72px; background-color: #e9ecef; display:flex; align-items:center; justify-content:center; overflow:hidden;">
								<img class="cfg-img-preview" data-post="' . $postEsc . '" src="' . ($exists ? $previewUrl : '') . '" alt="" style="max-width:120px; max-height:90px; object-fit:contain; ' . $imgStyle . '" onerror="this.style.display=\'none\'"/>
							</div>
							<div class="flex-grow-1">
								<input type="file" class="form-control form-control-sm cfg-img-file" name="' . $postEsc . '" id="' . $postEsc . '" accept="' . $accept . '"' . $fileDisabled . '/>';
		if ($clearable) {
			$htmlConfig .= '
								<div class="form-check mt-2 mb-0">
									<input class="form-check-input cfg-img-clear" type="checkbox" name="' . $postEsc . '_clear" id="' . $postEsc . '_clear" data-post="' . $postEsc . '"' . $clearChecked . '>
									<label class="form-check-label" for="' . $postEsc . '_clear">' . $noImageLabel . '</label>
								</div>';
		}
		$htmlConfig .= '
							</div>
						</div>
					</div>
				</div>
			</div>';
	}

	$msgSave = htmlspecialchars($lang['0000'], ENT_QUOTES, 'UTF-8');
	$msgSaving = htmlspecialchars(isset($lang['452-27']) ? $lang['452-27'] : 'Saving...', ENT_QUOTES, 'UTF-8');
	$msgOk = htmlspecialchars($lang['441'], ENT_QUOTES, 'UTF-8');
	$msgErr = htmlspecialchars($lang['452-9'], ENT_QUOTES, 'UTF-8');
	$msgAjaxGenericJs = json_encode($lang['js0002'] ?? '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
	$flyerTitle = htmlspecialchars(isset($lang['452-19']) ? $lang['452-19'] : 'Flyer text', ENT_QUOTES, 'UTF-8');
	$lblColor1 = htmlspecialchars(isset($lang['452-20']) ? $lang['452-20'] : 'First color', ENT_QUOTES, 'UTF-8');
	$lblColor2 = htmlspecialchars(isset($lang['452-21']) ? $lang['452-21'] : 'Second color', ENT_QUOTES, 'UTF-8');
	$lblWeek = htmlspecialchars(isset($lang['452-22']) ? $lang['452-22'] : 'Week font size', ENT_QUOTES, 'UTF-8');
	$lblCat = htmlspecialchars(isset($lang['452-23']) ? $lang['452-23'] : 'Category font size', ENT_QUOTES, 'UTF-8');
	$lblDate = htmlspecialchars(isset($lang['452-24']) ? $lang['452-24'] : 'Date font size', ENT_QUOTES, 'UTF-8');
	$lblHour = htmlspecialchars(isset($lang['452-25']) ? $lang['452-25'] : 'Hour font size', ENT_QUOTES, 'UTF-8');
	$lblField = htmlspecialchars(isset($lang['452-26']) ? $lang['452-26'] : 'Field font size', ENT_QUOTES, 'UTF-8');
	$c1 = htmlspecialchars($flyerStyle['color1Hex'], ENT_QUOTES, 'UTF-8');
	$c2 = htmlspecialchars($flyerStyle['color2Hex'], ENT_QUOTES, 'UTF-8');
	$lblFontFamily = htmlspecialchars(isset($lang['452-30']) ? $lang['452-30'] : 'Font', ENT_QUOTES, 'UTF-8');
	$lblFontStyle = htmlspecialchars(isset($lang['452-31']) ? $lang['452-31'] : 'Type', ENT_QUOTES, 'UTF-8');
	$styleLabels = array(
		'' => isset($lang['452-32']) ? $lang['452-32'] : 'Regular',
		'B' => isset($lang['452-33']) ? $lang['452-33'] : 'Bold',
		'I' => isset($lang['452-34']) ? $lang['452-34'] : 'Italic',
		'BI' => isset($lang['452-35']) ? $lang['452-35'] : 'Bold italic',
	);
	$fontDir = $siteRoot !== '' ? $siteRoot . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . 'font' : '';
	$pdfFonts = az_flyer_available_fonts($fontDir);
	$fontCurrent = az_flyer_font_choice($Config, $fontDir);
	$fontFamilyOptions = '';
	$fontStyleOptions = '';
	$fontStylesJs = array();
	foreach ($pdfFonts as $family => $info) {
		$famEsc = htmlspecialchars($family, ENT_QUOTES, 'UTF-8');
		$inUse = strcasecmp($family, $fontCurrent['family']) === 0;
		if ($info['symbolic'] || empty($info['styles'])) {
			continue;
		}
		$fontFamilyOptions .= '<option value="' . $famEsc . '"' . ($inUse ? ' selected' : '') . '>' . $famEsc . '</option>';
		$fontStylesJs[$family] = array();
		foreach ($info['styles'] as $code => $info2) {
			$fontStylesJs[$family][] = array($code, $styleLabels[$code]);
			if ($inUse) {
				$fontStyleOptions .= '<option value="' . $code . '"' . ($code === $fontCurrent['style'] ? ' selected' : '') . '>' . htmlspecialchars($styleLabels[$code], ENT_QUOTES, 'UTF-8') . '</option>';
			}
		}
	}
	$fontStylesJson = json_encode($fontStylesJs, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

	$fontWeb = az_flyer_font_web_assets($fontDir, array_keys($fontStylesJs));
	$fontFaceCss = $fontWeb['faces'];
	$fontCssMap = $fontWeb['css'];
	$fontCssJson = json_encode($fontCssMap, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
	$lblFontUpload = htmlspecialchars(isset($lang['452-37']) ? $lang['452-37'] : 'Add font (.ttf)', ENT_QUOTES, 'UTF-8');
	$lblFontUploadBtn = htmlspecialchars(isset($lang['452-38']) ? $lang['452-38'] : 'Add', ENT_QUOTES, 'UTF-8');
	$msgFontPickJs = json_encode(isset($lang['452-40']) ? $lang['452-40'] : 'Choose a font file (.ttf).', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
	$lblFontPreview = htmlspecialchars(isset($lang['452-36']) ? $lang['452-36'] : 'Preview', ENT_QUOTES, 'UTF-8');
	$previewBg = $configImgExists('pdf/FondoFlyer.png')
		? 'background-image: url(' . htmlspecialchars($configImgPreviewRel('pdf/FondoFlyer.png'), ENT_QUOTES, 'UTF-8') . '?tmp=' . $ts . '); background-size: cover; background-position: center;'
		: '';
	// Positions in mm on the 210 x 210 single-game flyer (pdf/flyer_ci_page.php).
	$previewItems = array(
		array('flyerFontWeek', 0, 38, 210, 25, 'center', 'Jornada 12'),
		array('flyerFontCategory', 0, 60, 210, 18, 'center', 'Categoria: Libre Varonil'),
		array('flyerFontDate', 45, 162, 90, 8, 'left', '13 de octubre'),
		array('flyerFontHour', 140, 162, 70, 8, 'left', '16:00 HRS'),
		array('flyerFontField', 75, 175, 135, 8, 'left', 'Campo Metepec'),
	);
	$previewHtml = '';
	foreach (array(array('pdf/calendar.png', 35, 160), array('pdf/clock.png', 130, 160), array('pdf/pointer.png', 65, 174)) as $icon) {
		if ($configImgExists($icon[0])) {
			$previewHtml .= '<img src="' . htmlspecialchars($configImgPreviewRel($icon[0]), ENT_QUOTES, 'UTF-8') . '?tmp=' . $ts . '" alt="" style="position:absolute; left:' . round($icon[1] / 2.1, 4) . '%; top:' . round($icon[2] / 2.1, 4) . '%; width:' . round(10 / 2.1, 4) . '%; height:' . round(10 / 2.1, 4) . '%;">';
		}
	}
	foreach ($previewItems as $it) {
		$previewHtml .= '<div class="flyer-font-preview-text" data-size-field="' . $it[0] . '" style="position:absolute; left:' . round($it[1] / 2.1, 4) . '%; top:' . round($it[2] / 2.1, 4) . '%; width:' . round($it[3] / 2.1, 4) . '%; height:' . round($it[4] / 2.1, 4) . '%; display:flex; align-items:center; justify-content:' . ($it[5] === 'center' ? 'center' : 'flex-start') . '; white-space:nowrap; line-height:1;">' . htmlspecialchars($it[6], ENT_QUOTES, 'UTF-8') . '</div>';
	}
	$fontsColumnsReady = $Config->configurationHasColumn('flyerFontFamily') && $Config->configurationHasColumn('flyerFontStyle');

	$htmlConfig .= '
						</div>
						<div class="row mt-4">
							<div class="col-12">
								<div class="card">
									<div class="card-body">
										<h6 class="mb-3">' . $flyerTitle . '</h6>
										<div class="row g-3">
											<div class="col-12 col-md-6 col-xl-4">
												<label class="form-label" for="flyerTextColor1">' . $lblColor1 . '</label>
												<input type="color" class="form-control form-control-color cfg-flyer-field" name="flyerTextColor1" id="flyerTextColor1" value="' . $c1 . '" title="' . $lblColor1 . '" style="width: 100%; max-width: 120px; height: 42px; padding: 2px; cursor: pointer;">
											</div>
											<div class="col-12 col-md-6 col-xl-4">
												<label class="form-label" for="flyerTextColor2">' . $lblColor2 . '</label>
												<input type="color" class="form-control form-control-color cfg-flyer-field" name="flyerTextColor2" id="flyerTextColor2" value="' . $c2 . '" title="' . $lblColor2 . '" style="width: 100%; max-width: 120px; height: 42px; padding: 2px; cursor: pointer;">
											</div>
											<div class="col-12 col-md-6 col-xl-4">
												<label class="form-label" for="flyerFontWeek">' . $lblWeek . '</label>
												<input type="number" class="form-control cfg-flyer-field" name="flyerFontWeek" id="flyerFontWeek" min="8" max="150" step="1" value="' . (int) $flyerStyle['week'] . '">
											</div>
											<div class="col-12 col-md-6 col-xl-4">
												<label class="form-label" for="flyerFontCategory">' . $lblCat . '</label>
												<input type="number" class="form-control cfg-flyer-field" name="flyerFontCategory" id="flyerFontCategory" min="8" max="150" step="1" value="' . (int) $flyerStyle['category'] . '">
											</div>
											<div class="col-12 col-md-6 col-xl-4">
												<label class="form-label" for="flyerFontDate">' . $lblDate . '</label>
												<input type="number" class="form-control cfg-flyer-field" name="flyerFontDate" id="flyerFontDate" min="8" max="150" step="1" value="' . (int) $flyerStyle['date'] . '">
											</div>
											<div class="col-12 col-md-6 col-xl-4">
												<label class="form-label" for="flyerFontHour">' . $lblHour . '</label>
												<input type="number" class="form-control cfg-flyer-field" name="flyerFontHour" id="flyerFontHour" min="8" max="150" step="1" value="' . (int) $flyerStyle['hour'] . '">
											</div>
											<div class="col-12 col-md-6 col-xl-4">
												<label class="form-label" for="flyerFontField">' . $lblField . '</label>
												<input type="number" class="form-control cfg-flyer-field" name="flyerFontField" id="flyerFontField" min="8" max="150" step="1" value="' . (int) $flyerStyle['field'] . '">
											</div>
										</div>' . (($fontFamilyOptions !== '' && $fontsColumnsReady) ? '
										<div class="row g-3 mt-1">
											<div class="col-12 col-md-6 col-xl-4">
												<label class="form-label" for="flyerFontFamily">' . $lblFontFamily . '</label>
												<select class="form-select cfg-flyer-field" name="flyerFontFamily" id="flyerFontFamily">' . $fontFamilyOptions . '</select>
											</div>
											<div class="col-12 col-md-6 col-xl-4">
												<label class="form-label" for="flyerFontStyle">' . $lblFontStyle . '</label>
												<select class="form-select cfg-flyer-field" name="flyerFontStyle" id="flyerFontStyle">' . $fontStyleOptions . '</select>
											</div>
											<div class="col-12 col-xl-4">
												<label class="form-label" for="flyerFontUpload">' . $lblFontUpload . '</label>
												<div class="input-group">
													<input type="file" class="form-control" id="flyerFontUpload" accept=".ttf,.otf" multiple>
													<button type="button" class="btn btn-outline-primary mb-0" id="btnFlyerFontUpload">' . $lblFontUploadBtn . '</button>
												</div>
											</div>
											<div class="col-12">
												<label class="form-label">' . $lblFontPreview . '</label>
												<style>' . $fontFaceCss . '</style>
												<div id="flyerFontPreview" style="position:relative; width:100%; max-width:420px; aspect-ratio:1 / 1; overflow:hidden; border-radius:4px; background-color:#5a5a5a; ' . $previewBg . '">' . $previewHtml . '</div>
											</div>
										</div>' : '') . '
									</div>
								</div>
							</div>
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
						var saving = false;
						function setConfigImagesSaving(on) {
							saving = on;
							var btn = document.getElementById("btnSaveConfigImages");
							if (btn) {
								btn.disabled = on;
								btn.innerHTML = on ? "<span class=\"spinner-border spinner-border-sm me-2\" role=\"status\" aria-hidden=\"true\"></span>' . $msgSaving . '" : "' . $msgSave . '";
							}
							if (on) {
								if (typeof mainLoadingOn === "function") { mainLoadingOn(); }
							} else if (typeof mainLoadingOff === "function") {
								mainLoadingOff();
							}
						}
						function readURLConfigImage(input) {
							if (!input || !input.files || !input.files[0]) { return; }
							var post = input.name;
							var img = document.querySelector("img.cfg-img-preview[data-post=\"" + post + "\"]");
							var clear = document.getElementById(post + "_clear");
							if (clear) { clear.checked = false; }
							if (!img) { return; }
							var reader = new FileReader();
							reader.onload = function (e) { img.src = e.target.result; img.style.display = ""; };
							reader.readAsDataURL(input.files[0]);
						}
						document.querySelectorAll("#configimages .cfg-img-file").forEach(function (inp) {
							inp.addEventListener("change", function () { readURLConfigImage(inp); });
						});
						document.querySelectorAll("#configimages .cfg-img-clear").forEach(function (cb) {
							cb.addEventListener("change", function () {
								var post = cb.getAttribute("data-post");
								var inp = post ? document.getElementById(post) : null;
								var img = post ? document.querySelector("img.cfg-img-preview[data-post=\"" + post + "\"]") : null;
								if (cb.checked) {
									if (inp) { inp.value = ""; inp.disabled = true; }
									if (img) { img.removeAttribute("src"); img.style.display = "none"; }
								} else {
									if (inp) { inp.disabled = false; }
								}
							});
						});
						function validateConfigImages() {
							if (saving) { return; }
							var fd = new FormData();
							var any = false;
							document.querySelectorAll("#configimages .cfg-img-file").forEach(function (inp) {
								if (inp.disabled) { return; }
								if (inp.files && inp.files.length > 0) {
									fd.append(inp.name, inp.files[0]);
									any = true;
								}
							});
							document.querySelectorAll("#configimages .cfg-img-clear").forEach(function (cb) {
								if (cb.checked) {
									fd.append(cb.name, "1");
									any = true;
								}
							});
							document.querySelectorAll("#configimages .cfg-flyer-field").forEach(function (inp) {
								fd.append(inp.name, inp.value || "");
								any = true;
							});
							if (!any) {
								if (typeof Swal !== "undefined") { Swal.fire({ icon: "info", title: "' . $msgErr . '" }); }
								else { alert("' . $msgErr . '"); }
								return;
							}
							if (typeof $ !== "undefined") {
								setConfigImagesSaving(true);
								$.ajax({
									url: saveUrl,
									type: "POST",
									data: fd,
									processData: false,
									contentType: false,
									dataType: "json",
									success: function (j) {
										setConfigImagesSaving(false);
										if (j && j.status === "1") {
											if (typeof Swal !== "undefined") { Swal.fire({ icon: "success", title: j.dataConfigAnswer || "' . $msgOk . '" }); }
											else { alert(j.dataConfigAnswer || "' . $msgOk . '"); }
											var t = Math.floor(Date.now() / 1000);
											document.querySelectorAll("#configimages .cfg-img-preview").forEach(function (im) {
												var post = im.getAttribute("data-post");
												var clear = post ? document.getElementById(post + "_clear") : null;
												var inp = post ? document.getElementById(post) : null;
												if (clear && clear.checked) {
													im.removeAttribute("src");
													im.style.display = "none";
													return;
												}
												if (inp && inp.files && inp.files.length > 0) {
													return;
												}
												var u = im.getAttribute("src");
												if (u && u.indexOf("?") > -1) { im.src = u.split("?")[0] + "?tmp=" + t; }
												else if (u) { im.src = u + "?tmp=" + t; }
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
										setConfigImagesSaving(false);
										if (typeof Swal !== "undefined") { Swal.fire({ icon: "error", title: MSG_AJAX_GENERIC }); }
										else { alert(MSG_AJAX_GENERIC); }
									}
								});
							}
						}
						var FLYER_FONT_STYLES = ' . $fontStylesJson . ';
						var famSel = document.getElementById("flyerFontFamily");
						var stySel = document.getElementById("flyerFontStyle");
						if (famSel && stySel) {
							famSel.addEventListener("change", function () {
								var prev = stySel.value;
								var opts = FLYER_FONT_STYLES[famSel.value] || [];
								stySel.innerHTML = "";
								opts.forEach(function (o) {
									var op = document.createElement("option");
									op.value = o[0];
									op.textContent = o[1];
									stySel.appendChild(op);
								});
								var keep = opts.some(function (o) { return o[0] === prev; });
								var bold = opts.some(function (o) { return o[0] === "B"; });
								stySel.value = keep ? prev : (bold ? "B" : (opts.length ? opts[0][0] : ""));
								updateFlyerFontPreview();
							});
						}
						var FLYER_FONT_CSS = ' . $fontCssJson . ';
						function updateFlyerFontPreview() {
							var box = document.getElementById("flyerFontPreview");
							if (!box || !famSel || !stySel) { return; }
							var w = box.clientWidth;
							if (!w) { return; }
							var c1 = (document.getElementById("flyerTextColor1") || {}).value || "#0098AF";
							var c2 = (document.getElementById("flyerTextColor2") || {}).value || "#FFFFFF";
							var off = Math.max(1, Math.round(w * 0.5 / 210 * 10) / 10);
							var sty = stySel.value || "";
							box.querySelectorAll(".flyer-font-preview-text").forEach(function (el) {
								var inp = document.getElementById(el.getAttribute("data-size-field"));
								var pt = inp ? parseFloat(inp.value) || 0 : 0;
								el.style.fontFamily = FLYER_FONT_CSS[famSel.value] || "sans-serif";
								el.style.fontWeight = sty.indexOf("B") > -1 ? "700" : "400";
								el.style.fontStyle = sty.indexOf("I") > -1 ? "italic" : "normal";
								el.style.fontSize = (pt * w / 595.28) + "px";
								el.style.color = c1;
								el.style.textShadow = (-off) + "px " + (-off) + "px 0 " + c2;
							});
						}
						document.querySelectorAll("#configimages .cfg-flyer-field").forEach(function (inp) {
							inp.addEventListener("input", updateFlyerFontPreview);
							inp.addEventListener("change", updateFlyerFontPreview);
						});
						window.addEventListener("resize", updateFlyerFontPreview);
						if (typeof ResizeObserver !== "undefined" && document.getElementById("flyerFontPreview")) {
							new ResizeObserver(updateFlyerFontPreview).observe(document.getElementById("flyerFontPreview"));
						}
						updateFlyerFontPreview();
						if (document.fonts && document.fonts.ready) { document.fonts.ready.then(updateFlyerFontPreview); }
						function flyerFontAlert(icon, text) {
							if (typeof Swal !== "undefined") { Swal.fire({ icon: icon, title: "", text: text }); }
							else { alert(text); }
						}
						var fontBtn = document.getElementById("btnFlyerFontUpload");
						if (fontBtn) {
							fontBtn.addEventListener("click", function () {
								var inp = document.getElementById("flyerFontUpload");
								if (!inp || !inp.files || inp.files.length === 0) { flyerFontAlert("info", ' . $msgFontPickJs . '); return; }
								var fd = new FormData();
								for (var i = 0; i < inp.files.length; i++) { fd.append("fontFiles[]", inp.files[i]); }
								fontBtn.disabled = true;
								if (typeof mainLoadingOn === "function") { mainLoadingOn(); }
								$.ajax({
									url: "./ajax/Admin/Config/configManagementFontUpload.php",
									type: "POST",
									data: fd,
									processData: false,
									contentType: false,
									dataType: "json",
									success: function (j) {
										fontBtn.disabled = false;
										if (typeof mainLoadingOff === "function") { mainLoadingOff(); }
										if (!j || j.status !== "1") {
											flyerFontAlert("error", (j && j.dataConfigAnswer) || "Error");
											return;
										}
										inp.value = "";
										if (j.fontFaces) {
											var st = document.createElement("style");
											st.textContent = j.fontFaces;
											document.head.appendChild(st);
										}
										Object.keys(j.fontCss || {}).forEach(function (k) { FLYER_FONT_CSS[k] = j.fontCss[k]; });
										FLYER_FONT_STYLES = j.fonts || FLYER_FONT_STYLES;
										if (famSel) {
											famSel.innerHTML = "";
											Object.keys(FLYER_FONT_STYLES).forEach(function (fam) {
												var op = document.createElement("option");
												op.value = fam;
												op.textContent = fam;
												famSel.appendChild(op);
											});
											if (j.added && j.added.length) { famSel.value = j.added[0].family; }
											famSel.dispatchEvent(new Event("change"));
											if (j.added && j.added.length && stySel) { stySel.value = j.added[0].style; }
										}
										updateFlyerFontPreview();
										if (document.fonts && document.fonts.ready) { document.fonts.ready.then(updateFlyerFontPreview); }
										flyerFontAlert("success", j.dataConfigAnswer);
									},
									error: function () {
										fontBtn.disabled = false;
										if (typeof mainLoadingOff === "function") { mainLoadingOff(); }
										flyerFontAlert("error", MSG_AJAX_GENERIC);
									}
								});
							});
						}
						var b = document.getElementById("btnSaveConfigImages");
						if (b) { b.addEventListener("click", validateConfigImages); }
					})();
					</script>';
?>
