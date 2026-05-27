		window.onload = function (){
			Resize();
			var descripcion = document.getElementById("descripcion");
			var jugadorid = document.getElementById("colorid");
			var bsubmit = document.getElementById("submit");
			var blimpiar = document.getElementById("limpiar");
			var bsubmit2 = document.getElementById("submit2");
			var blimpiar2 = document.getElementById("limpiar2");
			if(descripcion.value.length > 0){
				bsubmit.hidden = true;
				blimpiar.hidden = false;
				document.getElementById("descripcion").disabled = true;
				document.getElementById("colorEdit").disabled = true;
			}else{
				bsubmit.hidden = false;
				blimpiar.hidden = true;
			}
			var str = document.getElementById('error2').innerHTML.trim();
			if(str == "Color Actualizado"){
				bsubmit2.hidden = true;
				blimpiar2.hidden = false;
				document.getElementById("descripcion2").disabled = true;
				document.getElementById("colorEditA").disabled = true;
			}else{
				bsubmit2.hidden = false;
				blimpiar2.hidden = true;
			}
		}


		function validate(){
			var error = "Se encontraron los siguientes errores en el Formulario:";
			document.getElementById('error').innerHTML = "";
			return true;
		}
		
		
		function limpiar(){
			document.getElementById("descripcion").disabled = false;
			document.getElementById("colorEdit").disabled = false;

			document.getElementById("colorid").value = "";
			document.getElementById("descripcion").value = "";
			document.getElementById("colorEdit").value = "#FFFFFF";
			document.getElementById('colorMuestra').style.backgroundColor = "#FFFFFF";
            document.getElementById('colorTexto').innerHTML = "";			
			var bsubmit = document.getElementById("submit");
			var blimpiar = document.getElementById("limpiar");
			bsubmit.hidden = false;
			blimpiar.hidden = true;
			$('#alta').hide();
			$('#cambio').hide();
			$('#all').show();
		}
		
		function limpiarA(){
			document.getElementById("descripcion2").disabled = false;
			document.getElementById("colorEditA").disabled = false;

			document.getElementById("colorid2").value = "";
			document.getElementById("descripcion2").value = "";
			document.getElementById("colorEditA").value = "#FFFFFF";
			document.getElementById('colorMuestraA').style.backgroundColor = "#FFFFFF";
            document.getElementById('colorTextoA').innerHTML = "";			
			var bsubmit = document.getElementById("submit2");
			var blimpiar = document.getElementById("limpiar2");
			bsubmit.hidden = false;
			blimpiar.hidden = true;
			$('#alta').hide();
			$('#cambio').hide();
			$('#all').show();
		}		
		
		
		function validateA(){
			document.getElementById('error2').innerHTML = "";
			return true;
		}
