
		window.onload = function (){
			bsubmit2.hidden = true;
			document.getElementById("Fecha2").disabled = true;
			document.getElementById("Titulo2").disabled = true;
			document.getElementById("editor2").disabled = true;
			Resize();
		}

		function limpiarA(){
			$('#cambio').hide();
			$('#all').show();
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