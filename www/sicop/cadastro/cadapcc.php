<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_cadastro = get_session( 'n_cadastro', 'int' );
$n_cad_n    = 3;

$n_sind     = get_session( 'n_sind', 'int' );
$n_sind_n   = 2;

$motivo_pag = 'CADASTRAR APCC';

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

$iddet = get_get( 'iddet', 'int' );

if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$q_apcc = "SELECT
             `apcc`.`idapcc`,
             `apcc`.`data_add`,
             DATE_FORMAT(`apcc`.`data_add`,'%d/%m/%Y') AS data_add_f,
             `numeroapcc`.`numero_apcc`,
             `numeroapcc`.`ano`
           FROM
             `apcc`
             INNER JOIN `numeroapcc` ON `apcc`.`cod_numapcc` = `numeroapcc`.`idnumapcc`
           WHERE
             `apcc`.`cod_detento` = $iddet AND DATEDIFF(CURDATE(), `apcc`.`data_add`) < 30
           ORDER BY
             `apcc`.`data_add`";

$querysind = "SELECT
                `sindicancias`.`idsind`,
                `sindicancias`.`cod_detento`,
                `sindicancias`.`num_pda`,
                `sindicancias`.`ano_pda`,
                `sindicancias`.`local_pda`,
                DATE_FORMAT(`sindicancias`.`data_ocorrencia`, '%d/%m/%Y') AS data_ocorrencia,
                `sindicancias`.`sit_pda`,
                `tipositdet`.`situacaodet`,
                `sindicancias`.`data_reabilit`,
                DATE_FORMAT(`sindicancias`.`data_reabilit`, '%d/%m/%Y') AS data_reab_f
            FROM
                `sindicancias`
                LEFT JOIN `tipositdet` ON `sindicancias`.`cod_sit_detento` = `tipositdet`.`idsitdet`
            WHERE
                `sindicancias`.`cod_detento` = $iddet
                AND
                ( ( `sindicancias`.`sit_pda` = 1 OR `sindicancias`.`sit_pda` = 3 )
                OR
                ( `sindicancias`.`sit_pda` = 2 AND `sindicancias`.`data_reabilit` > DATE(NOW()) ) )
            ORDER BY
                `sindicancias`.`ano_pda`, `sindicancias`.`num_pda`";

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Atestados de permanência';

// adicionando o javascript
$cab_js   = array();
$cab_js[] = 'valida.js';
$cab_js[] = 'jquery.markrows.js';
set_cab_js( $cab_js );

$q_tip_cond = 'SELECT `idconduta`, `conduta` FROM `tipoconduta` ORDER BY `conduta`';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_tip_cond = $model->query( $q_tip_cond );

