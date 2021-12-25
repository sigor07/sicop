<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag      = link_pag();
$tipo     = '';
$ordpor   = '';
$q_string = '';

$n_rol   = get_session( 'n_rol', 'int' );
$n_rol_n = 2;

if ( $n_rol < $n_rol_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = 'PESQUISAR VISITANTES';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$proced      = get_get( 'proced', 'int' );
$reg_entrada = false;
$reg_saida   = false;
if ( !empty( $proced ) ){

    if ( $n_rol < 3 ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']     = 'perm';
        $msg['entre_ch'] = 'REGISTRO DE ENTRADA/SAÍDA DE VISITANTES';
        get_msg( $msg, 1 );

        require 'cab_simp.php';
        echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

        exit;

    }

    if ( $proced == 1 ) {
        $reg_entrada = true;
    } else {
        $reg_saida = true;
    }

}

$mot_pag     = '';
$link_proced = '';
if ( $reg_entrada ) {
    $mot_pag     = 'PARA REGISTRO DE ENTRADA';
    $link_proced = '&proced=1';
}

if ( $reg_saida ) {
    $mot_pag     = 'PARA REGISTRO DE SAÍDA';
    $link_proced = '&proced=2';
}

$valorbusca    = '';
$valorbusca_sf = '';
$campo_rg      = '';
$tipo_fon      = '';

if( !empty( $_GET['busca'] ) ) {

    $where         = '';
    $valorbusca    = get_get( 'campobusca', 'busca' );
    $valorbusca_sf = $_GET['campobusca'];
    $tipo_fon      = get_get( 'tipo_fon', 'int' );

//    if ( !empty ( $valorbusca ) ) {
//
//        $clausula = "( `visitas`.`nome_visit` LIKE '%$valorbusca%' OR `visitas`.`idvisita` LIKE '$valorbusca' )";
//
//        if ( !empty( $where ) ) {
//            $where .= " AND $clausula";
//        } else {
//            $where = "WHERE $clausula";
//        }
//
//    }

    if ( !empty( $valorbusca ) ) {

        if ( $tipo_fon == 1 ) {

            $where = "WHERE ( `visitas`.`nome_visit` LIKE '%$valorbusca%' OR `visitas`.`idvisita` LIKE '$valorbusca' )";

        } else {

            $valorbusca = preg_replace( '/\s?\b\w{1,2}\b/', null, $valorbusca ); // remover palavras com 2 letras ou menos

            if ( empty( $valorbusca ) ) {
                echo msg_js( '', 1 );
                exit;
            }

            $arr_busca = explode( ' ', $valorbusca );

            $where = 'WHERE (';
            foreach( $arr_busca as $indice => $valor ) {
                if ( $valor == NULL ) continue;
                $where .= " `visitas`.`nome_visit` LIKE '%$valor%' AND";
            }

        }

        if ( $tipo_fon == 2 ) {

            if ( !empty( $where ) ) {
                $where = substr( $where, 0, -3 ); //remover o ultimo 'AND'
                $where .= " OR `visitas`.`idvisita` LIKE '$valorbusca' ";
            }

            $where .= ' )';

        }

    }

    $campo_rg = get_get( 'campo_rg', 'string' );

    if ( !empty ( $campo_rg ) ) {

        $clausula = "( `visitas`.`rg_visit` LIKE '%$campo_rg%' )";

        if ( !empty( $where ) ) {
            $where .= " AND $clausula";
        } else {
            $where = "WHERE $clausula";
        }

    }

    $ordpor = 'nomeda';
    if ( !empty( $_GET['op'] ) ) {
        $ordpor = get_get( 'op', 'busca' );
    }

    switch( $ordpor ) {
        default:
        case 'nomeda':
            $ordbusca = "`detentos`.`nome_det` ASC, `visitas`.`nome_visit` ASC";
            break;
        case 'nomedd':
            $ordbusca = "`detentos`.`nome_det` DESC, `visitas`.`nome_visit` ASC";
            break;
        case 'matra':
            $ordbusca = "`detentos`.`matricula` ASC, `visitas`.`nome_visit` ASC";
            break;
        case 'matrd':
            $ordbusca = "`detentos`.`matricula` DESC, `visitas`.`nome_visit` ASC";
            break;
        case 'nomeva':
            $ordbusca = "`visitas`.`nome_visit` ASC, `detentos`.`nome_det` ASC";
            break;
        case 'nomevd':
            $ordbusca = "`visitas`.`nome_visit` DESC, `detentos`.`nome_det` ASC";
            break;
        case 'rgva':
            $ordbusca = "`visitas`.`rg_visit` ASC";
            break;
        case 'rgvd':
            $ordbusca = "`visitas`.`rg_visit` DESC";
            break;
        case 'ra':
            $ordbusca = "`raio`.`raio` ASC, `cela`.`cela` ASC, `detentos`.`nome_det` ASC";
            break;
        case 'rd':
            $ordbusca = "`raio`.`raio` DESC, `cela`.`cela` ASC, `detentos`.`nome_det` ASC";
            break;
    }

    $query = "SELECT
                `visitas`.`idvisita`,
                `visitas`.`nome_visit`,
                `visitas`.`rg_visit`,
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
                visitas
                LEFT JOIN `detentos` ON `visitas`.`cod_detento` = `detentos`.`iddetento`
                LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
                LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
                LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
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

        // gerar a mensagem q será salva no log
        $msg = sysmsg::create_msg();
        $msg->set_msg_type( SM_TYPE_ERR );
        $msg->add_quebras( 1 );
        $msg->set_msg_pre_def( SM_QUERY_FAIL );
        $msg->add_parenteses( "PESQUISAR $motivo" );
        $msg->add_quebras( 2 );
        $msg->set_msg( $msg_err_mysql );
        $msg->get_msg();

        echo msg_js( 'FALHA!', 1 );
        exit;

    }

    $db->closeConnection();

    $cont = $query->num_rows;

    /* PARTE PARA PEGAR OS VALORES INSERIDOS NO FORMULÁRIO */
    $valor_busca = valor_user( $_GET );

    // gerar a mensagem q será salva no log
    $msg = sysmsg::create_msg();
    $msg->add_chaves( "BUSCA DE VISITANTES EFETUADA", 0, 1 );
    $msg->set_msg( 'Busca de visitas efetuada' );
    $msg->add_quebras( 2 );
    $msg->set_msg( $valor_busca );
    $msg->add_quebras( 2 );
    $msg->set_msg( "Quantidade de registros retornados: $cont" );
    $msg->get_msg();

    if ( $cont == 1 ) { //se o número de ocorrências for igual a 1 vai direto para à página do detento, e finaliza com o exit
        $dados = $query->fetch_assoc();
        header( 'Location: detalvisit.php?idvisit=' . $dados['idvisita'] . $link_proced );
        exit;
    }

    parse_str( $_SERVER[ 'QUERY_STRING' ], $q_string );

    if ( isset( $q_string['op'] ) ) {
        unset( $q_string['op'] );
    }

}

