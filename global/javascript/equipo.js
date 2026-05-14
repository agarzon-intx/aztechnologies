		window.onload = function (){
			Resize();
			var descripcion = document.getElementById("descripcion");
			var equipoid = document.getElementById("equipoid");
			var bsubmit = document.getElementById("submit");
			var blimpiar = document.getElementById("limpiar");
			var bsubmit2 = document.getElementById("submit2");
			var blimpiar2 = document.getElementById("limpiar2");
			if(descripcion.value.length > 0){
				bsubmit.hidden = true;
				blimpiar.hidden = false;
				document.getElementById("descripcion").disabled = true;
				document.getElementById("estatus").disabled = true;
				document.getElementById("fuerza").disabled = true;
				document.getElementById("logo").disabled = true;
				document.getElementById("descripcionLarga").disabled = true;
				document.getElementById("campo").disabled = true;
			}else{
				bsubmit.hidden = false;
				blimpiar.hidden = true;
			}
			var str = document.getElementById('error2').innerHTML.trim();
			if(str == "Equipo Actualizado"){
				bsubmit2.hidden = true;
				blimpiar2.hidden = false;
				document.getElementById("descripcion2").disabled = true;
				document.getElementById("estatus2").disabled = true;
				document.getElementById("fuerza2").disabled = true;
				document.getElementById("logo2").disabled = true;
				document.getElementById("descripcionLarga2").disabled = true;
				document.getElementById("campoA").disabled = true;
			}else{
				bsubmit2.hidden = false;
				blimpiar2.hidden = true;
			}
		}

		function validate(){
			return true;
		}
		
		function limpiar(){
			document.getElementById("equipoid").disabled = false;
			document.getElementById("descripcion").disabled = false;
			document.getElementById("estatus2").disabled = false;
			document.getElementById("fuerza").disabled = false;
			document.getElementById("logo").disabled = false;
			document.getElementById("descripcionLarga").disabled = false;
			document.getElementById("campo").disabled = false;

			document.getElementById("equipoid").value = "";
			document.getElementById("descripcion").value = "";
			document.getElementById("estatus2").checked = false;
			document.getElementById("fuerza").value = "";
			document.getElementById("logo").value = "";
			document.getElementById("descripcionLarga").value = "";
			document.getElementById("campo").value = "";
			var bsubmit = document.getElementById("submit");
			var blimpiar = document.getElementById("limpiar");
			bsubmit.hidden = false;
			blimpiar.hidden = true;
			$('#alta').hide();
			$('#cambio').hide();
			$('#all').show();
			Resize();
		}
		
		function limpiarA(){
			document.getElementById("equipoid2").disabled = false;
			document.getElementById("descripcion2").disabled = false;
			document.getElementById("estatus2").disabled = false;
			document.getElementById("fuerza2").disabled = false;
			document.getElementById("logo2").disabled = false;
			document.getElementById("descripcionLarga2").disabled = false;
			document.getElementById("campoA").disabled = false;

			document.getElementById("equipoid2").value = "";
			document.getElementById("descripcion2").value = "";
			document.getElementById("estatus2").value = "";
			document.getElementById("fuerza2").value = "";
			document.getElementById("logo2").value = "";
			document.getElementById("descripcionLarga2").value = "";
			var bsubmit = document.getElementById("submit2");
			var blimpiar = document.getElementById("limpiar2");
			bsubmit.hidden = false;
			blimpiar.hidden = true;
			$('#alta').hide();
			$('#cambio').hide();
			$('#all').show();
			Resize();
		}		
		
		
		function validateA(){
			var hidfoto = document.getElementById("fotoA150");
			hidfoto.value = document.getElementById("foto").src;
			document.getElementById('error2').innerHTML = "";
			return true;
		}

		function readURL(input) {
			console.log("read");
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#foto')
                        .attr('src', e.target.result)
                };
                reader.readAsDataURL(input.files[0]);
                $('#foto').show();
            }
        }
				
		function readURL2(input) {
			console.log("read2");
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#foto2')
                        .attr('src', e.target.result)
                };
                reader.readAsDataURL(input.files[0]);
                $('#foto2').show();
            }
        }

		function readURLA(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#fotoA')
                        .attr('src', e.target.result)
                };
                reader.readAsDataURL(input.files[0]);
                $('#fotoA').show();
            }
        }
				
		function readURL2A(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#foto2A')
                        .attr('src', e.target.result)
                };
                reader.readAsDataURL(input.files[0]);
                $('#foto2A').show();
            }
        }

		function fireEvent(element,event) {
		   if (document.createEvent) {
			   return !element.click();
		   } else {
			console.log("else");
			   // dispatch for IE
			   var evt = document.createEventObject();
			   return element.fireEvent('on'+event,evt)
		   }
		};
