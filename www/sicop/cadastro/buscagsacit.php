<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_cadastro   = get_session( 'n_cadastro', 'int' );
$n_cad_n      = 2;

$motivo_pag = 'BUSCA DE CIDADES GSA';

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

$campo_gsa = '';

if( !empty( $_GET['busca'] ) ) {

    $ordbusca = '';

    $ordpor = get_get( 'op', 'busca' );

    if ( empty ( $ordpor ) ) {
        $ordpor = 'cida';
    }

    switch( $ordpor ) {
        default:
        case 'cida':
            $ordbusca = "`gsa_cidade_nome` ASC";
            break;
        case 'cidd':
            $ordbusca = "`gsa_cidade_nome` DESC";
            break;
        case 'coda':
            $ordbusca = "`gsa_cidade_cod` ASC";
            break;
        case 'codd':
            $ordbusca = "`gsa_cidade_cod` DESC";
            break;
    }

    $campo_gsa = get_get( 'campo_gsa', 'busca' );

    $q_gsa = "SELECT
                `gsa_cidade_id`,
                `gsa_cidade_cod`,
                `gsa_cidade_nome`
              FROM
                `gsa_cidades`
              WHERE
                `gsa_cidade_nome` LIKE '%$campo_gsa%' OR
                `gsa_cidade_cod` LIKE '$campo_gsa%'
              ORDER BY
                $ordbusca";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_gsa = $model->query( $q_gsa );

    // fechando a conexao
    $model->closeConnection();

    if( !$q_gsa ) {

        echo msg_js( '', 1 );
        exit;

    }

    $querytime = $model->getQueryTime();

    $cont_gsa = $q_gsa->num_rows;

    $valor_busca = valor_user($_GET);

    $mensagem = "[ BUSCA EFETUADA ]\nBusca de código de cidades do GSA efetuada\n\n $valor_busca\n\n Página: $pag";
    salvaLog($mensagem);

}

$q_string = '';
parse_str( $_SERVER[ 'QUERY_STRING' ], $q_string );

if ( isset( $q_string['op'] ) ){
    unset($q_string['op'] );
}

$desc_pag = 'Pesquisar códigos das cidades do GSA';

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



            <p class="descript_page"> PESQUISAR CIDADES DO GSA </p>

            <form action="<?php echo $_SERVER['PHP_SELF']?>" method="get" name="buscagsa" id="buscagsa">

                <p class="table_leg">Digite o CÓDIGO ou o NOME da cidade:</p>

                <div class="form_one_field">
                    <input name="campo_gsa" type="text" class="CaixaTexto" id="campo_gsa" onkeypress="return blockChars(event, 4);" value="<?php echo $campo_gsa ?>" size="50" />
                </div>

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="busca" id="busca" value="Buscar" />
                </div>

                <input name="busca" type="hidden" id="busca" value="busca" />

            </form>

            <script type="text/javascript"> id("campo_gsa").select(); id("campo_gsa").focus(); </script>


            <?php

                if ( empty( $_GET['busca'] ) ) {
                    include 'footer.php';
                    exit;
                }

                if( empty( $cont_gsa ) or $cont_gsa < 1 ) {
                    echo '<p class="p_q_no_result">Não foi encontrado nenhuma ocorrência.</p>';
                    include 'footer.php';
                    exit;
                }

            ?>

            <p class="p_q_info">Essa consulta retornou <?php echo $cont_gsa ?> registros (<?php echo round( $querytime, 2 ) ?> seg). </p>

            <table class="lista_busca">
                <tr class="cab">
                    <th class="num_od">N</th>
                    <th class="cod_gsa">Código
                        <?php if ($ordpor == 'coda') {?>
                        <img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_asc_m.png" alt="" width="11" height="9" />
                        <?php } else { ?>
                        <a href="?<?php echo http_build_query( $q_string )?>&op=coda" title="Ordenar pelo código crescente" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_asc.png" alt="Ordenar pelo código crescente" width="11" height="9" /></a>
                        <?php }; ?>
                        <?php if ($ordpor == 'codd') {?>
                        <img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_desc_m.png" alt="" width="11" height="9" />
                        <?php } else { ?>
                        <a href="?<?php echo http_build_query( $q_string )?>&op=codd" title="Ordenar pelo código decrescente" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_desc.png" alt="Ordenar pelo código decrescente" width="11" height="9" /></a>
                        <?php }; ?>
                    </th>
                    <th class="local_gsa">Cidade
                        <?php if ($ordpor == 'cida') {?>
                        <img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_asc_m.png" alt="" width="11" height="9" />
                        <?php } else { ?>
                        <a href="?<?php echo http_build_query( $q_string )?>&op=cida" title="Ordenar pela cidade crescente" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_asc.png" alt="Ordenar pela cidade crescente" width="11" height="9" /></a>
                        <?php }; ?>
                        <?php if ($ordpor == 'cidd') {?>
                        <img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_desc_m.png" alt="" width="11" height="9" />
                        <?php } else { ?>
                        <a href="?<?php echo http_build_query( $q_string )?>&op=cidd" title="Ordenar pela cidade decrescente" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_desc.png" alt="Ordenar pela cidade decrescente" width="11" height="9" /></a>
                        <?php }; ?>
                    </th>
                </tr>
                    <?php

                    $i = 1;

                    $classe = 'odd';

                    while( $d_gsa = $q_gsa->fetch_assoc() ) {

                        $classe == 'odd' ? $classe = 'even' : $classe = 'odd';

                        ?>
                <tr class="even_gsa">
                    <td class="num_od"><?php echo $i++; ?></td>
                    <td class="cod_gsa"> <?php echo $d_gsa['gsa_cidade_cod'];?> </td>
                    <td class="local_gsa"> <?php echo $d_gsa['gsa_cidade_nome'];?> </td>
                </tr>
                    <?php } // fim do while ?>
            </table>

<?php include 'footer.php'; ?>