$desc_pag = 'Pesquisar visitantes';

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();

?>

            <p class="descript_page"> PESQUISAR VISITANTES <?php echo $mot_pag;?></p>

            <form action="buscavisit.php" method="get" name="buscavisit" id="buscavisit" onSubmit="upperMe(campobusca); remacc(campobusca);">

                <table class="busca_form"><!--remacc(campobusca);-->
                    <tr>
                        <td class="bf_legend">NOME ou ID:</td>
                        <td class="bf_field"><input name="campobusca" type="text" class="CaixaTexto" id="campobusca" onkeypress="return blockChars(event, 3);" value="<?php echo $valorbusca_sf ?>" size="50" /></td>
                    </tr>
                    <tr>
                        <td class="bf_det_legend">Pesquisa fonética:</td>
                        <td class="bf_det_field"><input name="tipo_fon" type="radio" id="tipo_fon_0" value="1" <?php echo ( ( !empty($_GET ) and $tipo_fon == 1 ) or empty( $tipo_fon ) ) ? 'checked="checked"' : ''; ?> /> a frase exata &nbsp; <input name="tipo_fon" type="radio" id="tipo_fon_1" value="2" <?php echo ( !empty( $_GET ) and $tipo_fon == 2 ) ? 'checked="checked"' : ''; ?> /> que contenha as palavras </td>
                    </tr>
                    <tr>
                        <td class="bf_legend">R.G.:</td>
                        <td class="bf_field"><input name="campo_rg" type="text" class="CaixaTexto" id="campo_rg" onkeypress="return blockChars(event, 5);" value="<?php echo $campo_rg; ?>" size="30" /></td>
                    </tr>
                </table>

