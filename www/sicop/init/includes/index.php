<?php
if ( !isset( $_SESSION ) ) session_start();

require '../config.php';
require 'funcoes_init.php';

// montar a mensagem q será salva no log
$msg = array();
$msg['tipo']  = 'atn';
$msg['text']  = 'Tentativa de acesso à diretório.';
get_msg( $msg, 1 );

redir( 'home' );

exit;

?>