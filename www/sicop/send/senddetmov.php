<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';

$n_det_mov = get_session( 'n_det_mov', 'int' );

if ( empty( $n_det_mov ) or $n_det_mov < 1 ) {

    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso à página de manipulação de MOVIMENTAÇÃO DE ' . SICOP_DET_DESC_U . ' SEM PERMISSÕES.';
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

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

$targ  = empty( $targ ) ? 0 : 1;

$iddet = empty( $iddet ) ? '' : (int)$iddet;

if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Identificador d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . ' em branco. Operação cancelada ( MOVIMENTAÇÃO DE ' . SICOP_DET_DESC_U . ' ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    $ret = 2;
    if ( !empty( $targ ) ) $ret = 'f';
    echo msg_js( 'FALHA!', $ret );

    exit;

}

// pegar os dados do preso
$detento = dados_det( $iddet );

$tipo_mov  = empty( $tipo_mov ) ? 'NULL' : (int)$tipo_mov;
$local_mov = empty( $local_mov ) ? 'NULL' : (int)$local_mov;
$data_mov  = empty( $data_mov ) ? 'NULL' : "'" . $data_mov . "'";

$user      = get_session( 'user_id', 'int' );
$ip        = "'" . $_SERVER['REMOTE_ADDR'] . "'";

$query_mov = "INSERT INTO
                `mov_det`
                (
                  `cod_detento`,
                  `cod_tipo_mov`,
                  `cod_local_mov`,
                  `data_mov`,
                  `user_add`,
                  `data_add`,
                  `ip_add`
                )
              VALUES
                (
                  $iddet,
                  $tipo_mov,
                  $local_mov,
                  STR_TO_DATE($data_mov, '%d/%m/%Y'),
                  $user,
                  NOW(),
                  $ip
                )";

// QUERY PARA PEGAR A ULTIMA MOVIMENTAÇÃO POR IN OU IR
$q_last_mov_in = "SELECT
                    `id_mov`
                  FROM
                    `mov_det`
                  WHERE
                    ( `cod_tipo_mov` = 1 OR `cod_tipo_mov` = 3 ) AND `cod_detento` = $iddet
                  ORDER BY
                    `data_mov` DESC, `data_add` DESC
                  LIMIT 1";

// QUERY PARA PEGAR A PRIMEIRA EX OU ER DEPOIS DA ULTIMA IN OU IR
$q_last_mov_out = "SELECT
                      `id_mov`,
                      `cod_local_mov`
                    FROM
                      `mov_det`
                    WHERE
                      ( `cod_tipo_mov` = 5 OR `cod_tipo_mov` = 7 )
                      AND
                      `data_mov` >= ( SELECT `data_mov` FROM `mov_det` WHERE ( `cod_tipo_mov` = 1 OR `cod_tipo_mov` = 3 ) AND `cod_detento` = $iddet ORDER BY `data_mov` DESC, `data_add` DESC LIMIT 1 )
                      AND
                      `cod_detento` = $iddet
                    ORDER BY
                      `data_mov` ASC, `data_add` ASC
                    LIMIT 1";

// QUERY PARA PEGAR OS TIPOS DE MOVIMENTAÇÕES
$q_tipo_mov = "SELECT
                 `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
                 `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
                 `unidades_out`.`idunidades` AS iddestino
               FROM
                 `detentos`
                 LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                 LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                 LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
               WHERE
                 `detentos`.`iddetento` = $iddet
               LIMIT 1";

$success   = TRUE;
$erromysql = '';

// instanciando o model
$model = SicopModel::getInstance();

$model->transaction();

$query_mov = $model->query( $query_mov );

$lastid = 0;

if( $query_mov ) {
    $lastid = $model->lastInsertId();
} else {
    $erromysql .= "\n\n[ ERRO MYSQL - MOVIMENTAÇÃO ]\n";
    $erromysql .= $model->getErrorMsg();
    $success = FALSE;
}

$query_up_det = '';

