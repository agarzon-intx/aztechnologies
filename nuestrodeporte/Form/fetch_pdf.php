<?php
header("content-type:application/pdf");

header("content-type:application/pdf");
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
$sessionstat = $fgmembersite->CheckLogin($Config,'fetchPdf.php');
$curp=$_GET['curp'];

$select_curp = "select Documento from $schema.Curp where CURP = '$curp'";
$pdf_decoded = "";
$result=$Config->query($select_curp);
if ($result->num_rows > 0) {
	while($row2 = $result->fetch_assoc()) {
		$pdf_decoded = base64_decode($row2["Documento"]);
	}	
}
$Config->close();
echo $pdf_decoded;
?>