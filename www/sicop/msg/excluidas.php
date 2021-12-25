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

$q_msg = "SELECT
                    msg.idmsg,
                    msg.msg_titulo,
                    msg.msg_de,
                    msg.msg_para,
                    msg.msg_para_lida,
                    msg.msg_para_vdata,
                    DATE_FORMAT(msg.`msg_para_vdata`, '%d/%m/%Y às %H:%i') AS msg_para_vdata,
                    msg.msg_add,
                    DATE_FORMAT(msg.`msg_add`, '%d/%m/%Y às %H:%i') AS msg_add_f,
                    ude.nome_cham AS nome_de,
                    upara.nome_cham AS nome_para
                  FROM
                    msg
                    INNER JOIN sicop_users ude ON msg.msg_de = ude.iduser
                    INNER JOIN sicop_users upara ON msg.msg_para = upara.iduser
                  WHERE
                  ( `msg_de` = $iduser AND `msg_de_exc` = TRUE AND `msg_de_del` = FALSE AND `msg_block` = FALSE )
                  OR
                  ( `msg_para` = $iduser AND `msg_para_exc` = TRUE AND `msg_para_del` = FALSE AND `msg_block` = FALSE )
                  ORDER BY msg.`msg_add` DESC";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_msg = $model->query( $q_msg );

// fechando a conexao
$model->closeConnection();

$cont_msg = 0;

if( $q_msg ) $cont_msg = $q_msg->num_rows;

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );
?>
<p class="descript_page">Mensagens excluidas</p>
<p class="link_common">
    <a href="msg.php">Mensagens recebidas</a> | <a href="msg.php?p=env">Mensagens enviadas</a> | Mensagens excluidas | <a href="wmsg.php">Escrever mensagem</a>
</p>

<?php

if ( $cont_msg < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
    echo '<p class="p_q_no_result">Nenhuma mensagem.</p>';
} else {
    ?>
    <table class="lista_busca">
        <tr>
            <th class="num_od" scope="col">&nbsp;</th>
            <th class="msg_user" scope="col">De</th>
            <th class="msg_ass" scope="col">Assunto</th>
            <th class="desc_data" scope="col">Data / hora</th>
        </tr>
        <?php
        while ( $d_msg = $q_msg->fetch_assoc() ) {

            $recebida = false;

            if ( $d_msg['msg_para'] == $iduser )
                $recebida = true;

            $neg = '';
            $neg_f = '';
            $img = '<img src="' . SICOP_SYS_IMG_PATH . 'email_send.png" alt="" width="25" height="20" />';
            $link = 'detalmsgout.php?idmsg=' . $d_msg['idmsg'];


            if ( $recebida ) {

                $img = '<img src="' . SICOP_SYS_IMG_PATH . 'email_rec.png" alt="" width="25" height="20" />';
                $link = 'detalmsgin.php?idmsg=' . $d_msg['idmsg'];

                $lida = $d_msg['msg_para_lida'];

                if ( $lida == 0 ) {
                    $neg = '<b>';
                    $neg_f = '</b>';
                }
            }
            ?>
            <tr class="even">
                <td class="num_od"><?php echo $img; ?></td>
                <td class="msg_user"><?php echo $neg; ?><?php echo $d_msg['nome_de']; ?><?php echo $neg_f; ?></td>
                <td class="msg_ass"><?php echo $neg; ?><a href="<?php echo $link; ?>"><?php echo $d_msg['msg_titulo']; ?></a><?php echo $neg_f; ?></td>
                <td class="desc_data"><?php echo $neg; ?><?php echo $d_msg['msg_add_f']; ?><?php echo $neg_f; ?></td>
            </tr>
        <?php } ?>
    </table>
<?php } ?>
