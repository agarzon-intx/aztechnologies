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

	/** Grayscale in place over a canvas area; strong=true also stretches contrast around the area's mean. */
	function toGray(x, left, top, w, h, strong) {
		var d = x.getImageData(left, top, w, h), p = d.data, i, sum = 0;
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
		x.putImageData(d, left, top);
	}

	/** Grayscale copy scaled to ~1600px wide. */
	function prepCanvas(img, strong) {
		var scale = Math.max(1, Math.min(3, 1600 / img.naturalWidth));
		var c = document.createElement('canvas');
		c.width = Math.round(img.naturalWidth * scale);
		c.height = Math.round(img.naturalHeight * scale);
		var x = c.getContext('2d');
		x.imageSmoothingQuality = 'high';
		x.drawImage(img, 0, 0, c.width, c.height);
		toGray(x, 0, 0, c.width, c.height, strong);
		return { canvas: c, scale: scale };
	}

	/** One area of the image (box in image pixels), enlarged so the text is at least ~40px tall, on a white margin. */
	function regionCanvas(img, box, strong) {
		var sx = Math.max(0, box.x0), sy = Math.max(0, box.y0);
		var sw = Math.min(img.naturalWidth, box.x1) - sx, sh = Math.min(img.naturalHeight, box.y1) - sy;
		var scale = Math.max(1, Math.min(3, 1600 / img.naturalWidth));
		if (sh * scale < 40) { scale = Math.min(8, 40 / sh); }
		var m = 16, w = Math.round(sw * scale), h = Math.round(sh * scale);
		var c = document.createElement('canvas');
		c.width = w + 2 * m; c.height = h + 2 * m;
		var x = c.getContext('2d');
		x.fillStyle = '#fff';
		x.fillRect(0, 0, c.width, c.height);
		x.imageSmoothingQuality = 'high';
		x.drawImage(img, sx, sy, sw, sh, m, m, w, h);
		toGray(x, m, m, w, h, strong);
		return c;
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
		r.src.curp = { box: curp ? curp.box : null, fixed: !!(curp && curp.fixed), valid: !!(curp && curp.valid) };

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
		var box = { x0: x0, y0: y0, x1: x0 + w, y1: y0 + h };
		return { url: cropBox(img, box), box: box, estimated: !anchor };
	}

	function cropBox(img, box) {
		var c = document.createElement('canvas');
		c.width = Math.max(1, Math.round(box.x1 - box.x0)); c.height = Math.max(1, Math.round(box.y1 - box.y0));
		c.getContext('2d').drawImage(img, box.x0, box.y0, box.x1 - box.x0, box.y1 - box.y0, 0, 0, c.width, c.height);
		return c.toDataURL('image/png');
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

	var FIELD_KEYS = ['nombre', 'paterno', 'materno', 'fecha', 'sexo', 'curp', 'foto'];
	var FIELD_COLORS = { nombre: '#d81b60', paterno: '#1e88e5', materno: '#43a047', fecha: '#ef6c00', sexo: '#8e24aa', curp: '#00897b', foto: '#6d4c41' };
	var WHITELIST = {
		name: 'ABCDEFGHIJKLMNOPQRSTUVWXYZÁÉÍÓÚÑÜ ',
		key: 'ABCDEFGHIJKLMNÑOPQRSTUVWXYZ0123456789',
		mixed: 'ABCDEFGHIJKLMNÑOPQRSTUVWXYZ0123456789/-.: '
	};

	function dmy(isoDate) { return isoDate ? isoDate.split('-').reverse().join('/') : ''; }

	/** "DE LA ROSA" -> "De La Rosa" (also after hyphens and apostrophes). */
	function titleCase(s) {
		return String(s || '').toLowerCase().replace(/(^|[\s\-'])(\S)/g, function (m, sep, c) { return sep + c.toUpperCase(); });
	}

	function badge(n, color) {
		return '<span style="display:inline-block;min-width:18px;height:18px;line-height:18px;border-radius:9px;background:' + color
			+ ';color:#fff;font-size:11px;font-weight:700;text-align:center;margin-right:4px;">' + n + '</span>';
	}

	/* ---------- reading one marked area ---------- */

	/** Value of a field from the text of a marked area, or null. */
	function parseField(k, text) {
		var t = String(text || '').toUpperCase();
		if (k === 'curp') {
			var c = findCurp(t) || findCurp(t.replace(/\s+/g, ''));
			if (c) { return c; }
			var raw = t.replace(/[^A-Z0-9Ñ]/g, '');
			return raw.length >= 10 ? { value: raw.slice(0, 18), valid: false } : null;
		}
		if (k === 'fecha') {
			var d = findPrintedDate(t);
			if (d) { return { value: d.iso, raw: d.raw, fmt: 'dmy' }; }
			var key = findCurp(t.replace(/\s+/g, ''));
			if (key && curpBirth(key.value)) { return { value: curpBirth(key.value), raw: key.value.substr(4, 6), fmt: 'ymd' }; }
			var cl = findClave(t.replace(/\s+/g, ''));
			if (cl && cl.birth) { return { value: cl.birth, raw: cl.value.substr(6, 6), fmt: 'ymd' }; }
			var digits = t.replace(/[^0-9]/g, ''), y, m, dd;
			if (digits.length === 8) {
				dd = parseInt(digits.substr(0, 2), 10); m = parseInt(digits.substr(2, 2), 10); y = parseInt(digits.substr(4, 4), 10);
				if (y > 1900 && validDate(y, m, dd)) { return { value: iso(y, m, dd), raw: digits.substr(0, 2) + '/' + digits.substr(2, 2) + '/' + digits.substr(4), fmt: 'dmy' }; }
			}
			if (digits.length === 6) {
				var yy = parseInt(digits.substr(0, 2), 10);
				y = (yy > new Date().getFullYear() % 100 ? 1900 : 2000) + yy; m = parseInt(digits.substr(2, 2), 10); dd = parseInt(digits.substr(4, 2), 10);
				if (validDate(y, m, dd)) { return { value: iso(y, m, dd), raw: digits, fmt: 'ymd' }; }
			}
			return null;
		}
		if (k === 'sexo') {
			var sm = t.match(/SEXO\W*([HMX])\b/);
			if (sm) { return { value: sm[1] }; }
			var kc = findCurp(t.replace(/\s+/g, ''));
			if (kc) { return { value: kc.value.charAt(10) }; }
			var kl = findClave(t.replace(/\s+/g, ''));
			if (kl) { return { value: kl.sex }; }
			var letters = t.replace(/[^A-Z]/g, '');
			if (/^[HMX]$/.test(letters)) { return { value: letters }; }
			var one = t.match(/\b([HMX])\b/);
			return one ? { value: one[1] } : null;
		}
		var words = t.replace(/[^A-ZÁÉÍÓÚÑÜ\s]/g, ' ').split(/\s+/).filter(function (w) {
			return (w.length >= 2 || w === 'Y') && w !== 'NOMBRE' && !NAME_STOP.test(w);
		});
		return words.length ? { value: words.join(' ') } : null;
	}

	/** OCR of one area with a whitelist for the field; tries a soft then a high-contrast copy. */
	function readRegion(worker, img, box, k) {
		var wl = k === 'curp' ? WHITELIST.key : (k === 'fecha' || k === 'sexo' ? WHITELIST.mixed : WHITELIST.name);
		var once = function (strong) {
			return worker.recognize(regionCanvas(img, box, strong)).then(function (res) { return String(res.data.text || ''); });
		};
		var restore = function () { return worker.setParameters({ tessedit_char_whitelist: '' }); };
		return worker.setParameters({ tessedit_char_whitelist: wl })
			.then(function () { return once(false); })
			.then(function (t) { return parseField(k, t) ? t : once(true).then(function (t2) { return parseField(k, t2) ? t2 : t; }); })
			.then(function (t) { return restore().then(function () { return t; }); },
				function (e) { return restore().then(function () { throw e; }); });
	}

	/* ---------- result dialog ---------- */

	/**
	 * Data read, with a picture of the ID where each field has a numbered box. A field's box can be drawn,
	 * moved or resized on the picture; that area is then read again and replaces the value.
	 */
	function showResult(s, img, r, photo, T, worker) {
		photo = photo || { url: '', box: null, estimated: false };
		var labels = { nombre: T.nombre, paterno: T.paterno, materno: T.materno, fecha: T.fecha + ' (' + T.fmtDate + ')', sexo: T.sexo, curp: T.curp, foto: T.foto };
		var num = {}, seq = {}, pending = {}, queue = Promise.resolve(), edited = false;
		FIELD_KEYS.forEach(function (k, i) { num[k] = i + 1; });
		var selected = null, hover = null, drag = null, canvas = null, $pop = null;

		var multi = function (k) { return k === 'fecha' || k === 'sexo'; };
		var chosenOf = function (k) { return r.src[k].filter(function (x) { return x.chosen; })[0] || null; };
		var fmtLabel = function (f) { return f === 'dmy' ? T.fmtDate : (f === 'ymd' ? T.fmtYmd : f || ''); };
		r.src.fecha.forEach(function (x) { x.fmt = x.key === 'printed' ? 'dmy' : 'ymd'; });

		function primaryBox(k) {
			if (k === 'foto') { return photo.box; }
			if (multi(k)) { var c = chosenOf(k); return c ? c.box : null; }
			return r.src[k].box;
		}
		function setPrimaryBox(k, b) {
			if (k === 'foto') { photo.box = b; } else if (multi(k)) { chosenOf(k).box = b; } else { r.src[k].box = b; }
		}
		function boxesOf(k) {
			if (multi(k)) {
				return r.src[k].filter(function (x) { return x.box; }).map(function (x) { return { box: x.box, dashed: !x.chosen }; });
			}
			var b = primaryBox(k);
			return b ? [{ box: b, dashed: k === 'foto' && photo.estimated }] : [];
		}
		function hasValue(k) {
			if (k === 'foto') { return !!photo.url; }
			if (k === 'sexo') { return r.sexo !== ''; }
			return !!r[k];
		}
		function valueHtml(k) {
			if (pending[k]) { return '<span style="color:#999;font-weight:400;">' + esc(T.rereading) + '</span>'; }
			if (!hasValue(k)) { return '<span style="color:#bbb;font-weight:400;">' + esc(T.none) + '</span>'; }
			if (k === 'foto') { return '<img src="' + photo.url + '" style="max-width:70px;max-height:90px;border-radius:4px;border:2px solid ' + FIELD_COLORS.foto + ';">'; }
			if (k === 'fecha') { return esc(dmy(r.fecha)); }
			if (k === 'sexo') { return esc(r.sexo === '0' ? T.male : T.female); }
			return esc(k === 'curp' ? r[k] : titleCase(r[k]));
		}
		function whereHtml(k) {
			var located = function (b) { return b ? '' : ' <span style="color:#bbb;">(' + esc(T.notMarked) + ')</span>'; };
			if (multi(k)) {
				var chosen = chosenOf(k), out = '';
				r.src[k].slice().sort(function (a, b) { return b.chosen - a.chosen; }).forEach(function (x) {
					var mark = x.chosen || !chosen ? '' : (x.value === chosen.value ? ' &#10003;' : ' <span style="color:#c62828;">&#8800; ' + esc(T.differs) + '</span>');
					out += '<div style="' + (x.chosen ? 'font-weight:600;color:#333;' : 'color:#777;') + '">' + esc(x.label) + ': ' + (x.raw ? esc(x.raw) : esc(T.none))
						+ (x.raw && x.fmt ? ' <span style="color:#999;font-weight:400;">(' + esc(fmtLabel(x.fmt)) + ')</span>' : '') + mark + located(x.box) + '</div>';
				});
				return out;
			}
			if (k === 'foto') {
				return photo.box ? esc(photo.manual ? T.srcManual : (photo.estimated ? T.srcPhotoEst : T.srcPhoto)) : '';
			}
			var src = r.src[k];
			if (src.manual) { return esc(T.srcManual) + located(src.box); }
			if (!r[k]) { return ''; }
			return esc(k === 'curp' ? T.srcCurp : T.srcName) + (k === 'curp' && src.fixed ? ' <span style="color:#999;">(' + esc(T.srcCurpFixed) + ')</span>' : '') + located(src.box);
		}

		function computeWarnings() {
			var w = [];
			if (!r.curp) { w.push(T.noCurp); } else if (!r.src.curp.valid) { w.push(T.curpCheck); }
			if (!r.nombre || !r.paterno) { w.push(T.noNames); }
			var fn = curpFromNames(r.paterno, r.materno, r.nombre);
			if (r.curp && r.src.curp.valid && fn && Object.keys(fn).some(function (i) { return r.curp.charAt(i) !== fn[i]; })) { w.push(T.nameMismatch); }
			var cf = chosenOf('fecha');
			if (cf && r.src.fecha.some(function (x) { return x.value && x.value !== cf.value; })) { w.push(T.dateMismatch); }
			var cs = chosenOf('sexo');
			if (cs && cs.value === 'X') { w.push(T.sexX); }
			return w;
		}

		/** After the CURP changes, refresh the birth date / sex entries taken from it. */
		function refreshCurpSources() {
			var ok = /^[A-Z]{4}\d{6}[HMX]/.test(r.curp), box = r.src.curp.box;
			var fromCurp = ok && r.curp.length === 18 ? curpBirth(r.curp) : null;
			[['fecha', fromCurp ? { label: T.srcCurpDate, raw: r.curp.substr(4, 6), fmt: 'ymd', value: fromCurp } : null],
				['sexo', ok ? { label: T.srcCurpSex, raw: r.curp.charAt(10), value: r.curp.charAt(10) } : null]].forEach(function (pair) {
				var k = pair[0], list = r.src[k], cur = list.filter(function (x) { return x.key === 'curp'; })[0];
				if (!pair[1]) {
					if (cur) { list.splice(list.indexOf(cur), 1); }
				} else {
					if (!cur) { cur = { key: 'curp', chosen: false }; list.push(cur); }
					$.extend(cur, pair[1], { box: box });
				}
				if (!chosenOf(k) && list.length) { list[0].chosen = true; }
				syncChosen(k);
			});
		}
		function syncChosen(k) {
			var c = chosenOf(k);
			if (k === 'fecha') { r.fecha = c ? c.value || '' : ''; }
			if (k === 'sexo') { var v = c ? c.value : ''; r.sexo = v === 'H' ? '0' : (v === 'M' ? '1' : ''); }
		}

		/** First change to a field's box: it becomes a manual source. */
		function makeManual(k) {
			if (k === 'foto') { photo.manual = true; photo.estimated = false; return; }
			if (multi(k)) {
				var c = chosenOf(k);
				if (c && c.key === 'manual') { return; }
				r.src[k].forEach(function (x) { x.chosen = false; });
				r.src[k] = r.src[k].filter(function (x) { return x.key !== 'manual'; });
				r.src[k].unshift({ key: 'manual', label: T.srcManual, raw: '', fmt: '', value: '', box: c && c.box ? $.extend({}, c.box) : null, chosen: true });
				return;
			}
			r.src[k].manual = true;
			r.src[k].fixed = false;
		}

		function applyRead(k, text) {
			var p = parseField(k, text);
			if (k === 'curp') {
				r.curp = p ? p.value : '';
				r.src.curp.valid = !!(p && p.valid);
				refreshCurpSources();
			} else if (multi(k)) {
				var c = chosenOf(k);
				c.value = p ? p.value : '';
				c.raw = p ? (k === 'fecha' ? p.raw : p.value) : '';
				c.fmt = p && k === 'fecha' ? p.fmt : '';
				syncChosen(k);
			} else {
				r[k] = p ? p.value : '';
			}
		}

		function reread(k) {
			var b = primaryBox(k);
			if (!b || b.x1 - b.x0 < 4 || b.y1 - b.y0 < 4) { return; }
			b = $.extend({}, b);
			edited = true;
			if (k === 'foto') {
				photo.url = cropBox(img, b);
				refreshRow(k, true);
				redraw();
				return;
			}
			var my = seq[k] = (seq[k] || 0) + 1;
			pending[k] = true;
			refreshRow(k);
			queue = queue.then(function () { return readRegion(worker, img, b, k); }).then(function (text) {
				if (seq[k] === my) { applyRead(k, text); }
			}).catch(function (e) {
				console.log('playerIdOcr reread', e);
			}).then(function () {
				if (seq[k] !== my) { return; }
				pending[k] = false;
				FIELD_KEYS.forEach(function (key) { refreshRow(key, key === k); });
				refreshWarnings();
				redraw();
			});
		}

		/* ---- rendering ---- */

		function refreshRow(k, check) {
			if (!$pop) { return; }
			var $tr = $pop.find('tr[data-k="' + k + '"]'), has = hasValue(k) && !pending[k];
			var $cb = $tr.find('.pio-apply').prop('disabled', !has);
			if (!has) { $cb.prop('checked', false); } else if (check) { $cb.prop('checked', true); }
			$tr.find('.pio-val').html(valueHtml(k));
			$tr.find('.pio-where').html(whereHtml(k));
			$tr.css('background', selected === k ? '#eef3ff' : '');
		}
		function refreshWarnings() {
			if (!$pop) { return; }
			var w = edited ? computeWarnings() : r.warnings;
			$pop.find('.pio-warn').toggle(w.length > 0).html(w.map(function (x) { return '&#9888; ' + esc(x); }).join('<br>'));
		}
		function refreshHint() {
			if (!$pop) { return; }
			$pop.find('.pio-hint').text(selected ? T.editHint.replace('%1', labels[selected].replace(/\s*\(.*\)$/, '')) : T.hoverHint);
			$pop.find('.pio-mark').each(function () {
				var on = $(this).closest('tr').data('k') === selected;
				$(this).css({ background: on ? FIELD_COLORS[selected] : '#fff', color: on ? '#fff' : '#555' });
			});
			if (canvas) { canvas.style.cursor = selected ? 'crosshair' : 'default'; }
		}

		function redraw() {
			if (!canvas) { return; }
			var ratio = canvas.width / (canvas.clientWidth || canvas.width);
			var x = canvas.getContext('2d'), k = canvas.width / img.naturalWidth, lw = 2 * ratio, fs = Math.round(13 * ratio);
			var active = selected || hover;
			x.clearRect(0, 0, canvas.width, canvas.height);
			x.drawImage(img, 0, 0, canvas.width, canvas.height);
			FIELD_KEYS.forEach(function (key) {
				var color = FIELD_COLORS[key], on = !active || active === key;
				boxesOf(key).forEach(function (bx) {
					var X = bx.box.x0 * k, Y = bx.box.y0 * k, W = (bx.box.x1 - bx.box.x0) * k, H = (bx.box.y1 - bx.box.y0) * k;
					x.globalAlpha = on ? 1 : 0.2;
					x.lineWidth = on && active ? lw * 1.6 : lw;
					x.strokeStyle = color;
					x.setLineDash(bx.dashed ? [lw * 3, lw * 2] : []);
					x.strokeRect(X - lw, Y - lw, W + 2 * lw, H + 2 * lw);
					x.setLineDash([]);
					if (!bx.dashed && selected === key) {
						var hs = lw * 4;
						x.fillStyle = color;
						x.fillRect(X + W + lw - hs / 2, Y + H + lw - hs / 2, hs, hs);
					}
					// date and sex boxes are often slices of the CURP / voter key: tag below so the key stays readable
					x.font = 'bold ' + fs + 'px sans-serif';
					var tw = x.measureText(String(num[key])).width + fs * 0.6, th = fs * 1.2, below = multi(key);
					var tx = below ? X - lw : X - 2 * lw - tw, ty = below ? Y + H + lw : Y + H / 2 - th / 2;
					if (tx < 0) { tx = X - lw; ty = Y - lw - th; }
					ty = Math.min(canvas.height - th, Math.max(0, ty));
					x.fillStyle = color;
					x.fillRect(tx, ty, tw, th);
					x.fillStyle = '#fff';
					x.fillText(String(num[key]), tx + fs * 0.3, ty + fs * 0.95);
				});
			});
			x.globalAlpha = 1;
		}

		function select(k) {
			selected = k;
			hover = null;
			FIELD_KEYS.forEach(function (key) { refreshRow(key); });
			refreshHint();
			redraw();
		}

		/* ---- zoom: 1 = whole card fits; the stage scrolls when zoomed in ---- */

		var zoom = 1, stage = null, ZOOM_MAX = 5;
		function fitWidth() {
			return Math.min(stage.clientWidth, Math.max(340, 0.5 * window.innerHeight) * img.naturalWidth / img.naturalHeight);
		}
		/** Zoom keeping the image point `focus` (default: the centre of the view) at `at` in the stage (default: its middle). */
		function setZoom(z, focus, at) {
			z = Math.max(1, Math.min(ZOOM_MAX, z));
			if (!focus) {
				var sr = stage.getBoundingClientRect(), cr = canvas.getBoundingClientRect();
				focus = { x: (sr.left + stage.clientWidth / 2 - cr.left) / cr.width * img.naturalWidth, y: (sr.top + stage.clientHeight / 2 - cr.top) / cr.height * img.naturalHeight };
			}
			at = at || { x: stage.clientWidth / 2, y: stage.clientHeight / 2 };
			zoom = z;
			canvas.style.width = Math.round(fitWidth() * zoom) + 'px';
			stage.scrollLeft = focus.x / img.naturalWidth * canvas.clientWidth + canvas.offsetLeft - at.x;
			stage.scrollTop = focus.y / img.naturalHeight * canvas.clientHeight + canvas.offsetTop - at.y;
			$pop.find('.pio-zoom-val').text(Math.round(zoom * 100) + '%');
			redraw();
		}
		/** Zoom in on a field's box when it is selected for marking. */
		function focusField(k) {
			var b = k && primaryBox(k);
			if (!b) { return; }
			var fit = Math.min(ZOOM_MAX, 0.5 * img.naturalWidth / Math.max(1, b.x1 - b.x0));
			setZoom(Math.max(zoom, Math.min(2.5, fit)), { x: (b.x0 + b.x1) / 2, y: (b.y0 + b.y1) / 2 });
		}

		/* ---- pointer: draw / move / resize ---- */

		function toImg(e) {
			var rc = canvas.getBoundingClientRect();
			return { x: (e.clientX - rc.left) * img.naturalWidth / rc.width, y: (e.clientY - rc.top) * img.naturalHeight / rc.height, u: img.naturalWidth / rc.width };
		}
		function hitBox(k, p) {
			var b = primaryBox(k), hs = 10 * p.u;
			if (!b) { return null; }
			if (Math.abs(p.x - b.x1) <= hs && Math.abs(p.y - b.y1) <= hs) { return 'resize'; }
			return p.x >= b.x0 && p.x <= b.x1 && p.y >= b.y0 && p.y <= b.y1 ? 'move' : null;
		}
		function hitAny(p) {
			for (var i = 0; i < FIELD_KEYS.length; i++) {
				var mode = hitBox(FIELD_KEYS[i], p);
				if (mode) { return { k: FIELD_KEYS[i], mode: mode }; }
			}
			return null;
		}
		function clampBox(b) {
			var W = img.naturalWidth, H = img.naturalHeight;
			var x0 = Math.max(0, Math.min(b.x0, b.x1)), x1 = Math.min(W, Math.max(b.x0, b.x1));
			var y0 = Math.max(0, Math.min(b.y0, b.y1)), y1 = Math.min(H, Math.max(b.y0, b.y1));
			return { x0: x0, y0: y0, x1: x1, y1: y1 };
		}

		function onDown(e) {
			var p = toImg(e), mode = selected ? hitBox(selected, p) : null, k = selected;
			if (!mode && !selected) {
				var h = hitAny(p);
				if (!h) {
					if (zoom > 1) {
						drag = { mode: 'pan', cx: e.clientX, cy: e.clientY, sl: stage.scrollLeft, st: stage.scrollTop };
						try { canvas.setPointerCapture(e.pointerId); } catch (ignore) { }
						canvas.style.cursor = 'grabbing';
						e.preventDefault();
					}
					return;
				}
				k = h.k; mode = h.mode;
				select(k);
			}
			drag = { k: k, mode: mode || 'draw', start: p, orig: primaryBox(k) ? $.extend({}, primaryBox(k)) : null, started: false };
			try { canvas.setPointerCapture(e.pointerId); } catch (ignore) { }
			e.preventDefault();
		}
		function onMove(e) {
			var p = toImg(e);
			if (drag && drag.mode === 'pan') {
				stage.scrollLeft = drag.sl - (e.clientX - drag.cx);
				stage.scrollTop = drag.st - (e.clientY - drag.cy);
				return;
			}
			if (!drag) {
				var m = selected ? hitBox(selected, p) : (hitAny(p) ? 'move' : null);
				canvas.style.cursor = m === 'resize' ? 'nwse-resize' : (m === 'move' ? 'move' : (selected ? 'crosshair' : (zoom > 1 ? 'grab' : 'default')));
				return;
			}
			if (!drag.started) {
				if (Math.abs(p.x - drag.start.x) + Math.abs(p.y - drag.start.y) < 4 * p.u) { return; }
				drag.started = true;
				makeManual(drag.k);
			}
			var o = drag.orig, b;
			if (drag.mode === 'move') {
				var dx = Math.max(-o.x0, Math.min(img.naturalWidth - o.x1, p.x - drag.start.x));
				var dy = Math.max(-o.y0, Math.min(img.naturalHeight - o.y1, p.y - drag.start.y));
				b = { x0: o.x0 + dx, y0: o.y0 + dy, x1: o.x1 + dx, y1: o.y1 + dy };
			} else if (drag.mode === 'resize') {
				b = clampBox({ x0: o.x0, y0: o.y0, x1: Math.max(o.x0 + 6 * p.u, p.x), y1: Math.max(o.y0 + 6 * p.u, p.y) });
			} else {
				b = clampBox({ x0: drag.start.x, y0: drag.start.y, x1: p.x, y1: p.y });
			}
			setPrimaryBox(drag.k, b);
			redraw();
		}
		function onUp(e) {
			if (!drag) { return; }
			var d = drag;
			drag = null;
			try { canvas.releasePointerCapture(e.pointerId); } catch (ignore) { }
			if (d.mode === 'pan') {
				canvas.style.cursor = 'grab';
				return;
			}
			if (d.started) {
				reread(d.k);
				return;
			}
			if (d.mode === 'draw') {
				// a click (no drag) outside the selected box picks another field, or clears the selection
				var h = hitAny(d.start);
				select(h ? h.k : null);
			}
		}

		/* ---- dialog ---- */

		var zbtn = function (cls, title, label) {
			return '<button type="button" class="' + cls + '" title="' + esc(title) + '" style="border:1px solid #ccc;background:#fff;border-radius:4px;min-width:30px;padding:1px 8px;font-size:14px;cursor:pointer;">' + label + '</button>';
		};
		var html = '<div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;margin-bottom:4px;font-size:12px;color:#777;">'
			+ '<span style="margin-right:auto;text-align:left;">' + esc(T.zoomHint) + '</span>'
			+ zbtn('pio-zoom-out', T.zoomOut, '&minus;') + '<span class="pio-zoom-val" style="min-width:40px;text-align:center;">100%</span>' + zbtn('pio-zoom-in', T.zoomIn, '+')
			+ zbtn('pio-zoom-fit', T.zoomFit, esc(T.zoomFit)) + '</div>'
			+ '<div class="pio-stage" style="position:relative;overflow:auto;max-height:60vh;background:#f2f2f2;border-radius:6px;margin-bottom:4px;">'
			+ '<canvas class="pio-layout" style="display:block;margin:0 auto;touch-action:none;"></canvas></div>'
			+ '<div class="pio-hint" style="font-size:12px;color:#666;margin-bottom:8px;min-height:16px;"></div>'
			+ '<table style="width:100%;border-collapse:collapse;text-align:left;font-size:14px;">'
			+ '<tr style="font-size:12px;color:#999;"><td></td><td></td><td></td><td style="padding:2px 6px;">' + esc(T.source) + '</td><td></td></tr>';
		FIELD_KEYS.forEach(function (k) {
			html += '<tr data-k="' + k + '" style="border-bottom:1px solid #eee;cursor:pointer;">'
				+ '<td style="padding:4px 6px;width:24px;vertical-align:top;"><input type="checkbox" class="pio-apply" data-k="' + k + '"' + (hasValue(k) ? ' checked' : ' disabled') + '></td>'
				+ '<td style="padding:4px 6px;color:#777;white-space:nowrap;vertical-align:top;">' + badge(num[k], FIELD_COLORS[k]) + esc(labels[k]) + '</td>'
				+ '<td class="pio-val" style="padding:4px 6px;font-weight:600;word-break:break-all;vertical-align:top;"></td>'
				+ '<td class="pio-where" style="padding:4px 6px;font-size:12px;color:#555;vertical-align:top;"></td>'
				+ '<td style="padding:4px 6px;vertical-align:top;text-align:right;"><button type="button" class="pio-mark" style="border:1px solid #ccc;border-radius:4px;padding:1px 8px;font-size:12px;cursor:pointer;white-space:nowrap;">'
				+ '&#8982; ' + esc(T.mark) + '</button></td></tr>';
		});
		html += '</table>'
			+ '<div class="pio-warn" style="margin-top:10px;text-align:left;font-size:13px;color:#a15c00;background:#fff4e0;border-radius:6px;padding:6px 10px;"></div>'
			+ '<div style="margin-top:8px;font-size:12px;color:#777;">' + esc(T.review) + '</div>';

		Swal.fire({
			title: esc(T.title), html: html, width: 900, showCancelButton: true,
			confirmButtonText: esc(T.apply), cancelButtonText: esc(T.cancel), focusConfirm: false,
			didOpen: function (popup) {
				$pop = $(popup);
				canvas = $pop.find('.pio-layout')[0];
				stage = $pop.find('.pio-stage')[0];
				var scale = Math.min(1, 2000 / img.naturalWidth);
				canvas.width = Math.round(img.naturalWidth * scale);
				canvas.height = Math.round(img.naturalHeight * scale);
				canvas.addEventListener('pointerdown', onDown);
				canvas.addEventListener('pointermove', onMove);
				canvas.addEventListener('pointerup', onUp);
				canvas.addEventListener('pointercancel', onUp);
				canvas.style.width = Math.round(fitWidth()) + 'px';
				$pop.find('.pio-zoom-in').on('click', function () { setZoom(zoom * 1.5); });
				$pop.find('.pio-zoom-out').on('click', function () { setZoom(zoom / 1.5); });
				$pop.find('.pio-zoom-fit').on('click', function () { setZoom(1); });
				// trackpad pinch arrives as wheel + ctrlKey; plain wheel keeps scrolling
				stage.addEventListener('wheel', function (e) {
					if (!e.ctrlKey && !e.metaKey) { return; }
					e.preventDefault();
					var sr = stage.getBoundingClientRect();
					setZoom(zoom * Math.exp(-e.deltaY / 200), toImg(e), { x: e.clientX - sr.left, y: e.clientY - sr.top });
				}, { passive: false });
				$(window).on('resize.pio', function () { setZoom(zoom); });
				$pop.find('tr[data-k]')
					.on('mouseenter', function () { if (!selected) { hover = $(this).data('k'); redraw(); } })
					.on('mouseleave', function () { if (!selected) { hover = null; redraw(); } })
					.on('click', function (e) {
						if ($(e.target).is('input')) { return; }
						var k = $(this).data('k');
						select(selected === k ? null : k);
						if (selected) { focusField(selected); }
					});
				FIELD_KEYS.forEach(function (k) { refreshRow(k); });
				refreshWarnings();
				refreshHint();
				redraw();
			},
			willClose: function () { $(window).off('resize.pio'); },
			preConfirm: function () {
				var on = {};
				$(Swal.getHtmlContainer()).find('.pio-apply:checked').each(function () { on[$(this).data('k')] = true; });
				return on;
			}
		}).then(function (res) {
			if (!res.isConfirmed) { return; }
			var on = res.value || {};
			if (on.nombre) { setField($('#nombre' + s), titleCase(r.nombre)); }
			if (on.paterno) { setField($('#apellidop' + s), titleCase(r.paterno)); }
			if (on.materno) { setField($('#apellidom' + s), titleCase(r.materno)); }
			if (on.fecha) { setField($('#fechanac' + s), r.fecha); }
			if (on.sexo) { setField($('#sexo' + s), r.sexo); }
			if (on.curp) { setField($('#curp' + s), r.curp); }
			if (on.foto && photo.url && typeof playerPhotoLoadDataUrl === 'function') { playerPhotoLoadDataUrl(s, photo.url); }
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
				showResult(s, img, r, cropPhoto(img, r.anchor), T, worker);
			});
		}).catch(function (e) {
			console.log('playerIdOcr', e);
			Swal.fire({ icon: 'error', title: '', text: T.loadError });
		});
	};
})();
