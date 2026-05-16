<?php
$htmlColor .= '<div class="d-block d-xs-block d-md-block d-lg-block d-xl-block">
					<div class="row">
						<div class="col-8 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<h2>' . $lang['80'] . '</h2>
						</div>
						<div class="col-4 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<button type="button" class="btn btn-primary" onClick="colorsManagementShowAdd();" >' . $lang['0013'] . '</button>
						</div>
					</div>
					<div class="card">
						<div class="table-responsive">
							<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
								<thead class="">
									<th ' . $Config->ShowIDColumn . ' scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['82'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['83'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['84'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['86'] . '</span></th>
								</thead>
								<tbody>';
$count = 1;
$sql2 = "	SELECT Color_ID,
				Color_Desc,
				Color_HEX
			FROM $schema.Colores
			order by Color_Desc asc;";
//echo $sql2;
$result2 = $Config->query($sql2);
if ($result2->num_rows > 0) {
	while($row2 = $result2->fetch_assoc()) {
		if (($count % 2) == 1){
			$htmlColor .= "<tr>";
		}else{
			$htmlColor .= "<tr class='alt'>";
		}
		$htmlColor .=	'<td ' . $Config->ShowIDColumn . ' scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Color_ID"]. '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["Color_Desc"]. '</span></td>
						<td scope="row" class="align-middle text-left" style="background-color:' . $row2["Color_HEX"] . '"><span class="text-secondary text-xs font-weight-normal">' . $row2["Color_HEX"]. '</span></td>
						<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal"><img onClick="colorsManagementShowEdit(' . $row2["Color_ID"] . ')" src="./imagenes/edit.png" width="20" height="20" alt=""/></span></td>
					</tr>';
		$count++;
	}
}
$htmlColor .= '</tbody>';
$htmlColor .= '</table>';
$htmlColor .= '</div>';
$htmlColor .= '</div>';
			

$htmlColor .= '</div>';
?>