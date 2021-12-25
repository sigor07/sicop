<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_pront   = get_session( 'n_pront', 'int' );
$n_pront_n = 2;

$motivo_pag = 'INFOPEN - PROCEDÊNCIA';

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

$q_proced = 'SELECT
               `unidades_in`.`idunidades`,
               `unidades_in`.`unidades` AS procedencia,
               COUNT(`detentos`.`iddetento`) AS `total`
             FROM
               `detentos`
               LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
               LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
               LEFT JOIN `unidades` `unidades_in` ON `mov_det_in`.`cod_local_mov` = `unidades_in`.`idunidades`
             WHERE
               ( ( ISNULL( `mov_det_out`.`cod_tipo_mov` ) OR `mov_det_out`.`cod_tipo_mov` = 4 OR `mov_det_out`.`cod_tipo_mov` = 6 )
                 AND
                 ( `mov_det_in`.`cod_tipo_mov` = 1 OR `mov_det_in`.`cod_tipo_mov` = 3 ) )
             GROUP BY
               `unidades_in`.`idunidades`
             ORDER BY
               `unidades_in`.`unidades` ASC';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_proced = $model->query( $q_proced );

// fechando a conexao
$model->closeConnection();

if( !$q_proced ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_proced = $q_proced->num_rows;

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

$desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's pela procedência';

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

            <p class="descript_page">QUANTIDADE DE <?php echo SICOP_DET_DESC_U; ?>S DE ACORDO COM A PROCEDÊNCIA</p>

            <?php
            if ( empty( $cont_proced ) or $cont_proced < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não foi encontrado nenhuma ocorrência.</p>';
                include 'footer.php';
                exit;
            }
            ?>

            <table class="lista_busca">
                <tr>
                    <th width="30" align="center">N</th>
                    <th width="180" align="center">PROCEDÊNCIA</th>
                    <th width="120">QUANTIDADE</th>
                    <th width="45">%</th>
                </tr>
            <?php
            $i = 0;

            while ( $d_proced = $q_proced->fetch_assoc() ) {
                ?>
                    <tr class="even">
                        <td align="center" height="15"><?php echo++$i; ?></td>
                        <td><a href="lista_infop.php?tipo_infop=proced&idproced=<?php echo $d_proced['idunidades']; ?>" title="Clique aqui para listar <?php echo SICOP_DET_ART_L; ?>s <?php echo SICOP_DET_DESC_L; ?>s" ><?php echo $d_proced['procedencia']; ?></a></td>
                        <td align="center"><?php echo $d_proced['total']; ?></td>
                        <td align="center"><?php echo porcent_ref_pop( $d_proced['total'], $popdacasa ); ?></td>
                    </tr>
            <?php } ?>
                <tr class="even_dk">
                    <td align="center" height="15">&nbsp;</td>
                    <td align="center"><b>TOTAL</b></td>
                    <td align="center"><?php echo $popdacasa; ?></td>
                    <td align="center"><?php echo porcent_ref_pop( $popdacasa, $popdacasa ); ?></td>
                </tr>
            </table>

<?php include 'footer.php'; ?>