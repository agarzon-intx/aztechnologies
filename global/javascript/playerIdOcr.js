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

	function findPrintedDate(text) {
		var re = /\b(\d{2})\s*[\/\-.]\s*(\d{2})\s*[\/\-.]\s*(\d{4})\b/g, r;
		while ((r = re.exec(text))) {
			var d = parseInt(r[1], 10), m = parseInt(r[2], 10), y = parseInt(r[3], 10);
			if (y > 1900 && validDate(y, m, d)) { return iso(y, m, d); }
		}
		return null;
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
			var words = [], prev = null;
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
				prev = wd;
			});
			if (words.length) { out.push(words.join(' ')); }
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
		var text = lines.map(function (l) { return l.text; }).join('\n');
		var sexM = text.match(/\bSEXO\s*[:.]?\s*([HM])\b/);
		var curpLabel = null;
		lines.forEach(function (l) {
			l.words.forEach(function (w) {
				if (!curpLabel && /^CURP$/.test(w.t)) { curpLabel = w; }
			});
		});
		return {
			curpLabel: curpLabel,
			curp: findCurp(text),
			clave: findClave(text),
			printedDate: findPrintedDate(text),
			printedSex: sexM ? sexM[1] : null,
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

	/** Combine the OCR passes and cross-check fields. */
	function merge(passes, T) {
		var pick = function (fn) {
			for (var i = 0; i < passes.length; i++) { var v = fn(passes[i]); if (v) { return v; } }
			return null;
		};
		var curp = pick(function (p) { return p.curp && p.curp.valid ? p.curp : null; }) || pick(function (p) { return p.curp; });
		var clave = pick(function (p) { return p.clave; });
		var names = pick(function (p) { return p.names.lines.length >= 2 ? p.names : null; }) || pick(function (p) { return p.names.lines.length ? p.names : null; });
		var anchor = pick(function (p) { return p.names.anchor; });
		var warnings = [], r = { nombre: '', paterno: '', materno: '', fecha: '', sexo: '', curp: '', anchor: anchor };

		if (curp) {
			r.curp = curp.value;
			if (!curp.valid) { warnings.push(T.curpCheck); }
		} else {
			warnings.push(T.noCurp);
		}

		var L = names ? names.lines : [];
		if (L.length >= 3) {
			r.paterno = L[0]; r.materno = L[1]; r.nombre = L.slice(2).join(' ');
		} else if (L.length === 2) {
			r.paterno = L[0];
			if (curp && curp.value.charAt(2) !== 'X' && L[1].charAt(0) === curp.value.charAt(2) && L[1].charAt(0) !== curp.value.charAt(3)) {
				r.materno = L[1];
			} else {
				r.nombre = L[1];
			}
		} else if (L.length === 1) {
			r.paterno = L[0];
		}
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
					curp = { value: fixed, valid: true };
					r.curp = fixed;
					warnings = warnings.filter(function (w) { return w !== T.curpCheck; });
					differs = false;
				}
			}
			if (curp.valid && differs) { warnings.push(T.nameMismatch); }
		}

		var printed = pick(function (p) { return p.printedDate; });
		var fromCurp = curp ? curpBirth(curp.value) : null;
		var fromClave = clave ? clave.birth : null;
		if (printed && (printed === fromCurp || printed === fromClave)) {
			r.fecha = printed;
		} else if (fromCurp && (curp.valid || fromCurp === fromClave)) {
			r.fecha = fromCurp;
			if (printed) { warnings.push(T.dateMismatch); }
		} else {
			r.fecha = printed || fromClave || fromCurp || '';
			if (r.fecha && (printed || fromClave || fromCurp) && [printed, fromClave, fromCurp].filter(Boolean).some(function (v) { return v !== r.fecha; })) {
				warnings.push(T.dateMismatch);
			}
		}

		var sex = (curp && curp.valid ? curp.value.charAt(10) : null) || pick(function (p) { return p.printedSex; }) || (clave ? clave.sex : null) || (curp ? curp.value.charAt(10) : null);
		if (sex === 'H') { r.sexo = '0'; } else if (sex === 'M') { r.sexo = '1'; } else if (sex === 'X') { warnings.push(T.sexX); }

		r.warnings = warnings;
		return r;
	}

	/** Face photo: left of the NOMBRE label, sized from the label height; falls back to the card layout. */
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
			return worker.recognize(c.canvas, { rectangle: rectFor(c) }).then(function (r) {
				var curp = findCurp(String(r.data.text || '').toUpperCase().replace(/CURP/g, ' '));
				if (curp) {
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

	function showResult(s, r, photo, T) {
		var sexLabel = r.sexo === '0' ? T.male : (r.sexo === '1' ? T.female : '');
		var fecha = r.fecha ? r.fecha.split('-').reverse().join('/') : '';
		var rows = [
			['nombre', T.nombre, r.nombre], ['paterno', T.paterno, r.paterno], ['materno', T.materno, r.materno],
			['fecha', T.fecha, fecha], ['sexo', T.sexo, sexLabel], ['curp', T.curp, r.curp]
		];
		var html = '<div style="display:flex;flex-wrap:wrap;gap:14px;align-items:flex-start;text-align:left;font-size:14px;">';
		if (photo) {
			html += '<label style="flex:0 0 120px;text-align:center;cursor:pointer;"><img src="' + photo + '" style="width:120px;border-radius:6px;border:1px solid #ddd;"><br>'
				+ '<input type="checkbox" class="pio-apply" data-k="foto" checked> ' + esc(T.foto) + '</label>';
		}
		html += '<table style="flex:1 1 260px;border-collapse:collapse;">';
		rows.forEach(function (row) {
			var has = row[2] !== '';
			html += '<tr style="border-bottom:1px solid #eee;"><td style="padding:4px 6px;width:24px;"><input type="checkbox" class="pio-apply" data-k="' + row[0] + '"' + (has ? ' checked' : ' disabled') + '></td>'
				+ '<td style="padding:4px 6px;color:#777;white-space:nowrap;">' + esc(row[1]) + '</td>'
				+ '<td style="padding:4px 6px;font-weight:600;word-break:break-all;">' + (has ? esc(row[2]) : '<span style="color:#bbb;font-weight:400;">' + esc(T.none) + '</span>') + '</td></tr>';
		});
		html += '</table></div>';
		if (r.warnings.length) {
			html += '<div style="margin-top:10px;text-align:left;font-size:13px;color:#a15c00;background:#fff4e0;border-radius:6px;padding:6px 10px;">'
				+ r.warnings.map(function (w) { return '&#9888; ' + esc(w); }).join('<br>') + '</div>';
		}
		html += '<div style="margin-top:8px;font-size:12px;color:#777;">' + esc(T.review) + '</div>';

		Swal.fire({
			title: esc(T.title), html: html, width: 620, showCancelButton: true,
			confirmButtonText: esc(T.apply), cancelButtonText: esc(T.cancel), focusConfirm: false,
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
			if (on.foto && photo && typeof playerPhotoLoadDataUrl === 'function') { playerPhotoLoadDataUrl(s, photo); }
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
				showResult(s, r, cropPhoto(img, r.anchor), T);
			});
		}).catch(function (e) {
			console.log('playerIdOcr', e);
			Swal.fire({ icon: 'error', title: '', text: T.loadError });
		});
	};
})();
