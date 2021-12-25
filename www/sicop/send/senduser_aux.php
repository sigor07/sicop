<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_ajax.php';

$pag = link_pag();
$tipo = '';
$msg_falha = '<p class="q_error">FALHA!</p>';
$mensagem = '';

/* colocar o tipo de página, o setor que ela acessa
 * ex: PECÚLIO, CADASTRO, INCLUSÃO...
 */
$tipo_pag = 'USUÁRIO';

/*
 * define a variavel $n_acesso de acordo com o setor
 */

$n_acesso = get_session( 'n_admsist', 'int' );
if ( empty( $n_acesso ) or $n_acesso < 3 ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'perm';
    $msg['entre_ch'] = $tipo_pag;
    get_msg( $msg, 1 );

    echo $msg_falha;

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

$proced = empty( $proced ) ? '' : (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO

if ( empty( $proced ) or $proced > 2 ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "Número de procedimento em branco ou inválido. Operação cancelada ( $tipo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo $msg_falha;

    exit;
}

$user = get_session( 'user_id', 'int' );
$ip = "'" . $_SERVER['REMOTE_ADDR'] . "'";

if ( $proced == 1 ) { // ATUALIZAÇÃO
/*
 * -------------------------------------------------------------------
 * PARTE DO CODIGO RESPONSAVEL PELO RESET DE SENHA
 * -------------------------------------------------------------------
 */

    /*
     * aqui coloca a função da página
     */
    $proced_tipo_pag = 'DEFINIR SENHA PADRÃO - ' . $tipo_pag;

    $iduser = empty( $iduser ) ? '' : (int)$iduser;
    if ( empty( $iduser ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Identificador do usuário em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;

    }

    // pegar os dados do usuário
    $d_user = dados_user( $iduser );

    $query = "UPDATE `sicop_users` SET `senha` = sha1(123456), `user_up` = $user, `data_up` = NOW(), `ip_up` = $ip WHERE `iduser` = $iduser";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query = $model->query( $query );

    // fechando a conexao
    $model->closeConnection();

    if ( !$query ) {

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de atualização de usuário ( $tipo_pag ). \n\n $d_user \n\n $valor_user.";
        $msg['linha'] = __LINE__;

        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;

    }

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'SENHA DE USUÁRIO RESETADA';
    $msg['text']     = "Senha de usuário resetada. \n\n $d_user";

    get_msg( $msg, 1 );

    echo 1;

    exit;

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELO RESET DE SENHA
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

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'perm';
        $msg['entre_ch'] = $proced_tipo_pag;
        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;

    }

    $iduser = empty( $iduser ) ? '' : (int)$iduser;
    if ( empty( $iduser ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Identificador do usuário em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;

    }

    // pegar os dados do usuário
    $d_user = dados_user( $iduser );

    $query = "DELETE FROM `sicop_users` WHERE `iduser` = $iduser";

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
        $msg['text'] = "Erro de exclusão ( $tipo_pag ). \n\n $d_user \n\n $valor_user.";
        $msg['linha'] = __LINE__;

        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;

    }

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'desc';
    $msg['entre_ch'] = 'EXCLUSÃO DE USUÁRIO';
    $msg['text'] = "Exclusão de usuário. \n\n $d_user";

    get_msg( $msg, 1 );

    echo 2;

    exit;

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE DO CODIGO RESPONSAVEL PELA EXCLUSÃO
 * -------------------------------------------------------------------
 */
}
?>
</body>
</html>
