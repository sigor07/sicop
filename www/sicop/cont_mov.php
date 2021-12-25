<?php
if ( !isset( $_SESSION ) ) session_start();

require 'init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$data_ini_sf = '';
$data_fim_sf = '';

if ( !empty( $_GET['busca'] ) ) {

    $mensagem = "Acesso à página $pag";
    salvaLog( $mensagem );

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

            $clausula_data = "( `mov_det`.`data_mov` BETWEEN STR_TO_DATE( '$data_ini', '%d/%m/%Y' ) AND STR_TO_DATE( '$data_fim', '%d/%m/%Y' ) )";
        } else {

            $data_f = !empty( $data_ini ) ? $data_ini : $data_fim;

            $clausula_data = "`mov_det`.`data_mov` = STR_TO_DATE( '$data_f', '%d/%m/%Y' ) ";
        }
    }

/*
 * montar as querys das contagens de movimetações
 */

// 8 = in + it + ir + ie + ex + et + er + ee
$tipo_movs = 8;
$q_mov     = array();

for ( $index = 1; $index <= $tipo_movs; $index++ ) {

    $q_mov["$index"] = "SELECT
                          COUNT(`mov_det`.`cod_tipo_mov`) AS `totalmov`
                        FROM
                          `mov_det`
                          INNER JOIN `tipomov` ON `mov_det`.`cod_tipo_mov` = `tipomov`.`idtipo_mov`
                        WHERE
                          $clausula_data
                          AND
                          `mov_det`.`cod_tipo_mov` = $index";

}

/*
 * -----------------------------------------------------
 */


    $q_cont_mov_al = "SELECT
                        COUNT(`mov_det`.`cod_tipo_mov`) AS `totalmov`
                      FROM
                        `mov_det`
                        INNER JOIN `tipomov` ON `mov_det`.`cod_tipo_mov` = `tipomov`.`idtipo_mov`
                      WHERE
                        $clausula_data
                        AND
                        `mov_det`.`cod_tipo_mov` = 5
                        AND
                        `mov_det`.`cod_local_mov` BETWEEN 100 AND 199";

    $q_cont_mov_ob = "SELECT
                        COUNT(`mov_det`.`cod_tipo_mov`) AS `totalmov`
                      FROM
                        `mov_det`
                        INNER JOIN `tipomov` ON `mov_det`.`cod_tipo_mov` = `tipomov`.`idtipo_mov`
                      WHERE
                        $clausula_data
                        AND
                        `mov_det`.`cod_tipo_mov` = 5
                        AND
                        `mov_det`.`cod_local_mov` BETWEEN 300 AND 399";

    $q_cont_mov_ev = "SELECT
                        COUNT(`mov_det`.`cod_tipo_mov`) AS `totalmov`
                      FROM
                        `mov_det`
                        INNER JOIN `tipomov` ON `mov_det`.`cod_tipo_mov` = `tipomov`.`idtipo_mov`
                      WHERE
                        $clausula_data
                        AND
                        `mov_det`.`cod_tipo_mov` = 5
                        AND
                        `mov_det`.`cod_local_mov` BETWEEN 200 AND 299";

    $db = SicopModel::getInstance();

    // executa as querys das movimentações
    foreach ( $q_mov as $key => $value ) {

        $q_mov["$key"] = $db->fetchOne( $q_mov["$key"] );

    }

    $d_mov_al = $db->fetchOne( $q_cont_mov_al );
    $d_mov_ob = $db->fetchOne( $q_cont_mov_ob );
    $d_mov_ev = $db->fetchOne( $q_cont_mov_ev );

    $db->closeConnection();

    $d_mov_in = $q_mov['1'];
    $d_mov_it = $q_mov['2'];
    $d_mov_ir = $q_mov['3'];
    $d_mov_ie = $q_mov['4'];
    $d_mov_ex = $q_mov['5'];
    $d_mov_et = $q_mov['6'];
    $d_mov_er = $q_mov['7'];
    $d_mov_ee = $q_mov['8'];


    $qs_data = "data_ini=$data_ini_sf&data_fim=$data_fim_sf";

}

$desc_pag = 'Contagem de movimentações';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) )
    $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 4 );
