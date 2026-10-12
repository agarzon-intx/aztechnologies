/* PDF layout editor (Configuration → PDF layout). Renders a document with pdf.js and lets the user
 * move / resize / hide each drawing call. Overrides are keyed by "file.php:line" (see global/include/pdf_layout.php). */
(function () {
	"use strict";

	var PDFJS_BASE = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/";
	var pdfjsPromise = null;

	function loadPdfJs() {
		if (window.pdfjsLib && window.pdfjsLib.getDocument && window.pdfjsLib.version && window.pdfjsLib.version.indexOf("3.") === 0) {
			return Promise.resolve(window.pdfjsLib);
		}
		if (!pdfjsPromise) {
			pdfjsPromise = new Promise(function (resolve, reject) {
				var s = document.createElement("script");
				s.src = PDFJS_BASE + "pdf.min.js";
				s.onload = function () {
					window.pdfjsLib.GlobalWorkerOptions.workerSrc = PDFJS_BASE + "pdf.worker.min.js";
					resolve(window.pdfjsLib);
				};
				s.onerror = function () { pdfjsPromise = null; reject(new Error("pdf.js")); };
				document.head.appendChild(s);
			});
		}
		return pdfjsPromise;
	}

	function el(tag, attrs, text) {
		var e = document.createElement(tag);
		if (attrs) { Object.keys(attrs).forEach(function (k) { e.setAttribute(k, attrs[k]); }); }
		if (text !== undefined) { e.textContent = text; }
		return e;
	}

	function round1(v) { return Math.round(v * 10) / 10; }

	window.azPdfLayoutEditor = function (cfg) {
		var root = document.getElementById("pdfLayoutRoot");
		if (!root || root.getAttribute("data-ready") === "1") { return; }
		root.setAttribute("data-ready", "1");

		var $ = function (id) { return document.getElementById(id); };
		var docSel = $("plDoc"), qsInp = $("plQs"), pageSel = $("plPage"), stage = $("plStage"),
			canvas = $("plCanvas"), overlay = $("plOverlay"), busy = $("plBusy"), msg = $("plMsg"),
			props = $("plProps"), list = $("plList"), dirtyEl = $("plDirty"), openA = $("plOpen");
		var S = cfg.str;

		var layout = cfg.layout && typeof cfg.layout === "object" && !Array.isArray(cfg.layout) ? cfg.layout : {};
		var state = {
			pdf: null, capture: null, page: 1, scale: 1, k: 1,
			groups: {}, groupOf: {}, selected: null, selKey: null, dirty: false, loadSeq: 0, refreshTimer: null
		};

		function docUrl() {
			var d = cfg.docs[+docSel.value];
			if (!d) { return ""; }
			var qs = qsInp.value.trim().replace(/^\?/, "");
			return "pdf/" + d.script + (qs ? "?" + qs : "");
		}

		function setDirty(v) {
			state.dirty = v;
			dirtyEl.style.display = v ? "" : "none";
		}

		function notify(icon, text) {
			if (typeof Swal !== "undefined") { Swal.fire({ icon: icon, title: text }); } else { alert(text); }
		}

		function post(extra) {
			var fd = new FormData();
			fd.append("azLayoutDraft", JSON.stringify(layout));
			Object.keys(extra || {}).forEach(function (k) { fd.append(k, extra[k]); });
			return fetch(docUrl(), { method: "POST", body: fd, credentials: "same-origin" });
		}

		/* ---------- grouping: a key and its "shadow" copy (same text drawn ~same place) move together ---------- */
		function buildGroups() {
			var parent = {};
			var find = function (a) { while (parent[a] !== a) { parent[a] = parent[parent[a]]; a = parent[a]; } return a; };
			var els = state.capture.elements;
			els.forEach(function (e) { if (!(e.k in parent)) { parent[e.k] = e.k; } });
			var buckets = {};
			els.forEach(function (e) {
				if (!e.t || (e.m !== "Cell" && e.m !== "Text")) { return; }
				var b = e.p + "\u0000" + e.t;
				(buckets[b] = buckets[b] || []).push(e);
			});
			Object.keys(buckets).forEach(function (b) {
				var arr = buckets[b];
				for (var i = 0; i < arr.length && i < 60; i++) {
					for (var j = i + 1; j < arr.length && j < 60; j++) {
						if (arr[i].k !== arr[j].k && Math.abs(arr[i].x - arr[j].x) <= 1.5 && Math.abs(arr[i].y - arr[j].y) <= 1.5) {
							parent[find(arr[i].k)] = find(arr[j].k);
						}
					}
				}
			});
			var groups = {}, groupOf = {}, firstIdx = {};
			state.firstEl = {};
			els.forEach(function (e, i) {
				var g = find(e.k);
				groupOf[e.k] = g;
				if (!(e.k in firstIdx)) { firstIdx[e.k] = i; state.firstEl[e.k] = e; }
				if (!groups[g]) { groups[g] = { id: g, keys: [], m: e.m, sig: e.sig || "", label: "", pages: {} }; }
				var G = groups[g];
				if (G.keys.indexOf(e.k) < 0) { G.keys.push(e.k); }
				G.pages[e.p] = true;
				if (!G.label && e.t) { G.label = e.t; }
			});
			Object.keys(groups).forEach(function (g) {
				var G = groups[g];
				// draw order: the last key drawn is the visible text, earlier ones are its shadow
				G.keys.sort(function (a, b) { return firstIdx[a] - firstIdx[b]; });
				G.top = G.keys[G.keys.length - 1];
				G.kind = G.m === "Image" ? "image" : G.label ? "text" : "box";
				if (!G.label) {
					G.label = G.m === "Image" ? S.image + (G.sig ? ": " + G.sig : "") : S.line;
				}
			});
			state.groups = groups;
			state.groupOf = groupOf;
			state.selected = state.selKey && groupOf[state.selKey] ? groupOf[state.selKey] : null;
		}

		function ov(key) { return layout[key] || {}; }

		function groupValues(G) {
			var o = ov(G.top);
			return { dx: +o.dx || 0, dy: +o.dy || 0, s: o.s ? +o.s : 1, hide: !!o.hide };
		}

		/** Captured element of a key, preferably on the current page. */
		function keyEl(key) {
			var els = state.capture.elements;
			for (var i = 0; i < els.length; i++) {
				if (els[i].k === key && els[i].p === state.page) { return els[i]; }
			}
			return state.firstEl[key] || null;
		}

		function fontInfo(name) {
			var n = String(name || "").toLowerCase();
			for (var i = 0; i < cfg.fonts.length; i++) {
				if (cfg.fonts[i].name.toLowerCase() === n) { return cfg.fonts[i]; }
			}
			return null;
		}

		function styleLabel(code) {
			return { "": S.styleR, B: S.styleB, I: S.styleI, BI: S.styleBI }[code] || code;
		}

		var FIELDS = ["dx", "dy", "s", "hide", "font", "fs", "pt", "color", "al", "w", "h", "src"];

		/** patch: field → value (null removes the override); onlyKeys limits it to some keys of the group. */
		function setGroup(G, patch, onlyKeys) {
			(onlyKeys || G.keys).forEach(function (key) {
				var o = Object.assign({}, ov(key));
				Object.keys(patch).forEach(function (f) {
					if (patch[f] === null || patch[f] === undefined) { delete o[f]; } else { o[f] = patch[f]; }
				});
				var sample = state.firstEl[key];
				if (sample) {
					o.m = sample.m;
					if (sample.m === "Image" && sample.sig) { o.sig = sample.sig; }
				}
				o.label = G.label.slice(0, 80);
				["dx", "dy"].forEach(function (f) {
					if (o[f] !== undefined) { o[f] = round1(+o[f] || 0); if (!o[f]) { delete o[f]; } }
				});
				if (o.s !== undefined && (!o.s || Math.abs(o.s - 1) < 0.001)) { delete o.s; }
				if (!o.hide) { delete o.hide; }
				var has = FIELDS.some(function (f) { return o[f] !== undefined; });
				if (has) { layout[key] = o; } else { delete layout[key]; }
			});
			setDirty(true);
			scheduleRefresh();
		}

		/* ---------- rendering ---------- */
		function showBusy(v) { busy.style.display = v ? "flex" : "none"; }

		function load() {
			var seq = ++state.loadSeq;
			msg.textContent = "";
			showBusy(true);
			openA.href = docUrl();
			var capReq = post({ azLayoutCapture: "2" }).then(function (r) { return r.text(); });
			return Promise.all([loadPdfJs(), capReq]).then(function (res) {
				if (seq !== state.loadSeq) { return; }
				var cap = null;
				try { cap = JSON.parse(res[1]); } catch (e) { cap = null; }
				if (!cap || !cap.elements || !cap.pdf) { throw new Error("doc"); }
				var bin = atob(cap.pdf);
				var bytes = new Uint8Array(bin.length);
				for (var i = 0; i < bin.length; i++) { bytes[i] = bin.charCodeAt(i); }
				delete cap.pdf;
				state.capture = cap;
				state.k = +cap.k || 1;
				buildGroups();
				return res[0].getDocument({ data: bytes }).promise.then(function (pdf) {
					if (seq !== state.loadSeq) { return; }
					if (state.pdf) { state.pdf.destroy(); }
					state.pdf = pdf;
					fillPages(pdf.numPages);
					return renderPage();
				});
			}).catch(function () {
				if (seq !== state.loadSeq) { return; }
				msg.textContent = S.loadError;
				overlay.innerHTML = "";
				list.innerHTML = "";
			}).then(function () {
				if (seq === state.loadSeq) { showBusy(false); }
			});
		}

		function fillPages(n) {
			var cur = Math.min(state.page, n) || 1;
			if (pageSel.options.length !== n) {
				pageSel.innerHTML = "";
				for (var i = 1; i <= n; i++) { pageSel.appendChild(el("option", { value: String(i) }, String(i))); }
			}
			pageSel.value = String(cur);
			state.page = cur;
		}

		function renderPage() {
			return state.pdf.getPage(state.page).then(function (page) {
				var base = page.getViewport({ scale: 1 });
				var avail = Math.max(320, Math.min(900, stage.parentNode.clientWidth - 4));
				var scale = avail / base.width;
				var vp = page.getViewport({ scale: scale * (window.devicePixelRatio || 1) });
				canvas.width = Math.round(vp.width);
				canvas.height = Math.round(vp.height);
				canvas.style.width = Math.round(base.width * scale) + "px";
				canvas.style.height = Math.round(base.height * scale) + "px";
				state.scale = scale;
				return page.render({ canvasContext: canvas.getContext("2d"), viewport: vp }).promise;
			}).then(function () {
				drawOverlay();
				drawList();
				var ae = document.activeElement;
				if (!(props.contains(ae) && ae.tagName === "INPUT" && ae.type === "number")) { drawProps(); }
			});
		}

		function u2px(v) { return v * state.k * state.scale; }

		function drawOverlay() {
			overlay.innerHTML = "";
			var cap = state.capture;
			var pg = cap.pages[state.page] || cap.pages[String(state.page)];
			var pageArea = pg ? pg.w * pg.h : 0;
			var els = cap.elements.filter(function (e) { return e.p === state.page && e.w > 0 && e.h > 0; });
			els.sort(function (a, b) { return b.w * b.h - a.w * a.h; });
			els.forEach(function (e) {
				var g = state.groupOf[e.k];
				var isBg = pageArea > 0 && e.w * e.h >= pageArea * 0.8;
				var box = el("div", { "data-g": g, title: state.groups[g].label + "  [" + e.k + "]" });
				var sel = state.selected === g;
				box.style.cssText = "position:absolute;box-sizing:border-box;cursor:move;" +
					"left:" + u2px(e.x) + "px;top:" + u2px(e.y) + "px;width:" + Math.max(4, u2px(e.w)) + "px;height:" + Math.max(4, u2px(e.h)) + "px;" +
					"border:1px " + (e.hidden ? "dotted #999" : "dashed rgba(0,120,255,.55)") + ";" +
					(sel ? "border:2px solid #ff3d00;background:rgba(255,61,0,.12);" : "") +
					(isBg ? "pointer-events:none;border-color:transparent;" + (sel ? "border:3px solid #ff3d00;" : "") : "");
				overlay.appendChild(box);
			});
		}

		function groupsOnPage() {
			return Object.keys(state.groups).map(function (g) { return state.groups[g]; })
				.filter(function (G) { return G.pages[state.page]; })
				.map(function (G) {
					var first = null;
					state.capture.elements.some(function (e) { if (state.groupOf[e.k] === G.id && e.p === state.page) { first = e; return true; } return false; });
					return { G: G, y: first ? first.y : 0, x: first ? first.x : 0 };
				})
				.sort(function (a, b) { return a.y - b.y || a.x - b.x; })
				.map(function (r) { return r.G; });
		}

		function drawList() {
			list.innerHTML = "";
			groupsOnPage().forEach(function (G) {
				var v = groupValues(G);
				var row = el("div", { "data-g": G.id });
				row.style.cssText = "display:flex;gap:6px;align-items:center;padding:3px 6px;cursor:pointer;border-radius:4px;font-size:13px;" +
					(state.selected === G.id ? "background:#ffe3d9;" : "") + (v.hide ? "opacity:.5;text-decoration:line-through;" : "");
				var badge = el("span", null, G.kind === "image" ? "IMG" : G.kind === "box" ? "▭" : "T");
				badge.style.cssText = "flex:0 0 30px;font-size:10px;color:#666;text-align:center;border:1px solid #ccc;border-radius:3px;";
				var name = el("span", null, G.label);
				name.style.cssText = "flex:1;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;";
				row.appendChild(badge);
				row.appendChild(name);
				var changed = G.keys.some(function (key) {
					return FIELDS.some(function (f) { return f !== "hide" && ov(key)[f] !== undefined; });
				});
				if (changed) {
					var mark = el("span", null, "●");
					mark.style.color = "#ff3d00";
					row.appendChild(mark);
				}
				list.appendChild(row);
			});
		}

		function field(label, input, col) {
			var wrap = el("div", { "class": col || "col-4" });
			wrap.appendChild(el("label", { "class": "form-label mb-0 text-xs" }, label));
			wrap.appendChild(input);
			return wrap;
		}

		function numField(label, value, step, attr, col) {
			var inp = el("input", { type: "number", step: String(step), "class": "form-control form-control-sm", "data-f": attr });
			inp.value = String(value);
			return field(label, inp, col);
		}

		function selectField(label, options, value, attr, col) {
			var sel = el("select", { "class": "form-select form-select-sm", "data-f": attr });
			options.forEach(function (op) {
				var o = el("option", { value: op[0] }, op[1]);
				if (op[0] === value) { o.selected = true; }
				sel.appendChild(o);
			});
			return field(label, sel, col);
		}

		function drawProps() {
			props.innerHTML = "";
			var G = state.selected ? state.groups[state.selected] : null;
			if (!G) {
				props.appendChild(el("div", { "class": "text-sm text-muted" }, S.none));
				return;
			}
			var v = groupValues(G);
			var e = keyEl(G.top) || {};
			var o = ov(G.top);
			var unit = Math.abs(state.k - 72 / 25.4) < 0.01 ? " (mm)" : "";
			var title = el("div", { "class": "fw-bold text-sm mb-1" }, G.label);
			title.style.cssText = "overflow:hidden;white-space:nowrap;text-overflow:ellipsis;";
			props.appendChild(title);
			props.appendChild(el("div", { "class": "text-xs text-muted mb-2" }, G.keys.join(", ")));
			var row = el("div", { "class": "row g-2" });
			row.appendChild(numField(S.moveX + unit, v.dx, 0.5, "dx", "col-6"));
			row.appendChild(numField(S.moveY + unit, v.dy, 0.5, "dy", "col-6"));

			if (G.kind === "text") {
				var fi = fontInfo(o.font || e.f);
				var fontOpts = cfg.fonts.map(function (f) { return [f.name, f.name]; });
				var fontVal = fi ? fi.name : (e.f || "");
				if (!fi && fontVal) { fontOpts.unshift([fontVal, fontVal]); }
				row.appendChild(selectField(S.font, fontOpts, fontVal, "font", "col-6"));
				var styles = fi ? fi.styles : ["", "B", "I", "BI"];
				var styleVal = o.fs !== undefined ? o.fs : (e.fs || "");
				row.appendChild(selectField(S.type, styles.map(function (c) { return [c, styleLabel(c)]; }), styleVal, "fs", "col-6"));
				row.appendChild(numField(S.pt, o.pt !== undefined ? o.pt : (e.pt || ""), 0.5, "pt", "col-6"));
				if (G.m === "Cell") {
					row.appendChild(selectField(S.align, [["L", S.alignL], ["C", S.alignC], ["R", S.alignR]], o.al || e.al || "L", "al", "col-6"));
				}
				G.keys.slice().reverse().forEach(function (key) {
					var ke = keyEl(key) || {};
					var ci = el("input", { type: "color", "class": "form-control form-control-sm form-control-color w-100", "data-f": "color", "data-key": key });
					ci.value = (ov(key).color || ke.c || "#000000").toLowerCase();
					row.appendChild(field(key === G.top ? S.color : S.shadowColor, ci, "col-6"));
				});
				if (G.m === "Cell") {
					row.appendChild(numField(S.width + unit, round1(o.w !== undefined ? o.w : (e.w || 0)), 0.5, "w", "col-6"));
					row.appendChild(numField(S.height + unit, round1(o.h !== undefined ? o.h : (e.h || 0)), 0.5, "h", "col-6"));
				}
			} else if (G.kind === "image") {
				row.appendChild(numField(S.width + unit, round1(e.w || 0), 0.5, "w", "col-6"));
				row.appendChild(numField(S.height + unit, round1(e.h || 0), 0.5, "h", "col-6"));
				var kr = el("label", { "class": "col-12 mb-0 text-sm d-flex align-items-center gap-1" });
				var kc = el("input", { type: "checkbox", "data-f": "ratio" });
				kc.checked = state.keepRatio !== false;
				kr.appendChild(kc);
				kr.appendChild(document.createTextNode(S.keepRatio));
				row.appendChild(kr);
				// team logos ("<Season_ID>-<TEAM_ID>.png") cannot be replaced: one upload would replace every team's logo
				var isLogo = /^#-#\.(png|jpe?g|gif|webp)$/i.test(G.sig || "");
				var srcRow = el("div", { "class": "col-12 d-flex flex-wrap align-items-center gap-2" });
				if (isLogo) { srcRow.style.display = "none"; }
				if (o.src) {
					var info = el("span", { "class": "text-xs text-muted" }, S.replacedBy + " " + o.src.split("/").pop());
					srcRow.appendChild(info);
				}
				var file = el("input", { type: "file", accept: "image/png,image/jpeg,image/gif,image/webp", "data-f": "upload" });
				file.style.display = "none";
				srcRow.appendChild(file);
				srcRow.appendChild(el("button", { type: "button", "class": "btn btn-sm btn-outline-primary mb-0", "data-f": "pick" }, S.changeImage));
				if (o.src) {
					srcRow.appendChild(el("button", { type: "button", "class": "btn btn-sm btn-outline-secondary mb-0", "data-f": "original" }, S.originalImage));
				}
				row.appendChild(srcRow);
			} else if (G.m === "Cell" || G.m === "Rect") {
				row.appendChild(numField(S.width + unit, round1(o.w !== undefined ? o.w : (e.w || 0)), 0.5, "w", "col-6"));
				row.appendChild(numField(S.height + unit, round1(o.h !== undefined ? o.h : (e.h || 0)), 0.5, "h", "col-6"));
			}
			props.appendChild(row);
			var bar = el("div", { "class": "d-flex align-items-center gap-3 mt-2" });
			var hl = el("label", { "class": "mb-0 text-sm d-flex align-items-center gap-1" });
			var hc = el("input", { type: "checkbox", "data-f": "hide" });
			hc.checked = v.hide;
			hl.appendChild(hc);
			hl.appendChild(document.createTextNode(S.hide));
			bar.appendChild(hl);
			var rb = el("button", { type: "button", "class": "btn btn-sm btn-outline-secondary mb-0", "data-f": "reset" }, S.reset);
			bar.appendChild(rb);
			props.appendChild(bar);
		}

		function scheduleRefresh() {
			clearTimeout(state.refreshTimer);
			state.refreshTimer = setTimeout(load, 350);
		}

		function select(g) {
			state.selected = g && state.groups[g] ? g : null;
			state.selKey = state.selected ? state.groups[g].keys[0] : null;
			drawOverlay();
			drawList();
			drawProps();
		}

		/* ---------- interaction ---------- */
		var drag = null;
		overlay.addEventListener("pointerdown", function (ev) {
			var box = ev.target.closest("[data-g]");
			if (!box) { select(null); return; }
			ev.preventDefault();
			overlay.focus();
			var g = box.getAttribute("data-g");
			if (state.selected !== g) { select(g); }
			drag = { g: g, x0: ev.clientX, y0: ev.clientY, dx: 0, dy: 0, id: ev.pointerId };
			try { overlay.setPointerCapture(ev.pointerId); } catch (e) { /* synthetic pointer */ }
		});
		overlay.addEventListener("pointermove", function (ev) {
			if (!drag || ev.pointerId !== drag.id) { return; }
			drag.dx = ev.clientX - drag.x0;
			drag.dy = ev.clientY - drag.y0;
			overlay.querySelectorAll('[data-g="' + CSS.escape(drag.g) + '"]').forEach(function (b) {
				b.style.transform = "translate(" + drag.dx + "px," + drag.dy + "px)";
			});
		});
		function endDrag(ev) {
			if (!drag || ev.pointerId !== drag.id) { return; }
			var d = drag;
			drag = null;
			if (Math.abs(d.dx) < 2 && Math.abs(d.dy) < 2) { return; }
			var G = state.groups[d.g], v = groupValues(G), f = state.k * state.scale;
			setGroup(G, { dx: v.dx + d.dx / f, dy: v.dy + d.dy / f });
			drawProps();
		}
		overlay.addEventListener("pointerup", endDrag);
		overlay.addEventListener("pointercancel", endDrag);

		document.addEventListener("keydown", function (ev) {
			if (!state.selected || !root.offsetParent || !state.groups[state.selected]) { return; }
			var t = ev.target;
			if (t && (t.tagName === "INPUT" || t.tagName === "SELECT" || t.tagName === "TEXTAREA")) { return; }
			var step = ev.shiftKey ? 5 : 0.5, mx = 0, my = 0;
			if (ev.key === "ArrowLeft") { mx = -step; } else if (ev.key === "ArrowRight") { mx = step; }
			else if (ev.key === "ArrowUp") { my = -step; } else if (ev.key === "ArrowDown") { my = step; }
			else { return; }
			ev.preventDefault();
			var G = state.groups[state.selected], v = groupValues(G);
			setGroup(G, { dx: v.dx + mx, dy: v.dy + my });
			overlay.querySelectorAll('[data-g="' + CSS.escape(state.selected) + '"]').forEach(function (b) {
				b.style.left = (parseFloat(b.style.left) + u2px(mx)) + "px";
				b.style.top = (parseFloat(b.style.top) + u2px(my)) + "px";
			});
			drawProps();
		});

		list.addEventListener("click", function (ev) {
			var row = ev.target.closest("[data-g]");
			if (row) { select(row.getAttribute("data-g")); }
		});

		function uploadImage(G, file) {
			var fd = new FormData();
			fd.append("image", file);
			showBusy(true);
			fetch(cfg.uploadUrl, { method: "POST", body: fd, credentials: "same-origin" })
				.then(function (r) { return r.json(); })
				.then(function (j) {
					if (!j || j.status !== "1" || !j.src) { throw new Error((j && j.dataConfigAnswer) || "Error"); }
					setGroup(G, { src: j.src, w: null, h: null, s: null });
				})
				.catch(function (e) { showBusy(false); notify("error", e.message || "Error"); });
		}

		props.addEventListener("change", function (ev) {
			var t = ev.target, f = t.getAttribute("data-f");
			var G = state.selected ? state.groups[state.selected] : null;
			if (!G || !f) { return; }
			if (f === "hide") { setGroup(G, { hide: t.checked || null }); return; }
			if (f === "ratio") { state.keepRatio = t.checked; return; }
			if (f === "upload") { if (t.files && t.files[0]) { uploadImage(G, t.files[0]); } return; }
			if (f === "font") {
				var fi = fontInfo(t.value), cur = (props.querySelector('[data-f="fs"]') || {}).value || "";
				var fs = fi && fi.styles.indexOf(cur) < 0 ? fi.styles[0] : cur;
				setGroup(G, { font: t.value, fs: fs });
				drawProps();
				return;
			}
			if (f === "fs" || f === "al") { var p = {}; p[f] = t.value; setGroup(G, p); return; }
			if (f === "color") { setGroup(G, { color: t.value.toUpperCase() }, [t.getAttribute("data-key")]); return; }
			var n = parseFloat(t.value);
			if (!isFinite(n)) { return; }
			if (f === "dx" || f === "dy") { var d = {}; d[f] = n; setGroup(G, d); return; }
			if (f === "pt") { setGroup(G, { pt: Math.max(1, Math.min(400, n)), s: null }); return; }
			if (f === "w" || f === "h") {
				if (n <= 0) { return; }
				var patch = {};
				patch[f] = n;
				var e = keyEl(G.top);
				if (G.kind === "image" && state.keepRatio !== false && e && e.w > 0 && e.h > 0) {
					if (f === "w") { patch.h = round1(n * e.h / e.w); } else { patch.w = round1(n * e.w / e.h); }
					var other = props.querySelector('[data-f="' + (f === "w" ? "h" : "w") + '"]');
					if (other) { other.value = String(f === "w" ? patch.h : patch.w); }
				}
				if (G.kind === "image") { patch.s = null; }
				setGroup(G, patch);
			}
		});
		props.addEventListener("click", function (ev) {
			var f = ev.target.getAttribute("data-f");
			var G = state.selected ? state.groups[state.selected] : null;
			if (!G) { return; }
			if (f === "pick") { var fi = props.querySelector('[data-f="upload"]'); if (fi) { fi.click(); } return; }
			if (f === "original") { setGroup(G, { src: null, w: null, h: null }); drawProps(); return; }
			if (f === "reset") {
				var clear = {};
				FIELDS.forEach(function (k) { clear[k] = null; });
				setGroup(G, clear);
				drawProps();
			}
		});

		docSel.addEventListener("change", function () {
			var d = cfg.docs[+docSel.value];
			qsInp.value = d ? d.qs : "";
			state.page = 1;
			state.selected = state.selKey = null;
			load();
		});
		pageSel.addEventListener("change", function () {
			state.page = +pageSel.value || 1;
			state.selected = state.selKey = null;
			if (state.pdf) { showBusy(true); renderPage().then(function () { showBusy(false); }); }
		});
		$("plLoad").addEventListener("click", function () { load(); });
		qsInp.addEventListener("keydown", function (ev) { if (ev.key === "Enter") { ev.preventDefault(); load(); } });

		$("plResetDoc").addEventListener("click", function () {
			if (!state.capture || !confirm(S.confirmReset)) { return; }
			state.capture.elements.forEach(function (e) { delete layout[e.k]; });
			setDirty(true);
			load();
		});

		$("plSave").addEventListener("click", function () {
			var btn = this;
			btn.disabled = true;
			var fd = new FormData();
			fd.append("layout", JSON.stringify(layout));
			fetch(cfg.saveUrl, { method: "POST", body: fd, credentials: "same-origin" })
				.then(function (r) { return r.json(); })
				.then(function (j) {
					if (!j || j.status !== "1") { throw new Error((j && j.dataConfigAnswer) || "Error"); }
					layout = j.layout && !Array.isArray(j.layout) ? j.layout : {};
					setDirty(false);
					notify("success", j.dataConfigAnswer || S.saved);
				})
				.catch(function (e) { notify("error", e.message || "Error"); })
				.then(function () { btn.disabled = false; });
		});

		window.addEventListener("resize", function () {
			clearTimeout(state.resizeTimer);
			state.resizeTimer = setTimeout(function () { if (state.pdf && root.offsetParent) { renderPage(); } }, 250);
		});

		if (cfg.docs.length) {
			qsInp.value = cfg.docs[0].qs;
			var started = false;
			var tryStart = function () {
				if (started || !root.offsetParent) { return; }
				started = true;
				load();
			};
			tryStart();
			if (!started) {
				var obs = new MutationObserver(function () { tryStart(); if (started) { obs.disconnect(); } });
				var tab = document.getElementById("pdflayout");
				if (tab) { obs.observe(tab, { attributes: true, attributeFilter: ["style", "class"] }); }
			}
		}
	};
})();
