<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_admsist = get_session( 'n_admsist', 'int' );

if ( empty( $n_admsist ) or $n_admsist < 3 ) {
    $tipo = 0;
    include '../init/msgnopag.php';
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso à página SEM PERMISSÕES. \n\n Página: $pag";
    salvaLog($mensagem);
    exit;
}

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Cadastrar localidade';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) {
    $pag_atual .=  '?' . $qs;
}

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 5 );
$trail->output();
?>

            <p class="descript_page">CADASTRAR LOCALIDADE NA LISTA TELEFÔNICA</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendlistatel.php" method="post" name="edit_lt">

                <table class="edit">
                    <tr>
                        <td class="lista_tel_leg">Localidade:</td>
                        <td class="lista_tel_field">
                            <input name="tel_local" type="text" class="CaixaTexto" id="tel_local" onblur="upperMe(this);" onkeypress="return blockChars(event, 4);" size="80" maxlength="150" />
                        </td>
                    </tr>
                    <tr>
                        <td class="lista_tel_leg">Endereço:</td>
                        <td class="lista_tel_field">
                            <textarea style="width: 400px;" name="tel_end" id="tel_end" cols="80" rows="3" class="CaixaTexto" onblur="upperMe(this);" onkeypress="return blockChars(event, 4);"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td class="lista_tel_leg">CEP:</td>
                        <td class="lista_tel_field">
                            <input name="tel_cep" type="text" class="CaixaTexto" id="tel_cep" onkeypress="mascara(this, mcep);return blockChars(event, 2);" size="11" maxlength="9" />
                        </td>
                    </tr>
                    <tr>
                        <td class="lista_tel_leg">Código minemônico:</td>
                        <td class="lista_tel_field">
                            <input name="tel_codmin" type="text" class="CaixaTexto" id="tel_codmin" onblur="upperMe(this);" onkeypress="return blockChars(event, 3);" size="12" maxlength="10" />
                        </td>
                    </tr>
                    <tr>
                        <td class="lista_tel_leg">Diretor:</td>
                        <td class="local_lt_menor">
                            <input name="tel_diretor" type="text" class="CaixaTexto" id="tel_diretor" onblur="upperMe(this);" onkeypress="return blockChars(event, 4);" size="80" maxlength="100" />
                        </td>
                    </tr>
                </table><!-- fim da <table class="edit"> -->

                <input type="hidden" name="proced" value="6" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" onclick="return valida_listatel(2);" value="Cadastrar" />
                    <input class="form_bt" type="button" name="" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form><!-- fim do <form method="post" name="edit_lt"> -->

            <script type="text/javascript">id("tel_local").focus()</script>

<?php include 'footer.php'; ?>