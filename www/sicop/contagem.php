<?php
if ( !isset( $_SESSION ) ) session_start();

require 'init/config.php';
require 'incl_complete.php';

$pag = link_pag( 'Mapa populacional' );
$tipo = '';

include 'progress/progress.php';

$mensagem = "Acesso à página do $pag";
salvaLog($mensagem);

$imp_cadastro  = get_session( 'imp_cadastro', 'int' );
$imp_chefia    = get_session( 'imp_chefia', 'int' );
$imp_incl      = get_session( 'imp_incl', 'int' );
$imp_portaria  = get_session( 'imp_portaria', 'int' );

$where_total   = get_where_det( 1 );
$where_na      = get_where_det( 2 );
$where_da      = get_where_det( 3 );
$where_trana   = get_where_det( 4 );
$where_trada   = get_where_det( 5 );
$where_tranada = get_where_det( 11 );
$where_nada    = get_where_det( 12 );



/*
 * montar as querys dos raios
 * dos detentos na casa
 */
// 8 raios + incl + pd + ph + ps
$raios     = 10;
$q_raio_na = array();

for ( $index = 1; $index <= $raios; $index++ ) {

    $q_raio_na["$index"] = "SELECT
                           COUNT(*) AS `total`
                         FROM
                           `detentos`
                           INNER JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
                           INNER JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
                           LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                           LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                         WHERE
                           `raio`.`idraio` = $index
                           AND
                           $where_na";

}


/*
 * -----------------------------------------------------
 */

/*
 * montar as querys dos raios
 * dos detentos da casa
 */

// 8 raios + incl + pd + ph + ps
$raios     = 10;
$q_raio_da = array();

for ( $index = 1; $index <= $raios; $index++ ) {

    $q_raio_da["$index"] = "SELECT
                           COUNT(*) AS `total`
                         FROM
                           `detentos`
                           INNER JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
                           INNER JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
                           LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                           LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                         WHERE
                           `raio`.`idraio` = $index
                           AND
                           $where_da";

}

/*
 * -----------------------------------------------------
 */



/*
 * montar as querys dos raios
 * dos detentos da casa e na casa - total
 */

// 8 raios + incl + pd + ph + ps
$raios        = 10;
$q_raio_total = array();

for ( $index = 1; $index <= $raios; $index++ ) {

    $q_raio_total["$index"] = "SELECT
                                 COUNT(*) AS `total`
                               FROM
                                 `detentos`
                                 INNER JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
                                 INNER JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
                                 LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                                 LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                               WHERE
                                 `raio`.`idraio` = $index
                                 AND
                                 $where_total";

}

/*
 * -----------------------------------------------------
 */

$q_pop = array();

$q_pop['total'] = "SELECT
                     COUNT(*) AS `total`
                   FROM
                     `detentos`
                     LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                     LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                   WHERE
                     $where_total";

$q_pop['transna'] = "SELECT
                       COUNT(*) AS `total`
                     FROM
                       `detentos`
                       LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                       LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                     WHERE
                       $where_trana";

$q_pop['transda'] = "SELECT
                       COUNT(*) AS `total`
                     FROM
                       `detentos`
                       LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                       LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                     WHERE
                       $where_trada";

$q_pop['transnada'] = "SELECT
                         COUNT(*) AS `total`
                       FROM
                         `detentos`
                         LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                         LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                       WHERE
                         $where_tranada";

$q_pop['nada'] = "SELECT
                    COUNT(*) AS `total`
                  FROM
                    `detentos`
                    LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                    LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                  WHERE
                    $where_nada";

$q_pop['na'] = "SELECT
                  COUNT(*) AS `total`
                FROM
                  `detentos`
                  LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                  LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                WHERE
                  $where_na";

$q_pop['da'] = "SELECT
                  COUNT(*) AS `total`
                FROM
                  `detentos`
                  LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                  LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                WHERE
                  $where_da";


/*
 * montar as querys das contagens de movimetações
 */

// 8 = in + it + ir + ie + ex + et + er + ee
$tipo_movs = 8;
$q_mov     = array();

for ( $index = 1; $index <= $tipo_movs; $index++ ) {

    $q_mov["$index"] = "SELECT
                          COUNT(`mov_det`.`cod_tipo_mov`) AS `totalmov`
                        FROM
                          `mov_det`
                          INNER JOIN `tipomov` ON `mov_det`.`cod_tipo_mov` = `tipomov`.`idtipo_mov`
                        WHERE
                          `mov_det`.`data_mov` = DATE(NOW())
                          AND
                          `mov_det`.`cod_tipo_mov` = $index";

}

