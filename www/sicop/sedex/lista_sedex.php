<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';
$q_string = '';

$n_portaria   = get_session( 'n_portaria', 'int' );
$n_portaria_n = 2;

$n_sedex   = get_session( 'n_sedex', 'int' );
$n_sedex_n = 2;

$imp_sedex = get_session( 'imp_sedex', 'int' );

$n_incl   = get_session( 'n_incl', 'int' );
$n_incl_n = 2;

$motivo_pag = 'LISTA DE SEDEX';

$sit = get_get( 'sit', 'int' );

if ( empty( $sit ) or ( $sit > 3 ) ){

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( IDENTIFICADOR EM BRANCO - $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

if ( $n_sedex < $n_sedex_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

if ( $sit == 1 or $sit == 3 ) {

    if ( $n_portaria < $n_portaria_n ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']     = 'perm';
        $msg['entre_ch'] = $motivo_pag;
        get_msg( $msg, 1 );

        require 'cab_simp.php';
        echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

        exit;

    }

} else if ( $sit == 2 ) {

    if ( $n_incl < $n_incl_n ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']     = 'perm';
        $msg['entre_ch'] = $motivo_pag;
        get_msg( $msg, 1 );

        require 'cab_simp.php';
        echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

        exit;

    }

}

$sit_lista = 'RECEBIDOS';
if ( $sit == 2 ) {

    $sit_lista = 'ENCAMINHADOS PARA A INCLUSÃO';

} else if ( $sit == 3 ) {

    $sit_lista = 'SEPARADOS PARA DEVOLUÇÃO';

}

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


$ordpor = 'nomea';

if ( !empty( $_GET['op'] ) ) {
    $ordpor = get_get( 'op', 'busca' );
}

$ordbusca = "`detentos`.`nome_det` ASC";

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
            `sedex`.`sit_sedex` = $sit
            $clausula_data
          ORDER BY
            $ordbusca";

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

$querytime = $model->getQueryTime();

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

parse_str( $_SERVER[ 'QUERY_STRING' ], $q_string );

if ( isset( $q_string['op'] ) ){
    unset( $q_string['op'] );
}

$desc_pag = 'Listar Sedex';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>

            <script type="text/javascript" >
                function confirmAcao(){
                    var acao = confirm("Deseja realmente executar esta ação?");

                    if ( acao == true ) {
                        acao = validalistasedex();
                    }

                    return acao;
                }
            </script>

            <p class="descript_page">LISTAR SEDEX <?php echo $sit_lista; ?></p>

            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get" name="relat_entr" id="relat_entr">

                <p class="table_leg">Digite ou escolha a data:</p>

                <table class="busca_form">
                    <tr>
                        <td class="bf_legend">Entre:</td>
                        <td class="bf_field"><input name="data_ini" type="text" class="CaixaTexto" id="data_ini" onBlur="verifica_data(this, this.value)" onKeyPress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_ini_sf ?>" size="12" maxlength="10" /></td>
                    </tr>
                    <tr>
                        <td class="bf_legend">e:</td>
                        <td class="bf_field"><input name="data_fim" type="text" class="CaixaTexto" id="data_fim" onBlur="verifica_data(this, this.value)" onKeyPress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_fim_sf ?>" size="12" maxlength="10" /></td>
                    </tr>
                </table>

                <input type="hidden" name="sit" value="<?php echo $sit?>" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="buscar" id="buscar" value="Buscar" />
                </div>

            </form>

            <script type="text/javascript">

                $(function() {
                    $( "#data_ini" ).focus();
                    $( "#data_ini, #data_fim" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });
                });

            </script>

            <?php

                if ( empty( $cont ) or $cont < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                    echo '<p class="p_q_no_result">Não foi encontrado nenhuma ocorrência.</p>';
                    include 'footer.php';
                    exit;
                }

            ?>

            <?php if ( $n_sedex >= 3 ){ ?>

            <form action="" method="post" name="sendsedex" id="sendsedex" >

            <?php } ?>
            <?php
            $css_class = 'lista_busca';
            if ( $n_sedex >= 3 and ( $n_portaria >= 3 or $n_incl >= 3 ) ) {
                $css_class = 'lista_busca grid';
            }
            ?>
                <table class="<?php echo $css_class; ?>">
                    <thead>
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
                            <?php if ( $n_sedex >= 3 and ( $n_portaria >= 3 or $n_incl >= 3 ) ) { ?>
                            <th class="tb_ck">&nbsp;</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
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
                        <td class="nome_det<?php if ( stripos( $ordpor, 'nome' ) !== false ) echo ' ord';?>" title="Pai: <?php echo $d_det['pai_det'];?>&#13;Mãe: <?php echo $d_det['mae_det'];?>&#13;Situação atual: <?php echo $det['sitat'];?>"><a href="<?php echo SICOP_ABS_PATH; ?>detento/detalhesdet.php?iddet=<?php echo $d_det['iddetento'] /*alphaID($dados['iddetento'])*/;?>" > <?php echo $d_det['nome_det'];?></a></td>
                        <td class="matr_det <?php echo $det['css_class']; if ( stripos( $ordpor, 'matr' ) !== false ) echo ' ord';?>"><?php echo !empty( $d_det['matricula'] ) ? formata_num( $d_det['matricula'] ) : '&nbsp;';?></td>
                        <td class="raio_det <?php echo $det['css_class']; if ( stripos( $ordpor, 'raio' ) !== false ) echo ' ord';?>"><?php echo !empty( $d_det['raio'] ) ? $d_det['raio'] : '&nbsp;'; ?></td>
                        <td class="cela_det <?php echo $det['css_class']; if ( stripos( $ordpor, 'raio' ) !== false ) echo ' ord';?>"><?php echo !empty( $d_det['cela'] ) ? $d_det['cela'] : '&nbsp;'; ?></td>
                        <td class="cod_sedex<?php if ( stripos( $ordpor, 'cod' ) !== false ) echo ' ord';?>" title="Data de entrada: <?php echo $d_det['data_sedex']; ?>"><a href="detalsedex.php?ids=<?php echo $d_det['idsedex'] ;?>" ><?php echo formata_num_sedex ( $d_det['cod_sedex'] );?></a></td>
                        <?php if ( $n_sedex >= 3 and ( $n_portaria >= 3 or $n_incl >= 3 ) ) { ?>
                        <td class="tb_ck"><input name="idsedex[]" type="checkbox" class="mark_row" id="sedex" value="<?php echo $d_det['idsedex'];?>" /></td>
                        <?php } ?>
                    </tr>

                    <?php } // fim do while ?>
                    </tbody>
                    <?php if ( $n_sedex >= 3 and ( $n_portaria >= 3 or $n_incl >= 3 ) ) { ?>
                    <tfoot>
                        <tr>
                            <td  class="tb_ck_leg" colspan="6">todos:</td>
                            <td class="tb_ck"><input type="checkbox" name="todos" value="todos" /></td>
                        </tr>
                    </tfoot>
                    <?php } ?>
                </table>
                <?php if ( $n_sedex >= 3 and ( $n_portaria >= 3 or $n_incl >= 3 ) ) { ?>

                <p class="bt_leg">COM MARCADOS</p>

                <div class="form_bts">
                    <?php if ( $sit == 1 and $n_portaria >= 3 ) {?>
                    <?php if ( $imp_sedex >= 1 ) {?>
                    <input class="form_bt" name="iri" type="button" value="Imprimir recibo da inclusão" onclick="if ( validalistasedex() == true ) { submit_form_nw( 'sendsedex', '../print/rec_sedex_port.php' ) };" />
                    <?php } ?>
                    <input class="form_bt" name="eni" type="button" value="Encaminhar para a inclusão" onclick="if ( validalistasedex() == true ) { submit_form( 'sendsedex', '../send/sendsedex.php', 1, 2 ) };" />
                    <?php } else if ( $sit == 3 and $n_portaria >= 3 ) {?>
                    <?php if ( $imp_sedex >= 1 ) {?>
                    <input class="form_bt" name="irc" type="button" value="Imprimir recibo do correio" onclick="if ( validalistasedex() == true ) { submit_form_nw( 'sendsedex', '../print/rec_sedex_corr.php' ) };" />
                    <?php } ?>
                    <input class="form_bt" name="dev" type="button" value="Marcar como devolvidos" onclick="if ( validalistasedex() == true ) { submit_form( 'sendsedex', '../send/sendsedex.php', 1, 4 ) };" />
                    <?php } else if ( $sit == 2 and $n_incl >= 3 ) {?>
                    <?php if ( $imp_sedex >= 1 ) {?>
                    <input class="form_bt" name="irr" type="button" value="Imprimir recibo do raio" onclick="if ( validalistasedex() == true ) { submit_form_nw( 'sendsedex', '../print/rec_sedex_raio.php' ) };" />
                    <input class="form_bt" name="irm" type="button" value="Imprimir relação de mercadorias" onclick="if ( validalistasedex() == true ) { submit_form_nw( 'sendsedex', '../print/rel_merc.php' ) };" />
                    <?php } ?>
                    <input class="form_bt" name="ent" type="button" value="Marcar como entregues" onclick="if ( validalistasedex() == true ) { submit_form( 'sendsedex', '../send/sendsedex.php', 1, 5 ) };" />
                    <?php } ?>
                </div>

                <?php if ( $n_portaria >= 4 or $n_incl >= 4 ) {?>
                <div class="form_bts">
                    <input class="form_bt" name="exc" type="button" value="Excluir" onclick="if ( validalistasedex() == true ) { submit_form( 'sendsedex', '../send/sendsedex.php', 2, 1 ) };" />
                </div>
                <?php } ?>

                <input type="hidden" name="proced" id="proced" value="" />
                <input type="hidden" name="sub_proced" id="sub_proced" value="" />

            </form>
            <?php } ?>

<?php include 'footer.php'; ?>
