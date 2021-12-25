<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_ajax.php';

$tipo_pag = 'IMPRESSÕES DE DETENTO - AJAX';

// instanciando a classe
$sys = new SicopController();

// checando se o sistema esta ativo
$sys->ckSys();

// instanciando a classe
$user = new userAutController();

// validando o usuário e o nível de acesso
$arr_perm = array(
    'imp_incl',
    'imp_det',
    'imp_chefia'
);
$user->validateUser( $arr_perm, 1, 'af', 5 );


// checando se o acesso foi via post
$sys->ckPost( 4 );

$op = array(
    'method'         => 'post',       // metodo que a variável será recebida
    'name'           => 'uid',        // nome da variável
    'modo_validacao' => 'int',        // modo de validação
    'minLeng'        => 1,            // comprimento mínimo
    'required'       => true,         // se é requerida ou não
    'zero_ok'        => false,        // em caso de requerida, se pode ser o número 0 (zero)
    'return_type'    => 4             // tipo de retorno em caso de erro
);
$iddet = $sys->validate( $op );

//$cab_js = 'ajax/jq_print_det.js';
//set_cab_js( $cab_js );

header( 'Content-Type: text/html; charset=utf-8' );

//Evitando cache de arquivo
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0' );
header( 'Pragma: no-cache' );
header( 'Expires: 0' );

echo get_cab_js();

?>
<div class="form_ajax">

    <p class="descript_page">IMPRIMIR DOCUMENTOS D<?php echo SICOP_DET_ART_U; ?> <?php echo SICOP_DET_DESC_U; ?></p>

    <?php include 'quali/det_basic.php'; ?>

        <p id="form_error" class="form_error" style="display:none; text-align: center;"></p>

        <input type="hidden" name="iddet" id="iddet" value="<?php echo $iddet ?>" />

        <div class="form_bts">

            <input class="form_bt" style="margin-left: 70px; margin-bottom: 5px;" type="button" value="Ficha qualificativa" id="print_quali" /><input class="form_bt" style="margin-bottom: 5px;" type="button" value="3x" id="print_3_quali" /><br/>
            <input class="form_bt" style="margin-bottom: 5px;" type="button" value="Ficha de identificação" id="print_ficha" /><br/>
            <input class="form_bt" style="margin-bottom: 5px;" type="button" value="Cartão de identificação" id="print_cartao" /><br/>
            <input class="form_bt" style="margin-bottom: 5px;" type="button" value="3 - 1 - 1" id="print_all" />

        </div>

</div>