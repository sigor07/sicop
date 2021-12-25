<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';
$mensagem = '';

$n_pront = get_session( 'n_pront', 'int' );

if ( empty( $n_pront ) or $n_pront < 3 ) {
    $tipo = 0;
    include '../init/msgnopag.php';
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso à página de manipulação de observação de grade SEM PERMISSÕES. \n\n Página: $pag";
    salvaLog($mensagem);
    exit;
}

$is_post = is_post();

if ( !$is_post ) {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso direto à página de manipulação de grade.<br /><br /> Página: $pag";
    salvaLog($mensagem);
    echo msg_js( '', 1 );
    exit;
}


extract( $_POST, EXTR_OVERWRITE );

$proced = empty($proced) ? '' : (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO

if ( empty( $proced ) or $proced > 3 ) {
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Número de procedimento em branco ou inválido. Operação cancelada ( OBSERVAÇÃO DE GRADE ).\n\n Página: $pag";
    salvaLog($mensagem);
    $saida = msg_js( 'FALHA!', 2 );
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

    $idprocesso     = empty( $idprocesso ) ? '' : (int)$idprocesso;

    if ( empty( $idprocesso ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador do processo em branco. Operação cancelada ( ATUALIZAÇÃO DE PROCESSO ). \n\n Página: $pag";
        salvaLog( $mensagem );
        echo msg_js( 'FALHA!', 1 );
        exit;
    }

    // pegar os dados do preso
    $det_where = "( SELECT `cod_detento` FROM `grade` WHERE `idprocesso` = $idprocesso LIMIT 1 )";
    $detento = dados_det( $det_where );

    // instanciando o model
    $model = SicopModel::getInstance();

    $gra_num_exec    = empty( $gra_num_exec ) ? 'NULL' : (int)$gra_num_exec;
    $gra_num_in      = empty( $gra_num_in ) ? 'NULL' : (int)$gra_num_in;
    $gra_num_inq     = empty( $gra_num_inq ) ? 'NULL' : "'".$model->escape_string($gra_num_inq)."'";
    $gra_f_p         = empty( $gra_f_p ) ? 'NULL' : "'".tratastring($gra_f_p)."'";
    $gra_preso       = empty( $gra_preso ) ? '0' : '1';
    $gra_num_proc    = empty( $gra_num_proc ) ? 'NULL' : "'".$model->escape_string($gra_num_proc)."'";
    $gra_data_delito = empty( $gra_data_delito ) ? 'NULL' : "'".$model->escape_string($gra_data_delito)."'";
    $gra_data_sent   = empty( $gra_data_sent ) ? 'NULL' : "'".$model->escape_string($gra_data_sent)."'";
    $gra_vara        = empty( $gra_vara ) ? 'NULL' : "'".tratastring($gra_vara, 'U', false)."'";
    $gra_comarca     = empty( $gra_comarca ) ? 'NULL' : "'".tratastring($gra_comarca, 'U', false)."'";
    $gra_p_ano       = empty( $gra_p_ano ) ? 'NULL' : (int)$gra_p_ano;
    $gra_p_mes       = empty( $gra_p_mes ) ? 'NULL' : (int)$gra_p_mes;
    $gra_p_dia       = empty( $gra_p_dia ) ? 'NULL' : (int)$gra_p_dia;
    $gra_med_seg     = empty( $gra_med_seg ) ? '0' : '1';
    $gra_hediondo    = empty( $gra_hediondo ) ? '0' : '1';
    $gra_campo_x     = empty( $gra_campo_x ) ? '0' : '1';
    $gra_consumado   = empty( $gra_consumado ) ? '0' : '1';
    $gra_fed         = empty( $gra_fed ) ? '0' : '1';
    $gra_outro_est   = empty( $gra_outro_est ) ? '0' : '1';
    $gra_artigos     = empty( $gra_artigos ) ? 'NULL' : "'".tratastring($gra_artigos, 'N', false)."'";
    $gra_regime      = empty( $gra_regime ) ? 'NULL' : "'".tratastring($gra_regime, 'U', false)."'";
    $gra_sit_atual   = empty( $gra_sit_atual ) ? 'NULL' : "'".tratastring($gra_sit_atual, 'U', false)."'";
    $gra_obs         = empty( $gra_obs ) ? 'NULL' : "'".tratastring($gra_obs, 'U', false)."'";

    // pegar os dados do processo
    $q_s_proc = "SELECT
                   `gra_num_inq`,
                   `gra_num_proc`
                 FROM
                   `grade`
                 WHERE
                   `idprocesso` = $idprocesso
                 LIMIT 1";

    // executando a query
    $q_s_proc = $model->query( $q_s_proc );

    $d_s_proc = $q_s_proc->fetch_assoc();
    $inq_s    = $d_s_proc['gra_num_inq'];
    $nproc_s  = $d_s_proc['gra_num_proc'];
    $proc_s   = "<b>ID:</b> $idprocesso;";
    if ( !empty( $inq_s ) ) $proc_s .= " <b>Número do inquérito:</b> $inq_s;";
    if ( !empty( $nproc_s ) ) $proc_s .= " <b>Número do processo:</b> $nproc_s";

    $query_grade = "UPDATE
                      `grade`
                    SET
                      `gra_preso` = $gra_preso,
                      `gra_num_in` = $gra_num_in,
                      `gra_num_exec` = $gra_num_exec,
                      `gra_num_inq` = $gra_num_inq,
                      `gra_f_p` = $gra_f_p,
                      `gra_num_proc` = $gra_num_proc,
                      `gra_campo_x` = $gra_campo_x,
                      `gra_med_seg` = $gra_med_seg,
                      `gra_hediondo` = $gra_hediondo,
                      `gra_fed` = $gra_fed,
                      `gra_outro_est` = $gra_outro_est,
                      `gra_consumado` = $gra_consumado,
                      `gra_vara` = $gra_vara,
                      `gra_comarca` = $gra_comarca,
                      `gra_artigos` = $gra_artigos,
                      `gra_data_delito` = STR_TO_DATE($gra_data_delito, '%d/%m/%Y'),
                      `gra_data_sent` = STR_TO_DATE($gra_data_sent, '%d/%m/%Y'),
                      `gra_p_ano` = $gra_p_ano,
                      `gra_p_mes` = $gra_p_mes,
                      `gra_p_dia` = $gra_p_dia,
                      `gra_regime` = $gra_regime,
                      `gra_sit_atual` = $gra_sit_atual,
                      `gra_obs` = $gra_obs,
                      `user_up` = $user,
                      `data_up` = NOW(),
                      `ip_up` = $ip
                    WHERE
                      `idprocesso` = $idprocesso
                    LIMIT 1";

    // executando a query
    $query_grade = $model->query( $query_grade );

    $success = TRUE;
    if( $query_grade ) {

        $mensagem = "[ ATUALIZAÇÃO DE PROCESSO ]\n Atualização de processo. \n\n $detento \n\n[ PROCESSO ]\n $proc_s \n";

    } else {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de atualização de processo.\n\n $detento.\n\n[ PROCESSO ]\n $proc_s \n\n $valor_user \n";

    }

    // fechando a conexao
    $model->closeConnection();

    salvaLog( $mensagem );

    $msg = '';
    if ( !$success ) $msg = 'FALHA!!!';

    echo msg_js( "$msg", 2 );

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

    if ( empty( $n_pront ) or $n_pront < 4 ) {
        $tipo = 0;
        include '../init/msgnopag.php';
        $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso à página de manipulação de observação de grade SEM PERMISSÕES ( EXCLUSÃO DE PROCESSO ). \n\n Página: $pag";
        salvaLog($mensagem);
        exit;
    }

    $idprocesso     = empty( $idprocesso ) ? '' : (int)$idprocesso;

    if ( empty( $idprocesso ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador do processo em branco. Operação cancelada ( EXCLUSÃO DE PROCESSO ). \n\n Página: $pag";
        salvaLog( $mensagem );
        echo msg_js( 'FALHA!', 1 );
        exit;
    }

    // pegar os dados do preso
    $det_where = "( SELECT `cod_detento` FROM `grade` WHERE `idprocesso` = $idprocesso LIMIT 1 )";
    $detento = dados_det( $det_where );

    // pegar os dados do processo
    $q_s_proc = "SELECT
                   `gra_num_inq`,
                   `gra_num_proc`
                 FROM
                   `grade`
                 WHERE
                   `idprocesso` = $idprocesso
                 LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_s_proc = $model->query( $q_s_proc );

    $d_s_proc = $q_s_proc->fetch_assoc();
    $inq_s    = $d_s_proc['gra_num_inq'];
    $nproc_s  = $d_s_proc['gra_num_proc'];
    $proc_s   = "<b>ID:</b> $idprocesso;";
    if ( !empty( $inq_s ) ) $proc_s .= " <b>Número do inquérito:</b> $inq_s;";
    if ( !empty( $nproc_s ) ) $proc_s .= " <b>Número do processo:</b> $nproc_s";

    $query_up_grade = "UPDATE `grade` SET user_up = $user, data_up = NOW(), `ip_up` = $ip WHERE `idprocesso` = $idprocesso LIMIT 1";

    // executando a query
    $query_up_grade = $model->query( $query_up_grade );

    if ( !$query_up_grade ){

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Falha na consulta de atualização. Operação cancelada ( EXCLUSÃO DE PROCESSO ).\n\n $detento.\n\n[ PROCESSO ]\n $proc_s \n";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!', 1 );
        exit;

    }

    $query_grade = "DELETE FROM `grade` WHERE `idprocesso` = $idprocesso LIMIT 1";

    // executando a query
    $query_grade = $model->query( $query_grade );

    $success = TRUE;
    if( $query_grade ) {

        $mensagem = "[ EXCLUSÃO DE PROCESSO ]\n Exclusão de processo. \n\n $detento \n\n[ PROCESSO ]\n $proc_s \n";

    } else {

        $success = FALSE;

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de exclusão de processo.\n \n $detento.\n\n[ PROCESSO ]\n $proc_s \n";

    }

    // fechando a conexao
    $model->closeConnection();

    salvaLog( $mensagem );

    $msg = '';
    if ( !$success ) $msg = 'FALHA!!!';

    echo msg_js( "$msg", 1 );

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

    $iddet = empty( $iddet ) ? '' : (int)$iddet;

    if ( empty( $iddet ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. Operação cancelada ( CADASTRAMENTO DE PROCESSO ). \n\n Página: $pag";
        salvaLog( $mensagem );
        echo msg_js( 'FALHA!', 1 );
        exit;
    }

    // pegar os dados do preso
    $detento = dados_det( $iddet );

    // instanciando o model
    $model = SicopModel::getInstance();

    $gra_num_exec    = empty( $gra_num_exec ) ? 'NULL' : (int)$gra_num_exec;
    $gra_num_in      = empty( $gra_num_in ) ? 'NULL' : (int)$gra_num_in;
    $gra_num_inq     = empty( $gra_num_inq ) ? 'NULL' : "'".$model->escape_string($gra_num_inq)."'";
    $gra_f_p         = empty( $gra_f_p ) ? 'NULL' : "'".tratastring($gra_f_p)."'";
    $gra_preso       = empty( $gra_preso ) ? '0' : '1';
    $gra_num_proc    = empty( $gra_num_proc ) ? 'NULL' : "'".$model->escape_string($gra_num_proc)."'";
    $gra_data_delito = empty( $gra_data_delito ) ? 'NULL' : "'".$model->escape_string($gra_data_delito)."'";
    $gra_data_sent   = empty( $gra_data_sent ) ? 'NULL' : "'".$model->escape_string($gra_data_sent)."'";
    $gra_vara        = empty( $gra_vara ) ? 'NULL' : "'".tratastring($gra_vara, 'U', false)."'";
    $gra_comarca     = empty( $gra_comarca ) ? 'NULL' : "'".tratastring($gra_comarca, 'U', false)."'";
    $gra_p_ano       = empty( $gra_p_ano ) ? 'NULL' : (int)$gra_p_ano;
    $gra_p_mes       = empty( $gra_p_mes ) ? 'NULL' : (int)$gra_p_mes;
    $gra_p_dia       = empty( $gra_p_dia ) ? 'NULL' : (int)$gra_p_dia;
    $gra_med_seg     = empty( $gra_med_seg ) ? '0' : '1';
    $gra_hediondo    = empty( $gra_hediondo ) ? '0' : '1';
    $gra_campo_x     = empty( $gra_campo_x ) ? '0' : '1';
    $gra_consumado   = empty( $gra_consumado ) ? '0' : '1';
    $gra_fed         = empty( $gra_fed ) ? '0' : '1';
    $gra_outro_est   = empty( $gra_outro_est ) ? '0' : '1';
    $gra_artigos     = empty( $gra_artigos ) ? 'NULL' : "'".tratastring($gra_artigos, 'N', false)."'";
    $gra_regime      = empty( $gra_regime ) ? 'NULL' : "'".tratastring($gra_regime, 'U', false)."'";
    $gra_sit_atual   = empty( $gra_sit_atual ) ? 'NULL' : "'".tratastring($gra_sit_atual, 'U', false)."'";
    $gra_obs         = empty( $gra_obs ) ? 'NULL' : "'".tratastring($gra_obs, 'U', false)."'";


    $query_grade = "INSERT INTO
                      `grade`
                      (
                        `cod_detento`,
                        `gra_preso`,
                        `gra_num_in`,
                        `gra_num_exec`,
                        `gra_num_inq`,
                        `gra_f_p`,
                        `gra_num_proc`,
                        `gra_campo_x`,
                        `gra_med_seg`,
                        `gra_hediondo`,
                        `gra_fed`,
                        `gra_outro_est`,
                        `gra_consumado`,
                        `gra_vara`,
                        `gra_comarca`,
                        `gra_artigos`,
                        `gra_data_delito`,
                        `gra_data_sent`,
                        `gra_p_ano`,
                        `gra_p_mes`,
                        `gra_p_dia`,
                        `gra_regime`,
                        `gra_sit_atual`,
                        `gra_obs`,
                        `user_add`,
                        `data_add`,
                        `ip_add`
                      )
                    VALUES
                      (
                        $iddet,
                        $gra_preso,
                        $gra_num_in,
                        $gra_num_exec,
                        $gra_num_inq,
                        $gra_f_p,
                        $gra_num_proc,
                        $gra_campo_x,
                        $gra_med_seg,
                        $gra_hediondo,
                        $gra_fed,
                        $gra_outro_est,
                        $gra_consumado,
                        $gra_vara,
                        $gra_comarca,
                        $gra_artigos,
                        STR_TO_DATE($gra_data_delito, '%d/%m/%Y'),
                        STR_TO_DATE($gra_data_sent, '%d/%m/%Y'),
                        $gra_p_ano,
                        $gra_p_mes,
                        $gra_p_dia,
                        $gra_regime,
                        $gra_sit_atual,
                        $gra_obs,
                        $user,
                        NOW(),
                        $ip
                      )";

    // executando a query
    $query_grade = $model->query( $query_grade );

    $success = TRUE;
    if( $query_grade ) {

        $lastid = $model->lastInsertId();

        // pegar os dados do processo
        $q_s_proc = "SELECT
                       `gra_num_inq`,
                       `gra_num_proc`
                     FROM
                       `grade`
                     WHERE
                       `idprocesso` = $lastid
                     LIMIT 1";

        // executando a query
        $q_s_proc = $model->query( $q_s_proc );

        $d_s_proc = $q_s_proc->fetch_assoc();
        $inq_s    = $d_s_proc['gra_num_inq'];
        $nproc_s  = $d_s_proc['gra_num_proc'];
        $proc_s   = "<b>ID:</b> $lastid;";
        if ( !empty( $inq_s ) ) $proc_s .= " <b>Número do inquérito:</b> $inq_s;";
        if ( !empty( $nproc_s ) ) $proc_s .= " <b>Número do processo:</b> $nproc_s";

        $mensagem = "[ CADASTRAMENTO DE PROCESSO ]\n Cadastramento de processo. \n\n $detento \n\n[ PROCESSO ]\n $proc_s \n";

    } else {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de cadastramento de processo.\n\n $detento.\n\n $valor_user \n";

    }

    // fechando a conexao
    $model->closeConnection();

    salvaLog( $mensagem );

    $num_ret = 2;
    if ( isset( $cadadd ) ){
        $num_ret = 1;
    }

    $msg = '';
    if ( !$success ) $msg = 'FALHA!!!';

    echo msg_js( "$msg", $num_ret );

    exit;


/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELO CADASTRAMENTO
 * -------------------------------------------------------------------
 */

} else if ( empty( $proced ) ) { //SE NÃO HOUVER NUMERO DE PROCEDIMENTO OU ELE NÃO FOR UM INTEIRO
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Número de procedimento em branco ou inválido ( PROCESSOS ).";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!', 2 );
    exit;
}

?>
</body>
</html>