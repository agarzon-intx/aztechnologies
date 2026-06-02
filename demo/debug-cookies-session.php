<?php
require_once __DIR__ . '/site_paths.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}
require dirname(__DIR__) . '/global/include/debug-cookies-session.inc.php';
