$(function(){

    var caminho         = $('#js_caminho').val();
    var ids             = $('#ids').val(); // pegando o id do detento no input hidden
    var reload_on_close = false;

    // para atualizar as permissões
    (function($) {

        $.ajax_refresh_sedex_item = function () {

            var cont = $('#table_sedex_item'); // pega a div onde está os dados

            $.ajax({
                type   : "POST",
                cache  : false,
                url    : caminho + "ajax/ajax_sedex_item_data.php",
                data   : {
                    uid    : ids
                },
                success: function( response ){

                    //forçando o parser
                    var data = $( response );

                    // fazendo a troca com esmaecimento
                    cont.fadeOut( 'slow', function(){
                        cont.html( data ).fadeIn();
                    });

                }

            });// /$.ajax({

        }

    })(jQuery);

    $("#link_add_item_sedex").live( "click", function(){

        $.ajax_form_add( "ajax/ajax_sedex_item_form.php", ids, 3, true, 'un_med' );

    });

    $("input[name='edit_item_sedex[]']").live( "click", function(){

        var $this = $( this );//guardando o ponteiro em uma variavel, por performance

        var id_item = $this.val();

        $.ajax_form_add( "ajax/ajax_sedex_item_form.php", id_item, 1, true, 'un_med' );

    });

    $("input[name='del_item_sedex[]']").live( "click", function(){

        var $this = $( this );//guardando o ponteiro em uma variavel, por performance

        var id_item = $this.val();

        $.ajax_form_add( "ajax/ajax_sedex_item_form.php", id_item, 2, true );

    });

    var cadadd = 0;
    $("input#bt_submit").live( "click", function(){
        cadadd = 0;
        $("#form_sedex_add").submit();
    });

    $("input#bt_cadadd").live( "click", function(){
        cadadd = 1;
        $("#form_sedex_add").submit();
    });

    $("input#bt_cancel").live( "click", function(){

        if ( reload_on_close ) {
            $.ajax_refresh_sedex_item();
        }

        reload_on_close = false;

        $.fancybox.close();

    });

    // VALIDAÇÃO DO FORM DE CADASTRAMENTO/ALTERAÇÃO
    $("#form_sedex_add").live("submit", function() {

        var proced = $("#proced").val();

        if ( proced != 2 ) {  // se o procedimento não for 2 (exclusão)

            if ($("#un_med").val().length < 1 ) {                         // se não tiver nada digitado
                $("#form_error").show();                                  // mostrar o paragrafo de erro
                $("#form_error").html( 'Escolha a unidade de medida.' );  // troca a mensagem de erro
                $.fancybox.resize();                                      // redimensionar a janela do fancybox
                return false;
            }

            if ($("#quant").val().length < 1 ) {                   // se não tiver nada digitado
                $("#form_error").show();                           // mostrar o paragrafo de erro
                $("#form_error").html( 'Informe a quantidade.' );  // troca a mensagem de erro
                $.fancybox.resize();                               // redimensionar a janela do fancybox
                return false;
            }

            if ($("#desc_item_sedex").val().length < 1 ) {    // se não tiver nada digitado
                $("#form_error").show();                      // mostrar o paragrafo de erro
                $("#form_error").html( 'Descreva o item.' );  // troca a mensagem de erro
                $.fancybox.resize();                          // redimensionar a janela do fancybox
                return false;
            }

        }// if ( proced != 2 )

        $.fancybox.showActivity();

        $.ajax({
            type   : "POST",
            cache  : false,
            url    : caminho + "send/sendsedexitem.php",
            data   : $(this).serializeArray(),
            success: function( data ) {

                if ( data == 1 ) { // se retornar 1 obteve sucesso

                    if ( cadadd == 1 ) { // se tiver cadadd é porque vai adicionar outra permissão

                        cadadd = 0;             // resetar o valor de cadadd
                        reload_on_close = true;

                        $.fancybox.hideActivity();

                        $.ajax_form_add( "ajax/ajax_sedex_item_form.php", ids, 3, true, 'un_med' ); // reabrir o form de cadastramento

                    } else {

                        $.ajax_refresh_sedex_item(); // atualizar o conteudo dos itens
                        $.fancybox.close(); // fechar o fancybox
                        reload_on_close = false;

                    }

                } else { // se não, mostra a mensagem

                    $.fancybox( "Ocorreu um erro!!!" );
                    reload_on_close = false;

                } // /if ( data == 1 ) {

            } // /success: function( data ) {

        }); // /$.ajax({

        return false;

    }); // /$("#form_perm").live("submit", function() {

}); // /$(document).ready(function(){
