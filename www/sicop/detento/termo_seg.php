<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$imp_chefia = get_session( 'imp_chefia', 'int' );
if ( empty( $imp_chefia ) or $imp_chefia < 1 ) {

    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = 'IMPRESSÃO DE TERMOS DO SEGURO';
    get_msg( $msg, 1 );

    exit;

}

$iddet = get_get( 'iddet', 'int' );

if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso direto à página de impressão de termos d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . '.';
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Imprimir termos do seguro';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>

            <p class="descript_page">IMPRESSÃO DE TERMOS</p>

            <?php include 'quali/det_basic.php'; ?>

            <form action="" method="post" name="form_termo" id="form_termo" >

                <table class="edit">
                    <tr>
                        <td align="center">Motivo</td>
                    </tr>
                    <tr>
                        <td width="180">
                            <input name="mot_termo" type="radio" id="mot_termo_0" value="1" onClick="oculta_campos_termo_seg();" checked="checked" /> Apto<br />
                            <input name="mot_termo" type="radio" id="mot_termo_1" value="2" onClick="oculta_campos_termo_seg();"/> Solicitação de seguro <br />
                            <input name="mot_termo" type="radio" id="mot_termo_2" value="3" onClick="oculta_campos_termo_seg();"/> Solicitação de remoção
                        </td>
                    </tr>
                </table>

                <script type="text/javascript">id("mot_termo_0").focus();</script>
                <br />
                <br />

                <table class="edit">
                    <tr>
                        <td width="80" align="left">Escrivão:</td>
                        <td ><input name="escrivao" type="text" class="CaixaTexto" id="escrivao" onblur="upperMe(this);" onkeypress="return blockChars(event, 1);" size="60" maxlength="50" /></td>
                    </tr>
                    <tr>
                        <td width="80" align="left">Testemunha:</td>
                        <td ><input name="testemunha" type="text" class="CaixaTexto" id="testemunha" onblur="upperMe(this);" onkeypress="return blockChars(event, 1);" size="60" maxlength="50" /></td>
                    </tr>
                    <tr id="unid_field">
                        <td width="80" align="left">Unidade:</td>
                        <td ><input name="unid_dest" type="text" class="CaixaTexto" id="unid_dest" onblur="upperMe(this);" onkeypress="return blockChars(event, 4);" size="60" maxlength="50" /></td>
                    </tr>
                </table>

                <input name="iddet" type="hidden" id="iddet" value="<?php echo $iddet ?>" />

                <div class="form_bts">
                    <input class="form_bt" type="button" name="imprimir" value="Imprimir" onclick="if ( valida_termo_seg() == true ) { submit_form_nw( 'form_termo', '../print/termo_seg.php' ) };" />
                    <input class="form_bt" type="button" name="" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

            <script type="text/javascript">oculta_campos_termo_seg();</script>

<?php include 'footer.php'; ?>