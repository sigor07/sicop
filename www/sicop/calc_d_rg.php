<?php
if ( !isset( $_SESSION ) ) session_start();

require 'init/config.php';
require 'incl_complete.php';

$desc_pag = 'Cálculo de dígito';

require 'cab_simp.php';
?>
<script type="text/javascript">

function process_enter () {

    if ( window.event.keyCode == 13 ){

        gera_rg_d(rg.value);

    }

}

</script>

            <p style="margin: 10px 0px 5px; text-align: center;">Digite o R.G.:</p>

            <div class="form_one_field">
                <input name="rg" type="text" class="CaixaTextoC" id="rg" onKeyPress="process_enter(); return blockChars(event, 5);" size="13" maxlength="11" />
            </div>

            <script type="text/javascript">id("rg").focus();</script>

            <div class="form_bts">
                <input class="form_bt" name="" type="button" onClick="gera_rg_d(rg.value);" value="Calcular" />
                <input class="form_bt" name="" type="button" onClick="self.window.close()" value="Fechar" />
            </div>

            <div style="margin: 10px;" id="result"></div>

<?php include 'footer_simp.php'; ?>