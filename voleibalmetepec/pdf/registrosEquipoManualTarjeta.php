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
				$pdf->SetDrawColor(0, 0, 0);

				$pdf->SetXY($x+6,$y+47);
				$pdf->SetFont('Arial','',14);
				$pdf->SetFillColor($colorR ,$colorG, $colorB);
			//	$pdf->Rect($x, $y, 87.2, 52.8 , 'DF');  // Margen o borde
				$pdf->Rotate(90,$x+2,$y+34);
				$pdf->Rotate(360,$x+2,$y+34);
			//	$pdf->Rotate(90,$x+2,$y+34);
			//	$pdf->Rect($x, $y, $ancho, $alto);
				try{
			//		$pdf->SetAlpha(1);
			//	    az_pdf_image_file($pdf, $siteRoot, '/imagenes/' . $Config->logo . '.png', $x+2+((15.68 - (15.68 * ($Config->logowidth / 110)))/2)-18.7,$y+5+((15.68 - (15.68 * ($Config->logoheight / 110)))/2)+28.45,(15.68 * ($Config->logowidth / 110)), (15.68 * ($Config->logoheight / 110)));
			//original	
				     //try{ $pdf->SetAlpha(1);
				    // az_pdf_image_file($pdf, $siteRoot, '/imagenes/' . $Config->logo . '.png', $x+2+((15.68 - (15.68 * ($Config->logowidth / 110)))/2)-18.7,$y+5+((15.68 - (15.68 * ($Config->logoheight / 110)))/2)+28.45,(15.68 * ($Config->logowidth / 110)), (15.68 * ($Config->logoheight / 110))); 
				}catch(Exception $e){
					echo $e;
				}
    
    //$pdf->SetXY(62,12);
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
				/*
				
				try{
					$pdf->SetAlpha(1);
					az_pdf_player_photo($pdf, $Config, $schema, $row["Jugador_ID"], 'Foto', $x+29-18.7,$y+20+28.45,22., 25.38);
				}catch(Exception $e){
					try{
    					$pdf->SetAlpha(1);
    					az_pdf_player_photo($pdf, $Config, $schema, $row["Jugador_ID"], 'Foto', $x+29-18.7,$y+20+28.45,22.4, 25.38);
    				}catch(Exception $e){
    					try{
        					$pdf->SetAlpha(1);
        					az_pdf_player_photo($pdf, $Config, $schema, $row["Jugador_ID"], 'Foto', $x+29-18.7,$y+20+28.45,22.4, 25.38);
        				}catch(Exception $e){
        					try{
            					$pdf->SetAlpha(1);
            					az_pdf_player_photo($pdf, $Config, $schema, $row["Jugador_ID"], 'Foto', $x+29-18.7,$y+20+28.45,22.4, 25.38);
            				}catch(Exception $e){
            					echo $e->getMessage();
            				}
        				}
    				}
				}
				
				
				*/
				
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
				//try{
				//	$pdf->SetAlpha(1);
					
               //     $pdf->Rotate(90, 40,40); // Gira 90 grados alrededor del punto (30,30)
