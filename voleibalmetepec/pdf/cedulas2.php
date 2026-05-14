<?php
	require_once dirname(__DIR__) . '/site_paths.php';
   	set_time_limit(300);
	require("alphapdf.php");
	require("membersite_config.php");
	$Config = new Configuration();
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin($Config,'cedulas.php');
	$Config->connect();

	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	$folder = substr(substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT'])),1,strlen(substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT'])))-5);

	$torneo = $_COOKIE[$Config->getAlias() . 'season'];
	$categoria = $_COOKIE[$Config->getAlias() . 'category'];
	$jornada = htmlspecialchars($_GET['Jornada_ID']);
	
	$server = $fgmembersite->getSitename();

	$Config->LoadLogo();
	$Config->LoadFlags();
	
	$pdf = new FPDF('L','mm','Letter');
	
	$sql = "select dc.Categoria_DESC, b.Jornada_DescCorta, a.Juego_ID, a.Local_ID, d.Equipo_FULLDESC as Local, a.Visitante_ID, f.Equipo_FULLDESC as Visitante, b.Fecha, day(b.Fecha) Dia, 
					ELT(DATE_FORMAT(b.Fecha,'%m'),'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre')  Mes, year(b.Fecha) Anio, a.Campo_ID, 
					case when c.Campo_DESC is null then e.Campo_DESC else c.Campo_DESC end Campo_DESC, g.Torneo_Desc, DATE_FORMAT(b.Fecha, '%a, %d %b %Y') Fecha_String, DATE_FORMAT(a.Horario, '%I:%i %p') Horario
			from $schema.Juegos a
				join $schema.Jornada b on a.Fecha between b.Fecha_Inicio and b.Fecha_Fin
				left outer join $schema.Campos c on a.Campo_ID = c.Campo_ID
				join $schema.Equipos d on a.Torneo_ID = d.Torneo_ID and a.Local_ID = d.Equipo_ID 
				join $schema.Categorias dc on d.Fuerza = dc.Categoria_ID
				join $schema.Campos e on d.Campo_ID = e.Campo_ID
				join $schema.Equipos f on a.Torneo_ID = f.Torneo_ID and a.Visitante_ID = f.Equipo_ID 
				join $schema.Torneos g on a.Torneo_ID = g.Torneo_ID
			where a.Torneo_ID = $torneo and b.Jornada_ID = $jornada
			order by dc.Categoria_Orden asc, a.Juego_ID asc";
	$result1 = $Config->query($sql);
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
			/*
			$pdf->SetXY(0,0);
			$pdf->Image($server . '/imagenes/' . $Config->logo . '.png',5+((35 - (35 * ($Config->logowidth / 110)))/2),5+((35 - (35 * ($Config->logoheight / 110)))/2),(35 * ($Config->logowidth / 110)), (35 * ($Config->logoheight / 110)), 'PNG');
			*/
			$pdf->SetFont('Helvetica' , '' , 12);
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetXY(5,5);
			$pdf->Cell(110, 5, $Config->liga, 1, 1 , 'L' , false);
			
			$pdf->SetXY(115,5);
			$pdf->SetFont('Helvetica' , 'B' , 12);
			$pdf->SetTextColor(0, 0, 255);
			$pdf->Cell(60 , 5, utf8_decode($row1["Fecha_String"]) . '  ' . utf8_decode($row1["Horario"]) , 1, 1 , 'L' , false);
             /*Torneo y Categoria */
           	$pdf->SetFont('Helvetica' , '' , 12);
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetXY(5,10);
			$pdf->Cell(60, 5, $row1["Torneo_Desc"], 1, 1 , 'L' , false);			
			$pdf->SetXY(65,10);
			$pdf->Cell(50, 5, $row1["Categoria_DESC"], 1, 1 , 'L' , false);
		   
		    /*Titulo Equipos */
		    $pdf->SetXY(115,10);
            $pdf->SetFont('Helvetica' , '' , 7);
            $pdf->SetTextColor(0, 0, 0);
	    	$pdf->Cell(60, 3, 'EQUIPOS' , 35, 0 , 'C' , false);
            $pdf->SetFont('Helvetica' , 'B' , 7);
		    $pdf->SetXY(115,10);
	    	$pdf->Cell(30, 3, 'A' , 35, 0 , 'C' , false);
		    $pdf->SetXY(145,10);
	    	$pdf->Cell(30, 3, 'B' , 35, 0 , 'C' , false);
		    
		    /*Equipos */
		    $pdf->SetXY(115,10);
            $pdf->SetFont('Helvetica' , '' , 13);
            $pdf->SetTextColor(0, 0, 255);
	    	$pdf->Cell(60, 11, '' , 1, 1 , 'C' , false);
	    	$pdf->SetXY(115,13);
	    	$pdf->MultiCell(30, 4, utf8_decode($row1["Local"]), 0 , 'C' , false);
	    	$pdf->SetXY(145,13);
	    	$pdf->MultiCell(30, 4, utf8_decode($row1["Visitante"]), 0 , 'C' , false);
		
		   /*Cancha y Juego */
           	$pdf->SetFont('Helvetica' , 'B' , 11);
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetXY(5,15);
			$pdf->Cell(60, 6, $lang['10517'] . ' ' . $row1["Campo_DESC"], 1, 1 , 'L' , false);			
			$pdf->SetXY(65,15);
			/*$pdf->Cell(50, 6, 'Juego: '.$row1["Juego_ID"], 1, 1 , 'C' , false);*/
			$pdf->Cell(50, 6,  $lang['986'] . ' ' . utf8_decode($row1["Jornada_DescCorta"]) . '', 1, 0 , 'C' , false);
			
			/*Empieza Cabecera antes de los Set's*/
			$pdf->SetFont('Times' , 'B' , 8);
		    $pdf->SetXY(5,21);
		    $pdf->Cell(11 , 6,'E: ', 1, 1 , 'L' , false);
		    
		    $pdf->SetFont('Times' , 'B' , 6);
		    $pdf->SetXY(16,19);
		    $pdf->Cell(11 , 6, $lang['9999'], 0, 0 , 'L' , false);
		    $pdf->SetXY(16,21);
		    $pdf->Cell(11 , 6, $lang['10136'], 1, 1 , 'L' , false);
		    
		    /*Colocacion del equipo dentro de la cancha 1er set*/
		    $pdf->SetFont('Times' , 'B' , 9);
		    $pdf->SetXY(27,21);
		    $pdf->Cell(38 , 6, 'A) ' . utf8_decode($row1["Local"]), 1, 1 , 'L' , false);
		    
		     /*Dato de R=Recibe, S= Al servicio Visitante 1er set*/
		    $pdf->SetXY(65,21);
            $pdf->SetFont('Helvetica' , 'B' , 6);
            $pdf->SetTextColor(0, 0, 0);
	    	$pdf->Cell(3, 3, 'S' , 1, 0 , 'L' , false);
	    	$pdf->SetXY(65,24);
            $pdf->SetFont('Helvetica' , 'B' , 6);
            $pdf->SetTextColor(0, 0, 0);
	    	$pdf->Cell(3, 3, 'R' , 1, 0 , 'L' , false);
	    
	    	/*Titulo Puntos Local 1er set */
	    	$pdf->SetXY(68,21);
            $pdf->SetFont('Helvetica' , 'B' , 8);
            $pdf->SetTextColor(0, 0, 0);
	    	$pdf->Cell(16, 6, $lang['370'] , 1, 0 , 'C' , false);
		
		/*
			$pdf->Cell(40 , 5, mb_strtoupper(utf8_decode('' . utf8_decode($row1["Local"]) . '')), 1, 1 , 'C' , false);
			$pdf->SetFont('Times' , '' , 10);
			$y = $y + 4;
			$pdf->SetXY($x+5,$y+47);
			$pdf->Cell(16 , 4, $lang['991'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+26,$y+47);
			$pdf->Cell(8 , 4, $lang['992'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+34,$y+47);
			$pdf->Cell(72, 4, $lang['993'], 1, 0, 'L' , false);
			$pdf->SetXY($x+106,$y+47);
			$pdf->Cell(8, 4, $lang['994'], 1, 0, 'L' , false);
			$pdf->SetXY($x+114,$y+47);
			$pdf->Cell(8, 4, $lang['995'], 1, 0, 'L' , false);
			$pdf->SetXY($x+122,$y+47);
			$pdf->Cell(8, 4, $lang['996'], 1, 0, 'L' , false);
			$pdf->SetXY($x+130,$y+47);
			$pdf->Cell(8, 4, $lang['995'], 1, 0, 'L' , false);
			$y= $y+4;
			*/
			
			
			
			/*Termina cabecera*/
			
		/*	
			$pdf->SetXY(248,5);
			$pdf->SetFont('Helvetica' , '' , 10);
			$pdf->Cell(90 , 3, $lang['986'] . ' ' . utf8_decode($row1["Jornada_DescCorta"]) . '', 35, 0 , 'L' , false);
			$pdf->SetXY(40,35);
			$pdf->Cell(90 , 8, $lang['987'], 35, 0 , 'L' , false);
			$pdf->SetXY(120,35);
			$pdf->Cell(90 , 8, '2nd ' . $lang['987'], 35, 0 , 'L' , false);
			$pdf->SetXY(195,35);
			$pdf->Cell(90 , 8, '3rd ' . $lang['987'], 35, 0 , 'L' , false);
			$pdf->SetXY(65,19);
			$pdf->Cell(90 , 8, '________________________', 35, 0 , 'L' , false);
			$pdf->SetXY(140,19);
			$pdf->Cell(90 , 8, '________________________', 35, 0 , 'L' , false);
			$pdf->SetXY(220,19);
			$pdf->Cell(90 , 8, '________________________', 35, 0 , 'L' , false);
			$pdf->SetXY(65,26);
			$pdf->Cell(90 , 8, '________________________', 35, 0 , 'L' , false);
			$pdf->SetXY(140,26);
			$pdf->Cell(90 , 8, '________________________', 35, 0 , 'L' , false);
			$pdf->SetXY(220,26);
			$pdf->Cell(90 , 8, '________________________', 35, 0 , 'L' , false);
			$pdf->SetXY(40,32);
			*/
			
			$pdf->SetXY(84,21);
            $pdf->SetFont('Times' , 'B' , 9);
			$pdf->Cell(38, 6, 'B) ' . utf8_decode($row1["Visitante"]), 1, 0 , 'L' , false);
		 
		    /*Dato de R=Recibe, S= Al servicio Visitante 1er set*/
		    $pdf->SetXY(119,21);
            $pdf->SetFont('Helvetica' , 'B' , 6);
            $pdf->SetTextColor(0, 0, 0);
	    	$pdf->Cell(3, 3, 'S' , 1, 0 , 'L' , false);
	    	$pdf->SetXY(119,24);
            $pdf->SetFont('Helvetica' , 'B' , 6);
            $pdf->SetTextColor(0, 0, 0);
	    	$pdf->Cell(3, 3, 'R' , 1, 0 , 'L' , false);
	    	
	     /*Titulo Termina */
	       $pdf->SetFont('Times' , 'B' , 6);
		    $pdf->SetXY(122,19);
		    $pdf->Cell(10 , 6, $lang['989'], 0, 0 , 'L' , false);
		    $pdf->SetXY(122,21);
		    $pdf->Cell(10 , 6, $lang['10136'], 1, 1 , 'L' , false);
	    
	    
	    	/*Titulo Puntos Visitante 1er set*/
	    	$pdf->SetXY(132,21);
            $pdf->SetFont('Helvetica' , 'B' , 8);
            $pdf->SetTextColor(0, 0, 0);
	    	$pdf->Cell(15, 6, $lang['370'] , 1, 0 , 'C' , false);
	    	
	    	/*Titulo Inicia 2do. Set*/
	    	 $pdf->SetFont('Times' , 'B' , 6);
		    $pdf->SetXY(147,19);
		    $pdf->Cell(11 , 6, $lang['9999'], 0, 0 , 'L' , false);
		    $pdf->SetXY(147,21);
		    $pdf->Cell(11 , 6, $lang['10136'], 1, 1 , 'L' , false);
	    	
	    	
	        /*Datos de acomodo para el 2do. Set*/
	        $pdf->SetXY(158,21);
            $pdf->SetFont('Times' , 'B' , 8);
			$pdf->Cell(35 , 6, 'B) ' . utf8_decode($row1["Visitante"]), 1, 0 , 'L' , false);
			
		 
		    /*Dato de R=Recibe, S= Al servicio Visitante 2do set*/
		    $pdf->SetXY(193,21);
            $pdf->SetFont('Helvetica' , 'B' , 6);
            $pdf->SetTextColor(0, 0, 0);
	    	$pdf->Cell(3, 3, 'S' , 1, 0 , 'L' , false);
	    	$pdf->SetXY(193,24);
            $pdf->SetFont('Helvetica' , 'B' , 6);
            $pdf->SetTextColor(0, 0, 0);
	    	$pdf->Cell(3, 3, 'R' , 1, 0 , 'L' , false);
	    	
	    
	    	/*Titulo Puntos Visitante 2dr set*/
	    	$pdf->SetXY(196,21);
            $pdf->SetFont('Helvetica' , 'B' , 8);
            $pdf->SetTextColor(0, 0, 0);
	    	$pdf->Cell(15, 6, $lang['370'] , 1, 0 , 'C' , false);
	    	
	     /*Colocacion del equipo dentro de la cancha 2do set*/
		    $pdf->SetFont('Times' , 'B' , 9);
		    $pdf->SetXY(211,21);
		    $pdf->Cell(35 , 6, 'A) ' . utf8_decode($row1["Local"]), 1, 1 , 'L' , false);
		    
	     /*Dato de R=Recibe, S= Al servicio Local 2do set*/
		    $pdf->SetXY(246,21);
            $pdf->SetFont('Helvetica' , 'B' , 6);
            $pdf->SetTextColor(0, 0, 0);
	    	$pdf->Cell(3, 3, 'S' , 1, 0 , 'L' , false);
	    	$pdf->SetXY(246,24);
            $pdf->SetFont('Helvetica' , 'B' , 6);
            $pdf->SetTextColor(0, 0, 0);
	    	$pdf->Cell(3, 3, 'R' , 1, 0 , 'L' , false);
	    	
	    	
	    	 /*Titulo Termina */
	        $pdf->SetFont('Times' , 'B' , 6);
		    $pdf->SetXY(249,19);
		    $pdf->Cell(9.5 , 6, $lang['989'], 0, 0 , 'L' , false);
		    $pdf->SetXY(249,21);
		    $pdf->Cell(9.5 , 6, $lang['10136'], 1, 1 , 'L' , false);
		     
		     /*Titulo Puntos Visitante 2do set*/
	    	$pdf->SetXY(258.5,21);
            $pdf->SetFont('Helvetica' , 'B' , 8);
            $pdf->SetTextColor(0, 0, 0);
	    	$pdf->Cell(15, 6, $lang['370'] , 1, 0 , 'C' , false);
	    	
	    	
/*	    	 for($x=0;$x <2; $x++)
    {
        	$y = $y + 4;
        	
			$pdf->SetXY($x+5,$y+35);
			$pdf->Cell(10 , 6, 'I', 1, 1 , 'C' , false);
			$pdf->SetXY($x+8,$y+60);
			$pdf->Cell(8 , 6, 'II', 1, 1 , 'C' , false);
			$pdf->SetXY($x+34,$y+47);
			$pdf->Cell(8, 6, 'III', 1, 0, 'L' , false);
			$pdf->SetXY($x+106,$y+47);
			$pdf->Cell(8, 6, 'IV', 1, 0, 'L' , false);
    }
    // Línea de cierre
    */
    
  
