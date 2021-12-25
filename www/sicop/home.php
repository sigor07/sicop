<?php
if ( !isset( $_SESSION ) ) session_start();

require 'init/config.php';
require 'incl_complete.php';

$pag = link_pag( 'home' );

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

require 'cab.php';
$trail = new Breadcrumb();
$trail->add( 'Home', $_SERVER['PHP_SELF'], 0 );
$trail->output();
?>

        <div class="no_print">

            <p class="descript_page">HOME</p>
            <?php

            //depur( $_SESSION );

            ?>
<?php include 'footer.php'; ?>