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
	$Config->LoadFlags();
	
	$sessionstat = $fgmembersite->CheckLogin('userManagementEdit.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
    $retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');

	
	$Season = $_COOKIE[$Config->getAlias() . 'season'];
	$userID = SanitizeInteger($_POST['id']);
	$fecha = new DateTime();

	$sql = "SELECT username,
				phone_number,
				name,
				ApellidoP,
				ApellidoM,
				email,
				Equipo_ID,
				case When active = 0 then '' else 'checked' end active
			FROM $schema.usuarios
			where id_user = " . $userID .";";
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row2 = $result->fetch_assoc()) {
			$username = $row2["username"];
			$phone = $row2["phone_number"];
			$name = $row2["name"];
			$ApellidoP = $row2["ApellidoP"];
			$ApellidoM = $row2["ApellidoM"];
			$email = $row2["email"];
			$Equipo_ID = $row2["Equipo_ID"];
			$active = $row2["active"];
		}
	}
	
	$sqlCat = "SELECT Categoria_ID, Categoria_Desc FROM $schema.Categorias WHERE Torneo_ID = $Season ORDER BY Categoria_Orden ASC, Categoria_ID ASC";
	$resCat = $Config->query($sqlCat);
	$umCategoryOptionsHtml = '<option value="">' . htmlspecialchars($lang['378-2'], ENT_QUOTES, 'UTF-8') . '</option>';
	if ($resCat && $resCat->num_rows > 0) {
		while ($rc = $resCat->fetch_assoc()) {
			$cid = (int) $rc['Categoria_ID'];
			$cdn = htmlspecialchars($rc['Categoria_Desc'], ENT_QUOTES, 'UTF-8');
			$umCategoryOptionsHtml .= '<option value="' . $cid . '">' . $cdn . '</option>';
		}
	}
	
	$htmlUsers = '';
	$htmlUsers .= '<div class="container-fluid py-2">
					<div class="row">
						<div class="col-xl-12">
							<h3>' . $lang['823'] . '</h3>
						</div>
					</div>
					<div class="row">
						<div class="col-xl-12">
							<span id="register_errorloca" class="error" style="color: red;"></span>
						</div>
					</div>
					<div class="row">
						<div class="col-xl-12">
							<h6>' . $lang['811'] . '</h6>
						</div>
					</div>
					<div class="row">
						<div class="col-4 col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4" hidden>
							<div class="input-group input-group-static mb-4">
								<label>' . $lang['802'] . '</label>
								<input type="text" name="username" id="username" value="' . $username . '" maxlength="50" class="form-control"><br/>
								<span id="register_nombre_errorloc" class="error"></span>
							</div>
						</div>
						<div class="col-4 col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4">
							<div class="input-group input-group-static mb-4">
								<label>' . $lang['814'] . '</label>
								<input type="text" name="nombrea" id="nombrea" value="' . $name . '" maxlength="50" class="form-control"><br/>
								<span id="register_nombre_errorloc" class="error"></span>
							</div>
						</div>
						<div class="col-4 col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4">
							<div class="input-group input-group-static mb-4">
								<label>' . $lang['815'] . '</label>
								<input type="text" class="form-control" name="apellidopa" id="apellidopa" value="' . $ApellidoP . '" maxlength="50" /><br/>
								<span id="register_apellidop_errorloc" class="error"></span> 
							</div>
						</div>
						<div class="col-4 col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4">
							<div class="input-group input-group-static mb-4">
								<label>' . $lang['816'] . '</label>
								<input type="text" class="form-control" name="apellidoma" id="apellidoma" value="' . $ApellidoM . '" maxlength="50" /><br/>
								<span id="register_apellidom_errorloc" class="error"></span>
							</div>
						</div>
						<div class="col-4 col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4">
							<div class="input-group input-group-static mb-4">
								<label>' . $lang['817'] . '</label>
								<input type="text" class="form-control" name="telefonoa" id="telefonoa" value="' . $phone . '" maxlength="50" /><br/>
								<span id="register_telefono_errorloc" class="error"></span>
							</div>
						</div>
						<div class="col-4 col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4">
							<div class="input-group input-group-static mb-4">
								<label>' . $lang['818'] . '</label>
								<input type="text" class="form-control" name="emaila" id="emaila" value="' . $email . '" maxlength="50" /><br/>
								<span id="register_email_errorloc" class="error"></span>
							</div>
						</div>
						<div class="col-4 col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" name="activo" id="activo" ' . $active . '>
								<label class="custom-control-label" for="activo">' . $lang['835'] . '</label>
							</div>
						</div>
						<div class="col-4 col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4">
							<div class="input-group input-group-static mb-4">
								<label>' . htmlspecialchars($lang['378'], ENT_QUOTES, 'UTF-8') . '</label>
								<select class="form-control um-team-status-filter">
									<option value="2">' . htmlspecialchars($lang['378-2'], ENT_QUOTES, 'UTF-8') . '</option>
									<option value="0">' . htmlspecialchars($lang['378-0'], ENT_QUOTES, 'UTF-8') . '</option>
									<option value="1">' . htmlspecialchars($lang['378-1'], ENT_QUOTES, 'UTF-8') . '</option>
								</select>
							</div>
						</div>
						<div class="col-4 col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4">
							<div class="input-group input-group-static mb-4">
							<label >' . htmlspecialchars($lang['70'], ENT_QUOTES, 'UTF-8') . '</label>
							<select class="form-control flex-grow-1 um-category-filter" style="min-width: 8rem;">' . $umCategoryOptionsHtml . '</select>
						</div>
					</div>
					</div>
					<div class="row">
						<div class="col-5 col-xs-5 col-sm-5 col-md-5 col-lg-5 col-xl-5 col-xxl-5">
							<div class="input-group input-group-static mb-4">
								<label for="lenguaje" class="ms-0">' . $lang['819'] . '</label>
								<select class="form-control" id="equipol" size="5" multiple="multiple">';
	$sql0 = "   SELECT Equipo_ID
				FROM usuarios_equipo
				where username = '" . $username . "'
				    and Equipo_ID = 0;";
	$result = $Config->query($sql0);
	if ($result->num_rows == 0) {
			$htmlUsers .= '<option value="0">' . $Config->liga . '</option>';
	}
	$sql0 = "   SELECT Equipo_ID
				FROM usuarios_equipo
				where username = '" . $username . "'
				    and Equipo_ID = -1;";
	$result = $Config->query($sql0);
	if ($result->num_rows == 0) {
			$htmlUsers .= '<option value="-1">' . $lang['10761'] . '</option>';
	}
	$sql01 = "SELECT distinct c.Categoria_ID, b.Equipo_ID, b.Activo AS Equipo_Activo, concat(c.categoria_DESC,' - ',b.Equipo_FULLDESC) Equipo_FULLDESC 
				from (	select distinct b.Equipo_ID, Equipo_FULLDESC, a.Torneo_ID, a.Fuerza, a.Activo 
						from $schema.Equipos a
							join (	select distinct Equipo_ID, MAX(Torneo_ID) Torneo_ID, Fuerza 
									from $schema.Equipos    
									group by Equipo_ID) b on a.Equipo_ID = b.Equipo_ID and a.Torneo_ID = b.Torneo_ID) b
					join $schema.Categorias c on b.Fuerza = c.Categoria_ID and c.Torneo_ID = $Season
				where b.Equipo_ID > 0 and b.Equipo_ID not in (  SELECT Equipo_ID
                        										FROM $schema.usuarios_equipo
                        										where username = '" . $username . "')
				order by c.categoria_ID asc, b.Equipo_FULLDESC";
	
	//$htmlUsers .= $sql01;
	$result = $Config->query($sql01);
	if ($result->num_rows > 0) {
		// output data of each row
		$selected = false;
		while($row2 = $result->fetch_assoc()) {
			$eid = (int) $row2['Equipo_ID'];
			$act = (int) $row2['Equipo_Activo'];
			$cid = (int) $row2['Categoria_ID'];
			$htmlUsers .= "<option value='" . $eid . "' data-activo='" . $act . "' data-categoria='" . $cid . "'>" . $row2['Equipo_FULLDESC'] . "</option>";
		}
	}
	$htmlUsers .= '			    </select>
							</div>
						</div>
						<div class="col-2 col-xs-2 col-sm-2 col-md-2 col-lg-2 col-xl-2 col-xxl-2" style="text-align: center;">
						    <br/>
						    <button onclick="moveRight(\'equipol\',\'equipor\', 1);">>></button>
                            <br/>
                            <br/>
                            <button onclick="moveRight(\'equipor\',\'equipol\', 0)"><<</button>
                        </div>
						<div class="col-5 col-xs-5 col-sm-5 col-md-5 col-lg-5 col-xl-5 col-xxl-5">
							<div class="input-group input-group-static mb-4">
								<label for="lenguaje" class="ms-0">' . $lang['819-1'] . '</label>
								<select class="form-control" id="equipor" size="5" multiple="multiple">';
	$sql1 = "   SELECT distinct Equipo_ID
				FROM usuarios_equipo
				where username = '" . $username . "'
				    and Equipo_ID in (0);";
	$result = $Config->query($sql1);
	if ($result->num_rows > 0) {
		// output data of each row
		$selected = false;
		while($row2 = $result->fetch_assoc()) {
			$htmlUsers .= '<option value="0">' . $Config->liga . '</option>';
		}
	}
	$sql1 = "   SELECT distinct Equipo_ID
				FROM usuarios_equipo
				where username = '" . $username . "'
				    and Equipo_ID in (-1);";
	$result = $Config->query($sql1);
	if ($result->num_rows > 0) {
		// output data of each row
		$selected = false;
		while($row2 = $result->fetch_assoc()) {
			$htmlUsers .= '<option value="-1">' . $lang['10761'] . '</option>';
		}
	}
	$sql11 = "SELECT distinct c.Categoria_ID, b.Equipo_ID, concat(c.categoria_DESC,' - ',b.Equipo_FULLDESC) Equipo_FULLDESC 
				from (	select distinct b.Equipo_ID, Equipo_FULLDESC, a.Torneo_ID, a.Fuerza 
						from $schema.Equipos a
							join (	SELECT distinct Equipo_ID
									FROM $schema.usuarios_equipo
									where username = '" . $username . "') b on a.Equipo_ID = b.Equipo_ID
		                where Torneo_ID = $Season) b
					join $schema.Categorias c on b.Fuerza = c.Categoria_ID and c.Torneo_ID = $Season
				where b.Equipo_ID > 0
				order by c.categoria_ID asc, b.Equipo_FULLDESC";
	