/*
 * -----------------------------------------------------
 */


$db = SicopModel::getInstance();

$querytime_before = array_sum( explode( ' ', microtime() ) );

// executa as querys dos total dos raios na casa
foreach ( $q_raio_na as $key => $value ) {

    $q_raio_na["$key"] = $db->fetchOne( $q_raio_na["$key"] );

}

// executa as querys dos total dos raios da casa
foreach ( $q_raio_da as $key => $value ) {

    $q_raio_da["$key"] = $db->fetchOne( $q_raio_da["$key"] );

}

// executa as querys dos total dos raios total
foreach ( $q_raio_total as $key => $value ) {

    $q_raio_total["$key"] = $db->fetchOne( $q_raio_total["$key"] );

}

// executa as querys das populações
foreach ( $q_pop as $key => $value ) {

    $q_pop["$key"] = $db->fetchOne( $q_pop["$key"] );

}

// executa as querys das movimentações
foreach ( $q_mov as $key => $value ) {

    $q_mov["$key"] = $db->fetchOne( $q_mov["$key"] );

}

$querytime_after = array_sum( explode( ' ', microtime() ) );

$querytime = $querytime_after - $querytime_before;

$db->closeConnection();


$raio_1_na    = $q_raio_na['1'];
$raio_2_na    = $q_raio_na['2'];
$raio_3_na    = $q_raio_na['3'];
$raio_4_na    = $q_raio_na['4'];
$raio_incl_na    = $q_raio_na['5'];
$raio_pd_na    = $q_raio_na['6'];
$raio_tri_na    = $q_raio_na['7'];
$raio_ala_na    = $q_raio_na['8'];
$raio_incl_ala_na = $q_raio_na['9'];
$raio_ph_na   = $q_raio_na['10'];


$raio_1_da    = $q_raio_da['1'];
$raio_2_da    = $q_raio_da['2'];
$raio_3_da    = $q_raio_da['3'];
$raio_4_da    = $q_raio_da['4'];
$raio_incl_da    = $q_raio_da['5'];
$raio_pd_da    = $q_raio_da['6'];
$raio_tri_da    = $q_raio_da['7'];
$raio_ala_da    = $q_raio_da['8'];
$raio_incl_ala_da = $q_raio_da['9'];
$raio_ph_da   = $q_raio_da['10'];


$raio_1_total    = $q_raio_total['1'];
$raio_2_total    = $q_raio_total['2'];
$raio_3_total    = $q_raio_total['3'];
$raio_4_total    = $q_raio_total['4'];
$raio_incl_total    = $q_raio_total['5'];
$raio_pd_total    = $q_raio_total['6'];
$raio_tri_total    = $q_raio_total['7'];
$raio_ala_total    = $q_raio_total['8'];
$raio_incl_ala_total = $q_raio_total['9'];
$raio_ph_total   = $q_raio_total['10'];


$pop_total     = $q_pop['total'];
$pop_transna   = $q_pop['transna'];
$pop_transda   = $q_pop['transda'];
$pop_transnada = $q_pop['transnada'];
$pop_nada      = $q_pop['nada'];
$pop_na        = $q_pop['na'];
$pop_da        = $q_pop['da'];


$d_mov_in = $q_mov['1'];
$d_mov_it = $q_mov['2'];
$d_mov_ir = $q_mov['3'];
$d_mov_ie = $q_mov['4'];
$d_mov_ex = $q_mov['5'];
$d_mov_et = $q_mov['6'];
$d_mov_er = $q_mov['7'];
$d_mov_ee = $q_mov['8'];


$data_sf = date( 'd/m/Y' );

$porcentna = $pop_na / 768 * 100;
$porcentna = round( $porcentna, 0 );

$corna = 'red';
if ( $porcentna <= 90 ) {
    $corna = 'green';
} elseif ( ($porcentna > 90) && ($porcentna <= 100) ) {
    $corna = 'blue';
}

$porcentda = $pop_da / 768 * 100;
$porcentda = round( $porcentda, 0 );

$corda = 'red';
if ( $porcentda <= 90 ) {
    $corda = 'green';
} elseif ( ($porcentda > 90) && ($porcentda <= 100) ) {
    $corda = 'blue';
}