/*			$pdf->SetXY(118,30);
			$pdf->SetFont('Helvetica' , 'B' , 10);
			$pdf->Cell(90 , 8, $lang['0'] . ': "' . utf8_decode($row1["Torneo_Desc"]) . '"', 35, 0 , 'L' , false);
			$pdf->SetXY(118,34);
			$pdf->SetFont('Helvetica' , 'B' , 10);
			$pdf->Cell(90 , 8, $lang['1'] . ': "' . utf8_decode($row1["Categoria_DESC"]) . '"', 35, 0 , 'L' , false);
			$pdf->SetFont('Helvetica' , '' , 10);
			$pdf->SetXY(195, 32);
			$pdf->Cell(90 , 8, $lang['990'] . '  ________ ' . $lang['989'] . ' _______', 35, 0 , 'L' , false);*/
			
			$sql = "select * from (
                    				SELECT Jugador_ID,
                    					Clave,
                    					Nombre,
                    					Apellido_P,
                    					Apellido_M,
                    					Apodo,
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
                    				where Estatus = 'A' and Validado = 1 and Equipo_ID = $localid
                    				union
                    				SELECT Jugador_ID,
                    					Clave,
                    					Nombre,
                    					Apellido_P,
                    					Apellido_M,
                    					Apodo,
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
        								case when Sexo = 0 then '" . $lang["942"] . "'
        									when Sexo = 1 then '" . $lang["943"] . "'
        									end SexoT,
        								Sexo
                                        Validado,
                                        FechaAlta
                    				FROM Jugadores a
                    				where Estatus = 'A' and Validado = 0 and Equipo_ID = $localid and FechaAlta >= DATE_ADD(CURDATE(), interval -15 day)) a
                    order by                         
                    	EdadGrupo asc, convert(Numero,unsigned) asc
                    limit 35";
			$result = $Config->query($sql);
			$pages = 0;
			
			$y = $y -5;
		/*	$pdf->SetFont('Times' , 'B' , 9);
			$pdf->SetXY($x+25,$y+34.5);
			$pdf->Cell(128 , 5, mb_strtoupper(utf8_decode('' . utf8_decode($row1["Local"]) . '')), 1, 1 , 'C' , false);*/
			
				/*Empieza Cabecera antes de los Set's*/
		
		    
			$pdf->SetFont('Times' , 'B' , 7);
			$y = $y + 4;
			$pdf->SetXY($x+5,$y+28);
			$pdf->Cell(11 , 5,$lang['10500'], 1, 1 , 'C' , false);
			$pdf->TextWithDirection(110,50,'world!','U');
			$lang['10507'];
			$pdf->SetXY($x+16,$y+28);
			$pdf->Cell(4 , 45, 'SSSS1' , 1, 1 , 'C' , false);
			$pdf->SetXY($x+20,$y+28);
			$pdf->Cell(8, 5, $lang['10501'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+28,$y+28);
			$pdf->Cell(8, 5, $lang['10502'], 1, 0, 'C' , false);
			$pdf->SetXY($x+36,$y+28);
			$pdf->Cell(8, 5, $lang['10503'], 1, 0, 'C' , false);
			$pdf->SetXY($x+44,$y+28);
			$pdf->Cell(8, 5, $lang['10504'], 1, 0, 'C' , false);
			$pdf->SetXY($x+52,$y+28);
			$pdf->Cell(8, 5, $lang['10505'], 1, 0, 'C' , false);
			$pdf->SetXY($x+60,$y+28);
			$pdf->Cell(8, 5, $lang['10506'], 1, 0, 'C' , false);
			//$pdf->SetXY($x+62,$y+28);
			//$pdf->Cell(7, 5, $lang['10506'], 1, 0, 'C' , false);
		  

			
		    /*1ra fila 1set Local*/
		    
		    $pdf->SetFont('Times' , 'B' , 6);
			$pdf->SetXY($x+5,$y+33);
			$pdf->Cell(11 , 5,$lang['10508'], 1, 1 , 'C' , false);
		/*	$pdf->SetXY($x+22,$y+30.5);
			$pdf->Cell(3 , 32, $lang['10507'], 1, 1 , 'C' , false);*/
			$pdf->SetXY($x+20,$y+33);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+28,$y+33);
			$pdf->Cell(8, 5, $lang['10136'], 1, 0, 'C' , false);
			$pdf->SetXY($x+36,$y+33);
			$pdf->Cell(8, 5, $lang['10136'], 1, 0, 'C' , false);
			$pdf->SetXY($x+44,$y+33);
			$pdf->Cell(8, 5, $lang['10136'], 1, 0, 'C' , false);
			$pdf->SetXY($x+52,$y+33);
			$pdf->Cell(8, 5, $lang['10136'], 1, 0, 'C' , false);
			$pdf->SetXY($x+60,$y+33);
			$pdf->Cell(8, 5, $lang['10136'], 1, 0, 'C' , false);
		//	$pdf->SetXY($x+62,$y+33);
		//	$pdf->Cell(8, 5, $lang['10136'], 1, 0, 'C' , false);
			
			/*2da fila 1set local*/
		
		   $pdf->SetFont('Times' , 'B' , 6);
			$pdf->SetXY($x+5,$y+38);
			$pdf->Cell(11 , 5,$lang['637'], 1, 1 , 'C' , false);
		/*	$pdf->SetXY($x+22,$y+30.5);
			$pdf->Cell(3 , 32, $lang['10507'], 1, 1 , 'C' , false);*/
			$pdf->SetFont('Times' , 'B' , 7);
			$pdf->SetXY($x+20,$y+38);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+28,$y+38);
			$pdf->Cell(8, 5, $lang['10136'], 1, 0, 'C' , false);
			$pdf->SetXY($x+36,$y+38);
			$pdf->Cell(8, 5, $lang['10136'], 1, 0, 'C' , false);
			$pdf->SetXY($x+44,$y+38);
			$pdf->Cell(8, 5, $lang['10136'], 1, 0, 'C' , false);
			$pdf->SetXY($x+52,$y+38);
			$pdf->Cell(8, 5, $lang['10136'], 1, 0, 'C' , false);
			$pdf->SetXY($x+60,$y+38);
			$pdf->Cell(8, 5, $lang['10136'], 1, 0, 'C' , false);
			//$pdf->SetXY($x+62,$y+38);
			//$pdf->Cell(8, 5, $lang['10136'], 1, 0, 'C' , false);
		
			/*3ra fila 1set cambio de jugador Local*/
		
		   $pdf->SetFont('Times' , 'B' , 6);
			$pdf->SetXY($x+5,$y+43);
			$pdf->Cell(11 , 10,$lang['10508'], 1, 1 , 'C' , false);
		/*	$pdf->SetXY($x+22,$y+30.5);
			$pdf->Cell(3 , 32, $lang['10507'], 1, 1 , 'C' , false);*/
			$pdf->SetFont('Times' , 'B' , 7);
			$pdf->SetXY($x+20,$y+43);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+28,$y+43);
			$pdf->Cell(8, 5, $lang['10137'], 1, 0, 'C' , false);
			$pdf->SetXY($x+36,$y+43);
			$pdf->Cell(8, 5, $lang['10137'], 1, 0, 'C' , false);
			$pdf->SetXY($x+44,$y+43);
		    $pdf->Cell(8, 5, $lang['10137'], 1, 0, 'C' , false);
			$pdf->SetXY($x+52,$y+43);
			$pdf->Cell(8, 5, $lang['10137'], 1, 0, 'C' , false);
			$pdf->SetXY($x+60,$y+43);
			$pdf->Cell(8, 5, $lang['10137'], 1, 0, 'C' , false);
		//	$pdf->SetXY($x+62,$y+43);
		//	$pdf->Cell(7, 5, $lang['10137'], 1, 0, 'C' , false);
			
			/*4a fila 1set cambio de jugador Local*/
		
		   $pdf->SetFont('Times' , 'B' , 6);
		/*	$pdf->SetXY($x+5,$y+39.5);
			$pdf->Cell(14 , 10,$lang['10508'], 1, 1 , 'C' , false);*/
		/*	$pdf->SetXY($x+22,$y+30.5);
			$pdf->Cell(3 , 32, $lang['10507'], 1, 1 , 'C' , false);*/
			$pdf->SetFont('Times' , 'B' , 7);
			$pdf->SetXY($x+20,$y+48);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+28,$y+48);
			$pdf->Cell(8, 5, $lang['10137'], 1, 0, 'C' , false);
			$pdf->SetXY($x+36,$y+48);
			$pdf->Cell(8, 5, $lang['10137'], 1, 0, 'C' , false);
			$pdf->SetXY($x+44,$y+48);
			$pdf->Cell(8, 5, $lang['10137'], 1, 0, 'C' , false);
			$pdf->SetXY($x+52,$y+48);
			$pdf->Cell(8, 5, $lang['10137'], 1, 0, 'C' , false);
			$pdf->SetXY($x+60,$y+48);
			$pdf->Cell(8, 5, $lang['10137'], 1, 0, 'C' , false);
		//	$pdf->SetXY($x+62,$y+48);
		//	$pdf->Cell(8, 5, $lang['10137'], 1, 0, 'C' , false);
			
			/*5a fila 1set cambio de jugador*/
		
		   $pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+5,$y+53);
			$pdf->Cell(5.5 , 5,$lang['10509'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+10.5,$y+53);
			$pdf->Cell(5.5 , 5,$lang['10513'], 1, 1 , 'C' , false);
			
		/*	$pdf->SetXY($x+22,$y+30.5);
			$pdf->Cell(3 , 32, $lang['10507'], 1, 1 , 'C' , false);*/
			$pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+20.7,$y+51.5);
			$pdf->Cell(4 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+20,$y+53);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+24.7,$y+51.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+24,$y+53);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
		
			$pdf->SetXY($x+28.7,$y+51.5);
			$pdf->Cell(4 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+28,$y+53);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+32.7,$y+51.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+32,$y+53);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+36.7,$y+51.5);
			$pdf->Cell(4 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+36,$y+53);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+40.7,$y+51.5);
		    $pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
		    $pdf->SetXY($x+40,$y+53);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+44.7,$y+51.5);
			$pdf->Cell(4 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+44,$y+53);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+48.7,$y+51.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+48,$y+53);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
		
			$pdf->SetXY($x+52.7,$y+51.5);
			$pdf->Cell(4 , 5, $lang['10509'], 0, 0, 'R' , false);
            $pdf->SetXY($x+52,$y+53);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+56.7,$y+51.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+56,$y+53);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
		
			$pdf->SetXY($x+60.7,$y+51.5);
			$pdf->Cell(4 , 5, $lang['10509'], 0, 0 , 'R' , false);
            $pdf->SetXY($x+60,$y+53);
			$pdf->Cell(4, 5, $lang['10136'], 1, 1 , 'R' , false);

			$pdf->SetXY($x+64.7,$y+51.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
            $pdf->SetXY($x+64,$y+53);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
/*
			
			$pdf->SetXY($x+63,$y+51.5);
			$pdf->Cell(3.5 , 5, $lang['10509'], 0, 0 , 'R' , false);
            $pdf->SetXY($x+62,$y+53);
			$pdf->Cell(3.5 , 5, $lang['10136'], 1, 1 , 'R' , false);

            $pdf->SetXY($x+66.5,$y+51.5);
			$pdf->Cell(3.5, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+65.5,$y+53);
			$pdf->Cell(3.5, 5, $lang['10136'], 1, 0, 'R' , false);  */
			
			
          /*6a fila 1set cambio de jugador*/

		   $pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+5,$y+58);
			$pdf->Cell(5.5 , 5,$lang['10510'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+10.5,$y+58);
			$pdf->Cell(5.5 , 5,$lang['10514'], 1, 1 , 'C' , false);
			
		$pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+20.7,$y+56.5);
			$pdf->Cell(4 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+20,$y+58);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+24.7,$y+56.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+24,$y+58);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
		
			$pdf->SetXY($x+28.7,$y+56.5);
			$pdf->Cell(4 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+28,$y+58);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+32.7,$y+56.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+32,$y+58);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+36.7,$y+56.5);
			$pdf->Cell(4 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+36,$y+58);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+40.7,$y+56.5);
		    $pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
		    $pdf->SetXY($x+40,$y+58);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+44.7,$y+56.5);
			$pdf->Cell(4 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+44,$y+58);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+48.7,$y+56.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+48,$y+58);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
		
			$pdf->SetXY($x+52.7,$y+56.5);
			$pdf->Cell(4 , 5, $lang['10510'], 0, 0, 'R' , false);
            $pdf->SetXY($x+52,$y+58);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+56.7,$y+56.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+56,$y+58);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
		
			$pdf->SetXY($x+60.7,$y+56.5);
			$pdf->Cell(4 , 5, $lang['10510'], 0, 0 , 'R' , false);
            $pdf->SetXY($x+60,$y+58);
			$pdf->Cell(4, 5, $lang['10136'], 1, 1 , 'R' , false);

			$pdf->SetXY($x+64.7,$y+56.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
            $pdf->SetXY($x+64,$y+58);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);

			
			
                /*7a fila 1set cambio de jugador 1er. set*/

		    $pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+5,$y+63);
			$pdf->Cell(5.5 , 5,$lang['10511'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+10.5,$y+63);
			$pdf->Cell(5.5 , 5,$lang['10515'], 1, 1 , 'C' , false);
			
			$pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+20.7,$y+61.5);
			$pdf->Cell(4 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+20,$y+63);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+24.7,$y+61.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+24,$y+63);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
		
			$pdf->SetXY($x+28.7,$y+61.5);
			$pdf->Cell(4 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+28,$y+63);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+32.7,$y+61.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+32,$y+63);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+36.7,$y+61.5);
			$pdf->Cell(4 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+36,$y+63);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+40.7,$y+61.5);
		    $pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
		    $pdf->SetXY($x+40,$y+63);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+44.7,$y+61.5);
			$pdf->Cell(4 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+44,$y+63);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+48.7,$y+61.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+48,$y+63);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
		
			$pdf->SetXY($x+52.7,$y+61.5);
			$pdf->Cell(4 , 5, $lang['10511'], 0, 0, 'R' , false);
            $pdf->SetXY($x+52,$y+63);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+56.7,$y+61.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+56,$y+63);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
		
			$pdf->SetXY($x+60.7,$y+61.5);
			$pdf->Cell(4 , 5, $lang['10511'], 0, 0 , 'R' , false);
            $pdf->SetXY($x+60,$y+63);
			$pdf->Cell(4, 5, $lang['10136'], 1, 1 , 'R' , false);

			$pdf->SetXY($x+64.7,$y+61.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
            $pdf->SetXY($x+64,$y+63);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);

		     /*8a fila 1set cambio de jugador 1er. set*/

		   $pdf->SetFont('Times' , 'B' , 6);
			$pdf->SetXY($x+5,$y+68);
			$pdf->Cell(5.5 , 5,$lang['10512'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+10.5,$y+68);
			$pdf->Cell(5.5 , 5,$lang['10516'], 1, 1 , 'C' , false);
		/*	$pdf->SetXY($x+22,$y+30.5);
			$pdf->Cell(3 , 32, $lang['10507'], 1, 1 , 'C' , false);*/
		 	$pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+20.7,$y+66.5);
			$pdf->Cell(4 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+20,$y+68);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+24.7,$y+66.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+24,$y+68);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
		
			$pdf->SetXY($x+28.7,$y+66.5);
			$pdf->Cell(4 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+28,$y+68);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+32.7,$y+66.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+32,$y+68);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+36.7,$y+66.5);
			$pdf->Cell(4 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+36,$y+68);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+40.7,$y+66.5);
		    $pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
		    $pdf->SetXY($x+40,$y+68);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+44.7,$y+66.5);
			$pdf->Cell(4 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+44,$y+68);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+48.7,$y+66.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+48,$y+68);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
		
			$pdf->SetXY($x+52.7,$y+66.5);
			$pdf->Cell(4 , 5, $lang['10512'], 0, 0, 'R' , false);
            $pdf->SetXY($x+52,$y+68);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+56.7,$y+66.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+56,$y+68);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
		
			$pdf->SetXY($x+60.7,$y+66.5);
			$pdf->Cell(4 , 5, $lang['10512'], 0, 0 , 'R' , false);
            $pdf->SetXY($x+60,$y+68);
			$pdf->Cell(4, 5, $lang['10136'], 1, 1 , 'R' , false);

			$pdf->SetXY($x+64.7,$y+66.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
            $pdf->SetXY($x+64,$y+68);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);

			
		
		  /* Puntos Equipo Local 1er. set*/
		  
		    $pdf->SetFont('Times' , '' , 6);
           $pdf->SetXY(68,27);
			$pdf->Cell(4 , 3.3,$lang['10509'], 1, 0 , 'C' , false);
			$pdf->SetXY(72,27);
			$pdf->Cell(4 , 3.3,$lang['10521'], 1, 0 , 'C' , false);
			$pdf->SetXY(76,27);
			$pdf->Cell(4 , 3.3,$lang['10532'], 1, 0 , 'C' , false);
			$pdf->SetXY(80,27);
			$pdf->Cell(4 , 3.3,$lang['10543'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(68,30.4);
			$pdf->Cell(4 , 3.3,$lang['10510'], 1, 0 , 'C' , false);
			$pdf->SetXY(72,30.4);
			$pdf->Cell(4 , 3.3,$lang['10522'], 1, 0 , 'C' , false);
			$pdf->SetXY(76,30.4);
			$pdf->Cell(4 , 3.3,$lang['10533'], 1, 0 , 'C' , false);
			$pdf->SetXY(80,30.4);
			$pdf->Cell(4 , 3.3,$lang['10544'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(68,33.7);
			$pdf->Cell(4 , 3.3,$lang['10511'], 1, 0 , 'C' , false);
			$pdf->SetXY(72,33.7);
			$pdf->Cell(4 , 3.3,$lang['10523'], 1, 0 , 'C' , false);
			$pdf->SetXY(76,33.7);
			$pdf->Cell(4 , 3.3,$lang['10534'], 1, 0 , 'C' , false);
			$pdf->SetXY(80,33.7);
			$pdf->Cell(4 , 3.3,$lang['10545'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(68,37.1);
			$pdf->Cell(4 , 3.3,$lang['10512'], 1, 0 , 'C' , false);
			$pdf->SetXY(72,37.1);
			$pdf->Cell(4 , 3.3,$lang['10524'], 1, 0 , 'C' , false);
			$pdf->SetXY(76,37.1);
			$pdf->Cell(4 , 3.3,$lang['10535'], 1, 0 , 'C' , false);
			$pdf->SetXY(80,37.1);
			$pdf->Cell(4 , 3.3,$lang['10546'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(68,40.3);
			$pdf->Cell(4 , 3.3,$lang['10513'], 1, 0 , 'C' , false);
			$pdf->SetXY(72,40.3);
			$pdf->Cell(4 , 3.3,$lang['10525'], 1, 0 , 'C' , false);
			$pdf->SetXY(76,40.3);
			$pdf->Cell(4 , 3.3,$lang['10536'], 1, 0 , 'C' , false);
			$pdf->SetXY(80,40.3);
			$pdf->Cell(4 , 3.3,$lang['10547'], 1, 0 , 'C' , false);
			
            $pdf->SetXY(68,43.5);
			$pdf->Cell(4 , 3.3,$lang['10514'], 1, 0 , 'C' , false);
			$pdf->SetXY(72,43.5);
			$pdf->Cell(4 , 3.3,$lang['10526'], 1, 0 , 'C' , false);
			$pdf->SetXY(76,43.5);
			$pdf->Cell(4 , 3.3,$lang['10537'], 1, 0 , 'C' , false);
			$pdf->SetXY(80,43.5);
			$pdf->Cell(4 , 3.3,$lang['10548'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(68,46.9);
			$pdf->Cell(4 , 3.3,$lang['10515'], 1, 0 , 'C' , false);
			$pdf->SetXY(72,46.9);
			$pdf->Cell(4 , 3.3,$lang['10527'], 1, 0 , 'C' , false);
			$pdf->SetXY(76,46.9);
			$pdf->Cell(4 , 3.3,$lang['10538'], 1, 0 , 'C' , false);
			$pdf->SetXY(80,46.9);
			$pdf->Cell(4 , 3.3,$lang['10549'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(68,50.1);
			$pdf->Cell(4 , 3.3,$lang['10516'], 1, 0 , 'C' , false);
			$pdf->SetXY(72,50.1);
			$pdf->Cell(4 , 3.3,$lang['10528'], 1, 0 , 'C' , false);
			$pdf->SetXY(76,50.1);
			$pdf->Cell(4 , 3.3,$lang['10539'], 1, 0 , 'C' , false);
			$pdf->SetXY(80,50.1);
			$pdf->Cell(4 , 3.3,$lang['10550'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(68,53.4);
			$pdf->Cell(4 , 3.3,$lang['10518'], 1, 0 , 'C' , false);
			$pdf->SetXY(72,53.4);
			$pdf->Cell(4 , 3.3,$lang['10529'], 1, 0 , 'C' , false);
			$pdf->SetXY(76,53.4);
			$pdf->Cell(4 , 3.3,$lang['10540'], 1, 0 , 'C' , false);
			$pdf->SetXY(80,53.4);
			$pdf->Cell(4 , 3.3,$lang['10551'], 1, 0 , 'C' , false);
			
	    	$pdf->SetXY(68,56.6);
			$pdf->Cell(4 , 3.2,$lang['10519'], 1, 0 , 'C' , false);
			$pdf->SetXY(72,56.6);
			$pdf->Cell(4 , 3.2,$lang['10530'], 1, 0 , 'C' , false);
			$pdf->SetXY(76,56.6);
			$pdf->Cell(4 , 3.2,$lang['10541'], 1, 0 , 'C' , false);
			$pdf->SetXY(80,56.6);
			$pdf->Cell(4 , 3.2,$lang['10552'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(68,59.7);
			$pdf->Cell(4 , 3.2,$lang['10520'], 1, 0 , 'C' , false);
			$pdf->SetXY(72,59.7);
			$pdf->Cell(4 , 3.2,$lang['10531'], 1, 0 , 'C' , false);
			$pdf->SetXY(76,59.7);
			$pdf->Cell(4 , 3.2,$lang['10542'], 1, 0 , 'C' , false);
			$pdf->SetXY(80,59.7);
			$pdf->Cell(4 , 3.2,$lang['10553'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(68,63);
			$pdf->Cell(16 ,4.5, $lang['10137'], 1, 1 , 'C' , false);
			$pdf->SetXY(68,67.6);
			$pdf->Cell(16 ,4.4, $lang['10137'], 1, 1 , 'C' , false);
			
			
			
		/*Se pone el aparatado del equipo Visitante*/
			/*1ra. Fila de Equipo Visitante*/
			
		 // $y = $y + 4;
        //  $x = $x + 5;
			$pdf->SetFont('Times' , 'B' , 7);
			$pdf->SetXY(84,+27);
			$pdf->Cell(8 , 5, $lang['10501'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(92,+27);
			$pdf->Cell(8 , 5, $lang['10502'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(100,+27);
			$pdf->Cell(8 , 5, $lang['10503'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(108,+27);
			$pdf->Cell(8 , 5, $lang['10504'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(116,+27);
			$pdf->Cell(8 , 5, $lang['10505'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(124,+27);
			$pdf->Cell(8 , 5, $lang['10506'], 1, 0 , 'C' , false);
			
		
		/*2da. Fila del equipo Visitante*/
			$pdf->SetXY(84,+32);
			$pdf->Cell(8 , 5,$lang['10136'], 1, 1 , 'C' , false);
			
			$pdf->SetXY(92,+32);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(100,+32);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(108,+32);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(116,+32);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(124,+32);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
		/*3ra. Fila del equipo Visitante 1er. set*/
			$pdf->SetXY(84,+37);
			$pdf->Cell(8 , 5,$lang['10136'], 1, 1 , 'C' , false);
			
			$pdf->SetXY(92,+37);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(100,+37);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(108,+37);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(116,+37);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(124,+37);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
				/*4a. Fila del equipo Visitante 1er. Set*/
			$pdf->SetXY(84,42);
			$pdf->Cell(8 , 5,$lang['10137'], 1, 1 , 'C' , false);
			
			$pdf->SetXY(92,42);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(100,42);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(108,42);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(116,42);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(124,42);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			/*5a. Fila del equipo Visitante 1er. Set*/
			$pdf->SetXY(84,47);
			$pdf->Cell(8 , 5,$lang['10137'], 1, 1 , 'C' , false);
			
			$pdf->SetXY(92,47);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(100,47);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(108,47);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(116,47);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(124,47);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
	
		  /* 6a. Fila Equipo Visitante 1er. set*/
		  
		$pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+85,$y+51.5);
			$pdf->Cell(4 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+84,$y+53);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+89,$y+51.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+88,$y+53);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
	
	   	    $pdf->SetXY($x+93,$y+51.5);
			$pdf->Cell(4 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+92,$y+53);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+97,$y+51.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+96,$y+53);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);

	        $pdf->SetXY($x+101,$y+51.5);
			$pdf->Cell(4 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+100,$y+53);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+105,$y+51.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+104,$y+53);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);

	     	$pdf->SetXY($x+109,$y+51.5);
			$pdf->Cell(4, 5, $lang['10509'], 0, 0, 'R' , false);
			$pdf->SetXY($x+108,$y+53);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+113,$y+51.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+112,$y+53);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+117,$y+51.5);
			$pdf->Cell(4, 5, $lang['10509'], 0, 0, 'R' , false);
			$pdf->SetXY($x+116,$y+53);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+121,$y+51.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+120,$y+53);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+125,$y+51.5);
			$pdf->Cell(4, 5, $lang['10509'], 0, 0, 'R' , false);
			$pdf->SetXY($x+124,$y+53);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+129,$y+51.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+128,$y+53);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);

		  /* 7a. Fila Equipo Visitante 1er. set */
		  
	    $pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+85,$y+56.5);
			$pdf->Cell(4 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+84,$y+58);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+89,$y+56.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+88,$y+58);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
	
	   	    $pdf->SetXY($x+93,$y+56.5);
			$pdf->Cell(4 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+92,$y+58);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+97,$y+56.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+96,$y+58);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);

	        $pdf->SetXY($x+101,$y+56.5);
			$pdf->Cell(4 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+100,$y+58);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+105,$y+56.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+104,$y+58);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);

	     	$pdf->SetXY($x+109,$y+56.5);
			$pdf->Cell(4, 5, $lang['10510'], 0, 0, 'R' , false);
			$pdf->SetXY($x+108,$y+58);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+113,$y+56.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+112,$y+58);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+117,$y+56.5);
			$pdf->Cell(4, 5, $lang['10510'], 0, 0, 'R' , false);
			$pdf->SetXY($x+116,$y+58);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+121,$y+56.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+120,$y+58);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+125,$y+56.5);
			$pdf->Cell(4, 5, $lang['10510'], 0, 0, 'R' , false);
			$pdf->SetXY($x+124,$y+58);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+129,$y+56.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+128,$y+58);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);

		  /* 8a. Fila Equipo Visitante 1er. set*/
		  
		    $pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+85,$y+61.5);
			$pdf->Cell(4 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+84,$y+63);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+89,$y+61.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+88,$y+63);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
	
	   	    $pdf->SetXY($x+93,$y+61.5);
			$pdf->Cell(4 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+92,$y+63);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+97,$y+61.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+96,$y+63);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);

	        $pdf->SetXY($x+101,$y+61.5);
			$pdf->Cell(4 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+100,$y+63);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+105,$y+61.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+104,$y+63);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);

	     	$pdf->SetXY($x+109,$y+61.5);
			$pdf->Cell(4, 5, $lang['10511'], 0, 0, 'R' , false);
			$pdf->SetXY($x+108,$y+63);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+113,$y+61.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+112,$y+63);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+117,$y+61.5);
			$pdf->Cell(4, 5, $lang['10511'], 0, 0, 'R' , false);
			$pdf->SetXY($x+116,$y+63);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+121,$y+61.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+120,$y+63);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+125,$y+61.5);
			$pdf->Cell(4, 5, $lang['10511'], 0, 0, 'R' , false);
			$pdf->SetXY($x+124,$y+63);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+129,$y+61.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+128,$y+63);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);

		 /* 9a. Fila Equipo Visitante 1er. Set*/
	    $pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+85,$y+66.5);
			$pdf->Cell(4 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+84,$y+68);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+89,$y+66.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+88,$y+68);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
	
	   	    $pdf->SetXY($x+93,$y+66.5);
			$pdf->Cell(4 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+92,$y+68);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+97,$y+66.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+96,$y+68);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);

	        $pdf->SetXY($x+101,$y+66.5);
			$pdf->Cell(4 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+100,$y+68);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+105,$y+66.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+104,$y+68);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);

	     	$pdf->SetXY($x+109,$y+66.5);
			$pdf->Cell(4, 5, $lang['10512'], 0, 0, 'R' , false);
			$pdf->SetXY($x+108,$y+68);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+113,$y+66.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+112,$y+68);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+117,$y+66.5);
			$pdf->Cell(4, 5, $lang['10512'], 0, 0, 'R' , false);
			$pdf->SetXY($x+116,$y+68);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+121,$y+66.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+120,$y+68);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+125,$y+66.5);
			$pdf->Cell(4, 5, $lang['10512'], 0, 0, 'R' , false);
			$pdf->SetXY($x+124,$y+68);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+129,$y+66.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+128,$y+68);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);

