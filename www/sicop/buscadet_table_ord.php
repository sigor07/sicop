<?php
if ( !isset( $_SESSION ) ) session_start();

require 'init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';
$cont = '';
$ordpor = '';
$q_string = '';
/*ob_start("ob_gzhandler");*/

$n_rol        = get_session( 'n_rol', 'int' );
$n_rol_n      = 3;

$n_cadastro   = get_session( 'n_cadastro', 'int' );
$n_cad_n      = 3;

$n_sind       = get_session( 'n_sind', 'int' );
$n_sind_n     = 3;

$n_pront      = get_session( 'n_pront', 'int' );
$n_pront_n    = 3;

$n_portaria   = get_session( 'n_portaria', 'int' );
$n_portaria_n = 3;

$n_sedex      = get_session( 'n_sedex', 'int' );
$n_sedex_n    = 3;

$n_peculio    = get_session( 'n_peculio', 'int' );
$n_pec_baixa  = get_session( 'n_peculio_baixa', 'int' );
$n_peculio_n  = 3;

$n_inteli     = get_session( 'n_inteli', 'int' );
$n_inteli_n   = 3;

$n_incl       = get_session( 'n_incl', 'int' );
$n_incl_n     = 3;

$imp_chefia   = get_session( 'imp_chefia', 'int' );
$imp_cadastro = get_session( 'imp_cadastro', 'int' );
$n_imp_n      = 1;

$tipo_fon      = '';
$valorbusca    = NULL;
$valorbusca_sf = NULL;
$proced        = '';
$data_in_ini   = get_get( 'data_in_ini', 'busca' );
$data_in_fim   = get_get( 'data_in_fim', 'busca' );
$data_out_ini  = get_get( 'data_out_ini', 'busca' );
$data_out_fim  = get_get( 'data_out_fim', 'busca' );
$unidade       = get_get( 'unidade', 'int' );
$n_cela        = get_get( 'n_cela', 'int' );
$n_raio        = get_get( 'n_raio', 'int' );
$tipo_sit      = get_get( 'tipo_sit', 'int' );

if( !empty( $_GET['proced'] ) ) {

    $proced = get_get( 'proced', 'busca' );

}

$inner_join = '';
$desc_pag = 'Pesquisar detentos';
$link = 'detento/detalhesdet.php';

