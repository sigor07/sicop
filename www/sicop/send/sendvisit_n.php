<?php
if ( !isset( $_SESSION) ) session_start();

require '../init/config.php';
require 'incl_ajax.php';

$pag  = link_pag();
$tipo = '';
$mensagem = '';

/* colocar o tipo de página, o setor que ela acessa
 * ex: PECÚLIO, CADASTRO, INCLUSÃO...
 */
$tipo_pag = 'VISITAS';

/*
 * define a variavel $n_acesso de acordo com o setor
 */

$n_acesso = get_session( 'n_rol', 'int' );
if ( empty( $n_acesso ) or $n_acesso < 3 ) {

    // montar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->set_msg_type( SM_TYPE_ATEN );
    $msg->set_msg_pre_def( SM_NO_PERM );
    $msg->add_parenteses( $tipo_pag );
    $msg->get_msg();

    echo 0;

    exit;

}

$is_post = is_post();

if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->set_msg_type( SM_TYPE_ATEN );
    $msg->set_msg_pre_def( SM_DIRECT_ACCESS );
    $msg->add_parenteses( $tipo_pag );
    $msg->get_msg();

    echo 0;

    exit;

}

$targ   = get_post( 'targ', 'int' );
$proced = get_post( 'proced', 'int' ); // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO

$ret = 2;
if ( !empty( $targ ) ) $ret = 'f';

if ( empty( $proced ) or $proced > 3 ) {

    // montar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->set_msg_type( SM_TYPE_ERR );
    $msg->add_quebras( 1 );
    $msg->set_msg_pre_def( SM_INVALID_PROCED );
    $msg->add_parenteses( $tipo_pag );
    $msg->get_msg();

    echo 0;

    exit;

}

$user = get_session( 'user_id', 'int' );
$ip   = "'" . $_SERVER['REMOTE_ADDR'] . "'";

