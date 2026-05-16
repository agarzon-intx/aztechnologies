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
	$sessionstat = $fgmembersite->CheckLogin('playersManagementChangeCategoriesReloadList.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
	
	$Season = $_COOKIE[$Config->getAlias() . "season"];
	$Category = SanitizeInteger($_POST["Category"]);
	
	$htmlCategories = '';
	$CategoryRows = 0;
	$htmlLogos = '';
	$htmlLogosList = '';
	
	$sql0 = "SELECT distinct b.Categoria_ID, b.Categoria_Desc 
			FROM $schema.Categorias b
			Where b.Torneo_ID = $Season
			order by b.Categoria_Orden asc";
	$result = $Config->query($sql0);
	if ($result->num_rows > 0) {
		$totCat = $result->num_rows;
	}
	
	$sql1 = "SELECT distinct b.Categoria_ID, b.Categoria_Desc 
			FROM $schema.Categorias b
			Where b.Torneo_ID = $Season and b.Categoria_ID = $Category
			order by b.Categoria_Orden asc";
	$result = $Config->query($sql1);
	if ($result->num_rows > 0) {
		// output data of each row
		$selected = false;	
		while($row2 = $result->fetch_assoc()) {
			if($totCat > 1){
				$htmlCategories .= '<a class="btn bg-gradient-dark dropdown-toggle" data-bs-toggle="dropdown" id="navbarDropdownMenuLinkCat" style="padding-top: 0px; padding-bottom: 0px; margin-bottom: 0px;" aria-expanded="false">' . $row2["Categoria_Desc"] . '</a>';
			}else{
				$htmlCategories .= '<a class="btn bg-gradient-dark" data-bs-toggle="dropdown" id="navbarDropdownMenuLinkCat" style="padding-top: 0px; padding-bottom: 0px; margin-bottom: 0px;" aria-expanded="false">' . $row2["Categoria_Desc"] . '</a>';
			}
			$Category = (string) $row2["Categoria_ID"];
		}
	} else {
	   $htmlCategories .= "";
	}
	
	$sql2 = "SELECT distinct b.Categoria_ID, b.Categoria_Desc 
			FROM $schema.Categorias b 
			Where b.Torneo_ID = $Season and b.Categoria_ID <> $Category
			order by b.Categoria_Orden asc";
	$result = $Config->query($sql2);
	if ($result->num_rows > 0) {
		// output data of each row
		$selected = false;
		$htmlCategories .= '<ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLinkCat">';
		while($row2 = $result->fetch_assoc()) {
			$htmlCategories .= '<li><a class="dropdown-item" onclick="teamsManagementAdminCategoryTeamShow(' . $row2["Categoria_ID"] . ')">' . $row2["Categoria_Desc"] . '</a></li>';
		}
		$htmlCategories .= '</ul>';
	} else {
	   $htmlCategories .= "";
	}
	
	$htmlCategories .= '<input type="hidden" id="playersManagementAdminSelectedCategory" value="' . $Category . '">';
	
	$retunData = array('status' => '1', 'message' => 'Success.', 'dataCategories' => $htmlCategories, 'category' => $Category, 'sql0' => $sql0, 'sql1' => $sql1, 'sql2' => $sql2);
	$Config->Close();
	echo json_encode($retunData);
?>