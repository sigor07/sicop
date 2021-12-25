<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$imp_pront = get_session( 'imp_pront', 'int' );
if ( empty( $imp_pront ) or $imp_pront < 1 ) {

    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = 'IMPRESSÃO DE RESTITUIÇÃO DE MANDADO DE PRISÃO';
    get_msg( $msg, 1 );

    exit;

}

$iddet = get_get( 'iddet', 'int' );
if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso direto à página de impressão DE RESTITUIÇÃO DE MANDADO DE PRISÃO.';
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Imprimir restituição de mandado de prisão';

// adicionando o javascript
$cab_js   = array();
$cab_js[] = 'valida.js';
$cab_js[] = 'ajax/jq_rest.js';
set_cab_js( $cab_js );

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>
            <p class="descript_page">IMPRESSÃO DE RESTITUIÇÃO DE MANDADO DE PRISÃO</p>

            <?php include 'quali/det_basic.php'; ?>

            <form action="" method="post" name="form_termo" id="form_termo">

                <table class="edit">
                    <tr>
                        <td width="80" align="left">Tipo</td>
                        <td width="260">
                            <input name="tipo_rest" type="radio" id="tipo_rest_0" value="1" onClick="oculta_campos_termo_rest();" checked="checked" /> Mandado de prisão &nbsp;&nbsp;&nbsp;
                            <input name="tipo_rest" type="radio" id="tipo_rest_1" value="2" onClick="oculta_campos_termo_rest();"/> Alvará
                        </td>
                    </tr>
                    <tr id="sit_alv">
                        <td width="80" align="left">Cumprido:</td>
                        <td width="260">
                            <input name="sit_alv" type="radio" id="sit_alv_0" value="1" checked="checked" /> Sem impedimento &nbsp;&nbsp;&nbsp;
                            <input name="sit_alv" type="radio" id="sit_alv_1" value="2" /> Com Impedimento
                        </td>
                    </tr>
                </table>

                <table class="edit">
                    <tr>
                        <td width="80" align="left">Referente a(o):</td>
                        <td ><input name="referente" type="text" class="CaixaTexto" id="referente" onblur="upperMe(this);" size="60" maxlength="50" /></td>
                    </tr>
                    <tr>
                        <td width="80" align="left">Para a:</td>
                        <td ><input name="dest" type="text" class="CaixaTexto" id="dest" onblur="upperMe(this); rpcvara(this);" size="60" maxlength="50" /></td>
                    </tr>
                    <tr id="unid_field">
                        <td width="80" align="left">De:</td>
                        <td ><input name="cidade" type="text" class="CaixaTexto" id="cidade" onblur="upperMe(this); rpccidade(this);" size="60" maxlength="50" /></td>
                    </tr>
                </table>

                <script type="text/javascript">
                    id("tipo_rest_0").focus();
                    oculta_campos_termo_rest();
                </script>

                <input name="iddet" type="hidden" id="iddet" value="<?php echo $iddet ?>" />

                <div class="form_bts">
                    <!--<input class="form_bt" type="button" name="imprimir" value="Imprimir" onclick="submit_form_nw( 'form_termo', '../print/rest_mp.php' );" />-->
                    <input class="form_bt" id="print" type="button" name="imprimir" value="Imprimir" />
                    <input class="form_bt" type="button" name="" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

<?php include 'footer.php' ?>