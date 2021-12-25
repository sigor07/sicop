<?php
if ( !isset( $_SESSION ) ) session_start();

//if ( empty ( $_SESSION['incl_path'] ) ) {
//    session_destroy();
//    header( 'Location: /sicop/' );
//    exit;
//}
//
//set_include_path( get_include_path() . PATH_SEPARATOR . $_SESSION['incl_path'] );

require '../init/config.php';
require 'incl_complete.php';

//require 'incl_complete.php';
//echo get_include_path();
//
//include 'cab.php';
//
//exit;

$pag = link_pag();
$tipo = '';

$n_pront   = get_session( 'n_pront', 'int' );
$n_pront_n = 2;

if ( $n_pront < $n_pront_n ) {
    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
    salvaLog( $mensagem );
    exit;
}

$det           = get_get( 'det', 'busca' );
$data_del_in   = get_get( 'data_del_in', 'busca' );
$data_del_out  = get_get( 'data_del_out', 'busca' );
$data_sent_in  = get_get( 'data_sent_in', 'busca' );
$data_sent_out = get_get( 'data_sent_out', 'busca' );
$numinq        = get_get( 'numinq', 'busca' );
$numproc       = get_get( 'numproc', 'busca' );
$comarca       = get_get( 'comarca', 'busca' );
/*
$preso         = !empty($_GET['preso']) ? (int)($_GET['preso']) : '';
$ext           = isset($_GET['ext']) ? (int)($_GET['ext']) : '';
$outroest      = !empty($_GET['outroest']) ? (int)($_GET['outroest']) : '';
$fed           = !empty($_GET['fed']) ? (int)($_GET['fed']) : '';
 */
$tipo_sit      = get_get( 'tipo_sit', 'int' );

$preso = '';
if ( isset( $_GET['preso'] ) ) {
    if ( $_GET['preso'] !== '' ) {
        $preso = (int)$_GET['preso'];
    }
}

$ext = '';
if ( isset( $_GET['ext'] ) ) {
    if ( $_GET['ext'] !== '' ) {
        $ext = (int)$_GET['ext'];
    }
}

$outroest = '';
if ( isset( $_GET['outroest'] ) ) {
    if ( $_GET['outroest'] !== '' ) {
        $outroest = (int)$_GET['outroest'];
    }
}

$fed = '';
if ( isset( $_GET['fed'] ) ) {
    if ( $_GET['fed'] !== '' ) {
        $fed = (int)$_GET['fed'];
    }
}


$where = '';

if ( !empty( $det ) ){
    $where .= "WHERE ( `detentos`.`nome_det` LIKE '%$det%' OR `detentos`.`matricula` LIKE '$det%')";
}

if ( !empty( $numinq ) ){
    if ( !empty( $where ) ){
        $where .= " AND `grade`.`gra_num_inq` LIKE '%$numinq%'";
    } else {
        $where .= "WHERE `grade`.`gra_num_inq` LIKE '%$numinq%'";
    }
}

if ( !empty( $numproc ) ){
    if ( !empty( $where ) ){
        $where .= " AND `grade`.`gra_num_proc` LIKE '%$numproc%'";
    } else {
        $where .= "WHERE `grade`.`gra_num_proc` LIKE '%$numproc%'";
    }
}

if ( !empty( $comarca ) ){
    if ( !empty( $where ) ){
        $where .= " AND `grade`.`gra_comarca` LIKE '%$comarca%'";
    } else {
        $where .= "WHERE `grade`.`gra_comarca` LIKE '%$comarca%'";
    }
}

if ( $preso !== '' ){
    if ( !empty( $preso ) ){
        if ( !empty( $where ) ){
            $where .= " AND `grade`.`gra_preso` = TRUE";
        } else {
            $where .= "WHERE `grade`.`gra_preso` = TRUE";
        }
    } else {
        if ( !empty( $where ) ){
            $where .= " AND `grade`.`gra_preso` = FALSE";
        } else {
            $where .= "WHERE `grade`.`gra_preso` = FALSE";
        }
    }
}

