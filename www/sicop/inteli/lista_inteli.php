<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';
$q_string = '';

$imp_chefia   = get_session( 'imp_chefia', 'int' );
$imp_cadastro = get_session( 'imp_cadastro', 'int' );
$n_imp_n      = 1;

$n_inteli   = get_session( 'n_inteli', 'int' );
$n_inteli_n = 2;

if ( $n_inteli < $n_inteli_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'INTELIGÊNCIA';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

if (empty($_GET['op'])) {
    $ordpor = 'nomea';
} else {
    $ordpor = $_GET['op'];
}

$ordpor = tratabusca($ordpor);

switch($ordpor) {
    default:
    case 'nomea':
        $ordbusca = "detentos.nome_det ASC";
        break;
    case 'nomed':
        $ordbusca = "detentos.nome_det DESC";
        break;
    case 'matra':
        $ordbusca = "detentos.matricula ASC";
        break;
    case 'matrd':
        $ordbusca = "detentos.matricula DESC";
        break;
    case 'proca':
        $ordbusca = "detentos.procedencia ASC, detentos.nome_det ASC";
        break;
    case 'procd':
        $ordbusca = "detentos.procedencia DESC, detentos.nome_det ASC";
        break;
    case 'dataa':
        $ordbusca = "detentos.data_incl ASC, detentos.nome_det ASC";
        break;
    case 'datad':
        $ordbusca = "detentos.data_incl DESC, detentos.nome_det ASC";
        break;
    case 'ra':
        $ordbusca = "`detentos`.`cod_cela` ASC, detentos.nome_det ASC";
        break;
    case 'rd':
        $ordbusca = "`detentos`.`cod_cela` DESC, detentos.nome_det ASC";
        break;
}

$where = '( ( ISNULL( `mov_det_out`.`cod_tipo_mov` ) OR `mov_det_out`.`cod_tipo_mov` = 4 OR `mov_det_out`.`cod_tipo_mov` = 6 )
            AND
            ( `mov_det_in`.`cod_tipo_mov` = 1 OR `mov_det_in`.`cod_tipo_mov` = 2 OR `mov_det_in`.`cod_tipo_mov` = 3 ) ) ';


$query = "SELECT
            `detentos`.`iddetento`,
            `detentos`.`nome_det`,
            `detentos`.`matricula`,
            `detentos`.`pai_det`,
            `detentos`.`mae_det`,
            `mov_det_in`.`data_mov` AS `data_incl`,
            DATE_FORMAT ( `mov_det_in`.`data_mov`, '%d/%m/%Y' ) AS `data_incl_f`,
            `mov_det_in`.`cod_tipo_mov` AS `tipo_mov_in`,
            `mov_det_out`.`cod_tipo_mov` AS `tipo_mov_out`,
            `unidades_in`.`unidades` AS `procedencia`,
            `unidades_out`.`idunidades` AS `iddestino`,
            `cela`.`cela`,
            `raio`.`raio`,
            `inteligencia`.`idinteli`
          FROM
            `inteligencia`
            INNER JOIN `detentos` ON `detentos`.`iddetento` = `inteligencia`.`cod_detento`
            LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
            LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
            LEFT JOIN `unidades` `unidades_in` ON `mov_det_in`.`cod_local_mov` = `unidades_in`.`idunidades`
            LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
            LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
            LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
          WHERE
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
    $msg['text'] = "Falha na consulta ( LISTA DE " . SICOP_DET_DESC_U . "S MONITORADOS PELA INTELIGENCIA ).\n\n $msg_err_mysql";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );


    echo msg_js( 'FALHA!', 1 );
    exit;

}

$db->closeConnection();

$cont = $query->num_rows;

parse_str( $_SERVER['QUERY_STRING'], $q_string );

if ( isset( $q_string['op'] ) ) {
    unset( $q_string['op'] );
}

$desc_pag = 'Lista de ' . SICOP_DET_DESC_L . 's monitorad' . SICOP_DET_ART_L . 's';

