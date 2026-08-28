<?php
$teamNameColor = isset($teamNameColor) ? (string) $teamNameColor : '#000000';
$credentialColorIsUnset = !isset($credentialColor) || $credentialColor === null || trim((string) $credentialColor) === '';
$credentialColor = $credentialColorIsUnset ? '#000000' : (string) $credentialColor;

if (!preg_match('/^#[0-9a-fA-F]{6}$/', $teamNameColor)) {
	$teamNameColor = '#000000';
}
if (!preg_match('/^#[0-9a-fA-F]{6}$/', $credentialColor)) {
	$credentialColor = '#000000';
	$credentialColorIsUnset = true;
}

$htmlTeams .= '<div class="col-xl-12">
		<div class="input-group input-group-outline my-3">
			<label class="form-label">' . $lang['532'] . '</label>
			<input type="color" class="form-control form-control-color" name="nombreColor" id="nombreColor" value="' . htmlspecialchars($teamNameColor, ENT_QUOTES, 'UTF-8') . '" title="' . $lang['532'] . '" style="width: 30%; height: 42px; padding: 2px; cursor: pointer;">
		</div>
	</div>
	<div class="col-xl-12">
		<div class="form-check mt-2">
			<input class="form-check-input" type="checkbox" name="credencialColorUnset" id="credencialColorUnset"' . ($credentialColorIsUnset ? ' checked' : '') . ' onchange="$(\'#credencialColor\').prop(\'disabled\', this.checked); $(\'#credencialColorPickerContainer\').toggle(!this.checked);">
			<label class="custom-control-label" for="credencialColorUnset">' . $lang['534'] . '</label>
		</div>
		<div id="credencialColorPickerContainer" class="input-group input-group-outline my-3"' . ($credentialColorIsUnset ? ' style="display: none;"' : '') . '>
			<label class="form-label">' . $lang['533'] . '</label>
			<input type="color" class="form-control form-control-color" name="credencialColor" id="credencialColor" value="' . htmlspecialchars($credentialColor, ENT_QUOTES, 'UTF-8') . '" title="' . $lang['533'] . '" style="width: 30%; height: 42px; padding: 2px; cursor: pointer;"' . ($credentialColorIsUnset ? ' disabled' : '') . '>
		</div>
	</div>';
