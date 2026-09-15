<?php
	set_time_limit(300);
	require_once __DIR__ . '/site_paths.php';
	require("membersite_config.php");
	$fgmembersite->CheckLogin('QR.php');
	require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'global' . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'qr_player_profile_redirect.inc.php';
