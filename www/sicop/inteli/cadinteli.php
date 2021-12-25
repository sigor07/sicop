<?php

if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_inteli   = get_session( 'n_inteli', 'int' );
$n_inteli_n = 3;

$motivo_pag = 'CADASTRAMENTO DE ' . SICOP_DET_DESC_U . 'S - INTELIGÊNCIA';

if ( $n_inteli < $n_inteli_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$iddet = get_get( 'iddet', 'int' );
if ( empty( $iddet ) ) {


    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( IDENTIFICADOR EM BRANCO - $motivo_pag ).";
    get_msg( $msg, 1 );


    echo msg_js( '', 1 );


    exit;

}

$q_inteli = "SELECT `cod_detento` FROM `inteligencia` WHERE `cod_detento` = $iddet";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_inteli = $model->query( $q_inteli );

// fechando a conexao
$model->closeConnection();

if( !$q_inteli ) {

    echo msg_js( '', 1 );
    exit;

}

$cont_inteli = $q_inteli->num_rows;

if( $cont_inteli >= 1 ) {

    require 'cab_simp.php';
    echo msg_js( SICOP_DET_PRON_FU . ' ' . SICOP_DET_DESC_L . ' já está cadastrado na lista de monitoramento', 1 );
    exit;

}

$desc_pag = 'Incluir ' . SICOP_DET_DESC_L . ' na lista de monitoramento';

require 'cab.php';
?>

            <script type="text/javascript">
                $(function(){
                    // PARA FOTO
                    $("a#link_foto_det").fancybox({
                        'transitionIn'  : 'elastic',
                        'transitionOut' : 'elastic'
                    });
                });
            </script>

            <p class="descript_page">INCLUIR <?php echo SICOP_DET_DESC_U; ?> NA LISTA DE MONITORAMENTO</p>

            <?php include 'quali/det_top.php'; ?>

            <p class="confirm_ask">Tem certeza de que deseja incluir <?php echo SICOP_DET_PRON_L; ?> <?php echo SICOP_DET_DESC_L; ?> na lista de monitoramento?</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendinteli.php" method="post" name="delinteli" id="delinteli">

                <input name="iddet" type="hidden" id="iddet" value="<?php echo $iddet; ?>" />
                <input name="proced" type="hidden" id="proced" value="1">

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Incluir" />
                    <input class="form_bt" type="button" name="" onClick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

<?php include 'footer.php'; ?>