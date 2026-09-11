<?php
/**
 * Balanced round-robin helpers for Generate Schedule.
 * Enforces at most $maxConsec consecutive home or away appearances per team.
 */

if (!function_exists('az_rr_circle_pairings')) {
	/**
	 * Circle-method unordered pairings (one single RR).
	 * Returns rounds => list of [teamA, teamB] (order not home/away yet).
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
			for ($i = 0; $i < $half; $i++) {
				$a = $teams[$i];
				$b = $teams[$n - 1 - $i];
				if ($a === null || $b === null) {
					continue;
				}
				$games[] = array((int) $a, (int) $b);
			}
			$result[] = $games;
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
	 * Returns rounds => list of [homeId, awayId].
	 */
	function az_rr_assign_home_away(array $unorderedRounds, $maxConsec = 2) {
		$maxConsec = (int) $maxConsec;
		if ($maxConsec < 1) {
			$maxConsec = 2;
		}
		$history = array();
		$homeCount = array();
		$result = array();

		foreach ($unorderedRounds as $pairs) {
			$roundGames = array();
			$roundHist = $history;
			$roundHome = $homeCount;
			foreach ($pairs as $pair) {
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
			$result[] = $roundGames;
			$history = $roundHist;
			$homeCount = $roundHome;
		}

		// Repair pass: try flipping games that still violate streak.
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

if (!function_exists('az_rr_repair_consecutive')) {
	/**
	 * Flip individual games when a team would have more than $maxConsec consecutive H or A.
	 */
	function az_rr_repair_consecutive(array $rounds, $maxConsec = 2) {
		$maxConsec = (int) $maxConsec;
		$nRounds = count($rounds);
		for ($pass = 0; $pass < 3; $pass++) {
			$changed = false;
			$history = array();
			for ($r = 0; $r < $nRounds; $r++) {
				$games = $rounds[$r];
				for ($g = 0, $gCount = count($games); $g < $gCount; $g++) {
					$home = (int) $games[$g][0];
					$away = (int) $games[$g][1];
					$homeOk = az_rr_streak_ok($history, $home, 'H', $maxConsec);
					$awayOk = az_rr_streak_ok($history, $away, 'A', $maxConsec);
					if ($homeOk && $awayOk) {
						continue;
					}
					// Try flip if that orientation is legal for both.
					if (az_rr_streak_ok($history, $away, 'H', $maxConsec) && az_rr_streak_ok($history, $home, 'A', $maxConsec)) {
						$rounds[$r][$g] = array($away, $home);
						$changed = true;
					}
				}
				foreach ($rounds[$r] as $pair) {
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
	 * then repair consecutive streaks across the full horizon.
	 */
	function az_rr_expand_weeks(array $rrRounds, $weeksRequested, $maxConsec = 2) {
		$weeksRequested = (int) $weeksRequested;
		if ($weeksRequested < 1 || count($rrRounds) === 0) {
			return array();
		}
		$schedule = array();
		$cycle = 0;
		while (count($schedule) < $weeksRequested) {
			foreach ($rrRounds as $roundGames) {
				$games = $roundGames;
				if (($cycle % 2) === 1) {
					$flipped = array();
					foreach ($games as $pair) {
						$flipped[] = array((int) $pair[1], (int) $pair[0]);
					}
					$games = $flipped;
				}
				$schedule[] = $games;
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
	 */
	function az_gs_simulate_jornadas($startDate, $count, $calendarioId = 0) {
		$count = (int) $count;
		$calendarioId = (int) $calendarioId;
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
			$n = $i + 1;
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
