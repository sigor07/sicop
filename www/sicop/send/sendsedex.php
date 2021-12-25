<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';
$mensagem = '';

$n_sedex   = get_session( 'n_sedex', 'int' );
if ( empty( $n_sedex ) or $n_sedex < 3 ) {

    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso à página de manipulação de sedex SEM PERMISSÕES.';
    get_msg( $msg, 1 );

    exit;

}

$is_post = is_post();

if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'atn';
    $msg['text'] = 'Tentativa de acesso direto à página de manipulação de sedex.';
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

extract( $_POST, EXTR_OVERWRITE );

$targ   = empty($targ) ? 0 : 1;
$proced = empty( $proced ) ? '' : (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO

if ( empty( $proced ) or $proced > 3 ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = 'Número de procedimento em branco ou inválido. Operação cancelada ( SEDEX ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    $ret = 1;
    if ( !empty( $targ ) ) $ret = 'f';
    echo msg_js( 'FALHA!', $ret );

    exit;

}

$user      = get_session( 'user_id', 'int' );
$ip        = "'" . $_SERVER['REMOTE_ADDR'] . "'";

if ( $proced == 1 ) { // ATUALIZAÇÃO
/*
 * -------------------------------------------------------------------
 * PARTE RESPONSAVEL PELA ATUALIZAÇÃO
 * -------------------------------------------------------------------
 */

    $sub_proced = empty( $sub_proced ) ? '' : (int)$sub_proced;
    if ( empty( $sub_proced ) or $sub_proced > 5 or $sub_proced < 2 ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = 'Número de sub-procedimento em branco ou inválido. Operação cancelada ( ATUALIZAÇÃO DE SEDEX ).';
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 1;
        if ( !empty( $targ ) ) $ret = 'f';
        echo msg_js( 'FALHA!', $ret );

        exit;

    }

    $idsedex = empty( $idsedex ) ? '' : $idsedex;
    if ( empty( $idsedex ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = 'O usuário não marcou nenhum sedex ( ATUALIZAÇÃO DE SEDEX ).';
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'Você deve marcar pelo menos um sedex!', 1 );

        exit;

    }

    // monta a variavel para o comparador IN()
    $v_sedex = '';
    foreach ( $idsedex as $indice => $valor ) {
        if ( (int)$valor == NULL ) continue;
        $v_sedex .= (int)$valor . ',';
    }

    if ( empty( $v_sedex ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = 'Após validação, o array ficou vazio ( ATUALIZAÇÃO DE SEDEX ).';
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    $v_sedex = substr( $v_sedex, 0, -1 );

    $sit_sedex = '';
    $marq_como = '';
    $sql_mot   = '';

    if ( $sub_proced == 2 ) { // 2 = encaminhar para inclusão

        $sit_sedex = 2;
        $marq_como = 'ENCAMINHADO PARA INCLUSÃO';

    } else if ( $sub_proced == 3 ) { // 3 = separado para devolução

        $motivo_dev = empty( $motivo_dev ) ? '' : (int)$motivo_dev;

        if ( empty( $motivo_dev ) ) {

            // montar a mensagem q será salva no log
            $msg = array( );
            $msg['tipo'] = 'err';
            $msg['text'] = 'Identificador do motivo da devolução em branco ou inválido ( ATUALIZAÇÃO DE SEDEX ).';
            $msg['linha'] = __LINE__;
            get_msg( $msg, 1 );

            echo msg_js( 'Informe o motivo da devolução do sedex!', 1 );

            exit;

        }

        $sql_mot = "`cod_motivo_dev` = $motivo_dev,";

        $sit_sedex = 3;
        $marq_como = 'SEPARADO P/ DEVOLUÇÃO';

    } else if ( $sub_proced == 4 ) { // 4 = devolvido

        $sit_sedex = 4;
        $marq_como = 'DEVOLVIDO';

    } else if ( $sub_proced == 5 ) { // 5 = entregue p/ detento

        $sit_sedex = 5;
        $marq_como = 'ENTREGUE';

    }

    // pegar os dados dos sedex
    $sedex = dados_sedex( $v_sedex );

    $query_sedex = "UPDATE
                      `sedex`
                    SET
                      `sit_sedex` = $sit_sedex,
                      $sql_mot
                      `user_up` = $user,
                      `data_up` = NOW(),
                      `ip_up` = $ip
                    WHERE
                      `idsedex` IN( $v_sedex )";

    // monta a clausula VALUES, da query de movimentação
    $value_sedex = '';
    foreach ( $idsedex as $indice => $valor ) {
        $value_sedex .= "( $valor, $sit_sedex, NOW() ),";
    }

    $value_sedex = substr( $value_sedex, 0, -1 );

    $q_mov_sedex = "INSERT INTO
                      `sedex_mov`
                      (
                        `cod_sedex`,
                        `sit_sedex`,
                        `data_mov`
                      )
                    VALUES
                      $value_sedex";

    $success = TRUE;
    $erromysql = '';

    // instanciando o model
    $model = SicopModel::getInstance();

    // iniciando a transaction
    $model->transaction();

    // executando a query
    $query_sedex = $model->query( $query_sedex );

    if( !$query_sedex ) {

        // pegar a mensagem de erro mysql
        $msg_err_mysql = $model->getErrorMsg();

        $erromysql .= "\n\n[ ERRO MYSQL - ATUALIZAÇÃO DE SEDEX ]\n";
        $erromysql .= $msg_err_mysql;
        $success = FALSE;

    }

    // executando a query
    $q_mov_sedex = $model->query( $q_mov_sedex );

    if( !$q_mov_sedex ) {

        // pegar a mensagem de erro mysql
        $msg_err_mysql = $model->getErrorMsg();

        $erromysql .= "\n\n[ ERRO MYSQL - MOVIMENTAÇÃO DE SEDEX ]\n";
        $erromysql .= $msg_err_mysql;
        $success = FALSE;

    }

    if ( $success ) {

        // cofimando as alterações
        $model->commit();

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'desc';
        $msg['entre_ch'] = 'ATUALIZAÇÃO DE SEDEX';
        $msg['text'] = "Atualização de sedex. \n\n $sedex \n\n Marcados como: $marq_como";

        get_msg( $msg, 1 );

        $n_ret = 1;
        if ( $sub_proced == 3 ) $n_ret = 2;
        echo msg_js( '', $n_ret );

        exit;

    } else {

        // em caso de falha, cancela as alterações
        $model->rollback();

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo'] = 'err';
        $msg['text'] = "Erro de atualização de sedex.\n\n $sedex $erromysql.";
        $msg['linha'] = __LINE__;

        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELA ATUALIZAÇÃO
 * -------------------------------------------------------------------
 */
} else if ( $proced == 2 ) { //EXCLUSÃO
/*
 * -------------------------------------------------------------------
 * PARTE DO CODIGO RESPONSAVEL PELA EXCLUSÃO
 * -------------------------------------------------------------------
 */

    if ( empty( $n_sedex ) or $n_sedex < 4 ) {

        $tipo = 0;
        include '../init/msgnopag.php';

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'atn';
        $msg['text']  = 'Tentativa de acesso à página de manipulação de sedex SEM PERMISSÕES ( EXCLUSÃO DE SEDEX ).';
        get_msg( $msg, 1 );

        exit;

    }

    $sub_proced = empty( $sub_proced ) ? '' : (int)$sub_proced;
    if ( empty( $sub_proced ) or $sub_proced > 3 ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = 'Número de sub-procedimento em branco ou inválido. Operação cancelada ( ATUALIZAÇÃO DE SEDEX ).';
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 1;
        if ( !empty( $targ ) ) $ret = 'f';
        echo msg_js( 'FALHA!', $ret );

        exit;

    }

    if ( $sub_proced == 1 ) { // 1 = EXCLUSÃO DE SEDEX EM LOTE

        $idsedex = empty( $idsedex ) ? '' : $idsedex;
        if ( empty( $idsedex ) ) {

            // montar a mensagem q será salva no log
            $msg = array( );
            $msg['tipo'] = 'err';
            $msg['text'] = 'O usuário não marcou nenhum sedex ( EXCLUSÃO DE SEDEX ).';
            $msg['linha'] = __LINE__;
            get_msg( $msg, 1 );

            echo msg_js( 'Você deve marcar pelo menos um sedex!', 1 );

            exit;

        }

        // monta a variavel para o comparador IN()
        $v_sedex = '';
        foreach ( $idsedex as &$valor ) {
            if ( (int)$valor == NULL ) continue;
            $v_sedex .= (int)$valor . ',';
        }

        if ( empty( $v_sedex ) ) {

            // montar a mensagem q será salva no log
            $msg = array( );
            $msg['tipo'] = 'err';
            $msg['text'] = 'Após validação, o array ficou vazio ( EXCLUSÃO DE SEDEX ).';
            $msg['linha'] = __LINE__;
            get_msg( $msg, 1 );

            $ret = 1;
            if ( !empty( $targ ) ) $ret = 'f';
            echo msg_js( 'FALHA!', 1 );

            exit;

        }

        // retirar a última virgula
        $v_sedex = substr( $v_sedex, 0, -1 );

        // pegar os dados dos sedex
        $sedex = dados_sedex( $v_sedex );

        $query_sedex = "DELETE FROM `sedex` WHERE `idsedex` IN( $v_sedex )";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $query_sedex = $model->query( $query_sedex );

        // fechando a conexao
        $model->closeConnection();

        $success = TRUE;
        if ( $query_sedex ) {

            $msg = array( );
            $msg['tipo'] = 'desc';
            $msg['entre_ch'] = 'EXCLUSÃO DE SEDEX';
            $msg['text'] = "Exclusão de sedex. \n\n $sedex";

            $mensagem = get_msg( $msg );

        } else {

            $success = FALSE;

            // montar a mensagem q será salva no log
            $msg = array( );
            $msg['tipo'] = 'err';
            $msg['text'] = "Erro de exclusão de sedex.\n\n $sedex.";
            $msg['linha'] = __LINE__;

            $mensagem = get_msg( $msg );

        }

        salvaLog( $mensagem );

        $msg_saida = '';

        if ( !$success ) {

            $msg_saida = 'FALHA!!!';

        }

        echo msg_js( $msg_saida, 1 );

        exit;


    } else if ( $sub_proced == 2 ) { // 2 = EXCLUSÃO DE MOVIMENTAÇÃO DE SEDEX

        $idmovsedex = empty( $idmovsedex ) ? '' : (int)$idmovsedex;

        if ( empty( $idmovsedex ) ) {

            // montar a mensagem q será salva no log
            $msg = array( );
            $msg['tipo'] = 'err';
            $msg['text'] = 'Identificador da movimentação do sedex em branco ( EXCLUSÃO DE MOVIEMNTAÇÃO DE SEDEX ).';
            $msg['linha'] = __LINE__;
            get_msg( $msg, 1 );

            echo msg_js( 'FALHA!', 1 );

            exit;

        }

        $q_mov_atual = "SELECT
                          `sedex_mov`.`idmovsedex`,
                          `sedex_mov`.`cod_sedex`,
                          `sedex_mov`.`sit_sedex`,
                          `sedex_mov`.`data_mov`
                        FROM
                          `sedex_mov`
                        WHERE
                          `sedex_mov`.`idmovsedex` = $idmovsedex
                        LIMIT 1";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $q_mov_atual = $model->query( $q_mov_atual );

        // fechando a conexao
        $model->closeConnection();

        if( !$q_mov_atual ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']  = 'err';
            $msg['text']  = 'Falha na consulta de movimentação atual ( EXCLUSÃO DE MOVIEMNTAÇÃO DE SEDEX ).';
            $msg['linha'] = __LINE__;
            get_msg( $msg, 1 );

            echo msg_js( '', 1 );
            exit;

        }

        $cont_mov_atual = $q_mov_atual->num_rows;

        if( $cont_mov_atual < 1 ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']  = 'err';
            $msg['text']  = 'A consulta de movimentação atual retornou 0 ocorrências ( EXCLUSÃO DE MOVIEMNTAÇÃO DE SEDEX ).';
            $msg['linha'] = __LINE__;
            get_msg( $msg, 1 );

            echo msg_js( '', 1 );
            exit;

        }

        $d_mov_atual = $q_mov_atual->fetch_assoc();

        $data_mov_atual  = $d_mov_atual['data_mov'];
        //$sit_sedex_atual = $d_mov_atual['sit_sedex'];
        $id_mov_atual    = $d_mov_atual['idmovsedex'];
        $idsedex         = $d_mov_atual['idsedex'];

        // pegar os dados dos sedex
        $sedex = dados_sedex( $idsedex, 1 );

        $q_mov_ant = "SELECT
                        `sedex_mov`.`sit_sedex`,
                        `sedex_mov`.`data_mov`
                      FROM
                        `sedex_mov`
                      WHERE
                        `sedex_mov`.`cod_sedex` = $idsedex
                        AND
                        `sedex_mov`.`data_mov` <= '$data_mov_atual'
                        AND
                        `sedex_mov`.`idmovsedex` != $id_mov_atual
                      ORDER BY
                        `sedex_mov`.`data_mov` DESC, `sedex_mov`.`idmovsedex` DESC
                      LIMIT 1";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $q_mov_ant = $model->query( $q_mov_ant );

        // fechando a conexao
        $model->closeConnection();

        if( !$q_mov_ant ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']  = 'err';
            $msg['text']  = 'Falha na consulta de movimentação anterior ( EXCLUSÃO DE MOVIEMNTAÇÃO DE SEDEX ).';
            $msg['linha'] = __LINE__;
            get_msg( $msg, 1 );

            echo msg_js( '', 1 );
            exit;

        }

        $cont_mov_ant = $q_mov_ant->num_rows;

        if( $cont_mov_ant < 1 ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']  = 'err';
            $msg['text']  = 'A consulta de movimentação anterior retornou 0 ocorrências ( EXCLUSÃO DE MOVIEMNTAÇÃO DE SEDEX ).';
            $msg['linha'] = __LINE__;
            get_msg( $msg, 1 );

            echo msg_js( '', 1 );
            exit;

        }

        $d_mov_ant = $q_mov_ant->fetch_assoc();

        $sit_sedex_ant = $d_mov_ant['sit_sedex'];

        $query_sedex = "DELETE FROM `sedex_mov` WHERE `idmovsedex` = $idmovsedex LIMIT 1";

        $q_up_sedex = "UPDATE
                         `sedex`
                       SET
                         `sit_sedex` = $sit_sedex_ant,
                         `user_up` = $user,
                         `data_up` = NOW(),
                         `ip_up` = $ip
                       WHERE
                         `idsedex` = $idsedex
                       LIMIT 1";

        $success = TRUE;
        $erromysql = '';

        // instanciando o model
        $model = SicopModel::getInstance();

        // iniciando a transaction
        $model->transaction();

        // executando a query
        $query_sedex = $model->query( $query_sedex );

        if( !$query_sedex ) {

            // pegar a mensagem de erro mysql
            $msg_err_mysql = $model->getErrorMsg();

            $erromysql .= "\n\n[ ERRO MYSQL - EXCLUSÃO DE MOVIEMNTAÇÃO DE SEDEX - EXCLUSÃO DA MOVIMENTAÇÃO ]\n";
            $erromysql .= $msg_err_mysql;
            $success = FALSE;

        }

        // executando a query
        $q_up_sedex = $model->query( $q_up_sedex );

        if( !$q_up_sedex ) {

            // pegar a mensagem de erro mysql
            $msg_err_mysql = $model->getErrorMsg();

            $erromysql .= "\n\n[ ERRO MYSQL - EXCLUSÃO DE MOVIEMNTAÇÃO DE SEDEX - ATUALIZAÇÃO DO SEDEX ]\n";
            $erromysql .= $msg_err_mysql;
            $success = FALSE;

        }

        if ( $success ) {

            // cofimando as alterações
            $model->commit();

            // montar a mensagem q será salva no log
            $msg = array( );
            $msg['tipo'] = 'desc';
            $msg['entre_ch'] = 'EXCLUSÃO DE MOVIEMNTAÇÃO DE SEDEX';
            $msg['text'] = "Exclusão de movimentação de sedex. \n\n $sedex";

            get_msg( $msg, 1 );

            echo msg_js( '', 1 );

        } else {

            // em caso de falha, cancela as alterações
            $model->rollback();

            // montar a mensagem q será salva no log
            $msg = array( );
            $msg['tipo'] = 'err';
            $msg['text'] = "Erro de exclusão de movimentação de sedex.\n\n $sedex $erromysql.";
            $msg['linha'] = __LINE__;

            get_msg( $msg, 1 );

            echo msg_js( 'FALHA!', 1 );

        }

        // fechando a conexao
        $model->closeConnection();

        exit;

    }

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE DO CODIGO RESPONSAVEL PELA EXCLUSÃO
 * -------------------------------------------------------------------
 */
} else if ( $proced == 3 ) { //CADASTRAMENTO
/*
 * -------------------------------------------------------------------
 * PARTE DO CODIGO RESPONSAVEL PELO CADASTRAMENTO
 * -------------------------------------------------------------------
 */

    $iddet = empty( $iddet ) ? '' : (int)$iddet;

    if ( empty( $iddet ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = 'Identificador d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . ' em branco. Operação cancelada ( PECÚLIO ).';
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) )
            $ret = 1;
        echo msg_js( 'FALHA!', $ret );

        exit;

    }

    // pegar os dados do preso
    $detento = dados_det( $iddet );

    $idvisit = empty( $idvisit ) ? '' : (int)$idvisit;

    $visita = '*** VISITANTE NÃO CONSTA NO ROL ***';
    if ( !empty( $idvisit ) ) {
        // pegar os dados do visitante
        $visita = dados_visit( $idvisit );
    }

    // instanciando o model
    $model = SicopModel::getInstance();

    $sit_sedex  = empty( $sit_sedex ) ? '' : (int)$sit_sedex;
    $motivo_dev = empty( $motivo_dev ) ? 'NULL' : (int)$motivo_dev;
    $data_sedex = empty( $data_sedex ) ? 'NULL' : "'" . $model->escape_string( $data_sedex ) . "'";
    $cod_sedex  = empty( $cod_sedex ) ? 'NULL' : "'" . tratastring( $cod_sedex )  ."'";
    $cod_sedex  = preg_replace('/[ ]{1,}/','',$cod_sedex);

    $cod_sedex_leng  = mb_strlen ( $cod_sedex );

    if ( $cod_sedex_leng < 15 ){

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = 'Código do SEDEX inválido. Operação cancelada ( CADASTRAMENTO DE SEDEX ).';
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'Código de rastreamento inválido! Verifique!!', 1 );

        exit;

    }

    // validar novamente o $idvisit para, caso esteja em branco,
    // preencha com null no banco, validação para a consulta de inserção
    $idvisit = empty( $idvisit ) ? 'NULL' : (int)$idvisit;

    $query_sedex = "INSERT INTO
                      `sedex`
                      (
                        `cod_detento`,
                        `cod_visita`,
                        `sit_sedex`,
                        `cod_motivo_dev`,
                        `data_sedex`,
                        `cod_sedex`,
                        `user_add`,
                        `data_add`,
                        `ip_add`
                      )
                    VALUES
                      (
                        $iddet,
                        $idvisit,
                        $sit_sedex,
                        $motivo_dev,
                        STR_TO_DATE( $data_sedex, '%d/%m/%Y' ),
                        $cod_sedex,
                        $user,
                        NOW(),
                        $ip
                      )";

    $success       = TRUE;
    $erromysql     = '';
    $err_num_mysql = 0;
    $lastid        = '';

    // iniciando a transaction
    $model->transaction();

    // executando a query
    $query_sedex = $model->query( $query_sedex );

    if( !$query_sedex ) {

        // pegar a mensagem de erro mysql
        $msg_err_mysql = $model->getErrorMsg();

        //$err_num_mysql = $model->getErrorNum();
        $err_num_mysql = $model->getErrorNum();

        $erromysql .= "\n\n[ ERRO MYSQL - CADASTRAMENTO DE SEDEX ]\n";
        $erromysql .= $msg_err_mysql;
        $success = FALSE;

    }

    if ( $success ) {

        $lastid = $model->lastInsertId();

        $q_mov_sedex = "INSERT INTO `sedex_mov` (`cod_sedex`, `sit_sedex`, `data_mov`) VALUES ( $lastid, $sit_sedex, NOW() )";

        // executando a query
        $q_mov_sedex = $model->query( $q_mov_sedex );

        if ( !$q_mov_sedex ) {

            // pegar a mensagem de erro mysql
            $msg_err_mysql = $model->getErrorMsg();

            $erromysql .= "\n\n[ ERRO MYSQL - CADASTRAMENTO DE SEDEX - CADASTRAMENTO DA MOVIMENTAÇÃO ]\n";
            $erromysql .= $msg_err_mysql;
            $success = FALSE;

        }

    }

    if ( $success ) {

        // cofimando as alterações
        $model->commit();

        // pegar os dados do sedex somente
        $sedex = dados_sedex_only( $lastid );

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'desc';
        $msg['entre_ch'] = 'CADASTRAMENTO DE SEDEX';
        $msg['text'] = "Cadastramento de sedex. \n\n $detento \n\n $visita \n\n $sedex";

        get_msg( $msg, 1 );

        redir( 'buscadet', 'proced=cadsed' );

    } else {

        // em caso de falha, cancela as alterações
        $model->rollback();

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Erro de cadastramento de sedex.\n\n $valor_user $erromysql.";
        $msg['linha'] = __LINE__;

        get_msg( $msg, 1 );

        $msg_saida = 'FALHA!';
        if ( $err_num_mysql == 1062 ) $msg_saida = 'Este SEDEX já está cadastrado! Verifique!';
        echo msg_js( $msg_saida, 1 );

    }

    // fechando a conexao
    $model->closeConnection();

    exit;

} else { //SE NÃO HOUVER NUMERO DE PROCEDIMENTO OU ELE NÃO FOR UM INTEIRO

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo'] = 'err';
    $msg['text'] = 'Número de procedimento em branco ou inválido. Operação cancelada ( SEDEX ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    $ret = 2;
    if ( !empty( $targ ) ) $ret = 'f';
    echo msg_js( 'FALHA!', $ret );

    exit;

}
?>
</body>
</html>
