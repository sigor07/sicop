
$(function(){

    $("a#print_aud").live( "click", function( e ){

        e.preventDefault();

        var idaud = $("input#idaud").val();

        var campos = {
          "idaud": idaud
        };

        $.submit_new_form( "print/of_apr.php", "print", campos, 1, 1 );

        return false;

    });

});// /$(function(){