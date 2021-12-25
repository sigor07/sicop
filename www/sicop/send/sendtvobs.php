<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    extract($_POST, EXTR_OVERWRITE);

    $targ   = empty($targ) ? '0' : '1';
    $proced = (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO
    $iddet  = empty( $iddet ) ? '' : (int)$iddet;
    $idtv   = empty( $idtv ) ? '' : (int)$idtv;

    $user   = get_session( 'user_id', 'int' );
    $ip     = "'" . $_SERVER['REMOTE_ADDR'] . "'";

    if ( empty( $iddet ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. Operação cancelada (OBS TV).\n\n Página: $pag";
        salvaLog($mensagem);

        $ret = 2;
        if ( !empty ( $targ ) ) $ret = 'f';
        echo msg_js( 'FALHA!!!', $ret );

        exit;
    }

    if ( empty( $idtv ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Identificador da TV em branco. Operação cancelada (OBS TV).\n\n Página: $pag";
        salvaLog($mensagem);

        $ret = 2;
        if ( !empty ( $targ ) ) $ret = 'f';
        echo msg_js( 'FALHA!!!', $ret );

        exit;
    }

    // pegar os dados do preso
    $detento = dados_det( $iddet );

    // pegar os dados da TV
    $tv = dados_tv( $idtv, 1 );

    if ( $proced == 1 ) { // ATUALIZAÇÃO
/*
 * -----------------------------------------------------------
 * PARTE RESPONSAVEL PELA ATUALIZAÇÃO
 * -----------------------------------------------------------
 */

        $id_obs_tv = empty( $id_obs_tv ) ? '' : (int)$id_obs_tv;

        if (empty($id_obs_tv)) {
            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Identificador da observação em branco. Operação cancelada (atualização - OBS TV).\n\n Página: $pag";
            salvaLog($mensagem);
            echo msg_js( 'FALHA!!!', 2 );
            exit;
        }

        $obs_tv = empty($obs_tv) ? 'NULL' : "'" . tratastring( $obs_tv, 'U', false ) . "'";

        $query_obs = "UPDATE
                         `obs_tv`
                      SET
                         `obs_tv` = $obs_tv,
                         `user_up` = $user,
                         `data_up` = NOW(),
                         `ip_up` = $ip
                      WHERE
                         `id_obs_tv` = $id_obs_tv
                      LIMIT 1";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $query_obs = $model->query( $query_obs );

        // fechando a conexao
        $model->closeConnection();

        if ( $query_obs ) {

            $mensagem = "[ ATUALIZAÇÃO DE OBSERVAÇÃO DE TV ]\n Atualização de observação de TV: ID: $id_obs_tv;\n Observação: $obs_tv. \n\n $tv \n\n $detento \n";
            salvaLog($mensagem);
            echo msg_js( '', 2 );

        } else {

            /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
            $valor_user = valor_user( $_POST );

            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de atualização de observação de TV.\n\n $tv \n\n $detento.\n\n $valor_user";
            salvaLog($mensagem);
            echo msg_js( 'FALHA!!!', 2 );

        }

        exit;

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELA ATUALIZAÇÃO
 * -------------------------------------------------------------------
 */
    } else if ( $proced == 2 ) { //EXCLUSÃO
/*
 * -------------------------------------------------------------------
 * PARTE RESPONSAVEL PELA EXCLUSÃO
 * -------------------------------------------------------------------
 */
        if (empty($id_obs_tv)) {
            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Identificador da observação em branco. Operação cancelada (exclusão  - OBS TV).\n\n Página: $pag";
            salvaLog($mensagem);
            echo msg_js( 'FALHA!!!', 2 );
            exit;
        }

        $query_obs = "DELETE FROM `obs_tv` WHERE `id_obs_tv` = $id_obs_tv LIMIT 1";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $query_obs = $model->query( $query_obs );

        // fechando a conexao
        $model->closeConnection();

        if ( $query_obs ) {

            $mensagem = "[ EXCLUSÃO DE OBSERVAÇÃO DE TV ]\n Exclusão de observação de TV: ID: $id_obs_tv. \n\n $tv \n\n $detento \n";
            salvaLog($mensagem);
            echo msg_js( '', 2 );

        } else {

            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de exclusão de observação de TV. ID: $id_obs_tv. \n\n $tv \n\n $detento";
            salvaLog($mensagem);
            echo msg_js( 'FALHA!!!', 2 );

        }

        exit;

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELA EXCLUSÃO
 * -------------------------------------------------------------------
 */
    } else if ( $proced == 3 ) { //CADASTRAMENTO
/*
 * -------------------------------------------------------------------
 * PARTE RESPONSAVEL PELO CADASTRAMENTO
 * -------------------------------------------------------------------
 */
        $obs_tv = empty($obs_tv) ? 'NULL' : "'" . tratastring( $obs_tv, 'U', false ) . "'";

        $query_obs = "INSERT INTO `obs_tv`
                        (`cod_tv`,
                         `obs_tv`,
                         `user_add`,
                         `data_add`,
                         `ip_add`)
                      VALUES
                        ($idtv,
                         $obs_tv,
                         $user,
                         NOW(),
                         $ip)";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $query_obs = $model->query( $query_obs );

        if ( $query_obs ) {

            $lastid = $model->lastInsertId();
            $mensagem = "[ CADASTRAMENTO DE OBSERVAÇÃO DE TV ]\n Cadastro de observação de TV: ID: $lastid;\n Observação: $obs_tv. \n\n $tv \n\n $detento \n";
            salvaLog($mensagem);

            $ret = 2;
            if ( !empty ( $targ ) ) $ret = 'f';
            echo msg_js( '', $ret );

        } else {

            /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
            $valor_user = valor_user( $_POST );

            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de cadastramento de observação de TV.\n\n $tv \n\n $detento.\n\n $valor_user \n.";
            salvaLog($mensagem);

            $ret = 2;
            if ( !empty ( $targ ) ) $ret = 'f';
            echo msg_js( 'FALHA!!!', $ret );

        }

        // fechando a conexao
        $model->closeConnection();

        exit;

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELO CADASTRAMENTO
 * -------------------------------------------------------------------
 */
    } else {

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Número de procedimento em branco ou inválido (OBS TV).";
        salvaLog($mensagem);

        $ret = 2;
        if ( !empty ( $targ ) ) $ret = 'f';
        echo msg_js( 'FALHA!!!', $ret );

        exit;
    }

} else {

    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso direto à página de cadastro de observação de TV.\n\n Página: $pag";
    salvaLog($mensagem);
    header('Location: ../home.php');
    exit;

}
?>
</body>
</html>