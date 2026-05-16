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
	
	$pdf = new FPDF('P','mm','Letter');
	
	$sql = "select dc.Categoria_DESC, jor.Jornada_DescCorta, a.Juego_ID, a.Local_ID, d.Equipo_FULLDESC as Local, a.Visitante_ID, f.Equipo_FULLDESC as Visitante, a.Fecha, day(a.Fecha) Dia, 
					ELT(DATE_FORMAT(a.Fecha,'%m'),'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre')  Mes, year(a.Fecha) Anio, a.Campo_ID, 
					case when c.Campo_DESC is null then e.Campo_DESC else c.Campo_DESC end Campo_DESC, g.Torneo_Desc, DATE_FORMAT(a.Fecha, '%W, %M %d %Y') Fecha_String, DATE_FORMAT(a.Horario, '%h:%i %p') Horario
			from $schema.Juegos a
				join $schema.Jornada b on a.Fecha between b.Fecha_Inicio and b.Fecha_Fin
				left outer join $schema.Campos c on a.Campo_ID = c.Campo_ID
				join $schema.Equipos d on a.Torneo_ID = d.Torneo_ID and a.Local_ID = d.Equipo_ID 
				join $schema.Categorias dc on d.Fuerza = dc.Categoria_ID and dc.Torneo_ID = $torneo
				join $schema.Campos e on d.Campo_ID = e.Campo_ID
				join $schema.Equipos f on a.Torneo_ID = f.Torneo_ID and a.Visitante_ID = f.Equipo_ID 
				join $schema.Torneos g on a.Torneo_ID = g.Torneo_ID
				join $schema.Jornada jor on a.Jornada_ID = jor.Jornada_ID
			where a.Torneo_ID = $torneo and b.Jornada_ID = $jornada and ((weekday(a.Fecha) <> 2) or (weekday(a.Fecha) = (SELECT MarcadorDiaDefault-1 FROM $schema.Configuration) and a.Horario <> (SELECT MarcadorHoraDefault FROM $schema.Configuration)))
			order by a.Fecha, a.Horario, c.Campo_DESC, a.Juego_ID asc";
	//echo $sql;
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
			$pdf->SetMargins(5, 5, 5, 5);	
			$pdf->SetXY(0,0);
			$pdf->Image($server . '/imagenes/' . $Config->logo . '.png',5+((35 - (35 * ($Config->logowidth / 110)))/2),5+((35 - (35 * ($Config->logoheight / 110)))/2),(35 * ($Config->logowidth / 110)), (35 * ($Config->logoheight / 110)), 'PNG');
			$pdf->SetFont('Helvetica' , 'B' , 14);
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetXY(40,5);
			$pdf->Cell(60 , 8, utf8_decode($Config->liga), 35, 0 , 'L' , false);
			$pdf->SetXY(120,10);
			$pdf->SetFont('Helvetica' , '' , 12);
			$pdf->Cell(90 , 8, utf8_decode($row1["Fecha_String"]) . ' - ' . utf8_decode($row1["Horario"]) . ' ' . $lang['985'] . ' ' . utf8_decode($row1["Campo_DESC"]) . '', 35, 0 , 'R' , false);
			$pdf->SetXY(120,5);
			$pdf->SetFont('Helvetica' , '' , 10);
			$pdf->Cell(90 , 3, $lang['986'] . ' ' . utf8_decode($row1["Jornada_DescCorta"]) . '', 35, 0 , 'R' , false);
			$pdf->SetXY(40,19);
			$pdf->Cell(90 , 8, $lang['987'], 35, 0 , 'L' , false);
			$pdf->SetXY(65,19);
			$pdf->Cell(90 , 8, '_________________________________________________________________________', 35, 0 , 'L' , false);
			$pdf->SetXY(40,27);
			$pdf->SetFont('Helvetica' , 'B' , 10);
			$pdf->Cell(85 , 8, $lang['0'] . ': "' . utf8_decode($row1["Torneo_Desc"]) . '"', 35, 0 , 'L' , false);
			$pdf->SetXY(125,27);
			$pdf->SetFont('Helvetica' , 'B' , 10);
			$pdf->Cell(85 , 8, $lang['1'] . ': "' . utf8_decode($row1["Categoria_DESC"]) . '"', 35, 0 , 'R' , false);
			$pdf->SetFont('Helvetica' , '' , 10);
			$pdf->SetXY(40,32);
			$pdf->Cell(85 , 8, $lang['988'] . '   ________ ' . $lang['989'] . ' ________', 35, 0 , 'L' , false);
			$pdf->SetXY(125, 32);
			$pdf->Cell(85 , 8, $lang['990'] . '  ________ ' . $lang['989'] . ' ________', 35, 0 , 'R' , false);
			
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
                    				where Estatus = 'A' and Validado = 0 and Equipo_ID = $localid and FechaAlta >= DATE_ADD(CURDATE(), interval -21 day)) a
                    order by                         
                    	EdadGrupo asc, convert(Numero,unsigned) asc
                    limit 32";
			$result = $Config->query($sql);
			$pages = 0;
			
			$y = $y -5;
			$pdf->SetFont('Times' , 'B' , 12);
			$pdf->SetXY($x+6,$y+46);
			$pdf->Cell(100 , 5, mb_strtoupper('' . utf8_decode($row1["Local"]) . '', 'UTF-8'), 1, 1 , 'C' , false);
			$pdf->SetFont('Times' , '' , 10);
			$y = $y + 4;
			$pdf->SetXY($x+6,$y+47);
			$pdf->Cell(100 , 128, '', 1, 1 , 'C' , false);
			$pdf->SetXY($x+6,$y+47);
			$pdf->Cell(6 , 4, $lang['991'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+12,$y+47);
			$pdf->Cell(64, 4, $lang['993'], 1, 0, 'L' , false);
			$pdf->SetXY($x+76,$y+47);
			$pdf->Cell(7, 4, $lang['994'], 1, 0, 'L' , false);
			$pdf->SetXY($x+83,$y+47);
			$pdf->Cell(8, 4, $lang['995'], 1, 0, 'L' , false);
			$pdf->SetXY($x+91,$y+47);
			$pdf->Cell(7, 4, $lang['996'], 1, 0, 'L' , false);
			$pdf->SetXY($x+98,$y+47);
			$pdf->Cell(8, 4, $lang['995'], 1, 0, 'L' , false);
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
					$pdf->Cell(6 , 4, '', 1, 1 , 'C' , false);
					$pdf->SetXY($x+12,$y+47);
					$pdf->Cell(64, 4, '' . utf8_decode($row["Nombre"]) . ' ' . utf8_decode($row["Apellido_P"]) . ' ' . utf8_decode($row["Apellido_M"]) . '', 1, 0, 'L' , false);
					$pdf->SetXY($x+76,$y+47);
					$pdf->Cell(7, 4, '', 1, 0, 'L' , false);
					$pdf->SetXY($x+83,$y+47);
					$pdf->Cell(8, 4, '', 1, 0, 'L' , false);
					$pdf->SetXY($x+91,$y+47);
					$pdf->Cell(7, 4, '', 1, 0, 'L' , false);
					$pdf->SetXY($x+98,$y+47);
					$pdf->Cell(8, 4, '', 1, 0, 'L' , false);
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
                    				where Estatus = 'A' and Validado = 0 and Equipo_ID = $visitanteid and FechaAlta >= DATE_ADD(CURDATE(), interval -21 day)) a
                    order by                         
                    	EdadGrupo asc, convert(Numero,unsigned) asc
                    limit 37";
			$result = $Config->query($sql);
			$pages = 0;
		
			$pdf->SetFont('Times' , 'B' , 12);
			$pdf->SetXY($x+10,$y+46);
			$pdf->Cell(100 , 5, mb_strtoupper ('' . utf8_decode($row1["Visitante"]) . '', 'UTF-8'), 1, 1 , 'C' , false);
			$pdf->SetFont('Times' , '' , 10);
			$y = $y + 4;
			$pdf->SetXY($x+10,$y+47);
			$pdf->Cell(100 , 128, '', 1, 1 , 'C' , false);
			$pdf->SetXY($x+10,$y+47);
			$pdf->Cell(6 , 4, $lang['991'], 1, 1 , 'C' , false);
			$pdf->SetXY($x+16,$y+47);
			$pdf->Cell(64, 4, $lang['993'], 1, 0, 'L' , false);
			$pdf->SetXY($x+80,$y+47);
			$pdf->Cell(7, 4, $lang['994'], 1, 0, 'L' , false);
			$pdf->SetXY($x+87,$y+47);
			$pdf->Cell(8, 4, $lang['995'], 1, 0, 'L' , false);
			$pdf->SetXY($x+95,$y+47);
			$pdf->Cell(7, 4, $lang['996'], 1, 0, 'L' , false);
			$pdf->SetXY($x+102,$y+47);
			$pdf->Cell(8, 4, $lang['995'], 1, 0, 'L' , false);
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
					$pdf->Cell(6 , 4, '', 1, 1 , 'C' , 1);
					$pdf->SetXY($x+16,$y+47);
					$pdf->Cell(64, 4, '' . utf8_decode($row["Nombre"]) . ' ' . utf8_decode($row["Apellido_P"]) . ' ' . utf8_decode($row["Apellido_M"]) . '', 1, 0, 'L' , false);
					$pdf->SetXY($x+80,$y+47);
					$pdf->Cell(7, 4, '', 1, 0, 'L' , false);
					$pdf->SetXY($x+87,$y+47);
					$pdf->Cell(8, 4, '', 1, 0, 'L' , false);
					$pdf->SetXY($x+95,$y+47);
					$pdf->Cell(7, 4, '', 1, 0, 'L' , false);
					$pdf->SetXY($x+102,$y+47);
					$pdf->Cell(8, 4, '', 1, 0, 'L' , false);
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
			$pdf->SetXY($x+6,$y+5);
			$pdf->Cell(204 , 5, '', 1, 0 , 'C' , false);
			$pdf->SetXY($x+96,$y+5);
			$pdf->Cell(25 , 5, $lang['9991'], 1, 0 , 'C' , false);
			$y = $y + 5;
			$pdf->SetXY($x+6,$y+5);
			$pdf->Cell(204 , 5, '', 1, 0 , 'l' , false);
			$pdf->SetXY($x+96,$y+5);
			$pdf->Cell(25 , 5, $lang['9992'], 1, 0 , 'C' , false);
			$y = $y + 5;
			$pdf->SetXY($x+6,$y+5);
			$pdf->Cell(204 , 5, $lang['9993'], 1, 0 , 'l' , false);
			$pdf->SetXY($x+96,$y+5);
			$y = $y + 5;
			$pdf->SetXY($x+6,$y+5);
			$pdf->Cell(204 , 5, '', 1, 0 , 'l' , false);
			$pdf->SetXY($x+96,$y+5);
			$y = $y + 5;
			$pdf->SetXY($x+6,$y+5);
			$pdf->Cell(204 , 5, '', 1, 0 , 'l' , false);
			$pdf->SetXY($x+96,$y+5);
			$y = $y + 5;
			$pdf->SetXY($x+6,$y+5);
			$pdf->Cell(204 , 5, '', 1, 0 , 'l' , false);
			$pdf->SetXY($x+96,$y+5);
			$y = $y + 5;
			$pdf->SetXY($x+6,$y+5);
			$pdf->Cell(204 , 5, '', 1, 0 , 'l' , false);
			$pdf->SetXY($x+96,$y+5);
			$y = $y + 5;
			$pdf->SetXY($x+6,$y+5);
			$pdf->Cell(204 , 5, '', 1, 0 , 'l' , false);
			$pdf->SetXY($x+96,$y+5);
			$y = $y + 5;
			$pdf->SetXY($x+6,$y+5);
			$pdf->Cell(204 , 5, '', 1, 0 , 'l' , false);
			$pdf->SetXY($x+96,$y+5);
			$y = $y + 5;
			$pdf->SetXY($x+6,$y+5);
			$pdf->Cell(204 , 5, '', 1, 0 , 'l' , false);
			
			$pdf->SetFont('Times' , 'B' , 18);
			$pdf->SetXY($x+6,$y+20);
			$pdf->Cell(204 , 4, '(      ) _____________ ' . $lang['997'] . ' _____________ (      )', 0, 1 , 'C' , false);
			
			$y = $y + 20;
			$pdf->SetFont('Times' , '' , 14);
			$pdf->SetXY($x+6,$y+20);
			$pdf->Cell(68 , 4, '____________________', 0, 1 , 'C' , false);
			$pdf->SetXY($x+74,$y+20);
			$pdf->Cell(68 , 4, '____________________', 0, 1 , 'C' , false);
			$pdf->SetXY($x+142,$y+20);
			$pdf->Cell(68 , 4, '____________________', 0, 1 , 'C' , false);
			$pdf->SetXY($x+6,$y+30);
			$pdf->Cell(68 , 4, $lang['9995'], 0, 1 , 'C' , false);
			$pdf->SetXY($x+74,$y+30);
			$pdf->Cell(68 , 4, 'Arbitro', 0, 1 , 'C' , false);
			$pdf->SetXY($x+142,$y+30);
			$pdf->Cell(68 , 4, $lang['9996'], 0, 1 , 'C' , false);
			$pdf->SetFont('Times' , '' , 10);
			$pdf->SetXY($x+6,$y+35);
			$pdf->Cell(204 , 5, 'NOTA: En caso de inconformidad, firmar la cedula de juego bajo protesta, si no esta firmada perdera el derecho a replica.', 0, 0 , 'C' , false);
		} 
	}else {
		$pdf->Cell(200 , 8, $lang['9998'], 0, 0 , 'C' , false);
	}
	$Config->close();

	$pdf->Output();
?>
