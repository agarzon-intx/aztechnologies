<?php
	$fecha = new DateTime();
$sql2Institutions = "SELECT a.*, concat('I-', a.Torneo_ID,'-', a.Institucion_ID) newLogo
			FROM $schema.Instituciones a
			WHERE a.Torneo_ID = $Season
				and IFNULL(a.Activo, 0) = 1
				$institutionListFilterSql
			order by Institucion_DESC asc;";

$htmlInstitutions .= '<div class="d-none d-sm-none d-md-block d-lg-block d-xl-block">
					<div class="row">
						<div class="col-4 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<button type="button" class="btn btn-primary" onClick="institutionManagementShowAdd();" >' . $lang['0013'] . '</button>
						</div>
					</div>
					<div class="card">
						<div class="table-responsive">
							<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
								<thead class="">
									<th ' . $Config->ShowIDColumn . ' scope="col" class="text-left text-uppercase text-secondary text-xs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['inst002'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['inst003'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['inst004'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['inst005'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['inst006'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['inst007'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['427'] . '</span></th>
								</thead>
								<tbody>';
$count = 1;
$result2 = $Config->query($sql2Institutions);
if ($result2 && $result2->num_rows > 0) {
	while($row2 = $result2->fetch_assoc()) {
		$institutionStatus = ((int) $row2["Activo"] === 1) ? $lang['523'] : $lang['416'];
		if (($count % 2) == 1){
			$htmlInstitutions .= "<tr>";
		}else{
			$htmlInstitutions .= "<tr class='alt'>";
		}
		$htmlInstitutions .=	'<td ' . $Config->ShowIDColumn . ' scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Institucion_ID"]. '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal"><div class="" style="height: 30px;width: 30px;"><img src="imagenes/' . $row2["newLogo"] . '.png?tmp=' . $fecha->getTimestamp() . '" width="30" height="30" alt=""></div></span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Institucion_DESC"]. '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $institutionStatus . '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Institucion_FULLDESC"]. '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Institucion_DESC5"]. '</span></td>
						<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal"><img onClick="institutionManagementShowEdit(' . $row2["Institucion_ID"] . ');" src="./imagenes/edit.png" width="20" height="20" alt=""/></span></td>
					</tr>';
		$count++;
	}
}
$htmlInstitutions .= '</tbody>';
$htmlInstitutions .= '</table>';
$htmlInstitutions .= '</div>';
$htmlInstitutions .= '</div>';
$htmlInstitutions .= '</div>';

$htmlInstitutions .= '<div class="d-block d-sm-block d-md-none d-lg-none d-xl-none">
					<div class="row g-2 mb-2">
						<div class="col-12">
							<button type="button" class="btn btn-primary" onClick="institutionManagementShowAdd();" >' . $lang['0013'] . '</button>
						</div>
					</div>
					<div class="card">
						<div class="table-responsive" style="overflow-x: visible;">
							<table class=" table align-items-center mb-0" style="border-color: #136aeb; table-layout: fixed; width: 100%;">
								<tbody>';
$count = 1;
$result2 = $Config->query($sql2Institutions);
if ($result2 && $result2->num_rows > 0) {
	while($row2 = $result2->fetch_assoc()) {
		if (($count % 2) == 1){
			$htmlInstitutions .= "<tr>";
		}else{
			$htmlInstitutions .= "<tr class='alt'>";
		}
		$htmlInstitutions .= '<td scope="row" style="padding-top: 0.35rem; padding-bottom: 0.35rem;">
							<div class="d-flex flex-wrap px-0 py-0 w-100" style="min-width:0;">
								<div ' . $Config->ShowIDColumn . ' style="flex: 0 0 18%; max-width:18%; min-width:0; text-align: center; padding-top: 0px;">
									<p class="text-wrap" style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['inst002'] . '</span></p>
									<div class="lh-sm"><span class="text-secondary text-xs font-weight-normal text-wrap d-inline-block" style="max-width:100%;">' . $row2["Institucion_ID"] . '</span></div>
								</div>
								<div style="flex: 0 0 16%; max-width:16%; min-width:0; text-align: center; padding-top: 0px;">
									<p class="text-wrap" style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['inst003'] . '</span></p>
									<div class="d-flex justify-content-center"><div style="height: 30px;width: 30px;"><img src="imagenes/' . $row2["newLogo"] . '.png?tmp=' . $fecha->getTimestamp() . '" width="30" height="30" alt=""></div></div>
								</div>
								<div style="flex: 1 1 0; min-width:0; text-align: center; padding-top: 0px;">
									<p class="text-wrap" style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['inst004'] . '</span></p>
									<div class="lh-sm"><span class="text-secondary text-xs font-weight-normal text-wrap d-inline-block" style="max-width:100%;">' . $row2["Institucion_DESC"] . '</span></div>
								</div>
								<div style="flex: 0 0 14%; max-width:14%; min-width:0; text-align: center; padding-top: 0px;">
									<p class="text-wrap" style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['inst005'] . '</span></p>
									<div class="lh-sm"><span class="text-secondary text-xs font-weight-normal text-wrap d-inline-block" style="max-width:100%;">' . $institutionStatus . '</span></div>
								</div>
								<div style="flex: 0 0 14%; max-width:14%; min-width:0; text-align: center; padding-top: 0px;">
									<p style="margin-bottom: 0rem !important; padding-top: 0.5rem;"><span class="text-secondary text-xs font-weight-bold">&nbsp;</span></p>
									<div class="d-flex justify-content-center align-items-center"><img onClick="institutionManagementShowEdit(' . $row2["Institucion_ID"] . ');" src="./imagenes/edit.png" width="20" height="20" alt=""/></div>
								</div>
							</div>
							<div class="d-flex flex-wrap px-0 py-0 w-100" style="min-width:0; margin-top: 0.25rem;">
								<div style="flex: 1 1 50%; min-width:0; text-align: center; padding-top: 0px;">
									<p class="text-wrap" style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['inst006'] . '</span></p>
									<div class="lh-sm"><span class="text-secondary text-xs font-weight-normal text-wrap d-inline-block" style="max-width:100%;">' . $row2["Institucion_FULLDESC"] . '</span></div>
								</div>
								<div style="flex: 1 1 50%; min-width:0; text-align: center; padding-top: 0px;">
									<p class="text-wrap" style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['inst007'] . '</span></p>
									<div class="lh-sm"><span class="text-secondary text-xs font-weight-normal text-wrap d-inline-block" style="max-width:100%;">' . $row2["Institucion_DESC5"] . '</span></div>
								</div>
							</div>
						</td>
					</tr>';
		$count++;
	}
}
$htmlInstitutions .= '</tbody>';
$htmlInstitutions .= '</table>';
$htmlInstitutions .= '</div>';
$htmlInstitutions .= '</div>';
$htmlInstitutions .= '</div>';
?>
