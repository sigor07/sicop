<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_ajax.php';

$tipo_pag = 'PERMISSÕES DE USUÁRIO - AJAX';
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

$n_admsist = get_session( 'n_admsist', 'int' );
$n_adm_n   = 4;
if ( $n_admsist < $n_adm_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $tipo_pag;
    get_msg( $msg, 1 );

    echo $msg_falha;

    exit;

}

$iduser = get_post( 'iduser', 'int' );
if ( empty( $iduser ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página. Identificador do usuário em branco. ( $tipo_pag )";
    get_msg( $msg, 1 );

    echo $msg_falha;
    exit;

}

$idperm    = '';
$old_nivel = '';
$setor     = '';
$proced    = '3';
$bt_value  = 'Cadastrar';
$visit     = get_post( 'visit', 'int' );
$add       = get_post( 'add', 'int' );
$perm_type = '';
$edit      = get_post( 'edit', 'int' );
$del       = get_post( 'del', 'int' );

if ( !empty ( $add ) ) {


    $perm_type = get_post( 'perm_type', 'int' );

    $where_imp = 'FALSE';
    $where_esp = 'FALSE';

    if ( $perm_type == 1 ) {

        $where_imp = 'FALSE';
        $where_esp = 'FALSE';

    } else if ( $perm_type == 2 ) {

        $where_imp = 'TRUE';
        $where_esp = 'FALSE';

    } else if ( $perm_type == 3 ) {

        $where_imp = 'FALSE';
        $where_esp = 'TRUE';

    } else {

        echo $msg_falha;

    }

    $query = "SELECT
                `sicop_n_setor`.`id_n_setor`
              FROM
                `sicop_n_setor`
              WHERE
                `sicop_n_setor`.`especifico` = $where_esp
                AND
                `sicop_n_setor`.`impressao` = $where_imp
                AND
                `sicop_n_setor`.`id_n_setor` NOT IN ( SELECT `cod_n_setor` FROM `sicop_users_perm` WHERE `cod_user` = $iduser )
              ORDER BY
                `sicop_n_setor`.`n_setor_nome`";

        //depur( $query );

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query = $model->query( $query );

    // fechando a conexao
    $model->closeConnection();

    if ( !$query ) {

        echo $msg_falha;
        exit;

    }

    $cont = $query->num_rows;
    if ( $cont < 1 ) {

        echo '<p class="p_q_no_result">Não há mais permissões que possam ser adicionadas para este usuário.</p>';
        exit;

    }


}

if ( !empty ( $edit ) ) {

    $idperm = get_post( 'idperm', 'int' );

    if ( empty( $idperm ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo'] = 'atn';
        $msg['text'] = "Tentativa de acesso direto à página. Identificador da permissão em branco. ( $tipo_pag )";
        get_msg( $msg, 1 );

        echo $msg_falha;
        exit;

    }

    $motivo_pag = 'ALTERAÇÃO DE OBSERVAÇÃO - ' . SICOP_DET_DESC_U;

    $query_perm = "SELECT
                     `sicop_users_perm`.`idpermissao`,
                     `sicop_users_perm`.`cod_nivel`,
                     `sicop_n_setor`.`id_n_setor`,
                     `sicop_n_setor`.`n_setor_nome`
                   FROM
                     `sicop_users_perm`
                     INNER JOIN `sicop_n_setor` ON `sicop_users_perm`.`cod_n_setor` = `sicop_n_setor`.`id_n_setor`
                   WHERE
                     `sicop_users_perm`.`idpermissao` = $idperm
                   LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_perm = $model->query( $query_perm );

    // fechando a conexao
    $model->closeConnection();

    if ( !$query_perm ) {

        echo $msg_falha;
        exit;

    }

    $cont = $query_perm->num_rows;
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

    $d_perm    = $query_perm->fetch_assoc();
    $setor     = $d_perm['n_setor_nome'];
    $old_nivel = $d_perm['cod_nivel'];
    $proced    = 1;
    $bt_value  = 'Atualizar';

}

if ( !empty ( $del ) ) {

    $idperm = get_post( 'idperm', 'int' );

    if ( empty( $idperm ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo'] = 'atn';
        $msg['text'] = "Tentativa de acesso direto à página. Identificador da permissão em branco. ( $tipo_pag )";
        get_msg( $msg, 1 );

        echo $msg_falha;
        exit;

    }

    $proced    = 2;
    $bt_value  = 'Revogar';

}

header( 'Content-Type: text/html; charset=utf-8' );

//Evitando cache de arquivo
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0' );
header( 'Pragma: no-cache' );
header( 'Expires: 0' );

?>
<script type="text/javascript" src="<?php echo SICOP_ABS_PATH ?>js/ajax/ajax_combo.js"></script>
<div class="form_ajax">

    <form id="form_perm" method="post" action="">

        <?php if ( !empty ( $add ) ) { ?>

        <p class="form_leg">Setor:</p>

        <p>
            <select name="n_setor" class="CaixaTexto" id="n_setor" style="width: 200px;">
                <option value="" selected="selected">Escolha o setor...</option>
            </select>
        </p>

        <?php if ( $perm_type == 1 ) { ?>
        <p class="form_leg">Nível de acesso:</p>

        <p>
            <select name="n_nivel" class="CaixaTexto" id="n_nivel" style="width: 200px;">
                <option value="" selected="selected">Escolha o setor...</option>
            </select>
        </p>
        <?php } //if ( $perm_type == 1 ) { ?>

        <p id="form_error" class="form_error" style="display:none">Escolha o nível de acesso!</p>

        <input type="hidden" name="iduser" id="iduser" value="<?php echo $iduser; ?>" />
        <input type="hidden" name="proced" id="proced" value="<?php echo $proced; ?>" />
        <input type="hidden" name="perm_type" id="perm_type" value="<?php echo $perm_type; ?>" />

        <div class="form_bts">
            <input class="form_bt" type="submit" id="bt_submit" value="<?php echo $bt_value; ?>" />
            <input class="form_bt" type="button" id="bt_cadadd" value="Cadastrar e adicionar outra" />
        </div>

        <?php } // if ( $perm_type == 1 ) { ?>

        <?php if ( !empty ( $edit ) ) { ?>

        <p class="form_leg">Setor:</p>

        <p>
            <?php echo $setor; ?>
        </p>

        <p class="form_leg">Nível de acesso:</p>

        <p>
            <select name="n_nivel" class="CaixaTexto" id="n_nivel" style="width: 200px;">
                <option value="" selected="selected">Escolha o nivel...</option>
            </select>
        </p>

        <p id="form_error" class="form_error" style="display:none">Escolha o nível de acesso!</p>

        <input type="hidden" name="iduser" id="iduser" value="<?php echo $iduser; ?>" />
        <input type="hidden" name="idperm" id="idperm" value="<?php echo $idperm; ?>" />
        <input type="hidden" name="old_nivel" id="old_nivel" value="<?php echo $old_nivel; ?>" />
        <input type="hidden" name="proced" id="proced" value="<?php echo $proced; ?>" />
        <input type="hidden" name="visit" id="visit" value="<?php echo $visit; ?>" />

        <div class="form_bts">
            <input class="form_bt" type="submit" value="<?php echo $bt_value; ?>" />
        </div>

        <?php } // if ( !empty ( $edit ) ) { ?>

        <?php if ( !empty ( $del ) ) { ?>

        <p class="form_alert">
            Deseja realmente <b>REVOGAR</b> esta permissão?
        </p>

        <input type="hidden" name="iduser" id="iduser" value="<?php echo $iduser; ?>" />
        <input type="hidden" name="idperm" id="idperm" value="<?php echo $idperm; ?>" />
        <input type="hidden" name="proced" id="proced" value="<?php echo $proced; ?>" />

        <div class="form_bts">
            <input class="form_bt" type="submit" id="bt_submit" value="<?php echo $bt_value; ?>" />
            <input class="form_bt" type="button" id="bt_cancel" value="Cancelar" />
        </div>

        <?php } // if ( !empty ( $del ) ) { ?>

    </form> <!-- /form id="form_perm"  -->

</div> <!-- /div class="form_ajax"  -->