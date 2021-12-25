<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';
$mensagem = '';
$query_suspv = '';

$n_rol = get_session( 'n_rol', 'int' );

if ( $n_rol < 3 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = 'SUSPENSÃO DE VISITANTES';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$is_post = is_post();

if ( !$is_post ) {

    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso direto à página de manipulação de dados de suspenção de visitantes.\n\n Página: $pag";
    salvaLog($mensagem);
    header('Location: ../home.php');
    exit;

}

extract($_POST, EXTR_OVERWRITE);

$proced      = (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO

if ( empty( $proced ) or $proced > 3 ) {
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Número de procedimento em branco ou inválido. Operação cancelada ( SUSPENSÃO DE VISITANTES ).\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!', 2 );
    exit;
}

$idvisit     = empty($idvisit) ? '' : (int)$idvisit;

if ( empty( $idvisit ) ) {
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Identificador do visitante em branco ou inválido. Operação cancelada ( SUSPENSÃO DE VISITANTES ).\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!', 2 );
    exit;
}

// instanciando o model
$model = SicopModel::getInstance();

$idsusp      = empty($idsusp) ? '' : (int)$idsusp;
//$tipo_susp     = empty($tipo_susp) ? 'NULL' : "'".tratastring($tipo_susp, 'U', false)."'";
$data_inicio = empty($data_inicio) ? 'NULL' : "'" . $model->escape_string($data_inicio) . "'";
$periodo     = empty($periodo) ? 'NULL' : (int)$periodo;
$revog       = empty($revog) ? 0 : (int)$revog;
$motivo      = empty($motivo) ? 'NULL' : "'" . tratastring($motivo, 'U', false) . "'";

$user        = get_session( 'user_id', 'int' );
$ip          = "'" . $_SERVER['REMOTE_ADDR'] . "'";

$visita = '';
$detento = '';

if ( !empty( $idvisit ) ){

    // pegar os dados do visitante
    $visita = dados_visit( $idvisit );

    // pegar os dados do preso
    $det_where = "( SELECT `cod_detento` FROM `visitas` WHERE `idvisita` = $idvisit LIMIT 1 )";
    $detento = dados_det( $det_where );

}

/*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
$valor_user = valor_user( $_POST );

if ( $proced == 1 ){ // ATUALIZAÇÃO
/*
-----------------------------------------------------------
PARTE DO CODIGO RESPONSAVEL PELA ATUALIZAÇÃO
-----------------------------------------------------------
*/

    if (empty($idsusp)){
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador da suspenção em branco. Operação cancelada ( ATUALIZAÇÃO DE SUSPENÇÃO DE VISITANTE ).\n\n Página: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!', 1 );
        exit;
    }

    $query_suspv = "UPDATE
                      `visita_susp`
                    SET
                      `data_inicio` = STR_TO_DATE( $data_inicio, '%d/%m/%Y' ),
                      `periodo` = $periodo,
                      `revog` = $revog,
                      `motivo` = $motivo,
                      `user_up` = $user,
                      `data_up` = NOW(),
                      `ip_up` = $ip
                    WHERE
                      `id_visit_susp` = $idsusp
                    LIMIT 1";

/*
-------------------------------------------------------------------
FIM DA PARTE DO CODIGO RESPONSAVEL PELA ATUALIZAÇÃO
-------------------------------------------------------------------
*/
} else if ( $proced == 2 ){ //EXCLUSÃO
/*
-------------------------------------------------------------------
PARTE DO CODIGO RESPONSAVEL PELA EXCLUSÃO
-------------------------------------------------------------------
*/

    if ( $n_rol < 4 ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']     = 'perm';
        $msg['entre_ch'] = 'EXCLUSÃO DE SUSPENÇÃO DE VISITANTE';
        get_msg( $msg, 1 );

        require 'cab_simp.php';
        echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

        exit;

    }

    if ( empty( $idsusp ) ){
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador da suspenção em branco. Operação cancelada ( EXCLUSÃO DE SUSPENÇÃO DE VISITANTE ).\n\n Página: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!', 1 );
        exit;
    }

    $q_susp = "SELECT `id_visit_susp`, `cod_visita`, DATE_FORMAT(`data_inicio`, '%d/%m/%Y') AS data_inicio, `periodo`, `motivo` FROM `visita_susp` WHERE `id_visit_susp` = $idsusp LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_susp = $model->query( $q_susp );

    // fechando a conexao
    $model->closeConnection();

    $d_susp = $q_susp->fetch_assoc();

    $valor_ex = '';
    foreach ( $d_susp as $indice => $valor ) {
        if ( $valor == NULL ) continue;
        $valor_ex .= "$indice = $valor \n";
    }
/*        $idsusp_e      = $d_susp['id_visit_susp'];
    $tipo_susp_e   = $d_susp['tipo_susp'];
    $data_inicio_e = $d_susp['data_inicio'];
    $periodo       = $d_susp['periodo'];
    $motivo_e      = $d_susp['motivo'];*/

    $query_up_susp = "UPDATE `visita_susp` SET user_up = $user, data_up = NOW(), `ip_up` = $ip WHERE `id_visit_susp` = $idsusp LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_up_susp = $model->query( $query_up_susp );

    // fechando a conexao
    $model->closeConnection();

    if ( !$query_up_susp ){
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Falha na consulta de atualização. Operação cancelada ( EXCLUSÃO DE SUSPENÇÃO DE VISITANTE ).\n\n Página: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!', 1 );
        exit;
    }

    $query_suspv = "DELETE FROM `visita_susp` WHERE `id_visit_susp` = $idsusp LIMIT 1";

/*
-------------------------------------------------------------------
FIM DA PARTE DO CODIGO RESPONSAVEL PELA EXCLUSÃO
-------------------------------------------------------------------
*/
} else if ( $proced == 3 ){ //CADASTRAMENTO
/*
-------------------------------------------------------------------
PARTE DO CODIGO RESPONSAVEL PELO CADASTRAMENTO
-------------------------------------------------------------------
*/

    $query_suspv = "INSERT INTO `visita_susp` (
                              `cod_visita`,
                              `data_inicio`,
                              `periodo`,
                              `revog`,
                              `motivo`,
                              `user_add`,
                              `data_add`,
                              `ip_add`)
                            VALUES
                              ($idvisit,
                               STR_TO_DATE( $data_inicio, '%d/%m/%Y' ),
                               $periodo,
                               $revog,
                               $motivo,
                               $user,
                               NOW(),
                               $ip)";


/*
-------------------------------------------------------------------
FIM DA PARTE DO CODIGO RESPONSAVEL PELO CADASTRAMENTO
-------------------------------------------------------------------
*/
} else if (empty($proced)) { //SE NÃO HOUVER NUMERO DE PROCEDIMENTO OU ELE NÃO FOR UM INTEIRO
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Número de procedimento em branco ou inválido ( SUSPENSÃO DE VISITAS ).\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!', 1 );
    exit;
}

