<?php
	session_start();

	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Pragma: no-cache");
	header('Content-Type: application/json; charset=utf-8');

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

	require('membersite_config.php');
	$sessionstat = $fgmembersite->CheckLogin('configManagementFooterSave.php');

	include('lang.' . $_COOKIE[$Config->getAlias() . 'language'] . '.php');
	require_once dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'footer_links.php';
	require_once dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'role_menu.inc.php';

	$t = function ($key, $fallback) use ($lang) {
		return isset($lang[$key]) ? $lang[$key] : $fallback;
	};
	$fail = function ($msg) {
		while (ob_get_level() > 0) {
			ob_end_clean();
		}
		echo json_encode(array('status' => '0', 'dataConfigAnswer' => $msg));
		exit;
	};

	$alias = $Config->getAlias();
	if (!$sessionstat || !isset($_SESSION[$alias . 'equipo'])
		|| ((string) $_SESSION[$alias . 'equipo'] !== '0' && !az_user_is_admin($Config, $Config->getSchema(), $_SESSION[$alias . 'username'] ?? ''))) {
		$fail($t('js0002', 'Error'));
	}
	if (!az_footer_tables_exist($Config)) {
		$fail($t('452-129', 'The footer tables are not installed for this site.'));
	}

	$section = isset($_POST['section']) ? (string) $_POST['section'] : '';
	$items = json_decode(isset($_POST['items']) ? (string) $_POST['items'] : '', true);
	if (!in_array($section, array('logos', 'social'), true) || !is_array($items) || count($items) > 30) {
		$fail($t('js0002', 'Error'));
	}

	$conn = $Config->connectAdmin();
	if (!$conn) {
		$fail($t('js0002', 'Error'));
	}
	$conn->set_charset('utf8');
	$schema = '`' . $conn->real_escape_string($Config->getSchema()) . '`';
	$cut = function ($s, $len) {
		return function_exists('mb_substr') ? mb_substr(trim((string) $s), 0, $len, 'UTF-8') : substr(trim((string) $s), 0, $len);
	};

	$errors = array();
	$rows = array();

	if ($section === 'social') {
		$types = az_footer_social_types();
		foreach (array_values($items) as $i => $it) {
			$n = $i + 1;
			$type = isset($it['type']) ? (string) $it['type'] : '';
			$name = $cut(isset($it['alias']) ? $it['alias'] : '', 100);
			$url = az_footer_clean_url(isset($it['url']) ? $it['url'] : '');
			if (!isset($types[$type])) {
				$errors[] = $n . '. ' . $t('452-120', 'Choose the type.');
			}
			if ($name === '') {
				$errors[] = $n . '. ' . $t('452-121', 'Enter the alias.');
			}
			if ($url === '') {
				$errors[] = $n . '. ' . $t('452-122', 'Enter a valid URL (https://…).');
			}
			$rows[] = array($type, $name, $url);
		}
		if ($errors) {
			$fail(implode("\n", $errors));
		}

		$conn->begin_transaction();
		$ok = $conn->query("DELETE FROM $schema.RedesSociales") !== false;
		$stmt = $ok ? $conn->prepare("INSERT INTO $schema.RedesSociales (Orden, Tipo, Alias, Url) VALUES (?, ?, ?, ?)") : false;
		foreach ($rows as $i => $r) {
			if (!$stmt) {
				$ok = false;
				break;
			}
			$order = $i + 1;
			$stmt->bind_param('isss', $order, $r[0], $r[1], $r[2]);
			$ok = $stmt->execute() && $ok;
		}
		if ($stmt) {
			$stmt->close();
		}
		$ok ? $conn->commit() : $conn->rollback();
		echo json_encode(array('status' => $ok ? '1' : '0', 'dataConfigAnswer' => $ok ? $t('452-128', 'Saved.') : $t('js0002', 'Error'), 'items' => az_footer_social($Config)));
		exit;
	}

	// Logos
	$existing = array();
	$r = $conn->query("SELECT Logo_ID FROM $schema.FooterLogos");
	while ($r && ($row = $r->fetch_assoc())) {
		$existing[(int) $row['Logo_ID']] = true;
	}
	$mimes = array(IMAGETYPE_PNG => 'image/png', IMAGETYPE_JPEG => 'image/jpeg', IMAGETYPE_GIF => 'image/gif');
	if (defined('IMAGETYPE_WEBP')) {
		$mimes[IMAGETYPE_WEBP] = 'image/webp';
	}
	foreach (array_values($items) as $i => $it) {
		$n = $i + 1;
		$id = isset($it['id']) ? (int) $it['id'] : 0;
		$name = $cut(isset($it['alias']) ? $it['alias'] : '', 100);
		$rawUrl = isset($it['url']) ? trim((string) $it['url']) : '';
		$url = az_footer_clean_url($rawUrl);
		$width = isset($it['width']) ? max(0, min(400, (int) $it['width'])) : 0;
		$fileKey = isset($it['file']) ? (string) $it['file'] : '';
		$img = null;
		$mime = null;

		if ($id > 0 && !isset($existing[$id])) {
			$id = 0;
		}
		if ($name === '') {
			$errors[] = $n . '. ' . $t('452-121', 'Enter the alias.');
		}
		if ($rawUrl !== '' && $url === '') {
			$errors[] = $n . '. ' . $t('452-122', 'Enter a valid URL (https://…).');
		}
		if ($fileKey !== '' && preg_match('/^logoFile\d+$/', $fileKey) && isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] !== UPLOAD_ERR_NO_FILE) {
			$f = $_FILES[$fileKey];
			$info = ($f['error'] === UPLOAD_ERR_OK && is_uploaded_file($f['tmp_name'])) ? @getimagesize($f['tmp_name']) : false;
			if ($f['error'] === UPLOAD_ERR_INI_SIZE || $f['error'] === UPLOAD_ERR_FORM_SIZE || (int) $f['size'] > 2 * 1024 * 1024) {
				$errors[] = $n . '. ' . $t('452-124', 'The image must be 2 MB or less.');
			} else if (!$info || !isset($mimes[$info[2]])) {
				$errors[] = $n . '. ' . $t('452-125', 'The image must be PNG, JPG, GIF or WEBP.');
			} else {
				$img = file_get_contents($f['tmp_name']);
				$mime = $mimes[$info[2]];
			}
		} else if ($id === 0) {
			$errors[] = $n . '. ' . $t('452-123', 'Choose the image.');
		}
		$rows[] = array('id' => $id, 'alias' => $name, 'url' => $url, 'width' => $width, 'img' => $img, 'mime' => $mime);
	}
	if ($errors) {
		$fail(implode("\n", $errors));
	}

	$conn->begin_transaction();
	$ok = true;
	$keep = array();
	foreach ($rows as $row) {
		if ($row['id'] > 0) {
			$keep[] = $row['id'];
		}
	}
	$ok = $conn->query("DELETE FROM $schema.FooterLogos" . ($keep ? " WHERE Logo_ID NOT IN (" . implode(',', array_map('intval', $keep)) . ")" : '')) !== false;
	foreach ($rows as $i => $row) {
		if (!$ok) {
			break;
		}
		$order = $i + 1;
		$null = null;
		if ($row['id'] > 0 && $row['img'] === null) {
			$stmt = $conn->prepare("UPDATE $schema.FooterLogos SET Orden = ?, Alias = ?, Url = ?, Ancho = ? WHERE Logo_ID = ?");
			$ok = $stmt && $stmt->bind_param('issii', $order, $row['alias'], $row['url'], $row['width'], $row['id']) && $stmt->execute();
		} else if ($row['id'] > 0) {
			$stmt = $conn->prepare("UPDATE $schema.FooterLogos SET Orden = ?, Alias = ?, Url = ?, Ancho = ?, Imagen = ?, Mime = ? WHERE Logo_ID = ?");
			$ok = $stmt && $stmt->bind_param('issibsi', $order, $row['alias'], $row['url'], $row['width'], $null, $row['mime'], $row['id'])
				&& $stmt->send_long_data(4, $row['img']) && $stmt->execute();
		} else {
			$stmt = $conn->prepare("INSERT INTO $schema.FooterLogos (Orden, Alias, Url, Ancho, Imagen, Mime) VALUES (?, ?, ?, ?, ?, ?)");
			$ok = $stmt && $stmt->bind_param('issibs', $order, $row['alias'], $row['url'], $row['width'], $null, $row['mime'])
				&& $stmt->send_long_data(4, $row['img']) && $stmt->execute();
		}
		if ($stmt) {
			$stmt->close();
		}
	}
	$ok ? $conn->commit() : $conn->rollback();
	echo json_encode(array('status' => $ok ? '1' : '0', 'dataConfigAnswer' => $ok ? $t('452-128', 'Saved.') : $t('js0002', 'Error'), 'items' => az_footer_logos($Config)));
