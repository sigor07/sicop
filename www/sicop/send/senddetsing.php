<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag = link_pag();
$tipo = '';
$mensagem = '';
$msg_saida = '';

$n_cadastro = get_session( 'n_cadastro', 'int' );
$n_chefia   = get_session( 'n_chefia', 'int' );
$nivel_necessario = 3;

if ( ( $n_cadastro < $nivel_necessario ) and ( $n_chefia < $nivel_necessario ) ) {

    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'perm';
    $msg['entre_ch'] = 'CADASTRAMENTO DE ' . SICOP_DET_DESC_U;
    get_msg( $msg, 1 );

    exit;
}

$is_post = is_post();

if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'atn';
    $msg['text'] = 'Tentativa de acesso direto à página de manipulação de dados d' . SICOP_DET_DESC_L . '.';
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

// instanciando o model
$model = SicopModel::getInstance();

extract( $_POST, EXTR_OVERWRITE );

$nome_det      = empty( $nome_det ) ? 'NULL' : "'" . tratastring( $nome_det ) . "'";
$artigo        = empty( $artigo ) ? 'NULL' : "'" . $artigo . "'";
$matricula     = empty( $matricula ) ? 'NULL' : "'" . (int)preg_replace( '/[-.]/', '', $matricula ) . "'";
$rgcivil       = empty( $rgcivil ) ? 'NULL' : "'" . preg_replace( '/[-.]/', '', $rgcivil ) . "'";
$execucao      = empty( $execucao ) ? 'NULL' : "'" . (int)preg_replace( '/[-.]/', '', $execucao ) . "'";
$cpf           = empty( $cpf ) ? 'NULL' : "'" . (float)preg_replace( '/[-.]/', '', $cpf ) . "'";
$vulgo         = empty( $vulgo ) ? 'NULL' : "'" . tratastring( $vulgo ) . "'";
$dados_prov    = empty( $dados_prov ) ? 0 : 1;
$nacionalidade = empty( $nacionalidade ) ? 'NULL' : "'" . (int)$nacionalidade . "'";
$cidade        = empty( $cidade ) ? 'NULL' : "'" . (int)$cidade . "'";
$nasc_det      = empty( $nasc_det ) ? 'NULL' : "'" . $model->escape_string( $nasc_det ) . "'";
$profissao     = empty( $profissao ) ? 'NULL' : "'" . tratastring( $profissao ) . "'";
$nome_pai_det  = empty( $nome_pai_det ) ? 'NULL' : "'" . tratastring( $nome_pai_det ) . "'";
$nome_mae_det  = empty( $nome_mae_det ) ? 'NULL' : "'" . tratastring( $nome_mae_det ) . "'";
$fuga          = empty( $fuga ) ? 0 : 1;
$local_fuga    = empty( $local_fuga ) ? 'NULL' : "'" . tratastring( $local_fuga ) . "'";
$local_prisao  = empty( $local_prisao ) ? 'NULL' : "'" . (int)$local_prisao . "'";
$data_prisao   = empty( $data_prisao ) ? 'NULL' : "'" . $model->escape_string( $data_prisao ) . "'";
$pl            = empty( $pl ) ? 'NULL' : "'" . tratastring( $pl ) . "'";
$guia_local    = empty( $guia_local ) ? 'NULL' : "'" . tratastring( $guia_local ) . "'";
$guia_numero   = empty( $guia_numero ) ? 'NULL' : "'" . tratastring( $guia_numero ) . "'";
$primario      = empty( $primario ) ? 0 : 1;

$user = get_session( 'user_id', 'int' );
$ip   = "'" . $_SERVER['REMOTE_ADDR'] . "'";

$query = "INSERT INTO
            `detentos`
            (
              `nome_det`,
              `cod_artigo`,
              `matricula`,
              `rg_civil`,
              `execucao`,
              `cpf`,
              `vulgo`,
              `cod_nacionalidade`,
              `cod_cidade`,
              `nasc_det`,
              `profissao`,
              `pai_det`,
              `mae_det`,
              `data_prisao`,
              `cod_local_prisao`,
              `primario`,
              `fuga`,
              `local_fuga`,
              `dados_prov`,
              `pl`,
              `guia_local`,
              `guia_numero`,
              `user_add`,
              `data_add`,
              `ip_add`
            )
          VALUES
            (
              $nome_det,
              $artigo,
              $matricula,
              $rgcivil,
              $execucao,
              $cpf,
              $vulgo,
              $nacionalidade,
              $cidade,
              STR_TO_DATE( $nasc_det, '%d/%m/%Y' ),
              $profissao,
              $nome_pai_det,
              $nome_mae_det,
              STR_TO_DATE( $data_prisao, '%d/%m/%Y' ),
              $local_prisao,
              $primario,
              $fuga,
              $local_fuga,
              $dados_prov,
              $pl,
              $guia_local,
              $guia_numero,
              $user,
              NOW(),
              $ip
            )";

// executando a query
$query = $model->query( $query );

$success = TRUE;
if ( !$query ) {

    $success = FALSE;

    /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
    $valor_user = valor_user( $_POST );

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Erro de cadastramento de " . SICOP_DET_DESC_L . ". \n\n $valor_user";
    $msg['linha'] = __LINE__;

    get_msg( $msg, 1 );

    $msg_saida = 'FALHA!!!';

    $errno = $model->getErrorNum();

    if ( $errno == 1062 ) $msg_saida = 'Matrícula já cadastrada! Verifique!';

    echo msg_js( "$msg_saida", 2 );

    exit;

}

$l_id_det = $model->lastInsertId();

// fechando a conexao
$model->closeConnection();

// pegar os dados do preso
$detento = dados_det( $l_id_det );

$msg = array();
$msg['tipo']     = 'desc';
$msg['entre_ch'] = 'CADASTRAMENTO DE ' . SICOP_DET_DESC_U;
$msg['text']     = "Cadastramento de " . SICOP_DET_DESC_L . ". \n\n $detento";

get_msg( $msg, 1 );

$_SESSION['l_id_det'] = $l_id_det;

header( 'Location: ../detento/caddetok.php' );

exit;

?>
</body>
</html>