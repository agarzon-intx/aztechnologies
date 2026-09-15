<?php
/**
 * Flyer page layout for lidep (from flyerC.php).
 */
if (!function_exists('flyer_ci_add_page')) {

	function flyer_ci_add_page($pdf, $siteRoot, array $row1) {

			$localid = az_utf8_decode($row1["Local_ID"]);
			$visitanteid = az_utf8_decode($row1["Visitante_ID"]);
			$x = 0;
			$y = 0;
			$col = 0;
			$rowc = 0;
		
			$pdf->AddPage();
			$pdf->SetAutoPageBreak(false,1);
			$pdf->SetXY(0,0);
			az_pdf_image_file($pdf, $siteRoot, '/pdf/FondoFlyer.png', 0,0,210, 210);

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

			$pdf->SetFont('Coluna' , 'B' , $fs['date']);
			$pdf->SetTextColor($fs['color1'][0], $fs['color1'][1], $fs['color1'][2]);
			az_pdf_image_file($pdf, $siteRoot, '/pdf/calendar.png', 35,153,10, 10);
			$pdf->SetXY(45,155);
			$pdf->SetFont('Coluna' , 'B' , $fs['date']);
			$pdf->Cell(90 , 8, az_utf8_decode($row1["Fecha"]) . '', 45, 0 , 'L' , false);
			az_pdf_image_file($pdf, $siteRoot, '/pdf/clock.png', 120,153,10, 10);
			$pdf->SetXY(130,155);
			$pdf->SetFont('Coluna' , 'B' , $fs['hour']);
			$pdf->Cell(90 , 8, az_utf8_decode($row1["Horario"]) . '', 35, 0 , 'L' , false);
			az_pdf_image_file($pdf, $siteRoot, '/pdf/pointer.png', 80,169,10, 10);
			$pdf->SetXY(90,170);
			$pdf->SetFont('Coluna' , 'B' , $fs['field']);
			$pdf->Cell(90 , 8, az_utf8_decode($row1["Campo_DESC"]) . '', 35, 0 , 'L' , false);
			$pdf->SetTextColor($fs['color2'][0], $fs['color2'][1], $fs['color2'][2]);
			$pdf->SetXY(45.5,155.5);
			$pdf->SetFont('Coluna' , 'B' , $fs['date']);
			$pdf->Cell(90 , 8, az_utf8_decode($row1["Fecha"]) . '', 45, 0 , 'L' , false);
			$pdf->SetXY(130.5,155.5);
			$pdf->SetFont('Coluna' , 'B' , $fs['hour']);
			$pdf->Cell(90 , 8, az_utf8_decode($row1["Horario"]) . '', 35, 0 , 'L' , false);
			$pdf->SetXY(90.5,170.5);
			$pdf->SetFont('Coluna' , 'B' , $fs['field']);
			$pdf->Cell(90 , 8, az_utf8_decode($row1["Campo_DESC"]) . '', 35, 0 , 'L' , false);
			az_pdf_image($pdf, az_pdf_team_logo_path($siteRoot, $row1['Torneo_ID'], $row1['Local_ID']), 30, 95, 45, 45);
			az_pdf_image($pdf, az_pdf_team_logo_path($siteRoot, $row1['Torneo_ID'], $row1['Visitante_ID']), 135, 95, 45, 45);
            $pdf->SetXY(0,38);
			$pdf->SetFont('Coluna' , 'B' , $fs['week']);
			if(is_numeric($row1["Jornada_Desc"])){
			    $pdf->Cell(210 , 25, 'Jornada ' . az_utf8_decode($row1["Jornada_Desc"]) . '', 35, 0 , 'C' , false);
			}else{
			    $pdf->Cell(210 , 25, az_utf8_decode($row1["Jornada_Desc"]) . '', 35, 0 , 'C' , false);
			}
			//$pdf->Cell(210 , 25, '4TOS DE FINAL', 35, 0 , 'C' , false);
			$pdf->SetXY(0,70);
			$pdf->SetFont('Coluna' , 'B' , $fs['category']);
			$pdf->Cell(210 , 18, 'Categoria: ' . az_utf8_decode($row1["Categoria_Desc"]) . '', 35, 0 , 'C' , false);
			$pdf->SetTextColor($fs['color1'][0], $fs['color1'][1], $fs['color1'][2]);
			$pdf->SetXY(.5,38.5);
			$pdf->SetFont('Coluna' , 'B' , $fs['week']);
			if(is_numeric($row1["Jornada_Desc"])){
			    $pdf->Cell(210 , 25, 'Jornada ' . az_utf8_decode($row1["Jornada_Desc"]) . '', 35, 0 , 'C' , false);
			}else{
			    $pdf->Cell(210 , 25, az_utf8_decode($row1["Jornada_Desc"]) . '', 35, 0 , 'C' , false);
			}
			//$pdf->Cell(210 , 25, '4TOS DE FINAL', 35, 0 , 'C' , false);
			$pdf->SetXY(.5,70.5);
			$pdf->SetFont('Coluna' , 'B' , $fs['category']);
			$pdf->Cell(210 , 18, 'Categoria: ' . az_utf8_decode($row1["Categoria_Desc"]) . '', 35, 0 , 'C' , false);
			
			
		
	}

	function flyer_ci_juegos_sql($schema, $jornada, $categoria) {
		$jornada = (int) $jornada;
		$categoria = (int) $categoria;
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
                COALESCE(NULLIF(j.Campo_ID, 0), l.Campo_ID, v.Campo_ID) as Campo_ID,
                COALESCE(jc.Campo_DESC, lc.Campo_DESC, vc.Campo_DESC) as Campo_DESC,
                TIME_FORMAT(j.Horario, '%H:%i HRS') Horario,
                DATE_FORMAT(j.Fecha, '%e de %M') Fecha
            FROM $schema.Juegos j
            	left join $schema.Equipos l on j.Local_ID = l.Equipo_ID
            	left join $schema.Equipos v on j.Visitante_ID = v.Equipo_ID
                join $schema.Jornada jo on jo.Jornada_ID = j.Jornada_ID
                left join $schema.Campos jc on jc.Campo_ID = NULLIF(j.Campo_ID, 0)
                left join $schema.Campos lc on lc.Campo_ID = l.Campo_ID
                left join $schema.Campos vc on vc.Campo_ID = v.Campo_ID
                left join $schema.Categorias ca on ca.Categoria_ID = COALESCE(l.Fuerza, v.Fuerza) and ca.Torneo_ID = j.Torneo_ID
            where jo.Jornada_ID = $jornada and (l.Fuerza = $categoria OR v.Fuerza = $categoria)
            order by ca.Categoria_ID, j.Fecha, j.Horario, COALESCE(jc.Campo_DESC, lc.Campo_DESC, vc.Campo_DESC), j.Juego_ID asc";

	}
}
