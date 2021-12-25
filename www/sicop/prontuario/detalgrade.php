<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_pront    = get_session( 'n_pront', 'int' );
$n_cadastro = get_session( 'n_cadastro', 'int' );
$n_sind     = get_session( 'n_sind', 'int' );
$imp_pront  = get_session( 'imp_pront', 'int' );

$n_pront_n = 2;
$n_sind_n  = 2;
$n_cad_n   = 2;

if ( $n_pront < $n_pront_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'GRADE';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$iddet = get_get( 'iddet', 'int' );

if ( empty( $iddet ) ){
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso direto à página da grade.\n\n Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( '', 1 );
    exit;
}

$query_grade = "SELECT
                  `idprocesso`,
                  `cod_detento`,
                  `gra_preso`,
                  `gra_num_in`,
                  `gra_num_exec`,
                  `gra_num_inq`,
                  `gra_f_p`,
                  `gra_num_proc`,
                  `gra_campo_x`,
                  `gra_med_seg`,
                  `gra_hediondo`,
                  `gra_fed`,
                  `gra_outro_est`,
                  `gra_consumado`,
                  `gra_vara`,
                  `gra_comarca`,
                  `gra_artigos`,
                  `gra_data_delito`,
                  DATE_FORMAT(`gra_data_delito`, '%d/%m/%Y') AS gra_data_delito_f,
                  `gra_data_sent`,
                  DATE_FORMAT(`gra_data_sent`, '%d/%m/%Y') AS gra_data_sent_f,
                  `gra_p_ano`,
                  `gra_p_mes`,
                  `gra_p_dia`,
                  `gra_regime`,
                  `gra_sit_atual`,
                  `gra_obs`,
                  `user_add`,
                  DATE_FORMAT(`data_add`, '%d/%m/%Y às %H:%i') AS data_add_f,
                  `user_up`,
                  DATE_FORMAT(`data_up`, '%d/%m/%Y às %H:%i') AS data_up_f
                FROM
                  `grade`
                WHERE
                  `cod_detento` = $iddet
                ORDER BY
                  `gra_preso` DESC, `gra_campo_x` ASC, `gra_num_in` DESC, `gra_data_delito` DESC ";

$querysind = "SELECT
                `sindicancias`.`idsind`,
                `sindicancias`.`cod_detento`,
                `sindicancias`.`num_pda`,
                `sindicancias`.`ano_pda`,
                `sindicancias`.`local_pda`,
                DATE_FORMAT( `sindicancias`.`data_ocorrencia`, '%d/%m/%Y' ) AS data_ocorrencia,
                `sindicancias`.`sit_pda`,
                `tipositdet`.`situacaodet`,
                `sindicancias`.`data_reabilit`,
                DATE_FORMAT( `sindicancias`.`data_reabilit`, '%d/%m/%Y' ) AS data_reab_f
              FROM
                `sindicancias`
                LEFT JOIN `tipositdet` ON `sindicancias`.`cod_sit_detento` = `tipositdet`.`idsitdet`
              WHERE
                `sindicancias`.`cod_detento` = $iddet
              ORDER BY
                `sindicancias`.`ano_pda`, `sindicancias`.`num_pda`";

$query_obs = "SELECT
                `id_obs_grade`,
                `cod_detento`,
                `obs_grade`,
                `user_add`,
                DATE_FORMAT( `data_add`, '%d/%m/%Y' ) AS data_add_f,
                DATE_FORMAT( `data_add`, '%d/%m/%Y às %H:%i' ) AS data_add_fc,
                `data_add`,
                `user_up`,
                DATE_FORMAT(`data_up`, '%d/%m/%Y às %H:%i') AS data_up_f,
                `data_up`
              FROM
                `obs_grade`
              WHERE
                `cod_detento` = $iddet
              ORDER BY
                `data_add` DESC
              LIMIT 10";

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
                `data_aud` >= DATE(NOW())
              ORDER BY
                `data_aud` DESC, `hora_aud`";

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_obs = $model->query( $query_obs );

// fechando a conexao
$model->closeConnection();

$cont_obs = $query_obs->num_rows;

$desc_pag = 'Grade';


require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) {
    $pag_atual .= '?' . $qs;
}
$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 4 );
$trail->output();
?>


            <p class="descript_page">GRADE</p>

            <p class="link_common">
                <?php if ($imp_pront >= 1){?><a href="#" onClick="javascript: ow('../print/grade.php?iddet=<?php echo $iddet;?>', '600', '400'); return false" title="Imprimir a grade d<?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?>">Imprimir grade</a> | <a href="#" onClick="javascript: ow('../print/termo_ab_pront.php?iddet=<?php echo $iddet;?>', '600', '400'); return false" title="Imprimir o termo de abertura">Imprimir termo de abertura</a> | <a href="termo_enc.php?iddet=<?php echo $iddet;?>" title="Imprimir o termo de abertura">Imprimir termo de encerramento</a> | <?php }?><a href="<?php echo SICOP_ABS_PATH ?>buscadet.php?proced=bpro" title="Pesquisar outra grade">Pesquisar</a>
            </p>

            <p class="link_common">
                <a href="#sind" title="Ver as sindicâncias cadastradas para <?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?>">Sindicâncias</a> | <a href="#aud" title="Ver as audiências agendadas para <?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?>">Audiências</a> | <a href="#obs" title="Ir para as observações desta grade">Observações</a>
            </p>

            <?php if ($n_pront >= 3) { ?>
            <p class="link_common">
                <a href="editdetproc.php?iddet=<?php echo $iddet ?>" title="Alterar dados processuais d<?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?>">Alterar dados processuais</a>
            </p>
            <?php }?>

            <?php include 'quali/det_pront.php'; ?>

            <table style="margin: 10px auto; width: 740px;">
                <tr>
                    <td height="15" align="center">MOTIVO DA PRISÃO ATUAL</td>
                </tr>
                <tr style="background-color: #FAFAFA;">
                    <td style="height: 20px; padding: 5px;"><?php echo nl2br( $d_det['motivo_prisao'] ); ?></td>
                </tr>
            </table>

            <?php

                    // instanciando o model
                    $model = SicopModel::getInstance();

                    // executando a query
                    $query_grade = $model->query( $query_grade );

                    // fechando a conexao
                    $model->closeConnection();

                    $contg = $query_grade->num_rows;

            ?>

            <p class="table_leg">Processos (<?php echo $contg ?>)</p>

            <?php if ($n_pront >= 3) { ?>
            <p class="link_common">
                <a href="cadprocess.php?iddet=<?php echo $iddet ?>" title="Adicionar processos para <?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L; ?>">Adicionar</a>
            </p>
            <?php } ?>

            <?php
                if( !$query_grade or $contg < 1 ) {

                    echo '<p class="p_q_no_result">Não há dados cadastrados.</p>';

                } else {

                    while ( $d_grade = $query_grade->fetch_assoc() ) {

                        $corfont_preso = "#000000";

                        if ($d_grade['gra_preso'] == 1) {
                            $corfont_preso = "#FF0000";
                        }

                        $corfont_ext = "#000000";

                        if ($d_grade['gra_campo_x'] == 1) {
                            $corfont_ext = "#CC9900";
                        }

            ?>

            <span id="<?php echo $d_grade['idprocesso']; ?>"></span>

            <table class="detal_grade">
                <?php if ($n_pront >= 3) { ?>
                <tr>
                    <td class="grade_min">
                        <a href="editprocess.php?idproc=<?php echo $d_grade['idprocesso']; ?>" title="Alterar este processo">Alterar</a>
                        <?php if ($n_pront >= 4) { ?>
                        - <a href='javascript:void(0)' onclick='drop( "idprocesso", "<?php echo $d_grade['idprocesso']; ?>", "sendprocess", "drop_process_det", "2")' title="Excluir este processo">Excluir</a>
                        <?php } ?>
                    </td>
                    <td class="grade_min">&nbsp;</td>
                    <td class="grade_min">&nbsp;</td>
                </tr>
                <?php } ?>
                <tr>
                    <td class="grade_med">Execução: <?php echo $d_grade['gra_num_exec']; ?></td>
                    <td class="grade_med">Entrada: <?php echo $d_grade['gra_num_in']; ?></td>
                    <td class="grade_med">ID no sistema: <?php echo $d_grade['idprocesso']; ?></td>
                </tr>
                <tr>
                    <td class="grade_med">Nº do inquérito: <?php echo $d_grade['gra_num_inq']; ?></td>
                    <td class="grade_med">F/P: <?php echo $d_grade['gra_f_p']; ?></td>
                    <td class="grade_med"><font color="<?php echo $corfont_preso;?>">Preso: <?php echo tratasn($d_grade['gra_preso']); ?></font></td>
                </tr>
                <tr>
                    <td class="grade_med"><font color="#0000FF"><b>Nº do processo: <?php echo $d_grade['gra_num_proc']; ?></b></font></td>
                    <td class="grade_med">Data do delito: <?php echo $d_grade['gra_data_delito_f']; ?></td>
                    <td class="grade_med">Data da sentença: <?php echo $d_grade['gra_data_sent_f']; ?></td>
                </tr>
                <tr>
                    <td class="grade_med">Vara: <?php echo $d_grade['gra_vara']; ?></td>
                    <td class="grade_med">Comarca: <?php echo $d_grade['gra_comarca']; ?></td>
                    <td class="grade_med">Pena:  <?php echo (empty($d_grade['gra_p_ano'])) ? "" : $d_grade['gra_p_ano']." ano(s) "; ?><?php echo (empty($d_grade['gra_p_mes'])) ? "" : $d_grade['gra_p_mes']." meses ";?><?php echo (empty($d_grade['gra_p_dia'])) ? "" : $d_grade['gra_p_dia']." dias";?></td>
                </tr>
                <tr>
                    <td class="grade_med">Medida de segurança: <?php echo tratasn($d_grade['gra_med_seg']); ?></td>
                    <td class="grade_med">Crime hediondo: <?php echo tratasn($d_grade['gra_hediondo']); ?></td>
                    <td class="grade_med"><font color="<?php echo $corfont_ext;?>">Extinção da punibilidade: <?php echo tratasn($d_grade['gra_campo_x']); ?></font></td>
                </tr>
                <tr>
                    <td class="grade_med">Artigos: <?php echo $d_grade['gra_artigos']; ?></td>
                    <td class="grade_med">Consumado: <?php echo tratasn($d_grade['gra_consumado']); ?></td>
                    <td class="grade_med">Federal: <?php echo tratasn($d_grade['gra_fed']); ?></td>
                </tr>
                <tr>
                    <td colspan="2" class="grade_dual">Regime: <?php echo $d_grade['gra_regime']; ?></td>
                    <td class="grade_med">Outro estado: <?php echo tratasn($d_grade['gra_outro_est']); ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="grade_maior">Situação: <?php echo $d_grade['gra_sit_atual']; ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="grade_maior">Observação: <?php echo $d_grade['gra_obs']; ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="desc_user" ><span class="paragrafo9"><?php if ($d_grade['user_add'] and $d_grade['data_add_f']) {?>Cadastrado em <?php echo $d_grade['data_add_f'] ?>, usuário <?php echo $d_grade['user_add'] ?><?php }?><?php if ($d_grade['user_up'] and $d_grade['data_up_f']) {?> - Atualizado em <?php echo $d_grade['data_up_f'] ?>, usuário <?php echo $d_grade['user_up'] ?> <?php }?></span></td>
                </tr>
            </table>
            <?php
                        } // fim do while das grades
                    } // fim do if que verifica o número de ocorrencias
            ?>

            <div id="sind"></div>

            <div class="linha">
                SINDICÂNCIAS ( <font color="#FF0000">em reabilitação</font> )
                <hr />
            </div>
                    <?php

                    // instanciando o model
                    $model = SicopModel::getInstance();

                    // executando a query
                    $querysind = $model->query( $querysind );

                    // fechando a conexao
                    $model->closeConnection();

                    $conts = $querysind->num_rows;

                    if( !$querysind or $conts < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                        echo '<p class="p_q_no_result">Nada consta.</p>';
                    } else {
                        if ( $n_sind < $n_sind_n ) { // limitar o acesso de que não tem pemições para acessar a sindicância
                            echo '<p class="p_q_no_result"><img src="' . SICOP_SYS_IMG_PATH . 's_attention.png" alt="Atenção" class="icon_alert" /> ATENÇÃO -> ' . SICOP_DET_PRON_FU . SICOP_DET_DESC_L . ' possui sindicância(s) cadastrada(s). <br> Para mais informações, consulte o setor responsável.</p>';
                        } else {
                            ?>
            <table class="lista_busca">
                <tr >
                    <th class="num_pda">NÚMERO DO PDA</th>
                    <th class="data_oc">DATA DA OCORRÊNCIA</th>
                    <th class="sit_pda">SITUAÇÃO DO PDA</th>
                    <th class="sit_det_pda">SITUAÇÃO D<?php echo SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U; ?></th>
                    <th class="data_reab">REABILIATAÇÃO EM</th>
                </tr>

                <?php
                while ( $dadoss = $querysind->fetch_assoc() ) {

                    $numpda = format_num_pda( $dadoss['num_pda'], $dadoss['ano_pda'], $dadoss['local_pda'] );

                    $corfonts = muda_cor_pda($dadoss['data_reabilit'], $dadoss['sit_pda']);

                    ?>
                <tr class="even">
                    <td class="num_pda"><a href="<?php echo SICOP_ABS_PATH ?>sind/detalpda.php?idsind=<?php echo $dadoss['idsind'] ?>"><?php echo $numpda ?></a></td>
                    <td class="data_oc"><font color="<?php echo $corfonts;?>"><?php echo $dadoss['data_ocorrencia'] ?></font></td>
                    <td class="sit_pda"><font color="<?php echo $corfonts;?>"><?php echo trata_sit_pda($dadoss['sit_pda']) ?></font></td>
                    <td class="sit_det_pda"><font color="<?php echo $corfonts;?>"><?php echo $dadoss['situacaodet'] ?></font></td>
                    <td class="data_reab"><font color="<?php echo $corfonts;?>"><?php echo $dadoss['data_reab_f'] ?></font></td>
                </tr>
                    <?php } // fim do while ?>
            </table>
                <?php
                    } // fim do if que conta o número de ocorrencias
                } // fim do if que verifica as permissões
                ?>

            <div id="aud"></div>

            <div class="linha">
                AUDIÊNCIAS (ativa - <font color="#FF0000">justificada</font> - <font color="#CC9900">cancelada</font>) <?php if ( $n_cadastro >= 3 && !empty( $d_det['matricula'] ) ) {  ?> - <a href="<?php echo SICOP_ABS_PATH ?>cadastro/cadaud.php?iddet=<?php echo $iddet ?>">Cadastrar audiência</a><?php }; ?>
                <hr />
            </div>
            <?php

            // instanciando o model
            $model = SicopModel::getInstance();

            // executando a query
            $queryaud = $model->query( $queryaud );

            // fechando a conexao
            $model->closeConnection();

            $conta = $queryaud->num_rows;

            if( $conta < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Nenhuma audiência agendada.</p>';
            } else {
                if ( $n_cadastro < $n_cad_n ) { // limitar o acesso de que não tem pemições para acessar o cadastro
                    echo '<p class="p_q_no_result"><img src="' . SICOP_SYS_IMG_PATH . 's_attention.png" alt="Atenção" class="icon_alert" /> ATENÇÃO -> ' . SICOP_DET_PRON_FU . ' ' . SICOP_DET_DESC_L . ' possui audiências(s) cadastrada(s). <br> Para mais informações, consulte o setor responsável.</p>';
                } else {
                    ?>

                <table class="lista_busca">
                    <tr>
                        <th class="local_aud_hist">LOCAL DE APRESENTAÇÃO</th>
                        <th class="cidade_aud_hist">CIDADE</th>
                        <th class="data_hora_aud">DATA / HORA</th>
                        <th class="n_process">Nº DO PROCESSO</th>
                    </tr>

                        <?php
                        while ( $dadosa = $queryaud->fetch_assoc() ) {

                            $aud = trata_sit_aud( $dadosa['sit_aud'] );

                            ?>

                    <tr class="even" title="Situação da audiência: <?php echo $aud['sitaud']; ?>">
                        <td class="local_aud_hist"><a href="<?php echo SICOP_ABS_PATH ?>cadastro/detalaud.php?idaud=<?php echo $dadosa['idaudiencia'] ?>" ><?php echo $dadosa['local_aud'] ?></a></td>
                        <td class="cidade_aud_hist <?php echo $aud['css_class']; ?>"><?php echo $dadosa['cidade_aud'] ?></td>
                        <td class="data_hora_aud <?php echo $aud['css_class']; ?>"><?php echo $dadosa['data_aud_f'] . ' às ' . $dadosa['hora_aud_f']?></td>
                        <td class="n_process <?php echo $aud['css_class']; ?>"><?php echo $dadosa['num_processo'] ?></td>
                    </tr>

                        <?php } // fim do while ?>
            </table>
                    <?php
                        } // fim do if que conta o número de ocorrencias
                    } // fim do if que verifica as permissões
                    ?>

            <div id="obs"></div>

            <div class="linha">
                OBSERVAÇÕES<?php if ($n_pront >= 3) {  ?> - <a href="cadobsgrade.php?iddet=<?php echo $iddet ?>" title="Adicionar uma observação para esta grade">Adicionar observação</a> <a href="#" title="Abrir em outra janela" onClick="javascript: ow('cadobsgrade.php?iddet=<?php echo $iddet; ?>&targ=1', '800', '400'); return false" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>user_popup.png" alt="Abrir em outra janela" class="icon_popup" /></a><?php }; ?>
                <hr />
            </div>
                    <?php

                    if($cont_obs < 1) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                        echo '<p class="p_q_no_result">Não há observações.</p>';
                    } else {
                        ?>
            <table class="lista_busca">
                <tr >
                    <th class="desc_data">DATA</th>
                    <th class="desc_obs">OBSERVAÇÃO</th>
                    <th class="tb_bt">&nbsp;</th>
                    <th class="tb_bt">&nbsp;</th>
                </tr>
                    <?php
                    while($dados_obs = $query_obs->fetch_assoc()) {
                        ?>
                <tr class="even">
                    <td class="desc_data"><?php echo $dados_obs['data_add_f'] ?></td>
                    <td class="desc_obs" ><div align="justify"><?php echo nl2br($dados_obs['obs_grade']) ?></div></td>
                    <td class="tb_bt">
                    <?php if ($n_pront >= 3) {  ?>
                        <a href="editobsgrade.php?idobs=<?php echo $dados_obs['id_obs_grade']; ?>" title="Alterar esta observação" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" alt="" /></a>
                    <?php }; ?>
                    </td>
                    <td class="tb_bt">
                    <?php if ($n_pront >= 4) {  ?>
                        <a href='javascript:void(0)' onclick='drop( "id_obs_grade", "<?php echo $dados_obs['id_obs_grade']; ?>", "sendgradeobs", "drop_obs_grade", "2")' title="Excluir esta observação"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="Excluir esta observação" class="icon_button" /></a>
                    <?php }; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="desc_user" >Cadastrado em <?php echo $dados_obs['data_add_fc'] ?>, usuário <?php echo $dados_obs['user_add'] ?><?php if ($dados_obs['user_up'] and $dados_obs['data_up_f']) {?> - Atualizado em <?php echo $dados_obs['data_up_f'] ?>, usuário <?php echo $dados_obs['user_up'] ?> <?php }?></td>
                </tr>
                <?php
                    }
                }
                ?>
            </table>

<?php include 'footer.php'; ?>