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
	$sessionstat = $fgmembersite->CheckLogin('rolesManagementForm.php');

	include('lang.' . $_COOKIE[$Config->getAlias() . 'language'] . '.php');

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
	$h = function ($v) {
		return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
	};
	$id = (int) ($_POST['id'] ?? 0);
	$name = '';
	$desc = '';
	$menus = array();
	if ($id > 0) {
		$r = $Config->query("SELECT Role_Name, Role_Desc FROM $schema.role WHERE Role_ID = $id");
		if (!$r || !($row = $r->fetch_assoc())) {
			echo json_encode($retunData);
			exit;
		}
		$name = $row['Role_Name'];
		$desc = $row['Role_Desc'];
		$r = $Config->query("SELECT Menu_Element FROM $schema.roleMenu WHERE Role_ID = $id");
		while ($r && ($row = $r->fetch_assoc())) {
			$menus[] = (string) $row['Menu_Element'];
		}
	}

	$groupsHtml = '';
	foreach (az_role_menu_catalog($lang) as $group) {
		$items = $group['items'];
		if (!empty($group['sub'])) {
			$items = array_merge($items, $group['sub']['items']);
		}
		$boxes = '';
		foreach ($items as $key => $item) {
			$boxes .= '<div class="form-check">
								<input class="form-check-input rm-menu" type="checkbox" id="rmMenu_' . $h($key) . '" value="' . $h($key) . '"' . (in_array($key, $menus, true) ? ' checked' : '') . '>
								<label class="form-check-label" for="rmMenu_' . $h($key) . '">' . $h($item[1]) . '</label>
							</div>';
		}
		$groupsHtml .= '<div class="col-12 col-sm-6 col-lg-3 mb-3">
							<div class="d-flex align-items-center mb-1">
								<input class="form-check-input me-2 rm-group" type="checkbox" title="' . $h($group['title']) . '">
								<h6 class="mb-0"><i class="material-symbols-rounded" style="font-size: 16px; vertical-align: middle;">' . $h($group['icon']) . '</i> ' . $h($group['title']) . '</h6>
							</div>
							<div class="rm-group-items ps-2">' . $boxes . '</div>
						</div>';
	}

	$html = '<div class="container-fluid py-2">
				<div class="row"><div class="col-12"><h3>' . $h($id > 0 ? $lang['rol007'] : $lang['rol006']) . '</h3></div></div>
				<div class="row"><div class="col-12"><span id="roleFormError" class="error" style="color: red;"></span></div></div>
				<div class="row">
					<div class="col-12 col-md-3">
						<div class="input-group input-group-static mb-4">
							<label>' . $h($lang['rol003']) . ' *</label>
							<input type="text" class="form-control" id="roleName" maxlength="10" value="' . $h($name) . '">
						</div>
					</div>
					<div class="col-12 col-md-9">
						<div class="input-group input-group-static mb-4">
							<label>' . $h($lang['rol004']) . '</label>
							<input type="text" class="form-control" id="roleDesc" maxlength="200" value="' . $h($desc) . '">
						</div>
					</div>
				</div>
				<h5>' . $h($lang['rol005']) . '</h5>
				<div class="row" id="roleMenus">' . $groupsHtml . '</div>
				<div class="row">
					<div class="col-12">
						<button type="button" class="btn btn-primary" onclick="roleManagementSave(' . $id . ');">' . $h($lang['0000']) . '</button>
						<button type="button" class="btn btn-primary" onclick="roleManagementHideForm();">' . $h($lang['0001']) . '</button>
					</div>
				</div>
			</div>';

	$retunData = array('status' => '1', 'message' => 'Success.', 'dataRoleForm' => $html);
	$Config->Close();
	echo json_encode($retunData);
?>