$desc_pag = 'Mapa populacional';

// adicionando o javascript
$cab_js = 'ajax/jq_contagem.js';
set_cab_js( $cab_js );

require 'cab.php';
$trail = new Breadcrumb();
$trail->add( $desc_pag, $_SERVER['PHP_SELF'], 1 );
$trail->output();
?>

            <p class="descript_page">MAPA POPULACIONAL</p>

            <p class="table_leg">
                <a href="cont_pop.php" title="Consultar a população de outras datas" >Consultar outros períodos</a> |
                <a href="graf_pop.php" title="Gerar graficos populacionais por período" >Graficos populacionais</a>
                <?php if ( $imp_cadastro >= 1 or $imp_chefia >= 1 or $imp_incl >= 1 or $imp_portaria >= 1 ) { ?>
                <!--| <a id="print_map" href='javascript:void(0)' title="Imprimir a lista" >Imprimir</a>-->
                <?php } ?>
            </p>

            <form action="" method="post" name="lista_det" id="lista_det"></form>

            <table class="contagem" style="margin-top: 0">
                <tr>
                    <td class="soma_pop_mid"><?php if ( !empty( $pop_transna ) ) { ?><a href="listacont.php?sitd=4" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s" ><?php } ?>Transito na casa: <?php echo $pop_transna ?><?php if ( !empty( $pop_transna ) ) { ?></a><?php } ?></td>
                    <td class="soma_pop_mid"><?php if ( !empty( $pop_transda ) ) { ?><a href="listacont.php?sitd=5" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s" ><?php } ?>Transito da casa: <?php echo $pop_transda ?><?php if ( !empty( $pop_transda ) ) { ?></a><?php } ?></td>
                    <td class="soma_pop_mid"><?php if ( !empty( $pop_transnada ) ) { ?><a href="listacont.php?sitd=11" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s" ><?php } ?>Transito na casa da casa: <?php echo $pop_transnada ?><?php if ( !empty( $pop_transnada ) ) { ?></a><?php } ?></td>
                </tr>
                <tr>
                    <td class="soma_pop_mid"><?php if ( !empty( $pop_nada ) ) { ?><a href="listacont.php?sitd=12" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s" ><?php } ?>Na casa da casa: <?php echo $pop_nada ?><?php if ( !empty( $pop_nada ) ) { ?></a><?php } ?></td>
                    <td class="soma_pop_mid"><?php if ( !empty( $pop_na ) ) { ?><a href="listacont.php?sitd=2" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s" ><?php } ?>Na casa: <?php echo $pop_na ?><?php if ( !empty( $pop_na ) ) { ?></a><?php } ?></td>
                    <td class="soma_pop_mid"><?php if ( !empty( $pop_da ) ) { ?><a href="listacont.php?sitd=3" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s" ><?php } ?>Da casa: <?php echo $pop_da ?><?php if ( !empty( $pop_da ) ) { ?></a><?php } ?></td>
                </tr>
                <tr>
                    <td class="soma_pop_grt" colspan="3"><?php if ( !empty( $pop_total ) ) { ?><a href="listacont.php?sitd=1" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s" ><?php } ?>População total: <?php echo $pop_total ?><?php if ( !empty( $pop_total ) ) { ?></a><?php } ?></td>
                </tr>
            </table>

            <table style="margin: 20px auto 0;">
                <tr>
                    <td style="text-align: center; height: 15px;" colspan="2">Porcentagem de ocupação:</td>
                </tr>
                <tr>
                    <td style="text-align: center; height: 15px; width: 55px; vertical-align: middle;">Na Casa:</td>
                    <td style="text-align: center; height: 15px; width: 210px; vertical-align: middle;"><?php echo show_prog_bar( 200, $porcentna, $corna, 'black' ); ?></td>
                </tr>
                <tr>
                    <td style="text-align: center; height: 15px; width: 55px; vertical-align: middle;">Da Casa:</td>
                    <td style="text-align: center; height: 15px; width: 210px; vertical-align: middle;"><?php echo show_prog_bar( 200, $porcentda, $corda, 'black' ); ?></td>
                </tr>
            </table>

            <p style="text-align: center; height: 15px; margin-top: 20px">MOVIMENTAÇÕES HOJE</p>

            <p style="text-align: center; height: 15px;"><a href="cont_mov.php" title="Consultar movimentações de outras datas" >Consultar outros períodos</a></p>

            <table class="cont_mov">
                <tr>
                    <th>TIPO DE MOVIMENTAÇÃO</th>
                    <th>QUANTIDADE</th>
                </tr>
                <tr class="even">
                    <td class="cont_desc">
                        <?php
                        if ( empty( $d_mov_in ) ) {
                            echo 'Inclusão';
                        } else {
                        ?>
                        <a href="listamov.php?tipomov=1&data_ini=<?php echo $data_sf; ?>" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s" >Inclusão</a>
                        <?php } ?>
                    </td>
                    <td class="cont_result"><?php echo $d_mov_in; ?></td>
                </tr>
                <tr class="even">
                    <td class="cont_desc">
                        <?php
                        if ( empty( $d_mov_it ) ) {
                            echo 'Inclusão por transito';
                        } else {
                        ?>
                        <a href="listamov.php?tipomov=2&data_ini=<?php echo $data_sf; ?>" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s" >Inclusão por transito</a>
                        <?php } ?>
                    </td>
                    <td class="cont_result"><?php echo $d_mov_it; ?></td>
                </tr>
                <tr class="even">
                    <td class="cont_desc">
                        <?php
                        if ( empty( $d_mov_ir ) ) {
                            echo 'Inclusão por remoção';
                        } else {
                        ?>
                        <a href="listamov.php?tipomov=3&data_ini=<?php echo $data_sf; ?>" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s" >Inclusão por remoção</a>
                        <?php } ?>
                    </td>
                    <td class="cont_result"><?php echo $d_mov_ir; ?></td>
                </tr>
                <tr class="even">
                    <td class="cont_desc">
                        <?php
                        if ( empty( $d_mov_ie ) ) {
                            echo 'Inclusão por retorno';
                        } else {
                        ?>
                        <a href="listamov.php?tipomov=4&data_ini=<?php echo $data_sf; ?>" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s" >Inclusão por retorno</a>
                        <?php } ?>
                    </td>
                    <td class="cont_result"><?php echo $d_mov_ie; ?></td>
                </tr>
                <tr class="even">
                    <td class="cont_desc">
                        <?php
                        if ( empty( $d_mov_ex ) ) {
                            echo 'Exclusão';
                        } else {
                        ?>
                        <a href="listamov.php?tipomov=5&data_ini=<?php echo $data_sf; ?>" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s" >Exclusão</a>
                        <?php } ?>
                    </td>
                    <td class="cont_result"><?php echo $d_mov_ex; ?></td>
                </tr>
                <tr class="even">
                    <td class="cont_desc">
                        <?php
                        if ( empty( $d_mov_et ) ) {
                            echo 'Exclusão por transito';
                        } else {
                        ?>
                        <a href="listamov.php?tipomov=6&data_ini=<?php echo $data_sf; ?>" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s" >Exclusão por transito</a>
                        <?php } ?>
                    </td>
                    <td class="cont_result"><?php echo $d_mov_et; ?></td>
                </tr>
                <tr class="even">
                    <td class="cont_desc">
                        <?php
                        if ( empty( $d_mov_er ) ) {
                            echo 'Exclusão por remoção';
                        } else {
                        ?>
                        <a href="listamov.php?tipomov=7&data_ini=<?php echo $data_sf; ?>" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s" >Exclusão por remoção</a>
                        <?php } ?>
                    </td>
                    <td class="cont_result"><?php echo $d_mov_er; ?></td>
                </tr>
                <tr class="even">
                    <td class="cont_desc">
                        <?php
                        if ( empty( $d_mov_ee ) ) {
                            echo 'Exclusão por retorno';
                        } else {
                        ?>
                        <a href="listamov.php?tipomov=8&data_ini=<?php echo $data_sf; ?>" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s" >Exclusão por retorno</a>
                        <?php } ?>
                    </td>
                    <td class="cont_result"><?php echo $d_mov_ee; ?></td>
                </tr>
            </table>

            <table class="contagem">
                <tr>
                    <td class="cont_raio_dest_geral" colspan="8">Contagem  - detentos na casa</td>
                </tr>
                
                <tr>
                    <td class="cont_raio_link_grt" colspan="2"><a href="listacont.php?sitd=2&raio=1" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >raio 1</a></td>
                    <td class="cont_raio_link_grt" colspan="2"><a href="listacont.php?sitd=2&raio=2" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >raio 2</a></td>
                    <td class="cont_raio_link_grt" colspan="2"><a href="listacont.php?sitd=2&raio=3" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >raio 3</a></td>
                    <td class="cont_raio_link_grt" colspan="2"><a href="listacont.php?sitd=2&raio=4" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >raio 4</a></td>
                </tr>
                
                <tr>
                    <td class="cont_raio_link_grt" colspan="2">Total: <?php echo $raio_1_na; ?></td>
                    <td class="cont_raio_link_grt" colspan="2">Total: <?php echo $raio_2_na; ?></td>
                    <td class="cont_raio_link_grt" colspan="2">Total: <?php echo $raio_3_na; ?></td>
                    <td class="cont_raio_link_grt" colspan="2">Total: <?php echo $raio_4_na; ?></td>
                </tr>
                
                <tr>
                    <td class="cont_raio_link"><a href="listacont.php?sitd=2&raio=5" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >Inclusão</a></td>
                    <td class="cont_raio_link"><a href="listacont.php?sitd=2&raio=6" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >PD</a></td>
                    <td class="cont_raio_link"><a href="listacont.php?sitd=2&raio=7" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >Triagem</a></td>
                    <td class="cont_raio_link"><a href="listacont.php?sitd=2&raio=8" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >Ala</a></td>
                    <td class="cont_raio_link"><a href="listacont.php?sitd=2&raio=9" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >Ala inclusão</a></td>
                    <td class="cont_raio_link"><a href="listacont.php?sitd=2&raio=10" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >PH</a></td>
                    <td class="cont_raio_link"></td>
                    <td class="cont_raio_link"></td>
                </tr>
                
                <tr>
                    <td class="cont_raio_total">Total: <?php echo $raio_incl_na; ?></td>
                    <td class="cont_raio_total">Total: <?php echo $raio_pd_na; ?></td>
                    <td class="cont_raio_total">Total: <?php echo $raio_tri_na; ?></td>
                    <td class="cont_raio_total">Total: <?php echo $raio_ala_na; ?></td>
                    <td class="cont_raio_total">Total: <?php echo $raio_incl_ala_na; ?></td>
                    <td class="cont_raio_total">Total: <?php echo $raio_ph_na; ?></td>
                    <td class="cont_raio_total"></td>
                    <td class="cont_raio_total"></td>
                </tr>

            </table>
            
            <table class="contagem">
                <tr>
                    <td class="cont_raio_dest_geral" colspan="8">Contagem  - detentos da casa</td>
                </tr>
                
                <tr>
                    <td class="cont_raio_link_grt" colspan="2"><a href="listacont.php?sitd=3&raio=1" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >raio 1</a></td>
                    <td class="cont_raio_link_grt" colspan="2"><a href="listacont.php?sitd=3&raio=2" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >raio 2</a></td>
                    <td class="cont_raio_link_grt" colspan="2"><a href="listacont.php?sitd=3&raio=3" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >raio 3</a></td>
                    <td class="cont_raio_link_grt" colspan="2"><a href="listacont.php?sitd=3&raio=4" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >raio 4</a></td>
                </tr>
                
                <tr>
                    <td class="cont_raio_link_grt" colspan="2">Total: <?php echo $raio_1_da; ?></td>
                    <td class="cont_raio_link_grt" colspan="2">Total: <?php echo $raio_2_da; ?></td>
                    <td class="cont_raio_link_grt" colspan="2">Total: <?php echo $raio_3_da; ?></td>
                    <td class="cont_raio_link_grt" colspan="2">Total: <?php echo $raio_4_da; ?></td>
                </tr>
                
                <tr>
                    <td class="cont_raio_link"><a href="listacont.php?sitd=3&raio=5" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >Inclusão</a></td>
                    <td class="cont_raio_link"><a href="listacont.php?sitd=3&raio=6" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >PD</a></td>
                    <td class="cont_raio_link"><a href="listacont.php?sitd=3&raio=7" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >Triagem</a></td>
                    <td class="cont_raio_link"><a href="listacont.php?sitd=3&raio=8" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >Ala</a></td>
                    <td class="cont_raio_link"><a href="listacont.php?sitd=3&raio=9" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >Ala inclusão</a></td>
                    <td class="cont_raio_link"><a href="listacont.php?sitd=3&raio=10" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >PH</a></td>
                    <td class="cont_raio_link"></td>
                    <td class="cont_raio_link"></td>
                </tr>
                
                <tr>
                    <td class="cont_raio_total">Total: <?php echo $raio_incl_da; ?></td>
                    <td class="cont_raio_total">Total: <?php echo $raio_pd_da; ?></td>
                    <td class="cont_raio_total">Total: <?php echo $raio_tri_da; ?></td>
                    <td class="cont_raio_total">Total: <?php echo $raio_ala_da; ?></td>
                    <td class="cont_raio_total">Total: <?php echo $raio_incl_ala_da; ?></td>
                    <td class="cont_raio_total">Total: <?php echo $raio_ph_da; ?></td>
                    <td class="cont_raio_total"></td>
                    <td class="cont_raio_total"></td>
                </tr>

            </table>      


            <table class="contagem">
                <tr>
                    <td class="cont_raio_dest_geral" colspan="8">Contagem - Total</td>
                </tr>
                
                <tr>
                    <td class="cont_raio_link_grt" colspan="2"><a href="listacont.php?sitd=1&raio=1" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >raio 1</a></td>
                    <td class="cont_raio_link_grt" colspan="2"><a href="listacont.php?sitd=1&raio=2" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >raio 2</a></td>
                    <td class="cont_raio_link_grt" colspan="2"><a href="listacont.php?sitd=1&raio=3" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >raio 3</a></td>
                    <td class="cont_raio_link_grt" colspan="2"><a href="listacont.php?sitd=1&raio=4" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >raio 4</a></td>
                </tr>
                
                <tr>
                    <td class="cont_raio_link_grt" colspan="2">Total: <?php echo $raio_1_total; ?></td>
                    <td class="cont_raio_link_grt" colspan="2">Total: <?php echo $raio_2_total; ?></td>
                    <td class="cont_raio_link_grt" colspan="2">Total: <?php echo $raio_3_total; ?></td>
                    <td class="cont_raio_link_grt" colspan="2">Total: <?php echo $raio_4_total; ?></td>
                </tr>
                
                <tr>
                    <td class="cont_raio_link"><a href="listacont.php?sitd=1&raio=5" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >Inclusão</a></td>
                    <td class="cont_raio_link"><a href="listacont.php?sitd=1&raio=6" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >PD</a></td>
                    <td class="cont_raio_link"><a href="listacont.php?sitd=1&raio=7" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >Triagem</a></td>
                    <td class="cont_raio_link"><a href="listacont.php?sitd=1&raio=8" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >Ala</a></td>
                    <td class="cont_raio_link"><a href="listacont.php?sitd=1&raio=9" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >Ala inclusão</a></td>
                    <td class="cont_raio_link"><a href="listacont.php?sitd=1&raio=10" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s deste raio" >PH</a></td>
                    <td class="cont_raio_link"></td>
                    <td class="cont_raio_link"></td>
                </tr>
                
                <tr>
                    <td class="cont_raio_total">Total: <?php echo $raio_incl_total; ?></td>
                    <td class="cont_raio_total">Total: <?php echo $raio_pd_total; ?></td>
                    <td class="cont_raio_total">Total: <?php echo $raio_tri_total; ?></td>
                    <td class="cont_raio_total">Total: <?php echo $raio_ala_total; ?></td>
                    <td class="cont_raio_total">Total: <?php echo $raio_incl_ala_total; ?></td>
                    <td class="cont_raio_total">Total: <?php echo $raio_ph_total; ?></td>
                    <td class="cont_raio_total"></td>
                    <td class="cont_raio_total"></td>
                </tr>

            </table>                        

            <p class="p_q_info">Tempo gasto para realizar as contagens: <?php echo round($querytime, 2) ?> seg</p>

            <!--<p><?php //echo date('d \d\e F \d\e Y') ?> </p>
            <p><?php //echo date('d/m/Y') ?> </p>
            <p><?php //echo mktime() ?> </p>
            <p><?php //echo /*utf8_encode*/(strftime('%A, %d de %B de %Y, %H:%M', mktime())) ?> </p>
            <p>São José do Rio Preto, <?php //echo utf8_encode(strftime('%d de %B de %Y', mktime())) ?> </p>
            <p><?php //echo date('Y-m-d H:m:s') ?> </p>
            <p><?php //echo date('Y-m-d')  ?> </p>-->

<?php include 'footer.php';?>