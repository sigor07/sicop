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
$tipo_pag = 'ITEM DE SEDEX';

/*
 * define a variavel $n_acesso de acordo com o setor
 */

$n_acesso = get_session( 'n_sedex', 'int' );
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
    $proced_tipo_pag = 'ATUALIZAÇÃO - ' . $tipo_pag;

    $id_item = empty( $id_item ) ? '' : (int)$id_item ;
    if ( empty( $id_item ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Identificador do item do sedex em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;
    }

    //dados do sedex
    $where = "( SELECT `cod_sedex` FROM `sedex_itens` WHERE `id_item` = $id_item LIMIT 1 )";
    $d_sedex = dados_sedex( $where, 1 );

    //dados do item
    $d_item_sedex_old = dados_item_sedex( $id_item, 1 );

    $un_med = empty( $un_med ) ? '' : (int)$un_med ;
    if ( empty( $un_med ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Identificador da unidade de medida em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $d_sedex \n\n $d_item_sedex_old";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;

    }

    $quant = empty( $quant ) ? '' : tratabasico( $quant );
    if ( empty( $quant ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Quantidade em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $d_sedex \n\n $d_item_sedex_old";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;

    }

    $quant = str_replace( ',', '.', $quant );

    $desc_item_sedex  = empty( $desc_item_sedex ) ? '' : tratastring( $desc_item_sedex, 'U', FALSE );
    if ( empty( $desc_item_sedex ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Descrição do item em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $d_sedex \n\n $d_item_sedex_old";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;
        exit;

    }

    $ret = empty( $ret ) ? 0 : 1;

    $query = "UPDATE
                `sedex_itens`
              SET
                `cod_um`  = $un_med,
                `quant`   = $quant,
                `desc`    = '$desc_item_sedex',
                `retido`  = $ret,
                `user_up` = $user,
                `data_up` = NOW(),
                `ip_up`   = $ip
              WHERE
                `id_item` = $id_item
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
        $msg['text']  = "Erro de atualização ( $tipo_pag ). \n\n $d_sedex \n\n $d_item_sedex_old \n\n $valor_user.";
        $msg['linha'] = __LINE__;

        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;

    }

    //dados da permissão atualizada
    $d_item_sedex_new = dados_item_sedex( $id_item );

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'ATUALIZAÇÃO DE ITEM DE SEDEX';
    $msg['text']     = "Atualização de item de sedex. \n\n $d_sedex \n\n $d_item_sedex_old \n\n ALTERADO PARA \n\n $d_item_sedex_new";

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
    $proced_tipo_pag = 'EXCLUSÃO - ' . $tipo_pag;

    $id_item = empty( $id_item ) ? '' : (int)$id_item ;
    if ( empty( $id_item ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Identificador do item do sedex em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;
    }

    //dados do sedex
    $where = "( SELECT `cod_sedex` FROM `sedex_itens` WHERE `id_item` = $id_item LIMIT 1 )";
    $d_sedex = dados_sedex( $where, 1 );

    //dados do item
    $d_item_sedex_old = dados_item_sedex( $id_item, 1 );

    $query = "DELETE FROM `sedex_itens` WHERE `id_item` = $id_item LIMIT 1";

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
        $msg['text'] = "Erro de exclusão ( $tipo_pag ). \n\n $d_sedex \n\n $d_item_sedex_old \n\n $valor_user.";
        $msg['linha'] = __LINE__;

        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;

    }

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'desc';
    $msg['entre_ch'] = 'EXCLUSÃO DE ITEM DE SEDEX';
    $msg['text'] = "Exclusão de item de sedex. \n\n $d_sedex \n\n $d_item_sedex_old ";

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

    $ids = empty( $ids ) ? '' : (int)$ids ;
    if ( empty( $ids ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Identificador do sedex em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;
    }

    // pegar os dados do sedex
    $d_sedex = dados_sedex( $ids, 1 );

    $un_med = empty( $un_med ) ? '' : (int)$un_med ;
    if ( empty( $un_med ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Identificador da unidade de medida em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $d_sedex";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;

    }

    $quant = empty( $quant ) ? '' : tratabasico( $quant );
    if ( empty( $quant ) ) {

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Quantidade em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $d_sedex";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;

    }

    $quant = str_replace( ',', '.', $quant );

    $desc_item_sedex  = empty( $desc_item_sedex ) ? '' : tratastring( $desc_item_sedex, 'U', FALSE );
    if ( empty( $desc_item_sedex ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Descrição do item em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $d_sedex";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo $msg_falha;
        exit;

    }

    $ret = empty( $ret ) ? 0 : 1;

    $query = "INSERT INTO
                `sedex_itens`
                (
                  `cod_sedex`,
                  `cod_um`,
                  `quant`,
                  `desc`,
                  `retido`,
                  `user_add`,
                  `data_add`,
                  `ip_add`
                )
              VALUES
                (
                  $ids,
                  $un_med,
                  $quant,
                  '$desc_item_sedex',
                  $ret,
                  $user,
                  NOW(),
                  $ip
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
        $msg['text']  = "Erro de cadastramento ( $tipo_pag ). \n\n $d_sedex \n\n $valor_user.";
        $msg['linha'] = __LINE__;

        get_msg( $msg, 1 );

        echo $msg_falha;

        exit;

    }

    $lastid = $model->lastInsertId();

    // fechando a conexao
    $model->closeConnection();

    //dados do item
    $d_item_sedex_old = dados_item_sedex( $lastid, 1 );

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'CADASTRAMENTO DE ITEM DE SEDEX';
    $msg['text']     = "Cadastramento de item de sedex. \n\n $d_sedex \n\n $d_item_sedex_old";

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
