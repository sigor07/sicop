<?php phpinfo( ); ?>
<?php

echo '<br />';
echo '<br />';
echo '<br />';

setlocale(LC_ALL, 'pt_BR',"ptb",'pt-BR','pt-br','PT-BR', 'pt_BR.ISO-8859-1', 'pt_BR.utf-8', 'portuguese-brazil', 'bra');
date_default_timezone_set('America/Sao_Paulo');

$pag = '';
$mensagem = '';
echo 'PHP_SELF: ' .  $_SERVER['PHP_SELF'] . '<br />';
echo 'GATEWAY_INTERFACE: ' .  $_SERVER['GATEWAY_INTERFACE'] . '<br />';
echo 'SERVER_ADDR: ' .  $_SERVER['SERVER_ADDR'] . '<br />';
echo 'SERVER_NAME: ' .  $_SERVER['SERVER_NAME'] . '<br />';
echo 'SERVER_SOFTWARE: ' .  $_SERVER['SERVER_SOFTWARE'] . '<br />';
echo 'SERVER_PROTOCOL: ' .  $_SERVER['SERVER_PROTOCOL'] . '<br />';
echo 'REQUEST_METHOD: ' .  $_SERVER['REQUEST_METHOD'] . '<br />';
echo 'REQUEST_TIME: ' .  $_SERVER['REQUEST_TIME'] . '<br />';
echo 'DOCUMENT_ROOT: ' .  $_SERVER['DOCUMENT_ROOT'] . '<br />';
echo 'HTTP_ACCEPT: ' .  $_SERVER['HTTP_ACCEPT'] . '<br />';
echo 'HTTP_HOST: ' .  $_SERVER['HTTP_HOST'] . '<br />';
echo 'HTTP_REFERER: ' .  $_SERVER['HTTP_REFERER'] . '<br />';
echo 'HTTP_USER_AGENT: ' .  $_SERVER['HTTP_USER_AGENT'] . '<br />';
echo 'REMOTE_ADDR: ' .  $_SERVER['REMOTE_ADDR'] . '<br />';
echo 'REMOTE_HOST: ' .  $_SERVER['REMOTE_HOST'] . '<br />';
echo 'REMOTE_PORT: ' .  $_SERVER['REMOTE_PORT'] . '<br />';
echo 'SCRIPT_FILENAME: ' .  $_SERVER['SCRIPT_FILENAME'] . '<br />';
echo 'SCRIPT_NAME: ' .  $_SERVER['SCRIPT_NAME'] . '<br />';
echo 'REQUEST_URI: ' .  $_SERVER['REQUEST_URI'] . '<br />';

exit;


?>