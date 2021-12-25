<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_ajax.php';

$tipo_pag = mb_strtoupper( SICOP_RAIO ) . '/' . mb_strtoupper( SICOP_CELA ) . ' - AJAX';
$msg_falha = '<p class="q_error">FALHA!</p>';

$is_post = is_post();
if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( $tipo_pag ).";
    get_msg( $msg, 1 );

    echo $msg_falha;
    exit;

}

$n_det_rc = get_session( 'n_det_rc', 'int' );
$n_rc_n   = 1;
if ( $n_det_rc < $n_rc_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $tipo_pag;
    get_msg( $msg, 1 );

    echo $msg_falha;

    exit;

}

$iddet = get_post( 'iddet', 'int' );
if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página. Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. ( $tipo_pag )";
    get_msg( $msg, 1 );

    echo $msg_falha;
    exit;

}

$q_old_rc = "SELECT
               `cela`.`idcela`,
               `cela`.`cod_raio`
             FROM
               `detentos`
               INNER JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
             WHERE
               `detentos`.`iddetento` = $iddet
             LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_old_rc = $model->query( $q_old_rc );

// fechando a conexao
$model->closeConnection();

$motivo_pag = 'ALTERAÇÃO DE ' . mb_strtoupper( SICOP_RAIO ) . '/' . mb_strtoupper( SICOP_CELA ) . ' - ' . SICOP_DET_DESC_U;
if ( !$q_old_rc ) {

    echo $msg_falha;
    exit;

}

$cont     = $q_old_rc->num_rows;
$old_raio = '';
$old_cela = '';

if ( $cont >= 1 ) {

    $d_old_rc = $q_old_rc->fetch_assoc();

    $old_raio = $d_old_rc['cod_raio'];
    $old_cela = $d_old_rc['idcela'];

}

header( 'Content-Type: text/html; charset=utf-8' );

//Evitando cache de arquivo
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0' );
header( 'Pragma: no-cache' );
header( 'Expires: 0' );
?>


<script type="text/javascript">
$(function() {

    setTimeout( function(){
        $("#n_raio").focus();
    }, 200 );

    $( "#data_rc" ).datepicker({
        showOn: "button",
        buttonImageOnly: true
    });

    $.monta_box_raio();

//    $("#n_raio").show( function() {
//        setTimeout( function(){
//            //$("#n_raio").focus();
//        }, 1000 );
//    });

});
</script>


<div class="form_ajax">

    <form id="form_rc" method="post" action="">

        <p class="form_leg"><?php echo SICOP_RAIO ?>:</p>

        <p>
            <select name="n_raio" class="CaixaTexto" id="n_raio" onchange="$.monta_box_cela();">
                <option value="" selected="selected">Selecione</option>
            </select>
        </p>

        <p class="form_leg"><?php echo SICOP_CELA ?>:</p>

        <p>
            <select name="n_cela" class="CaixaTexto" id="n_cela">
                <option value="" selected="selected">Escolha o raio...</option>
            </select>
        </p>


        <p class="form_leg">Data:</p>

        <p>
            <input name="data_rc" type="text" class="CaixaTexto" id="data_rc" size="12" maxlength="10" onBlur="verifica_data(this, this.value)" onKeyPress="mascara_data(this, this.value);return blockChars(event, 2);" />
            &nbsp;
            <a href="#" onClick="javascript: datahoje('data_rc'); return false;">hoje</a>
        </p>


        <p id="form_error" class="form_error" style="display:none">Escolha o raio e a cela!</p>

        <input type="hidden" name="iddet" id="iddet" value="<?php echo $iddet ?>" />
        <input type="hidden" name="old_raio" id="old_raio" value="<?php echo $old_raio;?>" />
        <input type="hidden" name="old_cela" id="old_cela" value="<?php echo $old_cela;?>" />
        <input type="hidden" name="datahj" id="datahj" value="<?php echo date('d/m/Y') ?>" />


        <div class="form_bts">
            <input class="form_bt" type="submit" value="Cadastrar" />
        </div>

    </form>

</div>