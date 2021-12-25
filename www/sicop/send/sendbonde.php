<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';
$tipo_pag = 'BONDE';

$n_bonde = get_session( 'n_bonde', 'int' );

if ( empty( $n_bonde ) or $n_bonde < 3 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $tipo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

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

$cadbond        = '';
$editbond       = '';
$cadbondlocal   = '';
$droplocalbonde = '';
$dropdetbonde   = '';
$dropbonde      = '';

extract( $_POST, EXTR_OVERWRITE );

$targ = empty( $targ ) ? 0 : 1;

$user = get_session( 'user_id', 'int' );
$ip   = "'" . $_SERVER['REMOTE_ADDR'] . "'";

if ( !empty( $cadbond ) ){

    $bonde_data = empty( $bonde_data ) ? '' : $bonde_data;

//    if ( empty( $bonde_data ) ) {
//
//        // montar a mensagem q será salva no log
//        $msg = array();
//        $msg['tipo']  = 'err';
//        $msg['text']  = "Data do bonde em branco. Operação cancelada ( $tipo_pag - CADASTRAMENTO DE BONDE ).";
//        $msg['linha'] = __LINE__;
//        get_msg( $msg, 1 );
//
//        echo msg_js( 'FALHA!', 1 );
//
//        exit;
//
//    }

    if ( !empty( $bonde_data ) ) {

        if ( !validaData( $bonde_data, 'DD/MM/AAAA' ) ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']  = 'err';
            $msg['text']  = "Data do bonde inválida. Operação cancelada ( $tipo_pag - CADASTRAMENTO DE BONDE ).";
            $msg['linha'] = __LINE__;
            get_msg( $msg, 1 );

            echo msg_js( 'FALHA!', 1 );

            exit;

        }

    }

    // instanciando o model
    $model = SicopModel::getInstance();

    $bonde_data = empty( $bonde_data ) ? 'NULL' : "'" . $model->escape_string( $bonde_data ) . "'";

    $q_add_bonde = "INSERT INTO
                      `bonde`
                      (
                        `bonde_data`,
                        `user_add`,
                        `data_add`,
                        `ip_add`
                      )
                    VALUES
                      (
                        STR_TO_DATE( $bonde_data, '%d/%m/%Y' ),
                        $user,
                        NOW(),
                        $ip
                      )";

    // executando a query
    $q_add_bonde = $model->query( $q_add_bonde );

    if ( !$q_add_bonde ) {

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de cadastramento ( $tipo_pag ). \n\n $valor_user \n";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    $lastid = $model->lastInsertId();

    // fechando a conexao
    $model->closeConnection();

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'CADASTRAMENTO DE BONDE';
    $msg['text']     = "Cadastro de bonde.\n\n[ BONDE ]\n<b>ID:</b> $lastid \n <b>Data:</b> $bonde_data";
    get_msg( $msg, 1 );

    $qs = "idbonde=$lastid";
    redir( 'seguranca/add_bonde.php', $qs );

    exit;

} else if ( !empty( $editbond ) ){ // alterar bonde

    $idbonde = empty( $idbonde ) ? '' : (int)$idbonde;
    if ( empty( $idbonde ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador do bonde em branco. Operação cancelada ( $tipo_pag - ATUALIZAÇÃO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    $bonde_data = empty( $bonde_data ) ? '' : $bonde_data;
    if ( !empty( $bonde_data ) ) {

        if ( !validaData( $bonde_data, 'DD/MM/AAAA' ) ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']  = 'err';
            $msg['text']  = "Data do bonde inválida. Operação cancelada ( $tipo_pag - ATUALIZAÇÃO ).";
            $msg['linha'] = __LINE__;
            get_msg( $msg, 1 );

            echo msg_js( 'FALHA!', 1 );

            exit;

        }

    }

    // instanciando o model
    $model = SicopModel::getInstance();

    $bonde_data = empty( $bonde_data ) ? 'NULL' : "'" . $model->escape_string( $bonde_data ) . "'";

    $q_up_bonde = "UPDATE
                     `bonde`
                   SET
                     `bonde_data` = STR_TO_DATE( $bonde_data, '%d/%m/%Y' ),
                     `user_up` = $user,
                     `data_up` = NOW(),
                     `ip_up` = $ip
                   WHERE
                     `idbonde` = $idbonde
                   LIMIT 1";

    // executando a query
    $q_up_bonde = $model->query( $q_up_bonde );

    // fechando a conexao
    $model->closeConnection();

    if ( !$q_up_bonde ) {

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de atualização ( $tipo_pag ). \n\n $valor_user \n.";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'ATUALIZAÇÃO DE BONDE';
    $msg['text']     = "Atualização de bonde. \n\n[ BONDE ]\n<b>ID:</b> $idbonde \n <b>Data:</b> $bonde_data";
    get_msg( $msg, 1 );

    echo msg_js( '', 2 );

    exit;

} else if ( !empty( $cadbondlocal ) ){ // cadastrar destino bonde

    $idbonde = empty( $idbonde ) ? '' : (int)$idbonde;

    if ( empty( $idbonde ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador do bonde em branco. Operação cancelada ( $tipo_pag - CADASTRAMENTO DE DESTINO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    $local_bonde = empty( $local_bonde ) ? '' : (int)$local_bonde;

    if ( empty( $local_bonde ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador do destino do bonde em branco. Operação cancelada ( $tipo_pag - CADASTRAMENTO DE DESTINO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    $q_add_b_local = "INSERT INTO
                        `bonde_locais`
                        (
                          `cod_bonde`,
                          `cod_unidade`,
                          `user_add`,
                          `data_add`,
                          `ip_add`
                        )
                      VALUES
                        (
                          $idbonde,
                          $local_bonde,
                          $user,
                          NOW(),
                          $ip
                        )";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_add_b_local = $model->query( $q_add_b_local );

    if( !$q_add_b_local ) {

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de cadastramento ( $tipo_pag - LOCAL DE BONDE ). \n\n $valor_user \n";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    $lastid = $model->lastInsertId();

    $q_s_bonde = "SELECT
                    `bonde`.`idbonde`,
                    DATE_FORMAT( `bonde`.`bonde_data`, '%d/%m/%Y' ) AS bonde_data_f,
                    `unidades`.`unidades` AS dest_bonde
                  FROM
                    `bonde`
                    INNER JOIN `bonde_locais` ON `bonde_locais`.`cod_bonde` = `bonde`.`idbonde`
                    INNER JOIN `unidades` ON `bonde_locais`.`cod_unidade` = `unidades`.`idunidades`
                  WHERE
                    `bonde_locais`.`idblocal` = $lastid";

    // executando a query
    $q_s_bonde  = $model->query( $q_s_bonde );
    $d_bonde    = $q_s_bonde->fetch_assoc();
    $idb        = $d_bonde['idbonde'];
    $bonde_data = $d_bonde['bonde_data_f'];
    $dest_bonde = $d_bonde['dest_bonde'];
    $s_bonde    = "<b>ID:</b> $idb; <b>Data:</b> $bonde_data; <b>Local:</b> $dest_bonde";

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'CADASTRAMENTO DE BONDE';
    $msg['text']     = "Cadastro de local de bonde.\n\n[ BONDE ]\n $s_bonde";
    get_msg( $msg, 1 );

    $qs = "idbonde=$idbonde&idb_local=$lastid";
    redir( 'seguranca/add_bonde.php', $qs );

    exit;

} else if ( !empty( $droplocalbonde ) ){ // excluir destino bonde

    $idblocal = empty( $idblocal ) ? '' : (int)$idblocal;

    if ( empty( $idblocal ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador do local do bonde em branco. Operação cancelada ( $tipo_pag - EXCLUSÃO DE DESTINO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    // pegar os dados do bonde e do local
    $q_s_bonde = "SELECT
                    `bonde`.`idbonde`,
                    DATE_FORMAT( `bonde`.`bonde_data`, '%d/%m/%Y' ) AS bonde_data_f,
                    `unidades`.`unidades` AS dest_bonde
                  FROM
                    `bonde`
                    INNER JOIN `bonde_locais` ON `bonde_locais`.`cod_bonde` = `bonde`.`idbonde`
                    INNER JOIN `unidades` ON `bonde_locais`.`cod_unidade` = `unidades`.`idunidades`
                  WHERE
                    `bonde_locais`.`idblocal` = $idblocal";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_s_bonde = $model->query( $q_s_bonde );

    // fechando a conexao
    $model->closeConnection();

    $d_bonde    = $q_s_bonde->fetch_assoc();
    $idb        = $d_bonde['idbonde'];
    $bonde_data = $d_bonde['bonde_data_f'];
    $dest_bonde = $d_bonde['dest_bonde'];
    $s_bonde    = "<b>ID:</b> $idb; <b>Data:</b> $bonde_data; <b>Local:</b> $dest_bonde";


    $q_drop_b_local = "DELETE FROM `bonde_locais` WHERE `idblocal` = $idblocal LIMIT 1";
    $q_drop_det_b_local = "DELETE FROM `bonde_det` WHERE `cod_bonde_local` = $idblocal LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_drop_b_local = $model->query( $q_drop_b_local );

    // fechando a conexao
    $model->closeConnection();

    if( !$q_drop_b_local ) {

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de exclusão ( $tipo_pag - LOCAL DE BONDE ). \n\n $valor_user \n";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    // query executada apenas para confirmação
    // os detentos são excluidos pela FK
    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $model->query( $q_drop_det_b_local );

    // fechando a conexao
    $model->closeConnection();

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'EXCLUSÃO DE BONDE';
    $msg['text']     = "Exclusão de local de bonde.\n\n[ BONDE ]\n $s_bonde";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

} else if ( !empty( $dropdetbonde ) ){ // excluir detento bonde


    $idbd = empty( $idbd ) ? '' : (int)$idbd;

    if ( empty( $idbd ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " do bonde em branco. Operação cancelada ( $tipo_pag - EXCLUSÃO DE " . SICOP_DET_DESC_U . " ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    // pegar os dados do bonde, do local e id do detento
    $q_s_bonde = "SELECT
                    `bonde`.`idbonde`,
                    DATE_FORMAT( `bonde`.`bonde_data`, '%d/%m/%Y' ) AS bonde_data_f,
                    `unidades`.`unidades` AS dest_bonde,
                    `bonde_det`.`cod_detento`
                  FROM
                    `bonde`
                    INNER JOIN `bonde_locais` ON `bonde_locais`.`cod_bonde` = `bonde`.`idbonde`
                    INNER JOIN `unidades` ON `bonde_locais`.`cod_unidade` = `unidades`.`idunidades`
                    INNER JOIN `bonde_det` ON `bonde_det`.`cod_bonde_local` = `bonde_locais`.`idblocal`
                  WHERE
                    `bonde_det`.`idbd` = $idbd";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_s_bonde = $model->query( $q_s_bonde );

    // fechando a conexao
    $model->closeConnection();

    $d_bonde    = $q_s_bonde->fetch_assoc();
    $idb        = $d_bonde['idbonde'];
    $bonde_data = $d_bonde['bonde_data_f'];
    $dest_bonde = $d_bonde['dest_bonde'];
    $s_bonde    = "<b>ID:</b> $idb; <b>Data:</b> $bonde_data; <b>Local:</b> $dest_bonde";

    $idd        = $d_bonde['cod_detento'];

    // pegar os dados do preso
    $detento = dados_det( $idd );

    $q_drop_det_bonde = "DELETE FROM `bonde_det` WHERE `idbd` = $idbd LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_drop_det_bonde = $model->query( $q_drop_det_bonde );

    // fechando a conexao
    $model->closeConnection();

    if( !$q_drop_det_bonde ) {

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de exclusão ( $tipo_pag - " . SICOP_DET_DESC_U . " DE BONDE ). \n\n $valor_user \n";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'EXCLUSÃO DE ' . SICOP_DET_DESC_U . ' DE BONDE';
    $msg['text']     = "Exclusão de " . SICOP_DET_DESC_L . " de bonde.\n\n[ BONDE ]\n $s_bonde \n\n $detento \n";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

} else if ( !empty( $dropbonde ) ){ // excluir bonde

    $idbonde = empty( $idbonde ) ? '' : (int)$idbonde;

    if ( empty( $idbonde ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador do bonde em branco. Operação cancelada ( $tipo_pag - EXCLUSÃO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    // pegar os dados do bonde e do local
    $q_s_bonde = "SELECT
                    `bonde`.`idbonde`,
                    DATE_FORMAT( `bonde`.`bonde_data`, '%d/%m/%Y' ) AS bonde_data_f
                  FROM
                    `bonde`
                  WHERE
                    `bonde`.`idbonde` = $idbonde";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_s_bonde = $model->query( $q_s_bonde );

    // fechando a conexao
    $model->closeConnection();

    $d_bonde    = $q_s_bonde->fetch_assoc();
    $idb        = $d_bonde['idbonde'];
    $bonde_data = $d_bonde['bonde_data_f'];
    $s_bonde    = "<b>ID:</b> $idb; <b>Data:</b> $bonde_data";

    $q_drop_bonde = "DELETE FROM `bonde` WHERE `idbonde` = $idbonde LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_drop_bonde = $model->query( $q_drop_bonde );

    // fechando a conexao
    $model->closeConnection();

    if( !$q_drop_bonde ) {

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de exclusão ( $tipo_pag ). \n\n $valor_user \n";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'EXCLUSÃO DE BONDE';
    $msg['text']     = "Exclusão de bonde.\n\n[ BONDE ]\n $s_bonde";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

} else {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Código de procedimento em branco ou inválido ( $tipo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 1 );

    exit;

}


?>
</body>
</html>