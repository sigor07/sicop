<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';
$mensagem = '';

/* colocar o tipo de página, o setor que ela acessa
 * ex: PECÚLIO, CADASTRO, INCLUSÃO...
 */
$tipo_pag = 'MOVIMENTAÇÃO DE ' . SICOP_DET_DESC_U . ' - ACERVO';

/*
 * define a variavel $n_acesso de acordo com o setor
 */

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

$targ   = empty( $targ ) ? 0 : 1;
$cadadd = empty( $cadadd ) ? 0 : 1;

$user = get_session( 'user_id', 'int' );
$ip   = "'" . $_SERVER['REMOTE_ADDR'] . "'";

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

$tipo_mov  = empty( $tipo_mov ) ? 'NULL' : (int)$tipo_mov;
$local_mov = empty( $local_mov ) ? 'NULL' : (int)$local_mov;
$data_mov  = empty( $data_mov ) ? '' : $data_mov;
if ( empty( $data_mov ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Data da movimentação em branco. Operação cancelada ( $proced_tipo_pag ). \n\n $detento";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$q_ult_mov_in = "SELECT
                   DATE_FORMAT( `mov_det`.`data_mov`, '%d/%m/%Y' ) As data_mov_f
                 FROM
                   `mov_det`
                   LEFT JOIN `tipomov` ON `mov_det`.`cod_tipo_mov` = `tipomov`.`idtipo_mov`
                   LEFT JOIN `unidades` ON `mov_det`.`cod_local_mov` = `unidades`.`idunidades`
                 WHERE
                   `mov_det`.`cod_detento` = $iddet
                   AND
                   `mov_det`.`cod_tipo_mov` IN( 1, 2, 3 )
                 ORDER BY
                   `mov_det`.`data_mov` DESC,
                   `mov_det`.`data_add` DESC
                 LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_ult_mov_in = $model->query( $q_ult_mov_in );

// fechando a conexao
$model->closeConnection();

if ( !$q_ult_mov_in ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta da data da ultima movimentação ( ÚLTIMA MOVIMENTAÇÃO - $proced_tipo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    $ret = 1;
    if ( !empty( $targ ) ) $ret = 'f';
    echo msg_js( 'FALHA!!!', $ret );
    exit;

}

$cont_ult_mov = $q_ult_mov_in->num_rows;
if ( $cont_ult_mov == 1 ) {

    $d_ult_mov_in = $q_ult_mov_in->fetch_assoc();
    $data_ult_mov = $d_ult_mov_in['data_mov_f'];

    $partes_ult = explode( '/', $data_ult_mov );
    $time_data_mov_ult = mktime( 0, 0, 0, $partes_ult[1], $partes_ult[0], $partes_ult[2] );

    $partes_atu = explode( '/', $data_mov );
    $time_data_mov_atu = mktime( 0, 0, 0, $partes_atu[1], $partes_atu[0], $partes_atu[2] );

    if ( $time_data_mov_atu > $time_data_mov_ult ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "Data da movimentação posterior à da última movimentação de inclusão. Operação cancelada ( $proced_tipo_pag ). \n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!!!', 1 );
        exit;

    }

}

$data_mov = "'" . $model->escape_string( $data_mov ) . "'";

$query = "INSERT INTO
            `mov_det`
            (
              `cod_detento`,
              `cod_tipo_mov`,
              `cod_local_mov`,
              `data_mov`,
              `user_add`,
              `data_add`,
              `ip_add`
            )
          VALUES
            (
              $iddet,
              $tipo_mov,
              $local_mov,
              STR_TO_DATE( $data_mov, '%d/%m/%Y' ),
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
    $msg['text']  = "Erro de cadastramento de movimentação de deteno no acervo ( $tipo_pag ). \n\n $detento \n\n $valor_user.";
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

// pegar os dados da movimentação
$mov = dados_mov( $lastid, 1 );

// montar a mensagem q será salva no log
$msg = array();
$msg['tipo']     = 'desc';
$msg['entre_ch'] = 'CADASTRAMENTO DE MOVIMENTAÇÃO DE ' . SICOP_DET_DESC_U . ' - ACERVO';
$msg['text']     = 'Cadastramento de movimentação de ' . SICOP_DET_DESC_L . " no acervo. \n\n $mov \n\n $detento ";

get_msg( $msg, 1 );

$ret = 2;
if ( !empty( $targ ) ) $ret = 'rf';
if ( !empty( $cadadd ) ) $ret = 1;

echo msg_js( '', $ret );

exit;

?>
</body>
</html>
