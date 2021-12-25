/**
 * ---------------------------------------------------------
 * função para checar se a matrícula foi digitada
 * corretamente comparando a matrícula com o digito
 * verificador. Retira pontos e traço da matrícula
 * para chamar: onBlur="checkmatr(this, this.value);"
 * ---------------------------------------------------------
 */

function check_d_matr( campo, valormatr ){

    if ( valormatr == '' ) { //se nao tiver valor retorna nada
        return '';
    }

    var retorno = true;
    valormatr   = valormatr.replace(/[.-]/g,'' ); //retira pontos e traço do número
    var comp    = valormatr.length //pega o comprimento

    if ( comp < 2 ) {
        alert( 'Valor incorreto!' );
        campo.focus();
        retorno = false;
    }

    if ( retorno ) {

        valormatr  = valormatr.substring(0, comp - 1);
        var digito = caldmatr(valormatr);
        var d = dig( campo );

        if ( digito != d ){
            campo.focus();
            retorno = false;
        }

    }

    return retorno;

}

 var timeDiff  =  {
     setStartTime:function (){
         d = new Date();
         time  = d.getTime();
     },

     getDiff:function (){
         d = new Date();
         return (d.getTime()-time);
     }
 }

/**
 * Função para pegar os elementos das paginas pelo id
 * @param el str - o id do elemento que será capturado
 */
function id( el ){
    return document.getElementById( el );
}

/**
 * Função para verificar se o campo não está em branco.
 * Devolve o foco para o campo passado no parametro.
 * @param field str - o id do elemento que será capturado
 * @param modo bool - true devolve o foco para o campo
 */
function ck_valor( field, modo ){

    var retorno = true;
    var foco    = true;

    var valor   = id( field ).value;

    if ( !modo ) {
        foco = false;
    }

    if( valor == '' ){
        if ( foco ){
            var campo = id( field );
            campo.focus();
        }
        retorno = false;
    }

    return retorno;

}

/**
 * Função para verificar se o campo está marcado.
 * Devolve o foco para o campo passado no parametro.
 * @param field str - o id do elemento que será capturado
 * @param modo bool - true devolve o foco para o campo
 */
function is_checked( field, modo ){

    var retorno = false;
    var foco    = true;
    var campo   = id( field );

    if ( !modo ) {
        foco = false;
    }

    if ( campo.checked ) {
        retorno = true;
    }

    if ( foco ){
        campo.focus();
    }

    return retorno;

}

/**
 * Função para validar o index.php
 */
function validaIndex(){

    var retorno = true;

    if ( retorno ) {

        var login = id( 'login' );
        if( login.value == '' ){
            alert( 'Digite seu LOGIN.' );
            login.focus();
            retorno = false;
        }

    }

    if ( retorno ) {

        var senha = id( 'senha' );
        if( senha.value == '' ){
            alert( 'Digite sua SENHA.' );
            senha.focus();
            retorno = false;
        }

    }

    return retorno;

}

/**
 * Função para validar 'cadastrauser.php'
 */
function validacadastrauser( modo ){

    if( ( modo == 0 ) || ( modo == '' ) || ( isNaN( modo ) ) ){
        modo = 2;
    }

    var retorno = true;

    if ( retorno ) {

        var nomeuser = id( 'nomeuser' );
        if( nomeuser.value == '' ){
            alert( 'Digite o nome do usuário.' );
            nomeuser.focus();
            retorno = false;
        }

    }

    if ( retorno ) {

        var nome_cham = id( 'nome_cham' );
        if( nome_cham.value == '' ){
            alert( 'Digite o primeiro nome do usuário.' );
            nome_cham.focus();
            retorno = false;
        }

    }

    if ( retorno ) {

        var usuario = id( 'usuario' );
        if( usuario.value == '' ){
            alert( 'Digite o nome de acesso do usuário.' );
            usuario.focus();
            retorno = false;
        }

    }

    if ( retorno ) {

        if ( modo == 1 ) {

            var senha = id( 'senha' );
            if( senha.value == '' ){
                alert( 'Digite uma senha para o usuário.' );
                senha.focus();
                retorno = false;
            }

        }

    }

    if ( retorno ) {

        var cargo = id( 'cargo' );
        if( cargo.value == '' ){
            alert( 'Informe o cargo.' );
            cargo.focus();
            retorno = false;
        }

    }

    if ( retorno ) {

        var idsetor = id( 'cod_setor' );
        if( idsetor.value == '' ){
            alert( 'Informe o setor.' );
            idsetor.focus();
            retorno = false;
        }

    }

    return retorno;

}

// Função para validar "cadastradet.php" e "editdet.php"
function valida_det(){

    var retorno = true;

    if ( retorno ) {
        if( !ck_valor( 'nome_det' , 1 ) ){
            alert( 'Digite o nome do detento.' );
            retorno = false;
        }
    }

    if ( retorno ) {
        if ( is_checked( 'fuga_0' ) && !ck_valor( 'local_fuga' , 1 ) ){
            alert( 'Informe o local de fulga.' );
            retorno = false;
        }
    }

    if ( retorno ) {
        if ( ck_valor( 'uf' ) && !ck_valor( 'cidade' , 1 ) ){
            alert( 'Informe a cidade.' );
            retorno = false;
        }
    }

    if ( retorno ) {

        var valormatr  = id( 'matricula' ).value;

        if( valormatr != '' ) {

            var campo_matr = id( 'matricula' );

            valormatr = valormatr.replace(/[.-]/g,'' ); //retira pontos e traço do número
            valormatr = valormatr.slice(0, -1);

            var digito = caldmatr( valormatr );
            var d = dig( campo_matr );

            if ( digito != d ){

                id( 'matricula' ).focus();
                retorno = false;
            }

        }

    }

    return retorno;

}

