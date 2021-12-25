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

$del         = get_post( 'del', 'int' );
$reset_pass  = get_post( 'reset_pass', 'int' );

$bt_value    = '';
$proced      = '';

if ( !empty( $del ) ) {
    $bt_value = 'Excluir';
    $proced   = 2;
}

if ( !empty( $reset_pass ) ) {
    $bt_value = 'Definir';
    $proced   = 1;
}

header( 'Content-Type: text/html; charset=utf-8' );

//Evitando cache de arquivo
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0' );
header( 'Pragma: no-cache' );
header( 'Expires: 0' );

?>
<div class="form_ajax">

    <form id="form_user" method="post" action="">

        <?php if ( !empty ( $del ) ) { ?>

        <p class="form_alert">
            Deseja realmente <b>EXCLUIR</b> este usuário?
        </p>

        <?php } // if ( !empty ( $del ) ) { ?>

        <?php if ( !empty ( $reset_pass ) ) { ?>

        <p class="form_alert">
            Tem certeza de que deseja definir a senha padrão (123456) para este usuário?
        </p>

        <?php } // if ( !empty ( $del ) ) { ?>

        <input type="hidden" name="iduser" id="iduser" value="<?php echo $iduser; ?>" />
        <input type="hidden" name="proced" id="proced" value="<?php echo $proced; ?>" />

        <div class="form_bts">
            <input class="form_bt" type="submit" id="bt_submit" value="<?php echo $bt_value; ?>" />
            <input class="form_bt" type="button" id="bt_cancel" value="Cancelar" />
        </div>

    </form> <!-- /form id="form_perm"  -->

</div> <!-- /div class="form_ajax"  -->