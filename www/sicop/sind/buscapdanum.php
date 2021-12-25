<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';
$q_string = '';
$ordpor = '';

$n_sind   = get_session( 'n_sind', 'int' );
$n_sind_n = 2;

$motivo_pag = 'BUSCAR PDAs PELO NÚMERO';

if ($n_sind < $n_sind_n) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}


$numpda  = get_get( 'numpda', 'int' );
$anopda  = get_get( 'anopda', 'int' );

$numpda_ = get_get( 'numpda' );
$anopda_ = get_get( 'anopda' );

$where = '';

if ( !empty( $numpda ) ){
    $where .= "WHERE `sindicancias`.`num_pda` LIKE '%$numpda%'";
}

if ( !empty( $anopda ) ){
    if ( !empty( $where ) ){
        $where .= " AND `sindicancias`.`ano_pda` = '$anopda'";
    } else {
        $where .= "WHERE `sindicancias`.`ano_pda` = '$anopda'";
    }
}


if ( isset( $_GET['numpda'] ) || isset( $_GET['anopda'] ) ) {

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
        case 'execa':
            $ordbusca = "`detentos`.`execucao` ASC";
            break;
        case 'execd':
            $ordbusca = "`detentos`.`execucao` DESC";
            break;
        case 'pdaa':
            $ordbusca = "`sindicancias`.`ano_pda` ASC, `sindicancias`.`num_pda` ASC, `detentos`.`nome_det` ASC";
            break;
        case 'pdad':
            $ordbusca = "`sindicancias`.`ano_pda` DESC, `sindicancias`.`num_pda` DESC, `detentos`.`nome_det` ASC";
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
                `detentos`.`execucao`,
                `detentos`.`pai_det`,
                `detentos`.`mae_det`,
                `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
                `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
                `unidades_out`.`idunidades` AS iddestino,
                `sindicancias`.`idsind`,
                `sindicancias`.`num_pda`,
                `sindicancias`.`ano_pda`,
                `sindicancias`.`local_pda`,
                `sindicancias`.`sit_pda`,
                `sindicancias`.`data_reabilit`,
                `cela`.`cela`,
                `raio`.`raio`
              FROM
                `sindicancias`
                LEFT JOIN `detentos` ON `sindicancias`.`cod_detento` = `detentos`.`iddetento`
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

    $valor_busca = valor_user($_GET);

    $mensagem = "[ BUSCA EFETUADA ]\nBusca de PDA pelo número efetuada\n\n $valor_busca \n\n Página: $pag";
    salvaLog($mensagem);


}

parse_str( $_SERVER['QUERY_STRING'], $q_string );

