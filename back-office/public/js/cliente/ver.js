
$('input').prop('disabled', true);
$('#salvar').addClass('hidden');

$('#editar').click(function(){
    $('input').prop('disabled', false);
    $('#editar').addClass('hidden');
    $('#salvar').removeClass('hidden');
});

$('#cpf').mask('000.000.000-00');
$('#nascimento').mask('00/00/0000');

$('.form-cliente').on('submit', function(e){
    var self = this;
    e.preventDefault();
    $('#salvar').html('<div class="text-center"><i class="fa fa-spinner fa-pulse fa-fw"></i></div>');
    var error = 0;

    $('.required').each(function(i, el) {
        error += validateEmpty(el['id']);
    });

    if(error == 0){
        sendAjax(self);
    } else {
        swal("Oops!", "Parece que você não preencheu tudo!", "error");
        $('#salvar').html('<i class="fa fa-check"></i>');
    }
});

function sendAjax(self){
    var id = $('input[name="id"]').val();
    var formData = new FormData(self);
    $.ajax({
        url : '/action/EditClienteAdmin',
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
                    window.location = '/cliente/ver/'+id;
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

function jump(element){
    console.log(element);
    var top = document.getElementById(element).offsetTop;
    window.scrollTo(0, top);
}
    


