<?php
	$fecha = new DateTime();
$htmlTeams .= '<div class="d-block d-xs-block d-md-block d-lg-block d-xl-block">
					<div class="row">
						<div class="col-8 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<h2>' . $lang['514'] . '</h2>
						</div>
						<div class="col-4 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<button type="button" class="btn btn-primary" onClick="teamManagementShowAdd(' . $Category . ');" >' . $lang['0013'] . '</button>
						</div>
					</div>
					<div class="card">
						<div class="table-responsive">
							<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
								<thead class="">
									<th ' . $Config->ShowIDColumn . ' scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['515'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['516'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['517'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['518'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['519'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['520'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['521'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['427'] . '</span></th>
								</thead>
								<tbody>';
$count = 1;
$sql2 = "	SELECT a.*, concat(a.Torneo_ID,'-', a.Equipo_ID) newLogo, c.Campo_DESC, d.categoria_desc
			FROM $schema.Equipos a 
				left outer join $schema.Campos c on a.Campo_ID = c.Campo_ID
				left outer join $schema.Categorias d on a.fuerza = d.Categoria_ID and d.Torneo_ID = $Season
			WHERE a.Torneo_ID = $Season and Fuerza = $Category
			order by Activo desc, Equipo_DESC asc;";
//echo $sql2;
$result2 = $Config->query($sql2);
if ($result2->num_rows > 0) {
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
?>