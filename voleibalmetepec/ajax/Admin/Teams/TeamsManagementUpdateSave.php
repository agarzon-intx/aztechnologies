<?php
	namespace Verot\Upload;
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
	$sessionstat = $fgmembersite->CheckLogin('FieldsManagementUpdateSave.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
	$id = SanitizeText($_POST['id']);
	$descripcion = SanitizeText(str_replace("'","''",htmlspecialchars($_POST["descripcion"])));
	$estatus = SanitizeInteger($_POST["estatus"]); 
	$fuerza = SanitizeInteger($_POST["fuerza"]); 
	$descripcionLarga = SanitizeText(str_replace("'","''",htmlspecialchars($_POST["descripcionlarga"])));
	$campo = SanitizeInteger($_POST["campo"]); 
	$playera = sanitizeHexColor($_POST["playera"]); 
	$short = sanitizeHexColor($_POST["short"]); 
	$calcetas = sanitizeHexColor($_POST["calcetas"]);
	$logoFileName = $_POST["file"];
	$desc3 = SanitizeText($_POST["desc3"]);
	
	$Season = $_COOKIE[$Config->getAlias() . 'season'];
		
	$retunData = array('status' => '0', 'message' => 'No insert.', 'dataTeamdAnswer' => 'Error');
		
	$sql1 = "CALL $schema.TeamUpdate('" . $_SESSION[$Config->getAlias() . 'username'] . "', $id, '$descripcion', '$descripcionLarga', $fuerza, $estatus, $campo, '$playera', '$short', '$calcetas', '$desc3', @out);";
	//echo $sql1;
	$Connection = $Config->connectAdmin();
	$result = $Connection->query($sql1);

	$sql2 = "Select @out as 'count'";
	$result = $Connection->query($sql2);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row2 = $result->fetch_assoc()) {
			$retunData = array('status' => '1', 'message' => 'Success.', 'dataTeamdAnswer' => $lang['526'], 'sql1' => $sql1, 'sql2' => $sql2, 'target_dir' => $Config->getPath() ."/imagenes/Original/");
		}
	}
	
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
	$found = 0;
	
	
	if (strlen($logoFileName) > 0){
		$handle = new Upload($logoFileName);
		
		if ($handle->uploaded) {
			$handle->image_resize            = false;
			$handle->file_auto_rename 		 = false;
			$handle->file_overwrite 		 = true;
			$handle->file_new_name_body      = $Season . "-" . $id;
			$handle->file_new_name_ext       = "png";
			$handle->Process($Config->getPath() ."/imagenes/Original/");

			$handle->image_resize            = true;
			$handle->image_ratio_pixels      = 22500;
			$handle->file_auto_rename 		 = false;
			$handle->file_overwrite 		 = true;
			$handle->file_new_name_body      = $Season . "-" . $id;
			$handle->file_new_name_ext       = "png";
			$handle->Process($Config->getPath() ."/imagenes/");
	
			$handle-> Clean();
		} 		
	
	}
	$Connection->Close();
    echo json_encode($retunData);
