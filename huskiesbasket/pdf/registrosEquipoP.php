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

	$pdf = new AlphaPDF('L','mm','Letter');
	$pdf->SetAutoPageBreak(false);
	$pdf->AddPage();

	$conn = new mysqli($servername, $username, $password);

	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 

	$sql = "SELECT Jugador_ID, Clave, Nombre, Apellido_P, Apellido_M, Apodo, date_format(Fecha_Nacimiento,'%d/%m/%Y') Fecha_Nacimiento, 
				YEAR(CURDATE())-YEAR(Fecha_Nacimiento) Edad, Curp, Numero, Estatus, a.Equipo_ID, b.Equipo_FULLDESC, Comentarios, Telefono, correo, b.Logo,
				c.Categoria_Desc, c.Color, d.Color_HEX
			FROM $schema.Jugadores a 
				join $schema.Equipos b on a.Equipo_ID = b.Equipo_ID and b.Torneo_ID = (select max(Torneo_ID) from $schema.Equipos where Equipo_ID = $equipo) 
				join $schema.Categorias c on b.Fuerza = c.Categoria_ID
				join $schema.Colores d on c.Color = d.Color_HEX
			where a.Equipo_ID = $equipo and Estatus = 'A' and Validado = 1 and a.Foto is not null
			order by convert(Numero,unsigned) asc";
	$result = $conn->query($sql);
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
				$pdf->SetAlpha(1);
				az_pdf_player_photo($pdf, $Config, $schema, $row['Jugador_ID'], 'Foto', $x+2, $y+34, 20, 26);
				az_pdf_image_file($pdf, $siteRoot, 'imagenes/' . $row['Logo'] . '.png', $x+3, $y+64, 19, 19);
				$pdf->SetAlpha(1);
				az_pdf_image_file($pdf, $siteRoot, 'imagenes/LogoLiga.png', $x+4, $y+5, 10, 15);
				$pdf->SetXY($x+19,$y+6);
				$pdf->SetTextColor(255, 255, 255);
				$pdf->SetFont('Helvetica' , '' , 7);
				$pdf->SetAlpha(1);
				$pdf->SetFillColor(62 ,142, 105);
				$pdf->Cell(31 , 5, 'Liga Infantil y Juvenil', 0, 0 , 'C' , true);
				$pdf->SetXY($x+19,$y+11);
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Helvetica' , '' , 7);
				$pdf->SetAlpha(1);
				$pdf->SetFillColor(255 ,255, 255);
				$pdf->Cell(31 , 5, 'Hector Barraza A.C.', 0, 0 , 'C' , true);
				$pdf->SetXY($x+19,$y+16);
				$pdf->SetTextColor(255, 255, 255);
				$pdf->SetFont('Helvetica' , '' , 7);
				$pdf->SetAlpha(1);
				$pdf->SetFillColor(255 ,80, 84);
				$pdf->Cell(31 , 5, 'Temporada 2016-2017', 0, 0 , 'C' , true);
				$pdf->SetAlpha(1);
				az_pdf_image_file($pdf, $siteRoot, 'imagenes/patrocinador.png', $x+52, $y+5, 15, 15);

				$pdf->SetXY($x+2,$y+22);
				$pdf->SetTextColor(255, 255, 255);
				$pdf->SetFont('Helvetica' , 'B' , 10);
				$pdf->SetAlpha(1);
				$pdf->SetFillColor(0, 0, 0);
				$pdf->Cell(65 , 5, 'TORNEO DE COPA ADIDAS', 0, 0 , 'C' , true);

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
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Helvetica' , 'B' , 10);
				$pdf->SetAlpha(1);
				$pdf->SetFillColor(184 ,211, 220);
				$pdf->MultiCell(42 , 4, '' . $row["Equipo_FULLDESC"] . '', 0 , 'C' , true);
				$pdf->SetAlpha(.4);
				az_pdf_image_file($pdf, $siteRoot, 'imagenes/patrocinador.png', $x+29, $y+72, 32, 32);
				$pdf->SetAlpha(1);
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Courier' , 'B' , 30);
				$pdf->SetXY($x+25,$y+70);
				$pdf->Cell(42 , 14, '' . $row["Numero"] . '', 0, 0 , 'C' , false);

				//$pdf->Image('http://chart.googleapis.com/chart?cht=qr&chs=200x200&chld=L|1&chf=bg,s,65432100&chl=http://www.hectorbarraza.com/Reportes/jugador.php?Jugador_ID=' . $row["Jugador_ID"] . '',$x+2,$y+85,20, 20, 'PNG');
				az_pdf_qrcode($pdf, $fgmembersite, $row['Jugador_ID'], $x+2, $y+85, 20, 20);

				$pdf->SetDrawColor(0 ,0, 0);
				$pdf->Line($x+37,$y+90, $x+57,$y+90);
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Helvetica' , '' , 6);
				$pdf->SetXY($x+25,$y+90);
				$pdf->Cell(42, 4, $lang['979'], 0, 0 , 'C' , false);
				$pdf->Line($x+27,$y+100, $x+44,$y+100);
				$pdf->SetXY($x+25,$y+100);
				$pdf->Cell(21, 4, $lang['980'], 0, 0, 'C' , false);
				$pdf->Line($x+48,$y+100, $x+65,$y+100);
				$pdf->SetXY($x+46,$y+100);
				$pdf->Cell(21, 4, $lang['981'], 0, 0 , 'C' , false);
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
	$conn->close();
	$pdf->Output();
?>
