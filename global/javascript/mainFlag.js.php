<?php
	session_start();
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

	error_reporting(0);
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

	require("membersite_config.php");
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('mainFlag.js.php');

	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	Header("content-type: application/x-javascript");
	$__msg_ajax_generic = json_encode($lang['js0002'] ?? '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

	echo "
		var MSG_AJAX_GENERIC = " . $__msg_ajax_generic . ";

		function abrirFichaFlag(id, week, game, gamedesc, lgoals, vgoals){
			var attr = $('#'+id).attr('style');
			$('.juego').css('display', 'none');
			$('#expandir'+id).attr('src', './imagenes/expandir.png');
			$('.expandirButton').attr('src', './imagenes/expandir.png');
			if (typeof attr == typeof undefined) {
				$('#'+id).css('display', 'none');
				$('#expandir'+id).attr('src', './imagenes/expandir.png');
				$(\"#content\" + id).html('');
			}else{
				$('#'+id).removeAttr('style');
				$('#expandir'+id).attr('src', './imagenes/colapsar.png');
				mainLoadingOn();
				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: 'ajax/Content/week-ScheduleScoresGameDetailFlag.php',
					data: {week: week, game: game, gamedesc: gamedesc, lgoals: lgoals, vgoals: vgoals},
					success: function (res) {
						mainLoadingOff();
						if (res.status === '1') {
							$(\"#content\" + id).html(res.dataWeekGameDetail);
						}
					},
					error: function(jqxhr, status, exception) {
						mainLoadingOff();
						alert(MSG_AJAX_GENERIC);
						console.log('Exception:' + exception);
					}
				});
			}
		}

		function abrirFichaSFlag(id, week, game, gamedesc, lgoals, vgoals){
			var attr = $('#'+id).attr('style');
			$('.juego').css('display', 'none');
			$('#expandir'+id).attr('src', './imagenes/expandir.png');
			$('.expandirButton').attr('src', './imagenes/expandir.png');
			if (typeof attr == typeof undefined) {
				$('#'+id).css('display', 'none');
				$('#expandir'+id).attr('src', './imagenes/expandir.png');
				$(\"#content\" + id).html('');
			}else{
				$('#'+id).removeAttr('style');
				$('#expandir'+id).attr('src', './imagenes/colapsar.png');
				mainLoadingOn();
				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: 'ajax/Content/week-ScheduleScoresGameDetailSFlag.php',
					data: {week: week, game: game, gamedesc: gamedesc, lgoals: lgoals, vgoals: vgoals},
					success: function (res) {
						mainLoadingOff();
						if (res.status === '1') {
							$(\"#content\" + id).html(res.dataWeekGameDetail);
						}
					},
					error: function(jqxhr, status, exception) {
						mainLoadingOff();
						alert(MSG_AJAX_GENERIC);
						console.log('Exception:' + exception);
					}
				});
			}
		}
	";