/* Puntos Equipo Visitante 1er. set*/
		  
		    $pdf->SetFont('Times' , '' , 6);
              $pdf->SetXY(132,27);
			$pdf->Cell(3.7 , 3.4,$lang['10509'], 1, 0 , 'C' , false);
			$pdf->SetXY(135.7,27);
			$pdf->Cell(3.7 , 3.3,$lang['10521'], 1, 0 , 'C' , false);
			$pdf->SetXY(139.4,27);
			$pdf->Cell(3.7 , 3.3,$lang['10532'], 1, 0 , 'C' , false);
			$pdf->SetXY(143.1,27);
			$pdf->Cell(3.7 , 3.3,$lang['10543'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(132,30.4);
			$pdf->Cell(3.7 , 3.3,$lang['10510'], 1, 0 , 'C' , false);
			$pdf->SetXY(135.7,30.4);
			$pdf->Cell(3.7 , 3.3,$lang['10522'], 1, 0 , 'C' , false);
			$pdf->SetXY(139.4,30.4);
			$pdf->Cell(3.7 , 3.3,$lang['10533'], 1, 0 , 'C' , false);
			$pdf->SetXY(143.1,30.4);
			$pdf->Cell(3.7 , 3.3,$lang['10544'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(132,33.7);
			$pdf->Cell(3.7 , 3.3,$lang['10511'], 1, 0 , 'C' , false);
			$pdf->SetXY(135.7,33.7);
			$pdf->Cell(3.7 , 3.3,$lang['10523'], 1, 0 , 'C' , false);
			$pdf->SetXY(139.4,33.7);
			$pdf->Cell(3.7 , 3.3,$lang['10534'], 1, 0 , 'C' , false);
			$pdf->SetXY(143.1,33.7);
			$pdf->Cell(3.7 , 3.3,$lang['10545'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(132,37.1);
			$pdf->Cell(3.7 , 3.3,$lang['10512'], 1, 0 , 'C' , false);
			$pdf->SetXY(135.7,37.1);
			$pdf->Cell(3.7 , 3.3,$lang['10524'], 1, 0 , 'C' , false);
			$pdf->SetXY(139.4,37.1);
			$pdf->Cell(3.7 , 3.3,$lang['10535'], 1, 0 , 'C' , false);
			$pdf->SetXY(143.1,37.1);
			$pdf->Cell(3.7 , 3.3,$lang['10546'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(132,40.3);
			$pdf->Cell(3.7 , 3.3,$lang['10513'], 1, 0 , 'C' , false);
			$pdf->SetXY(135.7,40.3);
			$pdf->Cell(3.7 , 3.3,$lang['10525'], 1, 0 , 'C' , false);
			$pdf->SetXY(139.4,40.3);
			$pdf->Cell(3.7 , 3.3,$lang['10536'], 1, 0 , 'C' , false);
			$pdf->SetXY(143.1,40.3);
			$pdf->Cell(3.7 , 3.3,$lang['10547'], 1, 0 , 'C' , false);
			
            $pdf->SetXY(132,43.5);
			$pdf->Cell(3.7 , 3.3,$lang['10514'], 1, 0 , 'C' , false);
			$pdf->SetXY(135.7,43.5);
			$pdf->Cell(3.7 , 3.3,$lang['10526'], 1, 0 , 'C' , false);
			$pdf->SetXY(139.4,43.5);
			$pdf->Cell(3.7 , 3.3,$lang['10537'], 1, 0 , 'C' , false);
			$pdf->SetXY(143.1,43.5);
			$pdf->Cell(3.7 , 3.3,$lang['10548'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(132,46.9);
			$pdf->Cell(3.7 , 3.3,$lang['10515'], 1, 0 , 'C' , false);
			$pdf->SetXY(135.7,46.9);
			$pdf->Cell(3.7 , 3.3,$lang['10527'], 1, 0 , 'C' , false);
			$pdf->SetXY(139.4,46.9);
			$pdf->Cell(3.7 , 3.3,$lang['10538'], 1, 0 , 'C' , false);
			$pdf->SetXY(143.1,46.9);
			$pdf->Cell(3.7 , 3.3,$lang['10549'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(132,50.1);
			$pdf->Cell(3.7 , 3.3,$lang['10516'], 1, 0 , 'C' , false);
			$pdf->SetXY(135.7,50.1);
			$pdf->Cell(3.7 , 3.3,$lang['10528'], 1, 0 , 'C' , false);
			$pdf->SetXY(139.4,50.1);
			$pdf->Cell(3.7 , 3.3,$lang['10539'], 1, 0 , 'C' , false);
			$pdf->SetXY(143.1,50.1);
			$pdf->Cell(3.7 , 3.3,$lang['10550'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(132,53.4);
			$pdf->Cell(3.7 , 3.3,$lang['10518'], 1, 0 , 'C' , false);
			$pdf->SetXY(135.7,53.4);
			$pdf->Cell(3.7 , 3.3,$lang['10529'], 1, 0 , 'C' , false);
			$pdf->SetXY(139.4,53.4);
			$pdf->Cell(3.7 , 3.3,$lang['10540'], 1, 0 , 'C' , false);
			$pdf->SetXY(143.1,53.4);
			$pdf->Cell(3.7 , 3.3,$lang['10551'], 1, 0 , 'C' , false);
			
	    	$pdf->SetXY(132,56.6);
			$pdf->Cell(3.7 , 3.2,$lang['10519'], 1, 0 , 'C' , false);
			$pdf->SetXY(135.7,56.6);
			$pdf->Cell(3.7 , 3.2,$lang['10530'], 1, 0 , 'C' , false);
			$pdf->SetXY(139.4,56.6);
			$pdf->Cell(3.7 , 3.2,$lang['10541'], 1, 0 , 'C' , false);
			$pdf->SetXY(143.1,56.6);
			$pdf->Cell(3.7 , 3.2,$lang['10552'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(132,59.7);
			$pdf->Cell(3.8 , 3.2,$lang['10520'], 1, 0 , 'C' , false);
			$pdf->SetXY(135.7,59.7);
			$pdf->Cell(3.8 , 3.2,$lang['10531'], 1, 0 , 'C' , false);
			$pdf->SetXY(139.4,59.7);
			$pdf->Cell(3.8 , 3.2,$lang['10542'], 1, 0 , 'C' , false);
			$pdf->SetXY(143.1,59.7);
			$pdf->Cell(3.8 , 3.2,$lang['10553'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(132,63);
			$pdf->Cell(15 ,4.5, $lang['10137'], 1, 1 , 'C' , false);
			$pdf->SetXY(132,67.6);
			$pdf->Cell(15 ,4.4, $lang['10137'], 1, 1 , 'C' , false);

    /* Inicia Datos del Equipo Visitante 2do Set Titulos*/
          	$pdf->SetXY($x+147,$y+28);
			$pdf->Cell(4 , 45, $lang['10507'], 1, 1 , 'C' , false);
            
            $pdf->SetFont('Times' , 'B' , 7);
			
			$pdf->SetXY($x+151,$y+28);
			$pdf->Cell(7.5 , 5, $lang['10501'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+158.5,$y+28);
			$pdf->Cell(7.5, 5, $lang['10502'], 1, 0, 'C' , false);
			$pdf->SetXY($x+166,$y+28);
			$pdf->Cell(7.5, 5, $lang['10503'], 1, 0, 'C' , false);
			$pdf->SetXY($x+173.5,$y+28);
			$pdf->Cell(7.5, 5, $lang['10504'], 1, 0, 'C' , false);
			$pdf->SetXY($x+181,$y+28);
			$pdf->Cell(7.5, 5, $lang['10505'], 1, 0, 'C' , false);
			$pdf->SetXY($x+188.5,$y+28);
			$pdf->Cell(7.5, 5, $lang['10506'], 1, 0, 'C' , false);
			
/*2da. Fila del equipo Visitante*/
			$pdf->SetXY(151,+32);
			$pdf->Cell(7.5 , 5,$lang['10136'], 1, 1 , 'C' , false);
			
			$pdf->SetXY(158.5,+32);
			$pdf->Cell(7.5 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(166,+32);
			$pdf->Cell(7.5 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(173.5,+32);
			$pdf->Cell(7.5 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(181,+32);
			$pdf->Cell(7.5 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(188.5,+32);
			$pdf->Cell(7.5 , 5, $lang['10136'], 1, 0 , 'C' , false);

/*3ra. Fila del equipo Visitante*/
			$pdf->SetXY(151,+37);
			$pdf->Cell(7.5 , 5,$lang['10136'], 1, 1 , 'C' , false);
			
			$pdf->SetXY(158.5,+37);
			$pdf->Cell(7.5 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(166,+37);
			$pdf->Cell(7.5 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(173.5,+37);
			$pdf->Cell(7.5 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(181,+37);
			$pdf->Cell(7.5 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(188.5,+37);
			$pdf->Cell(7.5 , 5, $lang['10136'], 1, 0 , 'C' , false);

/*4a. Fila del equipo Visitante*/
			$pdf->SetXY(151,+42);
			$pdf->Cell(7.5 , 5,$lang['10137'], 1, 1 , 'C' , false);
			
			$pdf->SetXY(158.5,+42);
			$pdf->Cell(7.5 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(166,+42);
			$pdf->Cell(7.5 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(173.5,+42);
			$pdf->Cell(7.5 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(181,+42);
			$pdf->Cell(7.5 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(188.5,+42);
			$pdf->Cell(7.5 , 5, $lang['10137'], 1, 0 , 'C' , false);

/*5a. Fila del equipo Visitante 2do. set*/
			$pdf->SetXY(151,+47);
			$pdf->Cell(7.5 , 5,$lang['10137'], 1, 1 , 'C' , false);
			
			$pdf->SetXY(158.5,+47);
			$pdf->Cell(7.5 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(166,+47);
			$pdf->Cell(7.5 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(173.5,+47);
			$pdf->Cell(7.5 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(181,+47);
			$pdf->Cell(7.5 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(188.5,+47);
			$pdf->Cell(7.5 , 5, $lang['10137'], 1, 0 , 'C' , false);
	
  /* 6a. Fila Equipo Visitante 2do. set*/
		  
		$pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+151.7,$y+51.5);
			$pdf->Cell(3.6 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+151,$y+53);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+155.5,$y+51.5);
			$pdf->Cell(3.6, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+154.6,$y+53);
			$pdf->Cell(3.8, 5, $lang['10136'], 1, 0, 'R' , false);
	
	   	    $pdf->SetXY($x+158.5,$y+51.5);
			$pdf->Cell(3.6 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+158.5,$y+53);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+162.5,$y+51.5);
			$pdf->Cell(3.6, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+162,$y+53);
			$pdf->Cell(3.8, 5, $lang['10136'], 1, 0, 'R' , false);

            $pdf->SetXY($x+166.5,$y+51.5);
			$pdf->Cell(3.6 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+166,$y+53);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+170,$y+51.5);
			$pdf->Cell(3.6, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+169.5,$y+53);
			$pdf->Cell(3.8, 5, $lang['10136'], 1, 0, 'R' , false);


	     	$pdf->SetXY($x+174,$y+51.5);
			$pdf->Cell(3.6, 5, $lang['10509'], 0, 0, 'R' , false);
			$pdf->SetXY($x+173.5,$y+53);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+178.2,$y+51.5);
			$pdf->Cell(3.6, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+177,$y+53);
			$pdf->Cell(3.8, 5, $lang['10136'], 1, 0, 'R' , false);
				
			$pdf->SetXY($x+181.6,$y+51.5);
			$pdf->Cell(3.6, 5, $lang['10509'], 0, 0, 'R' , false);
			$pdf->SetXY($x+181,$y+53);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+185,$y+51.5);
			$pdf->Cell(3.6, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+184.5,$y+53);
			$pdf->Cell(3.8, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+189.3,$y+51.5);
			$pdf->Cell(3.6, 5, $lang['10509'], 0, 0, 'R' , false);
			$pdf->SetXY($x+188.5,$y+53);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+192.8,$y+51.5);
			$pdf->Cell(3.6, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+192.2,$y+53);
			$pdf->Cell(3.8, 5, $lang['10136'], 1, 0, 'R' , false);


/* 7a. Fila Equipo Visitante 2do. set Visitante*/
		  
		$pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+151.8,$y+56.3);
			$pdf->Cell(3.6 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+151,$y+58);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
	$pdf->SetXY($x+155.6,$y+56.3);
			$pdf->Cell(3.6, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+154.6,$y+58);
			$pdf->Cell(3.8, 5, $lang['10136'], 1, 0, 'R' , false);
	
	   	    $pdf->SetXY($x+158.8,$y+56.3);
			$pdf->Cell(3.6 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+158.5,$y+58);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+162.6,$y+56.3);
			$pdf->Cell(3.6, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+162,$y+58);
			$pdf->Cell(3.8, 5, $lang['10136'], 1, 0, 'R' , false);

            $pdf->SetXY($x+166.5,$y+56.3);
			$pdf->Cell(3.6 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+166,$y+58);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+170,$y+56.3);
			$pdf->Cell(3.6, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+169.5,$y+58);
			$pdf->Cell(3.8, 5, $lang['10136'], 1, 0, 'R' , false);


	     	$pdf->SetXY($x+174,$y+56.3);
			$pdf->Cell(3.6, 5, $lang['10510'], 0, 0, 'R' , false);
			$pdf->SetXY($x+173.5,$y+58);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+178.2,$y+56.3);
			$pdf->Cell(3.6, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+177,$y+58);
			$pdf->Cell(3.8, 5, $lang['10136'], 1, 0, 'R' , false);
				
			$pdf->SetXY($x+181.6,$y+56.3);
			$pdf->Cell(3.6, 5, $lang['10510'], 0, 0, 'R' , false);
			$pdf->SetXY($x+181,$y+58);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+185,$y+56.3);
			$pdf->Cell(3.6, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+184.5,$y+58);
			$pdf->Cell(3.8, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+189.3,$y+56.3);
			$pdf->Cell(3.6, 5, $lang['10510'], 0, 0, 'R' , false);
			$pdf->SetXY($x+188.5,$y+58);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+192.8,$y+56.3);
			$pdf->Cell(3.6, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+192.2,$y+58);
			$pdf->Cell(3.8, 5, $lang['10136'], 1, 0, 'R' , false);

	
/* 8a. Fila Equipo Visitante 2do. set Visitante*/
$pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+151.8,$y+61.5);
			$pdf->Cell(3.6 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+151,$y+63);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
	$pdf->SetXY($x+155.6,$y+61.5);
			$pdf->Cell(3.6, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+154.6,$y+63);
			$pdf->Cell(3.8, 5, $lang['10136'], 1, 0, 'R' , false);
	
	   	    $pdf->SetXY($x+158.8,$y+61.5);
			$pdf->Cell(3.6 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+158.5,$y+63);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+162.7,$y+61.5);
			$pdf->Cell(3.6, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+162,$y+63);
			$pdf->Cell(3.8, 5, $lang['10136'], 1, 0, 'R' , false);

            $pdf->SetXY($x+166.5,$y+61.5);
			$pdf->Cell(3.6 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+166,$y+63);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+170,$y+61.5);
			$pdf->Cell(3.6, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+169.5,$y+63);
			$pdf->Cell(3.8, 5, $lang['10136'], 1, 0, 'R' , false);


	     	$pdf->SetXY($x+174,$y+61.5);
			$pdf->Cell(3.6, 5, $lang['10511'], 0, 0, 'R' , false);
			$pdf->SetXY($x+173.5,$y+63);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+178.2,$y+61.5);
			$pdf->Cell(3.6, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+177,$y+63);
			$pdf->Cell(3.8, 5, $lang['10136'], 1, 0, 'R' , false);
				
			$pdf->SetXY($x+181.6,$y+61.5);
			$pdf->Cell(3.6, 5, $lang['10511'], 0, 0, 'R' , false);
			$pdf->SetXY($x+181,$y+63);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+185,$y+61.5);
			$pdf->Cell(3.6, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+184.5,$y+63);
			$pdf->Cell(3.8, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+189.3,$y+61.5);
			$pdf->Cell(3.6, 5, $lang['10511'], 0, 0, 'R' , false);
			$pdf->SetXY($x+188.5,$y+63);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+192.8,$y+61.5);
			$pdf->Cell(3.6, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+192.2,$y+63);
			$pdf->Cell(3.8, 5, $lang['10136'], 1, 0, 'R' , false);
			
	
/* 9a. Fila Equipo Visitante 2do. set Visitante*/
		$pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+151.8,$y+66.5);
			$pdf->Cell(3.6 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+151,$y+68);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
	$pdf->SetXY($x+155.6,$y+66.5);
			$pdf->Cell(3.6, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+154.6,$y+68);
			$pdf->Cell(3.8, 5, $lang['10136'], 1, 0, 'R' , false);
	
	  	    $pdf->SetXY($x+158.8,$y+66.5);
			$pdf->Cell(3.6 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+158.5,$y+68);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+162.7,$y+66.5);
			$pdf->Cell(3.6, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+162,$y+68);
			$pdf->Cell(3.8, 5, $lang['10136'], 1, 0, 'R' , false);

            $pdf->SetXY($x+166.5,$y+66.5);
			$pdf->Cell(3.6 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+166,$y+68);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+170,$y+66.5);
			$pdf->Cell(3.6, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+169.5,$y+68);
			$pdf->Cell(3.8, 5, $lang['10136'], 1, 0, 'R' , false);


	     	$pdf->SetXY($x+174,$y+66.5);
			$pdf->Cell(3.6, 5, $lang['10512'], 0, 0, 'R' , false);
			$pdf->SetXY($x+173.5,$y+68);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+178.2,$y+66.5);
			$pdf->Cell(3.6, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+177,$y+68);
			$pdf->Cell(3.8, 5, $lang['10136'], 1, 0, 'R' , false);
				
			$pdf->SetXY($x+181.6,$y+66.5);
			$pdf->Cell(3.6, 5, $lang['10512'], 0, 0, 'R' , false);
			$pdf->SetXY($x+181,$y+68);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+185,$y+66.5);
			$pdf->Cell(3.6, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+184.5,$y+68);
			$pdf->Cell(3.8, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+189.3,$y+66.5);
			$pdf->Cell(3.6, 5, $lang['10512'], 0, 0, 'R' , false);
			$pdf->SetXY($x+188.5,$y+68);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+192.8,$y+66.5);
			$pdf->Cell(3.6, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+192.2,$y+68);
			$pdf->Cell(3.8, 5, $lang['10136'], 1, 0, 'R' , false);
	
/* Puntos Equipo Visitante 2do. set visitante*/
		  
		    $pdf->SetFont('Times' , '' , 6);
              $pdf->SetXY(196,27);
			$pdf->Cell(3.7 , 3.4,$lang['10509'], 1, 0 , 'C' , false);
			$pdf->SetXY(199.7,27);
			$pdf->Cell(3.7 , 3.3,$lang['10521'], 1, 0 , 'C' , false);
			$pdf->SetXY(203.3,27);
			$pdf->Cell(3.7 , 3.3,$lang['10532'], 1, 0 , 'C' , false);
			$pdf->SetXY(206.9,27);
			$pdf->Cell(3.7 , 3.3,$lang['10543'], 1, 0 , 'C' , false);
			
	    	$pdf->SetXY(196,30.4);
			$pdf->Cell(3.7 , 3.3,$lang['10510'], 1, 0 , 'C' , false);
			$pdf->SetXY(199.7,30.4);
			$pdf->Cell(3.7 , 3.3,$lang['10522'], 1, 0 , 'C' , false);
			$pdf->SetXY(203.3,30.4);
			$pdf->Cell(3.7 , 3.3,$lang['10533'], 1, 0 , 'C' , false);
			$pdf->SetXY(206.9,30.4);
			$pdf->Cell(3.7 , 3.3,$lang['10544'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(196,33.7);
			$pdf->Cell(3.7 , 3.3,$lang['10511'], 1, 0 , 'C' , false);
			$pdf->SetXY(199.7,33.7);
			$pdf->Cell(3.7 , 3.3,$lang['10523'], 1, 0 , 'C' , false);
			$pdf->SetXY(203.3,33.7);
			$pdf->Cell(3.7 , 3.3,$lang['10534'], 1, 0 , 'C' , false);
			$pdf->SetXY(206.9,33.7);
			$pdf->Cell(3.7 , 3.3,$lang['10545'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(196,37.1);
			$pdf->Cell(3.7 , 3.3,$lang['10512'], 1, 0 , 'C' , false);
			$pdf->SetXY(199.7,37.1);
			$pdf->Cell(3.7 , 3.3,$lang['10524'], 1, 0 , 'C' , false);
			$pdf->SetXY(203.3,37.1);
			$pdf->Cell(3.7 , 3.3,$lang['10535'], 1, 0 , 'C' , false);
			$pdf->SetXY(206.9,37.1);
			$pdf->Cell(3.7 , 3.3,$lang['10546'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(196,40.3);
			$pdf->Cell(3.7 , 3.3,$lang['10513'], 1, 0 , 'C' , false);
			$pdf->SetXY(199.7,40.3);
			$pdf->Cell(3.7 , 3.3,$lang['10525'], 1, 0 , 'C' , false);
			$pdf->SetXY(203.3,40.3);
			$pdf->Cell(3.7 , 3.3,$lang['10536'], 1, 0 , 'C' , false);
			$pdf->SetXY(206.9,40.3);
			$pdf->Cell(3.7 , 3.3,$lang['10547'], 1, 0 , 'C' , false);
			
            $pdf->SetXY(196,43.5);
			$pdf->Cell(3.7 , 3.3,$lang['10514'], 1, 0 , 'C' , false);
			$pdf->SetXY(199.7,43.5);
			$pdf->Cell(3.7 , 3.3,$lang['10526'], 1, 0 , 'C' , false);
			$pdf->SetXY(203.3,43.5);
			$pdf->Cell(3.7 , 3.3,$lang['10537'], 1, 0 , 'C' , false);
			$pdf->SetXY(206.9,43.5);
			$pdf->Cell(3.7 , 3.3,$lang['10548'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(196,46.9);
			$pdf->Cell(3.7 , 3.3,$lang['10515'], 1, 0 , 'C' , false);
			$pdf->SetXY(199.7,46.9);
			$pdf->Cell(3.7 , 3.3,$lang['10527'], 1, 0 , 'C' , false);
			$pdf->SetXY(203.3,46.9);
			$pdf->Cell(3.7 , 3.3,$lang['10538'], 1, 0 , 'C' , false);
			$pdf->SetXY(206.9,46.9);
			$pdf->Cell(3.7 , 3.3,$lang['10549'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(196,50.1);
			$pdf->Cell(3.7 , 3.3,$lang['10516'], 1, 0 , 'C' , false);
			$pdf->SetXY(199.7,50.1);
			$pdf->Cell(3.7 , 3.3,$lang['10528'], 1, 0 , 'C' , false);
			$pdf->SetXY(203.3,50.1);
			$pdf->Cell(3.7 , 3.3,$lang['10539'], 1, 0 , 'C' , false);
			$pdf->SetXY(206.9,50.1);
			$pdf->Cell(3.7 , 3.3,$lang['10550'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(196,53.4);
			$pdf->Cell(3.7 , 3.3,$lang['10518'], 1, 0 , 'C' , false);
			$pdf->SetXY(199.7,53.4);
			$pdf->Cell(3.7 , 3.3,$lang['10529'], 1, 0 , 'C' , false);
			$pdf->SetXY(203.3,53.4);
			$pdf->Cell(3.7 , 3.3,$lang['10540'], 1, 0 , 'C' , false);
			$pdf->SetXY(206.9,53.4);
			$pdf->Cell(3.7 , 3.3,$lang['10551'], 1, 0 , 'C' , false);
			
	    	$pdf->SetXY(196,56.6);
			$pdf->Cell(3.7 , 3.2,$lang['10519'], 1, 0 , 'C' , false);
			$pdf->SetXY(199.7,56.6);
			$pdf->Cell(3.7 , 3.2,$lang['10530'], 1, 0 , 'C' , false);
			$pdf->SetXY(203.3,56.6);
			$pdf->Cell(3.7 , 3.2,$lang['10541'], 1, 0 , 'C' , false);
			$pdf->SetXY(206.9,56.6);
			$pdf->Cell(3.7 , 3.2,$lang['10552'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(196,59.7);
			$pdf->Cell(3.8 , 3.2,$lang['10520'], 1, 0 , 'C' , false);
			$pdf->SetXY(199.7,59.7);
			$pdf->Cell(3.8 , 3.2,$lang['10531'], 1, 0 , 'C' , false);
			$pdf->SetXY(203.3,59.7);
			$pdf->Cell(3.8 , 3.2,$lang['10542'], 1, 0 , 'C' , false);
			$pdf->SetXY(206.9,59.7);
			$pdf->Cell(3.8 , 3.2,$lang['10553'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(196,63);
			$pdf->Cell(14.5 ,4.5, $lang['10137'], 1, 1 , 'C' , false);
			$pdf->SetXY(196,67.6);
			$pdf->Cell(14.5 ,4.4, $lang['10137'], 1, 1 , 'C' , false);
	
/*Cabecera acomodoo en cancha del equipo Local 2do Set EMA 09.11.2022*/
 /*1ra. Fila Equipo Local 2do. Set*/
 
		$pdf->SetFont('Times' , 'B' , 7);
			$pdf->SetXY(210.5,+27);
			$pdf->Cell(8 , 5, $lang['10501'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(218.5,+27);
			$pdf->Cell(8 , 5, $lang['10502'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(226.5,+27);
			$pdf->Cell(8 , 5, $lang['10503'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(234.5,+27);
			$pdf->Cell(8 , 5, $lang['10504'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(242.5,+27);
			$pdf->Cell(8 , 5, $lang['10505'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(250.5,+27);
			$pdf->Cell(8 , 5, $lang['10506'], 1, 0 , 'C' , false);

	/*2da. Fila del equipo Equipo Local 2do. Set */
			$pdf->SetXY(210.5,+32);
			$pdf->Cell(8 , 5,$lang['10136'], 1, 1 , 'C' , false);
			
			$pdf->SetXY(218.5,+32);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(226.5,+32);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(234.5,+32);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(242.5,+32);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(250.5,+32);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);

	/*3a. Fila del equipo Equipo Local 2do. Set */
			$pdf->SetXY(210.5,+37);
			$pdf->Cell(8 , 5,$lang['10136'], 1, 1 , 'C' , false);
			
			$pdf->SetXY(218.5,+37);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(226.5,+37);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(234.5,+37);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(242.5,+37);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(250.5,+37);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
	
	/*4a. Fila del equipo Equipo Local 2do. Set */
				$pdf->SetXY(210.5,+42);
			$pdf->Cell(8 , 5,$lang['10137'], 1, 1 , 'C' , false);
			
			$pdf->SetXY(218.5,+42);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(226.5,+42);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(234.5,+42);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(242.5,+42);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(250.5,+42);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
	
	/*5a. Fila del equipo Equipo Local 2do. Set */
			$pdf->SetXY(210.5,+47);
			$pdf->Cell(8 , 5,$lang['10137'], 1, 1 , 'C' , false);
			
			$pdf->SetXY(218.5,+47);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(226.5,+47);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(234.5,+47);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(242.5,+47);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(250.5,+47);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
	/*6a. Fila del equipo Equipo Local 2do. Set */
	
	$pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+211,$y+51.5);
			$pdf->Cell(4 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+210.5,$y+53);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+215,$y+51.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+214.5,$y+53);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
	
	  	   $pdf->SetXY($x+219,$y+51.5);
			$pdf->Cell(4 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+218.5,$y+53);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+223,$y+51.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+222.5,$y+53);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+227,$y+51.5);
			$pdf->Cell(4 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+226.5,$y+53);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+231,$y+51.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+230.5,$y+53);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);

			$pdf->SetXY($x+235,$y+51.5);
			$pdf->Cell(4 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+234.5,$y+53);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+239,$y+51.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+238.5,$y+53);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+243,$y+51.5);
			$pdf->Cell(4 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+242.5,$y+53);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+247,$y+51.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+246.5,$y+53);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+251,$y+51.5);
			$pdf->Cell(4 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+250.5,$y+53);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+255,$y+51.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+254.5,$y+53);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
	/*7a. Fila del equipo Equipo Local 2do. Set */
	
	$pdf->SetFont('Times' , 'B' , 5);
		$pdf->SetXY($x+211,$y+56.5);
			$pdf->Cell(4 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+210.5,$y+58);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+215,$y+56.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+214.5,$y+58);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
	
	  	   $pdf->SetXY($x+219,$y+56.5);
			$pdf->Cell(4 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+218.5,$y+58);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+223,$y+56.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+222.5,$y+58);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+227,$y+56.5);
			$pdf->Cell(4 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+226.5,$y+58);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+231,$y+56.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+230.5,$y+58);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);

			$pdf->SetXY($x+235,$y+56.5);
			$pdf->Cell(4 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+234.5,$y+58);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+239,$y+56.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+238.5,$y+58);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+243,$y+56.5);
			$pdf->Cell(4 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+242.5,$y+58);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+247,$y+56.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+246.5,$y+58);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+251,$y+56.5);
			$pdf->Cell(4 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+250.5,$y+58);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+255,$y+56.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+254.5,$y+58);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);	
			
		/*8a. Fila del equipo Equipo Local 2do. Set */
	
		$pdf->SetFont('Times' , 'B' , 5);	
			$pdf->SetXY($x+211,$y+61.5);
			$pdf->Cell(4 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+210.5,$y+63);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+215,$y+61.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+214.5,$y+63);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
	
	  	   $pdf->SetXY($x+219,$y+61.5);
			$pdf->Cell(4 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+218.5,$y+63);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+223,$y+61.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+222.5,$y+63);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+227,$y+61.5);
			$pdf->Cell(4 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+226.5,$y+63);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+231,$y+61.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+230.5,$y+63);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);

			$pdf->SetXY($x+235,$y+61.5);
			$pdf->Cell(4 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+234.5,$y+63);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+239,$y+61.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+238.5,$y+63);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+243,$y+61.5);
			$pdf->Cell(4 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+242.5,$y+63);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+247,$y+61.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+246.5,$y+63);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+251,$y+61.5);
			$pdf->Cell(4 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+250.5,$y+63);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+255,$y+61.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+254.5,$y+63);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);	
			
	/*9a. Fila del equipo Equipo Local 2do. Set */
	
		    $pdf->SetFont('Times' , 'B' , 5);	
			$pdf->SetXY($x+211,$y+66.5);
			$pdf->Cell(4 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+210.5,$y+68);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+215,$y+66.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+214.5,$y+68);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
	
	  	   $pdf->SetXY($x+219,$y+66.5);
			$pdf->Cell(4 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+218.5,$y+68);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+223,$y+66.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+222.5,$y+68);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+227,$y+66.5);
			$pdf->Cell(4 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+226.5,$y+68);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+231,$y+66.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+230.5,$y+68);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);

			$pdf->SetXY($x+235,$y+66.5);
			$pdf->Cell(4 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+234.5,$y+68);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+239,$y+66.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+238.5,$y+68);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+243,$y+66.5);
			$pdf->Cell(4 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+242.5,$y+68);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+247,$y+66.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+246.5,$y+68);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+251,$y+66.5);
			$pdf->Cell(4 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+250.5,$y+68);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
			$pdf->SetXY($x+255,$y+66.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+254.5,$y+68);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
	/* Puntos Equipo Visitante 2do. set visitante*/
		  
		    $pdf->SetFont('Times' , '' , 6);
              $pdf->SetXY(258.5,27);
			$pdf->Cell(3.7 , 3.4,$lang['10509'], 1, 0 , 'C' , false);
			$pdf->SetXY(262.2,27);
			$pdf->Cell(3.7 , 3.3,$lang['10521'], 1, 0 , 'C' , false);
			$pdf->SetXY(265.8,27);
			$pdf->Cell(3.7 , 3.3,$lang['10532'], 1, 0 , 'C' , false);
			$pdf->SetXY(269.4,27);
			$pdf->Cell(3.7 , 3.3,$lang['10543'], 1, 0 , 'C' , false);
			
	    $pdf->SetXY(258.5,30.4);
			$pdf->Cell(3.7 , 3.3,$lang['10510'], 1, 0 , 'C' , false);
			$pdf->SetXY(262.2,30.4);
			$pdf->Cell(3.7 , 3.3,$lang['10522'], 1, 0 , 'C' , false);
			$pdf->SetXY(265.8,30.4);
			$pdf->Cell(3.7 , 3.3,$lang['10533'], 1, 0 , 'C' , false);
			$pdf->SetXY(269.4,30.4);
			$pdf->Cell(3.7 , 3.3,$lang['10544'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(258.5,33.7);
			$pdf->Cell(3.7 , 3.3,$lang['10511'], 1, 0 , 'C' , false);
			$pdf->SetXY(262.2,33.7);
			$pdf->Cell(3.7 , 3.3,$lang['10523'], 1, 0 , 'C' , false);
			$pdf->SetXY(265.8,33.7);
			$pdf->Cell(3.7 , 3.3,$lang['10534'], 1, 0 , 'C' , false);
			$pdf->SetXY(269.4,33.7);
			$pdf->Cell(3.7 , 3.3,$lang['10545'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(258.5,37.1);
			$pdf->Cell(3.7 , 3.3,$lang['10512'], 1, 0 , 'C' , false);
			$pdf->SetXY(262.2,37.1);
			$pdf->Cell(3.7 , 3.3,$lang['10524'], 1, 0 , 'C' , false);
			$pdf->SetXY(265.8,37.1);
			$pdf->Cell(3.7 , 3.3,$lang['10535'], 1, 0 , 'C' , false);
			$pdf->SetXY(269.4,37.1);
			$pdf->Cell(3.7 , 3.3,$lang['10546'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(258.5,40.3);
			$pdf->Cell(3.7 , 3.3,$lang['10513'], 1, 0 , 'C' , false);
			$pdf->SetXY(262.2,40.3);
			$pdf->Cell(3.7 , 3.3,$lang['10525'], 1, 0 , 'C' , false);
			$pdf->SetXY(265.8,40.3);
			$pdf->Cell(3.7 , 3.3,$lang['10536'], 1, 0 , 'C' , false);
			$pdf->SetXY(269.4,40.3);
			$pdf->Cell(3.7 , 3.3,$lang['10547'], 1, 0 , 'C' , false);
			
            $pdf->SetXY(258.5,43.5);
			$pdf->Cell(3.7 , 3.3,$lang['10514'], 1, 0 , 'C' , false);
			$pdf->SetXY(262.2,43.5);
			$pdf->Cell(3.7 , 3.3,$lang['10526'], 1, 0 , 'C' , false);
			$pdf->SetXY(265.8,43.5);
			$pdf->Cell(3.7 , 3.3,$lang['10537'], 1, 0 , 'C' , false);
			$pdf->SetXY(269.4,43.5);
			$pdf->Cell(3.7 , 3.3,$lang['10548'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(258.5,46.9);
			$pdf->Cell(3.7 , 3.3,$lang['10515'], 1, 0 , 'C' , false);
			$pdf->SetXY(262.2,46.9);
			$pdf->Cell(3.7 , 3.3,$lang['10527'], 1, 0 , 'C' , false);
			$pdf->SetXY(265.8,46.9);
			$pdf->Cell(3.7 , 3.3,$lang['10538'], 1, 0 , 'C' , false);
			$pdf->SetXY(269.4,46.9);
			$pdf->Cell(3.7 , 3.3,$lang['10549'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(258.5,50.1);
			$pdf->Cell(3.7 , 3.3,$lang['10516'], 1, 0 , 'C' , false);
			$pdf->SetXY(262.2,50.1);
			$pdf->Cell(3.7 , 3.3,$lang['10528'], 1, 0 , 'C' , false);
			$pdf->SetXY(265.8,50.1);
			$pdf->Cell(3.7 , 3.3,$lang['10539'], 1, 0 , 'C' , false);
			$pdf->SetXY(269.4,50.1);
			$pdf->Cell(3.7 , 3.3,$lang['10550'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(258.5,53.4);
			$pdf->Cell(3.7 , 3.3,$lang['10518'], 1, 0 , 'C' , false);
			$pdf->SetXY(262.2,53.4);
			$pdf->Cell(3.7 , 3.3,$lang['10529'], 1, 0 , 'C' , false);
			$pdf->SetXY(265.8,53.4);
			$pdf->Cell(3.7 , 3.3,$lang['10540'], 1, 0 , 'C' , false);
			$pdf->SetXY(269.4,53.4);
			$pdf->Cell(3.7 , 3.3,$lang['10551'], 1, 0 , 'C' , false);
			
	    	$pdf->SetXY(258.5,56.6);
			$pdf->Cell(3.7 , 3.2,$lang['10519'], 1, 0 , 'C' , false);
			$pdf->SetXY(262.2,56.6);
			$pdf->Cell(3.7 , 3.2,$lang['10530'], 1, 0 , 'C' , false);
			$pdf->SetXY(265.8,56.6);
			$pdf->Cell(3.7 , 3.2,$lang['10541'], 1, 0 , 'C' , false);
			$pdf->SetXY(269.4,56.6);
			$pdf->Cell(3.7 , 3.2,$lang['10552'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(258.5,59.7);
			$pdf->Cell(3.8 , 3.2,$lang['10520'], 1, 0 , 'C' , false);
			$pdf->SetXY(262.2,59.7);
			$pdf->Cell(3.8 , 3.2,$lang['10531'], 1, 0 , 'C' , false);
			$pdf->SetXY(265.8,59.7);
			$pdf->Cell(3.8 , 3.2,$lang['10542'], 1, 0 , 'C' , false);
			$pdf->SetXY(269.4,59.7);
			$pdf->Cell(3.8 , 3.2,$lang['10553'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(258.5,63);
			$pdf->Cell(14.5 ,4.5, $lang['10137'], 1, 1 , 'C' , false);
			$pdf->SetXY(258.5,67.6);
			$pdf->Cell(14.5 ,4.4, $lang['10137'], 1, 1 , 'C' , false);	
			
			
/*Termina cuadro de equipo cancha Equipo Loca 2sdo. set EMA 09.11.2022*/		
		
  /*Empieza linea de encabezado del 3er. Set */
 /*Empieza Cabecera antes del 3er. Set*/
        	$pdf->SetFont('Times' , 'B' , 8);
		    $pdf->SetXY(5,72);
		    $pdf->Cell(11 , 6,'E: ', 1, 1 , 'L' , false);

            $pdf->SetFont('Times' , 'B' , 6);
		    $pdf->SetXY(16,70);
		    $pdf->Cell(11 , 6, $lang['9999'], 0, 0 , 'L' , false);
		    $pdf->SetXY(16,72);
		    $pdf->Cell(11 , 6, $lang['10137'], 1, 1 , 'C' , false);

  /*Colocacion del equipo dentro de la cancha 3er set*/
		    $pdf->SetFont('Times' , 'B' , 9);
		    $pdf->SetXY(27,72);
		    $pdf->Cell(38 , 6, 'A) ' . utf8_decode($row1["Local"]), 1, 1 , 'L' , false);

  /*Dato de R=Recibe, S= Al servicio Visitante 1er set*/
     	    $pdf->SetXY(65,72);
            $pdf->SetFont('Helvetica' , 'B' , 6);
            $pdf->SetTextColor(0, 0, 0);
	    	$pdf->Cell(3, 3, 'S' , 1, 0 , 'L' , false);
	    	$pdf->SetXY(65,75);
            $pdf->SetFont('Helvetica' , 'B' , 6);
            $pdf->SetTextColor(0, 0, 0);
	    	$pdf->Cell(3, 3, 'R' , 1, 0 , 'L' , false);
     
	    	/*Titulo Puntos Local 3er set */
	    	$pdf->SetXY(68,72);
            $pdf->SetFont('Helvetica' , 'B' , 8);
            $pdf->SetTextColor(0, 0, 0);
	    	$pdf->Cell(16, 6, $lang['370'] , 1, 0 , 'C' , false);

 /*Equipo Visitante 3er. set*/  
	    	$pdf->SetXY(84,72);
            $pdf->SetFont('Times' , 'B' , 9);
			$pdf->Cell(38, 6, 'B) ' . utf8_decode($row1["Visitante"]), 1, 0 , 'L' , false);
	
   /*Dato de R=Recibe, S= Al servicio Visitante 1er set*/
		    $pdf->SetXY(119,72);
            $pdf->SetFont('Helvetica' , 'B' , 6);
            $pdf->SetTextColor(0, 0, 0);
	    	$pdf->Cell(3, 3, 'S' , 1, 0 , 'L' , false);
	    	$pdf->SetXY(119,75);
            $pdf->SetFont('Helvetica' , 'B' , 6);
            $pdf->SetTextColor(0, 0, 0);
	    	$pdf->Cell(3, 3, 'R' , 1, 0 , 'L' , false);
  /*Titulo Termina */
	       $pdf->SetFont('Times' , 'B' , 6);
		    $pdf->SetXY(122,70);
		    $pdf->Cell(10 , 6, $lang['989'], 0, 0 , 'L' , false);
		    $pdf->SetXY(122,72);
		    $pdf->Cell(10 , 6, $lang['10137'], 1, 1 , 'C' , false);

	/*Titulo Puntos Visitante 1er set*/
	    	$pdf->SetXY(132,72);
            $pdf->SetFont('Helvetica' , 'B' , 8);
            $pdf->SetTextColor(0, 0, 0);
	    	$pdf->Cell(15, 6, $lang['370'] , 1, 0 , 'C' , false);
	 
   /*Titulo Inicia 2do. Set*/
	    	 $pdf->SetFont('Times' , 'B' , 4.5);
		    $pdf->SetXY(146.5,70);
		    $pdf->Cell(11 , 6, $lang['10554'], 0, 0 , 'L' , false);
		    $pdf->SetXY(147,72);
		    $pdf->Cell(11 , 6, $lang['10136'], 1, 1 , 'L' , false);

/*Titulo Puntos Visitante 1er set*/
	    	$pdf->SetXY(158,72);
            $pdf->SetFont('Helvetica' , 'B' , 9);
            $pdf->SetTextColor(0, 0, 0);
	    	$pdf->Cell(8, 6, $lang['10137'] , 1, 0 , 'C' , false);

		    
   /*Datos del punt al cambio*/
	        $pdf->SetXY(166,72);
            $pdf->SetFont('Times' , 'B' , 8);
			$pdf->Cell(4 , 6, $lang['10136'], 1, 1 , 'L' , false); 	
					    
   /*Datos del punt al cambio*/
	        $pdf->SetXY(170,72);
            $pdf->SetFont('Times' , 'B' , 8);
			$pdf->Cell(26 , 6, $lang['10136'], 1, 1 , 'L' , false); 	 
			
    /*Titulo Puntos Visitante 2dr set*/
	    	$pdf->SetXY(196,72);
            $pdf->SetFont('Helvetica' , 'B' , 8);
            $pdf->SetTextColor(0, 0, 0);
	    	$pdf->Cell(15, 6, $lang['370'] , 1, 0 , 'C' , false);		



	
	/*Empieza arreglo de jugadores en cancha */
	  /*1ra fila 3er. 1set*/
	  
		$pdf->SetFont('Times' , 'B' , 3);
	         $pdf->SetXY($x+5,$y+79);
			$pdf->Cell(11 , 5,$lang['10500'], 1, 1 , 'C' , false);
			$pdf->SetFont('Times' , 'B' , 7);
			$pdf->SetXY($x+16,$y+79);
			$pdf->Cell(4 , 45, $lang['10507'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+20,$y+79);
			$pdf->Cell(8, 5, $lang['10501'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+28,$y+79);
			$pdf->Cell(8, 5, $lang['10502'], 1, 0, 'C' , false);
			$pdf->SetXY($x+36,$y+79);
			$pdf->Cell(8, 5, $lang['10503'], 1, 0, 'C' , false);
			$pdf->SetXY($x+44,$y+79);
			$pdf->Cell(8, 5, $lang['10504'], 1, 0, 'C' , false);
			$pdf->SetXY($x+52,$y+79);
			$pdf->Cell(8, 5, $lang['10505'], 1, 0, 'C' , false);
			$pdf->SetXY($x+60,$y+79);
			$pdf->Cell(8, 5, $lang['10506'], 1, 0, 'C' , false);
	
	/*2da fila 3er. set*/
		    
		    $pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+5,$y+84);
			$pdf->MultiCell(11 , 5,$lang['10508'], 1, 'C' , false);
		
		$pdf->SetFont('Times' , 'B' , 7);
			$pdf->SetXY($x+20,$y+84);
			$pdf->Cell(8, 5, $lang['10136'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+28,$y+84);
			$pdf->Cell(8, 5, $lang['10136'], 1, 0, 'C' , false);
			$pdf->SetXY($x+36,$y+84);
			$pdf->Cell(8, 5, $lang['10136'], 1, 0, 'C' , false);
			$pdf->SetXY($x+44,$y+84);
			$pdf->Cell(8, 5, $lang['10136'], 1, 0, 'C' , false);
			$pdf->SetXY($x+52,$y+84);
			$pdf->Cell(8, 5, $lang['10136'], 1, 0, 'C' , false);
			$pdf->SetXY($x+60,$y+84);
			$pdf->Cell(8, 5, $lang['10136'], 1, 0, 'C' , false);
			
			/*3da fila 3er set*/
		
		   $pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+5,$y+89);
			$pdf->Cell(11 , 5,$lang['637'], 1, 1 , 'C' , false);
			
			$pdf->SetFont('Times' , 'B' , 7);
			
			$pdf->SetFont('Times' , 'B' , 7);
			$pdf->SetXY($x+20,$y+89);
			$pdf->Cell(8, 5, $lang['10136'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+28,$y+89);
			$pdf->Cell(8, 5, $lang['10136'], 1, 0, 'C' , false);
			$pdf->SetXY($x+36,$y+89);
			$pdf->Cell(8, 5, $lang['10136'], 1, 0, 'C' , false);
			$pdf->SetXY($x+44,$y+89);
			$pdf->Cell(8, 5, $lang['10136'], 1, 0, 'C' , false);
			$pdf->SetXY($x+52,$y+89);
			$pdf->Cell(8, 5, $lang['10136'], 1, 0, 'C' , false);
			$pdf->SetXY($x+60,$y+89);
			$pdf->Cell(8, 5, $lang['10136'], 1, 0, 'C' , false);
		
	
		
			/*4a fila 3er Set cambio de jugador*/
		
		 $pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+5,$y+94);
			$pdf->Cell(11 , 10,$lang['10136'], 1, 1 , 'C' , false);
			
			$pdf->SetFont('Times' , 'B' , 7);
			$pdf->SetXY($x+20,$y+94);
			$pdf->Cell(8, 5, $lang['10137'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+28,$y+94);
			$pdf->Cell(8, 5, $lang['10137'], 1, 0, 'C' , false);
			$pdf->SetXY($x+36,$y+94);
			$pdf->Cell(8, 5, $lang['10137'], 1, 0, 'C' , false);
			$pdf->SetXY($x+44,$y+94);
			$pdf->Cell(8, 5, $lang['10137'], 1, 0, 'C' , false);
			$pdf->SetXY($x+52,$y+94);
			$pdf->Cell(8, 5, $lang['10137'], 1, 0, 'C' , false);
			$pdf->SetXY($x+60,$y+94);
			$pdf->Cell(8, 5, $lang['10137'], 1, 0, 'C' , false);
		
		
			/*5a fila 3er set cambio de jugador*/
	$pdf->SetFont('Times' , 'B' , 7);
			$pdf->SetXY($x+20,$y+99);
			$pdf->Cell(8, 5, $lang['10137'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+28,$y+99);
			$pdf->Cell(8, 5, $lang['10137'], 1, 0, 'C' , false);
			$pdf->SetXY($x+36,$y+99);
			$pdf->Cell(8, 5, $lang['10137'], 1, 0, 'C' , false);
			$pdf->SetXY($x+44,$y+99);
			$pdf->Cell(8, 5, $lang['10137'], 1, 0, 'C' , false);
			$pdf->SetXY($x+52,$y+99);
			$pdf->Cell(8, 5, $lang['10137'], 1, 0, 'C' , false);
			$pdf->SetXY($x+60,$y+99);
			$pdf->Cell(8, 5, $lang['10137'], 1, 0, 'C' , false);

          /*6a fila 1set cambio de jugador*/

            $pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+5,$y+104);
			$pdf->Cell(5.5 , 5,$lang['10509'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+10.5,$y+104);
			$pdf->Cell(5.5 , 5,$lang['10513'], 1, 1 , 'C' , false);

            $pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+20.7,$y+102.5);
			$pdf->Cell(4 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+20,$y+104);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			
            $pdf->SetXY($x+24.7,$y+102.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+24,$y+104);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);


			$pdf->SetXY($x+28.7,$y+102.5);
			$pdf->Cell(4 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+28,$y+104);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+32.7,$y+102.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+32,$y+104);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+36.7,$y+102.5);
			$pdf->Cell(4 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+36,$y+104);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+40.7,$y+102.5);
		    $pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
		    $pdf->SetXY($x+40,$y+104);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+44.7,$y+102.5);
			$pdf->Cell(4 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+44,$y+104);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+48.7,$y+102.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+48,$y+104);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
		
			$pdf->SetXY($x+52.7,$y+102.5);
			$pdf->Cell(4 , 5, $lang['10509'], 0, 0, 'R' , false);
            $pdf->SetXY($x+52,$y+104);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+56.7,$y+102.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+56,$y+104);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
		
			$pdf->SetXY($x+60.7,$y+102.5);
			$pdf->Cell(4 , 5, $lang['10509'], 0, 0 , 'R' , false);
            $pdf->SetXY($x+60,$y+104);
			$pdf->Cell(4, 5, $lang['10136'], 1, 1 , 'R' , false);

			$pdf->SetXY($x+64.7,$y+102.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
            $pdf->SetXY($x+64,$y+104);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);

        /*7a fila 1set cambio de jugador*/
  $pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+5,$y+109);
			$pdf->Cell(5.5 , 5,$lang['10510'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+10.5,$y+109);
			$pdf->Cell(5.5 , 5,$lang['10514'], 1, 1 , 'C' , false);
			
			$pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+20.7,$y+107.5);
			$pdf->Cell(4 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+20,$y+109);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
			$pdf->SetXY($x+24.7,$y+107.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+24,$y+109);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
		
			$pdf->SetXY($x+28.7,$y+107.5);
			$pdf->Cell(4 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+28,$y+109);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+32.7,$y+107.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+32,$y+109);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+36.7,$y+107.5);
			$pdf->Cell(4 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+36,$y+109);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+40.7,$y+107.5);
		    $pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
		    $pdf->SetXY($x+40,$y+109);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+44.7,$y+107.5);
			$pdf->Cell(4 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+44,$y+109);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+48.7,$y+107.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+48,$y+109);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
		
			$pdf->SetXY($x+52.7,$y+107.5);
			$pdf->Cell(4 , 5, $lang['10510'], 0, 0, 'R' , false);
            $pdf->SetXY($x+52,$y+109);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+56.7,$y+107.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+56,$y+109);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
		
			$pdf->SetXY($x+60.7,$y+107.5);
			$pdf->Cell(4 , 5, $lang['10510'], 0, 0 , 'R' , false);
            $pdf->SetXY($x+60,$y+109);
			$pdf->Cell(4, 5, $lang['10136'], 1, 1 , 'R' , false);

			$pdf->SetXY($x+64.7,$y+107.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
            $pdf->SetXY($x+64,$y+109);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);

		     /*8a fila 1set cambio de jugador*/

	      $pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+5,$y+114);
			$pdf->Cell(5.5 , 5,$lang['10511'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+10.5,$y+114);
			$pdf->Cell(5.5 , 5,$lang['10515'], 1, 1 , 'C' , false);
			
		$pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+20.7,$y+112.5);
			$pdf->Cell(4 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+20,$y+114);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	$pdf->SetXY($x+24.7,$y+112.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+24,$y+114);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
		
			$pdf->SetXY($x+28.7,$y+112.5);
			$pdf->Cell(4 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+28,$y+114);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+32.7,$y+112.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+32,$y+114);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+36.7,$y+112.5);
			$pdf->Cell(4 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+36,$y+114);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+40.7,$y+112.5);
		    $pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
		    $pdf->SetXY($x+40,$y+114);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+44.7,$y+112.5);
			$pdf->Cell(4 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+44,$y+114);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+48.7,$y+112.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+48,$y+114);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
		
			$pdf->SetXY($x+52.7,$y+112.5);
			$pdf->Cell(4 , 5, $lang['10511'], 0, 0, 'R' , false);
            $pdf->SetXY($x+52,$y+114);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+56.7,$y+112.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+56,$y+114);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
		
			$pdf->SetXY($x+60.7,$y+112.5);
			$pdf->Cell(4 , 5, $lang['10511'], 0, 0 , 'R' , false);
            $pdf->SetXY($x+60,$y+114);
			$pdf->Cell(4, 5, $lang['10136'], 1, 1 , 'R' , false);

			$pdf->SetXY($x+64.7,$y+112.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
            $pdf->SetXY($x+64,$y+114);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
    
    /*9a fila 3er. 1set cambio de jugador*/
		
	             $pdf->SetFont('Times' , 'B' , 6);
			$pdf->SetXY($x+5,$y+119);
			$pdf->Cell(5.5 , 5,$lang['10512'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+10.5,$y+119);
			$pdf->Cell(5.5 , 5,$lang['10516'], 1, 1 , 'C' , false);
		
		 	$pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+20.7,$y+117.5);
			$pdf->Cell(4 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+20,$y+119);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+24.7,$y+117.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+24,$y+119);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
		
			$pdf->SetXY($x+28.7,$y+117.5);
			$pdf->Cell(4 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+28,$y+119);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+32.7,$y+117.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+32,$y+119);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+36.7,$y+117.5);
			$pdf->Cell(4 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+36,$y+119);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+40.7,$y+117.5);
		    $pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
		    $pdf->SetXY($x+40,$y+119);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+44.7,$y+117.5);
			$pdf->Cell(4 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+44,$y+119);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+48.7,$y+117.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+48,$y+119);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
		
			$pdf->SetXY($x+52.7,$y+117.5);
			$pdf->Cell(4 , 5, $lang['10512'], 0, 0, 'R' , false);
            $pdf->SetXY($x+52,$y+119);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
		
			$pdf->SetXY($x+56.7,$y+117.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+56,$y+119);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
		
			$pdf->SetXY($x+60.7,$y+117.5);
			$pdf->Cell(4 , 5, $lang['10512'], 0, 0 , 'R' , false);
            $pdf->SetXY($x+60,$y+119);
			$pdf->Cell(4, 5, $lang['10136'], 1, 1 , 'R' , false);

			$pdf->SetXY($x+64.7,$y+117.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
            $pdf->SetXY($x+64,$y+119);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
      
      /*Puntos cortos del 3er. set*/
            $pdf->SetFont('Times' , '' , 8);
           $pdf->SetXY(68,83);
			$pdf->Cell(7.5 , 6,$lang['10509'], 0, 0 , 'C' , false);
			$pdf->SetXY(75.5,83);
			$pdf->Cell(7.5 , 6,$lang['10513'], 0, 0 , 'C' , false);
			
			$pdf->SetXY(68,89);
			$pdf->Cell(7.5 , 6,$lang['10510'], 0, 0 , 'C' , false);
			$pdf->SetXY(75.5,89);
			$pdf->Cell(7.5 , 6,$lang['10514'], 0, 0 , 'C' , false);
	
		    $pdf->SetXY(68,95);
			$pdf->Cell(7.5 , 6,$lang['10513'], 0, 0 , 'C' , false);
			$pdf->SetXY(75.5,95);
			$pdf->Cell(7.5 , 6,$lang['10515'], 0, 0 , 'C' , false);
			
			$pdf->SetXY(68,101);
			$pdf->Cell(7.5 , 6,$lang['10512'], 0, 0 , 'C' , false);
			$pdf->SetXY(75.5,101);
			$pdf->Cell(7.5 , 6,$lang['10516'], 0, 0 , 'C' , false);
			
			$pdf->SetXY(68,113);
			$pdf->Cell(16 ,5, $lang['10137'], 1, 1 , 'C' , false);
			$pdf->SetXY(68,118);
			$pdf->Cell(16 ,5, $lang['10137'], 1, 1 , 'C' , false);
	
	
	/*Se pone el aparatado del equipo Visitante 3er. set*/
		/*1ra. Fila de Equipo Visitante*/
		
			$pdf->SetFont('Times' , 'B' , 7);
			$pdf->SetXY(84,+78);
			$pdf->Cell(8 , 5, $lang['10501'], 1, 0 , 'C' , false);
	        
	        $pdf->SetXY(92,+78);
			$pdf->Cell(8 , 5, $lang['10502'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(100,+78);
			$pdf->Cell(8 , 5, $lang['10503'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(108,+78);
			$pdf->Cell(8 , 5, $lang['10504'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(116,+78);
			$pdf->Cell(8 , 5, $lang['10505'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(124,+78);
			$pdf->Cell(8 , 5, $lang['10506'], 1, 0 , 'C' , false);
			
	/*2da. Fila de Equipo Visitante*/
	
			$pdf->SetFont('Times' , 'B' , 7);
			$pdf->SetXY(84,+83);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
	        
	        $pdf->SetXY(92,+83);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(100,+83);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(108,+83);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(116,+83);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(124,+83);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
	
	/*3ra. Fila de Equipo Visitante*/
	
			$pdf->SetFont('Times' , 'B' , 7);
			$pdf->SetXY(84,+88);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
	        
	        $pdf->SetXY(92,+88);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(100,+88);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(108,+88);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(116,+88);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(124,+88);
			$pdf->Cell(8 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
		/*4a. Fila de Equipo Visitante*/
	
			$pdf->SetFont('Times' , 'B' , 7);
			$pdf->SetXY(84,+93);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
	        
	        $pdf->SetXY(92,+93);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(100,+93);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(108,+93);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(116,+93);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(124,+93);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
		
		/*5a. Fila de Equipo Visitante*/
	
			$pdf->SetFont('Times' , 'B' , 7);
			$pdf->SetXY(84,+98);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
	        
	        $pdf->SetXY(92,+98);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(100,+98);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(108,+98);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(116,+98);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(124,+98);
			$pdf->Cell(8 , 5, $lang['10137'], 1, 0 , 'C' , false);
	
		/*6a. Fila de Equipo Visitante*/
	        $pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+84.5,$y+102.5);
			$pdf->Cell(4 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+84,$y+104);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+88.5,$y+102.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+88,$y+104);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+92.5,$y+102.5);
			$pdf->Cell(4 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+92,$y+104);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+96.5,$y+102.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+96,$y+104);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+100.5,$y+102.5);
			$pdf->Cell(4 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+100,$y+104);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+104.5,$y+102.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+104,$y+104);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+108.5,$y+102.5);
			$pdf->Cell(4 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+108,$y+104);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+112.5,$y+102.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+112,$y+104);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+116.5,$y+102.5);
			$pdf->Cell(4 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+116,$y+104);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+120.5,$y+102.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+120,$y+104);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+124.5,$y+102.5);
			$pdf->Cell(4 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+124,$y+104);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+128.5,$y+102.5);
			$pdf->Cell(4, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+128,$y+104);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			  /* 7a. Fila Equipo Visitante 1er. set */
		  
	       $pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+84.5,$y+107.5);
			$pdf->Cell(4 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+84,$y+109);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+88.5,$y+107.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+88,$y+109);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+92.5,$y+107.5);
			$pdf->Cell(4 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+92,$y+109);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+96.5,$y+107.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+96,$y+109);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+100.5,$y+107.5);
			$pdf->Cell(4 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+100,$y+109);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+104.5,$y+107.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+104,$y+109);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+108.5,$y+107.5);
			$pdf->Cell(4 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+108,$y+109);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+112.5,$y+107.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+112,$y+109);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+116.5,$y+107.5);
			$pdf->Cell(4 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+116,$y+109);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+120.5,$y+107.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+120,$y+109);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+124.5,$y+107.5);
			$pdf->Cell(4 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+124,$y+109);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+128.5,$y+107.5);
			$pdf->Cell(4, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+128,$y+109);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);

	/* 8a. Fila Equipo Visitante 1er. set */
		  
	       $pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+84.5,$y+112.5);
			$pdf->Cell(4 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+84,$y+114);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+88.5,$y+112.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+88,$y+114);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+92.5,$y+112.5);
			$pdf->Cell(4 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+92,$y+114);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+96.5,$y+112.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+96,$y+114);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+100.5,$y+112.5);
			$pdf->Cell(4 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+100,$y+114);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+104.5,$y+112.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+104,$y+114);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+108.5,$y+112.5);
			$pdf->Cell(4 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+108,$y+114);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+112.5,$y+112.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+112,$y+114);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+116.5,$y+112.5);
			$pdf->Cell(4 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+116,$y+114);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+120.5,$y+112.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+120,$y+114);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+124.5,$y+112.5);
			$pdf->Cell(4 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+124,$y+114);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+128.5,$y+112.5);
			$pdf->Cell(4, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+128,$y+114);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			/* 9a. Fila Equipo Visitante 3er. set */
		  
	       $pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+84.5,$y+117.5);
			$pdf->Cell(4 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+84,$y+119);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+88.5,$y+117.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+88,$y+119);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+92.5,$y+117.5);
			$pdf->Cell(4 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+92,$y+119);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+96.5,$y+117.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+96,$y+119);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+100.5,$y+117.5);
			$pdf->Cell(4 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+100,$y+119);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+104.5,$y+117.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+104,$y+119);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+108.5,$y+117.5);
			$pdf->Cell(4 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+108,$y+119);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+112,$y+117.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+112,$y+119);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+116.5,$y+117.5);
			$pdf->Cell(4 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+116,$y+119);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+120.5,$y+117.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+120,$y+119);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+124.5,$y+117.5);
			$pdf->Cell(4 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+124,$y+119);
			$pdf->Cell(4 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+128.5,$y+117.5);
			$pdf->Cell(4, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+128,$y+119);
			$pdf->Cell(4, 5, $lang['10136'], 1, 0, 'R' , false);
			
			
			/* Puntos 3er.set */
			$pdf->SetFont('Times' , '' , 6);
            $pdf->SetXY(132,78);
			$pdf->Cell(3.7 , 3.3,$lang['10509'], 1, 0 , 'C' , false);
			$pdf->SetXY(135.7,78);
			$pdf->Cell(3.7 , 3.3,$lang['10521'], 1, 0 , 'C' , false);
			$pdf->SetXY(139.4,78);
			$pdf->Cell(3.7 , 3.3,$lang['10532'], 1, 0 , 'C' , false);
			$pdf->SetXY(143.10,78);
			$pdf->Cell(3.7 , 3.3,$lang['10543'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(132,81.3);
			$pdf->Cell(3.7 , 3.3,$lang['10510'], 1, 0 , 'C' , false);
			$pdf->SetXY(135.7,81.3);
			$pdf->Cell(3.7 , 3.3,$lang['10522'], 1, 0 , 'C' , false);
			$pdf->SetXY(139.4,81.3);
			$pdf->Cell(3.7 , 3.3,$lang['10533'], 1, 0 , 'C' , false);
			$pdf->SetXY(143.10,81.3);
			$pdf->Cell(3.7 , 3.3,$lang['10544'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(132,84.6);
			$pdf->Cell(3.7 , 3.3,$lang['10511'], 1, 0 , 'C' , false);
			$pdf->SetXY(135.7,84.6);
			$pdf->Cell(3.7 , 3.3,$lang['10523'], 1, 0 , 'C' , false);
			$pdf->SetXY(139.4,84.6);
			$pdf->Cell(3.7 , 3.3,$lang['10534'], 1, 0 , 'C' , false);
			$pdf->SetXY(143.10,84.6);
			$pdf->Cell(3.7 , 3.3,$lang['10545'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(132,88);
			$pdf->Cell(3.7 , 3.3,$lang['10512'], 1, 0 , 'C' , false);
			$pdf->SetXY(135.7,88);
			$pdf->Cell(3.7 , 3.3,$lang['10524'], 1, 0 , 'C' , false);
			$pdf->SetXY(139.4,88);
			$pdf->Cell(3.7 , 3.3,$lang['10535'], 1, 0 , 'C' , false);
			$pdf->SetXY(143.10,88);
			$pdf->Cell(3.7 , 3.3,$lang['10546'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(132,91.2);
			$pdf->Cell(3.7 , 3.3,$lang['10513'], 1, 0 , 'C' , false);
			$pdf->SetXY(135.7,91.2);
			$pdf->Cell(3.7 , 3.3,$lang['10525'], 1, 0 , 'C' , false);
			$pdf->SetXY(139.4,91.2);
			$pdf->Cell(3.7 , 3.3,$lang['10536'], 1, 0 , 'C' , false);
			$pdf->SetXY(143.10,91.2);
			$pdf->Cell(3.7 , 3.3,$lang['10547'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(132,94.5);
			$pdf->Cell(3.7 , 3.3,$lang['10514'], 1, 0 , 'C' , false);
			$pdf->SetXY(135.7,94.5);
			$pdf->Cell(3.7 , 3.3,$lang['10526'], 1, 0 , 'C' , false);
			$pdf->SetXY(139.4,94.5);
			$pdf->Cell(3.7 , 3.3,$lang['10537'], 1, 0 , 'C' , false);
			$pdf->SetXY(143.10,94.5);
			$pdf->Cell(3.7 , 3.3,$lang['10548'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(132,97.8);
			$pdf->Cell(3.7 , 3.3,$lang['10515'], 1, 0 , 'C' , false);
			$pdf->SetXY(135.7,97.8);
			$pdf->Cell(3.7 , 3.3,$lang['10527'], 1, 0 , 'C' , false);
			$pdf->SetXY(139.4,97.8);
			$pdf->Cell(3.7 , 3.3,$lang['10538'], 1, 0 , 'C' , false);
			$pdf->SetXY(143.10,97.8);
			$pdf->Cell(3.7 , 3.3,$lang['10549'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(132,101.1);
			$pdf->Cell(3.7 , 3.3,$lang['10516'], 1, 0 , 'C' , false);
			$pdf->SetXY(135.7,101.1);
			$pdf->Cell(3.7 , 3.3,$lang['10528'], 1, 0 , 'C' , false);
			$pdf->SetXY(139.4,101.1);
			$pdf->Cell(3.7 , 3.3,$lang['10539'], 1, 0 , 'C' , false);
			$pdf->SetXY(143.10,101.1);
			$pdf->Cell(3.7 , 3.3,$lang['10550'], 1, 0 , 'C' , false);
			
				$pdf->SetXY(132,104.3);
			$pdf->Cell(3.7 , 3.3,$lang['10518'], 1, 0 , 'C' , false);
			$pdf->SetXY(135.7,104.3);
			$pdf->Cell(3.7 , 3.3,$lang['10529'], 1, 0 , 'C' , false);
			$pdf->SetXY(139.4,104.3);
			$pdf->Cell(3.7 , 3.3,$lang['10540'], 1, 0 , 'C' , false);
			$pdf->SetXY(143.10,104.3);
			$pdf->Cell(3.7 , 3.3,$lang['10551'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(132,107.4);
			$pdf->Cell(3.7 , 3.3,$lang['10519'], 1, 0 , 'C' , false);
			$pdf->SetXY(135.7,107.4);
			$pdf->Cell(3.7 , 3.3,$lang['10530'], 1, 0 , 'C' , false);
			$pdf->SetXY(139.4,107.4);
			$pdf->Cell(3.7 , 3.3,$lang['10541'], 1, 0 , 'C' , false);
			$pdf->SetXY(143.10,107.4);
			$pdf->Cell(3.7 , 3.3,$lang['10552'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(132,110.7);
			$pdf->Cell(3.7 , 3.3,$lang['10520'], 1, 0 , 'C' , false);
			$pdf->SetXY(135.7,110.7);
			$pdf->Cell(3.7 , 3.3,$lang['10531'], 1, 0 , 'C' , false);
			$pdf->SetXY(139.4,110.7);
			$pdf->Cell(3.7 , 3.3,$lang['10542'], 1, 0 , 'C' , false);
			$pdf->SetXY(143.10,110.7);
			$pdf->Cell(3.7 , 3.3,$lang['10553'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(132,114);
			$pdf->Cell(15 ,4.5, $lang['10137'], 1, 1 , 'C' , false);
			$pdf->SetXY(132,118.5);
			$pdf->Cell(15 ,4.5, $lang['10137'], 1, 1 , 'C' , false);
			
	/*1ra. Fila de  Cambio 3er set */
	
	       	$pdf->SetXY($x+147,$y+79);
			$pdf->Cell(4 , 45, $lang['10507'], 1, 1 , 'C' , false);
			
			$pdf->SetFont('Times' , 'B' , 7);
			$pdf->SetXY(151,+78);
			$pdf->Cell(7.5 , 5, $lang['10501'], 1, 0 , 'C' , false);
	        
	        $pdf->SetXY(158.5,+78);
			$pdf->Cell(7.5 , 5, $lang['10502'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(166,+78);
			$pdf->Cell(7.5 , 5, $lang['10503'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(173.5,+78);
			$pdf->Cell(7.5 , 5, $lang['10504'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(181,+78);
			$pdf->Cell(7.5 , 5, $lang['10505'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(188.5,+78);
			$pdf->Cell(7.5 , 5, $lang['10506'], 1, 0 , 'C' , false);
	
	/* 2da. Fila de Cambio 3er. set*/		
			
			$pdf->SetXY(151,+83);
			$pdf->Cell(7.5 , 5, $lang['10136'], 1, 0 , 'C' , false);
	        
	        $pdf->SetXY(158.5,+83);
			$pdf->Cell(7.5 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(166,+83);
			$pdf->Cell(7.5 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(173.5,+83);
			$pdf->Cell(7.5 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(181,+83);
			$pdf->Cell(7.5 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(188.5,+83);
			$pdf->Cell(7.5 , 5, $lang['10136'], 1, 0 , 'C' , false);
	
	/* 3ra. Fila de Cambio 3er. set*/		
			
			$pdf->SetXY(151,+88);
			$pdf->Cell(7.5 , 5, $lang['10136'], 1, 0 , 'C' , false);
	        
	        $pdf->SetXY(158.5,+88);
			$pdf->Cell(7.5 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(166,+88);
			$pdf->Cell(7.5 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(173.5,+88);
			$pdf->Cell(7.5 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(181,+88);
			$pdf->Cell(7.5 , 5, $lang['10136'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(188.5,+88);
			$pdf->Cell(7.5 , 5, $lang['10136'], 1, 0 , 'C' , false);	

	/* 4a. Fila de Cambio 3er. set*/		
			
			$pdf->SetXY(151,+93);
			$pdf->Cell(7.5 , 5, $lang['10137'], 1, 0 , 'C' , false);
	        
	        $pdf->SetXY(158.5,+93);
			$pdf->Cell(7.5 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(166,+93);
			$pdf->Cell(7.5 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(173.5,+93);
			$pdf->Cell(7.5 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(181,+93);
			$pdf->Cell(7.5 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(188.5,+93);
			$pdf->Cell(7.5 , 5, $lang['10137'], 1, 0 , 'C' , false);		

	/* 5a. Fila de Cambio 3er. set*/		
		
			$pdf->SetXY(151,+98);
			$pdf->Cell(7.5 , 5, $lang['10137'], 1, 0 , 'C' , false);
	        
	        $pdf->SetXY(158.5,+98);
			$pdf->Cell(7.5 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(166,+98);
			$pdf->Cell(7.5 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(173.5,+98);
			$pdf->Cell(7.5 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(181,+98);
			$pdf->Cell(7.5 , 5, $lang['10137'], 1, 0 , 'C' , false);
			
			$pdf->SetXY(188.5,+98);
			$pdf->Cell(7.5 , 5, $lang['10137'], 1, 0 , 'C' , false);	
	
	/*6a. Fila de Equipo Visitante*/
	        $pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+151.8,$y+102.5);
			$pdf->Cell(3.7 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+151,$y+104);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+154.9,$y+102.5);
			$pdf->Cell(3.7, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+154.7,$y+104);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+159.2,$y+102.5);
			$pdf->Cell(3.7 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+158.4,$y+104);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+162.5,$y+102.5);
			$pdf->Cell(3.7, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+162.2,$y+104);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+166.5,$y+102.5);
			$pdf->Cell(3.7 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+165.8,$y+104);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+170.3,$y+102.5);
			$pdf->Cell(3.7, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+169.6,$y+104);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+174,$y+102.5);
			$pdf->Cell(3.7 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+173.4,$y+104);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+177.5,$y+102.5);
			$pdf->Cell(3.7, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+177,$y+104);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+181.5,$y+102.5);
			$pdf->Cell(3.7 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+180.9,$y+104);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	       $pdf->SetXY($x+185,$y+102.5);
			$pdf->Cell(3.7, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+184.5,$y+104);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+189,$y+102.5);
			$pdf->Cell(3.7 , 5, $lang['10509'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+188.4,$y+104);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+192.8,$y+102.5);
			$pdf->Cell(3.7, 5, $lang['10513'], 0, 0, 'R' , false);
			$pdf->SetXY($x+192.1,$y+104);
			$pdf->Cell(3.9, 5, $lang['10136'], 1, 0, 'R' , false);
			
	/*7a. Fila de Equipo Visitante*/
	        $pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+151.8,$y+107.5);
			$pdf->Cell(3.7 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+151,$y+109);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+154.9,$y+107.5);
			$pdf->Cell(3.7, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+154.7,$y+109);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+159.2,$y+107.5);
			$pdf->Cell(3.7 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+158.4,$y+109);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+162.5,$y+107.5);
			$pdf->Cell(3.7, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+162.2,$y+109);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+166.5,$y+107.5);
			$pdf->Cell(3.7 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+165.8,$y+109);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+170.3,$y+107.5);
			$pdf->Cell(3.7, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+169.6,$y+109);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+174,$y+107.5);
			$pdf->Cell(3.7 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+173.4,$y+109);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+177.5,$y+107.5);
			$pdf->Cell(3.7, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+177,$y+109);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+181.5,$y+107.5);
			$pdf->Cell(3.7 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+180.9,$y+109);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	       $pdf->SetXY($x+185,$y+107.5);
			$pdf->Cell(3.7, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+184.5,$y+109);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+189,$y+107.5);
			$pdf->Cell(3.7 , 5, $lang['10510'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+188.4,$y+109);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+192.8,$y+107.5);
			$pdf->Cell(3.7, 5, $lang['10514'], 0, 0, 'R' , false);
			$pdf->SetXY($x+192.1,$y+109);
			$pdf->Cell(3.9, 5, $lang['10136'], 1, 0, 'R' , false);
	
	/*8a. Fila de Equipo Visitante*/
	        $pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+151.8,$y+112.5);
			$pdf->Cell(3.7 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+151,$y+114);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+154.9,$y+112.5);
			$pdf->Cell(3.7, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+154.7,$y+114);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+159.2,$y+112.5);
			$pdf->Cell(3.7 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+158.4,$y+114);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+162.5,$y+112.5);
			$pdf->Cell(3.7, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+162.2,$y+114);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+166.5,$y+112.5);
			$pdf->Cell(3.7 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+165.8,$y+114);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+170.3,$y+112.5);
			$pdf->Cell(3.7, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+169.6,$y+114);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+174,$y+112.5);
			$pdf->Cell(3.7 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+173.4,$y+114);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+177.5,$y+112.5);
			$pdf->Cell(3.7, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+177,$y+114);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+181.5,$y+112.5);
			$pdf->Cell(3.7 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+180.9,$y+114);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	       $pdf->SetXY($x+185,$y+112.5);
			$pdf->Cell(3.7, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+184.5,$y+114);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+189,$y+112.5);
			$pdf->Cell(3.7 , 5, $lang['10511'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+188.4,$y+114);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+192.8,$y+112.5);
			$pdf->Cell(3.7, 5, $lang['10515'], 0, 0, 'R' , false);
			$pdf->SetXY($x+192.1,$y+114);
			$pdf->Cell(3.9, 5, $lang['10136'], 1, 0, 'R' , false);
	
	/*9a. Fila de Equipo Visitant 3er.set*/
	        $pdf->SetFont('Times' , 'B' , 5);
			$pdf->SetXY($x+151.8,$y+117.5);
			$pdf->Cell(3.7 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+151,$y+119);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+154.9,$y+117.5);
			$pdf->Cell(3.7, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+154.7,$y+119);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+159.2,$y+117.5);
			$pdf->Cell(3.7 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+158.4,$y+119);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+162.5,$y+117.5);
			$pdf->Cell(3.7, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+162.2,$y+119);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+166.5,$y+117.5);
			$pdf->Cell(3.7 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+165.8,$y+119);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+170.3,$y+117.5);
			$pdf->Cell(3.7, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+169.6,$y+119);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+174,$y+117.5);
			$pdf->Cell(3.7 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+173.4,$y+119);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+177.5,$y+117.5);
			$pdf->Cell(3.7, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+177,$y+119);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+181.5,$y+117.5);
			$pdf->Cell(3.7 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+180.9,$y+119);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	       $pdf->SetXY($x+185,$y+117.5);
			$pdf->Cell(3.7, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+184.5,$y+119);
			$pdf->Cell(3.7, 5, $lang['10136'], 1, 0, 'R' , false);
			
			$pdf->SetXY($x+189,$y+117.5);
			$pdf->Cell(3.7 , 5, $lang['10512'], 0, 0 , 'R' , false);
			$pdf->SetXY($x+188.4,$y+119);
			$pdf->Cell(3.7 , 5, $lang['10136'], 1, 1 , 'R' , false);
	
	        $pdf->SetXY($x+192.8,$y+117.5);
			$pdf->Cell(3.7, 5, $lang['10516'], 0, 0, 'R' , false);
			$pdf->SetXY($x+192.1,$y+119);
			$pdf->Cell(3.9, 5, $lang['10136'], 1, 0, 'R' , false);
			
	/*Termina Arreglo 3er. set*/
	
	
	
		    

		/*	$pdf->SetXY($x+42,$y+49.5);
			$pdf->Cell(10, 5, $lang['10137'], 1, 0, 'C' , false);
			$pdf->SetXY($x+52,$y+49.5);
			$pdf->Cell(10, 5, $lang['10137'], 1, 0, 'C' , false);
			$pdf->SetXY($x+62,$y+49.5);
			$pdf->Cell(10, 5, $lang['10137'], 1, 0, 'C' , false);
			$pdf->SetXY($x+72,$y+49.5);
			$pdf->Cell(10, 5, $lang['10137'], 1, 0, 'C' , false);
			$pdf->SetXY($x+82,$y+49.5);
			$pdf->Cell(10, 5, $lang['10137'], 1, 0, 'C' , false);*/
			
		
			if ($result->num_rows > 0) {
				// output data of each row
				while($row = $result->fetch_assoc()) {
					$colorR = 0;
					$colorG = 0;
					$colorB = 0;
					$date = DateTime::createFromFormat("d/m/Y", $row["Fecha_Nacimiento"]);
					$birthDate = explode("/", $date->format("m/d/Y"));
					//get age from date or birthdate
					$age = date("Y") - $birthDate[2];
					$Edad = $age;
					if($Edad >= 35){ 
						if($Edad >= 50){
							$colorR = 241;
							$colorG = 240;
							$colorB = 94;
						}else{
							$colorR = 75;
							$colorG = 225;
							$colorB = 218;
						}
					}else{
							$colorR = 249;
							$colorG = 86;
							$colorB = 147;
					}
					
					/*Equipo Local y visitante*/
					  /*Titulo Equipos */
		    $pdf->SetXY(213,73);
            $pdf->SetFont('Helvetica' , '' , 8);
            $pdf->SetTextColor(0, 0, 0);
	    	$pdf->Cell(60, 4, 'EQUIPOS' , 35, 0 , 'C' , false);
            $pdf->SetFont('Helvetica' , 'B' , 8);
		    $pdf->SetXY(210,73);
	    	$pdf->Cell(30, 4, 'A' , 35, 0 , 'C' , false);
		    $pdf->SetXY(243,73);
	    	$pdf->Cell(30, 4, 'B' , 35, 0 , 'C' , false);
		    
		    /*Equipos */
		    $pdf->SetXY(213,72);
            $pdf->SetFont('Helvetica' , '' , 10);
            $pdf->SetTextColor(0, 0, 0);
	    	$pdf->Cell(60, 11, '' , 1, 1 , 'C' , false);
	    	$pdf->SetXY(215,77);
	    	$pdf->MultiCell(30, 3, utf8_decode($row1["Local"]), 0 , 'C' , false);
	    	$pdf->SetXY(243,77);
	    	$pdf->MultiCell(30, 3, utf8_decode($row1["Visitante"]), 0 , 'C' , false);
	    	
			/*Color de la columna Número Jugador*/		
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetFont('Times' , 'B' , 8);
					//$pdf->SetXY($x+115,$y+83);
					//$pdf->Cell(16 , 4, '', 1, 1 , 'C' , false);
			$pdf->SetXY($x+213,$y+85);
			$pdf->SetFillColor($colorR ,$colorG, $colorB);
			$pdf->Cell(6 , 4, '' . utf8_decode($row["Numero"]) . '', 1, 1 , 'C' , 1);
		
			/*Datos del Apodo del Jugador Local*/		
			$pdf->SetXY($x+219,$y+85);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->Cell(24, 4, utf8_decode('' . utf8_decode($row["Apodo"]) . ''), 1, 0, 'L' , false);
			//$pdf->Cell(72, 4, utf8_decode('' . utf8_decode($row["Nombre"]) . ' ' . utf8_decode($row["Apellido_P"]) . ' ' . utf8_decode($row["Apellido_M"]) . ''), 1, 0, 'L' , false);
			$pdf->SetXY($x+219,$y+85);
			$pdf->Cell(24, 4, '', 1, 0, 'L' , false);
		
			/*Visitante*/
			$pdf->SetXY($x+243,$y+85);
			$pdf->SetFillColor($colorR ,$colorG, $colorB);
			$pdf->Cell(6 , 4, '' . utf8_decode($row["Numero"]) . '', 1, 1 , 'C' , 1);
			
			$pdf->SetXY($x+249,$y+85);
			$pdf->Cell(24, 4, '', 1, 0, 'L' , false);
		//	$pdf->SetXY($x+212,$y+51);
		//	$pdf->Cell(25, 4, '', 1, 0, 'L' , false);
		//	$pdf->SetXY($x+230,$y+51);
		//	$pdf->Cell(25, 4, '', 1, 0, 'L' , false);
			$y= $y+4;
					
		//				$pdf->SetXY(195, 32);
		//	$pdf->Cell(90 , 8, $lang['990'] . '  ________ ' . $lang['989'] . ' _______', 35, 0 , 'L' , false);
			
				}
			}
			$y=-5;
			$x=$x+130	;
			
			$sql = "select * from (
                    				SELECT Jugador_ID,
                    					Clave,
                    					Nombre,
                    					Apellido_P,
                    					Apellido_M,
                    					Apodo,
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
                    				where Estatus = 'A' and Validado = 1 and Equipo_ID = $visitanteid
                    				union
                    				SELECT Jugador_ID,
                    					Clave,
                    					Nombre,
                    					Apellido_P,
                    					Apellido_M,
                    					Apodo,
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
        								case when Sexo = 0 then '" . $lang["942"] . "'
        									when Sexo = 1 then '" . $lang["943"] . "'
        									end SexoT,
        								Sexo
                                        Validado,
                                        FechaAlta
                    				FROM Jugadores a
                    				where Estatus = 'A' and Validado = 0 and Equipo_ID = $visitanteid and FechaAlta >= DATE_ADD(CURDATE(), interval -15 day)) a
                    order by                         
                    	EdadGrupo asc, convert(Numero,unsigned) asc
                    limit 35";
			$result = $Config->query($sql);
			$pages = 0;
		
		/*Se comenta por el momento */
/*			$pdf->SetFont('Times' , 'B' , 14);
			$pdf->SetXY($x+10,$y+46);
			$pdf->Cell(128 , 5, mb_strtoupper (utf8_decode('' . utf8_decode($row1["Visitante"]) . '')), 1, 1 , 'C' , false);
			$pdf->SetFont('Times' , '' , 10);
			$y = $y + 4;
			$pdf->SetXY($x+10,$y+47);
			$pdf->Cell(16 , 4, 'daaaa' . $lang['991'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+26,$y+47);
			$pdf->Cell(8 , 4, $lang['992'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+34,$y+47);
			$pdf->Cell(72, 4, $lang['993'], 1, 0, 'L' , false);
			$pdf->SetXY($x+106,$y+47);
			$pdf->Cell(8, 4, $lang['994'], 1, 0, 'L' , false);
			$pdf->SetXY($x+114,$y+47);
			$pdf->Cell(8, 4, $lang['995'], 1, 0, 'L' , false);
			$pdf->SetXY($x+122,$y+47);
			$pdf->Cell(8, 4, $lang['996'], 1, 0, 'L' , false);
			$pdf->SetXY($x+130,$y+47);
			$pdf->Cell(8, 4, $lang['995'], 1, 0, 'L' , false);
			$y= $y+4;
		*/
			if ($result->num_rows > 0) {
				// output data of each row
				while($row = $result->fetch_assoc()) {
					$colorR = 0;
					$colorG = 0;
					$colorB = 0;
					$date = DateTime::createFromFormat("d/m/Y", $row["Fecha_Nacimiento"]);
					$birthDate = explode("/", $date->format("m/d/Y"));
					//get age from date or birthdate
					$age = date("Y") - $birthDate[2];
					$Edad = $age;
					if($Edad >= 35){ 
						if($Edad >= 50){
							$colorR = 241;
							$colorG = 240;
							$colorB = 94;
						}else{
							$colorR = 75;
							$colorG = 225;
							$colorB = 218;
						}
					}else{
							$colorR = 249;
							$colorG = 86;
							$colorB = 147;
					}
					$pdf->SetFillColor(255, 255, 255);
					$pdf->SetFont('Times' , '' , 10);
					$pdf->SetXY($x+10,$y+47);
					$pdf->Cell(16 , 4, '', 1, 1 , 'C' , false);
					$pdf->SetXY($x+26,$y+47);
					$pdf->SetFillColor($colorR ,$colorG, $colorB);
					$pdf->Cell(8 , 4, '' . utf8_decode($row["Numero"]) . '', 1, 1 , 'C' , 1);
					$pdf->SetXY($x+34,$y+47);
					$pdf->SetFillColor(255, 255, 255);
					$pdf->Cell(72, 4, utf8_decode('' . utf8_decode($row["Nombre"]) . ' ' . utf8_decode($row["Apellido_P"]) . ' ' . utf8_decode($row["Apellido_M"]) . ''), 1, 0, 'L' , false);
					$pdf->SetXY($x+106,$y+47);
					$pdf->Cell(8, 4, '', 1, 0, 'L' , false);
					$pdf->SetXY($x+114,$y+47);
					$pdf->Cell(8, 4, '', 1, 0, 'L' , false);
					$pdf->SetXY($x+122,$y+47);
					$pdf->Cell(8, 4, '', 1, 0, 'L' , false);
					$pdf->SetXY($x+130,$y+47);
					$pdf->Cell(8, 4, '', 1, 0, 'L' , false);
					
						/*Visitante*/
		    /*    	$pdf->SetXY($x+145,$y+83);
			        $pdf->SetFillColor($colorR ,$colorG, $colorB);
			        $pdf->Cell(6 , 4, '' . utf8_decode($row["Numero"]) . '', 1, 1 , 'C' , 1);
			*/
			
					$y= $y+4;
				}
			} else {
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Helvetica' , 'B' , 12);
		//		$pdf->Cell(200 , 8, 'No hay Jugadores dados de Alta o aun no han sido Validados', 0, 0 , 'C' , false);
			}
			$pdf->SetFont('Times' , 'B' , 18);
			$pdf->SetXY(10,202);
			$pdf->Cell(260 , 4, '(      ) _____________ ' . $lang['997'] . ' _____________ (      )', 0, 1 , 'C' , false);
			
			$pdf->AddPage();
			$x = 0;
			$y = 0;
			$pdf->SetFont('Times' , '' , 14);
			$pdf->SetXY(10,10);
			$pdf->Cell(260 , 4, $lang['9997'], 0, 1 , 'C' , false);
			$pdf->SetXY($x+10,$y+20);
			$pdf->Cell(128 , 4, mb_strtoupper(utf8_decode('' . utf8_decode($row1["Local"]) . ' (' . $lang['998'] . ')')), 0, 1 , 'l' , false);
			$pdf->SetXY($x+10,$y+30);
			$pdf->Cell(128 , 4,  $lang['9991'], 0, 1 , 'l' , false);
			$pdf->SetXY($x+10,$y+40);
			$pdf->Cell(128 , 7, '', 1, 1 , 'l' , false);
			$pdf->SetXY($x+10,$y+47);
			$pdf->Cell(128 , 7, '', 1, 1 , 'l' , false);
			$pdf->SetXY($x+10,$y+54);
			$pdf->Cell(128 , 7, '', 1, 1 , 'l' , false);
			$pdf->SetXY($x+10,$y+61);
			$pdf->Cell(128 , 7, '', 1, 1 , 'l' , false);
			$pdf->SetXY($x+10,$y+68);
			$pdf->Cell(128 , 7, '', 1, 1 , 'l' , false);
			$pdf->SetXY($x+10,$y+75);
			$pdf->Cell(128 , 7, '', 1, 1 , 'l' , false);
			$y = $y + 5;
			$pdf->SetXY($x+10,$y+82);
			$pdf->Cell(128 , 4, $lang['9992'], 0, 1 , 'l' , false);
			$pdf->SetXY($x+10,$y+89);
			$pdf->Cell(128 , 7, '', 1, 1 , 'l' , false);
			$pdf->SetXY($x+10,$y+96);
			$pdf->Cell(128 , 7, '', 1, 1 , 'l' , false);
			$pdf->SetXY($x+10,$y+103);
			$pdf->Cell(128 , 7, '', 1, 1 , 'l' , false);
			$pdf->SetXY($x+10,$y+110);
			$pdf->Cell(128 , 7, '', 1, 1 , 'l' , false);
			$pdf->SetXY($x+10,$y+117);
			$pdf->Cell(128 , 7, '', 1, 1 , 'l' , false);
			$pdf->SetXY($x+10,$y+124);
			$pdf->Cell(128 , 7, '', 1, 1 , 'l' , false);
			$y = 0;
			$x = $x + 130;
			$pdf->SetXY($x+10,$y+20);
			$pdf->Cell(128 , 4, mb_strtoupper (utf8_decode('' . utf8_decode($row1["Visitante"]) . ' (' . $lang['999'] . ')')), 0, 1 , 'l' , false);
			$pdf->SetXY($x+10,$y+30);
			$pdf->Cell(128 , 4, $lang['9991'], 0, 1 , 'l' , false);
			$pdf->SetXY($x+10,$y+40);
			$pdf->Cell(128 , 7, '', 1, 1 , 'l' , false);
			$pdf->SetXY($x+10,$y+47);
			$pdf->Cell(128 , 7, '', 1, 1 , 'l' , false);
			$pdf->SetXY($x+10,$y+54);
			$pdf->Cell(128 , 7, '', 1, 1 , 'l' , false);
			$pdf->SetXY($x+10,$y+61);
			$pdf->Cell(128 , 7, '', 1, 1 , 'l' , false);
			$pdf->SetXY($x+10,$y+68);
			$pdf->Cell(128 , 7, '', 1, 1 , 'l' , false);
			$pdf->SetXY($x+10,$y+75);
			$pdf->Cell(128 , 7, '', 1, 1 , 'l' , false);
			$y = $y + 5;
			$pdf->SetXY($x+10,$y+82);
			$pdf->Cell(128 , 4, $lang['9992'], 0, 1 , 'l' , false);
			$pdf->SetXY($x+10,$y+89);
			$pdf->Cell(128 , 7, '', 1, 1 , 'l' , false);
			$pdf->SetXY($x+10,$y+96);
			$pdf->Cell(128 , 7, '', 1, 1 , 'l' , false);
			$pdf->SetXY($x+10,$y+103);
			$pdf->Cell(128 , 7, '', 1, 1 , 'l' , false);
			$pdf->SetXY($x+10,$y+110);
			$pdf->Cell(128 , 7, '', 1, 1 , 'l' , false);
			$pdf->SetXY($x+10,$y+117);
			$pdf->Cell(128 , 7, '', 1, 1 , 'l' , false);
			$pdf->SetXY($x+10,$y+124);
			$pdf->Cell(128 , 7, '', 1, 1 , 'l' , false);
			$pdf->SetXY(10,$y+135);
			
			$pdf->Cell(128 , 7, $lang['9993'], 0, 1 , 'l' , false);
			$pdf->SetXY(10,$y+142);
			$pdf->Cell(258 , 7, '', 1, 1 , 'l' , false);
			$pdf->SetXY(10,$y+149);
			$pdf->Cell(258 , 7, '', 1, 1 , 'l' , false);
			$pdf->SetXY(10,$y+156);
			$pdf->Cell(258 , 7, '', 1, 1 , 'l' , false);
			$pdf->SetXY(10,$y+163);
			$pdf->Cell(258 , 7, '', 1, 1 , 'l' , false);
			$pdf->SetXY(10,$y+170);
			$pdf->Cell(258 , 7, '', 1, 1 , 'l' , false);
			$pdf->SetXY(10,$y+179);
			$pdf->Cell(258 , 7, $lang['9994'], 0, 1 , 'l' , false);
			$pdf->SetXY(10,197);
			$pdf->Cell(50 , 4, '____________________', 0, 1 , 'L' , false);
			$pdf->SetXY(115,197);
			$pdf->Cell(50 , 4, '____________________', 0, 1 , 'L' , false);
			$pdf->SetXY(218,197);
			$pdf->Cell(50 , 4, '____________________', 0, 1 , 'L' , false);
			$pdf->SetXY(10,204);
			$pdf->Cell(50 , 4, $lang['9995'], 0, 1 , 'C' , false);
			$pdf->SetXY(115,204);
			$pdf->Cell(50 , 4, 'Arbitro', 0, 1 , 'C' , false);
			$pdf->SetXY(218,204);
			$pdf->Cell(50 , 4, $lang['9996'], 0, 1 , 'C' , false);
		} 
	}else {
		$pdf->Cell(200 , 8, $lang['9998'], 0, 0 , 'C' , false);
	}
	$Config->close();

	$pdf->Output();
?>