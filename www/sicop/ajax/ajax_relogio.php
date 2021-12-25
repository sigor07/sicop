<?php

//require '../init/config.php';

require '../_controller/sicopcontroller.php';

header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

$dia_data = SicopController::diaSemanaF() . ', ' . SicopController::dataF();
$hora_data = date( 'H:i' );

$data = $dia_data . ' - ' . $hora_data;

echo $data;

?>