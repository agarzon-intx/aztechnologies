<?php
	require_once dirname(__DIR__) . '/site_paths.php';
	set_time_limit(300);
	require('qrcode/qrcode.class.php');
	require("alphapdf.php");
	require("membersite_config.php");
	$Config = new Configuration();
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('registrosEquipoManual.php');
	$Config->connect();

	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	$folder = substr(substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT'])),1,strlen(substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT'])))-5);

	$torneo = $_COOKIE[$Config->getAlias() . 'season'];
	$categoria = $_COOKIE[$Config->getAlias() . 'category'];

	$edad1 = htmlspecialchars($_GET["Edad1"]);	
	$edad2 = htmlspecialchars($_GET["Edad2"]);	
	$imprimir = htmlspecialchars($_GET["Imprimir"]);

	$server = $fgmembersite->getSitename();
	
	$x = 0;
	$y = 0;
	$col = 0;
	$rowc = 0;

	$pdf = new AlphaPDF('P','mm','Letter');
	$pdf->SetAutoPageBreak(false);
	$pdf->AddPage();

	$Config->LoadLogo();
	$Config->connect();
	$_SESSION[$Config->getAlias() . 'printList'];

	$sql = "SELECT  Jugador_ID, 
	                Clave, Nombre, 
	                Apellido_P, 
	                Apellido_M, 
	                Apodo, 
	                date_format(Fecha_Nacimiento,'%d/%m/%Y') Fecha_Nacimiento, 
            		YEAR(CURDATE())-YEAR(Fecha_Nacimiento) Edad, 
            		Curp, Numero, 
            		Estatus, 
            		a.Equipo_ID, 
            		b.Equipo_FULLDESC, 
            		Comentarios, 
                    Telefono, correo, 
                    concat(b.Torneo_ID,'-', b.Equipo_ID) Logo, 
                    c.Categoria_Desc, 
                    c.Color, 
                    d.Color_HEX, 
                    date_format(CURDATE(),'%d %M %Y') FechaAlta, 
                    e.Torneo_Desc 
            FROM $schema.Jugadores a 
            	join $schema.Equipos b on a.Equipo_ID = b.Equipo_ID and b.Torneo_ID = (	select max(Torneo_ID) 
            																					from $schema.Equipos 
                                                                                                where Equipo_ID in (	SELECT Equipo_ID 
            																											FROM $schema.Jugadores
            																											where Jugador_ID in (" . $_SESSION[$Config->getAlias() . 'printList'] . "))) 
            	join $schema.Categorias c on b.Fuerza = c.Categoria_ID  and c.Torneo_ID = $torneo
                join $schema.Colores d on c.Color = d.Color_HEX 
                join $schema.Torneos e on b.Torneo_ID = e.Torneo_ID 
            where a.Jugador_ID in (" . $_SESSION[$Config->getAlias() . 'printList'] . ") 
            order by c.Categoria_Desc, b.Equipo_FULLDESC, convert(Numero,unsigned), Nombre, Apellido_P asc;";
	//echo $sql;
	$result = $Config->query($sql);
	$pages = 0;
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			try{
					
				if($col == 0 && $rowc == 0 && $pages > 0){
					$pdf->AddPage();
                    $x = 0;
                	$y = 0;
				}
				
				$colorR = 0;
				$colorG = 0;
				$colorB = 0;

				$date = DateTime::createFromFormat("d/m/Y", $row["Fecha_Nacimiento"]);
				$birthDate = explode("/", $date->format("m/d/Y"));
				//get age from date or birthdate
				$age = date("Y") - $birthDate[2];
				$Edad = $age;
				$BG = "";

				
				try{
				    $pdf->Image($server . '/pdf/Credencial.png',$x+0,$y+0,108, 70, 'PNG');
			    }catch(Exception $e){
			        echo $e->getTraceAsString();
			    }
			    
				try{
					$pdf->SetAlpha(1);
					$pdf->Image($server . '/Form/fetch_image.php?Jugador_ID=' . $row["Jugador_ID"] . '&Imagen=Foto',$x+10,$y+15,26, 35, 'PNG');
				}catch(Exception $e){
					try{
    					$pdf->SetAlpha(1);
    					$pdf->Image($server . '/Form/fetch_image.php?Jugador_ID=' . $row["Jugador_ID"] . '&Imagen=Foto',$x+10,$y+15,26, 35, 'JPG');
    				}catch(Exception $e){
    					try{
        					$pdf->SetAlpha(1);
        					$pdf->Image($server . '/Form/fetch_image.php?Jugador_ID=' . $row["Jugador_ID"] . '&Imagen=Foto',$x+10,$y+15,26, 35, 'JPEG');
        				}catch(Exception $e){
        					try{
            					$pdf->SetAlpha(1);
            					$pdf->Image($server . '/Form/fetch_image.php?Jugador_ID=' . $row["Jugador_ID"] . '&Imagen=Foto',$x+10,$y+15,26, 35, 'GIF');
            				}catch(Exception $e){
            					echo $e->getTraceAsString();
            				}
        				}
    				}
				}
				try{
				    $headers = @get_headers($server . '/imagenes/' . $row["Logo"] . '.png');
				    $pdf->SetAlpha(1);
					if($headers && strpos($headers[0], '200 OK') !== false){
						$pdf->Image($server . '/imagenes/' . $row["Logo"] . '.png',$x+15.5,$y+45,15, 15, 'PNG');
					}
				}catch(Exception $e){
					echo $e;
				}
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetAlpha(1);
				$pdf->SetXY($x+40,$y+16);
				$pdf->SetFont('Helvetica' , '' , 11);
				$pdf->Cell(45 , 5, '' . az_utf8_decode($row["Equipo_FULLDESC"]) . '', 0, 0 , 'L' , false);
				$pdf->SetXY($x+40,$y+21);
				$pdf->Cell(45 , 5, '' . az_utf8_decode($row["Categoria_Desc"]) . '', 0, 0 , 'L' , false);
				$pdf->SetXY($x+40,$y+26);
				$pdf->MultiCell(45	 , 5, az_utf8_decode('' . $row["Nombre"] . ' ' . $row["Apellido_P"] . ' ' . $row["Apellido_M"] . ''), 0, 'L' , false);
				//$pdf->SetXY($x+40,$y+36);
				//$pdf->Cell(65 , 5, az_utf8_decode('' . $row["Apodo"] . ''), 0, 0 , 'L' , false);
				$pdf->SetXY($x+40,$y+36);
				$pdf->Cell(65 , 5, 'Fech Nac ' . az_utf8_decode($row["Fecha_Nacimiento"]) . '', 0, 0 , 'L' , false);
				$pdf->SetXY($x+40,$y+41);
				$pdf->Cell(65 , 5, '' . az_utf8_decode(substr($row["Curp"],0,11)) . 'XXXXXXX', 0, 0 , 'L' , false);
				$pdf->SetXY($x+40,$y+46);
				$pdf->Cell(65 , 5, az_utf8_decode($row["FechaAlta"]), 0, 0 , 'L' , false);
				//$pdf->Image($server . '/include/qrcode/image.php?msg=' . $server . 'ajax/QR.php?Jugador_ID=' . $row["Jugador_ID"],$x+92,$y+2,13, 13, 'PNG');

				
				//$pdf->Table();
				if($col == 1){
					$x = 0;
					$col = 0;
					if($rowc == 3){
						$y = 0;
						$rowc = 0;
					}else{
						$y = $y + 70;
						$rowc = $rowc + 1;
					}
				}else{
					$x = $x + 108;
					$col = $col + 1;
				}
				$pages++;
			}catch(Exception $ae){
				
			}
		}
	} else {
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont('Helvetica' , 'B' , 12);
		$pdf->Cell(200 , 8, 'No hay Jugadores dados de Alta o aun no han sido Validados', 0, 0 , 'C' , false);
	}

	$Config->close();
	$pdf->Output();
?>