if ( $tipo_mov == 1 || $tipo_mov == 3 ) { // inclusao || inclusao por remoçao

    $q_tipo_mov_ant = "SELECT
                         `tipomov`.`idtipo_mov`
                       FROM
                         `mov_det`
                         LEFT JOIN `tipomov` ON `mov_det`.`cod_tipo_mov` = `tipomov`.`idtipo_mov`
                       WHERE
                         `mov_det`.`cod_detento` = $iddet
                         AND
                         `mov_det`.`id_mov` != $lastid
                       ORDER BY
                         `data_mov` DESC, `data_add` DESC
                       LIMIT 1";

    $q_tipo_mov_ant = $model->query( $q_tipo_mov_ant );
    $d_tipo_mov_ant = $q_tipo_mov_ant->fetch_assoc();
    $tipo_mov_ant   = $d_tipo_mov_ant['idtipo_mov'];

    $query_up_det = "UPDATE
                       `detentos`
                     SET
                       `cod_movin` = $lastid,
                       `cod_movout` = NULL,
                       `cod_sit_proc` = NULL,
                       `cod_cela` = NULL,
                       `cod_foto` = NULL,
                       `jaleco` = NULL,
                       `calca` = NULL,
                       `conduta_ant` = NULL,
                       `data_reab` = NULL,
                       `n_passagem` = `n_passagem` + 1,
                       `n_p_trans` = `n_p_trans` + 1,
                       `user_up` = $user,
                       `data_up` = NOW(),
                       `ip_up` = $ip
                     WHERE
                       `iddetento` = $iddet
                     LIMIT 1";

    if ( $tipo_mov_ant == 2 ) {

        $query_up_det = "UPDATE
                           `detentos`
                         SET
                           `cod_movin` = $lastid,
                           `cod_movout` = NULL,
                           `n_passagem` = `n_passagem` + 1,
                           `user_up` = $user,
                           `data_up` = NOW(),
                           `ip_up` = $ip
                         WHERE
                           `iddetento` = $iddet
                         LIMIT 1";

    }

} else if ( $tipo_mov == 2 ) { // INCLUSAO POR TRANSITO

        $query_up_det = "UPDATE
                           `detentos`
                         SET
                           `cod_movin` = $lastid,
                           `cod_movout` = NULL,
                           `cod_cela` = NULL,
                           `cod_foto` = NULL,
                           `jaleco` = NULL,
                           `calca` = NULL,
                           `n_p_trans` = `n_p_trans` + 1,
                           `user_up` = $user,
                           `data_up` = NOW(),
                           `ip_up` = $ip
                         WHERE
                           `iddetento` = $iddet
                         LIMIT 1";

} else if ( $tipo_mov == 4 ) { // INCLUSAO POR RETORNO

        $query_up_det = "UPDATE
                           `detentos`
                         SET
                           `cod_movout` = $lastid,
                           `user_up` = $user,
                           `data_up` = NOW(),
                           `ip_up` = $ip
                         WHERE
                           `iddetento` = $iddet
                         LIMIT 1";

} else if ( $tipo_mov == 5 ) { // EXCLUSAO

    $q_tipo_mov = $model->query( $q_tipo_mov );
    $d_tipo_mov = $q_tipo_mov->fetch_assoc();

    $tipo_mov_in  = $d_tipo_mov['tipo_mov_in'];
    $tipo_mov_out = $d_tipo_mov['tipo_mov_out'];
    $iddestino    = $d_tipo_mov['iddestino'];

    if ( $tipo_mov_in == 1 || $tipo_mov_in == 3 ) {  // SE A SITUAÇAO DO DETENTO FOR 'NA CASA' || TRANSITO DA CASA

        $query_up_det = "UPDATE
                           `detentos`
                         SET
                           `cod_movout` = $lastid,
                           `user_up` = $user,
                           `data_up` = NOW(),
                           `ip_up` = $ip
                         WHERE
                           `iddetento` = $iddet
                         LIMIT 1";

    } else if ( $tipo_mov_in == 2 ) { // SE A SITUAÇAO DO DETENTO FOR TRANSITO NA CASA

        $last_local_out = '';

        // EXECUTA A QUERY PARA PEGAR A ULTIMA MOVIMENTAÇÃO POR IN OU IR
        $q_last_mov_in = $model->query( $q_last_mov_in );
        $cont_mov_in = $q_last_mov_in->num_rows;

        // SE RETORNAR 1 LINHA...
        if ( $cont_mov_in == 1 ) {

            $lastin     = $q_last_mov_in->fetch_assoc();
            $last_id_in = $lastin['id_mov'];

            // EXECUTA A QUERY PARA PEGAR A PRIMEIRA EX OU ER DEPOIS DA ULTIMA IN OU IR
            $q_last_mov_out = $model->query( $q_last_mov_out );
            $cont_mov_out = $q_last_mov_out->num_rows;

            if ( $cont_mov_out == 1 ) {

                $lastout        = $q_last_mov_out->fetch_assoc();
                $last_id_out    = $lastout['id_mov'];
                $last_local_out = $lastout['cod_local_mov'];

            }

        }

        // SE TIVER ULTIMA SAIDA, É POR QUE O DETENTO JA PASSOU NA UNIDADE, ENTÃO VAI PREENCHER COM OS DADOS DAS ULTIMAS MOV
        if ( !empty( $last_id_out ) ) {

            $query_up_det = "UPDATE
                               `detentos`
                             SET
                               `cod_movin` = $last_id_in,
                               `cod_movout` = $last_id_out,
                               `user_up` = $user,
                               `data_up` = NOW(),
                               `ip_up` = $ip
                             WHERE
                               `iddetento` = $iddet
                             LIMIT 1";

        // SE NÃO TIVER ULTIMA SAIDA, VAI PRESERVAR OS DADOS DA PASSAGEM POR TRANSITO
        } else {

            $query_up_det = "UPDATE
                               `detentos`
                             SET
                               `cod_movout` = $lastid,
                               `user_up` = $user,
                               `data_up` = NOW(),
                               `ip_up` = $ip
                             WHERE
                               `iddetento` = $iddet
                             LIMIT 1";
        }

    }

} else if ( $tipo_mov == 6 ) { // EXCLUSAO POR TRANSITO

        $query_up_det = "UPDATE
                           `detentos`
                         SET
                           `cod_movout` = $lastid,
                           `user_up` = $user,
                           `data_up` = NOW(),
                           `ip_up` = $ip
                         WHERE
                           `iddetento` = $iddet
                         LIMIT 1";

} else if ( $tipo_mov == 7 ) { // EXCLUSAO POR REMOÇÃO

    $q_tipo_mov = $model->query( $q_tipo_mov );
    $d_tipo_mov = $q_tipo_mov->fetch_assoc();

    $tipo_mov_in  = $d_tipo_mov['tipo_mov_in'];
    $tipo_mov_out = $d_tipo_mov['tipo_mov_out'];
    $iddestino    = $d_tipo_mov['iddestino'];

    if ( $tipo_mov_in == 1 || $tipo_mov_in == 3 ) {  // SE A SITUAÇAO DO DETENTO FOR 'NA CASA' || TRANSITO NA CASA

            $query_up_det = "UPDATE
                               `detentos`
                             SET
                               `cod_movout` = $lastid,
                               `user_up` = $user,
                               `data_up` = NOW(),
                               `ip_up` = $ip
                             WHERE
                               `iddetento` = $iddet
                             LIMIT 1";

    } else if ( $tipo_mov_in == 2 ) {

        $last_local_out = '';

        // EXECUTA A QUERY PARA PEGAR A ULTIMA MOVIMENTAÇÃO POR IN OU IR
        $q_last_mov_in = $model->query( $q_last_mov_in );
        $cont_mov_in = $q_last_mov_in->num_rows;

        // SE RETORNAR 1 LINHA...
        if ( $cont_mov_in == 1 ) {

            $lastin = $q_last_mov_in->fetch_assoc();
            $last_id_in = $lastin['id_mov'];

            // EXECUTA A QUERY PARA PEGAR A PRIMEIRA EX OU ER DEPOIS DA ULTIMA IN OU IR
            $q_last_mov_out = $model->query( $q_last_mov_out );
            $cont_mov_out = $q_last_mov_out->num_rows;

            if ( $cont_mov_out == 1 ) {

                $lastout        = $q_last_mov_out->fetch_assoc();
                $last_id_out    = $lastout['id_mov'];
                $last_local_out = $lastout['cod_local_mov'];

            }

        }

        // SE TIVER ULTIMA SAIDA, É POR QUE O DETENTO JA PASSOU NA UNIDADE, ENTÃO VAI PREENCHER COM OS DADOS DAS ULTIMAS MOV
        if ( !empty( $last_id_out ) ) {

            $query_up_det = "UPDATE
                               `detentos`
                             SET
                               `cod_movin` = $last_id_in,
                               `cod_movout` = $last_id_out,
                               `user_up` = $user,
                               `data_up` = NOW(),
                               `ip_up` = $ip
                             WHERE
                               `iddetento` = $iddet
                             LIMIT 1";

        // SE NÃO TIVER ULTIMA SAIDA, VAI PRESERVAR OS DADOS DA PASSAGEM POR TRANSITO
        } else {

            $query_up_det = "UPDATE
                               `detentos`
                             SET
                               `cod_movout` = $lastid,
                               `user_up` = $user,
                               `data_up` = NOW(),
                               `ip_up` = $ip
                             WHERE
                               `iddetento` = $iddet
                             LIMIT 1";

        }

    }

} else if ( $tipo_mov == 8 ) { // EXCLUSAO POR RETORNO

    $last_local_out = '';

    // EXECUTA A QUERY PARA PEGAR A ULTIMA MOVIMENTAÇÃO POR IN OU IR
    $q_last_mov_in = $model->query( $q_last_mov_in );
    $cont_mov_in = $q_last_mov_in->num_rows;

    // SE RETORNAR 1 LINHA...
    if ( $cont_mov_in == 1 ) {

        $lastin = $q_last_mov_in->fetch_assoc();
        $last_id_in = $lastin['id_mov'];

        // EXECUTA A QUERY PARA PEGAR A PRIMEIRA EX OU ER DEPOIS DA ULTIMA IN OU IR
        $q_last_mov_out = $model->query( $q_last_mov_out );
        $cont_mov_out = $q_last_mov_out->num_rows;

        if ( $cont_mov_out == 1 ) {

            $lastout = $q_last_mov_out->fetch_assoc();
            $last_id_out    = $lastout['id_mov'];
            $last_local_out = $lastout['cod_local_mov'];

        }

    }

    // SE TIVER ULTIMA SAIDA, É POR QUE O DETENTO JA PASSOU NA UNIDADE, ENTÃO VAI PREENCHER COM OS DADOS DAS ULTIMAS MOV
    if ( !empty( $last_id_out ) ) {

        $query_up_det = "UPDATE
                           `detentos`
                         SET
                           `cod_movin` = $last_id_in,
                           `cod_movout` = $last_id_out,
                           `user_up` = $user,
                           `data_up` = NOW(),
                           `ip_up` = $ip
                         WHERE
                           `iddetento` = $iddet
                         LIMIT 1";

    // SE NÃO TIVER ULTIMA SAIDA, VAI PRESERVAR OS DADOS DA PASSAGEM POR TRANSITO
    } else {

        $query_up_det = "UPDATE
                           `detentos`
                         SET
                           `cod_movout` = $lastid,
                           `user_up` = $user,
                           `data_up` = NOW(),
                           `ip_up` = $ip
                         WHERE
                           `iddetento` = $iddet
                         LIMIT 1";

    }

}

$query_up_det = $model->query( $query_up_det );

if ( !$query_up_det ) {
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
    $msg['text']  = 'Erro de cadastramento de movimentação de ' . SICOP_DET_DESC_L . ".\n\n $detento \n\n $valor_user \n\n $erromysql";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    $ret = 2;
    if ( !empty( $targ ) ) $ret = 'f';
    echo msg_js( 'FALHA!', $ret );

    exit;

}

// cofimando as alterações
$model->commit();

// fechando a conexao
$model->closeConnection();

// pegar os dados da movimentação
$mov = dados_mov( $lastid, 1 );

// montar a mensagem q será salva no log
$msg = array();
$msg['tipo']     = 'desc';
$msg['entre_ch'] = 'MOVIMENTAÇÃO DE ' . SICOP_DET_DESC_U;
$msg['text']     = 'Cadastro de nova movimentação de ' . SICOP_DET_DESC_L . ". \n\n $mov \n\n $detento ";
get_msg( $msg, 1 );

$ret = 2;
if ( !empty( $targ ) ) $ret = 'rf';

echo msg_js( '', $ret );

exit;

?>
    </body>
</html>