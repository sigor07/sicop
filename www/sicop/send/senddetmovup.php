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
    $msg['text']  = 'Tentativa de acesso direto à página de manipulação de MOVIMENTAÇÃO DE ' . SICOP_DET_DESC_U . '.';
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

extract($_POST, EXTR_OVERWRITE);

$idmov = empty( $idmov ) ? '' : (int)$idmov;

if ( empty( $idmov ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Identificador da movimentação em branco. Operação cancelada ( ATUALIZAÇÃO DE MOVIMENTAÇÃO DE ' . SICOP_DET_DESC_U . ' ).';
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

if ( !$iddet ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta do id d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " ( ATUALIZAÇÃO DE MOVIMENTAÇÃO DE " . SICOP_DET_DESC_U . " ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Identificador d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . ' em branco. Operação cancelada ( ATUALIZAÇÃO DE MOVIMENTAÇÃO DE ' . SICOP_DET_DESC_U . ' ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 2 );
    exit;

}

// pegar os dados do preso
$detento = dados_det( $iddet );

$tipo_mov = empty( $tipo_mov ) ? '' : (int)$tipo_mov;

if ( empty( $tipo_mov ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Identificador do tipo de movimentação em branco. Operação cancelada ( ATUALIZAÇÃO DE MOVIMENTAÇÃO DE " . SICOP_DET_DESC_U . " ). \n\n $detento";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$local_mov = empty( $local_mov ) ? 'NULL' : (int)$local_mov;

if ( ( $tipo_mov != 4 and $tipo_mov != 8 ) and ( empty( $local_mov ) or $local_mov == 'NULL' ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Identificador do local da movimentação em branco. Operação cancelada ( ATUALIZAÇÃO DE MOVIMENTAÇÃO DE " . SICOP_DET_DESC_U . " ). \n\n $detento";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$data_mov = empty( $data_mov ) ? '' : $data_mov;

if ( empty( $data_mov ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Data da movimentação em branco. Operação cancelada ( ATUALIZAÇÃO DE MOVIMENTAÇÃO DE " . SICOP_DET_DESC_U . " ). \n\n $detento";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

if ( !validaData( $data_mov, 'DD/MM/AAAA' ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Data da movimentação inválida. Operação cancelada ( ATUALIZAÇÃO DE MOVIMENTAÇÃO DE " . SICOP_DET_DESC_U . " ). \n\n $detento";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 2 );
    exit;

}

$time_data_atual = strtotime( date( 'Y-m-d' ) );

$partes = explode( '/', $data_mov );
$time_data_mov = mktime( 0, 0, 0, $partes[1], $partes[0], $partes[2] );

if ( $time_data_mov > $time_data_atual ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Data da movimentação futura. Operação cancelada ( ATUALIZAÇÃO DE MOVIMENTAÇÃO DE " . SICOP_DET_DESC_U . " ). \n\n $detento";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 2 );
    exit;

}

$data_mov  = "'" . $model->escape_string( $data_mov ) . "'";

$user      = get_session( 'user_id', 'int' );
$ip        = "'" . $_SERVER['REMOTE_ADDR'] . "'";

$q_tipo_mov_ant = "SELECT
                     `tipomov`.`idtipo_mov`
                   FROM
                     `mov_det`
                     LEFT JOIN `tipomov` ON `mov_det`.`cod_tipo_mov` = `tipomov`.`idtipo_mov`
                   WHERE
                     `mov_det`.`id_mov` = $idmov
                   LIMIT 1";

// executando a query
$tipo_mov_ant   = $model->fetchOne( $q_tipo_mov_ant );
$tipo_mov_novo  = $tipo_mov;
$add_n_pass     = false;
$rem_n_pass     = false;
$up_det_out     = false;
$up_det_in      = false;
$query_up_det   = '';

if ( $tipo_mov_ant == 2 and ( $tipo_mov_novo == 1 or $tipo_mov_novo == 3 ) ) {

    $add_n_pass = true;

}

if ( $tipo_mov_novo == 2 and ( $tipo_mov_ant == 1 or $tipo_mov_ant == 3 ) ) {

    $rem_n_pass = true;

}

if ( $tipo_mov_ant == 1 or $tipo_mov_ant == 3 ) {

    if ( $tipo_mov_novo == 5 or $tipo_mov_novo == 7 ) {

        $up_det_out = true;

    }

}

if ( $tipo_mov_ant == 5 or $tipo_mov_ant == 7 ) {

    if ( $tipo_mov_novo == 1 or $tipo_mov_novo == 3 ) {

        $up_det_in = true;

    }

}

$query_up_mov = "UPDATE
                   `mov_det`
                 SET
                   `cod_tipo_mov` = $tipo_mov,
                   `cod_local_mov` = $local_mov,
                   `data_mov` = STR_TO_DATE( $data_mov, '%d/%m/%Y' ),
                   `user_up` = $user,
                   `data_up` = NOW(),
                   `ip_up` = $ip
                 WHERE
                   `id_mov` = $idmov
                 LIMIT 1";

$success = TRUE;
$erromysql = '';

// iniciando a transaction
$model->transaction();

// executando a query
$query_up_mov = $model->query( $query_up_mov );

if ( !$query_up_mov ) {

    $erromysql .= "\n\n[ ERRO MYSQL - ATUALIZAÇÃO DE MOVIMENTAÇÃO MOVIMENTAÇÃO ]\n";
    $erromysql .= $model->getErrorMsg();
    $success = FALSE;

}

if ( $up_det_out ) {

    // QUERY PARA PEGAR A ULTIMA MOVIMENTAÇÃO POR IN, IT OU IR
    $q_last_mov_in = "SELECT
                        `id_mov`
                      FROM
                        `mov_det`
                      WHERE
                        ( `cod_tipo_mov` = 1 OR `cod_tipo_mov` = 2 OR `cod_tipo_mov` = 3 )
                        AND
                        `cod_detento` = $iddet
                        AND
                        `id_mov` != $idmov
                      ORDER BY
                        `data_mov` DESC, `data_add` DESC
                      LIMIT 1";

    // executando a query
    $id_last_mov_in = $model->fetchOne( $q_last_mov_in );

    $query_up_det = "UPDATE
                       `detentos`
                     SET
                       `cod_movin` = $id_last_mov_in,
                       `cod_movout` = $idmov,
                       `user_up` = $user,
                       `data_up` = NOW(),
                       `ip_up` = $ip
                     WHERE
                       `iddetento` = $iddet
                     LIMIT 1";

}

if ( $up_det_in ) {

    $query_up_det = "UPDATE
                       `detentos`
                     SET
                       `cod_movin` = $idmov,
                       `cod_movout` = NULL,
                       `user_up` = $user,
                       `data_up` = NOW(),
                       `ip_up` = $ip
                     WHERE
                       `iddetento` = $iddet
                     LIMIT 1";

}

if ( $success ) {

    if ( !empty( $query_up_det ) ) {

        // executando a query
        $query_up_det = $model->query( $query_up_det );

        if ( !$query_up_det ) {

            $erromysql .= "\n\n[ ERRO MYSQL - ATUALIZAÇÃO DE MOVIMENTAÇÃO N" . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U . " ]\n";
            $erromysql .= $model->getErrorMsg();
            $success = FALSE;

        }

    }

}

if ( $success ) {

    $q_d_up = '';

    if ( $add_n_pass ) {

        $q_d_up = "UPDATE
                     `detentos`
                   SET
                     `n_passagem` = `n_passagem` + 1
                   WHERE
                     `iddetento` = $iddet
                   LIMIT 1";

    }

    if ( $rem_n_pass ) {

        $q_d_up = "UPDATE
                     `detentos`
                   SET
                     `n_passagem` = `n_passagem` - 1
                   WHERE
                     `iddetento` = $iddet
                   LIMIT 1";

    }

    if ( !empty( $q_d_up ) ) {

        // executando a query
        $q_d_up = $model->query( $q_d_up );

        if ( !$q_d_up ) {

            $erromysql .= "\n\n[ ERRO MYSQL - ATUALIZAÇÃO DO NÚMERO DE PASSAGENS N" . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U . " ]\n";
            $erromysql .= $model->getErrorMsg();
            $success = FALSE;

        }

    }

}



if ( !$success ) {

    // em caso de falha, cancela as alterações
    $model->rollback();

    /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
    $valor_user = valor_user( $_POST );

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Erro de atualização de movimentação de " . SICOP_DET_DESC_L . ".\n\n $detento \n\n $valor_user \n\n $erromysql";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 2 );
    exit;

}

// cofimando as alterações
$model->commit();

// fechando a conexao
$model->closeConnection();

// pegar os dados da movimentação
$mov = dados_mov( $idmov, 1 );

// montar a mensagem q será salva no log
$msg = array();
$msg['tipo']     = 'desc';
$msg['entre_ch'] = 'ATUALIZAÇÃO DE MOVIMENTAÇÃO DE ' . SICOP_DET_DESC_U;
$msg['text']     = "Atualização de movimentação de " . SICOP_DET_DESC_L . ". \n\n $mov \n\n $detento ";
get_msg( $msg, 1 );

echo msg_js( '', 2 );
exit;


?>