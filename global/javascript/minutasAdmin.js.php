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
	$sessionstat = $fgmembersite->CheckLogin('minutasAdmin.js.php');
	
	include('lang.'.$_COOKIE[$Config->getAlias() . 'language'].'.php');

	Header("content-type: application/x-javascript");
	$__msg_ajax_generic = json_encode($lang['js0002'] ?? '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

	echo "
		var MSG_AJAX_GENERIC = " . $__msg_ajax_generic . ";
		function validateMemoCreate(){
			$('#editor').val(CKEDITOR.instances.editor.getData());
			var error = \"" . $lang['js0000'] . "\";
			var count = 0;
			if($('#Titulo').val().length == 0){
				count++;
				error = error + '\\n' + count +  '.- " . $lang['js710'] . "';
			}
			if($('#Fecha').val().length == 0){
				count++;
				error = error + '\\n' + count +  '.- " . $lang['js711'] . "';
			}
			if(count>0){
				alert(error);
				return false;
			}
			memoManagementCreateSave($('#Titulo').val(), $('#Fecha').val(), $('#editor').val(), $('#myMinutaFileName').val());
		}
		
		function validateMemoEdit(id){
			$('#editor').val(CKEDITOR.instances.editor.getData());
			var error = \"" . $lang['js0000'] . "\";
			var count = 0;
			if($('#Titulo').val().length == 0){
				count++;
				error = error + '\\n' + count +  '.- " . $lang['js710'] . "';
			}
			if($('#Fecha').val().length == 0){
				count++;
				error = error + '\\n' + count +  '.- " . $lang['js711'] . "';
			}
			if(count>0){
				alert(error);
				return false;
			}
			memoManagementEditSave(id, $('#Titulo').val(), $('#Fecha').val(), $('#editor').val(), $('#myMinutaFileName').val());
		}

		function readMinutaURL(input, Minuta) {
			var fd = new FormData();
			var files = $('#MyMinuta')[0].files;
			fd.append('MyMinuta',files[0]);
			$('#previewMyMinuta').html('');
			$('#previewMyMinuta').html('<img src=\"imagenes/loader.gif\" alt=\"Uploading....\" style=\"width: 150;\"/>');
			$.ajax({
				type: 'POST',
				enctype: 'multipart/form-data',
				dataType: 'json',
				url: 'objects/UploadMinuta.php',
				data: fd,
				contentType: false,
				processData: false,
				success: function (data) {
					if(data.status !== '1'){ 
						$('#Minuta').attr('src', ''); 
						$('#previewMyMinuta').html(data.alert);
					}
					if(data.status === '1'){ 
						$('#previewMyMinuta').html(data.alert); 
						$('#myMinutaFileName').val(data.action);
					} 
				},
				error: function(jqxhr, status, exception) {
					mainLoadingOff();
					alert(MSG_AJAX_GENERIC);
					console.log('Exception:' + exception);
				}
			});
			if (input.files && input.files[0]) {
				var reader = new FileReader();

				reader.onload = function (e) {
					$('#' + Minuta)
						.attr('src', e.target.result)
				};
				reader.readAsDataURL(input.files[0]);
				$('#' + Minuta).show();
			}
		}";
?>