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
	$sessionstat = $fgmembersite->CheckLogin('TeamsManagementMoveCategories.php');
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	$Season = (int) $_COOKIE[$Config->getAlias() . 'season'];
	$teamIdsRaw = isset($_POST['teamIds']) ? (string) $_POST['teamIds'] : '';
	$teamCount = 0;
	foreach (explode(',', $teamIdsRaw) as $part) {
		if ((int) trim($part) > 0) {
			$teamCount++;
		}
	}

	$retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');

	$title = isset($lang['522-2']) ? $lang['522-2'] : $lang['522-1'];
	$okLabel = $lang['0002'];
	$cancelLabel = $lang['0001'];
	$catLabel = $lang['519'];

	$html = '<div class="modal fade" id="teamsManagementMoveModal" tabindex="-1" aria-labelledby="teamsManagementMoveModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header py-2">
					<h5 class="modal-title" id="teamsManagementMoveModalLabel">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body py-2">
					<p class="text-secondary text-xs mb-2">' . htmlspecialchars($catLabel, ENT_QUOTES, 'UTF-8') . ' (' . (int) $teamCount . ')</p>
					<div class="list-group" id="teamsManagementMoveCategoryList">';

	$sql = "SELECT Categoria_ID, Categoria_Desc
			FROM $schema.Categorias
			WHERE Torneo_ID = $Season
			ORDER BY Categoria_Orden ASC, Categoria_Desc ASC";
	$result = $Config->query($sql);
	if ($result && $result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$cid = (int) $row['Categoria_ID'];
			$cdesc = htmlspecialchars((string) $row['Categoria_Desc'], ENT_QUOTES, 'UTF-8');
			$html .= '<label class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-2 mb-0">
						<input class="form-check-input teamsManagementMoveCategoryRadio mt-0" type="radio" name="teamsManagementMoveCategory" value="' . $cid . '"/>
						<span class="text-sm">' . $cdesc . '</span>
					</label>';
		}
	} else {
		$html .= '<div class="text-secondary text-sm">' . htmlspecialchars(isset($lang['522-4']) ? $lang['522-4'] : '', ENT_QUOTES, 'UTF-8') . '</div>';
	}

	$html .= '		</div>
				</div>
				<div class="modal-footer py-2">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">' . htmlspecialchars($cancelLabel, ENT_QUOTES, 'UTF-8') . '</button>
					<button type="button" class="btn btn-primary" id="teamsManagementMoveOkBtn" onClick="teamManagementMoveConfirm();">' . htmlspecialchars($okLabel, ENT_QUOTES, 'UTF-8') . '</button>
				</div>
			</div>
		</div>
	</div>';

	$retunData = array(
		'status' => '1',
		'message' => 'Success.',
		'dataMoveCategories' => $html,
	);
	echo json_encode($retunData);
?>
