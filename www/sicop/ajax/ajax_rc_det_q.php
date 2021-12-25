<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_ajax.php';

$tipo_pag = mb_strtoupper( SICOP_RAIO ) . '/' . mb_strtoupper( SICOP_CELA ) . ' DE ' . SICOP_DET_DESC_U . ' - AJAX';
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

$q_rc = "SELECT
           `raio`.`raio`,
           `cela`.`cela`
         FROM
           `detentos`
           INNER JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
           INNER JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
         WHERE
           `detentos`.`iddetento` = $iddet
         LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_rc = $model->query( $q_rc );

// fechando a conexao
$model->closeConnection();

if ( !$q_rc ) {

    echo $msg_falha;
    exit;

}

$cont = $q_rc->num_rows;
if ( $cont < 1 ) {

    echo '';
    exit;

}

$d_rc = $q_rc->fetch_assoc();

header( 'Content-Type: text/html; charset=utf-8' );

//Evitando cache de arquivo
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0' );
header( 'Pragma: no-cache' );
header( 'Expires: 0' );


?>

<span id="raio"><?php echo SICOP_RAIO ?>: <?php echo $d_rc['raio']; ?></span>
<span id="cela"><?php echo SICOP_CELA ?>: <?php echo $d_rc['cela']; ?></span>



