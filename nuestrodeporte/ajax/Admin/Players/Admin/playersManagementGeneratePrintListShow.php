<?php
    session_start();
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

	error_reporting(0);
$__APP_SITE_PATHS_START__ = __DIR__;
$__app_here = __DIR__;
for ($__i = 0, $__prev = null; $__i < 24; $__i++) {
	$__base = ($__i === 0) ? $__app_here : dirname($__app_here, $__i);
	if ($__base === $__prev) {
		break;
	}
	$__prev = $__base;
	$__inc = $__base . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'app_site_paths.inc.php';
	if (is_readable($__inc)) {
		require_once $__inc;
		break;
	}
}
unset($__i, $__prev, $__base, $__inc, $__app_here);

	require("membersite_config.php");
$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('playersManagementGeneratePrintListShow.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');


    $retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
    
    $Season = $_COOKIE[$Config->getAlias() . 'season'];
    $Category = $_COOKIE[$Config->getAlias() . 'category'];
    $Team = $_POST['Team'];

	$htmlPlayer = '';
	$Config->LoadFlags();
    $Config->LoadRegionalSettings();

	$htmlPlayer .= '<div class="d-none d-xs-none d-md-none d-lg-block d-xl-block">
					<div class="row">
						<div class="col-8 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
							<h2>' . $lang['915-1'] . '</h2>
						</div>
						<div class="col-4 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" >
						</div>
					</div>
					<div class="card">
						<div class="table-responsive">
							<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
								<thead class="">
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['953'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['954'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['905'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['906'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['907'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['910'] . '</span></th>
									<th scope="col" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 lh-1" style="width: 105px !important;white-space: normal;padding: 0.75rem 0.5rem;">' . $lang['950'] . '</span></th>
								</thead>
								<tbody>';
	$pList = '';
	if(strlen($_SESSION[$Config->getAlias() . 'printList'])> 0){
		$pList = $_SESSION[$Config->getAlias() . 'printList'];
	}else{
		$pList = -1;
	}
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
						case 
							when month(a.Fecha_Nacimiento) < 8 then 
								year(now())-year(a.Fecha_Nacimiento)+1 
							else 
								year(now())-year(a.Fecha_Nacimiento) 
						end Edad, 
						CASE WHEN f.Categoria_ID <> ifnull(d.Categoria_ID,-1) and case 
																					when month(a.Fecha_Nacimiento) < 8 then 
																						year(now())-year(a.Fecha_Nacimiento)+1 
																					else 
																						year(now())-year(a.Fecha_Nacimiento) 
																					end > f.Edad_Final 
								THEN '' ELSE '' END strikei, 
						CASE WHEN f.Categoria_ID <> ifnull(d.Categoria_ID,-1) and case 
																					when month(a.Fecha_Nacimiento) < 8 then 
																						year(now())-year(a.Fecha_Nacimiento)+1 
																					else 
																						year(now())-year(a.Fecha_Nacimiento) 
																					end > f.Edad_Final 
								THEN '' ELSE '' END strikef,
						f.Categoria_DESC, e.Equipo_DESC
				FROM $schema.Jugadores a  
					LEFT OUTER JOIN $schema.Range_Age b on b.Range_Active = 1 and b.Range_Id <> 1 and case
																			when month(a.Fecha_Nacimiento) < 8 then 
																				year(now())-year(a.Fecha_Nacimiento)+1 
																			else 
																				year(now())-year(a.Fecha_Nacimiento) 
																		end between b.Range_Start and b.Range_End
					LEFT OUTER JOIN $schema.Colores c on b.Range_Color_ID = c.Color_ID
					LEFT OUTER JOIN $schema.Categorias d on case
																when month(a.Fecha_Nacimiento) < 8 then 
																	year(now())-year(a.Fecha_Nacimiento)+1 
																else 
																	year(now())-year(a.Fecha_Nacimiento) 
															end between d.Edad_Inicial and d.Edad_Final and d.Torneo_ID = $Season
					JOIN $schema.Equipos e ON a.Equipo_ID = e.Equipo_ID and e.Torneo_ID = $Season
					JOIN $schema.Categorias f ON e.Fuerza = f.Categoria_ID and f.Torneo_ID = $Season
				where a.Jugador_ID in (" . $pList . ")
				order by f.Categoria_DESC, e.Equipo_DESC, cast(Numero as decimal) asc, Nombre, Apellido_P;";
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
			$htmlPlayer .= '<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["Categoria_DESC"] . $row2["strikef"] . '</span></td>
						<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["Equipo_DESC"] . $row2["strikef"] . '</span></td>
						<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["Numero"] . $row2["strikef"] . '</span></td>
						<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["Apodo"] . $row2["strikef"] . '</span></td>
						<td scope="row" class="align-middle text-center"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["Nombre"] . " " . $row2["Apellido_P"] . " " . $row2["Apellido_M"] . $row2["strikef"] . '</span></td>
						<td scope="row" class="align-middle text-center" style="' . $row2["Color_HEX"] . '"><span class="text-secondary text-xs font-weight-normal">' . $row2["strikei"] . $row2["Edad"] . $row2["strikef"] . '</span></td>
						<td scope="row" class="align-middle text-center" style="background:#fff38f"><span class="text-secondary text-xs font-weight-normal"><input checked type="checkbox" class="playersManagementAdminImprimirUpdate" id="playersManagementAdminImprimirUpdate' . $row2["Jugador_ID"] . '" name="playersManagementAdminImprimirUpdate' . $row2["Jugador_ID"] . '" value="' . $row2["Jugador_ID"] . '"/></span></td>
					</tr>';
			$count++;
		}
	}
$htmlPlayer .= '			<tr>
								<td colspan="2" scope="row" class="align-middle text-center">
									<span class="text-secondary text-xs font-weight-normal">
										<button type="button" class="btn btn-primary" onClick="clearPrintList();" >' . $lang['955'] . '</button></span></td>
                                <td colspan="3" scope="row" class="align-middle text-center">
								    <a hidden id="downloadPlayersIDBtn1" href="pdf/registrosEquipoManual.php?Edad1=0&Edad2=150&Imprimir=1" target="_blank" download ></a>
								    <span class="text-secondary text-xs font-weight-normal">
								        <button type="button" class="btn btn-primary" onClick="$(\'#downloadPlayersIDBtn1\')[0].click();">' . $lang['915'] . '</button>
								    </span>
								</td>
								<td colspan="2" scope="row" class="align-middle text-center" style="background:#fff38f">
									<span class="text-secondary text-xs font-weight-normal">
										<button type="button" class="btn btn-primary" onClick="updatePrintList();">' . $lang['956'] . '</button></span></td>
								</thead>
						</table>
					</div>
				</div>
			</div>';


$htmlPlayer .= '<div class="d-block d-sm-block d-md-block d-lg-none d-xl-none">
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
						case 
							when month(a.Fecha_Nacimiento) < 8 then 
								year(now())-year(a.Fecha_Nacimiento)+1 
							else 
								year(now())-year(a.Fecha_Nacimiento) 
						end Edad, 
						CASE WHEN f.Categoria_ID <> ifnull(d.Categoria_ID,-1) and case 
																					when month(a.Fecha_Nacimiento) < 8 then 
																						year(now())-year(a.Fecha_Nacimiento)+1 
																					else 
																						year(now())-year(a.Fecha_Nacimiento) 
																					end > f.Edad_Final 
								THEN '<strike>' ELSE '' END strikei, 
						CASE WHEN f.Categoria_ID <> ifnull(d.Categoria_ID,-1) and case 
																					when month(a.Fecha_Nacimiento) < 8 then 
																						year(now())-year(a.Fecha_Nacimiento)+1 
																					else 
																						year(now())-year(a.Fecha_Nacimiento) 
																					end > f.Edad_Final 
								THEN '</strike>' ELSE '' END strikef,
						f.Categoria_DESC, e.Equipo_DESC
				FROM $schema.Jugadores a  
					LEFT OUTER JOIN $schema.Range_Age b on b.Range_Active = 1 and b.Range_Id <> 1 and case
																			when month(a.Fecha_Nacimiento) < 8 then 
																				year(now())-year(a.Fecha_Nacimiento)+1 
																			else 
																				year(now())-year(a.Fecha_Nacimiento) 
																		end between b.Range_Start and b.Range_End
					LEFT OUTER JOIN $schema.Colores c on b.Range_Color_ID = c.Color_ID
					LEFT OUTER JOIN $schema.Categorias d on case
																when month(a.Fecha_Nacimiento) < 8 then 
																	year(now())-year(a.Fecha_Nacimiento)+1 
																else 
																	year(now())-year(a.Fecha_Nacimiento) 
															end between d.Edad_Inicial and d.Edad_Final
					JOIN $schema.Equipos e ON a.Equipo_ID = e.Equipo_ID and e.Torneo_ID = $Season
					JOIN $schema.Categorias f ON e.Fuerza = f.Categoria_ID 
				where a.Jugador_ID in (" . $pList . ")
				order by f.Categoria_DESC, e.Equipo_DESC, cast(Numero as decimal) asc, Nombre, Apellido_P;";
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
								<div style="width: 20%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['953'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"] . $row2["Categoria_DESC"] . $row2["strikef"] . '</span></div></div>
								</div>
								<div style="width: 15%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['905'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"] . $row2["Numero"] . $row2["strikef"]  . '</span></div></div>
								</div>
								<div style="width: 30%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['906'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . $row2["Apodo"] . $row2["strikef"]  . '</span></div></div>
								</div>
								<div style="width: 15%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['910'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center; ' . $row2["Color_HEX"] . '"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . $row2["Edad"] . $row2["strikef"]  . '</span></div></div>
								</div>
								<div style="width: 20%;text-align: center;padding-top: 0px; background:#fff38f">
									<p style="margin-bottom: 0rem !important; padding-top: 26px;"></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-bold">' . $lang['950'] . '</span></div></div>
								</div>
							</div>
							<div class="d-flex px-0 py-0">
								<div style="width: 30%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['954'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . $row2["Equipo_DESC"] . $row2["strikef"]  . '</span></div></div>
								</div>
								<div style="width: 50%;text-align: center;padding-top: 0px;">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['907'] . '</span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal text-wrap">' . $row2["strikei"]  . $row2["Nombre"] . " " . $row2["Apellido_P"] . " " . $row2["Apellido_M"] . $row2["strikef"]  . '</span></div></div>
								</div>
								<div style="width: 20%;text-align: center;padding-top: 0px; background:#fff38f">
									<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold"><input checked type="checkbox" class="playersManagementAdminImprimirUpdateS" id="playersManagementAdminImprimirUpdate' . $row2["Jugador_ID"] . '" name="playersManagementAdminImprimirUpdate' . $row2["Jugador_ID"] . '" value="' . $row2["Jugador_ID"] . '"/></span></p>
									<div class="d-flex px-0 py-0 lh-1"><div style="width: 100%;text-align: center;"><span class="text-secondary text-xs font-weight-normal"></span></div></div>
								</div>
							</div>
						</td>
					</tr>';
		$count++;
	}
}

