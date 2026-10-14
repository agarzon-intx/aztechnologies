<?php
/*
 * Roles (role / roleMenu / usuarios_role) decide which menu options a user sees:
 * every user needs at least one role and sees exactly the options of their roles (union of all of them);
 * users without roles only get logout. The "admin" user always gets the full admin menu.
 * Roles only hide / show menu options.
 */

if (!function_exists('az_role_menu_catalog')) {
	/**
	 * Menu groups: id, scope (admin / referee / team), icon, title, items (key => array(icon, label, onclick))
	 * and an optional nested group. "{equipo}" in an onclick is replaced by the user's teams.
	 */
	function az_role_menu_catalog($lang)
	{
		$t = function ($key, $fallback) use ($lang) {
			return isset($lang[$key]) ? $lang[$key] : $fallback;
		};
		return array(
			array('id' => 'menu1', 'scope' => 'admin', 'icon' => 'settings', 'title' => $t('121', 'Configuration'), 'items' => array(
				'config' => array('settings', $t('121', 'Configuration'), 'configManagementShow();'),
				'colors' => array('palette', $t('114', 'Colors'), 'colorManagementShow();'),
				'users' => array('emoji_events', $t('103', 'Users'), 'userManagementShow();'),
				'roles' => array('admin_panel_settings', $t('rol001', 'Roles'), 'roleManagementShow();'),
			)),
			array('id' => 'menu2', 'scope' => 'admin', 'icon' => 'emoji_events', 'title' => $t('104', 'Tournaments'), 'items' => array(
				'tournaments' => array('emoji_events', $t('105', 'Tournaments'), 'tournamentManagementShow();'),
			), 'sub' => array('id' => 'menu2-1', 'icon' => 'date_range', 'title' => $t('108', 'Weeks'), 'items' => array(
				'calendars' => array('date_range', $t('133', 'Calendars'), 'calendarManagementShow();'),
				'weeks' => array('date_range', $t('108', 'Weeks'), 'weekManagementShow();'),
			))),
			array('id' => 'menu3', 'scope' => 'admin', 'icon' => 'category', 'title' => $t('109', 'Categories'), 'items' => array(
				'categories' => array('category', $t('109', 'Categories'), 'categoryManagementShow();'),
			)),
			array('id' => 'menu4', 'scope' => 'admin', 'icon' => 'public', 'title' => $t('110', 'Fields'), 'items' => array(
				'fields' => array('public', $t('110', 'Fields'), 'fieldManagementShow();'),
			)),
			array('id' => 'menu5', 'scope' => 'admin', 'icon' => 'folder_shared', 'title' => $t('112', 'Teams'), 'items' => array(
				'institutions' => array('account_balance', $t('113-2', 'Institutions'), 'institutionsManagementShow();'),
				'teams' => array('groups', $t('113', 'Teams'), 'teamsManagementAdminCategoryShow();'),
				'players' => array('account_circle', $t('115', 'Players'), 'playersManagementAdminCategoryShow();'),
				'playerList' => array('badge', $t('123', 'Print list'), 'playersManagementAdminShowPrintList();'),
			)),
			array('id' => 'menu6', 'scope' => 'admin', 'icon' => 'scoreboard', 'title' => $t('100', 'Games'), 'items' => array(
				'games' => array('scoreboard', $t('101', 'Scores'), 'loadWeeksAdmin(0);'),
				'generateSchedule' => array('calendar_month', $t('101-1', 'Generate schedule'), 'generateScheduleShow();'),
				'teamSubstitution' => array('swap_horiz', $t('101-44', 'Team substitution'), 'teamSubstitutionShow();'),
				'gamesCsv' => array('download', $t('101-52', 'Generate CSV'), 'gamesExportCsv();'),
			)),
			array('id' => 'menu7', 'scope' => 'admin', 'icon' => 'notifications', 'title' => $t('116', 'Alerts'), 'items' => array(
				'alerts' => array('notifications', $t('117', 'Alerts'), 'alertManagementShow();'),
			)),
			array('id' => 'menu8', 'scope' => 'admin', 'icon' => 'description', 'title' => $t('118', 'Memos'), 'items' => array(
				'memos' => array('description', $t('118', 'Memos'), 'memoManagementShow();'),
			)),
			array('id' => 'menuReferee', 'scope' => 'referee', 'icon' => 'sports', 'title' => $t('135', 'Referees'), 'items' => array(
				'refereeGames' => array('scoreboard', $t('rol018', 'Referee scores'), 'loadWeeksAdminR(0);'),
			)),
			array('id' => 'menuTeam', 'scope' => 'team', 'icon' => 'account_circle', 'title' => $t('rol020', 'My teams'), 'items' => array(
				'teamPlayers' => array('account_circle', $t('115', 'Players'), 'playersManagementTeamCategoryShow({equipo});'),
				'teamGames' => array('scoreboard', $t('rol019', 'My teams scores'), 'loadWeeksAdminC(0);'),
			)),
		);
	}

	/** Menu keys of the catalog (optionally only the groups of $scope). */
	function az_role_menu_keys($lang, $scope = null)
	{
		$keys = array();
		foreach (az_role_menu_catalog($lang) as $group) {
			if ($scope !== null && $group['scope'] !== $scope) {
				continue;
			}
			$keys = array_merge($keys, array_keys($group['items']));
			if (!empty($group['sub'])) {
				$keys = array_merge($keys, array_keys($group['sub']['items']));
			}
		}
		return $keys;
	}

	/** Role_IDs of a user. */
	function az_user_role_ids($Config, $schema, $username)
	{
		$ids = array();
		$r = $Config->query("SELECT Role_ID FROM $schema.usuarios_role WHERE username = " . $Config->quote((string) $username));
		while ($r && ($row = $r->fetch_assoc())) {
			$ids[] = (int) $row['Role_ID'];
		}
		return $ids;
	}

	/** Menu keys granted by the user's roles; null when the user has no roles. */
	function az_user_role_menu($Config, $schema, $username)
	{
		$r = $Config->query("SELECT DISTINCT rm.Menu_Element FROM $schema.usuarios_role ur
				JOIN $schema.role r ON r.Role_ID = ur.Role_ID
				LEFT JOIN $schema.roleMenu rm ON rm.Role_ID = ur.Role_ID
			WHERE ur.username = " . $Config->quote((string) $username));
		if (!$r || $r->num_rows === 0) {
			return null;
		}
		$keys = array();
		while ($row = $r->fetch_assoc()) {
			if ($row['Menu_Element'] !== null && $row['Menu_Element'] !== '') {
				$keys[] = (string) $row['Menu_Element'];
			}
		}
		return $keys;
	}

	/** Replaces the user's roles with the existing roles among $roleIds (admin connection). */
	function az_save_user_roles($Config, $schema, $username, $roleIds)
	{
		$db = $Config->connectAdmin();
		if (!$db) {
			return false;
		}
		$user = "'" . $db->real_escape_string((string) $username) . "'";
		$ids = array_filter(array_map('intval', (array) $roleIds), function ($id) {
			return $id > 0;
		});
		$ok = $db->query("DELETE FROM $schema.usuarios_role WHERE username = $user");
		if ($ok && $ids) {
			$ok = $db->query("INSERT INTO $schema.usuarios_role (username, Role_ID)
				SELECT $user, Role_ID FROM $schema.role WHERE Role_ID IN (" . implode(',', $ids) . ")");
		}
		return (bool) $ok;
	}

	/** Role_IDs posted as CSV / array in $_POST[$field]. */
	function az_posted_role_ids($field = 'roles')
	{
		$raw = isset($_POST[$field]) ? $_POST[$field] : '';
		$list = is_array($raw) ? $raw : explode(',', (string) $raw);
		return array_values(array_unique(array_filter(array_map('intval', $list), function ($id) {
			return $id > 0;
		})));
	}

	/** Role types: 0 Admin and 2 Referee users get no teams; 1 Coach users get teams / institutions. */
	function az_role_types($lang)
	{
		return array(0 => $lang['rol024'], 1 => $lang['rol025'], 2 => $lang['rol026']);
	}

	/** Account type given by a set of roles: Admin wins over Coach, Coach over Referee; null without roles. */
	function az_role_account_type($Config, $schema, $roleIds)
	{
		$ids = array_filter(array_map('intval', (array) $roleIds), function ($id) {
			return $id > 0;
		});
		if (!$ids) {
			return null;
		}
		$types = array();
		$r = $Config->query("SELECT DISTINCT Role_Type FROM $schema.role WHERE Role_ID IN (" . implode(',', $ids) . ")");
		while ($r && ($row = $r->fetch_assoc())) {
			$types[] = (int) $row['Role_Type'];
		}
		foreach (array(0, 1, 2) as $type) {
			if (in_array($type, $types, true)) {
				return $type;
			}
		}
		return null;
	}

	/** usuarios_equipo value for an account type: "0" league, "-1" referee, or the posted teams / institutions. */
	function az_role_equipo($type, $postedEquipo)
	{
		if ($type === 0) {
			return '0';
		}
		if ($type === 2) {
			return '-1';
		}
		$keep = array();
		foreach (explode(',', (string) $postedEquipo) as $token) {
			$token = trim($token);
			if (preg_match('/^(i\d+|[1-9]\d*)$/', $token)) {
				$keep[$token] = $token;
			}
		}
		return implode(',', $keep);
	}

	/** Existing Role_IDs among $roleIds. */
	function az_valid_role_ids($Config, $schema, $roleIds)
	{
		$ids = array_filter(array_map('intval', (array) $roleIds), function ($id) {
			return $id > 0;
		});
		if (!$ids) {
			return array();
		}
		$valid = array();
		$r = $Config->query("SELECT Role_ID FROM $schema.role WHERE Role_ID IN (" . implode(',', $ids) . ")");
		while ($r && ($row = $r->fetch_assoc())) {
			$valid[] = (int) $row['Role_ID'];
		}
		return $valid;
	}

	/** Role checkboxes for the user create / edit forms. */
	function az_user_roles_html($Config, $schema, $lang, $selected = array())
	{
		$r = $Config->query("SELECT Role_ID, Role_Name, Role_Desc, Role_Type FROM $schema.role ORDER BY Role_Type, Role_Name");
		$boxes = '';
		while ($r && ($row = $r->fetch_assoc())) {
			$id = (int) $row['Role_ID'];
			$boxes .= '<div class="form-check form-check-inline" title="' . htmlspecialchars((string) $row['Role_Desc'], ENT_QUOTES, 'UTF-8') . '">
								<input class="form-check-input um-role" type="checkbox" id="umRole' . $id . '" value="' . $id . '" data-type="' . (int) $row['Role_Type'] . '"' . (in_array($id, $selected, true) ? ' checked' : '') . '>
								<label class="form-check-label" for="umRole' . $id . '">' . htmlspecialchars((string) $row['Role_Name'], ENT_QUOTES, 'UTF-8') . '</label>
							</div>';
		}
		if ($boxes === '') {
			$boxes = '<span class="text-secondary text-xs">' . htmlspecialchars($lang['rol017'], ENT_QUOTES, 'UTF-8') . '</span>';
		}
		return '<div class="row">
						<div class="col-12" id="umRoles" data-required-msg="' . htmlspecialchars($lang['rol021'], ENT_QUOTES, 'UTF-8') . '" data-teams-msg="' . htmlspecialchars($lang['rol022'], ENT_QUOTES, 'UTF-8') . '">
							<h6 class="mb-1">' . htmlspecialchars($lang['rol014'], ENT_QUOTES, 'UTF-8') . ' *</h6>
							' . $boxes . '
							<div id="umRolesError" class="error" style="color: red;"></div>
							<p class="text-secondary text-xs mb-3">' . htmlspecialchars($lang['rol015'], ENT_QUOTES, 'UTF-8') . '</p>
						</div>
					</div>
					<script type="text/javascript">setTimeout(function () { if (typeof userManagementApplyRoleType === "function") { userManagementApplyRoleType(); } }, 0);</script>';
	}

	/**
	 * Sidebar HTML of the catalog. $allowed: menu keys to show (null = all of $scopes); $hidden: keys never
	 * shown (options unavailable right now); $equipo replaces "{equipo}" in the onclick handlers.
	 */
	function az_role_menu_html($lang, $allowed = null, $hidden = array(), $scopes = null, $equipo = '0')
	{
		$show = function ($key) use ($allowed, $hidden) {
			return !in_array($key, $hidden, true) && ($allowed === null || in_array($key, $allowed, true));
		};
		$equipoJs = preg_replace('/[^0-9,\-]/', '', (string) $equipo);
		$itemsHtml = function ($items, $size) use ($show, $equipoJs) {
			$html = '';
			foreach ($items as $key => $item) {
				if (!$show($key)) {
					continue;
				}
				$html .= '
									<li class="nav-item ">
										<a class="nav-link text-white " aria-expanded="false" onclick="' . str_replace('{equipo}', $equipoJs === '' ? '0' : $equipoJs, $item[2]) . ' toggleSidenav();">
											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: ' . $size . 'px;">' . $item[0] . '</i> </span>
											<span class="sidenav-normal  ms-2  ps-1"> ' . $item[1] . ' </span>
										</a>
									</li>';
			}
			return $html;
		};
		$html = '';
		foreach (az_role_menu_catalog($lang) as $group) {
			if ($scopes !== null && !in_array($group['scope'], (array) $scopes, true)) {
				continue;
			}
			$body = $itemsHtml($group['items'], 16);
			if (!empty($group['sub'])) {
				$sub = $group['sub'];
				$subBody = $itemsHtml($sub['items'], 14);
				if ($subBody !== '') {
					$body .= '
									<li class="nav-item">
										<a data-bs-toggle="collapse" href="#' . $sub['id'] . '" class="nav-link text-white " aria-controls="' . $sub['id'] . '" role="button" aria-expanded="false">
											<i class="material-symbols-rounded me-2" style="font-size: 16px;">' . $sub['icon'] . '</i>
											<span class="nav-link-text ms-2 ps-1">' . $sub['title'] . '</span>
										</a>
										<div class="collapse " id="' . $sub['id'] . '" style="margin-left: 5px;">
											<ul class="nav ">' . $subBody . '
											</ul>
										</div>
									</li>';
				}
			}
			if ($body === '') {
				continue;
			}
			$html .= '<li class="nav-item">
							<a data-bs-toggle="collapse" href="#' . $group['id'] . '" class="nav-link text-white " aria-controls="' . $group['id'] . '" role="button" aria-expanded="false">
								<i class="material-symbols-rounded me-2">' . $group['icon'] . '</i>
								<span class="nav-link-text ms-2 ps-1">' . $group['title'] . '</span>
							</a>
							<div class="collapse " id="' . $group['id'] . '" style="margin-left: 5px;">
								<ul class="nav ">' . $body . '
								</ul>
							</div>
						</li>';
		}
		return $html;
	}
}
