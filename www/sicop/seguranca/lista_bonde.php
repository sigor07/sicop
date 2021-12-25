<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_bonde   = get_session( 'n_bonde', 'int' );
$n_bonde_n = 2;

if ( $n_bonde < $n_bonde_n ) {
    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
    salvaLog( $mensagem );
    exit;
}

$n_bonde_fut = get_session( 'n_bonde_fut', 'int' );
$imp_bonde   = get_session( 'imp_bonde', 'int' );


$data_ini_sf   = '';
$data_fim_sf   = '';
$clausula_data = '`bonde`.`bonde_data` >= DATE( NOW() )';

if ( !empty( $_GET['busca'] ) ) {

    $mensagem = "Acesso à página $pag";
    salvaLog( $mensagem );

    $data_ini_sf = $_GET['data_ini'];
    $data_ini = get_get ( 'data_ini', 'busca' );

    $data_fim_sf = $_GET['data_fim'];
    $data_fim = get_get ( 'data_fim', 'busca' );

    if ( !empty( $data_ini ) or !empty( $data_fim ) ) {

        if ( !empty( $data_ini ) and !empty( $data_fim ) ) {

            $clausula_data = "( `bonde`.`bonde_data` BETWEEN STR_TO_DATE( '$data_ini', '%d/%m/%Y' ) AND STR_TO_DATE( '$data_fim', '%d/%m/%Y' ) )";

        } else {

            $data_f = !empty( $data_ini ) ? $data_ini : $data_fim;

            $clausula_data = "`bonde`.`bonde_data` = STR_TO_DATE( '$data_f', '%d/%m/%Y' ) ";

        }

    }

}// /if ( !empty( $_GET['busca'] ) )

$clausula_data_add = 'OR ISNULL( `bonde`.`bonde_data` ) ';

if ( $n_bonde_fut < 1 ) {
    $clausula_data_add = 'AND NOT ISNULL( `bonde`.`bonde_data` ) AND `bonde`.`bonde_data` <= ADDDATE( DATE( NOW() ), 1 )';
}

$clausula_data .= $clausula_data_add;

$q_list_bonde = "SELECT
                   `bonde`.`idbonde`,
                   DATE_FORMAT( `bonde`.`bonde_data`, '%d/%m/%Y') AS bonde_data_f,
                   `unidades`.`unidades`
                 FROM
                   `bonde`
                   LEFT JOIN `bonde_locais` ON `bonde_locais`.`cod_bonde` = `bonde`.`idbonde`
                   LEFT JOIN `unidades` ON `bonde_locais`.`cod_unidade` = `unidades`.`idunidades`
                 WHERE
                    $clausula_data
                 ORDER BY
                   `bonde`.`bonde_data` DESC, `bonde`.`idbonde` DESC, `unidades`.`unidades`";

$sit_pag = 'LISTAR BONDES AGENDADOS';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_list_bonde = $model->query( $q_list_bonde );

// fechando a conexao
$model->closeConnection();

if( !$q_list_bonde ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_list_bonde = $q_list_bonde->num_rows;

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Listar bondes agendados';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) {
    $pag_atual .=  '?' . $qs;
}

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>

            <p class="descript_page">LISTAR BONDES AGENDADOS</p>

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

            <?php if ( $n_bonde >= 3 ) { ?>
            <p class="link_common" style="margin-top: 5px;"><a href="add_bonde.php">Agendar bonde</a></p>
            <?php } ?>

<?php
    if ( empty( $cont_list_bonde ) or $cont_list_bonde < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem

        echo '<p class="p_q_no_result">Não há bondes agendados.</p>';
        include 'footer.php';
        exit;

    }
?>
            <table class="bonde_list">
                <tr class="cab">
                    <th class="num_od">N</th>
                    <th class="data_bonde">DATA</th>
                    <th class="local_bonde">LOCAIS</th>
                    <?php if ( $imp_bonde >= 1 ) {  ?>
                    <th class="tb_bt">&nbsp;</th>
                    <?php }  ?>
                    <?php if ($n_bonde >= 3 ) {  ?>
                    <th class="tb_bt">&nbsp;</th>
                    <?php }  ?>
                </tr>
<?php
            $i = 0;
            $linha = array();
            $local_bonde = array();
            $bonde_atual = '';
            $bonde_ant = '';
            $data_bonde_ant = '';

            while( $d_list_bonde = $q_list_bonde->fetch_assoc() ) {

                ++$i;

                $bonde_atual = $d_list_bonde['idbonde'];
                $local_atual = $d_list_bonde['unidades'];

                if ( $bonde_atual == $bonde_ant or $i == 1 ) {

                    $data_bonde = !empty ( $d_list_bonde['bonde_data_f'] ) ? $d_list_bonde['bonde_data_f'] : 'N/D' ;

                    $local_bonde[] = $local_atual;

                    $linha["$bonde_atual"]['data'] = $data_bonde;
                    $linha["$bonde_atual"]['local'] = $local_bonde;

                } else {

                    $local_bonde = '';
                    $local_bonde[] = $local_atual;

                    $data_bonde = !empty ( $d_list_bonde['bonde_data_f'] ) ? $d_list_bonde['bonde_data_f'] : 'N/D' ;

                    $linha["$bonde_atual"]['data'] = $data_bonde;
                    $linha["$bonde_atual"]['local'] = $local_bonde;

                }

                $bonde_ant = $bonde_atual;

            }

            $i = 0;
            foreach ( $linha as $indice => $valor ) {

                ++$i;

                $idbonde = $indice;

                $bonde_data = $linha["$idbonde"]['data'];
                $linha_local = $linha["$idbonde"]['local'];
                $locais_bonde = implode(', ', $linha_local );

                if ( empty( $locais_bonde ) ) {
                    $locais_bonde = 'Não há locais.';
                }

?>
                <tr class="even">
                    <td class="num_od"><?php echo $i ?> </td>
                    <td class="data_bonde"><a href="detal_bonde.php?idbonde=<?php echo $idbonde ?>" title="Clique aqui para ver os detalhes deste bonde"><?php echo $bonde_data ?></a></td>
                    <td class="local_bonde"><?php echo $locais_bonde ?></td>
                    <?php if ( $imp_bonde >= 1 ) {  ?>
                    <td class="tb_bt"><a href='javascript:void(0)' onclick="javascript: ow('../print/lista_bonde.php?idbonde=<?php echo $idbonde; ?>', '600', '600'); return false"  title="Imprimir a lista de detentos deste bonde" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>print.png" alt="Imprimir a lista de detentos deste bonde" class="icon_button" /></a></td>
                    <?php }; ?>
                    <?php if ($n_bonde >= 3 ) {  ?>
                    <td class="tb_bt"><a href='javascript:void(0)' onclick='drop_bonde(<?php echo $idbonde; ?>)' title="Excluir este bonde"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="Excluir este bonde" class="icon_button" /></a></td>
                    <?php }; ?>
                </tr>
<?php } // fim do foreach ( $linha as $indice => $valor ) ?>
            </table><!-- fim da table."bonde_list" -->

<?php include 'footer.php'; ?>