<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_bonde   = get_session( 'n_bonde', 'int' );
$n_bonde_n = 2;

$motivo_pag = 'DETALHES DO BONDE';

if ( $n_bonde < $n_bonde_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$n_bonde_fut = get_session( 'n_bonde_fut', 'int' );
$imp_bonde   = get_session( 'imp_bonde', 'int' );
$n_cadastro  = get_session( 'n_cadastro', 'int' );
$n_sind      = get_session( 'n_sind', 'int' );
$n_pront     = get_session( 'n_pront', 'int' );
$n_peculio   = get_session( 'n_peculio', 'int' );

$idbonde = get_get( 'idbonde', 'int' );

if ( empty( $idbonde ) ){

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$q_bonde = "SELECT
              `bonde_det`.`cod_detento`,
              `bonde_det`.`idbd`,
              `bonde_locais`.`idblocal`,
              `detentos`.`nome_det`,
              `detentos`.`matricula`,
              `detentos`.`pai_det`,
              `detentos`.`mae_det`,
              `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
              `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
              `unidades`.`unidades` AS destino,
              `unidades_out`.`idunidades` AS iddestino,
              `cela`.`cela`,
              `raio`.`raio`
            FROM
              `bonde`
              INNER JOIN `bonde_locais` ON `bonde_locais`.`cod_bonde` = `bonde`.`idbonde`
              LEFT JOIN `bonde_det` ON `bonde_det`.`cod_bonde_local` = `bonde_locais`.`idblocal`
              LEFT JOIN `detentos` ON `bonde_det`.`cod_detento` = `detentos`.`iddetento`
              LEFT JOIN `unidades` ON `bonde_locais`.`cod_unidade` = `unidades`.`idunidades`
              LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
              LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
              LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
              LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
              LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
            WHERE
              `bonde`.`idbonde` = $idbonde
            ORDER BY
              `unidades`.`unidades`, `detentos`.`nome_det`";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_bonde = $model->query( $q_bonde );

// fechando a conexao
$model->closeConnection();

if( !$q_bonde ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_q_bonde = $q_bonde->num_rows;

$q_data_bonde = "SELECT
                   `bonde_data`,
                   DATE_FORMAT( `bonde_data`, '%d/%m/%Y' ) AS bonde_data_f
                 FROM
                   `bonde`
                 WHERE
                   `idbonde` = $idbonde";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_data_bonde = $model->query( $q_data_bonde );

// fechando a conexao
$model->closeConnection();

if( !$q_data_bonde ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_q_data_bonde = $q_data_bonde->num_rows;

if ( $cont_q_data_bonde < 1 ) {
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ocorrências ( DATA DO BONDE ).\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$d_data_bonde = $q_data_bonde->fetch_assoc();

if ( $n_bonde_fut < 1 ) {

    $data_bonde = $d_data_bonde['bonde_data'];

    if ( empty( $data_bonde ) ) {
        $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página de detalhes do bonde, sem permiçõs ( BONDE COM DATA FUTURA ).\n\nPágina: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!!!', 1 );
        exit;
    }

    $data_bonde = strtotime( $data_bonde );
    $data_limit = strtotime( '+1 day' );

    if ( $data_bonde > $data_limit ) {
        $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página de detalhes do bonde, sem permiçõs ( BONDE COM DATA FUTURA ).\n\nPágina: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!!!', 1 );
        exit;
    }

}

$data_bonde_f = !empty ( $d_data_bonde['bonde_data_f'] ) ? $d_data_bonde['bonde_data_f'] : 'N/D' ;

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Detalhes do bonde';

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


            <p class="descript_page">LISTA DO BONDE</p>

            <p class="sub_title_page" style="margin-top: 5px;">Data: <?php echo $data_bonde_f ?></p>

            <?php if ( $n_bonde >= 3 || $imp_bonde >= 1 ) { ?>
            <p class="link_common">
                <?php if ( $n_bonde >= 3 ) { ?>
                <a href="edit_bonde.php?idbonde=<?php echo $idbonde; ?>">Alterar</a> |
                <a href="add_bonde.php?idbonde=<?php echo $idbonde; ?>">Adicionar local</a>
                <?php } ?>
                <?php if ( $imp_bonde >= 1 && $cont_q_bonde > 0 ) {  ?>
                <?php if ( $n_bonde >= 3 ) { ?> | <?php }; ?><a href='javascript:void(0)' onclick="javascript: ow('../print/lista_bonde.php?idbonde=<?php echo $idbonde; ?>', '600', '600'); return false"  title="Imprimir a lista de detentos deste bonde" >Imprimir</a>
                <?php }; ?>
            </p>
            <?php } ?>

            <?php
            if ( empty( $cont_q_bonde ) or $cont_q_bonde < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não ha detentos cadastrados neste bonde.</p>';
                include 'footer.php';
                exit;
            }
            ?>

            <p class="link_common">
                <?php if ( $n_cadastro >= 2 ) { ?>
                <a href="check_bonde.php?idbonde=<?php echo $idbonde; ?>&tipo_ck=aud">Verificar audiências</a>
                <?php } ?>
                <?php if ( $n_sind >= 2 ) { ?>
                 | <a href="check_bonde.php?idbonde=<?php echo $idbonde; ?>&tipo_ck=sind">Verificar sindicâncias</a>
                <?php } ?>
                <?php if ( $n_pront >= 2 or $n_cadastro >= 2 ) { ?>
                 | <a href="check_bonde.php?idbonde=<?php echo $idbonde; ?>&tipo_ck=cond">Verificar condenação</a>
                <?php } ?>
                <?php if ( $n_peculio >= 2 ) { ?>
                 | <a href="check_bonde.php?idbonde=<?php echo $idbonde; ?>&tipo_ck=pec">Verificar pecúlio</a>
                <?php } ?>
            </p>

            <table class="bonde_list">
                <tr class="cab">
                    <th class="num_od">N</th>
                    <th class="nome_det"><?php echo SICOP_DET_DESC_FU; ?></th>
                    <th class="matr_det">Matrícula</th>
                    <th class="raio_det"><?php echo SICOP_RAIO ?></th>
                    <th class="cela_det"><?php echo SICOP_CELA ?></th>
                    <?php if ( $n_bonde >= 3 ) { ?>
                    <th class="tb_bt">&nbsp;</th>
                    <?php } ?>
                </tr>

                    <?php
                        $i = 0;

                        $corlinha = "#F0F0F0";

                        $dest_ant = '';

                        while( $d_bonde = $q_bonde->fetch_assoc() ) {

                            $quebra = FALSE;

                            if ( $d_bonde['destino'] != $dest_ant ){
                                $quebra = TRUE;
                            }

                            $tipo_mov_in  = $d_bonde['tipo_mov_in'];
                            $tipo_mov_out = $d_bonde['tipo_mov_out'];
                            $iddestino    = $d_bonde['iddestino'];

                            $det = manipula_sit_det_b($tipo_mov_in, $tipo_mov_out, $iddestino);

                    ?>
                <?php if ( $quebra ){ ?>
                <tr class="even_gr">

                    <td colspan="5" class="dest_det">PARA O(A) <?php echo $d_bonde['destino'] ?> <?php if ( $n_bonde >= 3 ) { ?><font style="font-weight: normal">- <a href="add_bonde.php?idbonde=<?php echo $idbonde; ?>&idb_local=<?php echo  $d_bonde['idblocal']; ?>">Adicionar detento</a></font><?php } ?></td>
                    <?php if ( $n_bonde >= 3 ) { ?>
                    <td class="tb_bt"><a href='javascript:void(0)' onclick='drop_local_bonde(<?php echo $d_bonde['idblocal']; ?>)' title="Excluir este destino"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="Excluir este destino" class="icon_button" /></a> </td>
                    <?php } ?>

                </tr>
                <?php } ?>
                <?php

                    // se nao tiver o $d_bonde['idbd'] é por que não possui detentos para o local
                    if ( empty( $d_bonde['idbd'] ) ){

                        $n_colspan = 5;

                        if ( $n_bonde >= 3 ) {
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
                    <td class="nome_det" title="Pai: <?php echo $d_bonde['pai_det'];?>&#13;Mãe: <?php echo $d_bonde['mae_det'];?>&#13;Situação atual: <?php echo $det['sitat'];?>"><a href="<?php echo SICOP_ABS_PATH ?>detento/detalhesdet.php?iddet=<?php echo $d_bonde['cod_detento'] ?>"> <?php echo $d_bonde['nome_det'] ?></a></td>
                    <td class="matr_det"><font color="<?php echo $det['corfontd']; ?>"><?php if ( !empty( $d_bonde['matricula'] ) ) echo formata_num( $d_bonde['matricula'] ); ?></font></td>
                    <td class="raio_det"><font color="<?php echo $det['corfontd']; ?>"><?php echo $d_bonde['raio'] ?></font></td>
                    <td class="cela_det"><font color="<?php echo $det['corfontd']; ?>"><?php echo $d_bonde['cela'] ?></font></td>
                    <?php if ( $n_bonde >= 3 ) { ?>
                    <td class="tb_bt"><a href='javascript:void(0)' onclick='drop_det_bonde(<?php echo $d_bonde['idbd']; ?>)' title="Excluir <?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L ;?> do bonde"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="Excluir <?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L ;?> do bonde" class="icon_button" /></a> </td>
                    <?php } ?>
                </tr>
                <?php
                        $dest_ant = $d_bonde['destino'];
                    }
                ?>
            </table><!-- fim da table."bonde_list" -->

            <p class="link_common" style="margin-top: 5px;">
                <?php if ( $n_cadastro >= 2 ) { ?>
                <a href="check_bonde.php?idbonde=<?php echo $idbonde; ?>&tipo_ck=aud">Verificar audiências</a>
                <?php } ?>
                <?php if ( $n_sind >= 2 ) { ?>
                 | <a href="check_bonde.php?idbonde=<?php echo $idbonde; ?>&tipo_ck=sind">Verificar sindicâncias</a>
                <?php } ?>
                <?php if ( $n_pront >= 2 or $n_cadastro >= 2 ) { ?>
                 | <a href="check_bonde.php?idbonde=<?php echo $idbonde; ?>&tipo_ck=cond">Verificar condenação</a>
                <?php } ?>
                <?php if ( $n_peculio >= 2 ) { ?>
                 | <a href="check_bonde.php?idbonde=<?php echo $idbonde; ?>&tipo_ck=pec">Verificar pecúlio</a>
                <?php } ?>
            </p>

<?php include 'footer.php'; ?>