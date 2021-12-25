<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag = link_pag();
$tipo = '';
$mensagem = '';

$tipo_pag = 'ATENDIMENTO INTERNO';

/*
 * define a variavel $n_acesso de acordo com o setor
 */

$n_acesso = get_session( '', 'int' );
if ( empty( $n_acesso ) or $n_acesso < 3 ) {

    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'perm';
    $msg['entre_ch'] = $tipo_pag;
    get_msg( $msg, 1 );

    exit;
}

$is_post = is_post();
if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( $tipo_pag ).";
    get_msg( $msg, 1 );

    redir( 'home' );

    exit;
}

extract( $_POST, EXTR_OVERWRITE );

$targ = empty( $targ ) ? 0 : 1;
$proced = empty( $proced ) ? '' : (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO

if ( empty( $proced ) or $proced > 3 ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "Número de procedimento em branco ou inválido. Operação cancelada ( $tipo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    $ret = 1;
    if ( !empty( $targ ) ) $ret = 'f';
    echo msg_js( 'FALHA!', $ret );

    exit;
}

$user = get_session( 'user_id', 'int' );
$ip = "'" . $_SERVER['REMOTE_ADDR'] . "'";

if ( $proced == 1 ) { // ATUALIZAÇÃO
/*
 * -------------------------------------------------------------------
 * PARTE RESPONSAVEL PELA ATUALIZAÇÃO
 * -------------------------------------------------------------------
 */

    /*
     * aqui coloca a função da página
     */
    $proced_tipo_pag = 'ATUALIZAÇÃO - ' . $tipo_pag;


    /*
     *
     *
     *
     * ESPAÇO PARA COLOCAR A QUERY E VALIDAÇÕES
     *
     *
     *
     */

    $ret = 2;
    if ( !empty( $targ ) ) $ret = 'f';

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
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Erro de atualização ( $tipo_pag ). \n\n /*dados do que foi manipulado*/ \n\n $detento \n\n $valor_user \n";
        $msg['linha'] = __LINE__;

        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', $ret );

        exit;

    }

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'desc';
    $msg['entre_ch'] = 'ATUALIZAÇÃO DE ';
    $msg['text'] = "Atualização de . \n\n /*dados do que foi manipulado*/ \n\n $detento ";

    get_msg( $msg, 1 );

    echo msg_js( '', $ret );

    exit;

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELA ATUALIZAÇÃO
 * -------------------------------------------------------------------
 */
} else if ( $proced == 2 ) { //EXCLUSÃO
/*
 * -------------------------------------------------------------------
 * PARTE DO CODIGO RESPONSAVEL PELA EXCLUSÃO
 * -------------------------------------------------------------------
 */

    /*
     * aqui coloca a função da página
     */
    $proced_tipo_pag = 'EXCLUSÃO - ' . $tipo_pag;

    if ( empty( $n_acesso ) or $n_acesso < 4 ) {

        $tipo = 0;
        include '../init/msgnopag.php';

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'perm';
        $msg['entre_ch'] = $proced_tipo_pag;
        get_msg( $msg, 1 );

        exit;
    }

    /*
     *
     *
     *
     * ESPAÇO PARA COLOCAR A QUERY E VALIDAÇÕES
     *
     *
     *
     */

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
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Erro de exclusão ( $tipo_pag ). \n\n /*dados do que foi manipulado*/ \n\n $detento \n\n $valor_user \n";
        $msg['linha'] = __LINE__;

        $mensagem = get_msg( $msg );
    }

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'desc';
    $msg['entre_ch'] = 'EXCLUSÃO DE ';
    $msg['text'] = "Exclusão de . \n\n /*dados do que foi manipulado*/ \n\n $detento ";

    $mensagem = get_msg( $msg );

    salvaLog( $mensagem );

    $msg_saida = '';
    if ( !$success ) $msg_saida = 'FALHA!!!';

    $ret = 1;
    if ( !empty( $targ ) ) $ret = 'f';

    echo msg_js( $msg_saida, $ret );

    exit;

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE DO CODIGO RESPONSAVEL PELA EXCLUSÃO
 * -------------------------------------------------------------------
 */
} else if ( $proced == 3 ) { //CADASTRAMENTO
/*
 * -------------------------------------------------------------------
 * PARTE DO CODIGO RESPONSAVEL PELO CADASTRAMENTO
 * -------------------------------------------------------------------
 */

    /*
     * aqui coloca a função da página
     */
    $proced_tipo_pag = 'CADASTRAMENTO - ' . $tipo_pag;

    $iddet = empty( $iddet ) ? '' : (int)$iddet;

    if ( empty( $iddet ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Identificador do detento em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 1;

        echo msg_js( 'FALHA!', $ret );

        exit;
    }

    // pegar os dados do preso
    $detento = dados_det( $iddet );




    /*
     *
     *
     *
     * ESPAÇO PARA COLOCAR A QUERY E VALIDAÇÕES
     *
     *
     *
     */

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query = $model->query( $query );

    $success = TRUE;
    if ( !$query ) {

        $success = FALSE;

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Erro de cadastramento ( $tipo_pag ). \n\n $detento \n\n $valor_user \n.";
        $msg['linha'] = __LINE__;

        $mensagem = get_msg( $msg );

    }

    $lastid = $model->lastInsertId();

    // fechando a conexao
    $model->closeConnection();

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'desc';
    $msg['entre_ch'] = 'CADASTRAMENTO DE ';
    $msg['text'] = "Cadastramento de . \n\n /*dados do que foi manipulado*/ \n\n $detento ";

    $mensagem = get_msg( $msg );

    salvaLog( $mensagem );

    $msg_saida = '';
    if ( !$success ) $msg_saida = 'FALHA!!!';

    $ret = 2;
    if ( !empty( $targ ) ) $ret = 'f';

    echo msg_js( $msg_saida, $ret );

    exit;

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELO CADASTRAMENTO
 * -------------------------------------------------------------------
 */
}
?>
</body>
</html>
