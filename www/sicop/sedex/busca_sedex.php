<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';
$cont = '';
$ordpor = '';
$q_string = '';

$n_sedex   = get_session( 'n_sedex', 'int' );
$n_sedex_n = 2;

if ( $n_sedex < $n_sedex_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'BUSCA DE SEDEX';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$valorbusca    = '';
$valorbusca_sf = '';
$data_ini_sf   = '';
$data_fim_sf   = '';

if( !empty( $_GET['busca'] ) ) {

    if ( !empty( $_GET['campobusca'] ) ) {

        $valorbusca = get_get( 'campobusca', 'busca' );
        $valorbusca_sf =  get_get( 'campobusca' );

    }

    $where = "`sedex`.`cod_sedex` LIKE '$valorbusca%'";

    $data_ini_sf = get_get( 'data_ini' );
    $data_ini    = get_get( 'data_ini', 'busca' );

    $data_fim_sf = get_get( 'data_fim' );
    $data_fim    = get_get( 'data_fim', 'busca' );

    $clausula_data = '';

    if ( !empty( $data_ini ) or !empty( $data_fim ) ){

        if ( !empty( $data_ini ) and  !empty( $data_fim ) ){

            $clausula_data = " AND ( DATE( `sedex`.`data_add` ) BETWEEN STR_TO_DATE( '$data_ini', '%d/%m/%Y' ) AND STR_TO_DATE( '$data_fim', '%d/%m/%Y' ) )";

        } else {

            $data_f = !empty( $data_ini ) ? $data_ini : $data_fim;

            $clausula_data = " AND DATE( `sedex`.`data_add` ) = STR_TO_DATE( '$data_f', '%d/%m/%Y' ) ";

        }

    }

    if ( !empty( $clausula_data ) ) {
        $where .= $clausula_data;
    }

    $ordpor = 'nomea';

    if ( !empty( $_GET['op'] ) ) {
        $ordpor = get_get( 'op', 'busca' );
    }

    switch( $ordpor ) {
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
        case 'raioa':
            $ordbusca = "`detentos`.`cod_cela` ASC, `detentos`.`nome_det` ASC";
            break;
        case 'raiod':
            $ordbusca = "`detentos`.`cod_cela` DESC, `detentos`.`nome_det` ASC";
            break;
        case 'coda':
            $ordbusca = "`sedex`.`cod_sedex` ASC, `detentos`.`nome_det` ASC";
            break;
        case 'codd':
            $ordbusca = "`sedex`.`cod_sedex` DESC, `detentos`.`nome_det` ASC";
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
                 `sedex`.`idsedex`,
                 `sedex`.`cod_sedex`,
                 DATE_FORMAT( `sedex`.`data_add`, '%d/%m/%Y' ) AS data_sedex,
                 `cela`.`cela`,
                 `raio`.`raio`
              FROM
                 `detentos`
                 INNER JOIN `sedex` ON `sedex`.`cod_detento` = `detentos`.`iddetento`
                 LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                 LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                 LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
                 LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
                 LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
              WHERE
                 $where
              ORDER BY
                 $ordbusca";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query = $model->query( $query );

    // fechando a conexao
    $model->closeConnection();

    $cont = $query->num_rows;

    if( $cont == 1 ) { //se o número de ocorrências for igual a 1 vai direto para à página do detento, e finaliza com o exit
        $d_det = $query->fetch_assoc();
        header( 'Location: detalsedex.php?ids=' . $d_det['idsedex'] );
        exit;
    }

    $querytime = $model->getQueryTime();

    $mensagem = "Acesso à página $pag";
    salvaLog( $mensagem );

    parse_str( $_SERVER['QUERY_STRING'], $q_string );

    if ( isset( $q_string['op'] ) ) {
        unset( $q_string['op'] );
    }
}

$desc_pag = 'Listar sedex';

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

$trail = new Breadcrumb();
$trail->add($desc_pag, $pag_atual, 3);
$trail->output();
?>

            <p class="descript_page">BUSCAR SEDEX PELO CÓDIGO</p>

            <form action="busca_sedex.php" method="get" name="busca_sedex" id="busca_sedex" onSubmit="upperMe(campobusca); ">

                <table class="busca_form"><!--remacc(campobusca);-->

                    <tr>
                        <td class="bf_legend">Código de Rastreamento:</td>
                        <td class="bf_field"><input name="campobusca" type="text" class="CaixaTexto" id="campobusca" onkeypress="return blockChars(event, 4);" value="<?php echo $valorbusca_sf ?>" size="50" /></td>
                    </tr>

                    <tr>
                        <td class="bf_legend">Data de entrada:</td>
                        <td class="bf_field">
                            <input name="data_ini" type="text" class="CaixaTexto" id="data_ini" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_ini_sf ?>" size="12" maxlength="10" /> e
                            <input name="data_fim" type="text" class="CaixaTexto" id="data_fim" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_fim_sf ?>" size="12" maxlength="10" />
                        </td>
                    </tr>

                </table>

                <input name="busca" type="hidden" id="busca" value="busca" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="buscar" id="buscar" value="Buscar" />
                </div>

            </form>

            <script type="text/javascript">

                $(function() {
                    $( "#campobusca" ).focus();
                    $( "#data_ini, #data_fim" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });
                });

            </script>

            <?php

                if ( empty( $_GET['busca'] ) ) {
                    include 'footer.php';
                    exit;
                }

                if( empty( $cont ) or $cont < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                    echo '<p class="p_q_no_result">Não foi encontrado nenhuma ocorrência.</p>';
                    include 'footer.php';
                    exit;
                }

            ?>
            <p class="p_q_info">Essa consulta retornou <?php echo $cont ?> registros (<?php echo round( $querytime, 2 ) ?> seg).</p>

            <table class="lista_busca">
                <tr>
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
                    <th class="cod_sedex">Código de rastreamento
                        <?php echo link_ord_asc( $ordpor, 'cod', $q_string, 'código de rastreamento' ) ?>
                        <?php echo link_ord_desc( $ordpor, 'cod', $q_string, 'código de rastreamento' ) ?>
                    </th>
                </tr>
                <?php
                    $i = 1;

                    while( $d_det = $query->fetch_assoc() ) {

                        $tipo_mov_in  = $d_det['tipo_mov_in'];
                        $tipo_mov_out = $d_det['tipo_mov_out'];
                        $iddestino    = $d_det['iddestino'];

                        $det = manipula_sit_det_b( $tipo_mov_in, $tipo_mov_out, $iddestino );

                        ?>
                <tr class="even">
                    <td class="num_od_alt"><?php echo $i++; ?></td>
                    <td class="nome_det<?php if ( stripos( $ordpor, 'nome' ) !== false ) echo 'ord';?>" title="Pai: <?php echo $d_det['pai_det'];?>&#13;Mãe: <?php echo $d_det['mae_det'];?>&#13;Situação atual: <?php echo $det['sitat'];?>"><a href="<?php echo SICOP_ABS_PATH ?>detento/detalhesdet.php?iddet=<?php echo $d_det['iddetento'] /*alphaID($dados['iddetento'])*/;?>" > <?php echo $d_det['nome_det'];?></a></td>
                    <td class="matr_det <?php echo $det['css_class']; if ( stripos( $ordpor, 'matr' ) !== false ) echo ' ord';?>"><?php echo !empty( $d_det['matricula'] ) ? formata_num( $d_det['matricula'] ) : '&nbsp;';?></td>
                    <td class="raio_det <?php echo $det['css_class']; if ( stripos( $ordpor, 'raio' ) !== false ) echo ' ord';?>"><?php echo !empty( $d_det['raio'] ) ? $d_det['raio'] : '&nbsp;'; ?></td>
                    <td class="cela_det <?php echo $det['css_class']; if ( stripos( $ordpor, 'raio' ) !== false ) echo ' ord';?>"><?php echo !empty( $d_det['cela'] ) ? $d_det['cela'] : '&nbsp;'; ?></td>
                    <td class="cod_sedex<?php if ( stripos( $ordpor, 'cod' ) !== false ) echo ' ord';?>" title="Data de entrada: <?php echo $d_det['data_sedex']; ?>"><a href="detalsedex.php?ids=<?php echo $d_det['idsedex'] ;?>" ><?php echo formata_num_sedex ( $d_det['cod_sedex'] );?></a></td>
                </tr>
                <?php } // fim do while ?>
            </table>

<?php include 'footer.php'; ?>

