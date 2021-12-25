<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_admsist = get_session( 'n_admsist', 'int' );
$n_admsist_n = 3;

if ( $n_admsist < $n_admsist_n ) {

    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'CADASTRAMENTO DE USUÁRIO';
    get_msg( $msg, 1 );

    exit;

}

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$q_setor = 'SELECT `idsetor`, `sigla_setor` FROM `sicop_setor` ORDER BY `sigla_setor`';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_setor = $model->query( $q_setor );

// fechando a conexao
$model->closeConnection();

$desc_pag = 'Cadastrar usuário';

// adicionando o javascript
$cab_js   = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();

?>

            <p class="descript_page">CADASTRAR USUÁRIO</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/senduser.php" method="post" name="cadastrauser" id="cadastrauser">

                <table width="529" class="edit">
                    <tr>
                        <td width="108" align="left">Nome do usuário:</td>
                        <td width="415" align="left">
                            <input name="nomeuser" type="text" class="CaixaTexto" id="nomeuser" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event,1);" size="80" maxlength="80" />
                        </td>
                    </tr>
                    <tr>
                        <td align="left">Primeiro nome:</td>
                        <td align="left"><input name="nome_cham" type="text" class="CaixaTexto" id="nome_cham" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event,1);" size="30" maxlength="20" /></td>
                    </tr>
                    <tr>
                        <td align="left">Nome de acesso:</td>
                        <td align="left">
                            <input name="usuario" type="text" class="CaixaTexto" id="usuario" onblur="lowerMe(this); remacc(this);" onkeypress="return blockChars(event,1);" size="20" maxlength="15" />
                        </td>
                    </tr>
                    <tr>
                        <td align="left">Senha:</td>
                        <td align="left">
                            <input name="senha" type="password" class="CaixaTexto" id="senha" size="20" maxlength="15" />
                        </td>
                    </tr>
                    <tr>
                        <td align="left">E-mail:</td>
                        <td align="left">
                            <input name="email" type="text" class="CaixaTexto" id="email" onblur="lowerMe(this); remacc(this);" size="50" maxlength="50" />
                        </td>
                    </tr>
                    <tr>
                        <td align="left">Cargo:</td>
                        <td align="left">
                            <input name="cargo" type="text" class="CaixaTexto" id="cargo" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event,1);" size="30" maxlength="30" />
                        </td>
                    </tr>
                    <tr>
                        <td align="left">Setor:</td>
                        <td align="left">
                            <select name="cod_setor" class="CaixaTexto" id="cod_setor">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while ( $dados = $q_setor->fetch_assoc() ) { ?>
                                <option value="<?php echo $dados['idsetor']; ?>"><?php echo $dados['sigla_setor']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td align="left">R.S.:</td>
                        <td align="left">
                            <input name="rsuser" type="text" class="CaixaTexto" id="rsuser" onkeypress="return blockChars(event,2);" size="20" maxlength="12" />
                        </td>
                    </tr>
                    <tr>
                        <td align="left">Ativo:</td>
                        <td align="left">
                            <input name="ativo" type="radio" id="ativo_0" value="1" checked="checked"/> Sim  &nbsp;&nbsp;
                            <input name="ativo" type="radio" id="ativo_1" value="0" /> Não
                        </td>
                    </tr>
                </table>

                <input type="hidden" name="proced" id="proced" value="3" />

                <div class="form_bts">
                    <input class="form_bt" name="cadastra" type="submit" id="cadastra" onclick="return validacadastrauser(1);" value="Cadastrar" />
                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

            <script type="text/javascript">id("nomeuser").focus();</script>

<?php include 'footer.php';?>