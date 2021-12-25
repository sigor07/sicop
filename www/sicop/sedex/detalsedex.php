<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag          = link_pag();
$tipo         = '';
$tipo_pag     = 'DETALHES DO SEDEX';
$img_sys_path = SICOP_SYS_IMG_PATH;

$n_sedex   = get_session( 'n_sedex', 'int' );
$n_rol     = get_session( 'n_rol', 'int' );
$n_sedex_n = 2;

if ( $n_sedex < $n_sedex_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $tipo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$ids = get_get( 'ids', 'int' );

if ( empty( $ids ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( $tipo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$q_sedex = "SELECT
              `sedex`.`cod_detento`,
              `sedex`.`cod_sedex`,
              `sedex`.`cod_motivo_dev`,
              `sedex`.`sit_sedex`,
              `sedex`.`user_add`,
              DATE_FORMAT( `sedex`.`data_add`, '%d/%m/%Y às %H:%i' ) AS data_add,
              `sedex`.`ip_add`,
              `sedex`.`user_up`,
              DATE_FORMAT( `sedex`.`data_up`, '%d/%m/%Y às %H:%i' ) AS data_up,
              `sedex`.`ip_up`,
              `sedex_motivo`.`motivo`,
              `visitas`.`idvisita`,
              `visitas`.`nome_visit`,
              `visitas`.`rg_visit`,
              `tipoparentesco`.`parentesco`
            FROM
              `sedex`
              LEFT JOIN `visitas` ON `sedex`.`cod_visita` = `visitas`.`idvisita`
              LEFT JOIN `tipoparentesco` ON `visitas`.`cod_parentesco` = `tipoparentesco`.`idparentesco`
              LEFT JOIN `sedex_motivo` ON `sedex`.`cod_motivo_dev` = `sedex_motivo`.`idmotivo`
            WHERE
              `sedex`.`idsedex` = $ids
            LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_sedex = $model->query( $q_sedex );

// fechando a conexao
$model->closeConnection();

if( !$q_sedex ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta ( $tipo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_sedex = $q_sedex->num_rows;

if( $cont_sedex < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "A consulta retornou 0 ocorrências ( $tipo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$d_sedex = $q_sedex->fetch_assoc();

$iddet = $d_sedex['cod_detento'];

$user_add = '';
$user_up = '';

if ( !empty( $d_sedex['user_add'] ) ) {
    $user_add = 'ID usuário: ' . $d_sedex['user_add'] . ', em ' . $d_sedex['data_add'];
}

if ( !empty( $d_sedex['user_up'] ) ) {
    $user_up = 'ID usuário: ' . $d_sedex['user_up'] . ', em ' . $d_sedex['data_up'];
}

$q_sit_sedex = "SELECT
                  `sedex_mov`.`sit_sedex`,
                  DATE_FORMAT( `sedex_mov`.`data_mov`, '%d/%m/%Y' ) AS data_mov_f
                FROM
                  `sedex`
                  INNER JOIN `sedex_mov` ON `sedex_mov`.`cod_sedex` = `sedex`.`idsedex`
                  LEFT JOIN `sedex_motivo` ON `sedex`.`cod_motivo_dev` = `sedex_motivo`.`idmotivo`
                WHERE
                  `sedex_mov`.`cod_sedex` = $ids
                ORDER BY
                  `sedex_mov`.`data_mov` DESC
                LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_sit_sedex = $model->query( $q_sit_sedex );

// fechando a conexao
$model->closeConnection();

if( !$q_sit_sedex ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta ( $tipo_pag - SITUAÇÃO DO SEDEX ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_sit_sedex = $q_sit_sedex->num_rows;

if( $cont_sit_sedex < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "A consulta retornou 0 ocorrências ( $tipo_pag - SITUAÇÃO DO SEDEX ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$d_sit_sedex = $q_sit_sedex->fetch_assoc();

$q_item_sedex = "SELECT
                   `tipo_un_medida`.`un_medida`,
                   `sedex_itens`.`id_item`,
                   `sedex_itens`.`quant`,
                   `sedex_itens`.`desc`,
                   `sedex_itens`.`retido`,
                   DATE_FORMAT( `sedex_itens`.`data_add`, '%d/%m/%Y' ) AS `data_add`
                 FROM
                   `sedex_itens`
                   INNER JOIN `tipo_un_medida` ON `sedex_itens`.`cod_um` = `tipo_un_medida`.`idum`
                 WHERE
                   `sedex_itens`.`cod_sedex` = $ids
                 ORDER BY
                   `sedex_itens`.`retido`";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_item_sedex = $model->query( $q_item_sedex );

// fechando a conexao
$model->closeConnection();

if ( !$q_item_sedex ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta ( $tipo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$cont_item_sedex = $q_item_sedex->num_rows;

$q_mov_sedex = "SELECT
                  `idmovsedex`,
                  `cod_sedex`,
                  `sit_sedex`,
                  DATE_FORMAT( `data_mov`, '%d/%m/%Y' ) AS data_mov_f
                FROM
                  `sedex_mov`
                WHERE
                  `cod_sedex` = $ids
                ORDER BY
                  `data_mov` DESC";

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Detalhes do Sedex';

// adicionando o javascript
$cab_js = 'ajax/ajax_sedex.js';
set_cab_js( $cab_js );


require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 4 );
$trail->output();
?>

            <input type="hidden" name="ids" id="ids" value="<?php echo $ids; ?>" />

            <p class="descript_page">DETALHES DO SEDEX</p>

            <?php if ( $n_sedex >= 4 ) {  ?>
            <p class="link_common"> <a href='javascript:void(0)' onclick='drop_sedex( <?php echo $ids; ?> )' title="Excluir este sedex" >Excluir</a> </p>
            <?php }; ?>

            <?php include 'quali/det_basic.php'; ?>

            <?php if ( !empty( $d_sedex['nome_visit'] ) ) {?>
            <p class="table_leg">Visitante</p>

            <table style="margin: 0 auto">
                <tr style="background-color: #ECE9D8">
                    <td style="height: 20px; padding: 1px 3px; vertical-align: middle; width: 350px;"><?php if ( $n_rol >= 2 ) { ?><a href="<?php echo SICOP_ABS_PATH ?>visita/detalvisit.php?idvisit=<?php echo $d_sedex['idvisita']; ?>" title="Clique aqui para abrir o cadastro deste visitante"><?php echo $d_sedex['nome_visit']; ?></a><?php } else { ?><?php echo $d_sedex['nome_visit']; ?><?php } ?></td>
                    <td style="height: 20px; padding: 1px 3px; vertical-align: middle; width: 140px;">R.G. <?php echo $d_sedex['rg_visit'] ?></td>
                    <td style="height: 20px; padding: 1px 3px; text-align: center; vertical-align: middle; width: 160px;">Parentesco: <?php echo $d_sedex['parentesco'] ?></td>
                </tr>
            </table>
            <?php }?>

            <p class="table_leg">Sedex</p>

            <table class="detal_sedex">

                <tr>
                    <td class="sedex_p" colspan="2">Código: <?php echo formata_num_sedex ( $d_sedex['cod_sedex'] ); ?></td>
                    <td class="sedex_p" colspan="2">Data: <?php echo $d_sit_sedex['data_mov_f'] ?></td>
                    <td class="sedex_p" colspan="2">Situação: <?php echo trata_sit_sedex($d_sit_sedex['sit_sedex']) ?></td>
                </tr>

                <?php if ( $d_sedex['sit_sedex'] == 3 or $d_sedex['sit_sedex'] == 4 ) {?>
                <tr>
                    <td class="sedex_g" colspan="6">Motivo: <?php echo $d_sedex['motivo'] ?></td>
                </tr>
                <?php } ?>

                <tr>
                    <td class="sedex_m_user_l" colspan="3">CADASTRAMENTO</td>
                    <td class="sedex_m_user_l" colspan="3">ÚLTIMA ATUALIZAÇÃO</td>
                </tr>
                <tr>
                    <td class="sedex_m_user_f" colspan="3"><?php echo $user_add ?></td>
                    <td class="sedex_m_user_f" colspan="3"><?php echo $user_up; ?></td>
                </tr>

            </table>

            <div class="linha">
                RELAÇÃO DE ITENS <?php if ( $n_sedex >= 3 ) {  ?> - <a id="link_add_item_sedex" href="javascript:void(0)" title="Relacionar itens para este sedex">Relacionar itens</a><?php }; ?>
                <hr />
            </div>

            <div id="table_sedex_item">
                <?php
                if( $cont_item_sedex < 1 ) {
                    echo '<p class="p_q_no_result">Não há itens para este sedex.</p>';
                } else {
                ?>
                <table class="lista_busca">
                    <tr>
                        <th class="desc_data">DATA</th>
                        <th class="sdx_item_medida">MEDIDA</th>
                        <th class="sdx_item_quant">QUANT</th>
                        <th class="sdx_item_desc">DESCRIÇÃO</th>
                        <th class="sdx_item_ret">SITUAÇÃO</th>
                        <?php if ( $n_sedex >= 3 ) {  ?>
                        <th class="tb_bt">&nbsp;</th>
                        <th class="tb_bt">&nbsp;</th>
                        <?php }; ?>
                    </tr>
                    <?php

                    while( $d_item_sedex = $q_item_sedex->fetch_object() ) {

                        $quant = str_replace( '.', ',', $d_item_sedex->quant );

                        $ret = $d_item_sedex->retido == 1 ? 'RETIDO' : 'ENTREGUE';

                        ?>
                    <tr class="even">
                        <td class="desc_data"><?php echo $d_item_sedex->data_add; ?></td>
                        <td class="sdx_item_medida"><?php echo $d_item_sedex->un_medida; ?></td>
                        <td class="sdx_item_quant"><?php echo $quant; ?></td>
                        <td class="sdx_item_desc"><?php echo $d_item_sedex->desc; ?></td>
                        <td class="sdx_item_ret"><?php echo $ret; ?></td>
                        <?php if ( $n_sedex >= 3 ) {  ?>
                        <td class="tb_bt"><input type="image" src="<?php echo $img_sys_path; ?>b_edit.png" name="edit_item_sedex[]" value="<?php echo $d_item_sedex->id_item; ?>" title="Alterar este item" /></td>
                        <td class="tb_bt"><input type="image" src="<?php echo $img_sys_path; ?>b_drop.png" name="del_item_sedex[]" value="<?php echo $d_item_sedex->id_item; ?>" title="Excluir este item" /></td>
                        <?php }; ?>
                    </tr>

                    <?php } ?>

                </table>

                <?php } // /if( $cont_item_sedex < 1 ) { ?>
            </div>


            <div class="linha">
                HISTÓRICO <?php if ( $d_sedex['sit_sedex'] == 2 || $d_sedex['sit_sedex'] == 2 ) {  ?> - <a href="devol_sedex.php?ids=<?php echo $ids; ?>" title="Encaminhar este sedex para a devolução">Encaminhar para devolução</a><?php }; ?>
                <hr />
            </div>
            <?php

            // instanciando o model
            $model = SicopModel::getInstance();

            // executando a query
            $q_mov_sedex = $model->query( $q_mov_sedex );

            // fechando a conexao
            $model->closeConnection();

            $cont_ms = 0;

            if( $q_mov_sedex ) $cont_ms = $q_mov_sedex->num_rows;

            if( $cont_ms < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não há dados.</p>';
            } else {
            ?>
            <table class="lista_busca">
                <tr>
                    <th class="desc_data">DATA</th>
                    <th class="mov_sedex">SITUAÇÃO</th>
                    <th class="tb_bt">&nbsp;</th>
                </tr>
                <?php while( $d_mov_sedex = $q_mov_sedex->fetch_assoc() ) { ?>
                <tr class="even">
                    <td class="desc_data"><?php echo $d_mov_sedex['data_mov_f']; ?></td>
                    <td class="mov_sedex"><?php echo trata_sit_sedex( $d_mov_sedex['sit_sedex'] ); ?></td>
                    <td class="tb_bt"><?php if ( $n_sedex >= 4 ) {  ?> <a href='javascript:void(0)' onclick='drop_mov_sedex(<?php echo $d_mov_sedex['idmovsedex']; ?>)' title="Excluir esta movimentação" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="Excluir esta movimentação" class="icon_button" /></a> <?php }; ?></td>
                </tr>
                <?php } ?>
            </table>
            <?php } ?>

<?php include 'footer.php';?>