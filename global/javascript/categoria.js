
		window.onload = function (){
			var descripcion = document.getElementById("descripcion");
			var bsubmit = document.getElementById("submit");
			var blimpiar = document.getElementById("limpiar");
			var bsubmit2 = document.getElementById("submit2");
			var blimpiar2 = document.getElementById("limpiar2");
			if(descripcion.value.length > 0){
				bsubmit.hidden = true;
				blimpiar.hidden = false;
				document.getElementById("descripcion").disabled = true;
				document.getElementById("orden").disabled = true;
				document.getElementById("Inicial").disabled = true;
				document.getElementById("Final").disabled = true;
				document.getElementById("Color").disabled = true;
			}else{
				bsubmit.hidden = false;
				blimpiar.hidden = true;
			}
			var str = document.getElementById('error2').innerHTML.trim();
			console.log(str);
			if(str == "Categoria Actualizado"){
				bsubmit2.hidden = true;
				blimpiar2.hidden = false;
				document.getElementById("descripcion2").disabled = true;
				document.getElementById("orden2").disabled = true;
				document.getElementById("Inicial2").disabled = true;
				document.getElementById("Final2").disabled = true;
				document.getElementById("Color2").disabled = true;
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
			document.getElementById("categoriaid").disabled = false;
			document.getElementById("descripcion").disabled = false;
			document.getElementById("orden").disabled = false;
			document.getElementById("Inicial").disabled = false;
			document.getElementById("Final").disabled = false;
			document.getElementById("Color").disabled = false;

			document.getElementById("categoriaid").value = "";
			document.getElementById("descripcion").value = "";
			document.getElementById("orden").value = "";
			document.getElementById("Inicial").value = "";
			document.getElementById("Final").value = "";
			var bsubmit = document.getElementById("submit");
			var blimpiar = document.getElementById("limpiar");
			bsubmit.hidden = false;
			blimpiar.hidden = true;
			$('#alta').hide();
			$('#cambio').hide();
			$('#all').show();
		}
		
		function limpiarA(){
			document.getElementById("categoriaid2").disabled = false;
			document.getElementById("descripcion2").disabled = false;
			document.getElementById("orden2").disabled = false;
			document.getElementById("Inicial2").disabled = false;
			document.getElementById("Final2").disabled = false;

			document.getElementById("categoriaid2").value = "";
			document.getElementById("descripcion2").value = "";
			document.getElementById("orden2").value = "";
			document.getElementById("Inicial2").value = "";
			document.getElementById("Final2").value = "";
			var bsubmit = document.getElementById("submit2");
			var blimpiar = document.getElementById("limpiar2");
			bsubmit.hidden = false;
			blimpiar.hidden = true;
			$('#alta').hide();
			$('#cambio').hide();
			$('#all').show();
			document.getElementById("categoriaid").value = "0";
		}		
		
		
		function validateA(){
			return true;
		}
