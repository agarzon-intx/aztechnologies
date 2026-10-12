<?php
	header("content-type:image/png");

	header("content-type:image/png");
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
	$sessionstat = $fgmembersite->CheckLogin($Config,'fetchImage.php');
	
	$Config->connect();
	$image_content = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=');
	
	$jugador = isset($_GET['Jugador_ID']) ? (int) $_GET['Jugador_ID'] : 0;
	$imagen = isset($_GET['Imagen']) ? (string) $_GET['Imagen'] : '';
	
	if ($jugador > 0 && in_array($imagen, array('Foto', 'Identificacion', 'Firma'), true)) {
		$select_image = "select $imagen from $schema.Jugadores where Jugador_ID = $jugador and OCTET_LENGTH($imagen) > 100";
		$result=$Config->query($select_image);
		if ($result && $result->num_rows > 0) {
			// output data of each row
			while($row = $result->fetch_assoc()) {
				$image_content = $row[$imagen];
			}
		}
	}
	$Config->close();
	echo $image_content;
?>
