$("#pass").keyup(function(e){
    var code = e.which;
    if(code==13){
    	errors = 0;
		errors += validateEmpty('login');
		errors += validateEmpty('pass');

		if(errors == 0) {
			$.ajax({
				url: '/action/login/',
				type: 'POST',
				async:false,
				data: {
					login: $('#login').val(),
					senha: $('#pass').val()
				},
		        success: function(json) {
		        	dados = json.split(";");
		        	//cadastre realizado com sucesso
		        	if(dados[0] == 'sucesso') {
		        			window.location = '/admin';
		        	} else {
						setInvalid('login',dados[1]);
						setInvalid('pass',dados[1]);
						swal('Erro!',dados[1],'error');
		        	}
		        }
			});
		}
    }
});

$('#form-login').on('submit', function(e){
	e.preventDefault()
	errors = 0;
	errors += validateEmpty('login');
	errors += validateEmpty('pass');

	if(errors == 0) {
		$.ajax({
			url: '/action/login/',
			type: 'POST',
			async:false,
			data: {
				login: $('#login').val(),
				senha: $('#pass').val()
			},
	        success: function(json) {
	        	dados = json.split(";");
	        	//cadastre realizado com sucesso
	        	if(dados[0] == 'sucesso') {
	        			window.location = '/admin';
	        	} else {
					setInvalid('login',dados[1]);
					setInvalid('pass',dados[1]);
					swal('Erro!',dados[1],'error');
	        	}
	        }
		});
	}
});
