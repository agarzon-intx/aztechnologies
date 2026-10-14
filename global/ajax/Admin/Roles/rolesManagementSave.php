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
	$sessionstat = $fgmembersite->CheckLogin('rolesManagementSave.php');

	include('lang.' . $_COOKIE[$Config->getAlias() . 'language'] . '.php');

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
	$id = (int) ($_POST['id'] ?? 0);
	$name = trim((string) ($_POST['name'] ?? ''));
	$desc = trim((string) ($_POST['desc'] ?? ''));
	$valid = az_role_menu_keys($lang);
	$menus = array_values(array_unique(array_intersect(array_map('trim', explode(',', (string) ($_POST['menus'] ?? ''))), $valid)));
	$user = (string) ($_SESSION[$Config->getAlias() . 'username'] ?? '');

	if ($name === '' || mb_strlen($name, 'UTF-8') > 10) {
		$retunData['message'] = $lang['rol009'];
		echo json_encode($retunData);
		exit;
	}
	if (!$menus) {
		$retunData['message'] = $lang['rol016'];
		echo json_encode($retunData);
		exit;
	}
	$db = $Config->connectAdmin();
	if (!$db) {
		echo json_encode($retunData);
		exit;
	}
	$db->set_charset('utf8');
	$q = function ($v) use ($db) {
		return "'" . $db->real_escape_string((string) $v) . "'";
	};
	$dup = $db->query("SELECT 1 FROM $schema.role WHERE Role_Name = " . $q($name) . " AND Role_ID <> $id LIMIT 1");
	if ($dup && $dup->num_rows > 0) {
		$retunData['message'] = $lang['rol010'];
		echo json_encode($retunData);
		exit;
	}
	$descSql = $desc === '' ? 'NULL' : $q(mb_substr($desc, 0, 200, 'UTF-8'));
	if ($id > 0) {
		$ok = $db->query("UPDATE $schema.role SET Role_Name = " . $q($name) . ", Role_Desc = $descSql, Update_Date = NOW(), Update_User = " . $q($user) . " WHERE Role_ID = $id");
	} else {
		$ok = $db->query("INSERT INTO $schema.role (Role_Name, Role_Desc, Create_User) VALUES (" . $q($name) . ", $descSql, " . $q($user) . ")");
		$id = $ok ? (int) $db->insert_id : 0;
	}
	if ($ok && $id > 0) {
		$ok = $db->query("DELETE FROM $schema.roleMenu WHERE Role_ID = $id");
		foreach ($menus as $menu) {
			$ok = $ok && $db->query("INSERT INTO $schema.roleMenu (Role_ID, Menu_Element) VALUES ($id, " . $q($menu) . ")");
		}
	}
	if ($ok) {
		$retunData = array('status' => '1', 'message' => $lang['rol008']);
	} else {
		$retunData['message'] = $db->error;
	}
	$Config->Close();
	echo json_encode($retunData);
?>
