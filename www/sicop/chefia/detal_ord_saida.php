<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_chefia   = get_session( 'n_chefia', 'int' );
$n_chefia_n = 2;

$n_portaria   = get_session( 'n_portaria', 'int' );
$n_portaria_n = 2;

$n_seg   = get_session( 'n_seg', 'int' );
$n_seg_n = 2;

$n_peculio  = get_session( 'n_peculio', 'int' );
$n_peculi_n = 2;

if ( $n_chefia < $n_chefia_n and
     $n_portaria < $n_portaria_n and
     $n_seg < $n_seg_n and
     $n_peculio < $n_peculi_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = 'ORDENS DE SAÍDA - CHEFIA';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$imp_chefia   = get_session( 'imp_chefia', 'int' );

$id_ord_saida= get_get( 'id_ord_saida', 'int' );
if ( empty( $id_ord_saida ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso direto à página ( IDENTIFICADOR DA ORDEM DE SAÍDA EM BRANCO - DETALHES DA ORDEM DE SAÍDA ).';
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$q_ord_saida = "SELECT
                  `ordens_saida_det`.`cod_detento`,
                  `ordens_saida_det`.`id_ord_saida_det`,
                  `ordens_saida_locais`.`id_local_ord_saida`,
                  `detentos`.`nome_det`,
                  `detentos`.`matricula`,
                  `detentos`.`pai_det`,
                  `detentos`.`mae_det`,
                  `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
                  `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
                  `unidades_out`.`idunidades` AS iddestino,
                  `locais_apr`.`local_apr` AS destino,
                  `locais_apr`.`local_end`,
                  `cela`.`cela`,
                  `raio`.`raio`
                FROM
                  `ordens_saida`
                  INNER JOIN `ordens_saida_locais` ON `ordens_saida_locais`.`cod_ord_saida` = `ordens_saida`.`id_ord_saida`
                  LEFT JOIN `ordens_saida_det` ON `ordens_saida_det`.`cod_local_ord_saida` = `ordens_saida_locais`.`id_local_ord_saida`
                  LEFT JOIN `detentos` ON `ordens_saida_det`.`cod_detento` = `detentos`.`iddetento`
                  LEFT JOIN `locais_apr` ON `ordens_saida_locais`.`cod_local` = `locais_apr`.`idlocal`
                  LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                  LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                  LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
                  LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
                  LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
                WHERE
                  `ordens_saida`.`id_ord_saida` = $id_ord_saida
                ORDER BY
                  `locais_apr`.`local_apr`, `detentos`.`nome_det`";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_ord_saida = $model->query( $q_ord_saida );

// fechando a conexao
$model->closeConnection();

if ( !$q_ord_saida ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Falha na consulta ( DETALHES - ORDEM DE SAÍDA - CHEFIA ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$cont_q_ord_saida = $q_ord_saida->num_rows;

$q_data_ord_saida = "SELECT
                       DATE_FORMAT( `ordens_saida`.`ord_saida_data`, '%d/%m/%Y' ) AS ord_saida_data_f,
                       DATE_FORMAT( `ordens_saida`.`ord_saida_hora`, '%H:%i' ) AS `ord_saida_hora_f`,
                       `ordens_saida`.`finalidade`,
                       `ordens_saida`.`responsavel_escolta`,
                       `ordens_saida`.`retorno`
                     FROM
                       `ordens_saida`
                     WHERE
                       `ordens_saida`.`id_ord_saida` = $id_ord_saida";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_data_ord_saida = $model->query( $q_data_ord_saida );

// fechando a conexao
$model->closeConnection();

if ( !$q_data_ord_saida ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Falha na consulta ( DETALHES - DATA - ORDEM DE SAÍDA - CHEFIA ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$cont_q_data_ord_saida = $q_data_ord_saida->num_rows;

if( $cont_q_data_ord_saida < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'A consulta retornou 0 ocorrências ( DETALHES - DATA - ORDEM DE SAÍDA - CHEFIA ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$d_data_ord_saida = $q_data_ord_saida->fetch_assoc();

$data_ord_saida_f = $d_data_ord_saida['ord_saida_data_f'];
$hora_ord_saida_f = $d_data_ord_saida['ord_saida_hora_f'];
$finalidade       = $d_data_ord_saida['finalidade'];
$resp_escolta     = $d_data_ord_saida['responsavel_escolta'];
$retorno          = $d_data_ord_saida['retorno'];

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Detalhes da ordem de saída';

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) {
    $pag_atual .=  '?' . $qs;
}

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 4 );
$trail->output();
?>


            <p class="descript_page">ORDEM DE SAÍDA</p>

            <p class="sub_title_page" style="margin-top: 5px;">
                Data: <?php echo $data_ord_saida_f ?> <?php echo !empty ( $hora_ord_saida_f ) ? " às $hora_ord_saida_f" : ''; ?>
            </p>

            <p class="table_leg" style="margin-top: 5px;">
                Finalidade: <?php echo $finalidade ?>
            </p>

            <?php if ( !empty( $resp_escolta ) ) {?>
            <p class="table_leg" style="margin-top: 5px;">
                Responsável pela escolta: <?php echo $resp_escolta ?>
            </p>
            <?php } ?>

            <?php
            $aux = 'SEM';
            if ( !empty( $retorno ) ) $aux = 'COM';
            ?>
            <p class="table_leg" style="margin-top: 5px;">
                <?php echo $aux; ?> retorno
            </p>

            <?php if ( $imp_chefia >= 1  and !empty( $cont_q_ord_saida ) ) { ?>
            <p class="link_common">

                <a href='javascript:void(0)' onclick="submit_form_nwid( '../print/ordem_saida.php', 'id_ord_saida', <?php echo $id_ord_saida ?> )"  title="Imprimir a lista de detentos desta ordem de saída" >Imprimir</a>
                -
                <a href='javascript:void(0)' onclick="submit_form_id( '../export/exp_ord_saida.php', 'id_ord_saida', <?php echo $id_ord_saida ?> )"  title="Exportar para excel" >Exportar</a>

            </p>
            <?php } ?>

            <?php
            if ( empty( $cont_q_ord_saida ) or $cont_q_ord_saida < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não ha detentos cadastrados nesta ordem de saída.';
                include 'footer.php';
                exit;
            }
            ?>

            <table class="bonde_list">
                <tr class="cab">
                    <th class="num_od">N</th>
                    <th class="nome_det"><?php echo SICOP_DET_DESC_FU; ?></th>
                    <th class="matr_det">Matrícula</th>
                    <th class="raio_det"><?php echo SICOP_RAIO ?></th>
                    <th class="cela_det"><?php echo SICOP_CELA ?></th>
                </tr>

                    <?php
                        $i = 0;

                        $corlinha = "#F0F0F0";

                        $dest_ant = '';

                        while( $d_ord_saida = $q_ord_saida->fetch_assoc() ) {

                            $quebra = FALSE;

                            if ( $d_ord_saida['destino'] != $dest_ant ){
                                $quebra = TRUE;
                            }

                            $tipo_mov_in  = $d_ord_saida['tipo_mov_in'];
                            $tipo_mov_out = $d_ord_saida['tipo_mov_out'];
                            $iddestino    = $d_ord_saida['iddestino'];

                            $det = manipula_sit_det_b($tipo_mov_in, $tipo_mov_out, $iddestino);

                    ?>
                <?php if ( $quebra ){ ?>
                <tr class="even_gr">

                    <td colspan="5" class="dest_det"><?php echo $d_ord_saida['destino'] ?> <?php if ( $imp_chefia >= 10 ) { ?><font style="font-weight: normal">- <a href="add_ord_saida.php?id_ord_saida=<?php echo $id_ord_saida; ?>&idlocalos=<?php echo $d_ord_saida['id_local_ord_saida']; ?>" title="Imprimir o recibo de escolta deste local">Imprimir recibo</a></font><?php } ?></td>

                </tr>
                <?php } ?>
                <?php

                    // se nao tiver o $d_ord_saida['id_ord_saida_det'] é por que não possui detentos para o local
                    if ( empty( $d_ord_saida['id_ord_saida_det'] ) ){

                    ?>

                <tr class="even">

                    <td class="noh_det" colspan="5">Não há detentos.</td>

                </tr>
                <?php
                    // para iniciar uma nova iteração
                    continue;

                    } ?>
                <tr class="even">
                    <td class="num_od"><?php echo++$i ?></td>
                    <td class="nome_det" title="Pai: <?php echo $d_ord_saida['pai_det'];?>&#13;Mãe: <?php echo $d_ord_saida['mae_det'];?>&#13;Situação atual: <?php echo $det['sitat'];?>"><a href="<?php echo SICOP_ABS_PATH ?>detento/detalhesdet.php?iddet=<?php echo $d_ord_saida['cod_detento'] ?>"> <?php echo $d_ord_saida['nome_det'] ?></a></td>
                    <td class="matr_det <?php echo $det['css_class']; ?>"><?php if ( !empty( $d_ord_saida['matricula'] ) ) echo formata_num( $d_ord_saida['matricula'] ); ?></td>
                    <td class="raio_det <?php echo $det['css_class']; ?>"><?php echo $d_ord_saida['raio'] ?></td>
                    <td class="cela_det <?php echo $det['css_class']; ?>"><?php echo $d_ord_saida['cela'] ?></td>
                </tr>
                <?php
                        $dest_ant = $d_ord_saida['destino'];
                    } // /while( $d_ord_saida...
                ?>
            </table><!-- fim da table."bonde_list" -->

<?php include 'footer.php'; ?>