$(function(){

    var caminho = $('#js_caminho').val();
    var caminho_img = $('#js_caminho_img').val();

    // pegando o id do visitante no input hidden
    var idvisit = $('#idvisit').val();

    /*
     * FUNÇÕES PARA O FANCYBOX
     */

    // PARA FOTO DO VISITANTE
    $("a#link_foto_visit").fancybox({
        'transitionIn'  : 'elastic',
        'transitionOut' : 'elastic'
    });

    // PARA O ALTERAÇÃO DE FOTO
    $("a#alter_foto_visit").live( "click", function(){

        $.ajax_form_add( "ajax/ajax_foto_visit.php", idvisit, 3, false, '' );

        return false;

    }); // /$("a#alter_foto_det").live( "click", function(){

    // VALIDAÇÃO DO FORM DE ALTERAÇÃO DE FOTO
    $("#form_alter_img_visit").live("submit", function() {

        var retorno = true;
        var err_msg = '';

        if ( retorno ) {
            var foto_det = $('#foto_visit').val();
            if ( foto_det == '' ){
                err_msg = 'Clique em "Procurar..." e escolha a foto!';
                retorno = false;
            }
        }


        if ( !retorno ) { // se não tiver validado
            $("#form_error").show();           // mostrar o paragrafo de erro
            $("#form_error").html( err_msg );  // troca a mensagem de erro
            $.fancybox.resize();               // redimensionar a janela do fancybox
            $("#n_raio").focus();
            return false;
        }

        $.fancybox.showActivity();

        return true;

    }); // /$("#form_alter_img_visit").live("submit", function() {


    //pre carregando o gif
    var loading = new Image();
    loading.src = caminho_img + 'system/loading.gif';
    var img_cont = '<p class="img_ajax"><img src="' + caminho_img + 'system/loading.gif" /></p>';

    // função para o link a#all_visit_in
    $('a#all_visit_in').live('click', function( e ){

        // previnindo a função default do link
        e.preventDefault();

        var content = $('#hist_visit');

        // colocando uma imagem de loading no lugar da tabela
        //content.html( img_cont );
        content.fadeTo( 200, 0.3 );

        // trocando a area que fica o link por uma mensagem
        $('span#span_all_visit').html( 'aguarde...' );

        // página que fará a busca
        var href = caminho + 'ajax/ajax_visit_hist_data.php';

        // abrindo o ajax
        $.ajax({
            type   : "POST",
            cache  : false,
            url    : href,
            data   : {uid: idvisit},
            success: function( response ){

                //forçando o parser
                var data = $( response );

                // fazendo a troca com esmaecimento
//                content.fadeOut( 'slow', function(){
//                    content.html( data ).fadeIn();
//                });


                setTimeout( function(){

                    // fazendo a troca com esmaecimento
                    content.html( data ).fadeTo( 500, 1);

                    // trocando a area que fica o link por uma mensagem
                    $('span#span_all_visit').html( 'mostrando todas as entradas' );

                }, 1000 );

            }
        });// /$.ajax({
    });// /$('a#all_obs').live('click', function( e ){


});// /$(document).ready(function(){