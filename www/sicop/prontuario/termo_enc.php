<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';
$tipo_pag = 'IMPRESSÃO DE TERMOS DE ENCERRAMENTO';

$n_pront   = get_session( 'n_pront', 'int' );
$n_pront_n = 3;

if ( $n_pront < $n_pront_n ) {

    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $tipo_pag;
    get_msg( $msg, 1 );

    exit;

}

$is_post = is_post();

if ( $is_post ) {

    extract( $_POST, EXTR_OVERWRITE );

    $iddet = empty( $iddet ) ? '' : (int)$iddet;

    if ( empty( $iddet ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Identificador d" . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L . " em branco. Operação cancelada (IMPRESSÃO DO TERMO DE ENCERRAMENTO DO PROTUÁRIO).\n\n Página: $pag";
        salvaLog( $mensagem );
        echo msg_js( 'FALHA!', 2 );
        exit;
    }

    $mot_termo = empty( $mot_termo ) ? '' : (int)$mot_termo;

    if ( empty( $mot_termo ) or $mot_termo > 4 ) {

        // pegar os dados do preso
        $detento = dados_det( $iddet );

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Identificador do motivo do termo de encerramento em branco ou inválido. Operação cancelada (IMPRESSÃO DO TERMO DE ENCERRAMENTO DO PROTUÁRIO).\n\n $detento \n\n Página: $pag";
        salvaLog( $mensagem );
        echo msg_js( 'FALHA!', 2 );
        exit;
    }

    $num_folhas = empty( $num_folhas ) ? '' : (int)$num_folhas;

    $destino = empty( $destino ) ? '' : tratastring( $destino );

    if ( $mot_termo >= 3 and empty( $destino ) ) {

        // pegar os dados do preso
        $detento = dados_det( $iddet );

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Destino do termo de encerramento em branco ou inválido. Operação cancelada (IMPRESSÃO DO TERMO DE ENCERRAMENTO DO PROTUÁRIO).\n\n $detento \n\n Página: $pag";
        salvaLog( $mensagem );
        echo msg_js( 'FALHA!', 2 );
        exit;
    }

    $data_termo = empty( $data_termo ) ? '' : $data_termo;

    if ( empty( $data_termo ) or !validaData( $data_termo, 'DD/MM/AAAA' ) ) {

        // pegar os dados do preso
        $detento = dados_det( $iddet );

        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Data do termo de encerramento em branco ou inválida. Operação cancelada (IMPRESSÃO DO TERMO DE ENCERRAMENTO DO PROTUÁRIO).\n\n $detento \n\n Página: $pag";
        salvaLog( $mensagem );
        echo msg_js( 'FALHA!', 2 );
        exit;
    }
    ?>
    <script type="text/javascript" src="<?php echo SICOP_ABS_PATH ?>js/funcoes.js"></script>
    <script type="text/javascript">javascript: ow('../print/termo_enc_pront.php?iddet=<?php echo $iddet; ?>&mot_termo=<?php echo $mot_termo; ?>&num_folhas=<?php echo $num_folhas; ?>&destino=<?php echo $destino; ?>&data_termo=<?php echo $data_termo; ?>', '600', '600'); focus(); history.go(-2);</script>
    <?php
    exit;
}

$iddet = get_get( 'iddet', 'int' );

if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( IDENTIFICADOR D" . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U . " EM BRANCO - $tipo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Termo de encerramento';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 4 );
$trail->output();
?>

            <p class="descript_page">IMPRIMIR TERMO DE ENCERRAMENTO</p>

            <?php include 'quali/det_basic.php'; ?>

            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="imp_termo" id="imp_termo" onSubmit="return valida_termo_pront()">

                <table class="edit">
                    <tr>
                        <td style="text-align: center;">Motivo</td>
                    </tr>
                    <tr>
                        <td style="width: 225px;">
                            <input name="mot_termo" type="radio" id="mot_termo_0" value="1" checked="checked" onClick="oculta_campos_termo();" /> Liberdade<br />
                            <input name="mot_termo" type="radio" id="mot_termo_1" value="2" onClick="oculta_campos_termo();"/> Óbito <br />
                            <input name="mot_termo" type="radio" id="mot_termo_2" value="3" onClick="oculta_campos_termo();"/> Transferência - fora da SAP <br />
                            <input name="mot_termo" type="radio" id="mot_termo_3" value="4" onClick="oculta_campos_termo();"/> Transferência - dentro da SAP
                        </td>
                    </tr>
                </table>

                <table class="edit">
                    <tr>
                        <td class="tbe_legend">Número de folhas:</td>
                        <td class="tbe_field_small"><input name="num_folhas" type="text" class="CaixaTexto" id="num_folhas" onkeypress="return blockChars(event, 2);" value="<?php if ( !empty( $_GET['num_folhas'] ) ) echo $_GET['num_folhas'] ?>" size="5" maxlength="5" /></td>
                    </tr>
                    <tr>
                        <td class="tbe_legend">Data:</td>
                        <td class="tbe_field_small">
                            <input name="data_termo" type="text" class="CaixaTexto" id="data_termo" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php if ( !empty( $_GET['data_termo'] ) ) echo $_GET['data_termo'] ?>" size="12" maxlength="10" />&nbsp;&nbsp;<a href="javascript:void(0)" onclick="javascript: datahoje('data_termo'); return false;" >hoje</a>
                        </td>
                    </tr>
                    <tr id="dest">
                        <td class="tbe_legend">Destino:</td>
                        <td class="tbe_field_small">
                            <input name="destino" type="text" class="CaixaTexto" id="destino" onkeypress="return blockChars(event, 4);" value="<?php if ( !empty( $_GET['destino'] ) ) echo $_GET['destino'] ?>" size="40" maxlength="40" />
                        </td>
                    </tr>
                </table>

                <input name="iddet" type="hidden" id="iddet" value="<?php echo $iddet ?>" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="imprimir" id="imprimir" value="Imprimir" />
                </div>

            </form>

            <input name="datahj" type="hidden" id="datahj" value="<?php echo date( 'd/m/Y' ) ?>">

            <script type="text/javascript">

                $(function() {
                    $( "#mot_termo_0" ).focus();
                    $( "#data_termo" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });
                });

                oculta_campos_termo();

            </script>

<?php include 'footer.php';?>