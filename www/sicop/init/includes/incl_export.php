<?php
if ( !isset( $_SESSION ) ) session_start();

setlocale( LC_ALL, 'pt_BR', 'ptb', 'pt-BR', 'pt-br', 'PT-BR', 'pt_BR.ISO-8859-1', 'pt_BR.utf-8', 'portuguese-brazil', 'bra' );
date_default_timezone_set( 'America/Sao_Paulo' );

require 'funcoes_init.php';
require 'funcoes.php';

aut_session();
ck_sys();

?>