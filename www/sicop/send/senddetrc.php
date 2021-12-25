<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_ajax.php';

$pag       = link_pag();
$tipo      = '';
$mensagem  = '';
$msg_falha = '<p class="q_error">FALHA!</p>';


$tipo_pag = mb_strtoupper( SICOP_RAIO ) . '/' . mb_strtoupper( SICOP_CELA ) . ' DE ' . SICOP_DET_DESC_U;

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

extract( $_POST, EXTR_OVERWRITE );

$proced_tipo_pag = 'ALTERAÇÃO - ' . $tipo_pag;

$iddet = empty( $iddet ) ? '' : (int)$iddet;
if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo'] = 'err';
    $msg['text'] = "Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. Operação cancelada ( $proced_tipo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo $msg_falha;
    exit;

}

$detento = dados_det( $iddet );

$old_cela = empty( $old_cela ) ? 'NULL' : (int)$old_cela;
$n_cela   = empty( $n_cela ) ? 'NULL' : (int)$n_cela;

if ( empty( $n_cela ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo'] = 'err';
    $msg['text'] = "Identificador do novo " . mb_strtolower( SICOP_RAIO ) . '/' . mb_strtolower( SICOP_CELA ) . " em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $detento";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo $msg_falha;
    exit;

}

$data_rc = empty( $data_rc ) ? '' : $data_rc;

if ( empty( $data_rc ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo'] = 'err';
    $msg['text'] = "Data em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $detento";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo $msg_falha;
    exit;

}

// verificar se a data é válida
if ( !validaData( $data_rc, 'DD/MM/AAAA' ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo'] = 'err';
    $msg['text'] = "Data inválida. Operação cancelada ( $proced_tipo_pag ). \n\n $detento";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo $msg_falha;
    exit;

}

// verificar se a data não é futura
$time_data_atual = strtotime( date( 'Y-m-d' ) );
$partes = explode( '/', $data_rc );
$time_data_mov = mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
if ( $time_data_mov > $time_data_atual ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo'] = 'err';
    $msg['text'] = "Data futura. Operação cancelada ( $proced_tipo_pag ). \n\n $detento";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo $msg_falha;
    exit;

}

// instanciando o model
$model = SicopModel::getInstance();

$data_rc  = "'" . $model->escape_string( $data_rc ) . "'";

$user     = get_session( 'user_id', 'int' );
$ip       = "'" . $_SERVER['REMOTE_ADDR'] . "'";

$rco = '';
if ( !empty( $old_cela ) ){

    // pegar o raio e cela antigos
    $q_rco = "SELECT
                `raio`.`raio`,
                `cela`.`cela`
              FROM
                `cela`
                INNER JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
              WHERE
                `idcela` = $old_cela
              LIMIT 1";

    // executando a query
    $q_rco = $model->query( $q_rco );
    $d_rco = $q_rco->fetch_assoc();
    $rco   = 'raio ' . $d_rco['raio'] . ', cela ' . $d_rco['cela'];

}

// pegar o raio e cela novos
$q_rcn = "SELECT
            `raio`.`raio`,
            `cela`.`cela`
          FROM
            `cela`
            INNER JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
          WHERE
            `idcela` = $n_cela
          LIMIT 1";

// executando a query
$q_rcn = $model->query( $q_rcn );
$d_rcn = $q_rcn->fetch_assoc();
$rcn   = 'raio ' . $d_rcn['raio'] . ', cela ' . $d_rcn['cela'];

$rc = $rcn;
if ( !empty( $rco ) ){
    $rc = "Mudou do $rco para o $rcn";
}

$query_rc = "INSERT INTO
               `mov_rc_det`
                 (
                   `cod_detento`,
                   `cod_old_cela`,
                   `cod_n_cela`,
                   `data_rc`,
                   `user_add`,
                   `data_add`,
                   `ip_add`
                 )
              VALUES
                (
                  $iddet,
                  $old_cela,
                  $n_cela,
                  STR_TO_DATE( $data_rc, '%d/%m/%Y' ),
                  $user,
                  NOW(),
                  $ip
                )";

$query_up_det = "UPDATE
                   `detentos`
                 SET
                   `cod_cela` = $n_cela,
                   `user_up` = $user,
                   `data_up` = NOW(),
                   `ip_up` = $ip
                 WHERE
                   `iddetento` = $iddet
                 LIMIT 1";

$success   = TRUE;
$erromysql = '';
$lastid    = 0;

// iniciando a transaction
$model->transaction();

// executando a query
$query_rc = $model->query( $query_rc );

if( $query_rc ) {

    $lastid = $model->lastInsertId();

} else {

    $erromysql .= "\n\n[ ERRO MYSQL - MUDANÇA DE " . mb_strtoupper( SICOP_RAIO ) . '/' . mb_strtoupper( SICOP_CELA ) . " ]\n";
    $erromysql .= $model->getErrorMsg();
    $success = FALSE;

}

if ( $success ) {

    // executando a query
    $query_up_det = $model->query( $query_up_det );

    if ( !$query_up_det ) {

        $erromysql .= "\n\n[ ERRO MYSQL - ATUALIZAÇÃO DE " . SICOP_DET_DESC_U . " - MUDANÇA DE " . mb_strtoupper( SICOP_RAIO ) . "/" . mb_strtoupper( SICOP_CELA ) . " ]\n";
        $erromysql .= $model->getErrorMsg();
        $success = FALSE;

    }

}



if ( $success ) {

    // cofimando as alterações
    $model->commit();

    $mensagem = "[ MUDANÇA DE " . mb_strtoupper( SICOP_RAIO ) . "/" . mb_strtoupper( SICOP_CELA ) . " ]\n Cadastro de uma nova mudança de raio e cela: ID: $lastid; \n\n $detento \n\n $rc \n";

} else {

    // em caso de falha, cancela as alterações
    $model->rollback();

    /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
    $valor_user = valor_user( $_POST );

    $mensagem = "[ <font color='#FF0000'><b>*** ERRO ***</b></font> ]\n Erro de mudança de " . mb_strtolower( SICOP_RAIO ) . '/' . mb_strtolower( SICOP_CELA ) . " de " . SICOP_DET_DESC_L . ".\n\n $detento.\n\n $valor_user \n $erromysql.";

}

salvaLog( $mensagem );

$msg_saida = 1;

if ( !$success ) $msg_saida = 0;

echo $msg_saida;

exit;

?>