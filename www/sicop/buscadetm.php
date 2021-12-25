<?php
if ( !isset( $_SESSION ) ) session_start();

/*ob_start("ob_gzhandler");*/

require 'init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$tipo_fon      = '';
$valorbusca    = NULL;
$valorbusca_sf = NULL;
$ordpor        = '';

$motivo = SICOP_DET_DESC_U . 'S';
$desc_pag = 'Pesquisar ' . SICOP_DET_DESC_L . 's';

if( !empty( $_GET['busca'] ) ) {

    $where = '';
    $tipo_fon = get_get( 'tipo_fon', 'int' );

    if ( !empty( $_GET['campobusca'] ) ) {

        $valorbusca = $_GET['campobusca'];
        $valorbusca_sf = $_GET['campobusca'];
        $valorbusca = tratabusca($valorbusca);

        if ( $tipo_fon == 1 ) {

            $where .= "`detentos`.`nome_det` LIKE '%$valorbusca%'";

        } else {

            $valorbusca = preg_replace( '/\s?\b\w{1,2}\b/' , null , $valorbusca ); // remover palavras com 2 letras ou menos

            $arr_busca = explode( ' ', $valorbusca );

            foreach( $arr_busca as $indice => $valor ) {
                if ($valor == NULL) continue;
                $where .= " `detentos`.`nome_det` LIKE '%$valor%' AND";
            }

        }

    }

    if ( $tipo_fon == 2 ) {
        if ( !empty( $where ) ) {
            $where = substr($where, 0, -3); //remover o ultimo 'AND'
        } else {
            $where = "`detentos`.`nome_det` LIKE '%%'";
        }
    } else if ( $tipo_fon == 1 and empty( $where ) ) {
        $where = "`detentos`.`nome_det` LIKE '%%'";
    }

    $ordpor = 'nomea';

    if ( !empty( $_GET['op'] ) ) {
        $ordpor = $_GET['op'];
        $ordpor = tratabusca($ordpor);
    }

    switch($ordpor) {
        default:
        case 'nomea':
            $ordbusca = '`detentos`.`nome_det` ASC';
            break;
        case 'nomed':
            $ordbusca = '`detentos`.`nome_det` DESC';
            break;
        case 'matra':
            $ordbusca = '`detentos`.`matricula` ASC';
            break;
        case 'matrd':
            $ordbusca = '`detentos`.`matricula` DESC';
            break;
        case 'ra':
            $ordbusca = '`detentos`.`cod_cela` ASC, `detentos`.`nome_det` ASC';
            break;
        case 'rd':
            $ordbusca = '`detentos`.`cod_cela` DESC, `detentos`.`nome_det` ASC';
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
                `raio`.`raio`
              FROM
                `detentos`
                LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
                LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
                LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`

              WHERE
                $where OR `detentos`.`matricula` Like '$valorbusca%'
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

    $valor_busca = valor_user($_GET);

    $mensagem = "[ BUSCA EFETUADA ]\nBusca de " . SICOP_DET_DESC_L . "s efetuada\n\n $valor_busca\n\n Página: $pag";
    salvaLog($mensagem);

    parse_str( $_SERVER[ 'QUERY_STRING' ], $q_string );

    if ( isset( $q_string['op'] ) ) {
        unset( $q_string['op'] );
    }

}

require 'cab_simp.php';

?>

            <p class="descript_page">PESQUISAR <?php echo $motivo; ?></p>

            <form action="buscadetm.php" method="get" name="buscadet" id="buscadet" onSubmit="upperMe(campobusca); ">

                <table class="busca_form">
                    <tr>
                        <td class="bf_detm">Digite o NOME ou a MATRÍCULA d<?php echo SICOP_DET_ART_L; ?> <?php echo SICOP_DET_DESC_L; ?>:</td>
                    </tr>
                    <tr>
                        <td class="bf_detm"><input name="campobusca" type="text" class="CaixaTexto" id="campobusca" onkeypress="return blockChars(event, 4);" value="<?php echo $valorbusca_sf ?>" size="50" /></td>
                    </tr>
                    <tr>
                        <td class="bf_detm">Opções da pesquisa fonética:</td>
                    </tr>
                    <tr>
                        <td class="bf_detm">
                            <input name="tipo_fon" type="radio" id="tipo_fon_0" value="1" <?php echo ( ( !empty($_GET ) and $tipo_fon == '1' ) or empty( $tipo_fon ) ) ? 'checked="checked"' : ''; ?> /> a frase exata &nbsp;
                            <input name="tipo_fon" type="radio" id="tipo_fon_1" value="2" <?php echo ( !empty($_GET ) and $tipo_fon == '2' ) ? 'checked="checked"' : ''; ?> /> que contenha as palavras
                        </td>
                    </tr>
                </table>

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="buscar" id="buscar" value="Buscar" />
                </div>

                <input name="busca" type="hidden" id="busca" value="busca" />
            </form>

            <script type="text/javascript">
                id("campobusca").select();
                id("campobusca").focus();
            </script>

            <?php

            if ( empty( $_GET['busca'] ) ) {
                include 'footer_simp.php';
                exit;
            }

            if(empty($cont) or $cont < 1) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não foi encontrado nenhuma ocorrência.</p>';
                include 'footer_simp.php';
                exit;
            }

            ?>

            <p class="p_q_info">Essa consulta retornou <?php echo $cont ?> registros (<?php echo round($querytime, 2) ?> seg). <a href="buscadetm.php">Nova consulta</a></p>

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
                    <th class="raio_det"><?php echo SICOP_RAIO ?>
                        <?php echo link_ord_asc( $ordpor, 'raio', $q_string, mb_strtolower( SICOP_RAIO ) . '/' . mb_strtolower( SICOP_CELA ) ) ?>
                        <?php echo link_ord_desc( $ordpor, 'raio', $q_string, mb_strtolower( SICOP_RAIO ) . '/' . mb_strtolower( SICOP_CELA ) ) ?>
                    </th>
                    <th class="cela_det"><?php echo SICOP_CELA ?></th>
                </tr>
                <?php
                $i = 1;

                while($d_det = $query->fetch_assoc()) {

                    $tipo_mov_in  = $d_det['tipo_mov_in'];
                    $tipo_mov_out = $d_det['tipo_mov_out'];
                    $iddestino    = $d_det['iddestino'];

                    $det = manipula_sit_det_b( $tipo_mov_in, $tipo_mov_out, $iddestino );

                    ?>
                <tr class="even">
                    <td class="num_od"><?php echo $i++; ?></td>
                    <td class="nome_det <?php if ( stripos( $ordpor, 'nome' ) !== false ) echo 'ord';?>" title="Pai: <?php echo $d_det['pai_det'];?>&#13;Mãe: <?php echo $d_det['mae_det'];?>&#13;Situação atual: <?php echo $det['sitat'];?>" ><a href="javascript:seleciona('<?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] );?>')"><?php echo highlight($valorbusca, $d_det['nome_det']);?></a></td>
                    <td class="matr_det <?php echo $det['css_class']; ?> <?php if ( stripos( $ordpor, 'matr' ) !== false ) echo 'ord';?>"><?php echo !empty( $d_det['matricula'] ) ? formata_num( $d_det['matricula'] ) : '&nbsp;';?></td>
                    <td class="raio_det <?php echo $det['css_class']; ?> <?php if ( stripos( $ordpor, 'raio' ) !== false ) echo 'ord';?>"><?php echo !empty( $d_det['raio'] ) ? $d_det['raio'] : '&nbsp;'; ?></td>
                    <td class="cela_det <?php echo $det['css_class']; ?> <?php if ( stripos( $ordpor, 'raio' ) !== false ) echo 'ord';?>"><?php echo !empty( $d_det['cela'] ) ? $d_det['cela'] : '&nbsp;'; ?></td>
                </tr>

                    <?php } // fim do while ?>
            </table>

<?php include 'footer_simp.php';?>