// fechando a conexao
$model->closeConnection();

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 4 );
$trail->output();
?>


            <p class="descript_page">ATESTADO DE PERMANÊNCIA</p>

            <?php include 'quali/det_cad.php'; ?>

            <p class="table_leg">Novo atestado</p>

            <?php

            $q_mov_in = "SELECT
                           `mov_det`.`id_mov`,
                           `mov_det`.`data_mov`,
                           DATE_FORMAT(`mov_det`.`data_mov`, '%d/%m/%Y') AS `data_mov_f`,
                           `unidades`.`unidades`
                         FROM
                           `mov_det`
                           INNER JOIN `unidades` ON `mov_det`.`cod_local_mov` = `unidades`.`idunidades`
                         WHERE
                           ( `mov_det`.`cod_tipo_mov` = 1 OR `mov_det`.`cod_tipo_mov` = 3 ) AND `cod_detento` = $iddet
                         ORDER BY
                           `data_mov` ASC, `data_add` ASC";

            // instanciando o model
            $model = SicopModel::getInstance();

            // executando a query
            $q_mov_in = $model->query( $q_mov_in );

            $cont_mov_in = $q_mov_in->num_rows;

            if ( !$q_mov_in or ( empty( $cont_mov_in ) or $cont_mov_in < 1 ) ) { // se o número de ocorrências for menor do que 1, mostra a mensagem

                echo '<p class="p_q_no_result">Não há movimentações cadastradas.</p>';
                include 'footer.php';
                exit;

            } else {

            ?>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendapcc.php" method="post" name="sendapcc" id="sendapcc" onSubmit="return validaapcc();">

                <table class="lista_busca grid">
                    <thead>
                        <tr>
                            <th class="num_od_sml">N</th>
                            <th width="125" scope="col">DATA DA INCLUSÃO</th>
                            <th width="204" scope="col">PROCEDÊNCIA</th>
                            <th width="125" scope="col">DATA DA EXCLUSÃO</th>
                            <th width="205" scope="col">DESTINO</th>
                            <th class="tb_ck">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php

                    $i = 0;
                    while( $d_mov_in = $q_mov_in->fetch_assoc() ) {

                        ++$i;

                        $data_in     = $d_mov_in['data_mov'];
                        $data_in_f   = $d_mov_in['data_mov_f'];
                        $idmovin     = $d_mov_in['id_mov'];
                        $procedencia = $d_mov_in['unidades'];

                        $q_mov_out = "SELECT
                                        `mov_det`.`id_mov`,
                                        `mov_det`.`data_mov`,
                                        Date_Format ( `mov_det`.`data_mov`, '%d/%m/%Y' ) AS `data_mov_f`,
                                        `unidades`.`unidades`
                                      FROM
                                        `mov_det`
                                        INNER JOIN `unidades` ON `mov_det`.`cod_local_mov` = `unidades`.`idunidades`
                                      WHERE
                                        `mov_det`.`data_mov` >= '$data_in' AND ( `mov_det`.`cod_tipo_mov` = 5 OR `mov_det`.`cod_tipo_mov` = 7 ) AND `mov_det`.`cod_detento` = $iddet
                                      ORDER BY
                                        `mov_det`.`data_mov`, `mov_det`.`data_add`
                                      LIMIT    1";

                        // executando a query
                        $q_mov_out = $model->query( $q_mov_out );

                        $mov_out    = '';
                        $destino    = '';
                        $data_out_f = 'ATÉ A PRESENTE DATA';
                        $cont_out   = $q_mov_out->num_rows;
                        if ( $cont_out >= 1 ){
                            $d_mov_out = $q_mov_out->fetch_assoc();
                            $data_out_f = $d_mov_out['data_mov_f'];
                            $destino    = $d_mov_out['unidades'];
                        }

                        $check = '';
                        if ( $i == $cont_mov_in ) $check = 'checked="checked"';

                    ?>
                        <tr class="even">
                            <td class="num_od_sml"><?php echo $i; ?></td>
                            <td align="center" ><?php echo $data_in_f; ?></td>
                            <td ><?php echo $procedencia; ?></td>
                            <?php
                            if ( empty( $cont_out ) ) {
                                echo '<td height="20" colspan="2" align="center" ><b>' . $data_out_f . '</b></td>';
                            } else {
                            ?>
                            <td align="center" ><?php echo $data_out_f; ?></td>
                            <td ><?php echo $destino; ?></td>
                            <?php } // fim do if else ?>
                            <td class="tb_ck"><input name="idmovin[]" type="checkbox" class="mark_row" id="idmovin" value="<?php echo $idmovin ?>"  <?php echo $check ?>/></td>
                        </tr>
                    <?php } // fim do while ?>
                    </tbody>

                    <tfoot>
                        <tr>
                            <td  class="tb_ck_leg" colspan="5">todos:</td>
                            <td class="tb_ck"><input type="checkbox" name="todos" value="todos" /></td>
                        </tr>
                    </tfoot>

                </table>

                <div style="margin: 5px auto; text-align: center;">

                        Conduta:
                        <select name="conduta" class="CaixaTexto" id="conduta" onchange="mostraPDA_APCC()">
                            <option value="" selected="selected">Sem Conduta</option>
                            <?php while( $d_cond = $q_tip_cond->fetch_assoc() ) { ?>
                            <option value="<?php echo $d_cond['idconduta'];?>" ><?php echo $d_cond['conduta'];?></option>
                            <?php };?>
                        </select>

                        <span id="f_pda" style="margin-left: 10px;">Número do PDA:
                            <input name="pda" type="text" class="CaixaTexto" id="pda" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event,6);" size="10" maxlength="11" />
                        </span>

                </div>

                <input name="iddet" type="hidden" id="iddet" value="<?php echo $iddet;?>" />
                <input name="proced" type="hidden" id="proced" value="3" />

                <div class="form_bts">
                    <input class="form_bt" name="atualizar" type="submit" value="Cadastrar" />
                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form><!-- /form#sendapcc -->

            <?php

            } // if ( !$q_mov_in or ( empty( $cont_mov_in ) or $cont_mov_in < 1 ) ) {

            // fechando a conexao
            $model->closeConnection();

            ?>

            <div id="sind"></div>
            <div class="linha">
                SINDICÂNCIAS (<font color="#FF0000">em reabilitação</font>) - Somente com situação "em andamento" ou "em reabilitação"
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
            if ( !$querysind or $conts < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Nada consta.</p>';
            } else {
                if ( $n_sind < $n_sind_n ) { // limitar o acesso de que não tem pemições para acessar a sindicância
                    echo '<p class="p_q_no_result"><img src="' . SICOP_SYS_IMG_PATH . 's_attention.png" alt="Atenção" class="icon_alert" /> ATENÇÃO -> ' . SICOP_DET_PRON_FU . ' ' . SICOP_DET_DESC_L . ' possui sindicância(s) cadastrada(s). <br> Para mais informações, consulte o setor responsável.</p>';
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

                    $corfonts = muda_cor_pda( $dadoss['data_reabilit'], $dadoss['sit_pda'] );

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

            <div class="linha">
                Atestados feitos nos últimos 30 dias:
                <hr />
            </div>

            <?php

                // instanciando o model
                $model = SicopModel::getInstance();

                // executando a query
                $q_apcc = $model->query( $q_apcc );

                // fechando a conexao
                $model->closeConnection();

                $contapcc = $q_apcc->num_rows;
                if ( !$q_apcc or $contapcc < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                    echo '<p class="p_q_no_result">Nenhum atestado.</p>';
                } else {
                    ?>
            <table class="lista_busca">
                <tr >
                    <td align="center" >NÚMERO</td>
                    <td align="center" >DATA</td>
                </tr>
                <?php while ( $d_apcc = $q_apcc->fetch_assoc() ) { ?>
                <tr class="even_dk">
                    <td width="120" height="15" align="center"><a href="<?php echo SICOP_ABS_PATH ?>cadastro/detalapcc.php?idapcc=<?php echo $d_apcc['idapcc'];?>" title="Clique aqui para ver os detalhes deste APCC"><?php echo $d_apcc['numero_apcc'] . '/' . $d_apcc['ano'] ?></a></td>
                    <td width="80" align="center"><?php echo $d_apcc['data_add_f'] ?></td>
                </tr>
                <?php } // fim do while ?>
            </table>
            <?php } // fim do if que conta o número de ocorrencias ?>

            <script type="text/javascript"> mostraPDA_APCC();</script>

<?php include 'footer.php'; ?>