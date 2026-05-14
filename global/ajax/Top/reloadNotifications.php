<?php
    session_start();
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

if (!defined('APP_SITE_ROOT')) {
	$___d = __DIR__;
	while ($___d !== dirname($___d)) {
		$___p = $___d . DIRECTORY_SEPARATOR . 'site_paths.php';
		if (is_readable($___p)) {
			require_once $___p;
			break;
		}
		$___d = dirname($___d);
	}
}
	require("membersite_config.php");
	$schema = $Config->getSchema();
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

    $retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
    
	$Season = $_COOKIE[$Config->getAlias() . 'season'];
	$Category = $_COOKIE[$Config->getAlias() . 'category'];

    $htmlAlert = '';

	$alertlist = '';
	$alertNumber = 0;

	$sql = "SELECT Aviso_ID, Aviso_Titulo, Aviso_Tipo, Aviso_Fecha_Inicio
					FROM $schema.Avisos
					where Aviso_Fecha_Inicio <= cast(now() as Date) and Aviso_Fecha_Fin >= cast(now() as Date) 
						and Aviso_Estatus = '1'
					order by Aviso_Fecha_Inicio desc, Aviso_Titulo asc";
	$result = $Config->query($sql);
	$count = 1;
	if($result){
		$alertNumber = $result->num_rows;
		if ($result->num_rows > 0) {
			while($row2 = $result->fetch_assoc()) {
				$alertlist .= '<li class="mb-2">
									<a class="dropdown-item border-radius-md" onclick="loadAlert(' . utf8_encode($row2["Aviso_ID"]) . ');">
										<div class="d-flex align-items-center py-1">
											<span class="material-icons">email</span>
											<div class="ms-2">
												<h6 class="text-sm font-weight-normal my-auto">
													' . utf8_encode($row2["Aviso_Titulo"]) . '
												</h6>
											</div>
										</div>
									</a>
								</li>';
				$count = $count + 1;
			}
		}
	}
	
	$htmlAlert .= '	<a href="javascript:;" class="nav-link text-body p-0 position-relative" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
						<i class="material-icons cursor-pointer">notifications</i>
						<span class="position-absolute top-5 start-100 translate-middle badge rounded-pill bg-danger border border-white small py-1 px-2">
							<span class="small">' . $alertNumber . '</span>
							<span class="visually-hidden">unread notifications</span>
						</span>
					</a>
					<ul class="dropdown-menu dropdown-menu-end p-2 me-sm-n4" aria-labelledby="dropdownMenuButton">
						' . $alertlist . '
					</ul>';
	
	
    
	$retunData = array('status' => '1', 'message' => 'Success.', 'dataAlert' => $htmlAlert, 'totAlert' => $alertNumber);
    $Config->Close();
    echo json_encode($retunData);
