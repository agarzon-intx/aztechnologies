<?php
/*
 * Roles (role / roleMenu / usuarios_role) decide which admin menu options a user sees:
 * - league admins (equipo 0) without roles, and the "admin" user, see the whole admin menu;
 * - league admins with roles see only the options of their roles;
 * - other users (teams, institutions, referees) keep their own menu plus the options of their roles.
 * Roles only hide / show menu options.
 */

if (!function_exists('az_role_menu_catalog')) {
	/** Admin menu groups: id, icon, title, items (key => array(icon, label, onclick)) and an optional nested group. */
	function az_role_menu_catalog($lang)
	{
		$t = function ($key, $fallback) use ($lang) {
			return isset($lang[$key]) ? $lang[$key] : $fallback;
		};
		return array(
			array('id' => 'menu1', 'icon' => 'settings', 'title' => $t('121', 'Configuration'), 'items' => array(
				'config' => array('settings', $t('121', 'Configuration'), 'configManagementShow();'),
				'colors' => array('palette', $t('114', 'Colors'), 'colorManagementShow();'),
				'users' => array('emoji_events', $t('103', 'Users'), 'userManagementShow();'),
				'roles' => array('admin_panel_settings', $t('rol001', 'Roles'), 'roleManagementShow();'),
			)),
			array('id' => 'menu2', 'icon' => 'emoji_events', 'title' => $t('104', 'Tournaments'), 'items' => array(
				'tournaments' => array('emoji_events', $t('105', 'Tournaments'), 'tournamentManagementShow();'),
			), 'sub' => array('id' => 'menu2-1', 'icon' => 'date_range', 'title' => $t('108', 'Weeks'), 'items' => array(
				'calendars' => array('date_range', $t('133', 'Calendars'), 'calendarManagementShow();'),
				'weeks' => array('date_range', $t('108', 'Weeks'), 'weekManagementShow();'),
			))),
			array('id' => 'menu3', 'icon' => 'category', 'title' => $t('109', 'Categories'), 'items' => array(
				'categories' => array('category', $t('109', 'Categories'), 'categoryManagementShow();'),
			)),
			array('id' => 'menu4', 'icon' => 'public', 'title' => $t('110', 'Fields'), 'items' => array(
				'fields' => array('public', $t('110', 'Fields'), 'fieldManagementShow();'),
			)),
			array('id' => 'menu5', 'icon' => 'folder_shared', 'title' => $t('112', 'Teams'), 'items' => array(
				'institutions' => array('account_balance', $t('113-2', 'Institutions'), 'institutionsManagementShow();'),
				'teams' => array('groups', $t('113', 'Teams'), 'teamsManagementAdminCategoryShow();'),
				'players' => array('account_circle', $t('115', 'Players'), 'playersManagementAdminCategoryShow();'),
				'playerList' => array('badge', $t('123', 'Print list'), 'playersManagementAdminShowPrintList();'),
			)),
			array('id' => 'menu6', 'icon' => 'scoreboard', 'title' => $t('100', 'Games'), 'items' => array(
				'games' => array('scoreboard', $t('101', 'Scores'), 'loadWeeksAdmin(0);'),
				'generateSchedule' => array('calendar_month', $t('101-1', 'Generate schedule'), 'generateScheduleShow();'),
				'teamSubstitution' => array('swap_horiz', $t('101-44', 'Team substitution'), 'teamSubstitutionShow();'),
				'gamesCsv' => array('download', $t('101-52', 'Generate CSV'), 'gamesExportCsv();'),
			)),
			array('id' => 'menu7', 'icon' => 'notifications', 'title' => $t('116', 'Alerts'), 'items' => array(
				'alerts' => array('notifications', $t('117', 'Alerts'), 'alertManagementShow();'),
			)),
			array('id' => 'menu8', 'icon' => 'description', 'title' => $t('118', 'Memos'), 'items' => array(
				'memos' => array('description', $t('118', 'Memos'), 'memoManagementShow();'),
			)),
		);
	}

	/** All menu keys of the catalog. */
	function az_role_menu_keys($lang)
	{
		$keys = array();
		foreach (az_role_menu_catalog($lang) as $group) {
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

	/** Replaces the user's roles with the existing roles among $roleIds. */
	function az_save_user_roles($Config, $schema, $username, $roleIds)
	{
		$user = $Config->quote((string) $username);
		$ids = array_filter(array_map('intval', (array) $roleIds), function ($id) {
			return $id > 0;
		});
		$Config->query("DELETE FROM $schema.usuarios_role WHERE username = $user");
		if ($ids) {
			$Config->query("INSERT INTO $schema.usuarios_role (username, Role_ID)
				SELECT $user, Role_ID FROM $schema.role WHERE Role_ID IN (" . implode(',', $ids) . ")");
		}
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

	/**
	 * Sidebar HTML of the catalog groups. $allowed: menu keys to show (null = all); $hidden: keys never shown
	 * (options unavailable on this site); $idPrefix keeps collapse ids unique next to other menus.
	 */
	function az_role_menu_html($lang, $allowed = null, $hidden = array(), $idPrefix = '')
	{
		$show = function ($key) use ($allowed, $hidden) {
			return !in_array($key, $hidden, true) && ($allowed === null || in_array($key, $allowed, true));
		};
		$itemsHtml = function ($items, $size) use ($show) {
			$html = '';
			foreach ($items as $key => $item) {
				if (!$show($key)) {
					continue;
				}
				$html .= '
									<li class="nav-item ">
										<a class="nav-link text-white " aria-expanded="false" onclick="' . $item[2] . ' toggleSidenav();">
											<span class="sidenav-mini-icon"> <i class="material-symbols-rounded" style="font-size: ' . $size . 'px;">' . $item[0] . '</i> </span>
											<span class="sidenav-normal  ms-2  ps-1"> ' . $item[1] . ' </span>
										</a>
									</li>';
			}
			return $html;
		};
		$html = '';
		foreach (az_role_menu_catalog($lang) as $group) {
			$body = $itemsHtml($group['items'], 16);
			if (!empty($group['sub'])) {
				$sub = $group['sub'];
				$subBody = $itemsHtml($sub['items'], 14);
				if ($subBody !== '') {
					$body .= '
									<li class="nav-item">
										<a data-bs-toggle="collapse" href="#' . $idPrefix . $sub['id'] . '" class="nav-link text-white " aria-controls="' . $idPrefix . $sub['id'] . '" role="button" aria-expanded="false">
											<i class="material-symbols-rounded me-2" style="font-size: 16px;">' . $sub['icon'] . '</i>
											<span class="nav-link-text ms-2 ps-1">' . $sub['title'] . '</span>
										</a>
										<div class="collapse " id="' . $idPrefix . $sub['id'] . '" style="margin-left: 5px;">
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
							<a data-bs-toggle="collapse" href="#' . $idPrefix . $group['id'] . '" class="nav-link text-white " aria-controls="' . $idPrefix . $group['id'] . '" role="button" aria-expanded="false">
								<i class="material-symbols-rounded me-2">' . $group['icon'] . '</i>
								<span class="nav-link-text ms-2 ps-1">' . $group['title'] . '</span>
							</a>
							<div class="collapse " id="' . $idPrefix . $group['id'] . '" style="margin-left: 5px;">
								<ul class="nav ">' . $body . '
								</ul>
							</div>
						</li>';
		}
		return $html;
	}
}
