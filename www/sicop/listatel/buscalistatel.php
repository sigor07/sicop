<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();

$campo_listatel = '';

if( !empty( $_GET['busca'] ) ) {

    $campo_listatel = get_get( 'campo_listatel', 'busca' );

    $q_listatel = "SELECT
                     `idlistatel`,
                     `tel_local`
                   FROM
                     `listatel`
                   WHERE
                     `tel_local` LIKE '%$campo_listatel%'
                   ORDER BY
                     `tel_local`";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_listatel = $model->query( $q_listatel );

    // fechando a conexao
    $model->closeConnection();

    if( !$q_listatel ) {

        echo msg_js( '', 1 );
        exit;

    }

    $querytime = $model->getQueryTime();

    $cont_listatel = $q_listatel->num_rows;

    $valor_busca = valor_user( $_GET );

    $mensagem = "[ BUSCA EFETUADA ] \n Busca na lista telefônica efetuada \n\n $valor_busca \n\n Página: $pag";
    salvaLog($mensagem);

}

$n_admsist = get_session( 'n_admsist', 'int' );

$desc_pag = 'Pesquisar na lista de telefones';

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>

            <p class="descript_page"> PESQUISAR LOCALIDADES </p>

            <form action="<?php echo $_SERVER['PHP_SELF']?>" method="get" name="buscalistatel" id="buscalistatel">

                <table class="busca_form">
                    <tr>
                        <td class="bf_lista_tel"> Digite o NOME da localidade: </td>
                    </tr>
                    <tr>
                        <td class="bf_lista_tel"><input name="campo_listatel" type="text" class="CaixaTexto" id="campo_listatel" onkeypress="return blockChars(event, 3);" value="<?php echo $campo_listatel ?>" size="50" /></td>
                    </tr>
                </table>

                <input type="hidden" name="busca" id="busca" value="busca" />

                <div class="form_bts">

                    <input class="form_bt" type="submit" name="busca" id="busca" value="Buscar" />
                </div>

            </form>

            <script type="text/javascript"> id("campo_listatel").select(); id("campo_listatel").focus(); </script>

<?php

    if ( empty( $_GET['busca'] ) ) {

        include 'footer.php';
        exit;

    }

    if( empty( $cont_listatel ) or $cont_listatel < 1 ) {

        $saida = '';
        $saida .= '<p class="p_q_no_result">Não foi encontrado nenhuma ocorrência.</p>';

        if ( $n_admsist >= 3 ) {
            $saida .= '<p class="link_common"><a href="cad_local_tel.php" title="Cadastrar nova localidade">Cadastrar</a></p>';
        }

        echo $saida;

        include 'footer.php';

        exit;

    }

?>
            <p class="p_q_info">
                Essa consulta retornou <?php echo $cont_listatel ?> registros (<?php echo round( $querytime, 2 ) ?> seg).
                <?php if ( $n_admsist >= 3 ) { ?>
                <a href='cad_local_tel.php' title="Cadastrar nova localidade">Cadastrar</a>
                <?php } ?>
            </p>

            <table class="lista_busca">
                <tr class="cab">
                    <th class="num_od">N</th>
                    <th class="local_listatel">Localidade</th>
                </tr>
                    <?php

                    $i = 1;

                    while( $d_listatel = $q_listatel->fetch_assoc() ) {

                        ?>
                <tr class="even">
                    <td class="num_od"><?php echo $i++; ?></td>
                    <td class="local_listatel"> <a href="detallistatel.php?idlt=<?php echo $d_listatel['idlistatel']; ?>" title="Clique para ver do detalhes desta localidade"><?php echo $d_listatel['tel_local'];?></a> </td>
                </tr>
                    <?php
                    } // fim do while
                    ?>
            </table><!-- fim da table class="lista_busca" -->

<?php include 'footer.php'; ?>