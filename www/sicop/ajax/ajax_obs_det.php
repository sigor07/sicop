<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_ajax.php';

$tipo_pag = 'OBSERVAÇÕES DE DETENTO - AJAX';
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

$n_det_obs = get_session( 'n_det_obs', 'int' );
$n_obs_n   = 1;
$n_chefia  = get_session( 'n_chefia', 'int' );
$del       = get_post( 'del', 'int' );
if ( $n_det_obs < $n_obs_n or ( !empty ( $del ) and $n_chefia < 4 ) ) {

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

$obs_det  = '';
$proced   = 3;
$bt_value = 'Cadastrar';
$idobs    = '';

if ( !empty ( $del ) ) {

    $idobs = get_post( 'idobs', 'int' );
    if ( empty( $idobs ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo'] = 'atn';
        $msg['text'] = "Tentativa de acesso direto à página. Identificador da observação em branco. ( $tipo_pag )";
        get_msg( $msg, 1 );

        echo $msg_falha;
        exit;

    }

    $proced   = 2;
    $bt_value = 'Excluir';

}

$edit = get_post( 'edit', 'int' );
if ( !empty ( $edit ) ) {

    $idobs = get_post( 'idobs', 'int' );

    if ( empty( $idobs ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo'] = 'atn';
        $msg['text'] = "Tentativa de acesso direto à página. Identificador da observação em branco. ( $tipo_pag )";
        get_msg( $msg, 1 );

        echo $msg_falha;
        exit;

    }

    $motivo_pag = 'ALTERAÇÃO DE OBSERVAÇÃO - ' . SICOP_DET_DESC_U;

    $query_obs = "SELECT
                    `obs_det`
                  FROM
                    `obs_det`
                  WHERE
                    `id_obs_det` = $idobs
                  LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_obs = $model->query( $query_obs );

    // fechando a conexao
    $model->closeConnection();

    if ( !$query_obs ) {

        echo $msg_falha;
        exit;

    }


    $cont = $query_obs->num_rows;
    if ( $cont < 1 ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "A consulta retornou 0 ocorrências ( $motivo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;
        exit;

    }

    $d_obs    = $query_obs->fetch_assoc();
    $obs_det  = $d_obs['obs_det'];
    $proced   = 1;
    $bt_value = 'Atualizar';

}

header( "Content-Type: text/html; charset=utf-8" );

//Evitando cache de arquivo
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0' );
header( 'Pragma: no-cache' );
header( 'Expires: 0' );


?>
<div class="form_ajax">
<?php if ( empty ( $del ) ) {?>


    <form id="form_obs" method="post" action="">

        <p class="form_leg">Observação</p>

        <p>
            <textarea name="obs_det" id="obs_det" cols="75" rows="3" class="CaixaTexto" onkeypress="return blockChars(event, 4);"><?php echo $obs_det; ?></textarea>
        </p>

        <p id="form_error" class="form_error" style="display:none">Digite a observação!</p>

        <input name="id_obs_det" type="hidden" id="id_obs_det" value="<?php echo $idobs;?>" />
        <input name="iddet" type="hidden" id="iddet" value="<?php echo $iddet ?>" />
        <input name="proced" type="hidden" id="proced" value="<?php echo $proced; ?>" />

        <div class="form_bts">
            <input class="form_bt" type="submit" value="<?php echo $bt_value; ?>" />
        </div>

    </form>

<?php

    } else {

?>

    <form id="form_obs" method="post" action="">


        <p class="form_alert">
            Deseja realmente <b>EXCLUIR</b> esta observação? <br />
            <b>ATENÇÃO</b>: Você não poderá desfazer essa operação!
        </p>

        <input name="id_obs_det" type="hidden" id="id_obs_det" value="<?php echo $idobs;?>" />
        <input name="proced" type="hidden" id="proced" value="<?php echo $proced; ?>" />

        <div class="form_bts">
            <input class="form_bt" type="submit" id="bt_submit" value="<?php echo $bt_value; ?>" />
            <input class="form_bt" type="button" id="bt_cancel" value="Cancelar" />
        </div>

    </form>

<?php } ?>
</div>