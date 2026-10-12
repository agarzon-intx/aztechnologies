<?php
	require_once dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'flyer_text_style.inc.php';

	$fnt = function ($key, $fallback) use ($lang) {
		return htmlspecialchars(isset($lang[$key]) ? $lang[$key] : $fallback, ENT_QUOTES, 'UTF-8');
	};
	$fntJson = function ($v) {
		return json_encode($v, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
	};
	$fntSiteRoot = rtrim((string) $Config->getPath(), '/\\');
	$fntDir = $fntSiteRoot !== '' ? $fntSiteRoot . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . 'font' : '';
	$fntStyleLabels = array(
		'' => isset($lang['452-32']) ? $lang['452-32'] : 'Regular',
		'B' => isset($lang['452-33']) ? $lang['452-33'] : 'Bold',
		'I' => isset($lang['452-34']) ? $lang['452-34'] : 'Italic',
		'BI' => isset($lang['452-35']) ? $lang['452-35'] : 'Bold italic',
	);

	// family => [[style code, label], ...] (same shape the font upload endpoint returns)
	$fntList = array();
	foreach (az_flyer_available_fonts($fntDir) as $fntFamily => $fntInfo) {
		if ($fntInfo['symbolic'] || empty($fntInfo['styles'])) {
			continue;
		}
		$fntList[$fntFamily] = array();
		foreach ($fntInfo['styles'] as $fntCode => $unused) {
			$fntList[$fntFamily][] = array((string) $fntCode, $fntStyleLabels[$fntCode]);
		}
	}
	$fntWeb = az_flyer_font_web_assets($fntDir, array_keys($fntList));

	$htmlConfig .= '
	<div class="container-fluid py-2" id="cfgFontsRoot">
		<div class="card">
			<div class="card-body">
				<h6 class="mb-1">' . $fnt('452-98', 'PDF fonts') . '</h6>
				<p class="text-sm text-muted mb-3">' . $fnt('452-99', 'Fonts available for every PDF (shared by all sites). Use them in Configuration → PDF layout.') . '</p>
				<style>' . $fntWeb['faces'] . '</style>
				<div class="table-responsive mb-3">
					<table class="table table-sm align-middle mb-0">
						<thead>
							<tr>
								<th class="text-xs">' . $fnt('452-30', 'Font') . '</th>
								<th class="text-xs">' . $fnt('452-31', 'Type') . '</th>
								<th class="text-xs">' . $fnt('452-36', 'Preview') . '</th>
							</tr>
						</thead>
						<tbody id="cfgFontList"></tbody>
					</table>
				</div>
				<div class="row g-2 align-items-end">
					<div class="col-12 col-md-8 col-xl-6">
						<label class="form-label" for="cfgFontUpload">' . $fnt('452-37', 'Add font (.ttf)') . '</label>
						<div class="input-group">
							<input type="file" class="form-control" id="cfgFontUpload" accept=".ttf,.otf" multiple>
							<button type="button" class="btn btn-outline-primary mb-0" id="btnCfgFontUpload">' . $fnt('452-38', 'Add') . '</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
	(function () {
		var FONTS = ' . $fntJson((object) $fntList) . ';
		var FONT_CSS = ' . $fntJson((object) $fntWeb['css']) . ';
		var MSG_PICK = ' . $fntJson(isset($lang['452-40']) ? $lang['452-40'] : 'Choose a font file (.ttf).') . ';
		var MSG_AJAX = ' . $fntJson(isset($lang['js0002']) ? $lang['js0002'] : 'Error') . ';
		var SAMPLE = "AaBbCc 0123 – 16 de octubre";
		var list = document.getElementById("cfgFontList");

		function styleCss(el, code) {
			el.style.fontWeight = code.indexOf("B") > -1 ? "700" : "400";
			el.style.fontStyle = code.indexOf("I") > -1 ? "italic" : "normal";
		}

		function render(highlight) {
			if (!list) { return; }
			list.innerHTML = "";
			Object.keys(FONTS).forEach(function (fam) {
				var css = FONT_CSS[fam] || "sans-serif";
				var tr = document.createElement("tr");
				if (highlight && highlight.indexOf(fam) > -1) { tr.style.background = "#e8f5e9"; }
				var tdName = document.createElement("td");
				tdName.className = "text-sm fw-bold";
				tdName.textContent = fam;
				var tdTypes = document.createElement("td");
				FONTS[fam].forEach(function (s) {
					var b = document.createElement("span");
					b.className = "badge bg-light text-dark border me-1";
					b.style.fontFamily = css;
					b.style.fontSize = "13px";
					styleCss(b, s[0]);
					b.textContent = s[1];
					tdTypes.appendChild(b);
				});
				var tdSample = document.createElement("td");
				tdSample.style.fontFamily = css;
				tdSample.style.fontSize = "18px";
				tdSample.style.whiteSpace = "nowrap";
				styleCss(tdSample, FONTS[fam].length ? FONTS[fam][0][0] : "");
				tdSample.textContent = SAMPLE;
				tr.appendChild(tdName);
				tr.appendChild(tdTypes);
				tr.appendChild(tdSample);
				list.appendChild(tr);
			});
		}

		function notify(icon, text) {
			if (typeof Swal !== "undefined") { Swal.fire({ icon: icon, title: "", text: text }); }
			else { alert(text); }
		}

		var btn = document.getElementById("btnCfgFontUpload");
		if (btn) {
			btn.addEventListener("click", function () {
				var inp = document.getElementById("cfgFontUpload");
				if (!inp || !inp.files || inp.files.length === 0) { notify("info", MSG_PICK); return; }
				var fd = new FormData();
				for (var i = 0; i < inp.files.length; i++) { fd.append("fontFiles[]", inp.files[i]); }
				btn.disabled = true;
				if (typeof mainLoadingOn === "function") { mainLoadingOn(); }
				$.ajax({
					url: "./ajax/Admin/Config/configManagementFontUpload.php",
					type: "POST",
					data: fd,
					processData: false,
					contentType: false,
					dataType: "json",
					success: function (j) {
						btn.disabled = false;
						if (typeof mainLoadingOff === "function") { mainLoadingOff(); }
						if (!j || j.status !== "1") {
							notify("error", (j && j.dataConfigAnswer) || "Error");
							return;
						}
						inp.value = "";
						if (j.fontFaces) {
							var st = document.createElement("style");
							st.textContent = j.fontFaces;
							document.head.appendChild(st);
						}
						Object.keys(j.fontCss || {}).forEach(function (k) { FONT_CSS[k] = j.fontCss[k]; });
						if (j.fonts) { FONTS = j.fonts; }
						render((j.added || []).map(function (a) { return a.family; }));
						notify("success", j.dataConfigAnswer);
					},
					error: function () {
						btn.disabled = false;
						if (typeof mainLoadingOff === "function") { mainLoadingOff(); }
						notify("error", MSG_AJAX);
					}
				});
			});
		}

		render();
	})();
	</script>';
?>
