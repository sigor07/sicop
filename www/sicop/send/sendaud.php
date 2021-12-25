<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';
$mensagem = '';

/*
 * colocar o tipo de página, o setor que ela acessa
 * ex: PECÚLIO, CADASTRO, INCLUSÃO...
 */
$tipo_pag = 'AUDIÊNCIA';

$n_acesso = get_session( 'n_cadastro', 'int' );
if ( empty( $n_acesso ) or $n_acesso < 3 ) {

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

$targ   = empty($targ) ? 0 : 1;
$proced = empty( $proced ) ? '' : (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO

if ( empty( $proced ) or $proced > 3 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Número de procedimento em branco ou inválido. Operação cancelada ( $tipo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    $ret = 1;
    if ( !empty( $targ ) ) $ret = 'f';
    echo msg_js( 'FALHA!', $ret );

    exit;

}

$user = get_session( 'user_id', 'int' );
$ip   = "'" . $_SERVER['REMOTE_ADDR'] . "'";

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

    $idaud = empty( $idaud ) ? '' : (int)$idaud;
    if ( empty( $idaud ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador da audência em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 'f';
        echo msg_js( 'FALHA!', $ret );

        exit;

    }

    // pegar os dados da audiência
    $audiencia = dados_aud( $idaud );

    // pegar os dados do preso
    $where_det = "( SELECT `cod_detento` FROM `audiencias` WHERE `idaudiencia` = $idaud LIMIT 1 )";
    $detento = dados_det( $where_det );

    $data_aud = empty( $data_aud ) ? '' : $data_aud;
    if ( empty( $data_aud ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Data da audiência em branco. Operação cancelada ( $proced_tipo_pag ).\n\n $audiencia \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    // verificar se a data é válida
    if ( !validaData( $data_aud, 'DD/MM/AAAA' ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Data da audiência inválida. Operação cancelada ( $proced_tipo_pag ).\n\n $audiencia \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    // instanciando o model
    $model = SicopModel::getInstance();

    $data_aud = "'" . $model->escape_string( $data_aud ) . "'";

    $hora_aud = empty( $hora_aud ) ? '' : "'" . $model->escape_string( $hora_aud ) . "'";
    if ( empty( $hora_aud ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Hora da audiência em branco. Operação cancelada ( $proced_tipo_pag ).\n\n $audiencia \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $local_aud = empty( $local_aud ) ? '' : "'" . tratastring( $local_aud, 'U', false ) . "'";
    if ( empty( $local_aud ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Local da audiência em branco. Operação cancelada ( $proced_tipo_pag ).\n\n $audiencia \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $cidade_aud   = empty( $cidade_aud ) ? 'NULL' : "'" . tratastring( $cidade_aud, 'U', false ) . "'";
    if ( empty( $cidade_aud ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Cidade da audiência em branco. Operação cancelada ( $proced_tipo_pag ).\n\n $audiencia \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $tipo_aud     = empty( $tipo_aud ) ? '' : (int)$tipo_aud;
    if ( empty( $tipo_aud ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Tipo da audiência em branco. Operação cancelada ( $proced_tipo_pag ).\n\n $audiencia \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $num_processo = empty( $num_processo ) ? 'NULL' : "'" . tratastring( $num_processo ) . "'";

    $sit_aud = empty( $sit_aud ) ? '' : (int)$sit_aud;
    if ( empty( $sit_aud ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Situação da audiência em branco. Operação cancelada ( $proced_tipo_pag ).\n\n $audiencia \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $motivo_justi = empty( $motivo_justi ) ? '' : tratastring( $motivo_justi, 'U', FALSE );
    if ( $sit_aud == 11 ) {

        $motivo_justi = 'NULL';

    } else {

        if ( empty( $motivo_justi ) ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']  = 'err';
            $msg['text']  = "Situação da audiência em branco. Operação cancelada ( $proced_tipo_pag ).\n\n $audiencia \n\n $detento";
            $msg['linha'] = __LINE__;
            get_msg( $msg, 1 );

            echo msg_js( 'FALHA!', 2 );
            exit;

        } else {

            $motivo_justi = "'" . $motivo_justi . "'";

        }

    }

    $query = "UPDATE
                `audiencias`
              SET
                `data_aud` = STR_TO_DATE( $data_aud, '%d/%m/%Y' ),
                `hora_aud` = STR_TO_DATE( $hora_aud, '%H:%i' ),
                `local_aud` = $local_aud,
                `cidade_aud` = $cidade_aud,
                `tipo_aud` = $tipo_aud,
                `num_processo` = $num_processo,
                `sit_aud` = $sit_aud,
                `motivo_justi` = $motivo_justi,
                `user_up` = $user,
                `data_up` = NOW(),
                `ip_up` = $ip
              WHERE
                `idaudiencia` = $idaud
              LIMIT 1";

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
        $msg['text']  = "Erro de atualização ( $tipo_pag ).\n\n $audiencia \n\n $detento \n\n $valor_user \n.";
        $msg['linha'] = __LINE__;

        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 'f';

        echo msg_js( 'FALHA!!!', $ret );

        exit;

    }

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'ATUALIZAÇÃO DE AUDIÊNCIA';
    $msg['text']     = "Atualização de audiência.\n\n $audiencia \n\n $detento ";

    get_msg( $msg, 1 );

    salvaLog( $mensagem );

    $ret = 2;
    if ( !empty( $targ ) ) $ret = 'f';

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
        $msg = array();
        $msg['tipo']     = 'perm';
        $msg['entre_ch'] = $proced_tipo_pag;
        get_msg( $msg, 1 );

        exit;

    }

    $idaud = empty( $idaud ) ? '' : (int)$idaud;
    if ( empty( $idaud ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador da audência em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 1;
        if ( !empty( $targ ) ) $ret = 'f';
        echo msg_js( 'FALHA!', $ret );

        exit;

    }

    // pegar os dados da audiência
    $audiencia = dados_aud( $idaud );

    // pegar os dados do preso
    $where_det = "( SELECT `cod_detento` FROM `audiencias` WHERE `idaudiencia` = $idaud LIMIT 1 )";
    $detento = dados_det( $where_det );

    $query = "DELETE FROM `audiencias` WHERE `idaudiencia` = $idaud LIMIT 1";

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
        $msg['text']  = "Erro de exclusão ( $tipo_pag ).\n\n $audiencia \n\n $detento \n\n $valor_user \n";
        $msg['linha'] = __LINE__;

        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 'f';

        echo msg_js( 'FALHA!!!', $ret );

        exit;

    }

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'EXCLUSÃO DE AUDIÊNCIA';
    $msg['text']     = "Exclusão de audiência.\n\n $audiencia \n\n $detento ";

    get_msg( $msg, 1 );

    redir( 'cadastro/buscaaud' );

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
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. Operação cancelada ( $proced_tipo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 1;
        echo msg_js( 'FALHA!', $ret );

        exit;

    }

    // pegar os dados do preso
    $detento = dados_det( $iddet );

    $data_aud = empty( $data_aud ) ? '' : $data_aud;
    if ( empty( $data_aud ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Data da audiência em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    // verificar se a data é válida
    if ( !validaData( $data_aud, 'DD/MM/AAAA' ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Data da audiência inválida. Operação cancelada ( $proced_tipo_pag ). \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    // instanciando o model
    $model = SicopModel::getInstance();

    $data_aud = "'" . $model->escape_string( $data_aud ) . "'";

    $hora_aud = empty( $hora_aud ) ? '' : "'" . $model->escape_string( $hora_aud ) . "'";
    if ( empty( $hora_aud ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Hora da audiência em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $local_aud = empty( $local_aud ) ? '' : "'" . tratastring( $local_aud, 'U', false ) . "'";
    if ( empty( $local_aud ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Local da audiência em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $cidade_aud   = empty( $cidade_aud ) ? 'NULL' : "'" . tratastring( $cidade_aud, 'U', false ) . "'";
    if ( empty( $cidade_aud ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Cidade da audiência em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $tipo_aud     = empty( $tipo_aud ) ? '' : (int)$tipo_aud;
    if ( empty( $tipo_aud ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Tipo da audiência em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $num_processo = empty( $num_processo ) ? 'NULL' : "'" . tratastring( $num_processo ) . "'";

    $sit_aud = empty( $sit_aud ) ? '' : (int)$sit_aud;
    if ( empty( $sit_aud ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Situação da audiência em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );
        exit;

    }

    $motivo_justi = empty( $motivo_justi ) ? '' : tratastring( $motivo_justi, 'U', FALSE );
    if ( $sit_aud == 11 ) {

        $motivo_justi = 'NULL';

    } else {

        if ( empty( $motivo_justi ) ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']  = 'err';
            $msg['text']  = "Situação da audiência em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $detento";
            $msg['linha'] = __LINE__;
            get_msg( $msg, 1 );

            echo msg_js( 'FALHA!', 2 );
            exit;

        } else {

            $motivo_justi = "'" . $motivo_justi . "'";

        }

    }


    $query = "INSERT INTO
                `audiencias`
                (
                  `cod_detento`,
                  `data_aud`,
                  `hora_aud`,
                  `local_aud`,
                  `cidade_aud`,
                  `tipo_aud`,
                  `num_processo`,
                  `sit_aud`,
                  `motivo_justi`,
                  `user_add`,
                  `data_add`,
                  `ip_add`
                )
              VALUES
                (
                  $iddet,
                  STR_TO_DATE($data_aud, '%d/%m/%Y'),
                  STR_TO_DATE($hora_aud, '%H:%i'),
                  $local_aud,
                  $cidade_aud,
                  $tipo_aud,
                  $num_processo,
                  $sit_aud,
                  $motivo_justi,
                  $user,
                  NOW(),
                  $ip
                )";

    $lastid  = '';

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
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de cadastramento ( $tipo_pag ). \n\n $detento \n\n $valor_user \n";
        $msg['linha'] = __LINE__;

        get_msg( $msg, 1 );

        $ret = 2;
        if ( !empty( $targ ) ) $ret = 'f';

        echo msg_js( 'FALHA!!!', $ret );

        exit;

    }

    $lastid = $model->lastInsertId();

    // fechando a conexao
    $model->closeConnection();

    // pegar os dados da audiência
    $audiencia = dados_aud( $lastid );

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'CADASTRAMENTO DE AUDIÊNCIA';
    $msg['text']     = "Cadastramento de audiência.\n\n $audiencia \n\n $detento ";

    get_msg( $msg, 1 );

    $_SESSION['l_id_aud'] = $lastid;
    redir( 'cadastro/cadaudok' );

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
