$('#cancelar-pedido').on('click', function(event){

    event.preventDefault();

    errors = 0;

    errors += validateEmpty('observacao-cancelamento');

        if(errors == 0){
            $.ajax({
               url: '/action/cancelarPedido/',
               type: 'POST',
               data: $('.form-pedido').serialize()
            })

           .success(function(data) {
                data = data.split(';');
                if(data[0] == 'sucesso'){
                    swal({ 
                        title: "Sucesso!",
                        text: 'Pedido cancelado com sucesso!',
                        type: "success",
                        showConfirmButton: true  
                    },
                    function(){
                        window.location = '/pedido/listar';
                    });
                } else if (data[0] == 'erro'){
                    swal({ 
                        title: "Ops..!",
                        text: data[1],
                        type: "error",
                        showConfirmButton: true  
                    },
                    function(){
                        if ($('#'+data[2]).length > 0){
                            $('#'+data[2]).css({border: '1px solid red'});
                        }
                    });
                } else {
                    swal({ 
                        title: "Ops..!",
                        text: 'Tivemos um pequeno problema, tente novamente!',
                        type: "error",
                        showConfirmButton: true  
                    },
                    function(){

                    });
                }
            })
        } else {
            swal("Ops..!", "Parece que você esqueceu de preencher algum campo!", "error");
        }
});