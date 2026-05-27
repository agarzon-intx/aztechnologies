<?php
namespace Verot\Upload;
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
	$sessionstat = $fgmembersite->CheckLogin('playersManagementCreate.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
	

	$team1 = $_POST['team'];
	$curp = $_POST['curp'];
	
	$sql=" SELECT Clave,
        Nombre,
        Apellido_P,
        Apellido_M,
        Fecha_Nacimiento,
        Estatus,
        $team1 team,
        Validado,
        Comentarios,
        NOW(),
        NOW(),
        Curp,
        Numero,
        Telefono,
        correo,
        Apodo,
        Foto,
        Identificacion,
        Firma,
        Sexo
	FROM $schema.Jugadores WHERE Curp ='$curp' Order by FechaAlta asc LIMIT 1";
//	echo $sql;
		$result = $Config->query($sql);
	  if ($result->num_rows > 0) {
	    	while($row2 = $result->fetch_assoc()) {
    			$clave = $row2["Clave"];
    			$name= $row2["Nombre"];
    			$lastname= $row2["Apellido_P"];
    			$lastname2= $row2["Apellido_M"];
    			$nickname= $row2["Apodo"];
    			$birthdate = $row2["Fecha_Nacimiento"];
    			$status= $row2["Estatus"];
    			$id= $row2["Curp"];
    			$playernumber= $row2["Numero"];
    			$valid= $row2["Validado"];
    			$comments= $row2["Comentarios"];
    			$phone= $row2["Telefono"];
    			$email= $row2["correo"];
    			$foto= $row2["Foto"];
    			$idfull= $row2["Identificacion"];
    			$firma= $row2["Firma"];
    			$sex= $row2["Sexo"];
		    }		
	  }
 //echo 'Name '.$name;
 
	$retunData = array('status' => '0', 'message' => 'No insert.', 'dataPlayerMessage' => 'Error');
	
//	echo "CALL $schema.PlayerCreate('" . $_SESSION[$Config->getAlias() . 'username'] . "', '$clave', '$name', '$lastname', '$lastname2', '$nickname', '$birthdate', '$status', '$id', '$playernumber', $team1, '$valid', '$comments', '$phone', '$email','$foto','$idfull','$firma',$sex, NOW(), NOW(), @out);";
		
	$sql0 = "CALL $schema.PlayerCopy('" . $_SESSION[$Config->getAlias() . 'username'] . "', '$id', $team1, @out);";
	//	$sql0 = "CALL $schema.PlayerCreate('" . $_SESSION[$Config->getAlias() . 'username'] . "', '71IM060502-4', 'Iovanna Marisol', 'Moreno', 'Rivera', 'iovis', '2006-05-02', 'A', 'MORI060502MDFRVVA9', '5', 1, '1', 'ninguno', '7221082657', 'iovsmoreno@gmail.com','null','null','null',1, NOW(), NOW(), @out);";

	
	$Connection = $Config->connectAdmin();
	$result = $Connection->query($sql0);
	
	$sql = "Select @out as 'count'";
	$result = $Connection->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
    		$retunData = array('status' => '1', 'message' => 'Success.', 'dataPlayerMessage' => $lang['916'], 'sql0' => $sql0);

	}
	$Connection->Close();
	//        print_r($retunData);
	echo json_encode($retunData);
?>