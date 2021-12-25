<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_sedex   = get_session( 'n_sedex', 'int' );
$n_sedex_n = 2;

$n_portaria = get_session( 'n_portaria', 'int' );


if ( $n_sedex < $n_sedex_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'ROL DE SEDEX';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$iddet = get_get( 'iddet', 'int' );

if ( empty( $iddet ) ){

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso direto à página de rol de sedex.';
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$q_sedex = "SELECT
              `idsedex`,
              `cod_sedex`,
              `sit_sedex`,
              DATE_FORMAT( `data_add`, '%d/%m/%Y' ) AS data_sedex
            FROM
              `sedex`
            WHERE
              `cod_detento` = $iddet
            ORDER BY
              `data_add` DESC";

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Rol de Sedex';

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 4);
$trail->output();
?>

            <p class="descript_page">ROL DE SEDEX</p>

            <?php include 'quali/det_cad.php'; ?>

            <div class="linha">
                SEDEX CADASTRADOS <?php if ( $n_sedex >= 3 and $n_portaria >= 3 and !empty( $d_det['matricula'] ) ) {  ?> - <a href="sedex_in.php?iddet=<?php echo $iddet ?>">Cadastrar sedex</a><?php }; ?>
                <hr />
            </div>

            <?php
            // instanciando o model
            $model = SicopModel::getInstance();

            // executando a query
            $q_sedex = $model->query( $q_sedex );

            // fechando a conexao
            $model->closeConnection();

            $cont_ms = 0;

            if( $q_sedex ) $cont_ms = $q_sedex->num_rows;

            if( $cont_ms < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não há dados.</p>';
            } else {
            ?>

            <table class="lista_busca">
                <tr>
                    <th class="desc_data">DATA</th>
                    <th class="cod_sedex_small">CÓDIGO</th>
                    <th class="sit_sedex">SITUAÇÃO</th>
                </tr>
                <?php while( $d_sedex = $q_sedex->fetch_assoc() ) { ?>
                <tr class="even">
                    <td class="desc_data"><?php echo $d_sedex['data_sedex']; ?></td>
                    <td class="cod_sedex_small"><a href="detalsedex.php?ids=<?php echo $d_sedex['idsedex'] ;?>" ><?php echo formata_num_sedex ( $d_sedex['cod_sedex'] ); ?></a></td>
                    <td class="sit_sedex"><?php echo trata_sit_sedex( $d_sedex['sit_sedex'] ); ?></td>
                </tr>
              <?php } ?>

            </table>

            <?php } ?>

<?php include 'footer.php';?>