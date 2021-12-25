<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_ajax.php';

$tipo_pag = 'FOTO DE VISITANTE - AJAX';
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

$n_rol = get_session( 'n_rol', 'int' );
$n_n   = 3;
if ( $n_rol < $n_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $tipo_pag;
    get_msg( $msg, 1 );

    echo $msg_falha;

    exit;

}

$proced = get_post( 'proced', 'int' );

if ( $proced == 3 ) {

    $idvisit = get_post( 'uid', 'int' );
    if ( empty( $idvisit ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo'] = 'atn';
        $msg['text'] = "Tentativa de acesso direto à página. Identificador do visitante em branco. ( $tipo_pag )";
        get_msg( $msg, 1 );

        echo $msg_falha;
        exit;

    }

} else {

    $bt_value = 'Excluir';
    if ( $proced == 1 ) {
        $bt_value = 'Definir';
    }

    $id_foto = get_post( 'uid', 'int' );
    if ( empty( $id_foto ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'atn';
        $msg['text']  = "Tentativa de acesso direto à página. Identificador da foto em branco. ( $tipo_pag )";
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
<div class="form_ajax">


    <?php if ( $proced == 1 or $proced == 2 ) { ?>

    <form action="" method="post" name="form_alter_img_visit" id="form_alter_img_visit">

        <?php if ( $proced == 1 ) { ?>

        <p class="form_alert"> Deseja definir esta foto como a principal? </p>

        <?php } else { ?>

        <p class="form_alert">
            Deseja realmente <b>EXCLUIR</b> esta foto? <br />
            <b>ATENÇÃO</b>: Você não poderá desfazer essa operação!
        </p>

        <?php } ?>

        <input name="id_foto" type="hidden" id="id_foto" value="<?php echo $id_foto; ?>" />
        <input name="proced" type="hidden" id="proced" value="<?php echo $proced; ?>" />

        <div class="form_bts">
            <input class="form_bt" type="button" id="bt_submit" value="<?php echo $bt_value; ?>" />
            <input class="form_bt" type="button" id="bt_cancel" value="Cancelar" />
        </div>

    </form>

    <?php } else { ?>


    <p class="descript_page">ALTERAR FOTO DE VISITANTE</p>

    <?php include 'quali/visit_basic.php'; ?>


    <form action="<?php echo SICOP_ABS_PATH ?>send/sendvisitimg.php" method="post" enctype="multipart/form-data" name="form_alter_img_visit" id="form_alter_img_visit">

        <p class="table_leg">Selecionar arquivo:</p>

        <p style="text-align: center;">
            <input name="foto_visit" type="file" class="CaixaTexto" id="foto_visit" size="70" />
        </p>

        <p id="form_error" class="form_error" style="display:none; text-align: center;"></p>

        <input type="hidden" name="idvisit" id="idvisit" value="<?php echo $idvisit ?>" />
        <input name="proced" type="hidden" id="proced" value="3" />

        <div class="form_bts">
            <input class="form_bt" type="submit" value="Cadastrar" />
        </div>

    </form>

    <?php } ?>

</div>