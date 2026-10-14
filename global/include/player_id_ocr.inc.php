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

/**
 * "Upload whole image" option of the ID upload: one picture with the front on top and the back below
 * (the stored 700x876 layout). On save the frame is sent as its two halves to the front / back uploads.
 */
if (!function_exists('az_player_id_whole_button')) {
	function az_player_id_whole_button(array $lang, $suffix)
	{
		$s = $suffix === 'E' ? 'E' : '';
		$label = isset($lang['933-50']) ? $lang['933-50'] : 'Upload whole image';
		return '<button style="margin: 6px 0 0; width: 180px;" class="btn btn-outline-secondary" type="button" onclick="playerIdUploadMode(\'' . $s . '\', 3, true);">'
			. htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</button>';
	}

	/** Link inside the front / back panel that switches to the whole-image panel. */
	function az_player_id_whole_switch(array $lang, $suffix)
	{
		$s = $suffix === 'E' ? 'E' : '';
		$label = isset($lang['933-50']) ? $lang['933-50'] : 'Upload whole image';
		return '<div style="text-align: center;"><button type="button" class="btn btn-link btn-sm mb-0" onclick="playerIdUploadMode(\'' . $s . '\', 3, true);">'
			. htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</button></div>';
	}

	function az_player_id_whole_block(array $lang, $suffix)
	{
		$s = $suffix === 'E' ? 'E' : '';
		$t = function ($key, $fallback) use ($lang) {
			return htmlspecialchars(isset($lang[$key]) ? $lang[$key] : $fallback, ENT_QUOTES, 'UTF-8');
		};
		$frame = '#id13Frame' . $s;
		return '<div id="myIDBackground3' . $s . '" style="display: none;">'
			. '<div id="id13Frame' . $s . '" class="player-photo-frame" data-out-w="700" data-out-h="876" data-split="1"'
			. ' data-upload="objects/UploadIDF' . $s . '.php" data-field="myID11' . $s . '" data-target="#myID11FileName' . $s . '"'
			. ' data-upload2="objects/UploadIDB' . $s . '.php" data-field2="myID12' . $s . '" data-target2="#myID12FileName' . $s . '"'
			. ' data-preview="#previewMyID13' . $s . '" data-tools="#id13Tools' . $s . '" data-zoom="#id13Zoom' . $s . '"'
			. ' style="width: 150px; height: 187px; background-color: #BFBFBF; margin: auto; position: relative; overflow: hidden;">'
			. '<img id="identificacion13' . $s . '" src="" width="150" height="187" style="display: none"/>'
			. '</div>'
			. '<div id="id13Tools' . $s . '" style="display: none; width: 150px; margin: 6px auto 0;">'
			. '<div class="d-flex align-items-center" style="gap: 4px;">'
			. '<button type="button" class="btn btn-sm btn-outline-secondary mb-0 px-2" onclick="playerPhotoStep(\'' . $frame . '\', 1 / 1.15);">&minus;</button>'
			. '<input type="range" class="form-range" id="id13Zoom' . $s . '" min="0" max="100" step="1" value="0" oninput="playerPhotoSlider(\'' . $frame . '\', this.value);" style="flex: 1;">'
			. '<button type="button" class="btn btn-sm btn-outline-secondary mb-0 px-2" onclick="playerPhotoStep(\'' . $frame . '\', 1.15);">+</button>'
			. '<button type="button" class="btn btn-sm btn-outline-secondary mb-0 px-2" title="' . $t('931-2', 'Reset') . '" onclick="playerPhotoReset(\'' . $frame . '\');">&#8634;</button>'
			. '</div>'
			. '</div>'
			. '<div style="font-size: 12px; color: #777; margin: 4px auto; max-width: 220px;">' . $t('933-51', 'Front and back in one image, front on top.') . '</div>'
			. '<button style="margin: 0; width: 180px;" class="btn btn-secondary" type="button" onclick="fireEvent($(\'#myID13' . $s . '\'), \'click\');">' . $t('933-50', 'Upload whole image') . '</button>'
			. '<input style="display: none; visibility: hidden;" type="file" accept="image/png,image/jpeg" id="myID13' . $s . '" onchange="playerPhotoLoadFile(this, \'' . $frame . '\');">'
			. '<div id="previewMyID13' . $s . '"></div>'
			. '<div style="text-align: center;"><button type="button" class="btn btn-link btn-sm mb-0" onclick="playerIdUploadMode(\'' . $s . '\', 2);">' . $t('933-52', 'Upload front and back separately') . '</button></div>'
			. '</div>';
	}
}
