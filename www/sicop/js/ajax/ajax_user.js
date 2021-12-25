$(function(){

    var caminho    = $('#js_caminho').val();
    var iduser     = $('#iduser').val();                       // pegando o id do detento no input hidden
    var href_fancy = caminho + "ajax/ajax_user_perm_form.php"; // página que o fancybox abrirá
    var href_data  = caminho + "ajax/ajax_user_perm_data.php"; // página para atualizar as permissões
    var href_user  = caminho + "ajax/ajax_user_form.php";      // página para o form do usuário
    var perm_type  = 0;                                        // 1 = comum; 2 impressão; 3 = especial

    // para atualizar as permissões
    (function($) {

        $.ajax_refresh_perm = function ( href ) {

            var cont = $('#table_user_perm'); // pega a div onde está os dados

            if ( perm_type == 1 ) {

                cont = $('#table_user_perm');

            } else if ( perm_type == 2 ) {

                cont = $('#table_user_perm_imp');

            } else if ( perm_type == 3 ) {

                cont = $('#table_user_perm_esp');

            }

            $.ajax({
                type   : "POST",
                cache  : false,
                url    : href,
                data   : {
                    iduser    : iduser,
                    perm_type : perm_type
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

    // PARA O FORMULÁRIO DE CADASTRAMENTO
    (function($) {

        $.ajax_form_add_perm = function ( refresh_oc ) {

            $.fancybox({
                ajax        : {
                                cache: false,
                                type : "POST",
                                data : {
                                    iduser    : iduser,
                                    add       : 1,
                                    perm_type : perm_type
                                }
                              },
                'href'      : href_fancy,
                'scrolling' : 'no',
                onComplete  : function() {
                                 $("#n_setor").focus();
                              },
                onClosed    : function() {

                                if ( refresh_oc == 1 ) {
                                    $.ajax_refresh_perm( href_data ); // função para atualizar o conteudo das permissões
                                }

                              }
            });

        }

    })(jQuery);

    // PARA O FORMULÁRIO DE EXCLUSÃO
    (function($) {

        $.ajax_form_drop_user = function ( id ) {

            $.fancybox({
                ajax        : {
                    cache: false,
                    type : "POST",
                    data : {
                        iduser    : id,
                        del       : 1
                    }
                },
                'href'      : href_user,
                'scrolling' : 'no'
            });

        }

    })(jQuery);


    $("#link_add_perm").live( "click", function(){

        perm_type = 1;

        $.ajax_form_add_perm();

    });

    $("#link_add_perm_imp").live( "click", function(){

        perm_type = 2;

        $.ajax_form_add_perm();

    });

    $("#link_add_perm_esp").live( "click", function(){

        perm_type = 3;

        $.ajax_form_add_perm();

    });


    // PARA O FORMULÁRIO DE ALTERAÇÃO
    $("input[name='edit_user_perm[]']").live( "click", function(){

        var $this = $( this );//guardando o ponteiro em uma variavel, por performance

        var idperm = $this.val();

        var visit  = $this.next("input[name='visit']").val();

        $.fancybox({
            // configurar o ajax do fancybox
            ajax        : {
                            cache: false,  // não cachear a página
                            type : "POST", // tipo de requisição
                            data : {       // dados que serão enviados PARA O FORMUÁRIO
                                iduser: iduser,
                                idperm: idperm,
                                visit : visit,
                                edit  : 1
                            }

                          },
            'href'      : href_fancy,
            'scrolling' : 'no',
            onComplete  : function() {
                             $("#n_nivel").focus();
                          }
        });

    });

    $("input#bt_cancel").live( "click", function(){
        $.fancybox.close();
    });

    // PARA O FORMULÁRIO DE EXCLUSÃO DE OBSERVAÇÃO
    $("input[name='del_user_perm[]']").live( "click", function(){

        var $this  = $( this );//guardando o ponteiro em uma variavel, por performance

        var idperm = $this.val();

        perm_type  = $this.next("input[name='perm_type']").val();

        $.fancybox({
            // configurar o ajax do fancybox
            ajax        : {
                            cache: false,  // não cachear a página
                            type : "POST", // tipo de requisição
                            data : {       // dados que serão enviados PARA O FORMUÁRIO
                                     iduser: iduser,
                                     idperm: idperm,
                                     del   : 1
                                   }

                          },
            //modal       : true,
            'href'      : href_fancy,
            'scrolling' : 'no'

        }); // $.fancybox({

    }); // $("input[name='del_obs_det[]']").live( "click", function(){


    var cadadd = 0;
    $("input#bt_cadadd").live( "click", function(){
        cadadd = 1;
        $("#form_perm").submit();
    });


    // VALIDAÇÃO DO FORM DE CADASTRAMENTO/ALTERAÇÃO
    $("#form_perm").live("submit", function() {

        var proced = $("#proced").val();

        if ( proced != 2 ) {  // se o procedimento não for 2 (exclusão)

            if ( $("#n_setor").exists() ) {

                if ($("#n_setor").val().length < 1 ) {            // se não tiver nada digitado
                    $("#form_error").show();                      // mostrar o paragrafo de erro
                    $("#form_error").html( 'Escolha o setor.' );  // troca a mensagem de erro
                    $.fancybox.resize();                          // redimensionar a janela do fancybox
                    return false;
                }

            }

            if ( $("#n_nivel").exists() ) {

                if ($("#n_nivel").val().length < 1 ) {                      // se não tiver nada digitado
                    $("#form_error").show();                                // mostrar o paragrafo de erro
                    $("#form_error").html( 'Escolha o nível de acesso.' );  // troca a mensagem de erro
                    $.fancybox.resize();                                    // redimensionar a janela do fancybox
                    return false;
                }

            } //if ( $("#n_setor").exists() )

        }// if ( proced != 2 )

        $.fancybox.showActivity();

        $.ajax({
            type   : "POST",
            cache  : false,
            url    : caminho + "send/senduserperm.php",
            data   : $(this).serializeArray(),
            success: function( data ) {

                if ( data == 1 ) { // se retornar 1 obteve sucesso

                    if ( cadadd == 1 ) { // se tiver cadadd é porque vai adicionar outra permissão

                        cadadd = 0; // resetar o valor de cadadd

                        $.fancybox.hideActivity();

                        $.ajax_form_add_perm( 1 ); // reabrir o form de cadastramento

                    } else {

                        $.fancybox.close(); // fechar o fancybox

                        if ( proced == 1 ) { // se for atualização, coloca os valores de permissões comuns

                            perm_type = 1;

                        }

                        $.ajax_refresh_perm( href_data ); // atualizar o conteudo das permissões

                    }

                } else { // se não, mostra a mensagem

                    $.fancybox( "Ocorreu um erro!!!" );

                } // /if ( data == 1 ) {

            } // /success: function( data ) {

        }); // /$.ajax({

        return false;

    }); // /$("#form_perm").live("submit", function() {


    // PARA CHAMAR A EXCLUSÃO DE USUSÁRIO
    $("#link_drop_user").live( "click", function(){

        $.ajax_form_drop_user( iduser );

    });

    // PARA CHAMAR A EXCLUSÃO DE USUSÁRIO NA PÁGINA DE LISTAGEM
    $("input[name='link_drop_user_arr[]']").live( "click", function(){

        var $this      = $( this );//guardando o ponteiro em uma variavel, por performance

        var iduser_arr = $this.val();

        $.ajax_form_drop_user( iduser_arr );

    }); // $("input[name='del_obs_det[]']").live( "click", function(){


    $("#link_reset_pass").live( "click", function(){

        $.fancybox({
            ajax        : {
                cache: false,
                type : "POST",
                data : {
                    iduser     : iduser,
                    reset_pass : 1
                }
            },
            'href'      : href_user,
            'scrolling' : 'no'
        });

    });

   // VALIDAÇÃO DO FORM DE EXCLUSÃO DE USUÁRIO/RESET DE SENHA
    $("#form_user").live("submit", function() {

        $.fancybox.showActivity();

        $.ajax({
            type   : "POST",
            cache  : false,
            url    : caminho + "send/senduser_aux.php",
            data   : $(this).serializeArray(),
            success: function( data ) {

                if ( data >= 1 ) { // se retornar 1 obteve sucesso

                    $.fancybox.close(); // fechar o fancybox

                    if ( data == 2 ) {
                        $.replace( "user/listauser.php" );
                    }

                } else { // se não, mostra a mensagem

                    $.fancybox( "Ocorreu um erro!!!" );

                } // /if ( data == 1 ) {

            } // /success: function( data ) {

        }); // /$.ajax({

        return false;

    }); // /$("#form_perm").live("submit", function() {

});// /$(document).ready(function(){
