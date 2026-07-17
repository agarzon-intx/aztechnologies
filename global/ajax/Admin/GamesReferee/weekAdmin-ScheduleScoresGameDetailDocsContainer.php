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
	$sessionstat = $fgmembersite->CheckLogin('weekAdmin-ScheduleScoresGameDetail.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');


	$Season = htmlspecialchars($_POST["Season"]);
	$Week = htmlspecialchars($_POST["Week"]);
	$Game = htmlspecialchars($_POST["Game"]);
	$fecha = new DateTime();
	
    $retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');

    $htmlWeekGameDetail = '<table width="100%" border="1" cellspacing="0" cellpadding="0">
              <tr>
                <th width="225">' . $lang['317'] . '</th>
                <th width="225">' . $lang['318'] . '</th>
                <th width="225">' . $lang['319'] . '</th>
                <th width="225">' . $lang['320'] . '</th>
                <th width="90">' . $lang['316'] . '</th>
              </tr>
              <tr style="height:30px;">
                <td style="height:30px; text-align: center;">
                    <span style="text-align: center;">';
	if(file_exists ('../../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo1.png')){
		$htmlWeekGameDetail .= '<img id="cedulaD' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo1.png?tmp=' . $fecha->getTimestamp() . '" alt="Foto" style="width: 100%; height: auto;">';
	}
    $htmlWeekGameDetail .= '</span>
                </td>
                <td style="height:30px; text-align: center;">
                    <span style="text-align: center; ">';
	if(file_exists ('../../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo2.png')){
		$htmlWeekGameDetail .= '<img id="cedulaD' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo2.png?tmp=' . $fecha->getTimestamp() . '" alt="Foto" style="width: 100%; height: auto;">';
	}
    $htmlWeekGameDetail .= '</span>
                 </td>
                <td style="height:30px; text-align: center;">
                    <span style="text-align: center; ">';
	if(file_exists ('../../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo3.png')){
		$htmlWeekGameDetail .= '<img id="cedulaA1' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo3.png?tmp=' . $fecha->getTimestamp() . '" alt="Foto" style="width: 100%; height: auto;">';
	}
    $htmlWeekGameDetail .= '</span>
                 </td>
                <td style="height:30px; text-align: center;">
                    <span style="text-align: center; ">';
	if(file_exists ('../../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo4.png')){
		$htmlWeekGameDetail .= '<img id="cedulaA2' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo4.png?tmp=' . $fecha->getTimestamp() . '" alt="Foto" style="width: 100%; height: auto;">';
	}
    $htmlWeekGameDetail .= '</span>
                 </td>
                <td style="height:30px; text-align: center;">
                    <span style="text-align: center; ">
                        <img id="" src="imagenes/edit.png" width="30" height="30" onclick="$(\'#gameDocInputDiv\').css(\'z-index\', \'2\'); loadWeekAdminGameDetailDocs(' . $Season . ', ' . $Week . ', ' . $Game . ')"/>
                    </span>
                 </td>
              </tr>
            </table>';
			
			
			
	$htmlWeekGameDetailS = '
			<div class="d-block  d-xs-block d-md-block d-lg-block d-xl-none">
				<div class="card">
					<div class="table-responsive">
						<table width="100%" border="1" cellspacing="0" cellpadding="0">
						  <tr>
							<th width="225">' . $lang['317'] . '</th>
							<th width="225">' . $lang['318'] . '</th>
							<th width="225">' . $lang['319'] . '</th>
							<th width="225">' . $lang['320'] . '</th>
							<th width="90">' . $lang['316'] . '</th>
						  </tr>
						  <tr style="height:30px;">
							<td style="height:30px; text-align: center;">
								<span style="text-align: center;">';
		if(file_exists ('../../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo1.png')){
			$htmlWeekGameDetailS .= '<img id="anexo1' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo1.png?tmp=' . $fecha->getTimestamp() . '" alt="Foto" width="250" height="187">';
		}
		$htmlWeekGameDetailS .= '</span>
					</td>
					<td style="height:30px; text-align: center;">
						<span style="text-align: center; ">';
		if(file_exists ('../../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo2.png')){
			$htmlWeekGameDetailS .= '<img id="anexo2' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo2.png?tmp=' . $fecha->getTimestamp() . '" alt="Foto" width="250" height="187">';
		}
		$htmlWeekGameDetailS .= '</span>
					 </td>
					<td style="height:30px; text-align: center;">
						<span style="text-align: center; ">';
		if(file_exists ('../../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo3.png')){
			$htmlWeekGameDetailS .= '<img id="anexo3' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo3.png?tmp=' . $fecha->getTimestamp() . '" alt="Foto" width="250" height="187">';
		}
		$htmlWeekGameDetailS .= '</span>
					 </td>
					<td style="height:30px; text-align: center;">
						<span style="text-align: center; ">';
		if(file_exists ('../../../imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo4.png')){
			$htmlWeekGameDetailS .= '<img id="anexo4' . $Season . '-' . $Week . '-' . $Game . '" src="imagenes/Cedulas/' . $Season . '-' . $Week . '-' . $Game . '-Anexo4.png?tmp=' . $fecha->getTimestamp() . '" alt="Foto" width="250" height="187">';
		}
		$htmlWeekGameDetailS .= '</span>
						 </td>
						<td style="height:30px; text-align: center;">
							<span style="text-align: center; ">
								<img id="" src="imagenes/edit.png" width="30" height="30" onclick="$(\'#gameDocInputDiv\').css(\'z-index\', \'2\'); loadWeekAdminGameDetailDocs(' . $Season . ', ' . $Week . ', ' . $Game . ')"/>
							</span>
						 </td>
					  </tr>
					</table>
				</div>
			</div>
		</div>';
    $retunData = array('status' => '1', 'message' => 'Success.', 'dataWeekGameDetailDocContainer' => $htmlWeekGameDetail, 'dataWeekGameDetailDocContainerS' => $htmlWeekGameDetailS);
    echo json_encode($retunData);
?>