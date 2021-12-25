<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    extract($_POST, EXTR_OVERWRITE);

    $proced     = empty($proced) ? '' : (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO
    //$msg_para    = empty($msg_para) ? "" : (int)$msg_para;
    //$idmsg        = empty($idmsg) ? "" : (int)$idmsg;
    //$msg_titulo    = empty($msg_titulo) ? "NULL" : "'".tratastring($msg_titulo, 'N', false)."'";
    //$msg_corpo  = empty($msg_corpo) ? "" : "'".tratastring($msg_corpo, 'N', false)."'";

    $user         = get_session( 'user_id', 'int' );
    $url_dest    = '../msg/msg.php';//retira_cerquilha(returnHistory());

    $msg_f_atu = 'FALHA ao atualizar!';
    $msg_f_exc = 'FALHA ao excluir!';
    $msg_f_cad = 'FALHA ao enviar mensagem!';


/**
 * PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO
 */
/*    $valor_user = '';
    foreach ($_POST as $indice => $valor) {
        if ($valor == NULL) continue;
        $valor_user .= "$indice = $valor \n";
    }*/


    if (isset($proced) and $proced == '1'){ // ATUALIZAÇÃO
/**
 *-----------------------------------------------------------
 *PARTE DO CODIGO RESPONSAVEL PELA ATUALIZAÇÃO
 *-----------------------------------------------------------
 */

        $url_dest = '../msg/listamsg.php';

        $idmsg = empty($idmsg) ? '' : (int)$idmsg;

        if (empty($idmsg)){
                $mensagem = "ERRO -> Identificador da mensagem em branco. Operação cancelada (ALTERAÇÃO DE MENSAGEM).\n\n Página: $pag";
                salvaLog($mensagem);
                echo msg_js( 'FALHA!!!', 1 );
                exit;
        }

        $msg_corpo  = empty($msg_corpo) ? '' : "'".tratastring($msg_corpo, 'N', false)."'";

        if ( empty($msg_corpo) ){
                $mensagem = "ERRO -> Corpo da mensagem em branco. Operação cancelada (ATUALIZAÇÃO DE MENSAGEM).\n\n Página: $pag";
                salvaLog($mensagem);
                echo msg_js( 'FALHA!!!', 1 );
                exit;
        }

        $msg_titulo      = empty($msg_titulo) ? "NULL" : "'".tratastring($msg_titulo, 'N', false)."'";
        $msg_de          = empty($msg_de) ? "" : (int)$msg_de;
        $msg_para      = empty($msg_para) ? "" : (int)$msg_para;
        $msg_de_exc      = empty($msg_de_exc) ? '0' : '1';
        $msg_para_exc = empty($msg_para_exc) ? '0' : '1';
        $msg_block      = empty($msg_block) ? '0' : '1';

        $query_msg = "UPDATE `msg` SET
                               `msg_titulo` = $msg_titulo,
                               `msg_corpo` = $msg_corpo,
                               `msg_de` = $msg_de,
                               `msg_para` = $msg_para,
                               `msg_de_exc` = $msg_de_exc,
                               `msg_para_exc` = $msg_para_exc,
                               `msg_block` = $msg_block
                        WHERE `idmsg` = $idmsg LIMIT 1";

        $q_para  = "SELECT `iduser`, `nome_cham` FROM `sicop_users` WHERE `iduser` = $msg_para LIMIT 1";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $q_para = $model->query( $q_para );

        // fechando a conexao
        $model->closeConnection();

        $d_para  = $q_para->fetch_assoc();
        $id_para = $d_para['iduser'];
        $u_para  = $d_para['nome_cham'];

        $q_de  = "SELECT `iduser`, `nome_cham` FROM `sicop_users` WHERE `iduser` = $msg_de LIMIT 1";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $q_de = $model->query( $q_de );

        // fechando a conexao
        $model->closeConnection();

        $d_de  = $q_de->fetch_assoc();
        $id_de = $d_de['iduser'];
        $u_de  = $d_de['nome_cham'];

/*
-------------------------------------------------------------------
FIM DA PARTE DO CODIGO RESPONSAVEL PELA ATUALIZAÇÃO
-------------------------------------------------------------------
*/
    } else if (isset($proced) and $proced == '2'){ //EXCLUSÃO
/*
-------------------------------------------------------------------
PARTE DO CODIGO RESPONSAVEL PELA EXCLUSÃO
-------------------------------------------------------------------
*/

        $idmsg = empty($idmsg) ? "" : (int)$idmsg;
        $url_dest = '../msg/listamsg.php';

        if ( empty( $idmsg ) ){
            $mensagem = "ERRO -> Identificador da mensagem em branco. Operação cancelada (EXCLUSÃO).\n\n Página: $pag";
            salvaLog($mensagem);
            echo msg_js( 'FALHA!!!', 1 );
            exit;
        }

        $q_s_msg = "SELECT
                    msg.`idmsg`,
                    msg.`msg_titulo`,
                    ude.nome_cham AS nome_de,
                    upara.nome_cham AS nome_para
                  FROM
                    msg
                    INNER JOIN sicop_users ude ON msg.msg_de = ude.iduser
                    INNER JOIN sicop_users upara ON msg.msg_para = upara.iduser
                  WHERE idmsg = $idmsg LIMIT 1";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $q_s_msg = $model->query( $q_s_msg );

        // fechando a conexao
        $model->closeConnection();

        $d_s_msg  = $q_s_msg->fetch_assoc();

        $msg_ex = 'De: ' . $d_s_msg['nome_de'] . '; Para: ' . $d_s_msg['nome_para'] . '; Assunto: ' . $d_s_msg['msg_titulo'] . '; ID da msg: ' . $d_s_msg['idmsg'];

        $query_msg = "DELETE FROM `msg` WHERE `idmsg` = $idmsg LIMIT 1";

/*
-------------------------------------------------------------------
FIM DA PARTE DO CODIGO RESPONSAVEL PELA EXCLUSÃO
-------------------------------------------------------------------
*/
    } else if (isset($proced) and $proced == '3'){ //CADASTRAMENTO
/*
-------------------------------------------------------------------
PARTE DO CODIGO RESPONSAVEL PELO CADASTRAMENTO
-------------------------------------------------------------------
*/

        $msg_para   = empty($msg_para) ? '' : (int)$msg_para;
        $idmsg      = empty($idmsg) ? '' : (int)$idmsg;
        $msg_titulo = empty($msg_titulo) ? 'NULL' : "'".tratastring($msg_titulo, 'N', false)."'";
        $msg_corpo  = empty($msg_corpo) ? '' : "'".tratastring($msg_corpo, 'N', false)."'";


        if (empty($msg_para)){
                $mensagem = "ERRO -> Identificador do destinatario em branco. Operação cancelada (ENVIO DE MENSAGEM).\n\n Página: $pag";
                salvaLog($mensagem);
                echo msg_js( 'FALHA!!!', 1 );
                exit;
        }

        if ( empty($msg_corpo) ){
                $mensagem = "ERRO -> Corpo da mensagem em branco. Operação cancelada (ENVIO DE MENSAGEM).\n\n Página: $pag";
                salvaLog($mensagem);
                echo msg_js( 'FALHA!!!', 1 );
                exit;
        }

        $q_para  = "SELECT `iduser`, `nome_cham` FROM `sicop_users` WHERE `iduser` = $msg_para LIMIT 1";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $q_para = $model->query( $q_para );

        // fechando a conexao
        $model->closeConnection();

        $d_para  = $q_para->fetch_assoc();
        $id_para = $d_para['iduser'];
        $u_para  = $d_para['nome_cham'];

        $q_de  = "SELECT `iduser`, `nome_cham` FROM `sicop_users` WHERE `iduser` = $user LIMIT 1";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $q_de = $model->query( $q_de );

        // fechando a conexao
        $model->closeConnection();

        $d_de  = $q_de->fetch_assoc();
        $id_de = $d_de['iduser'];
        $u_de  = $d_de['nome_cham'];

        $query_msg = "INSERT INTO `msg` (
                                    `msg_titulo`,
                                    `msg_corpo`,
                                    `msg_de`,
                                    `msg_para`,
                                    `msg_add`)
                                    VALUES
                                    ($msg_titulo,
                                    $msg_corpo,
                                    $user,
                                    $msg_para,
                                    NOW())";

/*
-------------------------------------------------------------------
FIM DA PARTE DO CODIGO RESPONSAVEL PELO CADASTRAMENTO
-------------------------------------------------------------------
*/

    } else if (isset($proced) and $proced == '4'){ //ENVIO PARA A LIXEIRA

/*
-------------------------------------------------------------------
PARTE DO CODIGO RESPONSAVEL PELO ENVIO DA MSG PARA A LIXEIRA
-------------------------------------------------------------------
*/



        //$idmsg       = empty($idmsg) ? "" : (int)$idmsg;
        $tipo_msg = empty($tipo_msg) ? "" : (int)$tipo_msg; // TIPO DE MENSAGEM: 1 = RECEBIDA; 2 = ENVIADA

        if ( empty( $tipo_msg ) ) {
            $mensagem = "ERRO -> Número do tipo de mensagem em branco ou inválido (EXCLUSÃO DE MENSAGEM PELO USUÁRIO).\n\n Página: $pag";
            salvaLog($mensagem);
            echo msg_js( 'FALHA!!!', 1 );
            exit;
        }

        if ( empty( $exc ) ) {
            $mensagem = "ERRO -> O usuário não marcou nenhuma mensagem para exclusão (EXCLUSÃO DE MENSAGEM PELO USUÁRIO).\n\n Página: $pag";
            salvaLog($mensagem);
            echo msg_js( 'FALHA!!!', 1 );
            exit;
        }

        $v_exc = '';
        foreach ( $exc as $indice => $valor ) {
            if ( (int)$valor == NULL ) continue;
            $v_exc[] .= (int)$valor;
        }

        $v_exc = implode( $v_exc, ', ' );

        if ( empty( $v_exc ) ) {
            $mensagem = "ERRO -> Após validação, o array ficou vazio. (EXCLUSÃO DE MENSAGEM PELO USUÁRIO).\n\n Página: $pag";
            salvaLog($mensagem);
            echo msg_js( 'FALHA!!!', 1 );
            exit;
        }

        $query_msg = "UPDATE `msg` SET `msg_de_exc` = 1, `msg_de_exdata` = NOW() WHERE `idmsg` IN($v_exc) AND `msg_de` = $user";

        $q_s_msg = "SELECT
                      msg.idmsg,
                      msg.msg_titulo,
                      ude.nome_cham AS nome_de,
                      upara.nome_cham AS nome_para
                    FROM
                      msg
                      INNER JOIN sicop_users ude ON msg.msg_de = ude.iduser
                      INNER JOIN sicop_users upara ON msg.msg_para = upara.iduser
                    WHERE `idmsg` IN($v_exc) AND `msg_de` = $user
                    ORDER BY msg.`msg_add` DESC";

        if ( $tipo_msg == 1 ) {

            $query_msg = "UPDATE `msg` SET `msg_para_exc` = 1, `msg_para_exdata` = NOW() WHERE `idmsg` IN($v_exc) AND `msg_para` = $user";

            $q_s_msg = "SELECT
                          msg.idmsg,
                          msg.msg_titulo,
                          ude.nome_cham AS nome_de,
                          upara.nome_cham AS nome_para
                        FROM
                          msg
                          INNER JOIN sicop_users ude ON msg.msg_de = ude.iduser
                          INNER JOIN sicop_users upara ON msg.msg_para = upara.iduser
                        WHERE `idmsg` IN($v_exc) AND `msg_para` = $user
                        ORDER BY msg.`msg_add` DESC";

        }

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $q_s_msg = $model->query( $q_s_msg );

        // fechando a conexao
        $model->closeConnection();

        $msg_ex = '';
        while ( $d_msg = $q_s_msg->fetch_assoc() ){
            $msg_ex .= 'De: ' . $d_msg['nome_de'] . '; Para: ' . $d_msg['nome_para'] . '; Assunto: ' . $d_msg['msg_titulo'] . '; ID da msg: ' . $d_msg['idmsg'] . " \n ";
        }



/*
-------------------------------------------------------------------
FIM DA PARTE DO CODIGO RESPONSAVEL PELO ENVIO DA MSG PARA A LIXEIRA
-------------------------------------------------------------------
*/

    } else if (empty($proced)) { //SE NÃO HOUVER NUMERO DE PROCEDIMENTO OU ELE NÃO FOR UM INTEIRO
        $mensagem = "ERRO -> Número de procedimento em branco ou inválido (MENSAGEM).\n\n Página: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!!!', 1 );
        exit;
    }

