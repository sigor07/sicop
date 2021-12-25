<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';
$mensagem = '';

$n_admsist = get_session( 'n_admsist', 'int' );

if ( empty( $n_admsist ) or $n_admsist < 3 ) {
    $tipo = 0;
    include '../init/msgnopag.php';
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso à página de manipulação de observação da lista telefônica SEM PERMISSÕES. \n\n Página: $pag";
    salvaLog($mensagem);
    exit;
}

$is_post = is_post();

if ( !$is_post ) {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso direto à página de manipulação da lista telefonica.<br /><br /> Página: $pag";
    salvaLog($mensagem);
    echo msg_js( '', 1 );
    exit;
}

extract($_POST, EXTR_OVERWRITE);

$targ       = empty( $targ ) ? 0 : 1;
$proced     = empty( $proced ) ? '' : (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO

if ( empty( $proced ) or $proced > 3 ) {
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Número de procedimento em branco ou inválido. Operação cancelada ( OBSERVAÇÃO - LISTA DE TELEFONES ).\n\n Página: $pag";
    salvaLog($mensagem);

    echo msg_js( 'FALHA!' );

    $saida = msg_js( '', 2 );
    if ( !empty( $targ ) ) {
        $saida = msg_js( '', 'f' );
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

    $id_obs_listatel = empty( $id_obs_listatel ) ? '' : (int)$id_obs_listatel;

    if ( empty( $id_obs_listatel ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Identificador da observação em branco. Operação cancelada ( ATUALIZAÇÃO DE OBS - LISTA DE TELEFONES ). \n\n Página: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!', 2 );
        exit;
    }

    $q_s_local = "SELECT
                    `idlistatel`,
                    `tel_local`
                  FROM
                    `listatel`
                  WHERE
                    `idlistatel` = ( SELECT `cod_listatel` FROM `obs_listatel` WHERE `id_obs_listatel` = $id_obs_listatel LIMIT 1 )
                  LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_s_local = $model->query( $q_s_local );

    // fechando a conexao
    $model->closeConnection();

    $d_s_local = $q_s_local->fetch_assoc();
    $idlocal   = $d_s_local['idlistatel'];
    $local     = $d_s_local['tel_local'];
    $local_s   = "<b>ID:</b> $idlocal; <b>Local:</b> $local";

    $obs_listatel  = empty( $obs_listatel ) ? '' : tratastring( $obs_listatel, 'U', FALSE );

    if ( empty( $obs_listatel ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Observação em branco. Operação cancelada ( ATUALIZAÇÃO DE OBS - LISTA DE TELEFONES ). \n\n[ LOCALIDADE ]\n $local_s \n\n Página: $pag";
        salvaLog( $mensagem );
        echo msg_js( 'FALHA!', 2 );
        exit;
    }

    $obs_listatel = "'" . $obs_listatel . "'";

    $query_obs = "UPDATE
                    `obs_listatel`
                  SET
                    `obs_listatel` = $obs_listatel,
                    `user_up` = $user,
                    `data_up` = NOW(),
                    `ip_up` = $ip
                  WHERE
                    `id_obs_listatel` = $id_obs_listatel
                  LIMIT 1";

    $q_s_obs = "SELECT
                  `obs_listatel`
                FROM
                  `obs_listatel`
                WHERE
                  `id_obs_listatel` = $id_obs_listatel
                LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_s_obs = $model->query( $q_s_obs );

    // fechando a conexao
    $model->closeConnection();

    $d_s_obs = $q_s_obs->fetch_assoc();
    $obs_s   = $d_s_obs['obs_listatel'];
    $obs_l_s = "<b>ID:</b> $id_obs_listatel; <b>Observação:</b> $obs_s";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_obs = $model->query( $query_obs );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if( $query_obs ) {

        $mensagem = "[ ATUALIZAÇÃO DE OBSERVAÇÃO DE TELEFONE ]\n Atualização de observação de localidade da lista de telefones. \n\n[ LOCALIDADE ]\n $local_s \n\n[ OBSERVAÇÃO ]\n $obs_l_s \n";

    } else {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de atualização de observação de localidade da lista de telefones. \n\n[ LOCALIDADE ]\n $local_s \n\n[ OBSERVAÇÃO ]\n $obs_l_s. \n\n $valor_user \n";

    }

    salvaLog( $mensagem );

    if ( !$success ) echo msg_js( 'FALHA!!!' );

    echo msg_js( '', 2 );

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

    $n_admsist = get_session( 'n_admsist', 'int' );

    if ( empty( $n_admsist ) or $n_admsist < 4 ) {
        $tipo = 0;
        include '../init/msgnopag.php';
        $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso à página de manipulação de observação da lista telefônica SEM PERMISSÕES ( EXCLUSÃO DE OBSERVAÇÃO ). \n\n Página: $pag";
        salvaLog($mensagem);
        exit;
    }

    $id_obs_listatel = empty( $id_obs_listatel ) ? '' : (int)$id_obs_listatel;

    if ( empty( $id_obs_listatel ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Identificador da observação em branco. Operação cancelada ( EXCLUSÃO DE OBS - LISTA DE TELEFONES ). \n\n Página: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!', 2 );
        exit;
    }

    $q_s_obs = "SELECT
                  `obs_listatel`.`cod_listatel`,
                  `obs_listatel`.`obs_listatel`,
                  `listatel`.`tel_local`
                FROM
                  `obs_listatel`
                  INNER JOIN `listatel` ON `obs_listatel`.`cod_listatel` = `listatel`.`idlistatel`
                WHERE
                  `obs_listatel`.`id_obs_listatel` = $id_obs_listatel
                LIMIT 1";

        // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_s_obs = $model->query( $q_s_obs );

    // fechando a conexao
    $model->closeConnection();

    $d_s_obs = $q_s_obs->fetch_assoc();
    $idlocal = $d_s_obs['cod_listatel'];
    $local   = $d_s_obs['tel_local'];
    $local_s = "<b>ID:</b> $idlocal; <b>Local:</b> $local";
    $obs_s   = $d_s_obs['obs_listatel'];
    $obs_l_s = "<b>ID:</b> $id_obs_listatel; <b>Observação:</b> $obs_s";

    $query_obs = "DELETE FROM `obs_listatel` WHERE `id_obs_listatel` = $id_obs_listatel LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_obs = $model->query( $query_obs );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if ( $query_obs ) {

        $mensagem = "[ EXCLUSÃO DE OBSERVAÇÃO DE TELEFONE ]\n Exclusão de observação de localidade da lista de telefones: \n\n[ LOCALIDADE ]\n $local_s \n\n[ OBSERVAÇÃO ]\n $obs_l_s \n";

    } else {

        $success = FALSE;

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de exclusão de observação de localidade da lista de telefones.\n\n[ LOCALIDADE ]\n $local_s \n\n[ OBSERVAÇÃO ]\n $obs_l_s.\n";

    }

    salvaLog( $mensagem );

    $saida = '';
    if ( !$success ) $saida .= msg_js( 'FALHA!!!' );

    $saida .= msg_js( '', 1 );

    echo $saida;

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

    $idlt = empty( $idlt ) ? '' : (int)$idlt;

    if ( empty( $idlt ) ){
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Identificador da localidade do telefone em branco. Operação cancelada ( CADASTRAMENTO DE OBS - LISTA DE TELEFONES ).\n\n Página: $pag";
        salvaLog( $mensagem );
        $saida = msg_js( '', 2 );
        if ( !empty( $targ ) ){
            $saida = msg_js( 'FALHA!', 'f' );
        }
        echo $saida;
        exit;
    }

    $q_s_local = "SELECT
                    `idlistatel`,
                    `tel_local`
                  FROM
                    `listatel`
                  WHERE
                    `idlistatel` = $idlt
                  LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_s_local = $model->query( $q_s_local );

    // fechando a conexao
    $model->closeConnection();

    $d_s_local = $q_s_local->fetch_assoc();
    $idlocal   = $d_s_local['idlistatel'];
    $local     = $d_s_local['tel_local'];
    $local_s   = "<b>ID:</b> $idlocal; <b>Local:</b> $local";

    $obs_listatel  = empty( $obs_listatel ) ? '' : tratastring( $obs_listatel, 'U', FALSE );

    if ( empty( $obs_listatel ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Observação em branco. Operação cancelada ( CADASTRAMENTO DE OBS - LISTA DE TELEFONES ). \n\n[ LOCALIDADE ]\n $local_s \n\n Página: $pag";
        salvaLog($mensagem);
        $saida = msg_js( '', 2 );
        if ( !empty( $targ ) ){
            $saida = msg_js( 'FALHA!', 'f' );
        }
        echo $saida;
        exit;
    }

    $obs_listatel = "'" . $obs_listatel . "'";

    $query_obs = "INSERT INTO
                    `obs_listatel`
                    (
                      `cod_listatel`,
                      `obs_listatel`,
                      `user_add`,
                      `data_add`,
                      `ip_add`
                    )
                  VALUES
                    (
                      $idlt,
                      $obs_listatel,
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

        $q_s_obs = "SELECT
                      `obs_listatel`
                    FROM
                      `obs_listatel`
                    WHERE
                      `id_obs_listatel` = $lastid
                    LIMIT 1";

            // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $q_s_obs = $model->query( $q_s_obs );

        $d_s_obs = $q_s_obs->fetch_assoc();

        $obs_s   = $d_s_obs['obs_listatel'];
        $obs_l_s = "<b>ID:</b> $lastid; <b>Observação:</b> $obs_s";

        $mensagem = "[ CADASTRAMENTO DE OBSERVAÇÃO DE TELEFONE ]\n Cadastramento de observação de localidade da lista de telefones. \n\n[ LOCALIDADE ]\n $local_s \n\n[ OBSERVAÇÃO ]\n $obs_l_s \n";

    } else {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de cadastramento de observação de localidade da lista de telefones. \n\n[ LOCALIDADE ]\n $local_s. \n\n $valor_user \n";

    }


    // fechando a conexao
    $model->closeConnection();

    salvaLog( $mensagem );

    $saida = msg_js( '', 2 );
    if ( !empty( $targ ) ){
        $saida = "<script type='text/javascript'> window.opener.location.reload(); opener.location.href='../listatel/detallistatel.php?idlt=$idlt'; self.window.close();</script>";
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
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Número de procedimento em branco ou inválido (OBS DE DETENTO).";
    salvaLog($mensagem);

    $saida = msg_js( 'FALHA!', 2 );
    if ( !empty( $targ ) ){
        $saida = msg_js( 'FALHA!', 'f' );
    }
    echo $saida;
    exit;
}

?>
</body>
</html>


