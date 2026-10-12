<?php
/*
 * Institution assignment for users (usuarios.Institucion_ID).
 * Options use value "i<Institucion_ID>" so they can live in the same #equipol / #equipor lists as
 * league (0), referee (-1) and teams; an institution is exclusive like 0 / -1.
 * Only offered when Equipos.Institucion_ID exists (otherwise an institution user would get no teams).
 */

if (!function_exists('um_institutions_enabled')) {
	function um_institutions_enabled($Config, $schema)
	{
		$r = $Config->query("SHOW COLUMNS FROM $schema.Equipos LIKE 'Institucion_ID'");
		return ($r && $r->num_rows > 0);
	}

	/** Institucion_ID assigned to the user, 0 when none. */
	function um_user_institution_id($Config, $schema, $userID)
	{
		$r = $Config->query("SELECT Institucion_ID FROM $schema.usuarios WHERE id_user = " . (int) $userID . " LIMIT 1");
		if ($r && ($row = $r->fetch_assoc())) {
			return (int) $row['Institucion_ID'];
		}
		return 0;
	}

	/**
	 * <option> list of institutions. $only > 0 returns just that institution, $exclude skips one.
	 */
	function um_institution_options_html($Config, $schema, $lang, $exclude = 0, $only = 0)
	{
		if (!um_institutions_enabled($Config, $schema)) {
			return '';
		}
		$where = "Institucion_ID > 0";
		if ((int) $only > 0) {
			$where .= " AND Institucion_ID = " . (int) $only;
		}
		if ((int) $exclude > 0) {
			$where .= " AND Institucion_ID <> " . (int) $exclude;
		}
		$r = $Config->query("SELECT * FROM $schema.Instituciones WHERE $where ORDER BY Institucion_DESC");
		$html = '';
		$prefix = isset($lang['819-2']) ? $lang['819-2'] : 'Institution';
		if ($r && $r->num_rows > 0) {
			while ($row = $r->fetch_assoc()) {
				if ((int) $only <= 0 && isset($row['Activo']) && (string) $row['Activo'] === '0') {
					continue;
				}
				$html .= '<option value="i' . (int) $row['Institucion_ID'] . '" data-tipo="inst">'
					. htmlspecialchars($prefix . ' - ' . $row['Institucion_DESC'], ENT_QUOTES, 'UTF-8') . '</option>';
			}
		}
		return $html;
	}

	/** True when the schema supports institutions and has at least one (active, or the user's own). */
	function um_institutions_available($Config, $schema, $lang, $userInstitucionId = 0)
	{
		if (um_institution_options_html($Config, $schema, $lang) !== '') {
			return true;
		}
		return (int) $userInstitucionId > 0 && um_institution_options_html($Config, $schema, $lang, 0, $userInstitucionId) !== '';
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
	 * moveRight() for the #equipol / #equipor lists: league (0), referee (-1) and institutions (i<ID>)
	 * are exclusive (only one assignment), teams can be combined.
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
						return v === "0" || v === "-1" || /^i\\d+$/.test(v);
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
