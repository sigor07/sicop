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

            gera_matr_d( matricula.value );

        }

    }

</script>

            <p style="margin: 10px 0px 5px; text-align: center;">Digite a matrícula:</p>

            <div class="form_one_field">
                <input name="matricula" type="text" class="CaixaTextoC" id="matricula" onKeyPress="process_enter(); return blockChars(event, 5);" size="11" maxlength="9" />
            </div>

            <script type="text/javascript">id("matricula").focus();</script>

            <div class="form_bts">
                <input class="form_bt" name="" type="button" onClick="gera_matr_d(matricula.value)" value="Calcular" />
                <input class="form_bt" name="" type="button" onClick="self.window.close()" value="Fechar" />
            </div>

            <div style="margin: 10px;" id="result"></div>

<?php include 'footer_simp.php'; ?>