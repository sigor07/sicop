<?php
if ( !isset( $_SESSION ) ) session_start();

/*ob_start("ob_gzhandler");*/

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_admsist_n = 2;
$n_admsist   = get_session( 'n_admsist', 'int' );

$motivo_pag = 'LISTAR MENSAGENS';

if ($n_admsist < $n_admsist_n) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$iduser = get_session( 'user_id', 'int' );

$q_msg_in = "SELECT
                `msg`.`idmsg`,
                `msg`.`msg_titulo`,
                `msg`.`msg_de`,
                `msg`.`msg_para`,
                `msg`.`msg_de_lida`,
                `msg`.`msg_para_lida`,
                `msg`.`msg_adm_lida`,
                `msg`.`msg_add`,
                DATE_FORMAT(`msg`.`msg_add`, '%d/%m/%Y às %H:%i') AS `msg_add_f`,
                `msg`.`msg_block`,
                `ude`.`nome_cham` AS `nome_de`,
                `upara`.`nome_cham` AS `nome_para`
              FROM
                `msg`
                INNER JOIN `sicop_users` `ude` ON `msg`.`msg_de` = `ude`.`iduser`
                INNER JOIN `sicop_users` `upara` ON `msg`.`msg_para` = `upara`.`iduser`
              ORDER BY
                `msg`.`msg_add` DESC";

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);


require 'cab.php';
$trail = new Breadcrumb();
$trail->add('Lista de mensagens', $_SERVER['PHP_SELF'], 2);
$trail->output();
?>

            <p class="descript_page">Lista de mensagens</p>

            <?php

            // instanciando o model
            $model = SicopModel::getInstance();

            // executando a query
            $q_msg_in = $model->query( $q_msg_in );

            // fechando a conexao
            $model->closeConnection();

            $cont_msg_in = 0;

            if( $q_msg_in ) $cont_msg_in = $q_msg_in->num_rows;

            if ( $cont_msg_in < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Nenhuma mensagem.</p>';
            } else {
                ?>
                <table class="lista_busca">
                    <tr>
                        <th class="num_od" scope="col">&nbsp;</th>
                        <th class="msg_user" scope="col">De</th>
                        <th class="msg_user" scope="col">Para</th>
                        <th class="msg_ass" scope="col">Assunto</th>
                        <th class="desc_data" scope="col">Data / hora</th>
                    </tr>
                    <?php
                    while ( $d_msg_in = $q_msg_in->fetch_assoc(  ) ) {

                        $plida = $d_msg_in['msg_para_lida'];
                        $dlida = $d_msg_in['msg_de_lida'];
                        $alida = $d_msg_in['msg_adm_lida'];

                        $pneg = '';
                        $pneg_f = '';

                        $dneg = '';
                        $dneg_f = '';

                        $img = 'msg_read';

                        if ( $plida == 0 ) {
                            $pneg = '<b>';
                            $pneg_f = '</b>';
                        }

                        if ( $dlida == 0 ) {
                            $dneg = '<b>';
                            $dneg_f = '</b>';
                        }

                        if ( $alida == 0 ) {
                            $img = 'msg_new';
                        } else if ( $d_msg_in['msg_block'] == 1 ) {
                            $img = 'msg_alert';
                        }
                        ?>
                        <tr class="even">
                            <td class="num_od"><img src="<?php echo SICOP_SYS_IMG_PATH; ?><?php echo $img; ?>.png" alt="" width="20" height="13" /></td>
                            <td class="msg_user"><?php echo $dneg; ?><?php echo $d_msg_in['nome_de']; ?><?php echo $dneg_f; ?></td>
                            <td class="msg_user"><?php echo $pneg; ?><?php echo $d_msg_in['nome_para']; ?><?php echo $pneg_f; ?></td>
                            <td class="msg_ass"><a href="detalmsg.php?idmsg=<?php echo $d_msg_in['idmsg']; ?>"><?php echo $d_msg_in['msg_titulo']; ?></a></td>
                            <td class="desc_data" align="center"><?php echo $d_msg_in['msg_add_f']; ?></td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } ?>

<?php include 'footer.php';?>