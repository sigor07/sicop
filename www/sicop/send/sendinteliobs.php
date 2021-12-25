<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';
$mensagem = '';

$n_inteli = get_session( 'n_inteli', 'int' );

if ( empty( $n_inteli ) or $n_inteli < 1 ) {
    $tipo = 0;
    include '../init/msgnopag.php';
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso à página de manipulação de observação da inteligência SEM PERMISSÕES. \n\n Página: $pag";
    salvaLog( $mensagem );
    exit;
}

$is_post = is_post();

if ( !$is_post ) {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso direto à página de manipulação de observação da inteligência.<br /><br /> Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( '', 1 );
    exit;
}


extract( $_POST, EXTR_OVERWRITE );

$targ   = empty($targ) ? 0 : 1;
$proced = empty($proced) ? '' : (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO

if ( empty( $proced ) or $proced > 3 ) {

    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Número de procedimento em branco ou inválido. Operação cancelada ( OBSERVAÇÃO DA INTELIGÊNCIA ).\n\n Página: $pag";
    salvaLog( $mensagem );

    $msg_ret = 2;
    if ( !empty( $targ ) ) $msg_ret = 'f';
    echo msg_js( 'FALHA!', $msg_ret );

    exit;

}

$idinteli = empty( $idinteli ) ? '' : (int)$idinteli;
if ( empty( $idinteli ) ) {
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Identificador da inteligência em branco. Operação cancelada ( OBSERVAÇÃO DA INTELIGÊNCIA ).\n\n Página: $pag";
    salvaLog($mensagem);

    $msg_ret = 2;
    if ( !empty( $targ ) ) $msg_ret = 'f';
    echo msg_js( 'FALHA!', $msg_ret );

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

    $id_obs_inteli = empty( $id_obs_inteli ) ? '' : (int)$id_obs_inteli;

    if ( empty( $id_obs_inteli ) ) {

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador da observação em branco. Operação cancelada ( ATUALIZAÇÃO DE OBSERVAÇÃO DA INTELIGÊNCIA ). \n\n Página: $pag";
        salvaLog( $mensagem );

        $msg_ret = 2;
        if ( !empty( $targ ) ) $msg_ret = 'f';
        echo msg_js( 'FALHA!', $msg_ret );

        exit;

    }

    // pegar os dados do preso
    $where_det = "( SELECT `cod_detento` FROM `inteligencia` WHERE `idinteli` = $idinteli LIMIT 1 )";
    $detento = dados_det( $where_det );

    // pegar os dados da observação
    $obs_s = dados_obs( 'inteli', $id_obs_inteli );

    $obs_inteli  = empty( $obs_inteli ) ? '' : tratastring( $obs_inteli, 'U', FALSE );

    if ( empty( $obs_inteli ) ) {

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Observação em branco. Operação cancelada ( OBSERVAÇÃO  DA INTELIGÊNCIA ). \n\n $detento \n\n $obs_s \n\n Página: $pag";
        salvaLog( $mensagem );

        $msg_ret = 2;
        if ( !empty( $targ ) ) $msg_ret = 'f';
        echo msg_js( 'FALHA!', $msg_ret );

        exit;

    }

    $obs_inteli = "'" . $obs_inteli . "'";

    $query_obs = "UPDATE
                    `obs_inteli`
                  SET
                    `obs_inteli` = $obs_inteli,
                    `user_up` = $user,
                    `data_up` = NOW(),
                    `ip_up` = $ip
                  WHERE
                    `id_obs_inteli` = $id_obs_inteli
                  LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_obs = $model->query( $query_obs );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if( $query_obs ) {

        $mensagem = "[ ATUALIZAÇÃO DE OBSERVAÇÃO DA INTELIGÊNCIA ]\n Atualização de observação da inteligência. \n\n $detento \n\n $obs_s \n";

    } else {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de atualização de observação da inteligência. \n\n $detento \n\n $obs_s \n\n $valor_user.";

    }

    salvaLog( $mensagem );

    $msg = '';
    if ( !$success ) $msg = 'FALHA!!!';

    echo msg_js( "$msg", 2 );

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

    if ( empty( $n_inteli ) or $n_inteli < 4 ) {
        $tipo = 0;
        include '../init/msgnopag.php';
        $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso à página de manipulação de observação da inteligência SEM PERMISSÕES ( EXCLUSÃO ). \n\n Página: $pag";
        salvaLog( $mensagem );
        exit;
    }

    $id_obs_inteli = empty( $id_obs_inteli ) ? '' : (int)$id_obs_inteli;

    if ( empty( $id_obs_inteli ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador da observação em branco. Operação cancelada ( EXCLUSÃO DE OBS DA INTELIGÊNCIA ). \n\n Página: $pag";
        salvaLog( $mensagem );
        echo msg_js( 'FALHA!', 2 );
        exit;
    }

    // pegar os dados do preso
    $where_det = "( SELECT `cod_detento` FROM `inteligencia` WHERE `idinteli` = $idinteli LIMIT 1 )";
    $detento = dados_det( $where_det );

    // pegar os dados da observação
    $obs_s = dados_obs( 'inteli', $id_obs_inteli );

    $query_obs = "DELETE FROM `obs_inteli` WHERE `id_obs_inteli` = $id_obs_inteli LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_obs = $model->query( $query_obs );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if( $query_obs ) {

        $mensagem = "[ EXCLUSÃO DE OBSERVAÇÃO DA INTELIGÊNCIA ]\n Exclusão de observação da inteligência. \n\n $detento \n\n $obs_s \n";

    } else {

        $success = FALSE;

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de exclusão de observação da inteligência. \n\n $detento \n\n $obs_s \n";

    }

    salvaLog( $mensagem );

    $msg = '';
    if ( !$success ) $msg = 'FALHA!!!';

    echo msg_js( "$msg", 2 );

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

    // pegar os dados do preso
    $where_det = "( SELECT `cod_detento` FROM `inteligencia` WHERE `idinteli` = $idinteli LIMIT 1 )";
    $detento = dados_det( $where_det );

    $obs_inteli  = empty( $obs_inteli ) ? '' : tratastring( $obs_inteli, 'U', FALSE );

    if ( empty( $obs_inteli ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Observação em branco. Operação cancelada ( OBS DA INTELIGÊNCIA - CADASTRAMENTO ). \n\n $detento \n\n Página: $pag";
        salvaLog( $mensagem );
        $msg_ret = 2;
        if ( !empty( $targ ) ) $msg_ret = 'f';
        echo msg_js( 'FALHA!', $msg_ret );
        exit;
    }

    $obs_inteli = "'" . $obs_inteli . "'";

    $query_obs = "INSERT INTO
                    `obs_inteli`
                    (
                      `cod_inteli`,
                      `obs_inteli`,
                      `user_add`,
                      `data_add`,
                      `ip_add`
                    )
                  VALUES
                    (
                      $idinteli,
                      $obs_inteli,
                      $user,
                      NOW(),
                      $ip
                    )";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_obs = $model->query( $query_obs );

    $success = TRUE;
    if( $query_obs ) {

        $lastid = $model->lastInsertId();

        // pegar os dados da observação
        $obs_s = dados_obs( 'inteli', $lastid );

        $mensagem = "[ CADASTRO DE OBSERVAÇÃO DA INTELIGÊNCIA ]\n Cadastro de observação da inteligência. \n\n $detento \n\n $obs_s \n";

    } else {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de cadastramento de observação da inteligência. \n\n $detento \n\n $valor_user";

    }

    // fechando a conexao
    $model->closeConnection();

    salvaLog( $mensagem );

    $msg = '';
    $ret = 2;
    if ( !empty( $targ ) ){
        echo $ret = 'rf';
    }

    if ( !$success ) {
        $msg = 'FALHA!!!';
        if ( !empty( $targ ) ){
            $ret = 'f';
        }
    }

    echo msg_js( $msg, $ret );

    exit;

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELO CADASTRAMENTO
 * -------------------------------------------------------------------
 */
} else {
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Número de procedimento em branco ou inválido ( OBS DA INTELIGÊNCIA ).";
    salvaLog( $mensagem );
    $msg_ret = 2;
    if ( !empty( $targ ) ) $msg_ret = 'f';
    echo msg_js( 'FALHA!', $msg_ret );
    exit;
}

?>
</body>
</html>