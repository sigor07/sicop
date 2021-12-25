<?php
if ( !isset( $_SESSION ) ) session_start();

$pag = link_pag();
$tipo = '';

$n_msg_n = 2;
$n_msg = get_session( 'n_msg', 'int' );

if ( $n_msg < $n_msg_n ) {
    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
    salvaLog( $mensagem );
    exit;
}

$iduser = get_session( 'user_id', 'int' );

$q_msg_in = "SELECT
                `msg`.`idmsg`,
                `msg`.`msg_titulo`,
                `msg`.`msg_de`,
                `msg`.`msg_para_lida`,
                `msg`.`msg_add`,
                DATE_FORMAT(`msg`.`msg_add`, '%d/%m/%Y às %H:%i') AS `msg_add_f`,
                `ude`.`nome_cham` AS `nome_de`,
                `upara`.`nome_cham` AS `nome_para`
              FROM
                `msg`
                INNER JOIN `sicop_users` `ude` ON `msg`.`msg_de` = `ude`.`iduser`
                INNER JOIN `sicop_users` `upara` ON `msg`.`msg_para` = `upara`.`iduser`
              WHERE
                `msg_para` = $iduser
                AND
                `msg_para_exc` = FALSE
                AND
                `msg_block` = FALSE
              ORDER BY
                `msg`.`msg_add` DESC";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_msg_in = $model->query( $q_msg_in );

// fechando a conexao
$model->closeConnection();

$cont_msg_in = 0;

if( $q_msg_in ) $cont_msg_in = $q_msg_in->num_rows;


$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

?>

<script type="text/javascript" >
    function confirmExcl(){
        return confirm("Deseja realmente excluir os itens marcados?")
    }
</script>

<p class="descript_page">Mensagens recebidas</p>

<p class="link_common">Mensagens recebidas | <a href="msg.php?p=env">Mensagens enviadas</a><?php if ( $n_msg >= 3 ) { ?> | <a href="wmsg.php">Escrever mensagem</a><?php } ?></p>

<?php

if ( $cont_msg_in < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
    echo '<p class="p_q_no_result">Nenhuma mensagem.</p>';
} else {
    ?>

    <?php if ( $n_msg >= 3 ) { ?><form action="<?php echo SICOP_ABS_PATH ?>send/sendmsg.php" method="post" name="deletamsg" id="deletamsg" onSubmit="return confirmExcl()"><?php } ?>

        <table class="lista_busca">

            <tr>
                <th class="num_od" scope="col">&nbsp;</th>
                <th class="msg_user" scope="col">De</th>
                <th class="msg_ass" scope="col">Assunto</th>
                <th class="desc_data" scope="col">Data / hora</th>
                <th class="tb_ck" scope="col">&nbsp;</th>
            </tr>

            <?php
            while ( $d_msg_in = $q_msg_in->fetch_assoc() ) {

                $lida = $d_msg_in['msg_para_lida'];

                $neg = '';
                $neg_f = '';
                $img = 'msg_read';

                if ( $lida == 0 ) {
                    $neg = '<b>';
                    $neg_f = '</b>';
                    $img = 'msg_new';
                }
                ?>

                <tr class="even">
                    <td class="num_od"><img src="<?php echo SICOP_SYS_IMG_PATH; ?><?php echo $img; ?>.png" alt="" width="20" height="13" /></td>
                    <td class="msg_user"><?php echo $neg; ?><?php echo $d_msg_in['nome_de']; ?><?php echo $neg_f; ?></td>
                    <td class="msg_ass"><?php echo $neg; ?><a href="detalmsgin.php?idmsg=<?php echo $d_msg_in['idmsg']; ?>"><?php echo $d_msg_in['msg_titulo']; ?></a><?php echo $neg_f; ?></td>
                    <td class="desc_data"><?php echo $neg; ?><?php echo $d_msg_in['msg_add_f']; ?><?php echo $neg_f; ?></td>
                    <td class="tb_ck"><?php if ( $n_msg >= 3 ) { ?><input name="exc[]" type="checkbox" id="exc" value="<?php echo $d_msg_in['idmsg']; ?>" /><?php } ?></td>
                </tr>

            <?php } // fim do while ?>

            <?php if ( $n_msg >= 3 ) { ?>
                <tr >
                    <td height="38" colspan="5" align="right">
                        <input class="form_bt" type="submit" name="submit" id="submit" value="Excluir marcadas" />
                        <input type="hidden" name="proced" id="proced" value="4" />
                        <input type="hidden" name="tipo_msg" id="tipo_msg" value="1" />
                    </td>
                </tr>
            <?php } // fim do if que verifica as permissões  ?>
        </table>
    <?php } // fim do if else que verifica a quantidade  ?>

</form>
