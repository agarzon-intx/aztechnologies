<?php
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'flyer_text_style.inc.php';
/**
 * Category schedule flyer (flyerSC) PDF builder.
 * Each site keeps its own copy in pdf/ (used by flyerSC.php, flyerSC-I.php and the Facebook flyers);
 * the global/include copy is only the fallback for sites without one.
 */
if (!function_exists('flyer_sc_draw_page_header')) {

	function flyer_sc_draw_page_header($pdf, $siteRoot, array $row0) {
		if (!function_exists('az_flyer_text_style')) {
			$inc = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'global' . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'flyer_text_style.inc.php';
			if (is_readable($inc)) {
				require_once $inc;
			}
		}
		$fs = function_exists('az_flyer_text_style')
			? az_flyer_text_style(isset($GLOBALS['Config']) ? $GLOBALS['Config'] : null)
			: array('color1' => array(0, 0, 0), 'color2' => array(0, 152, 175), 'week' => 20, 'category' => 20);

		az_pdf_image_file($pdf, $siteRoot, '/pdf/FondoFlyerS.png', 0, 0, 210, 210);

		az_flyer_pdf_set_text_color($pdf, $fs['color1']);
		$pdf->SetXY(95, 12);
		az_flyer_pdf_set_font($pdf, 30);
		if (is_numeric($row0['Jornada_Desc'])) {
			$pdf->Cell(170, 8, 'Jornada ' . az_utf8_decode($row0['Jornada_Desc']), 35, 0, 'L', false);
		} else {
			$pdf->Cell(170, 8, az_utf8_decode($row0['Jornada_Desc']), 35, 0, 'L', false);
		}
		$pdf->SetXY(95, 23);
		az_flyer_pdf_set_font($pdf, 30);
		$pdf->Cell(170, 8, 'Categoria: ' . az_utf8_decode($row0['Categoria_Desc']), 35, 0, 'L', false);
		az_flyer_pdf_set_text_color($pdf, $fs['color2']);
		$pdf->SetXY(95.5, 12.5);
		az_flyer_pdf_set_font($pdf, 30);
		if (is_numeric($row0['Jornada_Desc'])) {
			$pdf->Cell(170, 8, 'Jornada ' . az_utf8_decode($row0['Jornada_Desc']), 35, 0, 'L', false);
		} else {
			$pdf->Cell(170, 8, az_utf8_decode($row0['Jornada_Desc']), 35, 0, 'L', false);
		}
		$pdf->SetXY(95.5, 23.5);
		az_flyer_pdf_set_font($pdf, 30);
		$pdf->Cell(170, 8, 'Categoria: ' . az_utf8_decode($row0['Categoria_Desc']), 35, 0, 'L', false);
	}

	function flyer_sc_games_sql($schema, $jornada, $catid) {
		$jornada = (int) $jornada;
		$catid = (int) $catid;
		return "select distinct Torneo_ID, Local_ID, local, Visitante_ID, visitante, Horario, Campo_DESC, visit, Fecha, Juego_ID, visit
			        from
    			        (SELECT DISTINCT 1 visit, j.Juego_ID,
                            j.Torneo_ID,
                            j.Jornada_ID,
                            jo.Jornada_Desc,
                            jo.Jornada_DescCorta,
                            ca.Categoria_Desc,
                            j.Local_ID,
                            l.Equipo_FULLDESC as local,
                            j.Visitante_ID Visitante_ID,
                            ifnull(v.Equipo_FULLDESC, 'Descansa') as visitante,
                            COALESCE(NULLIF(j.Campo_ID, 0), l.Campo_ID, v.Campo_ID, -1) as Campo_ID,
                            COALESCE(jc.Campo_DESC, lc.Campo_DESC, vc.Campo_DESC, '') as Campo_DESC,
                            TIME_FORMAT(j.Horario, '%l:%i %p') Horario,
                            DATE_FORMAT(j.Fecha, '%e de %M') Fecha
                        FROM $schema.Juegos j
                        	join $schema.Equipos l on j.Local_ID = l.Equipo_ID and j.Torneo_ID = l.Torneo_ID
                        	left outer join $schema.Equipos v on j.Visitante_ID = v.Equipo_ID and j.Torneo_ID = v.Torneo_ID
                            join $schema.Jornada jo on j.Fecha between jo.Fecha_Inicio and jo.Fecha_Fin
                            left outer join $schema.Campos jc on jc.Campo_ID = NULLIF(j.Campo_ID, 0)
                            left outer join $schema.Campos lc on lc.Campo_ID = l.Campo_ID
                            left outer join $schema.Campos vc on vc.Campo_ID = v.Campo_ID
                            join $schema.Categorias ca on ca.Categoria_ID = l.Fuerza and j.Torneo_ID = ca.Torneo_ID
                        where jo.Jornada_ID = $jornada and ca.Categoria_ID = $catid and (j.Visitante_ID <> -1 and j.Local_ID <> -1) and 
								((weekday(j.Fecha) <> (SELECT MarcadorDiaDefault-1 FROM $schema.Configuration)) or (weekday(j.Fecha) = (SELECT MarcadorDiaDefault-1 FROM $schema.Configuration) and j.Horario <> (SELECT MarcadorHoraDefault FROM $schema.Configuration)))
                        UNION
                        SELECT DISTINCT 0 visit, j.Juego_ID,
                            j.Torneo_ID,
                            j.Jornada_ID,
                            jo.Jornada_Desc,
                            jo.Jornada_DescCorta,
                            ca.Categoria_Desc,
                            j.Local_ID Local_ID,
                            ifnull(l.Equipo_FULLDESC, 'Descansa') as local,
                            j.Visitante_ID Visitante_ID,
                            ifnull(v.Equipo_FULLDESC, 'Descansa') as visitante,
                            COALESCE(NULLIF(j.Campo_ID, 0), l.Campo_ID, v.Campo_ID, -1) as Campo_ID,
                            COALESCE(jc.Campo_DESC, lc.Campo_DESC, vc.Campo_DESC, '') as Campo_DESC,
                            'DESCANSA' Horario,
                            '' Fecha
                        FROM $schema.Juegos j
                        	left outer join $schema.Equipos l on j.Local_ID = l.Equipo_ID and j.Torneo_ID = l.Torneo_ID
                        	left outer join $schema.Equipos v on j.Visitante_ID = v.Equipo_ID and j.Torneo_ID = v.Torneo_ID
                            join $schema.Jornada jo on j.Fecha between jo.Fecha_Inicio and jo.Fecha_Fin
                            left outer join $schema.Campos jc on jc.Campo_ID = NULLIF(j.Campo_ID, 0)
                            left outer join $schema.Campos lc on lc.Campo_ID = l.Campo_ID
                            left outer join $schema.Campos vc on vc.Campo_ID = v.Campo_ID
                            join $schema.Categorias ca on ca.Categoria_ID = IFNULL(l.Fuerza, v.Fuerza) and j.Torneo_ID = ca.Torneo_ID
                        where jo.Jornada_ID = $jornada and ca.Categoria_ID = $catid and (j.Visitante_ID = -1 or j.Local_ID = -1)
                        UNION
                        SELECT DISTINCT -1 visit, j.Juego_ID,
                            j.Torneo_ID,
                            j.Jornada_ID,
                            jo.Jornada_Desc,
                            jo.Jornada_DescCorta,
                            ca.Categoria_Desc,
                            j.Local_ID,
                            l.Equipo_FULLDESC as local,
                            j.Visitante_ID Visitante_ID,
                            ifnull(v.Equipo_FULLDESC, 'Descansa') as visitante,
                            COALESCE(NULLIF(j.Campo_ID, 0), l.Campo_ID, v.Campo_ID, -1) as Campo_ID,
                            COALESCE(jc.Campo_DESC, lc.Campo_DESC, vc.Campo_DESC, '') as Campo_DESC,
                            'NO PROGRAMADO' Horario,
                            '' Fecha
                        FROM $schema.Juegos j
                        	join $schema.Equipos l on j.Local_ID = l.Equipo_ID and j.Torneo_ID = l.Torneo_ID
                        	left outer join $schema.Equipos v on j.Visitante_ID = v.Equipo_ID and j.Torneo_ID = v.Torneo_ID
                            join $schema.Jornada jo on j.Fecha between jo.Fecha_Inicio and jo.Fecha_Fin
                            left outer join $schema.Campos jc on jc.Campo_ID = NULLIF(j.Campo_ID, 0)
                            left outer join $schema.Campos lc on lc.Campo_ID = l.Campo_ID
                            left outer join $schema.Campos vc on vc.Campo_ID = v.Campo_ID
                            join $schema.Categorias ca on ca.Categoria_ID = l.Fuerza and j.Torneo_ID = ca.Torneo_ID
                        where jo.Jornada_ID = $jornada and ca.Categoria_ID = $catid and (j.Visitante_ID <> -1 and j.Local_ID <> -1) and ((weekday(j.Fecha) = (SELECT MarcadorDiaDefault-1 FROM $schema.Configuration) and j.Horario = (SELECT MarcadorHoraDefault FROM $schema.Configuration)))) a
                    order by a.Juego_ID asc";
	}

	function flyer_sc_render_game_row($pdf, $siteRoot, array $row1, $x, $y) {
		if (!function_exists('az_flyer_text_style')) {
			$__azFlyerText = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'global' . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'flyer_text_style.inc.php';
			if (is_readable($__azFlyerText)) {
				require_once $__azFlyerText;
			}
			unset($__azFlyerText);
		}
		$fs = function_exists('az_flyer_text_style')
			? az_flyer_text_style(isset($GLOBALS['Config']) ? $GLOBALS['Config'] : null)
			: array('color1' => array(0, 152, 175), 'color2' => array(255, 255, 255), 'week' => 75, 'category' => 60, 'date' => 35, 'hour' => 35, 'field' => 35);
		
		if($row1["visit"] < 1){
			az_pdf_image_file($pdf, $siteRoot, '/pdf/calendar.png', $x+23,$y+71,5, 5);
			$pdf->SetXY($x+25+4,$y+71.5);
			az_flyer_pdf_set_font($pdf, $fs['date']*.45);
			$pdf->SetTextColor($fs['color2'][0], $fs['color2'][1], $fs['color2'][2]);
			$pdf->Cell(40 , 5, az_utf8_decode($row1["Horario"]) . '', 17, 0 , 'L' , false);
			$pdf->SetXY($x+25.25+4.25,$y+71.75);
			$pdf->SetTextColor($fs['color1'][0], $fs['color1'][1], $fs['color1'][2]);
			$pdf->Cell(40 , 5, az_utf8_decode($row1["Horario"]) . '', 17, 0 , 'L' , false);

			if($row1["visit"] < 0){
				az_pdf_image_file($pdf, $siteRoot, '/imagenes/Original/' . $row1["Torneo_ID"] . '-' . $row1["Local_ID"] . '.png', $x+19,$y+40,30, 30);
				az_pdf_image_file($pdf, $siteRoot, '/imagenes/Original/' . $row1["Torneo_ID"] . '-' . $row1["Visitante_ID"] . '.png', $x+69,$y+40,30, 30);
			}else{
				if($row1["Local_ID"] == -1){
					az_pdf_image_file($pdf, $siteRoot, '/imagenes/Original/' . $row1["Torneo_ID"] . '-' . $row1["Visitante_ID"] . '.png', $x+25,$y+40,30, 30);
				}else{
					az_pdf_image_file($pdf, $siteRoot, '/imagenes/Original/' . $row1["Torneo_ID"] . '-' . $row1["Local_ID"] . '.png', $x+25,$y+40,30, 30);
				}
			}
		}else{
			az_pdf_image_file($pdf, $siteRoot, '/pdf/calendar.png', $x+23,$y+71,5, 5);
			$pdf->SetXY($x+25+4,$y+71.5);
			az_flyer_pdf_set_font($pdf, $fs['date']*.45);
			$pdf->SetTextColor($fs['color2'][0], $fs['color2'][1], $fs['color2'][2]);
			$pdf->Cell(40 , 5, az_utf8_decode($row1["Fecha"]) . '', 17, 0 , 'L' , false);
			$pdf->SetXY($x+25.25+4.25,$y+71.75);
			$pdf->SetTextColor($fs['color1'][0], $fs['color1'][1], $fs['color1'][2]);
			$pdf->Cell(40 , 5, az_utf8_decode($row1["Fecha"]) . '', 17, 0 , 'L' , false);


			az_pdf_image_file($pdf, $siteRoot, '/pdf/clock.png', $x+75,$y+71.5,5, 5);
			$pdf->SetXY($x+77+4,$y+71.5);
			$pdf->SetTextColor($fs['color2'][0], $fs['color2'][1], $fs['color2'][2]);
			$pdf->Cell(24 , 5, az_utf8_decode($row1["Horario"]) . '', 17, 0 , 'L' , false);
			$pdf->SetXY($x+77.25+4.25,$y+71.75);
			$pdf->SetTextColor($fs['color1'][0], $fs['color1'][1], $fs['color1'][2]);
			$pdf->Cell(24 , 5, az_utf8_decode($row1["Horario"]) . '', 17, 0 , 'L' , false);


			az_pdf_image_file($pdf, $siteRoot, '/pdf/pointer.png', $x+40,$y+77,4, 4);
			$pdf->SetXY($x+42+4,$y+77);
			$pdf->SetTextColor($fs['color2'][0], $fs['color2'][1], $fs['color2'][2]);
			$pdf->Cell(34 , 5, az_utf8_decode($row1["Campo_DESC"]) . '', 17, 0 , 'L' , false);
			$pdf->SetXY($x+42.25+4.25,$y+77.25);
			$pdf->SetTextColor($fs['color1'][0], $fs['color1'][1], $fs['color1'][2]);
			$pdf->Cell(34 , 5, az_utf8_decode($row1["Campo_DESC"]) . '', 17, 0 , 'L' , false);
			$pdf->SetTextColor($fs['color2'][0], $fs['color2'][1], $fs['color2'][2]);

			$pdf->SetXY($x+36,$y+50);
			$pdf->SetTextColor($fs['color1'][0], $fs['color1'][1], $fs['color1'][2]);
			$pdf->Cell(34 , 5, 'VS', 17, 0 , 'L' , false);
			$pdf->SetXY($x+36.25,$y+50.25);
			$pdf->SetTextColor($fs['color2'][0], $fs['color2'][1], $fs['color2'][2]);
			$pdf->Cell(34 , 5, 'VS', 17, 0 , 'L' , false);

			az_pdf_image_file($pdf, $siteRoot, '/imagenes/Original/' . $row1["Torneo_ID"] . '-' . $row1["Local_ID"] . '.png', $x+19,$y+40,30, 30);
			az_pdf_image_file($pdf, $siteRoot, '/imagenes/Original/' . $row1["Torneo_ID"] . '-' . $row1["Visitante_ID"] . '.png', $x+69,$y+40,30, 30);
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
			az_flyer_pdf_set_font($pdf, 90);
			$pdf->Cell(200, 8, $lang['9998'], 0, 0, 'C', false);
			return;
		}

		$x = 0;
		$y = 0;
		$rowc = 0;
		$colc = 0;
		$pageNum = 0;

		while ($row0 = $result0->fetch_assoc()) {
			$catid = (int) $row0['Categoria_ID'];

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
				az_flyer_pdf_set_font($pdf, 90);
				$pdf->Cell(200, 8, $lang['9998'], 0, 0, 'C', false);
				continue;
			}

			while ($row1 = $result1->fetch_assoc()) {
				if ($rowc === 3) {
					$x = 0;
					$y = 0;
					$rowc = 0;
					$colc = 0;

					$pdf->AddPage();
					$pageNum++;
					$pageLabels[] = 'j' . $jornada . '-c' . $catid . '-p' . $pageNum;
					$pdf->SetAutoPageBreak(false, 1);
					$pdf->SetXY(0, 0);
					flyer_sc_draw_page_header($pdf, $siteRoot, $row0);
				}
				flyer_sc_render_game_row($pdf, $siteRoot, $row1, $x, $y);
				if($colc === 1){
					$colc = 0;
					$rowc++;
					$x = 0;
					$y = $y + 50.55;;
				}else{
					$x = $x + 90;
					$colc++;
				}
			}
		}
	}
}