if ( isset( $q_string['op'] ) ) {
    unset( $q_string['op'] );
}

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( 'Buscar PDA pelo número/ano', $pag_atual, 3 );
$trail->output();
?>


            <p class="descript_page">PESQUISAR PDAs PELO NÚMERO</p>

            <form action="buscapdanum.php" method="get" name="buscapdanum" id="buscapdanum" >

                <p class="table_leg">Digite o NÚMERO/ANO do PDA:</p>

                <div class="form_one_field">
                    <input name="numpda" type="text" class="CaixaTexto" id="numpda" onkeypress="return blockChars(event, 2);" value="<?php echo $numpda_ ?>" size="5" maxlength="4" />/<input name="anopda" type="text" class="CaixaTexto" id="anopda" onkeypress="return blockChars(event, 2);" value="<?php echo $anopda_ ?>" size="5" maxlength="4" />
                </div>

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="busca" id="busca" value="Buscar" />
                </div>

            </form>
            <script type="text/javascript"> id("numpda").focus(); </script>

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

            <p class="p_q_info">Essa consulta retornou <?php echo $cont ?> registros (<?php echo round($querytime, 2) ?> seg). <a href="buscapdanum.php">Nova consulta</a></p>

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

                    <th class="exec_det">Execução
                        <?php echo link_ord_asc( $ordpor, 'exec', $q_string, 'execução' ) ?>
                        <?php echo link_ord_desc( $ordpor, 'exec', $q_string, 'execução' ) ?>
                    </th>
                    <th class="raio_det"><?php echo SICOP_RAIO ?>
                        <?php echo link_ord_asc( $ordpor, 'raio', $q_string, mb_strtolower( SICOP_RAIO ) . '/' . mb_strtolower( SICOP_CELA ) ) ?>
                        <?php echo link_ord_desc( $ordpor, 'raio', $q_string, mb_strtolower( SICOP_RAIO ) . '/' . mb_strtolower( SICOP_CELA ) ) ?>
                    </th>
                    <th class="cela_det"><?php echo SICOP_CELA ?></th>
                    <th class="desc_pda">Número do PDA
                        <?php echo link_ord_asc( $ordpor, 'pda', $q_string, 'número do PDA' ) ?>
                        <?php echo link_ord_desc( $ordpor, 'pda', $q_string, 'número do PDA' ) ?>
                    </th>
                </tr>
                <?php
                    $i = 1;

                    while ( $dados = $query->fetch_assoc() ) {

                        $tipo_mov_in  = $dados['tipo_mov_in'];
                        $tipo_mov_out = $dados['tipo_mov_out'];
                        $iddestino    = $dados['iddestino'];

                        $det = manipula_sit_det_b( $tipo_mov_in, $tipo_mov_out, $iddestino );

                        $numpda = format_num_pda( $dados['num_pda'], $dados['ano_pda'], $dados['local_pda'] );

                        $corfonts = muda_cor_pda( $dados['data_reabilit'], $dados['sit_pda'] );

                ?>

                <tr class="even">
                    <td class="num_od"><?php echo $i++; ?></td>
                    <?php if ( empty( $dados['iddetento'] ) ) { ?>
                    <td class="nome_det <?php if ( stripos( $ordpor, 'nome' ) !== false ) echo ' ord';?>">AUTORIA DESCONHECIDA</td>
                    <?php } else { ?>
                    <td class="nome_det <?php if ( stripos( $ordpor, 'nome' ) !== false ) echo ' ord';?>" title="Pai: <?php echo $dados['pai_det'];?>&#13;Mãe: <?php echo $dados['mae_det'];?>&#13;Situação atual: <?php echo $det['sitat'];?>"><a href="<?php echo SICOP_ABS_PATH ?>detento/detalhesdet.php?iddet=<?php echo $dados['iddetento'];?>" > <?php echo $dados['nome_det'];?></a></td>
                    <?php } ?>
                    <td class="matr_det <?php echo $det['css_class']; if ( stripos( $ordpor, 'matr' ) !== false ) echo ' ord';?>"><?php if ( !empty( $dados['matricula'] ) ) echo formata_num( $dados['matricula'] ) ?></td>
                    <td class="exec_det <?php echo $det['css_class']; if ( stripos( $ordpor, 'exec' ) !== false ) echo ' ord';?>"><?php echo !empty( $dados['execucao'] ) ? number_format( $dados['execucao'], 0, '', '.' ) : 'N/C' ?></td>
                    <td class="raio_det <?php echo $det['css_class']; if ( stripos( $ordpor, 'raio' ) !== false ) echo ' ord';?>"><?php echo $dados['raio'];?></td>
                    <td class="cela_det <?php echo $det['css_class']; if ( stripos( $ordpor, 'raio' ) !== false ) echo ' ord';?>"><?php echo $dados['cela'];?></td>
                    <td class="desc_pda <?php if ( stripos( $ordpor, 'pda' ) !== false ) echo ' ord';?>"><font color="<?php echo $corfonts;?>"><?php echo $numpda;?></font> <a href="detalpda.php?idsind=<?php echo $dados['idsind']?>" title="Ver detalhes deste PDA"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_view.png" alt="" class="icon_view" /></a></td>
                </tr>

                <?php } // fim do while ?>

            </table>

<?php include 'footer.php'; ?>