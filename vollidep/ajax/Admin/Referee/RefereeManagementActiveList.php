<?php

$htmlReferee .= '<div class="d-block d-xs-block d-md-block d-lg-block d-xl-block">
					<div class="row">
						<div class="col-8 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<h2>' .  $lang['10700'] . '</h2>
						</div>
						<div class="col-4 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<button type="button" class="btn btn-primary" onClick="refereeManagementShowAdd();" >' . $lang['0013'] . '</button>
						</div>
					</div>
					<div class="card">
						<div class="table-responsive">
							<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
								<thead class="">
									<th ' . $Config->ShowIDColumn . ' scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['10701'] . '</span></th>
    								<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;">' . $lang['10707'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;">' . $lang['10707-1'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;">' . $lang['10709'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;">' . $lang['10710'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;">' . $lang['10714'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;">' . $lang['10722'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;">' . $lang['10725'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;">' . $lang['10712'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;"></span></th>
									

								</thead>
								<tbody>';
								
$count = 1;
$sql2 = "	SELECT  Arbitro_ID,
    Nombre,
    Apellido_P,
    Apellido_M,
    Fecha_Nacimiento,
    CURP,
    Case when Estatus = 'A' then '" . $lang['927'] . "'
		when Estatus = 'B' then '" . $lang['928'] . "'
		when Estatus = 'S' then '" . $lang['929'] . "'
		end Estatus,
	case when Validado = 0 then '" . $lang["941"] . "'
	     when Validado = 1 then '" . $lang["940"] . "'
		 end Validado,
	case when Sexo = 0 then '" . $lang["942"] . "'
		 when Sexo = 1 then '" . $lang["943"] . "'
		 end SexoT,
	year(now())-year(Fecha_Nacimiento) Edad,
	Sexo,
    Telefono,
    Correo,
    Apodo,
    Identificacion,
    Foto,
    Sexo,
    Historial,
    Cursos,
    Comentarios
FROM $schema.Arbitro
Order by Arbitro_ID;";
//echo $sql2;
$result2 = $Config->query($sql2);
if ($result2->num_rows > 0) {
	while($row2 = $result2->fetch_assoc()) {
		if (($count % 2) == 1){
			$htmlReferee .= "<tr>";
		}else{
			$htmlReferee .= "<tr class='alt'>";
		}
		$htmlReferee .=	'<td ' . $Config->ShowIDColumn . ' scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Arbitro_ID"]. '</span></td>
						<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["Nombre"]. ' ' . $row2["Apellido_P"]. ' ' . $row2["Apellido_M"]. '</span></td>
						<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["Apodo"]. '</span></td>
						<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["Fecha_Nacimiento"]. '</span></td>
						<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["Edad"]. '</span></td>   <!--EDAD-->
						<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["SexoT"]. '</span></td>
						<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["CURP"]. '</span></td>
						<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["Telefono"]. '</span></td>
						<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["Validado"]. '</span></td>
						<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal"><img onClick="refereeManagementShowEdit(' . $row2["Arbitro_ID"]. ');" src="./imagenes/edit.png" width="20" height="20" alt=""/></span></td>
					</tr>';
		$count++;
	}
}
$htmlReferee .= '</tbody>';
$htmlReferee .= '</table>';
$htmlReferee .= '</div>';
$htmlReferee .= '</div>';
			

$htmlReferee .= '</div>';
?>