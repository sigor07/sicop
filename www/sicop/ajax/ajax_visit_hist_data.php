<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_ajax.php';

$tipo_pag  = 'HISTÓRICO DE VISITAS - AJAX';
$msg_falha = '<p class="q_error">FALHA!</p>';

$is_post = is_post();
if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->set_msg_type( SM_TYPE_ATEN );
    $msg->set_msg_pre_def( SM_DIRECT_ACCESS );
    $msg->add_parenteses( $tipo_pag );
    $msg->get_msg();

    echo $msg_falha;

    exit;

}

$idv = get_post( 'uid', 'int' );
if ( empty( $idv ) ) {

    // montar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->set_msg_type( SM_TYPE_ATEN );
    $msg->set_msg_pre_def( SM_DIRECT_ACCESS );
    $msg->add_parenteses( "IDENTIFICADOR EM BRANCO - $tipo_pag" );
    $msg->get_msg();

    echo $msg_falha;

    exit;

}

$query = "SELECT
              visita_mov.idmov_visit,
              visita_mov.cod_visita,
              visita_mov.num_seq,
              visita_mov.jumbo,
              visita_mov.data_in,
              DATE_FORMAT(visita_mov.data_in, '%d/%m/%Y') AS data_in_f,
              DATE_FORMAT(visita_mov.data_in, '%H:%i') AS hora_in,
              visita_mov.user_in,
              DATE_FORMAT(visita_mov.data_out, '%d/%m/%Y') AS data_out,
              DATE_FORMAT(visita_mov.data_out, '%H:%i') AS hora_out,
              visita_mov.user_out,
              detentos.nome_det,
              detentos.matricula
            FROM
              visita_mov
              INNER JOIN detentos ON visita_mov.cod_detento = detentos.iddetento
            WHERE
              visita_mov.cod_visita = $idv
            ORDER BY
              visita_mov.data_in DESC";

$db = SicopModel::getInstance();
$query = $db->query( $query );

if ( !$query ) {

    // pegar a mensagem de erro mysql
    $msg_err_mysql = $db->getErrorMsg();
    $db->closeConnection();

    // gerar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->set_msg_type( SM_TYPE_ERR );
    $msg->add_quebras( 1 );
    $msg->set_msg_pre_def( SM_QUERY_FAIL );
    $msg->add_parenteses( $tipo_pag );
    $msg->add_quebras( 2 );
    $msg->set_msg( $msg_err_mysql );
    $msg->get_msg();

    echo $msg_falha;
    exit;

}

$db->closeConnection();

$cont = $query->num_rows;

header( "Content-Type: text/html; charset=utf-8" );

//Evitando cache de arquivo
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0' );
header( 'Pragma: no-cache' );
header( 'Expires: 0' );

if ( $cont < 1 ) {
    echo '<p class="p_q_no_result">Nada consta.</p>';
    exit;
}

$i = 1;

$img_sys_path = SICOP_SYS_IMG_PATH;

?>

<div class="grid_div" id="aaa">
                <?php while( $d_mov_v = $query->fetch_assoc() ) { ?>

                <div class="row_div">
                    <div class="row_num">
                        <span class="row_index"><?php echo $i++; ?></span>
                    </div>
                    <div class="cell_div cell_line_1 hist_visit_date_in">
                        <span class="grid_div_legend">Entrada:</span> <?php echo $d_mov_v['data_in_f'] . ' às ' . $d_mov_v['hora_in'] ?>
                    </div>
                    <div class="cell_div cell_line_1 hist_visit_user_in">
                        <span class="grid_div_legend">Usuário:</span> <?php echo $d_mov_v['user_in'] ?>
                    </div>
                    <div class="cell_div cell_line_1 hist_visit_seq">
                        <span class="grid_div_legend">Sequência:</span> <?php echo $d_mov_v['num_seq'] ?>
                    </div>
                    <div class="cell_div cell_line_1 hist_visit_jumbo">
                        <span class="grid_div_legend">Jumbo:</span> <?php if ( !empty( $d_mov_v['jumbo'] ) ) { ?><img src="<?php echo $img_sys_path; ?>s_add.png" alt="Sim" class="icon_button" /><?php } ?>
                    </div>
                    <div class="cell_div cell_line_1 hist_visit_date_out">
                        <span class="grid_div_legend">Saida:</span> <?php echo $d_mov_v['hora_out'] ?>
                    </div>
                    <div class="cell_div cell_line_1 hist_visit_user_out">
                        <span class="grid_div_legend">Usuário:</span> <?php echo $d_mov_v['user_out'] ?>
                    </div>
                    <div class="cell_div cell_line_2 hist_visit_det_nome">
                        <span class="grid_div_legend">Detento:</span> <?php echo $d_mov_v['nome_det'] ?>
                    </div>
                    <div class="cell_div cell_line_2 hist_visit_det_mat">
                        <span class="grid_div_legend">Matrícula:</span> <?php echo formata_num( $d_mov_v['matricula'] ) ?>
                    </div>

                </div>

                <?php } // fim do while ?>

</div>
