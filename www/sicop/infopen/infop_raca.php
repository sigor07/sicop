<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_pront   = get_session( 'n_pront', 'int' );
$n_pront_n = 2;

$motivo_pag = 'INFOPEN - RAÇA';

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

$q_raca = "SELECT
             `tipocutis`.`cutis`,
             `detentos`.`cod_cutis`,
             COUNT(`detentos`.`cod_cutis`) AS `total`
           FROM
             `detentos`
             LEFT JOIN `tipocutis` ON `detentos`.`cod_cutis` = `tipocutis`.`idcutis`
             LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
             LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
           WHERE
             ( ( ISNULL( `mov_det_out`.`cod_tipo_mov` ) OR `mov_det_out`.`cod_tipo_mov` = 4 OR `mov_det_out`.`cod_tipo_mov` = 6 )
               AND
               ( `mov_det_in`.`cod_tipo_mov` = 1 OR `mov_det_in`.`cod_tipo_mov` = 3 ) )
           GROUP BY
             `detentos`.`cod_cutis`
           HAVING
             NOT ISNULL( `detentos`.`cod_cutis` )
           ORDER BY
             `tipocutis`.`cutis` ASC";

$sit_pag = 'INFOPEN - RAÇA';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_raca = $model->query( $q_raca );

// fechando a conexao
$model->closeConnection();

if( !$q_raca ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_raca = $q_raca->num_rows;

$q_t_raca = 'SELECT
              COUNT(*) AS total
            FROM
               `detentos`
               LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
               LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
             WHERE
               NOT ISNULL( `detentos`.`cod_cutis` ) AND
               ( ( ISNULL( `mov_det_out`.`cod_tipo_mov` ) OR `mov_det_out`.`cod_tipo_mov` = 4 OR `mov_det_out`.`cod_tipo_mov` = 6 )
                 AND
                 ( `mov_det_in`.`cod_tipo_mov` = 1 OR `mov_det_in`.`cod_tipo_mov` = 3 ) )';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$d_t_raca = $model->fetchOne( $q_t_raca );

// fechando a conexao
$model->closeConnection();

if( $d_t_raca === false ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$q_raca_pend = 'SELECT
                  COUNT( `detentos`.`iddetento` ) AS total
                FROM
                  `detentos`
                  LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                  LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                WHERE
                  ( ( ISNULL( `mov_det_out`.`cod_tipo_mov` ) OR `mov_det_out`.`cod_tipo_mov` = 4 OR `mov_det_out`.`cod_tipo_mov` = 6 )
                     AND
                     (`mov_det_in`.`cod_tipo_mov` = 1 OR `mov_det_in`.`cod_tipo_mov` = 3 ) )
                  AND ISNULL( `detentos`.`cod_cutis` )';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$d_raca_pend = $model->fetchOne( $q_raca_pend );

// fechando a conexao
$model->closeConnection();

if( $d_raca_pend === false ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

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

$desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_FU . 's pela raça';

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 4 );
$trail->output();
?>

            <p class="descript_page">QUANTIDADE DE <?php echo SICOP_DET_DESC_U; ?>S DE ACORDO COM A RAÇA</p>

            <?php
            if ( empty( $cont_raca ) or $cont_raca < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não foi encontrado nenhuma ocorrência.</p>';
                include 'footer.php';
                exit;
            }
            ?>

            <table class="lista_busca">
                <tr>
                    <th width="30" align="center">N</th>
                    <th width="129" align="center">RAÇA</th>
                    <th width="129">QUANTIDADE</th>
                    <th width="45">%</th>
                </tr>
            <?php
            $i = 0;

            while ( $d_raca = $q_raca->fetch_assoc() ) {
                ?>
                    <tr class="even">
                        <td align="center"><?php echo++$i; ?></td>
                        <td><a href="lista_infop.php?tipo_infop=raca&amp;idraca=<?php echo $d_raca['cod_cutis']; ?>" title="Clique aqui para listar <?php echo SICOP_DET_ART_L; ?>s <?php echo SICOP_DET_DESC_L; ?>s" ><?php echo $d_raca['cutis']; ?></a></td>
                        <td align="center"><?php echo $d_raca['total']; ?></td>
                        <td align="center"><?php echo porcent_ref_pop( $d_raca['total'], $popdacasa ); ?></td>
                    </tr>
            <?php } ?>
                <tr class="even_dk">
                    <td align="center">&nbsp;</td>
                    <td align="center"><b>TOTAL</b></td>
                    <td align="center"><?php echo $d_t_raca; ?></td>
                    <td align="center"><?php echo porcent_ref_pop( $d_t_raca, $popdacasa ); ?></td>
                </tr>
                <tr class="even_dk">
                    <td align="center">&nbsp;</td>
                    <td><?php if ( $d_raca_pend > 0 ) { ?><a href="lista_infop.php?tipo_infop=praca" title="Clique aqui para listar <?php echo SICOP_DET_ART_L; ?>s <?php echo SICOP_DET_DESC_L; ?>s" ><?php } ?>Pendencias de raça<?php if ( $d_raca_pend > 0 ) { ?></a><?php } ?></td>
                    <td align="center"><?php echo $d_raca_pend; ?></td>
                    <td align="center"><?php echo porcent_ref_pop( $d_raca_pend, $popdacasa ); ?></td>
                </tr>
            </table>

<?php include 'footer.php'; ?>