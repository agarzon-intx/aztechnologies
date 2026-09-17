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
	
	$siteRoot = az_pdf_site_root($Config);

	$Config->LoadLogo();
	$Config->LoadFlags();
	
	$pdf = new FPDF('P','mm','Letter');
	
	$sql = "select dc.Categoria_DESC, b.Jornada_DescCorta, a.Juego_ID, a.Local_ID, d.Equipo_FULLDESC as Local, a.Visitante_ID, f.Equipo_FULLDESC as Visitante, b.Fecha, day(b.Fecha) Dia, 
					ELT(DATE_FORMAT(b.Fecha,'%m'),'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre')  Mes, year(b.Fecha) Anio, a.Campo_ID, 
					case when c.Campo_DESC is null then e.Campo_DESC else c.Campo_DESC end Campo_DESC, g.Torneo_Desc, DATE_FORMAT(b.Fecha, '%W %d %M %Y') Fecha_String, DATE_FORMAT(a.Horario, '%l:%i %p') Hora_String
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
	//echo $sql;
	$result1 = $Config->query($sql);
	if ($result1->num_rows > 0) {
		// output data of each row
		while($row1 = $result1->fetch_assoc()) {
			$localid = az_utf8_decode($row1["Local_ID"]);
			$visitanteid = az_utf8_decode($row1["Visitante_ID"]);
			$x = 0;
			$y = 0;
			$col = 0;
			$rowc = 0;
		
			$pdf->AddPage();
			$pdf->SetAutoPageBreak(false,1);
			$pdf->SetMargins(5, 5, 5, 5);	
			$pdf->SetXY(0,0);
			az_pdf_image_file($pdf, $siteRoot, '/imagenes/' . $Config->logo . '.png', 5+((35 - (35 * ($Config->logowidth / 110)))/2),5+((35 - (35 * ($Config->logoheight / 110)))/2),(35 * ($Config->logowidth / 110)), (35 * ($Config->logoheight / 110)));
			$pdf->SetFont('Helvetica' , 'B' , 14);
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetXY(40,5);
			$pdf->Cell(60 , 8, $Config->liga, 35, 0 , 'L' , false);
			$pdf->SetXY(120,10);
			$pdf->SetFont('Helvetica' , '' , 12);
			$pdf->Cell(90 , 8, az_utf8_decode($row1["Fecha_String"]) . ' a las ' . az_utf8_decode($row1["Hora_String"]) . $lang['985'] . ' ' . az_utf8_decode($row1["Campo_DESC"]) . '', 35, 0 , 'R' , false);
			$pdf->SetXY(120,5);
			$pdf->SetFont('Helvetica' , '' , 10);
			$pdf->Cell(90 , 3, $lang['986'] . ' ' . az_utf8_decode($row1["Jornada_DescCorta"]) . '', 35, 0 , 'R' , false);
			$pdf->SetXY(40,19);
			$pdf->Cell(90 , 8, $lang['987'], 35, 0 , 'L' , false);
			$pdf->SetXY(65,19);
			$pdf->Cell(90 , 8, '_________________________________________________________________________', 35, 0 , 'L' , false);
			$pdf->SetXY(40,27);
			$pdf->SetFont('Helvetica' , 'B' , 10);
			$pdf->Cell(85 , 8, $lang['0'] . ': "' . az_utf8_decode($row1["Torneo_Desc"]) . '"', 35, 0 , 'L' , false);
			$pdf->SetXY(125,27);
			$pdf->SetFont('Helvetica' , 'B' , 10);
			$pdf->Cell(85 , 8, $lang['1'] . ': "' . az_utf8_decode($row1["Categoria_DESC"]) . '"', 35, 0 , 'R' , false);
			
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
                    					convert(Numero,unsigned) Numero,
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
                    					convert(Numero,unsigned) Numero,
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
                    				where Estatus = 'A' and Validado = 0 and Equipo_ID = $localid and FechaAlta >= DATE_ADD(CURDATE(), interval -21 day)) a
                    order by                         
                    	convert(Numero,unsigned) asc, Nombre, Apellido_P, Apellido_M
                    limit 30";
			$result = $Config->query($sql);
			$pages = 0;
			
			$y = $y -5;
			$pdf->SetFont('Times' , 'B' , 14);
			$pdf->SetXY($x+6,$y+46);
			$pdf->Cell(100 , 5, mb_strtoupper(az_utf8_decode('' . az_utf8_decode($row1["Local"]) . '')), 1, 1 , 'C' , false);
			$pdf->SetFont('Times' , '' , 10);
			$y = $y + 4;
			$pdf->SetXY($x+6,$y+47);
			$pdf->Cell(100 , 124, '', 1, 1 , 'C' , false);
			$pdf->SetXY($x+6,$y+47);
			$pdf->Cell(6 , 4, '#', 1, 1 , 'C' , false);
			$pdf->SetXY($x+12,$y+47);
			$pdf->Cell(62, 4, $lang['993'], 1, 0, 'L' , false);
			$pdf->SetXY($x+74,$y+47);
			$pdf->Cell(8, 4, 'Jug', 1, 0, 'C' , false);
			$pdf->SetXY($x+82,$y+47);
			$pdf->Cell(8, 4, $lang['991'], 1, 0, 'C' , false);
			$pdf->SetXY($x+90,$y+47);
			$pdf->Cell(8, 4, 'Amo', 1, 0, 'C' , false);
			$pdf->SetXY($x+98,$y+47);
			$pdf->Cell(8, 4, 'Roj', 1, 0, 'C' , false);
			$y= $y+4;
		
			if ($result->num_rows > 0) {
				// output data of each row
				while($row = $result->fetch_assoc()) {
					$colorR = 0;
					$colorG = 0;
					$colorB = 0;
					$Edad = $row["Edad"];
					$pdf->SetFillColor(255, 255, 255);
					$pdf->SetFont('Times' , '' , 10);
					$pdf->SetXY($x+6,$y+47);
					$pdf->Cell(6 , 4, '' . az_utf8_decode($row["Numero"]) . '', 1, 1 , 'C' , false);
					$pdf->SetXY($x+12,$y+47);
					$pdf->Cell(62, 4, '' . az_utf8_decode($row["Nombre"]) . ' ' . az_utf8_decode($row["Apellido_P"]) . ' ' . az_utf8_decode($row["Apellido_M"]) . '', 1, 0, 'L' , false);
					$pdf->SetXY($x+74,$y+47);
					$pdf->Cell(8, 4, '', 1, 0, 'C' , false);
					$pdf->SetXY($x+82,$y+47);
					$pdf->Cell(8, 4, '', 1, 0, 'C' , false);
					$pdf->SetXY($x+90,$y+47);
					$pdf->Cell(8, 4, '', 1, 0, 'C' , false);
					$pdf->SetXY($x+98,$y+47);
					$pdf->Cell(8, 4, '', 1, 0, 'C' , false);
					$y= $y+4;
				}
			}
			$y=-5;
			$x=$x+100	;
			
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
                    					convert(Numero,unsigned) Numero,
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
                    					convert(Numero,unsigned) Numero,
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
                    				where Estatus = 'A' and Validado = 0 and Equipo_ID = $visitanteid and FechaAlta >= DATE_ADD(CURDATE(), interval -21 day)) a
                    order by                         
                    	convert(Numero,unsigned) asc, Nombre, Apellido_P, Apellido_M
                    limit 30";
			$result = $Config->query($sql);
			$pages = 0;
		
			$pdf->SetFont('Times' , 'B' , 14);
			$pdf->SetXY($x+10,$y+46);
			$pdf->Cell(100 , 5, mb_strtoupper (az_utf8_decode('' . az_utf8_decode($row1["Visitante"]) . '')), 1, 1 , 'C' , false);
			$pdf->SetFont('Times' , '' , 10);
			$y = $y + 4;
			$pdf->SetXY($x+10,$y+47);
			$pdf->Cell(100 , 124, '', 1, 1 , 'C' , false);
			$pdf->SetXY($x+10,$y+47);
			$pdf->Cell(6 , 4, '#', 1, 1 , 'C' , false);
			$pdf->SetXY($x+16,$y+47);
			$pdf->Cell(62, 4, $lang['993'], 1, 0, 'L' , false);
			$pdf->SetXY($x+78,$y+47);
			$pdf->Cell(8, 4, 'Jug', 1, 0, 'C' , false);
			$pdf->SetXY($x+86,$y+47);
			$pdf->Cell(8, 4, $lang['991'], 1, 0, 'C' , false);
			$pdf->SetXY($x+94,$y+47);
			$pdf->Cell(8, 4, 'Amo', 1, 0, 'C' , false);
			$pdf->SetXY($x+102,$y+47);
			$pdf->Cell(8, 4, 'Roj', 1, 0, 'C' , false);
			$y= $y+4;
		
			if ($result->num_rows > 0) {
				// output data of each row
				while($row = $result->fetch_assoc()) {
					$colorR = 0;
					$colorG = 0;
					$colorB = 0;
					$Edad = $row["Edad"];
					$pdf->SetFillColor(255, 255, 255);
					$pdf->SetFont('Times' , '' , 10);
					$pdf->SetXY($x+10,$y+47);
					$pdf->Cell(6 , 4, '' . az_utf8_decode($row["Numero"]) . '', 1, 1 , 'C' , 1);
					$pdf->SetXY($x+16,$y+47);
					$pdf->Cell(62, 4, az_utf8_decode($row["Nombre"]) . ' ' . az_utf8_decode($row["Apellido_P"]) . ' ' . az_utf8_decode($row["Apellido_M"]), 1, 0, 'L' , false);
					$pdf->SetXY($x+78,$y+47);
					$pdf->Cell(8, 4, '', 1, 0, 'C' , false);
					$pdf->SetXY($x+86,$y+47);
					$pdf->Cell(8, 4, '', 1, 0, 'C' , false);
					$pdf->SetXY($x+94,$y+47);
					$pdf->Cell(8, 4, '', 1, 0, 'C' , false);
					$pdf->SetXY($x+102,$y+47);
					$pdf->Cell(8, 4, '', 1, 0, 'C' , false);
					$y= $y+4;
				}
			} else {
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Helvetica' , 'B' , 12);
				$pdf->Cell(200 , 8, 'No hay Jugadores dados de Alta o aun no han sido Validados', 0, 0 , 'C' , false);
			}
			$x = 0;
			$y = 170;
			$pdf->SetFont('Times' , '' , 12);
			/*
			$y = $y + 8;
			$pdf->SetXY($x+6,$y+0);
			$pdf->Cell(204 , 25, '', 1, 0 , 'C' , false);
			$pdf->SetXY($x+6,$y+0);
			$pdf->Cell(99 , 5, '', 1, 0 , 'C' , false);
			$pdf->SetXY($x+6,$y+5);
			$pdf->Cell(99 , 5, '', 1, 0 , 'l' , false);
			$pdf->SetXY($x+6,$y+10);
			$pdf->Cell(99 , 5, '', 1, 0 , 'l' , false);
			$pdf->SetXY($x+6,$y+15);
			$pdf->Cell(99 , 5, '', 1, 0 , 'l' , false);
			$pdf->SetXY($x+6,$y+20);
			$pdf->Cell(99 , 5, '', 1, 0 , 'l' , false);
			$pdf->SetXY($x+111,$y+0);
			$pdf->Cell(99 , 5, '', 1, 0 , 'C' , false);
			$pdf->SetXY($x+111,$y+5);
			$pdf->Cell(99 , 5, '', 1, 0 , 'l' , false);
			$pdf->SetXY($x+111,$y+10);
			$pdf->Cell(99 , 5, '', 1, 0 , 'l' , false);
			$pdf->SetXY($x+111,$y+15);
			$pdf->Cell(99 , 5, '', 1, 0 , 'l' , false);
			$pdf->SetXY($x+111,$y+20);
			$pdf->Cell(99 , 5, '', 1, 0 , 'l' , false);
			$pdf->SetTextColor(255, 0, 0);
			$pdf->SetXY($x+104,$y+0);
			$pdf->Cell(8 , 5, 'R', 0, 0 , 'C' , false);
			$pdf->SetXY($x+104,$y+5);
			$pdf->Cell(8 , 5, 'O', 0, 0 , 'C' , false);
			$pdf->SetXY($x+104,$y+10);
			$pdf->Cell(8 , 5, 'J', 0, 0 , 'C' , false);
			$pdf->SetXY($x+104,$y+15);
			$pdf->Cell(8 , 5, 'A', 0, 0 , 'C' , false);
			$pdf->SetXY($x+104,$y+20);
			$pdf->Cell(8 , 5, 'S', 0, 0 , 'C' , false);
			$pdf->SetTextColor(0, 0, 0);
			*/
			$y = $y - 2;
			$pdf->SetXY($x+6,$y+7);
			$pdf->Cell(204, 7, $lang['9993'], 0, 0 , 'l' , false);
			$pdf->SetXY($x+6,$y+7);
			$pdf->Cell(204, 7, '', 1, 0 , 'l' , false);
			$pdf->SetXY($x+6,$y+14);
			$pdf->Cell(204, 7, '', 1, 0 , 'l' , false);
			$pdf->SetXY($x+6,$y+21);
			$pdf->Cell(204, 7, '', 1, 0 , 'l' , false);
			$pdf->SetXY($x+6,$y+28);
			$pdf->Cell(204, 7, '', 1, 0 , 'l' , false);
			$pdf->SetXY($x+6,$y+35);
			$pdf->Cell(204,7, '', 1, 0 , 'l' , false);
			$pdf->SetXY($x+6,$y+42);
			$pdf->Cell(204, 7, '', 1, 0 , 'l' , false);
			$pdf->SetXY($x+6,$y+49);
			$pdf->Cell(204, 7, '', 1, 0 , 'l' , false);
			$pdf->SetXY($x+6,$y+56);
			$pdf->Cell(204, 7, '', 1, 0 , 'l' , false);
			
			$y = $y + 67;
			
			$pdf->SetFont('Times' , 'B' , 18);
			$pdf->SetXY($x+6,$y+10);
			$pdf->Cell(204 , 4, '(         ) _____________ MARCADOR _____________ (         )', 0, 1 , 'C' , false);
			
			$y = $y + 0;
			$pdf->SetFont('Times' , '' , 14);
			$pdf->SetXY($x+6,$y+25);
			$pdf->Cell(68 , 4, '____________________', 0, 1 , 'C' , false);
			$pdf->SetXY($x+74,$y+25);
			$pdf->Cell(68 , 4, '____________________', 0, 1 , 'C' , false);
			$pdf->SetXY($x+142,$y+25);
			$pdf->Cell(68 , 4, '____________________', 0, 1 , 'C' , false);
			$pdf->SetXY($x+6,$y+30);
			$pdf->Cell(68 , 4, $lang['9995'], 0, 1 , 'C' , false);
			$pdf->SetXY($x+74,$y+30);
			$pdf->Cell(68 , 4, 'Arbitro', 0, 1 , 'C' , false);
			$pdf->SetXY($x+142,$y+30);
			$pdf->Cell(68 , 4, $lang['9996'], 0, 1 , 'C' , false);
		} 
	}else {
		$pdf->Cell(200 , 8, $lang['9998'], 0, 0 , 'C' , false);
	}
	$Config->close();

	$pdf->Output();
?>
