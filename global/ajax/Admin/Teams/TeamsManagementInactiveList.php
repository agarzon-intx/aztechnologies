<?php
	$fecha = new DateTime();
$sql2TeamsInactive = "	SELECT a.*, concat(a.Torneo_ID,'-', a.Equipo_ID) newLogo, c.Campo_DESC, d.categoria_desc
			FROM $schema.Equipos a 
				left outer join $schema.Campos c on a.Campo_ID = c.Campo_ID
				left outer join $schema.Categorias d on a.fuerza = d.Categoria_ID and d.Torneo_ID = $Season
			WHERE a.Torneo_ID = $Season and Fuerza = $Category
				and IFNULL(a.Activo, 0) <> 1
			order by Equipo_DESC asc;";

$htmlTeams .= '<div class="d-none d-sm-none d-md-block d-lg-block d-xl-block">
					<div class="row">
						<div class="col-4 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<button type="button" class="btn btn-primary" onClick="teamManagementShowAdd(' . $Category . ');" >' . $lang['0013'] . '</button>
						</div>
					</div>
					<div class="card">
						<div class="table-responsive">
							<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
								<thead class="">
									<th ' . $Config->ShowIDColumn . ' scope="col" class="text-left text-uppercase text-secondary text-xs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['515'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['516'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['517'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['518'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['519'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['520'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['521'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['427'] . '</span></th>
								</thead>
								<tbody>';
$count = 1;
$result2 = $Config->query($sql2TeamsInactive);
if ($result2 && $result2->num_rows > 0) {
	while($row2 = $result2->fetch_assoc()) {
		if (($count % 2) == 1){
			$htmlTeams .= "<tr>";
		}else{
			$htmlTeams .= "<tr class='alt'>";
		}
		$htmlTeams .=	'<td ' . $Config->ShowIDColumn . ' scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Equipo_ID"]. '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal"><div class="" style="height: 30px;width: 30px;"><img src="imagenes/' . $row2["newLogo"] . '.png?tmp=' . $fecha->getTimestamp() . '" width="30" height="30" alt=""></div></span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Equipo_DESC"]. '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Activo"]. '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["categoria_desc"]. '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Equipo_FULLDESC"]. '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Campo_DESC"]. '</span></td>
						<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal"><img onClick="teamManagementShowEdit(' . $row2["Equipo_ID"] . ', ' . $Category . ');" src="./imagenes/edit.png" width="20" height="20" alt=""/></span></td>
					</tr>';
		$count++;
	}
}
$htmlTeams .= '</tbody>';
$htmlTeams .= '</table>';
$htmlTeams .= '</div>';
$htmlTeams .= '</div>';
$htmlTeams .= '</div>';

$htmlTeams .= '<div class="d-block d-sm-block d-md-none d-lg-none d-xl-none">
					<div class="row g-2 mb-2">
						<div class="col-12">
							<button type="button" class="btn btn-primary" onClick="teamManagementShowAdd(' . $Category . ');" >' . $lang['0013'] . '</button>
						</div>
					</div>
					<div class="card">
						<div class="table-responsive" style="overflow-x: visible;">
							<table class=" table align-items-center mb-0" style="border-color: #136aeb; table-layout: fixed; width: 100%;">
								<tbody>';
$count = 1;
$result2 = $Config->query($sql2TeamsInactive);
if ($result2 && $result2->num_rows > 0) {
	while($row2 = $result2->fetch_assoc()) {
		if (($count % 2) == 1){
			$htmlTeams .= "<tr>";
		}else{
			$htmlTeams .= "<tr class='alt'>";
		}
		$htmlTeams .= '<td scope="row" style="padding-top: 0.35rem; padding-bottom: 0.35rem;">
							<div class="d-flex flex-wrap px-0 py-0 w-100" style="min-width:0;">
								<div ' . $Config->ShowIDColumn . ' style="flex: 0 0 18%; max-width:18%; min-width:0; text-align: center; padding-top: 0px;">
									<p class="text-wrap" style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['515'] . '</span></p>
									<div class="lh-sm"><span class="text-secondary text-xs font-weight-normal text-wrap d-inline-block" style="max-width:100%;">' . $row2["Equipo_ID"] . '</span></div>
								</div>
								<div style="flex: 0 0 16%; max-width:16%; min-width:0; text-align: center; padding-top: 0px;">
									<p class="text-wrap" style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['516'] . '</span></p>
									<div class="d-flex justify-content-center"><div style="height: 30px;width: 30px;"><img src="imagenes/' . $row2["newLogo"] . '.png?tmp=' . $fecha->getTimestamp() . '" width="30" height="30" alt=""></div></div>
								</div>
								<div style="flex: 1 1 0; min-width:0; text-align: center; padding-top: 0px;">
									<p class="text-wrap" style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['517'] . '</span></p>
									<div class="lh-sm"><span class="text-secondary text-xs font-weight-normal text-wrap d-inline-block" style="max-width:100%;">' . $row2["Equipo_DESC"] . '</span></div>
								</div>
								<div style="flex: 0 0 14%; max-width:14%; min-width:0; text-align: center; padding-top: 0px;">
									<p class="text-wrap" style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['518'] . '</span></p>
									<div class="lh-sm"><span class="text-secondary text-xs font-weight-normal text-wrap d-inline-block" style="max-width:100%;">' . $row2["Activo"] . '</span></div>
								</div>
								<div style="flex: 0 0 14%; max-width:14%; min-width:0; text-align: center; padding-top: 0px;">
									<p style="margin-bottom: 0rem !important; padding-top: 0.5rem;"><span class="text-secondary text-xs font-weight-bold">&nbsp;</span></p>
									<div class="d-flex justify-content-center align-items-center"><img onClick="teamManagementShowEdit(' . $row2["Equipo_ID"] . ', ' . $Category . ');" src="./imagenes/edit.png" width="20" height="20" alt=""/></div>
								</div>
							</div>
							<div class="d-flex flex-wrap px-0 py-0 w-100" style="min-width:0; margin-top: 0.25rem;">
								<div style="flex: 1 1 30%; min-width:0; text-align: center; padding-top: 0px;">
									<p class="text-wrap" style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['519'] . '</span></p>
									<div class="lh-sm"><span class="text-secondary text-xs font-weight-normal text-wrap d-inline-block" style="max-width:100%;">' . $row2["categoria_desc"] . '</span></div>
								</div>
								<div style="flex: 1 1 35%; min-width:0; text-align: center; padding-top: 0px;">
									<p class="text-wrap" style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['520'] . '</span></p>
									<div class="lh-sm"><span class="text-secondary text-xs font-weight-normal text-wrap d-inline-block" style="max-width:100%;">' . $row2["Equipo_FULLDESC"] . '</span></div>
								</div>
								<div style="flex: 1 1 30%; min-width:0; text-align: center; padding-top: 0px;">
									<p class="text-wrap" style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['521'] . '</span></p>
									<div class="lh-sm"><span class="text-secondary text-xs font-weight-normal text-wrap d-inline-block" style="max-width:100%;">' . $row2["Campo_DESC"] . '</span></div>
								</div>
							</div>
						</td>
					</tr>';
		$count++;
	}
}
$htmlTeams .= '</tbody>';
$htmlTeams .= '</table>';
$htmlTeams .= '</div>';
$htmlTeams .= '</div>';
$htmlTeams .= '</div>';
?>
