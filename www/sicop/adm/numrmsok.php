<?php

if ( !isset( $_SESSION ) ) session_start();

/*ob_start("ob_gzhandler");*/

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

if (empty($_SESSION['num_rms'])) {
    header("Location: numrms.php");
    exit;
}

$id_l = $_SESSION['num_rms'];

$q_sel       = "SELECT `numero_rms`, `ano` FROM `numerorms` WHERE `idnumrms` IN($id_l)";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_sel     = $model->query( $q_sel );
$query_sel_log = $model->query( $q_sel );

// fechando a conexao
$model->closeConnection();

if( !$query_sel or !$query_sel_log ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$valor_user = '';
while ($d_numrms = $query_sel_log->fetch_assoc()) {
    $valor_user .= $d_numrms['numero_rms'] . '/' . $d_numrms['ano'] . "\n";
}

$mensagem = "[ SOLICITAÇÃO DE NÚMEROS DE REMESSA] \n\n Números solicitados:\n $valor_user \n Página: $pag";
salvaLog($mensagem);

require 'cab.php';
$trail = new Breadcrumb();
$trail->add('Números solicitados', $_SERVER['PHP_SELF'], 4);
$trail->output();
?>

            <p class="descript_page">NÚMERO(S) PARA REMESSA(S)</p>

            <?php while ( $d_numrms = $query_sel->fetch_assoc(  ) ) { ?>

            <p align="center" style="margin-top: 5px"><?php echo $d_numrms['numero_rms'] . '/' . $d_numrms['ano'] . '<br>'?></p>

            <?php }?>

            <p class="link_common" style="margin-top: 5px;"><a href="numrms.php">Solicitar mais números</a></p>

<?php include 'footer.php'; ?>
