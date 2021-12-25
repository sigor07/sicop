// função para verificar se um elemento exsite
$.fn.exists = function () {
    return $(this).length > 0 ? true : false;
};

(function($) {

    // caminho absoluto
    var caminho = '';
    // caminho da página PHP com as funções para o ajax
    var href_combo = '';

    // só atribui valor para as variáveis após o carregamento da página
    $(function(){
       caminho    = $('#js_caminho').val();
       href_combo = caminho + 'ajax/ajax_combox.php';
    });

    $.ajax_time = function ( cont ) {

        var href = caminho + "ajax/ajax_relogio.php";

        $.ajax({
            url     : href,
            cache   : false,
            success : function( response ){
                cont.html( response );
            }
        });

    }

    /*
     * submita um form que já existe
     * @param form_id o id do form
     * @param href a página (action) do form. Se deixado em branco, vai usar o que já existe no form
     * @param target o target do form. Se usado, o form vai abrir uma nova janela para o submit
     * @param reset se for usado vai setar os valores de target e href para '' ( vai ficar em branco )
     */
    $.submit_form = function ( form_id, href, target, reset ) {

        //var caminho = $('#js_caminho').val();

        if( $.empty( form_id ) ) return false;

        var form = "#" + form_id

        if ( href != '' ) {
            $(form).attr( 'action', caminho + href );
        }

        if ( target != '' && target != undefined ) {

            $(form).attr( 'target', target );

            ow( '', '800', '600', target );

        }

        $(form).submit();

        if ( !$.empty( reset ) ) {

            if ( href == '' ) {
                $(form).attr( 'target', '' );
            } else {
                $(form).attr({
                    target: '',
                    action: ''
                });
            }// /if ( href != '' ) {

        } // /if ( reset ) {

        return true;

    }

    /**
     * cria um novo formulário, com campos, e submita
     * @param href a página (action) do form. Se deixado em branco, a função retorna false
     * @param target o target do form.
     * @param campos um array contendo os campos e valores que serão utilizados nos forms
     * @param nw Se usado, o form vai abrir uma nova janela para o submit
     * @param reset se for usado vai apagar o form da página
     */
    $.submit_new_form = function ( href, target, campos, nw, reset ) {

        if ( $.empty( href ) ) {
            return false;
        }

        href = caminho + href

        var campos_form = '';
        $.each( campos, function( campo, valor ) {
            campos_form += "<input type='hidden' name='"+campo+"' value='"+valor+"' />";
        });

        // monta o form com os parametros da função
        var form = '';
        form += "<form action='"+href+"' method='post' id='new_form' name='new_form' target='"+target+"'>";
        form += campos_form;
        form += "</form>";

        // envia o html para a página
        $("span#saida").html( form );

        // se tiver valor em nw, vai abrir uma nova janela
        if ( !$.empty( nw ) ) {
            ow( '', '800', '600', target );
        }

        // submita o form
        $("#new_form").submit();

        // se tiver valor em reset, vai apagar o form da página
        if ( !$.empty( reset ) ) {

            $("span#saida").html('');

        } // /if

        return true;

    }

    /**
     * submita um formulário já existente, usando ajax
     *
     * @param form_id o id do form que vai ser submitado
     * @param href o caminho do arquivo de destino do submit. Se ficar em branco, a função tentará pegar o valor do action do form. Se ainda assim ficar em branco, a função retorna FALSE.
     * @param data_form o dados do formulário. Se ficar em branco, a função tentará pegar os valores do form.
     * @param lock_form se o formulário vai ser travado para ser submitado
     *
     * @return o retorno da página do submit.
     */
    $.submit_form_ajax = function ( form_id, href, data_form, lock_form ) {

        var retorno = false;

        if( $.empty( form_id ) ) return retorno;

        form_id = "#"+form_id;

        if( $.empty( href ) ) href = $( form_id ).attr( 'action' );

        if( $.empty( href ) ) return retorno;

        href = caminho + href;

        if( $.empty( data_form ) ) data_form = $( form_id ).serializeArray();

        if( !$.empty( lock_form ) ) $.lock_form_disable( form_id );

        $.ajax({
            type   : "POST",
            async  : false,
            cache  : false,
            url    : href,
            data   : data_form,
            success: function( response ){
                //retorno = $( response );
                retorno = response;
            } // /success: function( response ){
        });// /$.ajax({

        if( !$.empty( lock_form ) ) $.unlock_form( form_id );

        return retorno

    } // /$.submit_form_ajax = function

    // PARA O FORMULÁRIO DE CADASTRAMENTO
    $.ajax_form_add = function ( url, uid, proced, modal, field_focus_open ) {

        if ( !$.empty( modal ) ) {
            modal = true;
        } else {
            modal = false;
        }

        $.fancybox({
            ajax        : {
                            cache: false,
                            type : "POST",
                            data : {
                                uid    : uid,
                                proced : proced
                            }
                          },
            'href'      : caminho + url,
            'scrolling' : 'no',
            'modal'     : modal,
            onComplete  : function() {
                            if ( !$.empty( field_focus_open ) ) {

                                $("#"+field_focus_open).focus();

                            }
                         }
        });

    }

    /**
     * pega os valores dos checkbox marcados e grava em um array
     * @param name o nome do array de checkbox que serão pegos os valores
     * @return o array com os valores dos checkbox marcados
     */
    $.get_checked = function ( name ) {

        var checkbox = "input[type=checkbox][name='"+name+"[]']:checked"
        var checked = new Array();

        $( checkbox ).each(function(){
            checked.push($(this).val());
        });

        return checked;

    }

    /*
     * verifica se há checkbox marcados
     * @param form_id o id do form que será feito a verificação
     * @return bool retorna TRUE se encontrar pelo menos 1 checkbox marcado
     **/
    $.check_checkbox = function ( form_id ) {

        var uid_form = '';
        var retorno  = false

        if ( form_id != '' && form_id != undefined ) {

            uid_form = "form#" + form_id + ' ';

        }

        var checkbox = $( uid_form + "input[type='checkbox']:checked");
        var cont     = 0;

        // $("form#aud_print input[type='checkbox']:checked").each(function(){
        checkbox.each(function(){

            cont += 1;

        });

        // se cont for maior q 0 quer dizer q o
        // usuário marcou um checkbox
        if ( cont > 0 ) {
            retorno = true;
        }

        return retorno;

    }

    /**
     * para verificar se uma variavel está vazia
     * semelhante a função empty() do php
     * retorna true para os seguintes casos:
     * * variavel = ""
     * * variavel = undefined
     * * variavel = null
     * * variavel = false
     * * variavel = 0
     * @param variavel o nome da variavel que será verificada
     * @return bool se a variavel for vazia ou false, se não for
     */
    $.empty = function ( variavel ) {

        var retorno = true;

        if( variavel != ""
         && variavel != undefined
         && variavel != null
         && variavel != false
         && variavel != 0 ){
            retorno = false;
        }

        return retorno

    }

    /**
     * para pegar a query string da url, já dividida em um array
     * @return array a query string já dividida em um array
     */
    $.get_qs = function () {

        // pegando a url
        var url = $(location).attr('href');

        // separando a query string do restante da url
        var query_string = url.replace( /\+/g," " ).split("?")[1];

        // se não tiver nada na query string, retorna null
        if( $.empty( query_string ) ) return null;

        // separando as variaveis da query string em um array
        var variaveis = query_string.split("&");

        return variaveis;

    }

    /**
     * para pegar os valores passados por $_GET, através do javascript
     * @param index o indice ( key ) que se quer o valor
     * @return string o valor da key ou null caso não seja encontrado
     */
    $.get_get = function ( index ) {

        // pegando a query string já no formato de array
        var variaveis = $.get_qs();
        var qs = null;
        var variavel;
        var nome;
        var valor;

        $.each( variaveis, function( id, value ){

            // a variavel é composta por "key=valor" e tem que ser
            // dividida em outro array
            variavel = value.split( '=' );

            nome = variavel[0];
            valor = variavel[1];

            // se o nome for igual ao index( que está sendo procurado )
            if ( nome == index ) {

                // para decodificar os valores das barras e espaços
                qs = unescape( valor );
                // retorna false para interromper a iteração
                return false;

            }

            return true;

        });

        return qs;

    }

    /**
     * para pegar os valores passados por $_GET, através do javascript
     * e joga-los nos campos dos formulários, e submitar o form em seguida
     * @param uid_form o ID do form que será manipulado
     * @param submit_form qualquer valor que não seja empty vai submitar o form após a manipulação
     * @return string o valor da key ou null caso não seja encontrado
     */
    $.handle_form = function ( uid_form, submit_form ) {

        if ( $.empty( uid_form ) ) {
            uid_form = "form_busca";
        }

        var variaveis = $.get_qs();

        var variavel;
        var nome;
        var valor;

        var inputs = $( "#" + uid_form + " input, #" + uid_form + " select");

        $.each( variaveis, function(){

            // a variavel é composta por "key=valor" e tem que ser
            // dividida em outro array
            variavel = this.split( '=' );

            nome = variavel[0];
            valor = variavel[1];

            inputs.each(function(){

                // se this.name for igual ao nome
                if ( nome == this.name ) {

                    // para decodificar os valores das barras e espaços
                    this.value = unescape( valor );
                    // retorna false para interromper a iteração
                    return false;

                }

                return true;

            });

            return true;

        });

        if ( !$.empty( submit_form ) ) {
            $( "#" + uid_form ).submit();
        }

    }


    /*
     * para bloquear o form para evitar multiplos submits
     * @param form_id o id do form que será feito o bloqueio
     * @return bool retorna TRUE sempre
     **/
    $.lock_form = function ( form_id ) {

        var uid_form = '';

        if ( form_id != '' && form_id != undefined ) {

            uid_form = "form#" + form_id + ' ';

        }

        // ReadOnly em todos os inputs
        $( uid_form + "input" ).attr("readonly", true);

        // Desabilita os selects
        $( uid_form + "select" ).attr("disabled", true);

        // Desabilita os submits
        $( uid_form + "input[type='submit'],input[type='image']" ).attr("disabled", true);

        //alert(uid_form);

        return true;

    }

    /*
     * para bloquear o form para evitar multiplos submits mas com disable
     * neste caso, os dados do formulário devem ser capturados antes do bloqueio
     * @param form_id o id do form que será feito o bloqueio
     * @return bool retorna TRUE sempre
     **/
    $.lock_form_disable = function ( form_id ) {

        var uid_form = '';

        if ( form_id != '' && form_id != undefined ) {

            uid_form = "form#" + form_id + ' ';

        }

        // Desabilita todos os inputs
        $( uid_form + "input" ).attr("disabled", true);

        // Desabilita os selects
        $( uid_form + "select" ).attr("disabled", true);

        return true;

    }


    /*
     * para desbloquear o form que foi bloqueado com as funções de bloqueio
     * @param form_id o id do form que será feito o desbloqueio
     * @return bool retorna TRUE sempre
     **/
    $.unlock_form = function ( form_id ) {

        var uid_form = '';

        if ( form_id != '' && form_id != undefined ) {

            uid_form = "form#" + form_id + ' ';

        }

        $(uid_form + 'input').removeAttr('readonly');
        $(uid_form + 'select').removeAttr('disabled');
        $(uid_form + 'input').removeAttr('disabled');

        return true;

    }

    /*
     * para montar uma selectbox para os campos #tipo_mov
     **/
    $.monta_box_tipo_mov = function () {

        //var caminho = $('#js_caminho').val();
        //var href_combo = caminho + 'ajax/ajax_combox.php';

        var box_tipo_mov = $('#tipo_mov');
        var old_tipo_mov = $('#old_tipo_mov').val();
        var sit_det      = $('#sit_det').val();

        // abrindo o ajax
        $.ajax({
            type   : "POST",
            cache  : false,
            url    : href_combo,
            data   : {tipo: 'tipo_mov', old_tipo_mov: old_tipo_mov, sit_det: sit_det},
            success: function( response ){

                //forçando o parser
                var data = $( response );

                box_tipo_mov.html( data );

            }
        });// /$.ajax({

        if ( old_tipo_mov != 0 || old_tipo_mov != '' ) {

            var tipo_mov      = old_tipo_mov;
            var old_local_mov = $('#old_local_mov').val();
            $.monta_box_local_mov( tipo_mov, old_local_mov );

        }

    }

    /*
     * para montar uma selectbox para os campos #tipo_mov na alteração da movimentação
     **/
    $.monta_box_tipo_mov_ant = function () {

        //var caminho = $('#js_caminho').val();
        //var href_combo = caminho + 'ajax/ajax_combox.php';

        var box_tipo_mov   = $('#tipo_mov');
        var tipo_mov_atual = $('#tipo_mov_atual').val();
        var tipo_mov_ant   = $('#tipo_mov_ant').val();
        var sit_det        = $('#sit_det').val();
        var iddet          = $('#iddet').val();

        // abrindo o ajax
        $.ajax({
            type   : "POST",
            cache  : false,
            url    : href_combo,
            data   : {tipo: 'tipo_mov_ant', iddet: iddet, sit_det: sit_det, tipo_mov_atual: tipo_mov_atual, tipo_mov_ant: tipo_mov_ant},
            success: function( response ){

                //forçando o parser
                var data = $( response );

                box_tipo_mov.html( data );

            }
        });// /$.ajax({

        if ( tipo_mov_atual != 0 || tipo_mov_atual != '' ) {

            var tipo_mov      = tipo_mov_atual;
            var old_local_mov = $('#old_local_mov').val();
            $.monta_box_local_mov( tipo_mov, old_local_mov );

        }

    }

    /*
     * para montar uma selectbox para os campos #local_mov
     **/
    $.monta_box_local_mov = function ( tipo_mov, old_local_mov ) {

        //var caminho    = $('#js_caminho').val();
        //var href_combo = caminho + 'ajax/ajax_combox.php';

        var box_local_mov = $('#local_mov');

        var id_tipo_mov   = tipo_mov;

        if ( id_tipo_mov == 0 || id_tipo_mov == '' || id_tipo_mov == undefined ) {

            id_tipo_mov = $('#tipo_mov').val();

        }

        if ( id_tipo_mov == 0 || id_tipo_mov == '' ) {

            box_local_mov.html( '<option value="">Selecione o tipo de movimentação</option>' );

        } else {

            box_local_mov.html( '<option value="">Aguarde...</option>' );

            $.ajax({
                type   : "POST",
                cache  : false,
                url    : href_combo,
                data   : {tipo: 'local', tipo_mov: id_tipo_mov, old_local_mov: old_local_mov},
                success: function( response ){

                    //forçando o parser
                    var data = $( response );

                    box_local_mov.html( data );

                }

            });// /$.ajax({

        } // /if ( id_tipo_mov == 0 || id_tipo_mov == '' ) {

    }

    /*
     * para montar uma selectbox para os campos #uf (estado)
     **/
    $.monta_box_uf = function () {

        //var caminho = $('#js_caminho').val();
        //var href_combo = caminho + 'ajax/ajax_combox.php';

        var box_uf = $('#uf');
        var old_uf = $('#old_uf').val();

        // abrindo o ajax
        $.ajax({
            type   : "POST",
            cache  : false,
            url    : href_combo,
            data   : {tipo: 'estado', old_uf: old_uf},
            success: function( response ){

                //forçando o parser
                var data = $( response );

                box_uf.html( data );

            }
        });// /$.ajax({

        if ( old_uf != 0 || old_uf != '' ) {

            var estado     = old_uf;
            var old_cidade = $('#old_cidade').val();
            $.monta_box_cidade( estado, old_cidade );

        }

    }

    /*
     * para montar uma selectbox para os campos #cidade
     **/
    $.monta_box_cidade = function ( estado, old_cidade ) {

        //var caminho    = $('#js_caminho').val();
        //var href_combo = caminho + 'ajax/ajax_combox.php';

        var box_cidade = $('#cidade');

        var id_uf = estado;

        if ( id_uf == 0 || id_uf == '' || id_uf == undefined ) {

            id_uf = $('#uf').val();

        }

        if ( id_uf == 0 || id_uf == '' ) {

            box_cidade.html( '<option value="">Selecione o estado</option>' );

        } else {

            box_cidade.html( '<option value="">Aguarde...</option>' );

            $.ajax({
                type   : "POST",
                cache  : false,
                url    : href_combo,
                data   : {tipo: 'cidade', uf: id_uf, old_cidade: old_cidade},
                success: function( response ){

                    //forçando o parser
                    var data = $( response );

                    box_cidade.html( data );

                }

            });// /$.ajax({

        } // /if ( id_tipo_mov == 0 || id_tipo_mov == '' ) {

    }

    /*
     * para montar uma selectbox para os campos #n_raio
     **/
    $.monta_box_raio = function () {

        //var caminho = $('#js_caminho').val();
        //var href_combo = caminho + 'ajax/ajax_combox.php';

        var box_raio = $('#n_raio');
        var old_raio = $('#old_raio').val();

        // abrindo o ajax
        $.ajax({
            type   : "POST",
            cache  : false,
            url    : href_combo,
            data   : {tipo: 'raio', old_raio: old_raio},
            success: function( response ){

                //forçando o parser
                var data = $( response );

                box_raio.html( data );

            }
        });// /$.ajax({

        if ( old_raio != 0 || old_raio != '' ) {

            var raio     = old_raio;
            var old_cela = $('#old_cela').val();
            $.monta_box_cela( raio, old_cela );

        }

    }

    /*
     * para montar uma selectbox para os campos #n_cela
     **/
    $.monta_box_cela = function ( raio, old_cela ) {

        //var caminho    = $('#js_caminho').val();
        //var href_combo = caminho + 'ajax/ajax_combox.php';

        var box_cela = $('#n_cela');

        var idraio = raio;

        if ( idraio == 0 || idraio == '' || idraio == undefined ) {

            idraio = $('#n_raio').val();

        }

        if ( idraio == 0 || idraio == '' ) {

            box_cela.html( '<option value="">Selecione o raio</option>' );

        } else {

            box_cela.html( '<option value="">Aguarde...</option>' );

            $.ajax({
                type   : "POST",
                cache  : false,
                url    : href_combo,
                data   : {tipo: 'cela', raio: idraio, old_cela: old_cela},
                success: function( response ){

                    //forçando o parser
                    var data = $( response );

                    box_cela.html( data );

                }

            });// /$.ajax({

        } // /if ( id_tipo_mov == 0 || id_tipo_mov == '' ) {

    }

    /*
     * para verificar se uma matrícula ja está cadastrada no sistema
     * se a matrícula existir, informa
     * precisa dos campos #matricula e #old_matr
     **/
    $.ck_matr_exist = function () {

        //var caminho    = $('#js_caminho').val();
        //var href_combo = caminho + 'ajax/ajax_combox.php';

        var matr_val     = $('#matricula').val();
        var old_matr_val = $('#old_matr').val();
        matr_val         = matr_val.replace(/[.-]/g,'');
        old_matr_val     = old_matr_val.replace(/[.-]/g,'');

        if ( ( matr_val != old_matr_val ) && ( matr_val != 0 && matr_val != '' && matr_val != undefined ) ){

            $.ajax({
                type   : "POST",
                cache  : false,
                url    : href_combo,
                data   : {tipo: 'matr', matr: matr_val},
                success: function( response ){

                    if ( response == 1 ) {
                        alert( 'Esta matrícula já está cadastrada! Verifique!' );
                        $('#matricula').focus();
                    } // /if ( response == 1 ) {

                } // /success: function( response ){

            });// /$.ajax({

        } // /if ( ( matr_val != old_matr_val ) && ( matr_val != 0 && matr_val != '' && matr_val != undefined ) ){

    } // /$.ck_matr_exist = function () {

    /*
     * para verificar se um cpf ja está cadastrado no sistema
     * se a matrícula existir, informa
     * precisa dos campos #matricula e #old_matr
     **/
    $.ck_cpf_exist = function () {

        //var caminho    = $('#js_caminho').val();
        //var href_combo = caminho + 'ajax/ajax_combox.php';

        var cpf_val     = $('#cpf').val();
        var old_cpf_val = $('#old_cpf').val();
        cpf_val         = cpf_val.replace(/[.-]/g,'');
        old_cpf_val     = old_cpf_val.replace(/[.-]/g,'');

        if ( ( cpf_val != old_cpf_val ) && ( cpf_val != 0 && cpf_val != '' && cpf_val != undefined ) ){

            $.ajax({
                type   : "POST",
                cache  : false,
                url    : href_combo,
                data   : {tipo: 'cpf', cpf: cpf_val},
                success: function( response ){

                    if ( response == 1 ) {
                        alert( 'Este CPF já está cadastrado! Verifique!' );
                        $('#cpf').focus();
                    } // /if ( response == 1 ) {

                } // /success: function( response ){

            });// /$.ajax({

        } // /if ( ( matr_val != old_matr_val ) && ( matr_val != 0 && matr_val != '' && matr_val != undefined ) ){

    } // /$.ck_matr_exist = function () {

    $.ck_rgv_exist = function () {

        var rg_visit     = $('#rg_visit').val();
        var old_rg_visit = $('#old_rg_visit').val();
        rg_visit         = rg_visit.replace(/[.-]/g,'');
        old_rg_visit     = old_rg_visit.replace(/[.-]/g,'');

        var retorno = false;

        if ( ( rg_visit != old_rg_visit ) && ( rg_visit != 0 && rg_visit != '' && rg_visit != undefined ) ){

            $.ajax({
                type   : "POST",
                async  : false,
                cache  : false,
                url    : href_combo,
                data   : {tipo: 'rgv', rgv: rg_visit},
                success: function( data ){
                    if ( data == 1 ) {
                        retorno = true;
                    } // /if ( response == 1 ) {
                } // /success: function( response ){
            });// /$.ajax({

        } // /if ( ( matr_val != old_matr_val ) && ( matr_val != 0 && matr_val != '' && matr_val != undefined ) ){

        return retorno

    } // /$.ck_rgv_exist = function () {

    $.redir = function ( href ) {

        if ( $.empty( href ) ) {
            href = "home.php";
        }

        $(window.document.location).attr( "href", caminho + href );

    }

    $.replace = function ( href ) {

        if ( $.empty( href ) ) {
            href = "home.php";
        }

        window.location.replace( caminho + href );

    }

    /**
     * retorna um numero randomico inteiro
     * @param min o menor número que pode ser retornado
     * @param max o maior número que pode ser retornado
     * @return um número inteiro randomico
     * @example rand(1, 1); retorno 1
     */
    $.rand = function ( min, max ) {

        // Returns a random number
        //
        // version: 1109.2015
        // discuss at: http://phpjs.org/functions/rand
        // +   original by: Leslie Hoare
        // +   bugfixed by: Onno Marsman
        // %        note 1: See the commented out code below for a version which will work with our experimental (though probably unnecessary) srand() function)
        // *     example 1: rand(1, 1);
        // *     returns 1: 1
        var argc = arguments.length;
        if (argc === 0) {
            min = 0;
            max = 2147483647;
        } else if (argc === 1) {
            throw new Error('Warning: rand() expects exactly 2 parameters, 1 given');
        }
        return Math.floor(Math.random() * (max - min + 1)) + min;

        /**
         * See note above for an explanation of the following alternative code
         *
         * +   reimplemented by: Brett Zamir (http://brett-zamir.me)
         * -         depends on: srand
         * %             note 1: This is a very possibly imperfect adaptation from the PHP source code
         *                       var rand_seed, ctx, PHP_RAND_MAX=2147483647; // 0x7fffffff
         *
         * if (!this.php_js || this.php_js.rand_seed === undefined) {
         *     this.srand();
         * }
         * rand_seed = this.php_js.rand_seed;
         *
         * var argc = arguments.length;
         * if (argc === 1) {
         *     throw new Error('Warning: rand() expects exactly 2 parameters, 1 given');
         * }
         *
         *
         * var do_rand = function (ctx) {
         *     return ((ctx * 1103515245 + 12345) % (PHP_RAND_MAX + 1));
         * };
         *
         * var php_rand = function (ctxArg) { // php_rand_r
         *     this.php_js.rand_seed = do_rand(ctxArg);
         *     return parseInt(this.php_js.rand_seed, 10);
         * };
         *
         * var number = php_rand(rand_seed);
         *
         * if (argc === 2) {
         *     number = min + parseInt(parseFloat(parseFloat(max) - min + 1.0) * (number/(PHP_RAND_MAX + 1.0)), 10);
         * }
         * return number;
         */
    }

})(jQuery);