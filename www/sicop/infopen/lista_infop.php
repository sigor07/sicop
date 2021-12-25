<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_pront   = get_session( 'n_pront', 'int' );
$imp_pront = get_session( 'imp_pront', 'int' );
$n_pront_n = 2;
$n_imp_n   = 1;

if ( $n_pront < $n_pront_n ) {
    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
    salvaLog( $mensagem );
    exit;
}

$tipo_infop = get_get( 'tipo_infop', 'busca' );

if ( empty( $tipo_infop ) ) {
    echo msg_js( '', 1 );
    exit;
}

$motivo   = '';
$desc_pag = '';
$join     = '';
$where    = '';
$q_string = '';
$sit_pag  = 'INFOPEN - LISTAR ' . SICOP_DET_DESC_U . 'S';


switch( $tipo_infop ) {

    default:
    case '':
        $tipo_infop = '';
        break;

    case 'art':
        $param_busca = get_get( 'idart', 'int' );
        if ( empty( $param_busca ) ) {
            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador do artigo em branco ( $sit_pag ).\n\n Página: $pag";
            salvaLog($mensagem);
            echo msg_js( 'FALHA!', 1 );
            exit;
        }

        $q_art = "SELECT `artigo` FROM `tipoartigo` WHERE `idartigo` = $param_busca LIMIT 1";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $art = $model->fetchOne( $q_art );

        // fechando a conexao
        $model->closeConnection();

        if( $art === false ) {

            echo msg_js( 'FALHA!!!', 1 );
            exit;

        }

        $motivo = SICOP_DET_DESC_U . 'S NO ARTIGO ' . $art;
        $desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's pelo artigo';
        $join = 'LEFT JOIN `tipoartigo` ON `detentos`.`cod_artigo` = `tipoartigo`.`idartigo`';
        $where = " AND `detentos`.`cod_artigo` = $param_busca";
        break;

    case 'part':
        $motivo = SICOP_DET_DESC_U . 'S COM PENDÊNCIA DE ARTIGO';
        $desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's com pendência de artigo';
        $where = " AND ISNULL( `detentos`.`cod_artigo` )";
        break;

    case 'raca':
        $param_busca = get_get( 'idraca', 'int' );
        if ( empty( $param_busca ) ) {
            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador da raça em branco ( $sit_pag ).\n\n Página: $pag";
            salvaLog($mensagem);
            echo msg_js( 'FALHA!', 1 );
            exit;
        }

        $q_raca = "SELECT `cutis` FROM `tipocutis` WHERE `idcutis` = $param_busca LIMIT 1";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $raca = $model->fetchOne( $q_raca );

        // fechando a conexao
        $model->closeConnection();

        if( $raca === false ) {

            echo msg_js( 'FALHA!!!', 1 );
            exit;

        }

        $motivo = SICOP_DET_DESC_U . 'S DE COR ' . $raca;
        $desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's pela raça';
        $join = 'LEFT JOIN `tipocutis` ON `detentos`.`cod_cutis` = `tipocutis`.`idcutis`';
        $where = " AND `detentos`.`cod_cutis` = $param_busca";
        break;

    case 'praca':
        $motivo = SICOP_DET_DESC_U . 'S COM PENDÊNCIA DE RAÇA';
        $desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's com pendência de raça';
        $where = " AND ISNULL( `detentos`.`cod_cutis` )";
        break;

    case 'reg':
        $param_busca = get_get( 'idreg', 'int' );
        if ( empty( $param_busca ) ) {
            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador da situação processual em branco ( $sit_pag ).\n\n Página: $pag";
            salvaLog($mensagem);
            echo msg_js( 'FALHA!', 1 );
            exit;
        }

        $q_reg = "SELECT `sit_proc` FROM `tiposituacaoprocessual` WHERE `idsit_proc` = $param_busca LIMIT 1";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $reg = $model->fetchOne( $q_reg );

        // fechando a conexao
        $model->closeConnection();

        if( $reg === false ) {

            echo msg_js( 'FALHA!!!', 1 );
            exit;

        }

        $motivo = SICOP_DET_DESC_U . 'S COM SITUAÇÃO PROCESSUAL ' . $reg;
        $desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's pela situação processual';
        $join = 'LEFT JOIN `tiposituacaoprocessual` ON `detentos`.`cod_sit_proc` = `tiposituacaoprocessual`.`idsit_proc`';
        $where = " AND `detentos`.`cod_sit_proc` = $param_busca";
        break;

    case 'preg':
        $motivo = SICOP_DET_DESC_U . 'S COM PENDÊNCIA DE REGIME DE PRISÃO';
        $desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's com pendência de regime de prisão';
        $where = " AND ISNULL( `detentos`.`cod_sit_proc` )";
        break;

    case 'nac':
        $param_busca = get_get( 'idnac', 'int' );
        if ( empty( $param_busca ) ) {
            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador da nacionalidade em branco ( $sit_pag ).\n\n Página: $pag";
            salvaLog($mensagem);
            echo msg_js( 'FALHA!', 1 );
            exit;
        }

        $q_nac = "SELECT `nacionalidade` FROM `tiponacionalidade` WHERE `idnacionalidade` = $param_busca LIMIT 1";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $nac = $model->fetchOne( $q_nac );

        // fechando a conexao
        $model->closeConnection();

        if( $nac === false ) {

            echo msg_js( 'FALHA!!!', 1 );
            exit;

        }

        $motivo = SICOP_DET_DESC_U . 'S DE NACIONALIDADE ' . $nac;
        $desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's pela nacionalidade';
        $join = 'LEFT JOIN `tiponacionalidade` ON `detentos`.`cod_nacionalidade` = `tiponacionalidade`.`idnacionalidade`';
        $where = " AND `detentos`.`cod_nacionalidade` = $param_busca";
        break;

    case 'pnac':
        $motivo = SICOP_DET_DESC_U . 'S COM PENDÊNCIA DE NACIONALIDADE';
        $desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's com pendência de nacionalidade';
        $where = " AND ISNULL( `detentos`.`cod_nacionalidade` )";
        break;

    case 'esc':
        $param_busca = get_get( 'idesc', 'int' );
        if ( empty( $param_busca ) ) {
            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador da escolaridade em branco ( $sit_pag ).\n\n Página: $pag";
            salvaLog($mensagem);
            echo msg_js( 'FALHA!', 1 );
            exit;
        }

        $q_esc = "SELECT `escolaridade` FROM `tipoescolaridade` WHERE `idescolaridade` = $param_busca LIMIT 1";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $esc = $model->fetchOne( $q_esc );

        // fechando a conexao
        $model->closeConnection();

        if( $esc === false ) {

            echo msg_js( 'FALHA!!!', 1 );
            exit;

        }

        $motivo = SICOP_DET_DESC_U . 'S DE ESCOLARIDADE ' . $esc;
        $desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's pela escolaridade';
        $join = 'LEFT JOIN `tipoescolaridade` ON `detentos`.`cod_instrucao` = `tipoescolaridade`.`idescolaridade`';
        $where = " AND `detentos`.`cod_instrucao` = $param_busca";
        break;

    case 'pesc':
        $motivo = SICOP_DET_DESC_U . 'S COM PENDÊNCIA DE ESCOLARIDADE';
        $desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's com pendência de escolaridade';
        $where = " AND ISNULL( `detentos`.`cod_instrucao` )";
        break;

    case 'cond':
        $periodo_cond = get_get( 'cond', 'int' );
        if ( empty( $periodo_cond ) ) {
            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador do periodo de condenação em branco ( $sit_pag ).\n\n Página: $pag";
            salvaLog($mensagem);
            echo msg_js( 'FALHA!', 1 );
            exit;
        }

        $desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's pela condenação';
        $clausula_soma_cond = '( IFNULL( SUM( `gra_p_ano` ), 0 ) + IFNULL( SUM( `gra_p_mes` ), 0 )/12 + IFNULL( SUM( `gra_p_dia` ), 0 )/30 )';

        switch( $periodo_cond ) {
            default:
            case 1: //ATÉ 4 ANOS
                $clausula_having = " $clausula_soma_cond > 0 AND $clausula_soma_cond <= 4";
                $motivo = SICOP_DET_DESC_U . 'S CONDENADOS ATÉ 4 ANOS ';
                $desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's pela condenação';
                break;

            case 2: // DE 4 A 8 ANOS
                $clausula_having = " 4 < $clausula_soma_cond AND $clausula_soma_cond <= 8 ";
                $motivo = SICOP_DET_DESC_U . 'S CONDENADOS DE 4 À 8 ANOS';
                $desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's pela condenação';
                break;

            case 3: // DE 8 A 15 ANOS
                $clausula_having = " 8 < $clausula_soma_cond AND $clausula_soma_cond <= 15 ";
                $motivo = SICOP_DET_DESC_U . 'S CONDENADOS DE 8 À 15 ANOS';
                $desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's pela condenação';
                break;

            case 4: // DE 15 A 20 ANOS
                $clausula_having = " 15 < $clausula_soma_cond AND $clausula_soma_cond <= 20 ";
                $motivo = SICOP_DET_DESC_U . 'S CONDENADOS DE 15 À 20 ANOS';
                $desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's pela condenação';
                break;

            case 5: // DE 20 A 30 ANOS
                $clausula_having = " 20 < $clausula_soma_cond AND $clausula_soma_cond <= 30 ";
                $motivo = SICOP_DET_DESC_U . 'S CONDENADOS DE 20 À 30 ANOS';
                $desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's pela condenação';
                break;

            case 6: // DE 30 A 50 ANOS
                $clausula_having = " 30 < $clausula_soma_cond AND $clausula_soma_cond <= 50 ";
                $motivo = SICOP_DET_DESC_U . 'S CONDENADOS DE 30 À 50 ANOS';
                $desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's pela condenação';
                break;

            case 7: // DE 50 A 100 ANOS
                $clausula_having = " 50 < $clausula_soma_cond AND $clausula_soma_cond <= 100 ";
                $motivo = SICOP_DET_DESC_U . 'S CONDENADOS DE 50 À 100 ANOS';
                $desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's pela condenação';
                break;

            case 8: // MAIS DE 100 ANOS
                $clausula_having = " $clausula_soma_cond > 100 ";
                $motivo = SICOP_DET_DESC_U . 'S CONDENADOS A MAIS DE 100 ANOS';
                $desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's pela condenação';
                break;

            }

        $q_cond = "SELECT
                     `detentos`.`iddetento`
                   FROM
                     `detentos`
                     LEFT JOIN `grade` ON `detentos`.`iddetento` = `grade`.`cod_detento`
                     LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                     LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                   WHERE
                     `gra_campo_x` = FALSE AND
                     `gra_preso` = true AND
                     `detentos`.`cod_sit_proc` != 1 AND
                     ( ( ISNULL( `mov_det_out`.`cod_tipo_mov` ) OR `mov_det_out`.`cod_tipo_mov` = 4 OR `mov_det_out`.`cod_tipo_mov` = 6 )
                       AND
                       (`mov_det_in`.`cod_tipo_mov` = 1 OR `mov_det_in`.`cod_tipo_mov` = 3 ) )
                   GROUP BY
                     `detentos`.`iddetento`
                   HAVING
                     $clausula_having";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $q_cond = $model->query( $q_cond );

        // fechando a conexao
        $model->closeConnection();

        if( !$q_cond ) {

            echo msg_js( 'FALHA!!!', 1 );
            exit;

        }

        $iddet_cond = '';
        while( $d_cond = $q_cond->fetch_assoc() ) {
            $iddet_cond .= $d_cond['iddetento'] . ',';
        }

        $iddet_cond = substr( $iddet_cond, 0, -1 );

        $where = " AND `detentos`.`iddetento` IN( $iddet_cond )";

        break;

    case 'idade':
        $idade_cond = get_get( 'idade', 'int' );
        if ( empty( $idade_cond ) ) {
            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador do periodo de condenação em branco ( $sit_pag ).\n\n Página: $pag";
            salvaLog($mensagem);
            echo msg_js( 'FALHA!', 1 );
            exit;
        }

        $clausula_soma_idade = 'FLOOR( DATEDIFF( CURDATE(), `detentos`.`nasc_det` ) / 365.25 )';
        $clausula_idade = '';

        switch( $idade_cond ) {
            default:
            case 1: //ATÉ 24 ANOS
                $clausula_idade = "$clausula_soma_idade <= 24";
                $motivo = SICOP_DET_DESC_U . 'S COM IDADE ATÉ 24 ANOS ';
                $desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's com idade até 24 anos';
                break;
            case 2: // DE 25 A 29 ANOS
                $clausula_idade = "$clausula_soma_idade BETWEEN 25 AND 29";
                $motivo = SICOP_DET_DESC_U . 'S COM IDADE ENTRE 25 E 29 ANOS ';
                $desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's com idade entre 25 e 29 anos';
                break;
            case 3: // DE 30 A 34 ANOS
                $clausula_idade = "$clausula_soma_idade BETWEEN 30 AND 34";
                $motivo = SICOP_DET_DESC_U . 'S COM IDADE ENTRE 30 E 34 ANOS ';
                $desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's com idade entre 30 e 34 anos';
                break;
            case 4: // DE 35 A 45 ANOS
                $clausula_idade = "$clausula_soma_idade BETWEEN 35 AND 45";
                $motivo = SICOP_DET_DESC_U . 'S COM IDADE ENTRE 35 E 45 ANOS ';
                $desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's com idade entre 35 e 45 anos';
                break;
            case 5: // DE 46 A 60 ANOS
                $clausula_idade = "$clausula_soma_idade BETWEEN 46 AND 60";
                $motivo = SICOP_DET_DESC_U . 'S COM IDADE ENTRE 46 E 60 ANOS ';
                $desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's com idade entre 46 e 60 anos';
                break;
            case 6: // MAIS DE 60 ANOS
                $clausula_idade = "$clausula_soma_idade > 60";
                $motivo = SICOP_DET_DESC_U . 'S COM IDADE MAIOR QUE 60 ANOS ';
                $desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's com idade mais de 60 anos';
                break;

            }

        $where = " AND $clausula_idade";

        break;

    case 'pidade':
        $motivo = SICOP_DET_DESC_U . 'S COM PENDÊNCIA DE IDADE';
        $desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's com pendência de idade';
        $where = " AND ISNULL( `detentos`.`nasc_det` )";
        break;

    case 'proced':
        $param_busca = get_get( 'idproced', 'int' );
        if ( empty( $param_busca ) ) {
            $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n Identificador da unidade em branco ( $sit_pag ).\n\n Página: $pag";
            salvaLog($mensagem);
            echo msg_js( 'FALHA!', 1 );
            exit;
        }

        $q_unid = "SELECT `unidades` FROM `unidades` WHERE `idunidades` = $param_busca LIMIT 1";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $unid = $model->fetchOne( $q_unid );

        // fechando a conexao
        $model->closeConnection();

        if( $unid === false ) {

            echo msg_js( 'FALHA!!!', 1 );
            exit;

        }

        $motivo = SICOP_DET_DESC_U . 'S PROCEDENTES DO(A) ' . $unid;
        $desc_pag = 'INFOPEN - ' . SICOP_DET_DESC_L . 's pela procedência';
        $where = " AND `unidades_in`.`idunidades` = $param_busca";
        break;

}

