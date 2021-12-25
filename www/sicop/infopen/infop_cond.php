<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_pront   = get_session( 'n_pront', 'int' );
$n_pront_n = 2;

$motivo_pag = 'INFOPEN - CONDENAÇÃO';

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

$q_cond_base = 'SELECT
                 `detentos`.`iddetento`
               FROM
                 `detentos`
                 LEFT JOIN `grade` ON `detentos`.`iddetento` = `grade`.`cod_detento`
                 LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                 LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
               WHERE
                 `gra_campo_x` = FALSE AND
                 `gra_preso` = true AND
                 `detentos`.`cod_sit_proc` != 1 AND
                 ( ( ISNULL( `mov_det_out`.`cod_tipo_mov` ) OR `mov_det_out`.`cod_tipo_mov` = 4 OR `mov_det_out`.`cod_tipo_mov` = 6 )
                   AND
                   (`mov_det_in`.`cod_tipo_mov` = 1 OR `mov_det_in`.`cod_tipo_mov` = 3 ) )
               GROUP BY
                 `detentos`.`iddetento`
               HAVING ';

$clausula_soma_cond = '( IFNULL( SUM( `gra_p_ano` ), 0 ) + IFNULL( SUM( `gra_p_mes` ), 0 )/12 + IFNULL( SUM( `gra_p_dia` ), 0 )/30 )';

$q_cond = array(
    '4'      => $q_cond_base . "$clausula_soma_cond > 0 AND $clausula_soma_cond <= 4",

    '4_8'    => $q_cond_base . "4 < $clausula_soma_cond AND $clausula_soma_cond <= 8",

    '8_15'   => $q_cond_base . "8 < $clausula_soma_cond AND $clausula_soma_cond <= 15 ",

    '15_20'  => $q_cond_base . "15 < $clausula_soma_cond AND $clausula_soma_cond <= 20 ",

    '20_30'  => $q_cond_base . "20 < $clausula_soma_cond AND $clausula_soma_cond <= 30 ",

    '30_50'  => $q_cond_base . "30 < $clausula_soma_cond AND $clausula_soma_cond <= 50 ",

    '50_100' => $q_cond_base . "50 < $clausula_soma_cond AND $clausula_soma_cond <= 100 ",

    '100'    => $q_cond_base . "$clausula_soma_cond > 100 "
);

$sit_pag = 'INFOPEN - CONDENAÇÃO';

// instanciando o model
$model = SicopModel::getInstance();

// executa as querys
foreach ( $q_cond as $key => $value ) {

    $q_cond["$key"] = $model->query( $value );

}

// fechando a conexao
$model->closeConnection();

