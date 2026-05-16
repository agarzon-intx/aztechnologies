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
	$jornada = htmlspecialchars($_GET['Jornada_ID']);
	
	$server = $fgmembersite->getSitename();

	$Config->LoadLogo();
	$Config->LoadFlags();
	$Config->LoadRegionalSettings();
	
	$pdf = new FPDF('L','mm','Letter');
	$pdf->AddPage();
	$pdf->SetAutoPageBreak(false,1);
	$pdf->SetMargins(5, 5, 5, 5);	
	$x = 0;
	$y = 40;
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0, 110, 191);
	$pdf->SetDrawColor(0, 110, 191);
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
	$pdf->Cell(45 , 3, strtoupper('Observaciones'), 1, 0 , 'C' , true);
	$pdf->SetXY($x+238,$y+3);
	$pdf->Cell(37 , 3, strtoupper('Campo'), 1, 0 , 'C' , true);
	
	$sql = "select distinct b.Jornada_DescCorta, g.Torneo_Desc
			from $schema.Juegos a
				join $schema.Jornada b on a.Fecha between b.Fecha_Inicio and b.Fecha_Fin
				join $schema.Torneos g on a.Torneo_ID = g.Torneo_ID
			where a.Torneo_ID = $torneo and b.Jornada_ID = $jornada";
	$result1 = $Config->query($sql);
	if ($result1->num_rows > 0) {
		// output data of each row
		while($row1 = $result1->fetch_assoc()) {
	    	$x = 0;
        	$y = 0;
        	$col = 0;
        	$rowc = 0;
        
        	$pdf->SetXY(0,0);
        	$pdf->Image($server . '/imagenes/' . $Config->logo . '.png',5+((35 - (35 * ($Config->logowidth / 110)))/2),5+((35 - (35 * ($Config->logoheight / 110)))/2),(35 * ($Config->logowidth / 110)), (35 * ($Config->logoheight / 110)), 'PNG');
        	$pdf->SetFont('Helvetica' , 'B' , 25);
	        $pdf->SetTextColor(0, 110, 191);
	        $pdf->SetDrawColor(0, 0, 0);
        	$pdf->SetXY(40,8);
        	$pdf->Cell(225 , 8, utf8_decode($Config->liga), 35, 0 , 'C' , false);
        	$pdf->SetXY(40,16);
        	$pdf->SetFont('Helvetica' , 'IB' , 18);
        	$pdf->Cell(225 , 8, '"INNOVANDO EL FUTBOL"', 35, 0 , 'C' , false);
        	$pdf->SetXY(40,24);
        	$pdf->SetFont('Helvetica' , 'B' , 22);
        	$pdf->Cell(225 , 8, utf8_decode($row1["Torneo_Desc"]), 35, 0 , 'C' , false);
        	$pdf->SetXY(40,32);
        	$pdf->SetFont('Helvetica' , 'B' , 20);
        	$pdf->Cell(225 , 8, $lang['986'] . ' ' . utf8_decode($row1["Jornada_DescCorta"]) . ' (Fecha)', 35, 0 , 'C' , false);
        	$pdf->SetTextColor(0, 0, 0);

		} 
	}else {
		$pdf->Cell(200 , 8, $lang['9998'], 0, 0 , 'C' , false);
	}
    $x = 0;
    $y = 43;
	$sql = "select dc.Categoria_DESC, b.Jornada_DescCorta, a.Juego_ID, a.Local_ID, d.Equipo_FULLDESC as Local, a.Visitante_ID, f.Equipo_FULLDESC as Visitante, b.Fecha, day(b.Fecha) Dia, 
					ELT(DATE_FORMAT(a.Fecha,'%m'),'Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic')  Mes, year(a.Fecha) Anio, a.Campo_ID, DATE_FORMAT(a.Fecha, '%W') dia_sem,
					case when c.Campo_DESC is null then e.Campo_DESC else c.Campo_DESC end Campo_DESC, g.Torneo_Desc, DATE_FORMAT(a.Fecha, ' %d %Y') Fecha_String, Comentarios, TIME_FORMAT(a.Horario, '%H:%i %p') hora
			from $schema.Juegos a
				join $schema.Jornada b on a.Fecha between b.Fecha_Inicio and b.Fecha_Fin
				left outer join $schema.Campos c on a.Campo_ID = c.Campo_ID
				join $schema.Equipos d on a.Torneo_ID = d.Torneo_ID and a.Local_ID = d.Equipo_ID 
				join $schema.Categorias dc on d.Fuerza = dc.Categoria_ID
				join $schema.Campos e on d.Campo_ID = e.Campo_ID
				join $schema.Equipos f on a.Torneo_ID = f.Torneo_ID and a.Visitante_ID = f.Equipo_ID 
				join $schema.Torneos g on a.Torneo_ID = g.Torneo_ID
			where a.Torneo_ID = $torneo and b.Jornada_ID = $jornada
			order by a.Fecha, a.Horario asc";
	$result1 = $Config->query($sql);
	if ($result1->num_rows > 0) {
		// output data of each row
		while($row1 = $result1->fetch_assoc()) {
        	
        	$pdf->SetTextColor(0, 0, 0);
	        $pdf->SetDrawColor(0, 0, 0);
        	$pdf->SetFont('Times' , '' , 6);
        	$pdf->SetXY($x+5,$y+3);
        	$pdf->Cell(270 , 3, '', 1, 0 , 'C' , false);
        	$pdf->SetXY($x+5,$y+3);
        	$pdf->Cell(8 , 3, utf8_decode($row1["Jornada_DescCorta"]), 1, 0 , 'C' , false);
        	$pdf->SetXY($x+13,$y+3);
        	$pdf->Cell(20 , 3, utf8_decode($row1["Categoria_DESC"]), 1, 0 , 'C' , false);
        	$pdf->SetXY($x+33,$y+3);
        	$pdf->Cell(45 , 3, utf8_decode($row1["Local"]), 1, 0 , 'C' , false);
        	$pdf->SetXY($x+78,$y+3);
        	$pdf->Cell(6 , 3, '', 1, 0 , 'C' , false);
        	$pdf->SetXY($x+84,$y+3);
        	$pdf->Cell(6 , 3, '', 1, 0 , 'C' , false);
        	$pdf->SetXY($x+90,$y+3);
        	$pdf->Cell(6 , 3, 'VS', 1, 0 , 'C' , false);
        	$pdf->SetXY($x+96,$y+3);
        	$pdf->Cell(6 , 3, '', 1, 0 , 'C' , false);
        	$pdf->SetXY($x+102,$y+3);
        	$pdf->Cell(6 , 3, '', 1, 0 , 'C' , false);
        	$pdf->SetXY($x+108,$y+3);
        	$pdf->Cell(45 , 3, utf8_decode($row1["Visitante"]), 1, 0 , 'C' , false);
        	$pdf->SetXY($x+153,$y+3);
        	$pdf->Cell(40 , 3, utf8_decode($row1["dia_sem"]) . ', ' . utf8_decode($row1["Mes"]) . utf8_decode($row1["Fecha_String"]) . '/' .utf8_decode($row1["hora"]), 1, 0 , 'C' , false);
        	$pdf->SetXY($x+193,$y+3);
        	$pdf->Cell(45 , 3, utf8_decode($row1["Comentarios"]), 1, 0 , 'C' , false);
        	$pdf->SetXY($x+238,$y+3);
        	$pdf->Cell(37 , 3, utf8_decode($row1["Campo_DESC"]), 1, 0 , 'C' , false);
        	$y = $y + 3;
		} 
	}else {
		$pdf->Cell(200 , 8, $lang['9998'], 0, 0 , 'C' , false);
	}
	
	$pdf->AddPage();
	
	$x = 0;
	$y = 40;
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0, 110, 191);
	$pdf->SetDrawColor(0, 110, 191);
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
	$pdf->Cell(45 , 3, strtoupper('Observaciones'), 1, 0 , 'C' , true);
	$pdf->SetXY($x+238,$y+3);
	$pdf->Cell(37 , 3, strtoupper('Campo'), 1, 0 , 'C' , true);
	
	$sql = "select distinct b.Jornada_DescCorta, g.Torneo_Desc
			from $schema.Juegos a
				join $schema.Jornada b on a.Fecha between b.Fecha_Inicio and b.Fecha_Fin
				join $schema.Torneos g on a.Torneo_ID = g.Torneo_ID
			where a.Torneo_ID = $torneo and b.Jornada_ID = $jornada";
	$result1 = $Config->query($sql);
	if ($result1->num_rows > 0) {
		// output data of each row
		while($row1 = $result1->fetch_assoc()) {
	    	$x = 0;
        	$y = 0;
        	$col = 0;
        	$rowc = 0;
        
        	$pdf->SetXY(0,0);
        	$pdf->Image($server . '/imagenes/' . $Config->logo . '.png',5+((35 - (35 * ($Config->logowidth / 110)))/2),5+((35 - (35 * ($Config->logoheight / 110)))/2),(35 * ($Config->logowidth / 110)), (35 * ($Config->logoheight / 110)), 'PNG');
        	$pdf->SetFont('Helvetica' , 'B' , 25);
	        $pdf->SetTextColor(0, 110, 191);
	        $pdf->SetDrawColor(0, 0, 0);
        	$pdf->SetXY(40,8);
        	$pdf->Cell(225 , 8, utf8_decode($Config->liga), 35, 0 , 'C' , false);
        	$pdf->SetXY(40,16);
        	$pdf->SetFont('Helvetica' , 'IB' , 18);
        	$pdf->Cell(225 , 8, '"INNOVANDO EL FUTBOL"', 35, 0 , 'C' , false);
        	$pdf->SetXY(40,24);
        	$pdf->SetFont('Helvetica' , 'B' , 22);
        	$pdf->Cell(225 , 8, utf8_decode($row1["Torneo_Desc"]), 35, 0 , 'C' , false);
        	$pdf->SetXY(40,32);
        	$pdf->SetFont('Helvetica' , 'B' , 20);
        	$pdf->Cell(225 , 8, $lang['986'] . ' ' . utf8_decode($row1["Jornada_DescCorta"]) . ' (Categoria)', 35, 0 , 'C' , false);
        	$pdf->SetTextColor(0, 0, 0);

		} 
	}else {
		$pdf->Cell(200 , 8, $lang['9998'], 0, 0 , 'C' , false);
	}
    $x = 0;
    $y = 43;
	$sql = "select dc.Categoria_DESC, b.Jornada_DescCorta, a.Juego_ID, a.Local_ID, d.Equipo_FULLDESC as Local, a.Visitante_ID, f.Equipo_FULLDESC as Visitante, b.Fecha, day(b.Fecha) Dia, 
					ELT(DATE_FORMAT(a.Fecha,'%m'),'Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic')  Mes, year(a.Fecha) Anio, a.Campo_ID, DATE_FORMAT(a.Fecha, '%W') dia_sem,
					case when c.Campo_DESC is null then e.Campo_DESC else c.Campo_DESC end Campo_DESC, g.Torneo_Desc, DATE_FORMAT(a.Fecha, ' %d %Y') Fecha_String, Comentarios, TIME_FORMAT(a.Horario, '%H:%i %p') hora
			from $schema.Juegos a
				join $schema.Jornada b on a.Fecha between b.Fecha_Inicio and b.Fecha_Fin
				left outer join $schema.Campos c on a.Campo_ID = c.Campo_ID
				join $schema.Equipos d on a.Torneo_ID = d.Torneo_ID and a.Local_ID = d.Equipo_ID 
				join $schema.Categorias dc on d.Fuerza = dc.Categoria_ID
				join $schema.Campos e on d.Campo_ID = e.Campo_ID
				join $schema.Equipos f on a.Torneo_ID = f.Torneo_ID and a.Visitante_ID = f.Equipo_ID 
				join $schema.Torneos g on a.Torneo_ID = g.Torneo_ID
			where a.Torneo_ID = $torneo and b.Jornada_ID = $jornada
			order by dc.Categoria_DESC,a.Fecha, a.Horario asc";
	$result1 = $Config->query($sql);
	if ($result1->num_rows > 0) {
		// output data of each row
		while($row1 = $result1->fetch_assoc()) {
        	
        	$pdf->SetTextColor(0, 0, 0);
	        $pdf->SetDrawColor(0, 0, 0);
        	$pdf->SetFont('Times' , '' , 6);
        	$pdf->SetXY($x+5,$y+3);
        	$pdf->Cell(270 , 3, '', 1, 0 , 'C' , false);
        	$pdf->SetXY($x+5,$y+3);
        	$pdf->Cell(8 , 3, utf8_decode($row1["Jornada_DescCorta"]), 1, 0 , 'C' , false);
        	$pdf->SetXY($x+13,$y+3);
        	$pdf->Cell(20 , 3, utf8_decode($row1["Categoria_DESC"]), 1, 0 , 'C' , false);
        	$pdf->SetXY($x+33,$y+3);
        	$pdf->Cell(45 , 3, utf8_decode($row1["Local"]), 1, 0 , 'C' , false);
        	$pdf->SetXY($x+78,$y+3);
        	$pdf->Cell(6 , 3, '', 1, 0 , 'C' , false);
        	$pdf->SetXY($x+84,$y+3);
        	$pdf->Cell(6 , 3, '', 1, 0 , 'C' , false);
        	$pdf->SetXY($x+90,$y+3);
        	$pdf->Cell(6 , 3, 'VS', 1, 0 , 'C' , false);
        	$pdf->SetXY($x+96,$y+3);
        	$pdf->Cell(6 , 3, '', 1, 0 , 'C' , false);
        	$pdf->SetXY($x+102,$y+3);
        	$pdf->Cell(6 , 3, '', 1, 0 , 'C' , false);
        	$pdf->SetXY($x+108,$y+3);
        	$pdf->Cell(45 , 3, utf8_decode($row1["Visitante"]), 1, 0 , 'C' , false);
        	$pdf->SetXY($x+153,$y+3);
        	$pdf->Cell(40 , 3, utf8_decode($row1["dia_sem"]) . ', ' . utf8_decode($row1["Mes"]) . utf8_decode($row1["Fecha_String"]) . '/' .utf8_decode($row1["hora"]), 1, 0 , 'C' , false);
        	$pdf->SetXY($x+193,$y+3);
        	$pdf->Cell(45 , 3, utf8_decode($row1["Comentarios"]), 1, 0 , 'C' , false);
        	$pdf->SetXY($x+238,$y+3);
        	$pdf->Cell(37 , 3, utf8_decode($row1["Campo_DESC"]), 1, 0 , 'C' , false);
        	$y = $y + 3;
		} 
	}else {
		$pdf->Cell(200 , 8, $lang['9998'], 0, 0 , 'C' , false);
	}
	$Config->close();

	$pdf->Output();
?>
