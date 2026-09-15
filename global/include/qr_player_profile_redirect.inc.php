<?php
/**
 * Credential QR landing: set season/category from the player, then open the
 * main site so the menu and top bar load with the soccer player profile.
 */
	$player = 0;
	if (function_exists('SanitizeInteger')) {
		$player = (int) SanitizeInteger($_GET['Jugador_ID'] ?? $_GET['jugador_Id'] ?? $_GET['playerID'] ?? 0);
	} else {
		$player = (int) preg_replace('/[^0-9-]/', '', (string) ($_GET['Jugador_ID'] ?? $_GET['jugador_Id'] ?? $_GET['playerID'] ?? 0));
	}

	if ($player > 0 && isset($Config) && is_object($Config)) {
		$schema = $Config->getSchema();
		$alias = $Config->getAlias();
		$sql = "SELECT e.Torneo_ID, e.Fuerza
			FROM $schema.Jugadores a
				JOIN $schema.Equipos e ON a.Equipo_ID = e.Equipo_ID
			WHERE a.Jugador_ID = $player
			ORDER BY e.Torneo_ID DESC
			LIMIT 1";
		$result = $Config->query($sql);
		if ($result && $row = $result->fetch_assoc()) {
			$torneo = (int) $row['Torneo_ID'];
			$fuerza = (int) $row['Fuerza'];
			if ($torneo > 0) {
				setcookie($alias . 'season', (string) $torneo, 0, '/');
			}
			if ($fuerza > 0) {
				setcookie($alias . 'category', (string) $fuerza, 0, '/');
			}
		}
		header('Location: index.php?Jugador_ID=' . $player);
		exit;
	}

	header('Location: index.php');
	exit;
