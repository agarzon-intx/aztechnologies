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
	require_once __DIR__ . '/user_institution_options.inc.php';
	$schema = $Config->getSchema();
	$Config->LoadFlags();

	$sessionstat = $fgmembersite->CheckLogin('userManagementTeamLeftOptions.php');

	include('lang.' . $_COOKIE[$Config->getAlias() . 'language'] . '.php');

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');

	$Season = (int) $_COOKIE[$Config->getAlias() . 'season'];
	$mode = (isset($_POST['mode']) && $_POST['mode'] === 'edit') ? 'edit' : 'create';

	$optionsInnerHtml = '';

	if ($mode === 'create') {
		$optionsInnerHtml .= um_institution_options_html($Config, $schema, $lang);
		$sql01 = "SELECT distinct c.Categoria_ID, b.Equipo_ID, b.Activo AS Equipo_Activo, b.Equipo_Institucion, concat(c.categoria_DESC,' - ',b.Equipo_FULLDESC) Equipo_FULLDESC 
				from (	select distinct b.Equipo_ID, Equipo_FULLDESC, a.Torneo_ID, a.Fuerza, a.Activo, " . um_team_institution_sql($Config, $schema, 'a') . " Equipo_Institucion
						from $schema.Equipos a
							join (	select distinct Equipo_ID, MAX(Torneo_ID) Torneo_ID, Fuerza 
									from $schema.Equipos    
									group by Equipo_ID) b on a.Equipo_ID = b.Equipo_ID and a.Torneo_ID = b.Torneo_ID) b
					join $schema.Categorias c on b.Fuerza = c.Categoria_ID and c.Torneo_ID = $Season
				where b.Equipo_ID > 0
				order by c.categoria_ID asc, b.Equipo_FULLDESC";
		$result = $Config->query($sql01);
		if ($result && $result->num_rows > 0) {
			while ($row2 = $result->fetch_assoc()) {
				$eid = (int) $row2['Equipo_ID'];
				$act = (int) $row2['Equipo_Activo'];
				$cid = (int) $row2['Categoria_ID'];
				$optionsInnerHtml .= "<option value='" . $eid . "' data-activo='" . $act . "' data-categoria='" . $cid . "'" . um_team_institution_attr($row2) . ">" . $row2['Equipo_FULLDESC'] . '</option>';
			}
		}
	} else {
		$userID = SanitizeInteger($_POST['id']);
		$qr = $Config->query("SELECT username FROM $schema.usuarios WHERE id_user = " . (int) $userID . " LIMIT 1");
		if (!$qr || $qr->num_rows === 0) {
			$retunData['message'] = 'User not found.';
			echo json_encode($retunData);
			exit;
		}
		$username = $qr->fetch_assoc()['username'];

		$optionsInnerHtml .= um_institution_options_html($Config, $schema, $lang, um_user_institution_ids($Config, $schema, $userID));

		$sql01 = "SELECT distinct c.Categoria_ID, b.Equipo_ID, b.Activo AS Equipo_Activo, b.Equipo_Institucion, concat(c.categoria_DESC,' - ',b.Equipo_FULLDESC) Equipo_FULLDESC 
				from (	select distinct b.Equipo_ID, Equipo_FULLDESC, a.Torneo_ID, a.Fuerza, a.Activo, " . um_team_institution_sql($Config, $schema, 'a') . " Equipo_Institucion
						from $schema.Equipos a
							join (	select distinct Equipo_ID, MAX(Torneo_ID) Torneo_ID, Fuerza 
									from $schema.Equipos    
									group by Equipo_ID) b on a.Equipo_ID = b.Equipo_ID and a.Torneo_ID = b.Torneo_ID) b
					join $schema.Categorias c on b.Fuerza = c.Categoria_ID and c.Torneo_ID = $Season
				where b.Equipo_ID > 0 and b.Equipo_ID not in (SELECT Equipo_ID FROM $schema.usuarios_equipo WHERE username = '" . $username . "')
				order by c.categoria_ID asc, b.Equipo_FULLDESC";
		$result = $Config->query($sql01);
		if ($result && $result->num_rows > 0) {
			while ($row2 = $result->fetch_assoc()) {
				$eid = (int) $row2['Equipo_ID'];
				$act = (int) $row2['Equipo_Activo'];
				$cid = (int) $row2['Categoria_ID'];
				$optionsInnerHtml .= "<option value='" . $eid . "' data-activo='" . $act . "' data-categoria='" . $cid . "'" . um_team_institution_attr($row2) . ">" . $row2['Equipo_FULLDESC'] . '</option>';
			}
		}
	}

	$retunData = array('status' => '1', 'message' => 'Success.', 'optionsInnerHtml' => $optionsInnerHtml);
	$Config->Close();
	echo json_encode($retunData);
