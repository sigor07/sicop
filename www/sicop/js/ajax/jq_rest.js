$(function(){

    $("input#print").live( "click", function(){

        $.submit_form( 'form_termo', 'print/rest_mp.php', 'new_win', 1 );

    });

});// /$(function(){