$htmlPlayer .= '			<tr>
								<td scope="row" style="padding-top: 0px; padding-bottom: 0px;">
									<div class="d-flex px-0 py-0">
										<div style="width: 45%;text-align: center;padding-top: 7px;">
											<span class="text-secondary text-xs font-weight-normal">
												<button type="button" class="btn btn-primary" onClick="clearPrintList();" >' . $lang['955-1'] . '</button>
											</span>
										</div>
										<div style="width: 55%;text-align: center;padding-top: 7px; background:#fff38f">
											<span class="text-secondary text-xs font-weight-normal">
												<button type="button" class="btn btn-primary" onClick="updatePrintListS();">' . $lang['956-1'] . '</button>
											</span>
										</div>
									</div>
								</td>
							</tr>';

$htmlPlayer .= '			<tr>
								<td scope="row" style="padding-top: 0px; padding-bottom: 0px;">
									<div class="d-flex px-0 py-0">
										<div style="width: 100%;text-align: center;padding-top: 0px;">
											<p style="margin-bottom: 0rem !important;"><span class="text-secondary text-xs font-weight-bold">' . $lang['915'] . '</span></p>
											<a hidden id="downloadPlayersIDBtnM2" href="" target="_blank" download ></a>
											<select onChange="printIDM2();" style="width:150px" name="printRegistersM2" id="printRegistersM2" size="1">
												<option value="" selected>' . $lang['js914-1'] . '</option>
												<option value="pdf/registrosEquipoManual.php?Edad1=0&Edad2=150&Imprimir=1">' . $lang['js915'] . '</option>';
$sql = "SELECT Range_Name, concat('pdf/registrosEquipoManual.php?Edad1=', Range_Start,'&Edad2=', Range_End,'&Imprimir=1') url 
		FROM $schema.Range_Age a
		where Range_Active = 1 and Range_Id <> 1 
		order by Range_Sort";	
$result = $Config->query($sql);
if ($result->num_rows > 0) {
	// output data of each row
	while($row2 = $result->fetch_assoc()) {
		$htmlPlayer .= '						<option value="' . $row2["url"] . '">' . $row2["Range_Name"]. '</option>';
	}
}
$htmlPlayer .= '							</select>
										</div>
									</div>
								</td>
							</tr>';

$htmlPlayer .= '		</thead>
					</table>
				</div>
			</div>
		</div>';
		
	$retunData = array('status' => '1', 'message' => 'Success.', 'dataPrintList' => $htmlPlayer);
    $Config->Close();
    echo json_encode($retunData);
?>