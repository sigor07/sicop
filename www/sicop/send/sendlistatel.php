<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';
$mensagem = '';

$n_admsist = get_session( 'n_admsist', 'int' );

if ( empty( $n_admsist ) or $n_admsist < 3 ) {
    $tipo = 0;
    include '../init/msgnopag.php';
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso à página de manipulação da lista telefonica SEM PERMISSÕES. \n\n Página: $pag";
    salvaLog($mensagem);
    exit;
}

$is_post = is_post();

if ( !$is_post ) {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso direto à página de manipulação da lista telefonica.<br /><br /> Página: $pag";
    salvaLog($mensagem);
    echo msg_js( '', 1 );
    exit;
}

extract( $_POST, EXTR_OVERWRITE );

$proced = empty($proced) ? '' : (int) $proced;

if ( empty( $proced ) or $proced > 6 ) {
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Número de procedimento em branco ou inválido. Operação cancelada ( MANIPULAÇÃO DA LISTA TELEFÔNICA ).\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}


$user       = get_session( 'user_id', 'int' );
$ip         = "'" . $_SERVER['REMOTE_ADDR'] . "'";

if ( $proced == 1 ) { // ATUALIZAÇÃO DE NÚMERO
/*
 * -----------------------------------------------------------
 * PARTE RESPONSAVEL PELA ATUALIZAÇÃO DE NÚMERO
 * -----------------------------------------------------------
 */

    //$telefone_visit = empty($telefone_visit) ? 'NULL' : "'".(int)preg_replace( "/[()-\s]/", "", $telefone_visit)."'";

    $idnt = empty( $idnt ) ? '' : (int)$idnt;

    if ( empty( $idnt ) ){
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Identificador do número da localidade em branco. Operação cancelada ( ATUALIZAÇÃO DE NÚMERO DE LOCALIDADE DA LISTA DE TELEFONES ).\n\n Página: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!!!', 1 );
        exit;
    }

    $ltn_num = empty( $ltn_num ) ? 'NULL' : "'".preg_replace( "/[()-\s]/", "", $ltn_num )."'";

    if ( empty( $ltn_num ) ){
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Número do telefone da localidade em branco. Operação cancelada ( ATUALIZAÇÃO DE NÚMERO DE LOCALIDADE DA LISTA DE TELEFONES ).\n\n Página: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!!!', 1 );
        exit;
    }

    $ltn_ramal = empty( $ltn_ramal ) ? 'NULL' : "'" . (int)$ltn_ramal . "'";
    $ltn_desc  = empty( $ltn_desc ) ? 'NULL' : "'" . tratastring( $ltn_desc, 'U', FALSE ) . "'";

    $q_lt = "UPDATE
               `listatel_num`
             SET
               `ltn_num` = $ltn_num,
               `ltn_ramal` = $ltn_ramal,
               `ltn_desc` = $ltn_desc,
               `user_up` = $user,
               `data_up` = NOW(),
               `ip_up` = $ip
             WHERE
               `idlistatel_num` = $idnt
             LIMIT 1";

    $q_s_num = "SELECT
                  `listatel`.`tel_local`,
                  `listatel_num`.`ltn_num`,
                  `listatel_num`.`ltn_ramal`
                FROM
                  `listatel`
                  INNER JOIN `listatel_num` ON `listatel_num`.`cod_listatel` = `listatel`.`idlistatel`
                WHERE
                  `listatel_num`.`idlistatel_num` = $idnt
                LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_s_num = $model->query( $q_s_num );

    // fechando a conexao
    $model->closeConnection();

    $d_s_num = $q_s_num->fetch_assoc();
    $local_s = $d_s_num['tel_local'];
    $num_t_s = $d_s_num['ltn_num'];
    if ( !empty( $num_t_s  ) ) {
        $formata_tel = new FormataString();
        $num_t_s = $formata_tel->getTelefone( $num_t_s );
    }
    $num_s   = "<b>ID:</b> $idnt; <b>Local:</b> $local_s; <b>Número:</b> $num_t_s";
    $ramal_s = $d_s_num['ltn_ramal'];
    if ( !empty( $ramal_s ) ) {
        $num_s .= "; <b>Ramal:</b> $ramal_s.";
    }

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_lt = $model->query( $q_lt );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if( $q_lt ) {

        $mensagem = "[ ATUALIZAÇÃO DE NÚMERO DE TELEFONE ]\n Atualização de número de telefone da lista. \n\n[ NÚMERO ]\n $num_s \n";

    } else {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de atualização de localidade de telefone da lista.\n\n $valor_user \n\n[ NÚMERO ]\n $num_s.";

    }

    salvaLog( $mensagem );

    if ( !$success ) echo msg_js( 'FALHA!!!' );

    echo msg_js( '', 2 );

    exit;

/*
 * -----------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELA ATUALIZAÇÃO DE NÚMERO
 * -----------------------------------------------------------
 */
} else if ( $proced == 2 ) { // ATUALIZAÇÃO DE LOCALIDADE
/*
 * -----------------------------------------------------------
 * PARTE RESPONSAVEL PELA ATUALIZAÇÃO DE LOCALIDADE
 * -----------------------------------------------------------
 */

    $idlt_local  = empty( $idlt_local ) ? '' : (int)$idlt_local;

    if ( empty( $idlt_local ) ){
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Identificador da localidade do telefone em branco. Operação cancelada ( ATUALIZAÇÃO DE LOCALIDADE DA LISTA DE TELEFONES ).\n\n Página: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!!!', 1 );
        exit;
    }

    $tel_local   = empty( $tel_local ) ? 'NULL' : "'" . tratastring( $tel_local, 'U', FALSE ) . "'";

    if ( empty( $tel_local ) ){
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Nome da localidade do telefone em branco. Operação cancelada ( ATUALIZAÇÃO DE LOCALIDADE DA LISTA DE TELEFONES ).\n\n Página: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!!!', 1 );
        exit;
    }

    $tel_end     = empty( $tel_end ) ? 'NULL' : "'" . tratastring( $tel_end, 'U', FALSE ) . "'";
    $tel_cep     = empty( $tel_cep ) ? 'NULL' : "'" . (int)preg_replace( "/[().-\s]/", "", $tel_cep) . "'";
    $tel_codmin  = empty( $tel_codmin ) ? 'NULL' : "'" . tratastring( $tel_codmin, 'U', FALSE ) . "'";
    $tel_diretor = empty( $tel_diretor ) ? 'NULL' : "'" . tratastring( $tel_diretor, 'U', FALSE ) . "'";

    $q_lt = "UPDATE
               `listatel`
             SET
               `tel_local` = $tel_local,
               `tel_end` = $tel_end,
               `tel_cep` = $tel_cep,
               `tel_codmin` = $tel_codmin,
               `tel_diretor` = $tel_diretor,
               `user_up` = $user,
               `data_up` = NOW(),
               `ip_up` = $ip
             WHERE
               `idlistatel` = $idlt_local
             LIMIT 1";

    $q_s_local = "SELECT
                    `listatel`.`tel_local`
                  FROM
                    `listatel`
                  WHERE
                    `listatel`.`idlistatel` = $idlt_local";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_s_local = $model->query( $q_s_local );

    // fechando a conexao
    $model->closeConnection();

    $d_s_local = $q_s_local->fetch_assoc();
    $local     = $d_s_local['tel_local'];
    $local_s   = "<b>ID:</b> $idlt_local; <b>Local:</b> $local.";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_lt = $model->query( $q_lt );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if( $q_lt ) {

        $mensagem = "[ ATUALIZAÇÃO DE LOCALIDADE DE TELEFONE ]\n Atualização de localidade de telefone da lista. \n\n[ LOCALIDADE ]\n $local_s \n";

    } else {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de atualização de localidade de telefone da lista.\n\n $valor_user \n\n[ LOCALIDADE ]\n $local_s.";

    }

    salvaLog( $mensagem );

    if ( !$success ) echo msg_js( 'FALHA!!!' );

    echo msg_js( '', 2 );

    exit;

/*
 * -----------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELA ATUALIZAÇÃO DE LOCALIDADE
 * -----------------------------------------------------------
 */
} elseif ( $proced == 3 ) { // EXCLUSÃO DE NÚMERO
/*
 * -----------------------------------------------------------
 * PARTE RESPONSAVEL PELA EXCLUSÃO DE NÚMERO
 * -----------------------------------------------------------
 */

    if ( empty( $n_admsist ) or $n_admsist < 4 ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']     = 'perm';
        $msg['entre_ch'] = 'EXCLUSÃO DE NÚMERO DA LISTA DE TELEFONES';
        get_msg( $msg, 1 );

        require 'cab_simp.php';
        echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

        exit;

    }

    $idlt_num  = empty( $idlt_num ) ? '' : (int)$idlt_num;

    if ( empty( $idlt_num ) ){
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Identificador do número do telefone em branco. Operação cancelada ( EXCLUSÃO DE NÚMERO DA LISTA DE TELEFONES ).\n\n Página: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!!!', 1 );
        exit;
    }

    $q_s_num = "SELECT
                  `listatel`.`tel_local`,
                  `listatel_num`.`ltn_num`,
                  `listatel_num`.`ltn_ramal`
                FROM
                  `listatel`
                  INNER JOIN `listatel_num` ON `listatel_num`.`cod_listatel` = `listatel`.`idlistatel`
                WHERE
                  `listatel_num`.`idlistatel_num` = $idlt_num
                LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_s_num = $model->query( $q_s_num );

    // fechando a conexao
    $model->closeConnection();

    $d_s_num = $q_s_num->fetch_assoc();
    $local_s = $d_s_num['tel_local'];
    $num_t_s = $d_s_num['ltn_num'];
    if ( !empty( $num_t_s  ) ) {
        $formata_tel = new FormataString();
        $num_t_s = $formata_tel->getTelefone( $num_t_s );
    }
    $num_s   = "<b>ID:</b> $idlt_num; <b>Local:</b> $local_s; <b>Número:</b> $num_t_s";
    $ramal_s = $d_s_num['ltn_ramal'];
    if ( !empty( $ramal_s ) ) {
        $num_s .= "; <b>Ramal:</b> $ramal_s.";
    }

    $q_lista = "DELETE FROM `listatel_num` WHERE `idlistatel_num` = $idlt_num LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_lista = $model->query( $q_lista );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if( $q_lista ) {

        $mensagem = "[ EXCLUSÃO DE NÚMERO DE TELEFONE ]\n Exclusão de número de telefone da lista. \n\n[ NÚMERO ]\n $num_s \n";

    } else {

        $success = FALSE;

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de exclusão de número de telefone da lista. \n\n[ NÚMERO ]\n $num_s";

    }

    salvaLog( $mensagem );

    if ( !$success ) echo msg_js( 'FALHA!!!' );

    echo msg_js( '', 1 );

    exit;

