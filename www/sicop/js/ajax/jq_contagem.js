$(function(){

    $("#print_map").live( "click", function( e ){

        e.preventDefault();
        $.submit_new_form( 'print/mapa_pop.php', 'mapa', '', 1, 1 );

    });

});// /$(function(){