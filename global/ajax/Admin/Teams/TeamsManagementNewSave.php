<?php
	namespace Verot\Upload;
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
	$sessionstat = $fgmembersite->CheckLogin('TeamsManagementNewSave.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	$descripcion = str_replace("'","''",htmlspecialchars($_POST["descripcion"]));
	$estatus = SanitizeInteger($_POST["estatus"]); 
	$fuerza = $_POST["fuerza"]; 
	$descripcionLarga = str_replace("'","''",htmlspecialchars($_POST["descripcionlarga"]));
	$campo = SanitizeInteger($_POST["campo"]); 
	$playera = sanitizeHexColor($_POST["playera"]); 
	$short = sanitizeHexColor($_POST["short"]); 
	$calcetas = sanitizeHexColor($_POST["calcetas"]);
	$desc3 = SanitizeText($_POST["desc3"]);
	$institucion = SanitizeInteger($_POST["institucion"]);
	$nombreColorValue = isset($_POST["nombreColor"]) ? trim((string) $_POST["nombreColor"]) : '';
	$credencialColorValue = isset($_POST["credencialColor"]) ? trim((string) $_POST["credencialColor"]) : '';
	$nombreColor = sanitizeHexColor($nombreColorValue !== '' ? $nombreColorValue : "#000000");
	$credencialColor = sanitizeHexColor($credencialColorValue !== '' ? $credencialColorValue : "#000000");
	$credencialColorSql = $credencialColorValue === '' ? 'NULL' : "'" . $credencialColor . "'";
	$logoFileName = $_POST["file"];
	
	$__a = $Config->getAlias();
	$Season = (isset($_COOKIE[$__a . 'season']) && $_COOKIE[$__a . 'season'] !== '') ? (int) $_COOKIE[$__a . 'season'] : 0;
	$activeSeason = 0;
	$resultSeason = $Config->query("SELECT Torneo_ID FROM $schema.Torneos WHERE Actual = 'S' LIMIT 1");
	if ($resultSeason && ($rowSeason = $resultSeason->fetch_assoc())) {
		$activeSeason = (int) $rowSeason['Torneo_ID'];
	}
	if ($Season <= 0) {
		$Season = $activeSeason;
	}

	$retunData = array('status' => '0', 'message' => 'No insert.', 'dataTeamdAnswer' => 'Error');
	if ($Season <= 0) {
		$retunData['dataTeamdAnswer'] = $lang['206'];
		echo json_encode($retunData);
		exit;
	}
	// TeamCreate always inserts into the active season; other seasons are inserted directly.
	$createOtherSeason = ($Season !== $activeSeason);
	$myArrayFuerza = explode(',', $fuerza);
	$length = count($myArrayFuerza); 

    $target_dir = ".";
    	
	$target_dir = "../imagenes/Original/";
	$target_dir1 = "../imagenes/";
	
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    	chdir('..\\..\\..\\tmp');
		$target_dir = "..\\imagenes\\Original\\";
		$target_dir1 = "..\\imagenes\\";
	}else{
		chdir('../../../tmp');
		$target_dir = "../imagenes/Original/";
		$target_dir1 = "../imagenes/";
	}
	
    for ($i = 0; $i < $length; $i++) { 
        $fuerza = (int) $myArrayFuerza[$i];
        if ($createOtherSeason) {
        	$sql1 = "INSERT INTO $schema.Equipos (Equipo_DESC, Activo, Fuerza, Logo, Equipo_FULLDESC, Torneo_ID, Campo_ID, Playera, Short, Calcetas, Equipo_DESC3, Institucion_ID, Nombre_Color, Credencial_Color)
        		VALUES ('$descripcion', $estatus, $fuerza, '', '$descripcionLarga', $Season, $campo, '$playera', '$short', '$calcetas', '$desc3', $institucion, '$nombreColor', $credencialColorSql);";
        } else {
        	$sql1 = "CALL $schema.TeamCreate('" . $_SESSION[$__a . 'username'] . "', '$descripcion', '$descripcionLarga', $fuerza, $estatus, $campo, '$playera', '$short', '$calcetas', '$desc3', $institucion, '$nombreColor', $credencialColorSql, @out);";
        }
    	$Connection = $Config->connectAdmin();
    	try {
    		$result = $Connection->query($sql1);
    	} catch (\Throwable $e) {
    		$result = false;
    	}
    	if ($result === false) {
    		error_log('TeamCreate failed: ' . $Connection->error);
    		$retunData['dataTeamdAnswer'] = $lang['206'];
    		$Connection->close();
    		echo json_encode($retunData);
    		exit;
    	}
    	if ($createOtherSeason) {
    		$insertedRows = (int) $Connection->affected_rows;
    		$Connection->query("SET @out = $insertedRows");
    		$Connection->query("CALL $schema.insertIntoControlTable('" . $_SESSION[$__a . 'username'] . "', 'CREATE', 'TEAM', '" . $Connection->real_escape_string(preg_replace('/\s+/', ' ', $sql1)) . "', 'OK, total inserts: $insertedRows')");
    	}
    	
    	$sql2 = "Select @out as 'count'";
    	$result = $Connection->query($sql2);
    	if ($result && $result->num_rows > 0) {
    		// output data of each row
    		while($row2 = $result->fetch_assoc()) {
    			$retunData = array('status' => '1', 'message' => 'Success.', 'dataTeamdAnswer' => $lang['522'], 'sql1' => $sql1, 'sql2' => $sql2);
    		}
    	}
    	$newequipoid = 0;
    	$sql3 = "	SELECT max(Equipo_ID) Equipo_ID 
    				FROM $schema.Equipos
    				where Torneo_ID = $Season;";
    	//echo $sql2;
    	$result3 = $Config->query($sql3);
    	if ($result3 && $result3->num_rows > 0) {
    		while($row3 = $result3->fetch_assoc()) {
    			$newequipoid = $row3["Equipo_ID"];
    		}
    	}

    	$found = 0;
    	
        //echo $logoFileName;
    	if (strlen($logoFileName) > 0){
    		$handle = new Upload($logoFileName);
    		
    		if ($handle->uploaded) {
    			$handle->image_resize            = false;
    			$handle->file_auto_rename 		 = false;
    			$handle->file_overwrite 		 = true;
    			$handle->file_new_name_body      = $Season . "-" . $newequipoid;
    			$handle->file_new_name_ext       = "png";
    			$handle->Process($Config->getPath() ."/imagenes/Original/");
    
    			$handle->image_resize            = true;
    			$handle->image_ratio_pixels      = 22500;
    			$handle->file_auto_rename 		 = false;
    			$handle->file_overwrite 		 = true;
    			$handle->file_new_name_body      = $Season . "-" . $newequipoid;
    			$handle->file_new_name_ext       = "png";
    			$handle->Process($Config->getPath() ."/imagenes/");
    	
    		} 	
    		if($i == ($length-1)){
	            $handle->clean(); 
    		}
    	
    	}
    }

	$Connection->Close();
    echo json_encode($retunData);
?>