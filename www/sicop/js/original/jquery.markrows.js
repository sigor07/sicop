/*
 * ESTE ARQUIVO CONTÉM O CÓDIGO PARA MARCAÇÃO DE TABELAS
 *
 * Author:  William Bruno
 * Version: N/A
 * URL:     http://wbruno.com.br/blog/2011/03/20/selecionar-todos-checkb-ao-clicar-em-um-selecionar-check-ao-clicar-em-linha/
 *
 * *** COM ALTERAÇÕES ***
 *
 * Minified:
 *  (   ) SIM
 *  ( X ) NÃO
 *
 * DATA: 12/08/2011
 *
 * PARA FUNCIONAR:
 * a tabela deve ter class="grid"
 * e as tags <thead> <tbody> e <tfoot>
 *
 * o input do checkbox precisa ter a class "mark_row"
 */

$(function(){

    /* ao clicar no todos, seleciona todos e altera a class de todas as linhas */
    $("input[name='todos']").click(function(){

        var ckd = $( this ).attr('checked');

        $(".grid input[type='checkbox']").attr({
            checked: ckd
        });

        toogle_class( ckd, $('.grid tbody tr') );

    });

    /* ao clicar no checkbox, altera a class da linha */
    $("input.mark_row").click(function(){
        toogle_class( $( this ).attr('checked'), $( this ).parents('tr') );
    });

    $(".grid tbody tr").click(function( e ){

        var tagName = e.target.tagName.toLowerCase();

        if( tagName != "input" && tagName != "img" ) {

            var checkbox = $( this ).find("input[type='checkbox']");
            var ckd = !checkbox.attr('checked');

            checkbox.attr('checked', ckd);
            toogle_class( ckd, $( this ) );

        }

    });

});

function toogle_class( ckd, el ){
    if( ckd==true ) {
        el.addClass('marked');
    } else {
        el.removeClass('marked');
    }
}
