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
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso à página de manipulação de observação de rol de visitas SEM PERMISSÕES. \n\n Página: $pag";
    salvaLog( $mensagem );
    exit;
}

$is_post = is_post();

if ( !$is_post ) {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso direto à página de manipulação de observação de rol de visitas.<br /><br /> Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( '', 1 );
    exit;
}


extract( $_POST, EXTR_OVERWRITE );

$targ   = empty($targ) ? 0 : 1;
$proced = empty($proced) ? '' : (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO

if ( empty( $proced ) or $proced > 3 ) {
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Número de procedimento em branco ou inválido. Operação cancelada ( OBSERVAÇÃO DE ROL DE VISITAS ).\n\n Página: $pag";
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

    $id_obs_rol = empty( $id_obs_rol ) ? '' : (int)$id_obs_rol;

    if ( empty( $id_obs_rol ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador da observação em branco. Operação cancelada ( ATUALIZAÇÃO DE OBSERVAÇÃO DE ROL DE VISITAS ). \n\n Página: $pag";
        salvaLog( $mensagem );
        echo msg_js( 'FALHA!', 2 );
        exit;
    }

    // pegar os dados do preso
    $det_where = "( SELECT `cod_detento` FROM `obs_rol` WHERE `id_obs_rol` = $id_obs_rol LIMIT 1 )";
    $detento = dados_det( $det_where );

    // pegar os dados da observação
    $obs_s = dados_obs( 'rol', $id_obs_rol );

    $obs_rol = empty( $obs_rol ) ? '' : tratastring( $obs_rol, 'U', FALSE );

    if ( empty( $obs_rol ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Observação em branco. Operação cancelada ( OBSERVAÇÃO DE ROL DE VISITAS ). \n\n $detento \n\n $obs_s \n\n Página: $pag";
        salvaLog( $mensagem );
        $saida = msg_js( 'FALHA!', 2 );
        if ( !empty( $targ ) ) $saida = msg_js( 'FALHA!', 'f' );
        echo $saida;
        exit;
    }

    $obs_rol = "'" . $obs_rol . "'";

    $query_obs = "UPDATE
                    `obs_rol`
                  SET
                    `obs_rol` = $obs_rol,
                    `user_up` = $user,
                    `data_up` = NOW(),
                    `ip_up` = $ip
                  WHERE
                    `id_obs_rol` = $id_obs_rol
                  LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_obs = $model->query( $query_obs );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if( $query_obs ) {

        $mensagem = "[ ATUALIZAÇÃO DE OBSERVAÇÃO DE ROL DE VISITAS ]\n Atualização de observação de rol de visitas. \n\n $detento \n\n $obs_s \n";

    } else {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de atualização de observação de rol de visitas.\n\n $detento \n\n $obs_s \n\n $valor_user.";

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
        $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso à página de manipulação de observação de rol de visitas SEM PERMISSÕES ( EXCLUSÃO DE OBSERVAÇÃO DE ROL DE VISITAS ). \n\n Página: $pag";
        salvaLog( $mensagem );
        exit;
    }

    $id_obs_rol = empty( $id_obs_rol ) ? '' : (int)$id_obs_rol;

    if ( empty( $id_obs_rol ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador da observação em branco. Operação cancelada ( EXCLUSÃO DE OBSERVAÇÃO DE ROL DE VISITAS ). \n\n Página: $pag";
        salvaLog( $mensagem );
        echo msg_js( 'FALHA!', 2 );
        exit;
    }

    // pegar os dados do preso
    $det_where = "( SELECT `cod_detento` FROM `obs_rol` WHERE `id_obs_rol` = $id_obs_rol LIMIT 1 )";
    $detento = dados_det( $det_where );

    // pegar os dados da observação
    $obs_s = dados_obs( 'rol', $id_obs_rol );

    $query_obs = "DELETE FROM `obs_rol` WHERE `id_obs_rol` = $id_obs_rol LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_obs = $model->query( $query_obs );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if( $query_obs ) {

        $mensagem = "[ EXCLUSÃO OBSERVAÇÃO DE ROL DE VISITAS ]\n Exclusão de observação de rol de visitas. \n\n $detento \n\n $obs_s \n";

    } else {

        $success = FALSE;

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de exclusão de observação de rol de visitas. \n\n $detento \n\n $obs_s.";

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

    $iddet = empty( $iddet ) ? '' : (int)$iddet;

    if ( empty( $iddet ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. Operação cancelada ( CADASTRAMENTO DE OBSERVAÇÃO DE ROL DE VISITAS ). \n\n Página: $pag";
        salvaLog( $mensagem );
        $saida = msg_js( 'FALHA!', 2 );
        if ( !empty( $targ ) ) {
            $saida = msg_js( 'FALHA!', 'f' );
        }
        echo $saida;
        exit;
    }


    // pegar os dados do preso
    $detento = dados_det( $iddet );

    $obs_rol  = empty( $obs_rol ) ? '' : tratastring( $obs_rol, 'U', FALSE );

    if ( empty( $obs_rol ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Observação em branco. Operação cancelada ( OBSERVAÇÃO DE ROL DE VISITAS - CADASTRAMENTO ). \n\n $detento \n\n Página: $pag";
        salvaLog( $mensagem );
        $saida = msg_js( 'FALHA!', 2 );
        if ( !empty( $targ ) ) {
            $saida = msg_js( 'FALHA!', 'f' );
        }
        echo $saida;
        exit;
    }

    $obs_rol = "'" . $obs_rol . "'";

    $query_obs = "INSERT INTO
                    `obs_rol`
                    (
                      `cod_detento`,
                      `obs_rol`,
                      `user_add`,
                      `data_add`,
                      `ip_add`
                    )
                  VALUES
                    (
                      $iddet,
                      $obs_rol,
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
        $obs_s = dados_obs( 'rol', $lastid );

        $mensagem = "[ CADASTRO OBSERVAÇÃO DE ROL DE VISITAS ]\n Cadastro de observação de rol de visitas. \n\n $detento \n\n[ OBSERVAÇÃO ]\n $obs_s \n";

    } else {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de cadastramento de observação de rol de visitas. \n\n $detento \n\n $valor_user.";

    }

    // fechando a conexao
    $model->closeConnection();

    salvaLog( $mensagem );

    $saida = msg_js( '', 2 );
    if ( !empty( $targ ) ){
        $saida = "<script type='text/javascript'> window.opener.location.reload(); opener.location.href='../visita/rol_visit.php?iddet=$iddet#obs'; self.window.close();</script>";
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

    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Número de procedimento em branco ou inválido ( OBSERVAÇÃO DE ROL DE VISITAS ).";
    salvaLog( $mensagem );
    $saida = msg_js( 'FALHA!', 2 );
    if ( !empty( $targ ) ) $saida = msg_js( 'FALHA!', 'f' );
    echo $saida;
    exit;

}

?>
</body>
</html>