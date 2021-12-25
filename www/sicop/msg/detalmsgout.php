<?php
if ( !isset( $_SESSION ) ) session_start();

/*ob_start("ob_gzhandler");*/

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_msg_n = 2;
$n_msg   = get_session( 'n_msg', 'int' );

if ( $n_msg < $n_msg_n ) {

    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = 'MENSAGEM - ENVIADA';
    get_msg( $msg, 1 );

    exit;

}

$iduser = get_session( 'user_id', 'int' );

$is_post = is_post();

if ( $is_post ) {

    extract( $_POST, EXTR_OVERWRITE );

    $idmsg = empty( $idmsg ) ? '' : (int)$idmsg;

    if ( empty( $idmsg ) ) {
        header( "Location: msg.php" );
        exit;
    }

    $query_msg = "UPDATE `msg` SET `msg_de_exc` = 1, `msg_de_exdata` = NOW() WHERE `idmsg` = $idmsg AND `msg_de` = $iduser LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $model->query( $query_msg );

    // fechando a conexao
    $model->closeConnection();


    $q_s_msg = "SELECT
                  `msg`.`idmsg`,
                  `msg`.`msg_titulo`,
                  `ude`.`nome_cham` AS `nome_de`,
                  `upara`.`nome_cham` AS `nome_para`
                FROM
                  `msg`
                  INNER JOIN `sicop_users` `ude` ON `msg`.`msg_de` = `ude`.`iduser`
                  INNER JOIN `sicop_users` `upara` ON `msg`.`msg_para` = `upara`.`iduser`
                WHERE
                  `idmsg` = $idmsg
                  AND
                  `msg_de` = $iduser
                LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_s_msg = $model->query( $q_s_msg );

    // fechando a conexao
    $model->closeConnection();

    $d_msg = $q_s_msg->fetch_assoc();
    $msg_ex = 'De: ' . $d_msg['nome_de'] . '; Para: ' . $d_msg['nome_para'] . '; Assunto: ' . $d_msg['msg_titulo'] . '; ID da msg: ' . $d_msg['idmsg'] . " \n ";

    $mensagem = "[ MENSAGEM MARCADA COMO EXCLUIDA ]\n Marcação de exclusão de mensagens: ID da mensagem: $idmsg. \n\n [ DADOS DA MENSAGEM EXCLUIDA ]\n $msg_ex \r\n";
    salvaLog($mensagem);

    header('Location: msg.php');
    exit;

}

$idmsg = get_get( 'idmsg', 'int' );

if ( empty( $idmsg ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso direto à página ( IDENTIFICADOR DA MENSAGEM EM BRANCO - DETALHES DA MENSAGEM - ENVIADA ).';
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$q_msg = "SELECT
            `msg`.`idmsg`,
            `msg`.`msg_titulo`,
            `msg`.`msg_corpo`,
            `msg`.`msg_de`,
            `msg`.`msg_para`,
            `msg`.`msg_de_lida`,
            `msg`.`msg_de_vdata`,
            DATE_FORMAT(`msg`.`msg_de_vdata`, '%d/%m/%Y às %H:%i') AS `msg_de_vdata`,
            DATE_FORMAT(`msg`.`msg_de_ultdata`, '%d/%m/%Y às %H:%i') AS `msg_de_ultdata`,
            `msg`.`msg_add`,
            DATE_FORMAT(`msg`.`msg_add`, '%d/%m/%Y às %H:%i') AS `msg_add_f`,
            `ude`.`nome_cham` AS `nome_de`,
            `upara`.`nome_cham` AS `nome_para`
          FROM
            `msg`
            INNER JOIN `sicop_users` `ude` ON `msg`.`msg_de` = `ude`.`iduser`
            INNER JOIN `sicop_users` `upara` ON `msg`.`msg_para` = `upara`.`iduser`
          WHERE
            `idmsg` = $idmsg
            AND
            `msg_de` = $iduser
            AND
            `msg_de_exc` = FALSE
            AND
            `msg_block` = FALSE
          ORDER BY
            `msg`.`msg_add` DESC";

$motivo_pag = 'DETALHES DA MENSAGEM - ENVIADA';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_msg = $model->query( $q_msg );

// fechando a conexao
$model->closeConnection();

if ( !$q_msg ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "Falha na consulta ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 1 );
    exit;

}

$cont = $q_msg->num_rows;

if ( $cont < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "A consulta retornou 0 ocorrências ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 1 );
    exit;

}

$d_msg = $q_msg->fetch_assoc();

$lida = $d_msg['msg_de_lida'];

$q_msg_up = "UPDATE `msg` SET `msg_de_ultdata` = NOW() WHERE `idmsg` = $idmsg LIMIT 1";

if ( $lida == 0 ) {
    $q_msg_up = "UPDATE `msg` SET `msg_de_lida` = 1, `msg_de_vdata` = NOW(), `msg_de_ultdata` = NOW() WHERE `idmsg` = $idmsg LIMIT 1";
}

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$model->query( $q_msg_up );

// fechando a conexao
$model->closeConnection();

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Ler mensagem';

require 'cab.php';
$trail = new Breadcrumb();
$trail->add( $desc_pag, $_SERVER['PHP_SELF'], 3);
$trail->output();
?>

            <script type="text/javascript" >
                function confirmExcl(){
                    return confirm("Deseja realmente excluir esta mensagem?")
                }
            </script>

            <p class="descript_page">MENSAGEM ENVIADA</p>

            <?php if ( $n_msg >= 3 ){?><p class="link_common"><a href="wmsg.php">Escrever mensagem</a></p><?php } ?>


            <table  class="user_msg">
                <tr>
                    <td>De: <?php echo $d_msg['nome_de']; ?></td>
                </tr>
                <tr>
                    <td>Data: <?php echo $d_msg['msg_add_f']; ?></td>
                </tr>
                <tr>
                    <td>Assunto: <?php echo $d_msg['msg_titulo']; ?></td>
                </tr>
            </table>

            <p class="table_leg">Mensagem:</p>

            <table class="detal_msg">
                <tr >
                    <td class="msg_corpo"><?php echo nl2br( $d_msg['msg_corpo'] ); ?></td>
                </tr>
                <tr>
                    <td class="desc_user" ><?php if ( $d_msg['msg_de_vdata'] and $d_msg['msg_de_ultdata'] ) { ?> Lida em em <?php echo $d_msg['msg_de_vdata'] ?>, última visualização em <?php echo $d_msg['msg_de_ultdata'] ?> <?php } ?></td>
                </tr>
            </table>

            <?php if ( $n_msg >= 3 ){?>
            <form action="detalmsgin.php" method="post" name="deletamsg" id="deletamsg" onSubmit="return confirmExcl()">
                <input name="idmsg" type="hidden" id="idmsg" value="<?php echo $d_msg['idmsg']; ?>" />
            <?php } ?>
                <div class="form_bts">
                    <input class="form_bt" name="" type="button" onClick="history.go(-1)" value="Voltar" />
                    <?php if ( $n_msg >= 3 ) { ?>
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Excluir" />
                    <?php } ?>
                </div>
            <?php if ( $n_msg >= 3 ){?>
            </form>
            <?php } ?>

<?php include 'footer.php';?>