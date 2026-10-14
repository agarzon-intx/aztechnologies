<?php
/*
 * Institution assignments for users: usuarios_institucion (one row per institution) plus the legacy
 * single usuarios.Institucion_ID, which saving keeps pointing at one of the user's institutions.
 * Options use value "i<Institucion_ID>" so they can live in the same #equipol / #equipor lists as
 * league (0), referee (-1) and teams; like teams, several institutions can be combined with each other
 * and with teams, while 0 / -1 stay exclusive.
 * Only offered when Equipos.Institucion_ID exists (otherwise an institution user would get no teams).
 */

if (!function_exists('um_institutions_enabled')) {
	function um_institutions_enabled($Config, $schema)
	{
		$r = $Config->query("SHOW COLUMNS FROM $schema.Equipos LIKE 'Institucion_ID'");
		return ($r && $r->num_rows > 0);
	}

	/** Institucion_IDs assigned to the user (empty array when none). */
	function um_user_institution_ids($Config, $schema, $userID)
	{
		$userID = (int) $userID;
		$ids = array();
		$r = $Config->query("SELECT ui.Institucion_ID FROM $schema.usuarios_institucion ui
				JOIN $schema.usuarios u ON u.username = ui.username
			WHERE u.id_user = $userID AND ui.Institucion_ID > 0");
		while ($r && ($row = $r->fetch_assoc())) {
			$ids[(int) $row['Institucion_ID']] = (int) $row['Institucion_ID'];
		}
		$r = $Config->query("SELECT Institucion_ID FROM $schema.usuarios WHERE id_user = $userID AND Institucion_ID > 0");
		while ($r && ($row = $r->fetch_assoc())) {
			$ids[(int) $row['Institucion_ID']] = (int) $row['Institucion_ID'];
		}
		return array_values($ids);
	}

	/**
	 * <option> list of active institutions, skipping $exclude. With $only (array) it returns exactly
	 * those institutions, inactive ones included.
	 */
	function um_institution_options_html($Config, $schema, $lang, $exclude = array(), $only = null)
	{
		if (!um_institutions_enabled($Config, $schema)) {
			return '';
		}
		$where = "Institucion_ID > 0";
		if ($only !== null) {
			$only = array_filter(array_map('intval', (array) $only));
			if (!$only) {
				return '';
			}
			$where .= " AND Institucion_ID IN (" . implode(',', $only) . ")";
		}
		$exclude = array_filter(array_map('intval', (array) $exclude));
		if ($exclude) {
			$where .= " AND Institucion_ID NOT IN (" . implode(',', $exclude) . ")";
		}
		$r = $Config->query("SELECT * FROM $schema.Instituciones WHERE $where ORDER BY Institucion_DESC");
		$html = '';
		$seen = array();
		$prefix = isset($lang['819-2']) ? $lang['819-2'] : 'Institution';
		if ($r && $r->num_rows > 0) {
			while ($row = $r->fetch_assoc()) {
				$id = (int) $row['Institucion_ID'];
				if (isset($seen[$id]) || ($only === null && isset($row['Activo']) && (string) $row['Activo'] === '0')) {
					continue;
				}
				$seen[$id] = true;
				$html .= '<option value="i' . $id . '" data-tipo="inst">'
					. htmlspecialchars($prefix . ' - ' . $row['Institucion_DESC'], ENT_QUOTES, 'UTF-8') . '</option>';
			}
		}
		return $html;
	}

	/** True when the schema supports institutions and has at least one (active, or one of the user's). */
	function um_institutions_available($Config, $schema, $lang, $userInstitucionIds = array())
	{
		if (um_institution_options_html($Config, $schema, $lang) !== '') {
			return true;
		}
		return um_institution_options_html($Config, $schema, $lang, array(), (array) $userInstitucionIds) !== '';
	}

	/** Equipos / Institución selector shown left of the status and category filters. */
	function um_assign_type_filter_html($lang, $available, $selectInstitution = false)
	{
		$inst = $available && $selectInstitution;
		return '<div class="col-4 col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4">
								<div class="input-group input-group-static mb-4">
									<label>' . htmlspecialchars($lang['819-3'], ENT_QUOTES, 'UTF-8') . '</label>
									<select class="form-control um-assign-type-filter"' . ($available ? '' : ' disabled="disabled"') . '>
										<option value="equipos"' . ($inst ? '' : ' selected="selected"') . '>' . htmlspecialchars($lang['819-4'], ENT_QUOTES, 'UTF-8') . '</option>
										<option value="inst"' . ($inst ? ' selected="selected"' : '') . '>' . htmlspecialchars($lang['819-2'], ENT_QUOTES, 'UTF-8') . '</option>
									</select>
								</div>
							</div>';
	}

	/**
	 * moveRight() for the #equipol / #equipor lists: league (0) and referee (-1) are exclusive (only one
	 * assignment), teams and institutions (i<ID>) can be combined.
	 */
	function um_move_right_js($lang)
	{
		$m846 = json_encode((string) $lang['846']);
		$m8470 = json_encode((string) $lang['847-0']);
		$m8471 = json_encode((string) $lang['847-1']);
		$m8480 = json_encode((string) $lang['848-0']);
		$m8481 = json_encode((string) $lang['848-1']);
		return <<<JS
					function umIsExclusive(v) {
						v = String(v);
						return v === "0" || v === "-1";
					}
					function moveRight(leftValue, rightValue, type) {
						var \$l = $("#" + leftValue), \$r = $("#" + rightValue);
						var \$sel = \$l.find("option:selected");
						if (\$sel.length == 0) {
							window.alert($m846);
							return;
						}
						if (type) {
							var \$ex = \$sel.filter(function () { return umIsExclusive(this.value); }).first();
							if (\$ex.length) {
								if (\$r.find("option").length > 0) {
									if (!confirm($m8470 + \$ex.text() + $m8471)) {
										\$sel.prop("selected", false);
										return;
									}
									\$l.append(\$r.find("option").clone());
									\$r.empty();
								}
								\$r.append(\$ex.clone().prop("selected", false));
								\$ex.remove();
								\$l.find("option:selected").prop("selected", false);
							} else {
								var \$rex = \$r.find("option").filter(function () { return umIsExclusive(this.value); });
								if (\$rex.length) {
									if (!confirm($m8480 + \$rex.first().text() + $m8481)) {
										\$sel.prop("selected", false);
										return;
									}
									\$l.append(\$rex.clone());
									\$rex.remove();
								}
								\$r.append(\$sel.clone());
								\$sel.remove();
							}
						} else {
							\$r.append(\$sel.clone());
							\$sel.remove();
						}
						if ((leftValue === "equipol" || rightValue === "equipol") && typeof userManagementApplyTeamLeftFilters === "function") {
							var \$umPanel = $("#equipol").closest("#userManagementCreate, #userManagementEdit");
							if (\$umPanel.length) {
								setTimeout(function () { userManagementApplyTeamLeftFilters(\$umPanel); }, 0);
							}
						}
					}
JS;
	}
}
