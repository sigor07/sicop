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
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso à página de manipulação de dados processuais d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " SEM PERMISSÕES. \n\n Página: $pag";
    salvaLog($mensagem);
    exit;
}

$is_post = is_post();

if ( !$is_post ) {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso direto à página de manipulação de dados processuais d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . ".<br /><br /> Página: $pag";
    salvaLog($mensagem);
    echo msg_js( '', 1 );
    exit;
}

extract( $_POST, EXTR_OVERWRITE );

$iddet = empty( $iddet ) ? '' : (int)$iddet;

if ( empty( $iddet ) ) {
    $mensagem = "ERRO -> Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. Operação cancelada.\n\n Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( 'FALHA!', 1 );
    exit;
}

$user         = get_session( 'user_id', 'int' );
$ip           = "'" . $_SERVER['REMOTE_ADDR'] . "'";

// instanciando o model
$model = SicopModel::getInstance();

$artigo        = empty( $artigo ) ? 'NULL' : "'" . (int)$artigo . "'";
$execucao      = empty( $execucao ) ? 'NULL' : "'" . (int)preg_replace( '/[-.]/', '', $execucao ) . "'";
$primario      = empty( $primario ) ? 0 : 1;
$sit_proc      = empty( $sit_proc ) ? 'NULL' : "'" . (int)$sit_proc . "'";
$conduta_ant   = empty( $conduta_ant ) ? 'NULL' : "'" . (int)$conduta_ant . "'";
$data_reab     = empty( $data_reab ) ? 'NULL' : "'" . $model->escape_string( $data_reab ) . "'";
$local_prisao  = empty( $local_prisao ) ? 'NULL' : "'" . (int)$local_prisao . "'";
$data_prisao   = empty( $data_prisao ) ? 'NULL' : "'" . $model->escape_string( $data_prisao ) . "'";
$motivo_prisao = empty( $motivo_prisao ) ? 'NULL' : "'" . tratastring( $motivo_prisao, 'U', false ) . "'";

// pegar os dados do preso
$detento = dados_det( $iddet );

$query_up_det = "UPDATE
                   `detentos`
                 SET
                   `cod_artigo` = $artigo,
                   `execucao` = $execucao,
                   `data_prisao` = STR_TO_DATE( $data_prisao, '%d/%m/%Y' ),
                   `cod_local_prisao` = $local_prisao,
                   `primario` = $primario,
                   `cod_sit_proc` = $sit_proc,
                   `motivo_prisao` = $motivo_prisao,
                   `conduta_ant` = $conduta_ant,
                   `data_reab` = STR_TO_DATE($data_reab, '%d/%m/%Y'),
                   `user_up` = $user,
                   `data_up` = NOW(),
                   `ip_up` = $ip
                 WHERE
                   `iddetento` = $iddet
                 LIMIT 1";

// executando a query
$query_up_det = $model->query( $query_up_det );

$success = TRUE;
if( $query_up_det ) {

    $mensagem = "[ ATUALIZAÇÃO DE DADOS PROCESSUAIS DE " . SICOP_DET_DESC_U . " ]\n Atualização de dados processuais de " . SICOP_DET_DESC_L . ". \n\n $detento";

} else {

    $success = FALSE;

    /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
    $valor_user = valor_user( $_POST );

    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de atualização de dados processuais de " . SICOP_DET_DESC_L . ". \n\n $valor_user \n\n $detento.";

}

// fechando a conexao
$model->closeConnection();

salvaLog( $mensagem );

$msg = '';
if ( !$success ) $msg = 'FALHA!!!';

echo msg_js( "$msg", 2 );

exit;

?>