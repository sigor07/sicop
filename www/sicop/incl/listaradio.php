<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_incl   = get_session( 'n_incl', 'int' );
$n_incl_n = 2;

$motivo_pag = 'LISTA DE RÁDIOS';

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

$q_radio = "SELECT
            `detentos_radio`.`idradio`,
            `detentos_radio`.`marca_radio`,
            `detentos_radio`.`cor_radio`,
            `detentos_radio`.`lacre_1`,
            `detentos_radio`.`lacre_2`,
            `detentos`.`iddetento`,
            `detentos`.`nome_det`,
            `detentos`.`matricula`,
            `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
            `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
            `unidades_out`.`idunidades` AS iddestino,
            `tb_cela_det`.`cela` AS cela_det,
            `tb_raio_det`.`raio` AS raio_det,
            `tb_cela_radio`.`cela` AS cela_radio,
            `tb_raio_radio`.`raio` AS raio_radio
          FROM
            `detentos_radio`
            LEFT JOIN `detentos` ON `detentos_radio`.`cod_detento` = `detentos`.`iddetento`
            LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
            LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
            LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
            LEFT JOIN `cela` `tb_cela_det` ON `detentos`.`cod_cela` = `tb_cela_det`.`idcela`
            LEFT JOIN `raio` `tb_raio_det` ON `tb_cela_det`.`cod_raio` = `tb_raio_det`.`idraio`
            LEFT JOIN `cela` `tb_cela_radio` ON `detentos_radio`.`cod_cela` = `tb_cela_radio`.`idcela`
            LEFT JOIN `raio` `tb_raio_radio` ON `tb_cela_radio`.`cod_raio` = `tb_raio_radio`.`idraio`
          ORDER BY
            `detentos_radio`.`cod_cela`";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_radio = $model->query( $q_radio );

// fechando a conexao
$model->closeConnection();

if( !$q_radio ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont = $q_radio->num_rows;

$querytime = $model->getQueryTime();

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Lista de rádios';

require 'cab.php';
$trail = new Breadcrumb();
$trail->add('Lista de rádios', $_SERVER['PHP_SELF'], 3);
$trail->output();
?>


            <p class="descript_page"> LISTA DE RÁDIOS</p>
            <?php if ($n_incl >= 3) {?>
            <p class="link_common"><a href="<?php echo SICOP_ABS_PATH; ?>buscadet.php?proced=cadrd">Cadastrar rádio</a></p>
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
                    <td width="100"><?php echo mb_strtoupper( SICOP_RAIO ) ?> / <?php echo mb_strtoupper( SICOP_CELA ) ?> DO RÁDIO</td>
                    <td width="90">LACRES</td>
                    <td width="219"><?php echo SICOP_DET_DESC_U; ?></td>
                    <td width="91">MATRICULA</td>
                    <td width="100"><?php echo mb_strtoupper( SICOP_RAIO ) ?> / <?php echo mb_strtoupper( SICOP_CELA ) ?> D<?php echo SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U; ?></td>
                </tr>
                <?php
                while ( $d_radio = $q_radio->fetch_assoc() ) {

                    $tipo_mov_in = $d_radio['tipo_mov_in'];
                    $tipo_mov_out = $d_radio['tipo_mov_out'];
                    $iddestino = $d_radio['iddestino'];

                    $det = manipula_sit_det_b( $tipo_mov_in, $tipo_mov_out, $iddestino );
                    ?>
                <tr height="20" class="even">
                    <td><a href="detalradio.php?idradio=<?php echo $d_radio['idradio']; ?>"><?php echo $d_radio['marca_radio'] ?></a></td>
                    <td><?php echo $d_radio['cor_radio'] ?></td>
                    <td align="center"><?php echo $d_radio['raio_radio'] ?> - <?php echo $d_radio['cela_radio'] ?></td>
                    <td align="center"><?php echo $d_radio['lacre_1'] ?> / <?php echo $d_radio['lacre_2'] ?></td>
                    <td><a href="<?php echo SICOP_ABS_PATH; ?>detento/detalhesdet.php?iddet=<?php echo $d_radio['iddetento']; ?>" title="Clique aqui para abrir a qualificativa d<?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?>"><?php echo $d_radio['nome_det'] ?></a></td>
                    <td align="center"><font color="<?php echo $det['corfontd']; ?>"><?php if ( !empty( $d_radio['matricula'] ) ) echo formata_num( $d_radio['matricula'] ) ?></font></td>
                    <td align="center"><font color="<?php echo $det['corfontd']; ?>"><?php echo $d_radio['raio_det'] ?> - <?php echo $d_radio['cela_det'] ?></font></td>
                </tr>
                <?php } ?>
            </table>

<?php include 'footer.php'; ?>