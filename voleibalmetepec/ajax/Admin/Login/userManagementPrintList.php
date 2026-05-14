<?php
	$htmlUsers .= '<div class="d-none d-sm-none d-md-none d-lg-block d-xl-block">
					<div class="row">
						<div class="col-8 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<h2>' . $lang['800'] . '</h2>
						</div>
						<div class="col-4 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<button type="button" class="btn btn-primary" onClick="userManagementShowAdd();" >' . $lang['0013'] . '</button>
						</div>
					</div>
					<div class="card">
						<div class="table-responsive">
							<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
								<thead class="">
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;"></span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;">' . $lang['802'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;">' . $lang['803'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;">' . $lang['804'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;">' . $lang['805'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;">' . $lang['806'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;">' . $lang['807'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;">' . $lang['808'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;">' . $lang['837'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;">' . $lang['809'] . '</span></th>
								</thead>
								<tbody>';
								
	$sql0 = "SET @rank:=0;";
	$Config->query($sql0);
	$sql0 = "SELECT @rank:=@rank+1 AS rank, id_user,
				a.username,
				password,
				phone_number,
				confirmcode,
				name,
				ApellidoP,
				ApellidoM,
				email,
				GROUP_CONCAT(ifnull(b.Equipo_ID ,'')) Equipo_ID, 
				GROUP_CONCAT(ifnull(c.Equipo_FULLDESC ,'')) Equipo_FULLDESC,
				case When a.active = 0 then '" . $lang['0012'] . "' else '" . $lang['0011'] . "' end active,
				case When a.active = 0 then '' else '' end strikei,
				case When a.active = 0 then '' else '' end strikef,
				case when a.username <> 'admin' then case when ISNULL(d.ct) then '' else 'style=\"visibility: hidden; display: none;\"' end else 'style=\"visibility: hidden; display: none;\"' end dispDel
			FROM $schema.usuarios a
	            left outer join (select distinct * from $schema.usuarios_equipo) b on a.username = b.username
				left outer join (   select -1 Equipo_ID, '" . $lang['10761'] . "' Equipo_FULLDESC
            						UNION
            						select 0 Equipo_ID, '" . $Config->liga . "' Equipo_FULLDESC
            						UNION
            						select a.Equipo_ID, CONCAT(c.Categoria_Desc , ' - ' , a.Equipo_FULLDESC) Equipo_FULLDESC 
            						from (select Equipo_ID, Equipo_FULLDESC, Torneo_ID from $schema.Equipos where Torneo_ID = $Season) a
                        				join (	select Equipo_ID, Fuerza, MAX(Torneo_ID) Torneo_ID 
                        						from $schema.Equipos a 
												where a.Torneo_ID = $Season
                        						group by Equipo_ID) b on a.Equipo_ID = b.Equipo_ID and a.Torneo_ID = b.Torneo_ID
										join $schema.Categorias c on b.Fuerza = c.Categoria_ID
									where a.Torneo_ID = $Season and c.Torneo_ID = $Season) c on c.Equipo_ID = b.Equipo_ID
				left outer join (	SELECT count(*) ct, User_ID FROM $schema.Control_Table
									group by User_ID) d on a.username = d.User_ID
			GROUP BY id_user
			order by rank, username desc;";
			//echo $sql2;
	$result2 = $Config->query($sql0);

	$count = 0;
	if ($result2->num_rows > 0) {
		// output data of each row
		while($row2 = $result2->fetch_assoc()) {
			if (($count % 2) == 1){
				$htmlUsers .= "<tr>";
			}else{
				$htmlUsers .= "<tr class='alt'>";
			}
			$htmlUsers .= '<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal"><img src="imagenes/eliminar.png" height="15" width="15" onClick="userManagementDeleteUser(' . $row2["id_user"] . ')" ' . $row2["dispDel"] . '></span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"]  . $row2["username"] . $row2["strikef"]  . '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"]  . $row2["name"] . $row2["strikef"]  . '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"]  . $row2["ApellidoP"] . $row2["strikef"]  . '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"]  . $row2["ApellidoM"] . $row2["strikef"]  . '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"]  . $row2["phone_number"] . $row2["strikef"]  . '</span></td>';
			if(strlen($row2["email"]) > 27){
					$htmlUsers .= '<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"]  . substr($row2["email"],0,27) . '...' . $row2["strikef"]  . '</span></td>';
			}else{
				$htmlUsers .= '<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"]  . $row2["email"] . $row2["strikef"]  . '</span></td>';
			}
			if(strlen($row2["Equipo_FULLDESC"]) > 30){
				$htmlUsers .= '<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"]  . str_replace(",","<br/>",$row2["Equipo_FULLDESC"]) . $row2["strikef"]  . '</span></td>';
			}else{
				$htmlUsers .= '<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"]  . str_replace(",","<br/>",$row2["Equipo_FULLDESC"]) . $row2["strikef"]  . '</span></td>';
			}
			$htmlUsers .= '<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"]  . $row2["active"] . $row2["strikef"]  . '</span></td>
						<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal"><img onClick="userManagementShowEdit(' . $row2["id_user"] . ')" src="./imagenes/edit.png" width="20" height="20" alt=""/></span></td>
					</tr>';
			$count++;
		}
	}
	$htmlUsers .= '</tbody>';
	$htmlUsers .= '</table>';
	$htmlUsers .= '</div>';
	$htmlUsers .= '</div>';
	$htmlUsers .= '</div>';
	
	$htmlUsers .= '<div class="d-none d-sm-block d-md-block d-lg-none d-xl-none">
					<div class="row">
						<div class="col-8 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<h2>' . $lang['800'] . '</h2>
						</div>
						<div class="col-4 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<button type="button" class="btn btn-primary" onClick="userManagementShowAdd();" >' . $lang['0013'] . '</button>
						</div>
					</div>
					<div class="card">
						<div class="table-responsive">
							<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
								<tbody>';
								
	$sql1 = "SET @rank:=0;";
	$Config->query($sql1);
	$sql1 = "SELECT @rank:=@rank+1 AS rank, id_user,
			a.username,
			password,
			phone_number,
			confirmcode,
			name,
			ApellidoP,
			ApellidoM,
			email,
			GROUP_CONCAT(ifnull(b.Equipo_ID ,'')) Equipo_ID, 
			GROUP_CONCAT(ifnull(c.Equipo_FULLDESC ,'')) Equipo_FULLDESC,
			case When a.active = 0 then '" . $lang['0012'] . "' else '" . $lang['0011'] . "' end active,
			case When a.active = 0 then '<strike>' else '' end strikei,
			case When a.active = 0 then '</strike>' else '' end strikef,
			case when a.username <> 'admin' then case when ISNULL(d.ct) then '' else 'style=\"visibility: hidden; display: none;\"' end else 'style=\"visibility: hidden; display: none;\"' end dispDel
		FROM $schema.usuarios a
            left outer join (select distinct * from $schema.usuarios_equipo) b on a.username = b.username
			left outer join (   select 0 Equipo_ID, '" . $Config->liga . "' Equipo_FULLDESC
        						UNION
        						select a.Equipo_ID, CONCAT(c.Categoria_Desc , ' - ' , a.Equipo_FULLDESC) Equipo_FULLDESC 
            						from (select Equipo_ID, Equipo_FULLDESC, Torneo_ID from $schema.Equipos where Torneo_ID = $Season) a
                        				join (	select Equipo_ID, Fuerza, MAX(Torneo_ID) Torneo_ID 
                        						from $schema.Equipos a 
												where a.Torneo_ID = $Season
                        						group by Equipo_ID) b on a.Equipo_ID = b.Equipo_ID and a.Torneo_ID = b.Torneo_ID
										join $schema.Categorias c on b.Fuerza = c.Categoria_ID
									where a.Torneo_ID = $Season and c.Torneo_ID = $Season) c on c.Equipo_ID = b.Equipo_ID
			left outer join (	SELECT count(*) ct, User_ID FROM $schema.Control_Table
								group by User_ID) d on a.username = d.User_ID
		GROUP BY id_user
		order by rank, username desc;";
	$result2 = $Config->query($sql1);

	$count = 0;
	if ($result2->num_rows > 0) {
		// output data of each row
		while($row2 = $result2->fetch_assoc()) {
			if (($count % 2) == 1){
				$htmlUsers .= "<tr>";
			}else{
				$htmlUsers .= "<tr class='alt'>";
			}
			$htmlUsers .= '<td scope="row">
							<div class="d-flex px-0 py-1">
								<div style="width: 5%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold"></span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap"><img src="imagenes/eliminar.png" height="15" width="15" onClick="userManagementDeleteUser(' . $row2["id_user"] . ')" ' . $row2["dispDel"] . '></span></div></div></div>
									<div style="width: 15%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['802'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . $row2["username"] . $row2["strikef"]  . '</span></div></div></div>
									<div style="width: 20%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['803'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . $row2["name"] . $row2["strikef"]  . '</span></div></div></div>
									<div style="width: 20%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['804'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . $row2["ApellidoP"] . $row2["strikef"]  . '</span></div></div></div>
									<div style="width: 20%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['805'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . $row2["ApellidoM"] . $row2["strikef"]  . '</span></div></div></div>
									<div style="width: 20%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['806'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . $row2["phone_number"] . $row2["strikef"]  . '</span></div></div></div>
								</div>
							</div>
							<div class="d-flex px-0 py-1">
								<div style="width: 35%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['807'] . '</span></p>';
			if(strlen($row2["email"]) > 20){
					$htmlUsers .= '<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . substr($row2["email"],0,17) . '...' . $row2["strikef"]  . '</span></div></div></div>';
			}else{
				$htmlUsers .= '<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . $row2["email"] . $row2["strikef"]  . '</span></div></div></div>';
			}
			$htmlUsers .= '			<div style="width: 35%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['808'] . '</span></p>';
			$htmlUsers .= '<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . $row2["Equipo_FULLDESC"] . $row2["strikef"]  . '</span></div></div></div>';
			$htmlUsers .= '			<div style="width: 15%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['837'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . $row2["active"] . $row2["strikef"]  . '</span></div></div></div>
									<div style="width: 15%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['809'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal"><img onClick="userManagementShowEdit(' . $row2["id_user"] . ')" src="./imagenes/edit.png" width="20" height="20" alt=""/></span></div></div></div>
								</div>
							</div>
						   </td>
						</tr>';
			$count++;
		}
	}
	$htmlUsers .= '</tbody>';
	$htmlUsers .= '</table>';
	$htmlUsers .= '</div>';
	$htmlUsers .= '</div>';
	$htmlUsers .= '</div>';
	
	$htmlUsers .= '<div class="d-block d-sm-none d-md-none d-lg-none d-xl-none">
					<div class="row">
						<div class="col-8 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<h2>' . $lang['800'] . '</h2>
						</div>
						<div class="col-4 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<button type="button" class="btn btn-primary" onClick="userManagementShowAdd();" >' . $lang['0013'] . '</button>
						</div>
					</div>
					<div class="card">
						<div class="table-responsive">
							<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
								<tbody>';
								
	$sql2 = "SET @rank:=0;";
	$Config->query($sql2);
	$sql2 = "SELECT @rank:=@rank+1 AS rank, id_user,
			a.username,
			password,
			phone_number,
			confirmcode,
			name,
			ApellidoP,
			ApellidoM,
			email,
			GROUP_CONCAT(ifnull(b.Equipo_ID ,'')) Equipo_ID, 
			GROUP_CONCAT(ifnull(c.Equipo_FULLDESC ,'')) Equipo_FULLDESC,
			case When a.active = 0 then '" . $lang['0012'] . "' else '" . $lang['0011'] . "' end active,
			case When a.active = 0 then '<strike>' else '' end strikei,
			case When a.active = 0 then '</strike>' else '' end strikef,
			case when a.username <> 'admin' then case when ISNULL(d.ct) then '' else 'style=\"visibility: hidden; display: none;\"' end else 'style=\"visibility: hidden; display: none;\"' end dispDel
		FROM $schema.usuarios a
            left outer join (select distinct * from $schema.usuarios_equipo) b on a.username = b.username
			left outer join (   select 0 Equipo_ID, '" . $Config->liga . "' Equipo_FULLDESC
        						UNION
        						select a.Equipo_ID, CONCAT(c.Categoria_Desc , ' - ' , a.Equipo_FULLDESC) Equipo_FULLDESC 
            						from (select Equipo_ID, Equipo_FULLDESC, Torneo_ID from $schema.Equipos where Torneo_ID = $Season) a
                        				join (	select Equipo_ID, Fuerza, MAX(Torneo_ID) Torneo_ID 
                        						from $schema.Equipos a 
												where a.Torneo_ID = $Season
                        						group by Equipo_ID) b on a.Equipo_ID = b.Equipo_ID and a.Torneo_ID = b.Torneo_ID
										join $schema.Categorias c on b.Fuerza = c.Categoria_ID
									where a.Torneo_ID = $Season and c.Torneo_ID = $Season) c on c.Equipo_ID = b.Equipo_ID
			left outer join (	SELECT count(*) ct, User_ID FROM $schema.Control_Table
								group by User_ID) d on a.username = d.User_ID
		GROUP BY id_user
		order by rank, username desc;";
	$result2 = $Config->query($sql2);

	$count = 0;
	if ($result2->num_rows > 0) {
		// output data of each row
		while($row2 = $result2->fetch_assoc()) {
			if (($count % 2) == 1){
				$htmlUsers .= "<tr>";
			}else{
				$htmlUsers .= "<tr class='alt'>";
			}
			$htmlUsers .= '<td scope="row">
							<div class="d-flex px-0 py-1">
								<div style="width: 5%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold"></span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap"><img src="imagenes/eliminar.png" height="15" width="15" onClick="userManagementDeleteUser(' . $row2["id_user"] . ')" ' . $row2["dispDel"] . '></span></div></div></div>
									<div style="width: 20%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['802'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . $row2["username"] . $row2["strikef"]  . '</span></div></div></div>
									<div style="width: 25%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['803'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . $row2["name"] . $row2["strikef"]  . '</span></div></div></div>
									<div style="width: 25%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['804'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . $row2["ApellidoP"] . $row2["strikef"]  . '</span></div></div></div>
									<div style="width: 25%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['805'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . $row2["ApellidoM"] . $row2["strikef"]  . '</span></div></div></div>
								</div>
							</div>
							<div class="d-flex px-0 py-1">
								<div style="width: 35%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['807'] . '</span></p>';
			if(strlen($row2["email"]) > 15){
					$htmlUsers .= '<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . substr($row2["email"],0,12) . '...' . $row2["strikef"]  . '</span></div></div></div>';
			}else{
				$htmlUsers .= '<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . $row2["email"] . $row2["strikef"]  . '</span></div></div></div>';
			}
			$htmlUsers .= '			<div style="width: 35%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['808'] . '</span></p>';
			$htmlUsers .= '<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . $row2["Equipo_FULLDESC"] . $row2["strikef"]  . '</span></div></div></div>';
			$htmlUsers .= '			<div style="width: 15%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['837'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . $row2["active"] . $row2["strikef"]  . '</span></div></div></div>
									<div style="width: 15%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['809'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal"><img onClick="userManagementShowEdit(' . $row2["id_user"] . ')" src="./imagenes/edit.png" width="20" height="20" alt=""/></span></div></div></div>
								</div>
							</div>
						   </td>
						</tr>';
			$count++;
		}
	}
	$htmlUsers .= '</tbody>';
	$htmlUsers .= '</table>';
	$htmlUsers .= '</div>';
	$htmlUsers .= '</div>';
	$htmlUsers .= '</div>';
?>