$trail->output();
?>


            <p class="descript_page">LISTAR MOVIMENTAÇÕES POR DATA</p>

            <form action="cont_mov.php" method="get" name="relat_entr" id="relat_entr">

                <p class="table_leg">Digite ou escolha a data:</p>

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

            <?php if ( !empty( $_GET['busca'] ) ) { ?>

            <table class="cont_mov" style="margin-top: 10px;">
                <tr>
                    <th>TIPO DE MOVIMENTAÇÃO</th>
                    <th>QUANTIDADE</th>
                </tr>
                <tr class="even">
                    <td class="cont_desc">
                        <?php
                        if ( empty( $d_mov_in ) ) {
                            echo 'Inclusão';
                        } else {
                        ?>
                        <a href="listamov.php?tipomov=1&<?php echo $qs_data; ?>" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s" >Inclusão</a>
                        <?php } ?>
                    </td>
                    <td class="cont_result"><?php echo $d_mov_in; ?></td>
                </tr>
                <tr class="even">
                    <td class="cont_desc">
                        <?php
                        if ( empty( $d_mov_it ) ) {
                            echo 'Inclusão por transito';
                        } else {
                        ?>
                        <a href="listamov.php?tipomov=2&<?php echo $qs_data; ?>" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s" >Inclusão por transito</a>
                        <?php } ?>
                    </td>
                    <td class="cont_result"><?php echo $d_mov_it; ?></td>
                </tr>
                <tr class="even">
                    <td class="cont_desc">
                        <?php
                        if ( empty( $d_mov_ir ) ) {
                            echo 'Inclusão por remoção';
                        } else {
                        ?>
                        <a href="listamov.php?tipomov=3&<?php echo $qs_data; ?>" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s" >Inclusão por remoção</a>
                        <?php } ?>
                    </td>
                    <td class="cont_result"><?php echo $d_mov_ir; ?></td>
                </tr>
                <tr class="even">
                    <td class="cont_desc">
                        <?php
                        if ( empty( $d_mov_ie ) ) {
                            echo 'Inclusão por retorno';
                        } else {
                        ?>
                        <a href="listamov.php?tipomov=4&<?php echo $qs_data; ?>" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s" >Inclusão por retorno</a>
                        <?php } ?>
                    </td>
                    <td class="cont_result"><?php echo $d_mov_ie; ?></td>
                </tr>


                <tr class="even">
                    <td class="cont_desc">
                    <?php
                    if ( empty( $d_mov_al ) ) {
                        echo 'Exclusão - alvarás';
                    } else {
                        ?>
                        <a href="listamov.php?tipomov=5&idlocal=100&<?php echo $qs_data; ?>" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s" >Exclusão - alvarás</a>
                    <?php } ?>
                    </td>
                    <td class="cont_result"><?php echo $d_mov_al; ?></td>
                </tr>
                <tr class="even">
                    <td class="cont_desc">
                    <?php
                    if ( empty( $d_mov_ob ) ) {
                        echo 'Exclusão - óbitos';
                    } else {
                        ?>
                        <a href="listamov.php?tipomov=5&idlocal=300&<?php echo $qs_data; ?>" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s" >Exclusão - óbitos</a>
                    <?php } ?>
                    </td>
                    <td class="cont_result"><?php echo $d_mov_ob; ?></td>
                </tr>
                <tr class="even">
                    <td class="cont_desc">
                    <?php
                    if ( empty( $d_mov_ev ) ) {
                        echo 'Exclusão - evasões';
                    } else {
                        ?>
                        <a href="listamov.php?tipomov=5&idlocal=200&<?php echo $qs_data; ?>" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s" >Exclusão - evasões</a>
                    <?php } ?>
                    </td>
                    <td class="cont_result"><?php echo $d_mov_ev; ?></td>
                </tr>

                <tr class="even">
                    <td class="cont_desc">
                        <?php
                        if ( empty( $d_mov_ex ) ) {
                            echo 'Exclusão';
                        } else {
                        ?>
                        <a href="listamov.php?tipomov=5&<?php echo $qs_data; ?>" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s" >Exclusão - total</a>
                        <?php } ?>
                    </td>
                    <td class="cont_result"><?php echo $d_mov_ex; ?></td>
                </tr>

                <tr class="even">
                    <td class="cont_desc">
                        <?php
                        if ( empty( $d_mov_et ) ) {
                            echo 'Exclusão por transito';
                        } else {
                        ?>
                        <a href="listamov.php?tipomov=6&<?php echo $qs_data; ?>" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s" >Exclusão por transito</a>
                        <?php } ?>
                    </td>
                    <td class="cont_result"><?php echo $d_mov_et; ?></td>
                </tr>
                <tr class="even">
                    <td class="cont_desc">
                        <?php
                        if ( empty( $d_mov_er ) ) {
                            echo 'Exclusão por remoção';
                        } else {
                        ?>
                        <a href="listamov.php?tipomov=7&<?php echo $qs_data; ?>" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s" >Exclusão por remoção</a>
                        <?php } ?>
                    </td>
                    <td class="cont_result"><?php echo $d_mov_er; ?></td>
                </tr>
                <tr class="even">
                    <td class="cont_desc">
                        <?php
                        if ( empty( $d_mov_ee ) ) {
                            echo 'Exclusão por retorno';
                        } else {
                        ?>
                        <a href="listamov.php?tipomov=8&<?php echo $qs_data; ?>" title="Listar <?php echo SICOP_DET_ART_L . 's ' . SICOP_DET_DESC_L ; ?>s" >Exclusão por retorno</a>
                        <?php } ?>
                    </td>
                    <td class="cont_result"><?php echo $d_mov_ee; ?></td>
                </tr>
            </table>


            <span id='data'></span>

            <?php } ?>

<?php include 'footer.php'; ?>