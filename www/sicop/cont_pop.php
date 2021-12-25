<?php
if ( !isset( $_SESSION ) ) session_start();

require 'init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$motivo_pag = 'CONTAGEM POPULACIONAL POR PERÍODO';

$busca       = get_get( 'busca' );
$data_ini_sf = '';
$data_fim_sf = '';

if ( !empty( $busca ) ) {

    $data_ini_sf = get_get( 'data_ini' );
    $data_ini    = get_get( 'data_ini', 'busca' );

    $data_fim_sf = get_get( 'data_fim' );
    $data_fim    = get_get( 'data_fim', 'busca' );

    if ( empty( $data_ini ) and empty( $data_fim ) ) {
        echo msg_js( '', 1 );
        exit;
    }

    $clausula_data = '';

    if ( !empty( $data_ini ) or !empty( $data_fim ) ) {

        if ( !empty( $data_ini ) and !empty( $data_fim ) ) {

            $clausula_data = "DATE( `cp_data_hora` ) BETWEEN STR_TO_DATE( '$data_ini', '%d/%m/%Y' ) AND STR_TO_DATE( '$data_fim', '%d/%m/%Y' )";

        } else {

            $data_f = !empty( $data_ini ) ? $data_ini : $data_fim;

            $clausula_data = "DATE( `cp_data_hora` ) = STR_TO_DATE( '$data_f', '%d/%m/%Y' )";

        }

    }

    $query = "SELECT
                ROUND( AVG( `cp_trans_na` ), 0 ) AS `trans_na`,
                ROUND( AVG( `cp_trans_da` ), 0 ) AS `trans_da`,
                ROUND( AVG( `cp_trans_nada` ), 0 ) AS `trans_nada`,
                ROUND( AVG( `cp_pop_nada` ), 0 ) AS `pop_nada`,
                ROUND( AVG( `cp_pop_na` ), 0 ) AS `pop_na`,
                ROUND( AVG( `cp_pop_da` ), 0 ) AS `pop_da`,
                ROUND( AVG( `cp_pop_total` ), 0 ) AS `pop_total`
              FROM
                `cont_pop`
              WHERE
                $clausula_data";


    $db = SicopModel::getInstance();
    $query = $db->query( $query );

    if ( !$query ) {

        // pegar a mensagem de erro mysql
        $msg_err_mysql = $db->getErrorMsg();
        $db->closeConnection();

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Falha na consulta ( $motivo_pag ).\n\n $msg_err_mysql";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );


        echo msg_js( 'FALHA!', 1 );
        exit;

    }

    $db->closeConnection();

    $cont = $query->num_rows;
    if ( $cont < 1 ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = "A consulta retornou 0 ocorrências ( $motivo_pag ).";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        echo msg_js( 'FALHA!', 1 );
        exit;

    }

} // /if ( !empty( $busca ) ) {

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Contagem populacional por período';

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

            <p class="descript_page">CONTAGEM POPULACIONAL POR DATA</p>

            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="cont_pop" id="cont_pop">

                <p class="table_leg">Digite ou escolha a data:</p>
                <p class="table_leg">Caso escolha duas datas (período) será apresetado a média populacional do periodo.</p>

                <table class="busca_form">
                    <tr>
                        <td width="41" align="right">Entre:</td>
                        <td width="135"><input name="data_ini" type="text" class="CaixaTexto" id="data_ini" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_ini_sf ?>" size="12" maxlength="10" />&nbsp;&nbsp;<a href="#" onclick="javascript: datahoje('data_ini'); return false;" >hoje</a></td>
                    </tr>
                    <tr>
                        <td align="right">e:</td>
                        <td><input name="data_fim" type="text" class="CaixaTexto" id="data_fim" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_fim_sf ?>" size="12" maxlength="10" />&nbsp;&nbsp;<a href="#" onclick="javascript: datahoje('data_fim'); return false;" >hoje</a></td>
                    </tr>
                </table>

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="buscar" id="buscar" value="Buscar" />
                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>

                <input name="busca" type="hidden" id="busca" value="busca" />

            </form>

            <input name="datahj" type="hidden" id="datahj" value="<?php echo date( 'd/m/Y' ) ?>" />

            <script type="text/javascript">

                $(function() {
                    $( "#data_ini" ).focus();
                    $( "#data_ini, #data_fim" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });
                });

            </script>

            <?php

            if ( !empty( $_GET['busca'] ) ) {

                $dados = $query->fetch_assoc();

                $todos_null = true;
                foreach ( $dados as $key => $value ) {

                    if ( !empty( $value ) ) $todos_null = false;

                }

                if ( $todos_null ) {

                    echo '<p class="p_q_no_result">Não há registros para esta data.</p>';

                } else {

            ?>

            <table class="contagem">
                <tr>
                    <td class="soma_pop_mid">Transito na casa: <?php echo $dados['trans_na']; ?></td>
                    <td class="soma_pop_mid">Transito da casa: <?php echo $dados['trans_da']; ?></td>
                    <td class="soma_pop_mid">Transito na casa da casa: <?php echo $dados['trans_nada']; ?></td>
                </tr>
                <tr>
                    <td class="soma_pop_mid">Na casa da casa: <?php echo $dados['pop_nada']; ?></td>
                    <td class="soma_pop_mid">Na casa: <?php echo $dados['pop_na']; ?></td>
                    <td class="soma_pop_mid">Da casa: <?php echo $dados['pop_da']; ?></td>
                </tr>
                <tr>
                    <td class="soma_pop_grt" colspan="3">População total: <?php echo $dados['pop_total']; ?></td>
                </tr>
            </table>

            <?php
                } // /if ( $cont != 1 ) {
            } // /if ( !empty( $_GET['busca'] ) ) {
            ?>

<?php include 'footer.php'; ?>