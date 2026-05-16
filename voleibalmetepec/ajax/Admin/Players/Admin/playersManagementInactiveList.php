<?php
$htmlPlayer .= '<div class="d-none d-sm-none d-md-block d-lg-block d-xl-block">
					<div class="card">
						<div class="table-responsive">
							<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
								<thead class="">
									<th scope="col"  hidden="true" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;"></span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;"></span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;">' . $lang['906'] . '</span></th>
									<th scope="col" class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;">' . $lang['907'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;">#</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;">' . $lang['909'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;">' . $lang['910'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;">' . $lang['912'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;">' . $lang['914'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;">' . $lang['922'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.2rem;"></span></th>
								</thead>
								<tbody>';
								
								
$count = 1;
$sql2 = "SET @rank:=0;";
$Config->query($sql2);
$sql2 = "	SELECT distinct Jugador_ID,
				Numero,
				Clave,
				Apodo,
				Nombre,
				Apellido_P,
				Apellido_M,
				Fecha_Nacimiento,
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
					Sexo,
					case when FechaValidacionCurp is null then '" . $lang["941"] . "' else '" . $lang["940"] . "' end CurpValida,
					case when ISNULL(c.Color_HEX) then '' ELSE concat('background: ', c.Color_HEX, ';') END Color_HEX,
					year(now())-year(a.Fecha_Nacimiento) Edad, 
					CASE WHEN f.Categoria_ID <> ifnull(d.Categoria_ID,-1) and year(now())-year(a.Fecha_Nacimiento) > f.Edad_Final 
							THEN '<strike>' ELSE '' END strikei, 
					CASE WHEN f.Categoria_ID <> ifnull(d.Categoria_ID,-1) and year(now())-year(a.Fecha_Nacimiento) > f.Edad_Final 
							THEN '</strike>' ELSE '' END strikef 
			FROM $schema.Jugadores a  
				LEFT OUTER JOIN $schema.Range_Age b on b.Range_Active = 1 and b.Range_Id <> 1 and year(now())-year(a.Fecha_Nacimiento) between b.Range_Start and b.Range_End
				LEFT OUTER JOIN $schema.Colores c on b.Range_Color_ID = c.Color_ID
				LEFT OUTER JOIN $schema.Categorias d on year(now())-year(a.Fecha_Nacimiento) between d.Edad_Inicial and d.Edad_Final
				JOIN $schema.Equipos e ON a.Equipo_ID = e.Equipo_ID and e.Torneo_ID = $Season
				JOIN $schema.Categorias f ON e.Fuerza = f.Categoria_ID 
			where a.Equipo_ID = $Team
				and Estatus = 'B' 
			order by Jugador_tipo asc, cast(Numero as decimal) asc, Nombre, Apellido_P;";
//echo $sql2;
$result2 = $Config->query($sql2);
if ($result2->num_rows > 0) {
	// output data of each row
	while($row2 = $result2->fetch_assoc()) {
		if (($count % 2) == 1){
			$htmlPlayer .= "<tr>";
		}else{
			$htmlPlayer .= "<tr class='alt'>";
		}
		$htmlPlayer .= '<td scope="row" class="align-middle text-left" hidden="true"><span class="text-secondary text-xs font-weight-normal">' . $row2["Jugador_ID"] . '</span></td>
						<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $count . $row2["strikef"] . '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"]  . $row2["Apodo"] . $row2["strikef"]  . '</span></td>
						<td scope="row" class="align-middle text-left"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["Nombre"] . ' ' . $row2["Apellido_P"] . ' ' . $row2["Apellido_M"] . $row2["strikef"]  . '</span></td>
						<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"]  . $row2["Numero"] . $row2["strikef"]  . '</span></td>
						<td scope="row" class="align-middle text-center" style="' . $row2["Color_HEX"] . '"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"]  . $row2["Fecha_Nacimiento"] . $row2["strikef"]  . '</span></td>';
		$color = "";
		if($row2["Sexo"] == 0){ 
			$color = "#4BE1DA";
		}else{	
			$color = "#F95693";
		}
		$htmlPlayer .= '<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"]  . $row2["Edad"] . $row2["strikef"]  . '</span></td>
						<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"]  . $row2["Validado"] . $row2["strikef"]  . '</span></td>
						<td scope="row" class="align-middle text-center" style="background: ' . $color . '"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"]  . $row2["SexoT"] . $row2["strikef"]  . '</span></td>';
		if($row2["CurpValida"] === $lang["940"]){ 
			$color = "#00FF00";
		}else{	
			$color = "#FF0000";
		}
		$htmlPlayer .= '<td scope="row" class="align-middle text-center" style="background: ' . $color . '"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"]  . $row2["CurpValida"] . $row2["strikef"]  . '</span></td>
						<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal"><img onClick="playersManagementAdminShowEdit(' . $row2["Jugador_ID"] . ')" src="./imagenes/edit.png" width="20" height="20" alt=""/></span></td>';
		$htmlPlayer .= '</tr>';
		$count++;
	}
}
$htmlPlayer .= '	</table>
					</div>
				</div>
			</div>';
			
			
			
			
			

