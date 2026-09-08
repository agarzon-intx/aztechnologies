<?php
	session_start();

	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	header("Content-Type: application/json; charset=utf-8");

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
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('TournamentsManagementNewSave.php');

	include("class.upload.php");
	$__lang = !empty($_COOKIE[$Config->getAlias() . 'language'])
		? $_COOKIE[$Config->getAlias() . 'language']
		: $Config->LoadLanguage();
	include('lang.' . $__lang . '.php');

	/**
	 * Same behaviour as site imagenes/copy-rename.sh (main folder + Original/), without bash — works on Windows and Linux.
	 */
	function tournamentsManagementCopyRenameTourImagesNew(string $imagenesDir, int $fromId, int $toId): string
	{
		$fromId = (int) $fromId;
		$toId = (int) $toId;
		$log = [];
		$dirs = [
			$imagenesDir,
			$imagenesDir . DIRECTORY_SEPARATOR . 'Original',
		];
		foreach ($dirs as $base) {
			if (!is_dir($base) || !is_readable($base)) {
				continue;
			}
			$fromGlob = $base . DIRECTORY_SEPARATOR . $fromId . '-*';
			foreach (glob($fromGlob) ?: [] as $srcPath) {
				if (!is_file($srcPath)) {
					continue;
				}
				$bn = basename($srcPath);
				$dot = strrpos($bn, '.');
				$nameWithoutExt = $dot === false ? $bn : substr($bn, 0, $dot);
				$extPart = $dot === false ? '' : substr($bn, $dot);
				$destBn = $toId . $nameWithoutExt . ($dot === false ? '' : $extPart);
				$destPath = $base . DIRECTORY_SEPARATOR . $destBn;
				if (!@copy($srcPath, $destPath)) {
					$log[] = 'copy failed: ' . $srcPath . ' -> ' . $destPath;
				}
			}
			$badPrefix = (string) $toId . (string) $fromId;
			$renameGlob = $base . DIRECTORY_SEPARATOR . $badPrefix . '-*';
			foreach (glob($renameGlob) ?: [] as $srcPath) {
				if (!is_file($srcPath)) {
					continue;
				}
				$bn = basename($srcPath);
				if (strncmp($bn, $badPrefix, strlen($badPrefix)) !== 0) {
					continue;
				}
				$newBn = (string) $toId . substr($bn, strlen($badPrefix));
				$destPath = $base . DIRECTORY_SEPARATOR . $newBn;
				if (!@rename($srcPath, $destPath)) {
					$log[] = 'rename failed: ' . $srcPath . ' -> ' . $destPath;
				}
			}
		}
		return $log === [] ? 'OK' : implode('; ', $log);
	}

	$tournamentName = SanitizeText($_POST['tournamentName'] ?? '');
	$tournamentActual = SanitizeInteger($_POST['tournamentActual'] ?? '0');
	$tournamentInscr = SanitizeInteger($_POST['tournamentInscr'] ?? '0');
	$tournamentVs = SanitizeInteger($_POST['tournamentVs'] ?? '0');
	$tournamentWeeks = SanitizeInteger($_POST['tournamentWeeks'] ?? '0');
	if ($tournamentWeeks === '' || $tournamentWeeks === null) {
		$tournamentWeeks = '0';
	}
	if ($tournamentInscr === '' || $tournamentInscr === null) {
		$tournamentInscr = '0';
	}
	if ($tournamentVs === '' || $tournamentVs === null) {
		$tournamentVs = '0';
	}
	$prevTourID = 0;
	$newTourID = 0;

	$sql0 = "SELECT max(Torneo_ID) Torneo_ID FROM $schema.Torneos;";
	$result = $Config->query($sql0);
	if ($result && $result->num_rows > 0) {
		while ($row2 = $result->fetch_assoc()) {
			$prevTourID = (int) $row2["Torneo_ID"];
		}
	}

	$actual = 'N';
	if ((int) $tournamentActual === 1) {
		$actual = 'S';
	}

	$retunData = array('status' => '0', 'message' => 'No insert.', 'dataTournamentAnswer' => 'Error');

	$username = isset($_SESSION[$Config->getAlias() . 'username'])
		? SanitizeText($_SESSION[$Config->getAlias() . 'username'])
		: '';
	$sql1 = "CALL $schema.TournamentCreate('" . $username . "', '$tournamentName', '$actual', " . (int) $tournamentInscr . ", " . (int) $tournamentVs . ", " . (int) $tournamentWeeks . ", @out);";

	$Connection = $Config->connectAdmin();
	if (!$Connection) {
		echo json_encode(array('status' => '0', 'message' => 'DB connection failed.', 'dataTournamentAnswer' => 'Error'));
		exit;
	}
	$callOk = $Connection->query($sql1);

	$sql01 = "SELECT max(Torneo_ID) Torneo_ID FROM $schema.Torneos;";
	$result = $Config->query($sql01);
	if ($result && $result->num_rows > 0) {
		while ($row2 = $result->fetch_assoc()) {
			$newTourID = (int) $row2["Torneo_ID"];
		}
	}

	$siteRoot = defined('APP_SITE_ROOT') ? rtrim((string) APP_SITE_ROOT, '/\\') : dirname($_SERVER['SCRIPT_FILENAME'] ?? __FILE__, 4);
	$imagenesDir = $siteRoot . DIRECTORY_SEPARATOR . 'imagenes';
	$salida = is_dir($imagenesDir)
		? tournamentsManagementCopyRenameTourImagesNew($imagenesDir, (int) $prevTourID, (int) $newTourID)
		: ('imagenes not found: ' . $imagenesDir);

	$sql2 = "Select @out as 'count'";
	$result = $Connection->query($sql2);
	if ($callOk && $result && $result->num_rows > 0) {
		while ($row2 = $result->fetch_assoc()) {
			$retunData = array(
				'status' => '1',
				'message' => 'Success.',
				'dataTournamentAnswer' => $lang['756'],
				'sql1' => $sql1,
				'sql2' => $sql2,
				'out' => $salida,
			);
		}
	} elseif (!$callOk) {
		$retunData = array(
			'status' => '0',
			'message' => 'TournamentCreate failed.',
			'dataTournamentAnswer' => 'Error',
			'error' => $Connection->error,
		);
	}
	$Connection->Close();
	echo json_encode($retunData);
?>
