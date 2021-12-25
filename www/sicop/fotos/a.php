<?php

if ( !isset( $_SESSION ) ) session_start();

error_reporting(E_ALL);
ini_set('display_errors', TRUE);

require '../init/config.php';
//require 'incl_complete.php';

$user = new userAutController();
$user->ckSys();
$user->validateUser( 'n_rol', 4 );


$view = new SicopView();

//$view->addCssBasic();
//$view->addJSComplete();

$view->setCss( 'rafacss' );
$view->setJS( 'rafajs' );
echo $view->getHeader( 'oiii', 'C' );

//require 'cab_simp.php';

echo 'oi ááá';

echo $user->getUserLvlFromModel( 'imp_adm' );

echo '<br>';

$aaa = '0001234567';

echo (int)$aaa;

echo $view->getFooter();

?>