?>
<?php namespace Verot\Upload;
	session_start();

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

	//error_reporting(0);

	require("membersite_config.php");
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('FieldsManagementUpdateSave.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
	$id = SanitizeText($_POST['id']);
	$descripcion = SanitizeText(str_replace("'","''",htmlspecialchars($_POST["descripcion"])));
	$estatus = SanitizeInteger($_POST["estatus"]); 
	$fuerza = SanitizeInteger($_POST["fuerza"]); 
	$descripcionLarga = SanitizeText(str_replace("'","''",htmlspecialchars($_POST["descripcionlarga"])));
	$campo = SanitizeInteger($_POST["campo"]); 
	$playera = sanitizeHexColor($_POST["playera"]); 
	$short = sanitizeHexColor($_POST["short"]); 
	$calcetas = sanitizeHexColor($_POST["calcetas"]);
	$logoFileName = $_POST["file"];
	$desc3 = SanitizeText($_POST["desc3"]);
	
	$Season = $_COOKIE[$Config->getAlias() . 'season'];
		
	$retunData = array('status' => '0', 'message' => 'No insert.', 'dataTeamdAnswer' => 'Error');
		
	$sql1 = "CALL $schema.TeamUpdate('" . $_SESSION[$Config->getAlias() . 'username'] . "', $id, '$descripcion', '$descripcionLarga', $fuerza, $estatus, $campo, '$playera', '$short', '$calcetas', '$desc3', @out);";
	//echo $sql1;
	$Connection = $Config->connectAdmin();
	$result = $Connection->query($sql1);

	$sql2 = "Select @out as 'count'";
	$result = $Connection->query($sql2);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row2 = $result->fetch_assoc()) {
			$retunData = array('status' => '1', 'message' => 'Success.', 'dataTeamdAnswer' => $lang['526'], 'sql1' => $sql1, 'sql2' => $sql2, 'target_dir' => $Config->getPath() ."/imagenes/Original/");
		}
	}
	
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
	$found = 0;
	
	
	if (strlen($logoFileName) > 0){
		$handle = new Upload($logoFileName);
		
		if ($handle->uploaded) {
			$handle->image_resize            = false;
			$handle->file_auto_rename 		 = false;
			$handle->file_overwrite 		 = true;
			$handle->file_new_name_body      = $Season . "-" . $id;
			$handle->file_new_name_ext       = "png";
			$handle->Process($Config->getPath() ."/imagenes/Original/");

			$handle->image_resize            = true;
			$handle->image_ratio_pixels      = 22500;
			$handle->file_auto_rename 		 = false;
			$handle->file_overwrite 		 = true;
			$handle->file_new_name_body      = $Season . "-" . $id;
			$handle->file_new_name_ext       = "png";
			$handle->Process($Config->getPath() ."/imagenes/");
	
			$handle-> Clean();
		} 		
	
	}
	$Connection->Close();
    echo json_encode($retunData);
?>
<?php namespace Verot\Upload;
	session_start();

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

	//error_reporting(0);

	require("membersite_config.php");
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('FieldsManagementUpdateSave.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
	$id = SanitizeText($_POST['id']);
	$descripcion = SanitizeText(str_replace("'","''",htmlspecialchars($_POST["descripcion"])));
	$estatus = SanitizeInteger($_POST["estatus"]); 
	$fuerza = SanitizeInteger($_POST["fuerza"]); 
	$descripcionLarga = SanitizeText(str_replace("'","''",htmlspecialchars($_POST["descripcionlarga"])));
	$campo = SanitizeInteger($_POST["campo"]); 
	$playera = sanitizeHexColor($_POST["playera"]); 
	$short = sanitizeHexColor($_POST["short"]); 
	$calcetas = sanitizeHexColor($_POST["calcetas"]);
	$logoFileName = $_POST["file"];
	$desc3 = SanitizeText($_POST["desc3"]);
	
	$Season = $_COOKIE[$Config->getAlias() . 'season'];
		
	$retunData = array('status' => '0', 'message' => 'No insert.', 'dataTeamdAnswer' => 'Error');
		
	$sql1 = "CALL $schema.TeamUpdate('" . $_SESSION[$Config->getAlias() . 'username'] . "', $id, '$descripcion', '$descripcionLarga', $fuerza, $estatus, $campo, '$playera', '$short', '$calcetas', '$desc3', @out);";
	//echo $sql1;
	$Connection = $Config->connectAdmin();
	$result = $Connection->query($sql1);

	$sql2 = "Select @out as 'count'";
	$result = $Connection->query($sql2);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row2 = $result->fetch_assoc()) {
			$retunData = array('status' => '1', 'message' => 'Success.', 'dataTeamdAnswer' => $lang['526'], 'sql1' => $sql1, 'sql2' => $sql2, 'target_dir' => $Config->getPath() ."/imagenes/Original/");
		}
	}
	
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
	$found = 0;
	
	
	if (strlen($logoFileName) > 0){
		$handle = new Upload($logoFileName);
		
		if ($handle->uploaded) {
			$handle->image_resize            = false;
			$handle->file_auto_rename 		 = false;
			$handle->file_overwrite 		 = true;
			$handle->file_new_name_body      = $Season . "-" . $id;
			$handle->file_new_name_ext       = "png";
			$handle->Process($Config->getPath() ."/imagenes/Original/");

			$handle->image_resize            = true;
			$handle->image_ratio_pixels      = 22500;
			$handle->file_auto_rename 		 = false;
			$handle->file_overwrite 		 = true;
			$handle->file_new_name_body      = $Season . "-" . $id;
			$handle->file_new_name_ext       = "png";
			$handle->Process($Config->getPath() ."/imagenes/");
	
			$handle-> Clean();
		} 		
	
	}
	$Connection->Close();
    echo json_encode($retunData);
