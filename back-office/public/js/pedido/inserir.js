//MASKs
$('input[name="cliente[cpf]"]').mask('000.000.000-00');
$('input[name*="cep"]').mask('00000-000');

$('#cliente_cpf').keyup(function(){
	input = $(this);
	cpf = $(this).val();
	if($(this).val().length == 14){
		$.ajax({
			url: '/action/BuscaCPFCliente/' + cpf,
			type: 'GET',
			beforeSend: function(){
				$('.load_cpf').fadeIn('slow');
			},
			success: function(resp){
				json = JSON.parse(resp);
				if(json.return == 'success'){
					$('#cliente_id').val(json.id);
					$('#cliente_nome').val(json.nome);
					$('#cliente_nascimento').val(json.nascimento);
					$('#cliente_telefone').val(json.telefone);
					$('#cliente_email').val(json.email);
					$('#cliente_cep').val(json.cep);
					$('#cliente_numero').val(json.numero);
					$('#cliente_endereco').val(json.endereco);
					$('#cliente_complemento').val(json.complemento);
					$('#cliente_bairro').val(json.bairro);
					$('#cliente_cidade').val(json.cidade);
					$('#cliente_estado').val(json.estado);
					$('.corporate_id').val(json.corporate_id);
					$('#codigo_funcional').val(json.codigo_func);
				} else {
					swal({
						title: json.title,
						text: json.message,
						type: json.status
					});
				}
			},
			complete: function(){
				$('.load_cpf').fadeOut('slow');
				input.blur();
			}
		});
	}
});

var options = {
    onKeyPress: function (cpf, ev, el, op) {
        var masks = ['000.000.000-000', '00.000.000/0000-00'],
            mask = (cpf.length > 14) ? masks[1] : masks[0];
        el.mask(mask, op);
    }
}
$('#motorista_cpf').mask('000.000.000-000', options);

$('#motorista_veiculo_id').html("<option disable>Selecionar</option>");

$('#motorista_cpf').keyup(function(){
	$('#motorista_veiculo_id').html("<option disable>Selecionar</option>");
	input = $(this);
	cpf = $(this).val();
	if($(this).val().length >= 14){

		$.ajax({
			url: '/action/BuscaCPFMotorista/',
			type: 'POST',
			data: {
				cpf : cpf
			},
			beforeSend: function(){
				$('.load_cpf').fadeIn('slow');
			},
			success: function(resp){
				json = JSON.parse(resp);
				if(resp.indexOf('success') > 0){
					$('#motorista_id').val(json.dados.id);
					$('#motorista_nome').val(json.dados.nome);
					$('#pedido_motorista').val(json.dados.nome);
					$('#motorista_telefone').val(json.dados.telefone);
					$('#motorista_email').val(json.dados.email);
					$('#motorista_endereco').val(json.dados.endereco);
					$('#motorista_bairro').val(json.dados.bairro);
					$('#motorista_cidade').val(json.dados.cidade);
					$('#ordem_motorista').val(json.dados.nome);
					$('#motorista_veiculo_id').html("<option>Selecione o veículo</option>" + json.option);
				}else{
					swal({
						title: json.dados.title,
						text: json.dados.message,
						type: json.dados.status
					});
					$('#motorista_veiculo_id').html("<option disable>Selecionar</option>");
				}
			},
			complete: function(){
				$('.load_cpf').fadeOut('slow');
				input.blur();
			}
		});
	}
});

$('#pedido_cep_origem').on('blur', function(){
	$('#loader').addClass('loader');
	cep = $(this).val();
	$.ajax({
		url: '/action/Cep/' + cep,
		type: 'GET',
		beforeSend: function(){
			$('.load_cpf').fadeIn();
		},
		success: function(resp){
			$('#loader').removeClass('loader');
			resp = resp.replace("Template not found or empty", "");
			json = JSON.parse(resp);
			if(json.success){
				$('#pedido_endereco_origem').val(json.success.logradouro);
				$('#pedido_bairro_origem').val(json.success.bairro);
				$('#pedido_cidade_origem').val(json.success.cidade);
				$('#pedido_estado_origem').val(json.success.uf.toUpperCase());
				$('#pedido_numero_origem').focus();
			}else{
				swal({
					title: "CEP não encontrado",
					text: "O CEP inserido não existe por favor verifique se digitou corretamente",
					type: "info"
				});
				return false;
			}
		},
		complete: function(){
			$('.load_cpf').fadeOut();
		}
	});
});

