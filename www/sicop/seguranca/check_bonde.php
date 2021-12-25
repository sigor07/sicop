<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_bonde   = get_session( 'n_bonde', 'int' );
$n_bonde_n = 2;

$motivo_pag = 'LISTA DO BONDE - CHECAR SITUAÇÃO';

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

$idbonde = get_get( 'idbonde', 'busca' );

if ( empty( $idbonde ) ) {
    echo msg_js( '', 1 );
    exit;
}

$tipo_ck = get_get( 'tipo_ck', 'busca' );

if ( empty( $tipo_ck ) ) {
    echo msg_js( '', 1 );
    exit;
}

$motivo = '';
$desc_pag = '';
$sit_pag = '';

switch( $tipo_ck ) {
    default:
    case '':
        $tipo_ck = '';
        break;
    case 'aud':

        $n_cadastro = get_session( 'n_cadastro', 'int' );
        $n_cad_n    = 2;

        if ( $n_cadastro < $n_cad_n ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = $motivo_pag - 'AUDÊNCIAS';
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }

        $motivo = SICOP_DET_DESC_U . 'S QUE TERÃO AUDIÊNCIAS';
        $desc_pag = SICOP_DET_DESC_FU . 's que terão audiências';
        $sit_pag = 'VERIFICAÇÃO DE BONDE - CADASTRO';
        break;
    case 'sind':

        $n_sind   = get_session( 'n_sind', 'int' );
        $n_sind_n = 2;

        if ($n_sind < $n_sind_n) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = $motivo_pag - 'SINDICÂNCIAS';
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }

        $motivo = SICOP_DET_DESC_U . 'S QUE POSSUEM SINDICÂNCIAS';
        $desc_pag = SICOP_DET_DESC_FU . 's que possuem sindicâncias';
        $sit_pag = 'VERIFICAÇÃO DE BONDE - SINDICÂNCIAS';
        break;
    case 'cond':

        $n_cadastro = get_session( 'n_cadastro', 'int' );
        $n_cad_n    = 2;
        $n_pront    = get_session( 'n_pront', 'int' );
        $n_pront_n  = 2;

        if ( $n_cadastro < $n_cad_n and $n_pront < $n_pront_n ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = $motivo_pag - 'CONDENAÇÃO';
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }

        $motivo = SICOP_DET_DESC_U . 'S COM CONDENAÇÃO';
        $desc_pag = SICOP_DET_DESC_FU . 's com condenação';
        $sit_pag = 'VERIFICAÇÃO DE BONDE - CONDENAÇÃO';
        break;
    case 'pec':

        $n_peculio   = get_session( 'n_peculio', 'int' );
        $n_peculio_n = 2;

        if ( $n_peculio < $n_peculio_n ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = $motivo_pag - 'PECÚLIO';
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }

        $motivo = SICOP_DET_DESC_U . 'S COM PERTENCES';
        $desc_pag = SICOP_DET_DESC_FU . 's com pertences';
        $sit_pag = 'VERIFICAÇÃO DE BONDE - PECÚLIO';
        break;

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
              INNER JOIN `bonde_det` ON `bonde_det`.`cod_bonde_local` = `bonde_locais`.`idblocal`
              INNER JOIN `detentos` ON `bonde_det`.`cod_detento` = `detentos`.`iddetento`
              INNER JOIN `unidades` ON `bonde_locais`.`cod_unidade` = `unidades`.`idunidades`
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

