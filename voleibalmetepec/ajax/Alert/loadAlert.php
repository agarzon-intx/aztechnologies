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
	
	$sessionstat = $fgmembersite->CheckLogin('loadAlert.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
    $retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
    
    $htmlAlert = '';
	
	$alert = SanitizeInteger($_POST['alert']);
	$Config->LoadFlags();

	$titulo = "";
	$avisoid = "";
	$sql = "SELECT Aviso_ID, 
				Aviso_Fecha_Inicio,
				Aviso_Fecha_Fin,
				Aviso_Contenido,
				Aviso_Titulo,
				Aviso_Tipo
			FROM $schema.Avisos
			where Aviso_Fecha_Inicio <= cast(now() as Date) and Aviso_Fecha_Fin >= cast(now() as Date) 
				and Aviso_Estatus = '1' and Aviso_ID = $alert
			order by Aviso_ID desc
			LIMIT 1;";
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row2 = $result->fetch_assoc()) {
			$titulo = $row2["Aviso_Titulo"];
			$avisoid = $row2["Aviso_ID"];
		}
	}
	
	
	if($avisoid != ""){
		$out = "";
		$sql2 = "SELECT Aviso_Contenido
				FROM $schema.Avisos
				where Aviso_ID = $avisoid;";
		$result2 = $Config->query($sql2);
		if ($result2->num_rows > 0) {
		// output data of each row
			while($row2 = $result2->fetch_assoc()) {
				$out = trim($row2["Aviso_Contenido"]);
			}
		} 
		$out = str_replace("\"", "'", $out);
	}

    $htmlAlert .= '     <div style="background-color:#f1f1f1; height:30px; border-bottom:1px #e5e5e5 solid; width: 95%;margin: auto;">
					        <div style="float: left; width: 100%; font-size: 24px; color: #333; line-height: 0px; margin-left: 0px; background-color:#f1f1f1;"></div>
					        <div style="float:right;" style="background-color:#f1f1f1">
						        <img id="video-zione-cerrar" style="width:30px;height:30px;cursor:pointer;" src="imagenes/cerrarVentana.png" onClick="closeAlert()"/>
					        </div>
				        </div>
				        <div class="end" style="height: 95%;background: white; width: 95%;margin: auto; text-align: left; overflow-y: auto;" id="alertContent">' . htmlspecialchars_decode($out) . '</div>';
		  
   	$retunData = array('status' => '1', 'message' => 'Success.', 'dataAlert' => $htmlAlert);
    $Config->Close();
    echo json_encode($retunData);
?>