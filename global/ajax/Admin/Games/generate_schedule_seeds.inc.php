<?php
/**
 * Tournament-wide institution seeds in TEMPORARY TABLE tmp_gs_institution_seeds,
 * then teams filtered by category (Fuerza).
 * When Institucion_ID = 0, each team is its own seed (keyed by Equipo_ID).
 */

if (!function_exists('az_generate_schedule_seed_tmp_name')) {
	function az_generate_schedule_seed_tmp_name() {
		return 'tmp_gs_institution_seeds';
	}
}

if (!function_exists('az_generate_schedule_seed_log')) {
	function az_generate_schedule_seed_log($msg) {
		$path = __DIR__ . DIRECTORY_SEPARATOR . 'generate_schedule_error.log';
		@file_put_contents($path, date('c') . ' ' . $msg . "\n", FILE_APPEND | LOCK_EX);
	}
}

if (!function_exists('az_generate_schedule_fetch_rank_rows')) {
	/**
	 * Rank query (no category filter). Returns rows ordered for seeding.
	 */
	function az_generate_schedule_fetch_rank_rows($Config, $schema, $Season) {
		$Season = (int) $Season;
		$rows = array();
		$sql = "SELECT CASE WHEN IFNULL(e.Institucion_ID, 0) = 0 THEN e.Equipo_ID ELSE e.Institucion_ID END AS Institucion_ID,
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
				ORDER BY TeamCount DESC, Institucion_DESC ASC";
		$res = $Config->query($sql);
		if ($res && $res->num_rows > 0) {
			while ($row = $res->fetch_assoc()) {
				$rows[] = $row;
			}
		}
		return $rows;
	}
}

if (!function_exists('az_generate_schedule_build_seed_tmp')) {
	/**
	 * Builds session TEMPORARY TABLE with rank column first.
	 * Rank is assigned in PHP; rows are inserted one-by-one (avoids MySQL user-var / mode fatals).
	 */
	function az_generate_schedule_build_seed_tmp($Config, $schema, $Season) {
		$Season = (int) $Season;
		$tmp = az_generate_schedule_seed_tmp_name();
		$conn = $Config->connect();
		if (!$conn) {
			az_generate_schedule_seed_log('connect() failed');
			return false;
		}

		try {
			$conn->query("DROP TEMPORARY TABLE IF EXISTS `$tmp`");
			$create = "CREATE TEMPORARY TABLE `$tmp` (
					`rank` INT NOT NULL,
					Institucion_ID BIGINT NOT NULL,
					Institucion_DESC VARCHAR(255) NULL,
					TeamCount INT NOT NULL,
					RealInstitucion_ID BIGINT NOT NULL,
					PRIMARY KEY (`rank`)
				)";
			if ($conn->query($create) === false) {
				az_generate_schedule_seed_log('CREATE TMP failed: ' . $conn->error);
				return false;
			}

			$rows = az_generate_schedule_fetch_rank_rows($Config, $schema, $Season);
			$rank = 0;
			$stmt = $conn->prepare("INSERT INTO `$tmp` (`rank`, Institucion_ID, Institucion_DESC, TeamCount, RealInstitucion_ID) VALUES (?, ?, ?, ?, ?)");
			if ($stmt === false) {
				// Fallback without prepare.
				foreach ($rows as $row) {
					$rank++;
					$instId = (int) $row['Institucion_ID'];
					$desc = $conn->real_escape_string((string) $row['Institucion_DESC']);
					$teamCount = (int) $row['TeamCount'];
					$realId = (int) $row['RealInstitucion_ID'];
					$sqlIns = "INSERT INTO `$tmp` (`rank`, Institucion_ID, Institucion_DESC, TeamCount, RealInstitucion_ID)
						VALUES ($rank, $instId, '$desc', $teamCount, $realId)";
					if ($conn->query($sqlIns) === false) {
						az_generate_schedule_seed_log('INSERT TMP failed: ' . $conn->error);
						return false;
					}
				}
				return true;
			}

			foreach ($rows as $row) {
				$rank++;
				$instId = (int) $row['Institucion_ID'];
				$desc = (string) $row['Institucion_DESC'];
				$teamCount = (int) $row['TeamCount'];
				$realId = (int) $row['RealInstitucion_ID'];
				$stmt->bind_param('iisii', $rank, $instId, $desc, $teamCount, $realId);
				if (!$stmt->execute()) {
					az_generate_schedule_seed_log('INSERT TMP execute failed: ' . $stmt->error);
					$stmt->close();
					return false;
				}
			}
			$stmt->close();
			return true;
		} catch (Throwable $e) {
			az_generate_schedule_seed_log('build_seed_tmp exception: ' . $e->getMessage());
			return false;
		}
	}
}

if (!function_exists('az_generate_schedule_institution_seeds')) {
	function az_generate_schedule_institution_seeds($Config, $schema, $Season) {
		$seeds = array();
		$built = az_generate_schedule_build_seed_tmp($Config, $schema, $Season);

		if ($built) {
			$tmp = az_generate_schedule_seed_tmp_name();
			$conn = $Config->connect();
			if ($conn) {
				try {
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
						return $seeds;
					}
				} catch (Throwable $e) {
					az_generate_schedule_seed_log('read TMP exception: ' . $e->getMessage());
				}
			}
		}

		// Fallback: same ranking in PHP if TMP unavailable.
		$rank = 0;
		foreach (az_generate_schedule_fetch_rank_rows($Config, $schema, $Season) as $row) {
			$rank++;
			$seeds[] = array(
				'seed' => $rank,
				'rank' => $rank,
				'Institucion_ID' => (int) $row['Institucion_ID'],
				'Institucion_DESC' => (string) $row['Institucion_DESC'],
				'TeamCount' => (int) $row['TeamCount'],
				'RealInstitucion_ID' => (int) $row['RealInstitucion_ID'],
				'IsSoloTeam' => ((int) $row['RealInstitucion_ID'] === 0),
			);
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

		try {
			$res = $Config->query($sql);
			if ($res && $res->num_rows > 0) {
				while ($t = $res->fetch_assoc()) {
					$teams[] = array(
						'Equipo_ID' => (int) $t['Equipo_ID'],
						'Equipo_DESC' => (string) $t['Equipo_DESC'],
					);
				}
			}
		} catch (Throwable $e) {
			az_generate_schedule_seed_log('teams_for_seed exception: ' . $e->getMessage());
		}
		return $teams;
	}
}

if (!function_exists('az_generate_schedule_seeded_team_ids_for_category')) {
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

		try {
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
		} catch (Throwable $e) {
			az_generate_schedule_seed_log('seeded_team_ids TMP exception: ' . $e->getMessage());
		}

		if (!is_array($institutionSeeds)) {
			$institutionSeeds = az_generate_schedule_institution_seeds($Config, $schema, $Season);
		}
		foreach ($institutionSeeds as $seed) {
			$teams = az_generate_schedule_teams_for_seed_category($Config, $schema, $Season, $catId, $seed);
			foreach ($teams as $t) {
				$teamIds[] = (int) $t['Equipo_ID'];
			}
		}
		return $teamIds;
	}
}