if( $cont_q_bonde < 1 ) {
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ocorrências ( $sit_pag ).\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$q_data_bonde = "SELECT
                   DATE_FORMAT( `bonde_data`, '%d/%m/%Y' ) AS bonde_data_f,
                   `bonde_data`
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

if( $cont_q_data_bonde < 1 ) {

    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ocorrências ( DATA DO BONDE ).\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$d_data_bonde = $q_data_bonde->fetch_assoc();

$bonde_data = $d_data_bonde['bonde_data'];

if ( $n_bonde_fut < 1 ) {

    if ( empty( $bonde_data ) ) {
        $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página de detalhes do bonde, sem permiçõs ( BONDE COM DATA FUTURA ).\n\nPágina: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!!!', 1 );
        exit;
    }

    $bonde_data_ts = strtotime( $bonde_data );
    $data_limit    = strtotime('+1 day');

    if ( $bonde_data_ts > $data_limit ) {
        $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página de detalhes do bonde, sem permiçõs ( BONDE COM DATA FUTURA ).\n\nPágina: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!!!', 1 );
        exit;
    }

}

$data_bonde_f = !empty ( $d_data_bonde['bonde_data_f'] ) ? $d_data_bonde['bonde_data_f'] : 'N/D' ;

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) {
    $pag_atual .= '?' . $qs;
}

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 5 );
$trail->output();
?>



            <p class="descript_page">LISTA DO BONDE</p>

            <p class="sub_title_page" style="margin-top: 0px;"><?php echo $motivo ?></p>

            <p class="sub_title_page" style="margin-top: 0px;">Data: <?php echo $data_bonde_f ?></p>

            <?php
            if ( empty( $cont_q_bonde ) or $cont_q_bonde < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não ha ' . SICOP_DET_DESC_L . 's cadastrados neste bonde.</p>';
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

                    // instanciando o model
                    $model = SicopModel::getInstance();

                    while( $d_bonde = $q_bonde->fetch_assoc() ) {

                        $iddet = $d_bonde['cod_detento'];

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

                    <td colspan="5" class="dest_det">PARA O(A) <?php echo $d_bonde['destino'] ?></td>

                </tr>
                <?php } ?>
                <tr class="even">
                    <td class="num_od"><?php echo++$i ?></td>
                    <td class="nome_det" title="Pai: <?php echo $d_bonde['pai_det'];?>&#13;Mãe: <?php echo $d_bonde['mae_det'];?>&#13;Situação atual: <?php echo $det['sitat'];?>"><a href="<?php echo SICOP_ABS_PATH ?>detento/detalhesdet.php?iddet=<?php echo $d_bonde['cod_detento'] ?>"> <?php echo $d_bonde['nome_det'] ?></a></td>
                    <td class="matr_det <?php echo $det['css_class']; ?>"><?php if ( !empty( $d_bonde['matricula'] ) ) echo formata_num( $d_bonde['matricula'] ); ?></td>
                    <td class="raio_det <?php echo $det['css_class']; ?>"><?php echo $d_bonde['raio'] ?></td>
                    <td class="cela_det <?php echo $det['css_class']; ?>"><?php echo $d_bonde['cela'] ?></td>
                </tr>
                <?php

                        switch( $tipo_ck ) {
                            case 'aud':
                                $queryaud = "SELECT
                                                `idaudiencia`,
                                                `cod_detento`,
                                                `data_aud`,
                                                `hora_aud`,
                                                `local_aud`,
                                                `cidade_aud`,
                                                `tipo_aud`,
                                                `num_processo`,
                                                `sit_aud`,
                                                DATE_FORMAT(`data_aud`, '%d/%m/%Y') AS `data_aud_f`,
                                                DATE_FORMAT(`hora_aud`, '%H:%i') AS `hora_aud_f`
                                              FROM
                                                `audiencias`
                                              WHERE
                                                `cod_detento` = $iddet
                                                AND
                                                `data_aud` >= '$bonde_data'
                                              ORDER BY
                                                `data_aud` DESC, `hora_aud`";

                                // executando a query
                                $queryaud = $model->query( $queryaud );

                                $conta = $queryaud->num_rows;
                                if( !$queryaud or $conta < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                                ?>
                    <tr class="no_ocurr">
                        <td colspan="5"><p class="p_q_no_result">Nenhuma audiência agendada.</p></td>
                    </tr>
                           <?php } else { ?>
                    <tr class="ocurr" >
                        <td colspan="5">
                            <table class="bonde_check">
                                <tr>
                                    <th width="200" height="20">LOCAL DE APRESENTAÇÃO</th>
                                    <th width="180" align="center" >CIDADE</th>
                                    <th width="130" align="center" >DATA/HORA</th>
                                </tr>
                                        <?php
                                        while( $dadosa = $queryaud->fetch_assoc() ) {

                                            $aud = trata_sit_aud($dadosa['sit_aud']);

                                            ?>
                                <tr class="even" title="Situação da audiência: <?php echo $aud['sitaud']; ?>">
                                    <td width="200" height="20"><a href="<?php echo SICOP_ABS_PATH ?>cadastro/detalaud.php?idaud=<?php echo $dadosa['idaudiencia'] ?>" ><?php echo $dadosa['local_aud'] ?></a></td>
                                    <td width="180" align="center"><font color="<?php echo $aud['corfontaud']; ?>"><?php echo $dadosa['cidade_aud'] ?></font></td>
                                    <td width="130" align="center"><font color="<?php echo $aud['corfontaud']; ?>"><?php echo $dadosa['data_aud_f'] ?> às <?php echo $dadosa['hora_aud_f'] ?></font></td>
                                </tr>
                                        <?php } // fim do while ?>
                            </table>
                        </td>
                    </tr>
                          <?php } // fim do if que conta o número de ocorrencias ?>
                    <tr>
                        <td colspan="5">&nbsp;</td>
                    </tr>
                         <?php
                                break;
                            case 'sind':

                                $querysind = "SELECT
                                                `sindicancias`.`idsind`,
                                                `sindicancias`.`cod_detento`,
                                                `sindicancias`.`num_pda`,
                                                `sindicancias`.`ano_pda`,
                                                `sindicancias`.`local_pda`,
                                                DATE_FORMAT(`sindicancias`.`data_ocorrencia`, '%d/%m/%Y') AS data_ocorrencia,
                                                `sindicancias`.`sit_pda`,
                                                `sindicancias`.`data_reabilit`,
                                                DATE_FORMAT(`sindicancias`.`data_reabilit`, '%d/%m/%Y') AS data_reab_f,
                                                `tipositdet`.`situacaodet`
                                              FROM
                                                `sindicancias`
                                                LEFT JOIN `tipositdet` ON `sindicancias`.`cod_sit_detento` = `tipositdet`.`idsitdet`
                                              WHERE
                                                `sindicancias`.`cod_detento` = $iddet
                                              ORDER BY
                                                `sindicancias`.`ano_pda`, `sindicancias`.`num_pda`";

                                            // executando a query
                                            $querysind = $model->query( $querysind );

                                            $conts = $querysind->num_rows;
                                            if( !$querysind or $conts < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                                                ?>
                    <tr class="no_ocurr" >
                        <td colspan="5"><p class="p_q_no_result">Nada consta.</p></td>
                    </tr>
                                      <?php } else { ?>
                    <tr class="ocurr" >
                        <td colspan="5">
                            <table align="center" class="space" >
                                <tr >
                                    <td height="20">NÚMERO DO PDA</td>
                                    <td align="center" >SITUAÇÃO DO PDA</td>
                                    <td align="center" >SITUAÇÃO DO <?php echo SICOP_DET_DESC_U; ?></td>
                                    <td align="center" >REABILIATAÇÃO</td>
                                </tr>

                                        <?php
                                        while( $dadoss = $querysind->fetch_assoc() ) {

                                            $numpda = (empty( $dadoss['local_pda'] )) ? $dadoss['num_pda'] . '/' . $dadoss['ano_pda'] : $dadoss['num_pda'] . '/' . $dadoss['ano_pda'] . '-' . $dadoss['local_pda'];

                                            $corfonts = muda_cor_pda( $dadoss['data_reabilit'], $dadoss['sit_pda'] );

                                            ?>

                                <tr class="even">
                                    <td width="145" height="20"><a href="<?php echo SICOP_ABS_PATH ?>sind/detalpda.php?idsind=<?php echo $dadoss['idsind'] ?>"><?php echo $numpda ?></a></td>
                                    <td width="110" align="center"><font color="<?php echo $corfonts;?>"><?php echo trata_sit_pda($dadoss['sit_pda']) ?></font></td>
                                    <td width="150" align="center"><font color="<?php echo $corfonts;?>"><?php echo $dadoss['situacaodet'] ?></font></td>
                                    <td width="100" align="center"><font color="<?php echo $corfonts;?>"><?php echo $dadoss['data_reab_f'] ?></font></td>
                                </tr>
                                        <?php } // fim do while ?>
                            </table>
                        </td>
                    </tr>

                                    <?php } // fim do if que conta o número de ocorrencias ?>
                    <tr>
                        <td colspan="5">&nbsp;</td>
                    </tr>
                         <?php
                                break;
                            case 'cond':
                                $query_pena = "SELECT SUM(`gra_p_ano`) AS ano, SUM(`gra_p_mes`) AS mes, SUM(`gra_p_dia`) AS dia FROM `grade` WHERE `cod_detento` = $iddet AND `gra_campo_x` = false";

                                // executando a query
                                $query_pena = $model->query( $query_pena );

                                $pena = $query_pena->fetch_assoc();
                                $cond = cal_periodo( $pena['ano'], $pena['mes'], $pena['dia'] )

                                                ?>
                    <tr class="ocurr">
                        <td colspan="5" class="det_cond">Condenado a: <?php echo !empty( $cond ) ? $cond : 'N/C' ; ?></td>
                    </tr>
                    <tr>
                        <td colspan="5">&nbsp;</td>
                    </tr>
                                                <?php
                                 break;
                            case 'pec':

                                $q_pec = "SELECT
                                            `peculio`.`idpeculio`,
                                            `peculio`.`cod_detento`,
                                            `peculio`.`descr_peculio`,
                                            `peculio`.`confirm`,
                                            DATE_FORMAT( `peculio`.`data_add`, '%d/%m/%Y' ) AS data_add_f,
                                            `tipopeculio`.`tipo_peculio`
                                          FROM
                                            `peculio`
                                            INNER JOIN `tipopeculio` ON `peculio`.`cod_tipo_peculio` = `tipopeculio`.`idtipopeculio`
                                          WHERE
                                            `peculio`.`cod_detento` = $iddet
                                            AND
                                            `peculio`.`retirado` = FALSE
                                          ORDER BY
                                            `peculio`.`data_add`, `tipopeculio`.`tipo_peculio`";

                                // executando a query
                                $q_pec = $model->query( $q_pec );

                                $cont_pec = $q_pec->num_rows;
                                if( !$q_pec or $cont_pec < 1 ) {
                                                ?>
                    <tr class="no_ocurr" >
                        <td colspan="5"><p class="p_q_no_result">Nada consta.</p></td>
                    </tr>
                          <?php } else { ?>
                    <tr class="ocurr" >
                        <td colspan="5">
                            <table class="bonde_check">
                                <tr >
                                    <th width="80" height="20" align="center">DATA</th>
                                    <th width="100" align="center">TIPO</th>
                                    <th width="335" align="center" >DESCRIÇÃO</th>
                                </tr>
                                    <?php
                                    while($d_pec = $q_pec->fetch_assoc()) {

                                        $corfont_pec = '#000000';
                                        $img_botao = SICOP_SYS_IMG_PATH . 's_add_g.png';
                                        $text_alt = 'Este pertence já está confirmado';

                                        if ( $d_pec['confirm'] == 0 ) {
                                            $corfont_pec = '#FF0000';
                                            $img_botao = SICOP_SYS_IMG_PATH . 's_add.png';
                                            $text_alt = 'Confirmar este pertence';
                                        }

                                        ?>
                                <tr class="even">
                                    <td height="20" align="center"><font color="<?php echo $corfont_pec;?>"><?php echo $d_pec['data_add_f'] ?></font></td>
                                    <td ><font color="<?php echo $corfont_pec;?>"><?php echo $d_pec['tipo_peculio'] ?></font></td>
                                    <td width="335" ><font color="<?php echo $corfont_pec;?>"><?php echo nl2br($d_pec['descr_peculio']) ?></font></td>
                                </tr>
                                    <?php } // fim do while ?>
                            </table>
                        </td>
                    </tr>
                                <?php } // fim do if que conta o número de ocorrencias ?>
                    <tr>
                        <td colspan="5">&nbsp;</td>
                    </tr>
                         <?php
                                break;

                        } // fim do switch( $tipo_ck )

                        $dest_ant = $d_bonde['destino'];

                    } // fim do while( $d_bonde...

                    // fechando a conexao
                    $model->closeConnection();

                ?>

            </table><!-- fim da table class="bonde_list" -->

<?php include 'footer.php'; ?>