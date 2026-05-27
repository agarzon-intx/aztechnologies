<?php
/**
 * Build flyer PNG bundle for Facebook share (JSON API + launch window).
 */
if (!function_exists('az_flyer_facebook_prepare_share_data')) {

	require_once __DIR__ . DIRECTORY_SEPARATOR . 'flyer_facebook_share.php';

	/**
	 * @return array{title:string,token:string,image_urls:array<int,string>,image_count:int,image_url:string,share_page_url:string}
	 */
	function az_flyer_facebook_prepare_share_data($Config, $type, $jornada, $categoria, $juego, array &$lang) {
		$type = strtolower(trim((string) $type));
		$jornada = (int) $jornada;
		$categoria = (int) $categoria;
		$juego = (int) $juego;

		$sitePath = $Config->getPath();
		$website = $Config->getWebSite();
		if (trim($website) === '') {
			throw new RuntimeException('Site website URL is not configured in ini/config.ini.');
		}

		az_flyer_share_cleanup_old($sitePath);

		$sitePdf = $sitePath . DIRECTORY_SEPARATOR . 'pdf';
		$flyerCiPage = $sitePdf . DIRECTORY_SEPARATOR . 'flyer_ci_page.php';
		if (!is_readable($flyerCiPage)) {
			throw new RuntimeException('Missing pdf/flyer_ci_page.php on this site.');
		}
		require_once $flyerCiPage;

		$alphapdfPath = $sitePdf . DIRECTORY_SEPARATOR . 'alphapdf.php';
		if (!is_readable($alphapdfPath)) {
			throw new RuntimeException('Missing pdf/alphapdf.php on this site.');
		}
		require_once $alphapdfPath;

		$siteRoot = az_pdf_site_root($Config);
		$Config->connect();
		$Config->LoadLogo();
		$Config->LoadFlags();

		$pdf = new FPDF('P', 'mm', array(210, 210));
		$pdf->AddFont('Coluna', 'B', 'Coluna.php');
		$pageLabels = array();
		$shareTitle = az_flyer_lang('jsfb22');
		$shareDescription = '';

		if ($type === 'jornada') {
			if ($jornada < 1) {
				throw new RuntimeException('Jornada_ID is required.');
			}
			$categoria = az_flyer_ci_categoria_from_cookie($Config);
			if ($categoria < 1) {
				throw new RuntimeException('Select a category before sharing flyers.');
			}
			$Config->query("SET lc_time_names = 'es_MX';");
			$sql = flyer_ci_juegos_sql($Config->getSchema(), $jornada, $categoria);
			$result1 = $Config->query($sql);
			if (!$result1 || $result1->num_rows < 1) {
				throw new RuntimeException('No games found for this week.');
			}
			if ($result1->num_rows > 80) {
				throw new RuntimeException('Too many games (' . $result1->num_rows . '). Maximum is 80.');
			}
			while ($row1 = $result1->fetch_assoc()) {
				$pageLabels[] = 'juego-' . $row1['Juego_ID'];
				flyer_ci_add_page($pdf, $siteRoot, $row1);
			}
			$shareTitle = az_flyer_lang('jsfb15', array($jornada));
			$shareDescription = az_flyer_lang('jsfb16', array($jornada));
		} elseif ($type === 'categoria') {
			if ($jornada < 1 || $categoria < 1) {
				throw new RuntimeException('Jornada_ID and Categoria_ID are required.');
			}
			flyer_sc_build_pdf($pdf, $Config, $Config->getSchema(), $jornada, $categoria, $siteRoot, $lang, $pageLabels);
			$shareTitle = az_flyer_lang('jsfb17', array($jornada, $categoria));
			$shareDescription = az_flyer_lang('jsfb18', array($categoria, $jornada));
		} elseif ($type === 'juego') {
			if ($juego < 1) {
				throw new RuntimeException('Juego_ID is required.');
			}
			flyer_game_build_pdf($pdf, $Config, $Config->getSchema(), $juego, $siteRoot, $lang, $pageLabels);
			$shareTitle = az_flyer_lang('jsfb19', array($juego));
			$shareDescription = az_flyer_lang('jsfb20', array($juego));
		} else {
			throw new RuntimeException('Invalid flyer type.');
		}

		if (count($pageLabels) < 1) {
			throw new RuntimeException('No flyer pages were generated.');
		}

		$generated = az_flyer_ci_generate_png_paths($pdf->Output('S'), $pageLabels, 200, $sitePath);
		$tmpDir = $generated['tmpDir'];
		$pngPaths = $generated['paths'];
		$pageCount = count($pngPaths);

		try {
			$token = az_flyer_share_register_bundle($sitePath, $shareTitle, $shareDescription, '', $pngPaths);
		} finally {
			if (isset($tmpDir, $pngPaths) && is_string($tmpDir) && $tmpDir !== '') {
				az_flyer_ci_cleanup_png_paths($tmpDir, is_array($pngPaths) ? $pngPaths : array());
			}
		}

		$urls = az_flyer_share_urls($website, $token, '');
		$imageUrls = az_flyer_share_image_urls($website, $token, $pageCount);

		return array(
			'title' => $shareTitle,
			'token' => $token,
			'image_urls' => $imageUrls,
			'image_count' => $pageCount,
			'image_url' => $urls['image_url'],
			'share_page_url' => $urls['share_page_url'],
		);
	}
}
