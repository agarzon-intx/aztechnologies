<?php
	session_start();

	header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');
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

	require('membersite_config.php');
	$sessionstat = $fgmembersite->CheckLogin('configManagementImagesSave.php');

	include('lang.' . $_COOKIE[$Config->getAlias() . 'language'] . '.php');

	$targetsFn = require __DIR__ . '/configManagementImagesTargets.php';
	$allowed = $targetsFn()['allowed'];

	$retunData = array('status' => '0', 'message' => 'No insert.', 'dataConfigAnswer' => 'Error');

	$basePath = rtrim((string) $Config->getPath(), '/\\');
	if ($basePath === '' || !is_dir($basePath)) {
		$retunData['dataConfigAnswer'] = 'Site path is not configured (ini [path]).';
		echo json_encode($retunData);
		exit;
	}

	$hasGd = function_exists('imagecreatefromstring') && function_exists('imagepng');
	$hasImagick = extension_loaded('imagick') && class_exists('Imagick');
	if (!$hasGd && !$hasImagick) {
		$retunData['dataConfigAnswer'] = 'PHP GD or Imagick is required to save images.';
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

	$rasterMimeOk = function ($mime) {
		if ($mime === '') {
			return true;
		}
		$ok = array('image/png', 'image/jpeg', 'image/pjpeg', 'image/gif', 'image/webp', 'image/x-png');
		return in_array($mime, $ok, true);
	};

	$removeLegacyVariants = function ($destPath) {
		if (!preg_match('/\.png$/i', $destPath)) {
			return;
		}
		$base = preg_replace('/\.png$/i', '', $destPath);
		foreach (array('jpeg', 'jpg', 'JPEG', 'JPG', 'gif', 'GIF') as $ext) {
			$old = $base . '.' . $ext;
			if (is_file($old)) {
				@unlink($old);
			}
		}
	};

	/**
	 * @return string empty on success, error message otherwise
	 */
	$normalizeUploadToDest = function ($tmpPath, $destPath) use ($hasGd, $hasImagick, $removeLegacyVariants) {
		if (strtolower(substr($destPath, -4)) !== '.png') {
			return 'destination must be .png';
		}
		if ($hasGd) {
			$bin = @file_get_contents($tmpPath);
			if ($bin === false || $bin === '') {
				return 'read failed';
			}
			$im = @imagecreatefromstring($bin);
			if ($im !== false) {
				if (function_exists('imagepalettetotruecolor') && !imageistruecolor($im)) {
					imagepalettetotruecolor($im);
				}
				if (function_exists('imagealphablending')) {
					imagealphablending($im, false);
				}
				if (function_exists('imagesavealpha')) {
					imagesavealpha($im, true);
				}
				$ok = @imagepng($im, $destPath, 6);
				imagedestroy($im);
				if ($ok) {
					$removeLegacyVariants($destPath);
					return '';
				}
			}
		}
		if ($hasImagick) {
			try {
				$im = new Imagick($tmpPath);
				$im->setImageFormat('png');
				if (method_exists($im, 'setImageBackgroundColor')) {
					$im->setImageBackgroundColor(new ImagickPixel('transparent'));
				}
				if (defined('Imagick::LAYERMETHOD_FLATTEN')) {
					$im = $im->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
				}
				$im->writeImage($destPath);
				$im->clear();
				$im->destroy();
				if (is_file($destPath) && filesize($destPath) > 0) {
					$removeLegacyVariants($destPath);
					return '';
				}
			} catch (Throwable $e) {
				return 'imagick: ' . $e->getMessage();
			}
		}
		return 'unsupported or corrupt image';
	};

	$uploadTmpOk = function ($tmp, $fileError) {
		if ((int) $fileError !== UPLOAD_ERR_OK) {
			return false;
		}
		if (is_uploaded_file($tmp)) {
			return true;
		}
		return is_readable($tmp) && filesize($tmp) > 0;
	};

	$saved = 0;
	$errors = array();

	foreach ($allowed as $field => $rel) {
		if (!isset($_FILES[$field]) || !is_array($_FILES[$field])) {
			continue;
		}
		if ((int) $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
			continue;
		}
		if ((int) $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
			$errors[] = $field . ': upload error ' . (int) $_FILES[$field]['error'];
			continue;
		}

		$tmp = $_FILES[$field]['tmp_name'];
		if (!$uploadTmpOk($tmp, $_FILES[$field]['error'])) {
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
		if (!is_writable($dir)) {
			$errors[] = $field . ': folder not writable';
			continue;
		}

		$err = $normalizeUploadToDest($tmp, $dest);
		if ($err !== '') {
			$errors[] = $field . ': ' . $err;
			continue;
		}
		$saved++;
	}

	if ($saved === 0) {
		$retunData['dataConfigAnswer'] = count($errors) > 0
			? implode('; ', $errors)
			: $lang['452-8'];
		echo json_encode($retunData);
		exit;
	}

	$msg = $lang['441'];
	if (count($errors) > 0) {
		$msg .= ' (' . implode('; ', $errors) . ')';
	}
	$retunData = array(
		'status' => '1',
		'message' => 'Success.',
		'dataConfigAnswer' => $msg,
		'saved' => $saved,
	);
	echo json_encode($retunData);