require 'cab.php';
$trail = new Breadcrumb();
$trail->add( $desc_pag, $_SERVER['PHP_SELF'], 3 );
$trail->output();
?>

            <p class="descript_page"><?php echo SICOP_DET_DESC_U; ?>S MONITORAD<?php echo SICOP_DET_ART_U; ?>S PELA INTELIGÊNCIA</p>

            <?php
            if( empty( $cont ) or $cont < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não foi encontrado nenhuma ocorrência.</p>';
                include 'footer.php';
                exit;
            }
            ?>

            <p class="p_q_info">
                Essa consulta retornou <?php echo $cont ?> registros (<?php echo round($querytime, 2) ?> seg).
                <?php if ( $imp_chefia >= $n_imp_n or $imp_cadastro >= $n_imp_n ) { ?>
                - <a href='javascript:void(0)' title="Imprimir a lista" onclick="submit_form_nw( 'lista_det', '<?php echo SICOP_ABS_PATH ?>print/lista_busca.php' )" >Imprimir</a>
                - <a href='javascript:void(0)' title="Exportar a lista para o excel" onclick="submit_form_nlk( 'lista_det', '<?php echo SICOP_ABS_PATH ?>export/exp_busca.php' );">Exportar</a>
                <?php }; ?>
            </p>

            <form action="" method="post" name="lista_det" id="lista_det">

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
                        <th class="local_mov">Procedência
                            <?php echo link_ord_asc( $ordpor, 'proc', $q_string, 'procedência' ) ?>
                            <?php echo link_ord_desc( $ordpor, 'proc', $q_string, 'procedência' ) ?>
                        </th>
                        <th class="data_mov"> Inclusão
                            <?php echo link_ord_asc( $ordpor, 'data', $q_string, 'data da inclusão' ) ?>
                            <?php echo link_ord_desc( $ordpor, 'data', $q_string, 'data da inclusão' ) ?>
                        </th>
                        <th class="oculta"></th>
                    </tr>

                    <?php
                    $i = 1;

                    while( $d_det = $query->fetch_object() ) {

                        $tipo_mov_in  = $d_det->tipo_mov_in;
                        $procedencia  = $d_det->procedencia;
                        $data_incl    = $d_det->data_incl;
                        $tipo_mov_out = $d_det->tipo_mov_out;
                        $iddestino    = $d_det->iddestino;

                        $det = manipula_sit_det_l( $tipo_mov_in, $procedencia, $data_incl, $tipo_mov_out, $iddestino );

                        ?>
                    <tr class="even">
                        <td class="num_od"><?php echo $i++; ?></td>
                        <td class="nome_det <?php if ( stripos( $ordpor, 'nome' ) !== false ) echo 'ord';?>" title="Pai: <?php echo $d_det->pai_det;?>&#13;Mãe: <?php echo $d_det->mae_det;?>&#13;Situação atual: <?php echo $det['sitat'];?>"><a href="detal_inteli.php?idinteli=<?php echo $d_det->idinteli; ?>"> <?php echo $d_det->nome_det;?></a></td>
                        <td class="matr_det <?php echo $det['css_class']; if ( stripos( $ordpor, 'matr' ) !== false ) echo ' ord';?>"><?php echo !empty( $d_det->matricula ) ? formata_num( $d_det->matricula ) : '&nbsp;';?></td>
                        <td class="raio_det <?php echo $det['css_class']; if ( stripos( $ordpor, 'raio' ) !== false ) echo ' ord';?>"><?php echo !empty( $d_det->raio ) ? $d_det->raio : '&nbsp;'; ?></td>
                        <td class="cela_det <?php echo $det['css_class']; if ( stripos( $ordpor, 'raio' ) !== false ) echo ' ord';?>"><?php echo !empty( $d_det->cela ) ? $d_det->cela : '&nbsp;'; ?></td>
                        <td class="local_mov <?php echo $det['css_class']; if ( stripos( $ordpor, 'proc' ) !== false ) echo ' ord';?>"><?php echo !empty( $det['procedencia'] ) ? $det['procedencia'] : '&nbsp;'; ?></td>
                        <td class="data_mov <?php echo $det['css_class']; if ( stripos( $ordpor, 'data' ) !== false ) echo ' ord';?>"><?php echo !empty( $det['data_incl'] ) ? $det['data_incl'] : '&nbsp;'; ?></td>
                        <td class="oculta"><input type="hidden" name="iddet_p[]" value="<?php echo $d_det->iddetento;?>" /></td>
                    </tr>
                        <?php } // fim do while ?>
                </table>

                <input type="hidden" name="op" value="<?php echo $ordpor;?>" />

            </form>

<?php include 'footer.php'; ?>