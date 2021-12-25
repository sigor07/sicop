<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag = link_pag();
$tipo = '';
$mensagem = '';
$msg_saida = '';

$n_det_alt = get_session( 'n_det_alt', 'int' );

if ( empty( $n_det_alt ) or $n_det_alt < 1 ) {

    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'ALTERAÇÃO DE DADOS D' . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U;
    get_msg( $msg, 1 );

    exit;

}

$is_post = is_post();

if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso direto à página de manipulação de dados d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . '.';
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

extract( $_POST, EXTR_OVERWRITE );

$iddet = empty( $iddet ) ? '' : (int)$iddet;

if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Identificador d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . ' em branco. Operação cancelada ( ATUALIZAÇÃO DE ' . SICOP_DET_DESC_U . ' ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 1 );
    exit;

}

// pegar os dados do preso
$detento = dados_det( $iddet );

// instanciando o model
$model = SicopModel::getInstance();

$nome_det        = empty( $nome_det ) ? 'NULL' : "'" . tratastring( $nome_det ) . "'";
$artigo          = empty( $artigo ) ? 'NULL' : "'" . (int)$artigo . "'";
$outros_art      = empty( $outros_art ) ? 'NULL' : "'" . $outros_art . "'";
$matricula       = empty( $matricula ) ? 'NULL' : "'" . (int)preg_replace( '/[-.]/', '', $matricula ) . "'";
$rgcivil         = empty( $rgcivil ) ? 'NULL' : "'" . preg_replace( '/[-.]/', '', $rgcivil ) . "'";
$execucao        = empty( $execucao ) ? 'NULL' : "'" . (int)preg_replace( '/[-.]/', '', $execucao ) . "'";
$cpf             = empty( $cpf ) ? 'NULL' : "'" . (float)preg_replace( '/[-.]/', '', $cpf ) . "'";
$vulgo           = empty( $vulgo ) ? 'NULL' : "'" . tratastring( $vulgo ) . "'";
$dados_prov      = empty( $dados_prov ) ? 0 : 1;
$jaleco          = empty( $jaleco ) ? 0 : 1;
$calca           = empty( $calca ) ? 0 : 1;
$nacionalidade   = empty( $nacionalidade ) ? 'NULL' : "'" . (int)$nacionalidade . "'";
$cidade          = empty( $cidade ) ? 'NULL' : "'" . (int)$cidade . "'";
$nasc_det        = empty( $nasc_det ) ? 'NULL' : "'" .  $model->escape_string( $nasc_det ) . "'";
$profissao       = empty( $profissao ) ? 'NULL' : "'" . tratastring( $profissao ) . "'";
$est_civil       = empty( $est_civil ) ? 'NULL' : "'" . (int)$est_civil . "'";
$instrucao       = empty( $instrucao ) ? 'NULL' : "'" . (int)$instrucao . "'";
$nome_pai_det    = empty( $nome_pai_det ) ? 'NULL' : "'" . tratastring( $nome_pai_det ) . "'";
$nome_mae_det    = empty( $nome_mae_det ) ? 'NULL' : "'" . tratastring( $nome_mae_det ) . "'";
$cutis           = empty( $cutis ) ? 'NULL' : "'" . (int)$cutis . "'";
$cabelo          = empty( $cabelo ) ? 'NULL' : "'" . (int)$cabelo . "'";
$olhos           = empty( $olhos ) ? 'NULL' : "'" . (int)$olhos . "'";
$estatura        = empty( $estatura ) ? 'NULL' : "'" . (int)preg_replace( '/[-.,]/', '', $estatura ) . "'";
$peso            = empty( $peso ) ? 'NULL' : "'" . (int)$peso . "'";
$defeito_fisico  = empty( $defeito_fisico ) ? 'NULL' : "'" . tratastring( $defeito_fisico ) . "'";
$sinal_nasc      = empty( $sinal_nasc ) ? 'NULL' : "'" . tratastring( $sinal_nasc ) . "'";
$cicatrizes      = empty( $cicatrizes ) ? 'NULL' : "'" . tratastring( $cicatrizes ) . "'";
$tatuagens       = empty( $tatuagens ) ? 'NULL' : "'" . tratastring( $tatuagens ) . "'";
$local_prisao    = empty( $local_prisao ) ? 'NULL' : "'" . (int)$local_prisao . "'";
$data_prisao     = empty( $data_prisao ) ? 'NULL' : "'" . $model->escape_string( $data_prisao ) . "'";
$primario        = empty( $primario ) ? 0 : 1; // VERIFICAR
$sit_proc        = empty( $sit_proc ) ? 'NULL' : "'" . (int)$sit_proc . "'";
$prisoes_ant     = empty( $prisoes_ant ) ? 'NULL' : "'" . tratastring( replace_names_unidades ( $prisoes_ant ) ) . "'";
$fuga            = empty( $fuga ) ? 0 : 1;
$local_fuga      = empty( $local_fuga ) ? 'NULL' : "'" . tratastring( $local_fuga ) . "'";
$resid_det       = empty( $resid_det ) ? 'NULL' : "'" . tratastring( $resid_det ) . "'";
$caso_emergencia = empty( $caso_emergencia ) ? 'NULL' : "'" . tratastring( $caso_emergencia ) . "'";
$religiao        = empty( $religiao ) ? 'NULL' : "'" . (int)$religiao . "'";
$possui_adv      = empty( $possui_adv ) ? 0 : 1;
$pl              = empty( $pl ) ? 'NULL' : "'" . tratastring( $pl ) . "'";
$guia_local      = empty( $guia_local ) ? 'NULL' : "'" . tratastring( $guia_local ) . "'";
$guia_numero     = empty( $guia_numero ) ? 'NULL' : "'" . tratastring( $guia_numero ) . "'";


