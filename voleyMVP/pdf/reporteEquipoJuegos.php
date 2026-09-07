<?php
	require_once dirname(__DIR__) . '/site_paths.php';
	set_time_limit(300);
	require("alphapdf.php");
	require("membersite_config.php");
	$Config = new Configuration();
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('cedulas.php');
	$Config->connect();

	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	$folder = substr(substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT'])),1,strlen(substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT'])))-5);

	$torneo = $_COOKIE[$Config->getAlias() . 'season'];
	$categoria = $_COOKIE[$Config->getAlias() . 'category'];
	$equipo = htmlspecialchars($_GET['Equipo']);
	
	$siteRoot = az_pdf_site_root($Config);

	$Config->LoadLogo();
	$Config->LoadFlags();
	$Config->LoadRegionalSettings();
	
	$pdf = new FPDF('L','mm','Letter');
	$pdf->AddPage();
	$pdf->SetAutoPageBreak(false,1);
	$pdf->SetMargins(5, 5, 5, 5);	
	az_pdf_image_file($pdf, $siteRoot, '/imagenes/FondoReporte.png', 0,0,279,216);

    // restore full opacity
    //$pdf->SetAlpha(.5);
	$x = 0;
	$y = 40;
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(252, 1, 2);
	$pdf->SetDrawColor(252, 1, 2);
	$pdf->SetFont('Times' , '' , 6);
	$pdf->SetXY($x+5,$y+3);
	$pdf->Cell(270 , 3, '', 1, 0 , 'C' , true);
	$pdf->SetXY($x+5,$y+3);
	$pdf->Cell(8 , 3, strtoupper('Jor'), 1, 0 , 'C' , true);
	$pdf->SetXY($x+13,$y+3);
	$pdf->Cell(20 , 3, strtoupper('Categoria'), 1, 0 , 'C' , true);
	$pdf->SetXY($x+33,$y+3);
	$pdf->Cell(35 , 3, strtoupper('Local'), 1, 0 , 'C' , true);
	$pdf->SetXY($x+68,$y+3);
	$pdf->Cell(6 , 3, 'P', 1, 0 , 'C' , true);
	$pdf->SetXY($x+74,$y+3);
	$pdf->Cell(6 , 3, 'G', 1, 0 , 'C' , true);
	$pdf->SetXY($x+80,$y+3);
	$pdf->Cell(6 , 3, 'VS', 1, 0 , 'C' , true);
	$pdf->SetXY($x+86,$y+3);
	$pdf->Cell(6 , 3, 'G', 1, 0 , 'C' , true);
	$pdf->SetXY($x+92,$y+3);
	$pdf->Cell(6 , 3, 'P', 1, 0 , 'C' , true);
	$pdf->SetXY($x+98,$y+3);
	$pdf->Cell(35 , 3, strtoupper('Visitante'), 1, 0 , 'C' , true);
	$pdf->SetXY($x+133,$y+3);
	$pdf->Cell(35 , 3, strtoupper('Fecha/Hora'), 1, 0 , 'C' , true);
	$pdf->SetXY($x+168,$y+3);
	$pdf->Cell(70 , 3, strtoupper('Observaciones'), 1, 0 , 'C' , true);
	$pdf->SetXY($x+238,$y+3);
	$pdf->Cell(37 , 3, strtoupper('Campo'), 1, 0 , 'C' , true);
	

	$sql = "select distinct dc.Categoria_DESC, SUBSTRING(jor.Jornada_Desc, 1, 4) Jornada_DescCorta, a.Juego_ID, a.Local_ID, d.Equipo_FULLDESC as Local, a.Visitante_ID, f.Equipo_FULLDESC as Visitante, a.Fecha, day(a.Fecha) Dia, 
					ELT(DATE_FORMAT(a.Fecha,'%m'),'Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic')  Mes, year(a.Fecha) Anio, a.Campo_ID, DATE_FORMAT(a.Fecha, '%W') dia_sem,
					case when c.Campo_DESC is null then e.Campo_DESC else c.Campo_DESC end Campo_DESC, g.Torneo_Desc, DATE_FORMAT(a.Fecha, ' %d %Y') Fecha_String, Comentarios, TIME_FORMAT(a.Horario, '%H:%i %p') hora
			from $schema.Juegos a
				join $schema.Jornada b on a.Fecha between b.Fecha_Inicio and b.Fecha_Fin
				left outer join $schema.Campos c on a.Campo_ID = c.Campo_ID
				join $schema.Equipos d on a.Torneo_ID = d.Torneo_ID and a.Local_ID = d.Equipo_ID 
				join $schema.Categorias dc on d.Fuerza = dc.Categoria_ID and dc.Torneo_ID = $torneo
				join $schema.Campos e on d.Campo_ID = e.Campo_ID
				join $schema.Equipos f on a.Torneo_ID = f.Torneo_ID and a.Visitante_ID = f.Equipo_ID 
				join $schema.Torneos g on a.Torneo_ID = g.Torneo_ID
				join $schema.Jornada jor on a.Jornada_ID = jor.Jornada_ID
			where a.Torneo_ID = $torneo and (d.Equipo_FULLDESC like '%" . $equipo . "%' or f.Equipo_FULLDESC like '%" . $equipo . "%')and a.Jugado = 1
			order by dc.Categoria_Orden,a.Fecha, c.Campo_DESC, a.Horario, a.Juego_ID asc";
	//echo $sql;
	$result1 = $Config->query($sql);
	$totalJuegos = $result1->num_rows;
	$x = 0;
	$y = 0;
	$col = 0;
	$rowc = 0;
	$pdf->SetTextColor(252, 1, 2);
	$pdf->SetXY(40,32);
	$pdf->SetFont('Helvetica' , 'B' , 20);
	$pdf->Cell(225 , 8, 'Total de Juegos: ' . $totalJuegos, 35, 0 , 'C' , false);
	$pdf->SetTextColor(0, 0, 0);
	$count = 1;
    $x = 0;
    $y = 43;
	if ($result1->num_rows > 0) {
		// output data of each row
		while($row1 = $result1->fetch_assoc()) {
        	
        	
        	$height = 1;
        	$text = $row1["Comentarios"];
			$column_width = 70; // Width of your cell in FPDF units

			// 1. Get total width of the string in user units
			$string_width = $pdf->GetStringWidth($text);

			// 2. Divide by the column width to find the number of rows (use ceil to round up)
			$height = ceil($string_width / $column_width);

			// 3. (Optional) Account for explicit line breaks in your text
			$hard_breaks = substr_count($text, "\n");
			$height = max($height, $hard_breaks + 1);
        	/*
			if(strlen($row1["Comentarios"])/57 > 1){
        	    $height = intdiv(strlen($row1["Comentarios"]), 57);
        	    if(strlen($row1["Comentarios"])%15 > 0){
        	       $height++;
        	    }
        	}
			*/
        	$pdf->SetTextColor(0, 0, 0);
	        $pdf->SetDrawColor(0, 0, 0);
        	$pdf->SetFont('Times' , '' , 6);
        	$pdf->SetXY($x+5,$y+3);
        	//$pdf->Cell(270 , 3, '', 1, 0 , 'C' , false);
        	$pdf->SetXY($x+5,$y+3);
        	$pdf->Cell(8 , (3*$height), az_utf8_decode($row1["Jornada_DescCorta"]), 1, 0 , 'C' , false);
        	$pdf->SetXY($x+13,$y+3);
        	$pdf->SetFont('Times' , '' , 5);
        	$pdf->Cell(20 , (3*$height), az_utf8_decode($row1["Categoria_DESC"]), 1, 0 , 'C' , false);
        	$pdf->SetXY($x+33,$y+3);
        	$pdf->SetFont('Times' , '' , 6);
        	$pdf->Cell(35 , (3*$height), az_utf8_decode($row1["Local"]), 1, 0 , 'C' , false);
        	$pdf->SetXY($x+68,$y+3);
        	$pdf->Cell(6 , (3*$height), '', 1, 0 , 'C' , false);
        	$pdf->SetXY($x+74,$y+3);
        	$pdf->Cell(6 , (3*$height), '', 1, 0 , 'C' , false);
        	$pdf->SetXY($x+80,$y+3);
        	$pdf->Cell(6 , (3*$height), 'VS', 1, 0 , 'C' , false);
        	$pdf->SetXY($x+86,$y+3);
        	$pdf->Cell(6 , (3*$height), '', 1, 0 , 'C' , false);
        	$pdf->SetXY($x+92,$y+3);
        	$pdf->Cell(6 , (3*$height), '', 1, 0 , 'C' , false);
        	$pdf->SetXY($x+98,$y+3);
        	$pdf->Cell(35 , (3*$height), az_utf8_decode($row1["Visitante"]), 1, 0 , 'C' , false);
        	$pdf->SetXY($x+133,$y+3);
        	$pdf->Cell(35 , (3*$height), az_utf8_decode($row1["dia_sem"]) . ', ' . az_utf8_decode($row1["Mes"]) . az_utf8_decode($row1["Fecha_String"]) . '/' .az_utf8_decode($row1["hora"]), 1, 0 , 'C' , false);
        	$pdf->SetXY($x+168,$y+3);
			$pdf->MultiCell(70, (3), az_utf8_decode($row1["Comentarios"]), 1,'L', false);
        	$pdf->SetXY($x+238,$y+3);
        	$pdf->Cell(37 , (3*$height), az_utf8_decode($row1["Campo_DESC"]), 1, 0 , 'C' , false);
        	$y = $y + (3*$height);
            $count = $count + $height;
            
            if($count >= 51){
                $pdf->AddPage();
            	az_pdf_image_file($pdf, $siteRoot, '/imagenes/FondoReporte.png', 0,0,279,216);
            	
            	$x = 0;
            	$y = 40;
            	$pdf->SetTextColor(255,255,255);
            	$pdf->SetFillColor(252, 1, 2);
            	$pdf->SetDrawColor(252, 1, 2);
            	$pdf->SetFont('Times' , '' , 6);
            	$pdf->SetXY($x+5,$y+3);
            	$pdf->Cell(270 , 3, '', 1, 0 , 'C' , true);
            	$pdf->SetXY($x+5,$y+3);
            	$pdf->Cell(8 , 3, strtoupper('Jor'), 1, 0 , 'C' , true);
            	$pdf->SetXY($x+13,$y+3);
            	$pdf->Cell(20 , 3, strtoupper('Categoria'), 1, 0 , 'C' , true);
            	$pdf->SetXY($x+33,$y+3);
            	$pdf->Cell(45 , 3, strtoupper('Local'), 1, 0 , 'C' , true);
            	$pdf->SetXY($x+78,$y+3);
            	$pdf->Cell(6 , 3, 'P', 1, 0 , 'C' , true);
            	$pdf->SetXY($x+84,$y+3);
            	$pdf->Cell(6 , 3, 'G', 1, 0 , 'C' , true);
            	$pdf->SetXY($x+90,$y+3);
            	$pdf->Cell(6 , 3, 'VS', 1, 0 , 'C' , true);
            	$pdf->SetXY($x+96,$y+3);
            	$pdf->Cell(6 , 3, 'G', 1, 0 , 'C' , true);
            	$pdf->SetXY($x+102,$y+3);
            	$pdf->Cell(6 , 3, 'P', 1, 0 , 'C' , true);
            	$pdf->SetXY($x+108,$y+3);
            	$pdf->Cell(45 , 3, strtoupper('Visitante'), 1, 0 , 'C' , true);
            	$pdf->SetXY($x+153,$y+3);
            	$pdf->Cell(40 , 3, strtoupper('Fecha/Hora'), 1, 0 , 'C' , true);
            	$pdf->SetXY($x+193,$y+3);
            	$pdf->Cell(70 , 3, strtoupper('Observaciones'), 1, 0 , 'C' , true);
            	$pdf->SetXY($x+238,$y+3);
            	$pdf->Cell(37 , 3, strtoupper('Campo'), 1, 0 , 'C' , true);
            	
    	    	$x = 0;
            	$y = 0;
            	$col = 0;
            	$rowc = 0;
            	$pdf->SetTextColor(252, 1, 2);
            	$pdf->SetXY(40,32);
            	$pdf->SetFont('Helvetica' , 'B' , 20);
            	$pdf->Cell(225 , 8, 'Total de Juegos: ' . $totalJuegos, 35, 0 , 'C' , false);
            	$pdf->SetTextColor(0, 0, 0);
            	
                $x = 0;
                $y = 43;
                $count = 1;
        	}
		} 
	}else {
		$pdf->Cell(200 , 8, $lang['9998'], 0, 0 , 'C' , false);
	}
	
    
	$Config->close();

	$pdf->Output();
?>
