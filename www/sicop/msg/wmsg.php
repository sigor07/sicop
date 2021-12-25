<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

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

$q_user = "SELECT `iduser`, `nome_cham` FROM `sicop_users` WHERE `iduser` <> $iduser AND `ativo` = 1 ORDER BY `nome_cham`";
// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_user = $model->query( $q_user );

// fechando a conexao
$model->closeConnection();

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

// adicionando o javascript
$cab_js   = array();
$cab_js[] = 'valida.js';
$cab_js[] = 'ckeditor/ckeditor.js';
set_cab_js( $cab_js );

require 'cab.php';
$trail = new Breadcrumb();
$trail->add( 'Escrever mensagem', $_SERVER['PHP_SELF'], 3 );
$trail->output();
?>

            <p class="descript_page">ESCREVER MENSAGEM</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendmsg.php" method="post" name="deletamsg" id="deletamsg" onsubmit="return validamsg();">

                <table class="busca_form">
                    <tr>
                        <td class="bf_legend">Para:</td>
                        <td class="bf_field">
                            <select name="msg_para" class="CaixaTexto" id="msg_para">
                                <option value="" >Selecione...</option>
                                <?php while ( $d_user = $q_user->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_user['iduser']; ?>"><?php echo $d_user['nome_cham']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="bf_legend">Assunto: </td>
                        <td class="bf_field"><input name="msg_titulo" type="text" class="CaixaTexto" id="msg_titulo" size="50" maxlength="200" /></td>
                    </tr>
                </table>

                <p class="table_leg">Mensagem</p>

                <div align="center">
                    <textarea name="msg_corpo" cols="80" rows="5" class="CaixaTexto" id="msg_corpo"></textarea>
                </div>

                <script type="text/javascript">
                    CKEDITOR.replace( 'msg_corpo',
                    {
                        toolbar : 'MyToolbar',

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

                <input name="proced" type="hidden" id="proced" value="3">

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Enviar" />
                    <input class="form_bt" type="button" name="" onClick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

<?php include 'footer.php';?>