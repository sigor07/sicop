$(function(){

    //alert( $(location).attr('href') );

    //alert( $(location).attr('hash') );

    var hash_tag = $(location).attr('hash');

    var app_hash;

    var caminho = $('#js_caminho').val();
    var caminho_img = $('#js_caminho_img').val();

    //pre carregando o gif
    var loading = new Image();
    loading.src = caminho_img + 'system/loading.gif';
    var img_cont = '<p class="img_ajax"><img src="' + caminho_img + 'system/loading.gif" /></p>';

    var cont = $("div#album_principal");

    $( "#form_busca" ).live("submit", function() {

        var tipo = $("#tipo");
        if (tipo.val().length < 1 ) {
            alert( 'Escolha o tipo de foto que você quer pesquisar!' );
            tipo.focus();
            return false;
        }

        // colocando uma imagem de loading no lugar da tabela
        cont.html( img_cont );

        // a leitura dos valores deve ser feita
        // antes da função lock_form, pois ela
        // desabilita os inputs e select
        var data_form = $(this).serializeArray()

        var data_form_get = $(this).serialize()

        $.lock_form_disable( 'form_busca' );

        // página que fará a busca
        var href = caminho + 'ajax/ajax_foto_data.php';

        // abrindo o ajax
        $.ajax({
            type   : "POST",
            cache  : false,
            url    : href,
            data   : data_form,
            success: function( response ){

                $(location).attr( 'hash', '?' + data_form_get );

                app_hash = $(location).attr('hash');

                //history.pushState( response, 'aaa', '?id=aaaa' );

                //forçando o parser
                var data = $( response );

                // fazendo a troca com esmaecimento
                //cont.fadeOut( 'slow', function(){
                    cont.html( data ).fadeIn();

                //});

                // PARA FOTO
                $("a.link_group_foto").fancybox({
                    'transitionIn'  : 'elastic',
                    'transitionOut' : 'elastic'
                });

            },
            complete: function(){

                $.unlock_form( 'form_busca' );

            }

        });// /$.ajax({

        return false;

    }); // /$( "form#buscanum" ).live("submit", function() {


//    if ( !$.empty( hash_tag ) ) {
//
//        var data_ini = $.get_get( "data_ini" );
//        var data_fim = $.get_get( "data_fim" );
//        var tipo = $.get_get( "tipo" );
//
//        $("#data_ini").val( data_ini );
//        $("#data_fim").val( data_fim );
//        $("#tipo").val( tipo );
//
//        $( "#form_busca" ).submit();
//
//
//    }

    setInterval( function(){

        $.ck_hash_state();

    }, 100 );


    $.ck_hash_state = function () {

        hash_tag = $(location).attr('hash');

        if ( $.empty( hash_tag ) ) {
            app_hash = hash_tag;
            cont.html('');
        }

        if ( !$.empty( hash_tag ) ) {

            if ( hash_tag != app_hash ) {
                $.handle_form( "form_busca", 1 );
                app_hash = hash_tag;
            }

        }

    }

    $("input[name='del_foto[]']").live( "click", function(){

        var $this = $( this ); //guardando o ponteiro em uma variavel, por performance

        var id_foto = $this.val();

        //return alert(id_foto);

        var form_action = "ajax/ajax_foto_det.php";

        if ( $("#tipo").val() == 2 ){
            form_action = "ajax/ajax_foto_visit.php";
        }

        $.ajax_form_add( form_action, id_foto, 2, false, '' );

    });

    $("input#bt_submit").live( "click", function(){

        var form_id = "form_alter_img_det";

        if ( $("#tipo").val() == 2 ){
            form_id = "form_alter_img_visit";
        }

        $("#"+form_id).submit();

    });

    $("input#bt_cancel").live( "click", function(){

        $.fancybox.close();

    });

    // VALIDAÇÃO DO FORM DE CADASTRAMENTO/ALTERAÇÃO
    $("#form_alter_img_visit").live("submit", function() {

        $.fancybox.showActivity();

        $.ajax({
            type   : "POST",
            cache  : false,
            url    : caminho + "send/sendvisitimg.php",
            data   : $(this).serializeArray(),
            success: function( data ) {

                if ( data == 1 ) { // se retornar 1 obteve sucesso

                    $.fancybox.close(); // fechar o fancybox
                    $( "#form_busca" ).submit();

                } else { // se não, mostra a mensagem

                    $.fancybox( "Ocorreu um erro!!!" );

                } // /if ( data == 1 ) {

            } // /success: function( data ) {

        }); // /$.ajax({

        return false;

    }); // /$("#form_alter_img_visit").live("submit", function() {


    // VALIDAÇÃO DO FORM DE CADASTRAMENTO/ALTERAÇÃO
    $("#form_alter_img_det").live("submit", function() {

        $.fancybox.showActivity();

        $.ajax({
            type   : "POST",
            cache  : false,
            url    : caminho + "send/senddetimg.php",
            data   : $(this).serializeArray(),
            success: function( data ) {

                if ( data == 1 ) { // se retornar 1 obteve sucesso

                    $.fancybox.close(); // fechar o fancybox
                    $( "#form_busca" ).submit();

                } else { // se não, mostra a mensagem

                    $.fancybox( "Ocorreu um erro!!!" );

                } // /if ( data == 1 ) {

            } // /success: function( data ) {

        }); // /$.ajax({

        return false;

    }); // /$("#form_alter_img_det").live("submit", function() {

}); // /$(function(){
