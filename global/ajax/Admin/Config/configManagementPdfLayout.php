<?php
	require_once dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'pdf_layout.php';
	require_once dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'flyer_text_style.inc.php';

	$pl = function ($key, $fallback) use ($lang) {
		return isset($lang[$key]) ? $lang[$key] : $fallback;
	};
	$plSiteRoot = rtrim((string) $Config->getPath(), '/\\');
	$plSchema = $Config->getSchema();

	$plSample = array('Juego_ID' => '', 'Jornada_ID' => '', 'Torneo_ID' => '', 'Categoria_ID' => '', 'Equipo_ID' => '');
	$plRes = $Config->query("SELECT j.Juego_ID, j.Jornada_ID, j.Torneo_ID, l.Fuerza AS Categoria_ID, j.Local_ID
		FROM $plSchema.Juegos j JOIN $plSchema.Equipos l ON l.Equipo_ID = j.Local_ID
		WHERE j.Local_ID > 0 AND j.Visitante_ID > 0
		ORDER BY j.Fecha DESC, j.Juego_ID DESC LIMIT 1");
	if ($plRes && ($plRow = $plRes->fetch_assoc())) {
		$plSample = array(
			'Juego_ID' => (int) $plRow['Juego_ID'],
			'Jornada_ID' => (int) $plRow['Jornada_ID'],
			'Torneo_ID' => (int) $plRow['Torneo_ID'],
			'Categoria_ID' => (int) $plRow['Categoria_ID'],
			'Equipo_ID' => (int) $plRow['Local_ID'],
		);
	}
	$plWeekQs = 'Torneo_ID=' . $plSample['Torneo_ID'] . '&Jornada_ID=' . $plSample['Jornada_ID'] . '&Categoria_ID=' . $plSample['Categoria_ID'];

	// Known documents (label, sample query). Any other PDF script in pdf/ is listed after these.
	$plKnown = array(
		'flyer.php' => array($pl('452-60', 'Flyer - single game'), 'Juego_ID=' . $plSample['Juego_ID']),
		'flyerC.php' => array($pl('452-61', 'Flyer - week (one game per page)'), 'Jornada_ID=' . $plSample['Jornada_ID']),
		'flyerSC.php' => array($pl('452-62', 'Flyer - category (several games per page)'), 'Jornada_ID=' . $plSample['Jornada_ID'] . '&Categoria_ID=' . $plSample['Categoria_ID']),
		'cedulas.php' => array($pl('452-63', 'Score sheets (cedulas)'), $plWeekQs),
		'cedulas2.php' => array($pl('452-63', 'Score sheets (cedulas)') . ' 2', $plWeekQs),
		'reporteArbitros.php' => array($pl('452-64', 'Games report (referees)'), $plWeekQs),
		'reportePendientes.php' => array($pl('452-65', 'Pending games report'), $plWeekQs),
		'reporteEquipoJuegos.php' => array($pl('452-66', 'Team games report'), 'Equipo=' . $plSample['Equipo_ID']),
		'registrosEquipo.php' => array($pl('452-67', 'Team player IDs'), 'Equipo_ID=' . $plSample['Equipo_ID'] . '&Edad1=0&Edad2=150&Imprimir=1'),
		'registrosEquipoManual.php' => array($pl('452-68', 'Player IDs (manual)'), 'Equipo_ID=' . $plSample['Equipo_ID'] . '&Edad1=0&Edad2=150&Imprimir=1'),
		'tarjetasCambioEquipo.php' => array($pl('452-69', 'Team change cards'), 'Equipo_ID=' . $plSample['Equipo_ID']),
	);
	$plDocs = array();
	foreach ($plKnown as $script => $info) {
		if (is_file($plSiteRoot . '/pdf/' . $script)) {
			$plDocs[] = array('script' => $script, 'label' => $info[0], 'qs' => $info[1]);
		}
	}
	foreach (glob($plSiteRoot . '/pdf/*.php') ?: array() as $plFile) {
		$script = basename($plFile);
		if (isset($plKnown[$script]) || preg_match('/^(fpdf|alphapdf|qr|flyer_|flyerShare|flyerFacebook)|-I(-check)?\.php$|bkp|Actual\d/i', $script)) {
			continue;
		}
		$head = (string) @file_get_contents($plFile, false, null, 0, 20000);
		if (!preg_match('/new\s+(FPDF|AlphaPDF|PDF\w*)\s*\(|az_pdf_create_alphapdf\s*\(/', $head)) {
			continue;
		}
		$plDocs[] = array('script' => $script, 'label' => $script, 'qs' => $plWeekQs . '&Equipo_ID=' . $plSample['Equipo_ID'] . '&Equipo=' . $plSample['Equipo_ID'] . '&Edad1=0&Edad2=150&Imprimir=1');
	}

	$plStrings = array(
		'document' => $pl('452-51', 'Document'),
		'params' => $pl('452-52', 'Sample data (URL parameters)'),
		'page' => $pl('452-53', 'Page'),
		'load' => $pl('452-54', 'Load'),
		'save' => $pl('0000', 'Save'),
		'resetDoc' => $pl('452-55', 'Reset this document'),
		'open' => $pl('452-56', 'Open PDF'),
		'help' => $pl('452-57', 'Click an element to select it, drag it to move it, or use the arrow keys (Shift = 5 mm). Changes apply to every repetition of the element (every game, player or row).'),
		'elements' => $pl('452-58', 'Elements on this page'),
		'moveX' => $pl('452-70', 'Move X'),
		'moveY' => $pl('452-71', 'Move Y'),
		'size' => $pl('452-72', 'Size %'),
		'hide' => $pl('452-73', 'Hide'),
		'reset' => $pl('452-74', 'Reset'),
		'none' => $pl('452-75', 'Select an element on the PDF.'),
		'loading' => $pl('452-59', 'Loading...'),
		'saved' => $pl('441', 'Saved.'),
		'loadError' => $pl('452-76', 'This document could not be loaded with the sample data. Check the URL parameters.'),
		'unsaved' => $pl('452-77', 'Unsaved changes'),
		'image' => $pl('452-78', 'Image'),
		'line' => $pl('452-79', 'Line / box'),
		'confirmReset' => $pl('452-80', 'Remove all layout changes of this document?'),
		'font' => $pl('452-30', 'Font'),
		'type' => $pl('452-31', 'Type'),
		'styleR' => $pl('452-32', 'Regular'),
		'styleB' => $pl('452-33', 'Bold'),
		'styleI' => $pl('452-34', 'Italic'),
		'styleBI' => $pl('452-35', 'Bold italic'),
		'pt' => $pl('452-82', 'Size (pt)'),
		'color' => $pl('452-83', 'Color'),
		'shadowColor' => $pl('452-84', 'Shadow color'),
		'align' => $pl('452-85', 'Alignment'),
		'alignL' => $pl('452-90', 'Left'),
		'alignC' => $pl('452-91', 'Center'),
		'alignR' => $pl('452-92', 'Right'),
		'width' => $pl('452-86', 'Width'),
		'height' => $pl('452-93', 'Height'),
		'keepRatio' => $pl('452-94', 'Keep proportions'),
		'changeImage' => $pl('452-95', 'Change image'),
		'originalImage' => $pl('452-96', 'Use original image'),
		'replacedBy' => $pl('452-97', 'Replacement image:'),
		'debug' => $pl('452-104', 'Debug'),
		'debugCode' => $pl('452-105', 'Code that draws this element'),
	);

	$plFonts = array();
	foreach (az_flyer_available_fonts($plSiteRoot . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . 'font') as $plFam => $plInfo) {
		if (!$plInfo['symbolic'] && !empty($plInfo['styles'])) {
			$plFonts[] = array(
				'name' => $plFam,
				'styles' => strcasecmp($plInfo['type'], 'Core') === 0 ? array('', 'B', 'I', 'BI') : array_map('strval', array_keys($plInfo['styles'])),
			);
		}
	}

	$plJson = function ($v) {
		return json_encode($v, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
	};
	$plOptions = '';
	foreach ($plDocs as $i => $d) {
		$plOptions .= '<option value="' . $i . '">' . htmlspecialchars($d['label'], ENT_QUOTES, 'UTF-8') . '</option>';
	}
	$plEsc = function ($s) {
		return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
	};

	$htmlConfig .= '
	<div class="container-fluid py-2" id="pdfLayoutRoot">
		<div class="d-flex justify-content-end">
			<div class="form-check form-switch mb-1">
				<input class="form-check-input" type="checkbox" id="plDebug">
				<label class="form-check-label" for="plDebug">' . $plEsc($plStrings['debug']) . '</label>
			</div>
		</div>
		<div class="row g-2 align-items-end">
			<div class="col-12 col-md-4">
				<label class="form-label" for="plDoc">' . $plEsc($plStrings['document']) . '</label>
				<select class="form-select" id="plDoc">' . $plOptions . '</select>
			</div>
			<div class="col-12 col-md-5">
				<label class="form-label" for="plQs">' . $plEsc($plStrings['params']) . '</label>
				<input type="text" class="form-control" id="plQs">
			</div>
			<div class="col-6 col-md-1">
				<label class="form-label" for="plPage">' . $plEsc($plStrings['page']) . '</label>
				<select class="form-select" id="plPage"><option value="1">1</option></select>
			</div>
			<div class="col-6 col-md-2">
				<button type="button" class="btn btn-outline-primary w-100 mb-0" id="plLoad">' . $plEsc($plStrings['load']) . '</button>
			</div>
		</div>
		<p class="text-sm text-muted mt-2 mb-2">' . $plEsc($plStrings['help']) . '</p>
		<div class="row g-3">
			<div class="col-12 col-lg-8">
				<div id="plStage" style="position: relative; display: inline-block; max-width: 100%; border: 1px solid #ddd; background: #f4f4f4; min-height: 200px; user-select: none;">
					<canvas id="plCanvas" style="display: block; max-width: 100%;"></canvas>
					<div id="plOverlay" style="position: absolute; left: 0; top: 0; right: 0; bottom: 0;" tabindex="0"></div>
					<div id="plBusy" style="position: absolute; left: 0; top: 0; right: 0; bottom: 0; display: none; align-items: center; justify-content: center; background: rgba(255,255,255,.6);"><div class="spinner-border" role="status"></div></div>
				</div>
				<div id="plMsg" class="text-danger text-sm mt-2"></div>
			</div>
			<div class="col-12 col-lg-4">
				<div class="card mb-3">
					<div class="card-body py-2" id="plProps"></div>
				</div>
				<div class="card">
					<div class="card-body py-2">
						<h6 class="mb-2">' . $plEsc($plStrings['elements']) . '</h6>
						<div id="plList" style="max-height: 420px; overflow: auto;"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="d-flex flex-wrap gap-2 mt-3 align-items-center">
			<button type="button" class="btn btn-primary mb-0" id="plSave">' . $plEsc($plStrings['save']) . '</button>
			<button type="button" class="btn btn-outline-secondary mb-0" id="plResetDoc">' . $plEsc($plStrings['resetDoc']) . '</button>
			<a class="btn btn-outline-secondary mb-0" id="plOpen" target="_blank" rel="noopener">' . $plEsc($plStrings['open']) . '</a>
			<span id="plDirty" class="text-warning text-sm" style="display: none;">' . $plEsc($plStrings['unsaved']) . '</span>
		</div>
	</div>
	<script>
	(function () {
		var cfg = {
			docs: ' . $plJson($plDocs) . ',
			layout: ' . $plJson((object) az_pdf_layout_load($plSiteRoot)) . ',
			str: ' . $plJson($plStrings) . ',
			fonts: ' . $plJson($plFonts) . ',
			saveUrl: "./ajax/Admin/Config/configManagementPdfLayoutSave.php",
			uploadUrl: "./ajax/Admin/Config/configManagementPdfLayoutImage.php",
			sourceUrl: "./ajax/Admin/Config/configManagementPdfLayoutSource.php"
		};
		function start() {
			if (window.azPdfLayoutEditor) { window.azPdfLayoutEditor(cfg); }
		}
		if (window.azPdfLayoutEditor) { start(); return; }
		var s = document.createElement("script");
		s.src = "./javascript/pdfLayoutEditor.js?v=' . (int) @filemtime(dirname(__DIR__, 3) . '/javascript/pdfLayoutEditor.js') . '";
		s.onload = start;
		document.head.appendChild(s);
	})();
	</script>';
?>
