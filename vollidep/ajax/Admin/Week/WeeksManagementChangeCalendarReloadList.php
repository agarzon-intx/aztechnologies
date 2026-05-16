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
	$sessionstat = $fgmembersite->CheckLogin('WeeksManagementChangeCalendarReloadList.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
	
	$Season = $_COOKIE[$Config->getAlias() . "season"];
	$Calendar = SanitizeInteger($_POST["Calendar"]);
	
	$htmlCalendar = '';
	$CategoryRows = 0;
	$htmlLogos = '';
	$htmlLogosList = '';
	
	$sql0 = "select distinct a.Calendario_ID, b.Calendario_DESC
                from $schema.Categorias a
                	join $schema.Calendario b on a.Calendario_ID = b.Calendario_ID
                where Categoria_ID in ( select Fuerza
                						from $schema.Equipos
                						where Torneo_ID = $Season)
                	and a.Torneo_ID = $Season;";
	$result = $Config->query($sql0);
	if ($result->num_rows > 0) {
		$totCat = $result->num_rows;
	}
	
	$sql1 = "select distinct a.Calendario_ID, b.Calendario_DESC
                from $schema.Categorias a
                	join $schema.Calendario b on a.Calendario_ID = b.Calendario_ID
                where Categoria_ID in ( select Fuerza
                						from $schema.Equipos
                						where Torneo_ID = $Season)
                	and a.Torneo_ID = $Season
                    and a.Calendario_ID = $Calendar;";
	$result = $Config->query($sql1);
	if ($result->num_rows > 0) {
		// output data of each row
		$selected = false;	
		while($row2 = $result->fetch_assoc()) {
			if($totCat > 1){
				$htmlCalendar .= '<a class="btn bg-gradient-dark dropdown-toggle" data-bs-toggle="dropdown" id="navbarDropdownMenuLinkCat" style="padding-top: 0px; padding-bottom: 0px; margin-bottom: 0px;" aria-expanded="false">' . $row2["Calendario_DESC"] . '</a>';
			}else{
				$htmlCalendar .= '<a class="btn bg-gradient-dark" data-bs-toggle="dropdown" id="navbarDropdownMenuLinkCat" style="padding-top: 0px; padding-bottom: 0px; margin-bottom: 0px;" aria-expanded="false">' . $row2["Calendario_DESC"] . '</a>';
			}
			$Category = utf8_encode($row2["Categoria_ID"]);
		}
	} else {
	   $htmlCalendar .= "";
	}
	
	$sql2 = "select distinct a.Calendario_ID, b.Calendario_DESC
                from $schema.Categorias a
                	join $schema.Calendario b on a.Calendario_ID = b.Calendario_ID
                where Categoria_ID in ( select Fuerza
                						from $schema.Equipos
                						where Torneo_ID = $Season)
                	and a.Torneo_ID = $Season
                    and a.Calendario_ID <> $Calendar;";
	$result = $Config->query($sql2);
	if ($result->num_rows > 0) {
		// output data of each row
		$selected = false;
		$htmlCalendar .= '<ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLinkCat">';
		while($row2 = $result->fetch_assoc()) {
			$htmlCalendar .= '<li><a class="dropdown-item" onclick="weeksManagementAdminCalendarWeeksShow(' . $row2["Calendario_ID"] . ')">' . $row2["Calendario_DESC"] . '</a></li>';
		}
		$htmlCalendar .= '</ul>';
	} else {
	   $htmlCalendar .= "";
	}
	
	$htmlCalendar .= '<input type="hidden" id="weeksManagementAdminSelectedCalendar" value="' . $Calendar . '">';
	
	$retunData = array('status' => '1', 'message' => 'Success.', 'dataCalendar' => $htmlCalendar, 'calendar' => $Calendar, 'sql0' => $sql0, 'sql1' => $sql1, 'sql2' => $sql2);
	$Config->Close();
	echo json_encode($retunData);
?>