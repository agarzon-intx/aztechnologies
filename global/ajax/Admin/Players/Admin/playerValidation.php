<?php
	session_start();

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

	//error_reporting(0);

	session_start();

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

	//error_reporting(0);
if (!defined('APP_SITE_ROOT')) {
	$___d = __DIR__;
	while ($___d !== dirname($___d)) {
		$___p = $___d . DIRECTORY_SEPARATOR . 'site_paths.php';
		if (is_readable($___p)) {
			require_once $___p;
			break;
		}
		$___d = dirname($___d);
	}
}
	require("membersite_config.php");
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('playersManagementCreate.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
	$Season = $_COOKIE[$Config->getAlias() . 'season'];
    $Category = SanitizeInteger($_POST['Category']);
	$Team = SanitizeInteger($_POST['team']);

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');

    $curp = $_POST['curp'];
    $existe = 0;
    $categoria = 0;
    $equipo = 0;
    $mensaje = '';
	
	$sql0 = "SELECT b.Equipo_FULLDESC, b.Equipo_ID, c.Categoria_Desc, c.Categoria_ID, a.Jugador_ID
		FROM $schema.Jugadores a 
			JOIN $schema.Equipos b ON a.Equipo_ID = b.Equipo_ID and b.Torneo_ID = $Season
			JOIN $schema.Categorias c ON b.Fuerza = c.Categoria_ID
		WHERE Curp LIKE '$curp' AND a.Equipo_ID = $Team";
						
	$result = $Config->query($sql0);
	if ($result->num_rows > 0) {
	    
	    while($row = $result->fetch_assoc()) {
            $existe = -1;
	        $mensaje = 'El Jugador ya esta registrado en este equipo '. $row["Equipo_FULLDESC"] .', de la categoria '. $row["Categoria_Desc"];
	        $categoria = $row["Categoria_ID"];
            $equipo = $row["Equipo_ID"] ;
	    }
    }else{
        $sql = "SELECT b.Equipo_FULLDESC, b.Equipo_ID, c.Categoria_Desc, c.Categoria_ID, a.Jugador_ID
    		FROM $schema.Jugadores a 
    			JOIN $schema.Equipos b ON a.Equipo_ID = b.Equipo_ID
    			JOIN $schema.Categorias c ON b.Fuerza = c.Categoria_ID
    		WHERE Curp LIKE '$curp' AND b.Torneo_ID IN (SELECT MAX(a.Torneo_ID) 
    						FROM $schema.Equipos a 
    						JOIN $schema.Jugadores b ON a.Equipo_ID = b.Equipo_ID
    						WHERE Curp LIKE '$curp')";
    						
    	$result = $Config->query($sql);
    	if ($result->num_rows > 0) {
    	    
    	    while($row = $result->fetch_assoc()) {
                $existe = 1;
    	        $mensaje = 'El Jugador ya esta registrado en el equipo '. $row["Equipo_FULLDESC"] .', de la categoria '. $row["Categoria_Desc"];
    	        $categoria = $row["Categoria_ID"];
                $equipo = $row["Equipo_ID"] ;
    	    }
        } else{
            $existe = 0;
    	    $mensaje = 'Jugador sin equipo, puede continuar con el llenado de registro';
    	}
    }
    
	
	$retunData = array('status' => '1', 'message' => 'Success.', 'JugadorEXiste' => $existe, 'mensaje1' => $mensaje, 'equipo' => $equipo, 'categoria' => $categoria, 'sql0' => $sql0, 'sql' => $sql);
    $Config->Close();
    echo json_encode($retunData);
