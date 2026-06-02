<?php
/**
 * Flyer page layout for nuestrodeporte (from flyerC.php).
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
			$pdf->SetFont('Coluna' , 'B' , 35);
			$pdf->SetTextColor(61, 61, 61);
			az_pdf_image_file($pdf, $siteRoot, '/pdf/calendar.png', 35,153,10, 10);
			$pdf->SetXY(45,155);
			$pdf->SetFont('Coluna' , 'B' , 35);
			$pdf->Cell(90 , 8, az_utf8_decode($row1["Fecha"]) . '', 45, 0 , 'L' , false);
			az_pdf_image_file($pdf, $siteRoot, '/pdf/clock.png', 120,153,10, 10);
			$pdf->SetXY(130,155);
			$pdf->SetFont('Coluna' , 'B' , 35);
			$pdf->Cell(90 , 8, az_utf8_decode($row1["Horario"]) . '', 35, 0 , 'L' , false);
			az_pdf_image_file($pdf, $siteRoot, '/pdf/pointer.png', 80,169,10, 10);
			$pdf->SetXY(105,170);
			$pdf->SetFont('Coluna' , 'B' , 35);
			$pdf->Cell(90 , 8, az_utf8_decode($row1["Campo_DESC"]) . '', 35, 0 , 'L' , false);
			$pdf->SetTextColor(255,255,255);
			$pdf->SetXY(45.5,155.5);
			$pdf->SetFont('Coluna' , 'B' , 35);
			$pdf->Cell(90 , 8, az_utf8_decode($row1["Fecha"]) . '', 45, 0 , 'L' , false);
			$pdf->SetXY(130.5,155.5);
			$pdf->SetFont('Coluna' , 'B' , 35);
			$pdf->Cell(90 , 8, az_utf8_decode($row1["Horario"]) . '', 35, 0 , 'L' , false);
			$pdf->SetXY(105.5,170.5);
			$pdf->SetFont('Coluna' , 'B' , 35);
			$pdf->Cell(90 , 8, az_utf8_decode($row1["Campo_DESC"]) . '', 35, 0 , 'L' , false);
			az_pdf_image_file($pdf, $siteRoot, '/imagenes/Original/' . $row1["Torneo_ID"] . '-' . $row1["Local_ID"] . '.png', 30,95,45, 45);
			az_pdf_image_file($pdf, $siteRoot, '/imagenes/Original/' . $row1["Torneo_ID"] . '-' . $row1["Visitante_ID"] . '.png', 135,95,45, 45);
			$pdf->SetXY(0,38);
			$pdf->SetFont('Coluna' , 'B' , 90);
			
			if(is_numeric($row1["Jornada_Desc"])){
			    $pdf->Cell(210 , 25, 'Jornada ' . az_utf8_decode($row1["Jornada_Desc"]) . '', 35, 0 , 'C' , false);
			}else{
			    $pdf->Cell(210 , 25, az_utf8_decode($row1["Jornada_Desc"]) . '', 35, 0 , 'C' , false);
			}
			//$pdf->Cell(210 , 25, '4TOS DE FINAL', 35, 0 , 'C' , false);
			$pdf->SetXY(0,70);
			$pdf->SetFont('Coluna' , 'B' , 60);
			$pdf->Cell(210 , 18, 'Categoria: ' . az_utf8_decode($row1["Categoria_Desc"]) . '', 35, 0 , 'C' , false);
			$pdf->SetTextColor(61, 61, 61);
			$pdf->SetXY(.5,38.5);
			$pdf->SetFont('Coluna' , 'B' , 90);
			if(is_numeric($row1["Jornada_Desc"])){
			    $pdf->Cell(210 , 25, 'Jornada ' . az_utf8_decode($row1["Jornada_Desc"]) . '', 35, 0 , 'C' , false);
			}else{
			    $pdf->Cell(210 , 25, az_utf8_decode($row1["Jornada_Desc"]) . '', 35, 0 , 'C' , false);
			}
			//$pdf->Cell(210 , 25, '4TOS DE FINAL', 35, 0 , 'C' , false);
			$pdf->SetXY(.5,70.5);
			$pdf->SetFont('Coluna' , 'B' , 60);
			$pdf->Cell(210 , 18, 'Categoria: ' . az_utf8_decode($row1["Categoria_Desc"]) . '', 35, 0 , 'C' , false);
			
			
		
	}

	function flyer_ci_juegos_sql($schema, $jornada, $categoria) {
		$jornada = (int) $jornada;
		$categoria = (int) $categoria;
		return "SELECT j.Juego_ID,
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
                join $schema.Categorias ca on ca.Categoria_ID = l.Fuerza
            where jo.Jornada_ID = $jornada and l.Fuerza = $categoria
            order by ca.Categoria_ID, j.Fecha, j.Horario, c.Campo_DESC, j.Juego_ID asc";

	}
}
