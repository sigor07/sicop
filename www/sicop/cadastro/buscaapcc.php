<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';
$cont = '';
$ordpor = '';
$q_string = '';

$n_cadastro   = get_session( 'n_cadastro', 'int' );
$n_chefia     = get_session( 'n_chefia', 'int' );
$imp_cadastro = get_session( 'imp_cadastro', 'int' );
$n_cad_n      = 2;

$motivo_pag = 'PESQUISAR APCC';

if ($n_cadastro < $n_cad_n) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$campo_det = get_get( 'campo_det', 'busca' );
$c_det_sf  = $campo_det;
$num_apcc  = get_get( 'num_apcc', 'int' );
$ano_apcc  = get_get( 'ano_apcc', 'int' );
$tipo_fon  = get_get( 'tipo_fon', 'int' );
$data_apcc = get_get( 'data_apcc', 'busca' );

if( !empty( $_GET['busca'] ) ) {

    $where = '';

    if ( !empty( $campo_det ) ){

        $nome_det = '';

        if ( $tipo_fon == 1 ) {

            $nome_det .= "detentos.nome_det LIKE '%$campo_det%' OR ";

        } else {

            $campo_det = preg_replace( '/\s?\b\w{2}\b/' , null , $campo_det ); // remover palavras com 2 letras ou menos

            if ( !empty( $campo_det ) ) {

                $nome_det = '(';

                $arr_busca = explode( ' ', $campo_det );

                foreach( $arr_busca as $indice => $valor ) {
                    if ($valor == NULL) continue;
                    $nome_det .= " detentos.nome_det LIKE '%$valor%' AND";
                }
            }

            if ( !empty( $nome_det ) ) {
                $nome_det = substr($nome_det, 0, -3); //remover o ultimo 'AND'
                $nome_det = $nome_det . ') OR ';
            }

        }

        $where .= "WHERE ( $nome_det detentos.matricula LIKE '$campo_det%' )";

    }

    if ( !empty( $num_apcc ) ){
        if ( !empty( $where ) ){
            $where .= " AND numeroapcc.numero_apcc = '$num_apcc'";
        } else {
            $where .= "WHERE numeroapcc.numero_apcc = '$num_apcc'";
        }
    }

    if ( !empty( $ano_apcc ) ){
        if ( !empty( $where ) ){
            $where .= " AND numeroapcc.ano = '$ano_apcc'";
        } else {
            $where .= "WHERE numeroapcc.ano = '$ano_apcc'";
        }
    }

    if ( !empty( $data_apcc ) ){
        if ( !empty( $where ) ){
            $where .= " AND DATE(apcc.data_add) = STR_TO_DATE('$data_apcc', '%d/%m/%Y')";
        } else {
            $where .= "WHERE DATE(apcc.data_add) = STR_TO_DATE('$data_apcc', '%d/%m/%Y')";
        }
    }

    if (empty($_GET['op'])) {
        $ordpor = 'nomea';
    } else {
        $ordpor    = $_GET['op'];
    }

    $ordpor = tratabusca($ordpor);

    switch($ordpor) {

        default:
        case 'nomea':
            $ordbusca = "`detentos`.`nome_det` ASC, `apcc`.`data_add` ASC, `apcc`.`cod_numapcc` ASC";
            break;
        case 'nomed':
            $ordbusca = "`detentos`.`nome_det` DESC, `apcc`.`data_add` ASC, `apcc`.`cod_numapcc` ASC";
            break;
        case 'matra':
            $ordbusca = "`detentos`.`matricula` ASC, `apcc`.`data_add` ASC, `apcc`.`cod_numapcc` ASC";
            break;
        case 'matrd':
            $ordbusca = "`detentos`.`matricula` DESC, `apcc`.`data_add` ASC, `apcc`.`cod_numapcc` ASC";
            break;
        case 'numa':
            $ordbusca = "`apcc`.`cod_numapcc` ASC, `detentos`.`nome_det` ASC";
            break;
        case 'numd':
            $ordbusca = "`apcc`.`cod_numapcc` DESC, `detentos`.`nome_det` ASC";
            break;
        case 'dataa':
            $ordbusca = "`apcc`.`data_add` ASC, `apcc`.`cod_numapcc` ASC";
            break;
        case 'datad':
            $ordbusca = "`apcc`.`data_add` DESC, `apcc`.`cod_numapcc` ASC";
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
                `apcc`.`idapcc`,
                `apcc`.`cod_numapcc`,
                `apcc`.`data_add` AS data_apcc,
                DATE_FORMAT(`apcc`.`data_add`,'%d/%m/%Y') AS data_apcc_f,
                `numeroapcc`.`numero_apcc`,
                `numeroapcc`.`ano`
              FROM
                `apcc`
                INNER JOIN `detentos` ON `apcc`.`cod_detento` = `detentos`.`iddetento`
                INNER JOIN `numeroapcc` ON `apcc`.`cod_numapcc` = `numeroapcc`.`idnumapcc`
                LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
              $where
              ORDER BY
                $ordbusca";


    $db = SicopModel::getInstance();

    $query = $db->query( $query );

    $querytime = $db->getQueryTime();

    if ( !$query ) {

        // pegar a mensagem de erro mysql
        $msg_err_mysql = $db->getErrorMsg();
        $db->closeConnection();

        // montar a mensagem q será salva no log
        $msg = array( );
        $msg['tipo'] = 'err';
        $msg['text'] = "Falha na consulta ( PESQUISAR $motivo ).\n\n $msg_err_mysql";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );


        echo msg_js( 'FALHA!', 1 );
        exit;

    }

    $db->closeConnection();

    $cont = $query->num_rows;

    $valor_busca = valor_user( $_GET );

    $mensagem = "[ BUSCA EFETUADA ]\nBusca de APCC efetuada\n\nParametros: \n $valor_busca\n Página: $pag";
    salvaLog($mensagem);

}

