<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';
$mensagem = '';

$n_rol = get_session( 'n_rol', 'int' );

if ( empty( $n_rol ) or $n_rol < 3 ) {

    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso à página de manipulação de observação de audiência SEM PERMISSÕES ( OBSERVAÇÃO DE AUDIÊNCIA )';
    $mensagem = get_msg( $msg, 1 );

    exit;

}

$is_post = is_post();

if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso direto à página de manipulação de observação de audiência.';
    get_msg( $msg, 1 );
    echo msg_js( '', 1 );
    exit;

}


extract( $_POST, EXTR_OVERWRITE );

$targ   = empty($targ) ? 0 : 1;
$proced = empty($proced) ? '' : (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO

if ( empty( $proced ) or $proced > 3 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Número de procedimento em branco ou inválido. Operação cancelada ( OBSERVAÇÃO DE AUDIÊNCIA ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    $saida = msg_js( 'FALHA!', 2 );
    if ( !empty( $targ ) ) $saida = msg_js( 'FALHA!', 'f' );
    echo $saida;

    exit;

}

$user = get_session( 'user_id', 'int' );
$ip   = "'" . $_SERVER['REMOTE_ADDR'] . "'";

if ( $proced == 1 ){ // ATUALIZAÇÃO
/*
 * -----------------------------------------------------------
 * PARTE RESPONSAVEL PELA ATUALIZAÇÃO
 * -----------------------------------------------------------
 */

    $id_obs_aud = empty( $id_obs_aud ) ? '' : (int)$id_obs_aud;

    if ( empty( $id_obs_aud ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = 'Identificador da observação em branco. Operação cancelada ( ATUALIZAÇÃO DE OBSERVAÇÃO DE AUDIÊNCIA ).';
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );

        exit;

    }

    // pegar os dados do audiência
    $aud_where = "( SELECT `cod_audiencia` FROM `obs_aud` WHERE `id_obs_aud` = $id_obs_aud LIMIT 1 )";
    $aud = dados_aud( $aud_where );

    // pegar os dados do preso
    $det_where = "( SELECT `cod_detento` FROM `audiencias` WHERE `idaudiencia` = $aud_where LIMIT 1 )";
    $detento = dados_det( $det_where );

    // pegar os dados da observação
    $obs_s = dados_obs( 'aud', $id_obs_aud );

    $obs_aud = empty( $obs_aud ) ? '' : tratastring( $obs_aud, 'U', FALSE );

    if ( empty( $obs_aud ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Observação em branco. Operação cancelada ( ATUALIZAÇÃO DE OBSERVAÇÃO DE AUDIÊNCIA ).\n\n $aud \n\n $detento \n\n $obs_s";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );

        exit;

    }

    $obs_aud = "'" . $obs_aud . "'";

    $query = "UPDATE
                    `obs_aud`
                  SET
                    `obs_aud` = $obs_aud,
                    `user_up` = $user,
                    `data_up` = NOW(),
                    `ip_up` = $ip
                  WHERE
                    `id_obs_aud` = $id_obs_aud
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

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de atualização de observação de audiência.\n\n $aud \n\n $detento \n\n $obs_s \n\n $valor_user \n";
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
    $msg['entre_ch'] = 'ATUALIZAÇÃO DE OBSERVAÇÃO DE AUDIÊNCIA';
    $msg['text']     = "Atualização de observação de audiência. \n\n $aud \n\n $detento \n\n $obs_s \n";

    get_msg( $msg, 1 );

    $ret = 2;
    if ( !empty( $targ ) ) $ret = 'f';

    echo msg_js( '', $ret );

    exit;
/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELA ATUALIZAÇÃO
 * -------------------------------------------------------------------
 */
} else if ( $proced == 2 ){ //EXCLUSÃO
/*
 * -------------------------------------------------------------------
 * PARTE RESPONSAVEL PELA EXCLUSÃO
 * -------------------------------------------------------------------
 */

    $n_rol = get_session( 'n_rol', 'int' );

    if ( empty( $n_rol ) or $n_rol < 4 ) {

        $tipo = 0;
        include '../init/msgnopag.php';

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'atn';
        $msg['text']  = "Tentativa de acesso à página de manipulação de observação de audiência SEM PERMISSÕES ( EXCLUSÃO DE OBSERVAÇÃO DE AUDIÊNCIA )";
        get_msg( $msg, 1 );

        exit;

    }

    $id_obs_aud = empty( $id_obs_aud ) ? '' : (int)$id_obs_aud;

    if ( empty( $id_obs_aud ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Identificador da observação em branco. Operação cancelada ( EXCLUSÃO DE OBSERVAÇÃO DE AUDIÊNCIA ).";
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 2 );

        exit;

    }

    // pegar os dados do audiência
    $aud_where = "( SELECT `cod_audiencia` FROM `obs_aud` WHERE `id_obs_aud` = $id_obs_aud LIMIT 1 )";
    $aud = dados_aud( $aud_where );

    // pegar os dados do preso
    $det_where = "( SELECT `cod_detento` FROM `audiencias` WHERE `idaudiencia` = $aud_where LIMIT 1 )";
    $detento = dados_det( $det_where );

    // pegar os dados da observação
    $obs_s = dados_obs( 'aud', $id_obs_aud );

    $query = "DELETE FROM `obs_aud` WHERE `id_obs_aud` = $id_obs_aud LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query = $model->query( $query );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if ( !$query ) {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de exclusão de observação de audiência.\n\n $aud \n\n $detento \n\n $obs_s \n\n $valor_user \n";
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
    $msg['entre_ch'] = 'EXCLUSÃO OBSERVAÇÃO DE AUDIÊNCIA';
    $msg['text']     = "Exclusão de observação de audiência. \n\n $aud \n\n $detento \n\n $obs_s";

    get_msg( $msg, 1 );

    $ret = 2;
    if ( !empty( $targ ) ) $ret = 'f';

    echo msg_js( '', $ret );

    exit;

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELA EXCLUSÃO
 * -------------------------------------------------------------------
 */
} else if ( $proced == 3 ){ //CADASTRAMENTO
/*
 * -------------------------------------------------------------------
 * PARTE RESPONSAVEL PELO CADASTRAMENTO
 * -------------------------------------------------------------------
 */

    $idaud = empty( $idaud ) ? '' : (int)$idaud;

    if ( empty( $idaud ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador do audiência em branco. Operação cancelada ( CADASTRAMENTO DE OBSERVAÇÃO DE AUDIÊNCIA ). \n\n Página: $pag";
        salvaLog( $mensagem );
        $saida = msg_js( 'FALHA!', 2 );
        if ( !empty( $targ ) ) $saida = msg_js( 'FALHA!', 'f' );
        echo $saida;
        exit;
    }

    // pegar os dados do audiência
    $aud = dados_aud( $idaud );

    // pegar os dados do preso
    $det_where = "( SELECT `cod_detento` FROM `audiencias` WHERE `idaudiencia` = $idaud LIMIT 1 )";
    $detento = dados_det( $det_where );

    $obs_aud  = empty( $obs_aud ) ? '' : tratastring( $obs_aud, 'U', FALSE );

    if ( empty( $obs_aud ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Observação em branco. Operação cancelada ( OBSERVAÇÃO DE AUDIÊNCIA - CADASTRAMENTO ). \n\n[ AUDIÊNCIA ]\n $aud \n\n $detento \n\n Página: $pag";
        salvaLog( $mensagem );
        $saida = msg_js( 'FALHA!', 2 );
        if ( !empty( $targ ) ) $saida = msg_js( 'FALHA!', 'f' );
        echo $saida;
        exit;
    }

    $obs_aud = "'" . $obs_aud . "'";

    $query = "INSERT INTO
                `obs_aud`
                (
                  `cod_audiencia`,
                  `obs_aud`,
                  `user_add`,
                  `data_add`,
                  `ip_add`
                )
              VALUES
                (
                  $idaud,
                  $obs_aud,
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
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Erro de cadastramento de observação de audiência.\n\n $aud \n\n $detento \n\n $valor_user \n";
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

    // pegar os dados da observação
    $obs_s = dados_obs( 'aud', $lastid );

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'desc';
    $msg['entre_ch'] = 'ATUALIZAÇÃO DE OBSERVAÇÃO DE AUDIÊNCIA';
    $msg['text']     = "Atualização de observação de audiência. \n\n $aud \n\n $detento \n\n $obs_s \n";

    get_msg( $msg, 1 );

    $saida = msg_js( '', 2 );
    if ( !empty( $targ ) ){
        $saida = "<script type='text/javascript'> window.opener.location.reload(); opener.location.href='../cadastro/detalaud.php?idaud=$idaud#obs'; self.window.close();</script>";
    }

    echo $saida;

    exit;

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELO CADASTRAMENTO
 * -------------------------------------------------------------------
 */
} else {
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Número de procedimento em branco ou inválido ( OBSERVAÇÃO DE AUDIÊNCIA ).";
    salvaLog( $mensagem );
    $saida = msg_js( 'FALHA!', 2 );
    if ( !empty( $targ ) ) $saida = msg_js( 'FALHA!', 'f' );
    echo $saida;
    exit;
}

?>
</body>
</html>


