<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_sind   = get_session( 'n_sind', 'int' );
$n_sind_n = 2;

if ( $n_sind < $n_sind_n ) {
    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
    salvaLog( $mensagem );
    exit;
}

$idsind = get_get( 'idsind', 'int' );

if ( empty( $idsind ) ) {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso direto à página de detalhes da sindicância.\n\n Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( '', 1 );
    exit;
}

$query_pda = "SELECT
                `sindicancias`.`idsind`,
                `sindicancias`.`cod_detento`,
                `sindicancias`.`num_pda`,
                `sindicancias`.`ano_pda`,
                `sindicancias`.`local_pda`,
                DATE_FORMAT(`sindicancias`.`data_ocorrencia`, '%d/%m/%Y') AS data_ocorrencia,
                `sindicancias`.`sit_pda`,
                `sindicancias`.`cod_sit_detento` AS sit_det_pda,
                `tipositdet`.`situacaodet`,
                `sindicancias`.`data_reabilit`,
                `sindicancias`.`descr_pda`,
                DATE_FORMAT(`sindicancias`.`data_reabilit`, '%d/%m/%Y') AS data_reab_f,
                `sindicancias`.`user_add`,
                DATE_FORMAT(`sindicancias`.`data_add`, '%d/%m/%Y às %H:%i') AS data_add,
                `sindicancias`.`user_up`,
                DATE_FORMAT(`sindicancias`.`data_up`, '%d/%m/%Y às %H:%i') AS data_up
              FROM
                `sindicancias`
                LEFT JOIN `tipositdet` ON `sindicancias`.`cod_sit_detento` = `tipositdet`.`idsitdet`
              WHERE
                `sindicancias`.`idsind` = $idsind
              LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_pda = $model->query( $query_pda );

// fechando a conexao
$model->closeConnection();

