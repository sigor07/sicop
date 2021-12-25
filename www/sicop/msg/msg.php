<?php
if ( !isset( $_SESSION ) ) session_start();

/*ob_start("ob_gzhandler");*/

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

keepHistory();

$n_msg_n = 2;
$n_msg   = get_session( 'n_msg', 'int' );

$motivo_pag = 'MENSAGENS';

if ($n_msg < $n_msg_n) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$p = empty( $_GET['p'] ) ? 'ent' : $_GET['p'];


require 'cab.php';
$trail = new Breadcrumb();
$trail->add('Mensagens - Entrada', $_SERVER['PHP_SELF'], 2);
$trail->output();
?>

<?php
if ( $p == 'env' ) {
    require 'enviadas.php';
} else {
    require 'entrada.php';
}

?>

<?php include 'footer.php';?>