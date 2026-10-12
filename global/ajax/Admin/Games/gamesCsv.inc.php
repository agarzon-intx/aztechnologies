<?php
/*
 * Games CSV export (admin menu "Generar CSV"): always the current season (Torneos.Actual = 'S').
 */

if (!function_exists('games_csv_current_season')) {
	/** array(Torneo_ID, Torneo_Desc) of the current season, array(0, '') when none. */
	function games_csv_current_season($Config, $schema)
	{
		$r = $Config->query("SELECT Torneo_ID, Torneo_Desc FROM $schema.Torneos
			WHERE Torneo_ID = (SELECT max(Torneo_ID) FROM $schema.Torneos WHERE Actual = 'S') LIMIT 1");
		if ($r && ($row = $r->fetch_assoc())) {
			return array((int) $row['Torneo_ID'], (string) $row['Torneo_Desc']);
		}
		return array(0, '');
	}

	/** Jornada types (same labels as the week admin screen) that have games in the season: type => label. */
	function games_csv_week_types($Config, $schema, $season, $lang)
	{
		$labels = array(1 => $lang['890'], 2 => $lang['891'], 3 => $lang['892']);
		$types = array();
		$r = $Config->query("SELECT DISTINCT jo.Jornada_Type
			FROM $schema.Juegos j
				JOIN $schema.Jornada jo ON jo.Jornada_ID = j.Jornada_ID AND jo.Torneo_ID = j.Torneo_ID
			WHERE j.Torneo_ID = " . (int) $season . " AND jo.Jornada_Type IS NOT NULL
			ORDER BY jo.Jornada_Type");
		if ($r) {
			while ($row = $r->fetch_row()) {
				$t = (int) $row[0];
				$types[$t] = isset($labels[$t]) ? $labels[$t] : (string) $t;
			}
		}
		return $types;
	}

	function games_csv_is_admin($Config)
	{
		return trim((string) ($_SESSION[$Config->getAlias() . 'equipo'] ?? '')) === '0';
	}
}
