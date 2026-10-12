<?php
/**
 * Coach/admin access for moving a player between teams.
 * Admins can always move. Coaches can move onto an assigned team when the
 * player already belongs to an assigned team, or when the player's current
 * team shares Institucion_ID > 0 with the coach — even if the coach has no
 * access to that source team.
 */
if (!function_exists('az_user_is_league_admin')) {
	/**
	 * League admin (Equipo_ID 0) or referee (-1). True if present in usuarios_equipo
	 * or in the session team list (including mixed lists like "0,123").
	 */
	function az_user_is_league_admin($Config, $schema, $fgmembersite) {
		$sessionEq = trim((string) $fgmembersite->UserEquipo());
		if ($sessionEq !== '') {
			foreach (explode(',', $sessionEq) as $part) {
				$token = trim($part);
				if ($token === '') {
					continue;
				}
				if ($token === '0' || $token === '-1' || (int) $token === -1) {
					return true;
				}
			}
		}
		$username = '';
		if (isset($_SESSION[$Config->getAlias() . 'username'])) {
			$username = SanitizeUsername($_SESSION[$Config->getAlias() . 'username']);
		}
		if ($username === '') {
			return false;
		}
		$userEsc = $username;
		$conn = $Config->connect();
		if ($conn) {
			$userEsc = $conn->real_escape_string($username);
		}
		$ue = $Config->query("SELECT DISTINCT Equipo_ID FROM $schema.usuarios_equipo WHERE username = '" . $userEsc . "'");
		if ($ue && $ue->num_rows > 0) {
			while ($r = $ue->fetch_assoc()) {
				$id = (int) $r['Equipo_ID'];
				if ($id === 0 || $id === -1) {
					return true;
				}
			}
		}
		return false;
	}
}

if (!function_exists('az_user_allowed_equipo_ids')) {
	/** Positive Equipo_ID list for coaches; empty when admin (no restriction). */
	function az_user_allowed_equipo_ids($Config, $schema, $fgmembersite, $isAdmin = null) {
		if ($isAdmin === null) {
			$isAdmin = az_user_is_league_admin($Config, $schema, $fgmembersite);
		}
		if ($isAdmin) {
			return array();
		}
		$allowed = array();
		$username = '';
		if (isset($_SESSION[$Config->getAlias() . 'username'])) {
			$username = SanitizeUsername($_SESSION[$Config->getAlias() . 'username']);
		}
		if ($username !== '') {
			$userEsc = $username;
			$conn = $Config->connect();
			if ($conn) {
				$userEsc = $conn->real_escape_string($username);
			}
			$ue = $Config->query("SELECT DISTINCT Equipo_ID FROM $schema.usuarios_equipo WHERE username = '" . $userEsc . "'");
			if ($ue && $ue->num_rows > 0) {
				while ($r = $ue->fetch_assoc()) {
					$id = (int) $r['Equipo_ID'];
					if ($id > 0) {
						$allowed[$id] = $id;
					}
				}
			}
		}
		if (count($allowed) === 0) {
			foreach (explode(',', (string) $fgmembersite->UserEquipo()) as $part) {
				$id = (int) trim($part);
				if ($id > 0) {
					$allowed[$id] = $id;
				}
			}
		}
		return array_values($allowed);
	}
}

if (!function_exists('az_equipo_institucion_id')) {
	function az_equipo_institucion_id($Config, $schema, $equipoId) {
		$equipoId = (int) $equipoId;
		if ($equipoId <= 0) {
			return 0;
		}
		$result = $Config->query("SELECT IFNULL(Institucion_ID, 0) AS Institucion_ID
			FROM $schema.Equipos WHERE Equipo_ID = $equipoId LIMIT 1");
		if ($result && ($row = $result->fetch_assoc())) {
			return (int) $row['Institucion_ID'];
		}
		return 0;
	}
}

if (!function_exists('az_coach_institution_ids')) {
	function az_coach_institution_ids($Config, $schema, $allowedIds) {
		$ids = array();
		foreach ((array) $allowedIds as $equipoId) {
			$equipoId = (int) $equipoId;
			if ($equipoId > 0) {
				$ids[$equipoId] = $equipoId;
			}
		}
		if (!$ids) {
			return array();
		}
		$institutions = array();
		$result = $Config->query("SELECT IFNULL(Institucion_ID, 0) AS Institucion_ID
			FROM $schema.Equipos WHERE Equipo_ID IN (" . implode(',', $ids) . ")");
		while ($result && ($row = $result->fetch_assoc())) {
			$inst = (int) $row['Institucion_ID'];
			if ($inst > 0) {
				$institutions[$inst] = $inst;
			}
		}
		return array_values($institutions);
	}
}

if (!function_exists('az_player_coach_can_move_to_team')) {
	function az_player_coach_can_move_to_team($Config, $schema, $srcTeam, $destTeam, $allowedIds, $isAdmin) {
		if ($isAdmin) {
			return true;
		}
		$srcTeam = (int) $srcTeam;
		$destTeam = (int) $destTeam;
		if ($destTeam <= 0 || !in_array($destTeam, $allowedIds, true)) {
			return false;
		}
		if ($srcTeam > 0 && in_array($srcTeam, $allowedIds, true)) {
			return true;
		}
		$srcInst = az_equipo_institucion_id($Config, $schema, $srcTeam);
		if ($srcInst <= 0) {
			return false;
		}
		$coachInst = az_coach_institution_ids($Config, $schema, $allowedIds);
		return in_array($srcInst, $coachInst, true);
	}
}
