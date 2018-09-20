$('#datetimepicker1').datetimepicker({
    format: "DD/MM/YYYY"
});

$('tbody tr').hover(function(){
    $(this).css('cursor','pointer');
});

$('tbody tr').click(function(){
    var id = $(this).find('td > #id').val();
    window.location = '/pedido/ver/' + id;
});

$('#corporate').change(function(){

    var corporate = $('#corporate option:selected').val();

    $.ajax({
        url: '/action/getDepartamentos/',
        type: 'POST',
        data: {
            corporate_id: corporate,
        },
        success: function(json) {
            console.log(json)
            $('#departamento').html(json);
        }
    });
});

$('#departamento').change(function(){

    var departamento = $('#departamento option:selected').val();

    $.ajax({
        url: '/action/getClientes/',
        type: 'POST',
        data: {
            departamento_id: departamento,
        },
        success: function(json) {
            console.log(json)
            $('#cliente').html(json);
        }
    });
});

$('#buscar-pedido').click(function(event){

    event.preventDefault();

    var data_inicial = $('#data-inicial').val();

    for (var i = data_inicial.length - 1; i >= 0; i--) {
        var data_inicial = data_inicial.replace("/", "_");
    };

    window.location = '/pedido/listar/1/'
    +$('#cliente_tipo').val()+'/'
    +$('#corporate').val()+'/'
    +$('#departamento').val()+'/'
    +$('#cliente').val()+'/'
    +$('#status').val()+'/'
    +$('#corrida').val()+'/'
    +$('#codigo_pedido').val()+'/'
    +data_inicial;
});
