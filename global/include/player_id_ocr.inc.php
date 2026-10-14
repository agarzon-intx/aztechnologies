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
