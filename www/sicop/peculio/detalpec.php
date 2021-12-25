<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$imp_peculio     = get_session( 'imp_peculio', 'int' );
$n_peculio       = get_session( 'n_peculio', 'int' );
$n_peculio_baixa = get_session( 'n_peculio_baixa', 'int' );
$n_peculio_n     = 2;

if ( $n_peculio < $n_peculio_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'PECÚLIO';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$iddet = empty( $_GET['iddet'] ) ? '' : (int)$_GET['iddet'];

if ( empty( $iddet ) ) {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso direto à página da grade.\n\n Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( '', 1 );
    exit;
}

$q_pec = "SELECT
            `peculio`.`idpeculio`,
            `peculio`.`cod_detento`,
            `peculio`.`descr_peculio`,
            `peculio`.`retirado`,
            `peculio`.`confirm`,
            `peculio`.`user_add`,
            `peculio`.`data_add`,
            DATE_FORMAT( `peculio`.`data_add`, '%d/%m/%Y' ) AS data_add_f,
            DATE_FORMAT( `peculio`.`data_add`, '%d/%m/%Y às %H:%i' ) AS data_add_fc,
            `peculio`.`ip_add`,
            `peculio`.`user_conf`,
            `peculio`.`data_conf`,
            DATE_FORMAT( `peculio`.`data_conf`, '%d/%m/%Y às %H:%i' ) AS data_conf_f,
            `peculio`.`user_up`,
            `peculio`.`data_up`,
            DATE_FORMAT( `peculio`.`data_up`, '%d/%m/%Y às %H:%i' ) AS data_up_f,
            `peculio`.`ip_up`,
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

$q_pec_ret = "SELECT
                `peculio`.`idpeculio`,
                `peculio`.`cod_detento`,
                `peculio`.`descr_peculio`,
                `peculio`.`retirado`,
                `peculio`.`user_add`,
                `peculio`.`data_add`,
                DATE_FORMAT( `peculio`.`data_add`, '%d/%m/%Y' ) AS data_add_f,
                DATE_FORMAT( `peculio`.`data_add`, '%d/%m/%Y às %H:%i' ) AS data_add_fc,
                `peculio`.`ip_add`,
                `peculio`.`user_conf`,
                `peculio`.`data_conf`,
                 DATE_FORMAT( `peculio`.`data_conf`, '%d/%m/%Y às %H:%i' ) AS data_conf_f,
                `peculio`.`user_up`,
                `peculio`.`data_up`,
                DATE_FORMAT( `peculio`.`data_up`, '%d/%m/%Y às %H:%i' ) AS data_up_f,
                `peculio`.`ip_up`,
                `peculio`.`obs_ret`,
                `tipopeculio`.`tipo_peculio`
              FROM
                `peculio`
                INNER JOIN `tipopeculio` ON `peculio`.`cod_tipo_peculio` = `tipopeculio`.`idtipopeculio`
              WHERE
                `peculio`.`cod_detento` = $iddet
                AND
                `peculio`.`retirado` = TRUE
              ORDER BY
                `peculio`.`data_add`";

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Pertences e pecúlio';

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( 'Pecúlio e pertences', $pag_atual, 4 );
$trail->output();
?>

            <p class="descript_page">PECÚLIO E PERTENCES</p>

            <?php include 'quali/det_cad.php'; ?>

            <div class="linha">
                PERTENCES<?php if ( $n_peculio >= 3 ) { ?> - <a href="cadpert.php?iddet=<?php echo $iddet; ?>" title="Cadastrar um novo pertence">Adicionar pertences</a> <a href="#" title="Abrir em outra janela" onClick="javascript: ow('cadpert.php?iddet=<?php echo $iddet; ?>&targ=1', '800', '350'); return false"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>user_popup.png" alt="Abrir em outra janela" class="icon_popup" /></a><?php }; ?>
                         <?php if ( $imp_peculio >= 1 ) { ?> - <a href="print_pec.php?iddet=<?php echo $iddet ?>" title="Imprimir itens deste detento">Imprimir</a> <a href="#" title="Abrir em outra janela" onClick="javascript: ow('print_pec.php?iddet=<?php echo $iddet; ?>&targ=1', '830', '600'); return false"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>user_popup.png" alt="Abrir em outra janela" class="icon_popup" /></a><?php }; ?>
                         <?php if ( $n_peculio_baixa >= 1 ) {?> - <a href="baixapert.php?iddet=<?php echo $iddet; ?>" title="Dar baixa nos pertences deste detento">Baixar pertences</a> <?php } ?>
                <hr />
            </div>

            <?php

            // instanciando o model
            $model = SicopModel::getInstance();

            // executando a query
            $q_pec = $model->query( $q_pec );

            // fechando a conexao
            $model->closeConnection();

            $cont_pec = 0;

            if( $q_pec ) $cont_pec = $q_pec->num_rows;

            if ( $cont_pec < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não há pertences cadastrados.</p>';
            } else {
            ?>

            <table class="lista_busca">
                <tr >
                    <th class="desc_data">DATA</th>
                    <th class="tipo_pec">TIPO</th>
                    <th class="desc_pec_sml">DESCRIÇÃO</th>
                    <th class="tb_bt">&nbsp;</th>
                    <th class="tb_bt">&nbsp;</th>
                    <th class="tb_bt">&nbsp;</th>
                </tr>

                <?php
                while ( $d_pec = $q_pec->fetch_assoc() ) {

                    $corfont_pec = '#000000';
                    $img_botao   = SICOP_SYS_IMG_PATH . 's_add_g.png';
                    $text_alt    = 'Este pertence já está confirmado';

                    if ( $d_pec['confirm'] == 0 ) {
                        $corfont_pec = '#FF0000';
                        $img_botao   = SICOP_SYS_IMG_PATH . 's_add.png';
                        $text_alt    = 'Confirmar este pertence';
                    }
                ?>

                <tr class="even">
                    <td class="desc_data"><font color="<?php echo $corfont_pec; ?>"><?php echo $d_pec['data_add_f'] ?></font></td>
                    <td class="tipo_pec"><font color="<?php echo $corfont_pec; ?>"><?php echo $d_pec['tipo_peculio'] ?></font></td>
                    <td class="desc_pec_sml"><font color="<?php echo $corfont_pec; ?>"><?php echo nl2br( $d_pec['descr_peculio'] ) ?></font></td>
                    <td class="tb_bt">
                        <?php if ( $n_peculio >= 3 ) {
                        if ( $d_pec['confirm'] == 0 ) { ?>
                        <a href='javascript:void(0)' onclick='conf_pert( <?php echo $d_pec['idpeculio']; ?> )' title="Confirmar este pertence" >
                        <?php } ?>
                        <img src="<?php echo $img_botao ?>" alt="<?php echo $text_alt ?>" class="icon_button" />
                        <?php if ( $d_pec['confirm'] == 0 ) { ?></a><?php } }; ?>
                    </td>
                    <td class="tb_bt"><?php if ( $n_peculio >= 3 ) { ?> <a href="editpert.php?idpec=<?php echo $d_pec['idpeculio']; ?>" title="Alterar este pertence" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" alt="Alterar este pertence" /></a> <?php }; ?></td>
                    <td class="tb_bt"><?php if ( $n_peculio >= 4 ) { ?> <a href='javascript:void(0)' onclick='drop( "idpec", "<?php echo $d_pec['idpeculio']; ?>", "sendpeculio", "drop_pec", "2")' title="Excluir este pertence"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="Excluir este pertence" class="icon_button" /></a><?php }; ?></td>
                </tr>
                <tr>
                    <td colspan="6" class="desc_user">
                        Cadastrado em <?php echo $d_pec['data_add_fc'] ?>, usuário <?php echo $d_pec['user_add'] ?>
                        <?php if ( $d_pec['user_conf'] and $d_pec['data_conf_f'] ) { ?>
                        - Confirmado em <?php echo $d_pec['data_conf_f'] ?>, usuário <?php echo $d_pec['user_conf'] ?>
                        <?php } ?>
                        <?php if ( $d_pec['user_up'] and $d_pec['data_up_f'] ) { ?>
                        - Atualizado em <?php echo $d_pec['data_up_f'] ?>, usuário <?php echo $d_pec['user_up'] ?>
                        <?php } ?>
                    </td>
                </tr>
                <?php } // fim do while ?>
            </table>
            <?php } // fim do if que conta o número de ocorrencias ?>

            <div id="pertences"></div>

            <div class="linha">
                PERTENCES RETIRADOS
                <hr />
            </div>

            <?php

            // instanciando o model
            $model = SicopModel::getInstance();

            // executando a query
            $q_pec_ret = $model->query( $q_pec_ret );

            // fechando a conexao
            $model->closeConnection();

            $cont_pec_ret = 0;

            if( $q_pec_ret ) $cont_pec_ret = $q_pec_ret->num_rows;

            if ( $cont_pec_ret < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não há pertences retirados.</p>';
            } else {
            ?>

            <table class="lista_busca">
                <tr >
                    <th class="desc_data">DATA</th>
                    <th class="tipo_pec">TIPO</th>
                    <th class="desc_pec_inc">DESCRIÇÃO</th>
                    <th class="tb_bt">&nbsp;</th>
                    <th class="tb_bt">&nbsp;</th>
                </tr>

                <?php while ( $d_pec_ret = $q_pec_ret->fetch_assoc() ) { ?>

                <tr class="even">
                    <td class="desc_data"><?php echo $d_pec_ret['data_add_f'] ?></td>
                    <td class="tipo_pec"><?php echo $d_pec_ret['tipo_peculio'] ?></td>
                    <td class="desc_pec_inc"><?php echo nl2br( $d_pec_ret['descr_peculio'] ) ?></td>
                    <td class="tb_bt"><?php if ( $n_peculio >= 4 ) { ?><a href="editpert.php?idpec=<?php echo $d_pec_ret['idpeculio']; ?>" title="Alterar este pertence" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" alt="Alterar este pertence" /></a><?php }; ?></td>
                    <td class="tb_bt"><?php if ( $n_peculio >= 4 ) { ?><a href='javascript:void(0)' onclick='drop_pec(<?php echo $iddet; ?>, <?php echo $d_pec_ret['idpeculio']; ?>)' title="Excluir este pertence"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="Excluir este pertence" /></a><?php }; ?></td>
                </tr>
                <tr class="even">
                    <td class="pec_obs_rt" colspan="2">Observações de retirada</td>
                    <td class="pec_obs_rt" colspan="3"><?php echo $d_pec_ret['obs_ret'] ?></td>
                </tr>
                <tr>
                    <td colspan="5" class="desc_user">
                        Cadastrado em <?php echo $d_pec_ret['data_add_fc'] ?>, usuário <?php echo $d_pec_ret['user_add'] ?><?php if ( $d_pec_ret['user_conf'] and $d_pec_ret['data_conf_f'] ) { ?> - Confirmado em <?php echo $d_pec_ret['data_conf_f'] ?>, usuário <?php echo $d_pec_ret['user_conf'] ?><?php } ?><?php if ( $d_pec_ret['user_up'] and $d_pec_ret['data_up_f'] ) { ?> - Atualizado em <?php echo $d_pec_ret['data_up_f'] ?>, usuário <?php echo $d_pec_ret['user_up'] ?> <?php } ?>
                    </td>
                </tr>

                <?php } // fim do while ?>

            </table>

            <?php } // fim do if que conta o número de ocorrencias?>

<?php include 'footer.php'; ?>