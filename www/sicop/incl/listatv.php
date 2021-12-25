<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_incl   = get_session( 'n_incl', 'int' );
$n_incl_n = 2;

$motivo_pag = 'LISTA DE TV';

if ( $n_incl < $n_incl_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$q_tv = "SELECT
            `detentos_tv`.`idtv`,
            `detentos_tv`.`marca_tv`,
            `detentos_tv`.`cor_tv`,
            `detentos_tv`.`lacre_1`,
            `detentos_tv`.`lacre_2`,
            `detentos`.`iddetento`,
            `detentos`.`nome_det`,
            `detentos`.`matricula`,
            `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
            `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
            `unidades_out`.`idunidades` AS iddestino,
            `tb_cela_det`.`cela` AS cela_det,
            `tb_raio_det`.`raio` AS raio_det,
            `tb_cela_tv`.`cela` AS cela_tv,
            `tb_raio_tv`.`raio` AS raio_tv
          FROM
            `detentos_tv`
            LEFT JOIN `detentos` ON `detentos_tv`.`cod_detento` = `detentos`.`iddetento`
            LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
            LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
            LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
            LEFT JOIN `cela` `tb_cela_det` ON `detentos`.`cod_cela` = `tb_cela_det`.`idcela`
            LEFT JOIN `raio` `tb_raio_det` ON `tb_cela_det`.`cod_raio` = `tb_raio_det`.`idraio`
            LEFT JOIN `cela` `tb_cela_tv` ON `detentos_tv`.`cod_cela` = `tb_cela_tv`.`idcela`
            LEFT JOIN `raio` `tb_raio_tv` ON `tb_cela_tv`.`cod_raio` = `tb_raio_tv`.`idraio`
          ORDER BY
            `detentos_tv`.`cod_cela`";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_tv = $model->query( $q_tv );

// fechando a conexao
$model->closeConnection();

if( !$q_tv ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont = $q_tv->num_rows;

$querytime = $model->getQueryTime();

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Lista de TVs';

require 'cab.php';
$trail = new Breadcrumb();
$trail->add('Lista de TVs', $_SERVER['PHP_SELF'], 3);
$trail->output();
?>

            <p class="descript_page"> LISTA DE TVs</p>
            <?php if ($n_incl >= 3) {?>
            <p class="link_common"><a href="<?php echo SICOP_ABS_PATH; ?>buscadet.php?proced=cadtv">Cadastrar TV</a></p>
            <?php } ?>

            <?php
            if ( empty( $cont ) or $cont < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não foi encontrado nenhuma ocorrência.</p>';
                include 'footer.php';
                exit;
            }
            ?>

            <p class="p_q_info">Essa consulta retornou <?php echo $cont ?> registros (<?php echo round($querytime, 2) ?> seg).</p>

            <table class="lista_busca">
                <tr align="center">
                    <td width="80" >MARCA</td>
                    <td width="80">COR </td>
                    <td width="100"><?php echo mb_strtoupper( SICOP_RAIO ) ?> / <?php echo mb_strtoupper( SICOP_CELA ) ?> DA TV</td>
                    <td width="90">LACRES</td>
                    <td width="219"><?php echo SICOP_DET_DESC_U; ?></td>
                    <td width="91">MATRICULA</td>
                    <td width="100"><?php echo mb_strtoupper( SICOP_RAIO ) ?> / <?php echo mb_strtoupper( SICOP_CELA ) ?> D<?php echo SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U; ?></td>
                </tr>
                <?php
                while ( $d_tv = $q_tv->fetch_assoc() ) {

                    $tipo_mov_in = $d_tv['tipo_mov_in'];
                    $tipo_mov_out = $d_tv['tipo_mov_out'];
                    $iddestino = $d_tv['iddestino'];

                    $det = manipula_sit_det_b( $tipo_mov_in, $tipo_mov_out, $iddestino );
                    ?>
                <tr height="20" class="even">
                    <td><a href="detaltv.php?idtv=<?php echo $d_tv['idtv']; ?>"><?php echo $d_tv['marca_tv'] ?></a></td>
                    <td><?php echo $d_tv['cor_tv'] ?></td>
                    <td align="center"><?php echo $d_tv['raio_tv'] ?> - <?php echo $d_tv['cela_tv'] ?></td>
                    <td align="center"><?php echo $d_tv['lacre_1'] ?> / <?php echo $d_tv['lacre_2'] ?></td>
                    <td><a href="<?php echo SICOP_ABS_PATH; ?>detento/detalhesdet.php?iddet=<?php echo $d_tv['iddetento']; ?>" title="Clique aqui para abrir a qualificativa d<?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?>"><?php echo $d_tv['nome_det'] ?></a></td>
                    <td align="center"><font color="<?php echo $det['corfontd']; ?>"><?php if ( !empty( $d_tv['matricula'] ) ) echo formata_num( $d_tv['matricula'] ) ?></font></td>
                    <td align="center"><font color="<?php echo $det['corfontd']; ?>"><?php echo $d_tv['raio_det'] ?> - <?php echo $d_tv['cela_det'] ?></font></td>
                </tr>
                <?php } ?>
            </table>

<?php include 'footer.php'; ?>