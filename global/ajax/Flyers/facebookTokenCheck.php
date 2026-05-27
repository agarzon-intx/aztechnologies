<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

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

require_once dirname(__DIR__, 2) . '/include/membersite_config.php';
require_once dirname(__DIR__, 2) . '/include/flyer_facebook_publish.php';

$retunData = array('status' => '0', 'message' => 'Unauthorized');

try {
	$fgmembersite->CheckLogin('facebookTokenCheck.php');
	$sitePath = $Config->getPath();
	$fb = az_flyer_facebook_read_config($sitePath);
	if ($fb === null) {
		throw new RuntimeException('Facebook is not configured (check .local/facebook.env or ini/config.ini).');
	}

	$pageId = $fb['page_id'];
	$token = $fb['access_token'];

	$me = az_flyer_facebook_graph_request('me?fields=id,name', $token);
	$tokenIdentityId = isset($me['id']) ? (string) $me['id'] : '';
	$tokenIdentityName = isset($me['name']) ? (string) $me['name'] : '';

	// Page access token: GET /me returns the Page (id + name). User token: /me returns the user profile.
	$isPageToken = false;
	$publishTest = array('ok' => false, 'error' => '');
	if ($tokenIdentityId !== '') {
		try {
			az_flyer_facebook_graph_request($tokenIdentityId . '?fields=id,name', $token);
			$isPageToken = true;
			$publishTest['ok'] = true;
			$publishTest['note'] = 'Token can manage this Page. Publishing uses page_id from config; it must match this id for a Page token.';
		} catch (Throwable $e) {
			$publishTest['error'] = $e->getMessage();
		}
	}

	$idsMatch = ($isPageToken && $tokenIdentityId === $pageId);
	$message = '';
	if (!$isPageToken) {
		$message = 'This looks like a User token (or wrong token). In Graph API Explorer run GET /me/accounts and copy that Page access_token and id into .local/facebook.env.';
	} elseif ($idsMatch) {
		$message = 'OK: Page token matches configured page_id (' . $pageId . ').';
	} else {
		$message = 'Page token is for id ' . $tokenIdentityId . ' ("' . $tokenIdentityName . '") but config has page_id=' . $pageId . '. Update page_id in .local/facebook.env to ' . $tokenIdentityId . '.';
	}

	$retunData = array(
		'status' => '1',
		'message' => $message,
		'configured_page_id' => $pageId,
		'token_page_id' => $isPageToken ? $tokenIdentityId : '',
		'token_identity' => $me,
		'is_page_token' => $isPageToken,
		'ids_match' => $idsMatch,
		'page_read_test' => $publishTest,
		'help' => 'See tools/FACEBOOK-PAGE-PUBLISH-SETUP.md',
	);
} catch (Throwable $e) {
	$retunData = array('status' => '0', 'message' => $e->getMessage());
}

echo json_encode($retunData);
