<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';
$mensagem = '';

$n_sind = get_session( 'n_sind', 'int' );

if ( empty( $n_sind ) or $n_sind < 3 ) {

    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso à página de manipulação de PDA SEM PERMISSÕES.';
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$is_post = is_post();

if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso direto à página de manipulação de PDA.';
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}


extract( $_POST, EXTR_OVERWRITE );

$targ   = empty( $targ ) ? 0 : 1;
$proced = empty( $proced ) ? '' : (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO; 4 =  VINCULAÇÃO

if ( empty( $proced ) or $proced > 4 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Número de procedimento em branco ou inválido. Operação cancelada ( PDA ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 2 );
    exit;

}

$user = get_session( 'user_id', 'int' );
$ip   = "'" . $_SERVER['REMOTE_ADDR'] . "'";

if ( $proced == 1 ){ // ATUALIZAÇÃO
/*
 * -----------------------------------------------------------
 * PARTE RESPONSAVEL PELA ATUALIZAÇÃO
 * -----------------------------------------------------------
 */

    $id_pda = empty( $id_pda ) ? '' : (int)$id_pda;
    if ( empty( $id_pda ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = 'Identificador do PDA em branco. Operação cancelada ( ATUALIZAÇÃO DE PDA ).';
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    // pegar os dados do PDA
    $pda = dados_pda( $id_pda );


    // pegar os dados do preso
    $det_where = "( SELECT `cod_detento` FROM `sindicancias` WHERE `idsind` = $id_pda LIMIT 1 )";
    $detento = dados_det( $det_where );

    if ( empty( $detento ) ) {
        $detento = "[ " . SICOP_DET_DESC_L . " ]\n AUTORIA DESCONHECIDA";
    }

    $num_pda = empty( $num_pda ) ? '' : (int)$num_pda;
    if ( empty( $num_pda ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Número do PDA em branco. Operação cancelada ( ATUALIZAÇÃO DE PDA ).\n\n $pda \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $ano_pda = empty( $ano_pda ) ? '' : (int)$ano_pda;
    if ( empty( $num_pda ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Ano do PDA em branco. Operação cancelada ( ATUALIZAÇÃO DE PDA ).\n\n $pda \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $local_pda = empty( $local_pda ) ? 'NULL' : "'".tratastring($local_pda)."'";

    $data_ocorrencia = empty( $data_ocorrencia ) ? '' : $data_ocorrencia;
    if ( empty( $data_ocorrencia ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Data da ocorrência do PDA em branco. Operação cancelada ( ATUALIZAÇÃO DE PDA ).\n\n $pda \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    // verificar se a data é válida
    if ( !validaData( $data_ocorrencia, 'DD/MM/AAAA' ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Data da ocorrência do PDA inválida. Operação cancelada ( ATUALIZAÇÃO DE PDA ).\n\n $pda \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    // instanciando o model
    $model = SicopModel::getInstance();

    $data_ocorrencia  = "'" . $model->escape_string( $data_ocorrencia ) . "'";

    $sit_pda       = empty( $sit_pda ) ? '' : (int)$sit_pda;
    if ( empty( $sit_pda ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Situação do PDA em branco. Operação cancelada ( ATUALIZAÇÃO DE PDA ).\n\n $pda \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $situacaodet = empty( $situacaodet ) ? '' : (int)$situacaodet;
    $data_reabilit = empty( $data_reabilit ) ? '' : $data_reabilit;
    if ( $sit_pda == 2 ) {

        if ( empty( $situacaodet ) ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']  = 'err';
            $msg['text']  = "Situação d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " no PDA em branco. Operação cancelada ( ATUALIZAÇÃO DE PDA ).\n\n $pda \n\n $detento";
            $msg['linha'] = __LINE__;
            get_msg( $msg, 1 );

            echo msg_js( 'FALHA!', 2 );
            exit;

        }

        if ( $situacaodet > 2 ) {

            if ( empty( $data_reabilit ) ) {

                // montar a mensagem q será salva no log
                $msg = array();
                $msg['tipo']  = 'err';
                $msg['text']  = "Data da reabilitação em branco. Operação cancelada ( ATUALIZAÇÃO DE PDA ).\n\n $pda \n\n $detento";
                $msg['linha'] = __LINE__;
                get_msg( $msg, 1 );

                echo msg_js( 'FALHA!', 2 );
                exit;

            }

            // verificar se a data é válida
            if ( !validaData( $data_reabilit, 'DD/MM/AAAA' ) ) {

                // montar a mensagem q será salva no log
                $msg = array();
                $msg['tipo']  = 'err';
                $msg['text']  = "Data da reabilitação inválida. Operação cancelada ( ATUALIZAÇÃO DE PDA ).\n\n $pda \n\n $detento";
                $msg['linha'] = __LINE__;
                get_msg( $msg, 1 );

                echo msg_js( 'FALHA!', 2 );
                exit;

            }

            $data_reabilit  = "'" . $model->escape_string( $data_reabilit ) . "'";

        } else {

            $data_reabilit = 'NULL';

        }

    } else {

        $situacaodet   = 'NULL';
        $data_reabilit = 'NULL';

    }

    $descr_pda = empty( $descr_pda ) ? 'NULL' : "'" . tratastring( $descr_pda ) . "'";


    $query_pda = "UPDATE
                    `sindicancias`
                  SET
                    `num_pda` = $num_pda,
                    `ano_pda` = $ano_pda,
                    `local_pda` = $local_pda,
                    `data_ocorrencia` = STR_TO_DATE( $data_ocorrencia, '%d/%m/%Y' ),
                    `sit_pda` = $sit_pda,
                    `cod_sit_detento` = $situacaodet,
                    `data_reabilit` = STR_TO_DATE( $data_reabilit, '%d/%m/%Y' ),
                    `descr_pda` = $descr_pda,
                    `user_up` = $user,
                    `data_up` = NOW(),
                    `ip_up` = $ip
                  WHERE
                    `idsind` = $id_pda
                  LIMIT 1";

    // executando a query
    $query_pda = $model->query( $query_pda );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if ( !$query_pda ) {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de cadastramento de PDA.\n\n $pda \n\n $detento \n\n $valor_user.";
        $msg['linha'] = __LINE__;

        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!!!', 2 );

    }

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'ATUALIZAÇÃO DE PDA';
    $msg['text']     = "Atualização de PDA. \n\n $pda \n\n $detento \n";

    get_msg( $msg, 1 );

    echo msg_js( '', 2 );

    exit;

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELA ATUALIZAÇÃO
 * -------------------------------------------------------------------
 */
} else if ( $proced == 2 ){ //EXCLUSÃO
/*
 * -------------------------------------------------------------------
 * PARTE RESPONSAVEL PELA EXCLUSÃO
 * -------------------------------------------------------------------
 */

    $n_sind = get_session( 'n_sind', 'int' );

    if ( empty( $n_sind ) or $n_sind < 4 ) {
        $tipo = 0;
        include '../init/msgnopag.php';

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'atn';
        $msg['text']  = 'Tentativa de acesso à página de manipulação de PDA SEM PERMISSÕES ( EXCLUSÃO DE PDA ).';

        get_msg( $msg, 1 );
        exit;

    }

    $id_pda = empty( $id_pda ) ? '' : (int)$id_pda;
    if ( empty( $id_pda ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = 'Identificador do PDA em branco. Operação cancelada ( EXCLUSÃO DE PDA ).';
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    // pegar os dados do PDA
    $pda = dados_pda( $id_pda );

    // pegar os dados do preso
    $det_where = "( SELECT `cod_detento` FROM `sindicancias` WHERE `idsind` = $id_pda LIMIT 1 )";
    $detento = dados_det( $det_where );

    $query_pda = "DELETE FROM `sindicancias` WHERE `idsind` = $id_pda LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_pda = $model->query( $query_pda );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if ( !$query_pda ) {

        $success = FALSE;

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de exclusão de PDA.\n\n $pda \n\n $detento.";
        $msg['linha'] = __LINE__;

        get_msg( $msg, 1 );

        echo msg_js( "FALHA!!!", 1 );

        exit;

    }

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'EXCLUSÃO DE PDA';
    $msg['text']     = "Exclusão de PDA. \n\n $pda \n\n $detento \n";

    get_msg( $msg, 1 );

    $saida = '<script type="text/javascript">location.href="../buscadet.php?proced=bsind";</script>';

    echo $saida;

    exit;

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELA EXCLUSÃO
 * -------------------------------------------------------------------
 */
} else if ( $proced == 3 ){ //CADASTRAMENTO
/*
 * -------------------------------------------------------------------
 * PARTE RESPONSAVEL PELO CADASTRAMENTO
 * -------------------------------------------------------------------
 */

    $iddet = empty( $iddet ) ? '' : (int)$iddet;

    $detento = '[ ' . SICOP_DET_DESC_U . " ]\n AUTORIA DESCONHECIDA";
    if ( !empty( $iddet ) ) {
        // pegar os dados do preso
        $detento = dados_det( $iddet );
    }

    if ( empty( $iddet ) ) {
        $iddet = 'NULL';
    }

    $num_pda = empty( $num_pda ) ? '' : (int)$num_pda;
    if ( empty( $num_pda ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Número do PDA em branco. Operação cancelada ( CADASTRAMENTO DE PDA ).\n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $ano_pda = empty( $ano_pda ) ? '' : (int)$ano_pda;
    if ( empty( $num_pda ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Ano do PDA em branco. Operação cancelada ( CADASTRAMENTO DE PDA ).\n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $local_pda       = empty( $local_pda ) ? 'NULL' : "'".tratastring($local_pda)."'";

    $data_ocorrencia = empty( $data_ocorrencia ) ? '' : $data_ocorrencia;
    if ( empty( $data_ocorrencia ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Data da ocorrência do PDA em branco. Operação cancelada ( CADASTRAMENTO DE PDA ).\n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    // verificar se a data é válida
    if ( !validaData( $data_ocorrencia, 'DD/MM/AAAA' ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Data da ocorrência do PDA inválida. Operação cancelada ( CADASTRAMENTO DE PDA ).\n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    // instanciando o model
    $model = SicopModel::getInstance();

    $data_ocorrencia  = "'" . $model->escape_string( $data_ocorrencia ) . "'";

    $sit_pda = empty( $sit_pda ) ? '' : (int)$sit_pda;
    if ( empty( $sit_pda ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Situação do PDA em branco. Operação cancelada ( CADASTRAMENTO DE PDA ).\n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $situacaodet = empty( $situacaodet ) ? '' : (int)$situacaodet;
    $data_reabilit = empty( $data_reabilit ) ? '' : $data_reabilit;
    if ( $sit_pda == 2 ) {

        if ( empty( $situacaodet ) ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']  = 'err';
            $msg['text']  = "Situação d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " no PDA em branco. Operação cancelada ( CADASTRAMENTO DE PDA ).\n\n $detento";
            $msg['linha'] = __LINE__;
            get_msg( $msg, 1 );

            echo msg_js( 'FALHA!', 2 );
            exit;

        }

        if ( $situacaodet > 2 ) {

            if ( empty( $data_reabilit ) ) {

                // montar a mensagem q será salva no log
                $msg = array();
                $msg['tipo']  = 'err';
                $msg['text']  = "Data da reabilitação em branco. Operação cancelada ( CADASTRAMENTO DE PDA ).\n\n $detento";
                $msg['linha'] = __LINE__;
                get_msg( $msg, 1 );

                echo msg_js( 'FALHA!', 2 );
                exit;

            }

            // verificar se a data é válida
            if ( !validaData( $data_reabilit, 'DD/MM/AAAA' ) ) {

                // montar a mensagem q será salva no log
                $msg = array();
                $msg['tipo']  = 'err';
                $msg['text']  = "Data da reabilitação inválida. Operação cancelada ( CADASTRAMENTO DE PDA ).\n\n $detento";
                $msg['linha'] = __LINE__;
                get_msg( $msg, 1 );

                echo msg_js( 'FALHA!', 2 );
                exit;

            }

            $data_reabilit  = "'" . $model->escape_string( $data_reabilit ) . "'";

        } else {

            $data_reabilit = 'NULL';

        }

    } else {

        $situacaodet   = 'NULL';
        $data_reabilit = 'NULL';

    }

    $descr_pda = empty( $descr_pda ) ? 'NULL' : "'" . tratastring( $descr_pda ) . "'";

    $query_pda = "INSERT INTO
                    `sindicancias`
                      (
                        `cod_detento`,
                        `num_pda`,
                        `ano_pda`,
                        `local_pda`,
                        `data_ocorrencia`,
                        `sit_pda`,
                        `cod_sit_detento`,
                        `data_reabilit`,
                        `descr_pda`,
                        `user_add`,
                        `data_add`,
                        `ip_add`
                      )
                  VALUES
                    (
                      $iddet,
                      $num_pda,
                      $ano_pda,
                      $local_pda,
                      STR_TO_DATE( $data_ocorrencia, '%d/%m/%Y' ),
                      $sit_pda,
                      $situacaodet,
                      STR_TO_DATE( $data_reabilit, '%d/%m/%Y' ),
                      $descr_pda,
                      $user,
                      NOW(),
                      $ip
                    )";

    // executando a query
    $query_pda = $model->query( $query_pda );

    $success = TRUE;
    if( !$query_pda ) {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de cadastramento de PDA.\n\n $detento \n\n $valor_user.";
        $msg['linha'] = __LINE__;

        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!!!', 2 );

        exit;

    }

    $lastid = $model->lastInsertId();

    // fechando a conexao
    $model->closeConnection();

    // pegar os dados do PDA
    $pda = dados_pda( $lastid );

    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'CADASTRO DE PDA';
    $msg['text']     = "Cadastro de PDA. \n\n $pda \n\n $detento \n";

    get_msg( $msg, 1 );

    echo msg_js( '', 2 );

    exit;

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELO CADASTRAMENTO
 * -------------------------------------------------------------------
 */
} else if ( $proced == 4 ){ //VINCULAÇÃO
/*
 * -------------------------------------------------------------------
 * PARTE RESPONSAVEL PELO CADASTRAMENTO
 * -------------------------------------------------------------------
 */

    $id_pda = empty( $id_pda ) ? '' : (int)$id_pda;
    if ( empty( $id_pda ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = 'Identificador do PDA em branco. Operação cancelada ( VINCULAÇÃO DE PDA ).';
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    // pegar os dados do PDA
    $pda = dados_pda( $id_pda );

    $iddet = empty( $iddet ) ? '' : (int)$iddet;
    if ( empty( $iddet ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = 'Identificador d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . ' em branco. Operação cancelada ( VINCULAÇÃO DE PDA ).';
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    // pegar os dados do preso
    $detento = dados_det( $iddet );

    $query_pda = "UPDATE
                    `sindicancias`
                  SET
                    `cod_detento` = $iddet,
                    `user_up` = $user,
                    `data_up` = NOW(),
                    `ip_up` = $ip
                  WHERE
                    `idsind` = $id_pda
                  LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_pda = $model->query( $query_pda );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if ( !$query_pda ) {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de vinculação de PDA a " . SICOP_DET_DESC_L . ".\n\n $pda \n\n $detento \n\n $valor_user.";
        $msg['linha'] = __LINE__;

        $mensagem = get_msg( $msg );

        echo msg_js( 'FALHA!!!' );

        exit;

    }

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'VINCULAÇÃO DE PDA';
    $msg['text']     = "Vinculação de PDA. \n\n $pda \n\n $detento \n";

    get_msg( $msg, 1 );

    $saida = "<script type='text/javascript'>location.href='../sind/detalpda.php?idsind=$id_pda'; </script>";

    echo $saida;

    exit;

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELO CADASTRAMENTO
 * -------------------------------------------------------------------
 */
} else {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Número de procedimento em branco ou inválido. Operação cancelada ( PDA ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 2 );
    exit;

}

?>
</body>
</html>