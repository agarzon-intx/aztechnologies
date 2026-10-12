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
	require_once __DIR__ . '/../Players/Admin/player_team_move.inc.php';
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('TeamsManagementDelete.php');

	$__langCk = $Config->getAlias() . 'language';
	if (!isset($_COOKIE[$__langCk]) || $_COOKIE[$__langCk] === '') {
		$Config->LoadLanguage();
		$__lang = $Config->lan;
	} else {
		$__lang = $_COOKIE[$__langCk];
	}
	include 'lang.' . $__lang . '.php';

	$retunData = array('status' => '0', 'message' => $lang['206']);

	$Config->LoadFlags();
	if ((int) $Config->deleteTeam !== 1) {
		$retunData['message'] = $lang['539-24'];
		echo json_encode($retunData);
		exit;
	}
	if (!az_user_is_league_admin($Config, $schema, $fgmembersite)) {
		echo json_encode($retunData);
		exit;
	}

	$teamId = isset($_POST['team']) ? (int) $_POST['team'] : 0;

	$__a = $Config->getAlias();
	$Season = (isset($_COOKIE[$__a . 'season']) && $_COOKIE[$__a . 'season'] !== '') ? (int) $_COOKIE[$__a . 'season'] : 0;
	if ($Season <= 0) {
		$resultSeason = $Config->query("SELECT Torneo_ID FROM $schema.Torneos WHERE Actual = 'S' LIMIT 1");
		if ($resultSeason && ($rowSeason = $resultSeason->fetch_assoc())) {
			$Season = (int) $rowSeason['Torneo_ID'];
		}
	}
	if ($teamId <= 0 || $Season <= 0) {
		echo json_encode($retunData);
		exit;
	}

	$gamesFilter = "SELECT 1 FROM $schema.Juegos g WHERE g.Torneo_ID = $Season AND (g.Local_ID = $teamId OR g.Visitante_ID = $teamId)";
	$resultGames = $Config->query("SELECT EXISTS($gamesFilter) hasGames");
	if (!$resultGames || !($rowGames = $resultGames->fetch_assoc())) {
		echo json_encode($retunData);
		exit;
	}
	if ((int) $rowGames['hasGames'] === 1) {
		$retunData['message'] = $lang['539-23'];
		echo json_encode($retunData);
		exit;
	}

	$Connection = $Config->connectAdmin();
	if (!$Connection) {
		echo json_encode($retunData);
		exit;
	}

	// Juegos is re-checked inside the DELETE: MyISAM tables give no transaction to hold the earlier check.
	$sql1 = "DELETE FROM $schema.Equipos WHERE Torneo_ID = $Season AND Equipo_ID = $teamId AND NOT EXISTS($gamesFilter)";
	if ($Connection->query($sql1) === false) {
		error_log('TeamDelete failed: ' . $Connection->error);
		$Connection->close();
		echo json_encode($retunData);
		exit;
	}
	$deletedRows = (int) $Connection->affected_rows;
	if ($deletedRows === 0) {
		$Connection->close();
		$retunData['message'] = $lang['539-23'];
		echo json_encode($retunData);
		exit;
	}

	$Connection->query("DELETE FROM $schema.Equipo_Stats WHERE Torneo_ID = $Season AND Equipo_ID = $teamId");
	$Connection->query("DELETE FROM $schema.Permisos WHERE Torneo_ID = $Season AND Equipo_ID = $teamId");

	$username = isset($_SESSION[$__a . 'username']) ? $Connection->real_escape_string($_SESSION[$__a . 'username']) : '';
	$Connection->query("CALL $schema.insertIntoControlTable('" . $username . "', 'DELETE', 'TEAM', '" . $Connection->real_escape_string($sql1) . "', 'OK, total deletes: $deletedRows')");
	$Connection->close();

	$retunData = array('status' => '1', 'message' => $lang['539-22']);
	echo json_encode($retunData);