if ( empty( $tipo_infop ) ) {
    echo msg_js( '', 1 );
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
    case 'raioa':
        $ordbusca = "`detentos`.`cod_cela` ASC, `detentos`.`nome_det` ASC";
        break;
    case 'raiod':
        $ordbusca = "`detentos`.`cod_cela` DESC, `detentos`.`nome_det` ASC";
        break;
}

$query = "SELECT
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
            $join
            LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
            LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
            LEFT JOIN `unidades` `unidades_in` ON `mov_det_in`.`cod_local_mov` = `unidades_in`.`idunidades`
            LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
            LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
            LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
          WHERE
            ( ( ISNULL( `mov_det_out`.`cod_tipo_mov` ) OR `mov_det_out`.`cod_tipo_mov` = 4 OR `mov_det_out`.`cod_tipo_mov` = 6 )
              AND
             ( `mov_det_in`.`cod_tipo_mov` = 1 OR `mov_det_in`.`cod_tipo_mov` = 3 ) )
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

if( !$query ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}


$cont = $query->num_rows;

if ( $cont == 1 ) { //se o número de ocorrências for igual a 1 vai direto para à página do detento, e finaliza com o exit
    $d_det = $query->fetch_assoc();
    header( 'Location: ' . SICOP_ABS_PATH . 'detento/detalhesdet.php?iddet=' . $d_det['iddetento'] );
    exit;
}

$querytime = $model->getQueryTime();

parse_str( $_SERVER[ 'QUERY_STRING' ], $q_string );

if ( isset( $q_string['op'] ) ) {
    unset( $q_string['op'] );
}

$mensagem = "Acesso à página: $pag";
salvaLog($mensagem);

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 5 );
$trail->output();
?>

            <p class="descript_page"><?php echo $motivo; ?></p>

            <?php
            if ( empty( $cont ) or $cont < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem

                echo '<p class="p_q_no_result">Não foi encontrado nenhuma ocorrência.</p>';

            } else {

                include 'lista_busca.php';
            }

            include 'footer.php';
            ?>