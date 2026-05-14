        function loadScript() {
            var script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDYiDvsZGN5SeQjZIuwO1KwyW6BTkyuNBc&' +
                    'callback=initialize';
            document.body.appendChild(script);
        }

		window.onload = function (){
			loadScript();
			Resize();
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
				document.getElementById("latitud").disabled = true;
				document.getElementById("longitud").disabled = true;
				document.getElementById("zoom").disabled = true;
				document.getElementById("google").disabled = true;
			}else{
				bsubmit.hidden = false;
				blimpiar.hidden = true;
			}
			var str = document.getElementById('error2').innerHTML.trim();
			if(str == "Campo Actualizado"){
				bsubmit2.hidden = true;
				blimpiar2.hidden = false;
				document.getElementById("descripcion2").disabled = true;
				document.getElementById("latitud2").disabled = true;
				document.getElementById("longitud2").disabled = true;
				document.getElementById("zoom2").disabled = true;
				document.getElementById("google2").disabled = true;
			}else{
				bsubmit2.hidden = false;
				blimpiar2.hidden = true;
			}
		}

		function validate(){
			return true;
		}
		
		function limpiar(){
			Resize();
			document.getElementById("campoid").disabled = false;
			document.getElementById("descripcion").disabled = false;
			document.getElementById("longitud").disabled = false;
			document.getElementById("latitud").disabled = false;
			document.getElementById("zoom").disabled = false;
			document.getElementById("google").disabled = false;

			document.getElementById("campoid").value = "";
			document.getElementById("descripcion").value = "";
			document.getElementById("latitud").value = "";
			document.getElementById("longitud").value = "";
			document.getElementById("zoom").value = "";
			document.getElementById("google").value = "";
			var bsubmit = document.getElementById("submit");
			var blimpiar = document.getElementById("limpiar");
			bsubmit.hidden = false;
			blimpiar.hidden = true;
			$('#alta').hide();
			$('#cambio').hide();
			$('#all').show();
		}
		
		function limpiarA(){
			Resize();
			document.getElementById("campoid2").disabled = false;
			document.getElementById("descripcion2").disabled = false;
			document.getElementById("longitud2").disabled = false;
			document.getElementById("latitud2").disabled = false;
			document.getElementById("zoom2").disabled = false;
			document.getElementById("google2").disabled = false;

			document.getElementById("campoid2").value = "";
			document.getElementById("descripcion2").value = "";
			document.getElementById("latitud2").value = "";
			document.getElementById("longitud2").value = "";
			document.getElementById("zoom2").value = "";
			document.getElementById("google2").value = "";
			var bsubmit = document.getElementById("submit2");
			var blimpiar = document.getElementById("limpiar2");
			bsubmit.hidden = false;
			blimpiar.hidden = true;
			$('#alta').hide();
			$('#cambio').hide();
			$('#all').show();
			document.getElementById("campoid").value = "0";
		}		
		
		
		function validateA(){
			return true;
		}
