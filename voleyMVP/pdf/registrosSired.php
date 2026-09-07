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
	$x = $x + 45.25;
	$y = $y + 1;
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
				//	$y = 1.2;
				//	$x = 4.25;
					
				}
				$colorR = 0;
				$colorG = 0;
				$colorB = 0;

				$colorR = 255;
				$colorG = 255;
				$colorB = 255;
				$pdf->SetDrawColor(0, 0, 0);
            
             	$pdf->SetXY($x+5,$y+4);
			    $pdf->Cell(115, 79, '', 1, 0, 'C' , false);
			  
			 //  $pdf->SetXY(15,2);
			 //   az_pdf_image_file($pdf, $siteRoot, 'imagenes/Siafilia.png' ,60, 11.2, 55, 0 ,'PNG'));
			    
			//	$pdf->SetXY(15,2);
			 //   az_pdf_image_file($pdf, $siteRoot, 'imagenes/fmvb.PNG' ,165, 12, 14, 0 ,'PNG'));
			    
			  //  $pdf->SetXY(15,2);
			   // az_pdf_image_file($pdf, $siteRoot, 'imagenes/Fivb.PNG' ,120, 11.2, 32, 0 ,'PNG'));
			    
			
				$pdf->SetXY($x+10,$y+19);
				$pdf->SetFont('Arial','',10);
				$pdf->SetFillColor($colorR ,$colorG, $colorB);
				
			
				
				//$pdf->Rect($x, $y, 85.2, 53.8 , 'DF');
				$pdf->Rotate(0,$x+22,$y+13);
			//$pdf->SetXY(145,10);
		//    $pdf->Cell(11 , 6, $lang['10136'], 1, 1 , 'L' , false);
		   //	$pdf->Cell(45, 5, 'BBBB' , 1,1, 'L' , false);
		   	
		   	//	$pdf->SetXY($x+20.5-18.7,$y+30+28.45+3.2+1.5);
			//	$pdf->SetFont('Helvetica' , 'B' , 7.2);
		//	 az_utf8_decode($row1["Visitante"])
	          
	          
		    	$pdf->Cell(11, 5, 'Nombre  ' , 0,1, 'L' , false);
		    	
				$pdf->SetFont('Arial','',13);
		    	$pdf->SetXY($x+28,$y+19); //30 luego 24
				$pdf->Cell (45 , 5, az_utf8_decode($row["Nombre"]),0 ,0, 'L', false);
				
				$pdf->SetXY($x+28,$y+23);  //35 luego 29
				$pdf->Cell (55 , .1, '' ,1 , 1, 'L', false);
		    	
				
				$pdf->SetXY($x+15,$y+37);
				$pdf->SetFont('Arial','',10);
				$pdf->Cell(19, 5, 'Apellidos  ' , 0,1, 'L' , false);
				
				$pdf->SetFont('Arial','',13);
				$pdf->SetXY($x+32,$y+37);
				$pdf->Cell (40 , 5, az_utf8_decode($row["Apellido_P"] . ' ' . $row["Apellido_M"] . ''), 0, 'L' , true);
				$pdf->SetXY($x+38,$y+42);
				$pdf->Cell (55 , .1, '' ,1 , 1, 'L', false);
									
				$pdf->SetXY($x+15,$y+44);
				$pdf->SetFont('Arial','',10);
				$pdf->Cell(36, 5, 'Fecha de Nacimiento  ' , 0,1, 'L' , false);
					
				$pdf->SetFont('Arial','',13);
				$pdf->SetXY($x+55,$y+44);
				$pdf->Cell (45 , 5, $row["Fecha_Nacimiento"],0 , 1, 'L', false);
				
				$pdf->SetXY($x+53,$y+49);
				$pdf->Cell (40 , .1, '' ,1 , 1, 'L', false);
									
				
				$pdf->SetXY($x+15,$y+51);
				$pdf->SetFont('Arial','',10);
				$pdf->Cell(24, 5, 'Club o Equipo  ' , 0,1, 'L' , false);
					
				$pdf->SetFont('Arial','',13);
				$pdf->SetXY($x+41,$y+51);
				$pdf->Cell (45 , 5, az_utf8_decode($row["Equipo_FULLDESC"]) ,0 , 1, 'L', false);
				$pdf->SetXY($x+40,$y+56);
				$pdf->Cell (53 , .1, '' ,1 , 1, 'L', false);
				
		
		    	$pdf->SetXY($x+15,$y+59);
				$pdf->SetFont('Arial','',10);
				$pdf->Cell(24, 5, 'Categoria  ' , 0,1, 'L' , false);
				
			    $pdf->SetFont('Arial','',14);
				$pdf->SetXY($x+42,$y+59);
				$pdf->Cell (45 , 5, az_utf8_decode($row["Categoria_Desc"] ) ,0 , 1, 'L', false);
				
				$pdf->SetXY($x+40,$y+64);
				$pdf->Cell (53 , .1, '' ,1 , 1, 'L', false);
				
				$pdf->SetXY($x+15,$y+66);
				$pdf->SetFont('Arial','',10);
				$pdf->Cell(20, 5,az_utf8_decode('Asociación  ') , 0,1, 'L' , false);
				
			    $pdf->SetFont('Arial','',12);
				$pdf->SetXY($x+38,$y+66);
				$pdf->Cell (10 , 5, ' AMEV ' ,0 , 1, 'L', false);
				
				$pdf->SetXY($x+38,$y+71);
				$pdf->Cell (20 , .1, '' ,1 , 1, 'L', false);
			
				$pdf->SetXY($x+60,$y+66);
				$pdf->SetFont('Arial','',10);
				$pdf->Cell(10, 5,az_utf8_decode('Liga  ') , 0,1, 'L' , false);
				
			   	$pdf->SetXY($x+72,$y+71);
				$pdf->Cell (30 , .1, '' ,1 , 1, 'L', false);
				
			    $pdf->SetXY($x+15,$y+73);
				$pdf->SetFont('Arial','',10);
				$pdf->Cell(20, 5,az_utf8_decode('Función  ') , 0,1, 'L' , false);
				
				 $pdf->SetFont('Arial','',15);
				$pdf->SetXY($x+38,$y+78);
				$pdf->Cell (40 , .1, '' ,1 , 1, 'L', false);
			   
			    $pdf->SetXY($x+100,$y+79);
				$pdf->SetFont('Arial','',10);
				$pdf->Cell(20, 5,az_utf8_decode('Firma  ') , 0,0, 'C' , false);
				
				 $pdf->SetFont('Arial','',15);
				$pdf->SetXY($x+95,$y+78);
				$pdf->Cell (28 , .1, '' ,1 , 1, 'L', false);
			   
			   
			    $pdf->SetXY($x+15,$y+81);
			    $pdf->SetTextColor(255, 255, 255);
			    $pdf->SetFillColor(244, 67, 54);
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(60, 5,az_utf8_decode('Costo Máximo al afiliado: $150.00') , 1,1, 'L' , true);
			
			   
			    
				try{
			//		$pdf->SetAlpha(1);
			//		az_pdf_image_file($pdf, $siteRoot, '/imagenes/' . $Config->logo . '.png', $x+2+((25.68 - (15.68 * ($Config->logowidth / 110)))/2)-18.7,$y+1+((25.68 - (15.68 * ($Config->logoheight / 110)))/2)+28.45,(15.68 * ($Config->logowidth / 110)), (15.68 * ($Config->logoheight / 110)));
				}catch(Exception $e){
					echo $e;
				}
    
				try{
					$pdf->SetAlpha(1);
					az_pdf_player_photo($pdf, $Config, $schema, $row["Jugador_ID"], 'Foto', $x+89,$y+6+17,25.68, 32.36);
				//	az_pdf_player_photo($pdf, $Config, $schema, $row["Jugador_ID"], 'Foto', $x+2-18.7,$y+30+28.45,15.68, 20.38);
				}catch(Exception $e){
					try{
    					$pdf->SetAlpha(1);
    					az_pdf_player_photo($pdf, $Config, $schema, $row["Jugador_ID"], 'Foto', $x+2-18.7,$y+30+28.45,15.68, 20.38);
    				}catch(Exception $e){
    					try{
        					$pdf->SetAlpha(1);
        					az_pdf_player_photo($pdf, $Config, $schema, $row["Jugador_ID"], 'Foto', $x+2-18.7,$y+30+28.45,15.68, 20.38);
        				}catch(Exception $e){
        					try{
            					$pdf->SetAlpha(1);
            					az_pdf_player_photo($pdf, $Config, $schema, $row["Jugador_ID"], 'Foto', $x+2-18.7,$y+30+28.45,15.68, 20.38);
            				}catch(Exception $e){
            					echo $e->getMessage();
            				}
        				}
    				}
				}
				
				try{
					$pdf->SetAlpha(2);
					//az_pdf_image_file($pdf, $siteRoot, '/imagenes/' . $row["Logo"] . '.png', $x+75-18.7,$y+54.5+28.45,15.68, 15.68);
					/*Logo del equipo*/
						az_pdf_image_file($pdf, $siteRoot, '/imagenes/' . $row["Logo"] . '.png', $x+98-15.7,$y+47+28.45,12, 12);
				}catch(Exception $e){
					echo $e;
				}
				
				$pdf->SetXY($x+20.5-18.7,$y+6+28);
			//	$pdf->SetXY($x+20.5,$y+6+28);
				$pdf->SetTextColor(255, 255, 255);
				$pdf->SetFont('Helvetica' , 'B' , 7.8);
				$pdf->SetAlpha(1);
			//	$pdf->SetFillColor(23, 204, 56);
			//	$pdf->Cell(33 , 5.22, 'Ligaaaaa Municipal de', 0, 0 , 'C' , true);
			//	$pdf->SetXY($x+20.5-18.7,$y+6+5.22+28);
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Helvetica' , 'B' , 7.8);
				$pdf->SetAlpha(1);
			//	$pdf->SetFillColor(255 ,255, 255);
			//	$pdf->Cell(33 , 5.22, 'Voleiboooooool Metepec', 0, 0 , 'C' , true);
			//	$pdf->SetXY($x+20.5-18.7,$y+6+10.44+28);
				$pdf->SetTextColor(255, 255, 255);
				$pdf->SetFont('Helvetica' , 'B' , 7.8);
				$pdf->SetAlpha(1);
			//	$pdf->SetFillColor(23, 128, 204);
			//	$pdf->Cell(33 , 5.22, az_utf8_decode($torneo_desc), 0, 0 , 'C' , true);
			//	$pdf->SetXY($x+2-18.7,$y+28+6+15.66+2.75);
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Helvetica' , 'B' , 7.8);
				$pdf->SetAlpha(1);
			//	$pdf->SetFillColor(hexdec(substr($row["Color"],1,2)),hexdec(substr($row["Color"],3,2)), hexdec(substr($row["Color"],5,2)));
			//	$pdf->Cell(51.5 , 3.2, '' . $row["Categoria_Desc"] . '', 0, 0 , 'C' , true);