switch($proced) {
    default:
    case '':
    case 'bd':
        $link = 'detento/detalhesdet.php';
        $motivo = 'DETENTOS';
        $desc_pag = 'Pesquisar detentos';
        break;
    case 'cadrol':
        if ($n_rol < $n_rol_n) {
            require 'cab_simp.php';
            $tipo = 0;
            include 'init/msgnopag.php';
            $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
            salvaLog( $mensagem );
            exit;
        }
        $link = 'visita/cadastravisit.php';
        $motivo = 'DETENTOS PARA CADASTRAMENTO DE VISITANTES';
        $desc_pag = 'Pesquisar detentos para cadastramento de visitantes';
        break;
    case 'regrol':
        if ( $n_rol < $n_rol_n ) {
            require 'cab_simp.php';
            $tipo = 0;
            include 'init/msgnopag.php';
            $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
            salvaLog( $mensagem );
            exit;
        }
        $link = 'visita/regentrv.php';
        $motivo = 'DETENTOS PARA REGISTRO DE ENTRADA';
        $desc_pag = 'Pesquisar detentos para registro de entrada';
        break;
    case 'brol':
        $n_rol_n = 2;
        if ( $n_rol < $n_rol_n ) {
            require 'cab_simp.php';
            $tipo = 0;
            include 'init/msgnopag.php';
            $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
            salvaLog( $mensagem );
            exit;
        }
        $link = 'visita/rol_visit.php';
        $motivo = 'RÓIS DE VISITAS PELO DETENTO';
        $desc_pag = 'Pesquisar róis de visitas pelo detento';
        break;
    case 'cadaud':
        if ($n_cadastro < $n_cad_n) {
            require 'cab_simp.php';
            $tipo = 0;
            include 'init/msgnopag.php';
            $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
            salvaLog( $mensagem );
            exit;
        }
        $link = 'cadastro/cadaud.php';
        $motivo = 'DETENTOS PARA CADASTRAMENTO DE AUDIÊNCIA';
        $desc_pag = 'Pesquisar detentos para cadastramento de audiência';
        $ant = empty( $_GET['ant'] ) ? '' : (int)$_GET['ant'];
        if ( empty( $ant ) and isset( $_SESSION['l_id_aud'] ) ) unset( $_SESSION['l_id_aud'] );
        if ( !empty( $_SESSION['l_id_aud'] ) ) {
            $link = 'cadastro/cadaudant.php';
        }
        break;
    case 'cadapcc':
        if ($n_cadastro < $n_cad_n) {
            require 'cab_simp.php';
            $tipo = 0;
            include 'init/msgnopag.php';
            $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
            salvaLog( $mensagem );
            exit;
        }
        $link = 'cadastro/cadapcc.php';
        $motivo = 'DETENTOS PARA CADASTRAMENTO DE APCC';
        $desc_pag = 'Pesquisar detentos para cadastramento de APCC';
        break;
    case 'cadsind':
        if ($n_sind < $n_sind_n) {
            require 'cab_simp.php';
            $tipo = 0;
            include 'init/msgnopag.php';
            $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
            salvaLog( $mensagem );
            exit;
        }
        $link = 'sind/cadpda.php';
        $motivo = 'DETENTOS PARA CADASTRAMENTO DE PDA';
        $desc_pag = 'Pesquisar detentos para cadastramento de PDA';
        break;
    case 'bsind':
        $n_sind_n = 2;
        if ($n_sind < $n_sind_n) {
            require 'cab_simp.php';
            $tipo = 0;
            include 'init/msgnopag.php';
            $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
            salvaLog( $mensagem );
            exit;
        }
        $link = 'sind/rol_pda.php';
        $motivo = 'PDAs PELO DETENTO';
        $inner_join = 'INNER JOIN `sindicancias` ON `sindicancias`.`cod_detento` = `detentos`.`iddetento`';
        $desc_pag = 'Pesquisar PDAs pelo detento';
        break;
    case 'cadpro':
        if ($n_pront < $n_pront_n) {
            require 'cab_simp.php';
            $tipo = 0;
            include 'init/msgnopag.php';
            $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
            salvaLog( $mensagem );
            exit;
        }
        $link = 'prontuario/cadprocess.php';
        $motivo = 'DETENTOS PARA CADASTRAMENTO DE PROCESSO';
        $desc_pag = 'Pesquisar detentos para cadastramento de processo';
        break;
    case 'bpro':
        $n_pront_n = 2;
        if ($n_pront < $n_pront_n) {
            require 'cab_simp.php';
            $tipo = 0;
            include 'init/msgnopag.php';
            $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
            salvaLog( $mensagem );
            exit;
        }
        $link = 'prontuario/detalgrade.php';
        $motivo = 'GRADES PELO DETENTO';
        $desc_pag = 'Pesquisar grades pelo detento';
        break;
    case 'cadsed':
        if ($n_portaria < $n_portaria_n) {
            require 'cab_simp.php';
            $tipo = 0;
            require 'init/msgnopag.php';
            $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
            salvaLog( $mensagem );
            exit;
        }

        if ($n_sedex < $n_sedex_n) {
            require 'cab_simp.php';
            $tipo = 0;
            require 'init/msgnopag.php';
            $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
            salvaLog( $mensagem );
            exit;
        }
        $link = 'sedex/sedex_in.php';
        $motivo = 'DETENTOS PARA CADASTRAMENTO DE SEDEX';
        $desc_pag = 'Pesquisar detentos para cadastramento de sedex';
        break;
    case 'bsed': // buscar rol de sedex
        $n_sedex_n      = 2;
        if ($n_sedex < $n_sedex_n) {
            require 'cab_simp.php';
            $tipo = 0;
            require 'init/msgnopag.php';
            $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
            salvaLog( $mensagem );
            exit;
        }
        $link = 'sedex/rol_sedex.php';
        $motivo = 'SEDEX PELO DETENTO';
        $inner_join = 'INNER JOIN `sedex` ON `sedex`.`cod_detento` = `detentos`.`iddetento`';
        $desc_pag = 'Pesquisar sedex pelo detento';
        break;
    case 'cadpec': // cadastrar pertence
        if ($n_peculio < $n_peculio_n) {
            require 'cab_simp.php';
            $tipo = 0;
            include 'init/msgnopag.php';
            $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
            salvaLog( $mensagem );
            exit;
        }
        $link = 'peculio/cadpert.php';
        $motivo = 'DETENTOS PARA CADASTRAMENTO DE PERTENCES';
        $desc_pag = 'Pesquisar detentos para cadastramento de pertences';
        break;
    case 'caddep': // cadastrar deposito
        if ($n_peculio < $n_peculio_n) {
            require 'cab_simp.php';
            $tipo = 0;
            include 'init/msgnopag.php';
            $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
            salvaLog( $mensagem );
            exit;
        }
        $link = 'peculio/caddep.php';
        $motivo = 'DETENTOS PARA CADASTRAMENTO DE DEPÓSITO';
        $desc_pag = 'Pesquisar detentos para cadastramento de depósito';
        break;
    case 'cadsaq': // cadastrar saque
        if ($n_peculio < $n_peculio_n) {
            require 'cab_simp.php';
            $tipo = 0;
            include 'init/msgnopag.php';
            $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
            salvaLog( $mensagem );
            exit;
        }
        if ($n_pec_baixa < 1) {
            require 'cab_simp.php';
            $tipo = 0;
            include 'init/msgnopag.php';
            $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
            salvaLog( $mensagem );
            exit;
        }
        $link = 'peculio/cadsaq.php';
        $motivo = 'DETENTOS PARA CADASTRAMENTO DE SAQUE';
        $desc_pag = 'Pesquisar detentos para cadastramento de saque';
        break;
    case 'bpec': // pesquisar pecúlio
        $n_peculio_n = 2;
        if ($n_peculio < $n_peculio_n) {
            require 'cab_simp.php';
            $tipo = 0;
            include 'init/msgnopag.php';
            $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
            salvaLog( $mensagem );
            exit;
        }
        $link = 'peculio/detalpec.php';
        $motivo = 'PERTENCES PELO DETENTO';
        $desc_pag = 'Pesquisar pertences pelo detento';
        break;
    case 'cadint': //cadastrar na inteligência
        if ( $n_inteli < $n_inteli_n ) {
            require 'cab_simp.php';
            $tipo = 0;
            include 'init/msgnopag.php';
            $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
            salvaLog( $mensagem );
            exit;
        }
        $link = 'inteli/cadinteli.php';
        $motivo = 'DETENTOS PARA INCLUSÃO NA LISTA DE MONITORAMENTO';
        $desc_pag = 'Pesquisar detentos para inclusão na lista de monitoramento';
        break;
    case 'cadtv': //cadastrar tv
        if ( $n_incl < $n_incl_n ) {
            require 'cab_simp.php';
            $tipo = 0;
            include 'init/msgnopag.php';
            $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
            salvaLog( $mensagem );
            exit;
        }
        $link = 'incl/cadtv.php';
        $motivo = 'DETENTOS PARA CADASTRAMENTO DE TV';
        $desc_pag = 'Pesquisar detentos para cadastramento de tv';
        break;
    case 'cadrd': //cadastrar rádio
        if ( $n_incl < $n_incl_n ) {
            require 'cab_simp.php';
            $tipo = 0;
            include 'init/msgnopag.php';
            $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
            salvaLog( $mensagem );
            exit;
        }
        $link = 'incl/cadradio.php';
        $motivo = 'DETENTOS PARA CADASTRAMENTO DE RÁDIO';
        $desc_pag = 'Pesquisar detentos para cadastramento de rádio';
        break;
    case 'impter': // imprimir termos do prontuário
        $imp_pront = get_session( 'imp_pront', 'int' );
        if ($imp_pront < $n_imp_n) {
            require 'cab_simp.php';
            $tipo = 0;
            include 'init/msgnopag.php';
            $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
            salvaLog( $mensagem );
            exit;
        }
        $link = 'prontuario/termo_enc.php';
        $motivo = 'DETENTOS PARA IMPRESSÃO DE TERMOS DE ENCERRAMENTO';
        $desc_pag = 'Pesquisar detentos para impressão de termos de encerramento';
        break;
    case 'impterseg': // imprimir termos do seguro
        $imp_chefia = get_session( 'imp_chefia', 'int' );
        if ( $imp_chefia < $n_imp_n ) {
            require 'cab_simp.php';
            $tipo = 0;
            include 'init/msgnopag.php';
            $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
            salvaLog( $mensagem );
            exit;
        }
        $link = 'detento/termo_seg.php';
        $motivo = 'DETENTOS PARA IMPRESSÃO DE TERMOS DE SEGURO';
        $desc_pag = 'Pesquisar detentos para impressão de termos de seguro';
        break;
}



