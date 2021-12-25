$(function(){

    $("a#print_lista").live( "click", function( e ){

        e.preventDefault();
        $.submit_form( 'lista_det', 'print/lista_busca.php', 'new_win', 1 );

        return false;

    });

    $("a#exp_lista").live( "click", function( e ){

        e.preventDefault();
        $.submit_form( 'lista_det', 'export/exp_busca.php', '', 1 );

        return false;

    });

});// /$(function(){