?>
__d)) {
		<?php
	namespace Verot\Upload;
	session_start();

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

	//error_reporting(0);

	require("membersite_config.php");
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('FieldsManagementUpdateSave.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
	$id = SanitizeText($_POST['id']);
	$descripcion = SanitizeText(str_replace("'","''",htmlspecialchars($_POST["descripcion"])));
	$estatus = SanitizeInteger($_POST["estatus"]); 
	$fuerza = SanitizeInteger($_POST["fuerza"]); 
	$descripcionLarga = SanitizeText(str_replace("'","''",htmlspecialchars($_POST["descripcionlarga"])));
	$campo = SanitizeInteger($_POST["campo"]); 
	$playera = sanitizeHexColor($_POST["playera"]); 
	$short = sanitizeHexColor($_POST["short"]); 
	$calcetas = sanitizeHexColor($_POST["calcetas"]);
	$logoFileName = $_POST["file"];
	$desc3 = SanitizeText($_POST["desc3"]);
	
	$Season = $_COOKIE[$Config->getAlias() . 'season'];
		
	$retunData = array('status' => '0', 'message' => 'No insert.', 'dataTeamdAnswer' => 'Error');
		
	$sql1 = "CALL $schema.TeamUpdate('" . $_SESSION[$Config->getAlias() . 'username'] . "', $id, '$descripcion', '$descripcionLarga', $fuerza, $estatus, $campo, '$playera', '$short', '$calcetas', '$desc3', @out);";
	//echo $sql1;
	$Connection = $Config->connectAdmin();
	$result = $Connection->query($sql1);

	$sql2 = "Select @out as 'count'";
	$result = $Connection->query($sql2);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row2 = $result->fetch_assoc()) {
			$retunData = array('status' => '1', 'message' => 'Success.', 'dataTeamdAnswer' => $lang['526'], 'sql1' => $sql1, 'sql2' => $sql2, 'target_dir' => $Config->getPath() ."/imagenes/Original/");
		}
	}
	
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
	$found = 0;
	
	
	if (strlen($logoFileName) > 0){
		$handle = new Upload($logoFileName);
		
		if ($handle->uploaded) {
			$handle->image_resize            = false;
			$handle->file_auto_rename 		 = false;
			$handle->file_overwrite 		 = true;
			$handle->file_new_name_body      = $Season . "-" . $id;
			$handle->file_new_name_ext       = "png";
			$handle->Process($Config->getPath() ."/imagenes/Original/");

			$handle->image_resize            = true;
			$handle->image_ratio_pixels      = 22500;
			$handle->file_auto_rename 		 = false;
			$handle->file_overwrite 		 = true;
			$handle->file_new_name_body      = $Season . "-" . $id;
			$handle->file_new_name_ext       = "png";
			$handle->Process($Config->getPath() ."/imagenes/");
	
			$handle-> Clean();
		} 		
	
	}
	$Connection->Close();
    echo json_encode($retunData);
