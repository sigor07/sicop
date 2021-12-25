<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';
$mensagem = '';

$n_pront = get_session( 'n_pront', 'int' );

if ( empty( $n_pront ) or $n_pront < 3 ) {
    $tipo = 0;
    include '../init/msgnopag.php';
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso à página de manipulação de observação de grade SEM PERMISSÕES. \n\n Página: $pag";
    salvaLog($mensagem);
    exit;
}

$is_post = is_post();

if ( !$is_post ) {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso direto à página de manipulação de grade.<br /><br /> Página: $pag";
    salvaLog($mensagem);
    echo msg_js( '', 1 );
    exit;
}


extract( $_POST, EXTR_OVERWRITE );

$targ   = empty($targ) ? 0 : 1;
$proced = empty($proced) ? '' : (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO

if ( empty( $proced ) or $proced > 3 ) {
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Número de procedimento em branco ou inválido. Operação cancelada ( OBSERVAÇÃO DE GRADE ).\n\n Página: $pag";
    salvaLog($mensagem);
    $saida = msg_js( 'FALHA!', 2 );
    if ( !empty( $targ ) ) {
        $saida = msg_js( 'FALHA!', 'f' );
    }
    echo $saida;
    exit;
}

$user       = get_session( 'user_id', 'int' );
$ip         = "'" . $_SERVER['REMOTE_ADDR'] . "'";

if ( $proced == 1 ){ // ATUALIZAÇÃO
/*
 * -----------------------------------------------------------
 * PARTE RESPONSAVEL PELA ATUALIZAÇÃO
 * -----------------------------------------------------------
 */

    $id_obs_grade = empty( $id_obs_grade ) ? '' : (int)$id_obs_grade;

    if ( empty( $id_obs_grade ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador da observação em branco. Operação cancelada (ATUALIZAÇÃO DE OBS DE GRADE). \n\n Página: $pag";
        salvaLog( $mensagem );
        echo msg_js( 'FALHA!', 2 );
        exit;
    }

    // pegar os dados do preso
    $where_det = "( SELECT `cod_detento` FROM `obs_grade` WHERE `id_obs_grade` = $id_obs_grade LIMIT 1 )";
    $detento = dados_det( $where_det );

    $obs_grade  = empty( $obs_grade ) ? '' : tratastring( $obs_grade, 'U', FALSE );

    if ( empty( $obs_grade ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Observação em branco. Operação cancelada ( OBS DE GRADE ). \n\n $detento \n\n Página: $pag";
        salvaLog($mensagem);
        $saida = msg_js( 'FALHA!', 2 );
        if ( !empty( $targ ) ) {
            $saida = msg_js( 'FALHA!', 'f' );
        }
        echo $saida;
        exit;
    }

    $obs_grade = "'" . $obs_grade . "'";

    // pegar os dados da observação
    $obs_s = dados_obs( 'grade', $id_obs_grade );

    $query_obs = "UPDATE
                    `obs_grade`
                  SET
                    `obs_grade` = $obs_grade,
                    `user_up` = $user,
                    `data_up` = NOW(),
                    `ip_up` = $ip
                  WHERE
                    `id_obs_grade` = $id_obs_grade
                  LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_obs = $model->query( $query_obs );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if( $query_obs ) {

        $mensagem = "[ ATUALIZAÇÃO DE OBSERVAÇÃO DE GRADE ]\n Atualização de observação de grade. \n\n $detento \n\n $obs_s \n";

    } else {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de atualização de observação de grade.\n\n $detento.\n\n $obs_s \n\n $valor_user";

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

    if ( empty( $n_pront ) or $n_pront < 4 ) {
        $tipo = 0;
        include '../init/msgnopag.php';
        $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso à página de manipulação de observação de grade SEM PERMISSÕES ( EXCLUSÃO DE OBS DE GRADE ). \n\n Página: $pag";
        salvaLog($mensagem);
        exit;
    }

    $id_obs_grade = empty( $id_obs_grade ) ? '' : (int)$id_obs_grade;

    if ( empty( $id_obs_grade ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador da observação em branco. Operação cancelada ( EXCLUSÃO DE OBS DE GRADE ). \n\n Página: $pag";
        salvaLog( $mensagem );
        echo msg_js( 'FALHA!', 2 );
        exit;
    }

    // pegar os dados do preso
    $where_det = "( SELECT `cod_detento` FROM `obs_grade` WHERE `id_obs_grade` = $id_obs_grade LIMIT 1 )";
    $detento = dados_det( $where_det );

    // pegar os dados da observação
    $obs_s = dados_obs( 'grade', $id_obs_grade );

    $query_obs = "DELETE FROM `obs_grade` WHERE `id_obs_grade` = $id_obs_grade LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_obs = $model->query( $query_obs );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if( $query_obs ) {

        $mensagem = "[ EXCLUSÃO DE OBSERVAÇÃO DE GRADE ]\n Exclusão de observação de grade. \n\n $detento \n\n $obs_s";

    } else {

        $success = FALSE;

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de exclusão de observação de grade. \n\n $detento \n\n $obs_s";

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
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador da observação em branco. Operação cancelada ( CADASTRAMENTO DE OBS DE GRADE ). \n\n Página: $pag";
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

    $obs_grade  = empty( $obs_grade ) ? '' : tratastring( $obs_grade, 'U', FALSE );

    if ( empty( $obs_grade ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Observação em branco. Operação cancelada ( OBS DE GRADE - CADASTRAMENTO ). \n\n $detento \n\n Página: $pag";
        salvaLog($mensagem);
        $saida = msg_js( 'FALHA!', 2 );
        if ( !empty( $targ ) ) {
            $saida = msg_js( 'FALHA!', 'f' );
        }
        echo $saida;
        exit;
    }

    $obs_grade = "'" . $obs_grade . "'";

    $query_obs = "INSERT INTO
                    `obs_grade`
                    (
                      `cod_detento`,
                      `obs_grade`,
                      `user_add`,
                      `data_add`,
                      `ip_add`
                    )
                  VALUES
                    (
                      $iddet,
                      $obs_grade,
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
        $obs_s = dados_obs( 'grade', $lastid );

        $mensagem = "[ CADASTRO DE OBSERVAÇÃO DE GRADE ]\n Cadastro de observação de grade. \n\n $detento \n\n $obs_s \n";

    } else {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de cadastramento de observação.\n\n $detento.\n\n $valor_user";

    }

    // fechando a conexao
    $model->closeConnection();

    salvaLog( $mensagem );

    $saida = msg_js( '', 2 );
    if ( !empty( $targ ) ){
        $saida = "<script type='text/javascript'> window.opener.location.reload(); opener.location.href='../prontuario/detalgrade.php?iddet=$iddet#obs'; self.window.close();</script>";
    }

    if ( !$success ) {
        $saida = msg_js( 'FALHA!!!' );
        if ( !empty( $targ ) ){
            $saida .= msg_js( '', 'f' );
        } else {
            $saida .= msg_js( '', 2 );
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
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Número de procedimento em branco ou inválido ( OBS DE GRADE ).";
    salvaLog( $mensagem );
    $saida = msg_js( 'FALHA!', 2 );
    if ( !empty( $targ ) ) {
        $saida = msg_js( 'FALHA!', 'f' );
    }
    echo $saida;
    exit;
}

?>
</body>
</html>