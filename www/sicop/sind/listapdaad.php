<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_sind   = get_session( 'n_sind', 'int' );
$n_sind_n = 2;

$motivo_pag = 'LISTAR PDAs DE AUTORIA DESCONHECIDA';

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

$ordpor = 'pdaa';

if ( !empty( $_GET['op'] ) ) {
    $ordpor = get_get( 'op', 'busca' );
}

$ordpor = tratabusca($ordpor);

switch ( $ordpor ) {
    default:
    case 'pdaa':
        $ordbusca = "sindicancias.ano_pda ASC, sindicancias.num_pda ASC";
        break;
    case 'pdad':
        $ordbusca = "sindicancias.ano_pda DESC, sindicancias.num_pda DESC";
        break;
}

$query = "SELECT
            idsind,
            num_pda,
            ano_pda,
            local_pda,
            sit_pda,
            data_reabilit
          FROM
            sindicancias
          WHERE
            ISNULL(cod_detento)
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

$mensagem = "Acesso à página: $pag";
salvaLog($mensagem);

$desc_pag = 'PDAs de autoria desconhecida';

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( 'PDAs de autoria desconhecida', $pag_atual, 3 );
$trail->output();
?>

            <p class="descript_page">LISTA DE PDAs DE AUTORIA DESCONHECIDA</p>

            <?php
            if ( empty( $cont ) or $cont < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não foi encontrado nenhuma ocorrência.</p>';
                exit;
            }
            ?>
            <p class="p_q_info">Essa consulta retornou <?php echo $cont ?> registros (<?php echo round( $querytime, 2 ) ?> seg). <a href="<?php echo SICOP_ABS_PATH ?>buscadet.php?proced=bsind">Pesquisar PDAs</a></p>

            <table class="lista_busca">
                <tr>
                    <th class="num_od">N</th>
                    <th class="nome_det"></th>
                    <th class="desc_pda">Número do PDA
                        <?php echo link_ord_asc( $ordpor, 'pda', $q_string, 'número do PDA' ) ?>
                        <?php echo link_ord_desc( $ordpor, 'pda', $q_string, 'número do PDA' ) ?>
                    </th>
                </tr>
                    <?php
                    $i = 1;

                    while ( $dados = $query->fetch_assoc(  ) ) {

                        $numpda = format_num_pda( $dados['num_pda'], $dados['ano_pda'], $dados['local_pda'] );

                        $corfonts = muda_cor_pda( $dados['data_reabilit'], $dados['sit_pda'] );

                        ?>

                    <tr class="even">
                        <td class="num_od"><?php echo $i++; ?></td>
                        <td class="nome_det">AUTORIA DESCONHECIDA</td>
                        <td class="desc_pda"><font color="<?php echo $corfonts; ?>"><?php echo $numpda; ?></font> <a href="detalpda.php?idsind=<?php echo $dados['idsind'] ?>" title="Ver detalhes deste PDA"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_view.png" alt="Ver detalhes deste PDA" class="icon_view" /></a></td>
                    </tr>
                    <?php } // fim do while  ?>

            </table>

<?php include 'footer.php'; ?>