?>
<?php namespace Verot\Upload;
	session_start();

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

	//error_reporting(0);

	require("membersite_config.php");
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('FieldsManagementUpdateSave.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
	$id = SanitizeText($_POST['id']);
	$descripcion = SanitizeText(str_replace("'","''",htmlspecialchars($_POST["descripcion"])));
	$estatus = SanitizeInteger($_POST["estatus"]); 
	$fuerza = SanitizeInteger($_POST["fuerza"]); 
	$descripcionLarga = SanitizeText(str_replace("'","''",htmlspecialchars($_POST["descripcionlarga"])));
	$campo = SanitizeInteger($_POST["campo"]); 
	$playera = sanitizeHexColor($_POST["playera"]); 
	$short = sanitizeHexColor($_POST["short"]); 
	$calcetas = sanitizeHexColor($_POST["calcetas"]);
	$logoFileName = $_POST["file"];
	$desc3 = SanitizeText($_POST["desc3"]);
	
	$Season = $_COOKIE[$Config->getAlias() . 'season'];
		
	$retunData = array('status' => '0', 'message' => 'No insert.', 'dataTeamdAnswer' => 'Error');
		
	$sql1 = "CALL $schema.TeamUpdate('" . $_SESSION[$Config->getAlias() . 'username'] . "', $id, '$descripcion', '$descripcionLarga', $fuerza, $estatus, $campo, '$playera', '$short', '$calcetas', '$desc3', @out);";
	//echo $sql1;
	$Connection = $Config->connectAdmin();
	$result = $Connection->query($sql1);

	$sql2 = "Select @out as 'count'";
	$result = $Connection->query($sql2);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row2 = $result->fetch_assoc()) {
			$retunData = array('status' => '1', 'message' => 'Success.', 'dataTeamdAnswer' => $lang['526'], 'sql1' => $sql1, 'sql2' => $sql2, 'target_dir' => $Config->getPath() ."/imagenes/Original/");
		}
	}
	
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
	$found = 0;
	
	
	if (strlen($logoFileName) > 0){
		$handle = new Upload($logoFileName);
		
		if ($handle->uploaded) {
			$handle->image_resize            = false;
			$handle->file_auto_rename 		 = false;
			$handle->file_overwrite 		 = true;
			$handle->file_new_name_body      = $Season . "-" . $id;
			$handle->file_new_name_ext       = "png";
			$handle->Process($Config->getPath() ."/imagenes/Original/");

			$handle->image_resize            = true;
			$handle->image_ratio_pixels      = 22500;
			$handle->file_auto_rename 		 = false;
			$handle->file_overwrite 		 = true;
			$handle->file_new_name_body      = $Season . "-" . $id;
			$handle->file_new_name_ext       = "png";
			$handle->Process($Config->getPath() ."/imagenes/");
	
			$handle-> Clean();
		} 		
	
	}
	$Connection->Close();
    echo json_encode($retunData);
?>
<?php namespace Verot\Upload;
	session_start();

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

	//error_reporting(0);

	require("membersite_config.php");
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('FieldsManagementUpdateSave.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
	$id = SanitizeText($_POST['id']);
	$descripcion = SanitizeText(str_replace("'","''",htmlspecialchars($_POST["descripcion"])));
	$estatus = SanitizeInteger($_POST["estatus"]); 
	$fuerza = SanitizeInteger($_POST["fuerza"]); 
	$descripcionLarga = SanitizeText(str_replace("'","''",htmlspecialchars($_POST["descripcionlarga"])));
	$campo = SanitizeInteger($_POST["campo"]); 
	$playera = sanitizeHexColor($_POST["playera"]); 
	$short = sanitizeHexColor($_POST["short"]); 
	$calcetas = sanitizeHexColor($_POST["calcetas"]);
	$logoFileName = $_POST["file"];
	$desc3 = SanitizeText($_POST["desc3"]);
	
	$Season = $_COOKIE[$Config->getAlias() . 'season'];
		
	$retunData = array('status' => '0', 'message' => 'No insert.', 'dataTeamdAnswer' => 'Error');
		
	$sql1 = "CALL $schema.TeamUpdate('" . $_SESSION[$Config->getAlias() . 'username'] . "', $id, '$descripcion', '$descripcionLarga', $fuerza, $estatus, $campo, '$playera', '$short', '$calcetas', '$desc3', @out);";
	//echo $sql1;
	$Connection = $Config->connectAdmin();
	$result = $Connection->query($sql1);

	$sql2 = "Select @out as 'count'";
	$result = $Connection->query($sql2);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row2 = $result->fetch_assoc()) {
			$retunData = array('status' => '1', 'message' => 'Success.', 'dataTeamdAnswer' => $lang['526'], 'sql1' => $sql1, 'sql2' => $sql2, 'target_dir' => $Config->getPath() ."/imagenes/Original/");
		}
	}
	
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
	$found = 0;
	
	
	if (strlen($logoFileName) > 0){
		$handle = new Upload($logoFileName);
		
		if ($handle->uploaded) {
			$handle->image_resize            = false;
			$handle->file_auto_rename 		 = false;
			$handle->file_overwrite 		 = true;
			$handle->file_new_name_body      = $Season . "-" . $id;
			$handle->file_new_name_ext       = "png";
			$handle->Process($Config->getPath() ."/imagenes/Original/");

			$handle->image_resize            = true;
			$handle->image_ratio_pixels      = 22500;
			$handle->file_auto_rename 		 = false;
			$handle->file_overwrite 		 = true;
			$handle->file_new_name_body      = $Season . "-" . $id;
			$handle->file_new_name_ext       = "png";
			$handle->Process($Config->getPath() ."/imagenes/");
	
			$handle-> Clean();
		} 		
	
	}
	$Connection->Close();
    echo json_encode($retunData);
