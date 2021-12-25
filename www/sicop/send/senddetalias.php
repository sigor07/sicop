<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';
$mensagem = '';

$n_det_alias = get_session( 'n_det_alias', 'int' );

if ( empty( $n_det_alias ) or $n_det_alias < 1 ) {
    $tipo = 0;
    include '../init/msgnopag.php';
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso à página de manipulação de aliases de " . SICOP_DET_DESC_L . " SEM PERMISSÕES. \n\n Página: $pag";
    salvaLog($mensagem);
    exit;
}

$is_post = is_post();

if ( !$is_post ) {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso direto à página de manipulação de aliases de " . SICOP_DET_DESC_L . ".<br /><br /> Página: $pag";
    salvaLog($mensagem);
    echo msg_js( '', 1 );
    exit;
}

extract( $_POST, EXTR_OVERWRITE );

$targ     = empty( $targ ) ? 0 : 1;
$noreload = empty( $noreload ) ? 0 : 1;
$proced   = empty( $proced ) ? '' : (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO

if ( empty( $proced ) or $proced > 3 ) {
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Número de procedimento em branco ou inválido. Operação cancelada ( ALIAS DE " . SICOP_DET_DESC_U . " ).\n\n Página: $pag";
    salvaLog($mensagem);
    $saida = msg_js( 'FALHA!', 2 );
    if ( !empty( $targ ) ) {
        $saida = msg_js( 'FALHA!', 'f' );
    }
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

    $id_alias = empty( $id_alias ) ? '' : (int)$id_alias;

    if ( empty( $id_alias ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador do alias em branco. Operação cancelada ( ATUALIZAÇÃO DE ALIAS DE " . SICOP_DET_DESC_U . " ). \n\n Página: $pag";
        salvaLog( $mensagem );
        echo msg_js( 'FALHA!', 2 );
        exit;
    }

    // pegar os dados do preso
    $det_where = "( SELECT `cod_detento` FROM `aliases` WHERE `idalias` = $id_alias LIMIT 1 )";
    $detento = dados_det( $det_where );

    $tipoalias = empty( $tipoalias ) ? '' : (int)$tipoalias;

    if ( empty( $tipoalias ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\nTipo do alias em branco. Operação cancelada ( ATUALIZAÇÃO DE ALIAS DE " . SICOP_DET_DESC_U . " ). \n\n $detento \n\n Página: $pag";
        salvaLog($mensagem);
        $saida = msg_js( 'FALHA!', 2 );
        if ( !empty( $targ ) ) {
            $saida = msg_js( 'FALHA!', 'f' );
        }
        echo $saida;
        exit;
    }

    $alias_det  = empty( $alias_det ) ? '' : tratastring( $alias_det, 'U', FALSE );

    if ( empty( $alias_det ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Alias em branco. Operação cancelada ( ATUALIZAÇÃO DE ALIAS DE " . SICOP_DET_DESC_U . " ). \n\n $detento \n\n Página: $pag";
        salvaLog($mensagem);
        $saida = msg_js( 'FALHA!', 2 );
        if ( !empty( $targ ) ) {
            $saida = msg_js( 'FALHA!', 'f' );
        }
        echo $saida;
        exit;
    }

    $alias_det = "'" . $alias_det . "'";

    // pegar os dados do alias
    $q_s_alias = "SELECT
                    `tipoalias`.`tipoalias`,
                    `aliases`.`alias_det`
                  FROM
                    `aliases`
                    INNER JOIN `tipoalias` ON `aliases`.`cod_tipoalias` = `tipoalias`.`idtipoalias`
                  WHERE
                    `aliases`.`idalias` = $id_alias
                  LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_s_alias = $model->query( $q_s_alias );

    // fechando a conexao
    $model->closeConnection();

    $d_s_alias = $q_s_alias->fetch_assoc();
    $t_alias_s = $d_s_alias['tipoalias'];
    $alias_s   = $d_s_alias['alias_det'];
    $alias_l_s = "<b>ID:</b> $id_alias; <b>Tipo de alias:</b> $t_alias_s; <b>Alias:</b> $alias_s.";

    $query = "UPDATE
                  `aliases`
                SET
                  `cod_tipoalias` = $tipoalias,
                  `alias_det` = $alias_det,
                  `user_up` = $user,
                  `data_up` = NOW(),
                  `ip_up` = $ip
                WHERE
                  `idalias` = $id_alias
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

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de atualização de alias de " . SICOP_DET_DESC_L . ".\n\n $detento.\n\n[ ALIAS ]\n $alias_l_s \n\n $valor_user \n";
        salvaLog( $mensagem );

        echo msg_js( 'FALHA!!!', 2 );

        exit;

    }

    $mensagem = "[ ATUALIZAÇÃO DE ALIAS DE " . SICOP_DET_DESC_U . " ]\n Atualização de alias de " . SICOP_DET_DESC_L . ". \n\n $detento \n\n[ ALIAS ]\n $alias_l_s \n";

    salvaLog( $mensagem );

    echo msg_js( '', 2 );

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

    $n_chefia = get_session( 'n_chefia', 'int' );

    if ( empty( $n_chefia ) or $n_chefia < 4 ) {
        $tipo = 0;
        include '../init/msgnopag.php';
        $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso à página de manipulação de aliases de " . SICOP_DET_DESC_L . " SEM PERMISSÕES ( EXCLUSÃO DE ALIAS DE " . SICOP_DET_DESC_U . " ). \n\n Página: $pag";
        salvaLog($mensagem);
        exit;
    }

    $id_alias = empty( $id_alias ) ? '' : (int)$id_alias;

    if ( empty( $id_alias ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador do alias em branco. Operação cancelada ( EXCLUSÃO DE ALIAS DE " . SICOP_DET_DESC_U . " ). \n\n Página: $pag";
        salvaLog( $mensagem );
        echo msg_js( 'FALHA!', 2 );
        exit;
    }

    // pegar os dados do preso
    $det_where = "( SELECT `cod_detento` FROM `aliases` WHERE `idalias` = $id_alias LIMIT 1 )";
    $detento = dados_det( $det_where );

    // pegar os dados do alias
    $q_s_alias = "SELECT
                    `tipoalias`.`tipoalias`,
                    `aliases`.`alias_det`
                  FROM
                    `aliases`
                    INNER JOIN `tipoalias` ON `aliases`.`cod_tipoalias` = `tipoalias`.`idtipoalias`
                  WHERE
                    `aliases`.`idalias` = $id_alias
                  LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_s_alias = $model->query( $q_s_alias );

    // fechando a conexao
    $model->closeConnection();

    $d_s_alias = $q_s_alias->fetch_assoc();
    $t_alias_s = $d_s_alias['tipoalias'];
    $alias_s   = $d_s_alias['alias_det'];
    $alias_l_s = "<b>ID:</b> $id_alias; <b>Tipo de alias:</b> $t_alias_s; <b>Alias:</b> $alias_s.";

    $query = "DELETE FROM `aliases` WHERE `idalias` = $id_alias LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query = $model->query( $query );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if ( !$query ) {

        $success = FALSE;

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de exclusão de alias de " . SICOP_DET_DESC_L . ". \n\n $detento \n\n[ ALIAS ]\n $alias_l_s \n";

        salvaLog( $mensagem );

        echo msg_js( 'FALHA!!!', 1 );

        exit;

    }

    $mensagem = "[ EXCLUSÃO DE ALIAS DE " . SICOP_DET_DESC_U . " ]\n Exclusão de alias de " . SICOP_DET_DESC_L . ". \n\n $detento \n\n[ ALIAS ]\n $alias_l_s \n";

    salvaLog( $mensagem );

    echo msg_js( '', 1 );

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
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. Operação cancelada ( CADASTRAMENTO DE ALIAS DE " . SICOP_DET_DESC_U . " ). \n\n Página: $pag";
        salvaLog( $mensagem );
        $saida = msg_js( 'FALHA!', 2 );
        if ( !empty( $targ ) ) {
            $saida = msg_js( 'FALHA!', 'f' );
        }
        echo $saida;
        exit;
    }

    // pegar os dados do preso
    $detento = dados_det( $iddet );

    $tipoalias = empty( $tipoalias ) ? '' : (int)$tipoalias;

    if ( empty( $tipoalias ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\nTipo do alias em branco. Operação cancelada ( CADASTRAMENTO DE ALIAS DE " . SICOP_DET_DESC_U . " ). \n\n $detento \n\n Página: $pag";
        salvaLog($mensagem);
        $saida = msg_js( 'FALHA!', 2 );
        if ( !empty( $targ ) ) {
            $saida = msg_js( 'FALHA!', 'f' );
        }
        echo $saida;
        exit;
    }

    $alias_det  = empty( $alias_det ) ? '' : tratastring( $alias_det, 'U', FALSE );

    if ( empty( $alias_det ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Alias em branco. Operação cancelada ( CADASTRAMENTO DE ALIAS DE " . SICOP_DET_DESC_U . " ). \n\n $detento \n\n Página: $pag";
        salvaLog($mensagem);
        $saida = msg_js( 'FALHA!', 2 );
        if ( !empty( $targ ) ) {
            $saida = msg_js( 'FALHA!', 'f' );
        }
        echo $saida;
        exit;
    }

    $alias_det = "'" . $alias_det . "'";

    $query = "INSERT INTO
                  `aliases`
                  (
                    `cod_detento`,
                    `cod_tipoalias`,
                    `alias_det`,
                    `user_add`,
                    `data_add`,
                    `ip_add`
                  )
                VALUES
                  (
                    $iddet,
                    $tipoalias,
                    $alias_det,
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

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de cadastramento de alias de " . SICOP_DET_DESC_L . ".\n\n $detento.\n\n $valor_user \n";
        salvaLog( $mensagem );

        $msg = 'FALHA!!!';
        $saida = '';
        if ( !empty( $targ ) ){
            $saida = msg_js( "$msg", 'f' );
        } else {
            $saida = msg_js( "$msg", 2 );
        }

        echo $saida;

        exit;

    }

    $lastid = $model->lastInsertId();

    // fechando a conexao
    $model->closeConnection();

    // pegar os dados do alias
    $q_s_alias = "SELECT
                    `tipoalias`.`tipoalias`,
                    `aliases`.`alias_det`
                  FROM
                    `aliases`
                    INNER JOIN `tipoalias` ON `aliases`.`cod_tipoalias` = `tipoalias`.`idtipoalias`
                  WHERE
                    `aliases`.`idalias` = $lastid
                  LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_s_alias = $model->query( $q_s_alias );

    // fechando a conexao
    $model->closeConnection();

    $d_s_alias = $q_s_alias->fetch_assoc();
    $t_alias_s = $d_s_alias['tipoalias'];
    $alias_s   = $d_s_alias['alias_det'];
    $alias_l_s = "<b>ID:</b> $lastid; <b>Tipo de alias:</b> $t_alias_s; <b>Alias:</b> $alias_s.";

    $mensagem = "[ CADASTRO DE ALIAS DE " . SICOP_DET_DESC_U . " ]\n Cadastro de alias de " . SICOP_DET_DESC_L . ". \n\n $detento \n\n[ ALIAS ]\n $alias_l_s \n";

    salvaLog( $mensagem );

    $saida = msg_js( '', 2 );

    if ( isset( $cadadd ) ) {
        echo msg_js( '', 1 );
        exit;
    }

    if ( !empty( $targ ) ) {

        $saida = "<script type='text/javascript'> window.opener.location.reload(); opener.location.href='../detento/detalhesdet.php?iddet=$iddet#alias'; self.window.close();</script>";

        if ( !empty( $noreload ) ) {
            $saida = msg_js( '', 'f' );

        }

    }

    echo $saida;

    exit;

/*
 * -------------------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELO CADASTRAMENTO
 * -------------------------------------------------------------------
 */
} else {
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Número de procedimento em branco ou inválido ( ALIAS DE " . SICOP_DET_DESC_U . " ).";
    salvaLog( $mensagem );
    $saida = msg_js( 'FALHA!', 2 );
    if ( !empty( $targ ) ) {
        $saida = msg_js( 'FALHA!', 'f' );
    }
    echo $saida;
    exit;
}

?>
</body>
</html>