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
	require_once dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'view_as.inc.php';
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('viewAsManagement.php');

	include('lang.' . $_COOKIE[$Config->getAlias() . 'language'] . '.php');

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
	if (!$sessionstat || !az_user_is_admin($Config, $schema, az_view_as_real_username($Config))) {
		echo json_encode(array('status' => '0', 'message' => $lang['js0002'] ?? 'Error'));
		exit;
	}
	$h = function ($v) {
		return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
	};
	$th = '<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="white-space: normal;padding: 0.75rem 0.2rem;">';
	$td = '<td class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">';
	$status = array('1' => $lang['927'], '0' => $lang['928'], '-1' => $lang['929']);
	$realUser = az_view_as_real_username($Config);

	$html = '<div id="viewAsManagement" class="tabla active" style="display: block;padding-top: 10px;">
				<div class="row">
					<div class="col-12 col-md-6"><h2>' . $h($lang['rol027']) . '</h2></div>
					<div class="col-12 col-md-6">
						<div style="position: relative;">
							<input type="text" id="viewAsFilter" class="form-control" autocomplete="off" placeholder="' . $h($lang['rol030']) . '"
								style="border: 1px solid #136aeb !important; border-radius: .5rem; background: #fff; padding: .5rem 2.6rem .5rem .75rem;">
							<svg width="18" height="18" viewBox="0 0 24 24" style="position: absolute; right: .8rem; top: 50%; transform: translateY(-50%); pointer-events: none;" fill="none" stroke="#136aeb" stroke-width="2.2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><line x1="16.5" y1="16.5" x2="21" y2="21"/></svg>
						</div>
					</div>
				</div>
				<p class="text-secondary text-xs mt-2">' . $h($lang['rol031']) . '</p>
				<div class="card">
					<div class="table-responsive">
						<table class="table align-items-center mb-0" style="border-color: #136aeb;">
							<thead>
								' . $th . $h($lang['802']) . '</th>
								' . $th . $h($lang['803']) . '</th>
								' . $th . $h($lang['rol001']) . '</th>
								' . $th . $h($lang['926']) . '</th>
								<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="padding: 0.75rem 0.2rem;">' . $h($lang['rol027']) . '</th>
							</thead>
							<tbody>';
	$r = $Config->query("SELECT u.username, CONCAT_WS(' ', u.name, u.ApellidoP, u.ApellidoM) full_name, u.active,
				(SELECT GROUP_CONCAT(r.Role_Name ORDER BY r.Role_Name SEPARATOR ', ') FROM $schema.usuarios_role ur JOIN $schema.role r ON r.Role_ID = ur.Role_ID WHERE ur.username = u.username) roles
			FROM $schema.usuarios u
			ORDER BY u.username");
	$count = 0;
	while ($r && ($row = $r->fetch_assoc())) {
		if ((string) $row['username'] === $realUser) {
			continue;
		}
		$search = mb_strtolower($row['username'] . ' ' . $row['full_name'] . ' ' . $row['roles'], 'UTF-8');
		$html .= '<tr class="view-as-row' . (($count % 2) == 1 ? '' : ' alt') . '" data-search="' . $h($search) . '">'
			. $td . $h($row['username']) . '</span></td>'
			. $td . $h($row['full_name']) . '</span></td>'
			. $td . $h($row['roles'] ?? '') . '</span></td>'
			. $td . $h($status[(string) $row['active']] ?? $row['active']) . '</span></td>'
			. '<td class="align-middle text-center"><button type="button" class="btn btn-sm btn-primary mb-0" onclick="viewAsStart(' . $h(json_encode((string) $row['username'])) . ');">'
			. '<i class="material-symbols-rounded" style="font-size: 16px; vertical-align: middle;">visibility</i></button></td>
			</tr>';
		$count++;
	}
	$html .= '			</tbody>
						</table>
					</div>
				</div>
			</div>';

	$retunData = array('status' => '1', 'message' => 'Success.', 'dataViewAs' => $html);
	$Config->Close();
	echo json_encode($retunData);
?>