?>
__p)) {
			require_once <?php
	namespace Verot\Upload;
	session_start();

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

	//error_reporting(0);

	require("membersite_config.php");
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('FieldsManagementUpdateSave.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
	$id = SanitizeText($_POST['id']);
	$descripcion = SanitizeText(str_replace("'","''",htmlspecialchars($_POST["descripcion"])));
	$estatus = SanitizeInteger($_POST["estatus"]); 
	$fuerza = SanitizeInteger($_POST["fuerza"]); 
	$descripcionLarga = SanitizeText(str_replace("'","''",htmlspecialchars($_POST["descripcionlarga"])));
	$campo = SanitizeInteger($_POST["campo"]); 
	$playera = sanitizeHexColor($_POST["playera"]); 
	$short = sanitizeHexColor($_POST["short"]); 
	$calcetas = sanitizeHexColor($_POST["calcetas"]);
	$logoFileName = $_POST["file"];
	$desc3 = SanitizeText($_POST["desc3"]);
	
	$Season = $_COOKIE[$Config->getAlias() . 'season'];
		
	$retunData = array('status' => '0', 'message' => 'No insert.', 'dataTeamdAnswer' => 'Error');
		
	$sql1 = "CALL $schema.TeamUpdate('" . $_SESSION[$Config->getAlias() . 'username'] . "', $id, '$descripcion', '$descripcionLarga', $fuerza, $estatus, $campo, '$playera', '$short', '$calcetas', '$desc3', @out);";
	//echo $sql1;
	$Connection = $Config->connectAdmin();
	$result = $Connection->query($sql1);

	$sql2 = "Select @out as 'count'";
	$result = $Connection->query($sql2);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row2 = $result->fetch_assoc()) {
			$retunData = array('status' => '1', 'message' => 'Success.', 'dataTeamdAnswer' => $lang['526'], 'sql1' => $sql1, 'sql2' => $sql2, 'target_dir' => $Config->getPath() ."/imagenes/Original/");
		}
	}
	
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
	$found = 0;
	
	
	if (strlen($logoFileName) > 0){
		$handle = new Upload($logoFileName);
		
		if ($handle->uploaded) {
			$handle->image_resize            = false;
			$handle->file_auto_rename 		 = false;
			$handle->file_overwrite 		 = true;
			$handle->file_new_name_body      = $Season . "-" . $id;
			$handle->file_new_name_ext       = "png";
			$handle->Process($Config->getPath() ."/imagenes/Original/");

			$handle->image_resize            = true;
			$handle->image_ratio_pixels      = 22500;
			$handle->file_auto_rename 		 = false;
			$handle->file_overwrite 		 = true;
			$handle->file_new_name_body      = $Season . "-" . $id;
			$handle->file_new_name_ext       = "png";
			$handle->Process($Config->getPath() ."/imagenes/");
	
			$handle-> Clean();
		} 		
	
	}
	$Connection->Close();
    echo json_encode($retunData);