$('#pedido_cep_destino').on('blur', function(){
	$('#loader').addClass('loader');
	cep = $(this).val();
	$.ajax({
		url: '/action/Cep/' + cep,
		type: 'GET',
		beforeSend: function(){
			$('.load_cpf').fadeIn();
		},
		success: function(resp){
			$('#loader').removeClass('loader');
			resp = resp.replace("Template not found or empty", "");
			json = JSON.parse(resp);
			if(json.success){
				$('#pedido_endereco_destino').val(json.success.logradouro);
				$('#pedido_bairro_destino').val(json.success.bairro);
				$('#pedido_cidade_destino').val(json.success.cidade);
				$('#pedido_estado_destino').val(json.success.uf.toUpperCase());
				$('#pedido_numero_destino').focus();
			}else{
				swal({
					title: "CEP não encontrado",
					text: "O CEP inserido não existe por favor verifique se digitou corretamente",
					type: "info"
				});
				return false;
			}
		},
		complete: function(){
			$('.load_cpf').fadeOut();
		}
	});
});

$('#motorista_veiculo_id').on('change', function(){
	$('#veiculo_placa').val($('#motorista_veiculo_id option:selected').attr('data-placa'));
	carro = $('#motorista_veiculo_id option:selected').attr('value');
	$.ajax({
		url: '/action/BuscaServico/' + carro,
		type: 'GET',
		beforeSend: function(){
			$('.load_cpf').fadeIn('slow');
		},
		success: function(resp){
			json = JSON.parse(resp);
			console.log(json)
			$('#veiculo_tipo').html(json.pai);
			$('#veiculo_subtipo').html(json.filho);
		},
		complete: function(){
			$('.load_cpf').fadeOut('slow');
		}
	});
});

$('#veiculo_tipo').on('change', function(){
	carro = $('#veiculo_tipo option:selected').attr('data-car');
	pai = $('#veiculo_tipo option:selected').attr('value');
	$.ajax({
		url: '/action/BuscaServicoFilho/' + carro + '/' + pai,
		type: 'GET',
		success: function(resp){
			json = JSON.parse(resp);
			// $('#veiculo_tipo').html(json.pai).prop('disabled', false);
			$('#veiculo_subtipo').html(json.filho);
		}
	});
});


$('.form-pedido').on('submit', function(e){
    var self = this;
    e.preventDefault();
    $('#salvar').html('<div class="text-center"><i class="fa fa-spinner fa-pulse fa-fw"></i></div>');
    var error = 0;

    $('.required').each(function(i, element) {
		error += validateEmpty(element['id']);
    });

	if(error == 0){
		sendAjax(self);
	} else {
		swal("Oops!", "Parece que você não preencheu tudo!", "error");
		$('#salvar').html('<i class="fa fa-check"></i>');
	}
});

function sendAjax(self){
    var formData = new FormData(self);
	$.ajax({
		url : '/action/addPedido',
		type : 'POST',
		data : formData,
		processData: false,
		contentType: false,
		success : function(data) {
			json = JSON.parse(data);
			if(json.status == 'success'){
				swal({
					title: 'Sucesso',
					text: json.message,
					type: json.status
				}, function(){
					window.location = '/pedido/listar';
				});
			} else {
				swal({
					title: 'Ops..!',
					text: json.message,
					type: json.status
				}, function(){
					if ($('#'+json.id).length > 0){
						$('#'+json.id).css({border: '1px solid red'});
						jump(json.id);
					}
					$('#salvar').html('<i class="fa fa-check"></i>');
				});
			}
		},
		error : function () {
			swal({
				title: 'Que coisa...',
				text: 'Tivemos algum tipo de problema para criar o registro, tente de novo ou aguarde um pouco!',
				type: 'info'
			});
		}
	});
}
