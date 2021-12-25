$(function(){

    $("input#print_aud").live( "click", function( e ){

        e.preventDefault();

        var aud_ck = $.get_checked( "idaud" );

        var quant_check = aud_ck.length;

        if( $.empty( quant_check ) ) {
            alert( "Você deve marcar alguém!!!" );
            return false
        }

        var campos = {
          "idaud": aud_ck
        };

        $.submit_new_form( "print/of_apr.php", "print", campos, 1, 1 );

        return false;


//        var ckb_checked = $.check_checkbox( 'aud_print' );
//
//        if ( !ckb_checked ) {
//
//            alert( 'Você deve marcar pelo menos uma audiência!' );
//            return false;
//
//        }
//
//        $.submit_form( 'aud_print', 'print/of_apr.php', 'new_win', 1 );
//
//        return true;

    });

    $("input#exp_aud").live( "click", function(){

        var ckb_checked = $.check_checkbox( 'aud_print' );

        if ( !ckb_checked ) {

            alert( 'Você deve marcar pelo menos uma audiência!' );
            return false;

        }

        $.submit_form( 'aud_print', 'export/exp_aud.php', '', 1 );
        return true;

    });

    $("input#exp_aud_tran").live( "click", function(){

        var ckb_checked = $.check_checkbox( 'aud_print' );

        if ( !ckb_checked ) {

            alert( 'Você deve marcar pelo menos uma audiência!' );
            return false;

        }

        $.submit_form( 'aud_print', 'export/exp_aud_trans.php', '', 1 );
        return true;

    });

});// /$(function(){