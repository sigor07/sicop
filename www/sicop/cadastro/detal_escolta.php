<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_cadastro = get_session( 'n_cadastro', 'int' );
$n_cad_n    = 2;

$motivo_pag = 'DETALHES DO PEDIDO DE ESCOLTA';

if ( $n_cadastro < $n_cad_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$imp_cadastro = get_session( 'imp_cadastro', 'int');

$idescolta = get_get( 'idescolta', 'int' );
if ( empty( $idescolta ) ){

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( IDENTIFICADOR DO PEDIDO DE ESCOLTA EM BRANCO - $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$q_esc = "SELECT
            `ordens_escolta_det`.`cod_detento`,
            `ordens_escolta_det`.`id_escolta_det`,
            `ordens_escolta_det`.`hora`,
            `ordens_escolta_locais`.`id_local_escolta`,
            `ordens_escolta_tipo`.`tipo`,
            `detentos`.`nome_det`,
            `detentos`.`matricula`,
            `detentos`.`pai_det`,
            `detentos`.`mae_det`,
            `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
            `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
            `locais_apr`.`local_apr` AS destino,
            `locais_apr`.`local_end`,
            `unidades_out`.`idunidades` AS iddestino,
            `cela`.`cela`,
            `raio`.`raio`
          FROM
            `ordens_escolta`
            INNER JOIN `ordens_escolta_locais` ON `ordens_escolta_locais`.`cod_escolta` = `ordens_escolta`.`idescolta`
            LEFT JOIN `ordens_escolta_det` ON `ordens_escolta_det`.`cod_local_escolta` = `ordens_escolta_locais`.`id_local_escolta`
            LEFT JOIN `ordens_escolta_tipo` ON `ordens_escolta_tipo`.`id_tipo` = `ordens_escolta_det`.`cod_tipo`
            LEFT JOIN `detentos` ON `ordens_escolta_det`.`cod_detento` = `detentos`.`iddetento`
            LEFT JOIN `locais_apr` ON `ordens_escolta_locais`.`cod_local` = `locais_apr`.`idlocal`
            LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
            LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
            LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
            LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
            LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
          WHERE
            `ordens_escolta`.`idescolta` = $idescolta
          ORDER BY
            `locais_apr`.`local_apr`, `detentos`.`nome_det`";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_esc = $model->query( $q_esc );

// fechando a conexao
$model->closeConnection();

if ( !$q_esc ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Falha na consulta ( DETALHES - PEDIDO DE ESCOLTA ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$cont_q_esc = $q_esc->num_rows;

$q_data_esc = "SELECT
                 DATE_FORMAT( `ordens_escolta`.`escolta_data`, '%d/%m/%Y' ) AS escolta_data_f,
                 DATE_FORMAT( `ordens_escolta`.`escolta_hora`, '%H:%i' ) AS `escolta_hora_f`
               FROM
                 `ordens_escolta`
               WHERE
                 `ordens_escolta`.`idescolta` = $idescolta";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_data_esc = $model->query( $q_data_esc );

// fechando a conexao
$model->closeConnection();

if ( !$q_data_esc ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Falha na consulta ( DETALHES - DATA - PEDIDO DE ESCOLTA ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$cont_q_data_esc = $q_data_esc->num_rows;

if( $cont_q_data_esc < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'A consulta retornou 0 ocorrências ( DETALHES - DATA - PEDIDO DE ESCOLTA ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$d_data_esc = $q_data_esc->fetch_assoc();

$data_esc_f = $d_data_esc['escolta_data_f'];
$hora_esc_f = $d_data_esc['escolta_hora_f'];

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Detalhes do pedido de escolta';

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 4 );
$trail->output();
?>


            <p class="descript_page">PEDIDO DE ESCOLTA</p>

            <p class="sub_title_page" style="margin-top: 5px;">Data: <?php echo $data_esc_f ?></p>

            <?php if ( $n_cadastro >= 3 or $imp_cadastro >= 1 ) { ?>
            <p class="link_common">
                <?php if ( $n_cadastro >= 3 ) { ?>
                <a href="edit_escolta.php?idescolta=<?php echo $idescolta; ?>">Alterar</a> |
                <a href="add_escolta.php?idescolta=<?php echo $idescolta; ?>">Adicionar local</a>
                <?php } ?>
                <?php if ( $imp_cadastro >= 1 and !empty( $cont_q_esc ) ) { ?>
                <?php if ( $n_cadastro >= 3 ) { ?> | <?php } ?>
                <a href='javascript:void(0)' onclick="submit_form_nwid( '../print/ordem_escolta.php', 'idescolta', <?php echo $idescolta ?> )"  title="Imprimir este pedido de escolta" >Imprimir</a>
                |
                <a href='javascript:void(0)' onclick="submit_form_id( '../export/exp_ord_saida.php', 'idescolta', <?php echo $idescolta ?> )"  title="Exportar para excel" >Exportar</a>
                <?php } ?>
            </p>
            <?php } ?>

            <?php
            if ( empty( $cont_q_esc ) or $cont_q_esc < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não ha detentos cadastrados neste pedido de escolta.';
                include 'footer.php';
                exit;
            }
            ?>

            <p class="link_common">
                <?php if ( $n_cadastro >= 3 ) { ?>
                <a href='javascript:void(0)' onclick="submit_form_id( '../send/sendpesc.php', 'idescolta', <?php echo $idescolta ?> , 'get_ord_saida', 1)"  title="Gerar uma ordem de saída a partir deste pedido de escolta" >Gerar ordem de saida</a>
                <?php } ?>
            </p>

            <table class="bonde_list">
                <tr class="cab">
                    <th class="num_od">N</th>
                    <th class="nome_det"><?php echo SICOP_DET_DESC_FU; ?></th>
                    <th class="matr_det">Matrícula</th>
                    <th class="raio_det"><?php echo SICOP_RAIO ?></th>
                    <th class="cela_det"><?php echo SICOP_CELA ?></th>
                    <th class="motivo_bonde">Finalidade</th>
                    <?php if ( $n_cadastro >= 3 ) { ?>
                    <th class="tb_bt">&nbsp;</th>
                    <?php } ?>
                </tr>

                    <?php
                        $i = 0;

                        $corlinha = "#F0F0F0";

                        $dest_ant = '';

                        while( $d_esc = $q_esc->fetch_assoc() ) {

                            $quebra = FALSE;

                            if ( $d_esc['destino'] != $dest_ant ){
                                $quebra = TRUE;
                            }

                            $tipo_mov_in  = $d_esc['tipo_mov_in'];
                            $tipo_mov_out = $d_esc['tipo_mov_out'];
                            $iddestino    = $d_esc['iddestino'];

                            $det = manipula_sit_det_b($tipo_mov_in, $tipo_mov_out, $iddestino);

                    ?>
                <?php if ( $quebra ){ ?>
                <tr class="even_gr">

                    <td colspan="6" class="dest_det"><?php echo $d_esc['destino'] ?> <?php if ( $n_cadastro >= 3 ) { ?><font style="font-weight: normal">- <a href="add_escolta.php?idescolta=<?php echo $idescolta; ?>&idlocalesc=<?php echo $d_esc['id_local_escolta']; ?>">Adicionar detento</a></font><?php } ?></td>
                    <?php if ( $n_cadastro >= 3 ) { ?>
                    <td class="tb_bt"><a href='javascript:void(0)' onclick='drop_escolta(<?php echo $d_esc['id_local_escolta']; ?>, 1)' title="Excluir este destino"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="Excluir este destino" class="icon_button" /></a> </td>
                    <?php } ?>

                </tr>
                <?php } ?>
                <?php

                    // se nao tiver o $d_esc['id_escolta_det'] é por que não possui detentos para o local
                    if ( empty( $d_esc['id_escolta_det'] ) ){

                        $n_colspan = 5;

                        if ( $n_cadastro >= 3 ) {
                            $n_colspan = 6;
                        }

                    ?>

                <tr class="even">

                    <td class="noh_det" colspan="<?php echo $n_colspan; ?>">Não há detentos.</td>

                </tr>
                <?php
                    // para iniciar uma nova iteração
                    continue;

                    } ?>
                <tr class="even">
                    <td class="num_od"><?php echo++$i ?></td>
                    <td class="nome_det" title="Pai: <?php echo $d_esc['pai_det'];?>&#13;Mãe: <?php echo $d_esc['mae_det'];?>&#13;Situação atual: <?php echo $det['sitat'];?>"><a href="<?php echo SICOP_ABS_PATH ?>detento/detalhesdet.php?iddet=<?php echo $d_esc['cod_detento'] ?>"> <?php echo $d_esc['nome_det'] ?></a></td>
                    <td class="matr_det <?php echo $det['css_class']; ?>"><?php if ( !empty( $d_esc['matricula'] ) ) echo formata_num( $d_esc['matricula'] ); ?></td>
                    <td class="raio_det <?php echo $det['css_class']; ?>"><?php echo $d_esc['raio'] ?></td>
                    <td class="cela_det <?php echo $det['css_class']; ?>"><?php echo $d_esc['cela'] ?></td>
                    <td class="motivo_bonde"><?php if ( $n_cadastro >= 3 ) { ?><a href='javascript:void(0)' onClick="javascript: ow('edit_tipo_esc.php?idescdet=<?php echo $d_esc['id_escolta_det']; ?>', '800', '360', 'new_win' ); return false"><?php } ?><?php echo !empty ( $d_esc['tipo'] ) ? $d_esc['tipo'] : 'N/D' ?><?php echo empty( $d_esc['hora'] ) ? '*' : ''; ?><?php if ( $n_cadastro >= 3 ) { ?></a><?php } ?></td>
                    <?php if ( $n_cadastro >= 3 ) { ?>
                    <td class="tb_bt"><a href='javascript:void(0)' onclick='drop_escolta(<?php echo $d_esc['id_escolta_det']; ?>, 2)' title="Excluir <?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L ;?> do pedido de escolta"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="Excluir <?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L ;?> do pedido de escolta" class="icon_button" /></a> </td>
                    <?php } ?>
                </tr>
                <?php
                        $dest_ant = $d_esc['destino'];
                    }
                ?>
            </table><!-- fim da table."bonde_list" -->

<?php include 'footer.php'; ?>