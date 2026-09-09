<?php
	require_once dirname(__DIR__) . '/site_paths.php';
	set_time_limit(300);
    require('qrcode/qrcode.class.php');
	require("alphapdf.php");
	require("membersite_config.php");
	$Config = new Configuration();
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('registrosEquipo.php');
	$Config->connect();

	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	$folder = substr(substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT'])),1,strlen(substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT'])))-5);

	$torneo = $_COOKIE[$Config->getAlias() . 'season'];
	$categoria = $_COOKIE[$Config->getAlias() . 'category'];

	$equipo = htmlspecialchars($_GET["Equipo_ID"]);
	$edad1 = htmlspecialchars($_GET["Edad1"]);	
	$edad2 = htmlspecialchars($_GET["Edad2"]);	

	$siteRoot = az_pdf_site_root($Config);

	$pdf = new AlphaPDF('P','mm','Letter');
	$pdf->SetAutoPageBreak(false);

	$Config->LoadLogo();
	$Config->LoadFlags();
	$Config->connect();

	// 5mm page margin: each credential slot is 5mm narrower and 2.5mm shorter than 108×70.
	$cardW = 103.0;
	$cardH = 67.5;
	$marginX = 5.0;
	$marginY = 5.0;

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
					d.Color_HEX,
					date_format(CURDATE(),'%d %M %Y') FechaAlta,
					e.Torneo_Desc
			FROM $schema.Jugadores a 
				join $schema.Equipos b on a.Equipo_ID = b.Equipo_ID and b.Torneo_ID = (select max(Torneo_ID) from $schema.Equipos where Equipo_ID = $equipo) 
				join $schema.Categorias c on b.Fuerza = c.Categoria_ID and c.Torneo_ID = $torneo
				join $schema.Colores d on c.Color = d.Color_HEX
				join $schema.Torneos e on b.Torneo_ID = e.Torneo_ID
			where a.Equipo_ID = $equipo and Estatus = 'A' and Validado = 1 and a.Foto is not null
				and YEAR(CURDATE())-YEAR(Fecha_Nacimiento) between $edad1 and $edad2
			order by convert(Numero,unsigned), Nombre, Apellido_P";
	//echo $sql;
	$result = $Config->query($sql);
	$players = array();
	if ($result && $result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$players[] = $row;
		}
	}

	/**
	 * Draw one Letter page of credentials (2 cols × 4 rows) — front side.
	 */
	$drawFrontPage = function ($pagePlayers) use ($pdf, $Config, $schema, $siteRoot, $cardW, $cardH, $marginX, $marginY, $fgmembersite) {
		$x = $marginX;
		$y = $marginY;
		$col = 0;
		$rowc = 0;
		foreach ($pagePlayers as $row) {
			try {
				$date = DateTime::createFromFormat("d/m/Y", $row["Fecha_Nacimiento"]);
				$birthDate = explode("/", $date->format("m/d/Y"));
				$age = date("Y") - $birthDate[2];
				$Edad = $age;

				$pdf->SetAlpha(1);
				if ($Config->credencialFrontImage == 1) {
					az_pdf_image_file($pdf, $siteRoot, 'pdf/Credencial.png', $x+0, $y+0, $cardW, $cardH);
				}
				az_pdf_player_photo($pdf, $Config, $schema, $row['Jugador_ID'], 'Foto', $x+10, $y+15, 26, 35);
				az_pdf_image_file($pdf, $siteRoot, 'imagenes/' . $row['Logo'] . '.png',$x+15.5,$y+45,15, 15);

				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetAlpha(1);
				$pdf->SetXY($x+40,$y+16);
				$pdf->SetFont('Helvetica' , '' , 11);
				$pdf->Cell(45 , 5, '' . az_utf8_decode($row["Equipo_FULLDESC"]) . '', 0, 0 , 'L' , false);
				$pdf->SetXY($x+40,$y+21);
				$pdf->Cell(45 , 5, '' . $row["Categoria_Desc"] . '', 0, 0 , 'L' , false);
				$pdf->SetXY($x+40,$y+26);
				$pdf->MultiCell(45	 , 5, az_utf8_decode('' . $row["Nombre"] . ' ' . $row["Apellido_P"] . ' ' . $row["Apellido_M"] . ''), 0, 'L' , false);
				$pdf->SetXY($x+40,$y+36);
				$pdf->Cell(65 , 5, 'Fech Nac ' . az_utf8_decode($row["Fecha_Nacimiento"]) . '', 0, 0 , 'L' , false);
				$pdf->SetXY($x+40,$y+41);
				$pdf->Cell(65 , 5, '' . az_utf8_decode(substr($row["Curp"],0,11)) . 'XXXXXXX', 0, 0 , 'L' , false);
				$pdf->SetXY($x+40,$y+46);
				$pdf->Cell(65 , 5, $row["FechaAlta"], 0, 0 , 'L' , false);
				// Top-right of card (clear of photo/text); drawn last with white pad in helper.
				az_pdf_qrcode($pdf, $fgmembersite, $row['Jugador_ID'], $x+81, $y+45, 20, 20);
				if ($col == 1) {
					$x = $marginX;
					$col = 0;
					if ($rowc == 3) {
						$y = $marginY;
						$rowc = 0;
					} else {
						$y = $y + $cardH;
						$rowc = $rowc + 1;
					}
				} else {
					$x = $x + $cardW;
					$col = $col + 1;
				}
			} catch (Exception $ae) {
			}
		}
	};

	/**
	 * Back side: CredencialDetras.png in each occupied slot.
	 * Columns are mirrored so duplex (long-edge) lines up with the fronts.
	 */
	$drawBackPage = function ($pagePlayers) use ($pdf, $Config, $siteRoot, $cardW, $cardH, $marginX, $marginY) {
		if ($Config->credencialBack != 1) {
			return;
		}
		$n = count($pagePlayers);
		for ($i = 0; $i < $n; $i++) {
			$col = $i % 2;
			$rowc = (int) floor($i / 2);
			$x = ($col === 0) ? ($marginX + $cardW) : $marginX;
			$y = $marginY + ($rowc * $cardH);
			try {
				$pdf->SetAlpha(1);
				if ($Config->credencialBackImage == 1) {
					az_pdf_image_file($pdf, $siteRoot, 'pdf/CredencialDetras.png', $x, $y, $cardW, $cardH);
				}
			} catch (Exception $ae) {
			}
		}
	};

	if (count($players) > 0) {
		$perPage = 8; // 2 columns × 4 rows
		$pages = array_chunk($players, $perPage);
		foreach ($pages as $pagePlayers) {
			$pdf->AddPage();
			$drawFrontPage($pagePlayers);
			if ($Config->credencialBack == 1) {
				$pdf->AddPage();
				$drawBackPage($pagePlayers);
			}
		}
	} else {
		$pdf->AddPage();
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont('Helvetica' , 'B' , 12);
		$pdf->Cell(200 , 8, 'No hay Jugadores dados de Alta o aun no han sido Validados', 0, 0 , 'C' , false);
	}

	$Config->close();
	$pdf->Output();
?>
