<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_pront   = get_session( 'n_pront', 'int' );
$n_pront_n = 3;

$targ = get_get( 'targ', 'int' );

$botao_canc = 'history.go(-1)';
$botao_value = 'Cancelar';

if ( $targ == 1 ) {
    $botao_canc = 'self.window.close()';
    $botao_value = 'Fechar';
}

$motivo_pag = 'CADASTRAR OBSERVAÇÃO DE GRADE';

if ( $n_pront < $n_pront_n ){

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    $ret = 1;
    if ( !empty ( $targ ) ) $ret = 'f';

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', $ret );

    exit;

}

$iddet = get_get( 'iddet', 'int' );

if ( empty( $iddet ) ){

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    $ret = 1;
    if ( !empty ( $targ ) ) $ret = 'f';
    echo msg_js( '', $ret );

    exit;

}

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Cadastrar observação';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

if ( $targ == 1 ) {

    require 'cab_simp.php';

} else {

    require 'cab.php';
    $pag_atual = $_SERVER['PHP_SELF'];
    $qs = $_SERVER['QUERY_STRING'];

    if ( !empty( $qs ) ) {
        $pag_atual .= '?' . $qs;
    }

    $trail = new Breadcrumb();
    $trail->add( $desc_pag, $pag_atual, 5 );
    $trail->output();

}
?>

            <p class="descript_page">NOVA OBSERVAÇÃO DE GRADE</p>

            <?php include 'quali/det_basic.php'; ?>

            <p class="table_leg">Observações:</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendgradeobs.php" method="post" name="cadobsgrade" id="cadobsgrade">

                <div align="center">
                    <textarea name="obs_grade" id="obs_grade" cols="75" rows="5" class="CaixaTexto" onBlur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 4);"></textarea>
                </div>

                <script type="text/javascript">id("obs_grade").focus();</script>

                <input type="hidden" name="iddet" id="iddet" value="<?php echo $iddet?>" />
                <input type="hidden" name="targ" id="targ" value="<?php echo $targ;?>" />
                <input type="hidden" name="proced" id="proced" value="3" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" onclick="return valida_obs( 'obs_grade' );" value="Cadastrar" />
                    <input class="form_bt" type="button" name="" onclick="<?php echo $botao_canc; ?>" value="<?php echo $botao_value; ?>" />
                </div>

            </form>

<?php include 'footer.php'; ?>