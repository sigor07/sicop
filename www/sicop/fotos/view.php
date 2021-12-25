<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

// adicionando o javascript
$cab_js = 'ajax/ajax_buscafoto.js';
set_cab_js( $cab_js );

$desc_pag = 'Pesquisar fotos por período';

require 'cab.php';

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