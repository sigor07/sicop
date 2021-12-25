<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_basic.php';

$pag  = link_pag();
$tipo = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    extract($_POST, EXTR_OVERWRITE);

    $iduser = (int)$iduser;

    if ( empty( $iduser ) ) {
        $mensagem = "ERRO -> Identificador do usuário em branco. Operação cancelada.\n\n Página: $pag";
        salvaLog( $mensagem );
        echo msg_js( 'FALHA!!!', 1 );
        exit;
    }

    // instanciando o model
    $model = SicopModel::getInstance();

    $nova_senha = "'" . $model->escape_string( $nova_senha ) . "'";
    $conf_senha = "'" . $model->escape_string( $conf_senha ) . "'";
    $senha_atual = "'" . $model->escape_string( $senha_atual ) . "'";

    $query_v_u = "SELECT iduser, senha FROM sicop_users WHERE iduser = $iduser AND senha = sha1($senha_atual) LIMIT 1"; //query para validar usuário

    $query_u_u = "UPDATE sicop_users SET senha = sha1($nova_senha) WHERE iduser = $iduser AND senha = sha1($senha_atual) LIMIT 1"; //query para atualizar usuário

    // executando a query
    $query_v_u = $model->query( $query_v_u );

    // fechando a conexao
    $model->closeConnection();

    $cont = 0;

    if( $query_v_u ) $cont = $query_v_u->num_rows;

    if ( $cont != 1 ) {

        // Mensagem de erro quando os dados são inválidos e/ou o usuário não foi encontrado
        echo msg_js( 'A senha atual não confere!', 1 );
        $mensagem = "ID usuário: ".$iduser." - Senha Digitada: $senha_atual: Tentativa de alteração de senha inválido.";
        salvaLog($mensagem);

    } else {

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $query_u_u = $model->query( $query_u_u );

        // fechando a conexao
        $model->closeConnection();

        if( !$query_u_u ) {

            $mensagem = 'ERRO -> Erro de atualização de usuário.';
            salvaLog($mensagem);
            echo msg_js( 'FALHA!!!', 1 );
            exit;

        }

        $mensagem = "Usuário alterou a senha: $iduser.";
        salvaLog($mensagem);
        echo msg_js( '', 1 );
        exit;

    }

} else {

    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso direto à página de atualização de senha de usuários.\n\n Página: $pag";
    salvaLog($mensagem);
    header('Location: ../home.php');
    exit;

}
?>