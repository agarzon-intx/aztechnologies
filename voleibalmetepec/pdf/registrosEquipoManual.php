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
	
	$x = 1;
	$y = 0;
	$col = 0;
	$rowc = 0;

	$pdf = new AlphaPDF('P','mm','Letter');
	$pdf->SetAutoPageBreak(false);
	$pdf->AddPage();

	$Config->LoadLogo();
	$Config->connect();

	$sql = "SELECT Torneo_DESC 
            FROM $schema.Torneos
            where Torneo_ID = $torneo;";

	$result = $Config->query($sql);
	$pages = 0;
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
            $torneo_desc = $row["Torneo_DESC"];
		}
	}

	$sql = "SELECT 	Jugador_ID, 
					Clave, 
					Nombre, 
					Apellido_P, 
					Apellido_M, 
					CASE WHEN Apodo='' THEN CONCAT(Apellido_P,' ',SUBSTRING(Nombre,1,1),'.') ELSE Apodo END AS Apodo, 
					CONCAT(Apellido_P,' ',SUBSTRING(Nombre,1,1),'.') AS Jugador,
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
					d.Color_HEX,
					date_format(a.FechaAlta,'%d/%m/%Y') FechaAlta
			FROM $schema.Jugadores a 
				join $schema.Equipos b on a.Equipo_ID = b.Equipo_ID and b.Torneo_ID = $torneo
				join $schema.Categorias c on b.Fuerza = c.Categoria_ID AND b.Torneo_ID= c.Torneo_ID
				join $schema.Colores d on c.Color = d.Color_HEX
			where YEAR(CURDATE())-YEAR(Fecha_Nacimiento) between $edad1 and $edad2
				and a.Jugador_ID in (" . $_SESSION[$Config->getAlias() . 'printList'] . ")
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
				}
				$colorR = 0;
				$colorG = 0;
				$colorB = 0;

				$colorR = 255;
				$colorG = 255;
				$colorB = 255;
				$pdf->SetDrawColor(200, 200, 200);

				$pdf->SetXY($x+6,$y+47);
				$pdf->SetFont('Arial','',14);
				$pdf->SetFillColor($colorR ,$colorG, $colorB);
				$pdf->Rect($x+0, $y+5, 69, 100 , 'DF');
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
            					echo $e->getMessage();
            				}
        				}
    				}
				}
				try{
					$pdf->SetAlpha(1);
					$pdf->Image($server . '/imagenes/' . $row["Logo"] . '.png',$x+3,$y+64,19, 19, 'PNG');
				}catch(Exception $e){
					try{
    					$pdf->SetAlpha(1);
    					$pdf->Image($server . '/imagenes/' . $row["Logo"] . '.png',$x+3,$y+64,19, 19, 'JPG');
    				}catch(Exception $e){
    					try{
        					$pdf->SetAlpha(1);
        					$pdf->Image($server . '/imagenes/' . $row["Logo"] . '.png',$x+3,$y+64,19, 19, 'JPEG');
        				}catch(Exception $e){
        					try{
            					$pdf->SetAlpha(1);
            					$pdf->Image($server . '/imagenes/' . $row["Logo"] . '.png',$x+3,$y+64,19, 19, 'GIF');
            				}catch(Exception $e){
            					echo $e->getMessage();
            				}
        				}
    				}
				}
				try{
					$pdf->SetAlpha(1);
					$pdf->Image($server . '/imagenes/' . $Config->logo . '.png',$x+2+((20 - (20 * ($Config->logowidth / 110)))/2),$y+5+((20 - (20 * ($Config->logoheight / 110)))/2),(20 * ($Config->logowidth / 110)), (20 * ($Config->logoheight / 110)), 'PNG');
				}catch(Exception $e){
					echo $e;
				}
				$pdf->SetXY($x+25,$y+6);
				$pdf->SetTextColor(255, 255, 255);
				$pdf->SetFont('Helvetica' , 'B' , 10);
				$pdf->SetAlpha(1);
				$pdf->SetFillColor(23, 204, 56);
				$pdf->Cell(42 , 6.65, 'Liga Municipal de', 0, 0 , 'C' , true);
				$pdf->SetXY($x+25,$y+12.65);
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Helvetica' , 'B' , 10);
				$pdf->SetAlpha(1);
				$pdf->SetFillColor(255 ,255, 255);
				$pdf->Cell(42 , 6.65, 'Voleibol Metepec', 0, 0 , 'C' , true);
				$pdf->SetXY($x+25,$y+19.30);
				$pdf->SetTextColor(255, 255, 255);
				$pdf->SetFont('Helvetica' , 'B' , 10);
				$pdf->SetAlpha(1);
				$pdf->SetFillColor(23, 128, 204);
				$pdf->Cell(42 , 6.65, $torneo_desc, 0, 0 , 'C' , true);
				$pdf->SetXY($x+2,$y+28);
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Helvetica' , 'B' , 10);
				$pdf->SetAlpha(1);
				$pdf->SetFillColor(hexdec(substr($row["Color"],1,2)),hexdec(substr($row["Color"],3,2)), hexdec(substr($row["Color"],5,2)));
				$pdf->Cell(65 , 4, '' . $row["Categoria_Desc"] . '', 0, 0 , 'C' , true);

				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Helvetica' , 'B' , 9);
				$pdf->SetAlpha(1);
				$pdf->SetFillColor(184 ,211, 220);
				$pdf->SetXY($x+25,$y+34);
				$pdf->Cell(42 , 4, utf8_decode('' . $row["Apodo"] . ''), 0, 0 , 'L' , true);
				$pdf->SetXY($x+25,$y+40);
				$pdf->SetFont('Helvetica' , 'B' , 9);
				$pdf->MultiCell(42	 , 4, utf8_decode('' . $row["Nombre"] . ' ' . $row["Apellido_P"] . ' ' . $row["Apellido_M"] . ''), 0, 'L' , true);
				$pdf->SetFont('Helvetica' , 'B' , 9);

				$pdf->SetXY($x+25,$y+50);
				$pdf->SetFont('Helvetica' , 'B' , 10);
				$pdf->Cell(42 , 4, '' . $row["Fecha_Nacimiento"] . '', 0, 0 , 'L' , true);
				$pdf->SetXY($x+25,$y+56);
				$pdf->SetFont('Helvetica' , 'B' , 9);
				$pdf->Cell(42 , 4, '' . substr($row["Curp"],0,11) . 'XXXXXXX', 0, 0 , 'L' , true);

				$pdf->SetXY($x+25,$y+63);
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Helvetica' , 'B' , 10);
				$pdf->SetAlpha(1);
				$pdf->SetFillColor(184 ,211, 220);
				$pdf->MultiCell(42 , 4, '' . $row["Equipo_FULLDESC"] . '', 0 , 'C' , true);
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Courier' , 'B' , 30);
				$pdf->SetXY($x+25,$y+70);
				$pdf->Cell(42 , 14, '' . $row["Numero"] . '', 0, 0 , 'C' , false);

				//$pdf->Image('http://chart.googleapis.com/chart?cht=qr&chs=200x200&chld=L|1&chf=bg,s,65432100&chl=' . $server . 'ajax/QR.php?Jugador_ID=' . $row["Jugador_ID"],$x+2,$y+85,20, 20, 'PNG');
				//$pdf->Image('https://qrcode.tec-it.com/API/QRCode?data=' . $server . 'ajax/QR.php?Jugador_ID=' . $row["Jugador_ID"],$x+2,$y+85,20, 20, 'PNG');
                $pdf->Image($server . '/include/qrcode/image.php?msg=' . $server . 'ajax/QR.php?Jugador_ID=' . $row["Jugador_ID"],$x+2,$y+85,20, 20, 'PNG');

				$pdf->SetDrawColor(0 ,0, 0);

				try{
					$pdf->SetAlpha(1);
					$pdf->Image($server . '/imagenes/Aztechnologies-S.png',$x+25,$y+93,42, 12, 'PNG');
				}catch(Exception $e){
					echo $e;
				}	
				try{
					$pdf->SetAlpha(.2);
					$pdf->Image($server . '/imagenes/Aztechnologies-A.png',$x+25,$y+30,40, 50, 'PNG');
				}catch(Exception $e){
					echo $e;
				}
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
