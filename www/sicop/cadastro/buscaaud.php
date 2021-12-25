<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';


$n_cadastro   = get_session( 'n_cadastro', 'int' );
$imp_cadastro = get_session( 'imp_cadastro', 'int' );
$n_cad_n      = 2;

if ( $n_cadastro < $n_cad_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'BUSCAR AUDIÊNCIAS';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$n_chefia = get_session( 'n_chefia', 'int' );
$cont     = '';

/*    if (isset($_SESSION['l_id_aud'])){
    unset($_SESSION['l_id_aud']);
}*/

$det          = get_get( 'det', 'busca' );
$cidade_aud   = get_get( 'cidade_aud', 'busca' );
$local_aud    = get_get( 'local_aud', 'busca' );
$data_aud_in  = get_get( 'data_aud_in', 'busca' );
$data_aud_out = get_get( 'data_aud_out', 'busca' );
$data_fut     = get_get( 'data_fut', 'int' );
$sitaud       = get_get( 'sitaud', 'int' );
$tipo_sit     = get_get( 'tipo_sit', 'int' );

$ordpor   = '';
$q_string = '';

$where = '';

if ( isset( $_GET['local_aud'] ) ) {

    if ( !empty( $det ) ){
        $where .= "WHERE ( `detentos`.`nome_det` LIKE '%$det%' OR `detentos`.`matricula` LIKE '$det%')";
    }

    if ( !empty( $cidade_aud ) ){
        if ( !empty( $where ) ){
            $where .= " AND `audiencias`.`cidade_aud` LIKE '%$cidade_aud%'";
        } else {
            $where .= "WHERE `audiencias`.`cidade_aud` LIKE '%$cidade_aud%'";
        }
    }

    if ( !empty( $local_aud ) ){
        if ( !empty( $where ) ){
            $where .= " AND `audiencias`.`local_aud` LIKE '%$local_aud%'";
        } else {
            $where .= "WHERE `audiencias`.`local_aud` LIKE '%$local_aud%'";
        }
    }

    if ( !empty( $data_aud_in ) or !empty( $data_aud_out ) ) {
        if ( !empty( $data_aud_in ) and  !empty( $data_aud_out ) ) {
            if ( !empty( $where ) ) {
                $where .= " AND `audiencias`.`data_aud` BETWEEN STR_TO_DATE('$data_aud_in', '%d/%m/%Y') AND STR_TO_DATE('$data_aud_out', '%d/%m/%Y')";
            } else {
                $where .= "WHERE `audiencias`.`data_aud` BETWEEN STR_TO_DATE('$data_aud_in', '%d/%m/%Y') AND STR_TO_DATE('$data_aud_out', '%d/%m/%Y')";
            }
        } else {
            if ( !empty( $where ) ) {
                $where .= " AND `audiencias`.`data_aud` = IF(STR_TO_DATE('$data_aud_in', '%d/%m/%Y'), STR_TO_DATE('$data_aud_in', '%d/%m/%Y'), STR_TO_DATE('$data_aud_out', '%d/%m/%Y'))";
            } else {
                $where .= "WHERE `audiencias`.`data_aud` = IF(STR_TO_DATE('$data_aud_in', '%d/%m/%Y'), STR_TO_DATE('$data_aud_in', '%d/%m/%Y'), STR_TO_DATE('$data_aud_out', '%d/%m/%Y'))";
            }
        }
    }

    if ( !empty( $data_fut ) ) {
        if ( !empty( $where ) ) {
            $where .= ' AND `audiencias`.`data_aud` >= DATE(NOW())';
        } else {
            $where .= 'WHERE `audiencias`.`data_aud` >= DATE(NOW())';
        }
    }

    if ( !empty( $sitaud ) ) {
        if ( !empty( $where ) ) {
            $where .= " AND `audiencias`.`sit_aud` = $sitaud";
        } else {
            $where .= "WHERE `audiencias`.`sit_aud` = $sitaud";
        }
    }

    $clausula = '';

    if ( !empty( $tipo_sit ) ) {

        $clausula = get_where_det( $tipo_sit );

    }

    if ( !empty( $clausula ) ) {

        if ( !empty( $where ) ) {
            $where .= ' AND ' . $clausula;
        } else {
            $where .= 'WHERE ' . $clausula;
        }

    }

    $ordpor = 'dataa';
    if ( !empty( $_GET['op'] ) ) {
        $ordpor = get_get( 'op', 'busca' );
    }

    switch ( $ordpor ) {

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
        case 'locala':
            $ordbusca = '`audiencias`.`local_aud` ASC, `audiencias`.`cidade_aud` ASC, `audiencias`.`data_aud` ASC, `audiencias`.`hora_aud` ASC';
            break;
        case 'locald':
            $ordbusca = '`audiencias`.`local_aud` DESC, `audiencias`.`cidade_aud` ASC, `audiencias`.`data_aud` ASC, `audiencias`.`hora_aud` ASC';
            break;
        case 'cida':
            $ordbusca = '`audiencias`.`cidade_aud` ASC, `audiencias`.`local_aud` ASC, `audiencias`.`data_aud` ASC, `audiencias`.`hora_aud` ASC';
            break;
        case 'cidd':
            $ordbusca = '`audiencias`.`cidade_aud` DESC, `audiencias`.`local_aud` ASC, `audiencias`.`data_aud` ASC, `audiencias`.`hora_aud` ASC';
            break;
        default:
        case 'dataa':
            $ordbusca = '`audiencias`.`data_aud` ASC, `audiencias`.`cidade_aud` ASC, `audiencias`.`local_aud` ASC, `audiencias`.`hora_aud` ASC';
            break;
        case 'datad':
            $ordbusca = '`audiencias`.`data_aud` DESC, `audiencias`.`cidade_aud` ASC, `audiencias`.`local_aud` ASC, `audiencias`.`hora_aud` DESC';
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
                `audiencias`.`idaudiencia`,
                `audiencias`.`data_aud`,
                DATE_FORMAT ( `audiencias`.`data_aud`, '%d/%m/%Y' ) AS `data_aud_f`,
                `audiencias`.`hora_aud`,
                DATE_FORMAT ( `audiencias`.`hora_aud`, '%H:%i' ) AS `hora_aud_f`,
                `audiencias`.`local_aud`,
                `audiencias`.`cidade_aud`,
                `audiencias`.`sit_aud`,
                `cela`.`cela`,
                `raio`.`raio`
              FROM `detentos`
                INNER JOIN `audiencias` ON `audiencias`.`cod_detento` = `detentos`.`iddetento`
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

    $mensagem = "[ BUSCA EFETUADA ]\nBusca de audiência efetuada\n\n $valor_busca\n\n Página: $pag";
    salvaLog($mensagem);

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

// adicionando o javascript
$cab_js   = array();
$cab_js[] = 'valida.js';
$cab_js[] = 'ajax/ajax_aud_busca.js';
set_cab_js( $cab_js );


$desc_pag = 'Pesquisar audiência';

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>

            <p class="descript_page"> PESQUISAR AUDIÊNCIAS</p>

            <form action="buscaaud.php" method="get" name="buscadet" id="buscadet">

                <table class="busca_form">
                    <tr>
                        <td width="169" align="right">Nome ou matrícula: </td>
                        <td width="296" align="left"><input name="det" type="text" class="CaixaTexto" id="det" onkeypress="return blockChars(event, 4);" value="<?php echo $det ?>" size="50" /></td>
                    </tr>
                    <tr>
                        <td align="right">Data de apresentação: </td>
                        <td align="left"><input name="data_aud_in" type="text" class="CaixaTexto" id="data_aud_in" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_aud_in ?>" size="12" maxlength="10" /> e <input name="data_aud_out" type="text" class="CaixaTexto" id="data_aud_out" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_aud_out ?>" size="12" maxlength="10" /></td>

                    </tr>
                    <tr>
                        <td align="right">Local de apresentação: </td>
                        <td align="left"><input name="local_aud" type="text" class="CaixaTexto" id="local_aud" onkeypress="return blockChars(event, 3);" value="<?php echo $local_aud ?>" size="50" /></td>
                    </tr>
                    <tr>
                        <td align="right">Cidade de apresentação:</td>
                        <td align="left"><input name="cidade_aud" type="text" class="CaixaTexto" id="cidade_aud" onkeypress="return blockChars(event, 3);" value="<?php echo $cidade_aud ?>" size="50" />
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Somente datas futuras:          </td>
                        <td align="left">
                            <?php if ( !empty( $_GET['busca'] ) and empty( $data_fut ) ) { ?>
                            <input name="data_fut" type="checkbox" id="data_fut" value="1" />
                            <?php } else { ?>
                            <input name="data_fut" type="checkbox" id="data_fut" value="1" checked="checked" />
                            <?php } ?></td>
                    </tr>
                    <tr>
                        <td align="right">Situação d<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L; ?>:</td>
                        <td align="left">
                            <select name="tipo_sit" class="CaixaTexto" id="tipo_sit">
                                <option value="" >Todos</option>
                                <?php while ( $d_tipo_sit = $q_tipo_sit->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_tipo_sit['idtipo_sit']; ?>" <?php echo $d_tipo_sit['idtipo_sit'] == $tipo_sit ? 'selected="selected"' : ''; ?>><?php echo $d_tipo_sit['tipo_sit']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Situação da audiência:</td>
                        <td align="left">
                            <input name="sitaud" type="radio" id="sitaud_0" value="11" <?php echo (!empty( $_GET['busca'] ) and $sitaud == '11' ) ? 'checked="checked"' : ''; ?> /> Ativa &nbsp;
                            <input name="sitaud" type="radio" id="sitaud_1" value="12" <?php echo (!empty( $_GET['busca'] ) and $sitaud == '12' ) ? 'checked="checked"' : ''; ?> /> Canc. &nbsp;
                            <input name="sitaud" type="radio" id="sitaud_2" value="13" <?php echo (!empty( $_GET['busca'] ) and $sitaud == '13' ) ? 'checked="checked"' : ''; ?> /> Just. &nbsp;
                            <input name="sitaud" type="radio" id="sitaud_3" value="" <?php echo ( (!empty( $_GET['busca'] ) and $sitaud == '' ) or empty( $_GET['busca'] ) ) ? 'checked="checked"' : ''; ?> /> Indiferente
                        </td>
                    </tr>
                </table>

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="busca" id="busca" value="Buscar" />
                    <input class="form_bt" name="" type="button" onClick="javascript: limpa_campos_aud();" value="Limpar campos" />
                </div>

            </form>

            <script type="text/javascript">

                $(function() {
                    $( "#det" ).focus();
                    $( "#data_aud_in, #data_aud_out" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });
                });

            </script>

            <?php if ( $n_chefia >= 3 or $n_cadastro >= 3 ) { ?>
            <p class="link_common" style="margin-top: 10px;">
                <?php if ( $n_chefia >= 3 or $n_cadastro >= 3 ) { ?><a href="<?php echo SICOP_ABS_PATH; ?>detento/cadastradet.php">Cadastrar detento</a><?php } ?>
                <?php if ( $n_cadastro >= 3 ) { ?><?php if ( $n_chefia >= 3 ) { ?> | <?php } ?><a href="<?php echo SICOP_ABS_PATH; ?>buscadet.php?proced=cadaud">Cadastrar audiência</a><?php } ?>
            </p>
            <?php } ?>

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

            <form action="" method="post" name="aud_print" id="aud_print" onSubmit="return validaprintaud();">

                <table class="lista_busca grid">
                    <thead>
                        <tr>
                            <th class="num_od_sml">N</th>
                            <th class="nome_det_small"><?php echo SICOP_DET_DESC_FU; ?>
                                <?php echo link_ord_asc( $ordpor, 'nome', $q_string, 'nome d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L ) ?>
                                <?php echo link_ord_desc( $ordpor, 'nome', $q_string, 'nome d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L ) ?>
                            </th>
                            <th class="matr_det">Matrícula
                                <?php echo link_ord_asc( $ordpor, 'matr', $q_string, 'matrícula' ) ?>
                                <?php echo link_ord_desc( $ordpor, 'matr', $q_string, 'matrícula' ) ?>
                            </th>
                            <th class="local_aud">Local de apresentação
                                <?php echo link_ord_asc( $ordpor, 'local', $q_string, 'local de apresentação' ) ?>
                                <?php echo link_ord_desc( $ordpor, 'local', $q_string, 'local de apresentação' ) ?>
                            </th>
                            <th class="cidade_aud"> Cidade
                                <?php echo link_ord_asc( $ordpor, 'cid', $q_string, 'cidade de apresentação' ) ?>
                                <?php echo link_ord_desc( $ordpor, 'cid', $q_string, 'cidade de apresentação' ) ?>
                            </th>
                            <th class="data_aud"> Data
                                <?php echo link_ord_asc( $ordpor, 'data', $q_string, 'data de apresentação' ) ?>
                                <?php echo link_ord_desc( $ordpor, 'data', $q_string, 'data de apresentação' ) ?>
                            </th>
                            <th class="hora_aud">Hora</th>
                            <th class="tb_ck">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $i = 1;

                        $classe = 'odd';

                        while ( $d_det = $query->fetch_assoc() ) {

                            $tipo_mov_in = $d_det['tipo_mov_in'];
                            $tipo_mov_out = $d_det['tipo_mov_out'];
                            $iddestino = $d_det['iddestino'];

                            $det = manipula_sit_det_b( $tipo_mov_in, $tipo_mov_out, $iddestino );

                            $aud = trata_sit_aud( $d_det['sit_aud'] );

                            //$classe == 'odd' ? $classe = 'even' : $classe = 'odd';

                            /*            if($classe == 'odd') {
                              $classe = 'even'; // Aqui é a primeira cor
                              } else {
                              $classe = 'odd';
                              } */
                            ?>
                        <tr class="even">
                            <td class="num_od_sml"><?php echo $i++; ?></td>
                            <td class="nome_det_small" title="Pai: <?php echo $d_det['pai_det']; ?>&#13;Mãe: <?php echo $d_det['mae_det']; ?>&#13;<?php echo SICOP_RAIO ?>: <?php echo $d_det['raio']; ?>&#13;<?php echo SICOP_CELA ?>: <?php echo $d_det['cela']; ?>&#13;Situação atual: <?php echo $det['sitat']; ?>" ><a href="<?php echo SICOP_ABS_PATH; ?>detento/detalhesdet.php?iddet=<?php echo $d_det['iddetento']; ?>"> <?php echo $d_det['nome_det']; ?></a></td>
                            <td class="matr_det <?php echo $det['css_class']; ?>"><?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] ); ?></td>
                            <td class="local_aud" title="Situação da audiência: <?php echo $aud['sitaud']; ?>"><a href="detalaud.php?idaud=<?php echo $d_det['idaudiencia']; ?>"><?php echo $d_det['local_aud']; ?></a></td>
                            <td class="cidade_aud <?php echo $aud['css_class']; ?>"><?php echo $d_det['cidade_aud']; ?></td>
                            <td class="data_aud <?php echo $aud['css_class']; ?>"><?php echo $d_det['data_aud_f']; ?></td>
                            <td class="hora_aud <?php echo $aud['css_class']; ?>"><?php echo $d_det['hora_aud_f']; ?></td>
                            <td class="tb_ck"><?php if ( $imp_cadastro >= 1 ) { ?><input name="idaud[]" type="checkbox" class="mark_row" value="<?php echo $d_det['idaudiencia']; ?>" /><?php } ?></td>
                        </tr>
                        <?php } // fim do while ?>
                    </tbody>
                    <?php if ( $imp_cadastro >= 1 ) { ?>
                    <tfoot>
                        <tr>
                            <td  class="tb_ck_leg" colspan="7">todos:</td>
                            <td class="tb_ck"><input type="checkbox" name="todos" value="todos" /></td>
                        </tr>
                    </tfoot>
                    <?php } ?>
                </table>

                <p class="bt_leg">COM MARCADOS</p>

                <div class="form_bts">
                    <input class="form_bt" type="button" id="print_aud" name="imprimir" value="Imprimir" />
                    <input class="form_bt" type="button" id="exp_aud" name="excel" value="Exportar para excel" />
                    <input class="form_bt" type="button" id="exp_aud_tran" name="extran" value="Exportar para tabela do trânsito" />
                </div>

            </form>

<?php include 'footer.php'; ?>