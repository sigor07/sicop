<?php
if ( !isset( $_SESSION ) ) session_start();

require 'init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';
$cont = '';
$ordpor = '';
$q_string = '';

$imp_chefia   = get_session( 'imp_chefia', 'int' );
$imp_cadastro = get_session( 'imp_cadastro', 'int' );
$n_imp_n      = 1;

$n_inteli   = get_session( 'n_inteli', 'int' );
$n_inteli_n = 2;

$tipo_busca = '';
if( !empty( $_GET['tipo_busca'] ) ) {

    $tipo_busca = tratabusca( $_GET['tipo_busca']);

}

$link = 'detento/detalhesdet.php';
$motivo = 'POR PARAMETROS DIVERSOS';
$desc_pag = 'Pesquisar ' . SICOP_DET_DESC_L . 's ';

switch( $tipo_busca ) {
    default:
    case '':
        $tipo_busca = '';
        $desc_pag .= 'por parametros diversos';
        break;
    case 'brg':
        $motivo = 'PELO R.G.';
        $desc_pag .= 'pelo R.G.';
        break;
    case 'bex':
        $motivo = 'PELO NÚMERO DA EXECUÇÃO';
        $desc_pag .= 'pela execução';
        break;
    case 'bpm':
        $motivo = 'PELO NOME DOS PAIS';
        $desc_pag .= 'pelo nome dos pais';
        break;
    case 'bid':
        $motivo = 'PELA IDADE';
        $desc_pag .= 'pela idade';
        break;
    case 'bdn':
        $motivo = 'PELA DATA DE NASCIMENTO';
        $desc_pag .= 'pela data de nascimento';
        break;
    case 'bmv':
        $motivo = 'PELAS MOVIMENTAÇÕES';
        $desc_pag .= 'pelas movimentações';
        break;
    case 'bce':
        $motivo = 'PELA CIDADE/ESTADO';
        $desc_pag .= 'pela cidade/estado';
        break;
    case 'bvu':
        if ( $n_inteli < $n_inteli_n ) {

            // montar a mensagem q será salva no log
            $msg = array();
            $msg['tipo']     = 'perm';
            $msg['entre_ch'] = 'PESQUISAS ESPECIAIS - VULGO';
            get_msg( $msg, 1 );

            require 'cab_simp.php';
            echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

            exit;

        }
        $motivo = 'PELO VULGO';
        $desc_pag .= 'pelo vulgo';
        break;
}

$campo_rg        = get_get( 'campo_rg', 'busca' );
$campo_exec      = get_get( 'campo_exec', 'busca' );
$campo_pai       = get_get( 'campo_pai', 'busca' );
$campo_mae       = get_get( 'campo_mae', 'busca' );
$campo_idade_ini = get_get( 'campo_idade_ini', 'int' );
$campo_idade_fim = get_get( 'campo_idade_fim', 'int' );
$campo_dn_ini    = get_get( 'campo_dn_ini', 'busca' );
$campo_dn_fim    = get_get( 'campo_dn_fim', 'busca' );
$campo_uf        = get_get( 'uf', 'busca' );
$campo_cidade    = get_get( 'cidade', 'busca' );
$campo_vulgo     = get_get( 'campo_vulgo', 'busca' );
$campo_dmov_ini  = get_get( 'campo_dmov_ini', 'busca' );
$campo_dmov_fim  = get_get( 'campo_dmov_fim', 'busca' );
$tipo_mov        = get_get( 'tipo_mov', 'int' );
$local_mov       = get_get( 'local_mov', 'int' );

$tipo_sit = get_get( 'tipo_sit', 'int' );

