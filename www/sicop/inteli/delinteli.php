<?php

if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_inteli   = get_session( 'n_inteli', 'int' );
$n_inteli_n = 3;

$motivo_pag = 'EXCLUSÃO DE ' . SICOP_DET_DESC_U . 'S - INTELIGÊNCIA';

if ( $n_inteli < $n_inteli_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$idinteli = empty( $_GET['idinteli'] ) ? '' : (int)$_GET['idinteli'];

if ( empty( $_GET ) ) {


    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( IDENTIFICADOR EM BRANCO - $motivo_pag ).";
    get_msg( $msg, 1 );


    echo msg_js( '', 1 );


    exit;

}


$q_inteli = "SELECT `cod_detento` FROM `inteligencia` WHERE `idinteli` = $idinteli";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_inteli = $model->query( $q_inteli );

// fechando a conexao
$model->closeConnection();

if( !$q_inteli) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_inteli = $q_inteli->num_rows;

if( $cont_inteli < 1 ) {
    $mensagem = "A consulta retornou 0 ocorrencias ( $motivo_pag ).\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$d_inteli = $q_inteli->fetch_assoc();
$iddet = $d_inteli['cod_detento'];

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Excluir ' . SICOP_DET_DESC_L . ' do monitoramento';


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

            <p class="descript_page">EXCLUIR <?php echo SICOP_DET_DESC_U; ?> DA LISTA DE MONITORAMENTO</p>

            <?php include 'quali/det_top.php'; ?>

            <p class="confirm_ask">Tem certeza de que deseja excluir <?php echo SICOP_DET_PRON_L?> <?php echo SICOP_DET_DESC_L; ?> da lista de monitoramento?</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendinteli.php" method="post" name="delinteli" id="delinteli">

                <input name="iddet" type="hidden" id="iddet" value="<?php echo $iddet;?>" />
                <input name="idinteli" type="hidden" id="idinteli" value="<?php echo $idinteli;?>" />
                <input name="proced" type="hidden" id="proced" value="2" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Excluir" />
                    <input class="form_bt" type="button" name="" onClick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

<?php include 'footer.php'; ?>



