<?php
	session_start();
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

	error_reporting(1);
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
	$sessionstat = $fgmembersite->CheckLogin('jorndaAmin.js.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
    $retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
	
	$Season = $_COOKIE[$Config->getAlias() . 'season'];
    $Category = $_COOKIE[$Config->getAlias() . 'category'];
	$team = SanitizeInteger($_POST["Team"]);
	$htmlWeekVs = '';
	
	$htmlWeekVs .= '<option value="NULL">' . $lang['654'] . '</option>';

	$sql33 = "	SELECT a.Equipo_ID, 
						Equipo_DESC 
				 FROM $schema.Equipos a
				 where Torneo_ID = $Season
					and Activo = 1 
					and a.Fuerza = $Category 
					and Equipo_Id <> $team and Equipo_ID not in (	select Equipo2 Equipo_ID
										from (
											select Equipo1, Equipo2, count(*) ct
											from (
												select Juego_ID, Visitante_ID Equipo1, Local_ID as Equipo2
												from $schema.Juegos ju
													join $schema.Jornada jo on ju.Jornada_ID = jo.Jornada_ID
												where ju.Torneo_Id = $Season and Visitante_ID = $team and jo.Jornada_Type = 1 and ju.jugado <> 10
												UNION
												select Juego_ID, Local_ID Equipo1, Visitante_ID as Equipo2
												from $schema.Juegos ju
													join $schema.Jornada jo on ju.Jornada_ID = jo.Jornada_ID
												where ju.Torneo_Id = $Season and Local_ID = $team and jo.Jornada_Type = 1 and ju.jugado <> 10) a
											group by Equipo1, Equipo2
											having count(*) >= (select Rondas from $schema.Categorias where Torneo_Id = $Season and Categoria_ID = $Category)) a)
				 order by 2 asc;";
	//echo $sql33;
	$result33 = $Config->query($sql33);
	if ($result33->num_rows > 0) {
		// output data of each row
		while($row33 = $result33->fetch_assoc()) {
				$htmlWeekVs .= "<option value='" . $row33["Equipo_ID"] . "'>" . $row33["Equipo_DESC"] . "</option>";
		}
	}
	$retunData = array('status' => '1', 'message' => 'Success.', 'dataVs' => $htmlWeekVs, 'sql33' => $sql33);
    $Config->Close();
    echo json_encode($retunData);
?>