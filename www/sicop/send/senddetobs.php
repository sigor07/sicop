<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_ajax.php';

$pag       = link_pag();
$msg_falha = '<p class="q_error">FALHA!</p>';
$mensagem  = '';

/*
 * colocar o tipo de página, o setor que ela acessa
 * ex: PECÚLIO, CADASTRO, INCLUSÃO...
 */
$tipo_pag = 'OBSERVAÇÃO DE ' . SICOP_DET_DESC_U;

$n_det_obs = get_session( 'n_det_obs', 'int' );

if ( empty( $n_det_obs ) or $n_det_obs < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'perm';
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

$proced = empty( $proced ) ? '' : (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO

if ( empty( $proced ) or $proced > 3 ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "Número de procedimento em branco ou inválido. Operação cancelada ( $tipo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo $msg_falha;

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

    /*
     * aqui coloca a função da página
     */
    $proced_tipo_pag = 'ATUALIZAÇÃO - ' . $tipo_pag;

    $id_obs_det = empty( $id_obs_det ) ? '' : (int)$id_obs_det;
    if ( empty( $id_obs_det ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo'] = 'err';
        $msg['text'] = "Identificador da observação em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;
        exit;

    }

    // pegar os dados do preso
    $det_where = "( SELECT `cod_detento` FROM `obs_det` WHERE `id_obs_det` = $id_obs_det LIMIT 1 )";
    $detento = dados_det( $det_where );

    // pegar os dados da observação
    $obs_s = dados_obs( 'det', $id_obs_det );

    $obs_det  = empty( $obs_det ) ? '' : tratastring( $obs_det, 'U', FALSE );

    if ( empty( $obs_det ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Observação em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;
        exit;

    }

    $obs_det = "'" . $obs_det . "'";

    $query = "UPDATE
                `obs_det`
              SET
                `obs_det` = $obs_det,
                `user_up` = $user,
                `data_up` = NOW(),
                `ip_up` = $ip
              WHERE
                `id_obs_det` = $id_obs_det
              LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query = $model->query( $query );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if ( !$query ) {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de atualização ( $tipo_pag ). \n\n $detento \n\n $obs_s \n\n $valor_user.";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo '0';
        exit;

    }

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'ATUALIZAÇÃO DE OBSERVAÇÃO DE ' . SICOP_DET_DESC_U;
    $msg['text']     = "Atualização de observação de " . SICOP_DET_DESC_L . ". \n\n $detento \n\n $obs_s";
    get_msg( $msg, 1 );

    echo '1';

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

    /*
     * aqui coloca a função da página
     */
    $proced_tipo_pag = 'EXCLUSÃO - ' . $tipo_pag;

    $n_chefia = get_session( 'n_chefia', 'int' );

    if ( empty( $n_chefia ) or $n_chefia < 4 ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'perm';
        $msg['entre_ch'] = $proced_tipo_pag;
        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;

    }

    $id_obs_det = empty( $id_obs_det ) ? '' : (int)$id_obs_det;
    if ( empty( $id_obs_det ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo'] = 'err';
        $msg['text'] = "Identificador da observação em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;
        exit;

    }

    // pegar os dados do preso
    $det_where = "( SELECT `cod_detento` FROM `obs_det` WHERE `id_obs_det` = $id_obs_det LIMIT 1 )";
    $detento = dados_det( $det_where );

    // pegar os dados da observação
    $obs_s = dados_obs( 'det', $id_obs_det );

    $query = "DELETE FROM `obs_det` WHERE `id_obs_det` = $id_obs_det LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query = $model->query( $query );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if ( !$query ) {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de exclusão ( $tipo_pag ). \n\n $detento \n\n $obs_s \n\n $valor_user.";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo '0';
        exit;

    }

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'EXCLUSÃO DE OBSERVAÇÃO DE ' . SICOP_DET_DESC_U;
    $msg['text']     = "Exclusão de observação de " . SICOP_DET_DESC_L . ". \n\n $detento \n\n $obs_s";
    get_msg( $msg, 1 );

    echo '1';

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

    /*
     * aqui coloca a função da página
     */
    $proced_tipo_pag = 'CADASTRAMENTO - ' . $tipo_pag;

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

    // pegar os dados do preso
    $detento = dados_det( $iddet );

    $obs_det  = empty( $obs_det ) ? '' : tratastring( $obs_det, 'U', FALSE );
    if ( empty( $obs_det ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Observação em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;
        exit;

    }

    $obs_det = "'" . $obs_det . "'";

    $query = "INSERT INTO
                `obs_det`
                (
                  `cod_detento`,
                  `obs_det`,
                  `user_add`,
                  `data_add`,
                  `ip_add`
                )
              VALUES
                (
                  $iddet,
                  $obs_det,
                  $user,
                  NOW(),
                  $ip
                )";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query = $model->query( $query );

    $success = TRUE;
    if ( !$query ) {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de cadastramento ( $tipo_pag ). \n\n $detento \n\n $valor_user.";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo '0';
        exit;

    }

    $lastid = $model->lastInsertId();

    // fechando a conexao
    $model->closeConnection();

    // pegar os dados da observação
    $obs_s = dados_obs( 'det', $lastid );

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'CADASTRAMENTO DE OBSERVAÇÃO DE ' . SICOP_DET_DESC_U;
    $msg['text']     = "Cadastro de observação de " . SICOP_DET_DESC_L . ". \n\n $detento \n\n $obs_s";
    get_msg( $msg, 1 );

    echo '1';

    exit;

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELO CADASTRAMENTO
 * -------------------------------------------------------------------
 */
}
?>
</body>
</html>