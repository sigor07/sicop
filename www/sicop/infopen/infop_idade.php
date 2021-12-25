<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_pront   = get_session( 'n_pront', 'int' );
$n_pront_n = 2;

$motivo_pag = 'INFOPEN - IDADE';

if ( $n_pront < $n_pront_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$q_idade_base = 'SELECT
                   COUNT( `detentos`.`iddetento` ) AS total
                 FROM
                   `detentos`
                   LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                   LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                 WHERE
                   ( ( ISNULL( `mov_det_out`.`cod_tipo_mov` ) OR `mov_det_out`.`cod_tipo_mov` = 4 OR `mov_det_out`.`cod_tipo_mov` = 6 )
                     AND
                     (`mov_det_in`.`cod_tipo_mov` = 1 OR `mov_det_in`.`cod_tipo_mov` = 3 ) )
                   AND ';

$clausula_soma_idade = 'FLOOR( DATEDIFF( CURDATE(), `detentos`.`nasc_det` ) / 365.25 )';

$q_idade = array(
    '24'    => $q_idade_base . "$clausula_soma_idade <= 24",

    '25_29' => $q_idade_base . "$clausula_soma_idade BETWEEN 25 AND 29",

    '30_34' => $q_idade_base . "$clausula_soma_idade BETWEEN 30 AND 34 ",

    '35_45' => $q_idade_base . "$clausula_soma_idade BETWEEN 35 AND 45 ",

    '46_60' => $q_idade_base . "$clausula_soma_idade BETWEEN 46 AND 60 ",

    '60'    => $q_idade_base . "$clausula_soma_idade > 60",

    'pend'  => $q_idade_base . 'ISNULL( `detentos`.`nasc_det` ) '
);

$sit_pag = 'INFOPEN - IDADE';

// instanciando o model
$model = SicopModel::getInstance();

// executa as querys
foreach ( $q_idade as $key => $value ) {

    $q_idade["$key"] = $model->fetchOne( $value );

}

// fechando a conexao
$model->closeConnection();


// verificando se as querys foram executadas corretamente
foreach ( $q_idade as $key => $value ) {

    // caso uma das querys tenha falhado, encerra a execução do script
    if ( $value === false ) {

        // gerar a mensagem q será salva no log
        $msg = sysmsg::create_msg();
        $msg->set_msg_type( SM_TYPE_ERR );
        $msg->add_quebras( 1 );
        $msg->set_msg_pre_def( SM_QUERY_FAIL );
        $msg->add_parenteses( $motivo_pag );
        $msg->add_quebras( 2 );
        $msg->set_msg( "ID DA CONSULTA - $key" );
        $msg->get_msg();

        echo msg_js( 'FALHA!!!', 1 );
        exit;

    }

}

$d_idade_24    = $q_idade['24'];
$d_idade_25_29 = $q_idade['25_29'];
$d_idade_30_34 = $q_idade['30_34'];
$d_idade_35_45 = $q_idade['35_45'];
$d_idade_46_60 = $q_idade['46_60'];
$d_idade_60    = $q_idade['60'];

$total_idade = $d_idade_24 + $d_idade_25_29 + $d_idade_30_34 + $d_idade_35_45 + $d_idade_46_60 + $d_idade_60 ;

$d_idade_pend  = $q_idade['pend'];

$q_popdacasa = 'SELECT
                  COUNT(*) AS Totalda
                FROM
                   `detentos`
                   LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                   LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                 WHERE
                   ( ISNULL( `mov_det_out`.`cod_tipo_mov` ) OR `mov_det_out`.`cod_tipo_mov` = 4 OR `mov_det_out`.`cod_tipo_mov` = 6 )
                   AND
                   ( `mov_det_in`.`cod_tipo_mov` = 1 OR `mov_det_in`.`cod_tipo_mov` = 3 )';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$popdacasa = $model->fetchOne( $q_popdacasa );

// fechando a conexao
$model->closeConnection();

if( $popdacasa === false ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_FU . 's pela idade';

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 4 );
$trail->output();
?>

            <p class="descript_page">QUANTIDADE DE <?php echo SICOP_DET_DESC_U; ?>S DE ACORDO COM A IDADE</p>

            <table class="lista_busca">
                <tr>
                    <th width="30" align="center">N</th>
                    <th width="160" align="center">IDADE</th>
                    <th width="120">QUANTIDADE</th>
                    <th width="45">%</th>
                </tr>
                <tr  class="even">
                    <td align="center" height="15">1</td>
                    <td><?php if ( $d_idade_24 > 0 ) { ?><a href="lista_infop.php?tipo_infop=idade&idade=1" title="Clique aqui para listar <?php echo SICOP_DET_ART_L; ?>s <?php echo SICOP_DET_DESC_L; ?>s" ><?php } ?>Até 24 anos<?php if ( $d_idade_24 > 0 ) { ?></a><?php } ?></td>
                    <td align="center"><?php echo $d_idade_24; ?></td>
                    <td align="center"><?php echo porcent_ref_pop( $d_idade_24, $popdacasa ); ?></td>
                </tr>
                <tr  class="even">
                    <td align="center" height="15">2</td>
                    <td><?php if ( $d_idade_25_29 > 0 ) { ?><a href="lista_infop.php?tipo_infop=idade&idade=2" title="Clique aqui para listar <?php echo SICOP_DET_ART_L; ?>s <?php echo SICOP_DET_DESC_L; ?>s" ><?php } ?>De 25 até 29 anos<?php if ( $d_idade_25_29 > 0 ) { ?></a><?php } ?></td>
                    <td align="center"><?php echo $d_idade_25_29; ?></td>
                    <td align="center"><?php echo porcent_ref_pop( $d_idade_25_29, $popdacasa ); ?></td>
                </tr>
                <tr  class="even">
                    <td align="center" height="15">3</td>
                    <td><?php if ( $d_idade_30_34 > 0 ) { ?><a href="lista_infop.php?tipo_infop=idade&idade=3" title="Clique aqui para listar <?php echo SICOP_DET_ART_L; ?>s <?php echo SICOP_DET_DESC_L; ?>s" ><?php } ?>De 30 até 34 anos<?php if ( $d_idade_30_34 > 0 ) { ?></a><?php } ?></td>
                    <td align="center"><?php echo $d_idade_30_34; ?></td>
                    <td align="center"><?php echo porcent_ref_pop( $d_idade_30_34, $popdacasa ); ?></td>
                </tr>
                <tr class="even">
                    <td align="center" height="15" >4</td>
                    <td><?php if ( $d_idade_35_45 > 0 ) { ?><a href="lista_infop.php?tipo_infop=idade&idade=4" title="Clique aqui para listar <?php echo SICOP_DET_ART_L; ?>s <?php echo SICOP_DET_DESC_L; ?>s" ><?php } ?>De 35 até 45 anos<?php if ( $d_idade_35_45 > 0 ) { ?></a><?php } ?></td>
                    <td align="center"><?php echo $d_idade_35_45; ?></td>
                    <td align="center"><?php echo porcent_ref_pop( $d_idade_35_45, $popdacasa ); ?></td>
                </tr>
                <tr class="even">
                    <td align="center" height="15">5</td>
                    <td><?php if ( $d_idade_46_60 > 0 ) { ?><a href="lista_infop.php?tipo_infop=idade&idade=5" title="Clique aqui para listar <?php echo SICOP_DET_ART_L; ?>s <?php echo SICOP_DET_DESC_L; ?>s" ><?php } ?>De 46 até 60 anos<?php if ( $d_idade_46_60 > 0 ) { ?></a><?php } ?></td>
                    <td align="center"><?php echo $d_idade_46_60; ?></td>
                    <td align="center"><?php echo porcent_ref_pop( $d_idade_46_60, $popdacasa ); ?></td>
                </tr>
                <tr class="even">
                    <td align="center" height="15">6</td>
                    <td><?php if ( $d_idade_60 > 0 ) { ?><a href="lista_infop.php?tipo_infop=idade&idade=6" title="Clique aqui para listar <?php echo SICOP_DET_ART_L; ?>s <?php echo SICOP_DET_DESC_L; ?>s" ><?php } ?>Mais de 60 anos<?php if ( $d_idade_60 > 0 ) { ?></a><?php } ?></td>
                    <td align="center"><?php echo $d_idade_60; ?></td>
                    <td align="center"><?php echo porcent_ref_pop( $d_idade_60, $popdacasa ); ?></td>
                </tr>
                <tr class="even_dk">
                    <td align="center" height="15">&nbsp;</td>
                    <td align="center"><b>TOTAL</b></td>
                    <td align="center"><?php echo $total_idade; ?></td>
                    <td align="center"><?php echo porcent_ref_pop( $total_idade, $popdacasa ); ?></td>
                </tr>
                <tr class="even_dk">
                    <td align="center" height="15">&nbsp;</td>
                    <td><?php if ( $d_idade_pend > 0 ) { ?><a href="lista_infop.php?tipo_infop=pidade" title="Clique aqui para listar <?php echo SICOP_DET_ART_L; ?>s <?php echo SICOP_DET_DESC_L; ?>s" ><?php } ?>Pendências de idade<?php if ( $d_idade_pend > 0 ) { ?></a><?php } ?></td>
                    <td align="center"><?php echo $d_idade_pend; ?></td>
                    <td align="center"><?php echo porcent_ref_pop( $d_idade_pend, $popdacasa ); ?></td>
                </tr>
            </table>

<?php include 'footer.php'; ?>