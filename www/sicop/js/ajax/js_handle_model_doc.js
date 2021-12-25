$(function(){

    var caminho = $('#js_caminho').val();
    var caminho_img = $('#js_caminho_img').val();

    //pre carregando o gif
    var loading = new Image();
    loading.src = caminho_img + 'system/loading.gif';
    var img_cont = '<img src="' + caminho_img + 'system/loading.gif" />';

    $("input#bt_submit").live( "click", function(){
        $("#gera_of").submit();
    });

    // VALIDAÇÃO DO FORM DE CADASTRAMENTO/ALTERAÇÃO
    $("#gera_of").live("submit", function() {

        var data = $( "#gera_of" ).serializeArray();

        $.lock_form_disable( "#gera_of" );

        var result = $.submit_form_ajax( "gera_of", "send/senddocmodel.php", data );

        if ( result != 1 ) {
            alert( "FALHA!!!" );
        } else {
            alert( "Cadastrado com sucesso!!!" );
        }

        $.unlock_form( "#gera_of" );

        return false;

    }); // /$("#gera_of").live("submit", function() {


    // VALIDAÇÃO DO FORM DE CADASTRAMENTO/ALTERAÇÃO
    $("#gera_of1").live("submit", function() {

        var content = $('#status');

        // colocando uma imagem de loading
        content.html( img_cont );

        var data = $( "#gera_of" ).serializeArray();

        $.lock_form_disable( "#gera_of" );

        $.ajax({
            type   : "POST",
            cache  : false,
            url    : caminho + "send/senddocmodel.php",
            data   : data,
            success: function( response ){

                if ( response != 1 ) {
                    alert( "FALHA!!!" );
                } else {
                    alert( "Cadastrado com sucesso!!!" );
                }

                $.unlock_form( "#gera_of" );

                // tirando a imagem de loading
                content.html('');

            } // /success: function( response ){
        });// /$.ajax({

        return false;

    }); // /$("#gera_of").live("submit", function() {

});// /$(function(){


