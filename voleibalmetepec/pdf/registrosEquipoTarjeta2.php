<?php
	require_once dirname(__DIR__) . '/site_paths.php';
	set_time_limit(300);
	require('qrcode/qrcode.class.php');
	require("alphapdf.php");
	require("membersite_config.php");
	$Config = new Configuration();
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin($Config,'cedulas.php');
	$Config->connect();

	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	$folder = substr(substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT'])),1,strlen(substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT'])))-5);

	$torneo = $_COOKIE[$Config->getAlias() . 'season'];
	$torneo_desc = '';
	$categoria = $_COOKIE[$Config->getAlias() . 'category'];

	$equipo = htmlspecialchars($_GET["Equipo_ID"]);
	$edad1 = htmlspecialchars($_GET["Edad1"]);	
	$edad2 = htmlspecialchars($_GET["Edad2"]);	
	$imprimir = htmlspecialchars($_GET["Imprimir"]);

	$siteRoot = az_pdf_site_root($Config);
	
	$x = 1;
	$y = 0;
	$col = 0;
	$rowc = 0;

	$pdf = new AlphaPDF('L','mm','Letter');
	$pdf->SetAutoPageBreak(false);
	$pdf->AddPage();

	$Config->LoadLogo();
	$Config->LoadFlags();
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
	$x = 20;
	$y = 3;
	$sql = "SELECT 	Jugador_ID, 
					Clave, 
					Nombre, 
					Apellido_P, 
					Apellido_M, 
					Apodo, 
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
			where a.Equipo_ID = $equipo and Estatus = 'A' and a.Foto is not null 
				and Validado = 1
				and case 
					when month(Fecha_Nacimiento) < 8 then 
						year(now())-year(Fecha_Nacimiento)+1 
					else 
						year(now())-year(Fecha_Nacimiento) 
				    end between " . $edad1 . " and " . $edad2 . "
			order by convert(Numero,unsigned) asc";
    /*and Validado = 1 se quita del query por unica vez*/
    
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
				$pdf->SetDrawColor(0, 0, 0);

				$pdf->SetXY($x+6,$y+47);
				$pdf->SetFont('Arial','',14);
				$pdf->SetFillColor($colorR ,$colorG, $colorB);
				//$pdf->Rect($x, $y, 85.2, 53.8 , 'DF');
				//$pdf->Rotate(90,$x+2,$y+34);
				
				try{
					$pdf->SetAlpha(1);
					az_pdf_image_file($pdf, $siteRoot, '/imagenes/' . $Config->logo . '.png', $x+2+((15.68 - (15.68 * ($Config->logowidth / 110)))/2)-18.7,$y-30+5+((15.68 - (15.68 * ($Config->logoheight / 110)))/2)+28.45,(15.68 * ($Config->logowidth / 110)), (15.68 * ($Config->logoheight / 110)));
				}catch(Exception $e){
					echo $e;
				}
    
				try{
					$pdf->SetAlpha(1);
					az_pdf_player_photo($pdf, $Config, $schema, $row["Jugador_ID"], 'Foto', $x+2-18.7,$y-30+30+28.45,15.68, 20.38);
				}catch(Exception $e){
					try{
    					$pdf->SetAlpha(1);
    					az_pdf_player_photo($pdf, $Config, $schema, $row["Jugador_ID"], 'Foto', $x+2-18.7,$y-30+30+28.45,15.68, 20.38);
    				}catch(Exception $e){
    					try{
        					$pdf->SetAlpha(1);
        					az_pdf_player_photo($pdf, $Config, $schema, $row["Jugador_ID"], 'Foto', $x+2-18.7,$y-30+30+28.45,15.68, 20.38);
        				}catch(Exception $e){
        					try{
            					$pdf->SetAlpha(1);
            					az_pdf_player_photo($pdf, $Config, $schema, $row["Jugador_ID"], 'Foto', $x+2-18.7,$y-30+30+28.45,15.68, 20.38);
            				}catch(Exception $e){
            					echo $e->getMessage();
            				}
        				}
    				}
				}
				
				try{
					$pdf->SetAlpha(1);
					az_pdf_image_file($pdf, $siteRoot, '/imagenes/' . $row["Logo"] . '.png', $x+3-18.7,$y-30+54.5+28.45,15.68, 15.68);
				}catch(Exception $e){
					echo $e;
				}
				
				$pdf->SetXY($x+20.5-18.7,$y-30+6+28);
				$pdf->SetTextColor(255, 255, 255);
				$pdf->SetFont('Helvetica' , 'B' , 7.8);
				$pdf->SetAlpha(1);
				$pdf->SetFillColor(23, 204, 56);
				$pdf->Cell(33 , 5.22, 'Liga Municipal de', 0, 0 , 'C' , true);
				$pdf->SetXY($x+20.5-18.7,$y-30+6+5.22+28);
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Helvetica' , 'B' , 7.8);
				$pdf->SetAlpha(1);
				$pdf->SetFillColor(255 ,255, 255);
				$pdf->Cell(33 , 5.22, 'Voleibol Metepec', 0, 0 , 'C' , true);
				$pdf->SetXY($x+20.5-18.7,$y-30+6+10.44+28);
				$pdf->SetTextColor(255, 255, 255);
				$pdf->SetFont('Helvetica' , 'B' , 7.8);
				$pdf->SetAlpha(1);
				$pdf->SetFillColor(23, 128, 204);
				$pdf->Cell(33 , 5.22, $torneo_desc, 0, 0 , 'C' , true);
				$pdf->SetXY($x-16.6,$y-30+3+6+15.66+28);
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Helvetica' , 'B' , 7.8);
				$pdf->SetAlpha(1);
				$pdf->SetFillColor(hexdec(substr($row["Color"],1,2)),hexdec(substr($row["Color"],3,2)), hexdec(substr($row["Color"],5,2)));
				$pdf->Cell(51.5 , 3.2, '' . $row["Categoria_Desc"] . '', 0, 0 , 'C' , true);

				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Helvetica' , 'B' , 7.2);
				$pdf->SetAlpha(1);
				$pdf->SetFillColor(184 ,211, 220);
				$pdf->SetXY($x+20.5-18.7,$y-30+30+28.45);
				$pdf->Cell(33 , 3.2, az_utf8_decode('' . $row["Apodo"] . ''), 0, 0 , 'L' , true);
				
				$pdf->SetXY($x+20.5-18.7,$y-30+30+28.45+3.2+1.5);
				$pdf->SetFont('Helvetica' , 'B' , 7.2);
				$pdf->MultiCell(33	 , 3.2, az_utf8_decode('' . $row["Nombre"] . ' ' . $row["Apellido_P"] . ' ' . $row["Apellido_M"] . ''), 0, 'L' , true);
				
				
				$pdf->SetXY($x+20.5-18.7,$y-30+30+28.45+(9.6)+(3.0));
				$pdf->SetFont('Helvetica' , 'B' , 7.2);
				$pdf->Cell(33 , 3.2, '' . $row["Fecha_Nacimiento"] . '', 0, 0 , 'L' , true);
				
				$pdf->SetXY($x+20.5-18.7,$y-30+30+28.45+12.8+4.5);
				$pdf->SetFont('Helvetica' , 'B' , 7.2);
				$pdf->Cell(33 , 3.2, '' . substr($row["Curp"],0,11) . 'XXXXXXX', 0, 0 , 'L' , true);

				$pdf->SetXY($x+20.5-18.7,$y-30+54.5+28.45);
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Helvetica' , 'B' , 7.8);
				$pdf->SetAlpha(1);
				$pdf->SetFillColor(184 ,211, 220);
				$pdf->MultiCell(33 , 3.2, '' . $row["Equipo_FULLDESC"] . '', 0 , 'C' , true);
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Courier' , 'B' , 24);
				$pdf->SetXY($x+20.5-18.7,$y-30+54.5+28.45+5);
				$pdf->Cell(33 , 10.92, '' . $row["Numero"] . '', 0, 0 , 'C' , false);

				//$pdf->Image('http://chart.googleapis.com/chart?cht=qr&chs=200x200&chld=L|1&chf=bg,s,65432100&chl=' . $server . 'ajax/QR.php?Jugador_ID=' . $row["Jugador_ID"],$x+2-18.7,$y-30+72+28.45,15.68, 15.68, 'PNG');
                az_pdf_qrcode($pdf, $fgmembersite, $row["Jugador_ID"],$x+2-18.7,$y+72+28.45,15.68, 15.68);

				$pdf->SetDrawColor(0 ,0, 0);

				try{
					$pdf->SetAlpha(1);
					az_pdf_image_file($pdf, $siteRoot, '/imagenes/Aztechnologies-S.png', $x+20.5-18.7,$y-30+78.08+28.45,33, 9.42);
				}catch(Exception $e){
					echo $e;
				}	
				try{
					$pdf->SetAlpha(.3);
					az_pdf_image_file($pdf, $siteRoot, '/imagenes/Aztechnologies-A.png', $x+20.5-18.7,$y-30+27.5+28.45,33, 41.5);
				}catch(Exception $e){
					echo $e;
				}				
				if($col == 4){
					$x = 20;
					$col = 0;
					if($rowc == 1){
						$y = 3;
						$rowc = 0;
					}else{
						$y = $y + 100;
						$rowc++;
					}
				}else{
					$x = $x + 55;
					$col++;
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
