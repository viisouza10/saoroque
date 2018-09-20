$('.select-status').change(function(){
    var status = $('.select-status option:selected').val();
    var id = $('#pedido_id').val();
    $.ajax({
        url: '/action/alteraStatus/',
        type: 'POST',
        data: {
            id : id,
            status: status
        }
    })
    .success(function(data) {
        location.reload();
    });
});

$('.select-pagamento').change(function(){
    var pagamento = $('.select-pagamento option:selected').val();
    var id = $('#pedido_id').val();
    $.ajax({
        url: '/action/alteraStatusPagamento/',
        type: 'POST',
        data: {
            id : id,
            status: pagamento
        }
    })
    .success(function(data) {
        location.reload();
    });
});

$('#valor_pedido').maskMoney({showSymbol:true, symbol:"R$ ", decimal:",", thousands:"."});


$('#valor_pedido').keyup(function() {
    var valor = $('#valor_pedido').val();
    var id = $('#pedido_id').val();
    var status = 'pendente';

    $.ajax({
        url: '/action/alteraValorPedido/',
        type: 'POST',
        data: {
            id : id,
            valor : valor,
            status: status
        }
    })
    .success(function(data) {
        location.reload();
    });
});
