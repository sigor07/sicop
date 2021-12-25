<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_ajax.php';

$tipo_pag = 'OBSERVAÇÕES DE DETENTO - AJAX';
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
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página. Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. ( $tipo_pag )";
    get_msg( $msg, 1 );

    echo $msg_falha;
    exit;

}

$limit   = get_post( 'limit', 'int' );
$q_limit = '';
if ( !empty ( $limit ) ) {
    $q_limit = 'LIMIT 10';
}

$q_obs = "SELECT
            `id_obs_det`,
            `cod_detento`,
            `obs_det`,
            `user_add`,
            DATE_FORMAT( `data_add`, '%d/%m/%Y' ) AS data_add_f,
            DATE_FORMAT( `data_add`, '%d/%m/%Y às %H:%i' ) AS data_add_fc,
            `data_add`,
            `user_up`,
            DATE_FORMAT( `data_up`, '%d/%m/%Y às %H:%i' ) AS data_up_f,
            `data_up`
          FROM
            `obs_det`
          WHERE
            `cod_detento` = $iddet
          ORDER BY
            `data_add` DESC
          $q_limit";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_obs = $model->query( $q_obs );

// fechando a conexao
$model->closeConnection();

if ( !$q_obs ) {

    echo $msg_falha;
    exit;

}

$cont = $q_obs->num_rows;
if ( $cont < 1 ) {

    echo '<p class="p_q_no_result">Não há observações.</p>';
    exit;

}

$n_chefia  = get_session( 'n_chefia', 'int' );
$n_det_obs = get_session( 'n_det_obs', 'int' );
$n_obs_n   = 1;

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
            <th class="desc_obs">OBSERVAÇÃO</th>
            <th class="tb_bt">&nbsp;</th>
            <th class="tb_bt">&nbsp;</th>
        </tr>
        <?php while ( $dados_obs = $q_obs->fetch_assoc() ) { ?>
        <tr class="even">
            <td class="desc_data"><?php echo $dados_obs['data_add_f'] ?></td>
            <td class="desc_obs"><?php echo nl2br( $dados_obs['obs_det'] ) ?></td>
            <td class="tb_bt">
            <?php if ( $n_det_obs >= $n_obs_n ) {  ?>
                <input type="image" src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" name="edit_obs_det[]" value="<?php echo $dados_obs['id_obs_det'] ;?>" title="Alterar observação" />
            <?php }; ?>
            </td>
            <td class="tb_bt">
            <?php if ( $n_chefia >= 4 ) {  ?>
                <input type="image" src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" name="del_obs_det[]" value="<?php echo $dados_obs['id_obs_det'] ;?>" title="Excluir observação" />
            <?php }; ?>
            </td>
        </tr>
        <tr>
            <td colspan="4" class="desc_user">Cadastrado em <?php echo $dados_obs['data_add_fc'] ?>, usuário <?php echo $dados_obs['user_add'] ?><?php if ($dados_obs['user_up'] and $dados_obs['data_up_f']) {?> - Atualizado em <?php echo $dados_obs['data_up_f'] ?>, usuário <?php echo $dados_obs['user_up'] ?> <?php }?></td>
        </tr>
        <?php } // fim do while ?>
    </table>


