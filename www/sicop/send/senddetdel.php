<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag = link_pag();
$tipo = '';
$mensagem = '';
$msg_saida = '';

$n_chefia         = get_session( 'n_chefia', 'int' );
$nivel_necessario = 4;

if ( $n_chefia < $nivel_necessario ) {

    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'perm';
    $msg['entre_ch'] = 'EXCLUSÃO DE ' . SICOP_DET_DESC_U;
    get_msg( $msg, 1 );

    exit;

}

$is_post = is_post();

if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'atn';
    $msg['text'] = 'Tentativa de acesso direto à página de exclusão de ' . SICOP_DET_DESC_L . '.';
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

extract( $_POST, EXTR_OVERWRITE );

$iddet = (int)$iddet;

if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Identificador d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . ' em branco. Operação cancelada ( EXCLUSÃO DE ' . SICOP_DET_DESC_U . ' ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 1 );
    exit;

}

// pegar os dados do preso
$detento = dados_det( $iddet );

$user = get_session( 'user_id', 'int' );
$ip   = "'" . $_SERVER['REMOTE_ADDR'] . "'";


$query_f_d = "SELECT `foto_det_g`, `foto_det_p` FROM `det_fotos` WHERE `cod_detento` = $iddet";
$query_up_det = "UPDATE `detentos` SET user_up = $user, data_up = NOW(), ip_up = $ip WHERE `iddetento` = $iddet LIMIT 1";
$query_d_d = "DELETE FROM `detentos` WHERE `iddetento` = $iddet LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_f_d    = $model->query( $query_f_d );
$query_up_det = $model->query( $query_up_det );
$query_d_d    = $model->query( $query_d_d );

// fechando a conexao
$model->closeConnection();

$success = TRUE;
if ( !$query_d_d ) {

    $success = FALSE;

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Erro de cadastramento de " . SICOP_DET_DESC_L . ". \n\n $detento \n";
    $msg['linha'] = __LINE__;

    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 2 );

    exit;

}

$pasta = SICOP_DET_FOLDER;

// só executa o while se realmente excluiu o preso
while ( $d_foto_d = $query_f_d->fetch_assoc() ) {

    if ( !empty( $d_foto_d['foto_det_g'] ) ) {
        if ( file_exists( $pasta . $d_foto_d['foto_det_g'] ) ) {
            unlink( $pasta . $d_foto_d['foto_det_g'] );
        }
    }

    if ( !empty( $d_foto_d['foto_det_p'] ) ) {
        if ( file_exists( $pasta . $d_foto_d['foto_det_p'] ) ) {
            unlink( $pasta . $d_foto_d['foto_det_p'] );
        }
    }

}

$msg = array();
$msg['tipo']     = 'desc';
$msg['entre_ch'] = 'EXCLUSÃO DE ' . SICOP_DET_DESC_U;
$msg['text']     = 'Exclusão de ' . SICOP_DET_DESC_L . ". \n\n $detento";

get_msg( $msg, 1 );

redir( 'buscadet' );

exit;

?>
</body>
</html>