$(function(){

    //alert( $(location).attr('href') );

    //alert( $(location).attr('hash') );

    var hash_tag = $(location).attr('hash');

    var caminho = $('#js_caminho').val();
    var caminho_img = $('#js_caminho_img').val();

    //pre carregando o gif
    var loading = new Image();
    loading.src = caminho_img + 'system/loading.gif';
    var img_cont = '<p class="img_ajax"><img src="' + caminho_img + 'system/loading.gif" /></p>';

    $( "#form_buscanum" ).live("submit", function() {

        var tipo = $("#tipo");
        if (tipo.val().length < 1 ) {
            alert( 'Escolha o tipo de número que você quer pesquisar!' );
            tipo.focus();
            return false;
        }

        var cont = $("div#cont");

        // colocando uma imagem de loading no lugar da tabela
        cont.html( img_cont );

        // a leitura dos valores deve ser feita
        // antes da função lock_form, pois ela
        // desabilita os inputs e select
        var data_form = $(this).serializeArray()

        $.lock_form_disable( 'form_buscanum' );

        // página que fará a busca
        var href = caminho + 'ajax/ajax_buscanum_q.php';

        // abrindo o ajax
        $.ajax({
            type   : "POST",
            cache  : false,
            url    : href,
            data   : data_form,
            success: function( response ){

                //$(location).attr('href', '#1');

                $(location).attr('hash', '1');

                //history.pushState( response, 'aaa', '?id=aaaa' );

                //forçando o parser
                var data = $( response );

                // fazendo a troca com esmaecimento
                cont.fadeOut( 'slow', function(){
                    cont.html( data ).fadeIn();

                });

            },
            complete: function(){

                $.unlock_form( 'form_buscanum' );

            }

        });// /$.ajax({

        return false;

    }); // /$( "form#buscanum" ).live("submit", function() {


    if (hash_tag != '' && hash_tag != undefined) {

        $("#user").val( $("input#h_user").val() );
        $("#num").val( $("input#h_num").val() );
        $("#ano").val( $("input#h_ano").val() );
        $("#tipo").val( $("input#h_tipo").val() );

        $( "#form_buscanum" ).submit();

    }


}); // /$(function(){
