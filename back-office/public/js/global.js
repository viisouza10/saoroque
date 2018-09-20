function setValid(element) {
	$('#'+element).addClass('valid');
	$('#'+element).removeClass('invalid');
}
function setInvalid(element, msg) {
	$('#'+element).addClass('invalid');
	$('#'+element).removeClass('valid');
}
//Funcoes executadas no load da aplicação
$(document).ready(function() {

	// Função que substitui qualquer chave não definida ([#key#]), em uma string vazia
    var pattern = /\[(.*?)\]/;
    $(document).find(':input').each(function() {
        var str = $(this).val();
		if(str != null) {
			$(this).val(str.replace(pattern , ''));
		}
    });

	//formulario de login
	$('#sendSignIn').click(function(){

		//testa erros
		errors = 0;
		errors += validateEmpty('login');
		errors += validateEmpty('pass');
		errors += validateEmail('login');

		if(errors == 0) {
			$.ajax({
				url: '/action/login/',
				type: 'POST',
				async:false,
				data: {
      					login: $('#login').attr('value'),
      					senha: $('#pass').attr('value')
     				},
			        success: function(json) {
			        	dados = json.split(";");
			        	//cadastre realizado com sucesso
			        	if(dados[0] == 'sucesso') {
			        		if(dados[1] == 'cliente') {
			        			window.location = 'http://www.tratafoto.com.br/cliente';
			        		} else {
			        			window.location = 'http://www.tratafoto.com.br/admin';
			        		}
			        	} else {
							setInvalid('login',dados[1]);
			        	}
			        }
			});
		}

	});

	//cadastro
	$('#sendSignUp').click(function(){
		errors = 0;
		//validade empty fields
		errors += validateEmpty('nome');
		errors += validateEmpty('email');
		errors += validateEmpty('senha');
		errors += validateEmpty('resenha');
		errors += validateChecked('aceite');
		errors += validateEmail('email');

		//if havent empty fields test equals
		if(errors ==0) {
			errors += validateEqual('senha','resenha');
		}
		if(errors == 0) {
			$.ajax({
				url: '/action/signup/',
				type: 'POST',
				data: {
						nome: $('#nome').attr('value'),
						email: $('#email').attr('value'),
						senha: $('#senha').attr('value')
     				},
			        success: function(json) {
			        	dados = json.split(";");
			        	//cadastre realizado com sucesso
			        	if(dados[0] == 'sucesso') {
			        		alert(dados[1]);
			        		window.location = '/cliente/start';
			        	} else {
							alert(dados[1]);
			        	}
			        }
			});
		}
	});

});


function validatZero(element) {
	//testa erros
	errors = 0;
	if($('#'+element).val() == 0) {
		// $('#'+element).addClass('invalid');
		setInvalid(element);
		errors++;
	} else {
		// $('#'+element).removeClass('invalid');
		setValid(element);
	}
	return errors;
}

function validateEmpty(element) {
	//testa erros
	errors = 0;
	if($('#'+element).val() == '' || $('#'+element).val() == 0) {
		setInvalid(element);
		$('#'+element).prev('.error-msg').text('Preencha esse campo');
		errors++;
	} else {
		setValid(element);
		$('#'+element).prev('.error-msg').html('');

	}
	return errors;
}

function validateChecked(element) {
	//testa erros
	errors = 0;
	if($('#'+element).attr('checked') != 'checked') {
		setInvalid(element);
		errors++;
	} else {
		setValid(element);
	}
	return errors;
}

function validateEqual(element1,element2) {
	//testa erros
	errors = 0;
	if($('#'+element1).attr('value') == $('#'+element2).attr('value')) {
		setValid(element1);
	} else {
		setInvalid(element2);
		errors++;
	}
	return errors;
}


function goUrl(page, url, param) {
	window.location = url+'/'+page+'/'+param;
}

function validateEmail(element) {
	errors = 0;
	var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
     if ($('#'+element).val().search(emailRegEx) == -1) {
     	  setInvalid(element);
     	  $('#'+element).prev('.error-msg').text('E-mail inválido');
          errors++;
     }
     return errors;
}

function number_format(number, decimals, dec_point, thousands_sep) {
  number = (number + '')
    .replace(/[^0-9+\-Ee.]/g, '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function(n, prec) {
      var k = Math.pow(10, prec);
      return '' + (Math.round(n * k) / k)
        .toFixed(prec);
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
    .split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '')
    .length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1)
      .join('0');
  }
  return s.join(dec);
}

function validaExtensao(extensao) {
	if ((extensao != 'jpg') && (extensao != 'jpeg') &&(extensao != 'JPG') && (extensao != 'JPEG') && (extensao != 'png') && (extensao != 'PNG')) {
		return false;
	} else {
		return true;
	}
}
$(document).ready(function() {
	$('.celular').mask('(99)99999-9999');
});

function goBack() {
    window.history.back();
}
