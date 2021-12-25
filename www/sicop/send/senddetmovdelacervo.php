<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';
$mensagem = '';

/* colocar o tipo de página, o setor que ela acessa
 * ex: PECÚLIO, CADASTRO, INCLUSÃO...
 */
$tipo_pag = 'EXCLUSÃO MOVIMENTAÇÃO DE DETENTO - ACERVO';

/*
 * define a variavel $n_acesso de acordo com o setor
 */

$n_acesso = get_session( 'n_cadastro', 'int' );
if ( empty( $n_acesso ) or $n_acesso < 4 ) {

    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $tipo_pag;
    get_msg( $msg, 1 );

    exit;

}

$is_post = is_post();
if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( $tipo_pag ).";
    get_msg( $msg, 1 );

    redir( 'home' );

    exit;

}

extract( $_POST, EXTR_OVERWRITE );

$user = get_session( 'user_id', 'int' );
$ip   = "'" . $_SERVER['REMOTE_ADDR'] . "'";

/*
 * aqui coloca a função da página
 */
$proced_tipo_pag = 'EXCLUSÃO - ' . $tipo_pag;

$idmov = empty( $idmov ) ? '' : (int)$idmov;
if ( empty( $idmov ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Identificador da movimentação em branco. Operação cancelada ( $proced_tipo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );

    exit;

}

// pegar os dados da movimentação
$mov = dados_mov( $idmov, 1 );

// pegar os dados do preso
$where_det = "( SELECT `cod_detento` FROM `mov_det` WHERE `id_mov` = $idmov LIMIT 1 )";
$detento = dados_det( $where_det );


$query = "DELETE FROM `mov_det` WHERE `id_mov` = $idmov LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query = $model->query( $query );

// fechando a conexao
$model->closeConnection();

$success = TRUE;
if ( !$query ) {

    $success = FALSE;

    /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
    $valor_user = valor_user( $_POST );

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Erro de exclusão de movimentação de deteno no acervo ( $tipo_pag ). \n\n $detento \n\n $valor_user";
    $msg['linha'] = __LINE__;

    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );

    exit;

}

// montar a mensagem q será salva no log
$msg = array();
$msg['tipo']     = 'desc';
$msg['entre_ch'] = 'EXCLUSÃO DE MOVIMENTAÇÃO DE DETENTO - ACERVO';
$msg['text']     = "Exclusão de movimentação de deteno no acervo. \n\n $mov \n\n $detento ";

get_msg( $msg, 1 );

echo msg_js( '', 1 );

exit;

?>
</body>
</html>