/*
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Helvetica' , 'B' , 7.2);
				$pdf->SetAlpha(1);
				$pdf->SetFillColor(184 ,211, 220);
				$pdf->SetXY($x+20.5-18.7,$y+30+28.45);
				$pdf->Cell(33 , 3.2, az_utf8_decode('' . $row["Apodo"] . ''), 0, 0 , 'L' , true);
				
				$pdf->SetXY($x+20.5-18.7,$y+30+28.45+3.2+1.5);
				$pdf->SetFont('Helvetica' , 'B' , 7.2);
				$pdf->MultiCell(33	 , 3.2, az_utf8_decode('' . $row["Nombre"] . ' ' . $row["Apellido_P"] . ' ' . $row["Apellido_M"] . ''), 0, 'L' , true);
		*/		
			/*	
				$pdf->SetXY($x+20.5-18.7,$y+30+28.45+(9.6)+(3.0));
				$pdf->SetFont('Helvetica' , 'B' , 7.2);
				$pdf->Cell(33 , 3.2, '' . $row["Fecha_Nacimiento"] . '', 0, 0 , 'L' , true);
				
				$pdf->SetXY($x+20.5-18.7,$y+30+28.45+12.8+4.5);
				$pdf->SetFont('Helvetica' , 'B' , 7.2);
				$pdf->Cell(33 , 3.2, '' . substr($row["Curp"],0,11) . 'XXXXXXX', 0, 0 , 'L' , true);
             
				$pdf->SetXY($x+20.5-18.7,$y+54.5+28.45);
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Helvetica' , 'B' , 7.8);
				$pdf->SetAlpha(1);
				$pdf->SetFillColor(184 ,211, 220);
				$pdf->MultiCell(33 , 3.2, '' . az_utf8_decode($row["Equipo_FULLDESC"]) . '', 0 , 'C' , true);
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Courier' , 'B' , 24);
				$pdf->SetXY($x+20.5-18.7,$y+54.5+28.45+5);
				$pdf->Cell(33 , 10.92, '' . $row["Numero"] . '', 0, 0 , 'C' , false);
			*/	

			//	$pdf->Image('http://chart.googleapis.com/chart?cht=qr&chs=200x200&chld=L|1&chf=bg,s,65432100&chl=' . $server . 'ajax/QR.php?Jugador_ID=' . $row["Jugador_ID"],$x+2-18.7,$y+72+28.45,15.68, 15.68, 'PNG');

				$pdf->SetDrawColor(0 ,0, 0);

				try{
					$pdf->SetAlpha(1);
					//az_pdf_image_file($pdf, $siteRoot, '/imagenes/Aztechnologies-S.png', $x+20.5-18.7,$y+78.08+28.45,33, 9.42);
					az_pdf_image_file($pdf, $siteRoot, '/imagenes/Fivb.PNG', $x+80-18.7,$y+.2+5,30, 18);
					az_pdf_image_file($pdf, $siteRoot, '/imagenes/fmvb.PNG', $x+117-18.5,$y+.2+6,15, 14);
					az_pdf_image_file($pdf, $siteRoot, '/imagenes/Siafilia.png', $x+26-18.7,$y+.2+6,47, 14);
			
				}catch(Exception $e){
					echo $e;
				}	
				try{
					$pdf->SetAlpha(.3);
					//az_pdf_image_file($pdf, $siteRoot, '/imagenes/Aztechnologies-A.png', $x+20.5-18.7,$y+27.5+28.45,33, 41.5);
				//	az_pdf_image_file($pdf, $siteRoot, '/imagenes/Fondofibv.png', $x+26-18.7,$y+6+16.45,81, 46.5);
				}catch(Exception $e){
					echo $e;
				}
				$pdf->Rotate(0);
				
				$y = $y +90;
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
