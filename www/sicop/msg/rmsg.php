<?php
if ( !isset( $_SESSION ) ) session_start();

/*ob_start("ob_gzhandler");*/

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

keepHistory();

$url_ant = returnHistory();

$n_msg_n = 3;
$n_msg   = get_session( 'n_msg', 'int' );

$motivo_pag = 'MENSAGEM';

if ($n_msg < $n_msg_n) {

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

$idmsg = get_get( 'idmsg', 'int' );

if ( empty( $idmsg ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( IDENTIFICADOR EM BRANCO - $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$q_msg = "SELECT
                msg.idmsg,
                msg.msg_titulo,
                msg.msg_corpo,
                msg.msg_de,
                msg.msg_para,
                ude.nome_cham AS nome_de,
                upara.nome_cham AS nome_para
              FROM
                msg
                INNER JOIN sicop_users ude ON msg.msg_de = ude.iduser
                INNER JOIN sicop_users upara ON msg.msg_para = upara.iduser
              WHERE idmsg = $idmsg AND msg_para = $iduser AND msg_para_exc = FALSE AND msg_block = FALSE
              ORDER BY msg.msg_add DESC";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_msg = $model->query( $q_msg );

// fechando a conexao
$model->closeConnection();

if( !$q_msg ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_msg = $q_msg->num_rows;

if ( $cont_msg < 1 ) {
    $mensagem = "A consulta retornou 0 ocorrencias.\n\n Página $pag";
    salvaLog( $mensagem );
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$d_msg = $q_msg->fetch_assoc();

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

// adicionando o javascript
$cab_js   = array();
$cab_js[] = 'valida.js';
$cab_js[] = 'ckeditor/ckeditor.js';
set_cab_js( $cab_js );

require 'cab.php';
$trail = new Breadcrumb();
$trail->add('Escrever mensagem', $_SERVER['PHP_SELF'], 3);
$trail->output();
?>

            <p class="descript_page">NOVA RESPOSTA À MENSAGEM</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendmsg.php" method="post" name="deletamsg" id="deletamsg" onsubmit="return validamsg();">

                <table class="busca_form">
                    <tr>
                        <td width="56" height="20">Para: </td>
                        <td width="437"><?php echo $d_msg['nome_de']; ?></td>
                    </tr>
                    <tr>
                        <td height="20">Assunto: </td>
                        <td height="20"><input name="msg_titulo" type="text" class="CaixaTexto" id="msg_titulo" value="<?php echo 'RE: ' . $d_msg['msg_titulo']; ?>" size="70" maxlength="200" /></td>
                    </tr>
                </table>

                <p class="table_leg">Mensagem</p>

                <div align="center">
                    <textarea name="msg_corpo" cols="80" rows="5" class="CaixaTexto" id="msg_corpo"><?php echo '<p></p> <hr /> Em resposta à: <br /> <em>' . $d_msg['msg_corpo'] . '</em>'; ?></textarea>
                </div>

                <input name="proced" type="hidden" id="proced" value="3" />
                <input name="msg_para" type="hidden" id="msg_para" value="<?php echo $d_msg['msg_de']; ?>" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Enviar" />
                    <input class="form_bt" type="button" name="" onClick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

            <script type="text/javascript">
            CKEDITOR.replace( 'msg_corpo',
                {

                   on :
                    {
                        instanceReady : function( ev )
                        {
                        // Output paragraphs as <p>Text</p>.
                        this.dataProcessor.writer.setRules( 'p',
                            {
                                indent : false,
                                breakBeforeOpen : false,
                                breakAfterOpen : false,
                                breakBeforeClose : false,
                                breakAfterClose : false
                            });
                        }
                    }

                    //toolbar : 'Basic',
                    //uiColor : '#9AB8F3'
                });
            </script>

<?php include 'footer.php';?>