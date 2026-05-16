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
	$Config->LoadFlags();
	
	$sessionstat = $fgmembersite->CheckLogin('userManagementCreate.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
    $retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
	$fecha = new DateTime();

    $Season = $_COOKIE[$Config->getAlias() . 'season'];
	$salt = bin2hex(random_bytes(16));
    $htmlUsers = '';
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
	$htmlUsers .= '<div class="container-fluid py-2">
					<form onsubmit="event.preventDefault();" autocomplete="off">
						<div class="row">
							<div class="col-xl-12">
								<h3>' . $lang['810'] . '</h3>
							</div>
						</div>
						<div class="row">
							<div class="col-xl-12">
								<span id="register_errorloc" class="error" style="color: red;"></span>
							</div>
						</div>
						<div class="row">
							<div class="col-xl-12">
								<h6>' . $lang['811'] . '</h6>
							</div>
						</div>
						<div class="row">
							<div class="col-6 col-xs-6 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4">
								<div class="input-group input-group-static mb-4">
									<label>' . $lang['814'] . '</label>
									<input type="text" name="nombre" id="nombre" value="" maxlength="50" class="form-control"><br/>
									<span id="register_nombre_errorloc" class="error"></span>
								</div>
							</div>
							<div class="col-6 col-xs-6 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4">
								<div class="input-group input-group-static mb-4">
									<label>' . $lang['815'] . '</label>
									<input type="text" class="form-control" name="apellidop" id="apellidop" value="" maxlength="50" /><br/>
									<span id="register_apellidop_errorloc" class="error"></span> 
								</div>
							</div>
							<div class="col-6 col-xs-6 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4">
								<div class="input-group input-group-static mb-4">
									<label>' . $lang['816'] . '</label>
									<input type="text" class="form-control" name="apellidom" id="apellidom" value="" maxlength="50" /><br/>
									<span id="register_apellidom_errorloc" class="error"></span>
								</div>
							</div>
							<div class="col-6 col-xs-6 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4">
								<div class="input-group input-group-static mb-4">
									<label>' . $lang['817'] . '</label>
									<input type="text" class="form-control" name="telefono" id="telefono" value="" maxlength="50" /><br/>
									<span id="register_telefono_errorloc" class="error"></span>
								</div>
							</div>
							<div class="col-6 col-xs-6 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4">
								<div class="input-group input-group-static mb-4">
									<label>' . $lang['818'] . '</label>
									<input type="text" class="form-control" name="realemail" id="realemail" value="" maxlength="50" autocomplete="nope"/><br/>
									<span id="register_email_errorloc" class="error"></span>
								</div>
							</div>
							<div class="col-6 col-xs-6 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4">
								<div class="input-group input-group-static mb-4">
									
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
								<select class="form-control" id="equipol" size="5" multiple="multiple">
								    <option value="0">' . $Config->liga . '</option>
								    <option value="-1">' . $lang['10761'] . '</option>';
	$sql01 = "SELECT distinct c.Categoria_ID, b.Equipo_ID, b.Activo AS Equipo_Activo, concat(c.categoria_DESC,' - ',b.Equipo_FULLDESC) Equipo_FULLDESC 
				from (	select distinct b.Equipo_ID, Equipo_FULLDESC, a.Torneo_ID, a.Fuerza, a.Activo 
						from $schema.Equipos a
							join (	select distinct Equipo_ID, MAX(Torneo_ID) Torneo_ID, Fuerza 
									from $schema.Equipos    
									group by Equipo_ID) b on a.Equipo_ID = b.Equipo_ID and a.Torneo_ID = b.Torneo_ID) b
					join $schema.Categorias c on b.Fuerza = c.Categoria_ID and c.Torneo_ID = $Season
				where b.Equipo_ID > 0
				order by c.categoria_ID asc, b.Equipo_FULLDESC";
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
								<select class="form-control" id="equipor" size="5" multiple="multiple">
								</select>
							</div>
						</div>
					</div>
						<div class="row">
							<div class="col-6 col-xs-6 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4">
								<div class="input-group input-group-static mb-4">
									<label>' . $lang['820'] . '</label>
									<input type="text" autocomplete="nope" class="form-control" name="real-usuario" id="real-usuario" value="" maxlength="50"/>
									<span id="register_username_errorloc" class="error"></span>
								</div>
							</div>
							<div class="col-6 col-xs-6 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4">
								<div class="input-group input-group-static mb-4">
									<label>' . $lang['821'] . '</label>
									<div class="pwdwidgetdiv form-control" id="thepwddiv" style="padding: 0px;"></div>
									<noscript>
										<input class="form-control" autocomplete="new-password" type="password" name="real-password" id="real-password" placeholder="•••••••••••••" onkeydown="handlePassword(event)"/>
									</noscript> 
									<div style="display:none;">
										<input type="password" name="salt" id="salt" value=""/>
									</div>
									<div id="register_password_errorloc" class="error" style="clear:both"></div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="" >
								<button type="button" class="btn btn-primary" onClick="validateUserAdd($(\'#nombre\').val(), $(\'#apellidop\').val(), $(\'#apellidom\').val(), $(\'#telefono\').val(), $(\'#realemail\').val(), $(\'#real-usuario\').val(), CryptoJS.PBKDF2($(\'#real-password_text_id\').val(), \'' . $salt . '\', { keySize: 160/32, iterations: 1000 }).toString(), \'' . $salt . '\');" >' . $lang['0000'] . '</button>
								<button type="button" class="btn btn-primary" onClick="userManagementLimpiarCreateUser();" >' . $lang['0001'] . '</button>
							</div>
						</div>
					</form>
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
                    
					function handlePassword(event){
						if(event.keyCode == 13) {
							submit();
						}
					}
						
					var pwdwidget = new PasswordWidget("thepwddiv","real-password", "' . $lang['824'] . '", "' . $lang['825'] . '", "' . $lang['826'] . '", "' . $lang['827'] . '", "' . $lang['828'] . '", "' . $lang['829'] . '");
					pwdwidget.MakePWDWidget();
					setTimeout(() => { $(\'#usuario\').val(\'\'); $(\'#password_id\').val(\'\'); $(\'#password_text_id\').val(\'\'); }, 500);
					
				// ]]>
				</script>';
			
    $retunData = array('status' => '1', 'message' => 'Success.', 'dataUserAdd' => $htmlUsers, 'sql0' => $sql0, 'sql01' => $sql01, 'sql1' => $sql1, 'sql11' => $sql11);
    echo json_encode($retunData);
?>