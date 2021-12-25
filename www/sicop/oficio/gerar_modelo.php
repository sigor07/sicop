<?php

/**
 * arquivo para criação de modelos de ofícios
 * Data 15/02/2012
 *
 * ****************************************************************************
 *
 * SICOP - Sistema de Controle de Prisional
 *
 * Sistema para controle e gerenciamento de unidades prisionais
 *
 * @author  JOSÉ RAFAEL GONÇALVES - AGENTE DE SEGURANÇA III
 * @local   CENTRO DE DETENÇÃO PROVISÓRIA DE SÃO JOSÉ DO RIO PRETO - SP
 * @since   03/01/2011
 *
 * ****************************************************************************
 */

error_reporting(E_ALL);
ini_set('display_errors', TRUE);

require '../init/config.php';

// instanciando a classe
$user = new userAutController();

// checando se o sistema esta ativo
$user->ckSys();

// validando o usuário e o nível de acesso
$user->validateUser( 'n_adm', 3, '', 6 );


// gravando o acesso no log
$pag = $user->linkPag();
$mensagem = "Acesso à página $pag";
$user->salvaLog( $mensagem );

$diretores = Diretor::findTitulos();

$recibos = Oficio::findTiposRecibo();

$tratamentos = Oficio::findTiposTratamento();

// instanciado o view
$view = new SicopView();

// adicionando o javascript
$view->setJS( 'ajax/js_handle_model_doc' );
$view->setJS( 'jquery-ui.js' );

// título da página e escrevendo o header
$desc_pag = 'Gerar modelo de ofício';
echo $view->getHeader( $desc_pag, 'C' );

require 'menu.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>

            <p class="descript_page">GERAR MODELO DE DOCUMENTO</p>

            <p class="table_leg">Ofícios serão numerados na primeira impressão.</p>

            <form id="gera_of" name="gera_of">

                <table class="edit">

                    <tr class="">
                        <td class="tbe_legend_grd">Nome do documento:</td>
                        <td class="tbe_field"><input name="nome_doc" id="nome_doc" type="text" class="CaixaTexto" onKeyPress="return blockChars(event, 1);" size="80" maxlength="80" /></td>
                    </tr>