if ( $ext !== '' ){
    if ( !empty( $ext ) ){
        if ( !empty( $where ) ){
            $where .= " AND `grade`.`gra_campo_x` = TRUE";
        } else {
            $where .= "WHERE `grade`.`gra_campo_x` = TRUE";
        }
    } else {
        if ( !empty( $where ) ){
            $where .= " AND `grade`.`gra_campo_x` = FALSE";
        } else {
            $where .= "WHERE `grade`.`gra_campo_x` = FALSE";
        }
    }
}

if ( $outroest !== '' ){
    if ( !empty( $outroest ) ){
        if ( !empty( $where ) ){
            $where .= " AND `grade`.`gra_outro_est` = TRUE";
        } else {
            $where .= "WHERE `grade`.`gra_outro_est` = TRUE";
        }
    } else {
        if ( !empty( $where ) ){
            $where .= " AND `grade`.`gra_outro_est` = FALSE";
        } else {
            $where .= "WHERE `grade`.`gra_outro_est` = FALSE";
        }
    }
}

if ( $fed !== '' ){
    if ( !empty( $fed ) ){
        if ( !empty( $where ) ){
            $where .= " AND `grade`.`gra_fed` = TRUE";
        } else {
            $where .= "WHERE `grade`.`gra_fed` = TRUE";
        }
    } else {
        if ( !empty( $where ) ){
            $where .= " AND `grade`.`gra_fed` = FALSE";
        } else {
            $where .= "WHERE `grade`.`gra_fed` = FALSE";
        }
    }
}

if ( !empty( $data_del_in ) or !empty( $data_del_out )){
    if ( !empty( $data_del_in ) and  !empty( $data_del_out )){
        if ( !empty( $where ) ){
            $where .= " AND `grade`.`gra_data_delito` BETWEEN STR_TO_DATE('$data_del_in', '%d/%m/%Y') AND STR_TO_DATE('$data_del_out', '%d/%m/%Y')";
        } else {
            $where .= "WHERE `grade`.`gra_data_delito` BETWEEN STR_TO_DATE('$data_del_in', '%d/%m/%Y') AND STR_TO_DATE('$data_del_out', '%d/%m/%Y')";
        }
    } else {
        if ( !empty( $where ) ){
            $where .= " AND `grade`.`gra_data_delito` = IF(STR_TO_DATE('$data_del_in', '%d/%m/%Y'), STR_TO_DATE('$data_del_in', '%d/%m/%Y'), STR_TO_DATE('$data_del_out', '%d/%m/%Y'))";
        } else {
            $where .= "WHERE `grade`.`gra_data_delito` = IF(STR_TO_DATE('$data_del_in', '%d/%m/%Y'), STR_TO_DATE('$data_del_in', '%d/%m/%Y'), STR_TO_DATE('$data_del_out', '%d/%m/%Y'))";
        }
    }
}

if ( !empty( $data_sent_in ) or !empty( $data_sent_out )){
    if ( !empty( $data_sent_in ) and  !empty( $data_sent_out )){
        if ( !empty( $where ) ){
            $where .= " AND `grade`.`gra_data_sent` BETWEEN STR_TO_DATE('$data_sent_in', '%d/%m/%Y') AND STR_TO_DATE('$data_sent_out', '%d/%m/%Y')";
        } else {
            $where .= "WHERE `grade`.`gra_data_sent` BETWEEN STR_TO_DATE('$data_sent_in', '%d/%m/%Y') AND STR_TO_DATE('$data_sent_out', '%d/%m/%Y')";
        }
    } else {
        if ( !empty( $where ) ){
            $where .= " AND `grade`.`gra_data_sent` = IF(STR_TO_DATE('$data_sent_in', '%d/%m/%Y'), STR_TO_DATE('$data_sent_in', '%d/%m/%Y'), STR_TO_DATE('$data_sent_out', '%d/%m/%Y'))";
        } else {
            $where .= "WHERE `grade`.`gra_data_sent` = IF(STR_TO_DATE('$data_sent_in', '%d/%m/%Y'), STR_TO_DATE('$data_sent_in', '%d/%m/%Y'), STR_TO_DATE('$data_sent_out', '%d/%m/%Y'))";
        }
    }
}

