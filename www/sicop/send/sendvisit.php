<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag         = link_pag();
$mensagem    = '';
$query_visit = '';
$visita      = '';
$tipo        = '';

$n_rol = get_session( 'n_rol', 'int' );

if ( empty( $n_rol ) or $n_rol < 3 ) {

    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso à página de manipulação de visitantes SEM PERMISSÕES.';
    get_msg( $msg, 1 );

    exit;

}

$is_post = is_post();
if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso direto à página de manipulação de visitantes.';
    get_msg( $msg, 1 );

    redir( 'home' );
    exit;

}

extract( $_POST, EXTR_OVERWRITE );

$proced = empty($proced) ? '' : (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO

if ( empty( $proced ) or $proced > 4 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Número de procedimento em branco ou inválido. Operação cancelada ( VISITANTES ).';
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

    $idvisit = empty( $idvisit ) ? '' : (int)$idvisit;
    if ( empty( $idvisit ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = 'Identificador do visitante em branco. Operação cancelada ( ATUALIZAÇÃO DE VISITANTES ).';
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    // pegar os dados do visitante
    $visita = dados_visit( $idvisit );

    // pegar os dados do preso
    $det_where = "( SELECT `cod_detento` FROM `visitas` WHERE `idvisita` = $idvisit LIMIT 1 )";
    $detento = dados_det( $det_where );

    $nome_visit = empty( $nome_visit ) ? '' : tratastring( $nome_visit );
    if ( empty( $nome_visit ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Nome do visitante em branco. Operação cancelada ( ATUALIZAÇÃO DE VISITANTES ).\n\n $visita \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }
    $nome_visit =  "'" . $nome_visit . "'";


    // instanciando o model
    $model = SicopModel::getInstance();

    $rg_visit = empty( $rg_visit ) ? 'NULL' : "'" . $model->escape_string( mb_strtoupper( $rg_visit ) ) . "'";

    $sexo_visit = empty( $sexo_visit ) ? '' : tratastring( $sexo_visit );
    if ( empty( $sexo_visit ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Sexo do visitante em branco. Operação cancelada ( ATUALIZAÇÃO DE VISITANTES ).\n\n $visita \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }
    $sexo_visit =  "'" . $sexo_visit . "'";

    $pai_visit  = empty( $pai_visit ) ? 'NULL' : "'" . tratastring( $pai_visit ) . "'";
    $mae_visit  = empty( $mae_visit ) ? 'NULL' : "'" . tratastring( $mae_visit ) . "'";
    $cidade     = empty( $cidade ) ? 'NULL' : "'" . (int)$cidade . "'";
    $nasc_visit = empty( $nasc_visit ) ? 'NULL' : "'" . $model->escape_string( $nasc_visit ) . "'";

    $idparentesco   = empty( $idparentesco ) ? '' : (int)$idparentesco;
    if ( empty( $idparentesco ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador do parentesco do visitante em branco. Operação cancelada ( ATUALIZAÇÃO DE VISITANTES ).\n\n $visita \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $telefone_visit = empty( $telefone_visit ) ? 'NULL' : "'" . (int)preg_replace( '/[()-\s]/', '', $telefone_visit ) . "'";
    $resid_visit    = empty( $resid_visit ) ? 'NULL' : "'" . tratastring( $resid_visit ) . "'";
    $defeito_fisico = empty( $defeito_fisico ) ? 'NULL' : "'" . tratastring( $defeito_fisico, 'U', false ) . "'";
    $sinal_nasc     = empty( $sinal_nasc ) ? 'NULL' : "'" . tratastring( $sinal_nasc, 'U', false ) . "'";
    $cicatrizes     = empty( $cicatrizes ) ? 'NULL' : "'" . tratastring( $cicatrizes, 'U', false ) . "'";
    $tatuagens      = empty( $tatuagens ) ? 'NULL' : "'" . tratastring( $tatuagens, 'U', false ) . "'";
    $doc_rg         = (int)$doc_rg;
    $doc_foto34     = (int)$doc_foto34;
    $doc_resid      = (int)$doc_resid;
    $doc_ant        = (int)$doc_ant;
    $doc_cert       = (int)$doc_cert;

    $query_visit = "UPDATE
                      `visitas`
                    SET
                      `nome_visit` = $nome_visit,
                      `rg_visit` = $rg_visit,
                      `sexo_visit` = $sexo_visit,
                      `pai_visit` = $pai_visit,
                      `mae_visit` = $mae_visit,
                      `cod_cidade_v` = $cidade,
                      `nasc_visit` = STR_TO_DATE( $nasc_visit, '%d/%m/%Y' ),
                      `cod_parentesco` = $idparentesco,
                      `resid_visit` = $resid_visit,
                      `telefone_visit` = $telefone_visit,
                      `defeito_fisico` = $defeito_fisico,
                      `sinal_nasc` = $sinal_nasc,
                      `cicatrizes` = $cicatrizes,
                      `tatuagens` = $tatuagens,
                      `doc_rg` = $doc_rg,
                      `doc_foto34` = $doc_foto34,
                      `doc_resid` = $doc_resid,
                      `doc_ant` = $doc_ant,
                      `doc_cert` = $doc_cert,
                      `user_up` = $user,
                      `data_up` = NOW(),
                      `ip_up` = $ip
                    WHERE
                      `idvisita` = $idvisit
                    LIMIT 1";

    // executando a query
    $query_visit = $model->query( $query_visit );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if( $query_visit ) {


        $msg = array();
        $msg['tipo']     = 'desc';
        $msg['entre_ch'] = 'ATUALIZAÇÃO DE VISITANTE';
        $msg['text']     = "Atualização de dados de visitante. \n\n $visita \n\n $detento";

        $mensagem = get_msg( $msg );

    } else {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de atualizaçõa de visitante.\n\n $visita \n\n $detento \n\n $valor_user.";
        $msg['linha'] = __LINE__;

        $mensagem = get_msg( $msg );

    }

    salvaLog( $mensagem );

    $msg = '';
    if ( !$success ) $msg = 'FALHA!!!';

    echo msg_js( $msg, 2 );

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

    if ( empty( $n_rol ) or $n_rol < 4 ) {

        $tipo = 0;
        include '../init/msgnopag.php';

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'atn';
        $msg['text']  = 'Tentativa de acesso à página de manipulação de observação de rol de visitas SEM PERMISSÕES ( EXCLUSÃO DE VISITA ).';
        get_msg( $msg, 1 );

        exit;

    }

    $idvisit = empty( $idvisit ) ? '' : (int)$idvisit;

    if ( empty( $idvisit ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = 'Identificador do visitante em branco. Operação cancelada ( EXCLUSÃO DE VISITANTES ).';
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    // pegar os dados do preso
    $det_where = "( SELECT `cod_detento` FROM `visitas` WHERE `idvisita` = $idvisit LIMIT 1 )";
    $detento = dados_det( $det_where );

    // pegar os dados do visitante
    $visita = dados_visit( $idvisit );


    // pegar as fotos do visitante
    $query_f_v = "SELECT `foto_visit_g`, `foto_visit_p` FROM `visita_fotos` WHERE `cod_visita` = $idvisit";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_f_v = $model->query( $query_f_v );

    // fechando a conexao
    $model->closeConnection();


    $query_visit = "DELETE FROM `visitas` WHERE `idvisita` = $idvisit LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_visit = $model->query( $query_visit );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if( $query_visit ) {

        $pasta = SICOP_VISIT_FOLDER;

        // só executa o while se realmente excluiu o visitante
        while ( $d_foto_v = $query_f_v->fetch_assoc() ) {

            if ( !empty( $d_foto_v['foto_visit_g'] ) ) {
                if ( file_exists( $pasta . $d_foto_v['foto_visit_g'] ) ) {
                    unlink( $pasta . $d_foto_v['foto_visit_g'] );
                }
            }

            if ( !empty( $d_foto_v['foto_visit_p'] ) ) {
                if ( file_exists( $pasta . $d_foto_v['foto_visit_p'] ) ) {
                    unlink( $pasta . $d_foto_v['foto_visit_p'] );
                }
            }

        }


        $msg = array();
        $msg['tipo']     = 'desc';
        $msg['entre_ch'] = 'EXCLUSÃO DE VISITANTE';
        $msg['text']     = "Exclusão de visitante. \n\n $visita \n\n $detento";

        $mensagem = get_msg( $msg );

    } else {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de exclusão de visitante.\n\n $visita \n\n $detento \n\n $valor_user.";
        $msg['linha'] = __LINE__;

        $mensagem = get_msg( $msg );

    }

    salvaLog( $mensagem );

    $msg = '';
    if ( !$success ) $msg = 'FALHA!!!';

    echo msg_js( $msg, 1 );

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
    if ( empty( $iddet ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = 'Identificador d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . ' em branco. Operação cancelada ( CADASTRAMENTO DE VISITANTE ).';
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    // pegar os dados do preso
    $detento = dados_det( $iddet );

    $nome_visit = empty( $nome_visit ) ? '' : tratastring( $nome_visit );
    if ( empty( $nome_visit ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Nome do visitante em branco. Operação cancelada ( CADSTRAMENTO DE VISITANTES ). \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }
    $nome_visit =  "'" . $nome_visit . "'";

    $rg_visit = empty( $rg_visit ) ? 'NULL' : "'" . $rg_visit . "'";

    $sexo_visit = empty( $sexo_visit ) ? '' : tratastring( $sexo_visit );
    if ( empty( $sexo_visit ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Sexo do visitante em branco. Operação cancelada ( CADSTRAMENTO DE VISITANTES ). \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }
    $sexo_visit =  "'" . $sexo_visit . "'";

    $pai_visit      = empty( $pai_visit ) ? 'NULL' : "'" . tratastring( $pai_visit ) . "'";
    $mae_visit      = empty( $mae_visit ) ? 'NULL' : "'" . tratastring( $mae_visit ) . "'";
    $cidade         = empty( $cidade ) ? 'NULL' : "'" . (int)$cidade . "'";
    $nasc_visit     = empty( $nasc_visit ) ? 'NULL' : "'" . $nasc_visit . "'";

    $idparentesco = empty( $idparentesco ) ? '' : (int)$idparentesco;
    if ( empty( $idparentesco ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador do parentesco do visitante em branco. Operação cancelada ( CADSTRAMENTO DE VISITANTES ). \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $telefone_visit = empty( $telefone_visit ) ? 'NULL' : "'" . (int)preg_replace( '/[()-\s]/', '', $telefone_visit ) . "'";
    $resid_visit    = empty( $resid_visit ) ? 'NULL' : "'" . tratastring( $resid_visit ) . "'";
    $defeito_fisico = empty( $defeito_fisico ) ? 'NULL' : "'" . tratastring( $defeito_fisico, 'U', false ) . "'";
    $sinal_nasc     = empty( $sinal_nasc ) ? 'NULL' : "'" . tratastring( $sinal_nasc, 'U', false ) . "'";
    $cicatrizes     = empty( $cicatrizes ) ? 'NULL' : "'" . tratastring( $cicatrizes, 'U', false ) . "'";
    $tatuagens      = empty( $tatuagens ) ? 'NULL' : "'" . tratastring( $tatuagens, 'U', false ) . "'";
    $doc_rg         = (int)$doc_rg;
    $doc_foto34     = (int)$doc_foto34;
    $doc_resid      = (int)$doc_resid;
    $doc_ant        = (int)$doc_ant;
    $doc_cert       = (int)$doc_cert;



    $query_visit = "INSERT INTO
                      `visitas`
                         (
                          `cod_detento`,
                          `num_in`,
                          `nome_visit`,
                          `rg_visit`,
                          `sexo_visit`,
                          `nasc_visit`,
                          `cod_cidade_v`,
                          `pai_visit`,
                          `mae_visit`,
                          `cod_parentesco`,
                          `resid_visit`,
                          `telefone_visit`,
                          `defeito_fisico`,
                          `sinal_nasc`,
                          `cicatrizes`,
                          `tatuagens`,
                          `doc_rg`,
                          `doc_foto34`,
                          `doc_resid`,
                          `doc_ant`,
                          `doc_cert`,
                          `user_add`,
                          `data_add`,
                          `ip_add`
                         )
                    VALUES
                         (
                          $iddet,
                          ( SELECT `n_p_trans` FROM `detentos` WHERE `iddetento` = $iddet LIMIT 1 ),
                          $nome_visit,
                          $rg_visit,
                          $sexo_visit,
                          STR_TO_DATE( $nasc_visit, '%d/%m/%Y' ),
                          $cidade,
                          $pai_visit,
                          $mae_visit,
                          $idparentesco,
                          $resid_visit,
                          $telefone_visit,
                          $defeito_fisico,
                          $sinal_nasc,
                          $cicatrizes,
                          $tatuagens,
                          $doc_rg,
                          $doc_foto34,
                          $doc_resid,
                          $doc_ant,
                          $doc_cert,
                          $user,
                          NOW(),
                          $ip
                         )";
    $l_id_vis = '';

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_visit = $model->query( $query_visit );

    $success = TRUE;
    if( $query_visit ) {

        $l_id_vis = $model->lastInsertId();

        // pegar os dados do visitante
        $visita = dados_visit( $l_id_vis );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']     = 'desc';
        $msg['entre_ch'] = 'CADASTRAMENTO DE VISITANTE';
        $msg['text']     = "Cadastramento de visitante. \n\n $visita \n\n $detento";

        $mensagem = get_msg( $msg );

    } else {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de cadastramento de visitante.\n\n $visita \n\n $detento \n\n $valor_user.";
        $msg['linha'] = __LINE__;

        $mensagem = get_msg( $msg );

    }

    // fechando a conexao
    $model->closeConnection();

    salvaLog( $mensagem );

    if ( $success ) {

        $_SESSION['l_id_vis'] = $l_id_vis;
        header( 'Location: ../visita/cadvisitok.php' );

    } else {

        echo msg_js( 'FALHA!!!', 1 );

    }

    exit;

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELO CADASTRAMENTO
 * -------------------------------------------------------------------
 */
} else if ( $proced == 4 ){ //REATIVAÇÃO
/*
 * -------------------------------------------------------------------
 * PARTE RESPONSAVEL PELA REATIVAÇÃO
 * -------------------------------------------------------------------
 */

    $iddet = empty( $iddet ) ? '' : (int)$iddet;
    if ( empty( $iddet ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = 'Identificador d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . ' em branco. Operação cancelada ( REATIVAÇÃO DE VISITANTE ).';
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    // pegar os dados do preso
    $detento = dados_det( $iddet );

    $idvisit = empty( $idvisit ) ? '' : (int)$idvisit;
    if ( empty( $idvisit ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = 'Identificador do visitante em branco. Operação cancelada ( REATIVAÇÃO DE VISITANTE ).';
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    // pegar os dados do visitante
    $visita = dados_visit( $idvisit );

    $query_visit = "UPDATE
                      `visitas`
                    SET
                      `num_in` = ( SELECT `n_p_trans` FROM `detentos` WHERE `iddetento` = $iddet LIMIT 1 ),
                      `user_up` = $user,
                      `data_up` = NOW(),
                      `ip_up` = $ip
                    WHERE
                      `idvisita` = $idvisit
                    LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_visit = $model->query( $query_visit );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if( $query_visit ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']     = 'desc';
        $msg['entre_ch'] = 'REATIVAÇÃO DE VISITANTE';
        $msg['text']     = "Reativação de visitante. \n\n $visita \n\n $detento";

        $mensagem = get_msg( $msg );

    } else {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de cadastramento de visitante.\n\n $visita \n\n $detento \n\n $valor_user.";
        $msg['linha'] = __LINE__;

        $mensagem = get_msg( $msg );

    }

    salvaLog( $mensagem );

    $msg = '';
    if ( !$success ) $msg = 'FALHA!!!';

    echo msg_js( $msg, 2 );

    exit;


/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELA REATIVAÇÃO
 * -------------------------------------------------------------------
 */
}
?>
</body>
</html>



