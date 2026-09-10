<?php
/**
 * Tournament-wide institution seeds in a TEMPORARY TABLE (tmp_gs_institution_seeds),
 * then teams filtered by category (Fuerza).
 * When Institucion_ID = 0, each team is its own seed (keyed by Equipo_ID).
 */

if (!function_exists('az_generate_schedule_seed_tmp_name')) {
	function az_generate_schedule_seed_tmp_name() {
		return 'tmp_gs_institution_seeds';
	}
}

if (!function_exists('az_generate_schedule_build_seed_tmp')) {
	/**
	 * Builds session TEMPORARY TABLE with rank + institution seed rows for the tournament.
	 * Safe to call again in the same request (drops/recreates).
	 */
	function az_generate_schedule_build_seed_tmp($Config, $schema, $Season) {
		$Season = (int) $Season;
		$tmp = az_generate_schedule_seed_tmp_name();
		$conn = $Config->connect();
		if (!$conn) {
			return false;
		}

		// Use the same live connection so TEMPORARY TABLE + @vars stay in-session.
		$conn->query("DROP TEMPORARY TABLE IF EXISTS `$tmp`");
		$conn->query('SET @gs_rank := 0');

		// Create empty table first (avoids CREATE…AS + user-var quirks on some MySQL modes).
		$create = "CREATE TEMPORARY TABLE `$tmp` (
				`rank` INT NOT NULL,
				Institucion_ID BIGINT NOT NULL,
				Institucion_DESC VARCHAR(255) NULL,
				TeamCount INT NOT NULL,
				RealInstitucion_ID BIGINT NOT NULL,
				PRIMARY KEY (`rank`)
			)";
		if ($conn->query($create) === false) {
			return false;
		}

		$insert = "INSERT INTO `$tmp` (`rank`, Institucion_ID, Institucion_DESC, TeamCount, RealInstitucion_ID)
			SELECT @gs_rank := @gs_rank + 1,
				src.Institucion_ID,
				src.Institucion_DESC,
				src.TeamCount,
				src.RealInstitucion_ID
			FROM (
				SELECT CASE WHEN IFNULL(e.Institucion_ID, 0) = 0 THEN e.Equipo_ID ELSE e.Institucion_ID END AS Institucion_ID,
					CASE WHEN IFNULL(e.Institucion_ID, 0) = 0 THEN e.Equipo_DESC ELSE IFNULL(i.Institucion_DESC, '') END AS Institucion_DESC,
					COUNT(e.Equipo_ID) AS TeamCount,
					MAX(IFNULL(e.Institucion_ID, 0)) AS RealInstitucion_ID
				FROM $schema.Equipos e
					LEFT JOIN $schema.Instituciones i
						ON i.Institucion_ID = e.Institucion_ID
						AND i.Torneo_ID = e.Torneo_ID
				WHERE e.Torneo_ID = $Season
					AND IFNULL(e.Activo, 0) = 1
				GROUP BY CASE WHEN IFNULL(e.Institucion_ID, 0) = 0 THEN e.Equipo_ID ELSE e.Institucion_ID END,
					CASE WHEN IFNULL(e.Institucion_ID, 0) = 0 THEN e.Equipo_DESC ELSE IFNULL(i.Institucion_DESC, '') END
				ORDER BY COUNT(e.Equipo_ID) DESC, Institucion_DESC ASC
			) src";

		if ($conn->query($insert) === false) {
			return false;
		}
		return true;
	}
}

if (!function_exists('az_generate_schedule_institution_seeds')) {
	function az_generate_schedule_institution_seeds($Config, $schema, $Season) {
		$seeds = array();
		if (!az_generate_schedule_build_seed_tmp($Config, $schema, $Season)) {
			return $seeds;
		}

		$tmp = az_generate_schedule_seed_tmp_name();
		$conn = $Config->connect();
		if (!$conn) {
			return $seeds;
		}
		$res = $conn->query("SELECT `rank`, Institucion_ID, Institucion_DESC, TeamCount, RealInstitucion_ID
				FROM `$tmp`
				ORDER BY `rank` ASC");
		if ($res && $res->num_rows > 0) {
			while ($row = $res->fetch_assoc()) {
				$seeds[] = array(
					'seed' => (int) $row['rank'],
					'rank' => (int) $row['rank'],
					'Institucion_ID' => (int) $row['Institucion_ID'],
					'Institucion_DESC' => (string) $row['Institucion_DESC'],
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
				$teams[] = array(
					'Equipo_ID' => (int) $t['Equipo_ID'],
					'Equipo_DESC' => (string) $t['Equipo_DESC'],
				);
			}
		}
		return $teams;
	}
}

if (!function_exists('az_generate_schedule_seeded_team_ids_for_category')) {
	/**
	 * Reads seed order from tmp_gs_institution_seeds, then teams by Fuerza.
	 * Expects az_generate_schedule_institution_seeds() (or build_seed_tmp) already called.
	 */
	function az_generate_schedule_seeded_team_ids_for_category($Config, $schema, $Season, $catId, $institutionSeeds = null) {
		$Season = (int) $Season;
		$catId = (int) $catId;
		$teamIds = array();
		$tmp = az_generate_schedule_seed_tmp_name();
		$conn = $Config->connect();

		$sql = "SELECT e.Equipo_ID
				FROM `$tmp` t
					INNER JOIN $schema.Equipos e
						ON e.Torneo_ID = $Season
						AND e.Fuerza = $catId
						AND IFNULL(e.Activo, 0) = 1
						AND (
							(IFNULL(t.RealInstitucion_ID, 0) = 0 AND e.Equipo_ID = t.Institucion_ID)
							OR (IFNULL(t.RealInstitucion_ID, 0) <> 0 AND e.Institucion_ID = t.Institucion_ID)
						)
				ORDER BY t.`rank` ASC, e.Equipo_DESC ASC";

		$res = ($conn) ? $conn->query($sql) : false;
		if ($res === false || $res === null) {
			az_generate_schedule_build_seed_tmp($Config, $schema, $Season);
			$conn = $Config->connect();
			$res = ($conn) ? $conn->query($sql) : false;
		}
		if ($res && $res->num_rows > 0) {
			while ($t = $res->fetch_assoc()) {
				$teamIds[] = (int) $t['Equipo_ID'];
			}
			return $teamIds;
		}

		if (is_array($institutionSeeds)) {
			foreach ($institutionSeeds as $seed) {
				$teams = az_generate_schedule_teams_for_seed_category($Config, $schema, $Season, $catId, $seed);
				foreach ($teams as $t) {
					$teamIds[] = (int) $t['Equipo_ID'];
				}
			}
		}
		return $teamIds;
	}
}
