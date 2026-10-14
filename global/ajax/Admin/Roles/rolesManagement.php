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
	$sessionstat = $fgmembersite->CheckLogin('rolesManagement.php');

	include('lang.' . $_COOKIE[$Config->getAlias() . 'language'] . '.php');

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
	$h = function ($v) {
		return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
	};
	$th = '<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="white-space: normal;padding: 0.75rem 0.2rem;">';
	$td = '<td class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">';

	$html = '<div id="rolesManagement" class="tabla active" style="display: block;padding-top: 10px;">
				<div id="rolesManagementList">
					<div class="row">
						<div class="col-8 col-md-6"><h2>' . $h($lang['rol002']) . '</h2></div>
						<div class="col-4 col-md-6"><button type="button" class="btn btn-primary" onclick="roleManagementShowForm(0);">' . $h($lang['0013']) . '</button></div>
					</div>
					<div class="card">
						<div class="table-responsive">
							<table class="table align-items-center mb-0" style="border-color: #136aeb;">
								<thead>
									' . $th . '</th>
									' . $th . $h($lang['rol003']) . '</th>
									' . $th . $h($lang['rol004']) . '</th>
									' . $th . $h($lang['rol005']) . '</th>
									' . $th . $h($lang['103']) . '</th>
									' . $th . $h($lang['rol013']) . '</th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="padding: 0.75rem 0.2rem;">' . $h($lang['809']) . '</th>
								</thead>
								<tbody>';
	$r = $Config->query("SELECT r.Role_ID, r.Role_Name, r.Role_Desc,
				IFNULL(r.Update_Date, r.Create_Date) Changed, IFNULL(r.Update_User, r.Create_User) ChangedBy,
				(SELECT COUNT(*) FROM $schema.roleMenu m WHERE m.Role_ID = r.Role_ID) Menus,
				(SELECT COUNT(*) FROM $schema.usuarios_role u WHERE u.Role_ID = r.Role_ID) Users
			FROM $schema.role r
			ORDER BY r.Role_Name");
	$count = 0;
	while ($r && ($row = $r->fetch_assoc())) {
		$id = (int) $row['Role_ID'];
		$html .= ($count % 2) == 1 ? '<tr>' : "<tr class='alt'>";
		$html .= '<td class="align-middle text-left"><img src="imagenes/eliminar.png" height="15" width="15" style="cursor: pointer;" onclick="roleManagementDelete(' . $id . ');"></td>'
			. $td . $h($row['Role_Name']) . '</span></td>'
			. $td . $h($row['Role_Desc']) . '</span></td>'
			. $td . (int) $row['Menus'] . '</span></td>'
			. $td . (int) $row['Users'] . '</span></td>'
			. $td . $h($row['Changed']) . ' ' . $h($row['ChangedBy']) . '</span></td>'
			. '<td class="align-middle text-center"><img onclick="roleManagementShowForm(' . $id . ');" src="./imagenes/edit.png" width="20" height="20" style="cursor: pointer;" alt=""/></td>
				</tr>';
		$count++;
	}
	$html .= '			</tbody>
							</table>
						</div>
					</div>
				</div>
				<div id="rolesManagementForm" style="display: none;"></div>
			</div>';

	$retunData = array('status' => '1', 'message' => 'Success.', 'dataRoles' => $html, 'msgDelete' => $lang['rol011']);
	$Config->Close();
	echo json_encode($retunData);
?>
