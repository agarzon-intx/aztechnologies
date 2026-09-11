<?php
/**
 * Balanced round-robin helpers for Generate Schedule.
 * Enforces at most $maxConsec consecutive home or away appearances per team.
 */

if (!function_exists('az_rr_circle_pairings')) {
	/**
	 * Circle-method unordered pairings (one single RR).
	 * Returns rounds => array('games' => [[teamA, teamB], ...], 'bye' => teamId|null).
	 */
	function az_rr_circle_pairings(array $teamIds) {
		$teams = array_values($teamIds);
		$n = count($teams);
		if ($n < 2) {
			return array();
		}
		if ($n % 2 === 1) {
			$teams[] = null;
			$n++;
		}
		$rounds = $n - 1;
		$half = (int) ($n / 2);
		$result = array();
		for ($r = 0; $r < $rounds; $r++) {
			$games = array();
			$bye = null;
			for ($i = 0; $i < $half; $i++) {
				$a = $teams[$i];
				$b = $teams[$n - 1 - $i];
				if ($a === null || $b === null) {
					$bye = ($a === null) ? $b : $a;
					if ($bye !== null) {
						$bye = (int) $bye;
					}
					continue;
				}
				$games[] = array((int) $a, (int) $b);
			}
			$result[] = array(
				'games' => $games,
				'bye' => $bye,
			);
			$fixed = array_shift($teams);
			$last = array_pop($teams);
			array_unshift($teams, $last);
			array_unshift($teams, $fixed);
		}
		return $result;
	}
}

if (!function_exists('az_rr_streak_ok')) {
	/**
	 * Would assigning $side ('H' or 'A') to $team violate max consecutive?
	 * $history[$teamId] = list of 'H'|'A' for prior rounds (chronological).
	 */
	function az_rr_streak_ok(array $history, $teamId, $side, $maxConsec) {
		$teamId = (int) $teamId;
		$maxConsec = (int) $maxConsec;
		if ($maxConsec < 1) {
			return true;
		}
		$seq = isset($history[$teamId]) ? $history[$teamId] : array();
		$run = 0;
		for ($i = count($seq) - 1; $i >= 0; $i--) {
			if ($seq[$i] === $side) {
				$run++;
			} else {
				break;
			}
		}
		return ($run + 1) <= $maxConsec;
	}
}

if (!function_exists('az_rr_orientation_score')) {
	/**
	 * Lower is better. Penalize streak violations heavily; prefer balancing home counts.
	 */
	function az_rr_orientation_score(array $history, array $homeCount, $homeId, $awayId, $maxConsec) {
		$score = 0;
		if (!az_rr_streak_ok($history, $homeId, 'H', $maxConsec)) {
			$score += 1000;
		}
		if (!az_rr_streak_ok($history, $awayId, 'A', $maxConsec)) {
			$score += 1000;
		}
		$hHome = isset($homeCount[$homeId]) ? (int) $homeCount[$homeId] : 0;
		$aHome = isset($homeCount[$awayId]) ? (int) $homeCount[$awayId] : 0;
		// Prefer giving home to the team with fewer homes so far.
		$score += $hHome * 2;
		$score -= $aHome;
		return $score;
	}
}

if (!function_exists('az_rr_assign_home_away')) {
	/**
	 * Assign home/away for unordered pairings with max consecutive constraint.
	 * Input/output rounds: array('games' => [[home,away],...], 'bye' => id|null).
	 */
	function az_rr_assign_home_away(array $unorderedRounds, $maxConsec = 2) {
		$maxConsec = (int) $maxConsec;
		if ($maxConsec < 1) {
			$maxConsec = 2;
		}
		$history = array();
		$homeCount = array();
		$result = array();

		foreach ($unorderedRounds as $round) {
			$pairs = isset($round['games']) && is_array($round['games']) ? $round['games'] : (is_array($round) && isset($round[0]) ? $round : array());
			// Support legacy flat round list.
			if (!isset($round['games']) && is_array($round) && isset($round[0]) && is_array($round[0])) {
				$pairs = $round;
			}
			$bye = isset($round['bye']) ? $round['bye'] : null;
			$roundGames = array();
			$roundHist = $history;
			$roundHome = $homeCount;
			foreach ($pairs as $pair) {
				if (!is_array($pair) || count($pair) < 2) {
					continue;
				}
				$a = (int) $pair[0];
				$b = (int) $pair[1];
				$scoreAB = az_rr_orientation_score($roundHist, $roundHome, $a, $b, $maxConsec);
				$scoreBA = az_rr_orientation_score($roundHist, $roundHome, $b, $a, $maxConsec);
				if ($scoreBA < $scoreAB) {
					$home = $b;
					$away = $a;
				} else {
					$home = $a;
					$away = $b;
				}
				$roundGames[] = array($home, $away);
				if (!isset($roundHist[$home])) {
					$roundHist[$home] = array();
				}
				if (!isset($roundHist[$away])) {
					$roundHist[$away] = array();
				}
				$roundHist[$home][] = 'H';
				$roundHist[$away][] = 'A';
				$roundHome[$home] = (isset($roundHome[$home]) ? (int) $roundHome[$home] : 0) + 1;
			}
			$result[] = array(
				'games' => $roundGames,
				'bye' => ($bye === null || $bye === '') ? null : (int) $bye,
			);
			$history = $roundHist;
			$homeCount = $roundHome;
		}

		$result = az_rr_repair_consecutive($result, $maxConsec);
		return $result;
	}
}

