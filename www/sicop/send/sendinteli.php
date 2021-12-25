<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    extract($_POST, EXTR_OVERWRITE);

    $proced = empty( $proced ) ? '' : (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ADICIONAR ; 2 = EXCLUIR
    $iddet  = empty( $iddet ) ? '' : (int)$iddet;

    $user   = get_session( 'user_id', 'int' );
    $ip     = "'" . $_SERVER['REMOTE_ADDR'] . "'";

    if ( empty( $iddet ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Identificador d" . SICOP_DET_ART_L . SICOP_DET_DESC_L . " em branco. Operação cancelada (INTELIGÊNCIA).\n\n Página: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!', 2 );
        exit;
    }

    if ( empty( $proced ) or $proced > 2 ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Número de procedimento em branco. Operação cancelada (INTELIGÊNCIA).\n\n Página: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!', 2 );
        exit;
    }

    // pegar os dados do preso
    $detento = dados_det( $iddet );

    $q_inteli = '';
    if ( $proced == 1 ){

        $q_inteli =    "INSERT INTO `inteligencia` ( `cod_detento`, `user_add`, `data_add`, `ip_add` ) VALUES ( $iddet, $user, NOW(), $ip )";

    } else if ( $proced == 2 ){

        $idinteli = empty( $idinteli ) ? '' : (int)$idinteli;

        if ( empty( $idinteli ) ) {
            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Identificador da inteligência em branco. Operação cancelada (EXCLUSÃO).\n\n Página: $pag";
            salvaLog($mensagem);
            echo msg_js( 'FALHA!', 2 );
            exit;
        }

        $q_inteli = "DELETE FROM `inteligencia` WHERE `idinteli` = $idinteli LIMIT 1";

    } else {

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Número de procedimento em inválido. Operação cancelada (INTELIGÊNCIA).\n\n Página: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_inteli = $model->query( $q_inteli );

    // fechando a conexao
    $model->closeConnection();

    if( $q_inteli ) {

        $mensagem = "[ INCLUSÃO DE DETENTO NO MONITORAMENTO DA INTELIGÊNCIA ]\n " . SICOP_DET_DESC_FU . " incluído na lista de monitoramento da inteligência \n\n $detento \n ";

        if ( $proced == '2' ){
            $mensagem = "[ EXCLUSÃO DE DETENTO NO MONITORAMENTO DA INTELIGÊNCIA ]\n " . SICOP_DET_DESC_FU . " excluído na lista de monitoramento da inteligência \n\n $detento \n ";
        }
        salvaLog($mensagem);
        echo msg_js( '', 2 );
        exit;

    } else {

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro em inclusão de " . SICOP_DET_DESC_L . " na lista de monitoramento da inteligência.\n\n $detento.";

        if ( $proced == '2' ){
            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro em exclusão de " . SICOP_DET_DESC_L . " da lista de monitoramento da inteligência.\n\n $detento.";
        }
        salvaLog($mensagem);
        echo msg_js( 'FALHA!', 1 );
        exit;
    }

} else {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso direto à página de inclusão/exclusão de " . SICOP_DET_DESC_L . "s da lista de monitoramento da inteligência.\n\n Página: $pag";
    salvaLog($mensagem);
    redir( 'home' );
    exit;
}
?>
</body>
</html>