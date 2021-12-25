<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';
$mensagem = '';

/* colocar o tipo de página, o setor que ela acessa
 * ex: PECÚLIO, CADASTRO, INCLUSÃO...
 */
$tipo_pag = 'TV';

/*
 * define a variavel $n_acesso de acordo com o setor
 */

$n_acesso = get_session( 'n_incl', 'int' );
if ( empty( $n_acesso ) or $n_acesso < 3 ) {

    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $tipo_pag;
    get_msg( $msg, 1 );

    exit;

}

$is_post = is_post();

if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( $tipo_pag ).";
    get_msg( $msg, 1 );

    redir( 'home' );

    exit;

}

extract( $_POST, EXTR_OVERWRITE );

$targ   = empty($targ) ? 0 : 1;
$proced = empty( $proced ) ? '' : (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO; 4 = VINCULAÇÃO A OUTRO DETENTO

if ( empty( $proced ) or $proced > 4 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Número de procedimento em branco ou inválido. Operação cancelada ( $tipo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    $ret = 1;
    if ( !empty( $targ ) ) $ret = 'f';
    echo msg_js( 'FALHA!', $ret );

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

    $idtv = empty( $idtv ) ? '' : (int)$idtv;
    if ( empty( $idtv ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador da tv em branco. Operação cancelada ( $proced_tipo_pag )";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    // pegar os dados da tv
    $tv = dados_tv( $idtv, 1 );

    // pegar os dados do preso
    $where_det = "( SELECT `cod_detento` FROM `detentos_tv` WHERE `idtv` = $idtv LIMIT 1 )";
    $detento = dados_det( $where_det );

    $n_cela = empty( $n_cela ) ? '' : (int)$n_cela;
    if ( empty( $n_cela ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador da cela da tv em branco. Operação cancelada ( $proced_tipo_pag ).\n\n $tv \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $marca_tv  = empty( $marca_tv ) ? '' : tratastring( $marca_tv, 'U', false );
    if ( empty( $marca_tv ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Marca da tv em branco. Operação cancelada ( $proced_tipo_pag ).\n\n $tv \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }
    $marca_tv =  "'" . $marca_tv . "'";

    $cor_tv = empty( $cor_tv ) ? '' : tratastring( $cor_tv, 'U', false );
    if ( empty( $cor_tv ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Cor da tv em branco. Operação cancelada ( $proced_tipo_pag ).\n\n $tv \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }
    $cor_tv =  "'" . $cor_tv . "'";

    $polegadas = empty( $polegadas ) ? '' : (int)$polegadas;
    if ( empty( $polegadas ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Número de polegadas da tv em branco. Operação cancelada ( $proced_tipo_pag ).\n\n $tv \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $lacre_1 = empty( $lacre_1 ) ? '' : (int)$lacre_1;
    if ( empty( $lacre_1 ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Número do lacre 1 da tv em branco. Operação cancelada ( $proced_tipo_pag ).\n\n $tv \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $lacre_2 = empty( $lacre_2 ) ? '' : (int)$lacre_2;
    if ( empty( $lacre_2 ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Número do lacre 2 da tv em branco. Operação cancelada ( $proced_tipo_pag ).\n\n $tv \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $query = "UPDATE
                `detentos_tv`
              SET
                `cod_cela` = $n_cela,
                `marca_tv` = $marca_tv,
                `cor_tv` = $cor_tv,
                `polegadas` = $polegadas,
                `lacre_1` = $lacre_1,
                `lacre_2` = $lacre_2,
                `user_up` = $user,
                `data_up` = NOW(),
                `ip_up` = $ip
              WHERE
                `idtv` = $idtv
              LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query = $model->query( $query );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if ( $query ) {

        $msg = array();
        $msg['tipo']     = 'desc';
        $msg['entre_ch'] = 'ATUALIZAÇÃO DE TV';
        $msg['text']     = "Atualização de tv. \n\n $tv \n\n $detento ";

        $mensagem = get_msg( $msg );

    } else {

        $success = FALSE;

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de atualização ( $tipo_pag ). \n\n $tv \n\n $detento \n\n $valor_user.";
        $msg['linha'] = __LINE__;

        $mensagem = get_msg( $msg );
    }

    salvaLog( $mensagem );

    $msg_saida = '';
    if ( !$success ) $msg_saida = 'FALHA!!!';

    $ret = 2;
    if ( !empty( $targ ) ) $ret = 'f';

    echo msg_js( $msg_saida, $ret );

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

        $tipo = 0;
        include '../init/msgnopag.php';

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']     = 'perm';
        $msg['entre_ch'] = $proced_tipo_pag;
        get_msg( $msg, 1 );

        exit;

    }

    $idtv = empty( $idtv ) ? '' : (int)$idtv;
    if ( empty( $idtv ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador da tv em branco. Operação cancelada ( $proced_tipo_pag )";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    // pegar os dados da tv
    $tv = dados_tv( $idtv, 1 );

    // pegar os dados do preso
    $where_det = "( SELECT `cod_detento` FROM `detentos_tv` WHERE `idtv` = $idtv LIMIT 1 )";
    $detento = dados_det( $where_det );

    $query = "DELETE FROM `detentos_tv` WHERE `idtv` = $idtv LIMIT 1";


    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query = $model->query( $query );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if ( $query ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']     = 'desc';
        $msg['entre_ch'] = 'EXCLUSÃO DE TV';
        $msg['text']     = "Exclusão de tv. \n\n $tv \n\n $detento ";

        $mensagem = get_msg( $msg );

    } else {

        $success = FALSE;

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de exclusão ( $tipo_pag ). \n\n $tv \n\n $detento \n\n $valor_user.";
        $msg['linha'] = __LINE__;

        $mensagem = get_msg( $msg );

    }

    salvaLog( $mensagem );

    if ( !$success ) {

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 'f';

        echo msg_js( 'FALHA!!!', $ret );

    } else {

        redir( 'incl/listatv' );

    }

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

    $iddet = empty( $iddet ) ? '' : (int)$iddet;

    if ( empty( $iddet ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 1;
        echo msg_js( 'FALHA!', $ret );

        exit;

    }

    // pegar os dados do preso
    $detento = dados_det( $iddet );

    // pegar o idcela
    $q_idcela = "SELECT `cod_cela` FROM `detentos` WHERE `iddetento` = $iddet LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $idcela = $model->fetchOne( $q_idcela );

    // fechando a conexao
    $model->closeConnection();

    if( !$idcela ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Falha na consulta do id da cela d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " ( $proced_tipo_pag ). \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 1;
        echo msg_js( 'FALHA!', $ret );
        exit;

    }

    if ( empty( $idcela ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador da cela d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!!!', 2 );
        exit;

    }

    $marca_tv  = empty( $marca_tv ) ? '' : tratastring( $marca_tv, 'U', false );
    if ( empty( $marca_tv ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Marca da tv em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }
    $marca_tv =  "'" . $marca_tv . "'";

    $cor_tv = empty( $cor_tv ) ? '' : tratastring( $cor_tv, 'U', false );
    if ( empty( $cor_tv ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Cor da tv em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }
    $cor_tv =  "'" . $cor_tv . "'";

    $polegadas = empty( $polegadas ) ? '' : (int)$polegadas;
    if ( empty( $polegadas ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Número de polegadas da tv em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $lacre_1 = empty( $lacre_1 ) ? '' : (int)$lacre_1;
    if ( empty( $lacre_1 ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Número do lacre 1 da tv em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $lacre_2 = empty( $lacre_2 ) ? '' : (int)$lacre_2;
    if ( empty( $lacre_2 ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Número do lacre 2 da tv em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $query = "INSERT INTO
                `detentos_tv`
                (
                  `cod_detento`,
                  `cod_cela`,
                  `marca_tv`,
                  `cor_tv`,
                  `polegadas`,
                  `lacre_1`,
                  `lacre_2`,
                  `user_add`,
                  `data_add`,
                  `ip_add`)
              VALUES
                (
                  $iddet,
                  $idcela,
                  $marca_tv,
                  $cor_tv,
                  $polegadas,
                  $lacre_1,
                  $lacre_2,
                  $user,
                  NOW(),
                  $ip
                )";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query = $model->query( $query );

    $lastid = '';
    $success = TRUE;
    if ( $query ) {

        $lastid = $model->lastInsertId();

        // pegar os dados da tv
        $tv = dados_tv( $lastid, 1 );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']     = 'desc';
        $msg['entre_ch'] = 'CADASTRAMENTO DE TV';
        $msg['text']     = "Cadastramento de tv. \n\n $tv \n\n $detento ";

        $mensagem = get_msg( $msg );

    } else {

        $success = FALSE;

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de cadastramento ( $tipo_pag ). \n\n $detento \n\n $valor_user.";
        $msg['linha'] = __LINE__;

        $mensagem = get_msg( $msg );

    }

    salvaLog( $mensagem );

    if ( !$success ) {

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 'f';

        echo msg_js( 'FALHA!!!', $ret );

    } else {

        $qs = "idtv=$lastid";
        redir( 'incl/detaltv', $qs );

    }

    exit;

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELO CADASTRAMENTO
 * -------------------------------------------------------------------
 */
 } else if ( $proced == 4 ) { //VINCULAÇÃO
/*
 * -------------------------------------------------------------------
 * PARTE RESPONSAVEL PELA VINCULAÇÃO
 * -------------------------------------------------------------------
 */

    /*
     * aqui coloca a função da página
     */
    $proced_tipo_pag = 'VINCULAÇÃO - ' . $tipo_pag;

    $idtv = empty( $idtv ) ? '' : (int)$idtv;
    if ( empty( $idtv ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador da tv em branco. Operação cancelada ( $proced_tipo_pag )";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    // pegar os dados da tv
    $tv = dados_tv( $idtv, 1 );

    $iddet_new = empty( $iddet_new ) ? '' : (int)$iddet_new;
    if ( empty( $iddet_new ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 1;
        echo msg_js( 'FALHA!', $ret );

        exit;

    }

    // pegar os dados do preso
    $detento_new = dados_det( $iddet_new );

    // pegar o idcela
    $q_idcela = "SELECT `cod_cela` FROM `detentos` WHERE `iddetento` = $iddet_new LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $idcela = $model->fetchOne( $q_idcela );

    // fechando a conexao
    $model->closeConnection();

    if( !$idcela ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Falha na consulta do id da cela d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " ( $proced_tipo_pag ). \n\n $tv \n\n $detento_new";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 1;
        echo msg_js( 'FALHA!', $ret );
        exit;

    }

    if ( empty( $idcela ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador da cela d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $tv \n\n $detento_new";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $iddet_old = empty( $iddet_old ) ? '' : (int)$iddet_old;

    $detento_old = "[ " . SICOP_DET_DESC_U . " ]\n Não havia responsável anterior.";
    if ( !empty( $iddet_old ) ) {

        // pegar os dados do preso
        $detento_old = dados_det( $iddet_old );

    }

    $query = "UPDATE
                `detentos_tv`
              SET
                `cod_detento` = $iddet_new,
                `cod_cela` = $idcela,
                `user_up` = $user,
                `data_up` = NOW(),
                `ip_up` = $ip
              WHERE
                `idtv` = $idtv
              LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query = $model->query( $query );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if ( $query ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']     = 'desc';
        $msg['entre_ch'] = 'ALTERAÇÃO DE RESPONSÁVEL PELA TV';
        $msg['text']     = "Alteração de " . SICOP_DET_DESC_L . " responsável pela tv. \n\n $tv \n\n[ " . SICOP_DET_DESC_U . " ANTERIOR ]\n $detento_old \n\n [ " . SICOP_DET_DESC_U . " ATUALMENTE RESPONSÁVEL ]\n $detento_new";

        $mensagem = get_msg( $msg );

    } else {

        $success = FALSE;

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de cadastramento ( $tipo_pag ).\n\n $tv \n\n[ " . SICOP_DET_DESC_U . " ANTERIOR ]\n $detento_old \n\n [ " . SICOP_DET_DESC_U . " QUE SERIA RESPONSÁVEL ]\n $detento_new \n\n $valor_user.";
        $msg['linha'] = __LINE__;

        $mensagem = get_msg( $msg );

    }

    salvaLog( $mensagem );

    if ( !$success ) {

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 'f';

        echo msg_js( 'FALHA!!!', $ret );

    } else {

        $qs = "idtv=$idtv";
        redir( 'incl/detaltv', $qs );

    }

    exit;


/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELA VINCULAÇÃO
 * -------------------------------------------------------------------
 */
 }
?>
</body>
</html>
