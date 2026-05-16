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
	$sessionstat = $fgmembersite->CheckLogin('playersManagementChangeCategories.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');
	
	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
	
	$Season = $_COOKIE[$Config->getAlias() . 'season'];
	$Category = 'null';
	$htmlCat = '<div class="container-fluid py-0">
					<div class="row">
						<div id="teamsAdminContent" class="" style="padding-top: 7px !important;padding-left: 0px !important;padding-right: 0px !important;padding-bottom: 0px !important;">
							<div class="container-fluid" style="padding: 0px;">
								<div class="row" id="teamAdminContentCategory">
									<div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6" id="categoriesContent" >
										<div class="container-fluid" style="padding: 0px;">
											<div class="row">
												<div class="col-5 col-sm-5 col-md-5 col-lg-5 col-xl-5 col-xxl-5 d-none d-sm-none d-md-block d-lg-block d-xl-block d-xxl-block">
													<div class="">' . $lang['70'] . ': </div>
												</div>
												<div class="col-8 col-sm-8 col-md-5 col-lg-5 col-xl-5 col-xxl-5" id="teamContentCategoryList">';
	
	$sql = "select Categoria_ID
			from $schema.Categorias
			Where Torneo_ID = $Season
			order by Categoria_Orden asc
			limit 1;";
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row2 = $result->fetch_assoc()) {
			$Category = $row2["Categoria_ID"];
		}
	}
	
	$sql = "SELECT distinct b.Categoria_ID, b.Categoria_Desc 
			FROM $schema.Categorias b 
			Where b.Torneo_ID = $Season
			order by Categoria_Orden asc";
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		$totCat = $result->num_rows;
	}
	
	$sql = "SELECT distinct b.Categoria_ID, b.Categoria_Desc 
			FROM $schema.Categorias b
			Where b.Torneo_ID = $Season and b.Categoria_ID = $Category
			order by b.Categoria_Orden asc";
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		$selected = false;	
		while($row2 = $result->fetch_assoc()) {
			if($totCat > 1){
				$htmlCat .= '						<a class="btn bg-gradient-dark dropdown-toggle" data-bs-toggle="dropdown" id="navbarDropdownMenuLinkCat" style="padding-top: 0px; padding-bottom: 0px; margin-bottom: 0px;" aria-expanded="false">' . $row2["Categoria_Desc"] . '</a>';
			}else{
				$htmlCat .= '						<a class="btn bg-gradient-dark" data-bs-toggle="dropdown" id="navbarDropdownMenuLinkCat" style="padding-top: 0px; padding-bottom: 0px; margin-bottom: 0px;" aria-expanded="false">' . $row2["Categoria_Desc"] . '</a>';
			}
			$Category = utf8_encode($row2["Categoria_ID"]);
		}
	} else {
	   $htmlCat .= "";
	}
	
	$sql = "SELECT distinct b.Categoria_ID, b.Categoria_Desc 
			FROM $schema.Categorias b
			Where b.Torneo_ID = $Season and b.Categoria_ID <> $Category
			order by b.Categoria_Orden asc";
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		$selected = false;
		$htmlCat .= '								<ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLinkCat">';
		while($row2 = $result->fetch_assoc()) {
			$htmlCat .= '								<li><a class="dropdown-item" onclick="teamsManagementAdminCategoryTeamShow((' . $row2["Categoria_ID"] . ')">' . $row2["Categoria_Desc"] . '</a></li>';
		}
		$htmlCat .= '								</ul>';
	} else {
		$htmlCat .= "";
	}
	
	$htmlCat .= '									<input type="hidden" id="playersManagementAdminSelectedCategory" value="' . $Category . '">';
	$htmlCat .= '								</div>
												<div class="col-4 col-sm-4 col-md-2 col-lg-2 col-xl-2 col-xxl-2">
													<div class="dropdowni btn-tooltip" data-bs-toggle="tooltip" data-bs-placement="top">
														<img src="./imagenes/refresh.png" width="20" height="20" onclick="teamsManagementAdminCategoryTeamShow(($(\'#playersManagementAdminSelectedCategory\').val()));" style="margin-left: 10;  margin-top: 2px;">
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
									</div>
								</div>
							</div>
						</div>';
	$htmlCat .= '</div>';
	$htmlCat .= '</div>';
	$htmlCat .= "<div style='width: 50%; float: left'>";
	$htmlCat .= '<div id="teamsContent" width="100%" style="height: 42"></div>';
	$htmlCat .= '</div>';
	$htmlCat .= '</div>';
	$htmlCat .= '</div>';
	$htmlCat .= '<div id="teamContent" width="100%" style="height: auto;"></div>';
	$htmlCat .= '</div>';
	
	$retunData = array('status' => '1', 'message' => 'Success.', 'dataCategory' => $htmlCat, 'category' => $Category);
	$Config->Close();
	echo json_encode($retunData);
?>