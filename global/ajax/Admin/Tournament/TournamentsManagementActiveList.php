<?php
$htmlTournament .= '<div class="d-none d-xs-block d-md-block d-lg-block d-xl-block">
					<div class="row">
						<div class="col-8 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<h2>' . $lang['750'] . '</h2>
						</div>
						<div class="col-4 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<button type="button" class="btn btn-primary" onClick="tournamentsManagementShowAdd();" >' . $lang['0013'] . '</button>
						</div>
					</div>
					<div class="card">
						<div class="table-responsive">
							<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
								<thead class="">
									<th ' . $Config->ShowIDColumn . ' scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['751'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['752'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['753'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['754'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['762'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['763'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['755'] . '</span></th>
								</thead>
								<tbody>';
$count = 1;
$sql2 = "	SELECT Torneos.Torneo_ID,
				Torneos.Torneo_Desc,
				case when Torneos.Actual = 'S' then '" . $lang['0011'] . "' else '" . $lang['0012'] . "' end Actual,
				case when Torneos.Inscripciones = 1 then '" . $lang['0011'] . "' else '" . $lang['0012'] . "' end Inscripciones,
				case when Torneos.TodosVsTodos = 1 then '" . $lang['0011'] . "' else '" . $lang['0012'] . "' end TodosVsTodos,
				Jornadas
			FROM $schema.Torneos
			ORDER BY Torneo_ID desc;";
//echo $sql2;
$result2 = $Config->query($sql2);
if ($result2->num_rows > 0) {
	while($row2 = $result2->fetch_assoc()) {
		if (($count % 2) == 1){
			$htmlTournament .= "<tr>";
		}else{
			$htmlTournament .= "<tr class='alt'>";
		}
		$htmlTournament .=	'<td ' . $Config->ShowIDColumn . ' scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Torneo_ID"]. '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Torneo_Desc"]. '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Actual"]. '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Inscripciones"]. '</span></td>
						<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["TodosVsTodos"]. '</span></td>
						<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["Jornadas"]. '</span></td>
						<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal"><img onClick="tournamentsManagementShowEdit(' . $row2["Torneo_ID"] . ')" src="./imagenes/edit.png" width="20" height="20" alt=""/></span></td>
					</tr>';
		$count++;
	}
}
$htmlTournament .= '</tbody>';
$htmlTournament .= '</table>';
$htmlTournament .= '</div>';
$htmlTournament .= '</div>';
			

$htmlTournament .= '</div>';


$htmlTournament .= '<div class="d-block d-xs-none d-md-none d-lg-none d-xl-none">
					<div class="row">
						<div class="col-8 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<h2>' . $lang['750'] . '</h2>
						</div>
						<div class="col-4 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<button type="button" class="btn btn-primary" onClick="tournamentsManagementShowAdd();" >' . $lang['0013'] . '</button>
						</div>
					</div>
					<div class="card">
						<div class="table-responsive">
							<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
								<tbody>';
$count = 1;
$sql2 = "	SELECT Torneos.Torneo_ID,
				Torneos.Torneo_Desc,
				case when Torneos.Actual = 'S' then '" . $lang['0011'] . "' else '" . $lang['0012'] . "' end Actual,
				case when Torneos.Inscripciones = 1 then '" . $lang['0011'] . "' else '" . $lang['0012'] . "' end Inscripciones,
				case when Torneos.TodosVsTodos = 1 then '" . $lang['0011'] . "' else '" . $lang['0012'] . "' end TodosVsTodos,
				Jornadas
			FROM $schema.Torneos
			ORDER BY Torneo_ID desc;";
//echo $sql2;
$result2 = $Config->query($sql2);
if ($result2->num_rows > 0) {
	while($row2 = $result2->fetch_assoc()) {
		if (($count % 2) == 1){
			$htmlTournament .= "<tr>";
		}else{
			$htmlTournament .= "<tr class='alt'>";
		}
		$htmlTournament .=	'<td scope="row" style="padding-top: 0px; padding-bottom: 0px;">
								<div class="d-flex px-0 py-0">
									<div ' . $Config->ShowIDColumn . ' style="width: 20%;text-align: center;padding-top: 0px;">
										<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold text-wrap">' . $lang['751'] . '</span></p>
										<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Torneo_ID"] . '</span></div></div>
									</div>
									<div style="width: 60%;text-align: center;padding-top: 0px;">
										<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold text-wrap">' . $lang['752'] . '</span></p>
										<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Torneo_Desc"] . '</span></div></div>
									</div>
									<div style="width: 20%;text-align: center;padding-top: 0px;">
										<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold text-wrap">' . $lang['753'] . '</span></p>
										<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Actual"]  . '</span></div></div>
									</div>
								</div>
								<div class="d-flex px-0 py-0">
									<div style="width: 27%;text-align: center;padding-top: 0px;">
										<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold text-wrap">' . $lang['754'] . '</span></p>
										<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Inscripciones"]  . '</span></div></div>
									</div>
									<div style="width: 38%;text-align: center;padding-top: 0px;">
										<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold text-wrap">' . $lang['762-1'] . '</span></p>
										<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["TodosVsTodos"]  . '</span></div></div>
									</div>
									<div style="width: 25%;text-align: center;padding-top: 0px;">
										<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold text-wrap">' . $lang['763'] . '</span></p>
										<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Jornadas"]  . '</span></div></div>
									</div>
									<div style="width: 20%;text-align: center;padding-top: 0px;">
										<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold text-wrap">' . $lang['755'] . '</span></p>
										<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap"><img onClick="tournamentsManagementShowEdit(' . $row2["Torneo_ID"] . ')" src="./imagenes/edit.png" width="20" height="20" alt=""/></span></div></div>
									</div>
								</div>
							</td>
						</tr>';
		$count++;
	}
}
$htmlTournament .= '</tbody>';
$htmlTournament .= '</table>';
$htmlTournament .= '</div>';
$htmlTournament .= '</div>';
			

$htmlTournament .= '</div>';
?>