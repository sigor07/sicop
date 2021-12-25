<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_chefia   = get_session( 'n_chefia', 'int' );
$n_chefia_n = 2;

$n_portaria   = get_session( 'n_portaria', 'int' );
$n_portaria_n = 2;

$n_seg   = get_session( 'n_seg', 'int' );
$n_seg_n = 2;

$n_peculio  = get_session( 'n_peculio', 'int' );
$n_peculi_n = 2;

if ( $n_chefia < $n_chefia_n and
     $n_portaria < $n_portaria_n and
     $n_seg < $n_seg_n and
     $n_peculio < $n_peculi_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = 'LISTAR ORDENS DE SAÍDA - CHEFIA';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$imp_chefia   = get_session( 'imp_chefia', 'int' );

$data_ini_sf   = '';
$data_fim_sf   = '';
$clausula_data = '`ordens_saida`.`ord_saida_data` >= DATE( NOW() )';

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

            $clausula_data = "( `ordens_saida`.`ord_saida_data` BETWEEN STR_TO_DATE( '$data_ini', '%d/%m/%Y' ) AND STR_TO_DATE( '$data_fim', '%d/%m/%Y' ) )";

        } else {

            $data_f = !empty( $data_ini ) ? $data_ini : $data_fim;

            $clausula_data = "`ordens_saida`.`ord_saida_data` = STR_TO_DATE( '$data_f', '%d/%m/%Y' ) ";

        }

    }

}

$q_list_ord_saida = "SELECT
                       `ordens_saida`.`id_ord_saida`,
                       DATE_FORMAT( `ordens_saida`.`ord_saida_data`, '%d/%m/%Y' ) AS ord_saida_data_f,
                       DATE_FORMAT( `ordens_saida`.`ord_saida_hora`, '%H:%i' ) AS `ord_saida_hora_f`,
                       `locais_apr`.`local_apr`
                     FROM
                       `ordens_saida`
                       LEFT JOIN `ordens_saida_locais` ON `ordens_saida_locais`.`cod_ord_saida` = `ordens_saida`.`id_ord_saida`
                       LEFT JOIN `locais_apr` ON `ordens_saida_locais`.`cod_local` = `locais_apr`.`idlocal`
                     WHERE
                       $clausula_data
                     ORDER BY
                       `ordens_saida`.`ord_saida_data` DESC, `ordens_saida`.`id_ord_saida` DESC, `locais_apr`.`local_apr`";

$sit_pag = 'LISTAR ORDENS DE SAÍDA';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_list_ord_saida = $model->query( $q_list_ord_saida );

// fechando a conexao
$model->closeConnection();

if ( !$q_list_ord_saida ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta ( $sit_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$cont_list_esc = $q_list_ord_saida->num_rows;

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Listar ordens de saída';

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>


            <p class="descript_page">LISTAR ORDENS DE SAÍDA</p>

            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="ord_saida" id="ord_saida">

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

            <?php
                if ( empty( $cont_list_esc ) or $cont_list_esc < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem

                    echo '<p class="p_q_no_result">Não há ordens de saída cadastradas.</p>';
                    include 'footer.php';
                    exit;

                }
            ?>
            <table class="bonde_list">
                <tr class="cab">
                    <th class="num_od">N</th>
                    <th class="data_bonde">DATA</th>
                    <th class="local_bonde">LOCAIS</th>
                    <?php if ( $imp_chefia >= 1 ) {  ?>
                    <th class="tb_bt">&nbsp;</th>
                    <?php }  ?>
                </tr>
<?php
            $i = 0;
            $linha         = array();
            $local_ord_saida = array();
            $ord_saida_atual = '';
            $ord_saida_ant   = '';
            // $data_esc_ant  = '';

            while ( $d_list_esc = $q_list_ord_saida->fetch_assoc() ) {

                ++$i;

                $ord_saida_atual = $d_list_esc['id_ord_saida'];
                $local_atual = $d_list_esc['local_apr'];

                if ( $ord_saida_atual == $ord_saida_ant or $i == 1 ) {

                    $data_esc = $d_list_esc['ord_saida_data_f'];
                    $hora_esc = $d_list_esc['ord_saida_hora_f'];

                    $local_ord_saida[] = $local_atual;

                    $linha["$ord_saida_atual"]['data'] = $data_esc;
                    $linha["$ord_saida_atual"]['hora'] = $hora_esc;
                    $linha["$ord_saida_atual"]['local'] = $local_ord_saida;

                } else {

                    $local_ord_saida = '';
                    $local_ord_saida[] = $local_atual;

                    $data_esc = $d_list_esc['ord_saida_data_f'];
                    $hora_esc = $d_list_esc['ord_saida_hora_f'];

                    $linha["$ord_saida_atual"]['data'] = $data_esc;
                    $linha["$ord_saida_atual"]['hora'] = $hora_esc;
                    $linha["$ord_saida_atual"]['local'] = $local_ord_saida;

                }

                $ord_saida_ant = $ord_saida_atual;

            }

            $i = 0;
            foreach ( $linha as $indice => $valor ) {

                ++$i;

                $id_ord_saida = $indice;

                $ord_saida_data   = $linha["$id_ord_saida"]['data'];
                $ord_saida_hora   = $linha["$id_ord_saida"]['hora'];
                $linha_local      = $linha["$id_ord_saida"]['local'];
                $locais_ord_saida = implode('<br/>', $linha_local );

                if ( empty( $locais_ord_saida ) ) {
                    $locais_ord_saida = 'Não há locais.';
                }

?>
                <tr class="even">
                    <td class="num_od"><?php echo $i ?> </td>
                    <td class="data_bonde"><a href="detal_ord_saida.php?id_ord_saida=<?php echo $id_ord_saida ?>" title="Clique aqui para ver os detalhes desta ordem de saída"><?php echo $ord_saida_data ?> <?php echo !empty( $ord_saida_hora ) ? ' às ' . $ord_saida_hora : '' ?></a></td>
                    <td class="local_bonde"><?php echo $locais_ord_saida ?></td>
                    <?php if ( $imp_chefia >= 1 ) {  ?>
                    <td class="tb_bt"><a href='javascript:void(0)' onclick="submit_form_nwid( '../print/ordem_saida.php', 'id_ord_saida', <?php echo $id_ord_saida ?> )"  title="Imprimir a lista de detentos desta ordem de saída" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>print.png" alt="Imprimir a lista de detentos desta ordem de saída" class="icon_button" /></a></td>
                    <?php }; ?>
                </tr>
<?php } // fim do foreach ( $linha as $indice => $valor ) ?>
            </table><!-- fim da table."bonde_list" -->

<?php include 'footer.php'; ?>