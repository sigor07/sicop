<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_rol   = get_session( 'n_rol', 'int' );
$n_rol_n = 3;
$motivo_pag = 'ALTERAÇÃO DE DADOS DO VISITANTE';

if ( $n_rol < $n_rol_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$idvisit = get_get( 'idvisit', 'int' );

if ( empty( $idvisit ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página. Identificador do visitante em branco. ( $motivo_pag )";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$query_visit = "SELECT
                  `visitas`.`idvisita`,
                  `visitas`.`nome_visit`,
                  `visitas`.`rg_visit`,
                  `visitas`.`sexo_visit`,
                  `visitas`.`nasc_visit`,
                  DATE_FORMAT( `visitas`.`nasc_visit`, '%d/%m/%Y' ) AS nasc_visit_f,
                  `visitas`.`cod_cidade_v`,
                  `cidades`.`nome` AS cidade,
                  `estados`.`sigla` AS estado,
                  `estados`.`idestado`,
                  `visitas`.`pai_visit`,
                  `visitas`.`mae_visit`,
                  `visitas`.`resid_visit`,
                  `visitas`.`telefone_visit`,
                  `visitas`.`defeito_fisico`,
                  `visitas`.`sinal_nasc`,
                  `visitas`.`cicatrizes`,
                  `visitas`.`tatuagens`,
                  `visitas`.`doc_rg`,
                  `visitas`.`doc_foto34`,
                  `visitas`.`doc_resid`,
                  `visitas`.`doc_ant`,
                  `visitas`.`doc_cert`
                FROM
                  `visitas`
                  LEFT JOIN `cidades` ON `visitas`.`cod_cidade_v` = `cidades`.`idcidade`
                  LEFT JOIN `estados` ON `cidades`.`cod_uf` = `estados`.`idestado`
                WHERE
                  `visitas`.`idvisita` = $idvisit
                LIMIT 1";

$db = SicopModel::getInstance();
$query_visit = $db->query( $query_visit );
if ( !$query_visit ) {


    // pegar a mensagem de erro mysql
    $msg_err_mysql = $db->getErrorMsg();
    $db->closeConnection();

    // gerar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->set_msg_type( SM_TYPE_ERR );
    $msg->add_quebras( 1 );
    $msg->set_msg_pre_def( SM_QUERY_FAIL );
    $msg->add_parenteses( $motivo_pag );
    $msg->add_quebras( 2 );
    $msg->set_msg( $msg_err_mysql );
    $msg->get_msg();

    echo msg_js( 'FALHA!', 1 );
    exit;

}

$db->closeConnection();

$cont = $query_visit->num_rows;
if ( $cont < 1 ) {

    // gerar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->set_msg_type( SM_TYPE_ERR );
    $msg->add_quebras( 1 );
    $msg->set_msg( "A consulta retornou 0 ocorrências" );
    $msg->add_parenteses( $motivo_pag );
    $msg->add_quebras( 2 );
    $msg->get_msg();

    echo msg_js( 'FALHA!', 1 );
    exit;

}

$d_visit = $query_visit->fetch_assoc();

//$query_parent = 'SELECT `idparentesco`, `parentesco` FROM `tipoparentesco` ORDER BY `parentesco` ASC';
//
//// instanciando o model
//$model = SicopModel::getInstance();
//
//// executando a query
//$query_parent = $model->query( $query_parent );
//
//// fechando a conexao
//$model->closeConnection();
//
//if( !$query_parent ) {
//
//    echo msg_js( 'FALHA!!!', 1 );
//    exit;
//
//}


$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Alterar visitante';

// adicionando o javascript
$cab_js   = array();
$cab_js[] = 'jquery.validate.js';
$cab_js[] = 'ajax/visit_edit.js';
set_cab_js( $cab_js );

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 7 );
$trail->output();
?>


            <p class="descript_page">ALTERAR DADOS DO VISITANTE</p>

            <p class="table_leg">Visitante</p>

            <div class="cont_validator_error"></div>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendvisit.php" method="post" name="visit_up" id="visit_up">

                <table class="edit">
                    <tr>
                        <td width="95">Nome:</td>
                        <td colspan="3"><input name="nome_visit" type="text" class="CaixaTexto" id="nome_visit" value="<?php echo $d_visit['nome_visit'] ?>" onBlur="upperMe(this); remacc(this);" onKeyPress="return blockChars(event, 1);" size="100" maxlength="80" /></td>
                    </tr>
                    <tr>
                        <td width="95">R.G.:</td>
                        <td width="140"><input name="rg_visit" type="text" class="CaixaTexto" id="rg_visit" onkeypress="return blockChars(event, 7);" value="<?php echo $d_visit['rg_visit'] ?>" size="14" maxlength="12" /></td>
                        <td colspan="2">Sexo:
                            <input type="radio" name="sexo_visit" value="M" id="sexo_visit_0" <?php echo $d_visit['sexo_visit'] == 'M' ? 'checked="checked"' : ''; ?> />M &nbsp;&nbsp;
                            <input type="radio" name="sexo_visit" value="F" id="sexo_visit_1" <?php echo $d_visit['sexo_visit'] == 'F' ? 'checked="checked"' : ''; ?> />F
                        </td>
                    </tr>
                    <tr>
                        <td width="95">Nome do Pai:</td>
                        <td colspan="3"><input name="pai_visit" type="text" class="CaixaTexto" id="pai_visit" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 1);" value="<?php echo $d_visit['pai_visit'] ?>" size="100" maxlength="80" /></td>
                    </tr>
                    <tr>
                        <td width="95">Nome da Mãe:</td>
                        <td colspan="3"><input name="mae_visit" type="text" class="CaixaTexto" id="mae_visit" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 1);" value="<?php echo $d_visit['mae_visit'] ?>" size="100" maxlength="80" /></td>
                    </tr>
                    <tr>
                        <td width="95">Naturalidade:</td>
                        <td>Estado:
                            <select name="uf" class="CaixaTexto" id="uf" onchange="$.monta_box_cidade();">
                                <option value="" selected="selected">Selecione</option>
                            </select>
                        </td>
                        <td colspan="2">Cidade:
                            <select name="cidade" class="CaixaTexto" id="cidade">
                                <option value="">Selecione o estado</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="95">Nascimento*:</td>
                        <td><input name="nasc_visit" type="text" class="CaixaTexto" id="nasc_visit" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $d_visit['nasc_visit_f'] ?>" size="12" maxlength="10" /></td>
                        <td colspan="2">Telefone:
                            <input name="telefone_visit" type="text" class="CaixaTexto" id="telefone_visit" onkeypress="mascara(this, mtel); return blockChars(event, 2);" value="<?php echo $d_visit['telefone_visit'] = preg_replace( "/([0-9]{2})([0-9]{4})([0-9]{4})/", "(\\1) \\2-\\3", $d_visit['telefone_visit'] ) ?>" size="16" maxlength="14" />
                        </td>
                    </tr>
                    <tr>
                        <td width="95">Defeitos físicos:</td>
                        <td colspan="3">
                            <input name="defeito_fisico" type="text" class="CaixaTexto" id="defeito_fisico" onblur="upperMe(this);" onkeypress="return blockChars(event, 1);" value="<?php echo $d_visit['defeito_fisico'] ?>" size="100" maxlength="80" />
                        </td>
                    </tr>
                    <tr>
                        <td width="95">Sinal(is) de nascimento:</td>
                        <td colspan="3">
                            <input name="sinal_nasc" type="text" class="CaixaTexto" id="sinal_nasc" onblur="upperMe(this);" onkeypress="return blockChars(event, 1);" value="<?php echo $d_visit['sinal_nasc'] ?>" size="100" maxlength="80" />
                        </td>
                    </tr>
                    <tr>
                        <td width="95">Cicatrizes:</td>
                        <td colspan="3">
                            <input name="cicatrizes" type="text" class="CaixaTexto" id="cicatrizes" onblur="upperMe(this);" onkeypress="return blockChars(event, 1);" value="<?php echo $d_visit['cicatrizes'] ?>" size="100" maxlength="80" />
                        </td>
                    </tr>
                    <tr>
                        <td width="95">Tatuagem(ns):</td>
                        <td colspan="3">
                            <input name="tatuagens" type="text" class="CaixaTexto" id="tatuagens" onblur="upperMe(this);" onkeypress="return blockChars(event, 1);" value="<?php echo $d_visit['tatuagens'] ?>" size="100" maxlength="80" />
                        </td>
                    </tr>
                    <tr>
                        <td width="95">Endereço:</td>
                        <td colspan="3"><textarea name="resid_visit" cols="99" rows="2" class="CaixaTexto" id="resid_visit" onblur="upperMe(this);" onkeypress="return blockChars(event, 4);" onkeydown="textCounter(this, 150);" onkeyup="textCounter(this, 150);"><?php echo $d_visit['resid_visit']; ?></textarea></td>
                    </tr>
                </table>

                <div id="grupo">
                    <fieldset>
                        <p class="table_leg">Documentação:</p>
                        <table width="295" align="center" id="tbl_permissao" class="edit">
                            <tr align="center">
                                <th align="center" width="90">Documento</th>
                                <th width="55">OK</th>
                                <th width="55">FALTA</th>
                                <th width="70">DESNEC.</th>
                            </tr>
                            <tr class="even_gr">
                                <td align="left">Xerox RG</td>
                                <td align="center"><input name="doc_rg" type="radio" id="doc_rg_0" value="1" <?php echo $d_visit['doc_rg'] == "1" ? 'checked="checked"' : ''; ?> /></td>
                                <td align="center"><input name="doc_rg" type="radio" id="doc_rg_1" value="0" <?php echo empty( $d_visit['doc_rg'] ) ? 'checked="checked"' : ''; ?> /></td>
                                <td align="center"><input name="doc_rg" type="radio" id="doc_rg_2" value="2" <?php echo $d_visit['doc_rg'] == "2" ? 'checked="checked"' : ''; ?> /></td>
                            </tr>
                            <tr class="even_gr">
                                <td align="left">Foto 3x4</td>
                                <td align="center"><input name="doc_foto34" type="radio" id="doc_foto34_0" value="1" <?php echo $d_visit['doc_foto34'] == "1" ? 'checked="checked"' : ''; ?> /></td>
                                <td align="center"><input name="doc_foto34" type="radio" id="doc_foto34_1" value="0" <?php echo empty( $d_visit['doc_foto34'] ) ? 'checked="checked"' : ''; ?> /></td>
                                <td align="center"><input name="doc_foto34" type="radio" id="doc_foto34_2" value="2" <?php echo $d_visit['doc_foto34'] == "2" ? 'checked="checked"' : ''; ?> /></td>
                            </tr>
                            <tr class="even_gr">
                                <td align="left">Comp. resid.</td>
                                <td align="center"><input name="doc_resid" type="radio" id="doc_resid_0" value="1" <?php echo $d_visit['doc_resid'] == "1" ? 'checked="checked"' : ''; ?> /></td>
                                <td align="center"><input name="doc_resid" type="radio" id="doc_resid_1" value="0" <?php echo empty( $d_visit['doc_resid'] ) ? 'checked="checked"' : ''; ?> /></td>
                                <td align="center"><input name="doc_resid" type="radio" id="doc_resid_2" value="2" <?php echo $d_visit['doc_resid'] == "2" ? 'checked="checked"' : ''; ?> /></td>
                            </tr>
                            <tr class="even_gr">
                                <td align="left">Antecedentes criminais</td>
                                <td align="center"><input name="doc_ant" type="radio" id="doc_ant_0" value="1" <?php echo $d_visit['doc_ant'] == "1" ? 'checked="checked"' : ''; ?> /></td>
                                <td align="center"><input name="doc_ant" type="radio" id="doc_ant_1" value="0" <?php echo empty( $d_visit['doc_ant'] ) ? 'checked="checked"' : ''; ?> /></td>
                                <td align="center"><input name="doc_ant" type="radio" id="doc_ant_2" value="2" <?php echo $d_visit['doc_ant'] == "2" ? 'checked="checked"' : ''; ?> /></td>
                            </tr>
                            <tr class="even_gr">
                                <td align="left">Certidão de nascimento / casamento</td>
                                <td align="center"><input name="doc_cert" type="radio" id="doc_cert_0" value="1" <?php echo $d_visit['doc_cert'] == "1" ? 'checked="checked"' : ''; ?> /></td>
                                <td align="center"><input name="doc_cert" type="radio" id="doc_cert_1" value="0" <?php echo empty( $d_visit['doc_cert'] ) ? 'checked="checked"' : ''; ?> /></td>
                                <td align="center"><input name="doc_cert" type="radio" id="doc_cert_2" value="2" <?php echo $d_visit['doc_cert'] == "2" ? 'checked="checked"' : ''; ?> /></td>
                            </tr>
                        </table>
                    </fieldset>
                </div>

                <div id="grupo">
                    <p align="center">* Observação: A data de nascimento não é obrigatória para o cadastramento, mas <b>será exigida</b> para o ingresso do visitante na unidade.</p>
                </div>

                <input type="hidden" name="idvisit" id="idvisit" value="<?php echo $idvisit; ?>" />
                <input type="hidden" name="proced" id="proced" value="1" />
                <input type="hidden" name="old_rg_visit" id="old_rg_visit" value="<?php echo $d_visit['rg_visit'] ?>" />

                <input type="hidden" name="old_uf" id="old_uf" value="<?php echo $d_visit['idestado'];?>" />
                <input type="hidden" name="old_cidade" id="old_cidade" value="<?php echo $d_visit['cod_cidade_v'];?>" />

                <div class="form_bts">
                    <input class="form_bt" name="atualizar" type="submit" value="Atualizar" />
                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

            <span id="load_icon"></span>

            <input type="hidden" name="return_rg" id="return_rg" value="" />

            <script type="text/javascript">

                $(function() {

                    $.monta_box_uf();

                    //$( "input.form_bt" ).button();
                    //$("#visit_up input:not(:submit)").addClass("ui-widget-content");

                    $( "#nome_visit" ).focus();
                    $( "#nasc_visit" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });
                });

            </script>

<?php include 'footer.php';?>