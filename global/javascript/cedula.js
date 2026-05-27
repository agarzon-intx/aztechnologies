		function readURLF(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#cedulaF').attr('src', e.target.result)
					$('#cedulaF').attr('height', '200')
					$('#cedulaF').attr('width', '300')
                };
                reader.readAsDataURL(input.files[0]);
                $('#cedulaF').show();
            }
        }
				
		function readURLD(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#cedulaD').attr('src', e.target.result)
					$('#cedulaD').attr('height', '200')
					$('#cedulaD').attr('width', '300')
                };
                reader.readAsDataURL(input.files[0]);
                $('#cedulaD').show();
            }
        }

		function readURLA1(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#cedulaA1').attr('src', e.target.result)
					$('#cedulaA1').attr('height', '200')
					$('#cedulaA1').attr('width', '300')
                };
                reader.readAsDataURL(input.files[0]);
                $('#cedulaA1').show();
            }
        }
				
		function readURLA2(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#cedulaA2').attr('src', e.target.result)
					$('#cedulaA2').attr('height', '200')
					$('#cedulaA2').attr('width', '300')
                };
                reader.readAsDataURL(input.files[0]);
                $('#cedulaA2').show();
            }
        }

		function fireEvent(element,event) {
		   if (document.createEvent) {
				var event = new MouseEvent('click', {
					'view': window,
					'bubbles': true,
					'cancelable': true
				});
				return !element.dispatchEvent(event);
		   } else {
			   // dispatch for IE
			   	var evt = document.createEventObject();
			   	return element.fireEvent('on'+event,evt)
		   }
		   
		   
		};
