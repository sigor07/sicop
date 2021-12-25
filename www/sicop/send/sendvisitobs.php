<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';
$mensagem = '';

$n_rol = get_session( 'n_rol', 'int' );

if ( empty( $n_rol ) or $n_rol < 3 ) {
    $tipo = 0;
    include '../init/msgnopag.php';
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso à página de manipulação de observação de visitante SEM PERMISSÕES. \n\n Página: $pag";
    salvaLog( $mensagem );
    exit;
}

$is_post = is_post();

if ( !$is_post ) {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso direto à página de manipulação de observação de visitante.<br /><br /> Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( '', 1 );
    exit;
}


extract( $_POST, EXTR_OVERWRITE );

$targ   = empty($targ) ? 0 : 1;
$proced = empty($proced) ? '' : (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO

if ( empty( $proced ) or $proced > 3 ) {
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Número de procedimento em branco ou inválido. Operação cancelada ( OBSERVAÇÃO DE VISITANTE ).\n\n Página: $pag";
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

    $id_obs_visit = empty( $id_obs_visit ) ? '' : (int)$id_obs_visit;

    if ( empty( $id_obs_visit ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador da observação em branco. Operação cancelada ( ATUALIZAÇÃO DE OBSERVAÇÃO DE VISITANTE ). \n\n Página: $pag";
        salvaLog( $mensagem );
        echo msg_js( 'FALHA!', 2 );
        exit;
    }

    // pegar os dados do visitante
    $visit_where = "( SELECT `cod_visita` FROM `obs_visit` WHERE `id_obs_visit` = $id_obs_visit LIMIT 1 )";
    $visita = dados_visit( $visit_where );

    // pegar os dados do preso
    $det_where = "( SELECT `cod_detento` FROM `visitas` WHERE `idvisita` = $visit_where LIMIT 1 )";
    $detento = dados_det( $det_where );

    // pegar os dados da observação
    $obs_s = dados_obs( 'visit', $id_obs_visit );

    $obs_visit = empty( $obs_visit ) ? '' : tratastring( $obs_visit, 'U', FALSE );

    if ( empty( $obs_visit ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Observação em branco. Operação cancelada ( OBSERVAÇÃO DE VISITANTE ). \n\n[ VISITANTE ]\n $visita \n\n[ DETENTO ]\n $detento \n\n[ OBSERVAÇÃO ]\n $obs_s \n\n Página: $pag";
        salvaLog( $mensagem );
        $saida = msg_js( 'FALHA!', 2 );
        if ( !empty( $targ ) ) {
            $saida = msg_js( 'FALHA!', 'f' );
        }
        echo $saida;
        exit;
    }

    $obs_visit = "'" . $obs_visit . "'";

    $destacar  = empty( $destacar ) ? 0 : 1;

    $query_obs = "UPDATE
                    `obs_visit`
                  SET
                    `obs_visit` = $obs_visit,
                    `destacar` = $destacar,
                    `user_up` = $user,
                    `data_up` = NOW(),
                    `ip_up` = $ip
                  WHERE
                    `id_obs_visit` = $id_obs_visit
                  LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_obs = $model->query( $query_obs );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if( $query_obs ) {

        $mensagem = "[ ATUALIZAÇÃO DE OBSERVAÇÃO DE VISITANTE ]\n Atualização de observação de visitante. \n\n $visita \n\n $detento \n\n $obs_s \n";

    } else {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de atualização de observação de visitante.\n\n $visita \n\n $detento \n\n $obs_s \n\n $valor_user \n";

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

    $n_rol = get_session( 'n_rol', 'int' );

    if ( empty( $n_rol ) or $n_rol < 4 ) {
        $tipo = 0;
        include '../init/msgnopag.php';
        $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso à página de manipulação de observação de visitante SEM PERMISSÕES ( EXCLUSÃO DE OBSERVAÇÃO DE VISITANTE ). \n\n Página: $pag";
        salvaLog( $mensagem );
        exit;
    }

    $id_obs_visit = empty( $id_obs_visit ) ? '' : (int)$id_obs_visit;

    if ( empty( $id_obs_visit ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador da observação em branco. Operação cancelada ( EXCLUSÃO DE OBSERVAÇÃO DE VISITANTE ). \n\n Página: $pag";
        salvaLog( $mensagem );
        echo msg_js( 'FALHA!', 2 );
        exit;
    }

    // pegar os dados do visitante
    $visit_where = "( SELECT `cod_visita` FROM `obs_visit` WHERE `id_obs_visit` = $id_obs_visit LIMIT 1 )";
    $visita = dados_visit( $visit_where );

    // pegar os dados do preso
    $det_where = "( SELECT `cod_detento` FROM `visitas` WHERE `idvisita` = $visit_where LIMIT 1 )";
    $detento = dados_det( $det_where );

    // pegar os dados da observação
    $obs_s = dados_obs( 'visit', $id_obs_visit );

    $query_obs = "DELETE FROM `obs_visit` WHERE `id_obs_visit` = $id_obs_visit LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_obs = $model->query( $query_obs );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if( $query_obs ) {

        $mensagem = "[ EXCLUSÃO OBSERVAÇÃO DE VISITANTE ]\n Exclusão de observação de visitante. \n\n $visita \n\n $detento \n\n $obs_s \n";

    } else {

        $success = FALSE;

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de exclusão de observação de visitante. \n\n $visita \n\n $detento \n\n $obs_s \n";

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

    $idvisit = empty( $idvisit ) ? '' : (int)$idvisit;

    if ( empty( $idvisit ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador do visitante em branco. Operação cancelada ( CADASTRAMENTO DE OBSERVAÇÃO DE VISITANTE ). \n\n Página: $pag";
        salvaLog( $mensagem );
        $saida = msg_js( 'FALHA!', 2 );
        if ( !empty( $targ ) ) $saida = msg_js( 'FALHA!', 'f' );
        echo $saida;
        exit;
    }

    // pegar os dados do visitante
    $visita = dados_visit( $idvisit );

    // pegar os dados do preso
    $det_where = "( SELECT `cod_detento` FROM `visitas` WHERE `idvisita` = $idvisit LIMIT 1 )";
    $detento = dados_det( $det_where );

    $obs_visit  = empty( $obs_visit ) ? '' : tratastring( $obs_visit, 'U', FALSE );

    if ( empty( $obs_visit ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Observação em branco. Operação cancelada ( OBSERVAÇÃO DE VISITANTE - CADASTRAMENTO ). \n\n[ VISITANTE ]\n $visita \n\n[ DETENTO ]\n $detento \n\n Página: $pag";
        salvaLog( $mensagem );
        $saida = msg_js( 'FALHA!', 2 );
        if ( !empty( $targ ) ) $saida = msg_js( 'FALHA!', 'f' );
        echo $saida;
        exit;
    }

    $obs_visit = "'" . $obs_visit . "'";

    $destacar  = empty( $destacar ) ? 0 : 1;

    $query_obs = "INSERT INTO
                    `obs_visit`
                    (
                      `cod_visita`,
                      `obs_visit`,
                      `destacar`,
                      `user_add`,
                      `data_add`,
                      `ip_add`
                    )
                  VALUES
                    (
                      $idvisit,
                      $obs_visit,
                      $destacar,
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
        $obs_s = dados_obs( 'visit', $lastid );

        $mensagem = "[ CADASTRO OBSERVAÇÃO DE VISITANTE ]\n Cadastro de observação de visitante. \n\n $visita \n\n $detento \n\n $obs_s \n";

    } else {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de cadastramento de observação de visitante. \n\n $visita \n\n $detento. \n\n $valor_user \n";

    }

    // fechando a conexao
    $model->closeConnection();

    salvaLog( $mensagem );

    $saida = msg_js( '', 2 );
    if ( !empty( $targ ) ){
        $saida = "<script type='text/javascript'> window.opener.location.reload(); opener.location.href='../visita/detalvisit.php?idvisit=$idvisit#obs'; self.window.close();</script>";
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
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Número de procedimento em branco ou inválido ( OBSERVAÇÃO DE VISITANTE ).";
    salvaLog( $mensagem );
    $saida = msg_js( 'FALHA!', 2 );
    if ( !empty( $targ ) ) $saida = msg_js( 'FALHA!', 'f' );
    echo $saida;
    exit;
}

?>
</body>
</html>