if (!function_exists('az_rr_build_history_from_rounds')) {
	function az_rr_build_history_from_rounds(array $rounds) {
		$history = array();
		foreach ($rounds as $games) {
			$seen = array();
			foreach ($games as $pair) {
				$h = (int) $pair[0];
				$a = (int) $pair[1];
				if (!isset($history[$h])) {
					$history[$h] = array();
				}
				if (!isset($history[$a])) {
					$history[$a] = array();
				}
				$history[$h][] = 'H';
				$history[$a][] = 'A';
				$seen[$h] = true;
				$seen[$a] = true;
			}
		}
		return $history;
	}
}

if (!function_exists('az_rr_team_streak_at')) {
	function az_rr_team_streak_at(array $history, $teamId) {
		$seq = isset($history[$teamId]) ? $history[$teamId] : array();
		if (count($seq) === 0) {
			return array('side' => '', 'count' => 0);
		}
		$side = $seq[count($seq) - 1];
		$count = 0;
		for ($i = count($seq) - 1; $i >= 0; $i--) {
			if ($seq[$i] === $side) {
				$count++;
			} else {
				break;
			}
		}
		return array('side' => $side, 'count' => $count);
	}
}

if (!function_exists('az_rr_round_games')) {
	/** Normalize a round to a list of [home,away] pairs. */
	function az_rr_round_games($round) {
		if (is_array($round) && isset($round['games']) && is_array($round['games'])) {
			return $round['games'];
		}
		if (is_array($round) && isset($round[0]) && is_array($round[0])) {
			return $round;
		}
		return array();
	}
}

if (!function_exists('az_rr_round_bye')) {
	function az_rr_round_bye($round) {
		if (is_array($round) && array_key_exists('bye', $round) && $round['bye'] !== null && $round['bye'] !== '') {
			return (int) $round['bye'];
		}
		return null;
	}
}

if (!function_exists('az_rr_repair_consecutive')) {
	/**
	 * Flip individual games when a team would have more than $maxConsec consecutive H or A.
	 * Preserves structured rounds with bye.
	 */
	function az_rr_repair_consecutive(array $rounds, $maxConsec = 2) {
		$maxConsec = (int) $maxConsec;
		$nRounds = count($rounds);
		for ($pass = 0; $pass < 3; $pass++) {
			$changed = false;
			$history = array();
			for ($r = 0; $r < $nRounds; $r++) {
				$games = az_rr_round_games($rounds[$r]);
				$bye = az_rr_round_bye($rounds[$r]);
				for ($g = 0, $gCount = count($games); $g < $gCount; $g++) {
					$home = (int) $games[$g][0];
					$away = (int) $games[$g][1];
					$homeOk = az_rr_streak_ok($history, $home, 'H', $maxConsec);
					$awayOk = az_rr_streak_ok($history, $away, 'A', $maxConsec);
					if ($homeOk && $awayOk) {
						continue;
					}
					if (az_rr_streak_ok($history, $away, 'H', $maxConsec) && az_rr_streak_ok($history, $home, 'A', $maxConsec)) {
						$games[$g] = array($away, $home);
						$changed = true;
					}
				}
				$rounds[$r] = array(
					'games' => $games,
					'bye' => $bye,
				);
				foreach ($games as $pair) {
					$h = (int) $pair[0];
					$a = (int) $pair[1];
					if (!isset($history[$h])) {
						$history[$h] = array();
					}
					if (!isset($history[$a])) {
						$history[$a] = array();
					}
					$history[$h][] = 'H';
					$history[$a][] = 'A';
				}
			}
			if (!$changed) {
				break;
			}
		}
		return $rounds;
	}
}

if (!function_exists('az_rr_balanced_rounds')) {
	/**
	 * Full single RR with home/away assignment (max 2 consecutive H/A by default).
	 * Returns rounds => array('games'=>..., 'bye'=>...).
	 */
	function az_rr_balanced_rounds(array $teamIds, $maxConsec = 2) {
		$pairings = az_rr_circle_pairings($teamIds);
		if (count($pairings) === 0) {
			return array();
		}
		return az_rr_assign_home_away($pairings, $maxConsec);
	}
}

