<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';
$tipo_pag = 'ORDENS DE SAÍDA';

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

$cados        = '';
$cadlocalos   = '';
$droplocalos  = '';
$dropdetos    = '';
$dropos       = '';
$editos       = '';

extract( $_POST, EXTR_OVERWRITE );

$targ = empty( $targ ) ? 0 : 1;

$user = get_session( 'user_id', 'int' );
$ip   = "'" . $_SERVER['REMOTE_ADDR'] . "'";

if ( !empty( $cados ) ){

    $ord_saida_data = empty( $ord_saida_data ) ? '' : $ord_saida_data;
    if ( empty( $ord_saida_data ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Data da ordem de saída em branco. Operação cancelada ( $tipo_pag - CADASTRAMENTO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    if ( !validaData( $ord_saida_data, 'DD/MM/AAAA' ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Data da ordem de saída inválida. Operação cancelada ( $tipo_pag - CADASTRAMENTO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    // instanciando o model
    $model = SicopModel::getInstance();

    $ord_saida_data  = "'" . $model->escape_string( $ord_saida_data ) . "'";

    $ord_saida_hora = empty( $ord_saida_hora ) ? 'NULL' : "'" . $model->escape_string( $ord_saida_hora ) . "'";

    $finalidade = empty( $finalidade ) ? 'NULL' : "'" . tratastring( $finalidade, 'U', FALSE ) . "'";
    $escolta    = empty( $escolta ) ? 'NULL' : "'" . tratastring( $escolta, 'U', FALSE ) . "'";
    $retorno    = empty( $retorno ) ? 0 : 1;

    $q_add_ord_saida = "INSERT INTO
                          `ordens_saida`
                          (
                            `ord_saida_data`,
                            `ord_saida_hora`,
                            `finalidade`,
                            `responsavel_escolta`,
                            `retorno`,
                            `user_add`,
                            `data_add`,
                            `ip_add`
                          )
                        VALUES
                          (
                            STR_TO_DATE( $ord_saida_data, '%d/%m/%Y' ),
                            STR_TO_DATE( $ord_saida_hora, '%H:%i' ),
                            $finalidade,
                            $escolta,
                            $retorno,
                            $user,
                            NOW(),
                            $ip
                          )";

    // executando a query
    $q_add_ord_saida = $model->query( $q_add_ord_saida );

    if ( !$q_add_ord_saida ) {

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de cadastramento ( $tipo_pag ). \n\n $valor_user.";
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
    $msg['entre_ch'] = 'CADASTRAMENTO DE ORDEM DE SAÍDA';
    $msg['text']     = "Cadastro de ordem de saída. \n\n[ ORDEM DE SAÍDA ]\n<b>ID:</b> $lastid \n <b>Data:</b> $ord_saida_data";
    get_msg( $msg, 1 );

    $qs = "id_ord_saida=$lastid";
    redir( 'cadastro/add_ord_saida.php', $qs );
    exit;

} else if ( !empty( $editos ) ){ // alterar ordem de saída

    $id_ord_saida = empty( $id_ord_saida ) ? '' : (int)$id_ord_saida;
    if ( empty( $id_ord_saida ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador do ordem de saída em branco. Operação cancelada ( $tipo_pag - ATUALIZAÇÃO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    $ord_saida_data = empty( $ord_saida_data ) ? '' : $ord_saida_data;
    if ( empty( $ord_saida_data ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Data da ordem de saída em branco. Operação cancelada ( $tipo_pag - ATUALIZAÇÃO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    if ( !validaData( $ord_saida_data, 'DD/MM/AAAA' ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Data da ordem de saída inválida. Operação cancelada ( $tipo_pag - ATUALIZAÇÃO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    // instanciando o model
    $model = SicopModel::getInstance();

    $ord_saida_data  = "'" . $model->escape_string( $ord_saida_data ) . "'";

    $ord_saida_hora = empty( $ord_saida_hora ) ? 'NULL' : "'" . $model->escape_string( $ord_saida_hora ) . "'";

    $finalidade = empty( $finalidade ) ? 'NULL' : "'" . tratastring( $finalidade, 'U', FALSE ) . "'";
    $escolta    = empty( $escolta ) ? 'NULL' : "'" . tratastring( $escolta, 'U', FALSE ) . "'";
    $retorno    = empty( $retorno ) ? 0 : 1;

    $q_up_ord_saida = "UPDATE
                         `ordens_saida`
                       SET
                         `ord_saida_data` = STR_TO_DATE( $ord_saida_data, '%d/%m/%Y' ),
                         `ord_saida_hora` = STR_TO_DATE( $ord_saida_hora, '%H:%i' ),
                         `finalidade` = $finalidade,
                         `responsavel_escolta` = $escolta,
                         `retorno` = $retorno,
                         `user_up` = $user,
                         `data_up` = NOW(),
                         `ip_up` = $ip
                       WHERE
                         `id_ord_saida` = $id_ord_saida
                       LIMIT 1";

    // executando a query
    $q_up_ord_saida = $model->query( $q_up_ord_saida );

    // fechando a conexao
    $model->closeConnection();

    if ( !$q_up_ord_saida ) {

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
    $msg['entre_ch'] = 'ATUALIZAÇÃO DE ORDEM DE SAÍDA';
    $msg['text']     = "Atualização de ordem de saída. \n\n[ ORDEM DE SAÍDA ]\n<b>ID:</b> $id_ord_saida \n <b>Data:</b> $ord_saida_data";
    get_msg( $msg, 1 );

    echo msg_js( '', 2 );

    exit;

} else if ( !empty( $cadlocalos ) ){ // cadastrar local ordem de saída

    $id_ord_saida = empty( $id_ord_saida ) ? '' : (int)$id_ord_saida;

    if ( empty( $id_ord_saida ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador da ordem de saída em branco. Operação cancelada ( $tipo_pag - CADASTRAMENTO DE DESTINO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    $local_ord_saida = empty( $local_ord_saida ) ? '' : (int)$local_ord_saida;

    if ( empty( $local_ord_saida ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador do destino da ordem de saída em branco. Operação cancelada ( $tipo_pag - CADASTRAMENTO DE DESTINO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    // instanciando o model
    $model = SicopModel::getInstance();

    $local_hora = empty( $local_hora ) ? 'NULL' : "'" . $model->escape_string( $local_hora ) . "'";

    $q_add_os_local = "INSERT INTO
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
                           $id_ord_saida,
                           $local_ord_saida,
                           STR_TO_DATE( $local_hora, '%H:%i' ),
                           $user,
                           NOW(),
                           $ip
                         )";

    // executando a query
    $q_add_os_local = $model->query( $q_add_os_local );

    if ( !$q_add_os_local ) {

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de cadastramento ( $tipo_pag - LOCAL DE ORDEM DE SAÍDA ). \n\n $valor_user.";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    $lastid = $model->lastInsertId();


    $q_s_ord_saida = "SELECT
                        `ordens_saida`.`id_ord_saida`,
                        DATE_FORMAT( `ordens_saida`.`ord_saida_data`, '%d/%m/%Y' ) AS ord_saida_data_f,
                        `locais_apr`.`local_apr` AS local_apr
                      FROM
                        `ordens_saida`
                        INNER JOIN `ordens_saida_locais` ON `ordens_saida_locais`.`cod_ord_saida` = `ordens_saida`.`id_ord_saida`
                        INNER JOIN `locais_apr` ON `ordens_saida_locais`.`cod_local` = `locais_apr`.`idlocal`
                      WHERE
                        `ordens_saida_locais`.`id_local_ord_saida` = $lastid";

    // executando a query
    $q_s_ord_saida = $model->query( $q_s_ord_saida );

    // fechando a conexao
    $model->closeConnection();

    $d_ord_saida    = $q_s_ord_saida->fetch_assoc();
    $idos           = $d_ord_saida['id_ord_saida'];
    $ord_saida_data = $d_ord_saida['ord_saida_data_f'];
    $dest_ord_saida = $d_ord_saida['local_apr'];
    $s_ord_saida    = "<b>ID:</b> $idos; <b>Data:</b> $ord_saida_data; <b>Local:</b> $dest_ord_saida";

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'CADASTRAMENTO DE ORDEM DE SAÍDA';
    $msg['text']     = "Cadastro de local de ordem de saída.\n\n[ ORDEM DE SAÍDA ]\n $s_ord_saida";
    get_msg( $msg, 1 );

    $qs = "id_ord_saida=$id_ord_saida&idlocalos=$lastid";
    redir( 'cadastro/add_ord_saida.php', $qs );

    exit;

} else if ( !empty( $droplocalos ) ){ // excluir destino ordem de saída

    $idlocalos = empty( $idlocalos ) ? '' : (int)$idlocalos;

    if ( empty( $idlocalos ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador do local do ordem de saída em branco. Operação cancelada ( $tipo_pag - EXCLUSÃO DE DESTINO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    // pegar os dados da ordem de saída e do local
    $q_s_ord_saida = "SELECT
                      `ordens_saida`.`id_ord_saida`,
                      DATE_FORMAT( `ordens_saida`.`ord_saida_data`, '%d/%m/%Y' ) AS ord_saida_data_f,
                      `locais_apr`.`local_apr` AS local_apr
                    FROM
                      `ordens_saida`
                      INNER JOIN `ordens_saida_locais` ON `ordens_saida_locais`.`cod_ord_saida` = `ordens_saida`.`id_ord_saida`
                      INNER JOIN `locais_apr` ON `ordens_saida_locais`.`cod_local` = `locais_apr`.`idlocal`
                    WHERE
                      `ordens_saida_locais`.`id_local_ord_saida` = $idlocalos";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_s_ord_saida = $model->query( $q_s_ord_saida );

    // fechando a conexao
    $model->closeConnection();

    $d_ord_saida    = $q_s_ord_saida->fetch_assoc();
    $idos           = $d_ord_saida['id_ord_saida'];
    $ord_saida_data = $d_ord_saida['ord_saida_data_f'];
    $dest_ord_saida = $d_ord_saida['local_apr'];
    $s_ord_saida    = "<b>ID:</b> $idos; <b>Data:</b> $ord_saida_data; <b>Local:</b> $dest_ord_saida";

    $q_drop_ord_saida_local = "DELETE FROM `ordens_saida_locais` WHERE `id_local_ord_saida` = $idlocalos LIMIT 1";
    $q_drop_det_ord_saida_local = "DELETE FROM `ordens_saida_det` WHERE `cod_local_ord_saida` = $idlocalos LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_drop_ord_saida_local = $model->query( $q_drop_ord_saida_local );

    if ( !$q_drop_ord_saida_local ) {

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de exclusão ( $tipo_pag - LOCAL ). \n\n $valor_user.";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    // query executada apenas para confirmação
    // os detentos são excluidos pela FK
    $model->query( $q_drop_det_ord_saida_local );

    // fechando a conexao
    $model->closeConnection();

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'EXCLUSÃO DE ORDEM DE SAÍDA';
    $msg['text']     = "Exclusão de local de ordem de saída.\n\n[ ORDEM DE SAÍDA ]\n $s_ord_saida";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

} else if ( !empty( $dropdetos ) ){ // excluir detento ord_saida

    $idosd = empty( $idosd ) ? '' : (int)$idosd;

    if ( empty( $idosd ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " do ordem de saída em branco. Operação cancelada ( $tipo_pag - EXCLUSÃO DE " . SICOP_DET_DESC_U . " ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    // pegar os dados da ordem de saída, do local e id do detento
    $q_s_ord_saida = "SELECT
                        `ordens_saida`.`id_ord_saida`,
                        DATE_FORMAT( `ordens_saida`.`ord_saida_data`, '%d/%m/%Y' ) AS ord_saida_data_f,
                        DATE_FORMAT( `ord_saida_hora`, '%H:%i' ) AS `ord_saida_hora_f`,
                        `locais_apr`.`local_apr`,
                        `ordens_saida_det`.`cod_detento`
                      FROM
                        `ordens_saida`
                        INNER JOIN `ordens_saida_locais` ON `ordens_saida_locais`.`cod_ord_saida` = `ordens_saida`.`id_ord_saida`
                        INNER JOIN `locais_apr` ON `ordens_saida_locais`.`cod_local` = `locais_apr`.`idlocal`
                        INNER JOIN `ordens_saida_det` ON `ordens_saida_det`.`cod_local_ord_saida` = `ordens_saida_locais`.`id_local_ord_saida`
                      WHERE
                        `ordens_saida_det`.`id_ord_saida_det` = $idosd";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_s_ord_saida = $model->query( $q_s_ord_saida );

    // fechando a conexao
    $model->closeConnection();

    $d_ord_saida     = $q_s_ord_saida->fetch_assoc();
    $idos            = $d_ord_saida['id_ord_saida'];
    $ord_daida_data  = $d_ord_saida['ord_saida_data_f'];
    $ord_daida_hora  = $d_ord_saida['ord_saida_hora_f'];
    $ord_daida_local = $d_ord_saida['local_apr'];
    $s_ord_saida     = "<b>ID:</b> $idos; <b>Data:</b> $ord_daida_data";

    if ( !empty ( $ord_daida_hora ) ) $s_ord_saida .= "; <b>Hora:</b> $ord_daida_hora";

    $s_ord_saida    .= "; <b>Local:</b> $ord_daida_local";


    $idd = $d_ord_saida['cod_detento'];

    // pegar os dados do preso
    $detento = dados_det( $idd );

    $q_drop_det_ord_saida = "DELETE FROM `ordens_saida_det` WHERE `id_ord_saida_det` = $idosd LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_drop_det_ord_saida = $model->query( $q_drop_det_ord_saida );

    // fechando a conexao
    $model->closeConnection();

    if ( !$q_drop_det_ord_saida ) {

        /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de exclusão ( $tipo_pag - " . SICOP_DET_DESC_U . " ). \n\n $valor_user.";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'EXCLUSÃO DE ' . SICOP_DET_DESC_U . ' DE ORDEM DE SAÍDA';
    $msg['text']     = "Exclusão de " . SICOP_DET_DESC_L . " de ordem de saída.\n\n[ ORDEM DE SAÍDA ]\n $s_ord_saida \n\n $detento \n";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

} else if ( !empty( $dropos ) ){ // excluir ordem de saída

    $id_ord_saida = empty( $id_ord_saida ) ? '' : (int)$id_ord_saida;

    if ( empty( $id_ord_saida ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador do ordem de saída em branco. Operação cancelada ( $tipo_pag - EXCLUSÃO ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    // pegar os dados da ordem de saída
    $q_s_ord_saida = "SELECT
                        `ordens_saida`.`id_ord_saida`,
                        DATE_FORMAT( `ordens_saida`.`ord_saida_data`, '%d/%m/%Y' ) AS ord_saida_data_f,
                        DATE_FORMAT( `ordens_saida`.`ord_saida_hora`, '%H:%i' ) AS `ord_saida_hora_f`
                      FROM
                        `ordens_saida`
                      WHERE
                        `ordens_saida`.`id_ord_saida` = $id_ord_saida";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_s_ord_saida = $model->query( $q_s_ord_saida );

    // fechando a conexao
    $model->closeConnection();

    $d_ord_saida    = $q_s_ord_saida->fetch_assoc();
    $idos           = $d_ord_saida['id_ord_saida'];
    $ord_daida_data = $d_ord_saida['ord_saida_data_f'];
    $ord_daida_hora = $d_ord_saida['ord_saida_hora_f'];
    $s_ord_saida    = "<b>ID:</b> $idos; <b>Data:</b> $ord_daida_data";
    if ( !empty ( $ord_daida_hora ) ) $s_ord_saida .= "; <b>Hora:</b> $ord_daida_hora";


    $q_drop_ord_saida = "DELETE FROM `ordens_saida` WHERE `id_ord_saida` = $id_ord_saida LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_drop_ord_saida = $model->query( $q_drop_ord_saida );

    // fechando a conexao
    $model->closeConnection();

    if ( !$q_drop_ord_saida ) {

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de exclusão ( $tipo_pag ). \n\n $valor_user.";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );

        exit;

    }

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'EXCLUSÃO DE ORDEM DE SAÍDA';
    $msg['text']     = "Exclusão de ordem de saída.\n\n[ ORDEM DE SAÍDA ]\n $s_ord_saida";
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