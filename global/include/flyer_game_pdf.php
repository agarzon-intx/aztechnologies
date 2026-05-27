<?php
/**
 * Single-game flyer (flyer.php / flyer-I.php) — shared PDF builder.
 */
if (!function_exists('flyer_game_juego_sql')) {

	function flyer_game_juego_sql($schema, $juegoId) {
		$juegoId = (int) $juegoId;
		return "SELECT distinct j.Juego_ID,
                j.Torneo_ID,
                j.Jornada_ID,
                jo.Jornada_Desc,
                jo.Jornada_DescCorta,
                ca.Categoria_Desc,
                j.Local_ID,
                l.Equipo_FULLDESC,
                j.Visitante_ID,
                v.Equipo_FULLDESC,
                j.Campo_ID,
                c.Campo_DESC,
                TIME_FORMAT(j.Horario, '%H:%i HRS') Horario,
                DATE_FORMAT(j.Fecha, '%e de %M') Fecha
            FROM $schema.Juegos j
            	join $schema.Equipos l on j.Local_ID = l.Equipo_ID
            	join $schema.Equipos v on j.Visitante_ID = v.Equipo_ID
                join $schema.Jornada jo on jo.Jornada_ID = j.Jornada_ID
                join $schema.Campos c on c.Campo_ID = j.Campo_ID
                join $schema.Categorias ca on ca.Categoria_ID = l.Fuerza and ca.Torneo_ID = j.Torneo_ID
            where Juego_ID = $juegoId";
	}

	/**
	 * @param FPDF $pdf
	 * @param array $pageLabels
	 */
	function flyer_game_build_pdf($pdf, $Config, $schema, $juegoId, $siteRoot, $lang, array &$pageLabels) {
		$juegoId = (int) $juegoId;
		$pageLabels = array();

		$Config->query("SET lc_time_names = 'es_MX';");

		if (!function_exists('flyer_ci_add_page')) {
			return;
		}

		$sql = flyer_game_juego_sql($schema, $juegoId);
		$result1 = $Config->query($sql);
		if ($result1 && $result1->num_rows > 0) {
			while ($row1 = $result1->fetch_assoc()) {
				$pageLabels[] = 'juego-' . $row1['Juego_ID'];
				flyer_ci_add_page($pdf, $siteRoot, $row1);
			}
		} else {
			$pageLabels[] = 'sin-juego';
			$pdf->AddPage();
			$pdf->Cell(200, 8, $lang['9998'], 0, 0, 'C', false);
		}
	}
}
