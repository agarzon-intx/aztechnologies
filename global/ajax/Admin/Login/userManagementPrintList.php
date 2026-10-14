<?php
	$umParam = function ($key) {
		if (isset($_POST[$key])) {
			return trim((string) $_POST[$key]);
		}
		return isset($_GET[$key]) ? trim((string) $_GET[$key]) : '';
	};
	$userListFilter = $umParam('userListFilter');
	$umSort = strtolower($umParam('userListSort'));
	if ($umSort !== 'asc' && $umSort !== 'desc') {
		$umSort = '';
	}
	$umStatus = $umParam('userListStatus');
	if (!in_array($umStatus, array('1', '0', '-1'), true)) {
		$umStatus = '1';
	}

	$userListFilterSql = '';
	if ($userListFilter !== '') {
		$umConn = $Config->connect();
		if ($umConn) {
			$umEsc = $umConn->real_escape_string($userListFilter);
			$umLike = "'%" . $umEsc . "%'";
			$userListFilterSql = " WHERE (
				a.username LIKE $umLike
				OR a.name LIKE $umLike
				OR a.ApellidoP LIKE $umLike
				OR a.ApellidoM LIKE $umLike
				OR CONCAT(IFNULL(a.name, ''), ' ', IFNULL(a.ApellidoP, ''), ' ', IFNULL(a.ApellidoM, '')) LIKE $umLike
			)";
		}
	}

	$umStatuses = array(
		'1' => array('pane' => 'umUsersActive', 'tab' => $lang['js907'], 'label' => $lang['927']),
		'0' => array('pane' => 'umUsersInactive', 'tab' => $lang['js908'], 'label' => $lang['928']),
		'-1' => array('pane' => 'umUsersSuspended', 'tab' => $lang['js908-1'], 'label' => $lang['929']),
	);

	if (empty($umSkipFilterRow)) {
		$umFilterValue = htmlspecialchars($userListFilter, ENT_QUOTES, 'UTF-8');
		$umFilterPlaceholder = isset($lang['10762']) ? $lang['10762'] : 'Search user, name or last names';
		$htmlUsers .= '<div class="row">
						<div class="col-8 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<h2>' . $lang['800'] . '</h2>
						</div>
						<div class="col-4 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<button type="button" class="btn btn-primary" onClick="userManagementShowAdd();" >' . $lang['0013'] . '</button>
						</div>
					</div>
					<div class="row mb-3 align-items-center" id="userManagementListFilterRow">
						<div class="col-12 col-md-6 mb-2 mb-md-0">
							<div class="nav-wrapper position-relative end-0">
								<ul class="nav nav-pills nav-fill p-1" role="tablist" style="background: #cee6ff; flex-direction: unset !important;" id="umStatusTabs">';
		foreach ($umStatuses as $umCode => $umSt) {
			$umOn = ((string) $umCode === $umStatus);
			$htmlUsers .= '<li class="nav-item" id="' . $umSt['pane'] . 'li">
										<a class="nav-link mb-0 px-0 py-1' . ($umOn ? ' active' : '') . '" data-bs-toggle="tab" style="cursor: pointer;" callval="#' . $umSt['pane'] . '" data-status="' . $umCode . '" role="tab" aria-controls="' . $umSt['pane'] . 'li" aria-selected="' . ($umOn ? 'true' : 'false') . '">
											' . $umSt['tab'] . '
										</a>
									</li>';
		}
		$htmlUsers .= '			</ul>
							</div>
							<script>initNavs("umStatusTabs");</script>
						</div>
						<div class="col-12 col-md-6">
							<input type="search" class="form-control" id="userListFilter" name="userListFilter"
								placeholder="' . htmlspecialchars($umFilterPlaceholder, ENT_QUOTES, 'UTF-8') . '"
								value="' . $umFilterValue . '"
								autocomplete="off"
								oninput="userManagementFilterListDebounced();" />
						</div>
					</div>';
	}

	$umSeason = (int) $Season;
	$umOrder = $umSort !== '' ? 'a.username ' . strtoupper($umSort) : 'rank, username desc';
	$Config->query("SET @rank:=0;");
	$sql0 = "SELECT @rank:=@rank+1 AS rank, id_user,
				a.username,
				phone_number,
				name,
				ApellidoP,
				ApellidoM,
				email,
				GROUP_CONCAT(ifnull(c.Equipo_FULLDESC ,'')) Equipo_FULLDESC,
				MAX((select i.Institucion_DESC from $schema.Instituciones i where i.Institucion_ID = a.Institucion_ID and a.Institucion_ID > 0 limit 1)) Institucion_DESC,
				a.active,
				case when a.username <> 'admin' then case when ISNULL(d.ct) then '' else 'style=\"visibility: hidden; display: none;\"' end else 'style=\"visibility: hidden; display: none;\"' end dispDel
			FROM $schema.usuarios a
	            left outer join (select distinct * from $schema.usuarios_equipo) b on a.username = b.username
				left outer join (   select -1 Equipo_ID, '" . $lang['10761'] . "' Equipo_FULLDESC
            						UNION
            						select 0 Equipo_ID, '" . $Config->liga . "' Equipo_FULLDESC
            						UNION
            						select a.Equipo_ID, CONCAT(c.Categoria_Desc , ' - ' , a.Equipo_FULLDESC) Equipo_FULLDESC 
            						from (select Equipo_ID, Equipo_FULLDESC, Torneo_ID from $schema.Equipos where Torneo_ID = $umSeason) a
                        				join (	select Equipo_ID, Fuerza, MAX(Torneo_ID) Torneo_ID 
                        						from $schema.Equipos a 
												where a.Torneo_ID = $umSeason
                        						group by Equipo_ID) b on a.Equipo_ID = b.Equipo_ID and a.Torneo_ID = b.Torneo_ID
										join $schema.Categorias c on b.Fuerza = c.Categoria_ID
									where a.Torneo_ID = $umSeason and c.Torneo_ID = $umSeason) c on c.Equipo_ID = b.Equipo_ID
				left outer join (	SELECT count(*) ct, User_ID FROM $schema.Control_Table
									group by User_ID) d on a.username = d.User_ID
			$userListFilterSql
			GROUP BY id_user
			order by $umOrder;";
	$result2 = $Config->query($sql0);

	$umRows = array('1' => array(), '0' => array(), '-1' => array());
	if ($result2 && $result2->num_rows > 0) {
		while ($row2 = $result2->fetch_assoc()) {
			if (trim((string) $row2['Equipo_FULLDESC'], ', ') === '' && (string) $row2['Institucion_DESC'] !== '') {
				$row2['Equipo_FULLDESC'] = $lang['819-2'] . ' - ' . $row2['Institucion_DESC'];
			}
			$umCode = (string) (int) $row2['active'];
			if (!isset($umRows[$umCode])) {
				$umCode = '0';
			}
			$row2['active'] = $umStatuses[$umCode]['label'];
			$umRows[$umCode][] = $row2;
		}
	}

	$umSortMark = $umSort === 'asc' ? ' &#9650;' : ($umSort === 'desc' ? ' &#9660;' : ' &#8693;');
	$umTh = '<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;">';
	$umCell = function ($text) {
		return '<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $text . '</span></td>';
	};
	$umBox = function ($width, $title, $text, $wrap = true) {
		return '<div style="width: ' . $width . ';text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $title . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal' . ($wrap ? ' text-wrap' : '') . '">' . $text . '</span></div></div></div>';
	};
	$umShort = function ($text, $max, $cut) {
		return strlen($text) > $max ? substr($text, 0, $cut) . '...' : $text;
	};

	$htmlUsers .= '<div id="userManagementListTables"><div class="tabla-content">';
	foreach ($umStatuses as $umCode => $umSt) {
		$rows = $umRows[(string) $umCode];
		$strike = ((string) $umCode !== '1');
		$si = $strike ? '<strike>' : '';
		$sf = $strike ? '</strike>' : '';
		$htmlUsers .= '<div id="' . $umSt['pane'] . '" class="tabla' . ((string) $umCode === $umStatus ? ' active" style="display: block; height: auto;"' : '" style="display: none; height: auto;"') . '>';

		$htmlUsers .= '<div class="d-none d-sm-none d-md-none d-lg-block d-xl-block">
					<div class="card">
						<div class="table-responsive">
							<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
								<thead class="">
									' . $umTh . '</th>
									' . $umTh . '<a href="javascript:void(0)" onclick="userManagementSortBy();" class="text-secondary" style="cursor: pointer; white-space: nowrap;">' . $lang['802'] . $umSortMark . '</a></th>
									' . $umTh . $lang['803'] . '</th>
									' . $umTh . $lang['804'] . '</th>
									' . $umTh . $lang['805'] . '</th>
									' . $umTh . $lang['806'] . '</th>
									' . $umTh . $lang['807'] . '</th>
									' . $umTh . $lang['808'] . '</th>
									' . $umTh . $lang['926'] . '</th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;">' . $lang['809'] . '</th>
								</thead>
								<tbody>';
		foreach ($rows as $count => $row2) {
			$htmlUsers .= ($count % 2) == 1 ? '<tr>' : "<tr class='alt'>";
			$htmlUsers .= '<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal"><img src="imagenes/eliminar.png" height="15" width="15" onClick="userManagementDeleteUser(' . $row2["id_user"] . ')" ' . $row2["dispDel"] . '></span></td>'
				. $umCell($row2["username"])
				. $umCell($row2["name"])
				. $umCell($row2["ApellidoP"])
				. $umCell($row2["ApellidoM"])
				. $umCell($row2["phone_number"])
				. $umCell($umShort($row2["email"], 27, 27))
				. $umCell(str_replace(",", "<br/>", $row2["Equipo_FULLDESC"]))
				. $umCell($row2["active"])
				. '<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal"><img onClick="userManagementShowEdit(' . $row2["id_user"] . ')" src="./imagenes/edit.png" width="20" height="20" alt=""/></span></td>
					</tr>';
		}
		$htmlUsers .= '</tbody></table></div></div></div>';

		foreach (array('tablet', 'phone') as $umSize) {
			$phone = ($umSize === 'phone');
			$htmlUsers .= '<div class="' . ($phone ? 'd-block d-sm-none d-md-none d-lg-none d-xl-none' : 'd-none d-sm-block d-md-block d-lg-none d-xl-none') . '">
					<div class="card">
						<div class="table-responsive">
							<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
								<tbody>';
			foreach ($rows as $count => $row2) {
				$htmlUsers .= ($count % 2) == 1 ? '<tr>' : "<tr class='alt'>";
				$htmlUsers .= '<td scope="row">
							<div class="d-flex px-0 py-1">'
					. $umBox('5%', '', '<img src="imagenes/eliminar.png" height="15" width="15" onClick="userManagementDeleteUser(' . $row2["id_user"] . ')" ' . $row2["dispDel"] . '>')
					. $umBox($phone ? '20%' : '15%', $lang['802'], $si . $row2["username"] . $sf)
					. $umBox($phone ? '25%' : '20%', $lang['803'], $si . $row2["name"] . $sf)
					. $umBox($phone ? '25%' : '20%', $lang['804'], $si . $row2["ApellidoP"] . $sf)
					. $umBox($phone ? '25%' : '20%', $lang['805'], $si . $row2["ApellidoM"] . $sf)
					. ($phone ? '' : $umBox('20%', $lang['806'], $si . $row2["phone_number"] . $sf))
					. '</div>
							<div class="d-flex px-0 py-1">'
					. $umBox('35%', $lang['807'], $si . ($phone ? $umShort($row2["email"], 15, 12) : $umShort($row2["email"], 20, 17)) . $sf)
					. $umBox('35%', $lang['808'], $si . $row2["Equipo_FULLDESC"] . $sf)
					. $umBox('15%', $lang['926'], $si . $row2["active"] . $sf)
					. $umBox('15%', $lang['809'], '<img onClick="userManagementShowEdit(' . $row2["id_user"] . ')" src="./imagenes/edit.png" width="20" height="20" alt=""/>', false)
					. '</div>
						   </td>
						</tr>';
			}
			$htmlUsers .= '</tbody></table></div></div></div>';
		}
		$htmlUsers .= '</div>';
	}
	$htmlUsers .= '</div></div>';
?>