// verificando se as querys foram executadas corretamente
foreach ( $q_cond as $key => $value ) {

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

// pegando o número de linhas das querys
foreach ( $q_cond as $key => $value ) {

    $q_cond["$key"] = $q_cond["$key"]->num_rows;

}

$total_4      = $q_cond['4'];
$total_4_8    = $q_cond['4_8'];
$total_8_15   = $q_cond['8_15'];
$total_15_20  = $q_cond['15_20'];
$total_20_30  = $q_cond['20_30'];
$total_30_50  = $q_cond['30_50'];
$total_50_100 = $q_cond['50_100'];
$total_100    = $q_cond['100'];

$total_cond = $total_4 + $total_4_8 + $total_8_15 + $total_15_20 + $total_20_30 + $total_30_50 + $total_50_100 + $total_100;



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

$desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_FU . 's pela condenação';

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 4 );
$trail->output();
?>

            <p class="descript_page">QUANTIDADE DE <?php echo SICOP_DET_DESC_U; ?>S DE ACORDO COM A CONDENAÇÃO</p>

            <table class="lista_busca">
                <tr>
                    <th width="30" align="center">N</th>
                    <th width="200" align="center">CONDENAÇÃO</th>
                    <th width="120">QUANTIDADE</th>
                    <th width="45">%</th>
                </tr>
                <tr class="even">
                    <td align="center" height="15">1</td>
                    <td><?php if ( $total_4 > 0 ) { ?><a href="lista_infop.php?tipo_infop=cond&cond=1" title="Clique aqui para listar <?php echo SICOP_DET_ART_L; ?>s <?php echo SICOP_DET_DESC_L; ?>s" ><?php } ?>Condenado até 4 anos<?php if ( $total_4 > 0 ) { ?></a><?php } ?></td>
                    <td align="center"><?php echo $total_4; ?></td>
                    <td align="center"><?php echo porcent_ref_pop( $total_4, $popdacasa ); ?></td>
                </tr>
                <tr class="even">
                    <td align="center" height="15">2</td>
                    <td><?php if ( $total_4_8 > 0 ) { ?><a href="lista_infop.php?tipo_infop=cond&cond=2" title="Clique aqui para listar <?php echo SICOP_DET_ART_L; ?>s <?php echo SICOP_DET_DESC_L; ?>s" ><?php } ?>Mais de 4 anos até 8 anos<?php if ( $total_4_8 > 0 ) { ?></a><?php } ?></td>
                    <td align="center"><?php echo $total_4_8; ?></td>
                    <td align="center"><?php echo porcent_ref_pop( $total_4_8, $popdacasa ); ?></td>
                </tr>
                <tr class="even">
                    <td align="center" height="15">3</td>
                    <td><?php if ( $total_8_15 > 0 ) { ?><a href="lista_infop.php?tipo_infop=cond&cond=3" title="Clique aqui para listar <?php echo SICOP_DET_ART_L; ?>s <?php echo SICOP_DET_DESC_L; ?>s" ><?php } ?>Mais de 8 anos até 15 anos<?php if ( $total_8_15 > 0 ) { ?></a><?php } ?></td>
                    <td align="center"><?php echo $total_8_15; ?></td>
                    <td align="center"><?php echo porcent_ref_pop( $total_8_15, $popdacasa ); ?></td>
                </tr>
                <tr class="even">
                    <td align="center" height="15" >4</td>
                    <td><?php if ( $total_15_20 > 0 ) { ?><a href="lista_infop.php?tipo_infop=cond&cond=4" title="Clique aqui para listar <?php echo SICOP_DET_ART_L; ?>s <?php echo SICOP_DET_DESC_L; ?>s" ><?php } ?>Mais de 15 anos até 20 anos<?php if ( $total_15_20 > 0 ) { ?></a><?php } ?></td>
                    <td align="center"><?php echo $total_15_20; ?></td>
                    <td align="center"><?php echo porcent_ref_pop( $total_15_20, $popdacasa ); ?></td>
                </tr>
                <tr class="even">
                    <td align="center" height="15">5</td>
                    <td><?php if ( $total_20_30 > 0 ) { ?><a href="lista_infop.php?tipo_infop=cond&cond=5" title="Clique aqui para listar <?php echo SICOP_DET_ART_L; ?>s <?php echo SICOP_DET_DESC_L; ?>s" ><?php } ?>Mais de 20 anos até 30 anos<?php if ( $total_20_30 > 0 ) { ?></a><?php } ?></td>
                    <td align="center"><?php echo $total_20_30; ?></td>
                    <td align="center"><?php echo porcent_ref_pop( $total_20_30, $popdacasa ); ?></td>
                </tr>
                <tr class="even">
                    <td align="center" height="15">6</td>
                    <td><?php if ( $total_30_50 > 0 ) { ?><a href="lista_infop.php?tipo_infop=cond&cond=6" title="Clique aqui para listar <?php echo SICOP_DET_ART_L; ?>s <?php echo SICOP_DET_DESC_L; ?>s" ><?php } ?>Mais de 30 anos até 50 anos<?php if ( $total_30_50 > 0 ) { ?></a><?php } ?></td>
                    <td align="center"><?php echo $total_30_50; ?></td>
                    <td align="center"><?php echo porcent_ref_pop( $total_30_50, $popdacasa ); ?></td>
                </tr>
                <tr class="even">
                    <td align="center" height="15">7</td>
                    <td><?php if ( $total_50_100 > 0 ) { ?><a href="lista_infop.php?tipo_infop=cond&cond=7" title="Clique aqui para listar <?php echo SICOP_DET_ART_L; ?>s <?php echo SICOP_DET_DESC_L; ?>s" ><?php } ?>Mais de 50 anos até 100 anos<?php if ( $total_50_100 > 0 ) { ?></a><?php } ?></td>
                    <td align="center"><?php echo $total_50_100; ?></td>
                    <td align="center"><?php echo porcent_ref_pop( $total_50_100, $popdacasa ); ?></td>
                </tr>
                <tr class="even">
                    <td align="center" height="15">8</td>
                    <td><?php if ( $total_100 > 0 ) { ?><a href="lista_infop.php?tipo_infop=cond&cond=8" title="Clique aqui para listar <?php echo SICOP_DET_ART_L; ?>s <?php echo SICOP_DET_DESC_L; ?>s" ><?php } ?>Condenado a mais de 100 anos<?php if ( $total_100 > 0 ) { ?></a><?php } ?></td>
                    <td align="center"><?php echo $total_100; ?></td>
                    <td align="center"><?php echo porcent_ref_pop( $total_100, $popdacasa ); ?></td>
                </tr>
                <tr class="even_dk">
                    <td align="center" height="15">&nbsp;</td>
                    <td align="center"><b>TOTAL</b></td>
                    <td align="center"><?php echo $total_cond; ?></td>
                    <td align="center"><?php echo porcent_ref_pop( $total_cond, $popdacasa ); ?></td>
                </tr>
            </table>

<?php include 'footer.php'; ?>