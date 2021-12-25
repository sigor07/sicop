<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_cadastro = get_session( 'n_cadastro', 'int' );
$n_chefia   = get_session( 'n_chefia', 'int' );
$nivel_necessario = 3;

if ( ( $n_cadastro < $nivel_necessario ) and ( $n_chefia < $nivel_necessario ) ) {

    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'CADASTRAMENTO DE DETENTO';
    get_msg( $msg, 1 );

    exit;

}

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$query_art       = 'SELECT `idartigo`, `artigo` FROM `tipoartigo` ORDER BY `artigo`';
$query_nac       = 'SELECT `idnacionalidade`, `nacionalidade` FROM `tiponacionalidade` ORDER BY `nacionalidade`';
$q_local_prisao  = 'SELECT `unidades`.`idunidades`, `unidades`.`unidades` FROM `unidades` WHERE `in` = TRUE ORDER BY `unidades`.`unidades`';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_art      = $model->query( $query_art );
$query_nac      = $model->query( $query_nac );
$q_local_prisao = $model->query( $q_local_prisao );

// fechando a conexao
$model->closeConnection();

$desc_pag = 'Cadastrar detento';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 5 );
$trail->output();
?>

            <p class="descript_page">CADASTRAR <?php echo SICOP_DET_DESC_U; ?></p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/senddetsing.php" method="post" name="cadastradet" id="cadastradet" onsubmit="return valida_det();">

                <table class="edit">
                    <tr>
                        <td width="108">Nome d<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L; ?>:</td>
                        <td width="544" >
                            <input name="nome_det" type="text" class="CaixaTexto" id="nome_det" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 1);" size="100" maxlength="80" />
                        </td>
                    </tr>
                    <tr>
                        <td width="108">Matrícula:</td>
                        <td >
                            <input name="matricula" type="text" class="CaixaTexto" id="matricula" onblur="checkmatr(this, this.value); $.ck_matr_exist();" onkeypress="mascara(this, mmatr); return blockChars(event, 2);" size="11" maxlength="10" />
                            &nbsp;<a href="#" title="Abrir a calculadora de dígitos de matrícula" onclick="javascript: ow('../calc_d_matr.php', '600', '300'); return false" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>calc.png" alt="Abrir a calculadora de dígitos de matrícula" width="13" height="13" /></a>&nbsp;&nbsp;
                            RG civil:
                            <input name="rgcivil" type="text" class="CaixaTexto" id="rgcivil" onkeypress="mascara(this, mrg);" onblur="checkrg(this, this.value);" size="14" maxlength="12" />
                            &nbsp;<a href="#" title="Abrir a calculadora de dígitos de R.G." onclick="javascript: ow('../calc_d_rg.php', '600', '300'); return false" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>calc.png" alt="Abrir a calculadora de dígitos de R.G." width="13" height="13" /></a>&nbsp;&nbsp;
                            Execução:
                            <input name="execucao" type="text" class="CaixaTexto" id="execucao" onkeypress="mascara(this, mexec); return blockChars(event, 2);" size="11" maxlength="9" />
                        </td>
                    </tr>

                    <tr>
                        <td width="108">CPF:</td>
                        <td colspan="3">
                            <input name="cpf" type="text" class="CaixaTexto" id="cpf" onblur="checkcpf(this, this.value); $.ck_cpf_exist();" onkeypress="mascara(this, mcpf); return blockChars(event, 2);" value="" size="16" maxlength="14" />
                        </td>
                    </tr>

                    <tr>
                        <td width="108">Vulgo(s):</td>
                        <td ><input name="vulgo" type="text" class="CaixaTexto" id="vulgo" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 4);" size="60" maxlength="50" />
                            <?php if ( $n_cadastro >= 3 ) { ?>
                            &nbsp;&nbsp;&nbsp;
                            Dados Provisórios: <input name="dados_prov" type="checkbox" id="dados_prov" value="1" />
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="108">Artigo:</td>
                        <td >
                            <select name="artigo" class="CaixaTexto" id="artigo">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while ( $dados_art = $query_art->fetch_assoc() ) { ?>
                                <option value="<?php echo $dados_art['idartigo']; ?>"><?php echo $dados_art['artigo']; ?></option>
                                <?php }; ?>
                            </select>
                            &nbsp;&nbsp;&nbsp;
                            Outros Artigos:
                            <input name="outros_art" type="text" class="CaixaTexto" id="outros_art" onblur="upperMe(this); remacc(this);" size="37" onkeypress="return blockChars(event, 4);" maxlength="27" />
                        </td>
                    </tr>
                    <tr>
                        <td width="108">Nacionalidade:</td>
                        <td >
                            <select name="nacionalidade" class="CaixaTexto" id="nacionalidade">
                                <?php while ( $dados_nac = $query_nac->fetch_assoc() ) { ?>
                                <option value="<?php echo $dados_nac['idnacionalidade']; ?>" <?php echo $dados_nac['nacionalidade'] == "BRASILEIRA" ? 'selected="selected"' : ''; ?>><?php echo $dados_nac['nacionalidade']; ?></option>
                                <?php }; ?>
                            </select>
                            &nbsp;&nbsp;&nbsp;
                            Estado:
                            <select name="uf" class="CaixaTexto" id="uf" onchange="$.monta_box_cidade();">
                                <option value="" selected="selected">Selecione</option>
                            </select>
                            &nbsp;&nbsp;&nbsp;
                            Cidade:
                            <select name="cidade" class="CaixaTexto" id="cidade">
                                <option value="">Selecione o estado</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="108">Nascimento:</td>
                        <td >
                            <input name="nasc_det" type="text" class="CaixaTexto" id="nasc_det" size="12" maxlength="10" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value);return blockChars(event, 2);" />
                            &nbsp;&nbsp;&nbsp;
                            Profissão:
                            <input name="profissao" type="text" class="CaixaTexto" id="profissao" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 1);" size="62" maxlength="50" />
                        </td>
                    </tr>
                    <tr>
                        <td width="108">Nome do Pai:</td>
                        <td ><input name="nome_pai_det" type="text" class="CaixaTexto" id="nome_pai_det" onblur="upperMe(this); remacc(this); rpcnomepai(this, 1);" onkeypress="return blockChars(event, 1);" size="100" maxlength="80" /></td>
                    </tr>
                    <tr>
                        <td width="108">Nome da Mãe:</td>
                        <td ><input name="nome_mae_det" type="text" class="CaixaTexto" id="nome_mae_det" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 1);" size="100" maxlength="80" /></td>
                    </tr>
                    <tr>
                        <td width="108">Fuga:</td>
                        <td >
                            <input name="fuga" type="radio" id="fuga_0"  onclick="mostraFuga()" value="1" />Sim &nbsp;
                            <input name="fuga" type="radio" id="fuga_1"  onclick="mostraFuga()" value="0" checked="checked" />Não
                            &nbsp;&nbsp;&nbsp;
                            <span id="localfugal">Local:</span>
                            <span id="localfuga">
                                <input name="local_fuga" type="text" class="CaixaTexto" id="local_fuga" size="69" maxlength="60" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 3);"/>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td width="108">Data da prisão:</td>
                        <td>
                            <input name="data_prisao" type="text" class="CaixaTexto" id="data_prisao" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value);return blockChars(event, 2);" size="12" maxlength="10" />
                            &nbsp;&nbsp;&nbsp;
                            Primário: <input name="primario" type="radio" id="primario_0" value="1" checked="checked" /> Sim &nbsp;
                                      <input name="primario" type="radio" id="primario_1" value="0" /> Não
                        </td>
                    </tr>
                    <tr>
                        <td width="108">Local da prisão:</td>
                        <td>
                            <select name="local_prisao" class="CaixaTexto" id="local_prisao">
                                <option value="" >Selecione...</option>
                                <?php while ( $d_local_prisao = $q_local_prisao->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_local_prisao['idunidades'];?>"><?php echo $d_local_prisao['unidades'];?></option>
                                <?php };?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="108">PL:</td>
                        <td ><input name="pl" type="text" class="CaixaTexto" id="pl" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 4);" size="10" maxlength="8" /></td>
                    </tr>
                    <tr>
                        <td width="108">Guia local:</td>
                        <td ><input name="guia_local" type="text" class="CaixaTexto" id="guia_local" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 4);" size="10" maxlength="6" /></td>
                    </tr>
                    <tr>
                        <td width="108">Guia número:</td>
                        <td ><input name="guia_numero" type="text" class="CaixaTexto" id="guia_numero" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 4);" size="10" maxlength="10" /></td>
                    </tr>
                </table>

                <input name="old_matr" type="hidden" id="old_matr" value="" />
                <input name="old_cpf" type="hidden" id="old_cpf" value="" />

                <div class="form_bts">
                    <input class="form_bt" name="cadastra" type="submit" id="cadastra" value="Cadastrar" />
                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

            <script type="text/javascript">

                $(function() {

                    $.monta_box_uf();

                    $( "#nome_det" ).focus();
                    $( "#nasc_det, #data_prisao" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });

                });

                mostraFuga();

            </script>

<?php include 'footer.php'; ?>