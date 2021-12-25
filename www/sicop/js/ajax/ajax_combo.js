$(function(){

//    (function($) {
//
//        $.el_exists = function ( el_id ) {
//
//            return $(el_id).length > 0 ? true : false;
//
//        }
//
//    })(jQuery);
//
//    if ( $.el_exists( '#n_raio' ) ) {
//        alert('rafa');
//    }


    var caminho = $('#js_caminho').val();
    var href = caminho + 'ajax/ajax_combox.php';

    // para os combos de raio e cela
    var box_raio = $('#n_raio');

    if ( box_raio.exists() ) {

        //$("#n_raio").focus();

        var old_raio = $('#old_raio').val();

        // abrindo o ajax
        $.ajax({
            type   : "POST",
            cache  : false,
            url    : href,
            data   : {tipo: 'raio', old_raio: old_raio},
            success: function( response ){

                //forçando o parser
                var data = $( response );

                box_raio.html( data );

            }
        });// /$.ajax({

        if ( old_raio != 0 || old_raio != '' ) {

            var box_cela = $('#n_cela');
            var idraio   = old_raio;
            var old_cela = $('#old_cela').val();

            $.ajax({
                type   : "POST",
                cache  : false,
                url    : href,
                data   : {tipo: 'cela', raio: idraio, old_cela: old_cela},
                success: function( response ){

                    //forçando o parser
                    var data = $( response );

                    box_cela.html( data );

                    // causa um atraso para colocar o foco
                    // no box do raio
                    //setTimeout( function(){
                    //    box_raio.focus();
                    //}, 200 );

                }

            });// /$.ajax({


        }

        box_raio.change( function(){

            var box_cela = $('#n_cela');
            var idraio   = $(this).val();

            if ( idraio == 0 || idraio == '' ) {

                box_cela.html( '<option value="">Selecione o raio</option>' );

            } else {

                box_cela.html( '<option value="">Aguarde...</option>' );

                $.ajax({
                    type   : "POST",
                    cache  : false,
                    url    : href,
                    data   : {tipo: 'cela', raio: idraio},
                    success: function( response ){

                        //forçando o parser
                        var data = $( response );

                        box_cela.html( data );

                    }

                });// /$.ajax({

            } // /if ( idraio == 0 || idraio == '' ) {

        }); // /box_raio.change( function(){

    } // /if ( box_raio.exists() ) {
    // /para os combos de raio e cela


    // para os combos de tipo e local de movimentação
    var box_tipo_mov = $('#tipo_mov');

    if ( box_tipo_mov.exists() ) {

        var old_tipo_mov = $('#old_tipo_mov').val();
        var sit_det      = $('#sit_det').val();

        // abrindo o ajax
        $.ajax({
            type   : "POST",
            cache  : false,
            url    : href,
            data   : {tipo: 'tipo_mov', old_tipo_mov: old_tipo_mov, sit_det: sit_det},
            success: function( response ){

                //forçando o parser
                var data = $( response );

                box_tipo_mov.html( data );

            }
        });// /$.ajax({

        if ( old_tipo_mov != 0 || old_tipo_mov != '' ) {

            var box_local_mov = $('#local_mov');
            var tipo_mov      = old_tipo_mov;
            var old_local_mov = $('#old_local_mov').val();

            $.ajax({
                type   : "POST",
                cache  : false,
                url    : href,
                data   : {tipo: 'local', tipo_mov: tipo_mov, old_local_mov: old_local_mov},
                success: function( response ){

                    //forçando o parser
                    var data = $( response );

                    box_local_mov.html( data );

                }

            });// /$.ajax({

        }

        box_tipo_mov.change( function(){

            var box_local_mov = $('#local_mov');
            var id_tipo_mov   = $(this).val();

            if ( id_tipo_mov == 0 || id_tipo_mov == '' ) {

                box_local_mov.html( '<option value="">Selecione o tipo de movimentação</option>' );

            } else {

                box_local_mov.html( '<option value="">Aguarde...</option>' );

                $.ajax({
                    type   : "POST",
                    cache  : false,
                    url    : href,
                    data   : {tipo: 'local', tipo_mov: id_tipo_mov},
                    success: function( response ){

                        //forçando o parser
                        var data = $( response );

                        box_local_mov.html( data );

                    }

                });// /$.ajax({

            } // /if ( id_tipo_mov == 0 || id_tipo_mov == '' ) {

        }); // /box_tipo_mov.change( function(){

    } // /if ( box_tipo_mov.exists() ) {
    // /para os combos de tipo e local de movimentação


    // para os combos de cidade/estado
    var box_uf = $('#uf');

    if ( box_uf.exists() ) {

        var old_uf = $('#old_uf').val();

        // abrindo o ajax
        $.ajax({
            type   : "POST",
            cache  : false,
            url    : href,
            data   : {tipo: 'estado', old_uf: old_uf},
            success: function( response ){

                //forçando o parser
                var data = $( response );

                box_uf.html( data );

            }
        });// /$.ajax({

        if ( old_uf != 0 || old_uf != '' ) {

            var box_cidade = $('#cidade');
            var estado     = old_uf;
            var old_cidade = $('#old_cidade').val();

            $.ajax({
                type   : "POST",
                cache  : false,
                url    : href,
                data   : {tipo: 'cidade', uf: estado, old_cidade: old_cidade},
                success: function( response ){

                    //forçando o parser
                    var data = $( response );

                    box_cidade.html( data );

                }

            });// /$.ajax({

        }

        box_uf.change( function(){

            var box_cidade = $('#cidade');
            var estado     = $(this).val();

            if ( estado == 0 || estado == '' ) {

                box_cidade.html( '<option value="">Selecione o estado</option>' );

            } else {

                box_cidade.html( '<option value="">Aguarde...</option>' );

                $.ajax({
                    type   : "POST",
                    cache  : false,
                    url    : href,
                    data   : {tipo: 'cidade', uf: estado},
                    success: function( response ){

                        //forçando o parser
                        var data = $( response );

                        box_cidade.html( data );

                    }

                });// /$.ajax({

            } // /if ( estado == 0 || estado == '' ) {

        }); // /box_uf.change( function(){

    } // /if ( box_uf.exists() ) {
    // /para os combos de cidade/estado



    var box_perm  = $('#n_nivel');
    var box_setor = $('#n_setor');

    if ( box_perm.exists() ) {

        if ( !box_setor.exists() ) {

            var old_nivel = $('#old_nivel').val();
            var visit     = $('#visit').val();

            // abrindo o ajax
            $.ajax({
                type   : "POST",
                cache  : false,
                url    : href,
                data   : {
                          tipo     : 'perm',
                          visit    : visit,
                          old_nivel: old_nivel
                      },
                success: function( response ){

                    //forçando o parser
                    var data = $( response );

                    box_perm.html( data );

                }

            });// /$.ajax({

        }// /if ( !box_setor.exists() ) {

    }// /if ( box_perm.exists() ) {

    if ( box_setor.exists() ) {

        var iduser    = $('#iduser').val();

        var perm_type = $('#perm_type').val();

        // abrindo o ajax
        $.ajax({
            type   : "POST",
            cache  : false,
            url    : href,
            data   : {
                      tipo      : 'n_setor',
                      iduser    : iduser,
                      perm_type : perm_type
                     },
            success: function( response ){

                //forçando o parser
                var data = $( response );

                box_setor.html( data );

            }

        });// /$.ajax({

        box_setor.change( function(){

            if ( perm_type == 1 ) {

                var box_perm = $('#n_nivel');
                var idsetor  = $(this).val();

                if ( idsetor == 0 || idsetor == '' ) {

                    box_perm.html( '<option value="">Selecione o setor</option>' );

                } else {

                    box_perm.html( '<option value="">Aguarde...</option>' );

                    visit = 0;

                    if ( idsetor == 38 ) {
                        visit = 1;
                    }

                    $.ajax({
                        type   : "POST",
                        cache  : false,
                        url    : href,
                        data   : {
                                  tipo : 'perm',
                                  visit: visit
                              },
                        success: function( response ){

                            //forçando o parser
                            var data = $( response );

                            box_perm.html( data );

                        }

                    });// /$.ajax({

                } // /if ( idsetor == 0 || idsetor == '' ) {

            } // if ( perm_type == 1) {

        });// /box_setor.change( function(){

    }// /if ( box_setor.exists() ) {

});// /$(function(){
