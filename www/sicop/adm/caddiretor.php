<?php
if ( !isset( $_SESSION ) ) session_start();

/*ob_start("ob_gzhandler");*/

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$nivel_necessario = 3;
$n_admsist          = get_session( 'n_admsist', 'int' );

$motivo_pag = 'CADASTRAR DIRETOR';

if ($n_admsist < $nivel_necessario) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$querysetor = 'SELECT `idsetor`, `sigla_setor` FROM `sicop_setor` ORDER BY `sigla_setor`';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$querysetor = $model->query( $querysetor );

// fechando a conexao
$model->closeConnection();

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';
$trail = new Breadcrumb();
$trail->add('Cadastrar diretor', $_SERVER['PHP_SELF'], 4);
$trail->output();
?>



            <p class="descript_page">CADASTRAR DIRETOR</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/senddiretor.php" method="post" name="editdir" id="editdir" onSubmit="return validadiretor();">

                <table class="edit">
                    <tr>
                        <td width="50">Nome:</td>
                        <td width="440">
                            <input name="diretor" type="text" class="CaixaTexto" id="diretor" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event,1);" size="80" maxlength="80" />
                        </td>
                    </tr>
                    <tr>
                        <td>Título:</td>
                        <td><input name="titulo_diretor" type="text" class="CaixaTexto" id="titulo_diretor" onkeypress="return blockChars(event,1);" size="60" maxlength="60" /></td>
                    </tr>
                    <tr>
                        <td>Setor:</td>
                        <td>
                            <select name="setor" class="CaixaTexto" id="setor">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while ( $dados = $querysetor->fetch_assoc() ) { ?>
                                <option value="<?php echo $dados['idsetor']; ?>"><?php echo $dados['sigla_setor']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Ativo:</td>
                        <td>
                            <input name="ativo" type="radio" id="ativo_0" value="1" checked="checked" /> Sim  &nbsp;&nbsp;
                            <input name="ativo" type="radio" id="ativo_1" value="0" /> Não
                        </td>
                    </tr>
                </table>

                <script type="text/javascript">document.getElementById("diretor").focus();</script>

                <input name="proced" type="hidden" id="proced" value="3" />

                <div class="form_bts">
                    <input class="form_bt" name="atualizar" type="submit" value="Cadastrar" />
                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

<?php include 'footer.php';?>