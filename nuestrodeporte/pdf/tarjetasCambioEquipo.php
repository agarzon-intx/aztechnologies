<?php
	require_once dirname(__DIR__) . '/site_paths.php';
	require("alphapdf.php");
	require("membersite_config.php");
	$Config = new Configuration();
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('tarjetasCambioEquipo.php');
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
    $folder = substr(substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT'])),1,strlen(substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT'])))-5);

	$equipo = htmlspecialchars($_GET["Equipo_ID"]);

	$Season = $_COOKIE[$Config->getAlias() . 'season'];
	
	$server = $fgmembersite->getSitename();

	$logo = "";
	$equipoDesc = "";
	
	$x = 0;
	$y = 5;
	$col = 0;
	$rowc = 0;
	
	$Config->connect();
    $Config->LoadLogo();
    
	$pdf = new AlphaPDF('P','mm','Letter');
	$pdf->SetAutoPageBreak(false);
	$pdf->AddPage();

	$sql = "select a.*, concat(a.Torneo_ID,'-', a.Equipo_ID) newLogo 
	        from $schema.Equipos a
		        join (  select max(Torneo_ID) Torneo_ID, Equipo_ID 
		                from $schema.Equipos 
		                where Torneo_ID = " . $Season . "
		                group by Equipo_ID) b on a.Equipo_ID = b.Equipo_ID and a.Torneo_ID = b.Torneo_ID
		    where Activo = 1 and a.Equipo_ID = $equipo;";
	//echo $sql;
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			$logo = $row["newLogo"];
			$equipoDesc = $row["Equipo_DESC"];
			//echo '/imagenes/Original/' . $logo . '.png';
		}
	}
	for($rowc = 0; $rowc < 4; $rowc++){
		for($col = 0; $col < 2; $col++){
			$pdf->SetXY($x+6,$y+47);
			$pdf->SetAlpha(.4);
			try{
			    $headers = @get_headers($server . '/imagenes/Original/' . $logo . '.png');
				if($headers && strpos($headers[0], '200 OK') !== false){
    				try{
    					$pdf->Image($server . '/imagenes/Original/' . $logo . '.png',$x+31,$y+6,52, 52, 'PNG');
    				}catch(Exception $e){
    					try{
        					$pdf->Image($server . '/imagenes/Original/' . $logo . '.png',$x+31,$y+6,52, 52, 'JPG');
        				}catch(Exception $e){
        					try{
            					$pdf->Image($server . '/imagenes/Original/' . $logo . '.png',$x+31,$y+6,52, 52, 'JPEG');
            				}catch(Exception $e){
            					try{
                					$pdf->Image($server . '/imagenes/Original/' . $logo . '.png',$x+31,$y+6,52, 52, 'GIF');
                				}catch(Exception $e){
                					echo $e->getTraceAsString();
                				}
            				}
        				}
    				}
				}
			}catch(Exception $e){
				echo $e;
			}
			$pdf->SetTextColor(40, 151, 101);
			$pdf->SetFont('Courier' , 'B' , 40);
			$pdf->SetXY($x+60,$y+47);
			$pdf->SetAlpha(.3);
			try{
			    $headers = @get_headers($server . '/imagenes/' . $Config->logo . '.png');
				if($headers && strpos($headers[0], '200 OK') !== false){
					$pdf->Image($server . '/imagenes/' . $Config->logo . '.png',$x+88+((15 - (15 * ($Config->logowidth / 110)))/2),$y+7+((15 - (15 * ($Config->logoheight / 110)))/2),(15 * ($Config->logowidth / 110)), (15 * ($Config->logoheight / 110)), 'PNG');
				}
			}catch(Exception $e){
				echo $e;
			}
			$pdf->SetXY($x+25,$y+4);
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetFont('Helvetica' , '' , 12);
			$pdf->SetAlpha(1);
			$pdf->Cell(65 , 8, $lang['970'], 0, 0 , 'C' , false);
			$pdf->SetXY($x+6,$y+13);
			$pdf->Cell(65 , 8, $lang['971'] . strtoupper($equipoDesc), 0, 0 , 'L' , false);
			$pdf->SetXY($x+87,$y+13);
			$pdf->Cell(65 , 8, $lang['972'], 0, 0 , 'L' , false);
			$pdf->SetXY($x+6,$y+22);
			$pdf->Cell(65 , 8, $lang['973'], 0, 0 , 'L' , false);
			$pdf->SetXY($x+84,$y+22);
			$pdf->Cell(65 , 8, $lang['974'], 0, 0 , 'L' , false);
			$pdf->SetXY($x+6,$y+34);
			$pdf->Cell(65 , 8, $lang['975'], 0, 0 , 'L' , false);
			$pdf->SetXY($x+84,$y+31);
			$pdf->Cell(65 , 8, $lang['974'], 0, 0 , 'L' , false);
			$pdf->SetXY($x+6,$y+41);
			$pdf->Cell(65 , 8, $lang['976'], 0, 0 , 'L' , false);
			$pdf->SetXY($x+67,$y+41);
			$pdf->Cell(65 , 8, $lang['977'], 0, 0 , 'L' , false);
			$pdf->SetXY($x+6,$y+50);
			$pdf->Cell(65 , 8, $lang['978'], 0, 0 , 'L' , false);
			$pdf->SetXY($x+67,$y+50);
			$pdf->Cell(65 , 8, $lang['977'], 0, 0 , 'L' , false);
			$x = 105;
		}
		$y = $y + 65;
		$x = 0;
	}
	
	$Config->close();
	$pdf->Output();
?>