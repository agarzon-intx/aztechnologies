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

        $pdf = new FPDF('L','mm','Letter');


	$port = $_SERVER["SERVER_PORT"];

	$equipo = htmlspecialchars($_GET["Equipo_ID"]);
	$edad1 = htmlspecialchars($_GET["Edad1"]);	
	$edad2 = htmlspecialchars($_GET["Edad2"]);	
	$imprimir = htmlspecialchars($_GET["Imprimir"]);	

	$x = 1;
	$y = 0;
	$col = 0;
	$rowc = 0;
	$Menor = '';

	$pdf = new AlphaPDF('L','mm','Letter');
	$pdf->SetAutoPageBreak(false);
	$pdf->AddPage();

	$Config->LoadLogo();
	$Config->connect();
	
	$sql = "SELECT 	Jugador_ID, 
					Clave, 
					Nombre, 
					Apellido_P, 
					Apellido_M, 
					Apodo, 
					date_format(Fecha_Nacimiento,'%d/%m/%Y') Fecha_Nacimiento, 
					YEAR(CURDATE())-YEAR(Fecha_Nacimiento) Edad, 
					Curp, 
					Numero, 
					Estatus, 
					a.Equipo_ID, 
					b.Equipo_FULLDESC, 
					Comentarios, 
					Telefono, 
					correo, 
					concat(b.Torneo_ID,'-', b.Equipo_ID) Logo,
					c.Categoria_Desc, 
					c.Color, 
					d.Color_HEX
			FROM $schema.Jugadores a 
				join $schema.Equipos b on a.Equipo_ID = b.Equipo_ID and b.Torneo_ID = (select max(Torneo_ID) from $schema.Equipos where Equipo_ID = $equipo) 
				join $schema.Categorias c on b.Fuerza = c.Categoria_ID
				join $schema.Colores d on c.Color = d.Color_HEX
			where a.Equipo_ID = $equipo and Estatus = 'A' and Validado = 1 and a.Foto is not null
			order by convert(Numero,unsigned) asc";
	//echo $sql;
	$result = $Config->query($sql);
	$pages = 0;
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			try{
					
				if($col == 0 && $rowc == 0 && $pages > 0){
					$pdf->AddPage();
				}
				$colorR = 0;
				$colorG = 0;
				$colorB = 0;
				
				$date = DateTime::createFromFormat("d/m/Y", $row["Fecha_Nacimiento"]);
				$birthDate = explode("/", $date->format("m/d/Y"));
				//get age from date or birthdate
				$age = date("Y") - $birthDate[2];
				$Edad = $age;
				if($Edad <= 39){ 
					$colorR = 255;
					$colorG = 0;
					$colorB = 0;
					$Menor = 'MENOR';
				}else{
					$colorR = 255;
					$colorG = 255;
					$colorB = 255;
					$Menor = '';
				}
				
				$pdf->SetDrawColor(200, 200, 200);

				$pdf->SetXY($x+6,$y+47);
				$pdf->SetFont('Arial','',14);
				$pdf->SetFillColor(255,255,255);
				$pdf->Rect($x+0, $y+5, 69, 100 , 'DF');
				try{
				    $headers = @get_headers($server . '/imagenes/' . $row["Logo"] . '.png');
				    $pdf->SetAlpha(1);
					if($headers && strpos($headers[0], '200 OK') !== false){
						$pdf->Image($server . '/imagenes/' . $row["Logo"] . '.png',$x+3,$y+64,19, 19, 'PNG');
					}
				}catch(Exception $e){
					echo $e;
				}
				try{
				    $headers = @get_headers($server . '/imagenes/' . $Config->logo . '.png');
				    $pdf->SetAlpha(1);
					if($headers && strpos($headers[0], '200 OK') !== false){
						$pdf->Image($server . '/imagenes/' . $Config->logo . '.png',$x+2+((27 - (27 * ($Config->logowidth / 110)))/2),$y+6+((27 - (27 * ($Config->logoheight / 110)))/2),(27 * ($Config->logowidth / 110)), (27 * ($Config->logoheight / 110)), 'PNG');
					}
				}catch(Exception $e){
					echo $e;
				}
				try{
					$pdf->SetAlpha(1);
					$pdf->Image($server . '/Form/fetch_image.php?Jugador_ID=' . $row["Jugador_ID"] . '&Imagen=Foto',$x+2,$y+34,20, 26, 'PNG');
				}catch(Exception $e){
					try{
    					$pdf->SetAlpha(1);
    					$pdf->Image($server . '/Form/fetch_image.php?Jugador_ID=' . $row["Jugador_ID"] . '&Imagen=Foto',$x+2,$y+34,20, 26, 'JPG');
    				}catch(Exception $e){
    					try{
        					$pdf->SetAlpha(1);
        					$pdf->Image($server . '/Form/fetch_image.php?Jugador_ID=' . $row["Jugador_ID"] . '&Imagen=Foto',$x+2,$y+34,20, 26, 'JPEG');
        				}catch(Exception $e){
        					try{
            					$pdf->SetAlpha(1);
            					$pdf->Image($server . '/Form/fetch_image.php?Jugador_ID=' . $row["Jugador_ID"] . '&Imagen=Foto',$x+2,$y+34,20, 26, 'GIF');
            				}catch(Exception $e){
            					echo $e->getTraceAsString();
            				}
        				}
    				}
				}
				$pdf->SetAlpha(1);
				$pdf->SetXY($x+32,$y+9.5);
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Helvetica' , '' , 16);
				$pdf->SetAlpha(1);
				$pdf->SetFillColor(255 ,255, 255);
				$pdf->Cell(36 , 6.65, 'LIGA', 0, 0 , 'C' , true);
				$pdf->SetXY($x+32,$y+16.15);
				$pdf->Cell(36 , 6.65, 'NUESTRO', 0, 0 , 'C' , true);
				$pdf->SetXY($x+32,$y+22.80);
				$pdf->Cell(36 , 6.65, 'DEPORTE', 0, 0 , 'C' , true);
				
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Helvetica' , 'B' , 10);
				$pdf->SetAlpha(1);
				$pdf->SetFillColor(184 ,211, 220);
				$pdf->SetXY($x+25,$y+34);
				$pdf->Cell(42 , 4, az_utf8_decode('' . $row["Apodo"] . ''), 0, 0 , 'L' , true);
				$pdf->SetXY($x+25,$y+40);
				$pdf->SetFont('Helvetica' , 'B' , 9);
				$pdf->MultiCell(42	 , 4, az_utf8_decode('' . $row["Nombre"] . ' ' . $row["Apellido_P"] . ' ' . $row["Apellido_M"] . ''), 0, 'L' , true);
				$pdf->SetFont('Helvetica' , 'B' , 9);
				$pdf->SetXY($x+25,$y+50);
				$pdf->SetFont('Helvetica' , 'B' , 10);
				$pdf->Cell(42 , 4, '' . $row["Fecha_Nacimiento"] . '', 0, 0 , 'L' , true);
				$pdf->SetXY($x+25,$y+56);
				$pdf->SetFont('Helvetica' , 'B' , 9);
				$pdf->Cell(42 , 4, '' . substr($row["Curp"],0,11) . 'XXXXXXX', 0, 0 , 'L' , true);

				$pdf->SetXY($x+25,$y+63);
				$pdf->MultiCell(42 , 4, '' . $row["Equipo_FULLDESC"] . '', 0 , 'C' , true);
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Courier' , 'B' , 30);
				$pdf->SetXY($x+25,$y+70);
				$pdf->Cell(42 , 14, '' . $row["Numero"] . '', 0, 0 , 'C' , false);

                try{
                    $pdf->Image($server . '/include/qrcode/image.php?msg=' . $server . 'ajax/QR.php?Jugador_ID=' . $row["Jugador_ID"],$x+2,$y+85,20, 20, 'PNG');
    				//$pdf->Image('http://chart.googleapis.com/chart?cht=qr&chs=200x200&chld=L|1&chf=bg,s,65432100&chl=http://www.hectorbarraza.com/Reportes/jugador.php?Jugador_ID=' . $row["Jugador_ID"] . '',$x+2,$y+85,20, 20, 'PNG');
    				//$pdf->Image('https://qrcode.tec-it.com/API/QRCode?data=' . $server . 'ajax/QR.php?Jugador_ID=' . $row["Jugador_ID"],$x+2,$y+85,20, 20, 'PNG');
	            }catch(Exception $e){
					echo $e->getTraceAsString();
				}
				$pdf->SetDrawColor(0 ,0, 0);
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Helvetica' , '' , 6);
				$pdf->SetXY($x+25,$y+90);
				$pdf->Cell(42, 4, '', 0, 0 , 'C' , false);
				$pdf->SetXY($x+25,$y+100);
				$pdf->Cell(21, 4, '', 0, 0, 'C' , false);
				$pdf->SetXY($x+46,$y+100);
				$pdf->Cell(21, 4, '', 0, 0 , 'C' , false);
				//$pdf->Table();
				if($col == 3){
					$x = 1;
					$col = 0;
					if($rowc == 1){
						$y = 0;
						$rowc = 0;
					}else{
						$y = $y + 100;
						$rowc = $rowc + 1;
					}
				}else{
					$x = $x + 69;
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