//------------------------------------------------------------------------------------------------------------------------------

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_msg = $model->query( $query_msg );

    if( $query_msg ) {

        $lastid = $model->lastInsertId();

        if (isset($proced) and $proced == '1'){
            $mensagem = "[ ATUALIZAÇÃO DE MENSAGEM ]\n Atualização de mensagem .ID da mensagem: $idmsg. \n\n [ DE ]\n Usuário: $u_de, ID $id_de. \n\n [ PARA ]\n Usuário: $u_para, ID $id_para. \n\n [ ASSUNTO ]\n Assunto da mensagem : $msg_titulo.";
        } else if (isset($proced) and $proced == '2'){
            $mensagem = "[ EXCLUSÃO DE MENSAGEM ]\n Exclusão de mensagem.\n\n [ DADOS DA MENSAGEM EXCLUÍDA ]\n $msg_ex.";
        } else if (isset($proced) and $proced == '3'){
            $mensagem = "[ MENSAGEM ENVIADA ]\n Nova mensagem enviada: ID da mensagem: $lastid. \n\n [ DE ]\n Usuário: $u_de, ID $id_de. \n\n [ PARA ]\n Usuário: $u_para, ID $id_para. \n\n [ TÍTULO DA MENSAGEM ]\n $msg_titulo";
        } else if (isset($proced) and $proced == '4'){
            $mensagem = "[ MENSAGENS MARCADAS COMO EXCLUIDA ]\n Marcação de exclusão de mensagens em lotes: ID da(s) mensagem(s): $v_exc. \n\n [ DADOS DAS MENSAGENS EXCLUIDAS ]\n $msg_ex \n\n";
        }

        salvaLog($mensagem);

        echo msg_js( '', 2 );

        exit;

    }else{

        if (isset($proced) and $proced == '1'){
            $mensagem = "[ *** ERRO *** ]\n Erro de atualização de mensagem. ID $idmsg.\n\n [ DE ]\n Usuário: $u_de, ID $id_de. \n\n [ PARA ]\n Usuário: $u_para, ID $id_para. \n\n [ ASSUNTO ]\n Assunto da mensagem : $msg_titulo.";
            $alerta = $msg_f_atu;
        } else if (isset($proced) and $proced == '2'){
            $mensagem = "[ *** ERRO *** ]\n Erro de exclusão de mensagem. ID $idsusp.\n\n  [ DADOS DA MENSAGEM QUE SERIAM EXCLUÍDA ]\n $msg_ex. \n\n [ MENSAGEM MYSQL ]\n $erromysql.";
            $alerta = $msg_f_exc;
        } else if (isset($proced) and $proced == '3'){
            $mensagem = "[ *** ERRO *** ]\n Erro de envio de mensagem.\n\n $valor_user \n  [ DE ]\n Usuário: $u_de, ID $id_de. \n\n [ PARA ]\n Usuário: $u_para, ID $id_para.";
            $alerta = $msg_f_cad;
        } else if (isset($proced) and $proced == '4'){
            $mensagem = "[ *** ERRO *** ]\n Erro de exclusão de mensagens em lotes. ID da(s) mensagem(s): $v_exc. \n\n [ DADOS DAS MENSAGENS QUE SERIAM EXCLUIDAS ]\n $msg_ex.";
            $alerta = $msg_f_cad;
        }
        salvaLog($mensagem);

        echo msg_js( 'FALHA!!!', 1 );

        exit;
    }

    // fechando a conexao
    $model->closeConnection();

//------------------------------------------------------------------------------------------------------------------------------*/

} else {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso direto à página de manipulação de dados de suspenção de visitantes.\n\n Página: $pag";
    salvaLog($mensagem);
    header('Location: ../home.php');
    exit;
}
?>
</body>
</html>