$htmlPlayer .= '<div class="d-block d-sm-block d-md-none d-lg-none d-xl-none">
					<div class="card">
						<div class="table-responsive">
							<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
								<tbody>';
								
								
$count = 1;
$sql2 = "SET @rank:=0;";
$Config->query($sql2);
$sql2 = "	SELECT distinct Jugador_ID,
				Numero,
				Clave,
				Apodo,
				Nombre,
				Apellido_P,
				Apellido_M,
				Fecha_Nacimiento,
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
					Sexo,
					case when FechaValidacionCurp is null then '" . $lang["941"] . "' else '" . $lang["940"] . "' end CurpValida,
					case when ISNULL(c.Color_HEX) then '' ELSE concat('background: ', c.Color_HEX, ';') END Color_HEX,
					year(now())-year(a.Fecha_Nacimiento) Edad, 
					CASE WHEN f.Categoria_ID <> ifnull(d.Categoria_ID,-1) and year(now())-year(a.Fecha_Nacimiento) > f.Edad_Final 
							THEN '<strike>' ELSE '' END strikei, 
					CASE WHEN f.Categoria_ID <> ifnull(d.Categoria_ID,-1) and year(now())-year(a.Fecha_Nacimiento) > f.Edad_Final 
							THEN '</strike>' ELSE '' END strikef 
			FROM $schema.Jugadores a  
				LEFT OUTER JOIN $schema.Range_Age b on b.Range_Active = 1 and b.Range_Id <> 1 and year(now())-year(a.Fecha_Nacimiento) between b.Range_Start and b.Range_End
				LEFT OUTER JOIN $schema.Colores c on b.Range_Color_ID = c.Color_ID
				LEFT OUTER JOIN $schema.Categorias d on year(now())-year(a.Fecha_Nacimiento) between d.Edad_Inicial and d.Edad_Final
				JOIN $schema.Equipos e ON a.Equipo_ID = e.Equipo_ID and e.Torneo_ID = $Season
				JOIN $schema.Categorias f ON e.Fuerza = f.Categoria_ID 
			where a.Equipo_ID = $Team
				and Estatus = 'B' 
			order by cast(Numero as decimal) asc, Nombre, Apellido_P;";
//echo $sql2;
$result2 = $Config->query($sql2);
if ($result2->num_rows > 0) {
	// output data of each row
	while($row2 = $result2->fetch_assoc()) {
		if (($count % 2) == 1){
			$htmlPlayer .= "<tr>";
		}else{
			$htmlPlayer .= "<tr class='alt'>";
		}
		$htmlPlayer .= '<td scope="row" style="padding-top: 0px; padding-bottom: 0px;">
							<div class="d-flex px-0 py-0">
								<div style="width: 5%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold"></span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"] . $count . $row2["strikef"] . '</span></div></div>
								</div>
								<div style="width: 25%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['906'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . $row2["Apodo"] . $row2["strikef"]  . '</span></div></div>
								</div>
								<div style="width: 40%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['907'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"] . $row2["Nombre"] . ' ' . $row2["Apellido_P"] . ' ' . $row2["Apellido_M"] . $row2["strikef"]  . '</span></div></div>
								</div>
								<div style="width: 10%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">#</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . $row2["Numero"] . $row2["strikef"]  . '</span></div></div>
								</div>
								<div style="width: 20%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['909'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center; ' . $row2["Color_HEX"] . '"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . $row2["Fecha_Nacimiento"] . $row2["strikef"]  . '</span></div></div>
								</div>';
		$color = "";
		if($row2["Sexo"] == 0){ 
			$color = "#4BE1DA";
		}else{	
			$color = "#F95693";
		}
		$htmlPlayer .= '	</div>
							<div class="d-flex px-0 py-0">
								<div style="width: 5%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold"></span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap"></span></div></div>
								</div>
								<div style="width: 20%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['910'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . $row2["Edad"] . $row2["strikef"]  . '</span></div></div>
								</div>
								<div style="width: 20%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['912'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . $row2["Validado"] . $row2["strikef"]  . '</span></div></div>
								</div>
								<div style="width: 20%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold" style="width: 100%;text-align: center;">' . $lang['914'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center; background: ' . $color . '"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . $row2["SexoT"] . $row2["strikef"]  . '</span></div></div>
								</div>';
		if($row2["CurpValida"] === $lang["940"]){ 
			$color = "#00FF00";
		}else{	
			$color = "#FF0000";
		}
		$htmlPlayer .= '		<div style="width: 20%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['922'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center; background: ' . $color . '"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . $row2["CurpValida"] . $row2["strikef"]  . '</span></div></div>
								</div>
								<div style="width: 15%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important; padding-top: 23px;"><span class="text-secondary text-xs font-weight-bold"></span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal"><img onClick="playersManagementAdminShowEdit(' . $row2["Jugador_ID"] . ')" src="./imagenes/edit.png" width="20" height="20" alt=""/></span></div></div>
								</div>
							</div>
						</td>
					</tr>';
		$count++;
	}
}

$htmlPlayer .= '		</thead>
					</table>
				</div>
			</div>
		</div>';
?>