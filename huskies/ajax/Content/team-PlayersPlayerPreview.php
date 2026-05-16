<?php
    session_start();
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
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
	$sessionstat = $fgmembersite->CheckLogin('team-PlayersPlayerPreview.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
    
	$Player = $_POST["playerID"];
	$Season = $_COOKIE[$Config->getAlias() . 'season'];
    $htmlTeamPlayerPreview = '';
	
	$equipodesc = "";
	$logo = "";
	$Config->LoadFlags();

	$Clave = "";
	$Nombre = "";
	$Apellido_P = "";
	$Apellido_M = "";
	$Fecha_Nacimiento = "";
	$Equipo = "";
	$Equipologo = "";
	$Numero = "";
	$Apodo = "";
	$Sexo = "";
	$Foto = "";
	$Goles = "";
	$Amarillas = "";
	$Rojas = "";
	$Rojasdoble = "";
				
    $apellidos = 'Apellido_P, 
                  Apellido_M,';
	if($Config->jugadoresApellidos1){
	    $apellidos = '  SUBSTRING(Apellido_P, 1, 1) Apellido_P, 
				        SUBSTRING(Apellido_M, 1, 1) Apellido_M,';
	}

	$Config->query("SET NAMES 'utf8'");        
	$sql = "SELECT a.Jugador_ID,
				Nombre,
				$apellidos
				date_format(Fecha_Nacimiento,'%d/%m/%Y') Fecha_Nacimiento,
				Numero,
				ceiling(DATEDIFF(STR_TO_DATE(concat('01-01-',cast(Year(CURDATE()) as char(10))), '%d-%m-%Y'), Fecha_Nacimiento)/365) as Edad,
				Apodo,
				Validado,
				Estatus,
				Sexo,
				case 
					when OCTET_LENGTH(Foto) is null and Sexo = 'H' then 'boy.png'
					when OCTET_LENGTH(Foto) is null and Sexo = 'M' then 'girl.png'
					when OCTET_LENGTH(Foto) is null and Sexo not in ('M','H') then 'boy.png'
					when OCTET_LENGTH(Foto) is not null then ''
				end FotoFile,
				ifnull(Goles,0) Goles, 
				ifnull(Amarillas,0) Amarillas, 
				ifnull(Rojas,0) Rojas,
				ifnull(Rojasdoble,0) Rojasdoble 
			FROM $schema.Jugadores a
				left outer join (select Jugador_ID, sum(Goles) Goles from $schema.Goles where Jugador_ID = $Player and Torneo_ID = $Season group by Jugador_ID) b on a.Jugador_ID = b.Jugador_ID
				left outer join (select Jugador_ID, sum(Cantidad) Amarillas from $schema.Amonestados where Jugador_ID = $Player and Torneo_ID = $Season group by Jugador_ID) c on a.Jugador_ID = c.Jugador_ID
				left outer join (select Jugador_ID, sum(Cantidad) Rojas from $schema.Expulsados where Jugador_ID = $Player and Torneo_ID = $Season group by Jugador_ID) d on a.Jugador_ID = d.Jugador_ID
				left outer join (select Jugador_ID, sum(Cantidad) Rojasdoble from $schema.Expulsados where Doble = 2 and Jugador_ID = $Player and Torneo_ID = $Season group by Jugador_ID) e on a.Jugador_ID = d.Jugador_ID
			where a.Jugador_ID = $Player";
	//$htmlTeamPlayerPreview .= $sql;
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row2 = $result->fetch_assoc()) {
			$Nombre = $row2["Nombre"];
			$Apellido_P = $row2["Apellido_P"];
			$Apellido_M = $row2["Apellido_M"];
			$Fecha_Nacimiento = $row2["Fecha_Nacimiento"];
			$Numero = $row2["Numero"];
			$Apodo = $row2["Apodo"];						
			$Edad = $row2["Edad"];
			$Validado = $row2["Validado"];
			$Estatus = $row2["Estatus"];
			$Sexo = $row2["Sexo"];
			$Foto = $row2["FotoFile"];
			$Goles = $row2["Goles"];
			$Amarillas = $row2["Amarillas"];
			$Rojas = $row2["Rojas"];
			$Rojasdoble = $row2["Rojasdoble"];
		}
	}
	$imgsrc = '';
	if($Foto == ""){
		$imgsrc = './Form/fetch_image.php?Jugador_ID=' . $Player . '&Imagen=Foto';
	}else{
		$imgsrc = './imagenes/' . $Foto;
	}
    $birthDate = explode("/", $Fecha_Nacimiento);
	$Edad = date("Y") - $birthDate[2];
	$color = "";
	if($Sexo == "H"){ 
		$color = "#4BE1DA";																
	}else{
		$color = "#F95693";
	}
	$htmlTeamPlayerPreview .= '<div class="table-responsive d-none d-xs-block d-md-block d-lg-block d-xl-block">
								<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
										<tbody>
											<tr style="height:30px;">
												<td rowspan="11" scope="row" class="align-middle text-center" style="border-bottom-width: 0px;"><img id="foto" src="' . $imgsrc . '" alt="Foto" style="width:100%;"></td>
												<td scope="row" class="align-middle text-left" style="text-align: left;"><span class="text-secondary text-xs font-weight-normal">' . $lang['907'] . '</span></td>
												<td scope="row" class="align-middle text-left" style="text-align: left;"><span class="text-secondary text-xs font-weight-normal">' . $Nombre . '</span></td>
											</tr>
											<tr style="height:30px;">
												<td scope="row" class="align-middle text-left" style="text-align: left;"><span class="text-secondary text-xs font-weight-normal">' . $lang['919'] . '</span></td>
												<td scope="row" class="align-middle text-left" style="text-align: left;"><span class="text-secondary text-xs font-weight-normal">' . $Apellido_P . '</span></td>
											</tr>
											<tr style="height:30px;">
												<td scope="row" class="align-middle text-left" style="text-align: left;"><span class="text-secondary text-xs font-weight-normal">' . $lang['920'] . '</span></td>
												<td scope="row" class="align-middle text-left" style="text-align: left;"><span class="text-secondary text-xs font-weight-normal">' . $Apellido_M . '</span></td>
											</tr>
											<tr style="height:30px;">
												<td scope="row" class="align-middle text-left" style="text-align: left;"><span class="text-secondary text-xs font-weight-normal">' . $lang['906'] . '</span></td>
												<td scope="row" class="align-middle text-left" style="text-align: left;"><span class="text-secondary text-xs font-weight-normal">' . $Apodo . '</span></td>
											</tr>
											<tr style="height:30px;">
												<td scope="row" class="align-middle text-left" style="text-align: left;"><span class="text-secondary text-xs font-weight-normal">' . $lang['921'] . '</span></td>
												<td scope="row" class="align-middle text-left" style="text-align: left;"><span class="text-secondary text-xs font-weight-normal">' . $Fecha_Nacimiento . '</span></td>
											</tr>
											<tr style="height:30px;">
												<td scope="row" class="align-middle text-left" style="text-align: left;"><span class="text-secondary text-xs font-weight-normal">' . $lang['923'] . '</span></td>
												<td scope="row" class="align-middle text-left" style="text-align: left;"><span class="text-secondary text-xs font-weight-normal">' . $Numero . '</span></td>
											</tr>
											<tr style="height:30px;">
												<td scope="row" class="align-middle text-left" style="text-align: left;"><span class="text-secondary text-xs font-weight-normal">' . $lang['926'] . '</span></td>';
	if($Estatus == 'A'){
		$htmlTeamPlayerPreview .= '<td scope="row" class="align-middle text-left" style="text-align: left;"><span class="text-secondary text-xs font-weight-normal">' . $lang['927'] . '</span></td>';
	}
	if($Estatus == 'B'){
		$htmlTeamPlayerPreview .= '<td scope="row" class="align-middle text-left" style="text-align: left;"><span class="text-secondary text-xs font-weight-normal">' . $lang['928'] . '</span></td>';
	}
	if($Estatus == 'S'){
		$htmlTeamPlayerPreview .= '<td scope="row" class="align-middle text-left" style="text-align: left;"><span class="text-secondary text-xs font-weight-normal">' . $lang['929'] . '</span></td>';
	}

	$htmlTeamPlayerPreview .= '				</tr>
											<tr style="height:30px;">
												<td scope="row" class="align-middle text-left" style="text-align: left;"><span class="text-secondary text-xs font-weight-normal">' . $lang['910'] . '</span></td>
												<td scope="row" class="align-middle text-left" style="text-align: left;"><span class="text-secondary text-xs font-weight-normal">' . $Edad . '</span></td>
											</tr>
											<tr style="height:30px;">
												<td scope="row" class="align-middle text-left" style="text-align: left;"><img src="./imagenes/goal.png" width="20" height="20" alt=""></td>
												<td scope="row" class="align-middle text-left" style="text-align: left;"><span class="text-secondary text-xs font-weight-normal">' . $Goles . '</span></td>
											</tr>
											<tr style="height:30px;">
												<td scope="row" class="align-middle text-left" style="text-align: left;"><img src="./imagenes/amarilla.png" width="16" height="20" alt=""></td>
												<td scope="row" class="align-middle text-left" style="text-align: left;"><span class="text-secondary text-xs font-weight-normal">' . $Amarillas . '</span></td>
											</tr>
											<tr style="height:30px;">
												<td scope="row" class="align-middle text-left" style="text-align: left;"><img src="./imagenes/roja.png" width="16" height="20" alt=""> (<img src="./imagenes/damarilla.png" width="20" height="20" alt="">)</td>
												<td scope="row" class="align-middle text-left" style="text-align: left;"><span class="text-secondary text-xs font-weight-normal">' . $Rojas . ' (' . $Rojasdoble . ')</span></td>
											</tr>
										</tbody>
									</table>
						</div>';
    $htmlTeamPlayerPreview .= '</tr></tbody></table></div>';
	
	$htmlTeamPlayerPreview .= '<div class="table-responsive d-block d-xs-none d-md-none d-lg-none d-xl-none">
								<table class=" table align-items-center mb-0" style="border-color: #136aeb;">
										<tbody>
											<tr style="height:30px;">
												<td rowspan="11" scope="row" class="align-middle text-center" style="border-bottom-width: 0px; padding: 0px;"><img id="foto" src="' . $imgsrc . '" alt="Foto" style="width:100%;"></td>
												<td scope="row" class="align-middle text-left" style="text-align: left;padding: 0px;"><span class="text-secondary text-xxs font-weight-normal">' . substr($lang['907'], 0, 12) . '</span></td>
												<td scope="row" class="align-middle text-left" style="text-align: left;padding: 0px;"><span class="text-secondary text-xxs font-weight-normal">' . $Nombre . '</span></td>
											</tr>
											<tr style="height:30px;">
												<td scope="row" class="align-middle text-left" style="text-align: left;padding: 0px;"><span class="text-secondary text-xxs font-weight-normal">' . substr($lang['919-1'], 0, 12) . '</span></td>
												<td scope="row" class="align-middle text-left" style="text-align: left;padding: 0px;"><span class="text-secondary text-xxs font-weight-normal">' . $Apellido_P . '</span></td>
											</tr>
											<tr style="height:30px;">
												<td scope="row" class="align-middle text-left" style="text-align: left;padding: 0px;"><span class="text-secondary text-xxs font-weight-normal">' . substr($lang['920-1'], 0, 12) . '</span></td>
												<td scope="row" class="align-middle text-left" style="text-align: left;padding: 0px;"><span class="text-secondary text-xxs font-weight-normal">' . $Apellido_M . '</span></td>
											</tr>
											<tr style="height:30px;">
												<td scope="row" class="align-middle text-left" style="text-align: left;padding: 0px;"><span class="text-secondary text-xxs font-weight-normal">' . substr($lang['906'], 0, 12) . '</span></td>
												<td scope="row" class="align-middle text-left" style="text-align: left;padding: 0px;"><span class="text-secondary text-xxs font-weight-normal">' . $Apodo . '</span></td>
											</tr>
											<tr style="height:30px;">
												<td scope="row" class="align-middle text-left" style="text-align: left;padding: 0px;"><span class="text-secondary text-xxs font-weight-normal">' . substr($lang['921-1'], 0, 12) . '</span></td>
												<td scope="row" class="align-middle text-left" style="text-align: left;padding: 0px;"><span class="text-secondary text-xxs font-weight-normal">' . $Fecha_Nacimiento . '</span></td>
											</tr>
											<tr style="height:30px;">
												<td scope="row" class="align-middle text-left" style="text-align: left;padding: 0px;"><span class="text-secondary text-xxs font-weight-normal">' . substr($lang['923-1'], 0, 12). '</span></td>
												<td scope="row" class="align-middle text-left" style="text-align: left;padding: 0px;"><span class="text-secondary text-xxs font-weight-normal">' . $Numero . '</span></td>
											</tr>
											<tr style="height:30px;">
												<td scope="row" class="align-middle text-left" style="text-align: left;padding: 0px;"><span class="text-secondary text-xxs font-weight-normal">' . substr($lang['926'], 0, 12) . '</span></td>';
	if($Estatus == 'A'){
		$htmlTeamPlayerPreview .= '<td scope="row" class="align-middle text-left" style="text-align: left;padding: 0px;"><span class="text-secondary text-xxs font-weight-normal">' . $lang['927'] . '</span></td>';
	}
	if($Estatus == 'B'){
		$htmlTeamPlayerPreview .= '<td scope="row" class="align-middle text-left" style="text-align: left;padding: 0px;"><span class="text-secondary text-xxs font-weight-normal">' . $lang['928'] . '</span></td>';
	}
	if($Estatus == 'S'){
		$htmlTeamPlayerPreview .= '<td scope="row" class="align-middle text-left" style="text-align: left;padding: 0px;"><span class="text-secondary text-xxs font-weight-normal">' . $lang['929'] . '</span></td>';
	}

	$htmlTeamPlayerPreview .= '				</tr>
											<tr style="height:30px;">
												<td scope="row" class="align-middle text-left" style="text-align: left;padding: 0px;"><span class="text-secondary text-xxs font-weight-normal">' . substr($lang['910'], 0, 12) . '</span></td>
												<td scope="row" class="align-middle text-left" style="text-align: left;padding: 0px;"><span class="text-secondary text-xxs font-weight-normal">' . $Edad . '</span></td>
											</tr>
											<tr style="height:30px;">
												<td scope="row" class="align-middle text-left" style="text-align: left;padding: 0px;"><img src="./imagenes/goal.png" width="20" height="20" alt=""></td>
												<td scope="row" class="align-middle text-left" style="text-align: left;padding: 0px;"><span class="text-secondary text-xxs font-weight-normal">' . $Goles . '</span></td>
											</tr>
											<tr style="height:30px;">
												<td scope="row" class="align-middle text-left" style="text-align: left;padding: 0px;"><img src="./imagenes/amarilla.png" width="16" height="20" alt=""></td>
												<td scope="row" class="align-middle text-left" style="text-align: left;padding: 0px;"><span class="text-secondary text-xxs font-weight-normal">' . $Amarillas . '</span></td>
											</tr>
											<tr style="height:30px;">
												<td scope="row" class="align-middle text-left" style="text-align: left;padding: 0px;"><img src="./imagenes/roja.png" width="16" height="20" alt=""> (<img src="./imagenes/damarilla.png" width="20" height="20" alt="">)</td>
												<td scope="row" class="align-middle text-left" style="text-align: left;padding: 0px;"><span class="text-secondary text-xxs font-weight-normal">' . $Rojas . ' (' . $Rojasdoble . ')</span></td>
											</tr>
										</tbody>
									</table>
						</div>';
    $htmlTeamPlayerPreview .= '</tr></tbody></table></div>';
    $retunData = array('status' => '1', 'message' => 'Success.', 'dataTeamPlayerPreview' => $htmlTeamPlayerPreview);
    $Config->Close();
    echo json_encode($retunData);
?>