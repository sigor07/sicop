<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_rol_n    = 2;
$n_sind_n   = 2;
$n_cad_n    = 2;
$n_pront_n  = 2;
$n_pec_n    = 2;
$n_inteli_n = 2;

$n_rol      = get_session( 'n_rol', 'int' );
$n_sind     = get_session( 'n_sind', 'int' );
$n_cadastro = get_session( 'n_cadastro', 'int' );
$n_pront    = get_session( 'n_pront', 'int' );
$n_peculio  = get_session( 'n_peculio', 'int' );
$n_inteli   = get_session( 'n_inteli', 'int' );

$motivo_pag = 'DETALHES DA INTELIGÊNCIA';

if ($n_inteli < $n_inteli_n) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$idinteli = get_get( 'idinteli', 'int' );

if ( empty( $idinteli ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$q_inteli = "SELECT `cod_detento` FROM `inteligencia` WHERE `idinteli` = $idinteli";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_inteli = $model->query( $q_inteli );

// fechando a conexao
$model->closeConnection();

if ( !$q_inteli ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_inteli = $q_inteli->num_rows;

if( $cont_inteli < 1 ) {
    $mensagem = "A consulta retornou 0 ocorrencias (DETALHES DA INTELIGÊNCIA).\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$d_inteli = $q_inteli->fetch_assoc();
$iddet    = $d_inteli['cod_detento'];

$query_obs = "SELECT
                `id_obs_inteli`,
                `cod_inteli`,
                `obs_inteli`,
                `user_add`,
                DATE_FORMAT(`data_add`, '%d/%m/%Y') AS data_add_f,
                DATE_FORMAT(`data_add`, '%d/%m/%Y às %H:%i') AS data_add_fc,
                `data_add`,
                `user_up`,
                DATE_FORMAT(`data_up`, '%d/%m/%Y às %H:%i') AS data_up_f,
                `data_up`
              FROM
                `obs_inteli`
              WHERE
                `cod_inteli` = $idinteli
              ORDER BY
                `data_add` DESC";

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Detalhes da inteligência';

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if (!empty($qs)) {
    $pag_atual .=  '?' . $qs;
}
$trail = new Breadcrumb();
$trail->add('Detalhes da inteligência', $pag_atual, 4);
$trail->output();

?>

            <script type="text/javascript">
                $(function(){
                    // PARA FOTO
                    $("a#link_foto_det").fancybox({
                        'transitionIn'  : 'elastic',
                        'transitionOut' : 'elastic'
                    });
                });
            </script>

            <p class="descript_page">DETALHES DA INTELIGÊNCIA</p>

            <?php if ( $n_inteli >= 3 ) {  ?>
            <p class="link_common">
                <a href="delinteli.php?idinteli=<?php echo $idinteli ?>" title="Excluir <?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L ;?> do monitoramento">Excluir da lista de monitorados</a>
            </p>
            <?php }; ?>

            <p class="link_common">
                <a href="#obs" title="Ir para as observações">Observações</a>
                <?php if ($n_rol >= $n_rol_n) { ?>| <a href="<?php echo SICOP_ABS_PATH ?>visita/rol_visit.php?iddet=<?php echo $iddet ?>" title="Ir para o Rol de visitas">Rol de Visitas</a><?php }?>
                <?php if ($n_sind >= $n_sind_n) { ?>| <a href="<?php echo SICOP_ABS_PATH ?>sind/rol_pda.php?iddet=<?php echo $iddet ?>" title="Ir para a Sindicância">Sindicâncias</a><?php }?>
                <?php if ($n_pront >= $n_pront_n) { ?> | <a href="<?php echo SICOP_ABS_PATH ?>prontuario/detalgrade.php?iddet=<?php echo $iddet ?>" title="Ver a grade processual deste detento">Grade</a><?php }?>
                <?php if ($n_cadastro >= $n_cad_n) { ?>| <a href="<?php echo SICOP_ABS_PATH ?>visita/rol_visit.php?iddet=<?php echo $iddet ?>" title="Ir para as audiências">Audiências</a><?php }?>
            </p>

            <?php include 'quali/det_top.php'; ?>

            <div id="obs"></div>
            <div class="linha">
                OBSERVAÇÕES DA INTELIGÊNCIA <?php if ( $n_inteli >= 3 ) {  ?> - <a href="cadobsinteli.php?idinteli=<?php echo $idinteli ?>" title="Adicionar uma observação para <?php echo SICOP_DET_PRON_L . ' ' . SICOP_DET_DESC_L ;?>">Adicionar observação</a> <a href="#" title="Abrir em outra janela" onClick="javascript: ow('cadobsinteli.php?idinteli=<?php echo $idinteli ?>&targ=1', '800', '560'); return false" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>user_popup.png" alt="Abrir em outra janela" class="icon_popup" /></a><?php }; ?>
                <hr />
            </div>
            <?php

            // instanciando o model
            $model = SicopModel::getInstance();

            // executando a query
            $query_obs = $model->query( $query_obs );

            // fechando a conexao
            $model->closeConnection();

            $cont_obs = $query_obs->num_rows;
            if( !$query_obs or $cont_obs < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
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
                <?php while ( $dados_obs = $query_obs->fetch_assoc() ) { ?>
                <tr class="even">
                    <td class="desc_data"><?php echo $dados_obs['data_add_f'] ?></td>
                    <td class="desc_obs"><?php echo nl2br( $dados_obs['obs_inteli'] ) ?></td>
                    <td class="tb_bt">
                        <?php if ( $n_inteli >= 3 ) {  ?><a href="editobsinteli.php?idobs=<?php echo $dados_obs['id_obs_inteli']; ?>" title="Alterar esta observação" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" alt="" /></a><?php }; ?>
                    </td>
                    <td class="tb_bt">
                        <?php if ( $n_inteli >= 4 ) {  ?><a href="delobsinteli.php?idobs=<?php echo $dados_obs['id_obs_inteli']; ?>" title="Excluir esta observação" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="" /></a><?php }; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="desc_user">Cadastrado em <?php echo $dados_obs['data_add_fc'] ?>, usuário <?php echo $dados_obs['user_add'] ?><?php if ($dados_obs['user_up'] and $dados_obs['data_up_f']) {?> - Atualizado em <?php echo $dados_obs['data_up_f'] ?>, usuário <?php echo $dados_obs['user_up'] ?> <?php }?></td>
                </tr>
                <?php } // fim do while ?>
            </table>

            <?php } // fim do if que conta o número de ocorrencias ?>

<?php include 'footer.php'; ?>