//------------------------------------------------------------------------------------------------------------------------------

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_suspv = $model->query( $query_suspv );

$success = TRUE;
if( $query_suspv ) {

    $lastid = $model->lastInsertId();

    if ( $proced == 1 ){

        $mensagem = "[ SUSPENSÃO DE VISITANTE ]\n Atualização de suspenção de visitante. \n\n $valor_user \n\n $visita. \n\n $detento.";

    } else if ( $proced == 2 ){

        $mensagem = "[ SUSPENSÃO DE VISITANTE ]\n Exclusão de suspenção de visitante.\n\n [ DADOS DA SUSPENSÃO EXCLUÍDA ]\n $valor_ex \n\n $visita. \n\n $detento.";

    } else if ( $proced == 3 ){

        $mensagem = "[ SUSPENSÃO DE VISITANTE ]\n Suspenção de visitante: ID da suspensão: $lastid. \n\n $valor_user \n\n $visita \n\n  $detento.";

    }

} else {

    $success = FALSE;

    if ( $proced == 1 ){

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de atualização de dados da suspenção. ID $idsusp.\n\n $valor_user \n\n $visita \n\n $detento";

    } else if ( $proced == 2 ){

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de exclusão da suspenção. ID $idsusp.\n\n $visita \n\n  $detento";

    } else if ( $proced == 3 ){

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de cadastramento de suspenção de visitante.\n\n $valor_user \n\n $visita \n\n $detento";

    }

}

// fechando a conexao
$model->closeConnection();

salvaLog( $mensagem );

if ( !$success ) echo msg_js( 'FALHA!!!' );

$saida = msg_js( '', 2 );
if ( $proced == 2 ) $saida = msg_js( '', 1 );

echo $saida;

exit;

//------------------------------------------------------------------------------------------------------------------------------*/

?>
</body>
</html>


