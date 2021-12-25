<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_ajax.php';

$tipo_pag  = 'ITENS DE SEDEX - AJAX';
$msg_falha = '<p class="q_error">FALHA!</p>';

$is_post = is_post();
if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( $tipo_pag ).";
    get_msg( $msg, 1 );

    echo $msg_falha;
    exit;

}

$ids = get_post( 'uid', 'int' );
if ( empty( $ids ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página. Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. ( $tipo_pag )";
    get_msg( $msg, 1 );

    echo $msg_falha;
    exit;

}

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

$db = SicopModel::getInstance();
$q_item_sedex = $db->query( $q_item_sedex );

if ( !$q_item_sedex ) {

    // pegar a mensagem de erro mysql
    $msg_err_mysql = $db->getErrorMsg();
    $db->closeConnection();

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta ( $tipo_pag ). \n\n $msg_err_mysql";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$db->closeConnection();

$cont_item_sedex = $q_item_sedex->num_rows;

header( "Content-Type: text/html; charset=utf-8" );

//Evitando cache de arquivo
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0' );
header( 'Pragma: no-cache' );
header( 'Expires: 0' );

if ( $cont_item_sedex < 1 ) {
    echo '<p class="p_q_no_result">Não há itens para este sedex.</p>';
    exit;
}

$n_sedex      = get_session( 'n_sedex', 'int' );
$img_sys_path = SICOP_SYS_IMG_PATH;

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