<!--                    <tr>
                        <td class="tbe_legend_grd">Tipo de documento:</td>
                        <td class="tbe_field"><input name="tipo_doc" type="radio" id="tipo_doc_0" value="1" />Ofício &nbsp;&nbsp;&nbsp; <input name="tipo_doc" type="radio" id="tipo_doc_1" value="2" /> Outros documentos</td>
                    </tr>
                    <tr>
                        <td class="tbe_legend_grd">Título:</td>
                        <td class="tbe_field"><input name="title_doc" id="title_doc" type="text" class="CaixaTexto" onKeyPress="return blockChars(event, 1);" size="80" maxlength="80" /></td>
                    </tr>-->
                    <tr>
                        <td class="tbe_legend_grd">Incluir referente:</td>
                        <td class="tbe_field"><input name="ref_doc" id="ref_doc" type="checkbox" value="1" /></td>
                    </tr>
                    <tr>
                        <td class="tbe_legend_grd">Incluir local/data:</td>
                        <td class="tbe_field"><input name="local_data" id="local_data" type="checkbox" value="1" /></td>
                    </tr>

                    <tr>
                        <td class="tbe_legend_grd">Tipo de qualificativa:</td>
                        <td class="tbe_field">
                            <select name="tipo_quali" id="tipo_quali" class="CaixaTexto">
                                <option value="" selected="selected">Não incluir</option>
                                <option value="1">Básica</option>
                                <option value="2">Com foto</option>
                                <option value="3">Lista de detentos</option>
                            </select>
                        </td>
                    </tr>

                    <tr class="">
                        <td class="tbe_legend_grd">Saudação superior:</td>
                        <td class="tbe_field"><input name="saud_sup" id="saud_sup" type="text" class="CaixaTexto" onKeyPress="return blockChars(event, 1);" size="80" maxlength="50" /></td>
                    </tr>

                    <tr class="">
                        <td class="tbe_legend_grd">Texto:</td>
                        <td class="tbe_field">
                            <textarea name="texto_doc" id="texto_doc" cols="79" rows="11" class="CaixaTexto" onkeypress="return blockChars(event, 4);"></textarea>
                        </td>
                    </tr>



                    <tr>
                        <td class="tbe_legend_grd">Incluir protesto:</td>
                        <td class="tbe_field"><input name="protesto" id="protesto" type="checkbox" value="1" /></td>
                    </tr>

                    <tr>
                        <td class="tbe_legend_grd">Tratamento:</td>
                        <td class="tbe_field">
                            <select name="trat_doc" id="trat_doc" class="CaixaTexto">
                                <option value="" selected="selected">Não incluir</option>
                                <?php
                                if ( $tratamentos ) {
                                    while ( $dados = $tratamentos->fetch_object() ) { ?>
                                <option value="<?php echo $dados->uid; ?>" ><?php echo $dados->tipo_tratamento; ?></option>
                                <?php
                                    }
                                };
                                ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td class="tbe_legend_grd">Quem assina:</td>
                        <td class="tbe_field">
                            <select name="ass_doc" id="ass_doc" class="CaixaTexto">
                                <option value="" selected="selected">Selecione</option>
                                <?php
                                if ( $diretores ) {
                                    while ( $d_dir = $diretores->fetch_object() ) { ?>
                                <option value="<?php echo $d_dir->idsetor; ?>" ><?php echo $d_dir->titulo_diretor; ?></option>
                                <?php
                                    }
                                };
                                ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td class="tbe_legend_grd">Saudação inferior:</td>
                        <td class="tbe_field"><input name="senhoria_doc" id="senhoria_doc" type="text" class="CaixaTexto" onKeyPress="return blockChars(event, 1);" size="80" maxlength="50" value="A sua Senhoria o Senhor" /></td>
                    </tr>

                    <tr>
                        <td class="tbe_legend_grd">Nome do destinatário:</td>
                        <td class="tbe_field"><input name="nome_dest_doc" id="nome_dest_doc" type="text" class="CaixaTexto" onKeyPress="return blockChars(event, 1);" size="80" maxlength="50" /></td>
                    </tr>

                    <tr>
                        <td class="tbe_legend_grd">Cargo:</td>
                        <td class="tbe_field"><input name="cargo_doc" id="cargo_doc" type="text" class="CaixaTexto" onKeyPress="return blockChars(event, 1);" size="80" maxlength="50" /></td>
                    </tr>

                    <tr>
                        <td class="tbe_legend_grd">Cidade:</td>
                        <td class="tbe_field"><input name="cidade_doc" id="cidade_doc" type="text" class="CaixaTexto" onBlur="upperMe(this); rpccidade(this);" onKeyPress="return blockChars(event, 4);" size="80" maxlength="50" /></td>
                    </tr>

                    <tr>
                        <td class="tbe_legend_grd">Incluir recibo:</td>
                        <td class="tbe_field">
                            <select name="recibo_doc" id="recibo_doc" class="CaixaTexto">
                                <option value="" selected="selected">Não incluir</option>
                                <?php
                                if ( $recibos ) {
                                    while ( $dados = $recibos->fetch_object() ) { ?>
                                <option value="<?php echo $dados->uid; ?>" ><?php echo $dados->tipo_recibo; ?></option>
                                <?php
                                    }
                                };
                                ?>
                            </select>
                        </td>
                    </tr>

                </table>

                <input type="hidden" name="proced" id="proced" value="3" />

                <div class="form_bts">
                    <input class="form_bt" name="cadastrar" type="button" id="bt_submit" value="Gerar" />
                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>



            </form>

            <div id="status" style="margin: 5px auto; width: 35px"></div>

<?php include 'footer.php'; ?>