<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();

$n_cadastro = get_session( 'n_cadastro', 'int' );
$n_cad_n    = 3;

$sit_pag = 'ALTERAÇÃO DE PEDIDO DE ESCOLTA';

if ( $n_cadastro < $n_cad_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $sit_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$idescolta  = get_get( 'idescolta', 'int' );
if ( empty( $idescolta ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Tentativa de acesso direto à página. Identificador do pedido de escolta em branco. ( $sit_pag )";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$q_escolta = "SELECT
                DATE_FORMAT( `escolta_data`, '%d/%m/%Y' ) AS `escolta_data_f`,
                DATE_FORMAT( `escolta_hora`, '%H:%i' ) AS `escolta_hora_f`,
                `finalidade`
              FROM
                `ordens_escolta`
              WHERE
                `idescolta` = $idescolta
              LIMIT 1";



// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_escolta = $model->query( $q_escolta );

// fechando a conexao
$model->closeConnection();

if ( !$q_escolta ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta ( $sit_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_escolta = $q_escolta->num_rows;
if( $cont_escolta < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "A consulta retornou 0 ocorrências ( $sit_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$d_escolta = $q_escolta->fetch_assoc();

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Alterar pedido de escolta';

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

            <p class="descript_page">ALTERAR PEDIDO DE ESCOLTA</p>

            <form action="<?php echo SICOP_ABS_PATH; ?>send/sendpesc.php" method="post" name="cadpesc" id="cadesc">

                <table class="bonde_add">
                    <tr>
                        <td class="data_esc_leg">Data:</td>
                        <td class="data_esc_field"><input name="escolta_data" type="text" class="CaixaTexto" id="escolta_data" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $d_escolta['escolta_data_f']; ?>" size="12" maxlength="10" />&nbsp;&nbsp;<a href="#" onclick="javascript: datahoje('escolta_data'); return false;" >hoje</a></td>
                    </tr>
                    <tr>
                        <td class="data_esc_leg">Hora:</td>
                        <td class="data_esc_field"><input name="escolta_hora" type="text" class="CaixaTexto" id="escolta_hora" onkeypress="mascara_hora(this, this.value); return blockChars(event, 2);" value="<?php echo $d_escolta['escolta_hora_f']; ?>" size="5" maxlength="5" /></td>
                    </tr>
                    <tr>
                        <td class="data_esc_leg">Finalidade:</td>
                        <td class="data_esc_field"><input name="finalidade" type="text" class="CaixaTexto" id="finalidade" onkeypress="return blockChars(event, 4);" value="<?php echo $d_escolta['finalidade']; ?>" size="60" maxlength="50" /></td>
                    </tr>
                </table><!-- fim da table."bonde_add" -->

                <input type="hidden" name="idescolta" id="idescolta" value="<?php echo $idescolta; ?>">

                <div class="form_bts">
                    <input class="form_bt" name="editesc" type="submit" value="Alterar"  onclick="return valida_escolta(1);" />
                    <input class="form_bt" name="" type="button" onClick="history.go(-1)" value="Cancelar" />
                </div>

            </form><!-- fim do form name="cadesc" -->

            <input name="datahj" type="hidden" id="datahj" value="<?php echo date( 'd/m/Y' ) ?>">

            <script type="text/javascript">

                $(function() {
                    $( "#escolta_data" ).focus();
                    $( "#escolta_data" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });
                });

            </script>

<?php include 'footer.php'; ?>