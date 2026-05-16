<?php
$htmlWeek .= '<div class="d-none d-xs-block d-md-block d-lg-block d-xl-block">
					<div class="row">
						<div class="col-8 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<h2>' . $lang['860'] . '</h2>
						</div>
						<div class="col-4 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<button type="button" class="btn btn-primary" onClick="weekManagementShowAdd(' . $Calendar . ');" >' . $lang['0013'] . '</button>
						</div>
					</div>
					<div class="card">
						<div class="table-responsive">
							<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
								<thead class="">
									<th ' . $Config->ShowIDColumn . ' scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['861'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['862'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['863'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['864'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['865'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['866'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['867'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['873'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['868'] . '</span></th>
								</thead>
								<tbody>';
$count = 1;
$sql2 = "	SELECT Jornada_ID,
				DATE_FORMAT(Fecha, '%m / %d / %Y') Fecha,
				DATE_FORMAT(Fecha_Inicio, '%m / %d / %Y') Fecha_Inicio,
				DATE_FORMAT(Fecha_Fin, '%m / %d / %Y') Fecha_Fin,
				Torneo_ID,
				Jornada_Desc,
				Jornada_DescCorta,
				Jornada_Orden,
				case when Jornada_Type = 1 then '" . $lang['890'] . "' else case when Jornada_Type = 2 then '" . $lang['891'] . "' else case when Jornada_Type = 3 then '" . $lang['892'] . "' else '' end end end Jornada_Type
			FROM $schema.Jornada
			Where Torneo_ID = $Season and Calendario_ID = $Calendar
			Order by Jornada_Orden asc;";
//echo $sql2;
$result2 = $Config->query($sql2);
if ($result2->num_rows > 0) {
	while($row2 = $result2->fetch_assoc()) {
		if (($count % 2) == 1){
			$htmlWeek .= "<tr>";
		}else{
			$htmlWeek .= "<tr class='alt'>";
		}
		$htmlWeek .=	'<td ' . $Config->ShowIDColumn . ' scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Jornada_ID"]. '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Fecha"]. '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Fecha_Inicio"]. '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Fecha_Fin"]. '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Jornada_DescCorta"]. '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Jornada_Desc"]. '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Jornada_Orden"]. '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Jornada_Type"]. '</span></td>
						<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal"><img onClick="weekManagementShowEdit(' . $row2["Jornada_ID"] . ', ' . $Calendar . ')" src="./imagenes/edit.png" width="20" height="20" alt=""/></span></td>
					</tr>';
		$count++;
	}
}
$htmlWeek .= '</tbody>';
$htmlWeek .= '</table>';
$htmlWeek .= '</div>';
$htmlWeek .= '</div>';
			

$htmlWeek .= '</div>';


$htmlWeek .= '<div class="d-block d-xs-none d-md-none d-lg-none d-xl-none">
					<div class="row">
						<div class="col-8 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<h2>' . $lang['860'] . '</h2>
						</div>
						<div class="col-4 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<button type="button" class="btn btn-primary" onClick="weekManagementShowAdd();" >' . $lang['0013'] . '</button>
						</div>
					</div>
					<div class="card">
						<div class="table-responsive">
							<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
								<tbody>';
$count = 1;
$sql2 = "	SELECT Jornada_ID,
				DATE_FORMAT(Fecha, '%m / %d / %Y') Fecha,
				DATE_FORMAT(Fecha_Inicio, '%m / %d / %Y') Fecha_Inicio,
				DATE_FORMAT(Fecha_Fin, '%m / %d / %Y') Fecha_Fin,
				Torneo_ID,
				Jornada_Desc,
				Jornada_DescCorta,
				Jornada_Orden
			FROM $schema.Jornada
			Where Torneo_ID = $Season 
			Order by Jornada_Orden asc;";
//echo $sql2;
$result2 = $Config->query($sql2);
if ($result2->num_rows > 0) {
	while($row2 = $result2->fetch_assoc()) {
		if (($count % 2) == 1){
			$htmlWeek .= "<tr>";
		}else{
			$htmlWeek .= "<tr class='alt'>";
		}
		$htmlWeek .=	'<td scope="row" style="padding-top: 0px; padding-bottom: 0px;">
								<div class="d-flex px-0 py-0">
									<div ' . $Config->ShowIDColumn . ' style="width: 30%;text-align: center;padding-top: 0px;">
										<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold text-wrap">' . $lang['861'] . '</span></p>
										<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Jornada_ID"] . '</span></div></div>
									</div>
									<div style="width: 50%;text-align: center;padding-top: 0px;">
										<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold text-wrap">' . $lang['862'] . '</span></p>
										<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Fecha"] . '</span></div></div>
									</div>
									<div style="width: 20%;text-align: center;padding-top: 0px;">
										<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold text-wrap">' . $lang['863'] . '</span></p>
										<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Fecha_Inicio"]  . '</span></div></div>
									</div>
									<div style="width: 27%;text-align: center;padding-top: 0px;">
										<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold text-wrap">' . $lang['864'] . '</span></p>
										<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Fecha_Fin"]  . '</span></div></div>
									</div>
								</div>
								<div class="d-flex px-0 py-0">
									<div style="width: 38%;text-align: center;padding-top: 0px;">
										<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold text-wrap">' . $lang['865'] . '</span></p>
										<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Jornada_DescCorta"]  . '</span></div></div>
									</div>
									<div style="width: 25%;text-align: center;padding-top: 0px;">
										<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold text-wrap">' . $lang['866'] . '</span></p>
										<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Jornada_Desc"]  . '</span></div></div>
									</div>
									<div style="width: 20%;text-align: center;padding-top: 0px;">
										<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold text-wrap">' . $lang['867'] . '</span></p>
										<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Jornada_Orden"]  . '</span></div></div>
									</div>
									<div style="width: 20%;text-align: center;padding-top: 0px;">
										<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold text-wrap">' . $lang['868'] . '</span></p>
										<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap"><img onClick="weekManagementShowEdit(' . $row2["Jornada_ID"] . ')" src="./imagenes/edit.png" width="20" height="20" alt=""/></span></div></div>
									</div>
								</div>
							</td>
						</tr>';
		$count++;
	}
}
$htmlWeek .= '</tbody>';
$htmlWeek .= '</table>';
$htmlWeek .= '</div>';
$htmlWeek .= '</div>';
			

$htmlWeek .= '</div>';
?>