if( !empty( $_GET['busca'] ) ) {

    if ( !empty( $tipo_busca ) ) {

        $where = '';
        $inner_join = '';

        if ( !empty( $campo_rg ) ) {

            $where = "WHERE ( `detentos`.`rg_civil` LIKE '$campo_rg%'
                              OR
                              ( `aliases`.`cod_tipoalias` = 1 AND REPLACE( REPLACE( `aliases`.`alias_det`, '.','' ), '-','' ) LIKE '$campo_rg%' ) )";

            $inner_join = 'LEFT JOIN `aliases` ON `detentos`.`iddetento` = `aliases`.`cod_detento`';

        }

        if ( !empty( $campo_exec ) ) {

            $where = "WHERE ( `detentos`.`execucao` LIKE '$campo_exec%' )";

        }

        if ( !empty( $campo_pai ) or !empty( $campo_mae ) ) {

            $inner_join = 'LEFT JOIN `aliases` ON `detentos`.`iddetento` = `aliases`.`cod_detento`';

            if ( !empty( $campo_pai ) and !empty( $campo_mae ) ) {

                $where = "WHERE ( `detentos`.`pai_det` LIKE '%$campo_pai%'
                                  OR
                                  ( `aliases`.`cod_tipoalias` = 2 AND `aliases`.`alias_det` LIKE '%$campo_pai%' ) )
                                AND
                                ( `detentos`.`mae_det` LIKE '%$campo_mae%'
                                  OR
                                  ( `aliases`.`cod_tipoalias` = 3 AND `aliases`.`alias_det` LIKE '%$campo_mae%' ) )";

            } else {


                if ( !empty( $campo_pai ) ) {

                    $where = "WHERE ( `detentos`.`pai_det` LIKE '%$campo_pai%'
                                      OR
                                      ( `aliases`.`cod_tipoalias` = 2 AND `aliases`.`alias_det` LIKE '%$campo_pai%' ) )";

                }

                if ( !empty( $campo_mae ) ) {

                    $where = "WHERE ( `detentos`.`mae_det` LIKE '%$campo_mae%'
                                      OR
                                      ( `aliases`.`cod_tipoalias` = 3 AND `aliases`.`alias_det` LIKE '%$campo_mae%' ) )";

                }

            }

        }

        if ( !empty( $campo_idade_ini ) or !empty( $campo_idade_fim ) ) {

            if ( !empty( $campo_idade_ini ) and !empty( $campo_idade_fim ) ) {

                $where = "WHERE ( FLOOR( DATEDIFF( CURDATE(), `detentos`.`nasc_det` ) / 365.25 ) BETWEEN $campo_idade_ini AND $campo_idade_fim )";

            } else {

                $valor_idade = !empty( $campo_idade_ini ) ? $campo_idade_ini : $campo_idade_fim;

                $where = "WHERE ( FLOOR( DATEDIFF( CURDATE(), `detentos`.`nasc_det` ) / 365.25 ) = $valor_idade )";

            }

        }

        if ( !empty( $campo_dn_ini ) or !empty( $campo_dn_fim ) ) {

            if ( !empty( $campo_dn_ini ) and !empty( $campo_dn_fim ) ) {

                $where = "WHERE `detentos`.`nasc_det` BETWEEN STR_TO_DATE( '$campo_dn_ini', '%d/%m/%Y' ) AND STR_TO_DATE( '$campo_dn_fim', '%d/%m/%Y' )";

            } else {

                $valor_dn = !empty( $campo_dn_ini ) ? $campo_dn_ini : $campo_dn_fim;

                $where = "WHERE `detentos`.`nasc_det` = STR_TO_DATE( '$valor_dn', '%d/%m/%Y' )";

            }

        }

        if ( !empty( $campo_uf ) or !empty( $campo_cidade ) ) {

            $inner_join = 'LEFT JOIN `cidades` ON `detentos`.`cod_cidade` = `cidades`.`idcidade`
                           LEFT JOIN `estados` ON `cidades`.`cod_uf` = `estados`.`idestado`';

            $where = "WHERE `estados`.`idestado` = $campo_uf";

            if ( !empty( $campo_cidade ) ) {

                $where = "WHERE `detentos`.`cod_cidade` = $campo_cidade";

            }

        }

        if ( !empty( $campo_vulgo ) ) {

            $where = "WHERE ( `detentos`.`vulgo` LIKE '%$campo_vulgo%' )";

        }

        if ( $tipo_busca == 'bmv' ) {

            $where_sub = '';

            //$campo_dmov_ini = get_get( 'campo_dmov_ini', 'busca' );

            //$campo_dmov_fim = get_get( 'campo_dmov_fim', 'busca' );

            if ( !empty( $campo_dmov_ini ) or !empty( $campo_dmov_fim ) ){

                $data_f = !empty( $campo_dmov_ini ) ? $campo_dmov_ini : $campo_dmov_fim;

                $clausula_data = " `data_mov` = STR_TO_DATE( '$data_f', '%d/%m/%Y' ) ";

                if ( !empty( $campo_dmov_ini ) and  !empty( $campo_dmov_fim ) ){

                    $clausula_data = "`data_mov` BETWEEN STR_TO_DATE( '$campo_dmov_ini', '%d/%m/%Y' ) AND STR_TO_DATE( '$campo_dmov_fim', '%d/%m/%Y' )";

                }

                $where_sub = " WHERE ( $clausula_data ) ";

            }

            $clausula_mov = '';
            if ( !empty( $tipo_mov ) ){

                $clausula_mov = "`cod_tipo_mov` = $tipo_mov";

                if ( !empty( $where_sub ) ){
                    $where_sub .= ' AND ' . $clausula_mov;
                } else {
                    $where_sub .= 'WHERE ' . $clausula_mov;
                }

            }

            //$local_mov = get_get( 'local_mov', 'int' );

            $clausula_local = '';
            if ( !empty( $local_mov ) ){

                $clausula_local = "`cod_local_mov` = $local_mov";

                if ( !empty( $where_sub ) ){
                    $where_sub .= ' AND ' . $clausula_local;
                } else {
                    $where_sub .= 'WHERE ' . $clausula_local;
                }

            }

            if ( !empty( $where_sub ) ){
                $where = "WHERE `detentos`.`iddetento` IN( SELECT `cod_detento` FROM `mov_det` $where_sub )";
            }

        }

        $clausula = get_where_det( $tipo_sit );

        if ( !empty( $clausula ) ){

            if ( !empty( $where ) ){
                $where .= ' AND ' . $clausula;
            } else {
                $where .= 'WHERE ' . $clausula;
            }

        }

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
            case 'raioa':
                $ordbusca = '`detentos`.`cod_cela` ASC, `detentos`.`nome_det` ASC';
                break;
            case 'raiod':
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
                    LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
                    LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
                    LEFT JOIN `unidades` `unidades_in` ON `mov_det_in`.`cod_local_mov` = `unidades_in`.`idunidades`
                    LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
                    $inner_join
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

        $valor_busca = valor_user( $_GET );

        // gerar a mensagem q será salva no log
        $msg = sysmsg::create_msg();
        $msg->add_chaves( "BUSCA ESPECIAL EFETUADA", 0, 1 );
        $msg->set_msg( 'Busca de ' . SICOP_DET_DESC_L . 's efetuada' );
        $msg->add_quebras( 2 );
        $msg->set_msg( $valor_busca );
        $msg->add_quebras( 2 );
        $msg->set_msg( "Quantidade de registros retornados: $cont" );
        $msg->get_msg();

        if ( $cont == 1 ) { //se o número de ocorrências for igual a 1 vai direto para à página do detento, e finaliza com o exit
            $d_det = $query->fetch_object();
            $iddet = $d_det->iddetento;
            header( "Location: $link?iddet=$iddet" );
            exit;
        }

        parse_str( $_SERVER[ 'QUERY_STRING' ], $q_string );

        if ( isset( $q_string['op'] ) ) {
            unset( $q_string['op'] );
        }

    }

}

