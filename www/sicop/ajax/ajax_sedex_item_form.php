<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_ajax.php';

$tipo_pag = 'CADASTRAMENTO DE ITENS DO SEDEX - AJAX';
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


$n_sedex = get_session( 'n_sedex', 'int' );
$n_n     = 3;
if ( $n_sedex < $n_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $tipo_pag;
    get_msg( $msg, 1 );

    echo $msg_falha;

    exit;

}

$proced = get_post( 'proced', 'int' );

$bt_value = 'Cadastrar';

if ( $proced == 2 ) {
    $bt_value = 'Excluir';
}

if ( $proced == 1 ) {
    $bt_value = 'Alterar';
}

$ids     = '';
$id_item = '';
$cod_um  = '';
$quant   = '';
$desc    = '';
$retido  = '';

if ( $proced == 3 ) {

    $ids = get_post( 'uid', 'int' );
    if ( empty( $ids ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'atn';
        $msg['text']  = "Tentativa de acesso direto à página. Identificador do sedex em branco. ( $tipo_pag )";
        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;

    }

}

if ( $proced == 1 or $proced == 2 ) {

    $id_item = get_post( 'uid', 'int' );
    if ( empty( $id_item ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'atn';
        $msg['text']  = "Tentativa de acesso direto à página. Identificador do item do sedex em branco. ( $tipo_pag )";
        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;

    }

}

if ( $proced == 1 ) {

    $q_item_sedex = "SELECT
                       `sedex_itens`.`cod_um`,
                       `sedex_itens`.`quant`,
                       `sedex_itens`.`desc`,
                       `sedex_itens`.`retido`
                     FROM
                       `sedex_itens`
                     WHERE
                       `sedex_itens`.`id_item` = $id_item
                     ORDER BY
                       `sedex_itens`.`retido`";

    $db = SicopModel::getInstance();
    $q_item_sedex = $db->query( $q_item_sedex );

    if ( !$q_item_sedex ) {

        // pegar a mensagem de erro mysql
        $msg_err_mysql = $db->getErrorMsg();
        $db->closeConnection();

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Falha na consulta ( $tipo_pag ). \n\n $msg_err_mysql";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;
        exit;

    }

    $db->closeConnection();

    $cont_is = $q_item_sedex->num_rows;
    if ( $cont_is < 1 ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "A consulta retornou 0 ocorrências ( $tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;
        exit;

    }

    $d_item_sedex = $q_item_sedex->fetch_object();

    $cod_um = $d_item_sedex->cod_um;
    $quant  = str_replace( '.', ',', $d_item_sedex->quant );
    $desc   = $d_item_sedex->desc;
    $retido = $d_item_sedex->retido;

}

if ( $proced != 2 ) {

    $q_un_med = "SELECT
                   `idum`,
                   `un_medida`
                 FROM
                   `tipo_un_medida`";

    $db = SicopModel::getInstance();
    $q_un_med = $db->query( $q_un_med );
    $db->closeConnection();

    if ( !$q_un_med ) {

        // pegar a mensagem de erro mysql
        $msg_err_mysql = $db->getErrorMsg();

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Falha na consulta ( $tipo_pag ). \n\n $msg_err_mysql";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;
        exit;

    }

    $cont_um = $q_un_med->num_rows;
    if ( $cont_um < 1 ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "A consulta retornou 0 ocorrências ( $tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;
        exit;

    }

}

header( 'Content-Type: text/html; charset=utf-8' );

//Evitando cache de arquivo
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0' );
header( 'Pragma: no-cache' );
header( 'Expires: 0' );

?>

<style>
    div.sedex_fied{
        float: left;
        margin-bottom: 10px;
        margin-left: 3px;
        margin-right: 3px;
    }

    .c_t_sedex{
        height: 14px;
        margin-top: 2px;
    }

</style>

<div class="form_ajax">

    <form id="form_sedex_add" method="post" action="">

        <?php if ( $proced == 2 ) { ?>

        <p class="form_alert">
            Deseja realmente <b>EXCLUIR</b> este item?
        </p>

        <?php } else { ?>

        <div style="width: 430px;">

            <div class="sedex_fied">
                <p class="form_leg">Medida:</p>
                <p>
                    <select name="un_med" class="CaixaTexto" id="un_med" style="margin-top: 2px;">
                        <option value="" >Selecione...</option>
                        <?php while ( $d_un_med = $q_un_med->fetch_object() ) { ?>
                        <option value="<?php echo $d_un_med->idum ?>" <?php echo $d_un_med->idum == $cod_um ? 'selected="selected"' : ''; ?>><?php echo $d_un_med->un_medida; ?></option>
                        <?php }; ?>
                    </select>
                </p>
            </div>

            <div class="sedex_fied">
                <p class="form_leg">Quantidade:</p>

                <p>
                    <input name="quant" type="text" class="CaixaTexto c_t_sedex" id="quant" value="<?php echo $quant; ?>" onblur="upperMe(this);" onkeypress="return blockChars(event, 6);" size="10" maxlength="4" />
                </p>
            </div>

            <div class="sedex_fied">
                <p class="form_leg">Descrição:</p>

                <p>
                    <input name="desc_item_sedex" type="text" class="CaixaTexto c_t_sedex" id="desc_item_sedex" value="<?php echo $desc; ?>" onblur="upperMe(this);" onkeypress="return blockChars(event, 4);" size="50" maxlength="50" />
                </p>

            </div>

        </div>

        <div style="clear: both; text-align: center;">
            <input type="radio" name="ret" value="0" id="ret_0" <?php echo empty( $retido ) ? 'checked="checked"' : ''; ?> /> Entrege &nbsp;
            <input type="radio" name="ret" value="1" id="ret_1" <?php echo $retido == 1 ? 'checked="checked"' : ''; ?> /> Retido
        </div>

        <?php } // /if ( $proced == 2 ) { ?>

        <input type="hidden" name="ids" id="ids" value="<?php echo $ids; ?>" />
        <input type="hidden" name="id_item" id="id_item" value="<?php echo $id_item; ?>" />
        <input type="hidden" name="proced" id="proced" value="<?php echo $proced; ?>" />

        <p id="form_error" class="form_error" style="display:none"></p>

        <div class="form_bts" style="clear: both;">
            <input class="form_bt" type="button" id="bt_submit" value="<?php echo $bt_value; ?>" />
            <input class="form_bt" type="button" id="bt_cadadd" value="<?php echo $bt_value; ?> e adicionar outro" />
            <input class="form_bt" type="button" id="bt_cancel" value="Cancelar" />
        </div>

    </form> <!-- /form id="form_sedex_add"  -->

</div> <!-- /div class="form_ajax"  -->