<?php
if ( !isset( $_SESSION ) ) session_start();

/*ob_start("ob_gzhandler");*/

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_admsist_n = 2;
$n_admsist   = get_session( 'n_admsist', 'int' );

if ( $n_admsist < $n_admsist_n ) {

    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = 'DETALHES DA MENSAGEM';
    get_msg( $msg, 1 );

    exit;

}

$iduser = get_session( 'user_id', 'int' );

$idmsg = get_get( 'idmsg', 'int' );
if ( empty( $idmsg ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso direto à página ( IDENTIFICADOR DA MENSAGEM EM BRANCO - DETALHES DA MENSAGEM ).';
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
            `msg`.`msg_para_lida`,
            `msg`.`msg_adm_lida`,
            `msg`.`msg_de_exc`,
            `msg`.`msg_para_exc`,
            DATE_FORMAT(`msg`.`msg_de_vdata`, '%d/%m/%Y às %H:%i') AS `msg_de_vdata`,
            DATE_FORMAT(`msg`.`msg_para_vdata`, '%d/%m/%Y às %H:%i') AS `msg_para_vdata`,
            DATE_FORMAT(`msg`.`msg_adm_vdata`, '%d/%m/%Y às %H:%i') AS `msg_adm_vdata`,
            DATE_FORMAT(`msg`.`msg_de_ultdata`, '%d/%m/%Y às %H:%i') AS `msg_de_ultdata`,
            DATE_FORMAT(`msg`.`msg_para_ultdata`, '%d/%m/%Y às %H:%i') AS `msg_para_ultdata`,
            DATE_FORMAT(`msg`.`msg_adm_ultdata`, '%d/%m/%Y às %H:%i') AS `msg_adm_ultdata`,
            DATE_FORMAT(`msg`.`msg_de_exdata`, '%d/%m/%Y às %H:%i') AS `msg_de_exdata`,
            DATE_FORMAT(`msg`.`msg_para_exdata`, '%d/%m/%Y às %H:%i') AS `msg_para_exdata`,
            DATE_FORMAT(`msg`.`msg_add`, '%d/%m/%Y às %H:%i') AS `msg_add`,
            `msg`.`msg_block`,
            `ude`.`nome_cham` AS `nome_de`,
            `upara`.`nome_cham` AS `nome_para`
          FROM
            `msg`
            INNER JOIN `sicop_users` `ude` ON `msg`.`msg_de` = `ude`.`iduser`
            INNER JOIN `sicop_users` `upara` ON `msg`.`msg_para` = `upara`.`iduser`
          WHERE
            `idmsg` = $idmsg";

$motivo_pag = 'DETALHES DA MENSAGEM';

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

$lida = $d_msg['msg_adm_lida'];

$q_msg_up = "UPDATE `msg` SET `msg_adm_ultdata` = NOW() WHERE `idmsg` = $idmsg LIMIT 1";

if ( $lida == 0 ) {
    $q_msg_up = "UPDATE `msg` SET `msg_adm_lida` = 1, `msg_adm_vdata` = NOW(), `msg_adm_ultdata` = NOW() WHERE `idmsg` = $idmsg LIMIT 1";
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

            <p class="descript_page">DETALHES DA MENSAGEM</p>

            <p class="link_common"><a href="wmsg.php">Escrever mensagem</a></p>

            <table  class="user_msg">
                <tr>
                    <td>De: <?php echo $d_msg['nome_de']; ?></td>
                </tr>
                <tr>
                    <td>Para: <?php echo $d_msg['nome_para']; ?></td>
                </tr>
                <tr>
                    <td>Data: <?php echo $d_msg['msg_add']; ?></td>
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
                    <td class="desc_user" ><?php if ( $d_msg['msg_adm_vdata'] and $d_msg['msg_adm_ultdata'] ) { ?> Lida em em <?php echo $d_msg['msg_adm_vdata'] ?>, última visualização em <?php echo $d_msg['msg_adm_ultdata'] ?> <?php } ?></td>
                </tr>
            </table>

            <p class="table_leg">Detalhes da mensagem</p>

            <div class="dados_msg">
                <p>Data do envio: <?php echo $d_msg['msg_add']; ?></p>
                <p class="dm_q">Bloqueada: <?php echo tratasn( $d_msg['msg_block'] ); ?></p>

                <p>Remetente: <?php echo $d_msg['nome_de']; ?></p>
                <p>Lida pelo remetente: <?php echo tratasn( $d_msg['msg_de_lida'] ); ?><?php if ( $d_msg['msg_de_lida'] == 1 ) { ?>, em <?php echo $d_msg['msg_de_vdata'] ?><?php } ?><?php if ( !empty( $d_msg['msg_de_ultdata'] ) ) { ?>, última visualização em <?php echo $d_msg['msg_de_ultdata'] ?><?php } ?></p>
                <p class="dm_q">Marcada como excluída: <?php echo tratasn( $d_msg['msg_de_exc'] ); ?><?php if ( $d_msg['msg_de_exc'] == 1 ) { ?>, em <?php echo $d_msg['msg_de_exdata'] ?><?php } ?></p>

                <p>Destinatário: <?php echo $d_msg['nome_para']; ?></p>
                <p>Lida pelo destinatário: <?php echo tratasn( $d_msg['msg_para_lida'] ); ?><?php if ( $d_msg['msg_para_lida'] == 1 ) { ?>, em <?php echo $d_msg['msg_para_vdata'] ?><?php } ?><?php if ( !empty( $d_msg['msg_para_ultdata'] ) ) { ?>, última visualização em <?php echo $d_msg['msg_para_ultdata'] ?><?php } ?></p>
                <p>Marcada como excluída: <?php echo tratasn( $d_msg['msg_para_exc'] ); ?><?php if ( $d_msg['msg_para_exc'] == 1 ) { ?>, em <?php echo $d_msg['msg_para_exdata'] ?><?php } ?></p>

            </div>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendmsg.php" method="post" name="deletamsg" id="deletamsg" onSubmit="return confirmExcl()">

                <input name="proced" type="hidden" id="proced" value="2" />
                <input name="idmsg" type="hidden" id="idmsg" value="<?php echo $d_msg['idmsg'];?>" />

                <div class="form_bts">
                    <input class="form_bt" name="" type="button" onClick="history.go(-1)" value="Voltar" />
                    <input class="form_bt" name="" type="button" onclick="javascript: location.href='edmsg.php?idmsg=<?php echo $d_msg['idmsg'];?>';" value="Editar" />
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Excluir" />
                </div>

            </form>

<?php include 'footer.php'; ?>