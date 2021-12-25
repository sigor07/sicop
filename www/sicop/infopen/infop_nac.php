<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_pront   = get_session( 'n_pront', 'int' );
$n_pront_n = 2;

$motivo_pag = 'INFOPEN - NACIONALIDADE';

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

$q_nac = "SELECT
            `tiponacionalidade`.`nacionalidade`,
            `detentos`.`cod_nacionalidade`,
            COUNT(`detentos`.`cod_nacionalidade`) AS `total`
          FROM
            `detentos`
            LEFT JOIN `tiponacionalidade` ON `detentos`.`cod_nacionalidade` = `tiponacionalidade`.`idnacionalidade`
            LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
            LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
          WHERE
            ( ( ISNULL( `mov_det_out`.`cod_tipo_mov` ) OR `mov_det_out`.`cod_tipo_mov` = 4 OR `mov_det_out`.`cod_tipo_mov` = 6 )
              AND
              ( `mov_det_in`.`cod_tipo_mov` = 1 OR `mov_det_in`.`cod_tipo_mov` = 3 ) )
          GROUP BY
            `detentos`.`cod_nacionalidade`
          HAVING
            NOT ISNULL( `detentos`.`cod_nacionalidade` )
          ORDER BY
            `tiponacionalidade`.`nacionalidade` ASC";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_nac = $model->query( $q_nac );

// fechando a conexao
$model->closeConnection();

if( !$q_nac ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_nac = $q_nac->num_rows;

$q_t_nac = 'SELECT
              COUNT(*) AS total
            FROM
               `detentos`
               LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
               LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
             WHERE
               NOT ISNULL( `detentos`.`cod_nacionalidade` ) AND
               ( ( ISNULL( `mov_det_out`.`cod_tipo_mov` ) OR `mov_det_out`.`cod_tipo_mov` = 4 OR `mov_det_out`.`cod_tipo_mov` = 6 )
                 AND
                 ( `mov_det_in`.`cod_tipo_mov` = 1 OR `mov_det_in`.`cod_tipo_mov` = 3 ) )';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$d_t_nac = $model->fetchOne( $q_t_nac );

// fechando a conexao
$model->closeConnection();

if( $d_t_nac === false ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$q_nac_pend = 'SELECT
                 COUNT( `detentos`.`iddetento` ) AS total
               FROM
                 `detentos`
                 LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                 LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
               WHERE
                 ( ( ISNULL( `mov_det_out`.`cod_tipo_mov` ) OR `mov_det_out`.`cod_tipo_mov` = 4 OR `mov_det_out`.`cod_tipo_mov` = 6 )
                    AND
                    (`mov_det_in`.`cod_tipo_mov` = 1 OR `mov_det_in`.`cod_tipo_mov` = 3 ) )
                 AND ISNULL( `detentos`.`cod_nacionalidade` )';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$d_nac_pend = $model->fetchOne( $q_nac_pend );

// fechando a conexao
$model->closeConnection();

if( $d_nac_pend === false ) {

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

$desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_FU . 's pela nacionalidade';

?>
<?php
require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if (!empty($qs)){
    $pag_atual .=  '?' . $qs;
}

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 4 );
$trail->output();
?>

            <p class="descript_page">QUANTIDADE DE <?php echo SICOP_DET_DESC_U; ?>S DE ACORDO COM A NACIONALIDADE</p>

            <?php
            if ( empty( $cont_nac ) or $cont_nac < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não foi encontrado nenhuma ocorrência.</p>';
                include 'footer.php';
                exit;
            }
            ?>

            <table class="lista_busca">
                <tr>
                    <th width="30" align="center">N</th>
                    <th width="180" align="center">NACIONALIDADE</th>
                    <th width="125">QUANTIDADE</th>
                    <th width="45">%</th>
                </tr>
            <?php
            $i = 0;

            while ( $d_nac = $q_nac->fetch_assoc() ) {
                ?>
                    <tr class="even">
                        <td align="center"><?php echo++$i; ?></td>
                        <td><a href="lista_infop.php?tipo_infop=nac&amp;idnac=<?php echo $d_nac['cod_nacionalidade']; ?>" title="Clique aqui para listar <?php echo SICOP_DET_ART_L; ?>s <?php echo SICOP_DET_DESC_L; ?>s" ><?php echo $d_nac['nacionalidade']; ?></a></td>
                        <td align="center"><?php echo $d_nac['total']; ?></td>
                        <td align="center"><?php echo porcent_ref_pop( $d_nac['total'], $popdacasa ); ?></td>
                    </tr>
            <?php } ?>
                <tr class="even_dk">
                    <td align="center">&nbsp;</td>
                    <td align="center"><b>TOTAL</b></td>
                    <td align="center"><?php echo $d_t_nac; ?></td>
                    <td align="center">&nbsp;</td>
                </tr>
                <tr class="even_dk">
                    <td align="center">&nbsp;</td>
                    <td><?php if ( $d_nac_pend > 0 ) { ?><a href="lista_infop.php?tipo_infop=pnac" title="Clique aqui para listar <?php echo SICOP_DET_ART_L; ?>s <?php echo SICOP_DET_DESC_L; ?>s" ><?php } ?>Pendencias de nacionalidade<?php if ( $d_nac_pend > 0 ) { ?></a><?php } ?></td>
                    <td align="center"><?php echo $d_nac_pend; ?></td>
                    <td align="center"><?php echo porcent_ref_pop( $d_nac_pend, $popdacasa ); ?></td>
                </tr>
            </table>

<?php include 'footer.php'; ?>