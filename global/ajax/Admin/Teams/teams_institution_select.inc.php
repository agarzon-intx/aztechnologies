<?php
if (!isset($institucionSelected)) {
	$institucionSelected = 0;
}
$htmlTeams .= '<div class="col-xl-12">
										<div class="input-group input-group-outline my-3" style="margin-top: 5px !important;margin-bottom: 0px !important;">
											<label class="form-label">' . $lang['113-2'] . '</label>
											<select class="form-control" name="institucion" id="institucion">
												<option value="0">----</option>';
$sqlInstitutions = "SELECT Institucion_ID, Institucion_DESC, Institucion_FULLDESC
				FROM $schema.Instituciones
				WHERE Torneo_ID = $Season
					AND IFNULL(Activo, 0) = 1
				ORDER BY Institucion_DESC ASC";
$resultInstitutions = $Config->query($sqlInstitutions);
if ($resultInstitutions && $resultInstitutions->num_rows > 0) {
	while ($rowInstitution = $resultInstitutions->fetch_assoc()) {
		$institutionId = (int) $rowInstitution['Institucion_ID'];
		$institutionLabel = $rowInstitution['Institucion_DESC'];
		if (!empty($rowInstitution['Institucion_FULLDESC'])) {
			$institutionLabel = $rowInstitution['Institucion_FULLDESC'];
		}
		$selected = ($institutionId === (int) $institucionSelected) ? ' selected' : '';
		$htmlTeams .= "<option value='" . $institutionId . "'" . $selected . ">" . htmlspecialchars((string) $institutionLabel, ENT_QUOTES, 'UTF-8') . "</option>";
	}
}
$htmlTeams .= '							</select>
										</div>
									</div>';
