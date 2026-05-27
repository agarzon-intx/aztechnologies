<?php
/**
 * Category schedule flyer (flyerSC) PDF builder — shared by flyerSC.php and flyerSC-I.php.
 */
if (!function_exists('flyer_sc_draw_page_header')) {

	function flyer_sc_draw_page_header($pdf, $siteRoot, array $row0) {
		az_pdf_image_file($pdf, $siteRoot, '/pdf/FondoFlyerS.png', 0, 0, 210, 210);

		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetXY(135, 10);
		$pdf->SetFont('Coluna', 'B', 20);
		if (is_numeric($row0['Jornada_Desc'])) {
			$pdf->Cell(170, 8, 'Jornada ' . az_utf8_decode($row0['Jornada_Desc']), 35, 0, 'L', false);
		} else {
			$pdf->Cell(170, 8, az_utf8_decode($row0['Jornada_Desc']), 35, 0, 'L', false);
		}
		$pdf->SetXY(135, 18);
		$pdf->SetFont('Coluna', 'B', 20);
		$pdf->Cell(170, 8, 'Categoria: ' . az_utf8_decode($row0['Categoria_Desc']), 35, 0, 'L', false);
		$pdf->SetTextColor(0, 152, 175);
		$pdf->SetXY(135.5, 10.5);
		$pdf->SetFont('Coluna', 'B', 20);
		if (is_numeric($row0['Jornada_Desc'])) {
			$pdf->Cell(170, 8, 'Jornada ' . az_utf8_decode($row0['Jornada_Desc']), 35, 0, 'L', false);
		} else {
			$pdf->Cell(170, 8, az_utf8_decode($row0['Jornada_Desc']), 35, 0, 'L', false);
		}
		$pdf->SetXY(135.5, 18.5);
		$pdf->SetFont('Coluna', 'B', 20);
		$pdf->Cell(170, 8, 'Categoria: ' . az_utf8_decode($row0['Categoria_Desc']), 35, 0, 'L', false);
	}

	function flyer_sc_games_sql($schema, $jornada, $catid) {
		$jornada = (int) $jornada;
		$catid = (int) $catid;
		return "select distinct Torneo_ID, Local_ID, local, Visitante_ID, visitante, Horario, Campo_DESC, visit, Fecha, Juego_ID
			        from
    			        (SELECT DISTINCT 1 visit, j.Juego_ID,
                            j.Torneo_ID,
                            j.Jornada_ID,
                            jo.Jornada_Desc,
                            jo.Jornada_DescCorta,
                            ca.Categoria_Desc,
                            j.Local_ID,
                            l.Equipo_FULLDESC as local,
                            ifnull(j.Visitante_ID, -1) Visitante_ID,
                            ifnull(v.Equipo_FULLDESC, 'Descansa') as visitante,
                            ifnull(j.Campo_ID,-1) as Campo_ID,
                            ifnull(c.Campo_DESC,'') as Campo_DESC,
                            TIME_FORMAT(j.Horario, '%l:%i %p') Horario,
                            DATE_FORMAT(j.Fecha, '%e de %M') Fecha
                        FROM $schema.Juegos j
                        	join $schema.Equipos l on j.Local_ID = l.Equipo_ID and j.Torneo_ID = l.Torneo_ID
                        	left outer join $schema.Equipos v on j.Visitante_ID = v.Equipo_ID and j.Torneo_ID = v.Torneo_ID
                            join $schema.Jornada jo on j.Fecha between jo.Fecha_Inicio and jo.Fecha_Fin
                            left outer join $schema.Campos c on c.Campo_ID = j.Campo_ID
                            join $schema.Categorias ca on ca.Categoria_ID = l.Fuerza
                        where jo.Jornada_ID = $jornada and ca.Categoria_ID = $catid and j.Visitante_ID is not null and ((weekday(j.Fecha) <> 2) or (weekday(j.Fecha) = (SELECT MarcadorDiaDefault-1 FROM $schema.Configuration) and j.Horario <> (SELECT MarcadorHoraDefault FROM $schema.Configuration)))
                        UNION
                        SELECT DISTINCT 0 visit, j.Juego_ID,
                            j.Torneo_ID,
                            j.Jornada_ID,
                            jo.Jornada_Desc,
                            jo.Jornada_DescCorta,
                            ca.Categoria_Desc,
                            j.Local_ID,
                            l.Equipo_FULLDESC as local,
                            ifnull(j.Visitante_ID, -1) Visitante_ID,
                            ifnull(v.Equipo_FULLDESC, 'Descansa') as visitante,
                            ifnull(j.Campo_ID,-1) as Campo_ID,
                            ifnull(c.Campo_DESC,'') as Campo_DESC,
                            TIME_FORMAT(j.Horario, '%l:%i %p') Horario,
                            DATE_FORMAT(j.Fecha, '%e de %M') Fecha
                        FROM $schema.Juegos j
                        	join $schema.Equipos l on j.Local_ID = l.Equipo_ID and j.Torneo_ID = l.Torneo_ID
                        	left outer join $schema.Equipos v on j.Visitante_ID = v.Equipo_ID and j.Torneo_ID = v.Torneo_ID
                            join $schema.Jornada jo on j.Fecha between jo.Fecha_Inicio and jo.Fecha_Fin
                            left outer join $schema.Campos c on c.Campo_ID = j.Campo_ID
                            join $schema.Categorias ca on ca.Categoria_ID = l.Fuerza
                        where jo.Jornada_ID = $jornada and ca.Categoria_ID = $catid and j.Visitante_ID is null
                        UNION
                        SELECT DISTINCT -1 visit, j.Juego_ID,
                            j.Torneo_ID,
                            j.Jornada_ID,
                            jo.Jornada_Desc,
                            jo.Jornada_DescCorta,
                            ca.Categoria_Desc,
                            j.Local_ID,
                            l.Equipo_FULLDESC as local,
                            ifnull(j.Visitante_ID, -1) Visitante_ID,
                            ifnull(v.Equipo_FULLDESC, 'Descansa') as visitante,
                            ifnull(j.Campo_ID,-1) as Campo_ID,
                            '' as Campo_DESC,
                            'NO PROGRAMADO' Horario,
                            '' Fecha
                        FROM $schema.Juegos j
                        	join $schema.Equipos l on j.Local_ID = l.Equipo_ID and j.Torneo_ID = l.Torneo_ID
                        	left outer join $schema.Equipos v on j.Visitante_ID = v.Equipo_ID and j.Torneo_ID = v.Torneo_ID
                            join $schema.Jornada jo on j.Fecha between jo.Fecha_Inicio and jo.Fecha_Fin
                            left outer join $schema.Campos c on c.Campo_ID = j.Campo_ID
                            join $schema.Categorias ca on ca.Categoria_ID = l.Fuerza
                        where jo.Jornada_ID = $jornada and ca.Categoria_ID = $catid and j.Visitante_ID is not null and ((weekday(j.Fecha) = (SELECT MarcadorDiaDefault-1 FROM $schema.Configuration) and j.Horario = (SELECT MarcadorHoraDefault FROM $schema.Configuration)))) a
                    order by a.visit desc, a.Fecha, a.Horario, a.Campo_DESC, a.Juego_ID asc";
	}

	function flyer_sc_render_game_row($pdf, $siteRoot, array $row1, $x, $y) {
		$pdf->SetXY($x + 20, $y + 53);
		$pdf->SetAlpha(.6);
		$pdf->SetFillColor(63, 63, 63);
		$pdf->Cell(170, 14, '', 20, 0, 'L', true);

		$pdf->SetAlpha(1);
		$pdf->SetFillColor(255, 255, 255);
		$pdf->Circle($x + 30, $y + 60, 7, 'F');

		$pdf->SetAlpha(1);
		az_pdf_image_file($pdf, $siteRoot, '/imagenes/Original/' . $row1['Torneo_ID'] . '-' . $row1['Local_ID'] . '.png', $x + 25, $y + 55, 10, 10);
		$pdf->SetXY($x + 40, $y + 53);
		$pdf->SetFont('Coluna', 'B', 15);
		$pdf->SetTextColor(255, 255, 255);
		$pdf->Cell(130, 10, az_utf8_decode($row1['local']), 35, 0, 'L', false);

		$pdf->SetAlpha(1);
		$pdf->SetFillColor(255, 255, 255);
		if ($row1['Visitante_ID'] != -1) {
			$pdf->Circle($x + 180, $y + 60, 7, 'F');
		}
		$pdf->SetAlpha(1);
		if ($row1['Visitante_ID'] != -1) {
			az_pdf_image_file($pdf, $siteRoot, '/imagenes/Original/' . $row1['Torneo_ID'] . '-' . $row1['Visitante_ID'] . '.png', $x + 175, $y + 55, 10, 10);
		}
		$pdf->SetXY($x + 40, $y + 53);
		$pdf->SetFont('Coluna', 'B', 15);
		$pdf->Cell(130, 10, az_utf8_decode($row1['visitante']), 35, 0, 'R', false);
		$pdf->SetXY($x + 40, $y + 53);
		$pdf->SetFont('Coluna', 'B', 13);
		if ($row1['Visitante_ID'] != -1) {
			$pdf->Cell(130, 10, az_utf8_decode($row1['Fecha'] . ' - ' . $row1['Horario']), 35, 0, 'C', false);
		}
		$pdf->SetXY($x + 40, $y + 59);
		if ($row1['Visitante_ID'] != -1) {
			$pdf->Cell(130, 10, ' @ ' . az_utf8_decode($row1['Campo_DESC']), 35, 0, 'C', false);
		}
	}

	/**
	 * @param AlphaPDF $pdf
	 * @param array $pageLabels
	 */
	function flyer_sc_build_pdf($pdf, $Config, $schema, $jornada, $categoria, $siteRoot, $lang, array &$pageLabels) {
		$jornada = (int) $jornada;
		$categoria = (int) $categoria;
		$pageLabels = array();

		$Config->query("SET lc_time_names = 'es_MX';");

		$sql0 = "SELECT DISTINCT ca.Categoria_ID,
	                jo.Jornada_Desc,
                    jo.Jornada_DescCorta,
                    ca.Categoria_Desc
            FROM $schema.Juegos j
            	join $schema.Equipos l on j.Local_ID = l.Equipo_ID
                join $schema.Jornada jo on jo.Jornada_ID = j.Jornada_ID
                join $schema.Categorias ca on ca.Categoria_ID = l.Fuerza and j.Torneo_ID = ca.Torneo_ID
            where jo.Jornada_ID = $jornada and l.Fuerza = $categoria
            order by ca.Categoria_ID";
		$result0 = $Config->query($sql0);
		if (!$result0 || $result0->num_rows < 1) {
			$pdf->AddPage();
			$pageLabels[] = 'sin-juegos';
			$pdf->SetXY(0, 0);
			$pdf->SetFont('Coluna', 'B', 90);
			$pdf->Cell(200, 8, $lang['9998'], 0, 0, 'C', false);
			return;
		}

		while ($row0 = $result0->fetch_assoc()) {
			$catid = (int) $row0['Categoria_ID'];
			$x = 0;
			$y = -18;
			$rowc = 0;
			$pageNum = 0;

			$pdf->AddPage();
			$pageNum++;
			$pageLabels[] = 'j' . $jornada . '-c' . $catid . '-p' . $pageNum;
			$pdf->SetAutoPageBreak(false, 1);
			$pdf->SetXY(0, 0);
			flyer_sc_draw_page_header($pdf, $siteRoot, $row0);

			$sql1 = flyer_sc_games_sql($schema, $jornada, $catid);
			$result1 = $Config->query($sql1);
			if (!$result1 || $result1->num_rows < 1) {
				$pdf->SetXY(0, 0);
				$pdf->SetFont('Coluna', 'B', 90);
				$pdf->Cell(200, 8, $lang['9998'], 0, 0, 'C', false);
				continue;
			}

			while ($row1 = $result1->fetch_assoc()) {
				if ($rowc === 9) {
					$x = 0;
					$y = -18;
					$rowc = 0;

					$pdf->AddPage();
					$pageNum++;
					$pageLabels[] = 'j' . $jornada . '-c' . $catid . '-p' . $pageNum;
					$pdf->SetAutoPageBreak(false, 1);
					$pdf->SetXY(0, 0);
					flyer_sc_draw_page_header($pdf, $siteRoot, $row0);
				}
				flyer_sc_render_game_row($pdf, $siteRoot, $row1, $x, $y);
				$y = $y + 17;
				$rowc++;
			}
		}
	}
}
