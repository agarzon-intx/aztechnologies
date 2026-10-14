<?php
/**
 * "Read ID data" button for the player forms (javascript/playerIdOcr.js, loaded on first click).
 * $suffix is '' on the create forms and 'E' on the edit forms.
 */
if (!function_exists('az_player_id_ocr_button')) {
	function az_player_id_ocr_button(array $lang, $suffix)
	{
		$t = function ($key, $fallback) use ($lang) {
			return isset($lang[$key]) ? $lang[$key] : $fallback;
		};
		$texts = array(
			'needImage' => $t('933-5', 'Upload the front of the ID first.'),
			'reading' => $t('933-6', 'Reading ID…'),
			'firstTime' => $t('933-7', 'The first time may take a few seconds.'),
			'title' => $t('933-8', 'Data read from the ID'),
			'apply' => $t('933-9', 'Apply'),
			'cancel' => $t('933-10', 'Cancel'),
			'nothing' => $t('933-11', 'No data could be read. Use a clearer, straight photo of the front of the ID.'),
			'review' => $t('933-12', 'Check the data before saving.'),
			'dateMismatch' => $t('933-13', 'The birth date read does not match the CURP / voter key; please check it.'),
			'noCurp' => $t('933-14', 'The CURP was not found.'),
			'curpCheck' => $t('933-15', 'The CURP check digit does not match; please check it.'),
			'noNames' => $t('933-16', 'The full name could not be read.'),
			'sexX' => $t('933-17', 'Non-binary sex on the ID: choose the sex manually.'),
			'foto' => $t('933-18', 'Photo'),
			'loadError' => $t('933-19', 'The text reader could not be loaded. Check the internet connection.'),
			'none' => $t('933-20', 'Not found'),
			'nameMismatch' => $t('933-21', 'The name read does not match the CURP; please check it.'),
			'source' => $t('933-22', 'Where on the ID'),
			'srcName' => $t('933-23', 'Printed name'),
			'srcPrintedDate' => $t('933-24', 'Printed date'),
			'srcCurpDate' => $t('933-25', 'CURP, characters 5-10'),
			'srcClaveDate' => $t('933-26', 'Voter key, characters 7-12'),
			'srcPrintedSex' => $t('933-27', 'SEX field'),
			'srcCurpSex' => $t('933-28', 'CURP, character 11'),
			'srcClaveSex' => $t('933-29', 'Voter key, character 15'),
			'srcCurp' => $t('933-30', 'CURP field'),
			'srcCurpFixed' => $t('933-31', 'corrected using the name'),
			'srcPhoto' => $t('933-32', 'Cropped next to the name'),
			'srcPhotoEst' => $t('933-33', 'Estimated photo area'),
			'fmtDate' => $t('933-34', 'dd/mm/yyyy'),
			'fmtYmd' => $t('933-35', 'yymmdd'),
			'differs' => $t('933-36', 'does not match'),
			'notMarked' => $t('933-37', 'not located'),
			'hoverHint' => $t('933-38', 'Hover or tap a field to see where it was read. Use "Mark" to draw, move or resize its box.'),
			'mark' => $t('933-39', 'Mark'),
			'editHint' => $t('933-40', 'Drag on the image to mark "%1", or drag its box (or its corner) to move or resize it. It is read again when you let go.'),
			'srcManual' => $t('933-41', 'Marked manually'),
			'rereading' => $t('933-42', 'Reading…'),
			'zoomHint' => $t('933-43', 'Zoom with + / − or pinch; with no field selected, drag the image to move around.'),
			'zoomIn' => $t('933-44', 'Zoom in'),
			'zoomOut' => $t('933-45', 'Zoom out'),
			'zoomFit' => $t('933-46', 'Fit'),
			'fmtMDY' => $t('933-47', 'mm/dd/yyyy'),
			'fmtYMD' => $t('933-48', 'yyyy/mm/dd'),
			'dateOrder' => $t('933-49', 'Order of day, month and year in the printed date'),
			'nombre' => $t('907', 'Name'),
			'paterno' => $t('919', 'Last name'),
			'materno' => $t('920', 'Second last name'),
			'fecha' => $t('921', 'Birth date'),
			'sexo' => $t('914', 'Sex'),
			'curp' => $t('922', 'CURP'),
			'male' => $t('944', 'Male'),
			'female' => $t('945', 'Female'),
		);
		$js = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'javascript' . DIRECTORY_SEPARATOR . 'playerIdOcr.js';
		$src = 'javascript/playerIdOcr.js?v=' . (is_readable($js) ? filemtime($js) : '1');
		$suffix = $suffix === 'E' ? 'E' : '';
		return '<button type="button" class="btn btn-outline-primary btn-sm mb-0 mt-2" style="min-width: 180px;"'
			. ' data-src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '"'
			. ' data-ocr="' . htmlspecialchars(json_encode($texts, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') . '"'
			. ' onclick="playerIdOcr(\'' . $suffix . '\', this);"><i class="fas fa-id-card"></i> '
			. htmlspecialchars($t('933-4', 'Read ID data'), ENT_QUOTES, 'UTF-8') . '</button>';
	}
}
