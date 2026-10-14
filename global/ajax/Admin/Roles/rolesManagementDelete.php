<?php
    session_start();
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

	error_reporting(0);

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
	require_once dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'role_menu.inc.php';
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('rolesManagementDelete.php');

	include('lang.' . $_COOKIE[$Config->getAlias() . 'language'] . '.php');

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
	$id = (int) ($_POST['id'] ?? 0);
	$db = $Config->connectAdmin();
	if ($id > 0 && $db) {
		$ok = $db->query("DELETE FROM $schema.usuarios_role WHERE Role_ID = $id")
			&& $db->query("DELETE FROM $schema.roleMenu WHERE Role_ID = $id")
			&& $db->query("DELETE FROM $schema.role WHERE Role_ID = $id");
		$retunData = $ok ? array('status' => '1', 'message' => $lang['rol012']) : array('status' => '0', 'message' => $db->error);
	}
	$Config->Close();
	echo json_encode($retunData);
?>
