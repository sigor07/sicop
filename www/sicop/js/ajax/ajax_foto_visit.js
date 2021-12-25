$(function(){

    var caminho = $('#js_caminho').val();

    // PARA FOTO DO VISITANTE
    $("a.link_group_foto_visit").fancybox({
        'transitionIn'  : 'elastic',
        'transitionOut' : 'elastic'
    });

    $("input[name='def_foto_visit[]']").live( "click", function(){

        var $this = $( this );//guardando o ponteiro em uma variavel, por performance

        var id_foto = $this.val();

        $.ajax_form_add( "ajax/ajax_foto_visit.php", id_foto, 1, false, '' );

    });

    $("input[name='del_foto_visit[]']").live( "click", function(){

        var $this = $( this );//guardando o ponteiro em uma variavel, por performance

        var id_foto = $this.val();

        $.ajax_form_add( "ajax/ajax_foto_visit.php", id_foto, 2, false, '' );

    });

    $("input#bt_submit").live( "click", function(){

        $("#form_alter_img_visit").submit();

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

                    // página que fará a busca
                    var href = caminho + "ajax/ajax_foto_visit_data.php";

                    // pega a div onde está os dados
                    var content = $('#album_principal');

                    // pegando o id do visitante no input hidden
                    var idvisit = $('#idvisit').val();

                    // utilizo outro ajax para pegar os dados atualizados
                    $.ajax({
                        type   : "POST",
                        cache  : false,
                        url    : href,
                        data   : { idvisit: idvisit },
                        success: function( response ){

                            //forçando o parser
                            var data = $( response );

                            content.html( data );

                            // chamando novamente o fancybox
                            $("a.link_group_foto_visit").fancybox({
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

    }); // /$("#form_alter_img_visit").live("submit", function() {

});// /$(function(){