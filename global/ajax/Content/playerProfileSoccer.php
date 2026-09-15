<?php
    session_start();
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
$__APP_SITE_PATHS_START__ = __DIR__;
$__app_here = __DIR__;
for ($__i = 0, $__prev = null; $__i < 24; $__i++) {
	$__base = ($__i === 0) ? $__app_here : dirname($__app_here, $__i);
	if ($__base === $__prev) {
		break;
	}
	$__prev = $__base;
	$__inc = $__base . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'app_site_paths.inc.php';
	if (is_readable($__inc)) {
		require_once $__inc;
		break;
	}
}
unset($__i, $__prev, $__base, $__inc, $__app_here);

	require("membersite_config.php");
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('playerProfileSoccer.php');

	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');

	$Player = SanitizeInteger($_POST['Jugador_ID'] ?? $_POST['jugador_Id'] ?? $_POST['playerID'] ?? $_GET['Jugador_ID'] ?? $_GET['jugador_Id'] ?? $_GET['playerID'] ?? 0);
	$Season = SanitizeInteger($_COOKIE[$Config->getAlias() . 'season'] ?? 0);
	if ($Player <= 0) {
		echo json_encode($retunData);
		exit;
	}

	$Config->LoadFlags();
	$h = function ($v) {
		return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
	};

	$apellidos = 'Apellido_P,
                  Apellido_M,';
	if ($Config->jugadoresApellidos1) {
		$apellidos = '  SUBSTRING(Apellido_P, 1, 1) Apellido_P,
				        SUBSTRING(Apellido_M, 1, 1) Apellido_M,';
	}

	$Nombre = '';
	$Apellido_P = '';
	$Apellido_M = '';
	$Fecha_Nacimiento = '';
	$Numero = '';
	$Apodo = '';
	$Edad = '';
	$Validado = '';
	$Estatus = '';
	$Sexo = '';
	$Foto = '';
	$Goles = 0;
	$Amarillas = 0;
	$Rojas = 0;
	$Rojasdoble = 0;
	$Juegos = 0;
	$Pct = 0;
	$Equipo = '';
	$Categoria = '';
	$Logo = '';
	$EstatusLabel = '';

	$Config->query("SET NAMES 'utf8'");
	$sql = "SELECT a.Jugador_ID,
				Nombre,
				$apellidos
				date_format(Fecha_Nacimiento,'%d/%m/%Y') Fecha_Nacimiento,
				Numero,
				year(now())-year(a.Fecha_Nacimiento) as Edad,
				Apodo,
				Validado,
				Estatus,
				Sexo,
				eq.Equipo_FULLDESC,
				cat.Categoria_Desc,
				concat(eq.Torneo_ID,'-', eq.Equipo_ID) Logo,
				case
					when OCTET_LENGTH(Foto) is null and Sexo = 'H' then 'boy.png'
					when OCTET_LENGTH(Foto) is null and Sexo = 'M' then 'girl.png'
					when OCTET_LENGTH(Foto) is null and Sexo not in ('M','H') then 'boy.png'
					when OCTET_LENGTH(Foto) is not null then ''
				end FotoFile,
				ifnull(Goles,0) Goles,
				ifnull(Amarillas,0) Amarillas,
				ifnull(Rojas,0) Rojas,
				ifnull(Rojasdoble,0) Rojasdoble,
				ifnull(Juegos,0) Juegos,
				floor(ifnull((Juegos/(select Jornadas from $schema.Torneos where Torneo_ID = $Season))*100,0)) Pct
			FROM $schema.Jugadores a
				left outer join $schema.Equipos eq on a.Equipo_ID = eq.Equipo_ID and eq.Torneo_ID = $Season
				left outer join $schema.Categorias cat on eq.Fuerza = cat.Categoria_ID and cat.Torneo_ID = $Season
				left outer join (select Jugador_ID, sum(Goles) Goles from $schema.Goles where Jugador_ID = $Player and Torneo_ID = $Season group by Jugador_ID) b on a.Jugador_ID = b.Jugador_ID
				left outer join (select Jugador_ID, sum(Cantidad) Amarillas from $schema.Amonestados where Jugador_ID = $Player and Torneo_ID = $Season group by Jugador_ID) c on a.Jugador_ID = c.Jugador_ID
				left outer join (select Jugador_ID, sum(Cantidad) Rojas from $schema.Expulsados where Jugador_ID = $Player and Torneo_ID = $Season group by Jugador_ID) d on a.Jugador_ID = d.Jugador_ID
				left outer join (select Jugador_ID, sum(Cantidad) Rojasdoble from $schema.Expulsados where Doble = 2 and Jugador_ID = $Player and Torneo_ID = $Season group by Jugador_ID) e on a.Jugador_ID = e.Jugador_ID
				left outer join (select Jugador_ID, sum(Jugado) Juegos from $schema.JugadorJugado where Jugador_ID = $Player and Torneo_ID = $Season group by Jugador_ID) f on a.Jugador_ID = f.Jugador_ID
			where a.Jugador_ID = $Player
			limit 1";
	$result = $Config->query($sql);
	if (!$result || $result->num_rows === 0) {
		echo json_encode($retunData);
		exit;
	}
	$row2 = $result->fetch_assoc();
	$Nombre = $row2["Nombre"];
	$Apellido_P = $row2["Apellido_P"];
	$Apellido_M = $row2["Apellido_M"];
	$Fecha_Nacimiento = $row2["Fecha_Nacimiento"];
	$Numero = $row2["Numero"];
	$Apodo = $row2["Apodo"];
	$Edad = $row2["Edad"];
	$Validado = $row2["Validado"];
	$Estatus = $row2["Estatus"];
	$Sexo = $row2["Sexo"];
	$Foto = $row2["FotoFile"];
	$Goles = $row2["Goles"];
	$Amarillas = $row2["Amarillas"];
	$Rojas = $row2["Rojas"];
	$Rojasdoble = $row2["Rojasdoble"];
	$Juegos = $row2["Juegos"];
	$Pct = $row2["Pct"];
	$Equipo = $row2["Equipo_FULLDESC"];
	$Categoria = $row2["Categoria_Desc"];
	$Logo = $row2["Logo"];

	if ($Estatus == 'A') {
		$EstatusLabel = $lang['927'];
	} elseif ($Estatus == 'B') {
		$EstatusLabel = $lang['928'];
	} elseif ($Estatus == 'S') {
		$EstatusLabel = $lang['929'];
	}

	$imgsrc = ($Foto == '')
		? './Form/fetch_image.php?Jugador_ID=' . $Player . '&Imagen=Foto'
		: './imagenes/' . $Foto;
	$logoSrc = ($Logo !== '' && $Logo !== null) ? ('imagenes/' . $Logo . '.png') : '';

	$html = '<div class="container-fluid px-0">';
	$html .= '<div class="row g-2 align-items-start">';
	$html .= '<div class="col-12 col-sm-4 col-md-4 text-center">';
	$html .= '<img id="foto" src="' . $h($imgsrc) . '" alt="Foto" style="width:100%;max-width:220px;border-radius:8px;">';
	if ($logoSrc !== '') {
		$html .= '<div class="mt-2"><img src="' . $h($logoSrc) . '" alt="" style="height:42px;width:auto;"></div>';
	}
	$html .= '</div>';
	$html .= '<div class="col-12 col-sm-8 col-md-8">';
	$html .= '<h5 class="mb-1">' . $h($Nombre . ' ' . $Apellido_P . ' ' . $Apellido_M) . '</h5>';
	if ($Apodo !== '') {
		$html .= '<p class="mb-2 text-secondary">' . $h($Apodo) . ' &nbsp;#' . $h($Numero) . '</p>';
	} else {
		$html .= '<p class="mb-2 text-secondary">#' . $h($Numero) . '</p>';
	}
	$html .= '<table class="table align-items-center mb-0" style="border-color:#136aeb;">';
	$html .= '<tbody>';
	$html .= '<tr><td class="text-secondary text-xs">' . $h($lang['540']) . '</td><td class="text-secondary text-xs">' . $h($Equipo) . '</td></tr>';
	$html .= '<tr><td class="text-secondary text-xs">' . $h($lang['356']) . '</td><td class="text-secondary text-xs">' . $h($Categoria) . '</td></tr>';
	$html .= '<tr><td class="text-secondary text-xs">' . $h($lang['921']) . '</td><td class="text-secondary text-xs">' . $h($Fecha_Nacimiento) . '</td></tr>';
	$html .= '<tr><td class="text-secondary text-xs">' . $h($lang['910']) . '</td><td class="text-secondary text-xs">' . $h($Edad) . '</td></tr>';
	$html .= '<tr><td class="text-secondary text-xs">' . $h($lang['926']) . '</td><td class="text-secondary text-xs">' . $h($EstatusLabel) . '</td></tr>';
	$html .= '</tbody></table>';
	$html .= '</div></div>';

	$html .= '<div class="row text-center mt-3 mb-2">';
	$html .= '<div class="col-4 col-sm-2"><img src="./imagenes/gamePlayed.png" width="20" height="20" alt=""><div class="text-secondary text-xs">' . $h($Juegos) . '</div><div class="text-secondary text-xxs">' . $h($lang['100']) . '</div></div>';
	$html .= '<div class="col-4 col-sm-2"><span class="text-secondary text-xs font-weight-bold">%</span><div class="text-secondary text-xs">' . $h($Pct) . '</div></div>';
	$html .= '<div class="col-4 col-sm-2"><img src="./imagenes/goal.png" width="20" height="20" alt=""><div class="text-secondary text-xs">' . $h($Goles) . '</div></div>';
	$html .= '<div class="col-4 col-sm-2"><img src="./imagenes/amarilla.png" width="16" height="20" alt=""><div class="text-secondary text-xs">' . $h($Amarillas) . '</div></div>';
	$html .= '<div class="col-4 col-sm-2"><img src="./imagenes/roja.png" width="16" height="20" alt=""><div class="text-secondary text-xs">' . $h($Rojas) . '</div></div>';
	$html .= '<div class="col-4 col-sm-2"><img src="./imagenes/damarilla.png" width="20" height="20" alt=""><div class="text-secondary text-xs">' . $h($Rojasdoble) . '</div></div>';
	$html .= '</div>';

	$equipoId = 0;
	$eqRes = $Config->query("SELECT Equipo_ID FROM $schema.Jugadores WHERE Jugador_ID = $Player LIMIT 1");
	if ($eqRes && $eqRow = $eqRes->fetch_assoc()) {
		$equipoId = (int) $eqRow['Equipo_ID'];
	}

	if ($equipoId > 0 && $Season > 0) {
		$sqlWeeks = "SELECT j.Jornada_ID, j.Jornada_Desc, date_format(j.Fecha,'%d/%m/%Y') Fecha,
				ifnull(g.Goles,0) Goles,
				ifnull(y.Amarillas,0) Amarillas,
				ifnull(r.Rojas,0) Rojas,
				ifnull(p.Jugado,0) Jugado
			FROM $schema.Jornada j
				JOIN $schema.Categorias cat ON cat.Calendario_ID = j.Calendario_ID AND cat.Torneo_ID = $Season
				JOIN $schema.Equipos eq ON eq.Fuerza = cat.Categoria_ID AND eq.Equipo_ID = $equipoId AND eq.Torneo_ID = $Season
				LEFT JOIN (SELECT Jornada_ID, SUM(Goles) Goles FROM $schema.Goles WHERE Jugador_ID = $Player AND Torneo_ID = $Season GROUP BY Jornada_ID) g ON g.Jornada_ID = j.Jornada_ID
				LEFT JOIN (SELECT Jornada_ID, SUM(Cantidad) Amarillas FROM $schema.Amonestados WHERE Jugador_ID = $Player AND Torneo_ID = $Season GROUP BY Jornada_ID) y ON y.Jornada_ID = j.Jornada_ID
				LEFT JOIN (SELECT Jornada_ID, SUM(Cantidad) Rojas FROM $schema.Expulsados WHERE Jugador_ID = $Player AND Torneo_ID = $Season GROUP BY Jornada_ID) r ON r.Jornada_ID = j.Jornada_ID
				LEFT JOIN (SELECT Jornada_ID, SUM(Jugado) Jugado FROM $schema.JugadorJugado WHERE Jugador_ID = $Player AND Torneo_ID = $Season GROUP BY Jornada_ID) p ON p.Jornada_ID = j.Jornada_ID
			WHERE j.Torneo_ID = $Season
				AND EXISTS (
					SELECT 1 FROM $schema.Juegos ju
					WHERE ju.Jornada_ID = j.Jornada_ID AND ju.Torneo_ID = $Season
						AND (ju.Local_ID = $equipoId OR ju.Visitante_ID = $equipoId)
				)
			ORDER BY j.Fecha ASC, j.Jornada_ID ASC";
		$weeks = $Config->query($sqlWeeks);
		if ($weeks && $weeks->num_rows > 0) {
			$html .= '<div class="table-responsive mt-2">';
			$html .= '<table class="table align-items-center mb-0" style="border-color:#136aeb;">';
			$html .= '<thead><tr>';
			$html .= '<th class="text-uppercase text-secondary text-xxs font-weight-bolder">' . $h($lang['690']) . '</th>';
			$html .= '<th class="text-uppercase text-secondary text-xxs font-weight-bolder">' . $h($lang['604']) . '</th>';
			$html .= '<th class="text-center"><img src="./imagenes/gamePlayed.png" width="16" height="16" alt=""></th>';
			$html .= '<th class="text-center"><img src="./imagenes/goal.png" width="16" height="16" alt=""></th>';
			$html .= '<th class="text-center"><img src="./imagenes/amarilla.png" width="12" height="16" alt=""></th>';
			$html .= '<th class="text-center"><img src="./imagenes/roja.png" width="12" height="16" alt=""></th>';
			$html .= '</tr></thead><tbody>';
			while ($w = $weeks->fetch_assoc()) {
				$html .= '<tr>';
				$html .= '<td class="text-secondary text-xs">' . $h($w['Jornada_Desc']) . '</td>';
				$html .= '<td class="text-secondary text-xs">' . $h($w['Fecha']) . '</td>';
				$html .= '<td class="text-center text-secondary text-xs">' . $h($w['Jugado']) . '</td>';
				$html .= '<td class="text-center text-secondary text-xs">' . $h($w['Goles']) . '</td>';
				$html .= '<td class="text-center text-secondary text-xs">' . $h($w['Amarillas']) . '</td>';
				$html .= '<td class="text-center text-secondary text-xs">' . $h($w['Rojas']) . '</td>';
				$html .= '</tr>';
			}
			$html .= '</tbody></table></div>';
		}
	}

	$html .= '</div>';

	$retunData = array(
		'status' => '1',
		'message' => 'Success.',
		'dataTeamPlayerPreview' => $html,
		'dataPlayerProfile' => $html,
	);
	$Config->Close();
	echo json_encode($retunData, JSON_UNESCAPED_UNICODE);
?>
