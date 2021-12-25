<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';
$motivo_pag = 'RELATÓRIOS DE ENTRADA - ROL DE VISITAS';


$n_rol   = get_session( 'n_rol', 'int' );
$n_rol_n = 2;

if ($n_rol < $n_rol_n) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'RELATÓRIOS DE ENTRADA - ROL DE VISITAS';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;
}

$data_ini_sf = '';
$data_fim_sf = '';

if( !empty( $_GET ) ) {

    $mensagem = "Acesso à página $pag";
    salvaLog( $mensagem );

    $data_ini_sf = $_GET['data_ini'];
    $data_ini    = get_get( 'data_ini', 'busca' );

    $data_fim_sf = $_GET['data_fim'];
    $data_fim    = get_get( 'data_fim', 'busca' );

    if ( empty( $data_ini ) and empty( $data_fim ) ){
        echo msg_js( '', 1 );
        exit;
    }

    $clausula_data = '';

    if ( !empty( $data_ini ) or !empty( $data_fim ) ){

        if ( !empty( $data_ini ) and  !empty( $data_fim ) ){

            $clausula_data = "( DATE( `visita_mov`.`data_in` ) BETWEEN STR_TO_DATE( '$data_ini', '%d/%m/%Y' ) AND STR_TO_DATE( '$data_fim', '%d/%m/%Y' ) )";

        } else {

            $data_f = !empty( $data_ini ) ? $data_ini : $data_fim;

            $clausula_data = " DATE( `visita_mov`.`data_in` ) = STR_TO_DATE( '$data_f', '%d/%m/%Y' ) ";

        }

    }

    if ( empty( $clausula_data ) ){
        echo msg_js( 'FALHA!', 1 );
        exit;
    }

    $where_data = " WHERE $clausula_data";

    /*
     *
     * CONTAGEM DE VISITANTES
     *
     */

    $q_v_base = 'SELECT
                   COUNT( `visita_mov`.`cod_visita` ) AS total
                 FROM
                   `visita_mov`';

    $join = 'INNER JOIN `visitas` ON `visita_mov`.`cod_visita` = `visitas`.`idvisita`';

    $q_v = array(
        'ma'    => $q_v_base . $join . $where_data . ' AND `visitas`.`sexo_visit` = "M" AND `visita_mov`.`adulto` = TRUE',  // masculino adulto
        'fa'    => $q_v_base . $join . $where_data . ' AND `visitas`.`sexo_visit` = "F" AND `visita_mov`.`adulto` = TRUE',  // feminino adulto
        'ta'    => $q_v_base . $where_data . ' AND `visita_mov`.`adulto` = TRUE',                                           // total adulto

        'mm'    => $q_v_base . $join . $where_data . ' AND `visitas`.`sexo_visit` = "M" AND `visita_mov`.`adulto` = FALSE', // masculino menor
        'fm'    => $q_v_base . $join . $where_data . ' AND `visitas`.`sexo_visit` = "F" AND `visita_mov`.`adulto` = FALSE', // feminino menor
        'tm'    => $q_v_base . $where_data . ' AND `visita_mov`.`adulto` = FALSE',                                          // total menor

        'tmasc' => $q_v_base . $join . $where_data . ' AND `visitas`.`sexo_visit` = "M"',                                   // total masculino
        'tfem'  => $q_v_base . $join . $where_data . ' AND `visitas`.`sexo_visit` = "F"',                                   // total feminino
        'tt'    => $q_v_base . $where_data                                                                                  // total

    );

    /*
     *
     * CONTAGEM DE VISITANTES COM JUMBO - SOMENTE ADULTOS
     *
     */

    $q_vj = array(
        'jm' => $q_v_base . $join . $where_data . ' AND `visitas`.`sexo_visit` = "M" AND `visita_mov`.`adulto` = TRUE  AND `visita_mov`.`jumbo` = TRUE',  // jumbo masculino
        'jf' => $q_v_base . $join . $where_data . ' AND `visitas`.`sexo_visit` = "F" AND `visita_mov`.`adulto` = TRUE  AND `visita_mov`.`jumbo` = TRUE',  // jumbo feminino
        'jt' => $q_v_base . $where_data . ' AND `visita_mov`.`adulto` = TRUE  AND `visita_mov`.`jumbo` = TRUE'                                            // jumbo total
    );


    /*
     *
     * CONTAGEM DE DETENTOS
     *
     */
    $q_det_base = "SELECT
                     COUNT( DISTINCT `visitas`.`cod_detento`, DATE( `visita_mov`.`data_in` ) ) AS total
                   FROM
                     `visita_mov`
                     INNER JOIN `visitas` ON `visita_mov`.`cod_visita` = `visitas`.`idvisita`
                   $where_data";

    // detentos que receberam visitantes...
    $q_det = array(
        'ma'    => $q_det_base . ' AND `visitas`.`sexo_visit` = "M" AND `visita_mov`.`adulto` = TRUE',  // masculino adulto
        'fa'    => $q_det_base . ' AND `visitas`.`sexo_visit` = "F" AND `visita_mov`.`adulto` = TRUE',  // feminino adulto
        'ta'    => $q_det_base . ' AND `visita_mov`.`adulto` = TRUE',                                   // total adulto

        'mm'    => $q_det_base . ' AND `visitas`.`sexo_visit` = "M" AND `visita_mov`.`adulto` = FALSE', // masculino menor
        'fm'    => $q_det_base . ' AND `visitas`.`sexo_visit` = "F" AND `visita_mov`.`adulto` = FALSE', // feminino menor
        'tm'    => $q_det_base . ' AND `visita_mov`.`adulto` = FALSE',                                  // total menor

        'tmasc' => $q_det_base . ' AND `visitas`.`sexo_visit` = "M"',                                   // total masculino
        'tfem'  => $q_det_base . ' AND `visitas`.`sexo_visit` = "F"',                                   // total feminino
        'tt'    => $q_det_base                                                                          // total
    );



    /*
     *
     * CONTAGEM DE VISITANTES POR RAIO
     *
     */

    $q_v_raio_base = 'SELECT
                        `raio`.`raio`,
                        COUNT( `visita_mov`.`cod_visita` ) AS total
                      FROM
                        `visita_mov`';

    $join_visit = ' INNER JOIN `visitas` ON `visita_mov`.`cod_visita` = `visitas`.`idvisita`';
    $join_raio  = ' INNER JOIN `raio` ON `visita_mov`.`raio_det` = `raio`.`idraio`';
    $group      = ' GROUP BY `raio`.`raio`';

    // visitantes por raio ...
    $q_v_raio = array(
        'ma'    => $q_v_raio_base . $join_visit . $join_raio . $where_data . ' AND `visitas`.`sexo_visit` = "M" AND `visita_mov`.`adulto` = TRUE' . $group,  // masculino adulto
        'fa'    => $q_v_raio_base . $join_visit . $join_raio . $where_data . ' AND `visitas`.`sexo_visit` = "F" AND `visita_mov`.`adulto` = TRUE' . $group,  // feminino adulto
        'ta'    => $q_v_raio_base . $join_raio . $where_data . ' AND `visita_mov`.`adulto` = TRUE' . $group,                                                 // total adulto

        'mm'    => $q_v_raio_base . $join_visit . $join_raio . $where_data . ' AND `visitas`.`sexo_visit` = "M" AND `visita_mov`.`adulto` = FALSE' . $group, // masculino menor
        'fm'    => $q_v_raio_base . $join_visit . $join_raio . $where_data . ' AND `visitas`.`sexo_visit` = "F" AND `visita_mov`.`adulto` = FALSE' . $group, // feminino menor
        'tm'    => $q_v_raio_base . $join_raio . $where_data . ' AND `visita_mov`.`adulto` = FALSE' . $group,                                                // total menor

        'tmasc' => $q_v_raio_base . $join_visit . $join_raio . $where_data . ' AND `visitas`.`sexo_visit` = "M"' . $group,                                   // total masculino
        'tfem'  => $q_v_raio_base . $join_visit . $join_raio . $where_data . ' AND `visitas`.`sexo_visit` = "F"' . $group,                                   // total feminino
        'tt'    => $q_v_raio_base . $join_raio . $where_data . $group
    );



    /*
     *
     * CONTAGEM DE DETENTOS POR RAIO
     *
     */

    $q_d_raio_base = "SELECT
                        `raio`.`raio`,
                        COUNT( DISTINCT `visitas`.`cod_detento` ) AS total
                      FROM
                        `visita_mov`
                        INNER JOIN `visitas` ON `visita_mov`.`cod_visita` = `visitas`.`idvisita`
                        INNER JOIN `raio` ON `visita_mov`.`raio_det` = `raio`.`idraio`";

    $group      = ' GROUP BY `raio`.`raio`';

    // detentos que receberam visita por raio ...
    $q_d_raio = array(
        'ma'    => $q_d_raio_base . $where_data . ' AND `visitas`.`sexo_visit` = "M" AND `visita_mov`.`adulto` = TRUE' . $group,  // masculino adulto
        'fa'    => $q_d_raio_base . $where_data . ' AND `visitas`.`sexo_visit` = "F" AND `visita_mov`.`adulto` = TRUE' . $group,  // feminino adulto
        'ta'    => $q_d_raio_base . $where_data . ' AND `visita_mov`.`adulto` = TRUE' . $group,                                                 // total adulto

        'mm'    => $q_d_raio_base . $where_data . ' AND `visitas`.`sexo_visit` = "M" AND `visita_mov`.`adulto` = FALSE' . $group, // masculino menor
        'fm'    => $q_d_raio_base . $where_data . ' AND `visitas`.`sexo_visit` = "F" AND `visita_mov`.`adulto` = FALSE' . $group, // feminino menor
        'tm'    => $q_d_raio_base . $where_data . ' AND `visita_mov`.`adulto` = FALSE' . $group,                                                // total menor

        'tmasc' => $q_d_raio_base . $where_data . ' AND `visitas`.`sexo_visit` = "M"' . $group,                                   // total masculino
        'tfem'  => $q_d_raio_base . $where_data . ' AND `visitas`.`sexo_visit` = "F"' . $group,                                   // total feminino
        'tt'    => $q_d_raio_base . $where_data . $group
    );



    $querytime_before = array_sum( explode( ' ', microtime() ) );

    // instanciando o model
    $model = SicopModel::getInstance();

    // executa as querys dos visitantes
    foreach ( $q_v as $key => $value ) {

        $q_v["$key"] = $model->fetchOne( $value );

        // caso uma das querys tenha falhado, encerra a execução do script
        if ( $q_v["$key"] === false ) {

            // gerar a mensagem q será salva no log
            $msg = sysmsg::create_msg();
            $msg->set_msg_type( SM_TYPE_ERR );
            $msg->add_quebras( 1 );
            $msg->set_msg_pre_def( SM_QUERY_FAIL );
            $msg->add_parenteses( $motivo_pag );
            $msg->add_quebras( 2 );
            $msg->set_msg( "ID DA CONSULTA - $key" );
            $msg->get_msg();

            echo msg_js( 'FALHA!!!', 1 );
            exit;

        }

    }

    // executa as querys dos visitantes com jumbo
    foreach ( $q_vj as $key => $value ) {

        $q_vj["$key"] = $model->fetchOne( $value );

        // caso uma das querys tenha falhado, encerra a execução do script
        if ( $q_vj["$key"] === false ) {

            // gerar a mensagem q será salva no log
            $msg = sysmsg::create_msg();
            $msg->set_msg_type( SM_TYPE_ERR );
            $msg->add_quebras( 1 );
            $msg->set_msg_pre_def( SM_QUERY_FAIL );
            $msg->add_parenteses( $motivo_pag );
            $msg->add_quebras( 2 );
            $msg->set_msg( "ID DA CONSULTA - $key" );
            $msg->get_msg();

            echo msg_js( 'FALHA!!!', 1 );
            exit;

        }

    }

    // executa as querys dos detentos que receberam visitas
    foreach ( $q_det as $key => $value ) {

        $q_det["$key"] = $model->fetchOne( $value );

        // caso uma das querys tenha falhado, encerra a execução do script
        if ( $q_det["$key"] === false ) {

            // gerar a mensagem q será salva no log
            $msg = sysmsg::create_msg();
            $msg->set_msg_type( SM_TYPE_ERR );
            $msg->add_quebras( 1 );
            $msg->set_msg_pre_def( SM_QUERY_FAIL );
            $msg->add_parenteses( $motivo_pag );
            $msg->add_quebras( 2 );
            $msg->set_msg( "ID DA CONSULTA - $key" );
            $msg->get_msg();

            echo msg_js( 'FALHA!!!', 1 );
            exit;

        }

    }

    // executa as querys dos visitantes por raio
    foreach ( $q_v_raio as $key => $value ) {

        $q_v_raio["$key"] = $model->query( $value );

        // caso uma das querys tenha falhado, encerra a execução do script
        if ( !$q_v_raio["$key"] ) {

            // gerar a mensagem q será salva no log
            $msg = sysmsg::create_msg();
            $msg->set_msg_type( SM_TYPE_ERR );
            $msg->add_quebras( 1 );
            $msg->set_msg_pre_def( SM_QUERY_FAIL );
            $msg->add_parenteses( $motivo_pag );
            $msg->add_quebras( 2 );
            $msg->set_msg( "ID DA CONSULTA - $key" );
            $msg->get_msg();

            echo msg_js( 'FALHA!!!', 1 );
            exit;

        }

    }

    // executa as querys dos detentos que receberam visitas
    foreach ( $q_d_raio as $key => $value ) {

        $q_d_raio["$key"] = $model->query( $value );

        // caso uma das querys tenha falhado, encerra a execução do script
        if ( !$q_d_raio["$key"] ) {

            // gerar a mensagem q será salva no log
            $msg = sysmsg::create_msg();
            $msg->set_msg_type( SM_TYPE_ERR );
            $msg->add_quebras( 1 );
            $msg->set_msg_pre_def( SM_QUERY_FAIL );
            $msg->add_parenteses( $motivo_pag );
            $msg->add_quebras( 2 );
            $msg->set_msg( "ID DA CONSULTA - $key" );
            $msg->get_msg();

            echo msg_js( 'FALHA!!!', 1 );
            exit;

        }

    }

    // fechando a conexao
    $model->closeConnection();

    $querytime_after = array_sum( explode( ' ', microtime() ) );

    $querytime = $querytime_after - $querytime_before;

    $v_ma = $q_v['ma'];
    $v_fa = $q_v['fa'];
    $v_ta = $q_v['ta'];

    $v_mm = $q_v['mm'];
    $v_fm = $q_v['fm'];
    $v_tm = $q_v['tm'];

    $v_tmasc = $q_v['tmasc'];
    $v_tfem  = $q_v['tfem'];
    $v_tt    = $q_v['tt'];


    $v_jm = $q_vj['jm'];
    $v_jf = $q_vj['jf'];
    $v_jt = $q_vj['jt'];


    $det_ma    = $q_det['ma'];
    $det_fa    = $q_det['fa'];
    $det_ta    = $q_det['ta'];

    $det_mm    = $q_det['mm'];
    $det_fm    = $q_det['fm'];
    $det_tm    = $q_det['tm'];

    $det_tmasc = $q_det['tmasc'];
    $det_tfem  = $q_det['tfem'];
    $det_tt    = $q_det['tt'];


    $q_v_raio_ma = $q_v_raio['ma'];
    $q_v_raio_fa = $q_v_raio['fa'];
    $q_v_raio_ta = $q_v_raio['ta'];

    $q_v_raio_mm = $q_v_raio['mm'];
    $q_v_raio_fm = $q_v_raio['fm'];
    $q_v_raio_tm = $q_v_raio['tm'];

    $q_v_raio_m = $q_v_raio['tmasc'];
    $q_v_raio_f = $q_v_raio['tfem'];
    $q_v_raio_t = $q_v_raio['tt'];

    $q_d_raio_ma = $q_d_raio['ma'];
    $q_d_raio_fa = $q_d_raio['fa'];
    $q_d_raio_ta = $q_d_raio['ta'];

    $q_d_raio_mm = $q_d_raio['mm'];
    $q_d_raio_fm = $q_d_raio['fm'];
    $q_d_raio_tm = $q_d_raio['tm'];

    $q_d_raio_m = $q_d_raio['tmasc'];
    $q_d_raio_f = $q_d_raio['tfem'];
    $q_d_raio_t = $q_d_raio['tt'];




}