<!--                <p class="table_leg">Digite o NOME ou o IDENTIFICADOR do vistante:</p>

                <div class="form_one_field">
                    <input name="campobusca" type="text" class="CaixaTexto" id="campobusca" onkeypress="return blockChars(event, 3);" value="<?php echo $valorbusca ?>" size="50" />
                </div>-->

                <?php if ( $proced == 1 ) { ?>
                <p class="link_common" style="margin-top: 5px;"><a href="<?php echo SICOP_ABS_PATH ?>buscadet.php?proced=regrol">Pesquisar pel<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L; ?></a></p>
                <?php } ?>

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="busca" id="busca" value="Buscar" />
                </div>

                <input type="hidden" name="busca" id="busca" value="busca" />
                <input type="hidden" name="proced" id="proced" value="<?php echo $proced; ?>" />

            </form>

            <script type="text/javascript">
                document.getElementById("campobusca").focus();
                document.getElementById("campobusca").select();
            </script>

            <?php

                if ( empty( $_GET['busca'] ) ) {
                    include 'footer.php';
                    exit;
                }


                if ( empty( $cont ) or $cont < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                    echo '<p class="p_q_no_result">Não foi encontrado nenhuma ocorrência.</p>';
                    include 'footer.php';
                    exit;
                }

            ?>

            <p class="p_q_info">Essa consulta retornou <?php echo $cont ?> registros (<?php echo round($querytime, 2) ?> seg). <a href="buscavisit.php<?php echo '?proced=' . $proced; ?>">Nova consulta</a></p>

            <table class="lista_busca">
                <tr class="cab">
                    <th class="num_od">N</th>
                    <th class="visit_nome_busca">Visitante
                            <?php echo link_ord_asc( $ordpor, 'nomev', $q_string, 'nome do visitante' ) ?>
                            <?php echo link_ord_desc( $ordpor, 'nomev', $q_string, 'nome do visitante' ) ?>
                    </th>
                    <th class="visit_rg">R.G.
                            <?php echo link_ord_asc( $ordpor, 'rgv', $q_string, 'rg do visitante' ) ?>
                            <?php echo link_ord_desc( $ordpor, 'rgv', $q_string, 'rg do visitante' ) ?>
                    </th>
                    <th class="nome_det_small"><?php echo SICOP_DET_DESC_FU; ?>
                            <?php echo link_ord_asc( $ordpor, 'nomed', $q_string, 'nome d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L ) ?>
                            <?php echo link_ord_desc( $ordpor, 'nomed', $q_string, 'nome d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L ) ?>
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

                    while ( $dados = $query->fetch_assoc() ) {

                        $idv    = $dados['idvisita'];
                        $link_v = "detalvisit.php?idvisit=$idv";

                        if ( $reg_entrada ) {
                            $link_v .= '&proced=1';
                        }

                        if ( $reg_saida ) {
                            $link_v .= '&proced=2';
                        }

                        $tipo_mov_in  = $dados['tipo_mov_in'];
                        $tipo_mov_out = $dados['tipo_mov_out'];
                        $iddestino    = $dados['iddestino'];

                        $det = manipula_sit_det_b( $tipo_mov_in, $tipo_mov_out, $iddestino );

                        ?>

                <tr class="even">
                    <td class="num_od"><?php echo $i++; ?></td>
                    <td class="visit_nome_busca <?php if ( stripos( $ordpor, 'nomev' ) !== false ) echo 'ord';?>"><a href="detalvisit.php?idvisit=<?php echo $dados['idvisita'] . $link_proced;?>"><?php echo highlight($valorbusca, $dados['nome_visit']);?></a></td>
                    <td class="visit_rg <?php if ( stripos( $ordpor, 'rgv' ) !== false ) echo 'ord';?>"><?php echo !empty( $dados['rg_visit'] ) ? $dados['rg_visit'] : '&nbsp;'; ?></td>
                    <td class="nome_det_small <?php if ( stripos( $ordpor, 'nomed' ) !== false ) echo 'ord';?>"><a href="rol_visit.php?iddet=<?php echo $dados['iddetento'];?>" title="Pai: <?php echo $dados['pai_det'];?>&#13;Mãe: <?php echo $dados['mae_det'];?>&#13;Situação atual: <?php echo $det['sitat']; ?>"><?php echo $dados['nome_det'];?></a></td>
                    <td class="matr_det <?php echo $det['css_class']; ?> <?php if ( stripos( $ordpor, 'matr' ) !== false ) echo 'ord';?>"><?php if ( !empty( $dados['matricula'] ) ) echo formata_num( $dados['matricula'] ); else echo '&nbsp;'; ?></td>
                    <td class="raio_det <?php echo $det['css_class']; ?> <?php if ( stripos( $ordpor, 'raio' ) !== false ) echo 'ord';?>"><?php echo !empty( $dados['raio'] ) ? $dados['raio'] : '&nbsp;'; ?></td>
                    <td class="cela_det <?php echo $det['css_class']; ?> <?php if ( stripos( $ordpor, 'raio' ) !== false ) echo 'ord';?>"><?php echo !empty( $dados['cela'] ) ? $dados['cela'] : '&nbsp;'; ?></td>
                    <?php } // fim do while ?>
                </tr>
            </table>

<?php include 'footer.php'; ?>