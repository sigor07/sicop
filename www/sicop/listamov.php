<?php

if ( !isset( $_SESSION ) ) session_start();

require 'init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';
$q_string = '';

$imp_chefia   = get_session( 'imp_chefia', 'int' );
$imp_cadastro = get_session( 'imp_cadastro', 'int' );
$n_imp_n      = 1;

$tipomov = get_get( 'tipomov', 'int' );

if ( empty( $tipomov ) or $tipomov > 8 ) {
    header('Location: home.php');
    exit;
}

$ordpor = 'nomea';

if ( !empty( $_GET['op'] ) ) {
    $ordpor = get_get( 'op', 'busca' );
}

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
    case 'proca':
        $tabela = 'unidades_in';
        //if ( $tipomov > 4 ) $tabela = 'unidades_out';
        $ordbusca = '`' . $tabela . '`.`unidades` ASC, `detentos`.`nome_det` ASC';
        break;
    case 'procd':
        $tabela = 'unidades_in';
        //if ( $tipomov > 4 ) $tabela = 'unidades_out';
        $ordbusca = '`' . $tabela . '`.`unidades` DESC, `detentos`.`nome_det` ASC';
        break;
    case 'dataa':
        $tabela = 'mov_det_in';
        //if ( $tipomov > 4 ) $tabela = 'mov_det_out';
        $ordbusca = '`' . $tabela . '`.`data_mov` ASC, `detentos`.`nome_det` ASC';
        break;
    case 'datad':
        $tabela = 'mov_det_in';
        //if ( $tipomov > 4 ) $tabela = 'mov_det_out';
        $ordbusca = '`' . $tabela . '`.`data_mov` DESC, `detentos`.`nome_det` ASC';
        break;
    case 'raioa':
        $ordbusca = "`detentos`.`cod_cela` ASC, `detentos`.`nome_det` ASC";
        break;
    case 'raiod':
        $ordbusca = "`detentos`.`cod_cela` DESC, `detentos`.`nome_det` ASC";
        break;
}

if ( empty( $_GET['data_ini'] ) and empty( $_GET['data_fim'] ) ) {

    header('Location: home.php');
    exit;

}

    $data_ini = get_get( 'data_ini', 'busca' );
    $data_fim = get_get( 'data_fim', 'busca' );

    if ( !empty( $data_ini ) or !empty( $data_fim ) ){

        $data_f = !empty( $data_ini ) ? $data_ini : $data_fim;

        $clausula_data = "`data_mov` = STR_TO_DATE( '$data_f', '%d/%m/%Y' ) ";

        if ( !empty( $data_ini ) and  !empty( $data_fim ) ){

            $clausula_data = "`data_mov` BETWEEN STR_TO_DATE( '$data_ini', '%d/%m/%Y' ) AND STR_TO_DATE( '$data_fim', '%d/%m/%Y' )";

        }

    }

    $idlocal = get_get( 'idlocal', 'int' );

    $clausula_local = '';
    if ( !empty( $idlocal ) ){

        // alvará
        if ( $idlocal == 100 ) {

            $clausula_local = ' AND `cod_local_mov` BETWEEN 100 AND 199';

        // evadido
        } else if ( $idlocal == 200 ) {

            $clausula_local = ' AND `cod_local_mov` BETWEEN 200 AND 299';

        // falecido
        } else if ( $idlocal == 300 ) {

            $clausula_local = ' AND `cod_local_mov` BETWEEN 300 AND 399';

        } else {

            $clausula_local = " AND `cod_local_mov` = $idlocal";

        }

    }

$where = " `detentos`.`iddetento` IN( SELECT `cod_detento` FROM `mov_det` WHERE `cod_tipo_mov` = $tipomov $clausula_local AND ( $clausula_data ) )";

$query = "SELECT
            `detentos`.`iddetento`,
            `detentos`.`nome_det`,
            `detentos`.`matricula`,
            `detentos`.`pai_det`,
            `detentos`.`mae_det`,
            `mov_det_in`.`data_mov` AS data_incl,
            DATE_FORMAT(`mov_det_in`.`data_mov`, '%d/%m/%Y') AS data_incl_f,
            `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
            `mov_det_out`.`data_mov` AS data_excl,
            DATE_FORMAT(`mov_det_out`.`data_mov`, '%d/%m/%Y') AS data_excl_f,
            `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
            `unidades_in`.`unidades` AS procedencia,
            `unidades_out`.`unidades` AS destino,
            `unidades_out`.`idunidades` AS iddestino,
            `cela`.`cela`,
            `raio`.`raio`
          FROM
            `detentos`
            LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
            LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
            LEFT JOIN `unidades` `unidades_in` ON `mov_det_in`.`cod_local_mov` = `unidades_in`.`idunidades`
            LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
            LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
            LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
          WHERE
            $where
          Order By
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

if ( $cont == 1 ) { //se o número de ocorrências for igual a 1 vai direto para à página do detento, e finaliza com o exit
    $d_det = $query->fetch_assoc();
    header( 'Location: ' . SICOP_ABS_PATH . 'detento/detalhesdet.php?iddet=' . $d_det['iddetento'] );
    exit;
}

parse_str( $_SERVER['QUERY_STRING'], $q_string );

if ( isset( $q_string['op'] ) ) {
    unset( $q_string['op'] );
}

$mensagem = "Acesso à página: $pag";
salvaLog($mensagem);

$desc_pag = 'Lista de ' . SICOP_DET_DESC_L . 's';

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 4 );
$trail->output();
?>

            <p class="descript_page">LISTA DE <?php echo SICOP_DET_DESC_U; ?>S</p>

<?php include 'lista_busca.php'; ?>

<?php include 'footer.php'; ?>