//$pdf->Image('logo.png', 25, 25, 20); // Imagen girada
				//	az_pdf_image_file($pdf, $siteRoot, '/imagenes/' . $row["Logo"] . '.png', $x+22-18.7,$y+9.2+16,15.68, 15.68);    //Logo equipo
					//az_pdf_image_file($pdf, $siteRoot, '/imagenes/' . $row["Logo"] . '.png', $x+3-18.7,$y+18.5+28.45,16.68, 16.68);    //Logo equipo
				// az_pdf_image_file($pdf, $siteRoot, '/imagenes/' . $row["Logo"] . '.png', $x+3-18.7,$y+54.5+28.45,15.68, 15.68);    //Logo equipo
				//	$pdf->Rotate(5,$x+2,$y+4);
				//}catch(Exception $e){
				//	echo $e;
				//}
			
			//Categorias
				//$pdf->SetXY($x+2-18.7,$y+28+6+15.66+2.75);  Anterior
			//	$pdf->SetXY($x+2-18.7,$y+6+28);
				//$pdf->SetXY(62,12);
				$pdf->SetXY($x+21.7,$y+.7);
				
			//		$pdf->Rotate($x+18,$y+6);
				$pdf->SetTextColor(255, 255, 255);
				$pdf->SetFont('Helvetica' , 'B' , 13);
				$pdf->SetAlpha(1);
				
				//$pdf->SetFillColor(142, 81, 255); //anterior
				$pdf->SetFillColor(142, 81, 255);
			//	$pdf->SetFillColor(hexdec(substr($row["Color"],1,2)),hexdec(substr($row["Color"],3,2)), hexdec(substr($row["Color"],5,2)));
				$pdf->Cell(64.8 , 5.2, '' . $row["Categoria_Desc"] . '', 0, 0 , 'C' , true);
				

            // Nombre Equipo  
	           // $pdf->SetXY($x+20.5-18.7,$y+54.5+28.45);
				//$pdf->SetXY($x+2-18.7,$y+11+30);  //anterior
				$pdf->SetXY($x+32.7,$y+7);
				$pdf->SetTextColor(0, 0, 0);
				
				$pdf->SetFont('Helvetica' , 'B' , 14);
				$pdf->SetAlpha(1);
				$pdf->SetFillColor(255 ,255, 255);
				$pdf->Cell(39 , 6.2, '' . az_utf8_decode($row["Equipo_FULLDESC"]) . '', 0, 0, 'C' , true);
				//	$pdf->Cell(22 , 6.2, '' . $row["Fecha_Nacimiento"] . '', 0, 0 , 'C' , true);

		     //Nombre de liga		
		//		$pdf->SetXY($x+20.5-18.7,$y+6+28);
		//		$pdf->SetTextColor(255, 255, 255);
		//		$pdf->SetFont('Helvetica' , 'B' , 14);
		//		$pdf->SetAlpha(1);
				//$pdf->SetFillColor(23, 204, 56);
		//		$pdf->SetFillColor(166, 132, 255);
		//		$pdf->Cell(33 , 5.22, 'Pro League', 0, 0 , 'C' , true);
		
		//		$pdf->SetXY($x+20.5-18.7,$y+6+5.22+28);
		//		$pdf->SetTextColor(0, 0, 0);
		//		$pdf->SetFont('Helvetica' , 'B' , 8.5);
		//		$pdf->SetAlpha(1);
		//		$pdf->SetFillColor(255 ,255, 255);
		//		$pdf->Cell(33 , 5.22, 'Volleyball Metepec', 0, 0 , 'C' , true);
				
		//		$pdf->SetXY($x+20.5-18.7,$y+8+10.44+28);
		//		$pdf->SetTextColor(255, 255, 255);
		//		$pdf->SetFont('Helvetica' , 'B' , 8.5);
		//		$pdf->SetAlpha(1);
		//		$pdf->SetFillColor(23, 128, 204);
		//		$pdf->Cell(33 , 5.22, az_utf8_decode($torneo_desc), 0, 0 , 'C' , true);
			
			
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Helvetica' , 'B' , 7.7);
				$pdf->SetAlpha(1);
			//	$pdf->SetFillColor(184 ,211, 220);
			$pdf->SetFillColor(255 ,255, 255);
				$pdf->SetXY($x+20.5-18.7,$y+30+28.45);
	//			$pdf->Cell(33 , 3.2, az_utf8_decode('' . $row["Apodo"] . ''), 0, 0 , 'L' , true);
			
				//$pdf->SetXY($x+2-18.7,$y+15+28.45+3.2+1.5); logo equipo
				//nombre jugador
				//$pdf->SetXY($x+2-18.7,$y+40+28.45+3.2+1.5);
				//$pdf->SetXY(17,$y+48+25+2.7); //anterior
				$pdf->SetXY($x+23.7,$y+14.5);
				$pdf->SetFont('Helvetica' , 'B' , 12);
				//$pdf->Cell(33	 , 3.2, az_utf8_decode('' . $row["Nombre"] . ' ' . $row["Apellido_P"] . ' ' . $row["Apellido_M"] . ''), 0, 'C' , true);
				$pdf->MultiCell(60	 , 5, az_utf8_decode('' . $row["Nombre"] . ' ' . $row["Apellido_P"] . ' ' . $row["Apellido_M"] . ''), 0, 'L' , true);
				
				//fecha nacimiento
				//$pdf->SetXY($x+22-18.7,$y+60.5+28); //Anterior
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetXY($x+23.7,$y+26.5);
			//	$pdf->SetXY($x+20.5-18.7,$y+30+28.45+(9.6)+(3.0));
				$pdf->SetFont('Helvetica' , 'B' , 12);
				$pdf->Cell(22 , 3.2, '' . $row["Fecha_Nacimiento"] . '', 0, 0 , 'L' , true);
				
			//Jugador
				//$pdf->SetXY($x+22-18.7,$y+60.5+28); //Anterior
				$pdf->SetXY($x+25.2,$y+32);
			//	$pdf->SetXY($x+20.5-18.7,$y+30+28.45+(9.6)+(3.0));
				$pdf->SetFont('Helvetica' , 'B' , 13);
				$pdf->Cell(22 , 3.2, az_utf8_decode('' . $row["Jugador"] . ''), 0, 0 , 'C' , true);
				
				
				$pdf->SetXY($x+20.5-18.7,$y+30+28.45+12.8+4.5); //Anterior
				//$pdf->SetXY($x+32.7,$y+17.5);
				$pdf->SetFont('Helvetica' , 'B' , 7.2);
	//			$pdf->Cell(33 , 3.2, '' . substr($row["Curp"],0,11) . 'XXXXXXX', 0, 0 , 'L' , true);

			
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Courier' , 'B' , 38);
				//$pdf->SetXY($x+ 5-26.7,$y+36+28);
				$pdf->SetXY($x+12-15.7,$y+14+28);
				$pdf->Cell(30 , 10.92, '' . $row["Numero"] . '', 0, 0 , 'C' , false);

				//$pdf->Image('http://chart.googleapis.com/chart?cht=qr&chs=200x200&chld=L|1&chf=bg,s,65432100&chl=' . $server . 'ajax/QR.php?Jugador_ID=' . $row["Jugador_ID"],$x+2-18.7,$y+72+28.45,15.68, 15.68, 'PNG');
				//$pdf->Image('https://qrcode.tec-it.com/API/QRCode?data=' . $server . 'ajax/QR.php?Jugador_ID=' . $row["Jugador_ID"],$x+2.3-18.7,$y+72+28.45,15.68, 15.68, 'PNG');
                az_pdf_qrcode($pdf, $fgmembersite, $row["Jugador_ID"],$x+89-18.7,$y+10.5+27.45,13.68, 13.68);

				$pdf->SetDrawColor(0 ,0, 0);

				try{
					$pdf->SetAlpha(1);
				//	az_pdf_image_file($pdf, $siteRoot, '/imagenes/Aztechnologies-S.png', $x+15.5-18.7,$y+80.08+28.40,26, 9.42);
			//		az_pdf_image_file($pdf, $siteRoot, '/imagenes/Aztechnologies-S.png', $x+50-18.7,$y+11.5+27.45,25.68, 15.68);
				}catch(Exception $e){
					echo $e;
				}
				
				$pdf->SetXY($x+4-15.7,$y+14+28);
			    az_pdf_image_file($pdf, $siteRoot, 'imagenes/fmvb.PNG' ,$x+58-18.7,$y+11.5+27.45,13.68, 13.68,'PNG'));
			    //az_pdf_image_file($pdf, $siteRoot, 'imagenes/fmvb.PNG' ,$x+65-18.7,$y+10.5+27.45,13.68, 13.68,'PNG'));
			  
			//    az_pdf_image_file($pdf, $siteRoot, '/imagenes/' . $Config->logo . '.png', $x+45-18.7,$y+10.5+27.45,13.68, 13.68);
			  //Fondo
			   	try{
			  	    $pdf->SetAlpha(.4);
			   $pdf->SetXY(2,62);
			    az_pdf_image_file($pdf, $siteRoot, 'imagenes/FondoAvi1.png' ,$x+39-18.7,$y+1.5+5.45,66, 43,'PNG'));
			 //     az_pdf_image_file($pdf, $siteRoot, 'imagenes/FondoAvi1.png' ,$x+39-18.7,$y+1.5+5.45,66, 39,'PNG'));
			   	}catch(Exception $e){
					echo $e;
				}
			    
			  
				try{
					$pdf->SetAlpha(.3);
					
				//	az_pdf_image_file($pdf, $siteRoot, '/imagenes/Aztechnologies-A.png', $x+20.5-18.7,$y+27.5+28.45,33, 41.5);
			//		az_pdf_image_file($pdf, $siteRoot, '/imagenes/Aztechnologies-A.png', $x+57-18.7,$y+1.5+5.45,27, 27);
				//	az_pdf_image_file($pdf, $siteRoot, '/imagenes/' . $Config->logo . '.png', $x+15-18.7,$y+36.5+28.45,33, 37);
					az_pdf_image_file($pdf, $siteRoot, '/imagenes/' . $Config->logo . '.png', $x+57-18.7,$y+.5+5.55,28, 25);
					//,$x+2+((15.68 - (15.68 * ($Config->logowidth / 110)))/2)-18.7,$y+5+((15.68 - (15.68 * ($Config->logoheight / 110)))/2)+28.45,(15.68 * ($Config->logowidth / 110)), (15.68 * ($Config->logoheight / 110)), 'PNG');
			//	az_pdf_image_file($pdf, $siteRoot, '/imagenes/Aztechnologies-A.png', $x+20.5-18.7,$y+27.5+28.45,33, 41.5);
				}catch(Exception $e){
					echo $e;
				}
				$pdf->Rotate(0);
				
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
