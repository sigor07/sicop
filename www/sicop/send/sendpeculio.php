<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag = link_pag();
$tipo = '';
$mensagem = '';

$n_peculio       = get_session( 'n_peculio', 'int' );
$n_incl          = get_session( 'n_incl', 'int' );
$n_peculio_baixa = get_session( 'n_peculio_baixa', 'int' );

if ( $n_peculio < 3 and $n_incl < 3 ) {

    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso à página de manipulação de pertences SEM PERMISSÕES.';
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$is_post = is_post();

if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'atn';
    $msg['text'] = 'Tentativa de acesso direto à página de manipulação de PDA.';
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}


extract( $_POST, EXTR_OVERWRITE );

$targ     = empty( $targ ) ? 0 : 1;
$noreload = empty( $noreload ) ? 0 : 1;
$proced   = empty( $proced ) ? '' : (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO

if ( empty( $proced ) or $proced > 3 ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = 'Número de procedimento em branco ou inválido. Operação cancelada ( PECÚLIO ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    $ret = 2;
    if ( !empty( $targ ) ) $ret = 'f';
    echo msg_js( 'FALHA!', $ret );

    exit;

}

$user = get_session( 'user_id', 'int' );
$ip = "'" . $_SERVER['REMOTE_ADDR'] . "'";

if ( $proced == 1 ) { // ATUALIZAÇÃO
/*
 * -------------------------------------------------------------------
 * PARTE RESPONSAVEL PELA ATUALIZAÇÃO
 * -------------------------------------------------------------------
 */

    $idpec = empty( $idpec ) ? '' : (int)$idpec;

    if ( empty( $idpec ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Identificador do pertence em branco. Operação cancelada ( ATUALIZAÇÃO DE PECÚLIO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) )
            $ret = 'f';
        echo msg_js( 'FALHA!', $ret );

        exit;
    }

    // pegar os dados do pertence
    $peculio = dados_pec( $idpec );

    // pegar os dados do preso
    $det_where = "( SELECT `cod_detento` FROM `peculio` WHERE `idpeculio` = $idpec LIMIT 1 )";
    $detento = dados_det( $det_where );

    $tipo_peculio = (int)$tipo_peculio;
    if ( empty( $tipo_peculio ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Tipo do pertence em branco. Operação cancelada ( ATUALIZAÇÃO DE PECÚLIO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 'f';
        echo msg_js( 'FALHA!', $ret );

        exit;
    }

    $descr_peculio = empty( $descr_peculio ) ? '' : tratastring( $descr_peculio );
    if ( empty( $descr_peculio ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Descrição do pertence em branco. Operação cancelada ( ATUALIZAÇÃO DE PECÚLIO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 'f';
        echo msg_js( 'FALHA!', $ret );

        exit;
    }

    $descr_peculio = "'" . $descr_peculio . "'";

    $sql_retirado = '';
    if ( $n_peculio_baixa >= 1 ) {
        $retirado = empty( $retirado ) ? 0 : (int)$retirado;
        $obs_ret = empty( $obs_ret ) ? 'NULL' : "'" . tratastring( $obs_ret ) . "'";
        $sql_retirado = "`retirado` = $retirado, `obs_ret` = $obs_ret,";
    }

    $q_peculio = "UPDATE
                    `peculio`
                  SET
                    `cod_tipo_peculio` = $tipo_peculio,
                    `descr_peculio` = $descr_peculio,
                    $sql_retirado
                    `user_up` = $user,
                    `data_up` = NOW(),
                    `ip_up` = $ip
                  WHERE
                    `idpeculio` = $idpec
                  LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_peculio = $model->query( $q_peculio );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if ( $q_peculio ) {

        $msg = array( );
        $msg['tipo'] = 'desc';
        $msg['entre_ch'] = 'ATUALIZAÇÃO DE PECÚLIO';
        $msg['text'] = "Atualização de pertence/pecúlio. \n\n $peculio \n\n $detento \n";

        $mensagem = get_msg( $msg );

    } else {

        $success = FALSE;

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Erro de atualização de pertence/pecúlio.\n\n $peculio \n\n $detento \n\n $valor_user.";
        $msg['linha'] = __LINE__;

        $mensagem = get_msg( $msg );
    }


    salvaLog( $mensagem );

    $saida = msg_js( '', 2 );

    if ( !empty( $targ ) ) {

        $saida = "<script type='text/javascript'> window.opener.location.reload(); self.window.close();</script>";

    }

    if ( !$success ) {


        $ret = 2;
        if ( !empty( $targ ) ) $ret = 'f';

        $saida = msg_js( 'FALHA!!!', $ret );

    }

    echo $saida;

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

    if ( empty( $n_peculio ) or $n_peculio < 4 ) {

        $tipo = 0;
        include '../init/msgnopag.php';

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'atn';
        $msg['text']  = 'Tentativa de acesso à página de manipulação de pertences SEM PERMISSÕES ( EXCLUSÃO DE PERTENCE ).';
        get_msg( $msg, 1 );

        echo msg_js( '', 1 );
        exit;

    }

    $idpec = empty( $idpec ) ? '' : (int)$idpec;

    if ( empty( $idpec ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Identificador do pertence em branco. Operação cancelada ( EXCLUSÃO DE PECÚLIO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;
    }

    // pegar os dados do pertence
    $peculio = dados_pec( $idpec );

    // pegar os dados do preso
    $det_where = "( SELECT `cod_detento` FROM `peculio` WHERE `idpeculio` = $idpec LIMIT 1 )";
    $detento = dados_det( $det_where );

    $q_peculio = "DELETE FROM `peculio` WHERE `idpeculio` = $idpec LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_peculio = $model->query( $q_peculio );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if ( $q_peculio ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'desc';
        $msg['entre_ch'] = 'EXCLUSÃO DE PECÚLIO';
        $msg['text'] = "Exclusão de pertence/pecúlio. \n\n $peculio \n\n $detento \n";

        $mensagem = get_msg( $msg );
    } else {

        $success = FALSE;


        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Erro de exclusão de pertence/pecúlio.\n\n $peculio \n\n $detento.";
        $msg['linha'] = __LINE__;

        $mensagem = get_msg( $msg );
    }

    salvaLog( $mensagem );

    $saida = msg_js( '', 1 );

    if ( !$success ) {

        $saida = msg_js( 'FALHA!!!', 1 );
    }

    echo $saida;

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

    $iddet = empty( $iddet ) ? '' : (int)$iddet;

    if ( empty( $iddet ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = 'Identificador d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . ' em branco. Operação cancelada ( CADASTRAMENTO DE PECÚLIO ).';
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

    $tipo_peculio = (int)$tipo_peculio;
    if ( empty( $tipo_peculio ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Tipo do pertence em branco. Operação cancelada ( CADASTRAMENTO DE PECÚLIO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 'f';
        echo msg_js( 'FALHA!', $ret );

        exit;
    }

    $descr_peculio = empty( $descr_peculio ) ? '' : tratastring( $descr_peculio );
    if ( empty( $descr_peculio ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Descrição do pertence em branco. Operação cancelada ( CADASTRAMENTO DE PECÚLIO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 'f';
        echo msg_js( 'FALHA!', $ret );

        exit;
    }

    $descr_peculio = "'" . $descr_peculio . "'";

    $q_peculio = "INSERT INTO
                    `peculio`
                    (
                      `cod_detento`,
                      `cod_tipo_peculio`,
                      `descr_peculio`,
                      `user_add`,
                      `data_add`,
                      `ip_add`
                    )
                  VALUES
                    (
                      $iddet,
                      $tipo_peculio,
                      $descr_peculio,
                      $user,
                      NOW(),
                      $ip
                    )";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_peculio = $model->query( $q_peculio );

    $success = TRUE;
    if ( $q_peculio ) {

        $lastid = $model->lastInsertId();

        // pegar os dados do pertence
        $peculio = dados_pec( $lastid );

        $msg = array( );
        $msg['tipo']     = 'desc';
        $msg['entre_ch'] = 'CADASTRAMENTO DE PECÚLIO';
        $msg['text']     = "Cadastramento de pertence/pecúlio. \n\n $peculio \n\n $detento \n";

        $mensagem = get_msg( $msg );

    } else {

        $success = FALSE;

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Erro de cadastramento de pertence/pecúlio. \n\n $detento \n\n $valor_user.";
        $msg['linha'] = __LINE__;

        $mensagem = get_msg( $msg );

    }


    // fechando a conexao
    $model->closeConnection();

    salvaLog( $mensagem );

    if ( !$success ) {

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 1;

        echo msg_js( 'FALHA!!!', $ret );
        exit;

    }

    if ( isset( $cadadd ) ) {

        echo msg_js( '', 1 );
        exit;

    }

    $ret = 2;
    if ( !empty( $targ ) ) {

        $ret = 'rf';
        if ( $noreload == 1 ) {
            $ret = 'f';
        }

    }

    echo msg_js( '', $ret );

    exit;

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE DO CODIGO RESPONSAVEL PELO CADASTRAMENTO
 * -------------------------------------------------------------------
 */
} else { //SE NÃO HOUVER NUMERO DE PROCEDIMENTO OU ELE NÃO FOR UM INTEIRO
    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo'] = 'err';
    $msg['text'] = 'Número de procedimento em branco ou inválido. Operação cancelada ( PECÚLIO ).';
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
