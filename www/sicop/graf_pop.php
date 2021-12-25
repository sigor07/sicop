<?php
if ( !isset( $_SESSION ) ) session_start();

require 'init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$motivo_pag = 'GRÁFICO POPULACIONAL';

$busca       = get_get( 'busca' );
$data_ini_sf = '';
$data_fim_sf = '';
$group_by    = 0;
if ( !empty( $busca ) ) {

    $data_ini_sf = get_get( 'data_ini' );
    $data_ini    = get_get( 'data_ini', 'busca' );

    $data_fim_sf = get_get( 'data_fim' );
    $data_fim    = get_get( 'data_fim', 'busca' );

    $group_by    = get_get( 'group_by', 'int' );
    if ( empty ( $group_by ) ) $group_by = 1;

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

    $clausula_group = 'DAY( `cp_data_hora` )';

    switch ( $group_by ) {
        default:
        case 1:
            $clausula_group = 'YEAR( `cp_data_hora` ), MONTH( `cp_data_hora` ), DAY( `cp_data_hora` )';
            break;
        case 2:
            $clausula_group = 'YEAR( `cp_data_hora` ), MONTH( `cp_data_hora` )';
            break;
        case 3:
            $clausula_group = 'YEAR( `cp_data_hora` )';
            break;
    }


    $query = "SELECT
                UNIX_TIMESTAMP( `cp_data_hora` ) AS `timestamp`,
                MONTH( `cp_data_hora` ),
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
                $clausula_data
              GROUP BY
                $clausula_group";

    //depur($query);

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

$desc_pag = 'Gráfico populacional';

// adicionando o javascript
$cab_js   = array();
$cab_js[] = 'valida.js';
$cab_js[] = 'highcharts/js/highcharts.js';
$cab_js[] = 'highcharts/js/modules/exporting.js';
set_cab_js( $cab_js );

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>

            <p class="descript_page">GERAR GRÁFICO POPULACIONAL</p>

            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="cont_pop" id="cont_pop">

                <p class="table_leg">Digite ou escolha a data:</p>
                <p class="table_leg">
                    Caso escolha uma forma de agrupamento que não seja dia, será apresentado a média populacional de acordo com o agrupamento.
                </p>

                <table class="busca_form">
                    <tr>
                        <td width="70" align="right">Entre:</td>
                        <td width="180"><input name="data_ini" type="text" class="CaixaTexto" id="data_ini" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_ini_sf ?>" size="12" maxlength="10" />&nbsp;&nbsp;<a href="#" onclick="javascript: datahoje('data_ini'); return false;" >hoje</a></td>
                    </tr>
                    <tr>
                        <td align="right">e:</td>
                        <td><input name="data_fim" type="text" class="CaixaTexto" id="data_fim" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_fim_sf ?>" size="12" maxlength="10" />&nbsp;&nbsp;<a href="#" onclick="javascript: datahoje('data_fim'); return false;" >hoje</a></td>
                    </tr>
                    <tr>
                        <td align="right">Agrupar por:</td>
                        <td>
                            <input name="group_by" type="radio" id="group_by_0" value="1" <?php echo ( !empty( $_GET['busca'] ) and $group_by == 1 or empty( $group_by ) ) ? 'checked="checked"' : ''; ?> /> dias &nbsp;
                            <input name="group_by" type="radio" id="group_by_1" value="2" <?php echo ( !empty( $_GET['busca'] ) and $group_by == 2 ) ? 'checked="checked"' : ''; ?> /> meses &nbsp;
                            <input name="group_by" type="radio" id="group_by_2" value="3" <?php echo ( !empty( $_GET['busca'] ) and $group_by == 3 ) ? 'checked="checked"' : ''; ?> /> anos
                        </td>
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

//                $dados = $query->fetch_assoc();
//
                $todos_null = true;
//                foreach ( $dados as $key => $value ) {
//
//                    if ( !empty( $value ) ) $todos_null = false;
//
//                }

                if ( !$todos_null ) {

                    echo '<p class="p_q_no_result">Não há registros para esta data.</p>';

                } else {

                    $data_x            = '';

                    // $eixo_x é onde vai as datas
                    $eixo_x            = '';

                    // os eixos_y são onde vai o número de detentos
                    $eixo_y_trans_na   = '';
                    $eixo_y_trans_da   = '';
                    $eixo_y_trans_nada = '';
                    $eixo_y_nada       = '';
                    $eixo_y_na         = '';
                    $eixo_y_da         = '';
                    $eixo_y_total      = '';

                    while ( $dados = $query->fetch_assoc() ) {

                        $timestamp  = $dados['timestamp'];
                        $trans_na   = $dados['trans_na'];
                        $trans_da   = $dados['trans_da'];
                        $trans_nada = $dados['trans_nada'];
                        $pop_nada   = $dados['pop_nada'];
                        $pop_na     = $dados['pop_na'];
                        $pop_da     = $dados['pop_da'];
                        $pop_total  = $dados['pop_total'];

                        if ( $group_by == 1 ) {
                            $data_x = date( 'd/m/Y', $timestamp );
                        } else if ( $group_by == 2 ) {
                            $data_x = get_mes_f( $timestamp, '', TRUE, TRUE );
                        } else {
                            $data_x = date( 'Y', $timestamp );
                        }


                        $eixo_x            .= "'$data_x',";
                        //$eixo_x            .= "$timestamp,";
                        $eixo_y_trans_na   .= "$trans_na,";
                        $eixo_y_trans_da   .= "$trans_da,";
                        $eixo_y_trans_nada .= "$trans_nada,";
                        $eixo_y_nada       .= "$pop_nada,";
                        $eixo_y_na         .= "$pop_na,";
                        $eixo_y_da         .= "$pop_da,";
                        $eixo_y_total      .= "$pop_total,";


                    }

                    // retirar as últimas virgulas
                    $eixo_x            = substr( $eixo_x, 0, -1 );
                    $eixo_y_trans_na   = substr( $eixo_y_trans_na, 0, -1 );
                    $eixo_y_trans_da   = substr( $eixo_y_trans_da, 0, -1 );
                    $eixo_y_trans_nada = substr( $eixo_y_trans_nada, 0, -1 );
                    $eixo_y_nada       = substr( $eixo_y_nada, 0, -1 );
                    $eixo_y_na         = substr( $eixo_y_na, 0, -1 );
                    $eixo_y_da         = substr( $eixo_y_da, 0, -1 );
                    $eixo_y_total      = substr( $eixo_y_total, 0, -1 );


            ?>

                <script type="text/javascript">

                    var chart;
                    $(function() {
                        chart = new Highcharts.Chart({
                            chart: {
                                renderTo: 'container',
                                defaultSeriesType: 'line',
                                marginRight: 160,
                                marginBottom: 30
                            },
                            credits: {
                              text: "Sicop",
                              href: "/sicop/"
                            },
                            title: {
                                text: 'QUANTIDADE DE <?php echo SICOP_DET_DESC_U; ?>S POR PERÍODO',
                                x: -20 //center
                            },
                            subtitle: {
                                text: 'MÉDIA MENSAL',
                                x: -20
                            },
                            xAxis: {
//                                type: 'datetime',
//                                tickInterval: 7 * 24 * 3600 * 1000, // one week
//                                tickWidth: 0,
//                                gridLineWidth: 1,
//                                labels: {
//                                        align: 'left',
//                                        x: 3,
//                                        y: -3
//                                },

                                categories: [<?php echo $eixo_x; ?>]
                            },
                            yAxis: {
                                title: {
                                    text: 'QUANTIDADE DE <?php echo SICOP_DET_DESC_U; ?>S'
                                },
                                //min: 0,
                                plotLines: [{
                                    value: 0,
                                    width: 1,
                                    color: '#808080'
                                }]
                            },
                            labels: {
                                formatter: function() {
                                        return '<b>'+ this.series.name +'</b><br/>'+
                                        this.x +': '+ this.y +' <?php echo SICOP_DET_DESC_L; ?>s';
                                }
                            },

                            tooltip: {
                                crosshairs: true,
                                shared: true
                            },

                            legend: {
                                layout: 'vertical',
                                align: 'right',
                                verticalAlign: 'top',
                                x: -10,
                                y: 100,
                                borderWidth: 0
                            },
                            series: [{
                                name: 'Na casa',
                                color: '#000000',
                                data: [<?php echo $eixo_y_na; ?>]
                            }, {
                                name: 'Da casa',
                                color: '#666666',
                                data: [<?php echo $eixo_y_da; ?>]
                            }, {
                                name: 'Na cada Da casa',
                                color: '#BBBBBB',
                                data: [<?php echo $eixo_y_nada; ?>]
                            }, {
                                name: 'Total',
                                color: '#00DD00',
                                data: [<?php echo $eixo_y_total; ?>]
                            }, {
                                name: "Transito na casa <br /> da casa",
                                color: '#666666',
                                data: [<?php echo $eixo_y_trans_nada; ?>]
                            }, {
                                name: 'Transito na casa',
                                color: '#0000FF',
                                data: [<?php echo $eixo_y_trans_na; ?>]
                            }, {
                                name: 'Transito da casa',
                                color: '#FF0000',
                                data: [<?php echo $eixo_y_trans_da; ?>]
                            }]
                        });


                    });

                </script>

                <div id="container" style="height: 500px; margin: 10px auto; width: 900px;"></div>

            <?php
                } // /if ( $cont != 1 ) {
            } // /if ( !empty( $_GET['busca'] ) ) {
            ?>

<?php include 'footer.php'; ?>