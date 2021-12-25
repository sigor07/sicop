<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_bonde   = get_session( 'n_bonde', 'int' );
$n_bonde_n = 3;

$sit_pag = 'ALTERAÇÃO DE BONDE';

if ( $n_bonde < $n_bonde_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $sit_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$idbonde   = get_get( 'idbonde', 'int' );
if ( empty( $idbonde ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Tentativa de acesso direto à página. Identificador do bonde em branco. ( $sit_pag )";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$q_bonde = "SELECT
              DATE_FORMAT( `bonde_data`, '%d/%m/%Y' ) AS bonde_data_f
            FROM
              `bonde`
            WHERE
              `idbonde` = $idbonde
            LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_bonde = $model->query( $q_bonde );

// fechando a conexao
$model->closeConnection();

if ( !$q_bonde ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta ( $sit_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_bonde = $q_bonde->num_rows;
if( $cont_bonde < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "A consulta retornou 0 ocorrências ( $sit_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$d_bonde = $q_bonde->fetch_assoc();

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Alterar bonde';

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

            <p class="descript_page">ALTERAR BONDE</p>

            <form action="<?php echo SICOP_ABS_PATH; ?>send/sendbonde.php" method="post" name="cadbond" id="cadbond">

                <table class="bonde_add">
                    <tr>
                        <td class="data_bonde_leg">Data:</td>
                        <td class="data_bonde_field"><input name="bonde_data" type="text" class="CaixaTexto" id="bonde_data" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $d_bonde['bonde_data_f']; ?>" size="12" maxlength="10" />&nbsp;&nbsp;<a href="#" onclick="javascript: datahoje('bonde_data'); return false;" >hoje</a></td>
                    </tr>
                </table><!-- fim da table."bonde_add" -->

                <input type="hidden" name="idbonde" id="idbonde" value="<?php echo $idbonde; ?>">

                <div class="form_bts">
                    <input class="form_bt" name="editbond" type="submit" value="Atualizar"  onclick="return valida_bonde(1);" />
                    <input class="form_bt" name="" type="button" onClick="history.go(-1)" value="Cancelar" />
                </div>

            </form><!-- fim do form name="cadbond" -->

            <input name="datahj" type="hidden" id="datahj" value="<?php echo date( 'd/m/Y' ) ?>">

            <script type="text/javascript">

                $(function() {
                    $( "#bonde_data" ).focus();
                    $( "#bonde_data" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });
                });

            </script>

<?php include 'footer.php'; ?>