if ( !empty( $tipo_busca ) ) {

    $db = SicopModel::getInstance();

    $q_tipo_sit = "SELECT `idtipo_sit`, `tipo_sit` FROM `tipo_sit_det_busca` ORDER BY `idtipo_sit` ASC ";
    $q_tipo_sit = $db->query( $q_tipo_sit );

    $db->closeConnection();

}

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();

?>

            <p class="descript_page">PESQUISAR <?php echo SICOP_DET_DESC_U; ?>S <?php echo $motivo; ?></p>

            <?php if ( !empty( $tipo_busca ) ) { ?>
                <p class="link_common"><a href="buscadetesp.php">Outros parametros</a></p>

                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="buscaesp" id="buscaesp">

                    <table class="busca_form">
            <?php }; ?>

            <?php if ( $tipo_busca == 'brg' ) { ?>
                        <tr>
                            <td class="bf_det_legend">Número do R.G.:</td>
                            <td class="bf_det_field"><input name="campo_rg" type="text" class="CaixaTexto" id="campo_rg" onkeypress="return blockChars(event, 5);" value="<?php if ( !empty( $_GET['campo_rg'] ) ) echo $_GET['campo_rg']; ?>" size="30" /></td>
                        </tr>
                        <script type="text/javascript">
                            $(function() {
                                $( "#campo_rg" ).focus();
                            });
                        </script>
            <?php }; ?>

            <?php if ( $tipo_busca == 'bex' ) { ?>
                        <tr>
                            <td class="bf_det_legend">Número da EXECUÇÃO:</td>
                            <td class="bf_det_field"><input name="campo_exec" type="text" class="CaixaTexto" id="campo_exec" onkeypress="return blockChars(event, 5);" value="<?php if ( !empty( $_GET['campo_exec'] ) ) echo $_GET['campo_exec']; ?>" size="30" /></td>
                        </tr>
                        <script type="text/javascript">
                            $(function() {
                                $( "#campo_exec" ).focus();
                            });
                        </script>
            <?php }; ?>

            <?php if ( $tipo_busca == 'bpm' ) { ?>
                        <tr>
                            <td class="bf_det_legend">Nome do PAI:</td>
                            <td class="bf_det_field"><input name="campo_pai" type="text" class="CaixaTexto" id="campo_pai" onkeypress="return blockChars(event, 1);" value="<?php if ( !empty( $_GET['campo_pai'] ) ) echo $_GET['campo_pai'] ?>" size="30" /></td>
                        </tr>
                        <tr>
                            <td class="bf_det_legend">Nome da MÃE:</td>
                            <td class="bf_det_field"><input name="campo_mae" type="text" class="CaixaTexto" id="campo_mae" onkeypress="return blockChars(event, 1);" value="<?php if ( !empty( $_GET['campo_mae'] ) ) echo $_GET['campo_mae'] ?>" size="30" /></td>
                        </tr>
                        <script type="text/javascript">
                            $(function() {
                                $( "#campo_pai" ).focus();
                            });
                        </script>
            <?php }; ?>

            <?php if ( $tipo_busca == 'bid' ) { ?>

                        <tr>
                            <td class="bf_det_legend">Com idade entre:</td>
                            <td class="bf_det_field">
                                <input name="campo_idade_ini" type="text" class="CaixaTexto" id="campo_idade_ini" onkeypress="return blockChars(event, 2);" value="<?php if ( !empty( $_GET['campo_idade_ini'] ) ) echo $_GET['campo_idade_ini'] ?>" size="3" maxlength="3" /> e
                                <input name="campo_idade_fim" type="text" class="CaixaTexto" id="campo_idade_fim" onkeypress="return blockChars(event, 2);" value="<?php if ( !empty( $_GET['campo_idade_fim'] ) ) echo $_GET['campo_idade_fim'] ?>" size="3" maxlength="3" /> anos
                            </td>
                        </tr>
                        <script type="text/javascript">
                            $(function() {
                                $( "#campo_idade_ini" ).focus();
                            });
                        </script>
            <?php }; ?>

            <?php if ( $tipo_busca == 'bdn' ) { ?>

                        <tr>
                            <td class="bf_det_legend">Nascimento entre:</td>
                            <td class="bf_det_field">
                                <input name="campo_dn_ini" type="text" class="CaixaTexto" id="campo_dn_ini" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php if ( !empty( $_GET['campo_dn_ini'] ) ) echo $_GET['campo_dn_ini'] ?>" size="12" maxlength="10" /> e
                                <input name="campo_dn_fim" type="text" class="CaixaTexto" id="campo_dn_fim" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php if ( !empty( $_GET['campo_dn_fim'] ) ) echo $_GET['campo_dn_fim'] ?>" size="12" maxlength="10" />
                            </td>
                        </tr>

                        <script type="text/javascript">

                            $(function() {
                                $( "#campo_dn_ini" ).focus();
                                $( "#campo_dn_ini, #campo_dn_fim" ).datepicker({
                                    showOn: "button",
                                    buttonImageOnly: true
                                });
                            });

                        </script>
            <?php }; ?>

            <?php if ( $tipo_busca == 'bmv' ) { ?>

                        <tr>
                            <td class="bf_det_legend">Data entre:</td>
                            <td class="bf_det_field">
                                <input name="campo_dmov_ini" type="text" class="CaixaTexto" id="campo_dmov_ini" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php if ( !empty( $_GET['campo_dmov_ini'] ) ) echo $_GET['campo_dmov_ini'] ?>" size="12" maxlength="10" /> e
                                <input name="campo_dmov_fim" type="text" class="CaixaTexto" id="campo_dmov_fim" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php if ( !empty( $_GET['campo_dmov_fim'] ) ) echo $_GET['campo_dmov_fim'] ?>" size="12" maxlength="10" />
                            </td>
                        </tr>

                        <tr>
                            <td class="bf_det_legend">Tipo de movimentação:</td>
                            <td class="bf_det_field">
                                <select name="tipo_mov" class="CaixaTexto" id="tipo_mov" onChange="$.monta_box_local_mov();">
                                    <option value="" selected="selected">Selecione</option>
                                </select>
                                <input type="hidden" name="old_tipo_mov" id="old_tipo_mov" value="<?php echo $tipo_mov;?>" />
                                <input type="hidden" name="sit_det" id="sit_det" value="999" />
                            </td>
                        </tr>

                        <tr>
                            <td class="bf_det_legend">Procedência/destino:</td>
                            <td class="bf_det_field_w">
                                <select name="local_mov" class="CaixaTexto" id="local_mov">
                                      <option value="" selected="selected">Selecione o tipo de movimentação</option>
                                </select>
                                <input type="hidden" name="old_local_mov" id="old_local_mov" value="<?php echo $local_mov;?>" />
                            </td>
                        </tr>

                        <script type="text/javascript">

                            $(function() {
                                $.monta_box_tipo_mov();
                                $( "#campo_dmov_ini" ).focus();
                                $( "#campo_dmov_ini, #campo_dmov_fim" ).datepicker({
                                    showOn: "button",
                                    buttonImageOnly: true
                                });
                            });

                        </script>
            <?php }; ?>

            <?php if ( $tipo_busca == 'bce' ) { ?>

                        <tr>
                            <td class="bf_det_legend">Estado:</td>
                            <td class="bf_det_field_w">
                                <select name="uf" class="CaixaTexto" id="uf" onchange="$.monta_box_cidade();">
                                    <option value="" selected="selected">Selecione</option>
                                </select>
                                <input type="hidden" name="old_uf" id="old_uf" value="<?php echo $campo_uf;?>" />
                            </td>
                        </tr>

                        <tr>
                            <td class="bf_det_legend">Cidade:</td>
                            <td class="bf_det_field_w">
                                <select name="cidade" class="CaixaTexto" id="cidade">
                                    <option value="">Selecione o estado</option>
                                </select>
                                <input type="hidden" name="old_cidade" id="old_cidade" value="<?php echo $campo_cidade;?>" />
                            </td>
                        </tr>

                        <script type="text/javascript">
                            $(function() {
                                $.monta_box_uf();
                                $( "#uf" ).focus();
                            });
                        </script>

            <?php }; ?>

            <?php if ( $tipo_busca == 'bvu' ) { ?>

                        <tr>
                            <td class="bf_det_legend">Vulgo:</td>
                            <td class="bf_det_field"><input name="campo_vulgo" type="text" class="CaixaTexto" id="campo_vulgo" onkeypress="return blockChars(event, 1);" value="<?php if ( !empty( $_GET['campo_vulgo'] ) ) echo $_GET['campo_vulgo']; ?>" size="30" /></td>
                        </tr>

                        <script type="text/javascript">
                            $(function() {
                                $( "#campo_vulgo" ).focus();
                            });
                        </script>
            <?php }; ?>


            <?php if ( !empty( $tipo_busca ) ) { ?>

                        <tr>
                            <td class="bf_det_legend">Situação do preso:</td>
                            <td class="bf_det_field_ww">
                                <select name="tipo_sit" class="CaixaTexto" id="tipo_sit">
                                    <option value="" >Todos</option>
                                    <?php while( $d_tipo_sit = $q_tipo_sit->fetch_assoc() ) { ?>
                                    <option value="<?php echo $d_tipo_sit['idtipo_sit'];?>" <?php echo $d_tipo_sit['idtipo_sit'] == $tipo_sit ? 'selected="selected"' : ''; ?>><?php echo $d_tipo_sit['tipo_sit'];?></option>
                                    <?php };?>
                                </select>
                            </td>
                        </tr>
                    </table>

                    <input type="hidden" name="tipo_busca" id="tipo_busca" value="<?php echo $tipo_busca; ?>" />
                    <input type="hidden" name="busca" id="busca" value="busca" />

                    <div class="form_bts">
                        <input class="form_bt" type="submit" name="buscar" id="buscar" value="Buscar" />
                    </div>

                 </form>
            <?php }; ?>

            <?php if ( empty( $tipo_busca ) ) { ?>

                <p class="sub_title_page">Escolha o tipo de pesquisa</p>

                <ul id="menu_besp">
                    <li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?tipo_busca=brg" title="Pesquisar <?php echo SICOP_DET_DESC_L ?>s pelo R.G." >Pelo R.G.</a></li>
                    <li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?tipo_busca=bex" title="Pesquisar <?php echo SICOP_DET_DESC_L ?>s pelo número da execução" >Pela execução</a></li>
                    <li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?tipo_busca=bpm" title="Pesquisar <?php echo SICOP_DET_DESC_L ?>s pelo nome dos pais" >Pelo nome dos pais</a></li>
                    <li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?tipo_busca=bid" title="Pesquisar <?php echo SICOP_DET_DESC_L ?>s pela idade" >Pela idade</a></li>
                    <li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?tipo_busca=bdn" title="Pesquisar <?php echo SICOP_DET_DESC_L ?>s pela data de nascimento" >Pela data de nascimento</a></li>
                    <li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?tipo_busca=bce" title="Pesquisar <?php echo SICOP_DET_DESC_L ?>s pela cidade/estado" >Pela cidade/estado</a></li>
                    <li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?tipo_busca=bmv" title="Pesquisar <?php echo SICOP_DET_DESC_L ?>s pelas movimentações" >Pelas movimentações</a></li>
                    <?php if ( $n_inteli >= 2 ) {?>
                    <li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?tipo_busca=bvu" title="Pesquisar <?php echo SICOP_DET_DESC_L ?>s pelo vulgo" >Pelo vulgo</a></li>
                    <?php } ?>
                </ul>

            <?php }; ?>

            <?php

            if ( !empty( $_GET['busca'] ) ) {
                include 'lista_busca.php';
            }

            include 'footer.php';
?>