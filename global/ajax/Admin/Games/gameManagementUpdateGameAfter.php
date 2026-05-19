<?php
	session_start();

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

	//error_reporting(0);

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
	$sessionstat = $fgmembersite->CheckLogin('gameManagementUpdateGameAfter.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	$Season = (int) SanitizeInteger($_COOKIE[$Config->getAlias() . 'season'] ?? '0');
	$Category = (int) SanitizeInteger($_COOKIE[$Config->getAlias() . 'category'] ?? '0');
	$Week = (int) SanitizeInteger($_POST['Week'] ?? '0');

	$retunData = array('status' => '0', 'message' => 'No insert.', 'dataColorAnswer' => 'Error');

	$Connection = $Config->connectAdmin();
	if (!$Connection) {
		echo json_encode($retunData);
		exit;
	}
	if ($Season < 1 || $Week < 1) {
		$retunData['message'] = 'Invalid season or week.';
		$Connection->Close();
		echo json_encode($retunData);
		exit;
	}

	/** Drain all result sets from a mysqli CALL (required before running another query). */
	$drainMysqli = static function (mysqli $mysqli): void {
		while ($mysqli->more_results() && $mysqli->next_result()) {
			if ($res = $mysqli->store_result()) {
				$res->free();
			}
		}
	};

	/** Run CALL; tolerate mysqlnd "No data / zero rows" when the routine returns no result set. */
	$execCall = static function (mysqli $mysqli, string $sql, array &$retunData) use ($drainMysqli): bool {
		try {
			if (!$mysqli->query($sql)) {
				$retunData['message'] = 'Database error: ' . $mysqli->error;
				return false;
			}
		} catch (mysqli_sql_exception $e) {
			$msg = $e->getMessage();
			if (stripos($msg, 'No data') === false && stripos($msg, 'zero rows') === false) {
				$retunData['message'] = 'Database error: ' . $msg;
				return false;
			}
		}
		$drainMysqli($mysqli);
		return true;
	};

	$sql = "CALL $schema.Cal_Sem($Season, $Week);";
	if (!$execCall($Connection, $sql, $retunData)) {
		$Connection->Close();
		echo json_encode($retunData);
		exit;
	}

	$sql = "Select @out as 'count'";
	$result = $Connection->query($sql);
	if ($result && $result->num_rows > 0) {
		while ($row2 = $result->fetch_assoc()) {
			$retunData = array('status' => '1', 'message' => 'Success.');
		}
	}

	$sql = "CALL $schema.Generate_Equipo_Stats($Season, $Week, $Category);";
	if (!$execCall($Connection, $sql, $retunData)) {
		$Connection->Close();
		echo json_encode($retunData);
		exit;
	}

	$sql = "Select @out as 'count'";
	$result = $Connection->query($sql);
	if ($result && $result->num_rows > 0) {
		while ($row2 = $result->fetch_assoc()) {
			$retunData = array('status' => '1', 'message' => 'Success.');
		}
	}
	$Connection->Close();
    echo json_encode($retunData);
?>
