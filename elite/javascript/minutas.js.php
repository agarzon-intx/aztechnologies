<?php
	if (!defined('APP_SITE_ROOT')) {
		$___d = __DIR__;
		while ($___d !== dirname($___d)) {
			$___p = $___d . DIRECTORY_SEPARATOR . 'site_paths.php';
			if (is_readable($___p)) {
				require_once $___p;
				break;
			}
			$___d = dirname($___d);
		}
	}
	require("membersite_config.php");
	$sessionstat = $fgmembersite->CheckLogin('minutas.js.php');
	include('../languages/lang.'.$_SESSION['lang'].'.php');
	Header("content-type: application/x-javascript");

	echo "window.onload = function (){
			Resize();
		}

		function limpiarA(){
			$('#cambio').hide();
			$('#all').show();
			document.getElementById(\"Minuta\").hidden = \"true\";
		}		
		
		
		function validateA(){
			return true;
		}

		function readURLF(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#Minuta').attr('src', e.target.result)
					$('#Minuta').attr('height', '300')
					$('#Minuta').attr('width', '200')
                };
                reader.readAsDataURL(input.files[0]);
                $('#Minuta').show();
            }
        }";
?>