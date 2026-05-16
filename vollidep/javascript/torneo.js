		window.onload = function (){
			var descripcion = document.getElementById("descripcion");
			var jugadorid = document.getElementById("jugadorid");
			var bsubmit = document.getElementById("submit");
			var blimpiar = document.getElementById("limpiar");
			var bsubmit2 = document.getElementById("submit2");
			var blimpiar2 = document.getElementById("limpiar2");
			if(descripcion.value.length > 0){
				bsubmit.hidden = true;
				blimpiar.hidden = false;
				document.getElementById("descripcion").disabled = true;
				document.getElementById("actual").disabled = true;
				document.getElementById("inscripciones").disabled = true;
			}else{
				bsubmit.hidden = false;
				blimpiar.hidden = true;
			}
			var str = document.getElementById('error2').innerHTML.trim();
			if(str == "Torneo Actualizado"){
				bsubmit2.hidden = true;
				blimpiar2.hidden = false;
				document.getElementById("descripcion2").disabled = true;
				document.getElementById("actual2").disabled = true;
				document.getElementById("inscripciones2").disabled = true;
			}else{
				bsubmit2.hidden = false;
				blimpiar2.hidden = true;
			}
		}


		function validate(){
			var error = "Se encontraron los siguientes errores en el Formulario:";
			document.getElementById('error2').innerHTML = "";
			return true;
		}
		
		
		function limpiar(){
			document.getElementById("descripcion").disabled = false;
			document.getElementById("actual").disabled = false;
			document.getElementById("inscripciones").disabled = false;

			document.getElementById("torneoid").value = "";
			document.getElementById("descripcion").value = "";
			document.getElementById("actual").checked = false;
			document.getElementById("inscripciones").checked = false;
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
			document.getElementById("actual2").disabled = false;
			document.getElementById("inscripciones2").disabled = false;

			document.getElementById("torneoid2").value = "";
			document.getElementById("descripcion2").value = "";
			document.getElementById("actual2").checked = false;
			document.getElementById("inscripciones2").checked = false;
			var bsubmit = document.getElementById("submit2");
			var blimpiar = document.getElementById("limpiar2");
			bsubmit.hidden = false;
			blimpiar.hidden = true;
			$('#alta').hide();
			$('#cambio').hide();
			$('#all').show();
			document.getElementById("jugadorid").value = "0";
		}		
		
		
		function validateA(){
			document.getElementById('error2').innerHTML = "";
			document.getElementById("jugadorid").value = "1";
			return true;
		}
