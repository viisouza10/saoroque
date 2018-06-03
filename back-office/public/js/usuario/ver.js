$('#editar').click(function(){
	$('input').removeAttr('disabled');
	$('select').removeAttr('disabled');
	$('#salvar').removeClass('hidden');
});

$('.form-usuario').on('submit', function(e){
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

$('#remover').click(function(){
	swal({
		title: 'Tem certeza?',
		text: 'O usuário sera deletado e impossibilitado de acessar o sistema.',
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#d9534f',
		confirmButtonText: 'Sim, deletar!',
		cancelButtonText: 'Não',
		closeOnConfirm: false,
		closeOnCancel: true
	},
	function(isConfirm) {
		if (isConfirm) {
			$.ajax({
				url: '/usuario/remover/',
				type: 'POST',
				data: {
					id: $('input[name="id"]').val()
				}
			})
			.success(function(data) {
				json = JSON.parse(data);
				swal({
					title: json.title,
					text: json.message,
					type: json.status
				}, function(){
					if(json.status == 'success'){
						window.location = '/usuario/listar';
					}
				});
			});
		}
	});
});

function sendAjax(self){
	var formData = new FormData(self);
	$.ajax({
		url : '/action/editUsuario',
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
					window.location = '/usuario/listar/';
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
				text: 'Tivemos algum tipo de problema para criar a o registro, tente de novo ou aguarde um pouco!',
				type: 'info'
			});
		}
	});
}
