<?php
session_start();
$__APP_SITE_PATHS_START__ = dirname(__DIR__);
require_once dirname(__DIR__) . '/include/app_site_paths.inc.php';
require 'membersite_config.php';
$a = $Config->getAlias();
$_SESSION[$a . 'username'] = 'footer-test';
$_SESSION[$a . 'equipo'] = '0';
$_SESSION[$a . 'LAST_ACTIVITY'] = time();
setcookie($a . 'language', 'es', 0, '/');
$_COOKIE[$a . 'language'] = 'es';
include 'lang.es.php';
$htmlConfig = '';
require dirname(__DIR__, 2) . '/global/ajax/Admin/Config/configManagementFooter.php';
?><!doctype html><html><head><meta charset="utf-8"><base href="/">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head><body><?php echo $htmlConfig; ?></body></html>
