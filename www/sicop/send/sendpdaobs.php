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
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso à página de manipulação de observação de PDA SEM PERMISSÕES. \n\n Página: $pag";
    salvaLog( $mensagem );
    exit;
}

$is_post = is_post();

if ( !$is_post ) {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso direto à página de manipulação de observação de PDA.<br /><br /> Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( '', 1 );
    exit;
}


extract( $_POST, EXTR_OVERWRITE );

$targ   = empty( $targ ) ? 0 : 1;
$proced = empty( $proced ) ? '' : (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO

if ( empty( $proced ) or $proced > 3 ) {
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Número de procedimento em branco ou inválido. Operação cancelada ( OBSERVAÇÃO DE PDA ).\n\n Página: $pag";
    salvaLog( $mensagem );
    $saida = msg_js( 'FALHA!', 2 );
    if ( !empty( $targ ) ) $saida = msg_js( 'FALHA!', 'f' );
    echo $saida;
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

    $id_obs_pda = empty( $id_obs_pda ) ? '' : (int)$id_obs_pda;

    if ( empty( $id_obs_pda ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador da observação em branco. Operação cancelada ( ATUALIZAÇÃO DE OBSERVAÇÃO DE PDA ). \n\n Página: $pag";
        salvaLog( $mensagem );
        echo msg_js( 'FALHA!', 2 );
        exit;
    }

    // pegar os dados do PDA
    $pda_where = "( SELECT `cod_pda` FROM `obs_pda` WHERE `id_obs_pda` = $id_obs_pda LIMIT 1 )";
    $pda = dados_pda( $pda_where );

    // pegar os dados do preso
    $det_where = "( SELECT `cod_detento` FROM `sindicancias` WHERE `idsind` = $pda_where LIMIT 1 )";
    $detento = dados_det( $det_where );

    // pegar os dados da observação
    $obs_s = dados_obs( 'pda', $id_obs_pda );

    $obs_pda = empty( $obs_pda ) ? '' : tratastring( $obs_pda, 'U', FALSE );

    if ( empty( $obs_pda ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Observação em branco. Operação cancelada ( OBSERVAÇÃO DE PDA ). \n\n $pda \n\n $detento \n\n $obs_s \n\n Página: $pag";
        salvaLog( $mensagem );
        $saida = msg_js( 'FALHA!', 2 );
        if ( !empty( $targ ) ) $saida = msg_js( 'FALHA!', 'f' );
        echo $saida;
        exit;
    }

    $obs_pda = "'" . $obs_pda . "'";

    $query_obs = "UPDATE
                    `obs_pda`
                  SET
                    `obs_pda` = $obs_pda,
                    `user_up` = $user,
                    `data_up` = NOW(),
                    `ip_up` = $ip
                  WHERE
                    `id_obs_pda` = $id_obs_pda
                  LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_obs = $model->query( $query_obs );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if( $query_obs ) {

            $mensagem = "[ ATUALIZAÇÃO DE OBSERVAÇÃO DE PDA ]\n Atualização de observação de PDA. \n\n $pda \n\n $detento \n\n $obs_s \n";

    } else {

        $success = FALSE;;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de atualização de observação de PDA.\n\n $pda \n\n $detento \n\n $obs_s \n\n $valor_user.";

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

    $n_sind = get_session( 'n_sind', 'int' );

    if ( empty( $n_sind ) or $n_sind < 4 ) {
        $tipo = 0;
        include '../init/msgnopag.php';
        $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso à página de manipulação de observação de PDA SEM PERMISSÕES ( EXCLUSÃO DE OBSERVAÇÃO DE PDA ). \n\n Página: $pag";
        salvaLog( $mensagem );
        exit;
    }

    $id_obs_pda = empty( $id_obs_pda ) ? '' : (int)$id_obs_pda;

    if ( empty( $id_obs_pda ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador da observação em branco. Operação cancelada ( EXCLUSÃO DE OBSERVAÇÃO DE PDA ). \n\n Página: $pag";
        salvaLog( $mensagem );
        echo msg_js( 'FALHA!', 2 );
        exit;
    }

    // pegar os dados do PDA
    $pda_where = "( SELECT `cod_pda` FROM `obs_pda` WHERE `id_obs_pda` = $id_obs_pda LIMIT 1 )";
    $pda = dados_pda( $pda_where );

    // pegar os dados do preso
    $det_where = "( SELECT `cod_detento` FROM `sindicancias` WHERE `idsind` = $pda_where LIMIT 1 )";
    $detento = dados_det( $det_where );

    // pegar os dados da observação
    $obs_s = dados_obs( 'pda', $id_obs_pda );

    $query_obs = "DELETE FROM `obs_pda` WHERE `id_obs_pda` = $id_obs_pda LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_obs = $model->query( $query_obs );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if( $query_obs ) {

        $mensagem = "[ EXCLUSÃO OBSERVAÇÃO DE PDA ]\n Exclusão de observação de PDA. \n\n $pda \n\n $detento \n\n $obs_s \n";

    } else {

        $success = FALSE;

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de exclusão de observação de PDA. \n\n $pda \n\n $detento \n\n $obs_s \n";

    }

    salvaLog( $mensagem );

    $msg = '';
    if ( !$success ) $msg = 'FALHA!!!';

    echo msg_js( "$msg", 1 );

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

    $idpda = empty( $idpda ) ? '' : (int)$idpda;

    if ( empty( $idpda ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador do PDA em branco. Operação cancelada ( CADASTRAMENTO DE OBSERVAÇÃO DE PDA ). \n\n Página: $pag";
        salvaLog( $mensagem );
        $saida = msg_js( 'FALHA!', 2 );
        if ( !empty( $targ ) ) $saida = msg_js( 'FALHA!', 'f' );
        echo $saida;
        exit;
    }

    // pegar os dados do PDA
    $pda = dados_pda( $idpda );

    // pegar os dados do preso
    $det_where = "( SELECT `cod_detento` FROM `sindicancias` WHERE `idsind` = $idpda LIMIT 1 )";
    $detento = dados_det( $det_where );

    $obs_pda  = empty( $obs_pda ) ? '' : tratastring( $obs_pda, 'U', FALSE );

    if ( empty( $obs_pda ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Observação em branco. Operação cancelada ( OBSERVAÇÃO DE PDA - CADASTRAMENTO ). \n\n $pda \n\n $detento \n\n Página: $pag";
        salvaLog( $mensagem );
        $saida = msg_js( 'FALHA!', 2 );
        if ( !empty( $targ ) ) $saida = msg_js( 'FALHA!', 'f' );
        echo $saida;
        exit;
    }

    $obs_pda = "'" . $obs_pda . "'";

    $query_obs = "INSERT INTO
                    `obs_pda`
                    (
                      `cod_pda`,
                      `obs_pda`,
                      `user_add`,
                      `data_add`,
                      `ip_add`
                    )
                  VALUES
                    (
                      $idpda,
                      $obs_pda,
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
        $obs_s = dados_obs( 'pda', $lastid );

        $mensagem = "[ CADASTRO OBSERVAÇÃO DE PDA ]\n Cadastro de observação de PDA. \n\n $pda \n\n $detento \n\n $obs_s \n";

    } else {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de cadastramento de observação de PDA. \n\n $pda \n\n $detento \n\n $valor_user \n";

    }

    // fechando a conexao
    $model->closeConnection();

    salvaLog( $mensagem );

    $saida = msg_js( '', 2 );
    if ( !empty( $targ ) ){
        $saida = "<script type='text/javascript'> window.opener.location.reload(); opener.location.href='../sind/detalpda.php?idsind=$idpda#obs'; self.window.close();</script>";
    }

    if ( !$success ) {
        $msg = 'FALHA!!!';
        if ( !empty( $targ ) ){
            $saida = msg_js( "$msg", 'f' );
        } else {
            $saida = msg_js( "$msg", 2 );
        }
    }

    echo $saida;

    exit;

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELO CADASTRAMENTO
 * -------------------------------------------------------------------
 */
} else {
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Número de procedimento em branco ou inválido ( OBSERVAÇÃO DE PDA ).";
    salvaLog( $mensagem );
    $saida = msg_js( 'FALHA!', 2 );
    if ( !empty( $targ ) ) $saida = msg_js( 'FALHA!', 'f' );
    echo $saida;
    exit;
}

?>
</body>
</html>