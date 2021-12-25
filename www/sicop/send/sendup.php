<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag = link_pag();
$tipo = '';

$tipo_pag = 'ATUALIZAÇÃO DE DADOS DA UNIDADE';

$n_admsist = get_session( 'n_admsist', 'int' );

$nivel_necessario = 3;
if ( $n_admsist < $nivel_necessario ) {

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
    $msg['text'] = "Tentativa de acesso direto à página. ( $tipo_pag )";
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 1 );
    exit;

}

extract( $_POST, EXTR_OVERWRITE );


$secretaria    = "'" . tratabasico( $secretaria ) . "'";
$coordenadoria = "'" . tratabasico( $coordenadoria ) . "'";
$unidadelongo  = "'" . tratabasico( $unidadelongo ) . "'";
$unidadecurto  = "'" . tratabasico( $unidadecurto ) . "'";
$endereco      = "'" . tratabasico( $endereco ) . "'";
$enderecocurto = "'" . tratabasico( $enderecocurto ) . "'";
$cidade        = "'" . tratabasico( $cidade ) . "'";
$email         = "'" . tratabasico( $email ) . "'";
//$titulo        = "'" . tratabasico( $titulo ) . "'";

$query_up = "UPDATE
               `sicop_unidade`
             SET
               `secretaria` = $secretaria,
               `coord` = $coordenadoria,
               `unidade_sort` = $unidadecurto,
               `unidade_long` = $unidadelongo,
               `endereco` = $endereco,
               `endereco_sort` = $enderecocurto,
               `cidade` = $cidade,
               `email` = $email
              WHERE
                `idup` = 1
              LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_up = $model->query( $query_up );

// fechando a conexao
$model->closeConnection();

$success = TRUE;
if( !$query_up ) {

    $success = FALSE;

    /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
    $valor_user = valor_user( $_POST );

    // montar a mensagem q será salva no log
    $msg = array( );
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
$msg['entre_ch'] = 'ATUALIZAÇÃO DE DADOS DA UNIDADE';
$msg['text']     = 'Atualização de dados da unidade prisional.';
get_msg( $msg, 1 );

echo msg_js( '', 2 );

exit;

?>