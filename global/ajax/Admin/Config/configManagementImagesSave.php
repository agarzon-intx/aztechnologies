<?php
	session_start();

	header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');

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
	$sessionstat = $fgmembersite->CheckLogin('configManagementImagesSave.php');

	include('lang.' . $_COOKIE[$Config->getAlias() . 'language'] . '.php');

	$targetsFn = require __DIR__ . '/configManagementImagesTargets.php';
	$allowed = $targetsFn()['allowed'];

	$retunData = array('status' => '0', 'message' => 'No insert.', 'dataConfigAnswer' => 'Error');

	$basePath = rtrim($Config->getPath(), '/\\');

	if (!function_exists('imagecreatefromstring') || !function_exists('imagepng')) {
		$retunData['dataConfigAnswer'] = 'PHP GD (imagecreatefromstring, imagepng) is required for image uploads.';
		echo json_encode($retunData);
		exit;
	}

	$detectMime = function ($tmp) {
		if (function_exists('finfo_open')) {
			$f = finfo_open(FILEINFO_MIME_TYPE);
			if ($f) {
				$m = finfo_file($f, $tmp);
				finfo_close($f);
				return $m ? strtolower($m) : '';
			}
		}
		return '';
	};

	/**
	 * @param string $mime may be empty if finfo unavailable
	 */
	$rasterMimeOk = function ($mime) {
		if ($mime === '') {
			return true;
		}
		$ok = array('image/png', 'image/jpeg', 'image/pjpeg', 'image/gif', 'image/webp', 'image/x-png');
		return in_array($mime, $ok, true);
	};

	/**
	 * Decode upload, re-encode as PNG (all config image destinations use .png).
	 * @return string empty on success, error message otherwise
	 */
	$normalizeUploadToDest = function ($tmpPath, $destPath) {
		$bin = @file_get_contents($tmpPath);
		if ($bin === false || $bin === '') {
			return 'read failed';
		}
		$im = @imagecreatefromstring($bin);
		if ($im === false) {
			return 'unsupported or corrupt image';
		}
		if (function_exists('imagepalettetotruecolor') && !imageistruecolor($im)) {
			imagepalettetotruecolor($im);
		}
		$low = strtolower($destPath);
		if (substr($low, -4) !== '.png') {
			imagedestroy($im);
			return 'destination must be .png';
		}
		if (function_exists('imagealphablending')) {
			imagealphablending($im, false);
		}
		if (function_exists('imagesavealpha')) {
			imagesavealpha($im, true);
		}
		$ok = @imagepng($im, $destPath, 6);
		imagedestroy($im);
		return $ok ? '' : 'write png failed';
	};

	$saved = 0;
	$errors = array();

	foreach ($allowed as $field => $rel) {
		if (!isset($_FILES[$field]) || !is_array($_FILES[$field])) {
			continue;
		}
		if ((int)$_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
			continue;
		}
		if ((int)$_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
			$errors[] = $field . ': upload error ' . (int)$_FILES[$field]['error'];
			continue;
		}

		$tmp = $_FILES[$field]['tmp_name'];
		if (!is_uploaded_file($tmp)) {
			$errors[] = $field . ': invalid upload';
			continue;
		}

		$mime = $detectMime($tmp);
		if (!$rasterMimeOk($mime)) {
			$errors[] = $field . ': bad type ' . $mime;
			continue;
		}

		$dest = $basePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
		$dir = dirname($dest);
		if (!is_dir($dir)) {
			if (!@mkdir($dir, 0775, true)) {
				$errors[] = $field . ': cannot create dir';
				continue;
			}
		}

		$err = $normalizeUploadToDest($tmp, $dest);
		if ($err !== '') {
			$errors[] = $field . ': ' . $err;
			continue;
		}
		$saved++;
	}

	if (count($errors) > 0) {
		$retunData['dataConfigAnswer'] = implode('; ', $errors);
		echo json_encode($retunData);
		exit;
	}

	if ($saved === 0) {
		$retunData['dataConfigAnswer'] = $lang['452-8'];
		echo json_encode($retunData);
		exit;
	}

	$retunData = array('status' => '1', 'message' => 'Success.', 'dataConfigAnswer' => $lang['441'], 'saved' => $saved);
	echo json_encode($retunData);