$desc_pag = 'Relatórios de entrada de visitantes';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add($desc_pag, $pag_atual, 3);
$trail->output();
?>

            <p class="descript_page">RELÁTORIOS E ESTATÍSTICAS DE ENTRADA DE VISITANTES</p>

            <form action="relat_entr.php" method="get" name="relat_entr" id="relat_entr">

                <p class="table_leg">Digite ou escolha a data:</p>

                <table width="163" align="center">

                    <tr>
                        <td width="41" align="right">Entre:</td>
                        <td width="110"><input name="data_ini" type="text" class="CaixaTexto" id="data_ini" onBlur="verifica_data(this, this.value)" onKeyPress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_ini_sf ?>" size="12" maxlength="10" /></td>
                    </tr>
                    <tr>
                        <td align="right">e:</td>
                        <td><input name="data_fim" type="text" class="CaixaTexto" id="data_fim" onBlur="verifica_data(this, this.value)" onKeyPress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_fim_sf ?>" size="12" maxlength="10" /></td>
                    </tr>
                </table>

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="buscar" id="buscar" value="Buscar" />
                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

            <script type="text/javascript">

                $(function() {
                    $( "#data_ini" ).focus();
                    $( "#data_ini, #data_fim" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });
                });

            </script>

            <?php if ( !empty($_GET) ) {?>

            <p class="p_q_info">Tempo gastro para a pesquisa: <?php echo round($querytime, 2) ?> seg</p>

            <table class="lista_busca">
                <tr >
                    <th colspan="4" scope="col" class="relat_visit">VISITANTES</th>
                </tr>
                <tr class="even">
                    <th scope="col" class="relat_visit">&nbsp;</th>
                    <th scope="col" class="relat_visit">Masculino</th>
                    <th scope="col" class="relat_visit">Feminino</th>
                    <th scope="col" class="relat_visit">Total</th>
                </tr>
                <tr class="even">
                    <th scope="row" class="relat_visit">Adulto</th>
                    <td align="center"><?php echo $v_ma; ?></td>
                    <td align="center"><?php echo $v_fa; ?></td>
                    <td align="center"><?php echo $v_ta; ?></td>
                </tr>
                <tr class="even">
                    <th scope="row" class="relat_visit">Menor</th>
                    <td align="center"><?php echo $v_mm; ?></td>
                    <td align="center"><?php echo $v_fm; ?></td>
                    <td align="center"><?php echo $v_tm; ?></td>
                </tr>
                <tr class="even">
                    <th scope="row" class="relat_visit">Total</th>
                    <td align="center"><?php echo $v_tmasc; ?></td>
                    <td align="center"><?php echo $v_tfem; ?></td>
                    <td align="center"><?php echo $v_tt; ?></td>
                </tr>
            </table>


            <table class="lista_busca" style="margin-top: 10px;">
                <tr>
                    <th colspan="4" scope="col" class="relat_visit">VISITANTES - COM JUMBO</th>
                </tr>
                <tr class="even">
                    <th scope="col" class="relat_visit">&nbsp;</th>
                    <th scope="col" class="relat_visit">Masculino</th>
                    <th scope="col" class="relat_visit">Feminino</th>
                    <th scope="col" class="relat_visit">Total</th>
                </tr>
                <tr class="even">
                    <th scope="row" class="relat_visit">Adulto</th>
                    <td align="center"><?php echo $v_jm; ?></td>
                    <td align="center"><?php echo $v_jf; ?></td>
                    <td align="center"><?php echo $v_jt; ?></td>
                </tr>
            </table>


            <table class="lista_busca" style="margin-top: 10px;">
                <tr>
                    <th colspan="4" scope="col" class="relat_visit"><?php echo SICOP_DET_DESC_U; ?>S QUE RECEBERAM VISITANES:</th>
                </tr>
                <tr class="even">
                    <th scope="col" class="relat_visit">&nbsp;</th>
                    <th scope="col" class="relat_visit">Masculino</th>
                    <th scope="col" class="relat_visit">Feminino</th>
                    <th scope="col" class="relat_visit">Total</th>
                </tr>
                <tr class="even">
                    <th scope="row" class="relat_visit">Adulto</th>
                    <td align="center"><?php echo $det_ma; ?></td>
                    <td align="center"><?php echo $det_fa; ?></td>
                    <td align="center"><?php echo $det_ta; ?></td>
                </tr>
                <tr class="even">
                    <th scope="row" class="relat_visit">Menor</th>
                    <td align="center"><?php echo $det_mm; ?></td>
                    <td align="center"><?php echo $det_fm; ?></td>
                    <td align="center"><?php echo $det_tm; ?></td>
                </tr>
                <tr class="even">
                    <th scope="row" class="relat_visit">Total</th>
                    <td align="center"><?php echo $det_tmasc; ?></td>
                    <td align="center"><?php echo $det_tfem; ?></td>
                    <td align="center"><?php echo $det_tt; ?></td>
                </tr>
            </table>


            <table class="lista_busca" style="margin-top: 10px;">
                <tr>
                    <th colspan="4" scope="col" class="relat_visit">VISITANTES POR <?php echo mb_strtoupper( SICOP_RAIO ) ?>:</th>
                </tr>
                <tr class="even">
                    <th scope="col" class="relat_visit">&nbsp;</th>
                    <th scope="col" class="relat_visit">Masculino</th>
                    <th scope="col" class="relat_visit">Feminino</th>
                    <th scope="col" class="relat_visit">Total</th>
                </tr>
                <tr class="even">
                    <th scope="row" class="relat_visit">Adulto</th>
                    <td align="center"><br />
                    <?php
                    while( $v_raio_ma = $q_v_raio_ma->fetch_assoc() ) {
                        echo SICOP_RAIO . ' ' . $v_raio_ma['raio'] . ' - ' . $v_raio_ma['total'] . '<br /><br />';
                    }
                    ?>
                    </td>
                    <td align="center"><br />
                    <?php
                    while( $v_raio_fa = $q_v_raio_fa->fetch_assoc() ) {
                        echo SICOP_RAIO . ' ' . $v_raio_fa['raio'] . ' - ' . $v_raio_fa['total'] . '<br /><br />';
                    }
                    ?>
                    </td>
                    <td align="center"><br />
                    <?php
                    while( $v_raio_ta = $q_v_raio_ta->fetch_assoc() ) {
                        echo SICOP_RAIO . ' ' . $v_raio_ta['raio'] . ' - ' . $v_raio_ta['total'] . '<br /><br />';
                    }
                    ?>
                    </td>
                </tr>
                    <tr class="even">
                    <th scope="row" class="relat_visit">Menor</th>
                    <td align="center"><br />
                    <?php
                    while( $v_raio_mm = $q_v_raio_mm->fetch_assoc() ) {
                        echo SICOP_RAIO . ' ' . $v_raio_mm['raio'] . ' - ' . $v_raio_mm['total'] . '<br /><br />';
                    }
                    ?>
                    </td>
                    <td align="center"><br />
                    <?php
                    while( $v_raio_fm = $q_v_raio_fm->fetch_assoc() ) {
                        echo SICOP_RAIO . ' ' . $v_raio_fm['raio'] . ' - ' . $v_raio_fm['total'] . '<br /><br />';
                    }
                    ?>
                    </td>
                    <td align="center"><br />
                    <?php
                    while( $v_raio_tm = $q_v_raio_tm->fetch_assoc() ) {
                        echo SICOP_RAIO . ' ' . $v_raio_tm['raio'] . ' - ' . $v_raio_tm['total'] . '<br /><br />';
                    }
                    ?>
                    </td>
                </tr>
                <tr class="even">
                    <th scope="row" class="relat_visit">Total</th>
                    <td align="center"><br />
                    <?php
                    while( $v_raio_m = $q_v_raio_m->fetch_assoc() ) {
                        echo SICOP_RAIO . ' ' . $v_raio_m['raio'] . ' - ' . $v_raio_m['total'] . '<br /><br />';
                    }
                    ?>
                    </td>
                    <td align="center"><br />
                    <?php
                    while( $v_raio_f = $q_v_raio_f->fetch_assoc() ) {
                        echo SICOP_RAIO . ' ' . $v_raio_f['raio'] . ' - ' . $v_raio_f['total'] . '<br /><br />';
                    }
                    ?>
                    </td>
                    <td align="center"><br />
                    <?php
                    while( $v_raio_t = $q_v_raio_t->fetch_assoc() ) {
                        echo SICOP_RAIO . ' ' . $v_raio_t['raio'] . ' - ' . $v_raio_t['total'] . '<br /><br />';
                    }
                    ?>
                    </td>
                </tr>
            </table>


            <table class="lista_busca" style="margin-top: 10px;">
                <tr>
                    <th colspan="4" scope="col" class="relat_visit"><?php echo SICOP_DET_DESC_U; ?>S QUE RECEBERAM VISITANTES POR <?php echo mb_strtoupper( SICOP_RAIO ) ?>:</th>
                </tr>
                <tr class="even">
                    <th scope="col" class="relat_visit">&nbsp;</th>
                    <th scope="col" class="relat_visit">Masculino</th>
                    <th scope="col" class="relat_visit">Feminino</th>
                    <th scope="col" class="relat_visit">Total</th>
                </tr>
                <tr class="even">
                    <th scope="row" class="relat_visit">Adulto</th>
                    <td align="center"><br />
                    <?php
                    while( $d_raio_ma = $q_d_raio_ma->fetch_assoc() ) {
                        echo SICOP_RAIO . ' ' . $d_raio_ma['raio'] . ' - ' . $d_raio_ma['total'] . '<br /><br />';
                    }
                    ?>
                    </td>
                    <td align="center"><br />
                    <?php
                    while( $d_raio_fa = $q_d_raio_fa->fetch_assoc() ) {
                        echo SICOP_RAIO . ' ' . $d_raio_fa['raio'] . ' - ' . $d_raio_fa['total'] . '<br /><br />';
                    }
                    ?>
                    </td>
                    <td align="center"><br />
                    <?php
                    while( $d_raio_ta = $q_d_raio_ta->fetch_assoc() ) {
                        echo SICOP_RAIO . ' ' . $d_raio_ta['raio'] . ' - ' . $d_raio_ta['total'] . '<br /><br />';
                    }
                    ?>
                    </td>
                </tr>
                <tr class="even">
                    <th scope="row" class="relat_visit">Menor</th>
                    <td align="center"><br />
                    <?php
                    while( $d_raio_mm = $q_d_raio_mm->fetch_assoc() ) {
                        echo SICOP_RAIO . ' ' . $d_raio_mm['raio'] . ' - ' . $d_raio_mm['total'] . '<br /><br />';
                    }
                    ?>
                    </td>
                    <td align="center"><br />
                    <?php
                    while( $d_raio_fm = $q_d_raio_fm->fetch_assoc() ) {
                        echo SICOP_RAIO . ' ' . $d_raio_fm['raio'] . ' - ' . $d_raio_fm['total'] . '<br /><br />';
                    }
                    ?>
                    </td>
                    <td align="center"><br />
                    <?php
                    while( $d_raio_tm = $q_d_raio_tm->fetch_assoc() ) {
                        echo SICOP_RAIO . ' ' . $d_raio_tm['raio'] . ' - ' . $d_raio_tm['total'] . '<br /><br />';
                    }
                    ?>
                    </td>
                </tr>
                <tr class="even">
                    <th scope="row" class="relat_visit">Total</th>
                    <td align="center"><br />
                    <?php
                    while( $d_raio_m = $q_d_raio_m->fetch_assoc() ) {
                        echo SICOP_RAIO . ' ' . $d_raio_m['raio'] . ' - ' . $d_raio_m['total'] . '<br /><br />';
                    }
                    ?>
                    </td>
                    <td align="center"><br />
                    <?php
                    while( $d_raio_f = $q_d_raio_f->fetch_assoc() ) {
                        echo SICOP_RAIO . ' ' . $d_raio_f['raio'] . ' - ' . $d_raio_f['total'] . '<br /><br />';
                    }
                    ?>
                    </td>
                    <td align="center"><br />
                    <?php
                    while( $d_raio_t = $q_d_raio_t->fetch_assoc() ) {
                        echo SICOP_RAIO . ' ' . $d_raio_t['raio'] . ' - ' . $d_raio_t['total'] . '<br /><br />';
                    }
                    ?>
                    </td>
                </tr>
            </table>

            <?php }?>

<?php include 'footer.php'; ?>