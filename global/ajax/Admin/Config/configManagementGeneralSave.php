<?php
	session_start();

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

	//error_reporting(0);

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
	$sessionstat = $fgmembersite->CheckLogin('configManagementGeneralSave.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	$EmpatesPenalesA = SanitizeInteger($_POST["EmpatesPenales"]);
	$JugadorJugadoA = SanitizeInteger($_POST["JugadorJugado"]);
	$JuegoCedulasA = SanitizeInteger($_POST["JuegoCedulas"]);
	$MarcadorArbitroA = SanitizeInteger($_POST["MarcadorArbitro"]);
	$MarcadorFechaA = SanitizeInteger($_POST["MarcadorFecha"]);
	$JornadaCedulasA = SanitizeInteger($_POST["JornadaCedulas"]);
	$ShowIDColumnA = SanitizeInteger($_POST["columnid"]);
	$MarcadorDiaDefaultA = htmlspecialchars(SanitizeInteger($_POST["MarcadorDiaDefault"]));
	$IdiomaA = htmlspecialchars(SanitizeLang($_POST["lenguaje"]));
	$ByeWeekPointsA = SanitizeInteger($_POST["ByeWeekPoints"]);
	$ByeWeekPointsGoalsA = htmlspecialchars(SanitizeInteger($_POST["ByeWeekPointsGoals"]));
	$JuegoSemanalA = SanitizeInteger($_POST["juegoSemanal"]);
	$TresSetsA = SanitizeInteger($_POST["tressets"]);
	$PerfilJugadoresA = SanitizeInteger($_POST["perfilJugador"]);
	$JugadoresApellidos1 = SanitizeInteger($_POST["jugadoresApellidos1"]);
	$JuegosXNombre = SanitizeInteger($_POST["juegosXNombre"]);
	$CoachJuegos = SanitizeInteger($_POST["coachjuegos"]);
	$CoachJuegosDiaInicial = SanitizeInteger($_POST["coachjuegosdiainicial"]);
	$CoachJuegosDiaFinal = SanitizeInteger($_POST["coachjuegosdiafinal"]);
	$hora = SanitizeTime($_POST["hora"]);
	$hora2 = SanitizeTime($_POST["hora2"]);
	$tarjetaCambios = SanitizeInteger($_POST["tarjetaCambios"]);
	$VBByeWeekSets = SanitizeTime($_POST["VBByeWeekSets"]);
	$VBByeWeekPoints = SanitizeInteger($_POST["VBByeWeekPoints"]);
	$VBByeWeeSetkPoints = SanitizeInteger($_POST["VBByeWeekSetPoints"]);
	
	//echo $perfilJugadoresA;
		
	$retunData = array('status' => '0', 'message' => 'No insert.', 'dataConfigAnswer' => 'Error');
		
	$sql = "CALL $schema.ConfigGeneralUpdate('" . $_SESSION[$Config->getAlias() . 'username'] . "', '$EmpatesPenalesA', '$JugadorJugadoA', '$JuegoCedulasA', '$MarcadorArbitroA', '$MarcadorFechaA', '$MarcadorDiaDefaultA', '$JornadaCedulasA', '$IdiomaA', '$ShowIDColumnA', '$ByeWeekPointsA', '$ByeWeekPointsGoalsA', '$JuegoSemanalA', '$TresSetsA', '$PerfilJugadoresA' , '$JugadoresApellidos1' , '$JuegosXNombre' , '$CoachJuegos', '$CoachJuegosDiaInicial', '$CoachJuegosDiaFinal', '$hora', '$hora2', '$tarjetaCambios', '$VBByeWeekSets', '$VBByeWeekPoints', '$VBByeWeeSetkPoints', @out);";
	//echo $sql;
	
	$Connection = $Config->connectAdmin();
	$result = $Connection->query($sql);

	$sql1 = "Select @out as 'count'";
	$result = $Connection->query($sql1);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row2 = $result->fetch_assoc()) {
			$retunData = array('status' => '1', 'message' => 'Success.', 'dataConfigAnswer' => $lang['441'], 'sql' => $sql, 'updates' => $row2['count']);
		}
	}
	$Connection->Close();
    echo json_encode($retunData);
?>