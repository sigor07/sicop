<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo_pag = 'PEDIDOS DE ESCOLTA';

$n_cadastro = get_session( 'n_cadastro', 'int' );

if ( empty( $n_cadastro ) or $n_cadastro < 3 ) {

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

$cadesc        = '';
$cadlocalesc   = '';
$droplocalesc  = '';
$dropdetesc    = '';
$dropesc       = '';
$editesc       = '';
$get_ord_saida = '';

extract( $_POST, EXTR_OVERWRITE );

$targ = empty( $targ ) ? 0 : 1;

$user = get_session( 'user_id', 'int' );
$ip   = "'" . $_SERVER['REMOTE_ADDR'] . "'";

if ( !empty( $cadesc ) ){

    $escolta_data = empty( $escolta_data ) ? '' : $escolta_data;
    if ( empty( $escolta_data ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Data da escolta em branco. Operação cancelada ( $tipo_pag - CADASTRAMENTO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    if ( !validaData( $escolta_data, 'DD/MM/AAAA' ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Data da escolta inválida. Operação cancelada ( $tipo_pag - CADASTRAMENTO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    // instanciando o model
    $model = SicopModel::getInstance();

    $escolta_data  = "'" . $model->escape_string( $escolta_data ) . "'";

    $escolta_hora = empty( $escolta_hora ) ? 'NULL' : "'" . $model->escape_string( $escolta_hora ) . "'";

    $finalidade = empty( $finalidade ) ? 'NULL' : "'" . tratastring( $finalidade, 'U', FALSE ) . "'";

    $q_add_esc = "INSERT INTO
                    `ordens_escolta`
                    (
                      `escolta_data`,
                      `escolta_hora`,
                      `finalidade`,
                      `user_add`,
                      `data_add`,
                      `ip_add`
                    )
                  VALUES
                    (
                      STR_TO_DATE( $escolta_data, '%d/%m/%Y' ),
                      STR_TO_DATE( $escolta_hora, '%H:%i' ),
                      $finalidade,
                      $user,
                      NOW(),
                      $ip
                    )";

    // executando a query
    $q_add_esc = $model->query( $q_add_esc );

    if( $q_add_esc ) {

        $lastid = $model->lastInsertId();

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']     = 'desc';
        $msg['entre_ch'] = 'CADASTRAMENTO DE ESCOLTA';
        $msg['text']     = "Cadastro de pedido de escolta. \n\n[ ESCOLTA ]\n<b>ID:</b> $lastid \n <b>Data:</b> $escolta_data";
        get_msg( $msg, 1 );

        $qs = "idescolta=$lastid";
        redir( 'cadastro/add_escolta.php', $qs );

    } else {

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de cadastramento ( $tipo_pag ). \n\n $valor_user.";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

    }

    // fechando a conexao
    $model->closeConnection();

    exit;

} else if ( !empty( $editesc ) ){ // alterar local escolta

    $idescolta = empty( $idescolta ) ? '' : (int)$idescolta;
    if ( empty( $idescolta ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador do pedido de escolta em branco. Operação cancelada ( $tipo_pag - ATUALIZAÇÃO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    $escolta_data = empty( $escolta_data ) ? '' : $escolta_data;
    if ( empty( $escolta_data ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Data da escolta em branco. Operação cancelada ( $tipo_pag - ATUALIZAÇÃO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    if ( !validaData( $escolta_data, 'DD/MM/AAAA' ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Data da escolta inválida. Operação cancelada ( $tipo_pag - ATUALIZAÇÃO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    // instanciando o model
    $model = SicopModel::getInstance();

    $escolta_data  = "'" . $model->escape_string( $escolta_data ) . "'";

    $escolta_hora = empty( $escolta_hora ) ? 'NULL' : "'" . $model->escape_string( $escolta_hora ) . "'";

    $finalidade = empty( $finalidade ) ? 'NULL' : "'" . tratastring( $finalidade, 'U', FALSE ) . "'";

    $q_add_esc = "UPDATE
                    `ordens_escolta`
                  SET
                    `escolta_data` = STR_TO_DATE( $escolta_data, '%d/%m/%Y' ),
                    `escolta_hora` = STR_TO_DATE( $escolta_hora, '%H:%i' ),
                    `finalidade` = $finalidade,
                    `user_up` = $user,
                    `data_up` = NOW(),
                    `ip_up` = $ip
                  WHERE
                    `idescolta` = $idescolta
                  LIMIT 1";

    // executando a query
    $q_add_esc = $model->query( $q_add_esc );

    if ( !$q_add_esc ) {

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de atualização ( $tipo_pag ). \n\n $valor_user.";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'ATUALIZAÇÃO DE ESCOLTA';
    $msg['text']     = "Atualização de pedido de escolta. \n\n[ ESCOLTA ]\n<b>ID:</b> $idescolta \n <b>Data:</b> $escolta_data";
    get_msg( $msg, 1 );

    echo msg_js( '', 2 );

    exit;


} else if ( !empty( $cadlocalesc ) ){ // cadastrar local escolta

    $idescolta = empty( $idescolta ) ? '' : (int)$idescolta;

    if ( empty( $idescolta ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador da escolta em branco. Operação cancelada ( $tipo_pag - CADASTRAMENTO DE DESTINO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    $local_esc = empty( $local_esc ) ? '' : (int)$local_esc;

    if ( empty( $local_esc ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador do destino da escolta em branco. Operação cancelada ( $tipo_pag - CADASTRAMENTO DE DESTINO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    // instanciando o model
    $model = SicopModel::getInstance();

    $local_hora = empty( $local_hora ) ? 'NULL' : "'" . $model->escape_string( $local_hora ) . "'";

    $q_add_e_local = "INSERT INTO
                        `ordens_escolta_locais`
                        (
                          `cod_escolta`,
                          `cod_local`,
                          `local_hora`,
                          `user_add`,
                          `data_add`,
                          `ip_add`
                        )
                      VALUES
                        (
                          $idescolta,
                          $local_esc,
                          STR_TO_DATE( $local_hora, '%H:%i' ),
                          $user,
                          NOW(),
                          $ip
                        )";

    // executando a query
    $q_add_e_local = $model->query( $q_add_e_local );

    if ( $q_add_e_local ) {

        $lastid = $model->lastInsertId();

        $q_s_escolta = "SELECT
                          `ordens_escolta`.`idescolta`,
                          DATE_FORMAT( `ordens_escolta`.`escolta_data`, '%d/%m/%Y' ) AS escolta_data_f,
                          `locais_apr`.`local_apr` AS local_apr
                        FROM
                          `ordens_escolta`
                          INNER JOIN `ordens_escolta_locais` ON `ordens_escolta_locais`.`cod_escolta` = `ordens_escolta`.`idescolta`
                          INNER JOIN `locais_apr` ON `ordens_escolta_locais`.`cod_local` = `locais_apr`.`idlocal`
                        WHERE
                          `ordens_escolta_locais`.`id_local_escolta` = $lastid";

        // executando a query
        $q_s_escolta  = $model->query( $q_s_escolta );
        $d_escolta    = $q_s_escolta->fetch_assoc();
        $idb          = $d_escolta['idescolta'];
        $escolta_data = $d_escolta['escolta_data_f'];
        $dest_escolta = $d_escolta['local_apr'];
        $s_escolta    = "<b>ID:</b> $idb; <b>Data:</b> $escolta_data; <b>Local:</b> $dest_escolta";

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']     = 'desc';
        $msg['entre_ch'] = 'CADASTRAMENTO DE ESCOLTA';
        $msg['text']     = "Cadastro de local de escolta.\n\n[ ESCOLTA ]\n $s_escolta";
        get_msg( $msg, 1 );

        $qs = "idescolta=$idescolta&idlocalesc=$lastid";
        redir( 'cadastro/add_escolta.php', $qs );

    } else {

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de cadastramento ( $tipo_pag - LOCAL DE PEDIDO DE ESCOLTA ). \n\n $valor_user.";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

    }

    // fechando a conexao
    $model->closeConnection();

    exit;

} else if ( !empty( $droplocalesc ) ){ // excluir destino escolta

    $idlocalesc = empty( $idlocalesc ) ? '' : (int)$idlocalesc;

    if ( empty( $idlocalesc ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador do local do pedido de escolta em branco. Operação cancelada ( $tipo_pag - EXCLUSÃO DE DESTINO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    // pegar os dados da escolta e do local
    $q_s_escolta = "SELECT
                      `ordens_escolta`.`idescolta`,
                      DATE_FORMAT( `ordens_escolta`.`escolta_data`, '%d/%m/%Y' ) AS escolta_data_f,
                      `locais_apr`.`local_apr` AS local_apr
                    FROM
                      `ordens_escolta`
                      INNER JOIN `ordens_escolta_locais` ON `ordens_escolta_locais`.`cod_escolta` = `ordens_escolta`.`idescolta`
                      INNER JOIN `locais_apr` ON `ordens_escolta_locais`.`cod_local` = `locais_apr`.`idlocal`
                    WHERE
                      `ordens_escolta_locais`.`id_local_escolta` = $idlocalesc";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_s_escolta = $model->query( $q_s_escolta );

    // fechando a conexao
    $model->closeConnection();

    $d_escolta    = $q_s_escolta->fetch_assoc();
    $idb          = $d_escolta['idescolta'];
    $escolta_data = $d_escolta['escolta_data_f'];
    $dest_escolta = $d_escolta['local_apr'];
    $s_escolta    = "<b>ID:</b> $idb; <b>Data:</b> $escolta_data; <b>Local:</b> $dest_escolta";

    $q_drop_esc_local = "DELETE FROM `ordens_escolta_locais` WHERE `id_local_escolta` = $idlocalesc LIMIT 1";
    $q_drop_det_esc_local = "DELETE FROM `ordens_escolta_det` WHERE `cod_local_escolta` = $idlocalesc LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_drop_esc_local = $model->query( $q_drop_esc_local );

    if( $q_drop_esc_local ) {

        // query executada apenas para confirmação
        // os detentos são excluidos pela FK
        $model->query( $q_drop_det_esc_local );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']     = 'desc';
        $msg['entre_ch'] = 'EXCLUSÃO DE ESCOLTA';
        $msg['text']     = "Exclusão de local de pedido de escolta.\n\n[ PEDIDO DE ESCOLTA ]\n $s_escolta";
        get_msg( $msg, 1 );

        echo msg_js( '', 1 );

    } else {

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de exclusão ( $tipo_pag - LOCAL ). \n\n $valor_user.";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

    }

    // fechando a conexao
    $model->closeConnection();

    exit;

} else if ( !empty( $dropdetesc ) ){ // excluir detento escolta

    $ided = empty( $ided ) ? '' : (int)$ided;

    if ( empty( $ided ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = 'Identificador do ' . SICOP_DET_DESC_L . " do pedido de escolta em branco. Operação cancelada ( $tipo_pag - EXCLUSÃO DE " . SICOP_DET_DESC_U . " ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    // pegar os dados da escolta, do local e id do detento
    $q_s_esc = "SELECT
                  `ordens_escolta`.`idescolta`,
                  DATE_FORMAT( `ordens_escolta`.`escolta_data`, '%d/%m/%Y' ) AS escolta_data_f,
                  DATE_FORMAT( `escolta_hora`, '%H:%i' ) AS `escolta_hora_f`,
                  `locais_apr`.`local_apr`,
                  `ordens_escolta_det`.`cod_detento`
                FROM
                  `ordens_escolta`
                  INNER JOIN `ordens_escolta_locais` ON `ordens_escolta_locais`.`cod_escolta` = `ordens_escolta`.`idescolta`
                  INNER JOIN `locais_apr` ON `ordens_escolta_locais`.`cod_local` = `locais_apr`.`idlocal`
                  INNER JOIN `ordens_escolta_det` ON `ordens_escolta_det`.`cod_local_escolta` = `ordens_escolta_locais`.`id_local_escolta`
                WHERE
                  `ordens_escolta_det`.`id_escolta_det` = $ided";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_s_esc = $model->query( $q_s_esc );

    // fechando a conexao
    $model->closeConnection();

    $d_esc     = $q_s_esc->fetch_assoc();
    $ide       = $d_esc['idescolta'];
    $esc_data  = $d_esc['escolta_data_f'];
    $esc_hora  = $d_esc['escolta_hora_f'];
    $esc_local = $d_esc['local_apr'];
    $s_esc     = "<b>ID:</b> $ide; <b>Data:</b> $esc_data";

    if ( !empty ( $esc_hora ) ) $s_esc .= "; <b>Hora:</b> $esc_hora";

    $s_esc    .= "; <b>Local:</b> $esc_local";


    $idd = $d_esc['cod_detento'];

    // pegar os dados do preso
    $detento = dados_det( $idd );

    $q_drop_det_escolta = "DELETE FROM `ordens_escolta_det` WHERE `id_escolta_det` = $ided LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_drop_det_escolta = $model->query( $q_drop_det_escolta );

    // fechando a conexao
    $model->closeConnection();

    if( $q_drop_det_escolta ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']     = 'desc';
        $msg['entre_ch'] = 'EXCLUSÃO DE ' . SICOP_DET_DESC_U . ' DE ESCOLTA';
        $msg['text']     = "Exclusão de " . SICOP_DET_DESC_L . " de pedido de escolta.\n\n[ PEDIDO DE ESCOLTA ]\n $s_esc \n\n $detento \n";
        get_msg( $msg, 1 );

        echo msg_js( '', 1 );

    } else {

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de exclusão ( $tipo_pag - " . SICOP_DET_DESC_L . " ). \n\n $valor_user.";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

    }

    exit;

} else if ( !empty( $dropesc ) ){ // excluir escolta

    $idescolta = empty( $idescolta ) ? '' : (int)$idescolta;

    if ( empty( $idescolta ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador do pedido de escolta em branco. Operação cancelada ( $tipo_pag - EXCLUSÃO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    // pegar os dados da escolta
    $q_s_esc = "SELECT
                  `ordens_escolta`.`idescolta`,
                  DATE_FORMAT( `ordens_escolta`.`escolta_data`, '%d/%m/%Y' ) AS escolta_data_f,
                  DATE_FORMAT( `ordens_escolta`.`escolta_hora`, '%H:%i' ) AS `escolta_hora_f`
                FROM
                  `ordens_escolta`
                WHERE
                  `ordens_escolta`.`idescolta` = $idescolta";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_s_esc = $model->query( $q_s_esc );

    // fechando a conexao
    $model->closeConnection();

    $d_esc    = $q_s_esc->fetch_assoc();
    $ide      = $d_esc['idescolta'];
    $esc_data = $d_esc['escolta_data_f'];
    $esc_hora = $d_esc['escolta_hora_f'];
    $s_esc    = "<b>ID:</b> $ide; <b>Data:</b> $esc_data";
    if ( !empty ( $esc_hora ) ) $s_esc .= "; <b>Hora:</b> $esc_hora";


    $q_drop_escolta = "DELETE FROM `ordens_escolta` WHERE `idescolta` = $idescolta LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_drop_escolta = $model->query( $q_drop_escolta );

    // fechando a conexao
    $model->closeConnection();

    if( $q_drop_escolta ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']     = 'desc';
        $msg['entre_ch'] = 'EXCLUSÃO DE ESCOLTA';
        $msg['text']     = "Exclusão de pedido de escolta.\n\n[ PEDIDO DE ESCOLTA ]\n $s_esc";
        get_msg( $msg, 1 );

        echo msg_js( '', 1 );

    } else {

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de exclusão ( $tipo_pag ). \n\n $valor_user.";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

    }

    exit;

} else if ( !empty( $get_ord_saida ) ) { // gerar ordem de saida

    $idescolta = empty( $idescolta ) ? '' : (int)$idescolta;
    if ( empty( $idescolta ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador do pedido de escolta em branco. Operação cancelada ( $tipo_pag - GERAR ORDEM DE SAÍDA ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    $q_in_os = " INSERT INTO
                   `ordens_saida`
                   (
                     `ord_saida_data`,
                     `ord_saida_hora`,
                     `finalidade`,
                     `user_add`,
                     `data_add`,
                     `ip_add`
                   )
                 SELECT
                   `escolta_data`,
                   `escolta_hora`,
                   IFNULL( `finalidade`, 'ATENDIMENTO EXTERNO' ),
                   $user,
                   NOW(),
                   $ip
                 FROM
                   `ordens_escolta`
                 WHERE
                   `idescolta` = $idescolta
                 LIMIT 1";

    $success = true;
    $erromysql = '';

    // instanciando o model
    $model = SicopModel::getInstance();

    // iniciando a transaction
    $model->transaction();

    // executando a query
    $q_in_os = $model->query( $q_in_os );

    if( !$q_in_os ) {

        $erromysql .= "\n\n[ ERRO MYSQL - PEDIDO DE ESCOLTA P/ ORDEM DE SAIDA ]\n";
        $erromysql .= $model->getErrorMsg();

        $success = false;

    }

    if ( $success ) {

        $last_id_os = $model->lastInsertId();

        $q_local_esc = "SELECT
                          `id_local_escolta`,
                          `cod_local`,
                          `local_hora`
                        FROM
                          `ordens_escolta_locais`
                        WHERE
                          `cod_escolta` = $idescolta";

        // executando a query
        $q_local_esc = $model->query( $q_local_esc );
        if( !$q_local_esc ) {

            $erromysql .= "\n\n[ ERRO MYSQL - PEDIDO DE ESCOLTA P/ ORDEM DE SAIDA - LOCAIS DE ESCOLTA - SELECT ]\n";
            $erromysql .= $model->getErrorMsg();

            $success = false;

        }

        if ( $success ) {

            while ( $d_local_esc = $q_local_esc->fetch_assoc() ) {

                $id_local_escolta = $d_local_esc['id_local_escolta'];
                $cod_local        = $d_local_esc['cod_local'];
                $local_hora       = !empty ( $d_local_esc['local_hora'] ) ? "'" . $d_local_esc['local_hora'] . "'" : 'NULL' ;

                $q_in_osl = " INSERT INTO
                                `ordens_saida_locais`
                                (
                                  `cod_ord_saida`,
                                  `cod_local`,
                                  `local_hora`,
                                  `user_add`,
                                  `data_add`,
                                  `ip_add`
                                )
                                VALUES
                                (
                                  $last_id_os,
                                  $cod_local,
                                  $local_hora,
                                  $user,
                                  NOW(),
                                  $ip
                                )";

                // executando a query
                $q_in_osl = $model->query( $q_in_osl );

                if ( !$q_in_osl ) {

                    $erromysql .= "\n\n[ ERRO MYSQL - PEDIDO DE ESCOLTA P/ ORDEM DE SAIDA - LOCAIS DE ESCOLTA - INSERÇÃO ]\n";
                    $erromysql .= $model->getErrorMsg();

                    $success = false;
                    break;

                }

                /*
                 * $last_id_osl = last id ordem de saida local
                 * pegar o id da última inserção
                 */
                $last_id_osl = $model->lastInsertId();

                $q_in_osd = "INSERT INTO
                               `ordens_saida_det`
                               (
                                 `cod_local_ord_saida`,
                                 `cod_detento`,
                                 `user_add`,
                                 `data_add`,
                                 `ip_add`
                               )
                             SELECT
                               $last_id_osl,
                               `cod_detento`,
                               $user,
                               NOW(),
                               $ip
                             FROM
                               `ordens_escolta_det`
                             WHERE
                               `cod_local_escolta` = $id_local_escolta";

                // executando a query
                $q_in_osd = $model->query( $q_in_osd );

                if ( !$q_in_osd ) {

                    $erromysql .= "\n\n[ ERRO MYSQL - PEDIDO DE ESCOLTA P/ ORDEM DE SAIDA - " . SICOP_DET_DESC_U . "S ]\n";
                    $erromysql .= $model->getErrorMsg();

                    $success = false;
                    break;

                }

            } // fim do while ( $d_local_esc...

        } // fim do if ( $success )

    } // fim do if ( $success )

    // pegar os dados da escolta
    $q_s_esc = "SELECT
                  `ordens_escolta`.`idescolta`,
                  DATE_FORMAT( `ordens_escolta`.`escolta_data`, '%d/%m/%Y' ) AS escolta_data_f,
                  DATE_FORMAT( `ordens_escolta`.`escolta_hora`, '%H:%i' ) AS `escolta_hora_f`
                FROM
                  `ordens_escolta`
                WHERE
                  `ordens_escolta`.`idescolta` = $idescolta";

    // executando a query
    $q_s_esc  = $model->query( $q_s_esc );
    $d_esc    = $q_s_esc->fetch_assoc();
    $ide      = $d_esc['idescolta'];
    $esc_data = $d_esc['escolta_data_f'];
    $esc_hora = $d_esc['escolta_hora_f'];
    $s_esc    = "<b>ID:</b> $ide; <b>Data:</b> $esc_data";
    if ( !empty ( $esc_hora ) ) $s_esc .= "; <b>Hora:</b> $esc_hora";

    if ( !$success ) {

        // em caso de falha, cancela as alterações
        $model->rollback();

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro ao passar pedido de escolta para ordem de saída. \n\n [ ESCOLTA ]\n $s_esc \n\n $valor_user \n\n $erromysql";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );
        exit;

    }

    // cofimando as alterações
    $model->commit();

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'PEDIDO DE ESCOLTA P/ ORDEM DE SAIDA';
    $msg['text']     = "Pedido de escolta passado para ordem de saída. \n\n [ ESCOLTA ]\n $s_esc";
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