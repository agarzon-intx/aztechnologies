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

if (!function_exists('az_rr_round_as_unordered')) {
	/**
	 * Strip orientations so assign can choose local/away freely.
	 * Input may be oriented (byeGame) or unordered (bye).
	 */
	function az_rr_round_as_unordered($round) {
		$games = array();
		foreach (az_rr_round_games($round) as $pair) {
			if (!is_array($pair) || count($pair) < 2) {
				continue;
			}
			$a = (int) $pair[0];
			$b = (int) $pair[1];
			if ($a > 0 && $b > 0) {
				$games[] = array($a, $b);
			}
		}
		$bye = az_rr_round_bye($round);
		return array(
			'games' => $games,
			'bye' => $bye,
		);
	}
}

if (!function_exists('az_rr_assign_home_away')) {
	/**
	 * Assign home/away with a hard max-consecutive constraint (default 2), including bye slots.
	 * Uses DFS over orientations per round. Bye open side is 0 (future team).
	 * Output: array('games' => [[home,away],...], 'byeGame' => [home,away]|null).
	 */
	function az_rr_assign_home_away(array $unorderedRounds, $maxConsec = 2) {
		$maxConsec = (int) $maxConsec;
		if ($maxConsec < 1) {
			$maxConsec = 2;
		}
		$roundsIn = array();
		foreach ($unorderedRounds as $round) {
			$roundsIn[] = az_rr_round_as_unordered($round);
		}
		$n = count($roundsIn);
		if ($n === 0) {
			return array();
		}

		$best = null;
		$stack = array();
		$calls = 0;
		$maxCalls = 2000000;

		$search = function ($r, $history, $homeCount) use (
			&$search,
			&$stack,
			&$best,
			&$calls,
			$roundsIn,
			$n,
			$maxConsec,
			$maxCalls
		) {
			$calls++;
			if ($calls > $maxCalls) {
				return false;
			}
			if ($r >= $n) {
				$best = $stack;
				return true;
			}

			$pairs = $roundsIn[$r]['games'];
			$byeTeam = $roundsIn[$r]['bye'];
			$g = count($pairs);
			$limit = 1 << $g;

			// Score masks: prefer legal + balanced homes (lower is better). Try best first.
			$maskScores = array();
			for ($mask = 0; $mask < $limit; $mask++) {
				$histTmp = $history;
				$homeTmp = $homeCount;
				$ok = true;
				$score = 0;
				for ($i = 0; $i < $g; $i++) {
					$a = (int) $pairs[$i][0];
					$b = (int) $pairs[$i][1];
					if ($mask & (1 << $i)) {
						$home = $b;
						$away = $a;
					} else {
						$home = $a;
						$away = $b;
					}
					if (!az_rr_streak_ok($histTmp, $home, 'H', $maxConsec) || !az_rr_streak_ok($histTmp, $away, 'A', $maxConsec)) {
						$ok = false;
						break;
					}
					$score += az_rr_orientation_score($histTmp, $homeTmp, $home, $away, $maxConsec);
					if (!isset($histTmp[$home])) {
						$histTmp[$home] = array();
					}
					if (!isset($histTmp[$away])) {
						$histTmp[$away] = array();
					}
					$histTmp[$home][] = 'H';
					$histTmp[$away][] = 'A';
					$homeTmp[$home] = (isset($homeTmp[$home]) ? (int) $homeTmp[$home] : 0) + 1;
				}
				if ($ok) {
					$maskScores[] = array($score, $mask);
				}
			}
			usort($maskScores, function ($x, $y) {
				if ($x[0] === $y[0]) {
					return $x[1] - $y[1];
				}
				return ($x[0] < $y[0]) ? -1 : 1;
			});

			foreach ($maskScores as $entry) {
				$mask = $entry[1];
				$hist2 = $history;
				$homes2 = $homeCount;
				$games = array();
				for ($i = 0; $i < $g; $i++) {
					$a = (int) $pairs[$i][0];
					$b = (int) $pairs[$i][1];
					if ($mask & (1 << $i)) {
						$home = $b;
						$away = $a;
					} else {
						$home = $a;
						$away = $b;
					}
					$games[] = array($home, $away);
					if (!isset($hist2[$home])) {
						$hist2[$home] = array();
					}
					if (!isset($hist2[$away])) {
						$hist2[$away] = array();
					}
					$hist2[$home][] = 'H';
					$hist2[$away][] = 'A';
					$homes2[$home] = (isset($homes2[$home]) ? (int) $homes2[$home] : 0) + 1;
				}

				$byeOpts = array(null);
				if ($byeTeam !== null && (int) $byeTeam > 0) {
					$bt = (int) $byeTeam;
					$byeOpts = array();
					$asHomeOk = az_rr_streak_ok($hist2, $bt, 'H', $maxConsec);
					$asAwayOk = az_rr_streak_ok($hist2, $bt, 'A', $maxConsec);
					$homes = isset($homes2[$bt]) ? (int) $homes2[$bt] : 0;
					// Prefer alternating / fewer homes, but always try all legal sides.
					$ordered = array();
					if ($asHomeOk && $asAwayOk) {
						$last = '';
						if (isset($hist2[$bt]) && count($hist2[$bt]) > 0) {
							$last = $hist2[$bt][count($hist2[$bt]) - 1];
						}
						if ($last === 'H') {
							$ordered = array(array(0, $bt), array($bt, 0));
						} elseif ($last === 'A') {
							$ordered = array(array($bt, 0), array(0, $bt));
						} else {
							$ordered = ($homes <= 0)
								? array(array($bt, 0), array(0, $bt))
								: array(array(0, $bt), array($bt, 0));
						}
					} elseif ($asHomeOk) {
						$ordered = array(array($bt, 0));
					} elseif ($asAwayOk) {
						$ordered = array(array(0, $bt));
					}
					if (count($ordered) === 0) {
						continue;
					}
					$byeOpts = $ordered;
				}

				foreach ($byeOpts as $bg) {
					$hist3 = $hist2;
					$homes3 = $homes2;
					if ($bg !== null) {
						az_rr_apply_pair_history($hist3, $bg[0], $bg[1]);
						if ((int) $bg[0] > 0) {
							$homes3[(int) $bg[0]] = (isset($homes3[(int) $bg[0]]) ? (int) $homes3[(int) $bg[0]] : 0) + 1;
						}
					}
					$stack[$r] = array(
						'games' => $games,
						'byeGame' => $bg,
					);
					if ($search($r + 1, $hist3, $homes3)) {
						return true;
					}
				}
			}
			return false;
		};

		// az_rr_apply_pair_history must exist before search; defined below in file — ensure order.
		if (!function_exists('az_rr_apply_pair_history')) {
			function az_rr_apply_pair_history(array &$history, $home, $away) {
				$home = (int) $home;
				$away = (int) $away;
				if ($home > 0) {
					if (!isset($history[$home])) {
						$history[$home] = array();
					}
					$history[$home][] = 'H';
				}
				if ($away > 0) {
					if (!isset($history[$away])) {
						$history[$away] = array();
					}
					$history[$away][] = 'A';
				}
			}
		}

		$ok = $search(0, array(), array());
		if ($ok && is_array($best)) {
			$result = array();
			for ($i = 0; $i < $n; $i++) {
				$result[] = isset($best[$i]) ? $best[$i] : array('games' => array(), 'byeGame' => null);
			}
			return $result;
		}

		// Fallback: greedy (should be rare) + soft repair.
		return az_rr_assign_home_away_greedy($roundsIn, $maxConsec);
	}
}

