<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_rol   = get_session( 'n_rol', 'int' );
$n_rol_n = 3;

if ( $n_rol < $n_rol_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'CADASTRAMENTO DE SUSPENÇÃO DE VISITANTE';
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
    $msg['text']  = 'Tentativa de acesso direto à página. Identificador do visitante em branco. ( CADASTRAMENTO DE SUSPENÇÃO DE VISITANTE )';
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$desc_pag = 'Suspender visitante';

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

// adicionando o javascript
$cab_js   = array();
$cab_js[] = 'valida.js';
$cab_js[] = 'ajax/ajax_visit.js';
set_cab_js( $cab_js );

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 5 );
$trail->output();
?>

            <p class="descript_page">SUSPENDER VISITANTE</p>

            <?php include 'quali/visit_full.php'; ?>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendsuspvisit.php" method="post" name="visitsusp" id="visitsusp">
                <table class="edit">
                    <tr>
                        <td width="95">Tipo de suspensão:</td>
                        <td width="289">
                            <input type="radio" name="tipo_susp" value="T" id="tipo_susp_0" onClick="mostra_susp_visit();" checked="checked" />Temporária &nbsp;&nbsp;
                            <input type="radio" name="tipo_susp" value="D" id="tipo_susp_1" onClick="mostra_susp_visit();" />Definitiva
                        </td>

                    </tr>
                    <tr>
                        <td width="95">A partir de:</td>
                        <td>
                            <input name="data_inicio" type="text" class="CaixaTexto" id="data_inicio" onBlur="verifica_data(this, this.value)" onKeyPress="mascara_data(this, this.value); return blockChars(event, 2);" size="12" maxlength="10" />
                            &nbsp;&nbsp;<a href="#" onClick="javascript: datahoje('data_inicio'); return false;" >hoje</a>
                        </td>
                    </tr>
                    <tr id="tr_dias_susp">
                        <td width="95"><span id="idperiodo_l">Período <br />(em dias):</span></td>
                        <td><span id="idperiodo"><input name="periodo" type="text" class="CaixaTexto" id="periodo" onKeyPress="return blockChars(event, 2);" size="3" maxlength="3" /></span></td>
                    </tr>
                    <tr>
                        <td width="95">Motivo da suspensão:</td>
                        <td><textarea name="motivo" cols="50" rows="3" class="CaixaTexto" id="motivo" onBlur="upperMe(this);" onKeyPress="return blockChars(event, 4);" ></textarea></td>
                    </tr>
                    <?php if ( $n_rol >= 3 ) { ?>
                    <tr>
                        <td width="95">Revogar:</td>
                        <td><input name="revog" type="checkbox" id="revog" value="1" /></td>
                    </tr>
                    <?php } ?>
                </table>

                <div class="form_bts">
                    <input class="form_bt" name="cadastrar" type="submit" value="Cadastrar" />
                    <input class="form_bt" name="" type="button" onClick="history.go(-1)" value="Cancelar" />
                </div>

                <input name="datahj" type="hidden" id="datahj" value="<?php echo date( 'd/m/Y' ) ?>" />
                <input name="proced" type="hidden" id="proced" value="3" />
                <input name="idvisit" type="hidden" id="idvisit" value="<?php echo $idvisit; ?>" />

            </form>

            <script type="text/javascript">

                $(function() {

                    $( "#tipo_susp_0" ).focus();
                    $( "#data_inicio" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });

                    $("form").submit(function() {
                        if ( validasuspvisit() == true ) {
                            // ReadOnly em todos os inputs
                            $("input", this).attr("readonly", true);
                            // Desabilita os submits
                            $("input[type='submit'],input[type='image']", this).attr("disabled", true);
                            return true;
                        } else {
                            return false;
                        }
                    });
                });

                mostra_susp_visit();

            </script>

<?php include 'footer.php'; ?>