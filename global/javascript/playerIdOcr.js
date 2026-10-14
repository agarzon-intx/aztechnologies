/*
 * Read a Mexican voter ID (INE) from the player form's ID image, in the browser (Tesseract.js),
 * and fill name, surnames, birth date, sex, CURP and the player photo after the user confirms.
 * Loaded on demand by playerIdOcr() in main.js.php; texts come from the button's data-ocr attribute.
 */
(function () {
	var TESSERACT_SRC = 'https://cdn.jsdelivr.net/npm/tesseract.js@5.1.1/dist/tesseract.min.js';
	var workerPromise = null;

	function getWorker() {
		if (!workerPromise) {
			workerPromise = new Promise(function (resolve, reject) {
				var create = function () { Tesseract.createWorker('spa').then(resolve, reject); };
				if (window.Tesseract) { create(); return; }
				var sc = document.createElement('script');
				sc.src = TESSERACT_SRC;
				sc.onload = create;
				sc.onerror = function () { reject(new Error('tesseract load failed')); };
				document.head.appendChild(sc);
			}).catch(function (e) {
				workerPromise = null;
				throw e;
			});
		}
		return workerPromise;
	}

	/* ---------- image helpers ---------- */

	function sourceImage(s) {
		var ids = ['#identificacion11' + s, '#identificacion' + s];
		for (var i = 0; i < ids.length; i++) {
			var img = $(ids[i])[0];
			if (img && img.getAttribute('src') && img.complete && img.naturalWidth >= 200) {
				return img;
			}
		}
		return null;
	}

	/** Grayscale copy scaled to ~1600px wide; strong=true also stretches contrast around the mean. */
	function prepCanvas(img, strong) {
		var scale = Math.max(1, Math.min(3, 1600 / img.naturalWidth));
		var c = document.createElement('canvas');
		c.width = Math.round(img.naturalWidth * scale);
		c.height = Math.round(img.naturalHeight * scale);
		var x = c.getContext('2d');
		x.imageSmoothingQuality = 'high';
		x.drawImage(img, 0, 0, c.width, c.height);
		var d = x.getImageData(0, 0, c.width, c.height), p = d.data, i, sum = 0;
		for (i = 0; i < p.length; i += 4) {
			p[i] = 0.299 * p[i] + 0.587 * p[i + 1] + 0.114 * p[i + 2];
			sum += p[i];
		}
		var lo = 0, hi = 255;
		if (strong) {
			var mean = sum / (p.length / 4);
			lo = mean * 0.6;
			hi = mean * 0.92;
		}
		for (i = 0; i < p.length; i += 4) {
			var g = strong ? (p[i] <= lo ? 0 : (p[i] >= hi ? 255 : (p[i] - lo) * 255 / (hi - lo))) : p[i];
			p[i] = p[i + 1] = p[i + 2] = g;
		}
		x.putImageData(d, 0, 0);
		return { canvas: c, scale: scale };
	}

	/* ---------- field rules ---------- */

	var TO_DIGIT = { O: '0', Q: '0', D: '0', U: '0', I: '1', L: '1', T: '7', Z: '2', S: '5', B: '8', G: '6' };
	var TO_LETTER = { '0': 'O', '1': 'I', '2': 'Z', '5': 'S', '8': 'B', '6': 'G', '7': 'T', '4': 'A' };

	function coerce(chars, i, wantDigit, wantLetter) {
		if (wantDigit && !/\d/.test(chars[i])) { chars[i] = TO_DIGIT[chars[i]] || chars[i]; }
		if (wantLetter && /\d/.test(chars[i])) { chars[i] = TO_LETTER[chars[i]] || chars[i]; }
	}

	function curpCheckDigit(c17) {
		var dict = '0123456789ABCDEFGHIJKLMNÑOPQRSTUVWXYZ', sum = 0;
		for (var i = 0; i < 17; i++) { sum += dict.indexOf(c17.charAt(i)) * (18 - i); }
		return String((10 - sum % 10) % 10);
	}

	function validDate(y, m, d) {
		var dt = new Date(y, m - 1, d);
		return dt.getFullYear() === y && dt.getMonth() === m - 1 && dt.getDate() === d && dt <= new Date();
	}

	function iso(y, m, d) {
		return y + '-' + ('0' + m).slice(-2) + '-' + ('0' + d).slice(-2);
	}

	/** Best CURP in the text: {value, valid} (valid = check digit matches). */
	function findCurp(text) {
		var cands = text.replace(/[^A-Z0-9Ñ\n ]/g, '').match(/[A-Z0-9Ñ]{18}/g) || [];
		var best = null;
		cands.forEach(function (cand) {
			var s = cand.split(''), i;
			for (i = 0; i < 18; i++) {
				coerce(s, i, (i >= 4 && i <= 9) || i === 17, i < 4 || (i >= 10 && i <= 15));
			}
			var yy = parseInt(s.slice(4, 6).join(''), 10);
			coerce(s, 16, yy > new Date().getFullYear() % 100, false);
			var c = s.join('');
			if (!/^[A-Z]{4}\d{6}[HMX][A-Z]{5}[A-Z0-9]\d$/.test(c)) { return; }
			var valid = curpCheckDigit(c.slice(0, 17)) === c.charAt(17);
			if (!best || (valid && !best.valid)) { best = { value: c, valid: valid }; }
		});
		return best;
	}

	function curpBirth(curp) {
		var yy = parseInt(curp.substr(4, 2), 10), m = parseInt(curp.substr(6, 2), 10), d = parseInt(curp.substr(8, 2), 10);
		var y = (/\d/.test(curp.charAt(16)) ? 1900 : 2000) + yy;
		return validDate(y, m, d) ? iso(y, m, d) : null;
	}

	/** Voter key (clave de elector): 6 letters, YYMMDD, 2-digit state, H/M/X, 3 digits. */
	function findClave(text) {
		var cands = text.replace(/[^A-Z0-9\n ]/g, '').match(/[A-Z0-9]{18}/g) || [];
		for (var k = 0; k < cands.length; k++) {
			var s = cands[k].split('');
			for (var i = 0; i < 18; i++) {
				coerce(s, i, (i >= 6 && i <= 13) || i >= 15, i < 6);
			}
			var c = s.join('');
			if (/^[A-Z]{6}\d{8}[HMX]\d{3}$/.test(c)) {
				var yy = parseInt(c.substr(6, 2), 10), m = parseInt(c.substr(8, 2), 10), d = parseInt(c.substr(10, 2), 10);
				var y = (yy > new Date().getFullYear() % 100 ? 1900 : 2000) + yy;
				return { value: c, birth: validDate(y, m, d) ? iso(y, m, d) : null, sex: c.charAt(14) };
			}
		}
		return null;
	}

	/** Printed birth date, always dd/mm/yyyy on the card: {iso, raw}. */
	function findPrintedDate(text) {
		var re = /\b(\d{2})\s*[\/\-.]\s*(\d{2})\s*[\/\-.]\s*(\d{4})\b/g, r;
		while ((r = re.exec(text))) {
			var d = parseInt(r[1], 10), m = parseInt(r[2], 10), y = parseInt(r[3], 10);
			if (y > 1900 && validDate(y, m, d)) { return { iso: iso(y, m, d), raw: r[1] + '/' + r[2] + '/' + r[3] }; }
		}
		return null;
	}

	/* ---------- word boxes (image pixels) ---------- */

	function boxOf(words) {
		if (!words.length) { return null; }
		var b = { x0: Infinity, y0: Infinity, x1: -Infinity, y1: -Infinity };
		words.forEach(function (w) {
			b.x0 = Math.min(b.x0, w.b.x0); b.y0 = Math.min(b.y0, w.b.y0);
			b.x1 = Math.max(b.x1, w.b.x1); b.y1 = Math.max(b.y1, w.b.y1);
		});
		return b;
	}

	function joinBoxes(boxes) {
		boxes = boxes.filter(Boolean);
		return boxes.length ? boxOf(boxes.map(function (b) { return { b: b }; })) : null;
	}

	/** Box of the longest token on the line (where an 18-character key sits); exact when it is the key alone. */
	function keyBox(line) {
		var best = null, len = 0;
		line.words.forEach(function (w) {
			var n = w.t.replace(/[^A-Z0-9Ñ]/g, '').length;
			if (n > len) { best = w; len = n; }
		});
		return best ? { box: best.b, exact: len === 18 } : { box: boxOf(line.words), exact: false };
	}

	/** Part of an 18-character key box covering characters [a, b), when the box holds only the key. */
	function keySlice(key, a, b) {
		if (!key || !key.box) { return null; }
		if (!key.exact) { return key.box; }
		var w = (key.box.x1 - key.box.x0) / 18;
		return { x0: key.box.x0 + a * w, y0: key.box.y0, x1: key.box.x0 + b * w, y1: key.box.y1 };
	}

	var NAME_STOP = /^(SEXO|SEO|FECHA|NACIMIENTO|EDAD|GENERO|GÉNERO|DOMICILIO)$/;

	/** Name lines under the NOMBRE label: words aligned with the label, stopping at gaps and non-name tokens. */
	function findNames(lines) {
		var li = -1, anchor = null;
		for (var i = 0; i < lines.length && li < 0; i++) {
			for (var w = 0; w < lines[i].words.length; w++) {
				if (/^NOMBRE$/.test(lines[i].words[w].t)) { li = i; anchor = lines[i].words[w]; break; }
			}
		}
		if (li < 0) { return { anchor: null, lines: [] }; }
		var h = Math.max(4, anchor.b.y1 - anchor.b.y0), out = [];
		for (var j = li + 1; j < lines.length && j <= li + 5 && out.length < 4; j++) {
			if (/DOMICILIO|CLAVE|CURP|ELECTOR/.test(lines[j].text)) { break; }
			var words = [], used = [], prev = null;
			lines[j].words.forEach(function (wd) {
				if (words.stop) { return; }
				if (!words.length && (wd.b.x1 < anchor.b.x0 - h || wd.b.x0 > anchor.b.x0 + 3 * h)) { return; }
				var t = wd.t.replace(/[^A-ZÁÉÍÓÚÑÜ]/g, '');
				var gap = prev ? wd.b.x0 - prev.b.x1 : 0;
				if (t.length < 2 || t !== wd.t.replace(/[.,]$/, '') || NAME_STOP.test(t) || gap > 2.5 * h || wd.c < 45) {
					if (words.length) { words.stop = true; }
					return;
				}
				words.push(t);
				used.push(wd);
				prev = wd;
			});
			if (words.length) { out.push({ text: words.join(' '), box: boxOf(used) }); }
		}
		return { anchor: anchor, lines: out };
	}

	function parse(data, scale) {
		var lines = (data.lines || []).map(function (l) {
			return {
				text: String(l.text || '').toUpperCase(),
				words: (l.words || []).map(function (w) {
					return { t: String(w.text || '').toUpperCase(), c: w.confidence, b: { x0: w.bbox.x0 / scale, y0: w.bbox.y0 / scale, x1: w.bbox.x1 / scale, y1: w.bbox.y1 / scale } };
				})
			};
		});
		var curpLabel = null, curp = null, clave = null, printedDate = null, printedSex = null;
		lines.forEach(function (l) {
			l.words.forEach(function (w) {
				if (!curpLabel && /^CURP$/.test(w.t)) { curpLabel = w; }
			});
			var c = findCurp(l.text);
			if (c && (!curp || (c.valid && !curp.valid))) { curp = $.extend(c, keyBox(l)); }
			var k = !clave && findClave(l.text);
			if (k) { clave = $.extend(k, keyBox(l)); }
			var d = !printedDate && findPrintedDate(l.text);
			if (d) { d.box = boxOf(l.words.filter(function (w) { return /\d/.test(w.t); })); printedDate = d; }
			var sm = !printedSex && l.text.match(/\bSEXO\s*[:.]?\s*([HM])\b/);
			if (sm) {
				var si = -1;
				l.words.forEach(function (w, i) { if (si < 0 && /^SEXO/.test(w.t)) { si = i; } });
				printedSex = { value: sm[1], box: si < 0 ? null : boxOf(l.words.slice(si, /^SEXO\W*$/.test(l.words[si].t) ? si + 2 : si + 1)) };
			}
		});
		if (!printedSex) {
			var sexM = lines.map(function (l) { return l.text; }).join('\n').match(/\bSEXO\s*[:.]?\s*([HM])\b/);
			if (sexM) { printedSex = { value: sexM[1], box: null }; }
		}
		return {
			curpLabel: curpLabel,
			curp: curp,
			clave: clave,
			printedDate: printedDate,
			printedSex: printedSex,
			names: findNames(lines),
			confidence: data.confidence || 0
		};
	}

	var NAME_PARTICLES = /^(DA|DAS|DE|DEL|DER|DI|DIE|DD|EL|LA|LOS|LAS|LE|LES|MAC|MC|VAN|VON|Y)$/;

	function plain(s) {
		return String(s || '').toUpperCase().replace(/[ÁÀÄÂ]/g, 'A').replace(/[ÉÈËÊ]/g, 'E').replace(/[ÍÌÏÎ]/g, 'I')
			.replace(/[ÓÒÖÔ]/g, 'O').replace(/[ÚÙÜÛ]/g, 'U').replace(/[^A-ZÑ ]/g, '').trim();
	}

	function mainWord(s, skipCommon) {
		var w = plain(s).split(/\s+/).filter(function (x) { return x && !NAME_PARTICLES.test(x); });
		if (skipCommon && w.length > 1 && /^(JOSE|J|MARIA|MA)$/.test(w[0])) { w.shift(); }
		return w[0] || '';
	}

	/** CURP characters that come from the name: index => letter (positions 1-4 and 14-16). */
	function curpFromNames(paterno, materno, nombre) {
		var p = mainWord(paterno, false), m = mainWord(materno, false), n = mainWord(nombre, true);
		if (!p || !n) { return null; }
		var x = function (c) { return c === 'Ñ' ? 'X' : c; };
		var inner = function (w, vowel) {
			for (var i = 1; i < w.length; i++) {
				if (/[AEIOU]/.test(w[i]) === vowel) { return x(w[i]); }
			}
			return 'X';
		};
		return { 0: x(p[0]), 1: inner(p, true), 2: m ? x(m[0]) : 'X', 3: x(n[0]), 13: inner(p, false), 14: m ? inner(m, false) : 'X', 15: inner(n, false) };
	}

	/**
	 * Combine the OCR passes and cross-check fields. r.src holds, per field, where it was read on the image:
	 * {box} for single-source fields, and a list of {key, label, raw, fmt, value, box, chosen} for date and sex.
	 */
	function merge(passes, T) {
		var pick = function (fn) {
			for (var i = 0; i < passes.length; i++) { var v = fn(passes[i]); if (v) { return v; } }
			return null;
		};
		var curp = pick(function (p) { return p.curp && p.curp.valid ? p.curp : null; }) || pick(function (p) { return p.curp; });
		var clave = pick(function (p) { return p.clave; });
		var names = pick(function (p) { return p.names.lines.length >= 2 ? p.names : null; }) || pick(function (p) { return p.names.lines.length ? p.names : null; });
		var anchor = pick(function (p) { return p.names.anchor; });
		var warnings = [], r = { nombre: '', paterno: '', materno: '', fecha: '', sexo: '', curp: '', anchor: anchor, src: {} };

		if (curp) {
			r.curp = curp.value;
			if (!curp.valid) { warnings.push(T.curpCheck); }
		} else {
			warnings.push(T.noCurp);
		}

		var L = names ? names.lines : [], nameLine = { paterno: null, materno: null, nombre: [] };
		if (L.length >= 3) {
			nameLine.paterno = L[0]; nameLine.materno = L[1]; nameLine.nombre = L.slice(2);
		} else if (L.length === 2) {
			nameLine.paterno = L[0];
			if (curp && curp.value.charAt(2) !== 'X' && L[1].text.charAt(0) === curp.value.charAt(2) && L[1].text.charAt(0) !== curp.value.charAt(3)) {
				nameLine.materno = L[1];
			} else {
				nameLine.nombre = [L[1]];
			}
		} else if (L.length === 1) {
			nameLine.paterno = L[0];
		}
		r.paterno = nameLine.paterno ? nameLine.paterno.text : '';
		r.materno = nameLine.materno ? nameLine.materno.text : '';
		r.nombre = nameLine.nombre.map(function (l) { return l.text; }).join(' ');
		r.src.paterno = { box: nameLine.paterno ? nameLine.paterno.box : null };
		r.src.materno = { box: nameLine.materno ? nameLine.materno.box : null };
		r.src.nombre = { box: joinBoxes(nameLine.nombre.map(function (l) { return l.box; })) };
		if (!r.nombre || !r.paterno) { warnings.push(T.noNames); }

		var fromNames = curpFromNames(r.paterno, r.materno, r.nombre);
		if (curp && fromNames) {
			var idx = Object.keys(fromNames), differs = idx.some(function (k) { return curp.value.charAt(k) !== fromNames[k]; });
			// letters 10 places apart in the CURP alphabet (L/U, A/K…) give the same check digit, so a valid
			// CURP can still hold a misread letter: the name (larger print) wins when its version is also valid
			if (differs) {
				var fixed = curp.value.split('');
				idx.forEach(function (k) { fixed[k] = fromNames[k]; });
				fixed = fixed.join('');
				if (curpCheckDigit(fixed.slice(0, 17)) === fixed.charAt(17)) {
					curp = { value: fixed, valid: true, box: curp.box, exact: curp.exact, fixed: true };
					r.curp = fixed;
					warnings = warnings.filter(function (w) { return w !== T.curpCheck; });
					differs = false;
				}
			}
			if (curp.valid && differs) { warnings.push(T.nameMismatch); }
		}
		r.src.curp = { box: curp ? curp.box : null, fixed: !!(curp && curp.fixed) };

		var printed = pick(function (p) { return p.printedDate; });
		var fromCurp = curp ? curpBirth(curp.value) : null;
		var fromClave = clave ? clave.birth : null;
		var printedIso = printed ? printed.iso : null, dateKey = '';
		if (printedIso && (printedIso === fromCurp || printedIso === fromClave)) {
			dateKey = 'printed';
		} else if (fromCurp && (curp.valid || fromCurp === fromClave)) {
			dateKey = 'curp';
			if (printedIso) { warnings.push(T.dateMismatch); }
		} else {
			dateKey = printedIso ? 'printed' : (fromClave ? 'clave' : (fromCurp ? 'curp' : ''));
			if ([printedIso, fromClave, fromCurp].filter(Boolean).some(function (v, i, a) { return v !== a[0]; })) {
				warnings.push(T.dateMismatch);
			}
		}
		var dates = [];
		if (printedIso) { dates.push({ key: 'printed', label: T.srcPrintedDate, raw: printed.raw, fmt: T.fmtDate, value: printedIso, box: printed.box }); }
		if (fromCurp) { dates.push({ key: 'curp', label: T.srcCurpDate, raw: curp.value.substr(4, 6), fmt: T.fmtYmd, value: fromCurp, box: keySlice(curp, 4, 10) }); }
		if (fromClave) { dates.push({ key: 'clave', label: T.srcClaveDate, raw: clave.value.substr(6, 6), fmt: T.fmtYmd, value: fromClave, box: keySlice(clave, 6, 12) }); }
		dates.forEach(function (d) { d.chosen = d.key === dateKey; });
		r.fecha = (dates.filter(function (d) { return d.chosen; })[0] || { value: '' }).value;
		r.src.fecha = dates;

		var printedSex = pick(function (p) { return p.printedSex; });
		var sexKey = curp && curp.valid ? 'curp' : (printedSex ? 'printed' : (clave ? 'clave' : (curp ? 'curp' : '')));
		var sexes = [];
		if (curp) { sexes.push({ key: 'curp', label: T.srcCurpSex, raw: curp.value.charAt(10), box: keySlice(curp, 10, 11) }); }
		if (printedSex) { sexes.push({ key: 'printed', label: T.srcPrintedSex, raw: printedSex.value, box: printedSex.box }); }
		if (clave) { sexes.push({ key: 'clave', label: T.srcClaveSex, raw: clave.sex, box: keySlice(clave, 14, 15) }); }
		sexes.forEach(function (x) { x.chosen = x.key === sexKey; x.value = x.raw; });
		var sex = (sexes.filter(function (x) { return x.chosen; })[0] || {}).raw;
		if (sex === 'H') { r.sexo = '0'; } else if (sex === 'M') { r.sexo = '1'; } else if (sex === 'X') { warnings.push(T.sexX); }
		r.src.sexo = sexes;

		r.warnings = warnings;
		return r;
	}

	/** Face photo {url, box, estimated}: left of the NOMBRE label, sized from the label height; falls back to the card layout. */
	function cropPhoto(img, anchor) {
		var W = img.naturalWidth, H = img.naturalHeight, x0, y0, w, h;
		if (anchor) {
			var u = anchor.b.y1 - anchor.b.y0;
			var right = anchor.b.x0 - 0.4 * u;
			w = 16 * u; h = 21 * u;
			x0 = right - w; y0 = anchor.b.y0 - 0.5 * u;
		} else {
			x0 = 0.04 * W; y0 = 0.22 * H; w = 0.29 * W; h = 0.66 * H;
		}
		x0 = Math.max(0, x0); y0 = Math.max(0, y0);
		w = Math.min(W - x0, w); h = Math.min(H - y0, h);
		if (w < 40 || h < 40) { return null; }
		var c = document.createElement('canvas');
		c.width = Math.round(w); c.height = Math.round(h);
		c.getContext('2d').drawImage(img, x0, y0, w, h, 0, 0, c.width, c.height);
		return { url: c.toDataURL('image/png'), box: { x0: x0, y0: y0, x1: x0 + w, y1: y0 + h }, estimated: !anchor };
	}

	/**
	 * When no valid CURP was read, re-read only the strip by the CURP label (same line or the line below)
	 * with a letters/digits whitelist, which cuts most of the noise from the security background.
	 */
	function retryCurp(worker, passes, canvases) {
		var have = passes.some(function (p) { return p.curp && p.curp.valid; });
		var label = null;
		passes.forEach(function (p) { label = label || p.curpLabel; });
		if (have || !label) { return Promise.resolve(); }
		var u = label.b.y1 - label.b.y0;
		var list = canvases.slice(), i = 0;
		var rectFor = function (c) {
			var k = c.scale, W = c.canvas.width, H = c.canvas.height;
			var left = Math.max(0, (label.b.x0 - 0.5 * u) * k), top = Math.max(0, (label.b.y0 - 0.4 * u) * k);
			return { left: Math.round(left), top: Math.round(top), width: Math.round(Math.min(W - left, 26 * u * k)), height: Math.round(Math.min(H - top, 3.4 * u * k)) };
		};
		var restore = function () { return worker.setParameters({ tessedit_char_whitelist: '' }); };
		var next = function () {
			if (i >= list.length) { return restore(); }
			var c = list[i++];
			var rect = rectFor(c);
			return worker.recognize(c.canvas, { rectangle: rect }).then(function (r) {
				var curp = findCurp(String(r.data.text || '').toUpperCase().replace(/CURP/g, ' '));
				if (curp) {
					var words = (r.data.words || []).map(function (w) {
						return { t: String(w.text || '').toUpperCase(), b: { x0: w.bbox.x0 / c.scale, y0: w.bbox.y0 / c.scale, x1: w.bbox.x1 / c.scale, y1: w.bbox.y1 / c.scale } };
					}).filter(function (w) { return w.t.replace(/[^A-Z0-9Ñ]/g, '').length >= 10 && !/CURP/.test(w.t); });
					$.extend(curp, words.length ? keyBox({ words: words }) : {
						box: { x0: rect.left / c.scale, y0: rect.top / c.scale, x1: (rect.left + rect.width) / c.scale, y1: (rect.top + rect.height) / c.scale }, exact: false
					});
					passes.push({ curp: curp, clave: null, printedDate: null, printedSex: null, names: { anchor: null, lines: [] } });
					if (curp.valid) { return restore(); }
				}
				return next();
			});
		};
		return worker.setParameters({ tessedit_char_whitelist: 'ABCDEFGHIJKLMNOPQRSTUVWXYZÑ0123456789 ' }).then(next, function () { return next(); })
			.catch(function (e) { console.log('playerIdOcr curp retry', e); return restore(); });
	}

	/* ---------- UI ---------- */

	function esc(s) { return $('<div>').text(String(s == null ? '' : s)).html(); }

	function setField($el, value) {
		if (!$el.length) { return; }
		$el.val(value).trigger('change');
		$el.closest('.input-group').addClass('is-filled');
		$el.css('background-color', '#fff8c4').one('input change', function () { $(this).css('background-color', ''); });
	}

	var FIELD_COLORS = { nombre: '#d81b60', paterno: '#1e88e5', materno: '#43a047', fecha: '#ef6c00', sexo: '#8e24aa', curp: '#00897b', foto: '#6d4c41' };

	function dmy(isoDate) { return isoDate ? isoDate.split('-').reverse().join('/') : ''; }

	function badge(n, color) {
		return '<span style="display:inline-block;min-width:18px;height:18px;line-height:18px;border-radius:9px;background:' + color
			+ ';color:#fff;font-size:11px;font-weight:700;text-align:center;margin-right:4px;">' + n + '</span>';
	}

	/** ID image with a numbered box per field; boxes of the other fields fade while one row is hovered. */
	function drawLayout(canvas, img, rows, active) {
		var x = canvas.getContext('2d'), k = canvas.width / img.naturalWidth, lw = Math.max(2, canvas.width / 320), fs = Math.max(12, Math.round(canvas.width / 42));
		x.clearRect(0, 0, canvas.width, canvas.height);
		x.drawImage(img, 0, 0, canvas.width, canvas.height);
		rows.forEach(function (row) {
			row.boxes.forEach(function (bx) {
				var on = !active || active === row.key;
				var X = bx.box.x0 * k, Y = bx.box.y0 * k, W = (bx.box.x1 - bx.box.x0) * k, H = (bx.box.y1 - bx.box.y0) * k;
				x.globalAlpha = on ? 1 : 0.2;
				x.lineWidth = on && active ? lw * 1.6 : lw;
				x.strokeStyle = row.color;
				x.setLineDash(bx.dashed ? [lw * 3, lw * 2] : []);
				x.strokeRect(X - lw, Y - lw, W + 2 * lw, H + 2 * lw);
				x.setLineDash([]);
				x.font = 'bold ' + fs + 'px sans-serif';
				var tw = x.measureText(String(row.n)).width + fs * 0.6, th = fs * 1.2;
				// date and sex boxes are often slices of the CURP / voter key: tag below so the key stays readable
				var below = row.key === 'fecha' || row.key === 'sexo';
				var tx = below ? X - lw : X - 2 * lw - tw, ty = below ? Y + H + lw : Y + H / 2 - th / 2;
				if (tx < 0) { tx = X - lw; ty = Y - lw - th; }
				ty = Math.min(canvas.height - th, Math.max(0, ty));
				x.fillStyle = row.color;
				x.fillRect(tx, ty, tw, th);
				x.fillStyle = '#fff';
				x.fillText(String(row.n), tx + fs * 0.3, ty + fs * 0.95);
			});
		});
		x.globalAlpha = 1;
	}

	function showResult(s, img, r, photo, T) {
		var sexLabel = r.sexo === '0' ? T.male : (r.sexo === '1' ? T.female : '');
		var fields = [
			['nombre', T.nombre, r.nombre], ['paterno', T.paterno, r.paterno], ['materno', T.materno, r.materno],
			['fecha', T.fecha + ' (' + T.fmtDate + ')', dmy(r.fecha)], ['sexo', T.sexo, sexLabel], ['curp', T.curp, r.curp]
		];
		if (photo) { fields.push(['foto', T.foto, '']); }

		var rows = fields.map(function (f, i) {
			var key = f[0], src = r.src[key], boxes = [], where = '';
			var located = function (b) { return b ? '' : ' <span style="color:#bbb;">(' + esc(T.notMarked) + ')</span>'; };
			if (key === 'fecha' || key === 'sexo') {
				var chosen = src.filter(function (x) { return x.chosen; })[0];
				src.slice().sort(function (a, b) { return b.chosen - a.chosen; }).forEach(function (x) {
					if (x.box) { boxes.push({ box: x.box, dashed: !x.chosen }); }
					var mark = x.chosen ? '' : (chosen && x.value === chosen.value ? ' &#10003;' : ' <span style="color:#c62828;">&#8800; ' + esc(T.differs) + '</span>');
					where += '<div style="' + (x.chosen ? 'font-weight:600;color:#333;' : 'color:#777;') + '">' + esc(x.label) + ': ' + esc(x.raw)
						+ (x.fmt ? ' <span style="color:#999;font-weight:400;">(' + esc(x.fmt) + ')</span>' : '') + mark + located(x.box) + '</div>';
				});
			} else if (key === 'foto') {
				boxes.push({ box: photo.box, dashed: photo.estimated });
				where = esc(photo.estimated ? T.srcPhotoEst : T.srcPhoto);
			} else if (f[2] !== '') {
				if (src.box) { boxes.push({ box: src.box, dashed: false }); }
				where = esc(key === 'curp' ? T.srcCurp : T.srcName) + (key === 'curp' && src.fixed ? ' <span style="color:#999;">(' + esc(T.srcCurpFixed) + ')</span>' : '') + located(src.box);
			}
			return { key: key, n: i + 1, color: FIELD_COLORS[key], label: f[1], value: f[2], boxes: boxes, where: where };
		});

		var html = '<canvas class="pio-layout" style="display:block;max-width:100%;max-height:340px;margin:0 auto 4px;border-radius:6px;"></canvas>'
			+ '<div style="font-size:12px;color:#888;margin-bottom:8px;">' + esc(T.hoverHint) + '</div>'
			+ '<div style="display:flex;flex-wrap:wrap;gap:14px;align-items:flex-start;text-align:left;font-size:14px;">';
		if (photo) {
			html += '<label style="flex:0 0 110px;text-align:center;cursor:pointer;"><img src="' + photo.url + '" style="width:110px;border-radius:6px;border:2px solid ' + FIELD_COLORS.foto + ';"><br>'
				+ '<input type="checkbox" class="pio-apply" data-k="foto" checked> ' + badge(rows[rows.length - 1].n, FIELD_COLORS.foto) + esc(T.foto) + '</label>';
		}
		html += '<table style="flex:1 1 380px;border-collapse:collapse;">'
			+ '<tr style="font-size:12px;color:#999;"><td></td><td></td><td></td><td style="padding:2px 6px;">' + esc(T.source) + '</td></tr>';
		rows.forEach(function (row) {
			if (row.key === 'foto') { return; }
			var has = row.value !== '';
			html += '<tr data-k="' + row.key + '" style="border-bottom:1px solid #eee;cursor:default;"><td style="padding:4px 6px;width:24px;vertical-align:top;"><input type="checkbox" class="pio-apply" data-k="' + row.key + '"' + (has ? ' checked' : ' disabled') + '></td>'
				+ '<td style="padding:4px 6px;color:#777;white-space:nowrap;vertical-align:top;">' + badge(row.n, row.color) + esc(row.label) + '</td>'
				+ '<td style="padding:4px 6px;font-weight:600;word-break:break-all;vertical-align:top;">' + (has ? esc(row.value) : '<span style="color:#bbb;font-weight:400;">' + esc(T.none) + '</span>') + '</td>'
				+ '<td style="padding:4px 6px;font-size:12px;color:#555;vertical-align:top;">' + row.where + '</td></tr>';
		});
		html += '</table></div>';
		if (r.warnings.length) {
			html += '<div style="margin-top:10px;text-align:left;font-size:13px;color:#a15c00;background:#fff4e0;border-radius:6px;padding:6px 10px;">'
				+ r.warnings.map(function (w) { return '&#9888; ' + esc(w); }).join('<br>') + '</div>';
		}
		html += '<div style="margin-top:8px;font-size:12px;color:#777;">' + esc(T.review) + '</div>';

		Swal.fire({
			title: esc(T.title), html: html, width: 860, showCancelButton: true,
			confirmButtonText: esc(T.apply), cancelButtonText: esc(T.cancel), focusConfirm: false,
			didOpen: function (popup) {
				var canvas = $(popup).find('.pio-layout')[0], active = null;
				var scale = Math.min(1, 1000 / img.naturalWidth);
				canvas.width = Math.round(img.naturalWidth * scale);
				canvas.height = Math.round(img.naturalHeight * scale);
				var redraw = function (k) { active = k; drawLayout(canvas, img, rows, k); };
				redraw(null);
				$(popup).find('tr[data-k], label:has(.pio-apply[data-k="foto"])')
					.on('mouseenter', function () { redraw($(this).data('k') || 'foto'); })
					.on('mouseleave', function () { redraw(null); })
					.on('click', function (e) {
						if ($(e.target).is('input')) { return; }
						var k = $(this).data('k') || 'foto';
						redraw(active === k ? null : k);
					});
			},
			preConfirm: function () {
				var on = {};
				$(Swal.getHtmlContainer()).find('.pio-apply:checked').each(function () { on[$(this).data('k')] = true; });
				return on;
			}
		}).then(function (res) {
			if (!res.isConfirmed) { return; }
			var on = res.value || {};
			if (on.nombre) { setField($('#nombre' + s), r.nombre); }
			if (on.paterno) { setField($('#apellidop' + s), r.paterno); }
			if (on.materno) { setField($('#apellidom' + s), r.materno); }
			if (on.fecha) { setField($('#fechanac' + s), r.fecha); }
			if (on.sexo) { setField($('#sexo' + s), r.sexo); }
			if (on.curp) { setField($('#curp' + s), r.curp); }
			if (on.foto && photo && typeof playerPhotoLoadDataUrl === 'function') { playerPhotoLoadDataUrl(s, photo.url); }
		});
	}

	window.playerIdOcrRun = function (s, T) {
		var img = sourceImage(s);
		if (!img) {
			Swal.fire({ icon: 'info', title: '', text: T.needImage });
			return;
		}
		Swal.fire({ title: esc(T.reading), text: T.firstTime, allowOutsideClick: false, didOpen: function () { Swal.showLoading(); } });
		getWorker().then(function (worker) {
			var strong = prepCanvas(img, true), soft = prepCanvas(img, false), passes = [];
			return worker.recognize(strong.canvas).then(function (r1) {
				passes.push(parse(r1.data, strong.scale));
				var p = passes[0];
				if (p.curp && p.curp.valid && p.names.lines.length >= 2 && (p.printedDate || p.clave)) { return; }
				return worker.recognize(soft.canvas).then(function (r2) { passes.push(parse(r2.data, soft.scale)); });
			}).then(function () {
				return retryCurp(worker, passes, [strong, soft]);
			}).then(function () {
				var r = merge(passes, T);
				if (!r.curp && !r.nombre && !r.paterno && !r.fecha) {
					Swal.fire({ icon: 'warning', title: '', text: T.nothing });
					return;
				}
				showResult(s, img, r, cropPhoto(img, r.anchor), T);
			});
		}).catch(function (e) {
			console.log('playerIdOcr', e);
			Swal.fire({ icon: 'error', title: '', text: T.loadError });
		});
	};
})();
