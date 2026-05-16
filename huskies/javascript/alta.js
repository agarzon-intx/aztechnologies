		window.onload = function (){
			Resize();
			console.log("Load");
			var clave = document.getElementById("clave");
			var jugadorid = document.getElementById("jugadorid");
			var bsubmit = document.getElementById("submit");
			var blimpiar = document.getElementById("limpiar");
			var bsubmit2 = document.getElementById("submit2");
			var blimpiar2 = document.getElementById("limpiar2");
			if(clave.value.length > 0){
				bsubmit.hidden = true;
				blimpiar.hidden = false;
				document.getElementById("clave").disabled = true;
				document.getElementById("nombre").disabled = true;
				document.getElementById("apellidop").disabled = true;
				document.getElementById("apellidom").disabled = true;
				document.getElementById("apodo").disabled = true;
				document.getElementById("fechanac").disabled = true;
				document.getElementById("curp").disabled = true;
				document.getElementById("numero").disabled = true;
				document.getElementById("telefono").disabled = true;
				document.getElementById("correo").disabled = true;
				document.getElementById("equipo").disabled = true;
				document.getElementById("Alta").disabled = true;
				document.getElementById("Baja").disabled = true;
				document.getElementById("Suspendido").disabled = true;
				document.getElementById("comentarios").disabled = true;
				document.getElementById("subirfoto").disabled = true;
				document.getElementById("subiridentificacion").disabled = true;
				document.getElementById("subirfirma").disabled = true;
			}else{
				bsubmit.hidden = false;
				blimpiar.hidden = true;
			}
			var str = document.getElementById('error2').innerHTML.trim();
			if(str == "Jugador Actualizado"){
				bsubmit2.hidden = true;
				blimpiar2.hidden = false;
				document.getElementById("clave2").disabled = true;
				document.getElementById("nombre2").disabled = true;
				document.getElementById("apellidop2").disabled = true;
				document.getElementById("apellidom2").disabled = true;
				document.getElementById("apodo2").disabled = true;
				document.getElementById("fechanac2").disabled = true;
				document.getElementById("curp2").disabled = true;
				document.getElementById("numero2").disabled = true;
				document.getElementById("telefono2").disabled = true;
				document.getElementById("correo2").disabled = true;
				document.getElementById("equipo2").disabled = true;
				document.getElementById("Alta2").disabled = true;
				document.getElementById("Baja2").disabled = true;
				document.getElementById("Suspendido2").disabled = true;
				document.getElementById("comentarios2").disabled = true;
				document.getElementById("subirfoto2").disabled = true;
				document.getElementById("subiridentificacion2").disabled = true;
				document.getElementById("subirfirma2").disabled = true;
			}else{
				bsubmit2.hidden = false;
				blimpiar2.hidden = true;
			}
			var index = document.getElementById("equipo").selectedIndex;
			var optionsl = document.getElementById("equipol").options;
			document.getElementById("logoE").src = "../imagenes/"+optionsl[index].text+".png";
			console.log(optionsl[index].text);
			document.getElementById("logoE2").src = "../imagenes/"+optionsl[index].text+".png";
			console.log(optionsl[index].text);
			var index2 = document.getElementById("equipo2").selectedIndex;
			var optionsl2 = document.getElementById("equipol2").options;
			document.getElementById("logoE2").src = "../imagenes/"+optionsl2[index2].text+".png";
			Resize();
		}

		function fireEvent(element,event) {
		   if (document.createEvent) {
			   return !element.click();
		   } else {
			   // dispatch for IE
			   var evt = document.createEventObject();
			   return element.fireEvent('on'+event,evt)
		   }
		};
	    
		function readURL(input) {
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

		function readIDURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#identificacion')
                        .attr('src', e.target.result)
                };

                reader.readAsDataURL(input.files[0]);
                $('#identificacion').show();
            }
        }

		function readFirmaURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#firma')
                        .attr('src', e.target.result)
                };

                reader.readAsDataURL(input.files[0]);
                $('#firma').show();
            }
        }
		
		function validateEmail(email) {
			var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
			return re.test(email);
		}

		function validate(){
			var error = "Se encontraron los siguientes errores en el Formulario:";
			var count = 0;
			var nombre = document.getElementById("nombre");
			if(nombre.value.length==0){
				count++;
				error = error + "<p>" + count +  ".- El nombre no puede estar vacio";
			}
			var apellidop = document.getElementById("apellidop");
			if(apellidop.value.length==0){
				count++;
				error = error + "<p>" + count +  ".- El apellido paterno no puede estar vacio";
			}
			var apellidom = document.getElementById("apellidom");
			if(apellidom.value.length==0){
				count++;
				error = error + "<p>" + count +  ".- El apellido materno no puede estar vacio";
			}
			var fechanac = document.getElementById("fechanac");
			if(fechanac.value.length == 0){
				count++;
				error = error + "<p>" + count +  ".- La Fecha de Nacimiento es incorrecta " + fechanac.value;
			}
			var tcurp = generaCurp(	nombre.value,
									apellidop.value,
									apellidom.value,
									'H',
			  						'DF',
			  						[fechanac.value.substring(8,10), fechanac.value.substring(5,7), fechanac.value.substring(0,4)]);
			/*var curp = document.getElementById("curp");
			if(curp.value.length==0){
				count++;
				error = error + "<p>" + count +  ".- La Curp no puede estar vacia";
				//GAZA810131HDFRML03
			}else{
				if(curp.value.substring(0,11) != tcurp.substring(0,11)){
					count++;
					error = error + "<p>" + count +  ".- La estructura de la Curp no coincide con la informacion proporcionada";
				}
			}*/
			/*var numero = document.getElementById("numero");
			if(numero.value.length==0){
				count++;
				error = error + "<p>" + count +  ".- El numero no puede estar vacio";
			}
			var telefono = document.getElementById("telefono");
			if(telefono.value.length==0){
				count++;
				error = error + "<p>" + count +  ".- El telefono no puede estar vacio";
			}
			var correo = document.getElementById("correo");
			if(correo.value.length==0){
				count++;
				error = error + "<p>" + count +  ".- El correo no puede estar vacio";
			}else{
				if(!validateEmail(correo.value)){
					count++;
					error = error + "<p>" + count +  ".- El correo es incorrecto debe ser: <cuenta>@<dominio> ej: jugador@hotmail.com";					
				}
			}
			var myFoto = document.getElementById("myFoto");
			if(myFoto.value.length==0){
				count++;
				error = error + "<p>" + count +  ".- No has seleccionado una foto";
			}
			var myFirma = document.getElementById("myFirma");
			if(myFirma.value.length==0){
				count++;
				error = error + "<p>" + count +  ".- No has seleccionado una firma";
			}
			var myID = document.getElementById("myID");
			if(myID.value.length==0){
				count++;
				error = error + "<p>" + count +  ".- No has seleccionado una identificacion";
			}
			*/if(count>0){
				document.getElementById('error').innerHTML = error;
				return false;
			}
			var hidfoto = document.getElementById("fotostr");
			hidfoto.value = document.getElementById("foto").src;
			var hidid = document.getElementById("idstr");
			hidid.value = document.getElementById("identificacion").src;
			var hidfirma = document.getElementById("firmastr");
			hidfirma.value = document.getElementById("firma").src;
			document.getElementById('error2').innerHTML = "";
			return true;
		}
		
		function loadimage(){
			var index = document.getElementById("equipo").selectedIndex;
			var optionsl = document.getElementById("equipol").options;
			document.getElementById("logoE").src = "../imagenes/"+optionsl[index].text+".png";			
		}
		
		function loadimageA(){
			var index = document.getElementById("equipo2").selectedIndex;
			var optionsl = document.getElementById("equipol2").options;
			document.getElementById("logoE2").src = "../imagenes/"+optionsl[index].text+".png";			
		}
		
		function limpiar(){
			document.getElementById("clave").disabled = false;
			document.getElementById("nombre").disabled = false;
			document.getElementById("apellidop").disabled = false;
			document.getElementById("apellidom").disabled = false;
			document.getElementById("apodo").disabled = false;
			document.getElementById("fechanac").disabled = false;
			document.getElementById("curp").disabled = false;
			document.getElementById("numero").disabled = false;
			document.getElementById("telefono").disabled = false;
			document.getElementById("correo").disabled = false;
			document.getElementById("equipo").disabled = false;
			document.getElementById("Alta").disabled = false;
			document.getElementById("Baja").disabled = false;
			document.getElementById("Suspendido").disabled = false;
			document.getElementById("comentarios").disabled = false;
			document.getElementById("subirfoto").disabled = false;
			document.getElementById("subiridentificacion").disabled = false;
			document.getElementById("subirfirma").disabled = false;

			document.getElementById("clave").value = "";
			document.getElementById("nombre").value = "";
			document.getElementById("apellidop").value = "";
			document.getElementById("apellidom").value = "";
			document.getElementById("apodo").value = "";
			document.getElementById("fechanac").value = "";
			document.getElementById("curp").value = "";
			document.getElementById("numero").value = "";
			document.getElementById("telefono").value = "";
			document.getElementById("correo").value = "";
			document.getElementById("myFoto").value = "";
			document.getElementById("myFirma").value = "";
			document.getElementById("myID").value = "";
			document.getElementById('error').innerHTML = "";
			var bsubmit = document.getElementById("submit");
			var blimpiar = document.getElementById("limpiar");
			bsubmit.hidden = false;
			blimpiar.hidden = true;
			$('#firma').hide();
			$('#foto').hide();
			$('#identificacion').hide();
			$('#alta').hide();
			$('#cambio').hide();
			$('#all').show();
			Resize();
		}
		
		function limpiarA(){
			document.getElementById("clave2").disabled = false;
			document.getElementById("nombre2").disabled = false;
			document.getElementById("apellidop2").disabled = false;
			document.getElementById("apellidom2").disabled = false;
			document.getElementById("apodo2").disabled = false;
			document.getElementById("fechanac2").disabled = false;
			document.getElementById("curp2").disabled = false;
			document.getElementById("numero2").disabled = false;
			document.getElementById("telefono2").disabled = false;
			document.getElementById("correo2").disabled = false;
			document.getElementById("equipo2").disabled = false;
			document.getElementById("Alta2").disabled = false;
			document.getElementById("Baja2").disabled = false;
			document.getElementById("Suspendido2").disabled = false;
			document.getElementById("comentarios2").disabled = false;
			document.getElementById("subirfoto2").disabled = false;
			document.getElementById("subiridentificacion2").disabled = false;
			document.getElementById("subirfirma2").disabled = false;

			document.getElementById("clave2").value = "";
			document.getElementById("nombre2").value = "";
			document.getElementById("apellidop2").value = "";
			document.getElementById("apellidom2").value = "";
			document.getElementById("apodo2").value = "";
			document.getElementById("fechanac2").value = "";
			document.getElementById("curp2").value = "";
			document.getElementById("numero2").value = "";
			document.getElementById("telefono2").value = "";
			document.getElementById("correo2").value = "";
			document.getElementById("myFoto2").value = "";
			document.getElementById("myFirma2").value = "";
			document.getElementById("myID2").value = "";
			document.getElementById('error2').innerHTML = "";
			var bsubmit = document.getElementById("submit2");
			var blimpiar = document.getElementById("limpiar2");
			bsubmit.hidden = false;
			blimpiar.hidden = true;
			$('#firma2').hide();
			$('#foto2').hide();
			$('#identificacion2').hide();
			$('#alta').hide();
			$('#cambio').hide();
			$('#all').show();
			document.getElementById("jugadorid").value = "0";
			Resize();
		}		
		
		function readURL2(input) {
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

		function readIDURL2(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#identificacion2')
                        .attr('src', e.target.result)
                };

                reader.readAsDataURL(input.files[0]);
                $('#identificacion2').show();
            }
        }

		function readFirmaURL2(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#firma2')
                        .attr('src', e.target.result)
                };

                reader.readAsDataURL(input.files[0]);
                $('#firma2').show();
            }
        }
		
		function validateA(){
			var error = "Se encontraron los siguientes errores en el Formulario:";
			var count = 0;
			var nombre = document.getElementById("nombre2");
			if(nombre.value.length==0){
				count++;
				error = error + "<p>" + count +  ".- El nombre no puede estar vacio";
			}
			var apellidop = document.getElementById("apellidop2");
			if(apellidop.value.length==0){
				count++;
				error = error + "<p>" + count +  ".- El apellido paterno no puede estar vacio";
			}
			var apellidom = document.getElementById("apellidom2");
			if(apellidom.value.length==0){
				count++;
				error = error + "<p>" + count +  ".- El apellido materno no puede estar vacio";
			}
			var fechanac = document.getElementById("fechanac2");
			if(fechanac.value.length == 0){
				count++;
				error = error + "<p>" + count +  ".- La Fecha de Nacimiento es incorrecta " + fechanac.value;
			}
			/*var tcurp = generaCurp(	nombre.value,
									apellidop.value,
									apellidom.value,
									'H',
			  						'DF',
			  						[fechanac.value.substring(8,10), fechanac.value.substring(5,7), fechanac.value.substring(0,4)]);
			var curp = document.getElementById("curp2");
			if(curp.value.length==0){
				count++;
				error = error + "<p>" + count +  ".- La Curp no puede estar vacia";
				//GAZA810131HDFRML03
			}else{
				if(curp.value.substring(0,11) != tcurp.substring(0,11)){
					count++;
					error = error + "<p>" + count +  ".- La estructura de la Curp no coincide con la informacion proporcionada";
				}
			}*/
			/*var numero = document.getElementById("numero2");
			if(numero.value.length==0){
				count++;
				error = error + "<p>" + count +  ".- El numero no puede estar vacio";
			}
			var telefono = document.getElementById("telefono2");
			if(telefono.value.length==0){
				count++;
				error = error + "<p>" + count +  ".- El telefono no puede estar vacio";
			}
			var correo = document.getElementById("correo2");
			if(correo.value.length==0){
				count++;
				error = error + "<p>" + count +  ".- El correo no puede estar vacio";
			}else{
				if(!validateEmail(correo.value)){
					count++;
					error = error + "<p>" + count +  ".- El correo es incorrecto debe ser: <cuenta>@<dominio> ej: jugador@hotmail.com";					
				}
			}
			*/if(count>0){
				document.getElementById('error2').innerHTML = error;
				return false;
			}
			var hidfoto = document.getElementById("fotostr2");
			hidfoto.value = document.getElementById("foto2").src;
			var hidid = document.getElementById("idstr2");
			hidid.value = document.getElementById("identificacion2").src;
			var hidfirma = document.getElementById("firmastr2");
			hidfirma.value = document.getElementById("firma2").src;
			document.getElementById('error2').innerHTML = "";
			document.getElementById("jugadorid").value = "1";
			return true;
		}
