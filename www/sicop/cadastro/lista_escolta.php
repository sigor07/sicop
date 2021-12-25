<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_cadastro = get_session( 'n_cadastro', 'int' );
$n_cad_n    = 2;

$motivo_pag = 'LISTAR PEDIDOS DE ESCOLTA';

if ( $n_cadastro < $n_cad_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$imp_cadastro = get_session( 'imp_cadastro', 'int');

$data_ini_sf   = '';
$data_fim_sf   = '';
$clausula_data = '`ordens_escolta`.`escolta_data` >= DATE( NOW() )';

if ( !empty( $_GET['busca'] ) ) {

    $mensagem = "Acesso à página $pag";
    salvaLog( $mensagem );

    $data_ini_sf = $_GET['data_ini'];
    $data_ini = get_get ( 'data_ini', 'busca' );

    $data_fim_sf = $_GET['data_fim'];
    $data_fim = get_get ( 'data_fim', 'busca' );

    $clausula_data = '';

    if ( !empty( $data_ini ) or !empty( $data_fim ) ) {

        if ( !empty( $data_ini ) and  !empty( $data_fim )){

            $clausula_data = "( `ordens_escolta`.`escolta_data` BETWEEN STR_TO_DATE( '$data_ini', '%d/%m/%Y' ) AND STR_TO_DATE( '$data_fim', '%d/%m/%Y' ) )";

        } else {

            $data_f = !empty( $data_ini ) ? $data_ini : $data_fim;

            $clausula_data = "`ordens_escolta`.`escolta_data` = STR_TO_DATE( '$data_f', '%d/%m/%Y' ) ";

        }

    }

}

$q_list_esc = "SELECT
                 `ordens_escolta`.`idescolta`,
                 DATE_FORMAT( `ordens_escolta`.`escolta_data`, '%d/%m/%Y' ) AS escolta_data_f,
                 DATE_FORMAT( `ordens_escolta`.`escolta_hora`, '%H:%i' ) AS `escolta_hora_f`,
                 `locais_apr`.`local_apr`
               FROM
                 `ordens_escolta`
                 LEFT JOIN `ordens_escolta_locais` ON `ordens_escolta_locais`.`cod_escolta` = `ordens_escolta`.`idescolta`
                 LEFT JOIN `locais_apr` ON `ordens_escolta_locais`.`cod_local` = `locais_apr`.`idlocal`
               WHERE
                 $clausula_data
               ORDER BY
                 `ordens_escolta`.`escolta_data` DESC, `ordens_escolta`.`idescolta` DESC, `locais_apr`.`local_apr`";

$sit_pag = 'LISTAR PEDIDOS DE ESCOLTA';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_list_esc = $model->query( $q_list_esc );

// fechando a conexao
$model->closeConnection();

if ( !$q_list_esc ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta ( $sit_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$cont_list_esc = $q_list_esc->num_rows;

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Listar pedidos de escolta';

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>


            <p class="descript_page">LISTAR PEDIDOS DE ESCOLTA</p>

            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="ped_esc" id="ped_esc">

                <p class="table_leg">Digite ou escolha a data:</p>

                <table class="busca_form">
                    <tr>
                        <td width="41" align="right">Entre:</td>
                        <td width="135"><input name="data_ini" type="text" class="CaixaTexto" id="data_ini" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_ini_sf ?>" size="12" maxlength="10" />&nbsp;&nbsp;<a href="#" onclick="javascript: datahoje('data_ini'); return false;" >hoje</a></td>
                    </tr>
                    <tr>
                        <td align="right">e:</td>
                        <td><input name="data_fim" type="text" class="CaixaTexto" id="data_fim" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_fim_sf ?>" size="12" maxlength="10" />&nbsp;&nbsp;<a href="#" onclick="javascript: datahoje('data_fim'); return false;" >hoje</a></td>
                    </tr>
                </table>

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="buscar" id="buscar" value="Buscar" />
                </div>

                <input name="busca" type="hidden" id="busca" value="busca" />

            </form>

            <input name="datahj" type="hidden" id="datahj" value="<?php echo date( 'd/m/Y' ) ?>">

            <script type="text/javascript">

                $(function() {
                    $( "#data_ini" ).focus();
                    $( "#data_ini, #data_fim" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });
                });

            </script>

            <?php if ( $n_cadastro >= 3 ) { ?>
            <p class="link_common" style="margin-top: 5px;"><a href="add_escolta.php">Cadastrar pedido de escolta</a></p>
            <?php } ?>

            <?php
                if ( empty( $cont_list_esc ) or $cont_list_esc < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem

                    echo '<p class="p_q_no_result">Não há pedidos de escolta cadastrados.</p>';
                    include 'footer.php';
                    exit;

                }
            ?>
            <table class="bonde_list">
                <tr class="cab">
                    <th class="num_od">N</th>
                    <th class="data_bonde">DATA</th>
                    <th class="local_bonde">LOCAIS</th>
                    <?php if ( $imp_cadastro >= 1 ) {  ?>
                    <th class="tb_bt">&nbsp;</th>
                    <?php }  ?>
                    <?php if ( $n_cadastro >= 3 ) {  ?>
                    <th class="tb_bt">&nbsp;</th>
                    <?php }  ?>
                </tr>
            <?php
            $i = 0;
            $linha         = array();
            $local_escolta = array();
            $escolta_atual = '';
            $escolta_ant   = '';
            // $data_esc_ant  = '';

            while ( $d_list_esc = $q_list_esc->fetch_assoc() ) {

                ++$i;

                $escolta_atual = $d_list_esc['idescolta'];
                $local_atual = $d_list_esc['local_apr'];

                if ( $escolta_atual == $escolta_ant or $i == 1 ) {

                    $data_esc = $d_list_esc['escolta_data_f'];
                    $hora_esc = $d_list_esc['escolta_hora_f'];

                    $local_escolta[] = $local_atual;

                    $linha["$escolta_atual"]['data'] = $data_esc;
                    $linha["$escolta_atual"]['hora'] = $hora_esc;
                    $linha["$escolta_atual"]['local'] = $local_escolta;

                } else {

                    $local_escolta = '';
                    $local_escolta[] = $local_atual;

                    $data_esc = $d_list_esc['escolta_data_f'];
                    $hora_esc = $d_list_esc['escolta_hora_f'];

                    $linha["$escolta_atual"]['data'] = $data_esc;
                    $linha["$escolta_atual"]['hora'] = $hora_esc;
                    $linha["$escolta_atual"]['local'] = $local_escolta;

                }

                $escolta_ant = $escolta_atual;

            }

            $i = 0;
            foreach ( $linha as $indice => $valor ) {

                ++$i;

                $idescolta = $indice;

                $escolta_data   = $linha["$idescolta"]['data'];
                $escolta_hora   = $linha["$idescolta"]['hora'];
                $linha_local    = $linha["$idescolta"]['local'];
                $locais_escolta = implode('<br/>', $linha_local );

                if ( empty( $locais_escolta ) ) {
                    $locais_escolta = 'Não há locais.';
                }

?>
                <tr class="even">
                    <td class="num_od"><?php echo $i ?> </td>
                    <td class="data_bonde"><a href="detal_escolta.php?idescolta=<?php echo $idescolta ?>" title="Clique aqui para ver os detalhes deste pedido de escolta"><?php echo $escolta_data ?> <?php echo !empty( $escolta_hora ) ? ' às ' . $escolta_hora : '' ?></a></td>
                    <td class="local_bonde"><?php echo $locais_escolta ?></td>
                    <?php if ( $imp_cadastro >= 1 ) {  ?>
                    <td class="tb_bt"><a href='javascript:void(0)' onclick="submit_form_nwid( '../print/ordem_escolta.php', 'idescolta', <?php echo $idescolta ?> )"  title="Imprimir a lista de detentos deste pedido de escolta" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>print.png" alt="Imprimir a lista de detentos deste pedido de escolta"  /></a></td>
                    <?php }; ?>
                    <?php if ( $n_cadastro >= 3 ) {  ?>
                    <td class="tb_bt"><a href='javascript:void(0)' onclick='drop_escolta( <?php echo $idescolta; ?>, 3 )' title="Excluir este pedido de escolta"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="Excluir este pedido de escolta" class="icon_button" /></a></td>
                    <?php }; ?>
                </tr>
            <?php } // fim do foreach ( $linha as $indice => $valor ) ?>
            </table><!-- fim da table."bonde_list" -->

<?php include 'footer.php'; ?>