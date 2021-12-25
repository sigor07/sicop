<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    extract($_POST, EXTR_OVERWRITE);

    $targ       = empty($targ) ? 0 : 1;
    $proced     = empty($proced) ? '' : (int)$proced; // NÚMERO DE PROCEDIMENTO: 1 = ATUALIZAÇÃO; 2 = EXCLUSÃO; 3 = CADASTRAMENTO

    if ( empty( $proced ) or $proced > 3 ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Número de procedimento em branco ou inválido. Operação cancelada (PROTOCOLO).\n\n Página: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!', 1 );
        exit;
    }

    $user       = get_session( 'user_id', 'int' );
    $ip         = "'" . $_SERVER['REMOTE_ADDR'] . "'";

    if ( $proced == 1 ){ // ATUALIZAÇÃO
/*
-----------------------------------------------------------
PARTE DO CODIGO RESPONSAVEL PELA ATUALIZAÇÃO
-----------------------------------------------------------
*/

        $idprot = empty( $idprot ) ? '' : (int)$idprot;

        if ( empty( $idprot ) ) {
            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Identificador do documento em branco. Operação cancelada (ATUALIZAÇÃO DE PROTOCOLO). \n\n Página: $pag";
            salvaLog($mensagem);
            echo msg_js( 'FALHA!', 1 );
            exit;
        }

        // instanciando o model
        $model = SicopModel::getInstance();

        $prot_num = empty( $prot_num ) ? '' : (int)$prot_num;
        $modo_in = empty( $modo_in ) ? '' : (int)$modo_in;
        $tipo_doc = empty( $tipo_doc ) ? '' : (int)$tipo_doc;
        $prot_hora_in  = empty($prot_hora_in) ? '' : "'" . $model->escape_string($prot_hora_in) . "'";
        $prot_assunto  = empty($prot_assunto) ? '' : "'" . tratastring($prot_assunto, 'U', false) . "'";
        $prot_origem  = empty($prot_origem) ? '' : "'" . tratastring($prot_origem, 'U', false) . "'";
        $prot_setor = empty( $prot_setor ) ? '' : (int)$prot_setor;
        $prot_canc = empty( $prot_canc ) ? 0 : 1;

        $q_prot = "UPDATE
                        `protocolo`
                      SET
                        `prot_cod_modo_in` = $modo_in,
                        `prot_cod_tipo_doc` = $tipo_doc,
                        `prot_assunto` = $prot_assunto,
                        `prot_origem` = $prot_origem,
                        `prot_cod_setor` = $prot_setor,
                        `prot_hora_in` = $prot_hora_in,
                        `prot_canc` = $prot_canc,
                        `user_up` = $user,
                        `data_up` = NOW(),
                        `ip_up` = $ip
                      WHERE
                        `idprot` = $idprot
                      LIMIT 1";

        // executando a query
        $q_prot = $model->query( $q_prot );

        if ( $q_prot ) {

            $q_s_prot = "SELECT
                           `protocolo`.`idprot`,
                           `protocolo`.`prot_num`,
                           `protocolo`.`prot_ano`,
                           `protocolo`.`prot_assunto`,
                           `protocolo`.`prot_origem`,
                           DATE_FORMAT ( `protocolo`.`prot_data_in`, '%d/%m/%Y' ) AS prot_data_in_f,
                           DATE_FORMAT ( `protocolo`.`prot_hora_in`, '%H:%i' ) AS prot_hora_in_f,
                           `tipo_prot_doc`.`tipo_doc`
                         FROM
                           `protocolo`
                           LEFT JOIN `tipo_prot_doc` ON `protocolo`.`prot_cod_tipo_doc` = `tipo_prot_doc`.`id_tipo_doc`
                         WHERE
                           `protocolo`.`idprot` = $idprot
                         LIMIT 1";

            // executando a query
            $q_s_prot = $model->query( $q_s_prot );

            $d_s_prot = $q_s_prot->fetch_assoc();
            $idprot         = $d_s_prot['idprot'];
            $prot_num       = $d_s_prot['prot_num'];
            $prot_ano       = $d_s_prot['prot_ano'];
            $prot_assunto   = $d_s_prot['prot_assunto'];
            $prot_origem    = $d_s_prot['prot_origem'];
            $prot_data_in_f = $d_s_prot['prot_data_in_f'];
            $prot_hora_in_f = $d_s_prot['prot_hora_in_f'];
            $tipo_doc       = $d_s_prot['tipo_doc'];
            $doc_prot = "<b>ID:</b> $idprot, <b>Número:</b> $prot_num/$prot_ano, <b>Tipo de documento:</b> $tipo_doc, <b>Assunto:</b> $prot_assunto; <b>Origem:</b> $prot_origem, <b>Data / hora:</b> $prot_data_in_f às $prot_hora_in_f;";

            $mensagem = "[ ATUALIZAÇÃO DE DOCUMENTO ]\n Atualização de documento no protocolo. \n\n[ DOCUMENTO ]\n $doc_prot \n";
            salvaLog($mensagem);
            echo msg_js( '', 1 );

        }else{

            /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
            $valor_user = valor_user( $_POST );

            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de atualização de documento no protocolo.\n\n $valor_user";
            salvaLog($mensagem);
            echo msg_js( 'FALHA!', 1 );

        }

        // fechando a conexao
        $model->closeConnection();

        exit;

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

        $idprot = empty( $idprot ) ? '' : (int)$idprot;

        if ( empty( $idprot ) ) {
            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Identificador do documento em branco. Operação cancelada (    EXCLUSÃO DE PROTOCOLO ). \n\n Página: $pag";
            salvaLog($mensagem);
            echo msg_js( 'FALHA!', 1 );
            exit;
        }

        $q_s_prot = "SELECT
                       `protocolo`.`idprot`,
                       `protocolo`.`prot_num`,
                       `protocolo`.`prot_ano`,
                       `protocolo`.`prot_assunto`,
                       `protocolo`.`prot_origem`,
                       DATE_FORMAT ( `protocolo`.`prot_data_in`, '%d/%m/%Y' ) AS prot_data_in_f,
                       DATE_FORMAT ( `protocolo`.`prot_hora_in`, '%H:%i' ) AS prot_hora_in_f,
                       `tipo_prot_doc`.`tipo_doc`
                     FROM
                       `protocolo`
                       LEFT JOIN `tipo_prot_doc` ON `protocolo`.`prot_cod_tipo_doc` = `tipo_prot_doc`.`id_tipo_doc`
                     WHERE
                       `protocolo`.`idprot` = $idprot
                     LIMIT 1";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $q_s_prot = $model->query( $q_s_prot );

        $d_s_prot = $q_s_prot->fetch_assoc();
        $idprot         = $d_s_prot['idprot'];
        $prot_num       = $d_s_prot['prot_num'];
        $prot_ano       = $d_s_prot['prot_ano'];
        $prot_assunto   = $d_s_prot['prot_assunto'];
        $prot_origem    = $d_s_prot['prot_origem'];
        $prot_data_in_f = $d_s_prot['prot_data_in_f'];
        $prot_hora_in_f = $d_s_prot['prot_hora_in_f'];
        $tipo_doc       = $d_s_prot['tipo_doc'];
        $doc_prot = "<b>ID:</b> $idprot, <b>Número:</b> $prot_num/$prot_ano, <b>Tipo de documento:</b> $tipo_doc, <b>Assunto:</b> $prot_assunto; <b>Origem:</b> $prot_origem, <b>Data / hora:</b> $prot_data_in_f às $prot_hora_in_f;";


        $q_prot = "DELETE FROM `protocolo` WHERE `idprot` = $idprot LIMIT 1";

        // executando a query
        $q_prot = $model->query( $q_prot );

        if( $q_prot ) {

            $mensagem = "[ EXCLUSÃO DE DOCUMENTO DO PROTOCOLO ]\n Exclusão de documento no protocolo.\n\n[ DOCUMENTO ]\n $doc_prot \n";
            salvaLog($mensagem);
            header('Location: ../adm.php');

        } else {

            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de exclusão de documento no protocolo.\n\n[ DOCUMENTO ]\n $doc_prot";
            salvaLog($mensagem);
            echo msg_js( 'FALHA!', 1 );

        }

        // fechando a conexao
        $model->closeConnection();

        exit;

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

        // instanciando o model
        $model = SicopModel::getInstance();

        $prot_num = empty( $prot_num ) ? 'NULL' : (int)$prot_num;
        $modo_in = empty( $modo_in ) ? 'NULL' : (int)$modo_in;
        $tipo_doc = empty( $tipo_doc ) ? 'NULL' : (int)$tipo_doc;
        $prot_hora_in  = empty($prot_hora_in) ? "'" . date('H:i') . "'" : "'" . $model->escape_string($prot_hora_in) . "'";
        $prot_assunto  = empty($prot_assunto) ? 'NULL' : "'" . tratastring($prot_assunto, 'U', false) . "'";
        $prot_origem  = empty($prot_origem) ? 'NULL' : "'" . tratastring($prot_origem, 'U', false) . "'";
        $prot_setor = empty( $prot_setor ) ? 'NULL' : (int)$prot_setor;
        $prot_canc = empty( $prot_canc ) ? 0 : 1;

        $q_prot = "INSERT INTO `protocolo`
                        (`prot_num`,
                        `prot_ano`,
                        `prot_cod_modo_in`,
                        `prot_cod_tipo_doc`,
                        `prot_assunto`,
                        `prot_origem`,
                        `prot_cod_setor`,
                        `prot_data_in`,
                        `prot_hora_in`,
                        `prot_canc`,
                        `user_add`,
                        `data_add`,
                        `ip_add`)
                      VALUES
                        ($prot_num,
                        YEAR(CURDATE()),
                        $modo_in,
                        $tipo_doc,
                        $prot_assunto,
                        $prot_origem,
                        $prot_setor,
                        CURDATE(),
                        $prot_hora_in,
                        $prot_canc,
                        $user,
                        NOW(),
                        $ip)";

        // executando a query
        $q_prot = $model->query( $q_prot );

        if( $q_prot ) {

            $lastid = $model->lastInsertId();

            $q_s_prot = "SELECT
                           `protocolo`.`idprot`,
                           `protocolo`.`prot_num`,
                           `protocolo`.`prot_ano`,
                           `protocolo`.`prot_assunto`,
                           `protocolo`.`prot_origem`,
                           DATE_FORMAT ( `protocolo`.`prot_data_in`, '%d/%m/%Y' ) AS prot_data_in_f,
                           DATE_FORMAT ( `protocolo`.`prot_hora_in`, '%H:%i' ) AS prot_hora_in_f,
                           `tipo_prot_doc`.`tipo_doc`
                         FROM
                           `protocolo`
                           LEFT JOIN `tipo_prot_doc` ON `protocolo`.`prot_cod_tipo_doc` = `tipo_prot_doc`.`id_tipo_doc`
                         WHERE
                           `protocolo`.`idprot` = $lastid
                         LIMIT 1";

            // executando a query
            $q_s_prot = $model->query( $q_s_prot );

            $d_s_prot = $q_s_prot->fetch_assoc();
            $idprot         = $d_s_prot['idprot'];
            $prot_num       = $d_s_prot['prot_num'];
            $prot_ano       = $d_s_prot['prot_ano'];
            $prot_assunto   = $d_s_prot['prot_assunto'];
            $prot_origem    = $d_s_prot['prot_origem'];
            $prot_data_in_f = $d_s_prot['prot_data_in_f'];
            $prot_hora_in_f = $d_s_prot['prot_hora_in_f'];
            $tipo_doc       = $d_s_prot['tipo_doc'];
            $doc_prot = "<b>ID:</b> $idprot, <b>Número:</b> $prot_num/$prot_ano, <b>Tipo de documento:</b> $tipo_doc, <b>Assunto:</b> $prot_assunto; <b>Origem:</b> $prot_origem, <b>Data / hora:</b> $prot_data_in_f às $prot_hora_in_f;";

            $mensagem = "[ CADASTRO DE DOCUMENTO ]\n Cadastro de documento no protocolo. \n\n[ DOCUMENTO ]\n $doc_prot \n";
            salvaLog($mensagem);
            echo msg_js( '', 1 );

        } else {

            /*PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO*/
            $valor_user = valor_user( $_POST );

            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Erro de cadastramento de documento no protocolo.\n\n $valor_user";
            salvaLog($mensagem);
            echo msg_js( 'FALHA!', 1 );

        }

        // fechando a conexao
        $model->closeConnection();

        exit;

/*
-------------------------------------------------------------------
FIM DA PARTE DO CODIGO RESPONSAVEL PELO CADASTRAMENTO
-------------------------------------------------------------------
*/
    } else {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Número de procedimento em branco ou inválido (PROTOCOLO).";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!', 1 );
        exit;
    }

} else {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso direto à página de cadastro de documento no protocolo.\n\n Página: $pag";
    salvaLog($mensagem);
    header('Location: ../home.php');
    exit;
}
?>
</body>
</html>