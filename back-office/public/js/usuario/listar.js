$('#status').change(function(){
	window.location = '/usuario/listar/1/'+$('#status').val()+'/'+$('#nome').val()+'/'+$('#email').val();
});

$('#nome').keyup(function(e){
	if(e.which == 13){
		window.location = '/usuario/listar/1/'+$('#status').val()+'/'+$('#nome').val()+'/'+$('#email').val();
	}
});

$('#buscar-usuario').click(function(){
	window.location = '/usuario/listar/1/'+$('#status').val()+'/'+$('#nome').val()+'/'+$('#email').val();
});
