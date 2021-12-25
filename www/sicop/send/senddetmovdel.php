<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';

$is_post = is_post();
if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso direto à página de manipulação de MOVIMENTAÇÃO DE ' . SICOP_DET_DESC_L . '.';
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

extract( $_POST, EXTR_OVERWRITE );

$idmov = empty( $idmov ) ? '' : (int)$idmov;
if ( empty( $idmov ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Identificador da movimentação em branco. Operação cancelada ( EXCLUSÃO DE MOVIMENTAÇÃO DE ' . SICOP_DET_DESC_L . ' ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$q_iddet = "SELECT `cod_detento` FROM `mov_det` WHERE `id_mov` = $idmov LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$iddet = $model->fetchOne( $q_iddet );

// fechando a conexao
$model->closeConnection();

if ( !$iddet ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Falha na consulta do id d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . ' ( EXCLUSÃO DE MOVIMENTAÇÃO ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Identificador d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . ' em branco. Operação cancelada ( EXCLUSÃO DE MOVIMENTAÇÃO ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 2 );
    exit;

}

// pegar os dados do preso
$detento = dados_det( $iddet );

$user = get_session( 'user_id', 'int' );
$ip   = "'" . $_SERVER['REMOTE_ADDR'] . "'";

$q_up_det = '';

$acerta_foto = false;

$q_mov_atual = "SELECT
                  `mov_det`.`id_mov`,
                  `mov_det`.`cod_detento`,
                  `mov_det`.`data_mov`
                FROM
                  `mov_det`
                WHERE
                  `mov_det`.`id_mov` = $idmov
                LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_mov_atual = $model->query( $q_mov_atual );

$d_mov_atual = $q_mov_atual->fetch_assoc();

$data_mov_atual  = $d_mov_atual['data_mov'];
$id_mov_atual    = $d_mov_atual['id_mov'];
$iddet           = $d_mov_atual['cod_detento'];

$q_mov_ant = "SELECT
                `mov_det`.`id_mov`,
                `mov_det`.`cod_tipo_mov`
              FROM
                `mov_det`
              WHERE
                `mov_det`.`cod_detento` = $iddet
                AND
                `mov_det`.`data_mov` <= '$data_mov_atual'
                AND
                `mov_det`.`id_mov` != $id_mov_atual
              ORDER BY
                `mov_det`.`data_mov` DESC, `mov_det`.`data_add` DESC
              LIMIT 1";

// executando a query
$q_mov_ant = $model->query( $q_mov_ant );

$cont_mov_ant = $q_mov_ant->num_rows;

// se não tiver movimentação aterior, a situação do detento passará a ser A CHEGAR
if( $cont_mov_ant < 1 ) {

    $q_up_det = "UPDATE
                   `detentos`
                 SET
                   `cod_movin` = NULL,
                   `cod_movout` = NULL,
                   `cod_cela` = NULL,
                   `cod_foto` = NULL,
                   `n_passagem` = 0,
                   `n_p_trans` = 0,
                   `user_up` = $user,
                   `data_up` = NOW(),
                   `ip_up` = $ip
                 WHERE
                   `iddetento` = $iddet
                 LIMIT 1";

} else {

    $d_mov_ant = $q_mov_ant->fetch_assoc();

    $tipo_mov_ant  = $d_mov_ant['cod_tipo_mov'];
    $id_mov_ant    = $d_mov_ant['id_mov'];

    // se a mov ant for IN, IT, IR...
    if( $tipo_mov_ant == 1 or $tipo_mov_ant == 2 or $tipo_mov_ant == 3 ) {

        $q_up_det = "UPDATE
                       `detentos`
                     SET
                       `cod_movin` = $id_mov_ant,
                       `cod_movout` = NULL,
                       `user_up` = $user,
                       `data_up` = NOW(),
                       `ip_up` = $ip
                     WHERE
                       `iddetento` = $iddet
                     LIMIT 1";

    } else {

        $q_mov_atp = "SELECT
                        `mov_det`.`id_mov`
                      FROM
                        `mov_det`
                      WHERE
                        `mov_det`.`cod_detento` = $iddet
                        AND
                        `mov_det`.`data_mov` <= '$data_mov_atual'
                        AND
                        `mov_det`.`id_mov` != $id_mov_atual
                        AND
                        `mov_det`.`id_mov` != $id_mov_ant
                        AND
                        ( `mov_det`.`cod_tipo_mov` = 1 OR `mov_det`.`cod_tipo_mov` = 2 OR `mov_det`.`cod_tipo_mov` = 3 )
                      ORDER BY
                        `mov_det`.`data_mov` DESC, `mov_det`.`data_add` DESC
                      LIMIT 1";

        // executando a query
        $q_mov_atp  = $model->query( $q_mov_atp );

        $d_mov_atp  = $q_mov_atp->fetch_assoc();

        $id_mov_atp = $d_mov_atp['id_mov'];

        $q_up_det = "UPDATE
                       `detentos`
                     SET
                       `cod_movin` = $id_mov_atp,
                       `cod_movout` = $id_mov_ant,
                       `user_up` = $user,
                       `data_up` = NOW(),
                       `ip_up` = $ip
                     WHERE
                       `iddetento` = $iddet
                     LIMIT 1";

        $acerta_foto = true;

    }

}

$q_d_mov = "SELECT
              `mov_det`.`id_mov`,
              `tipomov`.`tipo_mov`,
              `unidades`.`unidades` AS local_mov,
              DATE_FORMAT( `mov_det`.`data_mov`, '%d/%m/%Y' ) AS data_mov_f
            FROM
              `mov_det`
              LEFT JOIN `tipomov` ON `mov_det`.`cod_tipo_mov` = `tipomov`.`idtipo_mov`
              LEFT JOIN `unidades` ON `mov_det`.`cod_local_mov` = `unidades`.`idunidades`
            WHERE
              `mov_det`.`id_mov` = $idmov
            LIMIT 1";

// executando a query
$q_d_mov = $model->query( $q_d_mov );

$d_mov = $q_d_mov->fetch_assoc();

$idm       = $d_mov['id_mov'];
$tipo_mov  = $d_mov['tipo_mov'];
$local_mov = $d_mov['local_mov'];
$data_mov  = $d_mov['data_mov_f'];
$mov       = "<b>Tipo de movimentação:</b> $tipo_mov; <b>Local:</b> $local_mov; <b>Data:</b> $data_mov; <b>ID:</b> $idm";

$q_del_mov = "DELETE FROM `mov_det` WHERE `id_mov` = $idmov LIMIT 1";

$success = TRUE;
$erromysql = '';

// iniciando a transaction
$model->transaction();

// executando a query
$q_del_mov = $model->query( $q_del_mov );

if( !$q_del_mov ) {

    $erromysql .= "\n\n[ ERRO MYSQL - MOVIMENTAÇÃO ]\n";
    $erromysql .= $model->getErrorMsg();
    $success = FALSE;
}

// executando a query
$q_up_det = $model->query( $q_up_det );

if( !$q_up_det ) {

    $erromysql .= "\n\n[ ERRO MYSQL - ATUALIZAÇÃO DE " . SICOP_DET_DESC_U . " ]\n";
    $erromysql .= $model->getErrorMsg();
    $success = FALSE;
}

if ( !$success ) {

    // em caso de falha, cancela as alterações
    $model->rollback();

    /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
    $valor_user = valor_user( $_POST );

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Erro de exclusão de movimentação de " . SICOP_DET_DESC_L . ".\n\n $detento \n\n $valor_user \n\n $erromysql";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 1 );
    exit;

}

// cofimando as alterações
$model->commit();

// fechando a conexao
$model->closeConnection();

// montar a mensagem q será salva no log
$msg = array();
$msg['tipo']     = 'desc';
$msg['entre_ch'] = 'EXCLUSÃO DE MOVIMENTAÇÃO DE ' . SICOP_DET_DESC_U;
$msg['text']     = "Exclusão de movimentação de " . SICOP_DET_DESC_L . ". \n\n $mov \n\n $detento ";
get_msg( $msg, 1 );

if ( $acerta_foto ) {
    set_last_pic( $iddet, 1 );
}

echo msg_js( '', 1 );

exit;



?>