$user = get_session( 'user_id', 'int' );
$ip   = "'" . $_SERVER['REMOTE_ADDR'] . "'";

$query = "UPDATE
               `detentos`
             SET
               `nome_det` = $nome_det,
               `cod_artigo` = $artigo,
               `matricula` = $matricula,
               `rg_civil` = $rgcivil,
               `execucao` = $execucao,
               `cpf` = $cpf,
               `vulgo` = $vulgo,
               `cod_nacionalidade` = $nacionalidade,
               `cod_cidade` = $cidade,
               `nasc_det` = STR_TO_DATE( $nasc_det, '%d/%m/%Y' ),
               `profissao` = $profissao,
               `cod_est_civil` = $est_civil,
               `cod_instrucao` = $instrucao,
               `pai_det` = $nome_pai_det,
               `mae_det` = $nome_mae_det,
               `data_prisao` = STR_TO_DATE( $data_prisao, '%d/%m/%Y' ),
               `cod_local_prisao` = $local_prisao,
               `primario` = $primario,
               `cod_sit_proc` = $sit_proc,
               `prisoes_ant` = $prisoes_ant,
               `fuga` = $fuga,
               `local_fuga` = $local_fuga,
               `cod_cutis` = $cutis,
               `cod_cabelos` = $cabelo,
               `cod_olhos` = $olhos,
               `estatura` = $estatura,
               `peso` = $peso,
               `defeito_fisico` = $defeito_fisico,
               `sinal_nasc` = $sinal_nasc,
               `cicatrizes` = $cicatrizes,
               `tatuagens` = $tatuagens,
               `resid_det` = $resid_det,
               `cod_religiao` = $religiao,
               `possui_adv` = $possui_adv,
               `caso_emergencia` = $caso_emergencia,
               `obs_artigos` = $outros_art,
               `dados_prov` = $dados_prov,
               `jaleco` = $jaleco,
               `calca` = $calca,
               `pl` = $pl,
               `guia_local` = $guia_local,
               `guia_numero` = $guia_numero,
               `user_up` = $user,
               `data_up` = NOW(),
               `ip_up` = $ip
             WHERE
               `iddetento` = $iddet
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
    $msg['text']  = "Erro de atualização de " . SICOP_DET_DESC_L . ". \n\n $detento \n\n $valor_user";
    $msg['linha'] = __LINE__;

    get_msg( $msg, 1 );

    $msg_saida = 'FALHA!!!';
    $errno = $model->getErrorNum();
    if ( $errno == 1062 ) $msg_saida = 'Matrícula já cadastrada! Verifique!';

    echo msg_js( $msg_saida, 2 );

    exit;

}

// montar a mensagem q será salva no log
$msg = array();
$msg['tipo']     = 'desc';
$msg['entre_ch'] = 'ATUALIZAÇÃO DE ' . SICOP_DET_DESC_U;
$msg['text']     = "Atualização de dados de " . SICOP_DET_DESC_L . ". \n\n $detento";

get_msg( $msg, 1 );

echo msg_js( '', 2 );

exit;

?>
</body>
</html>