if ( $proced == 1 ) { // ATUALIZAÇÃO
/*
 * -------------------------------------------------------------------
 * PARTE RESPONSAVEL PELA ATUALIZAÇÃO
 * -------------------------------------------------------------------
 */

    /*
     * aqui coloca a função da página
     */
    $proced_tipo_pag = 'ATUALIZAÇÃO - ' . $tipo_pag;

    $idvisit = get_post( 'idvisit', 'int' );

    if ( empty( $idvisit ) ) {

        // montar a mensagem q será salva no log
        $msg = sysmsg::create_msg();
        $msg->set_msg_type( SM_TYPE_ERR );
        $msg->add_quebras( 1 );
        $msg->set_msg( 'Identificador do visitante em branco. Operação cancelada' );
        $msg->add_parenteses( $proced_tipo_pag );
        $msg->get_msg();

        echo 0;

        exit;

    }

    // pegar os dados do visitante
    $visita = dados_visit( $idvisit );

    $nome_visit = get_post( 'nome_visit', 'string' );
    if ( empty( $nome_visit ) ) {

        // montar a mensagem q será salva no log
        $msg = sysmsg::create_msg();
        $msg->set_msg_type( SM_TYPE_ERR );
        $msg->set_msg( 'Nome do visitante em branco. Operação cancelada' );
        $msg->add_parenteses( $proced_tipo_pag );
        $msg->add_quebras( 2 );
        $msg->set_msg( $visita );
        $msg->get_msg();

        echo 0;
        exit;

    }

    $nome_visit =  "'" . $nome_visit . "'";

    $rg_visit   = get_post( 'rg_visit', 'string' );
    $rg_visit   = empty( $rg_visit ) ? 'NULL' : "'" . $rg_visit . "'";

    $sexo_visit = get_post( 'sexo_visit', 'string' );
    if ( empty( $sexo_visit ) ) {

        // montar a mensagem q será salva no log
        $msg = sysmsg::create_msg();
        $msg->set_msg_type( SM_TYPE_ERR );
        $msg->add_quebras( 1 );
        $msg->set_msg( 'Sexo do visitante em branco. Operação cancelada' );
        $msg->add_parenteses( $proced_tipo_pag );
        $msg->add_quebras( 2 );
        $msg->set_msg( $visita );
        $msg->get_msg();

        echo 0;
        exit;

    }

    $sexo_visit     = "'" . $sexo_visit . "'";

    $pai_visit      = get_post( 'pai_visit', 'string' );
    $pai_visit      = empty( $pai_visit ) ? 'NULL' : "'" . $pai_visit . "'";

    $mae_visit      = get_post( 'mae_visit', 'string' );
    $mae_visit      = empty( $mae_visit ) ? 'NULL' : "'" . $mae_visit . "'";

    $cidade         = get_post( 'cidade', 'int' );
    $cidade         = empty( $cidade ) ? 'NULL' : "'" . $cidade . "'";

    $nasc_visit     = get_post( 'nasc_visit', 'string' );
    $nasc_visit     = empty( $nasc_visit ) ? 'NULL' : "'" . $nasc_visit . "'";

    $telefone_visit = get_post( 'telefone_visit', 'string' );
    $telefone_visit = empty( $telefone_visit ) ? 'NULL' : "'" . (int)preg_replace( '/[()-\s]/', '', $telefone_visit ) . "'";

    $resid_visit    = get_post( 'resid_visit', 'string' );
    $resid_visit    = empty( $resid_visit ) ? 'NULL' : "'" . $resid_visit . "'";

    $defeito_fisico = get_post( 'defeito_fisico', 'string' );
    $defeito_fisico = empty( $defeito_fisico ) ? 'NULL' : "'" . $defeito_fisico . "'";

    $sinal_nasc     = get_post( 'sinal_nasc', 'string' );
    $sinal_nasc     = empty( $sinal_nasc ) ? 'NULL' : "'" . $sinal_nasc . "'";

    $cicatrizes     = get_post( 'cicatrizes', 'string' );
    $cicatrizes     = empty( $cicatrizes ) ? 'NULL' : "'" . $cicatrizes . "'";

    $tatuagens      = get_post( 'tatuagens', 'string' );
    $tatuagens      = empty( $tatuagens ) ? 'NULL' : "'" . $tatuagens . "'";

    $doc_rg         = get_post( 'doc_rg', 'int' );
    $doc_rg         = empty( $doc_rg ) ? 'NULL' : "'" . $doc_rg . "'";

    $doc_foto34     = get_post( 'doc_foto34', 'int' );
    $doc_foto34     = empty( $doc_foto34 ) ? 'NULL' : "'" . $doc_foto34 . "'";

    $doc_resid      = get_post( 'doc_resid', 'int' );
    $doc_resid      = empty( $doc_resid ) ? 'NULL' : "'" . $doc_resid . "'";

    $doc_ant        = get_post( 'doc_ant', 'int' );
    $doc_ant        = empty( $doc_ant ) ? 'NULL' : "'" . $doc_ant . "'";

    $doc_cert       = get_post( 'doc_cert', 'int' );
    $doc_cert       = empty( $doc_cert ) ? 'NULL' : "'" . $doc_cert . "'";

    $query = "UPDATE
                  `visitas`
                SET
                  `nome_visit` = $nome_visit,
                  `rg_visit` = $rg_visit,
                  `sexo_visit` = $sexo_visit,
                  `pai_visit` = $pai_visit,
                  `mae_visit` = $mae_visit,
                  `cod_cidade_v` = $cidade,
                  `nasc_visit` = STR_TO_DATE( $nasc_visit, '%d/%m/%Y' ),
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

    $db    = SicopModel::getInstance();
    $query = $db->query( $query );

    $success = TRUE;
    if ( !$query ) {

        $success = FALSE;

        // pegar a mensagem de erro mysql
        $msg_err_mysql = $db->getErrorMsg();
        $db->closeConnection();

        // pegar os valores inseridos no fomulário
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = sysmsg::create_msg();
        $msg->set_msg_type( SM_TYPE_ERR );
        $msg->add_quebras( 1 );
        $msg->set_msg( 'Erro de atualização ' );
        $msg->add_parenteses( $tipo_pag );
        $msg->add_quebras( 2 );
        $msg->set_msg( $visita );
        $msg->add_quebras( 2 );
        $msg->set_msg( $valor_user );
        $msg->add_quebras( 2 );
        $msg->set_msg( $msg_err_mysql );
        $msg->get_msg();

        echo 0;

        exit;

    }

    // montar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->add_chaves( 'ATUALIZAÇÃO DE VISITANTE', 0, 1 );
    $msg->set_msg( "Atualização de visitante." );
    $msg->add_quebras( 2 );
    $msg->set_msg( $visita );
    $msg->get_msg();

    echo 1;

    exit;

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

    /*
     * aqui coloca a função da página
     */
    $proced_tipo_pag = 'EXCLUSÃO - ' . $tipo_pag;

    if ( empty( $n_acesso ) or $n_acesso < 4 ) {

        // montar a mensagem q será salva no log
        $msg = sysmsg::create_msg();
        $msg->set_msg_type( SM_TYPE_ATEN );
        $msg->set_msg_pre_def( SM_NO_PERM );
        $msg->add_parenteses( $proced_tipo_pag );
        $msg->get_msg();

        echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

        exit;

    }

    $idvisit = get_post( 'idvisit', 'int' );

    if ( empty( $idvisit ) ) {

        // montar a mensagem q será salva no log
        $msg = sysmsg::create_msg();
        $msg->set_msg_type( SM_TYPE_ERR );
        $msg->add_quebras( 1 );
        $msg->set_msg( 'Identificador do visitante em branco. Operação cancelada' );
        $msg->add_parenteses( $proced_tipo_pag );
        $msg->get_msg();

        echo 0;
        exit;

    }

    // pegar os dados do preso
    $det_where = " IN( SELECT visitas_detentos.cod_detento FROM visitas_detentos WHERE visitas_detentos.cod_visita = $idvisit )";
    $detentos = dados_det_wl( $det_where );

    // pegar os dados do visitante
    $visita = dados_visit( $idvisit );

    // pegar as fotos do visitante
    $query_f_v = "SELECT `foto_visit_g`, `foto_visit_p` FROM `visita_fotos` WHERE `cod_visita` = $idvisit";

    $query = "DELETE FROM `visitas` WHERE `idvisita` = $idvisit LIMIT 1";
    $success = TRUE;

    $db = SicopModel::getInstance();

    $query_f_v = $db->query( $query_f_v );

    $query = $db->query( $query );

    $success = TRUE;
    if ( !$query ) {

        $success = FALSE;

        // pegar a mensagem de erro mysql
        $msg_err_mysql = $db->getErrorMsg();
        $db->closeConnection();

        // pegar os valores inseridos no fomulário
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = sysmsg::create_msg();
        $msg->set_msg_type( SM_TYPE_ERR );
        $msg->add_quebras( 1 );
        $msg->set_msg( 'Erro de exclusão' );
        $msg->add_parenteses( $tipo_pag );
        $msg->add_quebras( 2 );
        $msg->set_msg( $detentos );
        $msg->add_quebras( 2 );
        $msg->set_msg( $valor_user );
        $msg->add_quebras( 2 );
        $msg->set_msg( $msg_err_mysql );
        $msg->get_msg();

        echo 0;

        exit;

    }

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

    // montar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->add_chaves( 'EXCLUSÃO DE VISITANTE', 0, 1 );
    $msg->set_msg( "Exclusão de visitante." );
    $msg->add_quebras( 2 );
    $msg->set_msg( $visita );
    $msg->add_quebras( 2 );
    $msg->set_msg( $detentos );
    $msg->get_msg();

    echo 1;

    exit;

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

    /*
     * aqui coloca a função da página
     */
    $proced_tipo_pag = 'CADASTRAMENTO - ' . $tipo_pag;

    $nome_visit = get_post( 'nome_visit', 'string' );
    if ( empty( $nome_visit ) ) {

        // montar a mensagem q será salva no log
        $msg = sysmsg::create_msg();
        $msg->set_msg_type( SM_TYPE_ERR );
        $msg->add_quebras( 1 );
        $msg->set_msg( 'Nome do visitante em branco. Operação cancelada' );
        $msg->add_parenteses( $proced_tipo_pag );
        $msg->get_msg();

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    //$nome_visit =  "'" . $nome_visit . "'";

    $rg_visit       = get_post( 'rg_visit', 'string' );

    $sexo_visit = get_post( 'sexo_visit', 'string' );
    if ( empty( $sexo_visit ) ) {

        // montar a mensagem q será salva no log
        $msg = sysmsg::create_msg();
        $msg->set_msg_type( SM_TYPE_ERR );
        $msg->add_quebras( 1 );
        $msg->set_msg( 'Sexo do visitante em branco. Operação cancelada' );
        $msg->add_parenteses( $proced_tipo_pag );
        $msg->get_msg();

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $pai_visit      = get_post( 'pai_visit', 'string' );
    $mae_visit      = get_post( 'mae_visit', 'string' );
    $cidade         = get_post( 'cidade', 'int' );
    $nasc_visit     = get_post( 'nasc_visit', 'string' );

    $telefone_visit = get_post( 'telefone_visit', 'string' );
    if ( !empty ( $telefone_visit ) ) {
        $telefone_visit = (int)preg_replace( '/[()-\s]/', '', $telefone_visit );
    }

    $resid_visit    = get_post( 'resid_visit', 'string' );
    $resid_visit    = empty( $resid_visit ) ? 'NULL' : "'" . $resid_visit . "'";

    $defeito_fisico = get_post( 'defeito_fisico', 'string' );
    $defeito_fisico = empty( $defeito_fisico ) ? 'NULL' : "'" . $defeito_fisico . "'";

    $sinal_nasc     = get_post( 'sinal_nasc', 'string' );
    $cicatrizes     = get_post( 'cicatrizes', 'string' );
    $tatuagens      = get_post( 'tatuagens', 'string' );
    $doc_rg         = get_post( 'doc_rg', 'int' );
    $doc_foto34     = get_post( 'doc_foto34', 'int' );
    $doc_resid      = get_post( 'doc_resid', 'int' );
    $doc_ant        = get_post( 'doc_ant', 'int' );
    $doc_cert       = get_post( 'doc_cert', 'int' );

    $query = "INSERT
                  `visitas`
                     (
                      `nome_visit`,
                      `rg_visit`,
                      `sexo_visit`,
                      `nasc_visit`,
                      `cod_cidade_v`,
                      `pai_visit`,
                      `mae_visit`,
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
                      '$nome_visit',
                      '$rg_visit',
                      '$sexo_visit',
                      STR_TO_DATE( '$nasc_visit', '%d/%m/%Y' ),
                      '$cidade',
                      '$pai_visit',
                      '$mae_visit',
                      '$resid_visit',
                      '$telefone_visit',
                      '$defeito_fisico',
                      '$sinal_nasc',
                      '$cicatrizes',
                      '$tatuagens',
                      '$doc_rg',
                      '$doc_foto34',
                      '$doc_resid',
                      '$doc_ant',
                      '$doc_cert',
                      '$user',
                      NOW(),
                      '$ip'
                     )";

    $db    = SicopModel::getInstance();
    $query = $db->query( $query );

    $success = TRUE;
    if ( !$query ) {

        $success = FALSE;

        // pegar a mensagem de erro mysql
        $msg_err_mysql = $db->getErrorMsg();
        $db->closeConnection();

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = sysmsg::create_msg();
        $msg->set_msg_type( SM_TYPE_ERR );
        $msg->add_quebras( 1 );
        $msg->set_msg( 'Erro de cadastramento' );
        $msg->add_parenteses( $tipo_pag );
        $msg->add_quebras( 2 );
        $msg->set_msg( $valor_user );
        $msg->add_quebras( 2 );
        $msg->set_msg( $msg_err_mysql );
        $msg->get_msg();

        echo msg_js( 'FALHA!', $ret );

        exit;

    }

    $lastid = $db->lastInsertId();

    // pegar os dados do visitante
    $visita = dados_visit( $lastid );

    // montar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->add_chaves( 'CADASTRAMENTO DE VISITANTE', 0, 1 );
    $msg->set_msg( "Cadastramento de visitante." );
    $msg->add_quebras( 2 );
    $msg->set_msg( $visita );
    $msg->get_msg();

    echo msg_js( '', $ret );

    exit;

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELO CADASTRAMENTO
 * -------------------------------------------------------------------
 */
}
?>
