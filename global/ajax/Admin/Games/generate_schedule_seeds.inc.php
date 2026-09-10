<?php
/**
 * Tournament-wide institution seeds stored in physical table GenerateScheduleSeeds,
 * scoped by PHP session_id() so later reads match the requesting session.
 * When Institucion_ID = 0, each team is its own seed (keyed by Equipo_ID).
 */

if (!function_exists('az_generate_schedule_seed_table_name')) {
	function az_generate_schedule_seed_table_name() {
		return 'GenerateScheduleSeeds';
	}
}

if (!function_exists('az_generate_schedule_seed_log')) {
	function az_generate_schedule_seed_log($msg) {
		$path = __DIR__ . DIRECTORY_SEPARATOR . 'generate_schedule_error.log';
		@file_put_contents($path, date('c') . ' ' . $msg . "\n", FILE_APPEND | LOCK_EX);
	}
}

if (!function_exists('az_generate_schedule_request_session_id')) {
	function az_generate_schedule_request_session_id() {
		if (session_status() !== PHP_SESSION_ACTIVE) {
			@session_start();
		}
		$sid = session_id();
		return is_string($sid) ? $sid : '';
	}
}

if (!function_exists('az_generate_schedule_ensure_seed_table')) {
	function az_generate_schedule_ensure_seed_table($Config, $schema) {
		$table = az_generate_schedule_seed_table_name();
		$sql = "CREATE TABLE IF NOT EXISTS `$schema`.`$table` (
				`Session_ID` varchar(128) NOT NULL,
				`Torneo_ID` bigint(20) NOT NULL,
				`rank` int(11) NOT NULL,
				`Institucion_ID` bigint(20) NOT NULL,
				`Institucion_DESC` varchar(255) DEFAULT NULL,
				`TeamCount` int(11) NOT NULL DEFAULT 0,
				`RealInstitucion_ID` bigint(20) NOT NULL DEFAULT 0,
				`CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`Session_ID`,`Torneo_ID`,`rank`),
				KEY `idx_gs_seeds_session` (`Session_ID`),
				KEY `idx_gs_seeds_created` (`CreatedAt`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

		$connAdmin = null;
		try {
			$connAdmin = $Config->connectAdmin();
		} catch (Throwable $e) {
			$connAdmin = null;
		}
		if ($connAdmin) {
			$ok = $connAdmin->query($sql);
			if ($ok === false) {
				az_generate_schedule_seed_log('ensure table (admin) failed: ' . $connAdmin->error);
			} else {
				return true;
			}
		}

		$conn = $Config->connect();
		if (!$conn) {
			az_generate_schedule_seed_log('ensure table: no connection');
			return false;
		}
		$ok = $conn->query($sql);
		if ($ok === false) {
			az_generate_schedule_seed_log('ensure table failed: ' . $conn->error);
			return false;
		}
		return true;
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

if (!function_exists('az_generate_schedule_build_seed_table')) {
	/**
	 * Rebuilds GenerateScheduleSeeds rows for the current PHP session + tournament.
	 */
	function az_generate_schedule_build_seed_table($Config, $schema, $Season) {
		$Season = (int) $Season;
		$sessionId = az_generate_schedule_request_session_id();
		if ($sessionId === '') {
			az_generate_schedule_seed_log('build: empty session_id');
			return false;
		}
		if (!az_generate_schedule_ensure_seed_table($Config, $schema)) {
			return false;
		}

		$table = az_generate_schedule_seed_table_name();
		$conn = $Config->connect();
		if (!$conn) {
			az_generate_schedule_seed_log('build: connect() failed');
			return false;
		}

		try {
			$sidEsc = $conn->real_escape_string($sessionId);

			// Drop prior rows for this session (and stale rows older than 2 days).
			$conn->query("DELETE FROM `$schema`.`$table` WHERE Session_ID = '$sidEsc'");
			$conn->query("DELETE FROM `$schema`.`$table` WHERE CreatedAt < (NOW() - INTERVAL 2 DAY)");

			$rows = az_generate_schedule_fetch_rank_rows($Config, $schema, $Season);
			$rank = 0;
			foreach ($rows as $row) {
				$rank++;
				$instId = (int) $row['Institucion_ID'];
				$desc = $conn->real_escape_string((string) $row['Institucion_DESC']);
				$teamCount = (int) $row['TeamCount'];
				$realId = (int) $row['RealInstitucion_ID'];
				$sqlIns = "INSERT INTO `$schema`.`$table`
						(Session_ID, Torneo_ID, `rank`, Institucion_ID, Institucion_DESC, TeamCount, RealInstitucion_ID)
					VALUES
						('$sidEsc', $Season, $rank, $instId, '$desc', $teamCount, $realId)";
				if ($conn->query($sqlIns) === false) {
					az_generate_schedule_seed_log('INSERT failed: ' . $conn->error);
					return false;
				}
			}
			return true;
		} catch (Throwable $e) {
			az_generate_schedule_seed_log('build_seed_table exception: ' . $e->getMessage());
			return false;
		}
	}
}

// Back-compat alias used by older call sites.
if (!function_exists('az_generate_schedule_build_seed_tmp')) {
	function az_generate_schedule_build_seed_tmp($Config, $schema, $Season) {
		return az_generate_schedule_build_seed_table($Config, $schema, $Season);
	}
}

if (!function_exists('az_generate_schedule_institution_seeds')) {
	function az_generate_schedule_institution_seeds($Config, $schema, $Season) {
		$seeds = array();
		$Season = (int) $Season;
		$sessionId = az_generate_schedule_request_session_id();
		$built = az_generate_schedule_build_seed_table($Config, $schema, $Season);

		if ($built && $sessionId !== '') {
			$table = az_generate_schedule_seed_table_name();
			$conn = $Config->connect();
			if ($conn) {
				try {
					$sidEsc = $conn->real_escape_string($sessionId);
					$res = $conn->query("SELECT `rank`, Institucion_ID, Institucion_DESC, TeamCount, RealInstitucion_ID
							FROM `$schema`.`$table`
							WHERE Session_ID = '$sidEsc'
								AND Torneo_ID = $Season
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
					az_generate_schedule_seed_log('read table exception: ' . $e->getMessage());
				}
			}
		}

		// Fallback: same ranking in PHP if table unavailable.
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
		$table = az_generate_schedule_seed_table_name();
		$sessionId = az_generate_schedule_request_session_id();
		$conn = $Config->connect();

		if ($conn && $sessionId !== '') {
			$sidEsc = $conn->real_escape_string($sessionId);
			$sql = "SELECT e.Equipo_ID
					FROM `$schema`.`$table` t
						INNER JOIN $schema.Equipos e
							ON e.Torneo_ID = $Season
							AND e.Fuerza = $catId
							AND IFNULL(e.Activo, 0) = 1
							AND (
								(IFNULL(t.RealInstitucion_ID, 0) = 0 AND e.Equipo_ID = t.Institucion_ID)
								OR (IFNULL(t.RealInstitucion_ID, 0) <> 0 AND e.Institucion_ID = t.Institucion_ID)
							)
					WHERE t.Session_ID = '$sidEsc'
						AND t.Torneo_ID = $Season
					ORDER BY t.`rank` ASC, e.Equipo_DESC ASC";

			try {
				$res = $conn->query($sql);
				if ($res === false || $res === null) {
					az_generate_schedule_build_seed_table($Config, $schema, $Season);
					$res = $conn->query($sql);
				}
				if ($res && $res->num_rows > 0) {
					while ($t = $res->fetch_assoc()) {
						$teamIds[] = (int) $t['Equipo_ID'];
					}
					return $teamIds;
				}
			} catch (Throwable $e) {
				az_generate_schedule_seed_log('seeded_team_ids exception: ' . $e->getMessage());
			}
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
