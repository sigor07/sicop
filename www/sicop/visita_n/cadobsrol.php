<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_rol   = get_session( 'n_rol', 'int' );
$n_rol_n = 3;

$targ = get_get( 'targ', 'int' );

$botao_canc = 'history.go(-1)';
$botao_value = 'Cancelar';

if ( $targ == 1 ) {
    $botao_canc = 'self.window.close()';
    $botao_value = 'Fechar';
}

$motivo_pag = 'CADASTRO DE OBSERVAÇÃO - ROL DE VISITAS';

if ( $n_rol < $n_rol_n ){

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    $ret = 1;
    if ( $targ == 1 ) $ret = 'f';

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', $ret );

    exit;

}

$iddet = get_get( 'iddet', 'int' );

if ( empty( $iddet ) ) {

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
salvaLog( $mensagem );

$desc_pag = 'Cadastrar observação de rol de visitas';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

if ( $targ == 1 ){

    require 'cab_simp.php';

} else {

    require 'cab.php';
    $pag_atual = $_SERVER['PHP_SELF'];
    $qs = $_SERVER['QUERY_STRING'];

    if ( !empty( $qs ) ) {
        $pag_atual .=  '?' . $qs;
    }

    $trail = new Breadcrumb();
    $trail->add( $desc_pag, $pag_atual, 4 );
    $trail->output();

}
?>

            <p class="descript_page">NOVA OBSERVAÇÃO DE ROL DE VISITAS</p>

            <?php include 'quali/det_basic.php'; ?>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendrolobs.php" method="post" name="cadobsrol" id="cadobsrol">

                <p class="table_leg">Observações:</p>

                <div align="center">
                    <textarea name="obs_rol" id="obs_rol" cols="75" rows="5" class="CaixaTexto" onBlur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 4);"></textarea>
                </div>

                <input type="hidden" name="iddet" id="iddet" value="<?php echo $iddet;?>" />
                <input type="hidden" name="targ" id="targ" value="<?php echo $targ;?>" />
                <input type="hidden" name="proced" id="proced" value="3" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Cadastrar" />
                    <input class="form_bt" name="" type="button" onclick="<?php echo $botao_canc; ?>" value="<?php echo $botao_value ?>" />
                </div>

            </form>

            <script type="text/javascript">
                id("obs_rol").focus();

                $(function() {
                    $("form").submit(function() {
                        if ( valida_obs( 'obs_rol' ) == true ) {
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

            </script>

<?php include 'footer.php'; ?>