// Função para validar 'movdet.php'
function validacadastramovdet( modo ){

    var retorno   = true;
    var tipo_mov  = id( 'tipo_mov' );
    var local_mov = id( 'local_mov' );

    var inverso = modo != undefined ? true : false;

    if ( retorno ) {
        if ( tipo_mov.value == '' ){
            alert( 'Informe o tipo de movimentação.' );
            tipo_mov.focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        if ( ( tipo_mov.value != '4' && local_mov.value == '' ) && ( tipo_mov.value != '8' && local_mov.value == '' ) ){
            alert( 'Informe a procedência/destino do detento.' );
            local_mov.focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        var data_mov  = id( 'data_mov' );
        if ( data_mov.value == '' ){
            alert( 'Informe a data da movimentação.' );
            data_mov.focus();
            retorno = false;
        }
    }

    if ( retorno ) {

        var datahj   = id( 'datahj' ).value;
        var v_dt_mov = id( 'data_mov' ).value;

        var dthj = parseInt( datahj.split( '/' )[2].toString() + datahj.split( '/' )[1].toString() + datahj.split( '/' )[0].toString() );
        var dtmv = parseInt( v_dt_mov.split( '/' )[2].toString() + v_dt_mov.split( '/' )[1].toString() + v_dt_mov.split( '/' )[0].toString() );

        if ( dtmv > dthj ){
            alert( 'A data da movimentação não pode ser futura.' );
            id( 'data_mov' ).focus();
            retorno = false;
        }

    }

    if ( retorno ) {

        var data_ult = id( 'data_ult' ).value;

        if ( data_ult != '' ) {

            var dtut = parseInt( data_ult.split( '/' )[2].toString() + data_ult.split( '/' )[1].toString() + data_ult.split( '/' )[0].toString() );

            if ( !inverso ) {

                if ( dtmv < dtut ){
                    alert( 'A data da movimentação atual não pode ser anterior à da última movimentação.' );
                    id( 'data_mov' ).focus();
                    retorno = false;
                }

            } else {

                if ( dtmv > dtut ){
                    alert( 'A data da movimentação do acervo não pode ser posterior à da última movimentação de inclusão.' );
                    id( 'data_mov' ).focus();
                    retorno = false;
                }

            }


        }

    }

    return retorno;

}

// Função para validar 'movrcdet.php'
function validacadrcdet(){

    // timeDiff.setStartTime()

    var retorno = true;

    if ( retorno ) {
        if ( !ck_valor( 'n_raio' , 1 ) ){
            alert( 'Informe o raio.' );
            retorno = false;
        }
    }

    if ( retorno ) {
        if ( !ck_valor( 'n_cela' , 1 ) ){
            alert( 'Informe a cela.' );
            retorno = false;
        }
    }

    if ( retorno ) {
        var n_cela = id( 'n_cela' );
        var old_cela = id( 'old_cela' );
        if ( n_cela.value == old_cela.value ){
            alert( 'Ou o raio ou a cela devem ter um novo valor.' );
            id( 'n_raio' ).focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        if ( !ck_valor( 'data_rc' , 1 ) ){
            alert( 'Informe a data da movimentação.' );
            retorno = false;
        }
    }

    if ( retorno ) {
        var data_rc = id( 'data_rc' ).value;
        var datahj = id( 'datahj' ).value;
        var dthj = parseInt( datahj.split( '/' )[2].toString() + datahj.split( '/' )[1].toString() + datahj.split( '/' )[0].toString() );
        var dtrc = parseInt( data_rc.split( '/' )[2].toString() + data_rc.split( '/' )[1].toString() + data_rc.split( '/' )[0].toString() );

        if ( dtrc > dthj ){
            alert( 'A data da movimentação não pode ser futura.' );
            id( 'data' ).focus();
            retorno = false;
        }
    }

    // alert( timeDiff.getDiff() );

    return retorno;

}

// Função para validar observações
function valida_obs( field ){

    var retorno = true;
    var campo = id( field );

    if ( campo.value == '' ){
        alert( 'Digite a observação.' );
        campo.focus();
        retorno = false;
    }

    return retorno;

}

function validacadobstv(){

    obs = id( 'obs_tv' );

    if(obs.value=='' ){
        alert( 'Digite a observação.' );
        obs.focus();
        return (false);
    }
}

function validacadobsradio(){

    obs = id( 'obs_radio' );

    if(obs.value=='' ){
        alert( 'Digite a observação.' );
        obs.focus();
        return (false);
    }
}

// Função para validar 'cadaliasdet.php'
function validacadaliasdet(){

    var retorno = true;

    if ( retorno ) {

        if( !ck_valor( 'tipoalias' , 1 ) ){
            alert( 'Selecione o tipo de alias.' );
            retorno = false;
        }

    }

    if ( retorno ) {

        if( !ck_valor( 'alias_det' , 1 ) ){
            alert( 'Digite o alias.' );
            retorno = false;
        }

    }

    return retorno;

}

// Função para validar 'cadimgdet.php'
function validacadimgdet(){

    var retorno = true;
    var img     = id( 'foto_det' );

    if ( img.value == '' ){
        alert( 'Clique em "Procurar..." e escolha a foto para o detento.' );
        img.focus();
        retorno = false;
    }

    return retorno;

}

// Função para validar 'cadimgvisit.php'
function validacadimgvisit(){

    var retorno = true;

    if ( !ck_valor( 'foto_visit' , 1 ) ){
        alert( 'Clique em "Procurar..." e escolha a foto para o visitante.' );
        retorno = false;
    }

    return retorno;

}

// Função para validar 'cadastravisit.php' e 'editvisit.php'
function validacadvisit(){

    var retorno = true;

    if ( retorno ) {
        if ( !ck_valor( 'nome_visit' , 1 ) ){
            alert( 'Digite o nome do visitante.' );
            retorno = false;
        }
    }

    if ( retorno ) {
        if ( !is_checked( 'sexo_visit_0' , 1  ) && !is_checked( 'sexo_visit_1' ) ){
            alert( 'Escolha o sexo do visitante.' );
            retorno = false;
        }
    }

    if ( retorno ) {
        if ( !ck_valor( 'idparentesco' , 1 ) ){
            alert( 'Escolha o grau de parentesco do visitante com o detento.' );
            retorno = false;
        }
    }

    if ( retorno ) {
        if ( ck_valor( 'uf' ) && !ck_valor( 'cidade' , 1 ) ){
            alert( 'Informe a cidade.' );
            retorno = false;
        }
    }

    return retorno;

}

// Funçaõ para validar 'alterasenha.php'
function validasenha(){

    var retorno    = true;
    var nova_senha = id( 'nova_senha' );
    var conf_senha = id( 'conf_senha' );

    if ( retorno ) {
        if ( nova_senha.value == '' ){
            alert( 'Digite a NOVA SENHA.' );
            nova_senha.focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        if ( nova_senha.value.length < 6 ){
            alert( 'A senha deve ter no mínimo 6 caracteres.' );
            nova_senha.focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        if ( conf_senha.value == '' ){
            alert( 'Digite a SENHA NOVAMENTE.' );
            conf_senha.focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        if ( nova_senha.value != conf_senha.value ){
            alert( 'As SENHAS digitadas são DIFERENTES.' );
            nova_senha.value='';
            conf_senha.value='';
            nova_senha.focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        if ( !ck_valor( 'senha_atual' , 1 ) ){
            alert( 'Digite sua SENHA ATUAL.' );
            retorno = false;
        }
    }

    return retorno;

}

// Função para validar 'cadpda.php'
function validacadpda(){

    var retorno   = true;

    if ( retorno ) {
        if ( !ck_valor( 'num_pda' , 1 ) ){
            alert( 'Digite o número do PDA.' );
            retorno = false;
        }
    }

    if ( retorno ) {
        if ( !ck_valor( 'ano_pda' , 1 ) ){
            alert( 'Digite o ano do PDA.' );
            retorno = false;
        }
    }

    if ( retorno ) {
        if ( !ck_valor( 'data_ocorrencia' , 1 ) ){
            alert( 'Digite a data da ocorrência.' );
            retorno = false;
        }
    }

    if ( retorno ) {

        if ( is_checked( 'sit_pda_1' ) ){

            if ( !ck_valor( 'situacaodet' , 1 ) ){
                alert( 'Informe a situação do detento.' );
                retorno = false;
            }

            if ( retorno ) {
                var situacaodet = id( 'situacaodet' );
                if( situacaodet.value > 2){
                    if ( !ck_valor( 'data_reabilit' , 1 ) ){
                        alert( 'Digite a data da reabilitação.' );
                        retorno = false;
                    }
                }
            }

        }

    }

    return retorno;

}

// Função para validar 'cad_atendente.php'
function v_cad_ati_user(){

    var retorno   = true;

    if ( retorno ) {
        if ( !ck_valor( 'ati_user_nome' , 1 ) ){
            alert( 'Digite o nome do atendente.' );
            retorno = false;
        }
    }

    if ( retorno ) {
        if ( !ck_valor( 'ati_user_cargo' , 1 ) ){
            alert( 'Selecione o cargo.' );
            retorno = false;
        }
    }

    if ( retorno ) {
        if ( !ck_valor( 'ati_user_doc' , 1 ) ){
            alert( 'Informe o documento do atendente.' );
            retorno = false;
        }
    }

    return retorno;

}

function validavinculapda(){

    var retorno   = true;
    var matricula = id( 'matricula' );

    if ( matricula.value == '' ){
        alert( 'Digite a matrícula.' );
        matricula.focus();
        retorno = false;
    } else {
        retorno = checkmatr( matricula, matricula.value );
    }

    return retorno;

}

function validaeditup(){

    var retorno = true;

    if ( retorno ) {
        if ( !ck_valor( 'secretaria' , 1 ) ){
            alert( 'Digite a secretaria.' );
            retorno = false;
        }
    }

    if ( retorno ) {
        if ( !ck_valor( 'coordenadoria' , 1 ) ){
            alert( 'Digite a coordenadoria.' );
            retorno = false;
        }
    }

    if ( retorno ) {
        if ( !ck_valor( 'unidadelongo' , 1 ) ){
            alert( 'Digite o nome completo da unidade.' );
            retorno = false;
        }
    }

    if ( retorno ) {
        if ( !ck_valor( 'unidadecurto' , 1 ) ){
            alert( 'Digite o nome abreviado da unidade.' );
            retorno = false;
        }
    }

    if ( retorno ) {
        if ( !ck_valor( 'endereco' , 1 ) ){
            alert( 'Digite o endereço da unidade.' );
            retorno = false;
        }
    }

    if ( retorno ) {
        if ( !ck_valor( 'enderecocurto' , 1 ) ){
            alert( 'Digite o endereço abreviado da unidade.' );
            retorno = false;
        }
    }

    if ( retorno ) {
        if ( !ck_valor( 'cidade' , 1 ) ){
            alert( 'Digite a cidade.' );
            retorno = false;
        }
    }

    if ( retorno ) {
        if ( !ck_valor( 'email' , 1 ) ){
            alert( 'Digite o email.' );
            retorno = false;
        }
    }


    return retorno;

}

function validacadaud(){

    var retorno = true;

    if ( retorno ) {
        if ( !ck_valor( 'data_aud' , 1 ) ){
            alert( 'Digite a data da audiência.' );
            retorno = false;
        }
    }

    if ( retorno ) {
        if( !ck_valor( 'hora_aud' , 1 ) ){
            alert( 'Digite a hora da audiência.' );
            retorno = false;
        }
    }

    if ( retorno ) {
        if( !ck_valor( 'local_aud' , 1 ) ){
            alert( 'Digite o local da audiência.' );
            retorno = false;
        }
    }

    if ( retorno ) {
        if( !ck_valor( 'cidade_aud' , 1 ) ){
            alert( 'Digite a cidade da audiência.' );
            retorno = false;
        }
    }

    if ( retorno ) {
        var num_processo = id( 'num_processo' );

        if ( ( is_checked( 'tipo_aud_0' ) ||
               is_checked( 'tipo_aud_4' ) ||
               is_checked( 'tipo_aud_6' ) ||
               is_checked( 'tipo_aud_7' ) ) && num_processo.value == '' ){

              var msg = 'Informe o número do processo!';
              if ( is_checked( 'tipo_aud_4' ) ){
                  msg = 'Informe o número do inquérito!';
              } else if ( is_checked( 'tipo_aud_7' ) ) {
                  msg = 'Informe o tipo de atendimento!';
              }

              alert( msg );
              num_processo.focus();
              retorno = false;
        }

    }

    if ( retorno ) {
        var motivo_justi = id( 'motivo_justi' );
        if( (  is_checked( 'sit_aud_1' ) || is_checked( 'sit_aud_2' ) ) && motivo_justi.value == '' ){
            var tiposit = 'da justificativa';
            if ( is_checked( 'sit_aud_1' ) ){
                tiposit = 'do cancelamento';
            }
            alert( 'Informe o motivo '+tiposit+' da audiência.' );
            motivo_justi.focus();
            retorno = false;
        }
    }

    return retorno;

}

function validacadproc(){

    var retorno = true;

    if ( !ck_valor( 'gra_num_exec' ) &&
         !ck_valor( 'gra_num_inq' ) &&
         !ck_valor( 'gra_f_p' ) &&
         !ck_valor( 'gra_num_proc' ) &&
         !ck_valor( 'gra_data_delito' ) &&
         !ck_valor( 'gra_data_sent' ) &&
         !ck_valor( 'gra_vara' ) &&
         !ck_valor( 'gra_comarca' ) &&
         !ck_valor( 'gra_p_ano' ) &&
         !ck_valor( 'gra_p_mes' ) &&
         !ck_valor( 'gra_p_dia' ) &&
         !ck_valor( 'gra_artigos' ) &&
         !ck_valor( 'gra_regime' ) &&
         !ck_valor( 'gra_sit_atual' ) &&
         !ck_valor( 'gra_obs' ) ) {

        alert( 'Preencha pelo menos um dos campos.' );
        id( 'gra_num_exec' ).focus();
        retorno = false;

    }

    return retorno;

}

function validacaddetproc(){

    var retorno = true;
    var campo = id( 'sit_proc' );

    if ( campo.value=='' ){
        alert( 'A situação processual não pode ficar em branco.' );
        campo.focus();
        retorno = false;
    }

    return retorno;

}

function validasuspvisit(){

    var retorno = true;

    var tipo_susp_0 = id( 'tipo_susp_0' );

    if ( retorno ) {
        var tipo_susp_1 = id( 'tipo_susp_1' );
        if( !tipo_susp_0.checked && !tipo_susp_1.checked ){
            alert( 'Informe tipo de suspensão.' );
            tipo_susp_0.focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        var data_inicio = id( 'data_inicio' );
        if( data_inicio.value == '' ){
            alert( 'Informe a data inicial.' );
            data_inicio.focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        var periodo = id( 'periodo' );
        if( tipo_susp_0.checked && periodo.value == '' ){
            alert( 'Informe o periodo (em dias) que o visitante ficará suspenso.' );
            periodo.focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        var motivo = id( 'motivo' );
        if( motivo.value == '' ){
            alert( 'Informe o motivo da suspensão.' );
            motivo.focus();
            retorno = false;
        }
    }

    return retorno;

}

function validadiretor(){

    var retorno = true;

    if ( retorno ) {
        var diretor = id( 'diretor' );
        if( diretor.value == '' ){
            alert( 'Informe o nome do diretor.' );
            diretor.focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        var titulo_diretor = id( 'titulo_diretor' );
        if( titulo_diretor.value == '' ){
            alert( 'Informe o título do diretor.' );
            titulo_diretor.focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        var setor = id( 'setor' );
        if( setor.value == '' ){
            alert( 'Informe o setor.' );
            setor.focus();
            retorno = false;
        }
    }

    return retorno;

}

function validamsg(){

    var retorno = true;

    if ( retorno ) {
        var para = id( 'msg_para' );
        if( para.value == '' ){
            alert( 'Informe o destinatário.' );
            para.focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        var msg_titulo = id( 'msg_titulo' );
        if( msg_titulo.value == '' ){
            alert( 'Informe o assunto da mensagem.' );
            msg_titulo.focus();
            retorno = false;
        }
    }

    return retorno;

}

function validasedex(){

    var retorno = true;

    if ( retorno ) {
        var data_sedex = id( 'data_sedex' );
        if( data_sedex.value == '' ){
            alert( 'Informe a data de recebimento do sedex.' );
            data_sedex.focus();
            retorno = false;
        }
    }

    var cod_sedex  = id( 'cod_sedex' );

    if ( retorno ) {
        if( cod_sedex.value == '' ){
            alert( 'Informe o código de rastreamento do sedex.' );
            cod_sedex.focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        if( cod_sedex.value.length < 13 ){
            alert( 'O código de rastreamento do sedex está incorreto. Verifique!' );
            cod_sedex.focus();
            retorno = false;
        }
    }

    return retorno;

}

function validalistasedex(){

    var cont   = 0;
    var checkb = id( 'sedex' );
    var inputs = id( 'sendsedex' ).getElementsByTagName( 'input' );

    for( var i = 0; i < inputs.length; i++ ){
        if( inputs[i].type == 'checkbox' ){
            if( inputs[i].checked ){
                cont += 1;
            }
        }
    }

    if( cont == 0 ){
        alert( 'Você deve marcar pelo menos um sedex.' );
        checkb.focus();
        return false;
    }

    var acao = confirm( 'Confirma?' );

    return acao;

}

function valida_dev_sedex(){

    var retorno = true;

    if ( retorno ) {
        var motivo_dev = id( 'motivo_dev' );
        if ( motivo_dev.value == '' ){
            alert( 'Informe o motivo da devolução do sedex.' );
            motivo_dev.focus();
            retorno = false;
        }
    }

    return retorno;

}

function validaprintaud(){

    var retorno = true;
    var cont    = 0;
    var checkb  = id( 'idaud' );
    var inputs  = id( 'aud_print' ).getElementsByTagName( 'input' );

    for( var i = 0; i < inputs.length; i++ ){
        if( inputs[i].type == 'checkbox' ){
            if( inputs[i].checked ){
                cont += 1;
            }
        }
    }

    if( cont == 0 ){
        alert( 'Você deve marcar pelo menos uma audiência.' );
        checkb.focus();
        retorno = false;
    }

    return retorno;

}

function validaprintpec(){

    var retorno = true;
    var cont    = 0;
    var checkb  = id( 'idpec' );
    var inputs  = id( 'print_pec' ).getElementsByTagName( 'input' );

    for( var i = 0; i < inputs.length; i++ ){
        if( inputs[i].type == 'checkbox' ){
            if( inputs[i].checked ){
                cont += 1;
            }
        }
    }

    if( cont == 0 ){
        alert( 'Você deve marcar pelo menos um item.' );
        checkb.focus();
        retorno = false;
    }

    return retorno;

}

function validapec(){

    var retorno = true;
    var cont    = 0;
    var checkb  = id( 'idpec' );
    var inputs  = id( 'sendpecbaixa' ).getElementsByTagName( 'input' );

    for( var i = 0; i < inputs.length; i++ ){
        if( inputs[i].type == 'checkbox' ){
            if( inputs[i].checked ){
                cont += 1;
            }
        }
    }

    if( cont == 0 ){
        alert( 'Você deve marcar pelo menos um item.' );
        checkb.focus();
        retorno = false;
    }

    if ( retorno ) {
        var obs_ret  = id( 'obs_ret' );
        if( obs_ret.value == '' ){
            alert( 'Digite a observação de retirada.' );
            obs_ret.focus();
            retorno = false;
        }
    }

    if ( retorno ) {

        retorno = confirm( 'Confirma?' );

    }

    return retorno;

}

function validalistaincl(){

    var retorno = true;
    var cont    = 0;
    var checkb  = id( 'iddet' );
    var inputs  = id( 'senddet' ).getElementsByTagName( 'input' );

    for( var i = 0; i < inputs.length; i++ ){
        if( inputs[i].type == 'checkbox' ){
            if( inputs[i].checked ){
                cont += 1;
            }
        }
    }

    if( cont == 0 ){
        alert( 'Você deve marcar pelo menos um detento.' );
        checkb.focus();
        retorno = false;
    }

    if ( retorno ) {

        retorno = confirm( 'Confirma?' );

    }

    return retorno;

}

function validanum(){

    var retorno = true;

    if ( retorno ) {
        var quant  = id( 'quant' );
        if( quant.value == '' ){
            alert( 'Informe a quantidade de números.' );
            quant.focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        var coment = id( 'coment' );
        if( coment.value == '' ){
            alert( 'Informe uma breve descrição para os números que você vai solicitar.' );
            coment.focus();
            retorno = false;
        }
    }

    return retorno;

}

function validaapcc(){

    var retorno = true;

    if ( retorno ) {
        var conduta = id( 'conduta' );
        var pda     = id( 'pda' );
        if( conduta.value == '4' && pda.value == '' ){
            alert( 'Informe o número do PDA.' );
            pda.focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        var cont   = 0;
        var inputs = id( 'sendapcc' ).getElementsByTagName( 'input' );

        for( var i = 0; i < inputs.length; i++ ){
            if( inputs[i].type == 'checkbox' ){
                if( inputs[i].checked ){
                    cont += 1;
                }
            }
        }

        if( cont == 0 ){
            alert( 'Você deve marcar pelo menos uma movimentação.' );
            conduta.focus();
            retorno = false;
        }
    }

    return retorno;

}

function validacadpert_saldo(){

    var retorno = true;
    var esp = id( 'esp' );

    if ( esp.value == '1' ){

        if ( retorno ) {
            var tipo_peculio  = id( 'tipo_peculio' );
            if ( tipo_peculio.value == '' ){
                alert( 'Selecione o tipo de pertence.' );
                tipo_peculio.focus();
                retorno = false;
            }
        }

        if ( retorno ) {
            var descr_peculio = id( 'descr_peculio' );
            if ( descr_peculio.value == '' ){
                alert( 'Digite uma descrição sobre o pertence.' );
                descr_peculio.focus();
                retorno = false;
            }
        }

        if ( retorno ) {
            var ret     = id( 'retirado' );
            var obs_ret = id( 'obs_ret' );

            if ( ret != null ) {
                if ( ret.checked && obs_ret.value == '' ){
                    alert( 'Digite uma descrição sobre a retirada do pertence.' );
                    descr_peculio.focus();
                    retorno = false;
                }
            }
        }

    } else {

        if ( retorno ) {
            var valor = id( 'valor' );
            if( valor.value == '' ){
                alert( 'Digite o valor a ser cadastrado.' );
                valor.focus();
                retorno = false;
            }
        }

    }

    return retorno;

}

function validacadpert(){

    var retorno = true;

    if ( retorno ) {
        var tipo_peculio  = id( 'tipo_peculio' );
        if ( tipo_peculio.value == '' ){
            alert( 'Selecione o tipo de pertence.' );
            tipo_peculio.focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        var descr_peculio = id( 'descr_peculio' );
        if ( descr_peculio.value == '' ){
            alert( 'Digite uma descrição sobre o pertence.' );
            descr_peculio.focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        var ret     = id( 'retirado' );
        var obs_ret = id( 'obs_ret' );

        if ( ret != null ) {
            if ( ret.checked && obs_ret.value == '' ){
                alert( 'Digite uma descrição sobre a retirada do pertence.' );
                descr_peculio.focus();
                retorno = false;
            }
        }
    }

    return retorno;

}

function validacadtv(){

    var retorno     = true;
    var n_raio      = id( 'n_raio' );

    if ( n_raio != null ) {

        if ( retorno ) {
            if ( n_raio.value == '' ){
                alert( 'Escolha o raio.' );
                n_raio.focus();
                retorno = false;
            }
        }

        if ( retorno ) {
            var n_cela = id( 'n_cela' );
            if ( n_cela.value == '' ){
                alert( 'Escolha o cela.' );
                n_cela.focus();
                retorno = false;
            }
        }

    }

    if ( retorno ) {
        var marca_tv = id( 'marca_tv' );
        if ( marca_tv.value == '' ){
            alert( 'Digite a marca da TV.' );
            marca_tv.focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        var cor_tv = id( 'cor_tv' );
        if ( cor_tv.value == '' ){
            alert( 'Digite a cor da TV.' );
            cor_tv.focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        var polegadas = id( 'polegadas' );
        if ( polegadas.value == '' ){
            alert( 'Digite o número de polegadas da TV.' );
            polegadas.focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        var lacre_1 = id( 'lacre_1' );
        if ( lacre_1.value == '' ){
            alert( 'Digite o número do lacre da TV.' );
            lacre_1.focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        var lacre_2 = id( 'lacre_2' );
        if ( lacre_2.value == '' ){
            alert( 'Digite o número do lacre da TV.' );
            lacre_2.focus();
            retorno = false;
        }
    }

    return retorno;

}

function validavinculatv(){

    matr = id( 'matricula' );

    if( matr.value=='' ){
        alert( 'Digite a matrícula.' );
        matr.focus();
        return (false);
    } else {
        return checkmatr(matr, matr.value);
    }
}

function validacadradio(){

    var retorno     = true;
    var n_raio      = id( 'n_raio' );

    if ( n_raio != null ) {

        if ( retorno ) {
            if ( n_raio.value == '' ){
                alert( 'Escolha o raio.' );
                n_raio.focus();
                retorno = false;
            }
        }

        if ( retorno ) {
            var n_cela = id( 'n_cela' );
            if ( n_cela.value == '' ){
                alert( 'Escolha a cela.' );
                n_cela.focus();
                retorno = false;
            }
        }

    }

    if ( retorno ) {
        var marca_radio = id( 'marca_radio' );
        if ( marca_radio.value == '' ){
            alert( 'Digite a marca do rádio.' );
            marca_radio.focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        var cor_radio = id( 'cor_radio' );
        if ( cor_radio.value == '' ){
            alert( 'Digite a cor do rádio.' );
            cor_radio.focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        var faixas = id( 'faixas' );
        if ( faixas.value == '' ){
            alert( 'Digite o número de faixas do rádio.' );
            faixas.focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        var lacre_1 = id( 'lacre_1' );
        if ( lacre_1.value == '' ){
            alert( 'Digite o número do lacre do rádio.' );
            lacre_1.focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        var lacre_2 = id( 'lacre_2' );
        if ( lacre_2.value == '' ){
            alert( 'Digite o número do lacre do rádio.' );
            lacre_2.focus();
            retorno = false;
        }
    }

    return retorno;

}

function validavincularadio(){

    var matr = id( 'matricula' );

    if( matr.value == '' ){
        alert( 'Digite a matrícula.' );
        matr.focus();
        return false;
    } else {
        return checkmatr( matr, matr.value );
    }
}

function validalista(){

    var retorno = true;
    var n_raio = id( 'n_raio' );

    if( n_raio.value == '' ){
        alert( 'Escolha o raio.' );
        n_raio.focus();
        retorno = false;
    }

    return retorno;

}


function valida_prot(){

    var retorno = true;

    var prot_canc = id( 'prot_canc' );

    if( !prot_canc.checked ){

        if ( retorno ) {
            var modo_in = id( 'modo_in' );
            if( modo_in.value == '' ){
                alert( 'Escolha o modo de entrada do documento.' );
                modo_in.focus();
                retorno = false;
            }
        }

        if ( retorno ) {
            var tipo_doc = id( 'tipo_doc' );
            if( tipo_doc.value == '' ){
                alert( 'Escolha o tipo de documento.' );
                tipo_doc.focus();
                retorno = false;
            }
        }

        if ( retorno ) {
            var prot_hora_in = id( 'prot_hora_in' );
            if( prot_hora_in.value == '' ){
                alert( 'Informe o horário do protocolo.' );
                prot_hora_in.focus();
                retorno = false;
            }
        }

        if ( retorno ) {
            var prot_assunto = id( 'prot_assunto' );
            if( prot_assunto.value == '' ){
                alert( 'Informe o assunto.' );
                prot_assunto.focus();
                retorno = false;
            }
        }

        if ( retorno ) {
            var prot_origem = id( 'prot_origem' );
            if( prot_origem.value == '' ){
                alert( 'Informe a origem do documento.' );
                prot_origem.focus();
                retorno = false;
            }
        }

        if ( retorno ) {
            var prot_setor = id( 'prot_setor' );
            if( prot_setor.value == '' ){
                alert( 'Escolha o setor.' );
                prot_setor.focus();
                retorno = false;
            }
        }

    }

    return retorno;

}

function valida_lista_prot(){

    var cont   = 0;
    var checkb = id( 'prot' );
    var inputs = id( 'sendprotdesp' ).getElementsByTagName( 'input' );

    for( var i = 0; i < inputs.length; i++ ){
        if( inputs[i].type == 'checkbox' ){
            if( inputs[i].checked ){
                cont += 1;
            }
        }
    }

    if( cont == 0 ){
        alert( 'Você deve marcar pelo menos um documento.' );
        checkb.focus();
        return (false);
    }

    botao = id( 'dps' );

    //alert (botao);
    //return (false);

    var tipo_acao = 'receber os documentos';

    if ( botao != null ){
        tipo_acao = 'despachar para os setores';
    }

    var acao = confirm( 'Confirma ' + tipo_acao + '?' );

    return acao;

}

function valida_pert_conf( modo ){

    var retorno = true;

    var cont   = 0;
    var inputs = id( 'sendpeculioconf' ).getElementsByTagName( 'input' );

    for( var i = 0; i < inputs.length; i++ ){
        if( inputs[i].type == 'checkbox' ){
            if( inputs[i].checked ){
                cont += 1;
            }
        }
    }

    if( cont == 0 ){
        alert( 'Você deve marcar pelo menos um pertence.' );
        id('todos').focus();
        retorno = false;
    }

    if ( retorno ) {

        var text_alert = 'Confirma o recebimentos dos pertences?'

        if ( modo == 1 ) {
            text_alert = 'Confirma a impressão dos pertences?'
        }

        retorno = confirm(text_alert);

    }

    return retorno;

}

function valida_termo_pront(){

/*    num_folhas = id( 'num_folhas' );
    if(num_folhas.value=='' ){
        alert( 'Digite o número de folhas.' );
        num_folhas.focus();
        return (false);
    } */
    var retorno = true;

    if ( retorno ) {
        var data_termo = id( 'data_termo' );
        if( data_termo.value == '' ){
            alert( 'Digite a data.' );
            data_termo.focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        var mot_termo_2 = id( 'mot_termo_2' );
        var mot_termo_3 = id( 'mot_termo_3' );
        var destino     = id( 'destino' );

        if( ( mot_termo_2.checked || mot_termo_3.checked ) && destino.value == '' ){
            alert( 'Informe o destino.' );
            destino.focus();
            retorno = false;
        }
    }

    return retorno;

}

function valida_termo_seg(){

    var retorno = true;

    if ( retorno ) {
        var escrivao = id( 'escrivao' );
        if( escrivao.value == '' ){
            alert( 'Digite o nome do escrivão.' );
            escrivao.focus();
            retorno = false;
        }
    }

    if ( retorno ) {
        var testemunha = id( 'testemunha' );
        if( testemunha.value == '' ){
            alert( 'Digite o nome da testemunha.' );
            testemunha.focus();
            retorno = false;
        }
    }

    if ( retorno ) {

        var mot_termo_2 = id( 'mot_termo_2' );
        var unid_dest   = id( 'unid_dest' );

        if( mot_termo_2.checked && unid_dest.value == '' ){
            alert( 'Informe a unidade de destino.' );
            unid_dest.focus();
            retorno = false;
        }
    }

    return retorno;

}

function valida_bonde( modo ){

    var retorno = true;

    if( ( modo == 0 ) || ( modo == '' ) || ( isNaN( modo ) ) ){
        retorno = false;
    }

    if( modo == 1 ){ // cadastrar bonde

        var bonde_data = id( 'bonde_data' );
        if( bonde_data.value == '' ){

            return verifica_data(bonde_data, bonde_data.value);

        }

    }

    if( modo == 2 ){ // cadastrar destino bonde

        var local_bonde = id( 'local_bonde' );
        if( local_bonde.value == '' ){
            alert( 'Informe o destino.' );
            local_bonde.focus();
            retorno = false;
        }

    }

    return retorno;

}

function valida_escolta( modo ){

    var retorno = true;

    if( ( modo == 0 ) || ( modo == '' ) || ( isNaN( modo ) ) ){
        retorno = false;
    }

    if( modo == 1 ){ // cadastrar escolta

        var escolta_data = id( 'escolta_data' );
        if( escolta_data.value == '' ){
            alert( 'Digite a data.' );
            escolta_data.focus();
            retorno = false;
        }

        if ( retorno ) {

            return verifica_data(escolta_data, escolta_data.value);

        }

    }

    if( modo == 2 ){ // cadastrar destino escolta

        var local_esc = id( 'local_esc' );
        if( local_esc.value == '' ){
            alert( 'Informe o destino.' );
            local_esc.focus();
            retorno = false;
        }

    }

    if( modo == 3 ){ // cadastrar local escolta

        var local_apr = id( 'local_apr' );
        if( local_apr.value == '' ){
            alert( 'Informe o local que deseja cadastrar.' );
            local_apr.focus();
            retorno = false;
        }

    }

    return retorno;

}

function valida_ord_saida( modo ){

    var retorno = true;

    if( ( modo == 0 ) || ( modo == '' ) || ( isNaN( modo ) ) ){
        retorno = false;
    }

    if( modo == 1 ){ // cadastrar escolta

        var ord_saida_data = id( 'ord_saida_data' );
        if( ord_saida_data.value == '' ){
            alert( 'Digite a data.' );
            ord_saida_data.focus();
            retorno = false;
        }

        if ( retorno ) {

            return verifica_data(ord_saida_data, ord_saida_data.value);

        }

    }

    if( modo == 2 ){ // cadastrar destino escolta

        var local_ord_saida = id( 'local_ord_saida' );
        if( local_ord_saida.value == '' ){
            alert( 'Informe o destino.' );
            local_ord_saida.focus();
            retorno = false;
        }

    }

    return retorno;

}

function valida_listatel( modo ) {

    var retorno = true;

    if( ( modo == 0 ) || ( modo == '' ) || ( isNaN( modo ) ) ){
        retorno = false;
    }

    if( modo == 1 ){ // atualizar numero

        if ( retorno ) {
            var ltn_num = id( 'ltn_num' );
            if( ltn_num.value == '' ){
                alert( 'Digite o número do telefone.' );
                ltn_num.focus();
                retorno = false;
            }
        }

    }

    if( modo == 2 ){ // atualizar localidade

        if ( retorno ) {
            var tel_local = id( 'tel_local' );
            if( tel_local.value == '' ){
                alert( 'Digite o nome da localidade.' );
                tel_local.focus();
                retorno = false;
            }
        }

    }

    return retorno;

}






























// Função para aceitar somente números
function so_numero(campo){
    var digits='0123456789'
    var campo_temp
    for (var i=0;i<campo.value.length;i++){
        campo_temp=campo.value.substring(i,i+1)
        if (digits.indexOf(campo_temp)==-1){
            campo.value = campo.value.substring(0,i);
            break;
        }
    }
}

// Funçaõ para validar 'np_incluir.php'
function validaFuncionario(){
    funcionario = document.incluir_funcionario;
    if(funcionario.codigo.value=='' ){
        alert( 'Digite o CÓDIGO do funcionário.' );
        funcionario.codigo.focus();
        return (false);
    }

    //    if(funcionario.codigo.length!=8){
    //    alert( 'O CÓDIGO deve ter 8 caracteres.' );
    //    funcionario.codigo.focus();
    //    return (false);
    //    }

    if(funcionario.nome.value=='' ){
        alert( 'Digite o NOME do funcionário.' );
        funcionario.nome.focus();
        return (false);
    }

    if(funcionario.rg.value=='' ){
        alert( 'Digite o RG do funcionário.' );
        funcionario.rg.focus();
        return (false);
    }

    if(funcionario.list_sexo.value=='0' ){
        alert( 'Selecione um SEXO.' );
        funcionario.list_sexo.focus();
        return (false);
    }

    if(funcionario.list_cargo.value=='0' ){
        alert( 'Selecione um CARGO.' );
        funcionario.list_cargo.focus();
        return (false);
    }

    if(funcionario.list_setor.value=='0' ){
        alert( 'Selecione um SETOR.' );
        funcionario.list_setor.focus();
        return (false);
    }
}


function validaCargo(){
    cargo = document.incluir_cargo;
    if(cargo.codigo.value=='' ){
        alert( 'Digite o CÓDIGO do cargo.' );
        cargo.codigo.focus();
        return (false);
    }

    if(cargo.cargo.value=='' ){
        alert( 'Digite o CARGO.' );
        cargo.cargo.focus();
        return (false);
    }
}


function validaCargoAltera(){
    cargo = document.alterar_cargo;
    if(cargo.cargo.value=='' ){
        alert( 'Digite o CARGO.' );
        cargo.cargo.focus();
        return (false);
    }
}


function validaSubquadro(){
    subquadro = document.incluir_subquadro;
    if(subquadro.subquadro.value=='' ){
        alert( 'Digite o SUBQUADRO.' );
        subquadro.subquadro.focus();
        return (false);
    }
}


function validaUa(){
    ua = document.incluir_ua;
    if(ua.codigo_ua.value=='' ){
        alert( 'Digite o CÓDIGO DA UA.' );
        ua.codigo_ua.focus();
        return (false);
    }

    if(ua.descricao.value=='' ){
        alert( 'Digite a DENOMINAÇÃO DA UA.' );
        ua.descricao.focus();
        return (false);
    }
}


function validaSetor(){
    setor = document.incluir_setor;
    if(setor.setor.value=='' ){
        alert( 'Digite o SETOR.' );
        setor.setor.focus();
        return (false);
    }

    if(setor.list_diretoria.value=='0' ){
        alert( 'Selecione uma DIRETORIA.' );
        setor.list_diretoria.focus();
        return (false);
    }
}


function validaDiretoria(){
    diretoria = document.incluir_diretoria;
    if(diretoria.diretoria.value=='' ){
        alert( 'Digite a DIRETORIA.' );
        diretoria.diretoria.focus();
        return (false);
    }
}


function validaHorario(){
    horario = document.incluir_horario;
    if(horario.descricao.value=='' ){
        alert( 'Digite a DESCRIÇÃO do HORÁRIO.' );
        horario.descricao.focus();
        return (false);
    }

    if(horario.regime_trabalho.value=='' ){
        alert( 'Digite o REGIME DE TRABALHO do HORÁRIO.' );
        horario.regime_trabalho.focus();
        return (false);
    }

    if(horario.regime_trabalho_descricao.value=='' ){
        alert( 'Digite a DESCRIÇÃO DO REGIME DE TRABALHO.' );
        horario.regime_trabalho_descricao.focus();
        return (false);
    }
}


function validaAfastamento(){
    afastamento = document.incluir_afastamento;
    if(afastamento.codigo.value=='' ){
        alert( 'Digite o CÓDIGO do AFASTAMENTO.' );
        afastamento.codigo.focus();
        return (false);
    }

    if(afastamento.abreviatura.value=='' ){
        alert( 'Digite a ABREVIATURA do AFASTAMENTO.' );
        afastamento.abreviatura.focus();
        return (false);
    }

    if(afastamento.descricao.value=='' ){
        alert( 'Digite a DESCRIÇÃO do AFASTAMENTO.' );
        afastamento.descricao.focus();
        return (false);
    }
}


function validaRegularizacao(){
    regularizacao = document.incluir_regularizacao;
    if(regularizacao.regularizacao.value=='' ){
        alert( 'Digite o nome da REGULARIZAÇÃO.' );
        regularizacao.regularizacao.focus();
        return (false);
    }

    if(regularizacao.descricao.value=='' ){
        alert( 'Digite a DESCRIÇÃO da REGULARIZAÇÃO.' );
        regularizacao.descricao.focus();
        return (false);
    }
}


function validaOrdem_saida(){
    ordem_saida = document.incluir_ordem_saida;
    if(ordem_saida.ordem_saida.value=='' ){
        alert( 'Digite o nome da ORDEM DE SAÍDA.' );
        ordem_saida.ordem_saida.focus();
        return (false);
    }

    if(ordem_saida.descricao.value=='' ){
        alert( 'Digite a DESCRIÇÃO da ORDEM DE SAÍDA.' );
        ordem_saida.descricao.focus();
        return (false);
    }
}


// função para validar o cadastro de medicamentos
function validaMedicamento(){
    med = document.incluir_medicamento;
    if(med.medicamento.value=='' ){
        alert( 'Digite o nome do MEDICAMENTO.' );
        med.medicamento.focus();
        return (false);
    }

    if(med.radiounidade[0].checked){
        if(med.textunidade.value=='' ){
            alert( 'Digite a Unidade do Medicamento.' );
            med.textunidade.focus();
            return (false);
        }
    }
    if(med.radiounidade[1].checked){
        if(med.list_unidade.value=='0' ){
            alert( 'Selecione a Unidade do Medicamento.' );
            med.list_unidade.focus();
            return (false);
        }
    }
}


// função para validar a entrada de medicamentos
function validaEntrada(){
    entrada = document.entrada_medicamento;
    if(entrada.radionomecomercial[0].checked){
        if(entrada.textnomecomercial.value=='' ){
            alert( 'Digite o Nome Comercial do Medicamento.' );
            entrada.textnomecomercial.focus();
            return (false);
        }
    }
    if(entrada.radionomecomercial[1].checked){
        if(entrada.list_nome_comercial.value=='0' ){
            alert( 'Selecione o Nome Comercial do Medicamento.' );
            entrada.list_nome_comercial.focus();
            return (false);
        }
    }
    if(entrada.list_grupo.value=='0' ){
        alert( 'Selecione o Grupo do Medicamento.' );
        entrada.list_grupo.focus();
        return (false);
    }
    if(entrada.quantidade.value=='' ){
        alert( 'Digite a Quantidade de Medicamento.' );
        entrada.quantidade.focus();
        return (false);
    }
    if(entrada.lote.value=='' ){
        alert( 'Digite o Lote do Medicamento.' );
        entrada.lote.focus();
        return (false);
    }
    if(entrada.data_venc.value=='' ){
        alert( 'Digite a Data de Vencimento do Medicamento.' );
        entrada.data_venc.focus();
        return (false);
    }
}


// Funçaõ para validar as opções do Relatório de MEDICAMENTOS
function valida_classifica_medicamento(){
    classificacao = document.classifica_relat_medicamento;
    if(classificacao.campo_nome_comercial.checked == false && classificacao.campo_unidade.checked == false && classificacao.campo_grupo.checked == false && classificacao.campo_lote.checked == false && classificacao.campo_validade.checked == false && classificacao.campo_estoque.checked == false){
        alert( 'É necessário selecionar um campo para o relatório!' );
        return (false);
    }
}
