<?php
if ( !isset( $_SESSION ) ) session_start();

/*ob_start("ob_gzhandler");*/

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_admsist_n = 3;
$n_admsist   = get_session( 'n_admsist', 'int' );

$motivo_pag = 'ALTERAR MENSAGEM';

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

$idmsg = get_get( 'idmsg', 'int' );

if ( empty( $idmsg ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( IDENTIFICADOR EM BRANCO - $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );

    exit;

}

$q_msg = "SELECT
                msg.`idmsg`,
                msg.`msg_titulo`,
                msg.`msg_corpo`,
                msg.`msg_de`,
                msg.`msg_para`,
                msg.`msg_de_lida`,
                msg.`msg_para_lida`,
                msg.`msg_adm_lida`,
                msg.`msg_de_exc`,
                msg.`msg_para_exc`,
                DATE_FORMAT(msg.msg_de_vdata, '%d/%m/%Y às %H:%i') AS msg_de_vdata,
                DATE_FORMAT(msg.msg_para_vdata, '%d/%m/%Y às %H:%i') AS msg_para_vdata,
                DATE_FORMAT(msg.msg_adm_vdata, '%d/%m/%Y às %H:%i') AS msg_adm_vdata,
                DATE_FORMAT(msg.msg_de_ultdata, '%d/%m/%Y às %H:%i') AS msg_de_ultdata,
                DATE_FORMAT(msg.msg_para_ultdata, '%d/%m/%Y às %H:%i') AS msg_para_ultdata,
                DATE_FORMAT(msg.msg_adm_ultdata, '%d/%m/%Y às %H:%i') AS msg_adm_ultdata,
                DATE_FORMAT(msg.msg_de_exdata, '%d/%m/%Y às %H:%i') AS msg_de_exdata,
                DATE_FORMAT(msg.msg_para_exdata, '%d/%m/%Y às %H:%i') AS msg_para_exdata,
                DATE_FORMAT(msg.msg_add, '%d/%m/%Y às %H:%i') AS msg_add,
                msg.`msg_block`,
                ude.nome_cham AS nome_de,
                upara.nome_cham AS nome_para
              FROM
                msg
                INNER JOIN sicop_users ude ON msg.msg_de = ude.iduser
                INNER JOIN sicop_users upara ON msg.msg_para = upara.iduser
              WHERE idmsg = $idmsg";

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

if($cont_msg < 1) {
    $mensagem = "A consulta retornou 0 ocorrencias (EDIÇÃO DE MENSAGEM).\n\n Página $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$d_msg = $q_msg->fetch_assoc();

$iduser = get_session( 'user_id', 'int' );

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


            <p class="descript_page">EDITAR MENSAGEM</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendmsg.php" method="post" name="deletamsg" id="deletamsg" onsubmit="return validamsg();">

                <table width="500" class="lista_busca">
                    <tr>
                        <td width="56" height="20">De:</td>
                        <td width="437"><?php echo $d_msg['nome_de']; ?></td>
                    </tr>
                    <tr>
                        <td width="56" height="20">Para: </td>
                        <td width="437"><?php echo $d_msg['nome_para']; ?></td>
                    </tr>
                    <tr>
                        <td height="20">Assunto: </td>
                        <td height="20"><input name="msg_titulo" type="text" class="CaixaTexto" id="msg_titulo" value="<?php echo $d_msg['msg_titulo']; ?>" size="70" maxlength="200" /></td>
                    </tr>
                </table>

                <p class="table_leg">Mensagem</p>

                <div align="center">
                    <textarea name="msg_corpo" cols="80" rows="5" class="CaixaTexto" id="msg_corpo"><?php echo $d_msg['msg_corpo']; ?></textarea>
                </div>

                <table class="lista_busca">
                    <tr>
                        <td height="20" colspan="2">Excluida pelo remetente:
                            <input name="msg_de_exc" type="radio" id="msg_de_exc_0" value="1" <?php echo $d_msg['msg_de_exc'] == 1 || is_null( $d_msg['msg_de_exc'] ) ? 'checked="checked"' : ''; ?> /> Sim &nbsp;
                            <input name="msg_de_exc" type="radio" id="msg_de_exc_1" value="0" <?php echo $d_msg['msg_de_exc'] == 0 ? 'checked="checked"' : ''; ?> /> Não
                        </td>
                    </tr>
                    <tr>
                        <td height="20" colspan="2" >Excluida pelo destinatário:
                            <input name="msg_para_exc" type="radio" id="msg_para_exc_0" value="1" <?php echo $d_msg['msg_para_exc'] == 1 || is_null( $d_msg['msg_para_exc'] ) ? 'checked="checked"' : ''; ?> /> Sim &nbsp;
                            <input name="msg_para_exc" type="radio" id="msg_para_exc_1" value="0" <?php echo $d_msg['msg_para_exc'] == 0 ? 'checked="checked"' : ''; ?> /> Não
                        </td>
                    </tr>
                    <tr>
                        <td height="20" colspan="2" >Bloqueada:
                            <input name="msg_block" type="radio" id="msg_block_0" value="1" <?php echo $d_msg['msg_block'] == 1 || is_null( $d_msg['msg_block'] ) ? 'checked="checked"' : ''; ?> /> Sim &nbsp;
                            <input name="msg_block" type="radio" id="msg_block_1" value="0" <?php echo $d_msg['msg_block'] == 0 ? 'checked="checked"' : ''; ?> /> Não
                        </td>
                    </tr>
                </table>

                <input name="proced" type="hidden" id="proced" value="1" />
                <input name="msg_de" type="hidden" id="msg_de" value="<?php echo $d_msg['msg_de']; ?>" />
                <input name="msg_para" type="hidden" id="msg_para" value="<?php echo $d_msg['msg_para']; ?>" />
                <input name="idmsg" type="hidden" id="idmsg" value="<?php echo $d_msg['idmsg']; ?>" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Enviar" />
                    <input class="form_bt" name="" type="button" onClick="history.go(-1)" value="Cancelar" />
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