<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_pront   = get_session( 'n_pront', 'int' );
$n_pront_n = 2;

$motivo_pag = 'INFOPEN - ESCOLARIDADE';

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

$q_esc = "SELECT
            `tipoescolaridade`.`escolaridade`,
            `detentos`.`cod_instrucao`,
            COUNT(`detentos`.`cod_instrucao`) AS `total`
          FROM
            `detentos`
            LEFT JOIN `tipoescolaridade` ON `detentos`.`cod_instrucao` = `tipoescolaridade`.`idescolaridade`
            LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
            LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
          WHERE
            ( ( ISNULL( `mov_det_out`.`cod_tipo_mov` ) OR `mov_det_out`.`cod_tipo_mov` = 4 OR `mov_det_out`.`cod_tipo_mov` = 6 )
              AND
              ( `mov_det_in`.`cod_tipo_mov` = 1 OR `mov_det_in`.`cod_tipo_mov` = 3 ) )
          GROUP BY
            `detentos`.`cod_instrucao`
          HAVING
            NOT ISNULL( `detentos`.`cod_instrucao` )
          ORDER BY
            `tipoescolaridade`.`escolaridade` ASC";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_esc = $model->query( $q_esc );

// fechando a conexao
$model->closeConnection();

if( !$q_esc ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_esc = $q_esc->num_rows;

$q_t_esc = 'SELECT
              COUNT(*) AS total
            FROM
               `detentos`
               LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
               LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
             WHERE
               NOT ISNULL( `detentos`.`cod_instrucao` ) AND
               ( ( ISNULL( `mov_det_out`.`cod_tipo_mov` ) OR `mov_det_out`.`cod_tipo_mov` = 4 OR `mov_det_out`.`cod_tipo_mov` = 6 )
                 AND
                 ( `mov_det_in`.`cod_tipo_mov` = 1 OR `mov_det_in`.`cod_tipo_mov` = 3 ) )';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$d_t_esc = $model->fetchOne( $q_t_esc );

// fechando a conexao
$model->closeConnection();

if( $d_t_esc === false ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$q_esc_pend = 'SELECT
                 COUNT( `detentos`.`iddetento` ) AS total
               FROM
                 `detentos`
                 LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                 LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
               WHERE
                 ( ( ISNULL( `mov_det_out`.`cod_tipo_mov` ) OR `mov_det_out`.`cod_tipo_mov` = 4 OR `mov_det_out`.`cod_tipo_mov` = 6 )
                    AND
                    (`mov_det_in`.`cod_tipo_mov` = 1 OR `mov_det_in`.`cod_tipo_mov` = 3 ) )
                 AND ISNULL( `detentos`.`cod_instrucao` )';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$d_esc_pend = $model->fetchOne( $q_esc_pend );

// fechando a conexao
$model->closeConnection();

if( $d_esc_pend === false ) {

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

$desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_FU . 's pela escolaridade';

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

            <p class="descript_page">QUANTIDADE DE <?php echo SICOP_DET_DESC_U; ?>S DE ACORDO COM O ESCOLARIDADE</p>

            <?php

            if ( empty( $cont_esc ) or $cont_esc < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                    echo '<p class="p_q_no_result">Não foi encontrado nenhuma ocorrência.</p>';
                    include 'footer.php';
                    exit;
            }

            ?>

            <table class="lista_busca">
                <tr>
                    <th width="30" align="center">N</th>
                    <th width="210" align="center">ESCOLARIDADE</th>
                    <th width="130">QUANTIDADE</th>
                    <th width="40">%</th>
                </tr>
                <?php
                $i = 0;

                while ( $d_esc = $q_esc->fetch_assoc() ) {
                    ?>
                    <tr class="even">
                        <td align="center"><?php echo++$i; ?></td>
                        <td><a href="lista_infop.php?tipo_infop=esc&amp;idesc=<?php echo $d_esc['cod_instrucao']; ?>" title="Clique aqui para listar <?php echo SICOP_DET_ART_L; ?>s <?php echo SICOP_DET_DESC_L; ?>s" ><?php echo $d_esc['escolaridade']; ?></a></td>
                        <td align="center"><?php echo $d_esc['total']; ?></td>
                        <td align="center"><?php echo porcent_ref_pop( $d_esc['total'], $popdacasa ); ?></td>
                    </tr>
                <?php } ?>
                <tr class="even_dk">
                    <td align="center">&nbsp;</td>
                    <td align="center"><b>TOTAL</b></td>
                    <td align="center"><?php echo $d_t_esc; ?></td>
                    <td align="center">&nbsp;</td>
                </tr>
                <tr class="even_dk">
                    <td align="center">&nbsp;</td>
                    <td><?php if ( $d_esc_pend > 0 ) { ?><a href="lista_infop.php?tipo_infop=pesc" title="Clique aqui para listar <?php echo SICOP_DET_ART_L; ?>s <?php echo SICOP_DET_DESC_L; ?>s" ><?php } ?>Pendencias de escolaridade<?php if ( $d_esc_pend > 0 ) { ?></a><?php } ?></td>
                    <td align="center"><?php echo $d_esc_pend; ?></td>
                    <td align="center"><?php echo porcent_ref_pop( $d_esc_pend, $popdacasa ); ?></td>
                </tr>
            </table>

<?php include 'footer.php'; ?>