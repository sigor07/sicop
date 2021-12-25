$(function(){

    // pegando o id do detento no input hidden
    var iddet = $('#iddet').val();
    var caminho = $('#js_caminho').val();
    var caminho_img = $('#js_caminho_img').val();

    /*
     * FUNÇÕES PARA O FANCYBOX
     */

    // PARA FOTO DO DETENTO
    $("a#link_foto_det").fancybox({
        'transitionIn'  : 'elastic',
        'transitionOut' : 'elastic'
    });

    // PARA ABRIR O FORMULÁRIO DE ALTERAÇÃO DE RAIO E CELA
    $(".link_add_rc").fancybox({
        ajax        : {
                        cache: false,
                        type : "POST",
                        data : {iddet: iddet}
                      },
        'href'      : caminho + "ajax/ajax_rc_det_add.php", // página que o fancybox abrirá
        'scrolling' : 'no',
        onComplete  : function() {
                         //$("#n_raio").focus();
                      }
    });

    // VALIDAÇÃO DO FORM DE ALTERAÇÃO DE RAIO E CELA
    $("#form_rc").live("submit", function() {

        var retorno = true;
        var err_msg = '';

        if ( retorno ) {
            var n_raio = $('#n_raio').val();
            if ( n_raio == '' || n_raio == 0 ){
                err_msg = 'Informe o raio.';
                retorno = false;
            }
        }

        if ( retorno ) {
            var n_cela = $('#n_cela').val();
            if ( n_cela == '' || n_cela == 0 ){
                err_msg = 'Informe a cela.';
                retorno = false;
            }
        }

        if ( retorno ) {
            var old_cela = $('#old_cela').val();
            if ( n_cela == old_cela ){
                err_msg = 'Ou o raio ou a cela devem ter um novo valor.';
                retorno = false;
            }
        }

        if ( retorno ) {
            var data_rc = $('#data_rc').val();
            if ( data_rc == '' || data_rc == 0 ){
                err_msg = 'Informe a data da movimentação.';
                retorno = false;
            }
        }


        if ( retorno ) {
            var datahj = $('#datahj').val();
            var dthj = parseInt( datahj.split( '/' )[2].toString() + datahj.split( '/' )[1].toString() + datahj.split( '/' )[0].toString() );
            var dtrc = parseInt( data_rc.split( '/' )[2].toString() + data_rc.split( '/' )[1].toString() + data_rc.split( '/' )[0].toString() );

            if ( dtrc > dthj ){
                err_msg = 'A data da movimentação não pode ser futura.';
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

        $.ajax({
            type   : "POST",
            cache  : false,
            url    : caminho + "send/senddetrc.php",
            data   : $(this).serializeArray(),
            success: function( data ) {

                if ( data == 1 ) { // se retornar 1 obteve sucesso

                    $.fancybox.close(); // fechar o fancybox

                    // página que fará a busca
                    var href = caminho + "ajax/ajax_rc_det_q.php";

                    // pega o local do raio e cela
                    var raio = $('#raio');
                    var cela = $('#cela');

                    // utilizo outro ajax para pegar os dados atualizados
                    $.ajax({
                        type   : "POST",
                        cache  : false,
                        url    : href,
                        data   : {iddet: iddet, limit: 1},
                        success: function( response ){

                            //forçando o parser
                            //var data = $( response );

                            var n_raio = $( '<div>'+response+'</div>' ).find('#raio').html();
                            var n_cela = $( '<div>'+response+'</div>' ).find('#cela').html();

                            // fazendo a troca com esmaecimento
                            raio.fadeOut( 'slow', function(){
                                raio.html( n_raio ).fadeIn();
                            });

                            cela.fadeOut( 'slow', function(){
                                cela.html( n_cela ).fadeIn();
                            });

                        }
                    });// /$.ajax({

                } else { // se não, mostra a mensagem

                    $.fancybox( "Ocorreu um erro!!!" );

                }// /if ( data == 1 ) {

            } // /success: function( data ) {

        }); // /$.ajax({

        return false;
    }); // /$("#form_rc").live("submit", function() {




    // PARA ABRIR O FORMULÁRIO DE CADASTRAMENTO DE OBSERVAÇÃO
    $("#link_add_obs").fancybox({
        ajax        : {
                        cache: false,
                        type : "POST",
                        data : {iddet: iddet}
                      },
        'href'      : caminho + "ajax/ajax_obs_det.php", // página que o fancybox abrirá
        'scrolling' : 'no',
        onComplete  : function() {
                         $("#obs_det").focus();
                      }
    });

    // PARA O FORMULÁRIO DE ALTERAÇÃO DE OBSERVAÇÃO
    $("input[name='edit_obs_det[]']").live( "click", function(){
        var $this = $( this );//guardando o ponteiro em uma variavel, por performance

        var idobs = $this.val();

        $.fancybox({
            // configurar o ajax do fancybox
            ajax        : {
                            cache: false,  // não cachear a página
                            type : "POST", // tipo de requisição
                            data : {       // dados que serão enviados PARA O FORMUÁRIO
                                iddet: iddet,
                                idobs: idobs,
                                edit : 1
                            }

                          },
            'href'      : caminho + "ajax/ajax_obs_det.php", // página que o fancybox abrirá
            'scrolling' : 'no',
            onComplete  : function() {
                             $("#obs_det").focus();
                          }
        });
    });


    // PARA O FORMULÁRIO DE EXCLUSÃO DE OBSERVAÇÃO
    $("input[name='del_obs_det[]']").live( "click", function(){

        var $this = $( this );//guardando o ponteiro em uma variavel, por performance

        var idobs = $this.val();

        $.fancybox({
            // configurar o ajax do fancybox
            ajax        : {
                            cache: false,  // não cachear a página
                            type : "POST", // tipo de requisição
                            data : {       // dados que serão enviados PARA O FORMUÁRIO
                                iddet: iddet,
                                idobs: idobs,
                                del  : 1
                            }

                          },
            //modal       : true,
            'href'      : caminho + "ajax/ajax_obs_det.php", // página que o fancybox abrirá
            'scrolling' : 'no'

        }); // $.fancybox({
    }); // $("input[name='del_obs_det[]']").live( "click", function(){

    $("input#bt_cancel").live( "click", function(){
        $.fancybox.close();
    });


    // VALIDAÇÃO DO FORM DE CADASTRAMENTO/ALTERAÇÃO DE OBSERVAÇÃO
    $("#form_obs").live("submit", function() {

        if ($("#proced").val() != 2 ) {            // se o procedimento não for 2 (exclusão)
            if ($("#obs_det").val().length < 1 ) { // se não tiver nada digitado
                $("#form_error").show();           // mostrar o paragrafo de erro
                $.fancybox.resize();               // redimensionar a janela do fancybox
                return false;
            }
        }

        $.fancybox.showActivity();

        $.ajax({
            type   : "POST",
            cache  : false,
            url    : caminho + "send/senddetobs.php",
            data   : $(this).serializeArray(),
            success: function( data ) {
                if ( data == 1 ) { // se retornar 1 obteve sucesso

                    $.fancybox.close(); // fechar o fancybox

                    // página que fará a busca
                    var href = caminho + "ajax/ajax_obs_det_q.php";
                    // pega a div onde está os dados
                    var content = $('#table_obs');
                    // utilizo outro ajax para pegar os dados atualizados
                    $.ajax({
                        type   : "POST",
                        cache  : false,
                        url    : href,
                        data   : {iddet: iddet, limit: 1},
                        success: function( response ){

                            //forçando o parser
                            var data = $( response );

                            // fazendo a troca com esmaecimento
                            content.fadeOut( 'slow', function(){
                                content.html( data ).fadeIn();
                            });

                            $('span#span_all_obs').html( 'Mostrando as 10 últimas observações - <a id="all_obs" href="javascript:void(0)">Mostrar todas</a>' );

                        }
                    });// /$.ajax({

                } else { // se não, mostra a mensagem

                    $.fancybox( "Ocorreu um erro!!!" );

                }// /if ( data == 1 ) {

            } // /success: function( data ) {

        }); // /$.ajax({

        return false;
    }); // /$("#form_obs").live("submit", function() {




    /*
     * /FUNÇÕES PARA O FANCYBOX
     */


    //pre carregando o gif
    var loading = new Image();
    loading.src = caminho_img + 'system/loading.gif';
    var img_cont = '<p class="img_ajax"><img src="' + caminho_img + 'system/loading.gif" /></p>';

    // função para o link a#all_obs
    $('a#all_obs').live('click', function( e ){

        // previnindo a função default do link
        e.preventDefault();

        var content = $('#table_obs');

        // colocando uma imagem de loading no lugar da tabela
        content.html( img_cont );

        // página que fará a busca
        var href = caminho + 'ajax/ajax_obs_det_q.php';

        // abrindo o ajax
        $.ajax({
            type   : "POST",
            cache  : false,
            url    : href,
            data   : {iddet: iddet},
            success: function( response ){

                //forçando o parser
                var data = $( response );

                // fazendo a troca com esmaecimento
                content.fadeOut( 'slow', function(){
                    content.html( data ).fadeIn();
                });

                // trocando a area que fica o link por uma mensagem
                $('span#span_all_obs').html( 'mostrando todas as observações' );

            }
        });// /$.ajax({
    });// /$('a#all_obs').live('click', function( e ){



    // função para o link a#all_mov
    $('a#all_mov').live('click', function( e ){

        var content = $('#table_mov');

        // previnindo a função default do link
        e.preventDefault();

        // colocando uma imagem de loading no lugar da tabela
        content.html( img_cont );

        // página que fará a busca
        var href = caminho + 'ajax/ajax_mov_det_q.php';

        // abrindo o ajax
        $.ajax({
            type   : "POST",
            cache  : false,
            url    : href,
            data   :{iddet: iddet},
            success: function( response ){

                //forçando o parser
                var data = $( response );

                // fazendo a troca com esmaecimento
                content.fadeOut( 'slow', function(){
                    content.html( data ).fadeIn();
                });

                // trocando a area que fica o link por uma mensagem
                $('span#span_all_mov').html( 'mostrando todas as movimentações' );

            }
        });// /$.ajax({
    });// /$('a#all_mov').live('click', function( e ){


    // função para o link a#all_aud
    $('a#all_aud').live('click', function( e ){

        var content = $('#table_aud');

        // previnindo a função default do link
        e.preventDefault();

        // colocando uma imagem de loading no lugar da tabela
        content.html( img_cont );

        // página que fará a busca
        var href = caminho + 'ajax/ajax_aud_det_q.php';

        // abrindo o ajax
        $.ajax({
            type   : "POST",
            cache  : false,
            url    : href,
            data   :{iddet: iddet},
            success: function( response ){

                //forçando o parser
                var data = $( response );

                // fazendo a troca com esmaecimento
                content.fadeOut( 'slow', function(){
                    content.html( data ).fadeIn();
                });

                // trocando a area que fica o link por uma mensagem
                $('span#span_all_aud').html( 'mostrando todas as audiências' );

            }
        });// /$.ajax({
    });// /$('a#all_aud').live('click', function( e ){


    // PARA O ALTERAÇÃO DE FOTO
    $("a#alter_foto_det").live( "click", function(){

        $.ajax_form_add( "ajax/ajax_foto_det.php", iddet, 3, false, '' );

        return false;

    }); // /$("input[name='alter_foto_det[]']").live( "click", function(){

    // VALIDAÇÃO DO FORM DE ALTERAÇÃO DE FOTO
    $("#form_alter_img_det").live("submit", function() {

        var retorno = true;
        var err_msg = '';

        if ( retorno ) {
            var foto_det = $('#foto_det').val();
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

    }); // /$("#form_alter_img_det").live("submit", function() {

    $("a#print_doc_det").live( "click", function( e ){

        e.preventDefault();

        $.ajax_form_add( "ajax/ajax_print_det.php", iddet, 0, false, '' );

        return false;

    });

});// /$(function(){