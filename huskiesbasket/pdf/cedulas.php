<?php
	require_once dirname(__DIR__) . '/site_paths.php';
	set_time_limit(300);
	require("alphapdf.php");
	require("membersite_config.php");
	$Config = new Configuration();
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin($Config,'cedulas.php');
	$Config->connect();

	$langCode = 'es';
	if (!empty($_COOKIE[$Config->getAlias() . 'language'])) {
		$langCode = preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $_COOKIE[$Config->getAlias() . 'language']);
		if ($langCode === '') {
			$langCode = 'es';
		}
	}
	if ((@include 'lang.' . $langCode . '.php') !== 1 && $langCode !== 'es') {
		include 'lang.es.php';
	}
	$folder = substr(substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT'])),1,strlen(substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT'])))-5);

	$alias = $Config->getAlias();
	$torneoRaw = isset($_GET['Torneo_ID']) && $_GET['Torneo_ID'] !== '' ? $_GET['Torneo_ID'] : ($_COOKIE[$alias . 'season'] ?? '');
	$categoriaRaw = isset($_GET['Categoria_ID']) && $_GET['Categoria_ID'] !== '' ? $_GET['Categoria_ID'] : ($_COOKIE[$alias . 'category'] ?? '');
	$jornadaRaw = isset($_GET['Jornada_ID']) && $_GET['Jornada_ID'] !== '' ? $_GET['Jornada_ID'] : '';
	$torneo = (int) SanitizeInteger($torneoRaw);
	$categoria = (int) SanitizeInteger($categoriaRaw);
	$jornada = (int) SanitizeInteger($jornadaRaw);
	$categoriaFilter = $categoria > 0 ? " and d.Fuerza = $categoria" : '';
	
	$server = $fgmembersite->getSitename();

	$Config->LoadLogo();
	$Config->LoadFlags();
	$Config->LoadRegionalSettings();
	//'P' =Vertical 'L'= Horizontal
	$pdf = new AlphaPDF('P','mm','Letter');
	
	$sql0 = "select dc.Categoria_DESC, b.Jornada_DescCorta, b.Jornada_Desc, a.Juego_ID, a.Local_ID, d.Equipo_FULLDESC as Local, a.Visitante_ID, f.Equipo_FULLDESC as Visitante, b.Fecha, day(b.Fecha) Dia, 
					ELT(DATE_FORMAT(b.Fecha,'%m'),'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre')  Mes, year(b.Fecha) Anio, a.Campo_ID, 
					case when c.Campo_DESC is null then e.Campo_DESC else c.Campo_DESC end Campo_DESC, g.Torneo_Desc, DATE_FORMAT(a.Fecha, '" . $Config->DateFormat5 . "') Fecha_String, DATE_FORMAT(a.Horario, '%I:%i %p') Horario
			from $schema.Juegos a
				join $schema.Jornada b on a.Torneo_ID = b.Torneo_ID and a.Fecha between b.Fecha_Inicio and b.Fecha_Fin
				left outer join $schema.Campos c on a.Campo_ID = c.Campo_ID
				join $schema.Equipos d on a.Torneo_ID = d.Torneo_ID and a.Local_ID = d.Equipo_ID 
				join $schema.Categorias dc on d.Fuerza = dc.Categoria_ID and dc.Calendario_ID in (select Calendario_ID from $schema.Jornada where Torneo_ID = a.Torneo_ID and Jornada_ID = $jornada)
				join $schema.Campos e on d.Campo_ID = e.Campo_ID
				join $schema.Equipos f on a.Torneo_ID = f.Torneo_ID and a.Visitante_ID = f.Equipo_ID 
				join $schema.Torneos g on a.Torneo_ID = g.Torneo_ID
			where a.Torneo_ID = $torneo and b.Jornada_ID = $jornada
			order by a.Fecha, a.Horario;";
	$result1 = $Config->query($sql0);
	if ($result1->num_rows > 0) {
		// output data of each row
		while($row1 = $result1->fetch_assoc()) {
			$localid = utf8_decode($row1["Local_ID"]);
			$visitanteid = utf8_decode($row1["Visitante_ID"]);
			$x = 0;
			$y = 0;
			$col = 0;
			$rowc = 0;
		
			$pdf->AddPage();
			$pdf->SetAutoPageBreak(false,1);
			$pdf->SetMargins(4, 4, 4, 4);	
		
		    $pdf->SetFont('Helvetica' , '' , 12);
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetXY(5,20.5);
			$pdf->Cell(206, 13, ' ', 1, 1 , 'L' , false); 
			
		

        	/*LOGOS*/
	       	$pdf->SetXY(15,8);
			$pdf->Image($server . '/imagenes/' . $Config->logo . '.png',5,5+((30 - (30 * ($Config->logoheight / 110)))/2),(15 * ($Config->logowidth / 110)), (15 * ($Config->logoheight / 110)), 'PNG');

			$pdf->SetXY(70,7);
			$pdf->Image($server . '/imagenes/elite.png' ,28, 5, 30, 0 ,'PNG');
			
			$pdf->SetXY(97,5);
			$pdf->Image($server . '/imagenes/aztechnologies.png' ,151, 5, 60, 0 ,'PNG');
			
			/*Titulo Hoja de Anotación*/
			$pdf->SetFont('Helvetica' , '' , 20);
			$pdf->SetTextColor(0, 0, 0);
         	$pdf->SetXY(5,8);
			$pdf->Cell(206, 5,  $lang['10609'] , '', 0 , 'C' , false);

			/*Titulo Jubal*/
			$pdf->SetXY(5,15);
			$pdf->SetFont('Helvetica' , 'B' , 18);
			$pdf->Cell(206,4, $Config->liga, 0, 1 , 'C' , false);

			$pdf->SetFont('Helvetica' , '' , 9);
			$pdf->SetTextColor(0, 0, 0);	   

			/*Desc. Torneo */
			$pdf->SetXY(5,21);
			$pdf->Cell(65, 4, $lang['110816'] . ': ' . utf8_decode($row1["Torneo_Desc"]), 0, 1 , 'L' , false);
			
			/*Desc. Categoria - Rama */
			$pdf->SetXY(70,21);
			$pdf->Cell(70, 4, $lang['953'] . ': ' . utf8_decode($row1["Categoria_DESC"]), 0, 1 , 'L' , false);

			/*Torneo y Categoria */
			$pdf->SetXY(140,21);
			$pdf->Cell(71, 4,  $lang['986'] . ': ' . utf8_decode($row1["Jornada_Desc"]) . '', 0, 1 , 'L' , false);

			 /*Cancha y Juego */
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetXY(5,25);
			$pdf->Cell(65, 4, $lang['10517'] . ' ' . $row1["Campo_DESC"], 0, 1 , 'L' , false);	
        
			// Configurar una sóla línea titulo Jubal
			$pdf->SetXY(70,25);
			$pdf->SetFont('Helvetica' , 'B' , 10);
			$pdf->SetTextColor(0, 0, 255);
			$pdf->Cell(68 , 4, utf8_decode($row1["Fecha_String"]) . '  ' . utf8_decode($row1["Horario"]) , 0, 1 , 'L' , false);
			
			/*1er. Juez*/
			$pdf->SetFont('Helvetica' , '' , 9);
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetXY(140,25);
			$pdf->Cell(70, 4, $lang['110818'] . ':', 0, 1 , 'L' , false);
			
			/*1er. Juez*/
			$pdf->SetFont('Helvetica' , '' , 9);
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetXY(5,29);
			$pdf->Cell(65, 4, $lang['110817'] . ' 1:', 0, 1 , 'L' , false);
			
			/*2do. Juez*/
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetXY(70,29);
			$pdf->Cell(70, 4, $lang['110817'] . ' 2:', 0, 1 , 'L' , false);
		
	      /*3er. Juez*/
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetXY(140,29);
			$pdf->Cell(71, 4, $lang['110819'] . ':', 0, 1 , 'L' , false);
		
        
		
			$y = 79;
		
			$pages = 0;
			
			$y = $y -5;

				
			$y = 2;
			$x = 0;
			/*Empieza apartado de Anotación Progresivo*/
			/*TExto A -  B Encabezado*/
			$pdf->SetFont('Times' , 'B' , 12);
	        $pdf->SetXY($x+131,$y+33);
			$pdf->Cell(80, 5, $lang['10617'], 1,2 , 'C' , false);	

			$pdf->SetFont('Times' , 'B' , 9);
			$pdf->SetXY($x+131,$y+38);
			$pdf->Cell(10 , 5, $lang['10557'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+141,$y+38);
			$pdf->Cell(10 , 5, $lang['10558'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+151,$y+38);
			$pdf->Cell(10 , 5, $lang['10557'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+161,$y+38);
			$pdf->Cell(10 , 5, $lang['10558'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+171,$y+38);
			$pdf->Cell(10 , 5, $lang['10557'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+181,$y+38);
			$pdf->Cell(10 , 5, $lang['10558'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+191,$y+38);
			$pdf->Cell(10 , 5, $lang['10557'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+201,$y+38);
			$pdf->Cell(10 , 5, $lang['10558'], 1, 1 , 'C' , false);
			
		    $pdf->SetFont('Times' , '' , 9);
			/*1a. Fila Anotación*/
			$pdf->SetXY($x+131,$y+43);
			$pdf->Cell(10, 4.5, '1', 1, 0, 'C' , false);
			$pdf->SetXY($x+141,$y+43);
			$pdf->Cell(10, 4.5, '1', 1, 0, 'C' , false);
			$pdf->SetXY($x+151,$y+43);
			$pdf->Cell(10, 4.5, '41', 1, 0, 'C' , false);
			$pdf->SetXY($x+161,$y+43);
			$pdf->Cell(10, 4.5, '41', 1, 0, 'C' , false);
			$pdf->SetXY($x+171,$y+43);
			$pdf->Cell(10, 4.5, '81', 1, 0, 'C' , false);
			$pdf->SetXY($x+181,$y+43);
			$pdf->Cell(10, 4.5, '81', 1, 0, 'C' , false);
			$pdf->SetXY($x+191,$y+43);
			$pdf->Cell(10, 4.5, '121', 1, 0, 'C' , false);
			$pdf->SetXY($x+201,$y+43);
			$pdf->Cell(10, 4.5, '121', 1, 0, 'C' , false);
			
			
			/*2a. Fila Anotación*/
            $pdf->SetXY($x+131,$y+47.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '2', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+47.5);
            $pdf->Cell(10, 4.5, '2', 1, 0, 'C' , false);
            $pdf->SetXY($x+151,$y+47.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '42', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+47.5);
            $pdf->Cell(10, 4.5, '42', 1, 0, 'C' , false);
            $pdf->SetXY($x+171,$y+47.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '82', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+47.5);
            $pdf->Cell(10, 4.5, '82', 1, 0, 'C' , false);
            $pdf->SetXY($x+191,$y+47.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '122', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+47.5);
            $pdf->Cell(10, 4.5, '122', 1, 0, 'C' , false);
            
            /*3ra. Fila Anotación*/
            $pdf->SetXY($x+131,$y+52);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '3', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+52);
            $pdf->Cell(10, 4.5, '3', 1, 0, 'C' , false);
            $pdf->SetXY($x+151,$y+52);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10,4.5, '43', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+52);
            $pdf->Cell(10, 4.5, '43', 1, 0, 'C' , false);
            $pdf->SetXY($x+171,$y+52);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '83', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+52);
            $pdf->Cell(10, 4.5, '83', 1, 0, 'C' , false);
            $pdf->SetXY($x+191,$y+52);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '123', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+52);
            $pdf->Cell(10, 4.5, '123', 1, 0, 'C' , false);

           /*4a. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+56.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '4', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+56.5);
            $pdf->Cell(10, 4.5, '4', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+56.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '44', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+56.5);
            $pdf->Cell(10, 4.5, '44', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+56.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '84', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+56.5);
            $pdf->Cell(10, 4.5, '84', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+56.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '124', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+56.5);
            $pdf->Cell(10, 4.5, '124', 1, 0, 'C' , false);

           /*5a. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+61);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '5', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+61);
            $pdf->Cell(10, 4.5, '5', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+61);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '45', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+61);
            $pdf->Cell(10, 4.5, '45', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+61);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '85', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+61);
            $pdf->Cell(10, 4.5, '85', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+61);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '125', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+61);
            $pdf->Cell(10, 4.5, '125', 1, 0, 'C' , false);

           /*6a. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+65.5);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '6', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+65.5);
            $pdf->Cell(10, 4.5, '6', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+65.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '46', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+65.5);
            $pdf->Cell(10, 4.5, '46', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+65.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '86', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+65.5);
            $pdf->Cell(10, 4.5, '86', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+65.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '126', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+65.5);
            $pdf->Cell(10, 4.5, '126', 1, 0, 'C' , false);

           /*7a. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+70);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '7', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+70);
            $pdf->Cell(10, 4.5, '7', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+70);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '47', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+70);
            $pdf->Cell(10, 4.5, '47', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+70);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '87', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+70);
            $pdf->Cell(10, 4.5, '87', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+70);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '127', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+70);
            $pdf->Cell(10, 4.5, '127', 1, 0, 'C' , false);

   /*8a. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+74.5);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '8', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+74.5);
            $pdf->Cell(10, 4.5, '8', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+74.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '48', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+74.5);
            $pdf->Cell(10, 4.5, '48', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+74.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '88', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+74.5);
            $pdf->Cell(10, 4.5, '88', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+74.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '128', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+74.5);
            $pdf->Cell(10, 4.5, '128', 1, 0, 'C' , false);

           /*9a. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+79);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '9', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+79);
            $pdf->Cell(10, 4.5, '9', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+79);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '49', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+79);
            $pdf->Cell(10, 4.5, '49', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+79);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '89', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+79);
            $pdf->Cell(10, 4.5, '89', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+79);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '129', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+79);
            $pdf->Cell(10, 4.5, '129', 1, 0, 'C' , false);

       /*10a. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+83.5);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '10', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+83.5);
            $pdf->Cell(10, 4.5, '10', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+83.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '50', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+83.5);
            $pdf->Cell(10, 4.5, '50', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+83.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '90', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+83.5);
            $pdf->Cell(10, 4.5, '90', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+83.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '130', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+83.5);
            $pdf->Cell(10, 4.5, '130', 1, 0, 'C' , false);

        /*11a. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+88);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '11', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+88);
            $pdf->Cell(10, 4.5, '11', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+88);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '51', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+88);
            $pdf->Cell(10, 4.5, '51', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+88);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '91', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+88);
            $pdf->Cell(10, 4.5, '91', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+88);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '131', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+88);
            $pdf->Cell(10, 4.5, '131', 1, 0, 'C' , false);

            /*12a. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+92.5);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '12', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+92.5);
            $pdf->Cell(10, 4.5, '12', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+92.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '52', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+92.5);
            $pdf->Cell(10, 4.5, '52', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+92.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '92', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+92.5);
            $pdf->Cell(10, 4.5, '92', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+92.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '132', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+92.5);
            $pdf->Cell(10, 4.5, '132', 1, 0, 'C' , false);

            /*13a. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+97);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '13', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+97);
            $pdf->Cell(10, 4.5, '13', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+97);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '53', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+97);
            $pdf->Cell(10, 4.5, '53', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+97);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '93', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+97);
            $pdf->Cell(10, 4.5, '93', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+97);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '133', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+97);
            $pdf->Cell(10, 4.5, '133', 1, 0, 'C' , false);

                /*14a. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+101.5);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '14', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+101.5);
            $pdf->Cell(10, 4.5, '14', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+101.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '54', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+101.5);
            $pdf->Cell(10, 4.5, '54', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+101.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '94', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+101.5);
            $pdf->Cell(10, 4.5, '94', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+101.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '134', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+101.5);
            $pdf->Cell(10, 4.5, '134', 1, 0, 'C' , false);

        /*15a. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+106);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '15', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+106);
            $pdf->Cell(10, 4.5, '15', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+106);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '55', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+106);
            $pdf->Cell(10, 4.5, '55', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+106);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '95', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+106);
            $pdf->Cell(10, 4.5, '95', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+106);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '135', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+106);
            $pdf->Cell(10, 4.5, '135', 1, 0, 'C' , false);

        /*16a. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+110.5);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '16', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+110.5);
            $pdf->Cell(10, 4.5, '16', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+110.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '56', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+110.5);
            $pdf->Cell(10, 4.5, '56', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+110.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '96', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+110.5);
            $pdf->Cell(10, 4.5, '96', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+110.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '136', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+110.5);
            $pdf->Cell(10, 4.5, '136', 1, 0, 'C' , false);

    /*17a. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+115);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '17', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+115);
            $pdf->Cell(10, 4.5, '17', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+115);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '57', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+115);
            $pdf->Cell(10, 4.5, '57', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+115);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '97', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+115);
            $pdf->Cell(10, 4.5, '97', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+115);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '137', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+115);
            $pdf->Cell(10, 4.5, '137', 1, 0, 'C' , false);

   /*18a. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+119.5);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '18', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+119.5);
            $pdf->Cell(10, 4.5, '18', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+119.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '58', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+119.5);
            $pdf->Cell(10, 4.5, '58', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+119.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '98', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+119.5);
            $pdf->Cell(10, 4.5, '98', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+119.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '138', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+119.5);
            $pdf->Cell(10, 4.5, '138', 1, 0, 'C' , false);

     /*19a. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+124);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '19', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+124);
            $pdf->Cell(10, 4.5, '19', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+124);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '59', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+124);
            $pdf->Cell(10, 4.5, '59', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+124);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '99', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+124);
            $pdf->Cell(10, 4.5, '99', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+124);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '139', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+124);
            $pdf->Cell(10, 4.5, '139', 1, 0, 'C' , false);

   /*20a. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+128.5);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '20', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+128.5);
            $pdf->Cell(10, 4.5, '20', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+128.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '60', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+128.5);
            $pdf->Cell(10, 4.5, '60', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+128.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '100', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+128.5);
            $pdf->Cell(10, 4.5, '100', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+128.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '140', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+128.5);
            $pdf->Cell(10, 4.5, '140', 1, 0, 'C' , false);

/* 21. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+133);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '21', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+133);
            $pdf->Cell(10, 4.5, '21', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+133);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '61', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+133);
            $pdf->Cell(10, 4.5, '61', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+133);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '101', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+133);
            $pdf->Cell(10, 4.5, '101', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+133);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '141', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+133);
            $pdf->Cell(10, 4.5, '141', 1, 0, 'C' , false);

/* 22. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+137.5);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '22', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+137.5);
            $pdf->Cell(10, 4.5, '22', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+137.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '62', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+137.5);
            $pdf->Cell(10, 4.5, '62', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+137.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '102', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+137.5);
            $pdf->Cell(10, 4.5, '102', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+137.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '142', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+137.5);
            $pdf->Cell(10, 4.5, '142', 1, 0, 'C' , false);

/* 23. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+142);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '23', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+142);
            $pdf->Cell(10, 4.5, '23', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+142);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '63', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+142);
            $pdf->Cell(10, 4.5, '63', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+142);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '103', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+142);
            $pdf->Cell(10, 4.5, '103', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+142);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '143', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+142);
            $pdf->Cell(10, 4.5, '143', 1, 0, 'C' , false);

/* 24. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+146.5);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '24', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+146.5);
            $pdf->Cell(10, 4.5, '24', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+146.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '64', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+146.5);
            $pdf->Cell(10, 4.5, '64', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+146.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '104', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+146.5);
            $pdf->Cell(10, 4.5, '104', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+146.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '144', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+146.5);
            $pdf->Cell(10, 4.5, '144', 1, 0, 'C' , false);

     /* 25. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+151);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '25', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+151);
            $pdf->Cell(10, 4.5, '25', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+151);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '65', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+151);
            $pdf->Cell(10, 4.5, '65', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+151);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '105', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+151);
            $pdf->Cell(10, 4.5, '105', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+151);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '145', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+151);
            $pdf->Cell(10, 4.5, '145', 1, 0, 'C' , false);

/* 26. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+155.5);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '26', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+155.5);
            $pdf->Cell(10, 4.5, '26', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+155.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '66', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+155.5);
            $pdf->Cell(10, 4.5, '66', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+155.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '106', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+155.5);
            $pdf->Cell(10, 4.5, '106', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+155.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '146', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+155.5);
            $pdf->Cell(10, 4.5, '146', 1, 0, 'C' , false);

    /* 27. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+160);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '27', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+160);
            $pdf->Cell(10, 4.5, '27', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+160);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '67', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+160);
            $pdf->Cell(10, 4.5, '67', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+160);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '107', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+160);
            $pdf->Cell(10,4.5, '107', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+160);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '147', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+160);
            $pdf->Cell(10, 4.5, '147', 1, 0, 'C' , false);

    /* 28. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+164.5);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '28', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+164.5);
            $pdf->Cell(10, 4.5, '28', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+164.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '68', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+164.5);
            $pdf->Cell(10, 4.5, '68', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+164.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '108', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+164.5);
            $pdf->Cell(10, 4.5, '108', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+164.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '148', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+164.5);
            $pdf->Cell(10, 4.5, '148', 1, 0, 'C' , false);

    /* 29. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+169);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '29', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+169);
            $pdf->Cell(10, 4.5, '29', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+169);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '69', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+169);
            $pdf->Cell(10, 4.5, '69', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+169);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '109', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+169);
            $pdf->Cell(10, 4.5, '109', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+169);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '149', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+169);
            $pdf->Cell(10, 4.5, '149', 1, 0, 'C' , false);

    /* 30. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+173.5);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '30', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+173.5);
            $pdf->Cell(10, 4.5, '30', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+173.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '70', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+173.5);
            $pdf->Cell(10, 4.5, '70', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+173.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '110', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+173.5);
            $pdf->Cell(10, 4.5, '110', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+173.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '150', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+173.5);
            $pdf->Cell(10, 4.5, '150', 1, 0, 'C' , false);

        /* 31. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+178);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '31', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+178);
            $pdf->Cell(10, 4.5, '31', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+178);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '71', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+178);
            $pdf->Cell(10, 4.5, '71', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+178);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '111', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+178);
            $pdf->Cell(10, 4.5, '111', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+178);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '151', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+178);
            $pdf->Cell(10, 4.5, '151', 1, 0, 'C' , false);

        /* 32. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+182.5);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '32', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+182.5);
            $pdf->Cell(10, 4.5, '32', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+182.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '72', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+182.5);
            $pdf->Cell(10, 4.5, '72', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+182.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '112', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+182.5);
            $pdf->Cell(10, 4.5, '112', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+182.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '152', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+182.5);
            $pdf->Cell(10, 4.5, '152', 1, 0, 'C' , false);

    /* 33. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+187);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '33', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+187);
            $pdf->Cell(10, 4.5, '33', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+187);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '73', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+187);
            $pdf->Cell(10, 4.5, '73', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+187);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '113', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+187);
            $pdf->Cell(10, 4.5, '113', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+187);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '153', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+187);
            $pdf->Cell(10, 4.5, '153', 1, 0, 'C' , false);

    /* 34. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+191.5);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '34', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+191.5);
            $pdf->Cell(10, 4.5, '34', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+191.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '74', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+191.5);
            $pdf->Cell(10, 4.5, '74', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+191.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '114', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+191.5);
            $pdf->Cell(10, 4.5, '114', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+191.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '154', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+191.5);
            $pdf->Cell(10, 4.5, '154', 1, 0, 'C' , false);

        /* 35. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+196);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '35', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+196);
            $pdf->Cell(10, 4.5, '35', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+196);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '75', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+196);
            $pdf->Cell(10, 4.5, '75', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+196);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '115', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+196);
            $pdf->Cell(10, 4.5, '115', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+196);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '155', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+196);
            $pdf->Cell(10, 4.5, '155', 1, 0, 'C' , false);

    /* 36. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+200.5);
             $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '36', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+200.5);
            $pdf->Cell(10, 4.5, '36', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+200.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '76', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+200.5);
            $pdf->Cell(10, 4.5, '76', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+200.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '116', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+200.5);
            $pdf->Cell(10, 4.5, '116', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+200.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '156', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+200.5);
            $pdf->Cell(10, 4.5, '156', 1, 0, 'C' , false);

       /* 37. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+205);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '37', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+205);
            $pdf->Cell(10, 4.5, '37', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+205);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '77', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+205);
            $pdf->Cell(10, 4.5, '77', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+205);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '117', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+205);
            $pdf->Cell(10, 4.5, '117', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+205);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '157', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+205);
            $pdf->Cell(10, 4.5, '157', 1, 0, 'C' , false);

            /* 38. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+209.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '38', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+209.5);
            $pdf->Cell(10, 4.5, '38', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+209.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '78', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+209.5);
            $pdf->Cell(10, 4.5, '78', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+209.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '118', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+209.5);
            $pdf->Cell(10, 4.5, '118', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+209.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '158', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+209.5);
            $pdf->Cell(10, 4.5, '158', 1, 0, 'C' , false);

        /* 39. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+214);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '39', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+214);
            $pdf->Cell(10, 4.5, '39', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+214);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '79', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+214);
            $pdf->Cell(10, 4.5, '79', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+214);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '119', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+214);
            $pdf->Cell(10, 4.5, '119', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+214);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '159', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+214);
            $pdf->Cell(10, 4.5, '159', 1, 0, 'C' , false);

        /* 40. Fila Anotación*/
            $x = 0;
            $pdf->SetXY($x+131,$y+218.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '40', 1, 0, 'C' , false);
            $pdf->SetXY($x+141,$y+218.5);
            $pdf->Cell(10, 4.5, '40', 1, 0, 'C' , false);

            $pdf->SetXY($x+151,$y+218.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '80', 1, 0, 'C' , false);
            $pdf->SetXY($x+161,$y+218.5);
            $pdf->Cell(10, 4.5, '80', 1, 0, 'C' , false);

            $pdf->SetXY($x+171,$y+218.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '120', 1, 0, 'C' , false);
            $pdf->SetXY($x+181,$y+218.5);
            $pdf->Cell(10, 4.5, '120', 1, 0, 'C' , false);

            $pdf->SetXY($x+191,$y+218.5);
            $pdf->SetFont('Times' , '' , 9);
            $pdf->Cell(10, 4.5, '160', 1, 0, 'C' , false);
            $pdf->SetXY($x+201,$y+218.5);
            $pdf->Cell(10, 4.5, '160', 1, 0, 'C' , false);

			/*Equipo Local y visitante*/
			/*Titulo Equipos */
			/*Equipos y Jugadores lado IZquierdo del formato EMA 30.08.2023*/
         
           /*1er. Equipo Local */
	        $pdf->SetFont('Helvetica' , 'B' , 11);
			$pdf->SetXY(5,35);
			$pdf->Cell(124, 5, $lang['10604']. ' ' . $lang['10587'] . ': ' . utf8_decode($row1["Local"]), 1, 1 , 'L' , false);
		  	
			$pdf->SetXY(5,40);
			$pdf->Cell(45, 24, ' ', 1, 1 , 'L' , false); 

			$pdf->SetXY(50,40);
			$pdf->Cell(79, 24, ' ', 1, 1 , 'L' , false); 

		  	 /*Tiempos fuera y Faltas de Equipo */
		  	$pdf->SetFont('Helvetica' , 'B' , 9);
			$pdf->SetTextColor(0, 0, 0);
	        $pdf->SetXY(5,40);
			$pdf->Cell(45, 4, $lang['110822'], 0, 1 , 'L' , false);
			
			/*Tiempos Fuera*/
		  	$pdf->SetFont('Helvetica' , '' , 7);
			$pdf->SetTextColor(0, 0, 0);
	        $pdf->SetXY(5,44);
			$pdf->Cell(15, 4, $lang['110824'], 0, 1 , 'L' , false);
			$pdf->SetXY(22,44);
			$pdf->Cell(4, 4, '', 1, 1 , 'L' , false);
			$pdf->SetXY(26,44);
			$pdf->Cell(4, 4, '', 1, 1 , 'L' , false);
			$pdf->SetXY(30,44);
			$pdf->Cell(4, 4, '', 1, 1 , 'L' , false);

	        $pdf->SetXY(5,49);
			$pdf->Cell(15, 4, $lang['110825'], 0, 1 , 'L' , false);
			$pdf->SetXY(22,49);
			$pdf->Cell(4, 4, '', 1, 1 , 'L' , false);
			$pdf->SetXY(26,49);
			$pdf->Cell(4, 4, '', 1, 1 , 'L' , false);
			$pdf->SetXY(30,49);
			$pdf->Cell(4, 4, '', 1, 1 , 'L' , false);

	        $pdf->SetXY(5,54);
			$pdf->Cell(15, 4, $lang['110826'], 0, 1 , 'L' , false);
			$pdf->SetXY(22,54);
			$pdf->Cell(4, 4, '', 1, 1 , 'L' , false);
			$pdf->SetXY(26,54);
			$pdf->Cell(4, 4, '', 1, 1 , 'L' , false);
			$pdf->SetXY(30,54);
			$pdf->Cell(4, 4, '', 1, 1 , 'L' , false);

	        $pdf->SetXY(5,59);
			$pdf->Cell(15, 4, $lang['110827'], 0, 1 , 'L' , false);
			$pdf->SetXY(22,59);
			$pdf->Cell(4, 4, '', 1, 1 , 'L' , false);
			$pdf->SetXY(26,59);
			$pdf->Cell(4, 4, '', 1, 1 , 'L' , false);
			$pdf->SetXY(30,59);
			$pdf->Cell(4, 4, '', 1, 1 , 'L' , false);
		  
		  
		   /*Faltas de Equipo*/
		  	$pdf->SetFont('Helvetica' , 'B' , 9);
			$pdf->SetTextColor(0, 0, 0);
	        $pdf->SetXY(50,40);
			$pdf->Cell(79, 4, $lang['110823'], 0, 1 , 'C' , false);

			$pdf->SetFont('Helvetica' , '' , 7);
			$pdf->SetTextColor(0, 0, 0);
	        $pdf->SetXY(52,45);
			$pdf->Cell(15, 4, $lang['110824'], 0, 1 , 'L' , false);
			$pdf->SetXY(69,45);
			$pdf->Cell(4, 4, '1', 1, 1 , 'C' , false);
			$pdf->SetXY(73,45);
			$pdf->Cell(4, 4, '2', 1, 1 , 'C' , false);
			$pdf->SetXY(77,45);
			$pdf->Cell(4, 4, '3', 1, 1 , 'C' , false);
			$pdf->SetXY(81,45);
			$pdf->Cell(4, 4, '4', 1, 1 , 'C' , false);
			
			$pdf->SetFont('Helvetica' , '' , 7);
			$pdf->SetTextColor(0, 0, 0);
	        $pdf->SetXY(90,45);
			$pdf->Cell(15, 4, $lang['110825'], 0, 1 , 'L' , false);
			$pdf->SetXY(107,45);
			$pdf->Cell(4, 4, '1', 1, 1 , 'C' , false);
			$pdf->SetXY(111,45);
			$pdf->Cell(4, 4, '2', 1, 1 , 'C' , false);
			$pdf->SetXY(115,45);
			$pdf->Cell(4, 4, '3', 1, 1 , 'C' , false);
			$pdf->SetXY(119,45);
			$pdf->Cell(4, 4, '4', 1, 1 , 'C' , false);
			
			$pdf->SetFont('Helvetica' , '' , 7);
			$pdf->SetTextColor(0, 0, 0);
	        $pdf->SetXY(52,50);
			$pdf->Cell(15, 4, $lang['110826'], 0, 1 , 'L' , false);
			$pdf->SetXY(69,50);
			$pdf->Cell(4, 4, '1', 1, 1 , 'C' , false);
			$pdf->SetXY(73,50);
			$pdf->Cell(4, 4, '2', 1, 1 , 'C' , false);
			$pdf->SetXY(77,50);
			$pdf->Cell(4, 4, '3', 1, 1 , 'C' , false);
			$pdf->SetXY(81,50);
			$pdf->Cell(4, 4, '4', 1, 1 , 'C' , false);
			
			$pdf->SetFont('Helvetica' , '' , 7);
			$pdf->SetTextColor(0, 0, 0);
	        $pdf->SetXY(90,50);
			$pdf->Cell(15, 4, $lang['110827'], 0, 1 , 'L' , false);
			$pdf->SetXY(107,50);
			$pdf->Cell(4, 4, '1', 1, 1 , 'C' , false);
			$pdf->SetXY(111,50);
			$pdf->Cell(4, 4, '2', 1, 1 , 'C' , false);
			$pdf->SetXY(115,50);
			$pdf->Cell(4, 4, '3', 1, 1 , 'C' , false);
			$pdf->SetXY(119,50);
			$pdf->Cell(4, 4, '4', 1, 1 , 'C' , false);
			
			$pdf->SetFont('Helvetica' , '' , 7);
			$pdf->SetTextColor(0, 0, 0);
	        $pdf->SetXY(52,58);
			$pdf->Cell(15, 4, $lang['110828'], 0, 1 , 'L' , false);
			$pdf->SetXY(77,58);
			$pdf->Cell(4, 4, '1', 1, 1 , 'C' , false);
			$pdf->SetXY(81,58);
			$pdf->Cell(4, 4, '2', 1, 1 , 'C' , false);
			$pdf->SetXY(85,58);
			$pdf->Cell(4, 4, '3', 1, 1 , 'C' , false);
			$pdf->SetXY(89,58);
			$pdf->Cell(4, 4, '4', 1, 1 , 'C' , false);
			
			$pdf->SetXY(97,58);
			$pdf->Cell(4, 4, '1', 1, 1 , 'C' , false);
			$pdf->SetXY(101,58);
			$pdf->Cell(4, 4, '2', 1, 1 , 'C' , false);
			$pdf->SetXY(105,58);
			$pdf->Cell(4, 4, '3', 1, 1 , 'C' , false);
			$pdf->SetXY(109,58);
			$pdf->Cell(4, 4, '4', 1, 1 , 'C' , false);
			
			/*No*/
			$pdf->SetFont('Helvetica' , 'B' , 9);
			$pdf->SetTextColor(0, 0, 0);

			$pdf->SetXY(5,64);
			$pdf->Cell(10, 6, $lang['10619'], 1, 1 , 'C' , false);
			$pdf->SetXY(15,64);
			$pdf->Cell(69, 6, $lang['10618'], 1, 1 , 'C' , false);
			$pdf->SetFont('Helvetica' , 'B' , 7);
			$pdf->SetXY(84,64);
			$pdf->Cell(45, 3, $lang['110829'], 1, 1 , 'C' , false);
			$pdf->SetXY(84,67);
			$pdf->Cell(9, 3, '1', 1, 1 , 'C' , false);
			$pdf->SetXY(93,67);
	    	$pdf->Cell(9, 3, '2', 1, 1 , 'C' , false);
			$pdf->SetXY(102,67);
	    	$pdf->Cell(9, 3, '3', 1, 1 , 'C' , false);
			$pdf->SetXY(111,67);
	    	$pdf->Cell(9, 3, '4', 1, 1 , 'C' , false);
			$pdf->SetXY(120,67);
	    	$pdf->Cell(9, 3, '5', 1, 1 , 'C' , false);
			
			$tmpy = 70;
			$ct = 0;
			while($ct < 17){
				$pdf->SetFont('Helvetica' , 'B' , 9);
				$pdf->SetTextColor(0, 0, 0);

				$pdf->SetXY(5,$tmpy);
				$pdf->Cell(10, 4, '', 1, 1 , 'C' , false);
				$pdf->SetXY(15,$tmpy);
				$pdf->Cell(69, 4, '', 1, 1 , 'L' , false);
				$pdf->SetXY(84,$tmpy);
				$pdf->Cell(9, 4, '', 1, 1 , 'C' , false);
				$pdf->SetXY(93,$tmpy);
				$pdf->Cell(9, 4, '', 1, 1 , 'C' , false);
				$pdf->SetXY(102,$tmpy);
				$pdf->Cell(9, 4, '', 1, 1 , 'C' , false);
				$pdf->SetXY(111,$tmpy);
				$pdf->Cell(9, 4, '', 1, 1 , 'C' , false);
				$pdf->SetXY(120,$tmpy);
				$pdf->Cell(9, 4, '', 1, 1 , 'C' , false);

				$tmpy = $tmpy + 4;
				$ct++;
			}
			/*Equipos */
			$x = $x +0;
			$y = $y - 0.5;

			$countRow = 0;
			$y = $y - 2;
			
			$y = 70;
			
			$sqlL = "select * from (
									SELECT Jugador_ID,
										Clave,
										Nombre,
										Apellido_P,
										Apellido_M,
										Apodo,
										CONCAT(Apellido_P,' ',Nombre) AS Jugador,
										date_format(Fecha_Nacimiento,'%d/%m/%Y') Fecha_Nacimiento,
										YEAR(CURDATE())-YEAR(Fecha_Nacimiento) Edad,
										case when YEAR(CURDATE())-YEAR(Fecha_Nacimiento)  >= 50 then 1
											 when YEAR(CURDATE())-YEAR(Fecha_Nacimiento)  >= 35  and YEAR(CURDATE())-YEAR(Fecha_Nacimiento)  < 50 then 2
											 when YEAR(CURDATE())-YEAR(Fecha_Nacimiento)  < 35 then 3
										end EdadGrupo,
										Curp,
										Numero,
										Estatus,
										a.Equipo_ID,
										Comentarios,
										Telefono,
										correo,
										Sexo,
										Validado,
										FechaAlta
									FROM Jugadores a
									where Estatus = 'A' and Validado = 1 and Jugador_tipo = 0 and Equipo_ID = $localid) a
					order by                         
						EdadGrupo asc, convert(Numero,unsigned) asc
					limit 15";
			$resultL = $Config->query($sqlL);
			//Lista Local
			$tmpy = 70;
			if ($resultL->num_rows > 0) {
				// output data of each row
				while($rowL = $resultL->fetch_assoc()) {
					$pdf->SetXY(5,$tmpy);
					$pdf->SetFont('Times' , 'B' , 9);
					$pdf->Cell(10, 4, utf8_decode('' . $rowL["Numero"] . ''), 0, 1 , 'C' , false);
					$pdf->SetXY(15,$tmpy);
					$pdf->SetFont('Times' , '' , 9);
					$pdf->Cell(69, 4, utf8_decode('' . $rowL["Jugador"] . ''), 0, 1 , 'L' , false);
					$pdf->SetXY(84,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
					$pdf->SetXY(93,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
					$pdf->SetXY(102,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
					$pdf->SetXY(111,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
					$pdf->SetXY(120,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
						
					$tmpy = $tmpy + 4;
				}
			}

			$sqlL = "select * from (
									SELECT Jugador_ID,
										Clave,
										Nombre,
										Apellido_P,
										Apellido_M,
										Apodo,
										CONCAT(Apellido_P,' ',Nombre) AS Jugador,
										date_format(Fecha_Nacimiento,'%d/%m/%Y') Fecha_Nacimiento,
										YEAR(CURDATE())-YEAR(Fecha_Nacimiento) Edad,
										case when YEAR(CURDATE())-YEAR(Fecha_Nacimiento)  >= 50 then 1
											 when YEAR(CURDATE())-YEAR(Fecha_Nacimiento)  >= 35  and YEAR(CURDATE())-YEAR(Fecha_Nacimiento)  < 50 then 2
											 when YEAR(CURDATE())-YEAR(Fecha_Nacimiento)  < 35 then 3
										end EdadGrupo,
										Curp,
										Numero,
										Estatus,
										a.Equipo_ID,
										Comentarios,
										Telefono,
										correo,
										Sexo,
										Validado,
										FechaAlta
									FROM Jugadores a
									where Estatus = 'A' and Validado = 1 and Jugador_tipo = 1 and Equipo_ID = $localid) a
					order by                         
						EdadGrupo asc, convert(Numero,unsigned) asc
					limit 1";
			$resultL = $Config->query($sqlL);
			//Lista Local
			$tmpy = 130;
			if ($resultL->num_rows > 0) {
				// output data of each row
				while($rowL = $resultL->fetch_assoc()) {
					$pdf->SetXY(5,$tmpy);
					$pdf->SetFont('Times' , 'B' , 9);
					$pdf->Cell(10, 4, $lang['110830'], 0, 1 , 'C' , false);
					$pdf->SetXY(15,$tmpy);
					$pdf->SetFont('Times' , '' , 9);
					$pdf->Cell(69, 4, utf8_decode('' . $rowL["Jugador"] . ''), 0, 1 , 'L' , false);
					$pdf->SetXY(84,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
					$pdf->SetXY(93,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
					$pdf->SetXY(102,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
					$pdf->SetXY(111,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
					$pdf->SetXY(120,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
						
					$tmpy = $tmpy + 4;
				}
			}

			$sqlL = "select * from (
									SELECT Jugador_ID,
										Clave,
										Nombre,
										Apellido_P,
										Apellido_M,
										Apodo,
										CONCAT(Apellido_P,' ',Nombre) AS Jugador,
										date_format(Fecha_Nacimiento,'%d/%m/%Y') Fecha_Nacimiento,
										YEAR(CURDATE())-YEAR(Fecha_Nacimiento) Edad,
										case when YEAR(CURDATE())-YEAR(Fecha_Nacimiento)  >= 50 then 1
											 when YEAR(CURDATE())-YEAR(Fecha_Nacimiento)  >= 35  and YEAR(CURDATE())-YEAR(Fecha_Nacimiento)  < 50 then 2
											 when YEAR(CURDATE())-YEAR(Fecha_Nacimiento)  < 35 then 3
										end EdadGrupo,
										Curp,
										Numero,
										Estatus,
										a.Equipo_ID,
										Comentarios,
										Telefono,
										correo,
										Sexo,
										Validado,
										FechaAlta
									FROM Jugadores a
									where Estatus = 'A' and Validado = 1 and Jugador_tipo = 2 and Equipo_ID = $localid) a
					order by                         
						EdadGrupo asc, convert(Numero,unsigned) asc
					limit 1";
			$resultL = $Config->query($sqlL);
			//Lista Local
			$tmpy = 134;
			if ($resultL->num_rows > 0) {
				// output data of each row
				while($rowL = $resultL->fetch_assoc()) {
					$pdf->SetXY(5,$tmpy);
					$pdf->SetFont('Times' , 'B' , 9);
					$pdf->Cell(10, 4, $lang['110831'], 0, 1 , 'C' , false);
					$pdf->SetXY(15,$tmpy);
					$pdf->SetFont('Times' , '' , 9);
					$pdf->Cell(69, 4, utf8_decode('' . $rowL["Jugador"] . ''), 0, 1 , 'L' , false);
					$pdf->SetXY(84,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
					$pdf->SetXY(93,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
					$pdf->SetXY(102,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
					$pdf->SetXY(111,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
					$pdf->SetXY(120,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
						
					$tmpy = $tmpy + 4;
				}
			}
            
            /*Equipos y Jugadores lado IZquierdo del formato EMA 30.08.2023*/
         
           	/*1er. Equipo Visitante */
	        $pdf->SetFont('Helvetica' , 'B' , 11);
			$pdf->SetXY(5,140);
			$pdf->Cell(124, 5, $lang['10604']. ' ' . $lang['10588'] . ': ' . utf8_decode($row1["Visitante"]), 1, 1 , 'L' , false);
		  	
			$pdf->SetXY(5,145);
			$pdf->Cell(45, 24, ' ', 1, 1 , 'L' , false); 

			$pdf->SetXY(50,145);
			$pdf->Cell(79, 24, ' ', 1, 1 , 'L' , false); 

		  	 /*Tiempos fuera y Faltas de Equipo */
		  	$pdf->SetFont('Helvetica' , 'B' , 9);
			$pdf->SetTextColor(0, 0, 0);
	        $pdf->SetXY(5,145);
			$pdf->Cell(45, 4, $lang['110822'], 0, 1 , 'L' , false);
			
			/*Tiempos Fuera*/
		  	$pdf->SetFont('Helvetica' , '' , 7);
			$pdf->SetTextColor(0, 0, 0);
	        $pdf->SetXY(5,149);
			$pdf->Cell(15, 4, $lang['110824'], 0, 1 , 'L' , false);
			$pdf->SetXY(22,149);
			$pdf->Cell(4, 4, '', 1, 1 , 'L' , false);
			$pdf->SetXY(26,149);
			$pdf->Cell(4, 4, '', 1, 1 , 'L' , false);
			$pdf->SetXY(30,149);
			$pdf->Cell(4, 4, '', 1, 1 , 'L' , false);

	        $pdf->SetXY(5,154);
			$pdf->Cell(15, 4, $lang['110825'], 0, 1 , 'L' , false);
			$pdf->SetXY(22,154);
			$pdf->Cell(4, 4, '', 1, 1 , 'L' , false);
			$pdf->SetXY(26,154);
			$pdf->Cell(4, 4, '', 1, 1 , 'L' , false);
			$pdf->SetXY(30,154);
			$pdf->Cell(4, 4, '', 1, 1 , 'L' , false);

	        $pdf->SetXY(5,159);
			$pdf->Cell(15, 4, $lang['110826'], 0, 1 , 'L' , false);
			$pdf->SetXY(22,159);
			$pdf->Cell(4, 4, '', 1, 1 , 'L' , false);
			$pdf->SetXY(26,159);
			$pdf->Cell(4, 4, '', 1, 1 , 'L' , false);
			$pdf->SetXY(30,159);
			$pdf->Cell(4, 4, '', 1, 1 , 'L' , false);

	        $pdf->SetXY(5,164);
			$pdf->Cell(15, 4, $lang['110827'], 0, 1 , 'L' , false);
			$pdf->SetXY(22,164);
			$pdf->Cell(4, 4, '', 1, 1 , 'L' , false);
			$pdf->SetXY(26,164);
			$pdf->Cell(4, 4, '', 1, 1 , 'L' , false);
			$pdf->SetXY(30,164);
			$pdf->Cell(4, 4, '', 1, 1 , 'L' , false);
		  
		  
		   /*Faltas de Equipo*/
		  	$pdf->SetFont('Helvetica' , 'B' , 9);
			$pdf->SetTextColor(0, 0, 0);
	        $pdf->SetXY(50,145);
			$pdf->Cell(79, 4, $lang['110823'], 0, 1 , 'C' , false);

			$pdf->SetFont('Helvetica' , '' , 7);
			$pdf->SetTextColor(0, 0, 0);
	        $pdf->SetXY(52,149);
			$pdf->Cell(15, 4, $lang['110824'], 0, 1 , 'L' , false);
			$pdf->SetXY(69,149);
			$pdf->Cell(4, 4, '1', 1, 1 , 'C' , false);
			$pdf->SetXY(73,149);
			$pdf->Cell(4, 4, '2', 1, 1 , 'C' , false);
			$pdf->SetXY(77,149);
			$pdf->Cell(4, 4, '3', 1, 1 , 'C' , false);
			$pdf->SetXY(81,149);
			$pdf->Cell(4, 4, '4', 1, 1 , 'C' , false);
			
			$pdf->SetFont('Helvetica' , '' , 7);
			$pdf->SetTextColor(0, 0, 0);
	        $pdf->SetXY(90,149);
			$pdf->Cell(15, 4, $lang['110825'], 0, 1 , 'L' , false);
			$pdf->SetXY(107,149);
			$pdf->Cell(4, 4, '1', 1, 1 , 'C' , false);
			$pdf->SetXY(111,149);
			$pdf->Cell(4, 4, '2', 1, 1 , 'C' , false);
			$pdf->SetXY(115,149);
			$pdf->Cell(4, 4, '3', 1, 1 , 'C' , false);
			$pdf->SetXY(119,149);
			$pdf->Cell(4, 4, '4', 1, 1 , 'C' , false);
			
			$pdf->SetFont('Helvetica' , '' , 7);
			$pdf->SetTextColor(0, 0, 0);
	        $pdf->SetXY(52,153);
			$pdf->Cell(15, 4, $lang['110826'], 0, 1 , 'L' , false);
			$pdf->SetXY(69,153);
			$pdf->Cell(4, 4, '1', 1, 1 , 'C' , false);
			$pdf->SetXY(73,153);
			$pdf->Cell(4, 4, '2', 1, 1 , 'C' , false);
			$pdf->SetXY(77,153);
			$pdf->Cell(4, 4, '3', 1, 1 , 'C' , false);
			$pdf->SetXY(81,153);
			$pdf->Cell(4, 4, '4', 1, 1 , 'C' , false);
			
			$pdf->SetFont('Helvetica' , '' , 7);
			$pdf->SetTextColor(0, 0, 0);
	        $pdf->SetXY(90,153);
			$pdf->Cell(15, 4, $lang['110827'], 0, 1 , 'L' , false);
			$pdf->SetXY(107,153);
			$pdf->Cell(4, 4, '1', 1, 1 , 'C' , false);
			$pdf->SetXY(111,153);
			$pdf->Cell(4, 4, '2', 1, 1 , 'C' , false);
			$pdf->SetXY(115,153);
			$pdf->Cell(4, 4, '3', 1, 1 , 'C' , false);
			$pdf->SetXY(119,153);
			$pdf->Cell(4, 4, '4', 1, 1 , 'C' , false);
			
			$pdf->SetFont('Helvetica' , '' , 7);
			$pdf->SetTextColor(0, 0, 0);
	        $pdf->SetXY(52,161);
			$pdf->Cell(15, 4, $lang['110828'], 0, 1 , 'L' , false);
			$pdf->SetXY(77,161);
			$pdf->Cell(4, 4, '1', 1, 1 , 'C' , false);
			$pdf->SetXY(81,161);
			$pdf->Cell(4, 4, '2', 1, 1 , 'C' , false);
			$pdf->SetXY(85,161);
			$pdf->Cell(4, 4, '3', 1, 1 , 'C' , false);
			$pdf->SetXY(89,161);
			$pdf->Cell(4, 4, '4', 1, 1 , 'C' , false);
			
			$pdf->SetXY(97,161);
			$pdf->Cell(4, 4, '1', 1, 1 , 'C' , false);
			$pdf->SetXY(101,161);
			$pdf->Cell(4, 4, '2', 1, 1 , 'C' , false);
			$pdf->SetXY(105,161);
			$pdf->Cell(4, 4, '3', 1, 1 , 'C' , false);
			$pdf->SetXY(109,161);
			$pdf->Cell(4, 4, '4', 1, 1 , 'C' , false);
                 
			$pdf->SetFont('Helvetica' , 'B' , 9);
			$pdf->SetTextColor(0, 0, 0);

			$pdf->SetXY(5,169);
			$pdf->Cell(10, 6, $lang['10619'], 1, 1 , 'C' , false);
			$pdf->SetXY(15,169);
			$pdf->Cell(69, 6, $lang['10618'], 1, 1 , 'C' , false);
			$pdf->SetFont('Helvetica' , 'B' , 7);
			$pdf->SetXY(84,169);
			$pdf->Cell(45, 3, $lang['110829'], 1, 1 , 'C' , false);
			$pdf->SetXY(84,172);
			$pdf->Cell(9, 3, '1', 1, 1 , 'C' , false);
			$pdf->SetXY(93,172);
	    	$pdf->Cell(9, 3, '2', 1, 1 , 'C' , false);
			$pdf->SetXY(102,172);
	    	$pdf->Cell(9, 3, '3', 1, 1 , 'C' , false);
			$pdf->SetXY(111,172);
	    	$pdf->Cell(9, 3, '4', 1, 1 , 'C' , false);
			$pdf->SetXY(120,172);
	    	$pdf->Cell(9, 3, '5', 1, 1 , 'C' , false);

			$tmpy = 175;
			$ct = 0;
			while($ct < 17){
				$pdf->SetFont('Helvetica' , 'B' , 9);
				$pdf->SetTextColor(0, 0, 0);

				$pdf->SetXY(5,$tmpy);
				$pdf->Cell(10, 4, '', 1, 1 , 'C' , false);
				$pdf->SetXY(15,$tmpy);
				$pdf->Cell(69, 4, '', 1, 1 , 'L' , false);
				$pdf->SetXY(84,$tmpy);
				$pdf->Cell(9, 4, '', 1, 1 , 'C' , false);
				$pdf->SetXY(93,$tmpy);
				$pdf->Cell(9, 4, '', 1, 1 , 'C' , false);
				$pdf->SetXY(102,$tmpy);
				$pdf->Cell(9, 4, '', 1, 1 , 'C' , false);
				$pdf->SetXY(111,$tmpy);
				$pdf->Cell(9, 4, '', 1, 1 , 'C' , false);
				$pdf->SetXY(120,$tmpy);
				$pdf->Cell(9, 4, '', 1, 1 , 'C' , false);

				$tmpy = $tmpy + 4;
				$ct++;
			}

			$y = 175;
			
			$sqlL = "select * from (
									SELECT Jugador_ID,
										Clave,
										Nombre,
										Apellido_P,
										Apellido_M,
										Apodo,
										CONCAT(Apellido_P,' ',Nombre) AS Jugador,
										date_format(Fecha_Nacimiento,'%d/%m/%Y') Fecha_Nacimiento,
										YEAR(CURDATE())-YEAR(Fecha_Nacimiento) Edad,
										case when YEAR(CURDATE())-YEAR(Fecha_Nacimiento)  >= 50 then 1
											 when YEAR(CURDATE())-YEAR(Fecha_Nacimiento)  >= 35  and YEAR(CURDATE())-YEAR(Fecha_Nacimiento)  < 50 then 2
											 when YEAR(CURDATE())-YEAR(Fecha_Nacimiento)  < 35 then 3
										end EdadGrupo,
										Curp,
										Numero,
										Estatus,
										a.Equipo_ID,
										Comentarios,
										Telefono,
										correo,
										Sexo,
										Validado,
										FechaAlta
									FROM Jugadores a
									where Estatus = 'A' and Validado = 1 and Jugador_tipo = 0 and Equipo_ID = $visitanteid) a
					order by                         
						EdadGrupo asc, convert(Numero,unsigned) asc
					limit 15";
			$resultL = $Config->query($sqlL);
			//Lista Local
			$tmpy = 175;
			if ($resultL->num_rows > 0) {
				// output data of each row
				while($rowL = $resultL->fetch_assoc()) {
					$pdf->SetXY(5,$tmpy);
					$pdf->SetFont('Times' , 'B' , 9);
					$pdf->Cell(10, 4, utf8_decode('' . $rowL["Numero"] . ''), 0, 1 , 'C' , false);
					$pdf->SetXY(15,$tmpy);
					$pdf->SetFont('Times' , '' , 9);
					$pdf->Cell(69, 4, utf8_decode('' . $rowL["Jugador"] . ''), 0, 1 , 'L' , false);
					$pdf->SetXY(84,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
					$pdf->SetXY(93,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
					$pdf->SetXY(102,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
					$pdf->SetXY(111,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
					$pdf->SetXY(120,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
						
					$tmpy = $tmpy + 4;
				}
			}

			$sqlL = "select * from (
									SELECT Jugador_ID,
										Clave,
										Nombre,
										Apellido_P,
										Apellido_M,
										Apodo,
										CONCAT(Apellido_P,' ',Nombre) AS Jugador,
										date_format(Fecha_Nacimiento,'%d/%m/%Y') Fecha_Nacimiento,
										YEAR(CURDATE())-YEAR(Fecha_Nacimiento) Edad,
										case when YEAR(CURDATE())-YEAR(Fecha_Nacimiento)  >= 50 then 1
											 when YEAR(CURDATE())-YEAR(Fecha_Nacimiento)  >= 35  and YEAR(CURDATE())-YEAR(Fecha_Nacimiento)  < 50 then 2
											 when YEAR(CURDATE())-YEAR(Fecha_Nacimiento)  < 35 then 3
										end EdadGrupo,
										Curp,
										Numero,
										Estatus,
										a.Equipo_ID,
										Comentarios,
										Telefono,
										correo,
										Sexo,
										Validado,
										FechaAlta
									FROM Jugadores a
									where Estatus = 'A' and Validado = 1 and Jugador_tipo = 1 and Equipo_ID = $visitanteid) a
					order by                         
						EdadGrupo asc, convert(Numero,unsigned) asc
					limit 15";
			$resultL = $Config->query($sqlL);
			//Lista Local
			$tmpy = 235;
			if ($resultL->num_rows > 0) {
				// output data of each row
				while($rowL = $resultL->fetch_assoc()) {
					$pdf->SetXY(5,$tmpy);
					$pdf->SetFont('Times' , 'B' , 9);
					$pdf->Cell(10, 4, $lang['110830'], 0, 1 , 'C' , false);
					$pdf->SetXY(15,$tmpy);
					$pdf->SetFont('Times' , '' , 9);
					$pdf->Cell(69, 4, utf8_decode('' . $rowL["Jugador"] . ''), 0, 1 , 'L' , false);
					$pdf->SetXY(84,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
					$pdf->SetXY(93,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
					$pdf->SetXY(102,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
					$pdf->SetXY(111,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
					$pdf->SetXY(120,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
						
					$tmpy = $tmpy + 4;
				}
			}

			$sqlL = "select * from (
									SELECT Jugador_ID,
										Clave,
										Nombre,
										Apellido_P,
										Apellido_M,
										Apodo,
										CONCAT(Apellido_P,' ',Nombre) AS Jugador,
										date_format(Fecha_Nacimiento,'%d/%m/%Y') Fecha_Nacimiento,
										YEAR(CURDATE())-YEAR(Fecha_Nacimiento) Edad,
										case when YEAR(CURDATE())-YEAR(Fecha_Nacimiento)  >= 50 then 1
											 when YEAR(CURDATE())-YEAR(Fecha_Nacimiento)  >= 35  and YEAR(CURDATE())-YEAR(Fecha_Nacimiento)  < 50 then 2
											 when YEAR(CURDATE())-YEAR(Fecha_Nacimiento)  < 35 then 3
										end EdadGrupo,
										Curp,
										Numero,
										Estatus,
										a.Equipo_ID,
										Comentarios,
										Telefono,
										correo,
										Sexo,
										Validado,
										FechaAlta
									FROM Jugadores a
									where Estatus = 'A' and Validado = 1 and Jugador_tipo = 2 and Equipo_ID = $visitanteid) a
					order by                         
						EdadGrupo asc, convert(Numero,unsigned) asc
					limit 15";
			$resultL = $Config->query($sqlL);
			//Lista Local
			$tmpy = 239;
			if ($resultL->num_rows > 0) {
				// output data of each row
				while($rowL = $resultL->fetch_assoc()) {
					$pdf->SetXY(5,$tmpy);
					$pdf->SetFont('Times' , 'B' , 9);
					$pdf->Cell(10, 4, $lang['110831'], 0, 1 , 'C' , false);
					$pdf->SetXY(15,$tmpy);
					$pdf->SetFont('Times' , '' , 9);
					$pdf->Cell(69, 4, utf8_decode('' . $rowL["Jugador"] . ''), 0, 1 , 'L' , false);
					$pdf->SetXY(84,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
					$pdf->SetXY(93,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
					$pdf->SetXY(102,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
					$pdf->SetXY(111,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
					$pdf->SetXY(120,$tmpy);
					$pdf->Cell(9, 4, '', 0, 1 , 'C' , false);
						
					$tmpy = $tmpy + 4;
				}
			}
			
			$pdf->SetXY(5,245);
			$pdf->Cell(206, 28, ' ', 1, 1 , 'L' , false); 
			
			$pdf->SetXY(5,245);
			$pdf->SetFont('Times' , 'B' , 9);
			$pdf->Cell(35, 6, $lang['110832'], 1, 1 , 'C' , false); 
			
			$pdf->SetXY(40,245);
			$pdf->Cell(24, 6, $lang['110820'], 1, 1 , 'C' , false); 
			
			$pdf->SetXY(64,245);
			$pdf->Cell(19, 6, '', 1, 1 , 'C' , false);  
			
			$pdf->SetXY(83,245);
			$pdf->Cell(24, 6, $lang['110821'], 1, 1 , 'C' , false); 
			
			$pdf->SetXY(107,245);
			$pdf->Cell(18, 6, '', 1, 1 , 'C' , false); 
			
			$pdf->SetXY(125,245);
			$pdf->Cell(86, 6, $lang['110833'], 1, 1 , 'L' , false); 


			$pdf->SetFont('Times' , '' , 7);
			$pdf->SetXY(5,251);
			$pdf->Cell(206, 3, '________________________________________________________________________________________________________________________________________________', 0, 1 , 'R' , false); 

			$pdf->SetXY(5,251);
			$pdf->Cell(45, 3, $lang['110834'], 0, 1 , 'L' , false); 
			$pdf->SetXY(5,254);
			$pdf->Cell(206, 3, '_____________________________________________________________________________________________________________________________________________________________________', 0, 1 , 'C' , false); 
			$pdf->SetXY(5,257);
			$pdf->Cell(206, 3, '_____________________________________________________________________________________________________________________________________________________________________', 0, 1 , 'C' , false); 
			$pdf->SetXY(5,260);
			$pdf->Cell(206, 3, '_____________________________________________________________________________________________________________________________________________________________________', 0, 1 , 'C' , false); 
			
			$pdf->SetFont('Times' , '' , 9);
			$pdf->SetXY(5,268);
			$pdf->Cell(40, 5, $lang['110817'] . ' 1', 0, 1 , 'C' , false); 
			$pdf->SetXY(45,268);
			$pdf->Cell(40, 5, $lang['110817'] . ' 2', 0, 1 , 'C' , false); 
			$pdf->SetXY(85,268);
			$pdf->Cell(40, 5, $lang['110818'], 0, 1 , 'C' , false); 
			$pdf->SetXY(125,268);
			$pdf->Cell(40, 5, $lang['110835'] . ' ' . $lang['110820'], 0, 1 , 'C' , false); 
			$pdf->SetXY(165,268);
			$pdf->Cell(40, 5, $lang['110835'] . ' ' . $lang['110821'], 0, 1 , 'C' , false); 

		} 
	}	

	$Config->close();

	$pdf->Output();
?>