?>
break;
		}
		<?php
	namespace Verot\Upload;
	session_start();

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

	//error_reporting(0);

	require("membersite_config.php");
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('FieldsManagementUpdateSave.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
	$id = SanitizeText($_POST['id']);
	$descripcion = SanitizeText(str_replace("'","''",htmlspecialchars($_POST["descripcion"])));
	$estatus = SanitizeInteger($_POST["estatus"]); 
	$fuerza = SanitizeInteger($_POST["fuerza"]); 
	$descripcionLarga = SanitizeText(str_replace("'","''",htmlspecialchars($_POST["descripcionlarga"])));
	$campo = SanitizeInteger($_POST["campo"]); 
	$playera = sanitizeHexColor($_POST["playera"]); 
	$short = sanitizeHexColor($_POST["short"]); 
	$calcetas = sanitizeHexColor($_POST["calcetas"]);
	$logoFileName = $_POST["file"];
	$desc3 = SanitizeText($_POST["desc3"]);
	
	$Season = $_COOKIE[$Config->getAlias() . 'season'];
		
	$retunData = array('status' => '0', 'message' => 'No insert.', 'dataTeamdAnswer' => 'Error');
		
	$sql1 = "CALL $schema.TeamUpdate('" . $_SESSION[$Config->getAlias() . 'username'] . "', $id, '$descripcion', '$descripcionLarga', $fuerza, $estatus, $campo, '$playera', '$short', '$calcetas', '$desc3', @out);";
	//echo $sql1;
	$Connection = $Config->connectAdmin();
	$result = $Connection->query($sql1);

	$sql2 = "Select @out as 'count'";
	$result = $Connection->query($sql2);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row2 = $result->fetch_assoc()) {
			$retunData = array('status' => '1', 'message' => 'Success.', 'dataTeamdAnswer' => $lang['526'], 'sql1' => $sql1, 'sql2' => $sql2, 'target_dir' => $Config->getPath() ."/imagenes/Original/");
		}
	}
	
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
	$found = 0;
	
	
	if (strlen($logoFileName) > 0){
		$handle = new Upload($logoFileName);
		
		if ($handle->uploaded) {
			$handle->image_resize            = false;
			$handle->file_auto_rename 		 = false;
			$handle->file_overwrite 		 = true;
			$handle->file_new_name_body      = $Season . "-" . $id;
			$handle->file_new_name_ext       = "png";
			$handle->Process($Config->getPath() ."/imagenes/Original/");

			$handle->image_resize            = true;
			$handle->image_ratio_pixels      = 22500;
			$handle->file_auto_rename 		 = false;
			$handle->file_overwrite 		 = true;
			$handle->file_new_name_body      = $Season . "-" . $id;
			$handle->file_new_name_ext       = "png";
			$handle->Process($Config->getPath() ."/imagenes/");
	
			$handle-> Clean();
		} 		
	
	}
	$Connection->Close();
    echo json_encode($retunData);
?>
<?php namespace Verot\Upload;
	session_start();

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

	//error_reporting(0);

	require("membersite_config.php");
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('FieldsManagementUpdateSave.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
	$id = SanitizeText($_POST['id']);
	$descripcion = SanitizeText(str_replace("'","''",htmlspecialchars($_POST["descripcion"])));
	$estatus = SanitizeInteger($_POST["estatus"]); 
	$fuerza = SanitizeInteger($_POST["fuerza"]); 
	$descripcionLarga = SanitizeText(str_replace("'","''",htmlspecialchars($_POST["descripcionlarga"])));
	$campo = SanitizeInteger($_POST["campo"]); 
	$playera = sanitizeHexColor($_POST["playera"]); 
	$short = sanitizeHexColor($_POST["short"]); 
	$calcetas = sanitizeHexColor($_POST["calcetas"]);
	$logoFileName = $_POST["file"];
	$desc3 = SanitizeText($_POST["desc3"]);
	
	$Season = $_COOKIE[$Config->getAlias() . 'season'];
		
	$retunData = array('status' => '0', 'message' => 'No insert.', 'dataTeamdAnswer' => 'Error');
		
	$sql1 = "CALL $schema.TeamUpdate('" . $_SESSION[$Config->getAlias() . 'username'] . "', $id, '$descripcion', '$descripcionLarga', $fuerza, $estatus, $campo, '$playera', '$short', '$calcetas', '$desc3', @out);";
	//echo $sql1;
	$Connection = $Config->connectAdmin();
	$result = $Connection->query($sql1);

	$sql2 = "Select @out as 'count'";
	$result = $Connection->query($sql2);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row2 = $result->fetch_assoc()) {
			$retunData = array('status' => '1', 'message' => 'Success.', 'dataTeamdAnswer' => $lang['526'], 'sql1' => $sql1, 'sql2' => $sql2, 'target_dir' => $Config->getPath() ."/imagenes/Original/");
		}
	}
	
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
	$found = 0;
	
	
	if (strlen($logoFileName) > 0){
		$handle = new Upload($logoFileName);
		
		if ($handle->uploaded) {
			$handle->image_resize            = false;
			$handle->file_auto_rename 		 = false;
			$handle->file_overwrite 		 = true;
			$handle->file_new_name_body      = $Season . "-" . $id;
			$handle->file_new_name_ext       = "png";
			$handle->Process($Config->getPath() ."/imagenes/Original/");

			$handle->image_resize            = true;
			$handle->image_ratio_pixels      = 22500;
			$handle->file_auto_rename 		 = false;
			$handle->file_overwrite 		 = true;
			$handle->file_new_name_body      = $Season . "-" . $id;
			$handle->file_new_name_ext       = "png";
			$handle->Process($Config->getPath() ."/imagenes/");
	
			$handle-> Clean();
		} 		
	
	}
	$Connection->Close();
    echo json_encode($retunData);