$clausula = '';

if ( !empty( $tipo_sit ) ){

    $clausula = get_where_det( $tipo_sit );

}

if ( !empty( $clausula ) ){

    if ( !empty( $where ) ){
        $where .= ' AND ' . $clausula;
    } else {
        $where .= 'WHERE ' . $clausula;
    }

}

$ordpor = '';
$q_string = '';

if ( isset( $_GET['det'] ) ) {

    if ( empty( $_GET['op'] ) ) {
        $ordpor = 'nomea';
    } else {
        $ordpor = $_GET['op'];
    }

    $ordpor = tratabusca( $ordpor );

    switch($ordpor) {
        default:
        case 'nomea':
            $ordbusca = "`detentos`.`nome_det` ASC";
            break;
        case 'nomed':
            $ordbusca = "`detentos`.`nome_det` DESC";
            break;
        case 'matra':
            $ordbusca = "`detentos`.`matricula` ASC";
            break;
        case 'matrd':
            $ordbusca = "`detentos`.`matricula` DESC";
            break;
        case 'inqa':
            $ordbusca = "`grade`.`gra_num_inq` ASC, `grade`.`gra_num_proc` ASC, `grade`.`gra_vara` ASC, `grade`.`gra_comarca` ASC";
            break;
        case 'inqd':
            $ordbusca = "`grade`.`gra_num_inq` DESC, `grade`.`gra_num_proc` ASC, `grade`.`gra_vara` ASC, `grade`.`gra_comarca` ASC";
            break;
        case 'proca':
            $ordbusca = "`grade`.`gra_num_proc` ASC, `grade`.`gra_vara` ASC, `grade`.`gra_comarca` ASC";
            break;
        case 'procd':
            $ordbusca = "`grade`.`gra_num_proc` DESC, `grade`.`gra_vara` ASC, `grade`.`gra_comarca` ASC";
            break;
        case 'varaa':
            $ordbusca = "`grade`.`gra_vara` ASC, `grade`.`gra_comarca` ASC";
            break;
        case 'varad':
            $ordbusca = "`grade`.`gra_vara` DESC, `grade`.`gra_comarca` ASC";
            break;
        case 'coma':
            $ordbusca = "`grade`.`gra_comarca` ASC";
            break;
        case 'comd':
            $ordbusca = "`grade`.`gra_comarca` DESC";
            break;
    }

    $query = "SELECT
                `detentos`.`iddetento`,
                `detentos`.`nome_det`,
                `detentos`.`matricula`,
                `detentos`.`pai_det`,
                `detentos`.`mae_det`,
                `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
                `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
                `unidades_out`.`idunidades` AS iddestino,
                `cela`.`cela`,
                `raio`.`raio`,
                `grade`.`idprocesso`,
                `grade`.`gra_num_inq`,
                `grade`.`gra_num_proc`,
                `grade`.`gra_vara`,
                `grade`.`gra_comarca`,
                `grade`.`gra_preso`,
                `grade`.`gra_campo_x`
              FROM
                `detentos`
                INNER JOIN `grade` ON `grade`.`cod_detento` = `detentos`.`iddetento`
                LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
                LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
                LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
              $where
              ORDER BY
                $ordbusca";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query = $model->query( $query );

    // fechando a conexao
    $model->closeConnection();

    $querytime = $model->getQueryTime();

    $cont = $query->num_rows;

    $valor_busca = valor_user( $_GET );

    $mensagem = "[ BUSCA EFETUADA ]\nBusca de processo efetuada. \n\n $valor_busca \n\n Página: $pag";
    salvaLog( $mensagem );

}

$q_tipo_sit = 'SELECT `idtipo_sit`, `tipo_sit` FROM `tipo_sit_det_busca` ORDER BY `idtipo_sit` ASC';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_tipo_sit = $model->query( $q_tipo_sit );

// fechando a conexao
$model->closeConnection();

