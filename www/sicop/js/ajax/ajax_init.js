$(function() {

    var caminho = $('#js_caminho').val();
    var wdl     = $(window.document.location);

    /**
     * -------------------------------
     * parte responsável pelo menu
     * -------------------------------
     */
    $('ul.sf-menu').superfish({
        delay       : 500,                             // one second delay on mouseout
        animation   : {
            opacity:'show',
            height:'show'
        }, // fade-in and slide-down animation
        speed       : 'fast',                          // faster animation speed
        dropShadows : false                            // disable drop shadows
    });

    /*
     * /-------------------------------
     */


    /**
     * -------------------------------
     * parte responsável pelo relógio
     * -------------------------------
     */

    // id do locao onde o relógio aparece
    var cont_time = $("#relogio");
    // endereço da página para a requisição
    var href_time = caminho + "ajax/ajax_relogio.php";

    $.ajax_time( cont_time, href_time );

    setInterval( function(){

        $.ajax_time( cont_time, href_time );

    }, 60000 );

    /*
     * /-------------------------------
     */


    /**
     * -------------------------------
     * parte responsável pelos atalhos do teclado
     * -------------------------------
     */

    shortcut.add("Ctrl+A",function() {
        alert("Hi there!");
    });
    shortcut.add("f1",function() {
        alert("Hi there!");
    });
    shortcut.add("f2",function() {
        alert("Hi there!");
    });
    shortcut.add("f3",function() {
        $.redir( "buscadet.php" );
    });
    shortcut.add("f4",function() {
        alert("Hi there!");
    });
    shortcut.add("f6",function() {
        alert("Hi there!");
    });
    shortcut.add("f7",function() {
        alert("Hi there!");
    });
    shortcut.add("f8",function() {
        alert("Hi there!");
    });
    shortcut.add("f9",function() {
        alert("Hi there!");
    });
    shortcut.add("f10",function() {
        alert("Hi there!");
    });
    shortcut.add("f11",function() {
        alert("Hi there!");
    });
    //    shortcut.add("f12",function() {
    //        alert("Hi there!");
    //    });
    shortcut.add("Ctrl+s",function() {
        alert("Hi there!");
    });

    /*
     * /-------------------------------
     */

    $("#print_list_cnj").live( "click", function( e ){

        e.preventDefault();
        $.submit_new_form( 'print/lista_cnj.php', 'lista_cnj', '', 1, 1 );

    });

    $("#print_list_cnj_rc").live( "click", function( e ){

        e.preventDefault();
        $.submit_new_form( 'print/lista_cnj_rc.php', 'lista_cnj_rc', '', 1, 1 );

    });

    $("#print_pop_cnj").live( "click", function( e ){

        e.preventDefault();
        $.submit_new_form( 'print/mapa_cnj.php', 'mapa_cnj', '', 1, 1 );

    });


});