parse_str( $_SERVER['QUERY_STRING'], $q_string );

if ( isset( $q_string['op'] ) ) {
    unset( $q_string['op'] );
}

$desc_pag = 'Pesquisar APCC';

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if (!empty($qs)) {
    $pag_atual .=  '?' . $qs;
}

$trail = new Breadcrumb();
$trail->add($desc_pag, $pag_atual, 3);
$trail->output();
?>


            <p class="descript_page">PESQUISAR APCC</p>

            <form action="buscaapcc.php" method="get" name="buscaapcc" id="buscaapcc">

                <table class="busca_form">
                    <tr>
                        <td width="169" align="right">Nome ou matrícula: </td>
                        <td width="296" align="left"><input name="campo_det" type="text" class="CaixaTexto" id="campo_det" onkeypress="return blockChars(event, 4);" value="<?php echo $c_det_sf ?>" size="50" /></td>
                    </tr>
                    <tr>
                        <td align="right">Número do APCC:</td>
                        <td align="left"><input name="num_apcc" type="text" class="CaixaTexto" id="num_apcc" onkeypress="return blockChars(event, 2);" value="<?php echo $num_apcc ?>" size="5" maxlength="4" /> /
                            <input name="ano_apcc" type="text" class="CaixaTexto" id="ano_apcc" onkeypress="return blockChars(event, 2);" value="<?php echo $ano_apcc ?>" size="5" maxlength="4" /></td>
                    </tr>
                    <tr>
                        <td align="right">Data do APCC:</td>
                        <td align="left"><input name="data_apcc" type="text" class="CaixaTexto" id="data_apcc" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_apcc ?>" size="12" maxlength="10" /></td>
                    </tr>
                    <tr>
                        <td align="right">Pesquisa fonética: </td>
                        <td align="left">
                            <input name="tipo_fon" type="radio" id="tipo_fon_0" value="1" <?php echo ( (!empty( $_GET ) and $tipo_fon == '1' ) or empty( $tipo_fon ) ) ? 'checked="checked"' : ''; ?> /> a frase exata &nbsp;
                            <input name="tipo_fon" type="radio" id="tipo_fon_1" value="2" <?php echo (!empty( $_GET ) and $tipo_fon == '2' ) ? 'checked="checked"' : ''; ?> /> que contenha as palavras
                        </td>
                    </tr>
                </table>


                <div class="form_bts">
                    <input class="form_bt" type="submit" name="buscar" id="buscar" value="Buscar" />
                </div>

                <input name="busca" type="hidden" id="busca" value="busca" />

            </form>

            <script type="text/javascript">

                $(function() {
                    $( "#campo_det" ).focus();
                    $( "#data_apcc" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });
                });


            </script>

            <?php if ( $n_chefia >= 3 or $n_cadastro >= 3 ) { ?>
            <p class="link_common" style="margin-top: 10px;">
                <?php if ( $n_chefia >= 3 or $n_cadastro >= 3 ) { ?> <a href="<?php echo SICOP_ABS_PATH; ?>detento/cadastradet.php">Cadastrar detento</a><?php }; ?><?php if ( $n_cadastro >= 3 ) { ?> | <a href="<?php echo SICOP_ABS_PATH; ?>buscadet.php?proced=cadapcc">Cadastrar APCC</a><?php }; ?>
            </p>
            <?php }; ?>


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

                <tr class="cab">
                    <th class="num_od">N</th>
                    <th class="nome_det"><?php echo SICOP_DET_DESC_FU; ?>
                        <?php echo link_ord_asc( $ordpor, 'nome', $q_string, 'nome d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L ) ?>
                        <?php echo link_ord_desc( $ordpor, 'nome', $q_string, 'nome d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L ) ?>
                    </th>
                    <th class="matr_det">Matrícula
                        <?php echo link_ord_asc( $ordpor, 'matr', $q_string, 'matrícula' ) ?>
                        <?php echo link_ord_desc( $ordpor, 'matr', $q_string, 'matrícula' ) ?>
                    </th>
                    <th class="num_apcc">Número
                        <?php echo link_ord_asc( $ordpor, 'num', $q_string, 'número do apcc' ) ?>
                        <?php echo link_ord_desc( $ordpor, 'num', $q_string, 'número do apcc' ) ?>
                    </th>
                    <th class="desc_data">Data
                        <?php echo link_ord_asc( $ordpor, 'data', $q_string, 'data do apcc' ) ?>
                        <?php echo link_ord_desc( $ordpor, 'data', $q_string, 'data do apcc' ) ?>
                    </th>
                    <?php

                    $i = 1;

                    $classe = 'odd';

                    while ( $d_det = $query->fetch_object() ) {

                        $tipo_mov_in  = $d_det->tipo_mov_in;
                        $tipo_mov_out = $d_det->tipo_mov_out;
                        $iddestino    = $d_det->iddestino;

                        $det = manipula_sit_det_b( $tipo_mov_in, $tipo_mov_out, $iddestino );

                        $classe == 'odd' ? $classe = 'even' : $classe = 'odd';

                        ?>
                <tr class="<?php echo $classe; ?>">
                    <td class="num_od"><?php echo $i++; ?></td>
                    <td class="nome_det <?php if ( stripos( $ordpor, 'nome' ) !== false ) echo 'ord';?>" title="Pai: <?php echo $d_det->pai_det;?>&#13;Mãe: <?php echo $d_det->mae_det;?>&#13;Situação atual: <?php echo $det['sitat'];?>" ><a href="<?php echo SICOP_ABS_PATH; ?>detento/detalhesdet.php?iddet=<?php echo $d_det->iddetento;?>"> <?php echo $d_det->nome_det; ?></a></td>
                    <td class="matr_det <?php echo $det['css_class']; if ( stripos( $ordpor, 'matr' ) !== false ) echo ' ord';?>"><?php echo !empty( $d_det->matricula ) ? formata_num( $d_det->matricula ) : '&nbsp;';?></td>
                    <td class="num_apcc <?php if ( stripos( $ordpor, 'num' ) !== false ) echo 'ord';?>"><a href="<?php echo SICOP_ABS_PATH; ?>cadastro/detalapcc.php?idapcc=<?php echo $d_det->idapcc;?>"><?php echo $d_det->numero_apcc;?>/<?php echo $d_det->ano;?></a></td>
                    <td class="desc_data <?php if ( stripos( $ordpor, 'data' ) !== false ) echo 'ord';?>"><?php echo $d_det->data_apcc_f;?></td>
                </tr>
                    <?php } // fim do while ?>
            </table>

<?php include 'footer.php'; ?>