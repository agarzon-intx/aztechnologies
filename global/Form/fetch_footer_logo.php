<?php
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
	require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'footer_links.php';

	$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
	$conn = ($id > 0 && az_footer_tables_exist($Config)) ? az_footer_conn($Config) : null;
	$row = null;
	if ($conn) {
		$stmt = $conn->prepare("SELECT Imagen, Mime FROM `" . $conn->real_escape_string($Config->getSchema()) . "`.FooterLogos WHERE Logo_ID = ?");
		if ($stmt) {
			$stmt->bind_param('i', $id);
			$stmt->execute();
			$res = $stmt->get_result();
			$row = $res ? $res->fetch_assoc() : null;
			$stmt->close();
		}
	}

	while (ob_get_level() > 0) {
		ob_end_clean();
	}
	if (!$row || strlen((string) $row['Imagen']) === 0) {
		http_response_code(404);
		header('Content-Type: image/png');
		header('Cache-Control: no-store');
		echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=');
		exit;
	}
	$mime = in_array($row['Mime'], array('image/png', 'image/jpeg', 'image/gif', 'image/webp'), true) ? $row['Mime'] : 'image/png';
	header('Content-Type: ' . $mime);
	header('X-Content-Type-Options: nosniff');
	header('Cache-Control: public, max-age=604800');
	header('Content-Length: ' . strlen($row['Imagen']));
	echo $row['Imagen'];
