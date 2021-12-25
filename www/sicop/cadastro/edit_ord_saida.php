<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag         = link_pag();
$tipo        = '';

$n_cadastro = get_session( 'n_cadastro', 'int' );
$n_cad_n    = 3;

$sit_pag = 'ALTERAÇÃO DE ORDEM DE SAÍDA';

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

$id_ord_saida  = get_get( 'id_ord_saida', 'int' );
if ( empty( $id_ord_saida ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Tentativa de acesso direto à página. Identificador da ordem de saída em branco. ( $sit_pag )";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );

    exit;

}

$q_ordsaida = "SELECT
                 DATE_FORMAT( `ord_saida_data`, '%d/%m/%Y' ) AS `ord_saida_data_f`,
                 DATE_FORMAT( `ord_saida_hora`, '%H:%i' ) AS `ord_saida_hora_f`,
                 `finalidade`,
                 `responsavel_escolta`,
                 `retorno`
               FROM
                 `ordens_saida`
               WHERE
                 `id_ord_saida` = $id_ord_saida
               LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_ordsaida = $model->query( $q_ordsaida );

// fechando a conexao
$model->closeConnection();

if ( !$q_ordsaida ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta ( $sit_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_ordsaida = $q_ordsaida->num_rows;
if( $cont_ordsaida < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "A consulta retornou 0 ocorrências ( $sit_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$d_ordsaida     = $q_ordsaida->fetch_assoc();

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Alterar ordem de saída';

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

            <p class="descript_page">ALTERAR ORDEM DE SAÍDA</p>

            <form action="<?php echo SICOP_ABS_PATH; ?>send/sendordsaida.php" method="post" name="editos" id="editos">

                <table class="bonde_add">
                    <tr>
                        <td class="data_esc_leg">Data:</td>
                        <td class="data_esc_field"><input name="ord_saida_data" type="text" class="CaixaTexto" id="ord_saida_data" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $d_ordsaida['ord_saida_data_f']; ?>" size="12" maxlength="10" />&nbsp;&nbsp;<a href="#" onclick="javascript: datahoje('ord_saida_data'); return false;" >hoje</a></td>
                    </tr>
                    <tr>
                        <td class="data_esc_leg">Hora:</td>
                        <td class="data_esc_field"><input name="ord_saida_hora" type="text" class="CaixaTexto" id="ord_saida_hora" onkeypress="mascara_hora(this, this.value); return blockChars(event, 2);" value="<?php echo $d_ordsaida['ord_saida_hora_f']; ?>" size="5" maxlength="5" /></td>
                    </tr>
                    <tr>
                        <td class="data_esc_leg">Finalidade:</td>
                        <td class="data_esc_field"><input name="finalidade" type="text" class="CaixaTexto" id="finalidade" onkeypress="return blockChars(event, 4);" value="<?php echo $d_ordsaida['finalidade']; ?>" size="60" maxlength="50" /></td>
                    </tr>
                    <tr>
                        <td class="data_esc_leg">Escolta:</td>
                        <td class="data_esc_field"><input name="escolta" type="text" class="CaixaTexto" id="escolta" onkeypress="return blockChars(event, 4);" value="<?php echo $d_ordsaida['responsavel_escolta']; ?>" size="60" maxlength="50" /></td>
                    </tr>
                    <tr>
                        <td class="data_esc_leg">Com retorno:</td>
                        <td class="data_esc_field">
                            <input name="retorno" type="checkbox" id="retorno" value="1" <?php echo !empty( $d_ordsaida['retorno'] ) ? 'checked="checked"' : ''; ?>/>
                        </td>
                    </tr>
                </table><!-- fim da table."bonde_add" -->

                <input type="hidden" name="id_ord_saida" id="idescolta" value="<?php echo $id_ord_saida; ?>">

                <div class="form_bts">
                    <input class="form_bt" name="editos" type="submit" value="Alterar"  onclick="return valida_ord_saida(1);" />
                    <input class="form_bt" name="" type="button" onClick="history.go(-1)" value="Cancelar" />
                </div>

            </form><!-- fim do form name="editos" -->

            <input name="datahj" type="hidden" id="datahj" value="<?php echo date( 'd/m/Y' ) ?>">

            <script type="text/javascript">

                $(function() {
                    $( "#ord_saida_data" ).focus();
                    $( "#ord_saida_data" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });
                });

            </script>

<?php include 'footer.php'; ?>