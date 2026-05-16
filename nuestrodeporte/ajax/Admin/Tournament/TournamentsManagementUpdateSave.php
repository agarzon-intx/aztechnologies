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
	$sessionstat = $fgmembersite->CheckLogin('TournamentsManagementUpdateSave.php');
	
	include("class.upload.php");
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	/**
	 * Same behaviour as site imagenes/copy-rename.sh (main folder + Original/), without bash — works on Windows and Linux.
	 */
	function tournamentsManagementCopyRenameTourImages(string $imagenesDir, int $fromId, int $toId): string
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

	$tournamentid = SanitizeText($_POST['tournamentid']);
	$tournamentName = SanitizeText($_POST['tournamentName']);
	$tournamentActual = SanitizeText($_POST['tournamentActual']);
	$tournamentInscr = SanitizeText($_POST['tournamentInscr']);
	$tournamentVs = SanitizeText($_POST['tournamentVs']);
	$tournamentWeeks = SanitizeText($_POST['tournamentWeeks']);
	$tournamentRounds = SanitizeText($_POST['tournamentRounds']);
	$prevTourID = 0;
	$newTourID = 0;
	
	$sql0="	SELECT max(Torneo_ID) Torneo_ID
			FROM $schema.Torneos;";
	$result = $Config->query($sql0);
	if ($result->num_rows > 0) {
		while($row2 = $result->fetch_assoc()) {
			$newTourID = $row2["Torneo_ID"];
		}		
	}
	
	$sql0="	SELECT max(Torneo_ID) Torneo_ID
			FROM $schema.Torneos
			where Torneo_ID <> " . $newTourID . ";";
	$result = $Config->query($sql0);
	if ($result->num_rows > 0) {
		while($row2 = $result->fetch_assoc()) {
			$prevTourID = $row2["Torneo_ID"];
		}		
	}
	
	$actual = 'N';
	
	if($tournamentActual == 1){
		$actual = 'S';
	}
		
	$retunData = array('status' => '0', 'message' => 'No insert.', 'dataTournamentAnswer' => 'Error');
		
	$sql1 = "CALL $schema.TournamentUpdate('" . $_SESSION[$Config->getAlias() . 'username'] . "', $tournamentid, '$tournamentName', '$actual', $tournamentInscr, $tournamentVs, $tournamentWeeks, @out);";
	//echo $sql1;
	$Connection = $Config->connectAdmin();
	$result = $Connection->query($sql1);

	$siteRoot = defined('APP_SITE_ROOT') ? rtrim((string) APP_SITE_ROOT, '/\\') : dirname(__DIR__, 4) . DIRECTORY_SEPARATOR . 'voleibalmetepec';
	$imagenesDir = $siteRoot . DIRECTORY_SEPARATOR . 'imagenes';
	if (is_dir($imagenesDir)) {
		$salida = tournamentsManagementCopyRenameTourImages($imagenesDir, (int) $prevTourID, (int) $newTourID);
	} else {
		$salida = 'imagenes not found: ' . $imagenesDir;
	}

	$sql2 = "Select @out as 'count'";
	$result = $Connection->query($sql2);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row2 = $result->fetch_assoc()) {
			$retunData = array('status' => '1', 'message' => 'Success.', 'dataTournamentAnswer' => $lang['757'], 'sql1' => $sql1, 'sql2' => $sql2, 'out' => $salida);
		}
	}
	$Connection->Close();
    echo json_encode($retunData);
?>