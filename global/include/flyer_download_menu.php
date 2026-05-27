<?php
/**
 * Flyer download menus: PDF, PNG (ZIP), and Facebook share.
 */
if (!function_exists('az_flyer_lang')) {
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'flyer_facebook_share.php';
}

if (!function_exists('az_flyer_jornada_download_menu_html')) {

	function az_flyer_jornada_download_icon_pdf() {
		return '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" aria-hidden="true">'
			. '<path fill="#c62828" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 2l5 5h-5V4zM8 12h8v2H8v-2zm0 4h5v2H8v-2z"/>'
			. '<text x="7" y="11" font-size="5" font-weight="bold" fill="#fff">PDF</text></svg>';
	}

	function az_flyer_jornada_download_icon_zip() {
		return '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" aria-hidden="true">'
			. '<path fill="#1565c0" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 2l5 5h-5V4zM8 11h2v2H8v-2zm0 3h2v2H8v-2zm3-3h2v2h-2v-2zm0 3h2v2h-2v-2zm3-3h2v2h-2v-2zm0 3h2v2h-2v-2z"/>'
			. '</svg>';
	}

	function az_flyer_download_icon_facebook() {
		return '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" aria-hidden="true">'
			. '<path fill="#1877F2" d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.026 10.125 24v-8.385H7.078v-3.54h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.54h-2.796V24C19.612 23.026 24 18.1 24 12.073z"/>'
			. '</svg>';
	}

	/**
	 * Facebook share (one post with all flyer images).
	 *
	 * @param string $type jornada|categoria|juego
	 */
	function az_flyer_download_menu_item_facebook($type, $jornadaId, $categoriaId, $juegoId) {
		$jid = (int) $jornadaId;
		$cid = (int) $categoriaId;
		$gid = (int) $juegoId;
		$typeJs = htmlspecialchars($type, ENT_QUOTES, 'UTF-8');
		$html = '<li><a class="dropdown-item d-flex align-items-center gap-2 py-2 px-2 rounded" href="#"';
		$html .= ' onclick="azShareFlyersFacebook(\'' . $typeJs . '\',' . $jid . ',' . $cid . ',' . $gid . '); return false;">';
		$html .= az_flyer_download_icon_facebook();
		$html .= '<span class="small fw-semibold">' . htmlspecialchars(az_flyer_lang('jsfb01'), ENT_QUOTES, 'UTF-8') . '</span></a></li>';
		return $html;
	}

	/**
	 * @param int|string $jornadaId
	 * @param string $sitePath optional site root to detect flyerC-I.php
	 */
	function az_flyer_jornada_download_menu_html($jornadaId, $sitePath = '') {
		$jid = (int) $jornadaId;
		$menuId = 'flyerDlMenu' . $jid;
		$pdfUrl = 'pdf/flyerC.php?Jornada_ID=' . $jid;

		$pngSites = array('elite', 'huskies', 'lidep', 'nuestrodeporte', 'vollidep', 'voleibalmetepec');
		$showPngZip = true;
		if ($sitePath !== '') {
			$sitePathNorm = rtrim(str_replace('\\', '/', $sitePath), '/');
			$siteKey = basename($sitePathNorm);
			$pngScript = $sitePathNorm . '/pdf/flyerC-I.php';
			$showPngZip = in_array($siteKey, $pngSites, true) || is_readable($pngScript);
		}

		$html = '<div class="dropdown d-inline-flex align-items-center flex-shrink-0" style="vertical-align:middle;">';
		$html .= '<img src="imagenes/flyer.png" width="20" height="20" alt="Flyers" role="button"';
		$html .= ' id="' . htmlspecialchars($menuId, ENT_QUOTES, 'UTF-8') . '"';
		$html .= ' data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false"';
		$html .= ' style="cursor:pointer;" title="Descargar flyers">';
		$html .= '<ul class="dropdown-menu dropdown-menu-end shadow py-2 px-2"';
		$html .= ' aria-labelledby="' . htmlspecialchars($menuId, ENT_QUOTES, 'UTF-8') . '"';
		$html .= ' style="min-width:9rem;">';
		$html .= '<li><a class="dropdown-item d-flex align-items-center gap-2 py-2 px-2 rounded" href="'
			. htmlspecialchars($pdfUrl, ENT_QUOTES, 'UTF-8') . '" target="_blank" download>';
		$html .= az_flyer_jornada_download_icon_pdf();
		$html .= '<span class="small fw-semibold">PDF</span></a></li>';
		if ($showPngZip) {
			$zipUrl = 'pdf/flyerC-I.php?Jornada_ID=' . $jid;
			$html .= '<li><a class="dropdown-item d-flex align-items-center gap-2 py-2 px-2 rounded" href="'
				. htmlspecialchars($zipUrl, ENT_QUOTES, 'UTF-8') . '" target="_blank">';
			$html .= az_flyer_jornada_download_icon_zip();
			$html .= '<span class="small fw-semibold">PNG (ZIP)</span></a></li>';
		}
		$html .= az_flyer_download_menu_item_facebook('jornada', $jid, 0, 0);
		$html .= '</ul></div>';
		return $html;
	}

	/**
	 * Category flyer (flyerSC): PDF or PNG ZIP for a jornada + category.
	 *
	 * @param int|string $jornadaId
	 * @param int|string $categoriaId
	 * @param string $sitePath
	 * @param string $label button label
	 */
	function az_flyer_sc_category_download_menu_html($jornadaId, $categoriaId, $sitePath = '', $label = 'Flyer Categoria') {
		$jid = (int) $jornadaId;
		$cid = (int) $categoriaId;
		$menuId = 'flyerScDl' . $jid . '_' . $cid;
		$query = 'Jornada_ID=' . $jid . '&Categoria_ID=' . $cid;
		$pdfUrl = 'pdf/flyerSC.php?' . $query;

		$pngSites = array('elite', 'huskies', 'lidep', 'nuestrodeporte', 'vollidep', 'voleibalmetepec');
		$showPngZip = true;
		if ($sitePath !== '') {
			$sitePathNorm = rtrim(str_replace('\\', '/', $sitePath), '/');
			$siteKey = basename($sitePathNorm);
			$pngScript = $sitePathNorm . '/pdf/flyerSC-I.php';
			$showPngZip = in_array($siteKey, $pngSites, true) || is_readable($pngScript);
		}

		$html = '<div class="dropdown d-inline-block">';
		$html .= '<button type="button" class="btn btn-primary dropdown-toggle" id="' . htmlspecialchars($menuId, ENT_QUOTES, 'UTF-8') . '"';
		$html .= ' data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">';
		$html .= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</button>';
		$html .= '<ul class="dropdown-menu dropdown-menu-end shadow py-2 px-2"';
		$html .= ' aria-labelledby="' . htmlspecialchars($menuId, ENT_QUOTES, 'UTF-8') . '" style="min-width:9rem;">';
		$html .= '<li><a class="dropdown-item d-flex align-items-center gap-2 py-2 px-2 rounded" href="'
			. htmlspecialchars($pdfUrl, ENT_QUOTES, 'UTF-8') . '" target="_blank" download>';
		$html .= az_flyer_jornada_download_icon_pdf();
		$html .= '<span class="small fw-semibold">PDF</span></a></li>';
		if ($showPngZip) {
			$zipUrl = 'pdf/flyerSC-I.php?' . $query;
			$html .= '<li><a class="dropdown-item d-flex align-items-center gap-2 py-2 px-2 rounded" href="'
				. htmlspecialchars($zipUrl, ENT_QUOTES, 'UTF-8') . '" target="_blank">';
			$html .= az_flyer_jornada_download_icon_zip();
			$html .= '<span class="small fw-semibold">PNG (ZIP)</span></a></li>';
		}
		$html .= az_flyer_download_menu_item_facebook('categoria', $jid, $cid, 0);
		$html .= '</ul></div>';
		return $html;
	}

	/**
	 * Single-game flyer: PDF or PNG for one Juego_ID.
	 *
	 * @param int|string $juegoId
	 * @param string $sitePath
	 */
	function az_flyer_game_download_menu_html($juegoId, $sitePath = '') {
		$gid = (int) $juegoId;
		$menuId = 'flyerGameDl' . $gid;
		$query = 'Juego_ID=' . $gid;
		$pdfUrl = 'pdf/flyer.php?' . $query;

		$pngSites = array('elite', 'huskies', 'lidep', 'nuestrodeporte', 'vollidep', 'voleibalmetepec');
		$showPngZip = true;
		if ($sitePath !== '') {
			$sitePathNorm = rtrim(str_replace('\\', '/', $sitePath), '/');
			$siteKey = basename($sitePathNorm);
			$pngScript = $sitePathNorm . '/pdf/flyer-I.php';
			$showPngZip = in_array($siteKey, $pngSites, true) || is_readable($pngScript);
		}

		$html = '<div class="dropdown d-inline-flex align-items-center flex-shrink-0" style="vertical-align:middle;">';
		$html .= '<img src="imagenes/flyer.png" width="20" height="20" alt="Flyer" role="button"';
		$html .= ' id="' . htmlspecialchars($menuId, ENT_QUOTES, 'UTF-8') . '"';
		$html .= ' data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false"';
		$html .= ' style="cursor:pointer;" title="Descargar flyer">';
		$html .= '<ul class="dropdown-menu dropdown-menu-end shadow py-2 px-2"';
		$html .= ' aria-labelledby="' . htmlspecialchars($menuId, ENT_QUOTES, 'UTF-8') . '"';
		$html .= ' style="min-width:9rem;">';
		$html .= '<li><a class="dropdown-item d-flex align-items-center gap-2 py-2 px-2 rounded" href="'
			. htmlspecialchars($pdfUrl, ENT_QUOTES, 'UTF-8') . '" target="_blank" download>';
		$html .= az_flyer_jornada_download_icon_pdf();
		$html .= '<span class="small fw-semibold">PDF</span></a></li>';
		if ($showPngZip) {
			$zipUrl = 'pdf/flyer-I.php?' . $query;
			$html .= '<li><a class="dropdown-item d-flex align-items-center gap-2 py-2 px-2 rounded" href="'
				. htmlspecialchars($zipUrl, ENT_QUOTES, 'UTF-8') . '" target="_blank">';
			$html .= az_flyer_jornada_download_icon_zip();
			$html .= '<span class="small fw-semibold">PNG</span></a></li>';
		}
		$html .= az_flyer_download_menu_item_facebook('juego', 0, 0, $gid);
		$html .= '</ul></div>';
		return $html;
	}

	function az_flyer_week_actions_toolbar_html($selectedWeek, $sitePath = '') {
		$week = (int) $selectedWeek;
		$html = '<div class="d-flex flex-row flex-nowrap align-items-center justify-content-start" style="gap:8px;min-width:52px;">';
		$html .= '<img src="./imagenes/refresh.png" width="20" height="20" alt="" role="button"';
		$html .= ' onclick="loadWeek(' . $week . ');"';
		$html .= ' style="margin:0;flex-shrink:0;cursor:pointer;vertical-align:middle;">';
		$html .= az_flyer_jornada_download_menu_html($week, $sitePath);
		$html .= '</div>';
		return $html;
	}
}
