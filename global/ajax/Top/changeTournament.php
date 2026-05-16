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
	require_once("membersite_config.php");
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('changeTournament.php');

	$__langCk = $Config->getAlias() . 'language';
	if (!isset($_COOKIE[$__langCk]) || $_COOKIE[$__langCk] === '') {
		$Config->LoadLanguage();
		$__lang = $Config->lan;
	} else {
		$__lang = $_COOKIE[$__langCk];
	}
	include 'lang.' . $__lang . '.php';

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
	
	$Season = SanitizeInteger($_POST["Season"]);
	
	$htmlCategories = '';
	$CategoryRows = 0;
	$htmlLogos = '';
	$htmlLogosList = '';
	//echo $Season;
	if($Season == '-1'){
		$sql = "select max(Torneo_ID) Torneo_ID
				from $schema.Torneos  
				where Actual = 'S'";
		$result = $Config->query($sql);
		if ($result->num_rows > 0) {
			// output data of each row
			while($row2 = $result->fetch_assoc()) {
				$Season = mb_convert_encoding((string)$row2["Torneo_ID"], 'UTF-8', 'ISO-8859-1');
				setcookie($Config->getAlias() . "season",$Season,0,'/');
			}
		}
	}else{
		setcookie($Config->getAlias() . "season",$Season,0,'/');
	}
	
	$sql = "select Categoria_ID
			from $schema.Categorias
			where Categoria_ID in ( select Fuerza
									from $schema.Equipos
									where Torneo_ID = $Season and Activo = 1)
			order by Categoria_Orden asc
			limit 1;";
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row2 = $result->fetch_assoc()) {
			setcookie($Config->getAlias() . "category",$row2["Categoria_ID"],0,'/');
			$Category = $row2["Categoria_ID"];
		}
	}
	
	$sql = "SELECT distinct a.Fuerza Categoria_ID, b.Categoria_Desc 
			FROM $schema.Equipos a
				join $schema.Categorias b on a.Fuerza = b.Categoria_ID and b.Torneo_Id = $Season
			Where a.Torneo_ID = $Season and a.Activo = 1
			order by Categoria_Orden asc";
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		$totCat = $result->num_rows;
	}
	
	$sql = "SELECT distinct a.Fuerza Categoria_ID, b.Categoria_Desc 
			FROM $schema.Equipos a
				join $schema.Categorias b on a.Fuerza = b.Categoria_ID and b.Torneo_Id = $Season
			Where a.Torneo_ID = $Season and a.Fuerza = $Category and a.Activo = 1
			order by Categoria_Orden asc";
	//echo $sql;
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		$selected = false;	
		while($row2 = $result->fetch_assoc()) {
			if($totCat > 1){
				$htmlCategories .= '<a class="btn bg-gradient-dark dropdown-toggle" data-bs-toggle="dropdown" id="navbarDropdownMenuLinkCat" style="padding-top: 0px; padding-bottom: 0px; margin-bottom: 0px;" aria-expanded="false">' . $row2["Categoria_Desc"] . '</a>';
			}else{
				$htmlCategories .= '<a class="btn bg-gradient-dark" data-bs-toggle="dropdown" id="navbarDropdownMenuLinkCat" style="padding-top: 0px; padding-bottom: 0px; margin-bottom: 0px;" aria-expanded="false">' . $row2["Categoria_Desc"] . '</a>';
			}
			$Category = mb_convert_encoding((string)$row2["Categoria_ID"], 'UTF-8', 'ISO-8859-1');
			setcookie($Config->getAlias() . "category",$Category,0,'/');
		}
	} else {
	   $htmlCategories .= "";
	}
	
	$sql = "SELECT distinct a.Fuerza Categoria_ID, b.Categoria_Desc 
			FROM $schema.Equipos a
				join $schema.Categorias b on a.Fuerza = b.Categoria_ID and b.Torneo_Id = $Season
			Where a.Torneo_ID = $Season and a.Fuerza <> $Category and a.Activo = 1
			order by Categoria_Orden asc";
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		$selected = false;
		$htmlCategories .= '<ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLinkCat">';
		while($row2 = $result->fetch_assoc()) {
			$htmlCategories .= '<li><a class="dropdown-item" onclick="loadCategory(' . $Season . ', ' . $row2["Categoria_ID"] . ')">' . $row2["Categoria_Desc"] . '</a></li>';
		}
		$htmlCategories .= '</ul>';
	} else {
	   $htmlCategories .= "";
	}
	
	$htmlCategories .= '<input type="hidden" id="selectedCategory" value="' . $Category . '">';
	
	$retunData = array('status' => '1', 'message' => 'Success.', 'dataCategories' => $htmlCategories, 'category' => $Category);
	$Config->Close();
	echo json_encode($retunData);
