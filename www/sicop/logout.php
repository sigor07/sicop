<?php
if ( !isset( $_SESSION ) ) session_start();

require 'init/config.php';
require 'funcoes_init.php';
require 'contadorVisitas.php';
require 'funcoes.php';
require 'cab_simp.php';

aut_session();

$iduser = get_session( 'user_id', 'int' );
if ( empty( $iduser ) ) {

    session_destroy();

    $mensagem = 'Tentativa de acesso à página de logout sem estar logado.';
    salvaLog( $mensagem );

    redir();

    exit;

} else {

    $query = "DELETE FROM `visitas_online` WHERE `cod_user` = $iduser";
    $db = SicopModel::getInstance();
    $db->query( $query );
    $db->closeConnection();

    $user_id = get_session( 'user_id', 'int' );
    $d_user = dados_user( $user_id );

    $mensagem = "[ LOGOUT ] \n Logout efetuado. \n\n $d_user";
    salvaLog( $mensagem );

    session_destroy();

    redir();

    exit;

}
?>
</body>
</html>