parse_str( $_SERVER['QUERY_STRING'], $q_string );

if ( isset( $q_string['op'] ) ) {
    unset( $q_string['op'] );
}

$desc_pag = 'Pesquisar processos';

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>



            <p class="descript_page">PESQUISAR PROCESSOS</p>

            <form action="buscaprocess.php" method="get" name="buscaprocess" id="buscaprocess" >

                <table class="busca_form">
                    <tr>
                        <td class="bf_legend">Nome ou matrícula: </td>
                        <td class="bf_field"><input name="det" type="text" class="CaixaTexto" id="det" onkeypress="return blockChars(event, 4);" value="<?php echo $det ?>" size="50" /></td>
                    </tr>
                    <tr>
                        <td class="bf_legend">Data do delito: </td>
                        <td class="bf_field"><input name="data_del_in" type="text" class="CaixaTexto" id="data_del_in" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_del_in ?>" size="12" maxlength="10" /> e <input name="data_del_out" type="text" class="CaixaTexto" id="data_del_out" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_del_out ?>" size="12" maxlength="10" /></td>

                    </tr>
                    <tr>
                        <td class="bf_legend">Data da sentença:</td>
                        <td class="bf_field"><input name="data_sent_in" type="text" class="CaixaTexto" id="data_sent_in" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_sent_in ?>" size="12" maxlength="10" /> e <input name="data_sent_out" type="text" class="CaixaTexto" id="data_sent_out" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_sent_out ?>" size="12" maxlength="10" /></td>

                    </tr>
                    <tr>
                        <td class="bf_legend">Número do inquérito: </td>
                        <td class="bf_field"><input name="numinq" type="text" class="CaixaTexto" id="numinq" onkeypress="return blockChars(event, 3);" value="<?php echo $numinq ?>" size="50" /></td>
                    </tr>
                    <tr>
                        <td class="bf_legend">Número do processo:</td>
                        <td class="bf_field"><input name="numproc" type="text" class="CaixaTexto" id="numproc" onkeypress="return blockChars(event, 3);" value="<?php echo $numproc ?>" size="50" /></td>
                    </tr>
                    <tr>
                        <td class="bf_legend">Comarca:</td>
                        <td class="bf_field"><input name="comarca" type="text" class="CaixaTexto" id="comarca" onkeypress="return blockChars(event, 4);" value="<?php echo $comarca ?>" size="50" /></td>
                    </tr>
                    <tr>
                        <td aclass="bf_legend">Situação d<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L; ?>:</td>
                        <td class="bf_field">
                            <select name="tipo_sit" class="CaixaTexto" id="tipo_sit">
                                <option value="" >Todos</option>
                                <?php while ( $d_tipo_sit = $q_tipo_sit->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_tipo_sit['idtipo_sit']; ?>" <?php echo $d_tipo_sit['idtipo_sit'] == $tipo_sit ? 'selected="selected"' : ''; ?>><?php echo $d_tipo_sit['tipo_sit']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="bf_legend">Preso:</td>
                        <td class="bf_field">
                            <input name="preso" type="radio" id="preso_0" value="1" <?php echo (!empty( $_GET['busca'] ) and $preso == '1' ) ? 'checked="checked"' : ''; ?> /> Sim &nbsp;&nbsp;
                            <input name="preso" type="radio" id="preso_1" value="0" <?php echo (!empty( $_GET['busca'] ) and $preso == '0' ) ? 'checked="checked"' : ''; ?> /> Não &nbsp;&nbsp;
                            <input name="preso" type="radio" id="preso_2" value="" <?php echo ( (!empty( $_GET['busca'] ) and $preso === '' ) or empty( $_GET['busca'] ) ) ? 'checked="checked"' : ''; ?> /> Indiferente
                        </td>
                    </tr>
                    <tr>
                        <td class="bf_legend">Extinto:</td>
                        <td class="bf_field">
                            <input name="ext" type="radio" id="ext_0" value="1" <?php echo (!empty( $_GET['busca'] ) and $ext == '1' ) ? 'checked="checked"' : ''; ?> /> Sim &nbsp;&nbsp;
                            <input name="ext" type="radio" id="exto_1" value="0" <?php echo (!empty( $_GET['busca'] ) and $ext == '0' ) ? 'checked="checked"' : ''; ?>/> Não &nbsp;&nbsp;
                            <input name="ext" type="radio" id="ext_2" value="" <?php echo ( (!empty( $_GET['busca'] ) and $ext === '' ) or empty( $_GET['busca'] ) ) ? 'checked="checked"' : ''; ?> /> Indiferente
                        </td>
                    </tr>
                    <tr>
                        <td class="bf_legend">Outro estado:</td>
                        <td class="bf_field">
                            <input name="outroest" type="radio" id="outroest_0" value="1" <?php echo (!empty( $_GET['busca'] ) and $outroest == '1' ) ? 'checked="checked"' : ''; ?> /> Sim &nbsp;&nbsp;
                            <input name="outroest" type="radio" id="outroest_1" value="0" <?php echo (!empty( $_GET['busca'] ) and $outroest == '0' ) ? 'checked="checked"' : ''; ?> /> Não &nbsp;&nbsp;
                            <input name="outroest" type="radio" id="outroest_2" value="" <?php echo ( (!empty( $_GET['busca'] ) and $outroest === '' ) or empty( $_GET['busca'] ) ) ? 'checked="checked"' : ''; ?> /> Indiferente
                        </td>
                    </tr>
                    <tr>
                        <td class="bf_legend">Federal:</td>
                        <td class="bf_field">
                            <input name="fed" type="radio" id="fed_0" value="1" <?php echo (!empty( $_GET['busca'] ) and $fed == '1' ) ? 'checked="checked"' : ''; ?> /> Sim &nbsp;&nbsp;
                            <input name="fed" type="radio" id="fed_1" value="0" <?php echo (!empty( $_GET['busca'] ) and $fed == '0' ) ? 'checked="checked"' : ''; ?> /> Não &nbsp;&nbsp;
                            <input name="fed" type="radio" id="fed_2" value="" <?php echo ( (!empty( $_GET['busca'] ) and $fed === '' ) or empty( $_GET['busca'] ) ) ? 'checked="checked"' : ''; ?> /> Indiferente
                        </td>
                    </tr>
                </table>

                <div class="form_bts">

                    <input class="form_bt" type="submit" name="busca" id="busca" value="Buscar" />
                    <input class="form_bt" type="button" name="" onClick="javascript: limpa_campos_proc();" value="Limpar campos" />

                </div>

            </form>
            <script type="text/javascript">

                $(function() {
                    $( "#det" ).focus();
                    $( "#data_del_in, #data_del_out, #data_sent_in, #data_sent_out" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });
                });

            </script>

            <?php

            if ( empty( $_GET ) ) {
                include 'footer.php';
                exit;
            }

            if ( empty( $cont ) or $cont < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não foi encontrado nenhuma ocorrência.</p>';
                include 'footer.php';
                exit;
            }

            ?>

            <p class="p_q_info">Essa consulta retornou <?php echo $cont ?> registros (<?php echo round( $querytime, 2 ) ?> seg).</p>

            <table class="lista_busca">
                <tr>
                    <th class="num_od">N</th>
                    <th class="nome_det_small"><?php echo SICOP_DET_DESC_FU; ?>
                        <?php echo link_ord_asc( $ordpor, 'nome', $q_string, 'nome d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L ) ?>
                        <?php echo link_ord_desc( $ordpor, 'nome', $q_string, 'nome d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L ) ?>
                    </th>
                    <th class="matr_det">Matrícula
                        <?php echo link_ord_asc( $ordpor, 'matr', $q_string, 'matrícula' ) ?>
                        <?php echo link_ord_desc( $ordpor, 'matr', $q_string, 'matrícula' ) ?>

                    </th>
                    <th class="bp_inq">Inquérito
                        <?php echo link_ord_asc( $ordpor, 'inq', $q_string, 'inquérito' ) ?>
                        <?php echo link_ord_desc( $ordpor, 'inq', $q_string, 'inquérito' ) ?>
                    </th>
                    <th class="bp_process">Processo
                        <?php echo link_ord_asc( $ordpor, 'proc', $q_string, 'processo' ) ?>
                        <?php echo link_ord_desc( $ordpor, 'proc', $q_string, 'processo' ) ?>
                    </th>
                    <th class="bp_vara">Vara
                        <?php echo link_ord_asc( $ordpor, 'vara', $q_string, 'vara' ) ?>
                        <?php echo link_ord_desc( $ordpor, 'vara', $q_string, 'vara' ) ?>
                    </th>
                    <th class="bp_comarca">Comarca
                        <?php echo link_ord_asc( $ordpor, 'com', $q_string, 'comarca' ) ?>
                        <?php echo link_ord_desc( $ordpor, 'com', $q_string, 'comarca' ) ?>
                    </th>
                    <th class="tb_ck">&nbsp;</th>
                </tr>

                <?php

                $i = 1;
                $process_class = '';
                while ( $d_det = $query->fetch_assoc() ) {

                    $tipo_mov_in = $d_det['tipo_mov_in'];
                    $tipo_mov_out = $d_det['tipo_mov_out'];
                    $iddestino = $d_det['iddestino'];

                    $det = manipula_sit_det_b( $tipo_mov_in, $tipo_mov_out, $iddestino );

                    $preso   = $d_det['gra_preso'];
                    $extinto = $d_det['gra_campo_x'];
                    get_sit_process_b( $preso, $extinto );

                    //$process_class = 'process_ativo';


                    ?>

                <tr class="even">
                    <td class="num_od"><?php echo $i++; ?></td>
                    <td class="nome_det_small <?php if ( stripos( $ordpor, 'nome' ) !== false ) echo 'ord';?>" title="Pai: <?php echo $d_det['pai_det'];?>&#13;Mãe: <?php echo $d_det['mae_det'];?>&#13;Situação atual: <?php echo $det['sitat'];?>" ><a href="detalgrade.php?iddet=<?php echo $d_det['iddetento'] /*alphaID($d_det['iddetento'])*/;?>"> <?php echo $d_det['nome_det']; ?></a></td>
                    <td class="matr_det <?php echo $det['css_class']; if ( stripos( $ordpor, 'matr' ) !== false ) echo ' ord';?>"><?php echo !empty( $d_det['matricula'] ) ? formata_num( $d_det['matricula'] ) : '&nbsp;'; ?></td>
                    <td class="bp_inq <?php echo $process_class; if ( stripos( $ordpor, 'inq' ) !== false ) echo ' ord'; ?>"><?php echo !empty( $d_det['gra_num_inq'] ) ? $d_det['gra_num_inq'] : '&nbsp;'; ?></td>
                    <td class="bp_process <?php echo $process_class; if ( stripos( $ordpor, 'proc' ) !== false ) echo ' ord'; ?>"><?php echo !empty( $d_det['gra_num_proc'] ) ? $d_det['gra_num_proc'] : '&nbsp;';?></td>
                    <td class="bp_vara <?php echo $process_class; if ( stripos( $ordpor, 'vara' ) !== false ) echo ' ord'; ?>"><?php echo !empty( $d_det['gra_vara'] ) ? $d_det['gra_vara'] : '&nbsp;'; ?></td>
                    <td class="bp_comarca <?php echo $process_class; if ( stripos( $ordpor, 'com' ) !== false ) echo ' ord'; ?>"><?php echo !empty( $d_det['gra_comarca'] ) ? $d_det['gra_comarca'] : '&nbsp;'; ?></td>
                    <td class="tb_ck"><a href="detalgrade.php?iddet=<?php echo $d_det['iddetento']; ?>#<?php echo $d_det['idprocesso'] ?>" title="Ver detalhes deste processo"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_view.png" alt="Ver detalhes deste processo" class="icon_view" /></a></td>
                </tr>

                <?php } // fim do while ?>

            </table>

<?php include 'footer.php'; ?>