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
	
	$edad1 = htmlspecialchars($_GET["Edad1"]);	
	$edad2 = htmlspecialchars($_GET["Edad2"]);	
	$imprimir = htmlspecialchars($_GET["Imprimir"]);

	$siteRoot = az_pdf_site_root($Config);
	
	
	$x = 1;
	$y = 0;
	$col = 0;
	$rowc = 0;

	$pdf = new AlphaPDF('P','mm','Letter');
	//$pdf = new AlphaPDF('P','mm','Letter');
	$pdf->SetAutoPageBreak(false);
	$pdf->AddPage();
	
	$ancho = 54;   // ancho PVC
$alto  = 85.6; // alto PVC
	
	

	$Config->LoadLogo();
//	$Config->LoadFlags();
	$Config->connect();
	$credencialFront = $Config->credencialFrontImageStored();

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
	$x = $x + 31.25;
	$y = $y + 11.2; 
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
			where Estatus = 'A' and a.Foto is not null 
				and Validado = 1
				and case 
					when month(Fecha_Nacimiento) < 8 then 
						year(now())-year(Fecha_Nacimiento)+1 
					else 
						year(now())-year(Fecha_Nacimiento) 
				    end between " . $edad1 . " and " . $edad2 . "
				and a.Jugador_ID in (" . $_SESSION[$Config->getAlias() . 'printList'] . ")
			order by convert(Numero,unsigned) asc";
    /*and Validado = 1 se quita del query por unica vez*/
    //echo $sql;
	$result = $Config->query($sql);
	$pages = 0;
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			try{
					
				if($rowc == 2){
					$pdf->AddPage();
					$rowc = 0;
					$y = 11.2;
					$x = 32.25;
					
				}
				$colorR = 0;
				$colorG = 0;
				$colorB = 0;

				$colorR = 255;
				$colorG = 255;
				$colorB = 255;

				$pdf->SetDrawColor(0 ,0, 0);

				try{
					$pdf->SetAlpha(1);
					az_pdf_image_file($pdf, $siteRoot, '/imagenes/Aztechnologies-S.png', $x+39-18.7,$y+11.5+27.45+7,25.68, 0);
				}catch(Exception $e){
					echo $e;
				}
				
				$pdf->SetXY($x+4-15.7,$y+14+28);
			    az_pdf_image_file($pdf, $siteRoot, 'imagenes/fmvb.PNG' ,$x+48,$y+11.5+27.45,13.68, 13.68,'PNG');
			   	try{
			  	    $pdf->SetAlpha(.4);
			   $pdf->SetXY(2,62);
			    if ($credencialFront) {
			        az_pdf_image_file($pdf, $siteRoot, 'pdf/Credencial.png' ,$x+39-18.7,$y+5.3,66, 43);
			    }
			   	}catch(Exception $e){
					echo $e;
				}				
				
				try{
					$pdf->SetAlpha(.3);
					
					az_pdf_image_file($pdf, $siteRoot, '/imagenes/' . $Config->logo . '.png', $x+44,$y+.5+5.7,21, 21);
				}catch(Exception $e){
					echo $e;
				}
				$pdf->Rotate(0);

				$pdf->SetDrawColor(0, 0, 0);

				$pdf->SetXY($x+6,$y+47);
				$pdf->SetFont('Arial','',14);
				$pdf->SetFillColor($colorR ,$colorG, $colorB);
				$pdf->Rotate(90,$x+2,$y+34);
				$pdf->Rotate(360,$x+2,$y+34);
				try{
					$pdf->SetAlpha(1);
					az_pdf_player_photo($pdf, $Config, $schema, $row["Jugador_ID"], 'Foto', $x+20-18.7,$y+.4+.3,19.5, 22.38);
				}catch(Exception $e){
					try{
    					$pdf->SetAlpha(1);
    					az_pdf_player_photo($pdf, $Config, $schema, $row["Jugador_ID"], 'Foto', $x+20-18.7,$y+.5+1,20.4, 22.38);
    				}catch(Exception $e){
    					try{
        					$pdf->SetAlpha(1);
        					az_pdf_player_photo($pdf, $Config, $schema, $row["Jugador_ID"], 'Foto', $x+20-18.7,$y+.5+1,20.4, 22.38);
        				}catch(Exception $e){
        					try{
            					$pdf->SetAlpha(1);
            					az_pdf_player_photo($pdf, $Config, $schema, $row["Jugador_ID"], 'Foto', $x+20-18.7,$y+.5+1,20.4, 22.38);
            				}catch(Exception $e){
            					echo $e->getMessage();
            				}
        				}
    				}
				}
				
				try{
					$pdf->SetAlpha(1);
					az_pdf_image_file($pdf, $siteRoot, '/imagenes/' . $row["Logo"] . '.png', $x+22-18.7,$y+9.2+16,15.68, 15.68);
				}catch(Exception $e){
					try{
    					$pdf->SetAlpha(1);
    					az_pdf_image_file($pdf, $siteRoot, '/imagenes/' . $row["Logo"] . '.png', $x+22-18.7,$y+9.2+16,15.68, 15.68);
    				}catch(Exception $e){
    					try{
        					$pdf->SetAlpha(1);
        					az_pdf_image_file($pdf, $siteRoot, '/imagenes/' . $row["Logo"] . '.png', $x+22-18.7,$y+9.2+16,15.68, 15.68);
        				}catch(Exception $e){
        					try{
            					$pdf->SetAlpha(1);
            					az_pdf_image_file($pdf, $siteRoot, '/imagenes/' . $row["Logo"] . '.png', $x+22-18.7,$y+9.2+16,15.68, 15.68);
            				}catch(Exception $e){
            					echo $e->getMessage();
            				}
        				}
    				}
				}
				$pdf->SetXY($x+21.7,$y+.7);
				
				$pdf->SetTextColor(255, 255, 255);
				$pdf->SetFont('Helvetica' , 'B' , 13);
				$pdf->SetAlpha(1);
				
				$pdf->SetFillColor(30, 153, 37);
				$pdf->Cell(64.8 , 5.2, '' . $row["Categoria_Desc"] . '', 0, 0 , 'C' , true);
				
				$pdf->SetXY($x+21.7,$y+5.9);
				$pdf->SetTextColor(0, 0, 0);
				
				$pdf->SetFont('Helvetica' , 'B' , 14);
				$pdf->SetAlpha(1);
				$pdf->SetFillColor(255 ,255, 255);
				$pdf->Cell(64.8 , 6.2, '' . az_utf8_decode($row["Equipo_FULLDESC"]) . '', 0, 0, 'C' , false);
			
			
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Helvetica' , 'B' , 7.7);
				$pdf->SetAlpha(1);
				$pdf->SetFillColor(255 ,255, 255);
				$pdf->SetXY($x+21.7,$y+5.9+6.2);
				$pdf->SetFont('Helvetica' , 'B' , 12);
				$pdf->MultiCell(64.8	 , 5, az_utf8_decode('' . $row["Nombre"] . ' ' . $row["Apellido_P"] . ' ' . $row["Apellido_M"] . ''), 0, 'C' , false);

				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetXY($x+21.7,$y+26.5);
				$pdf->SetFont('Helvetica' , 'B' , 12);
				$pdf->Cell(22 , 3.2, '' . $row["Fecha_Nacimiento"] . '', 0, 0 , 'L' , false);
				
				$pdf->SetXY($x+21.7,$y+32);
				$pdf->SetFont('Helvetica' , 'B' , 13);
				$pdf->Cell(22 , 3.2, az_utf8_decode('' . $row["Jugador"] . ''), 0, 0 , 'L' , false);
				
				
				$pdf->SetXY($x+20.5-18.7,$y+30+28.45+12.8+4.5); 
				$pdf->SetFont('Helvetica' , 'B' , 7.2);
	//			$pdf->Cell(33 , 3.2, '' . substr($row["Curp"],0,11) . 'XXXXXXX', 0, 0 , 'L' , true);

			
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Courier' , 'B' , 38);
				//$pdf->SetXY($x+ 5-26.7,$y+36+28);
				$pdf->SetXY($x+12-15.7,$y+14+28);
				$pdf->Cell(30 , 10.92, '' . $row["Numero"] . '', 0, 0 , 'C' , false);

				az_pdf_qrcode($pdf, $fgmembersite, $row["Jugador_ID"],$x+89-18.7,$y+10.5+27.45,13.68, 13.68);
				
				$y = $y +60;
				$rowc++;
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
