<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_print.php';

$pag = link_pag();
$tipo = '';

$imp_incl = get_session( 'imp_incl', 'int' );
$n_incl_n = 1;

$titulo  = get_session( 'titulo' );
$unidade = get_session( 'unidade_long' );
$cidade  = get_session( 'cidade' );
$iduser  = get_session( 'user_id', 'int' );

$motivo_pag = 'IMPRESSÃO DE AUTORIZAÇÃO DE RÁDIO';

if ($imp_incl < $n_incl_n) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 'f' );

    exit;

}

$idradio_g = get_get( 'idradio', 'int' );
$idradio_s = get_session( 'idradio' );

$idradio = empty( $idradio_g ) ? $idradio_s : $idradio_g;

if ( empty( $idradio ) ){

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 'f' );

    exit;

}


$q_radio = "SELECT
            `detentos_radio`.`idradio`,
            `detentos_radio`.`marca_radio`,
            `detentos_radio`.`cor_radio`,
            `detentos_radio`.`faixas`,
            `detentos_radio`.`lacre_1`,
            `detentos_radio`.`lacre_2`,
            `detentos`.`iddetento`,
            `detentos`.`nome_det`,
            `detentos`.`matricula`,
            `cela`.`cela`,
            `raio`.`raio`
          FROM
            `detentos_radio`
            LEFT JOIN `detentos` ON `detentos_radio`.`cod_detento` = `detentos`.`iddetento`
            LEFT JOIN `cela` ON `detentos_radio`.`cod_cela` = `cela`.`idcela`
            LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
          WHERE
            `detentos_radio`.`idradio` IN( $idradio )
          ORDER BY
            `detentos`.`nome_det`";
                    /*,21,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47 */

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_radio = $model->query( $q_radio );

// fechando a conexao
$model->closeConnection();

$cont_radio = $q_radio->num_rows;

if( $cont_radio < 1 ) {
    $mensagem = 'A consulta retornou 0 ocorrencias ( ' . SICOP_DET_DESC_U . " ).\n\n Página $pag";
    salvaLog($mensagem);
    echo msg_js( '', 'f' );
    exit;
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="pt-br" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="shortcut icon" href="<?php echo SICOP_SYS_IMG_PATH; ?>favicon.ico" type="image/x-icon" />
        <title><?php echo $titulo ?></title>
        <link href="<?php echo SICOP_ABS_PATH ?>css/reset.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SICOP_ABS_PATH ?>css/estilo_p_aut.css" rel="stylesheet" type="text/css" />
    </head>

    <body onload="Javascript:window.print();self.window.close()">
    <!-- onload="Javascript:window.print();self.window.close()" -->
    <div class="corpo_cartao">
    <?php
        $i = 0;
        while( $d_radio = $q_radio->fetch_assoc() ) {
        ++$i;

        if ( !empty( $iddet_g ) ){
            $d_det = dados_det( $iddet_g );
            $mensagem = "[ IMPRESSÃO DE AUTORIZAÇÃO PARA RÁDIO ] \n\n $d_det";
            salvaLog($mensagem);
        }
    ?>
      <?php if ( $i == 1 ) { ?>
      <div>&nbsp;</div>
      <?php } ?>
         <div class="cartao_det">
            <p class="par_cartao_top"><?php echo $unidade; ?></p>
            <p class="par_cartao_top">AUTORIZAÇÃO PARA RÁDIO</p>
            <p class="par_cartao_rc"><b><?php echo SICOP_RAIO ?>:</b> <?php echo $d_radio['raio'] ?> - <b><?php echo SICOP_CELA ?>:</b> <?php echo $d_radio['cela'] ?> </p>
            <p class="par_cartao"><b><?php echo SICOP_DET_DESC_FU; ?>:</b> <?php echo $d_radio['nome_det'];?></p>
            <p class="par_cartao"><b>Matrícula:</b> <?php echo formata_num( $d_radio['matricula'] ) ?></p>
            <p class="par_cartao_tv"><b>Marca:</b> <?php echo $d_radio['marca_radio'] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Cor:</b> <?php echo $d_radio['cor_radio'] ?></p>
            <p class="par_cartao_tv"><b>Faixas:</b> <?php echo $d_radio['faixas'] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Lacres:</b> <?php echo $d_radio['lacre_1'] ?> / <?php echo $d_radio['lacre_2'] ?></p>

            <div class="normas">
                <p class="par_cartao">
                <?php echo SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_L; ?> qualificad<?php echo SICOP_DET_ART_L; ?> está autorizad<?php echo SICOP_DET_ART_L; ?> a utilizar o aparelho de RÁDIO descrito nesta autorização, sujeitando-se as normas definidas pela diretoria
                desta Unidade Prisional.
                </p>
            </div>
            <p class="par_data"><?php echo $cidade; ?>, <?php echo data_f(); ?></p>


      </div>
        <?php If ( $i%10 == 0 and $cont_radio != 10  ) { ?>
        <div style="page-break-before: always;">&nbsp;</div>
        <?php } ?>
        <?php }?>

        </div>
    </body>
</html>