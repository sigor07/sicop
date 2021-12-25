<?php
if ( !isset( $_SESSION ) ) session_start();

error_reporting(E_ALL);
ini_set('display_errors', TRUE);

require '../init/config.php';
//require 'incl_complete.php';

// instanciando a classe
$user = new userAutController();

// checando se o sistema esta ativo
$user->ckSys();

// validando o usuário e o nível de acesso
$user->validateUser( 'n_admsist', 2, '', 6 );

// gravando o acesso no log
$pag = $user->linkPag();
$mensagem = "Acesso à página $pag";
$user->salvaLog( $mensagem );

// instanciado o view
$view = new SicopView();

// adicionando o javascript
$view->setJS( 'ajax/ajax_buscafoto' );

// título da página e escrevendo o header
$desc_pag = 'Pesquisar fotos por período';
echo $view->getHeader( $desc_pag, 'C' );

require 'menu.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>


            <p class="descript_page">PESQUISAR FOTOS POR PERÍODO</p>

            <form name="form_busca" id="form_busca">

                <table class="busca_form">
                    <tr>
                        <td class="bf_legend">Data entre:</td>
                        <td class="bf_field">
                            <input name="data_ini" type="text" class="CaixaTexto" id="data_ini" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="" size="12" maxlength="10" /> e
                            <input name="data_fim" type="text" class="CaixaTexto" id="data_fim" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="" size="12" maxlength="10" />
                        </td>
                    </tr>
                    <tr>
                        <td class="bf_legend">Tipo:</td>
                        <td class="bf_field">
                            <select name="tipo" class="CaixaTexto" id="tipo">
                                <option value="" >Selecione...</option>
                                <option value="1" >Detentos</option>
                                <option value="2" >Visitas</option>
                            </select>
                        </td>
                    </tr>
                </table><!-- /table class="busca_form" -->

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="buscar" id="buscar" value="Buscar" />
                </div>

                <input type="hidden" name="busca" id="busca" value="busca" />

            </form>

            <script type="text/javascript">

                $(function() {
                    $( "#data_ini" ).focus().select();
                    $( "#data_ini, #data_fim" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });
                });

            </script>

            <div id="album_principal" class="album_foto" style="margin-top: 10px"></div>

<?php include 'footer.php'; ?>