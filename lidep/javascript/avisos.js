<?php
	require("../login/include/membersite_config.php");
	include ("../Config.php");
	$sessionstat = $fgmembersite->CheckLogin();
	include('../languages/lang.'.$_SESSION['lang'].'.php');
	Header("content-type: application/x-javascript");

	echo "window.onload = function (){
			var descripcion = document.getElementById(\"Titulo\");
			var bsubmit = document.getElementById(\"submit\");
			var blimpiar = document.getElementById(\"limpiar\");
			var bsubmit2 = document.getElementById(\"submit2\");
			var blimpiar2 = document.getElementById(\"limpiar2\");
			if(descripcion.value.length > 0){
				bsubmit.hidden = true;
				blimpiar.hidden = false;
				document.getElementById(\"Inicio\").disabled = true;
				document.getElementById(\"Fin\").disabled = true;
				document.getElementById(\"Titulo\").disabled = true;
				document.getElementById(\"Tipo\").disabled = true;
				document.getElementById(\"editor\").disabled = true;
			}else{
				bsubmit.hidden = false;
				blimpiar.hidden = true;
			}
			var str = document.getElementById('error2').innerHTML.trim();
			console.log(str);
			if(str == \"" . $lang['409'] . "\"){
				bsubmit2.hidden = true;
				blimpiar2.hidden = false;
				document.getElementById(\"Inicio2\").disabled = true;
				document.getElementById(\"Fin2\").disabled = true;
				document.getElementById(\"Titulo2\").disabled = true;
				document.getElementById(\"Tipo2\").disabled = true;
				document.getElementById(\"editor2\").disabled = true;
			}else{
				bsubmit2.hidden = false;
				blimpiar2.hidden = true;
			}
			Resize();
		}

		function validate(){
			return true;
		}
		
		function limpiar(){
			document.getElementById(\"avisoid\").disabled = false;
			document.getElementById(\"Inicio\").disabled = false;
			document.getElementById(\"Fin\").disabled = false;
			document.getElementById(\"Titulo\").disabled = false;
			document.getElementById(\"Tipo\").disabled = false;
			document.getElementById(\"editor\").disabled = false;

			document.getElementById(\"avisoid\").value = \"\";
			document.getElementById(\"Inicio\").value = \"\";
			document.getElementById(\"Fin\").value = \"\";
			document.getElementById(\"Titulo\").value = \"\";
			document.getElementById(\"Tipo\").value = \"\";
			document.getElementById(\"editor\").innerHTML = \"\";
			var bsubmit = document.getElementById(\"submit\");
			var blimpiar = document.getElementById(\"limpiar\");
			bsubmit.hidden = false;
			blimpiar.hidden = true;
			$('#alta').hide();
			$('#cambio').hide();
			$('#all').show();
		}
		
		function limpiarA(){
			document.getElementById(\"avisoid2\").disabled = false;
			document.getElementById(\"Inicio2\").disabled = false;
			document.getElementById(\"Fin2\").disabled = false;
			document.getElementById(\"Titulo2\").disabled = false;
			document.getElementById(\"Tipo2\").disabled = false;
			document.getElementById(\"editor2\").disabled = false;

			document.getElementById(\"avisoid2\").value = \"\";
			document.getElementById(\"Inicio2\").value = \"\";
			document.getElementById(\"Fin2\").value = \"\";
			document.getElementById(\"Titulo2\").value = \"\";
			document.getElementById(\"Tipo2\").value = \"\";
			document.getElementById(\"editor2\").innerHTML = \"\";
			var bsubmit = document.getElementById(\"submit2\");
			var blimpiar = document.getElementById(\"limpiar2\");
			bsubmit.hidden = false;
			blimpiar.hidden = true;
			$('#alta').hide();
			$('#cambio').hide();
			$('#all').show();
			document.getElementById(\"avisoid\").value = \"0\";
		}		
		
		
		function validateA(){
			return true;
		}"
		
?>
