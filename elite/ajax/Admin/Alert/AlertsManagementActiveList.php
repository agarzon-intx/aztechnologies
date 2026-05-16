<?php
$htmlAlerts .= '<div class="d-none d-xs-none d-md-block d-lg-block d-xl-block">
					<div class="row">
						<div class="col-8 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<h2>' . $lang['400'] . '</h2>
						</div>
						<div class="col-4 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<button type="button" class="btn btn-primary" onClick="alertManagementShowAdd();" >' . $lang['0013'] . '</button>
						</div>
					</div>
					<div class="card">
						<div class="table-responsive">
							<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
								<thead class="">
									<th ' . $Config->ShowIDColumn . ' scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['402'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['403'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['404'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['405'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['414'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['417'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['406'] . '</span></th>
								</thead>
								<tbody>';
$count = 1;
$sql2 = "	SELECT Aviso_ID,
				cast(Aviso_Fecha_Inicio as Date) Aviso_Fecha_Inicio,
				cast(Aviso_Fecha_Fin as Date) Aviso_Fecha_Fin,
				Aviso_Contenido,
				Aviso_Titulo,
				Aviso_Tipo,
				case 
					when Aviso_Estatus = 1 then '" . $lang['415'] . "'
					when Aviso_Estatus = 0 then '" . $lang['416'] . "'
				end	Aviso_Estatus,
				case 
					when Aviso_Mostrar = 1 then '" . $lang['0011'] . "'
					when Aviso_Mostrar = 0 then '" . $lang['0012'] . "'
				end	Aviso_Mostrar
			FROM $schema.Avisos
			order by Aviso_Estatus desc, Aviso_Fecha_Fin desc;";
//echo $sql2;
$result2 = $Config->query($sql2);
if ($result2->num_rows > 0) {
	while($row2 = $result2->fetch_assoc()) {
		if (($count % 2) == 1){
			$htmlAlerts .= "<tr>";
		}else{
			$htmlAlerts .= "<tr class='alt'>";
		}
		$htmlAlerts .=	'<td ' . $Config->ShowIDColumn . ' scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Aviso_ID"]. '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Aviso_Fecha_Inicio"]. '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Aviso_Fecha_Fin"]. '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Aviso_Titulo"]. '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Aviso_Estatus"]. '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Aviso_Mostrar"]. '</span></td>
						<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal"><img onClick="alertManagementShowEdit(' . $row2["Aviso_ID"] . ');" src="./imagenes/edit.png" width="20" height="20" alt=""/></span></td>
					</tr>';
		$count++;
	}
}
$htmlAlerts .= '</tbody>';
$htmlAlerts .= '</table>';
$htmlAlerts .= '</div>';
$htmlAlerts .= '</div>';
			

$htmlAlerts .= '</div>';

$htmlAlerts .= '<div class="d-block d-sm-block d-md-none d-lg-none d-xl-none">
					<div class="card">
						<div class="table-responsive">
							<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
								<tbody>';
								
								
$count = 1;
$sql2 = "SET @rank:=0;";
$Config->query($sql2);
$sql2 = "	SELECT Aviso_ID,
				cast(Aviso_Fecha_Inicio as Date) Aviso_Fecha_Inicio,
				cast(Aviso_Fecha_Fin as Date) Aviso_Fecha_Fin,
				Aviso_Contenido,
				Aviso_Titulo,
				Aviso_Tipo,
				case 
					when Aviso_Estatus = 1 then '" . $lang['415'] . "'
					when Aviso_Estatus = 0 then '" . $lang['416'] . "'
				end	Aviso_Estatus
			FROM $schema.Avisos
			order by Aviso_Estatus desc, Aviso_Fecha_Fin desc;";
//echo $sql2;
$result2 = $Config->query($sql2);
if ($result2->num_rows > 0) {
	// output data of each row
	while($row2 = $result2->fetch_assoc()) {
		if (($count % 2) == 1){
			$htmlAlerts .= "<tr>";
		}else{
			$htmlAlerts .= "<tr class='alt'>";
		}
		$htmlAlerts .= '<td scope="row" style="padding-top: 0px; padding-bottom: 0px;">
							<div class="d-flex px-0 py-0">
								<div style="width: 100%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['405'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Aviso_Titulo"]  . '</span></div></div>
								</div>
							</div>
							<div class="d-flex px-0 py-0">
								<div style="width: 5%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span ' . $Config->ShowIDColumn . ' class="text-secondary text-xs font-weight-bold">' . $lang['402'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span ' . $Config->ShowIDColumn . ' class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Aviso_ID"]. '</span></div></div>
								</div>
								<div style="width: 25%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['403'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Aviso_Fecha_Inicio"]. '</span></div></div>
								</div>
								<div style="width: 25%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['404'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Aviso_Fecha_Fin"]. '</span></div></div>
								</div>
								<div style="width: 25%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['414'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["Aviso_Estatus"]  . '</span></div></div>
								</div>
								<div style="width: 20%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['406'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap"><img onClick="alertManagementShowEdit(' . $row2["Aviso_ID"] . ');" src="./imagenes/edit.png" width="20" height="20" alt=""/></span></div></div>
								</div>
							</div>
						</td>
					</tr>';
		$count++;
	}
}

$htmlAlerts .= '			<tr>
								<td scope="row" style="padding-top: 0px; padding-bottom: 0px;">
									<div class="d-flex px-0 py-0">
										<div style="width: 45%;text-align: center;padding-top: 7px;">
											<span class="text-secondary text-xs font-weight-normal">
												<button type="button" class="btn btn-primary" onClick="playersManagementAdminShowCreate(' . $Category . ',' . $Team . ');" >' . $lang['0013'] . '</button>
											</span>
										</div>
										<div style="width: 55%;text-align: center;padding-top: 7px; background:#fff38f">
											<span class="text-secondary text-xs font-weight-normal">
												<button type="button" class="btn btn-primary" onClick="playersManagementAdminAddPrintList();">' . $lang['952-1'] . '</button>
											</span>
										</div>
									</div>
								</td>
							</tr>';

$htmlAlerts .= '		</body>
					</table>
				</div>
			</div>
		</div>';
?>
