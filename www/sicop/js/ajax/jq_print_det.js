$(function(){

    $("input#print_quali").live( "click", function( e ){

        e.preventDefault();

        var iddet = $("input#iddet").val();

        var campos = {
          "iddet": iddet,
          "type": "quali",
          "incl": 1
        };

        var rand = $.rand();

        $.submit_new_form( "print/det_docs.php", "print_"+rand, campos, 1, 1 );

        return false;


    });

    $("input#print_3_quali").live( "click", function( e ){

        e.preventDefault();

        var iddet = $("input#iddet").val();

        var campos = {
          "iddet": iddet,
          "type" : "quali",
          "quant": 3,
          "incl" : 1
        };

        var rand = $.rand();

        $.submit_new_form( "print/det_docs.php", "print_"+rand, campos, 1, 1 );

        return false;


    });

    $("input#print_ficha").live( "click", function( e ){

        e.preventDefault();

        var iddet = $("input#iddet").val();

        var campos = {
          "iddet": iddet,
          "type": "plan"
        };

        var rand = $.rand();

        $.submit_new_form( "print/det_docs.php", "print_"+rand, campos, 1, 1 );

        return false;


    });

    $("input#print_cartao").live( "click", function( e ){

        e.preventDefault();

        var iddet = $("input#iddet").val();

        var campos = {
          "iddet": iddet,
          "type": "cartao"
        };

        var rand = $.rand();

        $.submit_new_form( "print/det_docs.php", "print_"+rand, campos, 1, 1 );

        return false;


    });

    $("input#print_all").live( "click", function( e ){

        e.preventDefault();

        var iddet = $("input#iddet").val();

        var campos = {
          "iddet": iddet,
          "type": "all",
          "incl": 1
        };

        var rand = $.rand();

        $.submit_new_form( "print/det_docs.php", "print_"+rand, campos, 1, 1 );

        return false;


    });


});
