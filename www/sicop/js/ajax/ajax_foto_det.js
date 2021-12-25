$(function(){

    var caminho = $('#js_caminho').val();

    /**
     * 1 = demais procedimentos
     * 2 = exclusão de foto especial
     */
    var proced_ajax = 1;

    // PARA FOTO DO DETENTO
    $("a.link_group_foto_det").fancybox({
        'transitionIn'  : 'elastic',
        'transitionOut' : 'elastic'
    });

    $("input[name='def_foto_det[]']").live( "click", function(){

        var $this = $( this );//guardando o ponteiro em uma variavel, por performance

        var id_foto = $this.val();

        proced_ajax = 1;

        $.ajax_form_add( "ajax/ajax_foto_det.php", id_foto, 1, false, '' );

    });

    $("input[name='del_foto_det[]']").live( "click", function(){

        var $this = $( this );//guardando o ponteiro em uma variavel, por performance

        var id_foto = $this.val();

        proced_ajax = 1;

        $.ajax_form_add( "ajax/ajax_foto_det.php", id_foto, 2, false, '' );

    });

    $("input[name='del_foto_det_esp[]']").live( "click", function(){

        var $this = $( this );//guardando o ponteiro em uma variavel, por performance

        var id_foto = $this.val();

        proced_ajax = 2;

        $.ajax_form_add( "ajax/ajax_foto_det.php", id_foto, 5, false, '' );

    });

    $("input#bt_submit").live( "click", function(){

        $("#form_alter_img_det").submit();

    });

    $("input#bt_cancel").live( "click", function(){

        $.fancybox.close();

    });

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

                    // página que fará a busca
                    var href = caminho + "ajax/ajax_foto_det_data.php";

                    // pegando o id do detento no input hidden
                    var iddet = $('#iddet').val();

                    // pega a div onde está os dados
                    var content = $('#album_principal');
                    if ( proced_ajax == 2 ) {
                        content = $('#album_especial');
                    }

                    // utilizo outro ajax para pegar os dados atualizados
                    $.ajax({
                        type   : "POST",
                        cache  : false,
                        url    : href,
                        data   : {
                            iddet: iddet,
                            proced: proced_ajax
                        },
                        success: function( response ){

                            //forçando o parser
                            var data = $( response );

                            content.html( data );

                            // chamando novamente o fancybox
                            $("a.link_group_foto_det").fancybox({
                                'transitionIn'  : 'elastic',
                                'transitionOut' : 'elastic'
                            });

                        }

                    });// /$.ajax({

                } else { // se não, mostra a mensagem

                    $.fancybox( "Ocorreu um erro!!!" );

                } // /if ( data == 1 ) {

            } // /success: function( data ) {

        }); // /$.ajax({

        return false;

    }); // /$("#form_alter_img_det").live("submit", function() {

});// /$(function(){