if (!function_exists('az_rr_expand_weeks')) {
	/**
	 * Expand RR rounds to $weeksRequested. Odd cycles flip home/away (double-RR style),
	 * then repair consecutive streaks across the full horizon. Byes are preserved.
	 */
	function az_rr_expand_weeks(array $rrRounds, $weeksRequested, $maxConsec = 2) {
		$weeksRequested = (int) $weeksRequested;
		if ($weeksRequested < 1 || count($rrRounds) === 0) {
			return array();
		}
		$schedule = array();
		$cycle = 0;
		while (count($schedule) < $weeksRequested) {
			foreach ($rrRounds as $round) {
				$games = az_rr_round_games($round);
				$bye = az_rr_round_bye($round);
				if (($cycle % 2) === 1) {
					$flipped = array();
					foreach ($games as $pair) {
						$flipped[] = array((int) $pair[1], (int) $pair[0]);
					}
					$games = $flipped;
				}
				$schedule[] = array(
					'games' => $games,
					'bye' => $bye,
				);
				if (count($schedule) >= $weeksRequested) {
					break;
				}
			}
			$cycle++;
		}
		return az_rr_repair_consecutive($schedule, $maxConsec);
	}
}

if (!function_exists('az_gs_week_sun_sat')) {
	/**
	 * Match week.js DateChange: Sunday–Saturday around $fechaYmd.
	 * Returns array(fecha, inicio, fin) as Y-m-d.
	 */
	function az_gs_week_sun_sat($fechaYmd) {
		$dt = DateTime::createFromFormat('Y-m-d', (string) $fechaYmd);
		if (!$dt) {
			$dt = new DateTime((string) $fechaYmd);
		}
		$fecha = $dt->format('Y-m-d');
		$dow = (int) $dt->format('w'); // 0=Sun .. 6=Sat
		$inicio = clone $dt;
		if ($dow > 0) {
			$inicio->modify('-' . $dow . ' days');
		}
		$fin = clone $inicio;
		$fin->modify('+6 days');
		return array($fecha, $inicio->format('Y-m-d'), $fin->format('Y-m-d'));
	}
}

if (!function_exists('az_gs_simulate_jornadas')) {
	/**
	 * Build N simulated Jornada rows starting at $startDate (weekly).
	 * @param int $ordenStart first Jornada_Orden value (default 1)
	 */
	function az_gs_simulate_jornadas($startDate, $count, $calendarioId = 0, $ordenStart = 1) {
		$count = (int) $count;
		$calendarioId = (int) $calendarioId;
		$ordenStart = (int) $ordenStart;
		if ($ordenStart < 1) {
			$ordenStart = 1;
		}
		$rows = array();
		if ($count < 1 || $startDate === '') {
			return $rows;
		}
		$base = DateTime::createFromFormat('Y-m-d', (string) $startDate);
		if (!$base) {
			$base = new DateTime((string) $startDate);
		}
		for ($i = 0; $i < $count; $i++) {
			$weekDate = clone $base;
			if ($i > 0) {
				$weekDate->modify('+' . ($i * 7) . ' days');
			}
			list($fecha, $inicio, $fin) = az_gs_week_sun_sat($weekDate->format('Y-m-d'));
			$n = $ordenStart + $i;
			$rows[] = array(
				'Jornada_ID' => 0,
				'Jornada_Desc' => 'Jornada ' . $n,
				'Jornada_DescCorta' => (string) $n,
				'Jornada_Orden' => $n,
				'Fecha' => $fecha,
				'Fecha_Inicio' => $inicio,
				'Fecha_Fin' => $fin,
				'Calendario_ID' => $calendarioId,
				'createWeek' => true,
			);
		}
		return $rows;
	}
}

if (!function_exists('az_gs_extend_jornadas')) {
	/**
	 * Keep existing jornadas and append simulated weeks until $weeksRequested.
	 * Extra weeks start 7 days after the last existing Fecha.
	 */
	function az_gs_extend_jornadas(array $existing, $weeksRequested, $calendarioId = 0) {
		$weeksRequested = (int) $weeksRequested;
		$existing = array_values($existing);
		$have = count($existing);
		if ($weeksRequested <= $have) {
			return array_slice($existing, 0, $weeksRequested);
		}
		$need = $weeksRequested - $have;
		$last = $existing[$have - 1];
		$lastFecha = isset($last['Fecha']) ? (string) $last['Fecha'] : '';
		if ($lastFecha === '' && isset($last['Fecha_Inicio'])) {
			$lastFecha = (string) $last['Fecha_Inicio'];
		}
		$lastOrden = isset($last['Jornada_Orden']) ? (int) $last['Jornada_Orden'] : $have;
		if ($lastFecha === '') {
			return $existing;
		}
		$next = DateTime::createFromFormat('Y-m-d', $lastFecha);
		if (!$next) {
			$next = new DateTime($lastFecha);
		}
		$next->modify('+7 days');
		$calId = $calendarioId ? (int) $calendarioId : (isset($last['Calendario_ID']) ? (int) $last['Calendario_ID'] : 0);
		$extra = az_gs_simulate_jornadas($next->format('Y-m-d'), $need, $calId, $lastOrden + 1);
		return array_merge($existing, $extra);
	}
}
