<?php
$htmlCalendar .= '<div class="d-none d-xs-block d-md-block d-lg-block d-xl-block">
					<div class="row">
						<div class="col-8 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<h2>' . $lang['50-1'] . '</h2>
						</div>
						<div class="col-4 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<button type="button" class="btn btn-primary" onClick="calendarManagementShowAdd();" >' . $lang['0013'] . '</button>
						</div>
					</div>
					<div class="card">
						<div class="table-responsive">
							<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
								<thead class="">
									<th ' . $Config->ShowIDColumn . ' scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['51-1'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['52-1'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['53-1'] . '</span></th>
								</thead>
								<tbody>';
$count = 1;
$sql2 = "	SELECT Calendario_ID,
                    Calendario_DESC 
            FROM $schema.Calendario
			order by 1 asc;";
//echo $sql2;
$result2 = $Config->query($sql2);
if ($result2->num_rows > 0) {
	while($row2 = $result2->fetch_assoc()) {
		if (($count % 2) == 1){
			$htmlCalendar .= "<tr>";
		}else{
			$htmlCalendar .= "<tr class='alt'>";
		}
		$htmlCalendar .=	'<td ' . $Config->ShowIDColumn . ' scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Calendario_ID"]. '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Calendario_DESC"]. '</span></td>
						<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal"><img onClick="calendarManagementShowEdit(' . $row2["Calendario_ID"] . ')" src="./imagenes/edit.png" width="20" height="20" alt=""/></span></td>
					</tr>';
		$count++;
	}
}
$htmlCalendar .= '</tbody>';
$htmlCalendar .= '</table>';
$htmlCalendar .= '</div>';
$htmlCalendar .= '</div>';
			

$htmlCalendar .= '</div>';


$htmlCalendar .= '<div class="d-block d-xs-none d-md-none d-lg-none d-xl-none">
					<div class="row">
						<div class="col-8 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<h2>' . $lang['50'] . '</h2>
						</div>
						<div class="col-4 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<button type="button" class="btn btn-primary" onClick="categoryManagementShowAdd();" >' . $lang['0013'] . '</button>
						</div>
					</div>
					<div class="card">
						<div class="table-responsive">
							<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
								<tbody>';
$count = 1;
$sql2 = "	SELECT Categoria_ID,
					Categoria_Desc,
					Categoria_Orden,
					Edad_Inicial,
					Edad_Final,
					Color
				FROM $schema.Categorias
				order by Categoria_Orden asc;";
//echo $sql2;
$result2 = $Config->query($sql2);
if ($result2->num_rows > 0) {
	while($row2 = $result2->fetch_assoc()) {
		if (($count % 2) == 1){
			$htmlCalendar .= "<tr>";
		}else{
			$htmlCalendar .= "<tr class='alt'>";
		}
		$htmlCalendar .=	'<td scope="row" style="padding-top: 0px; padding-bottom: 0px;">
								<div class="d-flex px-0 py-0">
									<div ' . $Config->ShowIDColumn . ' style="width: 30%;text-align: center;padding-top: 0px;">
										<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold text-wrap">' . $lang['51'] . '</span></p>
										<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Categoria_ID"] . '</span></div></div>
									</div>
									<div style="width: 50%;text-align: center;padding-top: 0px;">
										<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold text-wrap">' . $lang['52'] . '</span></p>
										<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Categoria_Desc"] . '</span></div></div>
									</div>
									<div style="width: 20%;text-align: center;padding-top: 0px;">
										<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold text-wrap">' . $lang['53'] . '</span></p>
										<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Categoria_Orden"]  . '</span></div></div>
									</div>
								</div>
								<div class="d-flex px-0 py-0">
									<div style="width: 27%;text-align: center;padding-top: 0px;">
										<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold text-wrap">' . $lang['54'] . '</span></p>
										<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Edad_Inicial"]  . '</span></div></div>
									</div>
									<div style="width: 38%;text-align: center;padding-top: 0px;">
										<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold text-wrap">' . $lang['55'] . '</span></p>
										<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Edad_Final"]  . '</span></div></div>
									</div>
									<div style="width: 25%;text-align: center;padding-top: 0px;">
										<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold text-wrap">' . $lang['56'] . '</span></p>
										<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center; background-color:' . $row2["Color"] . '; height: 18px;"><span class="text-secondary text-xs font-weight-normal text-wrap"></span></div></div>
									</div>
									<div style="width: 20%;text-align: center;padding-top: 0px;">
										<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold text-wrap">' . $lang['57'] . '</span></p>
										<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap"><img onClick="categoryManagementShowEdit(' . $row2["Torneo_ID"] . ')" src="./imagenes/edit.png" width="20" height="20" alt=""/></span></div></div>
									</div>
								</div>
							</td>
						</tr>';
		$count++;
	}
}
$htmlCalendar .= '</tbody>';
$htmlCalendar .= '</table>';
$htmlCalendar .= '</div>';
$htmlCalendar .= '</div>';
			

$htmlCalendar .= '</div>';
?>