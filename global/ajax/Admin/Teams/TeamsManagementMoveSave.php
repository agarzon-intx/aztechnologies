<?php
	session_start();
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	header('Content-Type: application/json; charset=UTF-8');

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
	$sessionstat = $fgmembersite->CheckLogin('TeamsManagementMoveSave.php');
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	$Season = (int) $_COOKIE[$Config->getAlias() . 'season'];
	$categoryId = isset($_POST['categoryId']) ? SanitizeInteger($_POST['categoryId']) : 0;
	$teamIdsRaw = isset($_POST['teamIds']) ? (string) $_POST['teamIds'] : '';

	$retunData = array('status' => '0', 'message' => 'Error', 'dataMoveAnswer' => 'Error');

	$ids = array();
	foreach (explode(',', $teamIdsRaw) as $part) {
		$id = (int) trim($part);
		if ($id > 0) {
			$ids[$id] = $id;
		}
	}
	$ids = array_values($ids);

	if ($Season <= 0 || $categoryId <= 0 || count($ids) === 0) {
		$retunData['dataMoveAnswer'] = isset($lang['522-4']) ? $lang['522-4'] : 'Invalid selection';
		echo json_encode($retunData);
		exit;
	}

	$catCheck = $Config->query(
		"SELECT Categoria_ID FROM $schema.Categorias WHERE Torneo_ID = $Season AND Categoria_ID = $categoryId LIMIT 1"
	);
	if (!$catCheck || $catCheck->num_rows === 0) {
		$retunData['dataMoveAnswer'] = isset($lang['522-4']) ? $lang['522-4'] : 'Invalid category';
		echo json_encode($retunData);
		exit;
	}

	$idList = implode(',', $ids);
	$Connection = $Config->connectAdmin();
	if (!$Connection) {
		$retunData['dataMoveAnswer'] = 'DB error';
		echo json_encode($retunData);
		exit;
	}

	$sql = "UPDATE $schema.Equipos
			SET Fuerza = $categoryId
			WHERE Torneo_ID = $Season
			  AND Equipo_ID IN ($idList)";
	$ok = $Connection->query($sql);
	$affected = $ok ? (int) $Connection->affected_rows : 0;
	$Connection->Close();

	if (!$ok) {
		$retunData['dataMoveAnswer'] = 'Update failed';
		echo json_encode($retunData);
		exit;
	}

	$retunData = array(
		'status' => '1',
		'message' => 'Success.',
		'dataMoveAnswer' => isset($lang['522-3']) ? $lang['522-3'] : $lang['441'],
		'updated' => $affected,
		'categoryId' => $categoryId,
	);
	echo json_encode($retunData);
?>
