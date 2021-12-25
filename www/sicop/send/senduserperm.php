<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_ajax.php';

$pag = link_pag();
$tipo = '';
$msg_falha = 0;
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

    echo $msg_falha;

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

    echo $msg_falha;

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
    $proced_tipo_pag = 'ATUALIZAÇÃO DE PERMISSÃO - ' . $tipo_pag;

    $idperm = empty( $idperm ) ? '' : (int)$idperm ;
    if ( empty( $idperm ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Identificador da permissão em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;
    }

    //dados do usuário
    $where = "( SELECT `cod_user` FROM `sicop_users_perm` WHERE `idpermissao` = $idperm LIMIT 1 )";
    $d_user = dados_user( $where );

    //dados da permissão
    $d_perm_old = dados_perm( $idperm );

    $n_nivel = empty( $n_nivel ) ? '' : (int)$n_nivel ;
    if ( empty( $n_nivel ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Identificador do novo nível de acesso em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $d_user";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;
    }

    $query = "UPDATE
                `sicop_users_perm`
              SET
                `cod_nivel` = $n_nivel
              WHERE
                `idpermissao` = $idperm
              LIMIT 1";

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
        $msg['text']  = "Erro de atualização ( $tipo_pag ). \n\n $d_user \n\n $d_perm_old \n\n $valor_user.";
        $msg['linha'] = __LINE__;

        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;

    }

    //dados da permissão atualizada
    $d_perm_new = dados_perm( $idperm );

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'ATUALIZAÇÃO DE PERMISSÃO DE USUÁRIO';
    $msg['text']     = "Atualização de permissão de usuário. \n\n $d_user \n\n $d_perm_old \n\n ALTERADO PARA \n\n $d_perm_new";

    get_msg( $msg, 1 );

    echo 1;

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
    $proced_tipo_pag = 'EXCLUSÃO DE PERMISSÃO - ' . $tipo_pag;

    if ( empty( $n_acesso ) or $n_acesso < 4 ) {

        $tipo = 0;
        include '../init/msgnopag.php';

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'perm';
        $msg['entre_ch'] = $proced_tipo_pag;
        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;

    }

    $idperm = empty( $idperm ) ? '' : (int)$idperm ;
    if ( empty( $idperm ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Identificador da permissão em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;

    }

    // dados do usuário
    $where = "( SELECT `cod_user` FROM `sicop_users_perm` WHERE `idpermissao` = $idperm LIMIT 1 )";
    $d_user = dados_user( $where );

    //dados da permissão
    $d_perm = dados_perm( $idperm );

    $query = "DELETE FROM `sicop_users_perm` WHERE `idpermissao` = $idperm LIMIT 1";

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
        $msg['text'] = "Erro de exclusão ( $tipo_pag ). \n\n /*dados do que foi manipulado*/ \n\n $d_user \n\n $d_perm \n\n $valor_user.";
        $msg['linha'] = __LINE__;

        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;

    }

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'desc';
    $msg['entre_ch'] = 'EXCLUSÃO DE PERMISSÃO DE USUÁRIO';
    $msg['text'] = "Exclusão de permissão de usuário. \n\n $d_user \n\n $d_perm ";

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
    $proced_tipo_pag = 'CADASTRAMENTO DE PERMISSÃO - ' . $tipo_pag;

    $iduser = empty( $iduser ) ? '' : (int)$iduser ;
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

    $n_setor = empty( $n_setor ) ? '' : (int)$n_setor ;
    if ( empty( $n_setor ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Identificador do setor em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $d_user";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;

    }

    $n_nivel = empty( $n_nivel ) ? 1 : (int)$n_nivel ;
    if ( empty( $n_nivel ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Identificador do nivel de acesso em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $d_user";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;

    }

    $query = "INSERT INTO
                `sicop_users_perm`
                (
                  `cod_user`,
                  `cod_n_setor`,
                  `cod_nivel`
                )
              VALUES
                (
                  $iduser,
                  $n_setor,
                  $n_nivel
                )";

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
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de cadastramento ( $tipo_pag ). \n\n $d_user \n\n $valor_user.";
        $msg['linha'] = __LINE__;

        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;

    }

    $lastid = $model->lastInsertId();

    // fechando a conexao
    $model->closeConnection();

    //dados da permissão
    $d_perm = dados_perm( $lastid );

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'CADASTRAMENTO DE PERMISSÃO DE USUÁRIO';
    $msg['text']     = "Cadastramento de permissão de usuário. \n\n $d_user \n\n $d_perm";

    get_msg( $msg, 1 );

    echo 1;

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
