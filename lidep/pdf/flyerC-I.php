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

	$jornada = htmlspecialchars($_GET['Jornada_ID']);
	
	$server = $fgmembersite->getSitename();

	$Config->LoadLogo();
	$Config->LoadFlags();
	
	$pdf = new FPDF('P','mm',array(210,210));
	$pdf->AddFont('Coluna','B','Coluna.php'); //Regular
	
	$sql = "SET lc_time_names = 'es_MX';";
	$result1 = $Config->query($sql);	
			
	$sql = "SELECT distinct j.Juego_ID,
                j.Torneo_ID,
                j.Jornada_ID,
                jo.Jornada_Desc,
                jo.Jornada_DescCorta,
                ca.Categoria_Desc,
                j.Local_ID,
                l.Equipo_FULLDESC,
                j.Visitante_ID,
                v.Equipo_FULLDESC,
                j.Campo_ID,
                c.Campo_DESC,
                TIME_FORMAT(j.Horario, '%H:%i HRS') Horario,
                DATE_FORMAT(j.Fecha, '%e de %M') Fecha
            FROM $schema.Juegos j
            	join $schema.Equipos l on j.Local_ID = l.Equipo_ID
            	join $schema.Equipos v on j.Visitante_ID = v.Equipo_ID
                join $schema.Jornada jo on jo.Jornada_ID = j.Jornada_ID
                join $schema.Campos c on c.Campo_ID = j.Campo_ID
                join $schema.Categorias ca on ca.Categoria_ID = l.Fuerza and ca.Torneo_ID = j.Torneo_ID
            where jo.Jornada_ID = $jornada
            order by ca.Categoria_ID, j.Fecha, j.Horario, c.Campo_DESC, j.Juego_ID asc";
	$result1 = $Config->query($sql);
	$pageLabels = array();
	if ($result1->num_rows > 0) {
		// output data of each row
		while($row1 = $result1->fetch_assoc()) {
			$pageLabels[] = 'juego-' . $row1['Juego_ID'];
			$localid = az_utf8_decode($row1["Local_ID"]);
			$visitanteid = az_utf8_decode($row1["Visitante_ID"]);
			$x = 0;
			$y = 0;
			$col = 0;
			$rowc = 0;
		
			$pdf->AddPage();
			$pdf->SetAutoPageBreak(false,1);
			$pdf->SetXY(0,0);
			$pdf->Image($server . '/pdf/FondoFlyer.png',0,0,210, 210, 'PNG');
			$pdf->SetFont('Coluna' , 'B' , 35);
			$pdf->SetTextColor(210, 44, 46);
			$pdf->Image($server . '/pdf/calendar.png',35,153,10, 10, 'PNG');
			$pdf->SetXY(45,155);
			$pdf->SetFont('Coluna' , 'B' , 35);
			$pdf->Cell(90 , 8, az_utf8_decode($row1["Fecha"]) . '', 45, 0 , 'L' , false);
			$pdf->Image($server . '/pdf/clock.png',120,153,10, 10, 'PNG');
			$pdf->SetXY(130,155);
			$pdf->SetFont('Coluna' , 'B' , 35);
			$pdf->Cell(90 , 8, az_utf8_decode($row1["Horario"]) . '', 35, 0 , 'L' , false);
			$pdf->Image($server . '/pdf/pointer.png',80,169,10, 10, 'PNG');
			$pdf->SetXY(90,170);
			$pdf->SetFont('Coluna' , 'B' , 35);
			$pdf->Cell(90 , 8, az_utf8_decode($row1["Campo_DESC"]) . '', 35, 0 , 'L' , false);
			$pdf->SetTextColor(255,255,255);
			$pdf->SetXY(45.5,155.5);
			$pdf->SetFont('Coluna' , 'B' , 35);
			$pdf->Cell(90 , 8, az_utf8_decode($row1["Fecha"]) . '', 45, 0 , 'L' , false);
			$pdf->SetXY(130.5,155.5);
			$pdf->SetFont('Coluna' , 'B' , 35);
			$pdf->Cell(90 , 8, az_utf8_decode($row1["Horario"]) . '', 35, 0 , 'L' , false);
			$pdf->SetXY(90.5,170.5);
			$pdf->SetFont('Coluna' , 'B' , 35);
			$pdf->Cell(90 , 8, az_utf8_decode($row1["Campo_DESC"]) . '', 35, 0 , 'L' , false);
			try{
				$pdf->Image($server . '/imagenes/Original/' . $row1["Torneo_ID"] . '-' . $row1["Local_ID"] . '.png',30,95,45, 45, 'PNG');
			}catch(Exception $e){
				try{
					$pdf->Image($server . '/imagenes/Original/' . $row1["Torneo_ID"] . '-' . $row1["Local_ID"] . '.png',30,95,45, 45, 'JPG');
				}catch(Exception $e){
					try{
						$pdf->Image($server . '/imagenes/Original/' . $row1["Torneo_ID"] . '-' . $row1["Local_ID"] . '.png',30,95,45, 45, 'JPEG');
					}catch(Exception $e){
						try{
							$pdf->Image($server . '/imagenes/Original/' . $row1["Torneo_ID"] . '-' . $row1["Local_ID"] . '.png',30,95,45, 45, 'GIF');
						}catch(Exception $e){
							//echo $e->getTraceAsString();
						}
					}
				}
			}
			try{
				$pdf->Image($server . '/imagenes/Original/' . $row1["Torneo_ID"] . '-' . $row1["Visitante_ID"] . '.png',135,95,45, 45, 'PNG');
			}catch(Exception $e){
				try{
					$pdf->Image($server . '/imagenes/Original/' . $row1["Torneo_ID"] . '-' . $row1["Visitante_ID"] . '.png',135,95,45, 45, 'JPG');
				}catch(Exception $e){
					try{
						$pdf->Image($server . '/imagenes/Original/' . $row1["Torneo_ID"] . '-' . $row1["Visitante_ID"] . '.png',135,95,45, 45, 'JPEG');
					}catch(Exception $e){
						try{
							$pdf->Image($server . '/imagenes/Original/' . $row1["Torneo_ID"] . '-' . $row1["Visitante_ID"] . '.png',135,95,45, 45, 'GIF');
						}catch(Exception $e){
							//echo $e->getTraceAsString();
						}
					}
				}
			}
            $pdf->SetXY(0,38);
			$pdf->SetFont('Coluna' , 'B' , 75);
			if(is_numeric($row1["Jornada_Desc"])){
			    $pdf->Cell(210 , 25, 'Jornada ' . az_utf8_decode($row1["Jornada_Desc"]) . '', 35, 0 , 'C' , false);
			}else{
			    $pdf->Cell(210 , 25, az_utf8_decode($row1["Jornada_Desc"]) . '', 35, 0 , 'C' , false);
			}
			//$pdf->Cell(210 , 25, '4TOS DE FINAL', 35, 0 , 'C' , false);
			$pdf->SetXY(0,70);
			$pdf->SetFont('Coluna' , 'B' , 60);
			$pdf->Cell(210 , 18, 'Categoria: ' . az_utf8_decode($row1["Categoria_Desc"]) . '', 35, 0 , 'C' , false);
			$pdf->SetTextColor(210, 44, 46);
			$pdf->SetXY(.5,38.5);
			$pdf->SetFont('Coluna' , 'B' , 75);
			if(is_numeric($row1["Jornada_Desc"])){
			    $pdf->Cell(210 , 25, 'Jornada ' . az_utf8_decode($row1["Jornada_Desc"]) . '', 35, 0 , 'C' , false);
			}else{
			    $pdf->Cell(210 , 25, az_utf8_decode($row1["Jornada_Desc"]) . '', 35, 0 , 'C' , false);
			}
			//$pdf->Cell(210 , 25, '4TOS DE FINAL', 35, 0 , 'C' , false);
			$pdf->SetXY(.5,70.5);
			$pdf->SetFont('Coluna' , 'B' , 60);
			$pdf->Cell(210 , 18, 'Categoria: ' . az_utf8_decode($row1["Categoria_Desc"]) . '', 35, 0 , 'C' , false);
			
			
		} 
	}else {
		$pageLabels[] = 'sin-juegos';
		$pdf->AddPage();
		$pdf->Cell(200 , 8, $lang['9998'], 0, 0 , 'C' , false);
	}
	$Config->close();

	$pdf_content = $pdf->Output('S');

	if (!extension_loaded('imagick')) {
		header('HTTP/1.1 500 Internal Server Error');
		header('Content-Type: text/plain; charset=UTF-8');
		echo 'Imagick extension is required to export flyer PNGs.';
		exit;
	}
	if (!class_exists('ZipArchive')) {
		header('HTTP/1.1 500 Internal Server Error');
		header('Content-Type: text/plain; charset=UTF-8');
		echo 'ZipArchive is required to download flyer PNGs.';
		exit;
	}

	$tmpDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'flyerC-I-' . uniqid('', true);
	if (!mkdir($tmpDir, 0700, true)) {
		header('HTTP/1.1 500 Internal Server Error');
		header('Content-Type: text/plain; charset=UTF-8');
		echo 'Could not create temporary directory.';
		exit;
	}

	$pngPaths = array();
	try {
		$imagick = new Imagick();
		$imagick->setResolution(150, 150);
		$imagick->readImageBlob($pdf_content);

		$pageIndex = 0;
		foreach ($imagick as $page) {
			$page->setImageBackgroundColor(new ImagickPixel('white'));
			$page->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
			$flat = $page->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
			$flat->setImageFormat('png');

			$label = isset($pageLabels[$pageIndex])
				? (string) $pageLabels[$pageIndex]
				: sprintf('page-%03d', $pageIndex + 1);
			$safeName = preg_replace('/[^\w\-]+/', '_', $label);
			$pngFile = $tmpDir . DIRECTORY_SEPARATOR . $safeName . '.png';
			$flat->writeImage($pngFile);
			$flat->destroy();
			$pngPaths[] = $pngFile;
			$pageIndex++;
		}
		$imagick->clear();
		$imagick->destroy();
	} catch (Throwable $e) {
		foreach ($pngPaths as $path) {
			if (is_file($path)) {
				@unlink($path);
			}
		}
		@rmdir($tmpDir);
		header('HTTP/1.1 500 Internal Server Error');
		header('Content-Type: text/plain; charset=UTF-8');
		echo 'Error generating flyer images: ' . $e->getMessage();
		exit;
	}

	if (count($pngPaths) === 0) {
		@rmdir($tmpDir);
		header('HTTP/1.1 500 Internal Server Error');
		header('Content-Type: text/plain; charset=UTF-8');
		echo 'No flyer pages were generated.';
		exit;
	}

	$zipPath = $tmpDir . DIRECTORY_SEPARATOR . 'flyers.zip';
	$zip = new ZipArchive();
	if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
		foreach ($pngPaths as $path) {
			@unlink($path);
		}
		@rmdir($tmpDir);
		header('HTTP/1.1 500 Internal Server Error');
		header('Content-Type: text/plain; charset=UTF-8');
		echo 'Could not create ZIP archive.';
		exit;
	}
	foreach ($pngPaths as $path) {
		$zip->addFile($path, basename($path));
	}
	$zip->close();

	$zipDownloadName = 'flyers-jornada-' . preg_replace('/\D+/', '', $jornada) . '.zip';
	header('Content-Type: application/zip');
	header('Content-Disposition: attachment; filename="' . $zipDownloadName . '"');
	header('Content-Length: ' . filesize($zipPath));
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Pragma: no-cache');
	readfile($zipPath);

	foreach ($pngPaths as $path) {
		@unlink($path);
	}
	@unlink($zipPath);
	@rmdir($tmpDir);
	exit;