//	$htmlUsers .= $sql11;
	$result = $Config->query($sql11);
	if ($result->num_rows > 0) {
		// output data of each row
		$selected = false;
		while($row2 = $result->fetch_assoc()) {
			$htmlUsers .= "<option value='" . $row2["Equipo_ID"] . "'>" . $row2["Equipo_FULLDESC"] . "</option>";
		}
	}
	$htmlUsers .= '			    </select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="" >
							<button type="button" class="btn btn-primary" onClick="validateUserEdit(' . $userID . ', $(\'#nombrea\').val(), $(\'#apellidopa\').val(), $(\'#apellidoma\').val(), $(\'#telefonoa\').val(), $(\'#emaila\').val(), $(\'#activo\').is(\':checked\'), $(\'#username\').val());" >' . $lang['0000'] . '</button>
							<button type="button" class="btn btn-primary" onClick="userManagementLimpiarEditUser();" >' . $lang['0001'] . '</button>
						</div>
					</div>
				</div>
				<script type="text/javascript">
				// <![CDATA[
				    function moveRight(leftValue, rightValue, type) {
                        if ($("#" + leftValue + " > option:selected").length == 0) {
                            window.alert("' . $lang['846'] . '");
                        } else {
                            if(($("#" + leftValue + " option[value=0]:selected").length > 0 || $("#" + leftValue + " option[value=-1]:selected").length > 0) && type){
                                if($("#" + rightValue + " option").length > 0){
                                    if(confirm("' . $lang['847-0'] . '" + $("#" + leftValue + " option").eq(0).text()+ "' . $lang['847-1'] . '")){
                                        var options1 = $("#" + rightValue + " option").clone();
                                        $("#" + leftValue).append(options1);
                                        $("#" + rightValue).empty();
                                        var value = $("#" + leftValue + " option:selected").val();
                                        var options = $("#" + leftValue + " > option[value=\'" + value + "\']").clone();
                                        $("#" + rightValue).append(options);
                                        $("#" + leftValue + " option[value=\'" + value + "\']").remove();
                                        
                                    }else{
                                        var value = $("#" + leftValue + " option:selected").val();
                                        $("#" + leftValue + " option[value=\'" + value + "\']").prop("selected", false);
                                    }
                                }else{
                                    var value = $("#" + leftValue + " option:selected").val();
                                    var options = $("#" + leftValue + " > option[value=\'" + value + "\']").clone();
                                    $("#" + rightValue).append(options);
                                    $("#" + leftValue + " option[value=\'" + value + "\']").remove();
                                }
                            }else{
                                if(($("#" + rightValue + " option").eq(0).val() == 0 || $("#" + rightValue + " option").eq(0).val() == -1) && type){
                                    if(confirm("' . $lang['848-0'] . '" + $("#" + rightValue + " option").eq(0).text()+ "' . $lang['848-1'] . '")){
                                        var value = $("#" + rightValue + " option").eq(0).val();
                                        var options = $("#" + rightValue + " > option[value=\'" + value + "\']").clone();
                                        $("#" + leftValue).append(options);
                                        $("#" + rightValue + " option[value=\'" + value + "\']").remove();
                                        var options1 = $("#" + leftValue + " option:selected").clone();
                                        $("#" + rightValue).append(options1);

                                    }else{
                                        $("#" + leftValue + " option").prop("selected", false);
                                    }
                                }else{
                                    var options = $("#" + leftValue + " > option:selected").clone();
                                    $("#" + rightValue).append(options);
                                    $("#" + leftValue + " option:selected").remove();
                                }
                            }
                            if ((leftValue === \'equipol\' || rightValue === \'equipol\') && typeof userManagementApplyTeamLeftFilters === \'function\') {
                                var $umPanel = $(\'#equipol\').closest(\'#userManagementCreate, #userManagementEdit\');
                                if ($umPanel.length) {
                                    setTimeout(function () { userManagementApplyTeamLeftFilters($umPanel); }, 0);
                                }
                            }
                        }
                    }
				// ]]>
				</script>';
	
    $retunData = array('status' => '1', 'message' => 'Success.', 'dataUser' => $htmlUsers, 'sql0' => $sql0, 'sql01' => $sql01, 'sql1' => $sql1, 'sql11' => $sql11);
    $Config->Close();
    echo json_encode($retunData);
?>