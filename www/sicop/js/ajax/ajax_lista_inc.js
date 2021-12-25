$(function(){

    var caminho = $('#js_caminho').val();

    // PARA FOTO DO DETENTO
    $("a.link_foto_det").fancybox({
        'transitionIn'  : 'elastic',
        'transitionOut' : 'elastic'

    });

    // PARA O ALTERAÇÃO DE FOTO
    $("input[name='alter_foto_det[]']").live( "click", function(){

        var $this = $( this );//guardando o ponteiro em uma variavel, por performance

        var iddet = $this.val();

        $.ajax_form_add( "ajax/ajax_foto_det.php", iddet, 3, false, '' );

        return false;

    }); // /$("input[name='alter_foto_det[]']").live( "click", function(){

    // PARA O ALTERAÇÃO DE FOTOS ESPECIAIS
    $("input[name='alter_foto_esp_det[]']").live( "click", function(){

        var $this = $( this );//guardando o ponteiro em uma variavel, por performance

        var iddet = $this.val();

        $.ajax_form_add( "ajax/ajax_foto_esp_det.php", iddet, 3, false, '' );

        return false;

    }); // /$("input[name=

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

    // PARA IMPRIMIR O PECÚLIO
    $("input[name='print_pec_det[]']").live( "click", function(){

        var $this = $( this );//guardando o ponteiro em uma variavel, por performance

        var iddet = $this.val();

        ow(caminho + 'incl/print_pec.php?iddet='+iddet+'&targ=1', '830', '600');

        return false;

    });

    $("img.print_doc_det").live( "click", function( e ){

        e.preventDefault();

        var $this = $( this );//guardando o ponteiro em uma variavel, por performance

        var iddet = $this.next("input").val();

        $.ajax_form_add( "ajax/ajax_print_det.php", iddet, 0, false, '' );

        return false;

    });

    $("input#print_quali_m").live( "click", function( e ){

        e.preventDefault();

        var dets_ck = $.get_checked( "iddet" );

        var quant_check = dets_ck.length;

        if( $.empty( quant_check ) ) {
            alert( "Você deve marcar alguém!!!" );
            return false
        }

        var campos = {
          "iddet":dets_ck,
          "type": "quali"
        };

        var rand = $.rand();

        $.submit_new_form( "print/det_docs_m.php", "print_"+rand, campos, 1, 1 );

        return false

    });

    $("input#print_ficha_m").live( "click", function( e ){

        e.preventDefault();

        var dets_ck = $.get_checked( "iddet" );

        var quant_check = dets_ck.length;

        if( $.empty( quant_check ) ) {
            alert( "Você deve marcar alguém!!!" );
            return false
        }

        var campos = {
          "iddet":dets_ck,
          "type": "plan"
        };

        var rand = $.rand();

        $.submit_new_form( "print/det_docs_m.php", "print_"+rand, campos, 1, 1 );

        return false;

    });

    $("input#print_cartao_m").live( "click", function( e ){

        e.preventDefault();

        var dets_ck = $.get_checked( "iddet" );

        var quant_check = dets_ck.length;

        if( $.empty( quant_check ) ) {
            alert( "Você deve marcar alguém!!!" );
            return false
        }

        var campos = {
          "iddet": dets_ck,
          "type": "cartao"
        };

        var rand = $.rand();

        $.submit_new_form( "print/det_docs_m.php", "print_"+rand, campos, 1, 1 );

        return false;

    });

    $("input#print_doc_rol").live( "click", function( e ){

        e.preventDefault();

        var rand = $.rand();

        $.submit_new_form( 'print/rec_visit.php', "new_win_"+rand, '', 1, 1 );

        return false;

    });

});