/*
 * -----------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELA EXCLUSÃO DE NÚMERO
 * -----------------------------------------------------------
 */
} elseif ( $proced == 4 ) { // EXCLUSÃO DE LOCALIDADE
/*
 * -----------------------------------------------------------
 * PARTE RESPONSAVEL PELO EXCLUSÃO DE LOCALIDADE
 * -----------------------------------------------------------
 */

    if ( empty( $n_admsist ) or $n_admsist < 4 ) {
        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']     = 'perm';
        $msg['entre_ch'] = 'EXCLUSÃO DE LOCALIDADE DA LISTA DE TELEFONES';
        get_msg( $msg, 1 );

        require 'cab_simp.php';
        echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

        exit;

    }

    $idlt_local  = empty( $idlt_local ) ? '' : (int)$idlt_local;

    if ( empty( $idlt_local ) ){
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Identificador da localidade do telefone em branco. Operação cancelada ( EXCLUSÃO DE LOCALIDADE DA LISTA DE TELEFONES ).\n\n Página: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!!!', 1 );
        exit;
    }

    $q_s_local = "SELECT
                    `listatel`.`tel_local`
                  FROM
                    `listatel`
                  WHERE
                    `listatel`.`idlistatel` = $idlt_local";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_s_local = $model->query( $q_s_local );

    // fechando a conexao
    $model->closeConnection();

    $d_s_local = $q_s_local->fetch_assoc();
    $local_s   = $d_s_local['tel_local'];

    $q_s_num = "SELECT
                  `listatel_num`.`ltn_num`,
                  `listatel_num`.`ltn_ramal`
                FROM
                  `listatel_num`
                WHERE
                  `listatel_num`.`cod_listatel` = $idlt_local";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_s_num = $model->query( $q_s_num );

    // fechando a conexao
    $model->closeConnection();

    $cont_q_s_num = $q_s_num->num_rows;

    $num_s = "<b>ID:</b> $idlt_local; <b>Local:</b> $local_s \n<b>Números:</b>\n";

    if ( $cont_q_s_num < 1 ) {

        $num_s .= 'Não havia números.';

    } else {

        while ( $d_s_num = $q_s_num->fetch_assoc() ) {

            $num_t_s = $d_s_num['ltn_num'];
            if ( !empty( $num_t_s  ) ) {
                $formata_tel = new FormataString();
                $num_t_s = $formata_tel->getTelefone( $num_t_s );
            }
            $num_s  .= "$num_t_s";
            $ramal_s = $d_s_num['ltn_ramal'];
            if ( !empty( $ramal_s ) ) {
                $num_s .= "; <b>Ramal:</b> $ramal_s.";
            }

            $num_s .= "\n";

        }

    }

    $q_lista = "DELETE FROM `listatel` WHERE `idlistatel` = $idlt_local LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_lista = $model->query( $q_lista );

    // fechando a conexao
    $model->closeConnection();

    $success = TRUE;
    if( $q_lista ) {

        $mensagem = "[ EXCLUSÃO DE LOCALIDADE DE TELEFONE ]\n Exclusão de localidade da lista de telefones. \n\n[ LOCALIDADE/NÚMERO(S) ]\n $num_s \n";

    } else {

        $success = FALSE;

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de exclusão de localidade da lista de telefones. \n\n[ LOCALIDADE/NÚMERO(S) ]\n $num_s.";

    }

    salvaLog( $mensagem );

    if ( !$success ) {
        echo msg_js( 'FALHA!!!', 1 );
    } else {
        header( 'Location: ../listatel/buscalistatel.php' );
    }

    exit;

/*
 * -----------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELO EXCLUSÃO DE LOCALIDADE
 * -----------------------------------------------------------
 */
} elseif ( $proced == 5 ) { // CADASTRAMENTO DE NÚMERO
/*
 * -----------------------------------------------------------
 * PARTE RESPONSAVEL PELO CADASTRAMENTO DE NÚMERO
 * -----------------------------------------------------------
 */

    $idlt  = empty( $idlt ) ? '' : (int)$idlt;

    if ( empty( $idlt ) ){
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Identificador da localidade do telefone em branco. Operação cancelada ( CADASTRAMENTO DE NÚMERO NA LISTA TELEFÔNICA ).\n\n Página: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!!!', 1 );
        exit;
    }

    $ltn_num = empty($ltn_num) ? 'NULL' : "'".preg_replace( "/[()-\s]/", "", $ltn_num)."'";

    if ( empty( $ltn_num ) ){
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Número do telefone da localidade em branco. Operação cancelada ( ATUALIZAÇÃO DE NÚMERO DE LOCALIDADE DA LISTA DE TELEFONES ).\n\n Página: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!!!', 1 );
        exit;
    }

    $ltn_ramal = empty( $ltn_ramal ) ? 'NULL' : "'" . (int)$ltn_ramal . "'";
    $ltn_desc  = empty( $ltn_desc ) ? 'NULL' : "'" . tratastring( $ltn_desc, 'U', FALSE ) . "'";

    $q_lt = "INSERT INTO
               `listatel_num`
               (
                 `cod_listatel`,
                 `ltn_num`,
                 `ltn_ramal`,
                 `ltn_desc`,
                 `user_add`,
                 `data_add`,
                 `ip_add`
               )
             VALUES
               (
                 $idlt,
                 $ltn_num,
                 $ltn_ramal,
                 $ltn_desc,
                 $user,
                 NOW(),
                 $ip
               )";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_lt = $model->query( $q_lt );

    $success = TRUE;
    if( $q_lt ) {

        $lastid = $model->lastInsertId();

        $q_s_num = "SELECT
                      `listatel`.`tel_local`,
                      `listatel_num`.`ltn_num`,
                      `listatel_num`.`ltn_ramal`
                    FROM
                      `listatel`
                      INNER JOIN `listatel_num` ON `listatel_num`.`cod_listatel` = `listatel`.`idlistatel`
                    WHERE
                      `listatel_num`.`idlistatel_num` = $lastid
                    LIMIT 1";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $q_s_num = $model->query( $q_s_num );

        $d_s_num = $q_s_num->fetch_assoc();
        $local_s = $d_s_num['tel_local'];
        $num_t_s = $d_s_num['ltn_num'];
        if ( !empty( $num_t_s  ) ) {
            $formata_tel = new FormataString();
            $num_t_s = $formata_tel->getTelefone( $num_t_s );
        }
        $num_s   = "<b>ID:</b> $lastid; <b>Local:</b> $local_s; <b>Número:</b> $num_t_s";
        $ramal_s = $d_s_num['ltn_ramal'];
        if ( !empty( $ramal_s ) ) {
            $num_s .= "; <b>Ramal:</b> $ramal_s.";
        }

        $mensagem = "[ CADASTRAMENTO DE NÚMERO DE TELEFONE ]\n Cadastramento de número de telefone na lista. \n\n[ NÚMERO ]\n $num_s \n";

    } else {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de cadastramento de número de telefone da lista.\n\n $valor_user";

    }

    // fechando a conexao
    $model->closeConnection();

    salvaLog( $mensagem );

    if ( !$success ) echo msg_js( 'FALHA!!!' );

    if ( isset( $cadadd ) ) {

        echo msg_js( '', 1 );

    } else {

        $redir = empty($redir) ? 0 : 1;
        if ( !empty( $redir ) ) {

            header("Location: ../listatel/detallistatel.php?idlt=$idlt");

        } else {

            echo msg_js( '', 2 );

        }

    }

    exit;

/*
 * -----------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELO CADASTRAMENTO DE NÚMERO
 * -----------------------------------------------------------
 */
} elseif ( $proced == 6 ) { // CADASTRAMENTO DE LOCALIDADE
/*
 * -----------------------------------------------------------
 * PARTE RESPONSAVEL PELO CADASTRAMENTO DE LOCALIDADE
 * -----------------------------------------------------------
 */

    $tel_local   = empty( $tel_local ) ? 'NULL' : "'" . tratastring( $tel_local, 'U', FALSE ) . "'";

    if ( empty( $tel_local ) ){
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Nome da localidade do telefone em branco. Operação cancelada ( ATUALIZAÇÃO DE LOCALIDADE DA LISTA DE TELEFONES ).\n\n Página: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!!!', 1 );
        exit;
    }

    $tel_end     = empty( $tel_end ) ? 'NULL' : "'" . tratastring( $tel_end, 'U', FALSE ) . "'";
    $tel_cep     = empty( $tel_cep ) ? 'NULL' : "'" . (int)preg_replace( "/[().-\s]/", "", $tel_cep) . "'";
    $tel_codmin  = empty( $tel_codmin ) ? 'NULL' : "'" . tratastring( $tel_codmin, 'U', FALSE ) . "'";
    $tel_diretor = empty( $tel_diretor ) ? 'NULL' : "'" . tratastring( $tel_diretor, 'U', FALSE ) . "'";

    $q_lt = "INSERT INTO
               `listatel`
               (
                 `tel_local`,
                 `tel_end`,
                 `tel_cep`,
                 `tel_codmin`,
                 `tel_diretor`,
                 `user_add`,
                 `data_add`,
                 `ip_add`
               )
             VALUES
               (
                 $tel_local,
                 $tel_end,
                 $tel_cep,
                 $tel_codmin,
                 $tel_diretor,
                 $user,
                 NOW(),
                 $ip
               )";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_lt = $model->query( $q_lt );

    $success = TRUE;
    if( $q_lt ) {

        $lastid = $model->lastInsertId();

        $q_s_local = "SELECT
                        `listatel`.`tel_local`
                      FROM
                        `listatel`
                      WHERE
                        `listatel`.`idlistatel` = $lastid";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $q_s_local = $model->query( $q_s_local );

        $d_s_local = $q_s_local->fetch_assoc();
        $local     = $d_s_local['tel_local'];
        $local_s   = "<b>ID:</b> $lastid; <b>Local:</b> $local.";

        $mensagem = "[ CADASTRAMENTO DE LOCALIDADE DE TELEFONE ]\n Cadastramento de localidade de telefone na lista. \n\n[ LOCALIDADE ]\n $local_s \n";

        header("Location: ../listatel/cad_num_tel.php?idlt=$lastid&redir=1");

    } else {

        $success = FALSE;

        /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
        $valor_user = valor_user( $_POST );

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de cadastramento de localidade de telefone da lista.\n\n $valor_user";

    }

    // fechando a conexao
    $model->closeConnection();

    salvaLog( $mensagem );

    if ( !$success ) echo msg_js( 'FALHA!!!', 1 );

    exit;

/*
 * -----------------------------------------------------------
 * FIM DA PARTE RESPONSAVEL PELO CADASTRAMENTO DE LOCALIDADE
 * -----------------------------------------------------------
 */
}

?>
    </body>
</html>