<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';
$mensagem = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    extract($_POST, EXTR_OVERWRITE);

    $targ    = empty($targ) ? '0' : '1';
    $proced  = (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO
    $iddet   = empty( $iddet ) ? '' : (int)$iddet;
    $idradio = empty( $idradio ) ? '' : (int)$idradio;

    $user    = get_session( 'user_id', 'int' );
    $ip      = "'" . $_SERVER['REMOTE_ADDR'] . "'";

    if ( empty( $iddet ) ) {

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. Operação cancelada (OBS RÁDIO).\n\n Página: $pag";
        salvaLog($mensagem);

        $ret = 2;
        if ( !empty ( $targ ) ) $ret = 'f';
        echo msg_js( 'FALHA!!!', $ret );

        exit;

    }

    if ( empty( $idradio ) ) {

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Identificador do rádio em branco. Operação cancelada (OBS RÁDIO).\n\n Página: $pag";
        salvaLog($mensagem);

        $ret = 2;
        if ( !empty ( $targ ) ) $ret = 'f';
        echo msg_js( 'FALHA!!!', $ret );

        exit;

    }

    // pegar os dados do preso
    $detento = dados_det( $iddet );

    // pegar os dados do rádio
    $radio = dados_radio( $idradio, 1 );

    if ( $proced == 1 ) { // ATUALIZAÇÃO
/*
 * -----------------------------------------------------------
 * PARTE RESPONSAVEL PELA ATUALIZAÇÃO
 * -----------------------------------------------------------
 */

        $id_obs_radio = empty( $id_obs_radio ) ? '' : (int)$id_obs_radio;

        if ( empty( $id_obs_radio ) ) {
            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Identificador da observação em branco. Operação cancelada (atualização - OBS RÁDIO).\n\n Página: $pag";
            salvaLog( $mensagem );
            echo msg_js( 'FALHA!!!', 2 );
            exit;
        }

        $obs_radio = empty($obs_radio) ? 'NULL' : "'" . tratastring( $obs_radio, 'U', false ) . "'";

        $query_obs = "UPDATE
                         `obs_radio`
                      SET
                         `obs_radio` = $obs_radio,
                         `user_up` = $user,
                         `data_up` = NOW(),
                         `ip_up` = $ip
                      WHERE
                         `id_obs_radio` = $id_obs_radio
                      LIMIT 1";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $query_obs = $model->query( $query_obs );

        // fechando a conexao
        $model->closeConnection();

        if ( $query_obs ) {

            $mensagem = "[ ATUALIZAÇÃO DE OBSERVAÇÃO DE RÁDIO ]\n Atualização de observação de rádio: ID: $id_obs_radio;\n Observação: $obs_radio. \n\n $radio \n\n $detento \n";
            salvaLog($mensagem);
            echo msg_js( '', 2 );

        } else {

            /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
            $valor_user = valor_user( $_POST );

            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de atualização de observação de rádio.\n\n $radio \n\n $detento.\n\n $valor_user \n";
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
        if (empty($id_obs_radio)) {
            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Identificador da observação em branco. Operação cancelada (exclusão  - OBS RÁDIO).\n\n Página: $pag";
            salvaLog($mensagem);
            echo msg_js( 'FALHA!!!', 2 );
            exit;
        }

        $query_obs = "DELETE FROM `obs_radio` WHERE `id_obs_radio` = $id_obs_radio LIMIT 1";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $query_obs = $model->query( $query_obs );

        // fechando a conexao
        $model->closeConnection();

        if($query_obs) {

            $mensagem = "[ EXCLUSÃO DE OBSERVAÇÃO DE RÁDIO ]\n Exclusão de observação de rádio: ID: $id_obs_radio. \n\n $radio \n\n $detento \n";
            salvaLog($mensagem);
            echo msg_js( '', 2 );

        } else {

            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de exclusão de observação de rádio. ID: $id_obs_radio. \n\n $radio \n\n $detento.\n\n";
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
        $obs_radio = empty($obs_radio) ? 'NULL' : "'" . tratastring( $obs_radio, 'U', false ) . "'";

        $query_obs = "INSERT INTO `obs_radio`
                        (`cod_radio`,
                         `obs_radio`,
                         `user_add`,
                         `data_add`,
                         `ip_add`)
                      VALUES
                        ($idradio,
                         $obs_radio,
                         $user,
                         NOW(),
                         $ip)";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $query_obs = $model->query( $query_obs );

        if ( $query_obs ) {

            $lastid = $model->lastInsertId();
            $mensagem = "[ CADASTRAMENTO DE OBSERVAÇÃO DE RÁDIO ]\n Cadastro de observação de rádio: ID: $lastid;\n Observação: $obs_radio. \n\n $radio \n\n $detento \n";
            salvaLog($mensagem);

            $ret = 2;
            if ( !empty ( $targ ) ) $ret = 'rf';
            echo msg_js( '', $ret );

        } else {

            /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
            $valor_user = valor_user( $_POST );

            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de cadastramento de observação de rádio.\n\n $radio \n\n $detento.\n\n $valor_user \n";
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
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Número de procedimento em branco ou inválido (OBS RÁDIO).";
        salvaLog($mensagem);

        $ret = 2;
        if ( !empty ( $targ ) ) $ret = 'f';
        echo msg_js( 'FALHA!!!', $ret );

        exit;
    }

} else {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso direto à página de cadastro de observação de rádio.\n\n Página: $pag";
    salvaLog($mensagem);
    header('Location: ../home.php');
    exit;
}
?>
</body>
</html>


