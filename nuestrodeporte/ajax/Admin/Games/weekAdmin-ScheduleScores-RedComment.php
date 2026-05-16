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
	$sessionstat = $fgmembersite->CheckLogin('weekAdmin-ScheduleScores-RedComment.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');


    $retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
    
	$RedComment = SanitizeText($_POST['RedComment']);
	$PlayerID = SanitizeInteger($_POST['PlayerID']);
	$RedDays = SanitizeInteger($_POST['RedDays']);
	$RedFee = SanitizeInteger($_POST['RedFee']);
	$RedPaid = SanitizeInteger($_POST['RedPaid']);
	$type = SanitizeText($_POST['Type']);
	
    
	$htmlWeekAdminGameComment = "<table width='100%'>
            	<tr height='200'>
                	<td colspan='2'></td>
                </tr>
				<tr>
                	<td colspan='2' style='width: 100%; text-align: center;'><TEXTAREA id='comentarios$PlayerID' style='width:390px; height: 100px' maxlength='400' rows='2' cols='20' name='txttext'>$RedComment</TEXTAREA></td>
                </tr>
                <tr>
                    <td colspan='2' height='10'></td>
                </tr>
                <tr>
                	<td style='text-align: right; padding-right: 10;'>" . $lang['30'] . " <INPUT name='dias$PlayerID' type='text' id='dias$PlayerID' value='$RedDays' size='4'></td>
                    <td style='padding-left: 10;'>" . $lang['31'] . " <INPUT name='multa$PlayerID' type='text' id='multa$PlayerID' value='$RedFee' size='10'></td>
                </tr>
                <tr>
                    <td colspan='2' height='10'></td>
                </tr>
                <!--<tr>";
				if($RedPaid == 1){
                	$htmlWeekAdminGameComment .= "<td style='text-align: right; padding-right: 104px;'>" . $lang['32'] . " <INPUT name='pagado$PlayerID' id='pagado$PlayerID' type='checkbox' checked></td>";
				}else{
					$htmlWeekAdminGameComment .= "<td style='text-align: right; padding-right: 104px;'>" . $lang['32'] . " <INPUT name='pagado$PlayerID' id='pagado$PlayerID' type='checkbox'></td>";
				}
                $htmlWeekAdminGameComment .= "<td></td>
                </tr>-->
                <tr>
                    <td colspan='2' height='10'></td>
                </tr>
                <tr>
                    <td colspan='2' style='width: 100%; text-align: center;'>
						<button type='button' class='btn btn-primary' onClick='$(\"#rojaComentario". $PlayerID . "\").val($(\"#comentarios$PlayerID\").val());
																					$(\"#rojaComentarioS". $PlayerID . "\").val($(\"#comentarios$PlayerID\").val());
																					$(\"#rojaDias". $PlayerID . "\").val($(\"#dias$PlayerID\").val());
																					$(\"#rojaDiasS". $PlayerID . "\").val($(\"#dias$PlayerID\").val());
																					$(\"#rojaMulta". $PlayerID . "\").val($(\"#multa$PlayerID\").val());
																					$(\"#rojaMultaS". $PlayerID . "\").val($(\"#multa$PlayerID\").val());
																					var pagado = 0;
																					if ($(\"#pagado". $PlayerID . "\").is(\":checked\")){
																					  pagado = 1;
																					}
																					$(\"#rojaPagado". $PlayerID . "\").val(pagado);
																					$(\"#rojaPagadoS". $PlayerID . "\").val(pagado);
																					$(\"#roja". $PlayerID . "\").prop(\"title\", \"" . $lang['655'] . "\" + $(\"#comentarios$PlayerID\").val() + \", " . $lang['656'] . "\" + $(\"#multa$PlayerID\").val() + \", " . $lang['657'] . "\" + $(\"#dias$PlayerID\").val());
																					$(\"#rojaS". $PlayerID . "\").prop(\"title\", \"" . $lang['655'] . "\" + $(\"#comentarios$PlayerID\").val() + \", " . $lang['656'] . "\" + $(\"#multa$PlayerID\").val() + \", " . $lang['657'] . "\" + $(\"#dias$PlayerID\").val());
																					$(\"#rojaInputDiv\").css(\"z-index\", \"-1\");
																					$(\"#rojaInput\").html(\"\");' >" . $lang['0000'] . "</button>
						<button type='button' class='btn btn-primary' onClick='$(\"#rojaInputDiv\").css(\"z-index\", \"-1\");
																					$(\"#rojaInput\").html(\"\");' >" . $lang['0001'] . "</button>
						<button type='button' class='btn btn-primary' onClick='$(\"#rojaComentario". $PlayerID . "\").val(\"\");
																					$(\"#rojaComentarioS". $PlayerID . "\").val(\"\");
																					$(\"#rojaDias". $PlayerID . "\").val(0);
																					$(\"#rojaDiasS". $PlayerID . "\").val(0);
																					$(\"#rojaMulta". $PlayerID . "\").val(0);
																					$(\"#rojaMultaS". $PlayerID . "\").val(0);
																					$(\"#rojaPagado". $PlayerID . "\").val(0);
																					$(\"#rojaPagadoS". $PlayerID . "\").val(0);
																					$(\"#roja". $PlayerID . "\").prop(\"checked\", false);
																					$(\"#rojaS". $PlayerID . "\").prop(\"checked\", false);
																					$(\"#rojaInputDiv\").css(\"z-index\", \"-1\");
																					$(\"#rojaInput\").html(\"\");
																					$(\"#roja". $PlayerID . "\").prop(\"title\", \"\");
																					$(\"#rojaS". $PlayerID . "\").prop(\"title\", \"\");' >" . $lang['0005'] . "</button>
                    </td>
                </tr>
            </table>";
    $retunData = array('status' => '1', 'message' => 'Success.', 'dataWeekAdminGameRedComment' => $htmlWeekAdminGameComment);
    echo json_encode($retunData);
?>