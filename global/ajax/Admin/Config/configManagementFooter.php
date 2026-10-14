<?php
	require_once dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'footer_links.php';

	if (az_footer_tables_exist($Config)) {
		$ftr = function ($key, $fallback) use ($lang) {
			return htmlspecialchars(isset($lang[$key]) ? $lang[$key] : $fallback, ENT_QUOTES, 'UTF-8');
		};
		$ftrJson = function ($v) {
			return json_encode($v, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
		};
		$ftrTypes = array();
		foreach (az_footer_social_types() as $ftrKey => $ftrType) {
			$ftrTypes[] = array($ftrKey, $ftrType[0], $ftrType[1], $ftrType[2]);
		}
		$ftrText = array();
		foreach (array('114' => 'Image', '115' => 'Alias', '116' => 'URL', '117' => 'Type', '118' => 'Width (px)', '119' => 'Order', '123' => 'Choose the image.', '127' => 'No items.', '130' => 'Delete', '131' => 'Move up', '132' => 'Move down') as $ftrKey => $ftrFallback) {
			$ftrText[$ftrKey] = isset($lang['452-' . $ftrKey]) ? $lang['452-' . $ftrKey] : $ftrFallback;
		}
		$ftrText['ajax'] = isset($lang['js0002']) ? $lang['js0002'] : 'Error';

		$ftrCard = function ($prefix, $title, $desc) use ($ftr, $lang) {
			return '
			<div class="card mb-3">
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-start mb-1">
						<h6 class="mb-0">' . $title . '</h6>
						<button type="button" class="btn btn-sm btn-outline-primary mb-0" id="btn' . $prefix . 'Add" title="' . $ftr('452-126', 'Add') . '"><i class="fas fa-plus"></i> ' . $ftr('452-126', 'Add') . '</button>
					</div>
					<p class="text-sm text-muted mb-3">' . $desc . '</p>
					<div class="table-responsive mb-3">
						<table class="table table-sm align-middle mb-0"><thead id="' . $prefix . 'Head"></thead><tbody id="' . $prefix . 'List"></tbody></table>
					</div>
					<button type="button" class="btn btn-primary mb-0" id="btn' . $prefix . 'Save">' . $lang['0000'] . '</button>
				</div>
			</div>';
		};

		$htmlConfig .= '
	<div class="container-fluid py-2" id="cfgFooterRoot">'
		. $ftrCard('cfgFooterLogos', $ftr('452-110', 'Footer logos'), $ftr('452-111', 'Shown in the main page footer, in this order.'))
		. $ftrCard('cfgFooterSocial', $ftr('452-112', 'Social media'), $ftr('452-113', 'The icons are shown in the footer after the logos, in this order.')) . '
	</div>
	<script>
	(function () {
		var T = ' . $ftrJson($ftrText) . ';
		var TYPES = ' . $ftrJson($ftrTypes) . ';
		var logos = ' . $ftrJson(az_footer_logos($Config)) . ';
		var social = ' . $ftrJson(az_footer_social($Config)) . ';

		function el(tag, attrs, kids) {
			var e = document.createElement(tag);
			Object.keys(attrs || {}).forEach(function (k) {
				if (attrs[k] == null) { return; }
				if (k === "text") { e.textContent = attrs[k]; }
				else if (k === "style") { e.style.cssText = attrs[k]; }
				else if (k.slice(0, 2) === "on") { e.addEventListener(k.slice(2), attrs[k]); }
				else { e.setAttribute(k, attrs[k]); }
			});
			(kids || []).forEach(function (c) { if (c) { e.appendChild(c); } });
			return e;
		}
		function notify(icon, text) {
			if (typeof Swal !== "undefined") { Swal.fire({ icon: icon, title: "", html: String(text).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/\n/g, "<br>") }); }
			else { alert(text); }
		}
		function input(type, value, onChange, extra) {
			var a = { type: type, class: "form-control form-control-sm", value: value == null ? "" : String(value), oninput: function () { onChange(this.value); } };
			Object.keys(extra || {}).forEach(function (k) { a[k] = extra[k]; });
			return el("input", a);
		}
		function head(id, cols) {
			var h = document.getElementById(id);
			h.innerHTML = "";
			h.appendChild(el("tr", {}, cols.map(function (c) { return el("th", { class: "text-xs", text: c }); })));
		}
		function orderCell(list, i, redraw) {
			function move(d) {
				var j = i + d;
				if (j < 0 || j >= list.length) { return; }
				var x = list[i]; list[i] = list[j]; list[j] = x;
				redraw();
			}
			return el("td", { style: "white-space:nowrap" }, [
				el("button", { type: "button", class: "btn btn-sm btn-outline-secondary mb-0 px-2", title: T["131"], disabled: i === 0 ? "disabled" : null, onclick: function () { move(-1); } }, [el("i", { class: "fas fa-arrow-up" })]),
				el("button", { type: "button", class: "btn btn-sm btn-outline-secondary mb-0 px-2 ms-1", title: T["132"], disabled: i === list.length - 1 ? "disabled" : null, onclick: function () { move(1); } }, [el("i", { class: "fas fa-arrow-down" })])
			]);
		}
		function deleteCell(list, i, redraw) {
			return el("td", {}, [el("button", { type: "button", class: "btn btn-sm btn-outline-danger mb-0 px-2", title: T["130"], onclick: function () { list.splice(i, 1); redraw(); } }, [el("i", { class: "fas fa-trash" })])]);
		}
		function emptyRow(tbody, cols) {
			if (tbody.children.length === 0) { tbody.appendChild(el("tr", {}, [el("td", { colspan: cols, class: "text-sm text-muted text-center", text: T["127"] })])); }
		}
		function post(section, fd, onOk) {
			fd.append("section", section);
			if (typeof mainLoadingOn === "function") { mainLoadingOn(); }
			$.ajax({ type: "POST", url: "ajax/Admin/Config/configManagementFooterSave.php", data: fd, processData: false, contentType: false, dataType: "json",
				success: function (res) {
					if (typeof mainLoadingOff === "function") { mainLoadingOff(); }
					if (res && res.status === "1") { onOk(res.items || []); notify("success", res.dataConfigAnswer); }
					else { notify("error", res && res.dataConfigAnswer ? res.dataConfigAnswer : T.ajax); }
				},
				error: function () { if (typeof mainLoadingOff === "function") { mainLoadingOff(); } notify("error", T.ajax); }
			});
		}

		function drawLogos() {
			var tbody = document.getElementById("cfgFooterLogosList");
			head("cfgFooterLogosHead", [T["119"], T["114"], T["115"], T["116"], T["118"], ""]);
			tbody.innerHTML = "";
			logos.forEach(function (it, i) {
				var src = it.preview || (it.id ? "Form/fetch_footer_logo.php?id=" + it.id + "&v=" + (it.v || 0) : "");
				var img = el("img", { style: "max-height:40px;max-width:120px;" + (src ? "" : "display:none") });
				if (src) { img.src = src; }
				var file = el("input", { type: "file", accept: "image/png,image/jpeg,image/gif,image/webp", class: "form-control form-control-sm", style: "min-width:180px",
					onchange: function () {
						it.file = this.files && this.files[0] ? this.files[0] : null;
						if (it.preview) { URL.revokeObjectURL(it.preview); }
						it.preview = it.file ? URL.createObjectURL(it.file) : "";
						if (it.preview) { img.src = it.preview; img.style.display = ""; }
					} });
				tbody.appendChild(el("tr", {}, [
					orderCell(logos, i, drawLogos),
					el("td", {}, [el("div", { class: "d-flex align-items-center", style: "gap:8px" }, [img, file])]),
					el("td", {}, [input("text", it.alias, function (v) { it.alias = v; }, { maxlength: "100", style: "min-width:140px" })]),
					el("td", {}, [input("url", it.url, function (v) { it.url = v; }, { maxlength: "500", placeholder: "https://", style: "min-width:200px" })]),
					el("td", {}, [input("number", it.width || "", function (v) { it.width = parseInt(v, 10) || 0; }, { min: "0", max: "400", style: "width:80px" })]),
					deleteCell(logos, i, drawLogos)
				]));
			});
			emptyRow(tbody, 6);
		}

		function drawSocial() {
			var tbody = document.getElementById("cfgFooterSocialList");
			head("cfgFooterSocialHead", [T["119"], T["117"], T["115"], T["116"], ""]);
			tbody.innerHTML = "";
			social.forEach(function (it, i) {
				var icon = el("i", { style: "font-size:22px;width:24px" });
				var sel = el("select", { class: "form-select form-select-sm", style: "min-width:130px", onchange: function () { it.type = this.value; paint(); } },
					TYPES.map(function (t) { return el("option", { value: t[0], text: t[1] }); }));
				function paint() {
					var t = TYPES.filter(function (x) { return x[0] === it.type; })[0];
					icon.className = t ? t[2] : "";
					icon.style.color = t ? t[3] : "";
				}
				sel.value = it.type;
				paint();
				tbody.appendChild(el("tr", {}, [
					orderCell(social, i, drawSocial),
					el("td", {}, [el("div", { class: "d-flex align-items-center", style: "gap:8px" }, [icon, sel])]),
					el("td", {}, [input("text", it.alias, function (v) { it.alias = v; }, { maxlength: "100", style: "min-width:140px" })]),
					el("td", {}, [input("url", it.url, function (v) { it.url = v; }, { maxlength: "500", placeholder: "https://", style: "min-width:240px" })]),
					deleteCell(social, i, drawSocial)
				]));
			});
			emptyRow(tbody, 5);
		}

		document.getElementById("btncfgFooterLogosAdd").addEventListener("click", function () {
			logos.push({ id: 0, alias: "", url: "", width: 0, file: null, preview: "" });
			drawLogos();
		});
		document.getElementById("btncfgFooterSocialAdd").addEventListener("click", function () {
			social.push({ id: 0, type: TYPES[0][0], alias: "", url: "" });
			drawSocial();
		});
		document.getElementById("btncfgFooterLogosSave").addEventListener("click", function () {
			var fd = new FormData();
			var items = logos.map(function (it, i) {
				var key = "";
				if (it.file) { key = "logoFile" + i; fd.append(key, it.file); }
				return { id: it.id || 0, alias: it.alias || "", url: it.url || "", width: it.width || 0, file: key };
			});
			fd.append("items", JSON.stringify(items));
			post("logos", fd, function (saved) { logos = saved; drawLogos(); });
		});
		document.getElementById("btncfgFooterSocialSave").addEventListener("click", function () {
			var fd = new FormData();
			fd.append("items", JSON.stringify(social.map(function (it) { return { type: it.type, alias: it.alias || "", url: it.url || "" }; })));
			post("social", fd, function (saved) { social = saved; drawSocial(); });
		});

		drawLogos();
		drawSocial();
	})();
	</script>';
	}
