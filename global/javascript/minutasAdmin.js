
		window.onload = function (){
			var descripcion = document.getElementById("Titulo");
			var bsubmit = document.getElementById("submit");
			var blimpiar = document.getElementById("limpiar");
			var bsubmit2 = document.getElementById("submit2");
			var blimpiar2 = document.getElementById("limpiar2");
			var str = document.getElementById('error').innerHTML.trim();
			if(str == "Minuta Guardada"){
				bsubmit.hidden = true;
				blimpiar.hidden = false;
				document.getElementById("Fecha").disabled = true;
				document.getElementById("Titulo").disabled = true;
				document.getElementById("editor").disabled = true;
			}else{
				bsubmit.hidden = false;
				blimpiar.hidden = true;
			}
			var str2 = document.getElementById('error2').innerHTML.trim();
			if(str2 == "Minuta Actualizada"){
				bsubmit2.hidden = true;
				blimpiar2.hidden = false;
				document.getElementById("Fecha2").disabled = true;
				document.getElementById("Titulo2").disabled = true;
				document.getElementById("editor2").disabled = true;
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
			document.getElementById("minutaid").disabled = false;
			document.getElementById("Fecha").disabled = false;
			document.getElementById("Titulo").disabled = false;
			document.getElementById("editor").disabled = false;

			document.getElementById("minutaid").value = "";
			document.getElementById("Fecha").value = "";
			document.getElementById("Titulo").value = "";
			document.getElementById("editor").innerHTML = "";
			var bsubmit = document.getElementById("submit");
			var blimpiar = document.getElementById("limpiar");
			bsubmit.hidden = false;
			blimpiar.hidden = true;
			$('#alta').hide();
			$('#cambio').hide();
			$('#all').show();
		}
		
		function limpiarA(){
			document.getElementById("minutaid2").disabled = false;
			document.getElementById("Fecha2").disabled = false;
			document.getElementById("Titulo2").disabled = false;
			document.getElementById("editor2").disabled = false;

			document.getElementById("minutaid2").value = "";
			document.getElementById("Fecha2").value = "";
			document.getElementById("Titulo2").value = "";
			document.getElementById("editor2").innerHTML = "";
			var bsubmit = document.getElementById("submit2");
			var blimpiar = document.getElementById("limpiar2");
			bsubmit.hidden = false;
			blimpiar.hidden = true;
			$('#alta').hide();
			$('#cambio').hide();
			$('#all').show();
			document.getElementById("minutaid").value = "0";
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
        }
		
		function readURLF2(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#Minuta2').attr('src', e.target.result)
					$('#Minuta2').attr('height', '300')
					$('#Minuta2').attr('width', '200')
                };
                reader.readAsDataURL(input.files[0]);
                $('#Minuta2').show();
            }
        }