<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_sind   = get_session( 'n_sind', 'int' );
$n_sind_n = 2;

if ( $n_sind < $n_sind_n ) {
    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
    salvaLog( $mensagem );
    exit;
}

$iddet = get_get( 'iddet', 'int' );

if ( empty( $iddet ) ){
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso direto à página do rol de visitas.\n\n Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( '', 1 );
    exit;
}

$querysind = "SELECT
                `sindicancias`.`idsind`,
                `sindicancias`.`cod_detento`,
                `sindicancias`.`num_pda`,
                `sindicancias`.`ano_pda`,
                `sindicancias`.`local_pda`,
                DATE_FORMAT( `sindicancias`.`data_ocorrencia`, '%d/%m/%Y' ) AS data_ocorrencia,
                `sindicancias`.`sit_pda`,
                `tipositdet`.`situacaodet`,
                `sindicancias`.`data_reabilit`,
                DATE_FORMAT( `sindicancias`.`data_reabilit`, '%d/%m/%Y' ) AS data_reab_f
              FROM
                `sindicancias`
                LEFT JOIN `tipositdet` ON `sindicancias`.`cod_sit_detento` = `tipositdet`.`idsitdet`
              WHERE
                `sindicancias`.`cod_detento` = $iddet
              ORDER BY
                `sindicancias`.`ano_pda`, `sindicancias`.`num_pda`";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$querysind = $model->query( $querysind );

// fechando a conexao
$model->closeConnection();

if( !$querysind ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta ( ROL DE PDA ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Rol de pda';

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 4 );
$trail->output();
?>

            <p class="descript_page">PDAs CADASTRADOS</p>

            <?php include 'quali/det_cad.php'; ?>

            <div class="linha">
                SINDICÂNCIAS (<font color="#FF0000">em reabilitação</font>) <?php if ( $n_sind >= 3 && !empty( $d_det['matricula'] ) ) {  ?> - <a href="cadpda.php?iddet=<?php echo $d_det['iddetento'] ?>">Cadastrar sindicância</a><?php }; ?>
                <hr />
            </div>
            <?php
                $conts = $querysind->num_rows;
                if( $conts < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                    echo '<p class="p_q_no_result">Nada consta.</p>';
                } else {
                    ?>
            <table class="lista_busca">
                <tr >
                    <th class="num_pda">NÚMERO DO PDA</th>
                    <th class="data_oc">DATA DA OCORRÊNCIA</th>
                    <th class="sit_pda">SITUAÇÃO DO PDA</th>
                    <th class="sit_det_pda">SITUAÇÃO D<?php echo SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U  ?></th>
                    <th class="data_reab">REABILIATAÇÃO EM</th>
                </tr>
                <?php
                while ( $dadoss = $querysind->fetch_assoc() ) {

                    $numpda = format_num_pda( $dadoss['num_pda'], $dadoss['ano_pda'], $dadoss['local_pda'] );

                    $corfonts = muda_cor_pda( $dadoss['data_reabilit'], $dadoss['sit_pda'] );

                    ?>
                <tr class="even">
                    <td class="num_pda"><a href="<?php echo SICOP_ABS_PATH ?>sind/detalpda.php?idsind=<?php echo $dadoss['idsind'] ?>"><?php echo $numpda ?></a></td>
                    <td class="data_oc"><font color="<?php echo $corfonts;?>"><?php echo $dadoss['data_ocorrencia'] ?></font></td>
                    <td class="sit_pda"><font color="<?php echo $corfonts;?>"><?php echo trata_sit_pda($dadoss['sit_pda']) ?></font></td>
                    <td class="sit_det_pda"><font color="<?php echo $corfonts;?>"><?php echo $dadoss['situacaodet'] ?></font></td>
                    <td class="data_reab"><font color="<?php echo $corfonts;?>"><?php echo $dadoss['data_reab_f'] ?></font></td>
                </tr>
                        <?php } // fim do while ?>
            </table>
                    <?php } // fim do if que conta o número de ocorrencias ?>

<?php include 'footer.php'; ?>