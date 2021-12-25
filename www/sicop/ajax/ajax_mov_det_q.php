<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_ajax.php';

$tipo_pag = 'MOVIMENTAÇÃO DE DETENTO - AJAX';
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

$iddet = get_post( 'iddet', 'int' );
if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página. Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. ( $tipo_pag )";
    get_msg( $msg, 1 );

    echo $msg_falha;
    exit;

}

$query_mov = "SELECT
                `mov_det`.`id_mov`,
                `mov_det`.`cod_tipo_mov`,
                `mov_det`.`cod_local_mov`,
                `mov_det`.`data_mov`,
                DATE_FORMAT(`mov_det`.`data_mov`, '%d/%m/%Y') AS data_mov_f,
                `mov_det`.`user_add`,
                DATE_FORMAT(`mov_det`.`data_add`, '%d/%m/%Y às %H:%i') AS data_add_fc,
                `mov_det`.`user_up`,
                DATE_FORMAT(`mov_det`.`data_up`, '%d/%m/%Y às %H:%i') AS data_up_f,
                `tipomov`.`sigla_mov`,
                `tipomov`.`tipo_mov`,
                `unidades`.`unidades` AS local_mov
              FROM
                `mov_det`
                LEFT JOIN `tipomov` ON `mov_det`.`cod_tipo_mov` = `tipomov`.`idtipo_mov`
                LEFT JOIN `unidades` ON `mov_det`.`cod_local_mov` = `unidades`.`idunidades`
              WHERE
                `mov_det`.`cod_detento` = $iddet
              ORDER BY
                `mov_det`.`data_mov` DESC,
                `mov_det`.`data_add` DESC";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_mov = $model->query( $query_mov );

// fechando a conexao
$model->closeConnection();


if ( !$query_mov ) {

    echo $msg_falha;
    exit;

}

$cont = $query_mov->num_rows;
if ( $cont < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "A consulta retornou 0 ocorrências ( $tipo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo $msg_falha;
    exit;

}

$n_chefia = get_session( 'n_chefia', 'int' );

header( "Content-Type: text/html; charset=utf-8" );

//Evitando cache de arquivo
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0' );
header( 'Pragma: no-cache' );
header( 'Expires: 0' );

?>


    <table class="lista_busca">
        <tr >
            <th class="desc_data">DATA</th>
            <th class="tipo_mov">TIPO DE MOVIEMTAÇÃO</th>
            <th class="local_hist_mov">LOCAL</th>
            <th class="tb_bt">&nbsp;</th>
            <th class="tb_bt">&nbsp;</th>
        </tr>
        <?php
        $i = 0;
        while ( $dados_mov = $query_mov->fetch_assoc() ) {
            ++$i;
        ?>
        <tr class="even">
            <td class="desc_data"><?php echo $dados_mov['data_mov_f'] ?></td>
            <td class="tipo_mov"><?php echo $dados_mov['sigla_mov']. ' - ' .$dados_mov['tipo_mov'] ?></td>
            <td class="local_hist_mov"><?php echo $dados_mov['local_mov'] ?></td>
            <td class="tb_bt">
            <?php if ( $n_chefia >= 3 ) { ?>
                <?php if ( $i == 1 ) { ?>
                    <a href="edit_mov_det.php?idmov=<?php echo $dados_mov['id_mov']; ?>" title="Alterar esta movimentação" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" alt="Alterar esta movimentação" /></a>
                <?php }; ?>
            <?php }; ?>
            </td>
            <td class="tb_bt">
            <?php if ( $n_chefia >= 4 ) { ?>
                <?php if ( $i == 1 ) { ?>
                    <a href='javascript:void(0)' onclick='drop( "idmov", "<?php echo $dados_mov['id_mov']; ?>", "senddetmovdel", "drop_mov_det", "2")' title="Excluir esta movimentação"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="Excluir esta movimentação" class="icon_button" /></a>
                <?php } else { ?>
                    <a href='javascript:void(0)' onclick='drop( "idmov", "<?php echo $dados_mov['id_mov']; ?>", "senddetmovdelacervo", "drop_mov_det", "2")' title="Excluir esta movimentação"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="Excluir esta movimentação" class="icon_button" /></a>
                <?php }; ?>
            <?php }; ?>
            </td>
        </tr>
        <tr>
            <td class="desc_user" colspan="5">Cadastrado em <?php echo $dados_mov['data_add_fc'] ?>, usuário <?php echo $dados_mov['user_add'] ?><?php if ($dados_mov['user_up'] and $dados_mov['data_up_f']) {?> - Atualizado em <?php echo $dados_mov['data_up_f'] ?>, usuário <?php echo $dados_mov['user_up'] ?> <?php }?></td>
        </tr>
    <?php } // fim do while ?>
    </table>