if (!function_exists('az_rr_assign_home_away_greedy')) {
	/**
	 * Greedy fallback when DFS cannot finish; still prefers legal orientations.
	 */
	function az_rr_assign_home_away_greedy(array $unorderedRounds, $maxConsec = 2) {
		$history = array();
		$homeCount = array();
		$result = array();
		foreach ($unorderedRounds as $round) {
			$u = az_rr_round_as_unordered($round);
			$pairs = $u['games'];
			$byeTeam = $u['bye'];
			$roundGames = array();
			$roundHist = $history;
			$roundHome = $homeCount;
			foreach ($pairs as $pair) {
				$a = (int) $pair[0];
				$b = (int) $pair[1];
				$cand = array();
				foreach (array(array($a, $b), array($b, $a)) as $orient) {
					$home = $orient[0];
					$away = $orient[1];
					$legal = az_rr_streak_ok($roundHist, $home, 'H', $maxConsec)
						&& az_rr_streak_ok($roundHist, $away, 'A', $maxConsec);
					$score = az_rr_orientation_score($roundHist, $roundHome, $home, $away, $maxConsec);
					$cand[] = array($legal ? 0 : 1, $score, $home, $away);
				}
				usort($cand, function ($x, $y) {
					if ($x[0] !== $y[0]) {
						return $x[0] - $y[0];
					}
					if ($x[1] === $y[1]) {
						return 0;
					}
					return ($x[1] < $y[1]) ? -1 : 1;
				});
				$home = $cand[0][2];
				$away = $cand[0][3];
				$roundGames[] = array($home, $away);
				az_rr_apply_pair_history($roundHist, $home, $away);
				$roundHome[$home] = (isset($roundHome[$home]) ? (int) $roundHome[$home] : 0) + 1;
			}
			$byeGame = null;
			if ($byeTeam !== null && (int) $byeTeam > 0) {
				$bt = (int) $byeTeam;
				$opts = array();
				if (az_rr_streak_ok($roundHist, $bt, 'H', $maxConsec)) {
					$opts[] = array($bt, 0);
				}
				if (az_rr_streak_ok($roundHist, $bt, 'A', $maxConsec)) {
					$opts[] = array(0, $bt);
				}
				if (count($opts) === 0) {
					$opts[] = array($bt, 0);
					$opts[] = array(0, $bt);
				}
				$byeGame = $opts[0];
				az_rr_apply_pair_history($roundHist, $byeGame[0], $byeGame[1]);
				if ((int) $byeGame[0] > 0) {
					$roundHome[(int) $byeGame[0]] = (isset($roundHome[(int) $byeGame[0]]) ? (int) $roundHome[(int) $byeGame[0]] : 0) + 1;
				}
			}
			$result[] = array('games' => $roundGames, 'byeGame' => $byeGame);
			$history = $roundHist;
			$homeCount = $roundHome;
		}
		return az_rr_repair_consecutive($result, $maxConsec);
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
	/** Normalize a round to a list of [home,away] pairs (real matches only). */
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

if (!function_exists('az_rr_round_bye_game')) {
	/**
	 * Bye slot as [homeId, awayId] with one side 0 (open for a future team).
	 * Also accepts legacy 'bye' => teamId (treated as home vs open).
	 */
	function az_rr_round_bye_game($round) {
		if (!is_array($round)) {
			return null;
		}
		if (isset($round['byeGame']) && is_array($round['byeGame']) && count($round['byeGame']) >= 2) {
			$h = (int) $round['byeGame'][0];
			$a = (int) $round['byeGame'][1];
			if (($h > 0 && $a === 0) || ($h === 0 && $a > 0)) {
				return array($h, $a);
			}
			return null;
		}
		if (array_key_exists('bye', $round) && $round['bye'] !== null && $round['bye'] !== '') {
			return array((int) $round['bye'], 0);
		}
		return null;
	}
}

if (!function_exists('az_rr_round_bye')) {
	function az_rr_round_bye($round) {
		$bg = az_rr_round_bye_game($round);
		if ($bg === null) {
			return null;
		}
		return ($bg[0] > 0) ? $bg[0] : $bg[1];
	}
}

if (!function_exists('az_rr_apply_pair_history')) {
	function az_rr_apply_pair_history(array &$history, $home, $away) {
		$home = (int) $home;
		$away = (int) $away;
		if ($home > 0) {
			if (!isset($history[$home])) {
				$history[$home] = array();
			}
			$history[$home][] = 'H';
		}
		if ($away > 0) {
			if (!isset($history[$away])) {
				$history[$away] = array();
			}
			$history[$away][] = 'A';
		}
	}
}

if (!function_exists('az_rr_repair_consecutive')) {
	/**
	 * Flip individual games when a team would have more than $maxConsec consecutive H or A.
	 * Bye slots ([team,0] / [0,team]) flip side the same way to keep local/away balance.
	 */
	function az_rr_repair_consecutive(array $rounds, $maxConsec = 2) {
		$maxConsec = (int) $maxConsec;
		$nRounds = count($rounds);
		for ($pass = 0; $pass < 3; $pass++) {
			$changed = false;
			$history = array();
			for ($r = 0; $r < $nRounds; $r++) {
				$games = az_rr_round_games($rounds[$r]);
				$byeGame = az_rr_round_bye_game($rounds[$r]);
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
				if ($byeGame !== null) {
					$home = (int) $byeGame[0];
					$away = (int) $byeGame[1];
					$homeOk = ($home <= 0) || az_rr_streak_ok($history, $home, 'H', $maxConsec);
					$awayOk = ($away <= 0) || az_rr_streak_ok($history, $away, 'A', $maxConsec);
					if (!($homeOk && $awayOk)) {
						$flipHome = $away;
						$flipAway = $home;
						$flipHomeOk = ($flipHome <= 0) || az_rr_streak_ok($history, $flipHome, 'H', $maxConsec);
						$flipAwayOk = ($flipAway <= 0) || az_rr_streak_ok($history, $flipAway, 'A', $maxConsec);
						if ($flipHomeOk && $flipAwayOk) {
							$byeGame = array($flipHome, $flipAway);
							$changed = true;
						}
					}
				}
				$rounds[$r] = array(
					'games' => $games,
					'byeGame' => $byeGame,
				);
				foreach ($games as $pair) {
					az_rr_apply_pair_history($history, $pair[0], $pair[1]);
				}
				if ($byeGame !== null) {
					az_rr_apply_pair_history($history, $byeGame[0], $byeGame[1]);
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
	 * Returns rounds => array('games'=>..., 'byeGame'=>...).
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
	 * including bye open slots, then repair consecutive streaks across the full horizon.
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
				$byeGame = az_rr_round_bye_game($round);
				if (($cycle % 2) === 1) {
					$flipped = array();
					foreach ($games as $pair) {
						$flipped[] = array((int) $pair[1], (int) $pair[0]);
					}
					$games = $flipped;
					if ($byeGame !== null) {
						$byeGame = array((int) $byeGame[1], (int) $byeGame[0]);
					}
				}
				$schedule[] = array(
					'games' => $games,
					'byeGame' => $byeGame,
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