if( !empty( $_GET['busca'] ) ) {

    $where = '';
    $tipo_fon = (int)$_GET['tipo_fon'];
    $tipo_fon = !empty( $tipo_fon ) ? (int)$tipo_fon : '';

    if ( !empty( $_GET['campobusca'] ) ) {

        $valorbusca = $_GET['campobusca'];
        $valorbusca_sf = $_GET['campobusca'];
        $valorbusca = tratabusca($valorbusca);

        if ( $tipo_fon == 1 ) {

            $where .= "WHERE ( `detentos`.`nome_det` LIKE '%$valorbusca%'
                              OR
                              `detentos`.`matricula` LIKE '$valorbusca%'
                              OR
                              ( `aliases`.`cod_tipoalias` = 4 AND `aliases`.`alias_det` LIKE '%$valorbusca%' ) )";

        } else {

            $valorbusca = preg_replace( '/\s?\b\w{1,2}\b/' , null , $valorbusca ); // remover palavras com 2 letras ou menos

            if ( empty($valorbusca) ) {
                echo '<script type="text/javascript">history.go(-1);</script>';
                exit;
            }

            $arr_busca = explode( ' ', $valorbusca );

            $where .= 'WHERE (';
            foreach( $arr_busca as $indice => $valor ) {
                if ($valor == NULL) continue;
                $where .= " `detentos`.`nome_det` LIKE '%$valor%' AND";
            }

            $where_alias = '(';
            foreach( $arr_busca as $indice => $valor ) {
                if ($valor == NULL) continue;
                $where_alias .= " `aliases`.`alias_det` LIKE '%$valor%' AND";
            }

        }

        if ( $tipo_fon == 2 ) {

            if ( !empty( $where ) ) {
                $where = substr($where, 0, -3); //remover o ultimo 'AND'
                $where = $where . " OR `detentos`.`matricula` LIKE '$valorbusca%' ";
            }

            $where_alias_f = '';
            if ( !empty( $where_alias ) ) {
                $where_alias_f = substr($where_alias, 0, -3); //remover o ultimo 'AND'
                $where_alias_f =  " OR ( `aliases`.`cod_tipoalias` = 4 AND " . $where_alias_f . ' ) )';
            }

            $where = $where . $where_alias_f . ' )';

        }

    }

    if ( !empty( $unidade ) ){
        if ( !empty( $where ) ){
            $where .= " AND ( `unidades_in`.`idunidades` = $unidade )";
        } else {
            $where .= "WHERE ( `unidades_in`.`idunidades` = $unidade )";
        }
    }

    if ( !empty( $n_cela ) or !empty( $n_raio ) ){
        if ( empty( $n_cela ) ){
            if ( !empty( $where ) ){
                $where .= " AND ( `raio`.`idraio` = $n_raio )";
            } else {
                $where .= "WHERE ( `raio`.`idraio` = $n_raio )";
            }
        } else {
            if ( !empty( $where ) ){
                $where .= " AND ( `detentos`.`cod_cela` = $n_cela )";
            } else {
                $where .= "WHERE ( `detentos`.`cod_cela` = $n_cela )";
            }
        }
    }

    if ( !empty( $data_in_ini ) or !empty( $data_in_fim ) ){

        if ( !empty( $data_in_ini ) and  !empty( $data_in_fim ) ){

            $clausula_data_in = "`mov_det_in`.`data_mov` BETWEEN STR_TO_DATE('$data_in_ini', '%d/%m/%Y') AND STR_TO_DATE('$data_in_fim', '%d/%m/%Y')";

            if ( !empty( $where ) ){
                $where .= ' AND ' . $clausula_data_in;
            } else {
                $where .= 'WHERE ' . $clausula_data_in;
            }

        } else {

            $data_in = !empty( $data_in_ini ) ? $data_in_ini : $data_in_fim;

            $clausula_data_in = "`mov_det_in`.`data_mov` = STR_TO_DATE( '$data_in', '%d/%m/%Y' )";

            if ( !empty( $where ) ){
                $where .= ' AND ' . $clausula_data_in;
            } else {
                $where .= 'WHERE ' . $clausula_data_in;
            }

        }

    }

    if ( !empty( $data_out_ini ) or !empty( $data_out_fim ) ){

        if ( !empty( $data_out_ini ) and  !empty( $data_out_fim ) ){

            $clausula_data_out = "`mov_det_out`.`data_mov` BETWEEN STR_TO_DATE('$data_out_ini', '%d/%m/%Y') AND STR_TO_DATE('$data_out_fim', '%d/%m/%Y')";

            if ( !empty( $where ) ){
                $where .= ' AND ' . $clausula_data_out;
            } else {
                $where .= 'WHERE ' . $clausula_data_out;
            }

        } else {

            $data_out = !empty( $data_out_ini ) ? $data_out_ini : $data_out_fim;

            $clausula_data_out = "`mov_det_out`.`data_mov` = STR_TO_DATE( '$data_out', '%d/%m/%Y' )";

            if ( !empty( $where ) ){
                $where .= ' AND ' . $clausula_data_out;
            } else {
                $where .= 'WHERE ' . $clausula_data_out;
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


    //echo $where;
    //exit;

    $ordpor = 'nomea';

    if ( !empty( $_GET['op'] ) ) {
        $ordpor = get_get( 'op', 'busca' );
    }

    $ordbusca = '`detentos`.`nome_det` ASC';

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
        case 'proca':
            $ordbusca = '`unidades_in`.`unidades` ASC, `detentos`.`nome_det` ASC';
            break;
        case 'procd':
            $ordbusca = '`unidades_in`.`unidades` DESC, `detentos`.`nome_det` ASC';
            break;
        case 'dataa':
            $ordbusca = '`mov_det_in`.`data_mov` ASC, `detentos`.`nome_det` ASC';
            break;
        case 'datad':
            $ordbusca = '`mov_det_in`.`data_mov` DESC, `detentos`.`nome_det` ASC';
            break;
        case 'ra':
            $ordbusca = '`detentos`.`cod_cela` ASC, `detentos`.`nome_det` ASC';
            break;
        case 'rd':
            $ordbusca = '`detentos`.`cod_cela` DESC, `detentos`.`nome_det` ASC';
            break;
    }

    $query = "SELECT DISTINCT
                `detentos`.`iddetento`,
                `detentos`.`nome_det`,
                `detentos`.`matricula`,
                `detentos`.`pai_det`,
                `detentos`.`mae_det`,
                `mov_det_in`.`data_mov` AS data_incl,
                DATE_FORMAT(`mov_det_in`.`data_mov`, '%d/%m/%Y') AS data_incl_f,
                `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
                `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
                `unidades_in`.`unidades` AS procedencia,
                `unidades_out`.`idunidades` AS iddestino,
                `cela`.`cela`,
                `raio`.`raio`
              FROM
                `detentos`
                $inner_join
                LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                LEFT JOIN `unidades` `unidades_in` ON `mov_det_in`.`cod_local_mov` = `unidades_in`.`idunidades`
                LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
                LEFT JOIN `aliases` ON `detentos`.`iddetento` = `aliases`.`cod_detento`
                LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
                LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
              $where
              ORDER BY
                $ordbusca";

    //echo nl2br($query);
    //exit;

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query = $model->query( $query );

    // fechando a conexao
    $model->closeConnection();

    $querytime = $model->getQueryTime();

    $cont = $query->num_rows;

    $valor_busca = valor_user($_GET);

    $mensagem = "[ BUSCA EFETUADA ]\nBusca de detentos efetuada\n\n $valor_busca\n\n Página: $pag";
    salvaLog( $mensagem );

    if( $cont == 1 ) { //se o número de ocorrências for igual a 1 vai direto para à página do detento, e finaliza com o exit
        $d_det = $query->fetch_assoc();
        header( "Location: $link?iddet=" . $d_det['iddetento'] );
        exit;
    }

    //parse_str( $_SERVER[ 'QUERY_STRING' ], $q_string );

//    if ( isset( $q_string['op'] ) ) {
//        unset( $q_string['op'] );
//    }

}

$q_tipo_sit = 'SELECT `idtipo_sit`, `tipo_sit` FROM `tipo_sit_det_busca` ORDER BY `idtipo_sit` ASC ';
$q_proced = 'SELECT `idunidades`, `unidades` FROM `unidades` WHERE `in` = true ORDER BY `unidades`';
$query_raio = 'SELECT `idraio`, `raio` FROM `raio` ORDER BY `raio` ASC';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_tipo_sit = $model->query( $q_tipo_sit );
$q_proced = $model->query( $q_proced );
$query_raio = $model->query( $query_raio );


if ( !empty( $n_raio ) ) {

    $query_cela = "SELECT `idcela`, `cela` FROM `cela` WHERE `cod_raio` = $n_raio ORDER BY cela ASC";
    $query_cela = $model->query( $query_cela );

}

// fechando a conexao
$model->closeConnection();

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

$trail = new Breadcrumb();
$trail->add($desc_pag, $pag_atual, 3);
$trail->output();

?>
        <script type="text/javascript" src="js/jquery.highlight.js"></script>
        <script type="text/javascript" src="js/jquery.tablesorter.js" ></script>

            <br />
            <p align="center" class="paragrafo12Italico">PESQUISAR <?php echo $motivo; ?></p>
            <form action="<?php echo $_SERVER['PHP_SELF']?>" method="get" name="buscadet" id="buscadet" onSubmit="upperMe(campobusca); ">
                <table width="446" align="center"><!--remacc(campobusca);-->
                    <tr>
                        <td align="right">Nome ou matrícula:</td>
                        <td align="left"><input name="campobusca" type="text" class="CaixaTexto" id="campobusca" onkeypress="return blockChars(event, 4);" value="<?php echo $valorbusca_sf ?>" size="50" /></td>
                    </tr>
                    <tr>
                        <td align="right">Pesquisa fonética:</td>
                        <td align="left"><input name="tipo_fon" type="radio" id="tipo_fon_0" value="1" <?php echo ( ( !empty($_GET ) and $tipo_fon == '1' ) or empty( $tipo_fon ) ) ? 'checked="checked"' : ''; ?> /> a frase exata &nbsp; <input name="tipo_fon" type="radio" id="tipo_fon_1" value="2" <?php echo ( !empty($_GET ) and $tipo_fon == '2' ) ? 'checked="checked"' : ''; ?> /> que contenha as palavras </td>
                    </tr>
                    <tr>
                        <td align="right"><?php echo SICOP_RAIO ?>:</td>
                        <td align="left">
                            <select name="n_raio" class="CaixaTexto" id="n_raio" onchange="buscaCela(this.value, '1')">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while($dados_raio = $query_raio->fetch_assoc()) { ?>
                                <option value="<?php echo $dados_raio['idraio'];?>" <?php echo $dados_raio['idraio'] == $n_raio ? 'selected="selected"' : ''; ?>><?php echo $dados_raio['raio'];?></option>
                                <?php };?>
                            </select> &nbsp;
                            <?php echo SICOP_CELA ?>:
                            <select name="n_cela" class="CaixaTexto" id="n_cela">
                                <?php if ( empty( $n_raio ) ) { ?>
                                <option value="" selected="selected">Escolha o raio...</option>
                                <?php } else { ?>
                                <option value="" selected="selected">Selecione...</option>
                                <?php while( $dados_cela = $query_cela->fetch_assoc() ) { ?>
                                <option value="<?php echo $dados_cela['idcela'];?>" <?php echo $dados_cela['idcela'] == $n_cela ? 'selected="selected"' : ''; ?>><?php echo $dados_cela['cela'];?></option>
                                <?php
                                          }
                                      }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="120" align="right">Procedência:</td>
                        <td width="314" align="left">
                            <select name="unidade" class="CaixaTexto" id="unidade">
                                <option value="" >Selecione...</option>
                                <?php while($d_proced = $q_proced->fetch_assoc()) { ?>
                                <option value="<?php echo $d_proced['idunidades'];?>" <?php echo $d_proced['idunidades'] == $unidade ? 'selected="selected"' : ''; ?>><?php echo $d_proced['unidades'];?></option>
                                <?php };?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Data da inclusão:</td>
                        <td align="left">
                            <input name="data_in_ini" type="text" class="CaixaTexto" id="data_in_ini" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_in_ini ?>" size="12" maxlength="10" /> e
                            <input name="data_in_fim" type="text" class="CaixaTexto" id="data_in_fim" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_in_fim ?>" size="12" maxlength="10" />
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Data da exclusão:</td>
                        <td align="left">
                            <input name="data_out_ini" type="text" class="CaixaTexto" id="data_out_ini" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_out_ini ?>" size="12" maxlength="10" /> e
                            <input name="data_out_fim" type="text" class="CaixaTexto" id="data_out_fim" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_out_fim ?>" size="12" maxlength="10" />
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Situação do preso:</td>
                        <td align="left">
                            <select name="tipo_sit" class="CaixaTexto" id="tipo_sit">
                                <option value="" >Todos</option>
                                <?php while($d_tipo_sit = $q_tipo_sit->fetch_assoc()) { ?>
                                <option value="<?php echo $d_tipo_sit['idtipo_sit'];?>" <?php echo $d_tipo_sit['idtipo_sit'] == $tipo_sit ? 'selected="selected"' : ''; ?>><?php echo $d_tipo_sit['tipo_sit'];?></option>
                                <?php };?>
                            </select>
                        </td>
                    </tr>
                </table>
                <table width="369" align="center" cellspacing="5">
                    <?php if ( $proced == 'regrol' ) { ?>
                    <tr>
                        <td width="365" height="20px" align="center">
                            <a href="visita/buscavisit.php?proced=1">Pesquisar pelo visitante</a>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td width="365" align="center">
                            <input name="proced" type="hidden" id="proced" value="<?php echo $proced; ?>" />
                            <?php if ( !empty( $ant ) ) { ?>
                            <input name="ant" type="hidden" id="ant" value="<?php echo $ant; ?>" />
                            <?php } ?>
                            <input name="busca" type="hidden" id="busca" value="busca" />
                            <input type="submit" name="buscar" id="buscar" value="Buscar" />
                        </td>
                    </tr>
              </table>
            </form>

            <script type="text/javascript">

                $(function() {

                    $( "#campobusca" ).focus().select();

                    $( "#data_in_ini, #data_in_fim, #data_out_ini, #data_out_fim" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });

                });

            </script>

            <?php

            if ( empty( $_GET['busca'] ) ) {
                echo '</div></body></html>';
                exit;
            }

            if ( empty( $cont ) or $cont < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p>&nbsp;</p>
                              <p align="center" class="paragrafo12Italico">Não foi encontrado nenhuma ocorrência.</p>
                              <p>&nbsp;</p>
                              <p>&nbsp;</p>
                              </div>
                              </body>
                              </html>';
                exit;
            }

            ?>

            <p align="center">
                Essa consulta retornou <?php echo $cont ?> registros (<?php echo round($querytime, 2) ?> seg). <a href="buscadet.php<?php echo '?proced=' . $proced; ?>">Nova consulta</a>
                <?php if ( $imp_chefia >= $n_imp_n or $imp_cadastro >= $n_imp_n ) { ?>
                - <a href='javascript:void(0)' title="Imprimir a lista" onclick="javascript: ow('print/lista_casa.php?campobusca=<?php echo $valorbusca_sf; ?>&tipo_fon=<?php echo $tipo_fon; ?>&data_in_ini=<?php echo $data_in_ini; ?>&data_in_fim=<?php echo $data_in_fim; ?>&data_out_ini=<?php echo $data_out_ini; ?>&data_out_fim=<?php echo $data_out_fim; ?>&unidade=<?php echo $unidade; ?>&n_cela=<?php echo $n_cela; ?>&n_raio=<?php echo $n_raio; ?>&tipo_sit=<?php echo $tipo_sit; ?>&op=<?php echo $ordpor; ?>', '600', '600'); return false" >Imprimir</a>
                - <a href='javascript:void(0)' title="Exportar a lista para o excel" onclick="submit_form_nlk( 'lista_det', 'export/exp_busca.php' );">Exportar</a>
                <?php }; ?>
            </p>
            <form action="" method="post" name="lista_det" id="lista_det">
                <table class="lista_busca" id="tb_order">
                    <thead>
                        <tr class="cab">
                            <th class="num_od">N</th>
                            <th class="nome_det"><?php echo SICOP_DET_DESC_FU; ?></th>
                            <th class="matr_det">Matrícula</th>
                            <th class="raio_det"><?php echo SICOP_RAIO ?></th>
                            <th class="cela_det"><?php echo SICOP_CELA ?></th>
                            <th class="local_mov">Procedência</th>
                            <th class="data_mov"> Inclusão</th>
                            <th class="oculta"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;

                        while( $d_det = $query->fetch_assoc(  ) ) {

                            $tipo_mov_in  = $d_det['tipo_mov_in'];
                            $procedencia  = $d_det['procedencia'];
                            $data_incl    = $d_det['data_incl'];
                            $tipo_mov_out = $d_det['tipo_mov_out'];
                            $iddestino    = $d_det['iddestino'];

                            $det = manipula_sit_det_l( $tipo_mov_in, $procedencia, $data_incl, $tipo_mov_out, $iddestino );

                            ?>
                        <tr class="even">
                            <td class="num_od"><?php echo $i++; ?></td>
                            <td class="nome_det" title="Pai: <?php echo $d_det['pai_det'];?>&#13;Mãe: <?php echo $d_det['mae_det'];?>&#13;Situação atual: <?php echo $det['sitat'];?>" ><a href="<?php echo $link; ?>?iddet=<?php echo $d_det['iddetento'] /*alphaID($d_det['iddetento'])*/;?>"> <?php echo $d_det['nome_det'] //highlight($valorbusca, $d_det['nome_det']);?></a></td>
                            <td class="matr_det <?php echo $det['css_class']; ?>"><?php echo !empty( $d_det['matricula'] ) ? formata_num( $d_det['matricula'] ) : '&nbsp;';?></td>
                            <td class="raio_det <?php echo $det['css_class']; ?>"><?php echo !empty( $d_det['raio'] ) ? $d_det['raio'] : '&nbsp;'; ?></td>
                            <td class="cela_det <?php echo $det['css_class']; ?>"><?php echo !empty( $d_det['cela'] ) ? $d_det['cela'] : '&nbsp;'; ?></td>
                            <td class="local_mov <?php echo $det['css_class']; ?>"><?php echo !empty( $det['procedencia'] ) ? $det['procedencia'] : '&nbsp;'; ?></td>
                            <td class="data_mov <?php echo $det['css_class']; ?>"><?php echo !empty( $det['data_incl'] ) ? $det['data_incl'] : '&nbsp;'; ?></td>
                            <td class="oculta"><input type="hidden" name="iddet_p[]" value="<?php echo $d_det['iddetento'];?>" /></td>
                        </tr>
                            <?php
                        } // fim do while
                        ?>
                    </tbody>
                </table>

                <input type="hidden" name="op" value="<?php echo $ordpor;?>" />
            </form>

            <div id="pager" class="pager" style="margin: 10px auto; text-align: center; width: 500px;">
                <form>
                    <span>
                        Exibir <select class="pagesize">
                                    <option value="10">10</option>
                                    <option selected="selected" value="20">20</option>
                                    <option value="30">30</option>
                                    <option value="40">40</option>
                                </select> registros
                    </span>
                    <img src="<?php echo SICOP_SYS_IMG_PATH; ?>first.png" class="first" style="margin-bottom: -5px" />
                    <img src="<?php echo SICOP_SYS_IMG_PATH; ?>prev.png" class="prev" style="margin-bottom: -5px" />

                    <span id="span_page_pos" class="pagedisplay"></span>

                    <img src="<?php echo SICOP_SYS_IMG_PATH; ?>next.png" class="next" style="margin-bottom: -5px"/>
                    <img src="<?php echo SICOP_SYS_IMG_PATH; ?>last.png" class="last" style="margin-bottom: -5px"/>

                </form>
            </div>

            <script type="text/javascript">

                $(function() {

                    $( "#data_in_ini, #data_in_fim, #data_out_ini, #data_out_fim" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });

                    $(".first").click( function(){
                        console.log('oii');
                    });

                    // instanciar o tablesorter plugin
                    $("#tb_order").tablesorter({

                        widgets: ['indexFirstColumn', 'columnHighlight'],

                        // passar os argumentos dos headers
                        headers: {
                            0: {
                                sorter: false // desabilitar a ordenação na coluna dos números de ordem
                            },
                            2: {
                                sorter: 'digit' // forçar a ordenação pelo parser digit
                            },
                            3: {
                                // forçar a ordenação por texto da coluna raio, pois senão o plugin
                                // entende q é número, e ordena errado
                                sorter: 'text'
                            },
                            4: {
                                sorter: false // desabilitar a ordenação na coluna cela
                            }
                        },

                        // define a custom text extraction function
                        // textExtraction: 'complex',

                        // ordenar a 2ª coluna (nome) asc
                        sortList: [[1,0]],
                        highlightClass: 'ord',
                        dateFormat: 'dd/mm/yy'

                    });
                    /*.tablesorterPager({
                        container: $("#pager"),
                        size: 20,
                        positionFixed: false

                    }).bind('sortEnd', function(){
                        $("#span_page_pos").html( $("#inp_page_pos").val() );
                    });
                    */


                });

            </script>

<?php include 'footer.php'; ?>