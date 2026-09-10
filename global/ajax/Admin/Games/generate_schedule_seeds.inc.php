<?php
/**
 * Tournament-wide institution seeds, then teams filtered by category (Fuerza).
 * When Institucion_ID = 0, each team is its own seed (keyed by Equipo_ID).
 */

if (!function_exists('az_generate_schedule_institution_seeds')) {
	function az_generate_schedule_institution_seeds($Config, $schema, $Season) {
		$Season = (int) $Season;
		$seeds = array();
		$sql = "SELECT CASE WHEN IFNULL(e.Institucion_ID, 0) = 0 THEN e.Equipo_ID ELSE e.Institucion_ID END AS Institucion_ID,
					CASE WHEN IFNULL(e.Institucion_ID, 0) = 0 THEN e.Equipo_DESC ELSE i.Institucion_DESC END AS Institucion_DESC,
					COUNT(e.Equipo_ID) AS TeamCount,
					MAX(IFNULL(e.Institucion_ID, 0)) AS RealInstitucion_ID
				FROM $schema.Equipos e
					LEFT JOIN $schema.Instituciones i
						ON i.Institucion_ID = e.Institucion_ID
						AND i.Torneo_ID = e.Torneo_ID
				WHERE e.Torneo_ID = $Season
					AND IFNULL(e.Activo, 0) = 1
				GROUP BY CASE WHEN IFNULL(e.Institucion_ID, 0) = 0 THEN e.Equipo_ID ELSE e.Institucion_ID END,
					CASE WHEN IFNULL(e.Institucion_ID, 0) = 0 THEN e.Equipo_DESC ELSE i.Institucion_DESC END
				ORDER BY TeamCount DESC, Institucion_DESC ASC";
		$res = $Config->query($sql);
		$seedNum = 0;
		if ($res && $res->num_rows > 0) {
			while ($row = $res->fetch_assoc()) {
				$seedNum++;
				$seeds[] = array(
					'seed' => $seedNum,
					'Institucion_ID' => (int) $row['Institucion_ID'],
					'Institucion_DESC' => $row['Institucion_DESC'],
					'TeamCount' => (int) $row['TeamCount'],
					'RealInstitucion_ID' => (int) $row['RealInstitucion_ID'],
					'IsSoloTeam' => ((int) $row['RealInstitucion_ID'] === 0),
				);
			}
		}
		return $seeds;
	}
}

if (!function_exists('az_generate_schedule_teams_for_seed_category')) {
	function az_generate_schedule_teams_for_seed_category($Config, $schema, $Season, $catId, $seed) {
		$Season = (int) $Season;
		$catId = (int) $catId;
		$seedKey = (int) $seed['Institucion_ID'];
		$teams = array();

		if (!empty($seed['IsSoloTeam'])) {
			$sql = "SELECT e.Equipo_ID, e.Equipo_DESC
					FROM $schema.Equipos e
					WHERE e.Torneo_ID = $Season
						AND e.Fuerza = $catId
						AND IFNULL(e.Activo, 0) = 1
						AND e.Equipo_ID = $seedKey
					ORDER BY e.Equipo_DESC ASC";
		} else {
			$sql = "SELECT e.Equipo_ID, e.Equipo_DESC
					FROM $schema.Equipos e
					WHERE e.Torneo_ID = $Season
						AND e.Fuerza = $catId
						AND IFNULL(e.Activo, 0) = 1
						AND e.Institucion_ID = $seedKey
					ORDER BY e.Equipo_DESC ASC";
		}

		$res = $Config->query($sql);
		if ($res && $res->num_rows > 0) {
			while ($t = $res->fetch_assoc()) {
				$teams[] = $t;
			}
		}
		return $teams;
	}
}

if (!function_exists('az_generate_schedule_seeded_team_ids_for_category')) {
	function az_generate_schedule_seeded_team_ids_for_category($Config, $schema, $Season, $catId, $institutionSeeds) {
		$teamIds = array();
		foreach ($institutionSeeds as $seed) {
			$teams = az_generate_schedule_teams_for_seed_category($Config, $schema, $Season, $catId, $seed);
			foreach ($teams as $t) {
				$teamIds[] = (int) $t['Equipo_ID'];
			}
		}
		return $teamIds;
	}
}
