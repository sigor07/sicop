<?php

if ( isset( $tipo ) ) {
    $tipo = (int)$tipo;
}

if ( empty( $tipo ) ) { //mensagen na tela com voltar automático ?>

    <p>&nbsp;</p><p align="center" class="paragrafo12Italico">Você não tem permissões para acessar esta página.</p>
    <script type="text/javascript">setTimeout("history.go(-1);", 3000)</script>

<?php } else if ( isset( $tipo ) and $tipo == 1 ) { //alerta com voltar automático ?>

    <script type="text/javascript">alert("Você não tem permissões para acessar esta página."); history.go(-1);</script>

<?php } else if ( isset( $tipo ) and $tipo == 2 ) { //mensagen na tela com link voltar ?>

    <p>&nbsp;</p><p align="center" class="paragrafo12Italico">Você não tem permissões para acessar esta página.</p>
    <p align="center"><a href="javascript: history.go(-1)">Voltar</a></p>

<?php } else if ( isset( $tipo ) and $tipo == 3 ) { //mensagen na tela com fechar ?>

    <p>&nbsp;</p><p align="center" class="paragrafo12Italico">Você não tem permissões para acessar esta página.</p>
    <p align="center"><a href="#" title="Fechar esta janela" onClick="javascript: self.window.close(); return false" >Fechar</a></p>

<?php } ?>