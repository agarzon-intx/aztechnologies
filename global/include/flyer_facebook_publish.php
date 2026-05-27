<?php
/**
 * Publish flyer PNGs to a Facebook Page as photo posts (Graph API).
 *
 * Configure per site in ini/config.ini:
 * [facebook]
 * enabled = 1
 * page_id = YOUR_PAGE_ID
 * access_token = YOUR_PAGE_ACCESS_TOKEN
 *
 * Local dev (Laragon): optional overrides in repo .local/facebook.env (never commit).
 * Use a section per site key (elite, lidep, …) or [default] for all local sites.
 */
if (!function_exists('az_flyer_facebook_read_config')) {

	/**
	 * @return array<string,string>|null
	 */
	function az_flyer_facebook_repo_root_from_site($sitePath) {
		$sitePath = rtrim(str_replace('\\', '/', $sitePath), '/');
		if ($sitePath === '') {
			return null;
		}
		$dir = $sitePath;
		for ($i = 0; $i < 8; $i++) {
			$localDir = $dir . '/.local';
			if (is_dir($localDir)) {
				return $dir;
			}
			$parent = dirname($dir);
			if ($parent === $dir) {
				break;
			}
			$dir = $parent;
		}
		return null;
	}

	/**
	 * @return array<string,mixed>|null
	 */
	function az_flyer_facebook_read_local_env($sitePath) {
		$repoRoot = az_flyer_facebook_repo_root_from_site($sitePath);
		if ($repoRoot === null) {
			return null;
		}
		$envPath = $repoRoot . '/.local/facebook.env';
		if (!is_readable($envPath)) {
			return null;
		}
		$parsed = @parse_ini_file($envPath, true, INI_SCANNER_RAW);
		if ($parsed === false || !is_array($parsed)) {
			return null;
		}
		$siteKey = strtolower(basename(rtrim(str_replace('\\', '/', $sitePath), '/')));
		if (isset($parsed[$siteKey]) && is_array($parsed[$siteKey])) {
			return $parsed[$siteKey];
		}
		if (isset($parsed['default']) && is_array($parsed['default'])) {
			return $parsed['default'];
		}
		return null;
	}

	function az_flyer_facebook_truthy($value) {
		$s = strtolower(trim((string) $value));
		return $s !== '' && $s !== '0' && $s !== 'false' && $s !== 'no' && $s !== 'off';
	}

	function az_flyer_facebook_read_config($sitePath) {
		if ($sitePath === '') {
			return null;
		}
		$enabled = false;
		$pageId = '';
		$token = '';

		$iniPath = rtrim(str_replace('\\', '/', $sitePath), '/') . '/ini/config.ini';
		if (is_readable($iniPath)) {
			$parsed = @parse_ini_file($iniPath, true, INI_SCANNER_RAW);
			if ($parsed !== false && isset($parsed['facebook']) && is_array($parsed['facebook'])) {
				$fb = $parsed['facebook'];
				$enabled = az_flyer_facebook_truthy($fb['enabled'] ?? '');
				$pageId = trim((string) ($fb['page_id'] ?? ''));
				$token = trim((string) ($fb['access_token'] ?? ''));
			}
		}

		$local = az_flyer_facebook_read_local_env($sitePath);
		if (is_array($local)) {
			if (array_key_exists('enabled', $local)) {
				$enabled = az_flyer_facebook_truthy($local['enabled']);
			}
			if (!empty($local['page_id'])) {
				$pageId = trim((string) $local['page_id']);
			}
			if (!empty($local['access_token'])) {
				$token = trim((string) $local['access_token']);
			}
		}

		if (!$enabled || $pageId === '' || $token === '') {
			return null;
		}
		if (preg_match('/^YOUR_/i', $pageId) || preg_match('/^YOUR_/i', $token)) {
			return null;
		}
		return array(
			'enabled' => true,
			'page_id' => $pageId,
			'access_token' => $token,
		);
	}

	function az_flyer_facebook_is_configured($sitePath) {
		return az_flyer_facebook_read_config($sitePath) !== null;
	}

	/**
	 * @param array<string,mixed> $postFields
	 * @param array<string,string> $fileFields path => field name for CURLFile
	 * @return array<string,mixed>
	 */
	function az_flyer_facebook_graph_request($endpoint, $accessToken, array $postFields = array(), array $fileFields = array()) {
		$url = 'https://graph.facebook.com/v21.0/' . ltrim($endpoint, '/');
		$postFields['access_token'] = $accessToken;

		$ch = curl_init($url);
		if ($ch === false) {
			throw new RuntimeException('Could not initialize cURL.');
		}

		if (!empty($fileFields)) {
			foreach ($fileFields as $filePath => $fieldName) {
				if (!is_readable($filePath)) {
					throw new RuntimeException('Image file not readable: ' . basename($filePath));
				}
				$postFields[$fieldName] = new CURLFile($filePath, 'image/png', basename($filePath));
			}
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
		} else {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_TIMEOUT, 120);

		$body = curl_exec($ch);
		$errno = curl_errno($ch);
		$error = curl_error($ch);
		$httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($errno !== 0) {
			throw new RuntimeException('Facebook API connection error: ' . $error);
		}

		$decoded = json_decode((string) $body, true);
		if (!is_array($decoded)) {
			throw new RuntimeException('Invalid Facebook API response (HTTP ' . $httpCode . ').');
		}
		if (isset($decoded['error'])) {
			$msg = isset($decoded['error']['message']) ? $decoded['error']['message'] : 'Facebook API error';
			$code = isset($decoded['error']['code']) ? (int) $decoded['error']['code'] : 0;
			if ($code === 190 || stripos($msg, 'access token') !== false) {
				$msg .= ' Generate a new long-lived Page access token in Graph API Explorer.';
			} elseif ($code === 283 || stripos($msg, 'pages_manage_metadata') !== false) {
				$msg .= ' Add pages_manage_metadata to your Meta app (App Review), then regenerate the Page token with: pages_show_list, pages_read_engagement, pages_manage_posts, pages_manage_metadata. See tools/FACEBOOK-PAGE-PUBLISH-SETUP.md';
			} elseif (stripos($msg, 'pages_read_engagement') !== false) {
				$msg .= ' Your Meta app needs Advanced Access for pages_read_engagement, pages_manage_posts, pages_manage_metadata, and pages_show_list. See tools/FACEBOOK-PAGE-PUBLISH-SETUP.md';
			} elseif (stripos($msg, 'publish_actions') !== false) {
				$msg .= ' That permission is for personal profiles only. Use a Page access token from GET /me/accounts (Page id must match page_id in config). This app publishes via /{page-id}/feed.';
			} elseif ($code === 100 || stripos($msg, 'Parameters do not match') !== false) {
				$msg .= ' (Photo publish parameter format.) Retry after reload; if it persists, regenerate the Page token.';
			} elseif ($code === 200 || stripos($msg, 'permission') !== false || stripos($msg, 'capability') !== false) {
				$msg .= ' Use a Page access token from GET /me/accounts with pages_show_list, pages_read_engagement, pages_manage_posts, pages_manage_metadata. See tools/FACEBOOK-PAGE-PUBLISH-SETUP.md';
			}
			throw new RuntimeException($msg);
		}
		if ($httpCode >= 400) {
			throw new RuntimeException('Facebook API HTTP error ' . $httpCode);
		}

		return $decoded;
	}

	/**
	 * Graph API list params must be sent as a JSON string (not attached_media[0]=...).
	 *
	 * @param array<int,array<string,string>> $items
	 */
	function az_flyer_facebook_attached_media_param(array $items) {
		return json_encode($items);
	}

	/**
	 * @return array<string,mixed>
	 */
	function az_flyer_facebook_upload_page_photo($pageId, $accessToken, $pngPath, $publishNow = false) {
		$fields = array();
		if (!$publishNow) {
			$fields['published'] = 'false';
		}
		return az_flyer_facebook_graph_request(
			$pageId . '/photos',
			$accessToken,
			$fields,
			array($pngPath => 'source')
		);
	}

	/**
	 * @return string post id
	 */
	function az_flyer_facebook_publish_feed_with_photo($pageId, $accessToken, $mediaFbid, $message = '') {
		$feedFields = array(
			'attached_media' => az_flyer_facebook_attached_media_param(
				array(array('media_fbid' => $mediaFbid))
			),
		);
		if ($message !== '') {
			$feedFields['message'] = $message;
		}
		$result = az_flyer_facebook_graph_request($pageId . '/feed', $accessToken, $feedFields);
		if (!empty($result['id'])) {
			return (string) $result['id'];
		}
		if (!empty($result['post_id'])) {
			return (string) $result['post_id'];
		}
		throw new RuntimeException('Facebook did not return a post id from feed publish.');
	}

	/**
	 * @return string post id
	 */
	function az_flyer_facebook_publish_photo_direct($pageId, $accessToken, $pngPath, $message = '') {
		$fields = array();
		if ($message !== '') {
			$fields['message'] = $message;
		}
		$result = az_flyer_facebook_graph_request(
			$pageId . '/photos',
			$accessToken,
			$fields,
			array($pngPath => 'source')
		);
		if (!empty($result['post_id'])) {
			return (string) $result['post_id'];
		}
		if (!empty($result['id'])) {
			return (string) $result['id'];
		}
		throw new RuntimeException('Facebook did not return a post id from photo publish.');
	}

	/**
	 * One Page feed post with all images attached (multi-photo post).
	 *
	 * @param array<int,string> $pngPaths
	 * @return array{photo_count:int,post_ids:array<int,string>,page_url:string,first_post_url:string}
	 */
	function az_flyer_facebook_publish_page_feed_multi_photos($pageId, $accessToken, array $pngPaths, $message = '') {
		if (count($pngPaths) < 1) {
			throw new RuntimeException('No images to publish.');
		}
		if (count($pngPaths) === 1) {
			$postId = az_flyer_facebook_publish_photo_direct($pageId, $accessToken, $pngPaths[0], $message);
			$postUrl = 'https://www.facebook.com/' . rawurlencode($postId);
			return array(
				'photo_count' => 1,
				'post_ids' => array($postId),
				'page_url' => 'https://www.facebook.com/' . rawurlencode($pageId),
				'first_post_url' => $postUrl,
			);
		}

		$attached = array();
		foreach ($pngPaths as $pngPath) {
			$upload = az_flyer_facebook_upload_page_photo($pageId, $accessToken, $pngPath, false);
			if (empty($upload['id'])) {
				throw new RuntimeException('Facebook did not return a photo id after upload.');
			}
			$attached[] = array('media_fbid' => (string) $upload['id']);
		}

		$feedFields = array(
			'attached_media' => az_flyer_facebook_attached_media_param($attached),
		);
		$message = trim((string) $message);
		if ($message !== '') {
			$feedFields['message'] = $message;
		}
		$result = az_flyer_facebook_graph_request($pageId . '/feed', $accessToken, $feedFields);
		$postId = '';
		if (!empty($result['id'])) {
			$postId = (string) $result['id'];
		} elseif (!empty($result['post_id'])) {
			$postId = (string) $result['post_id'];
		}
		if ($postId === '') {
			throw new RuntimeException('Facebook did not return a post id for the multi-photo feed post.');
		}

		$postUrl = 'https://www.facebook.com/' . rawurlencode($postId);
		return array(
			'photo_count' => count($pngPaths),
			'post_ids' => array($postId),
			'page_url' => 'https://www.facebook.com/' . rawurlencode($pageId),
			'first_post_url' => $postUrl,
		);
	}

	/**
	 * Publish flyer PNGs on the Page feed (one post with all images when possible).
	 *
	 * @param array<int,string> $pngPaths
	 * @return array{photo_count:int,post_ids:array<int,string>,page_url:string,first_post_url:string}
	 */
	function az_flyer_facebook_publish_page_photos($pageId, $accessToken, array $pngPaths, $message = '') {
		try {
			return az_flyer_facebook_publish_page_feed_multi_photos($pageId, $accessToken, $pngPaths, $message);
		} catch (Throwable $e) {
			// Fallback: one post per image (older Graph behaviour on some tokens).
			$postIds = array();
			$caption = trim((string) $message);
			$index = 0;
			foreach ($pngPaths as $pngPath) {
				$postMessage = ($caption !== '' && $index === 0) ? $caption : '';
				$postIds[] = az_flyer_facebook_publish_photo_direct($pageId, $accessToken, $pngPath, $postMessage);
				$index++;
			}
			$pageUrl = 'https://www.facebook.com/' . rawurlencode($pageId);
			$firstPostUrl = !empty($postIds[0])
				? 'https://www.facebook.com/' . rawurlencode($postIds[0])
				: $pageUrl;
			return array(
				'photo_count' => count($pngPaths),
				'post_ids' => $postIds,
				'page_url' => $pageUrl,
				'first_post_url' => $firstPostUrl,
			);
		}
	}

	/**
	 * @deprecated Albums API is not available for Pages; delegates to publish_page_photos.
	 * @param array<int,string> $pngPaths
	 * @return array<string,mixed>
	 */
	function az_flyer_facebook_publish_album($pageId, $accessToken, array $pngPaths, $albumName, $message = '') {
		$text = trim((string) $message);
		if ($text === '' && trim((string) $albumName) !== '') {
			$text = (string) $albumName;
		}
		$result = az_flyer_facebook_publish_page_photos($pageId, $accessToken, $pngPaths, $text);
		return array(
			'album_id' => !empty($result['post_ids'][0]) ? $result['post_ids'][0] : '',
			'album_url' => $result['first_post_url'],
			'photo_count' => $result['photo_count'],
			'post_ids' => $result['post_ids'],
			'page_url' => $result['page_url'],
		);
	}
}