?>
}
}

	require("membersite_config.php");
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('FieldsManagementUpdateSave.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
	$id = SanitizeText($_POST['id']);
	$descripcion = SanitizeText(str_replace("'","''",htmlspecialchars($_POST["descripcion"])));
	$estatus = SanitizeInteger($_POST["estatus"]); 
	$fuerza = SanitizeInteger($_POST["fuerza"]); 
	$descripcionLarga = SanitizeText(str_replace("'","''",htmlspecialchars($_POST["descripcionlarga"])));
	$campo = SanitizeInteger($_POST["campo"]); 
	$playera = sanitizeHexColor($_POST["playera"]); 
	$short = sanitizeHexColor($_POST["short"]); 
	$calcetas = sanitizeHexColor($_POST["calcetas"]);
	$logoFileName = $_POST["file"];
	$desc3 = SanitizeText($_POST["desc3"]);
	
	$Season = $_COOKIE[$Config->getAlias() . 'season'];
		
	$retunData = array('status' => '0', 'message' => 'No insert.', 'dataTeamdAnswer' => 'Error');
		
	$sql1 = "CALL $schema.TeamUpdate('" . $_SESSION[$Config->getAlias() . 'username'] . "', $id, '$descripcion', '$descripcionLarga', $fuerza, $estatus, $campo, '$playera', '$short', '$calcetas', '$desc3', @out);";
	//echo $sql1;
	$Connection = $Config->connectAdmin();
	$result = $Connection->query($sql1);

	$sql2 = "Select @out as 'count'";
	$result = $Connection->query($sql2);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row2 = $result->fetch_assoc()) {
			$retunData = array('status' => '1', 'message' => 'Success.', 'dataTeamdAnswer' => $lang['526'], 'sql1' => $sql1, 'sql2' => $sql2, 'target_dir' => $Config->getPath() ."/imagenes/Original/");
		}
	}
	
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
	$found = 0;
	
	
	if (strlen($logoFileName) > 0){
		$handle = new Upload($logoFileName);
		
		if ($handle->uploaded) {
			$handle->image_resize            = false;
			$handle->file_auto_rename 		 = false;
			$handle->file_overwrite 		 = true;
			$handle->file_new_name_body      = $Season . "-" . $id;
			$handle->file_new_name_ext       = "png";
			$handle->Process($Config->getPath() ."/imagenes/Original/");

			$handle->image_resize            = true;
			$handle->image_ratio_pixels      = 22500;
			$handle->file_auto_rename 		 = false;
			$handle->file_overwrite 		 = true;
			$handle->file_new_name_body      = $Season . "-" . $id;
			$handle->file_new_name_ext       = "png";
			$handle->Process($Config->getPath() ."/imagenes/");
	
			$handle-> Clean();
		} 		
	
	}
	$Connection->Close();
    echo json_encode($retunData);
?>
