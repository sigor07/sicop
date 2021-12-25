<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag = link_pag();
$tipo = '';
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

if ( empty( $proced ) or $proced > 4 ) {

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

    $iduser = empty( $iduser ) ? '' : (int)$iduser;
    if ( empty( $iduser ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Identificador do usuário em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 'f';
        echo msg_js( 'FALHA!', $ret );

        exit;

    }

    // pegar os dados do usuário
    $d_user = dados_user( $iduser );

    $nomeuser = empty( $nomeuser ) ? '' : $nomeuser;
    if ( empty( $nomeuser ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo'] = 'err';
        $msg['text'] = "Nome do usuário em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $d_user";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 'f';
        echo msg_js( 'FALHA!', $ret );

        exit;

    }

    $pcs_nome = preg_replace( '/\s?\b\w{1,2}\b/', null, $nomeuser );
    $pcs_nome = explode( ' ', $pcs_nome );
    $iniciais = '';
    foreach ( $pcs_nome as &$value ) {
        $iniciais .= mb_strtolower( substr( $value, 0, 1 ) );
    }

    // instanciando o model
    $model = SicopModel::getInstance();

    $nomeuser = "'" . tratastring( $nomeuser ) . "'";
    $iniciais = "'" . $model->escape_string( $iniciais ) . "'";

    $nome_cham = empty( $nome_cham ) ? '' : "'" . tratastring( $nome_cham ) . "'";
    if ( empty( $nome_cham ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Primeiro nome do usuário em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $d_user";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 'f';
        echo msg_js( 'FALHA!', $ret );

        exit;
    }

    $usuario = empty( $usuario ) ? '' : "'" . tratastring( $usuario, 'L' ) . "'";
    if ( empty( $usuario ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo'] = 'err';
        $msg['text'] = "Nome de acesso/login do usuário em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $d_user";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 'f';
        echo msg_js( 'FALHA!', $ret );

        exit;
    }

    $email = empty( $email ) ? 'NULL' : "'" . tratastring( $email, 'L' ) . "'";

    $cargo = empty( $cargo ) ? '' : "'" . tratastring( $cargo ) . "'";
    if ( empty( $cargo ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Cargo do usuário em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $d_user";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 'f';
        echo msg_js( 'FALHA!', $ret );

        exit;
    }

    $cod_setor = empty( $cod_setor ) ? '' : (int)$cod_setor;
    if ( empty( $cod_setor ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Identificador do setor do usuário em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $d_user";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 'f';
        echo msg_js( 'FALHA!', $ret );

        exit;
    }

    $rsuser = empty( $rsuser ) ? 'NULL' : "'" . (int)$rsuser . "'";
    $ativo = empty( $ativo ) ? 0 : 1;


    $query = "UPDATE
                `sicop_users`
              SET
                `nomeuser` = $nomeuser,
                `nome_cham` = $nome_cham,
                `usuario` = $usuario,
                `email` = $email,
                `cargo` = $cargo,
                `cod_setor` = $cod_setor,
                `iniciais` = $iniciais,
                `rsuser` = $rsuser,
                `ativo` = $ativo,
                `user_up` = $user,
                `data_up` = NOW(),
                `ip_up` = $ip
              WHERE
                `iduser` = $iduser
              LIMIT 1";

    $ret = 2;
    if ( !empty( $targ ) ) $ret = 'f';

    // executando a query
    $query = $model->query( $query );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if( !$query ) {

        $success = FALSE;

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Erro de atualização ( $tipo_pag ). \n\n $d_user \n\n $valor_user.";
        $msg['linha'] = __LINE__;

        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!!!', $ret );

        exit;

    }

    $msg = array( );
    $msg['tipo'] = 'desc';
    $msg['entre_ch'] = 'ATUALIZAÇÃO DE USUÁRIO';
    $msg['text'] = "Atualização de dados de usuário . \n\n $d_user ";

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

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'perm';
        $msg['entre_ch'] = $proced_tipo_pag;
        get_msg( $msg, 1 );

        echo 0;

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

        echo 0;

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
    if( !$query ) {

        $success = FALSE;

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Erro de exclusão ( $tipo_pag ). \n\n $d_user \n\n $valor_user.";
        $msg['linha'] = __LINE__;

        get_msg( $msg, 1 );

        echo 0;

        exit;

    }

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'desc';
    $msg['entre_ch'] = 'EXCLUSÃO DE USUÁRIO';
    $msg['text'] = "Exclusão de usuário. \n\n $d_user";

    get_msg( $msg, 1 );

    echo 1;

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

    $nomeuser = empty( $nomeuser ) ? '' : $nomeuser;
    if ( empty( $nomeuser ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo'] = 'err';
        $msg['text'] = "Nome do usuário em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 'f';
        echo msg_js( 'FALHA!', $ret );

        exit;

    }

    $pcs_nome = preg_replace( '/\s?\b\w{1,2}\b/', null, $nomeuser );
    $pcs_nome = explode( ' ', $pcs_nome );
    $iniciais = '';
    foreach ( $pcs_nome as &$value ) {
        $iniciais .= mb_strtolower( substr( $value, 0, 1 ) );
    }

    // instanciando o model
    $model = SicopModel::getInstance();

    $nomeuser = "'" . tratastring( $nomeuser ) . "'";
    $iniciais = "'" . $model->escape_string( $iniciais ) . "'";

    $nome_cham = empty( $nome_cham ) ? '' : "'" . tratastring( $nome_cham ) . "'";
    if ( empty( $nome_cham ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Primeiro nome do usuário em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 'f';
        echo msg_js( 'FALHA!', $ret );

        exit;
    }

    $usuario = empty( $usuario ) ? '' : "'" . tratastring( $usuario, 'L' ) . "'";
    if ( empty( $usuario ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo'] = 'err';
        $msg['text'] = "Nome de acesso/login do usuário em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 'f';
        echo msg_js( 'FALHA!', $ret );

        exit;
    }

    $senha = empty( $senha ) ? '' : "'" . $model->escape_string( $senha ) . "'";
    if ( empty( $senha ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Senha do usuário em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 'f';
        echo msg_js( 'FALHA!', $ret );

        exit;
    }

    $email = empty( $email ) ? 'NULL' : "'" . tratastring( $email, 'L' ) . "'";

    $cargo = empty( $cargo ) ? '' : "'" . tratastring( $cargo ) . "'";
    if ( empty( $cargo ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Cargo do usuário em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 'f';
        echo msg_js( 'FALHA!', $ret );

        exit;
    }

    $cod_setor = empty( $cod_setor ) ? '' : (int)$cod_setor;
    if ( empty( $cod_setor ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Identificador do setor do usuário em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 'f';
        echo msg_js( 'FALHA!', $ret );

        exit;
    }

    $rsuser = empty( $rsuser ) ? 'NULL' : "'" . (int)$rsuser . "'";
    $ativo = empty( $ativo ) ? 0 : 1;


    $query = "INSERT INTO
                `sicop_users`
                (
                  `nomeuser`,
                  `nome_cham`,
                  `usuario`,
                  `senha`,
                  `email`,
                  `cargo`,
                  `cod_setor`,
                  `iniciais`,
                  `rsuser`,
                  `ativo`,
                  `user_add`,
                  `data_add`,
                  `ip_add`
                )
              VALUES
                (
                  $nomeuser,
                  $nome_cham,
                  $usuario,
                  SHA1( $senha ),
                  $email,
                  $cargo,
                  $cod_setor,
                  $iniciais,
                  $rsuser,
                  $ativo,
                  $user,
                  NOW(),
                  $ip
                )";

    $ret = 2;
    if ( !empty( $targ ) ) $ret = 'f';

    // executando a query
    $query = $model->query( $query );

    $success = TRUE;
    if( !$query ) {

        $success = FALSE;

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de cadastramento ( $tipo_pag ). \n\n $valor_user.";
        $msg['linha'] = __LINE__;

        get_msg( $msg, 1 );

        $int_err_mysql = $model->getErrorNum();
        $msg_falha     = 'FALHA!';
        if ( $int_err_mysql == 1062 ) {

            $msg_falha = 'Nome de acesso já cadastrado! Verifique!';
            if ( empty( $targ ) ) $ret = 1;

        }

        echo msg_js( $msg_falha, $ret );

        exit;

    }

    $lastid = $model->lastInsertId();

    // fechando a conexao
    $model->closeConnection();

    // pegar os dados do usuário
    $sys_user = dados_user( $lastid );

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'CADASTRAMENTO DE USUÁRIO';
    $msg['text']     = "Cadastramento de usuário. \n\n $sys_user";

    get_msg( $msg, 1 );

    echo msg_js( '', $ret );

    exit;

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELO CADASTRAMENTO
 * -------------------------------------------------------------------
 */
} else if ( $proced == 4 ) { //CADASTRAMENTO
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

        echo 0;

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

    $success = TRUE;
    if ( !$query ) {

        $success = FALSE;

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de atualização de usuário ( $tipo_pag ). \n\n $d_user \n\n $valor_user.";
        $msg['linha'] = __LINE__;

        get_msg( $msg, 1 );

        echo 0;

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
}
?>
</body>
</html>