if( !$query_pda ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_pda = $query_pda->num_rows;

if( $cont_pda < 1 ) {

    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta do PDA retornou 0 ocorrências ( DETALHES DO PDA ).\n\n Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$query_obs = "SELECT
                `id_obs_pda`,
                `cod_pda`,
                `obs_pda`,
                `user_add`,
                DATE_FORMAT(`data_add`, '%d/%m/%Y') AS data_add_f,
                DATE_FORMAT(`data_add`, '%d/%m/%Y às %H:%i') AS data_add_fc,
                `data_add`,
                `user_up`,
                DATE_FORMAT(`data_up`, '%d/%m/%Y às %H:%i') AS data_up_f,
                `data_up`
              FROM
                `obs_pda`
              WHERE
                `cod_pda` = $idsind
              ORDER BY
                `data_add` DESC
              LIMIT 10";



$d_pda = $query_pda->fetch_assoc();

$iddet = $d_pda['cod_detento'];

$user_add = '';
$user_up = '';

if (!empty($d_pda['user_add'])) {
    $user_add = 'ID usuário: ' . $d_pda['user_add'] . ', em ' . $d_pda['data_add'];
};

if (!empty($d_pda['user_up'])) {
    $user_up = 'ID usuário: ' . $d_pda['user_up'] . ', em ' . $d_pda['data_up'];
};

$numpda = (empty($d_pda['local_pda'])) ? $d_pda['num_pda'] . '/' . $d_pda['ano_pda'] : $d_pda['num_pda'] . '/' . $d_pda['ano_pda'] . '-' . $d_pda['local_pda'];

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Detalhes do PDA';


require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 5 );
$trail->output();
?>

            <p class="descript_page">DETALHES DO PDA</p>

            <?php if ( empty( $iddet ) ) { ?>

            <div class="detal_var">
                AUTORIA DESCONHECIDA
            </div>

            <?php if ( $n_sind >= 3 ) { ?>
            <p class="link_common"><a href="vinculapda.php" title="Atribuir a autorida deste PDA a <?php echo SICOP_DET_DESC_L; ?>">Vincular PDA</a></p>
            <?php
                    /* ?idsind=<?php echo $d_pda['idsind'] ?> */
                    $_SESSION['pda_id'] = $d_pda['idsind'];
                };
            ?>
            <?php } else { ?>

            <?php include 'quali/det_cad.php'; ?>

            <?php } ?>
            <p class="table_leg" >PDA</p>

            <?php if ( $n_sind >= 3 ) { ?>
            <p class="link_common">

                <a href="editpda.php?idsind=<?php echo $d_pda['idsind'] ?>" title="Alterar dados deste PDA">Alterar</a>

            <?php if ( $n_sind >= 4 ) { ?> | <a href='javascript:void(0)' onclick='drop( "id_pda", "<?php echo $d_pda['idsind']; ?>", "sendpda", "drop_pda_det", "2")' title="Excluir este PDA">Excluir</a><?php }; ?>
            </p>
            <?php } ?>

            <table class="detal_pda">

                <tr>
                    <td class="pda_p" colspan="2">Número do PDA: <?php echo $numpda ?></td>
                    <td class="pda_p" colspan="2">Data da ocorrência: <?php echo $d_pda['data_ocorrencia'] ?></td>
                    <td class="pda_p" colspan="2">Situação do PDA: <?php echo trata_sit_pda($d_pda['sit_pda']) ?></td>
                </tr>

                <tr>
                    <td class="pda_p" colspan="2"><?php if ($d_pda['sit_pda'] == 2) { ?>Situação d<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L; ?>: <?php echo $d_pda['situacaodet'] ?><?php } ?></td>
                    <td class="pda_p" colspan="2"><?php if ($d_pda['sit_pda'] == 2 && $d_pda['sit_det_pda'] > 2) { ?>Data da reabilitação: <?php echo $d_pda['data_reab_f'] ?><?php } ?></td>
                    <td class="pda_p" colspan="2">ID no sistema: <?php echo $d_pda['idsind'] ?></td>
                </tr>

                <tr>
                    <td class="pda_g" colspan="6">Descrição: <?php echo $d_pda['descr_pda'] ?></td>
                </tr>

                <tr>
                    <td class="pda_m_user_l" colspan="3">CADASTRAMENTO</td>
                    <td class="pda_m_user_l" colspan="3">ÚLTIMA ATUALIZAÇÃO</td>
                </tr>
                <tr>
                    <td class="pda_m_user_f" colspan="3"><?php echo $user_add ?></td>
                    <td class="pda_m_user_f" colspan="3"><?php echo $user_up; ?></td>
                </tr>

            </table>

            <div id="obs"></div>

            <div class="linha">
                OBSERVAÇÕES<?php if ( $n_sind >= 3 ) { ?> - <a href="cadobspda.php?idpda=<?php echo $d_pda['idsind'] ?>" title="Adicionar uma observação para este PDA">Adicionar observação</a> <a href="#" title="Abrir em outra janela" onClick="javascript: ow('cadobspda.php?idpda=<?php echo $d_pda['idsind']; ?>&targ=1', '800', '450'); return false" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>user_popup.png" alt="Abrir em outra janela" class="icon_popup" /></a><?php }; ?>
                <hr />
            </div>
            <?php

            // instanciando o model
            $model = SicopModel::getInstance();

            // executando a query
            $query_obs = $model->query( $query_obs );

            // fechando a conexao
            $model->closeConnection();

            $cont_obs = 0;
            if( $query_obs ) $cont_obs = $query_obs->num_rows;

            if ( $cont_obs < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
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
                    <td class="desc_obs"><?php echo nl2br( $dados_obs['obs_pda'] = preg_replace(' /[\t]/', '&nbsp;&nbsp;&nbsp;&nbsp;', $dados_obs['obs_pda'] ) ) ?></td>
                    <td class="tb_bt">
                    <?php if ( $n_sind >= 3 ) { ?>
                        <a href="editobspda.php?idobs=<?php echo $dados_obs['id_obs_pda']; ?>" title="Alterar esta observação" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" alt="" /></a>
                    <?php }; ?>
                    </td>
                    <td class="tb_bt">
                    <?php if ( $n_sind >= 4 ) { ?>
                        <a href='javascript:void(0)' onclick='drop( "id_obs_pda", "<?php echo $dados_obs['id_obs_pda']; ?>", "sendpdaobs", "drop_obs_pda", "2")' title="Excluir esta observação"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="Excluir esta observação" class="icon_button" /></a>
                    <?php }; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="desc_user">Cadastrado em <?php echo $dados_obs['data_add_fc'] ?>, usuário <?php echo $dados_obs['user_add'] ?><?php if ($dados_obs['user_up'] and $dados_obs['data_up_f']) { ?> - Atualizado em <?php echo $dados_obs['data_up_f'] ?>, usuário <?php echo $dados_obs['user_up'] ?> <?php } ?></td>
                </tr>
                <?php
                    }
                }
                ?>
            </table>

<?php include 'footer.php'; ?>