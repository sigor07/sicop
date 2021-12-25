<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_incl   = get_session( 'n_incl', 'int' );
$n_incl_n = 2;

if ( $n_incl < $n_incl_n ) {

    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = 'DETALHES DO RÁDIO';
    get_msg( $msg, 1 );

    exit;

}

$imp_incl = get_session( 'imp_incl', 'int' );

$idradio = get_get( 'idradio', 'int' );

if ( empty( $idradio ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página. Identificador do rádio em branco. ( DETALHES DO RÁDIO )";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$q_radio = "SELECT
              `detentos_radio`.`idradio`,
              `detentos_radio`.`cod_detento`,
              `detentos_radio`.`cod_cela`,
              `detentos_radio`.`marca_radio`,
              `detentos_radio`.`cor_radio`,
              `detentos_radio`.`lacre_1`,
              `detentos_radio`.`lacre_2`,
              `detentos_radio`.`user_add`,
              DATE_FORMAT( `detentos_radio`.`data_add`, '%d/%m/%Y às %H:%i' ) AS data_add,
              `detentos_radio`.`user_up`,
              DATE_FORMAT( `detentos_radio`.`data_up`, '%d/%m/%Y às %H:%i' ) AS data_up,
              `cela`.`cela`,
              `raio`.`raio`
            FROM
              `detentos_radio`
              LEFT JOIN `cela` ON `detentos_radio`.`cod_cela` = `cela`.`idcela`
              LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
            WHERE
              `detentos_radio`.`idradio` = $idradio
            LIMIT 1";

$query_obs = "SELECT
                `id_obs_radio`,
                `cod_radio`,
                `obs_radio`,
                `user_add`,
                DATE_FORMAT( `data_add`, '%d/%m/%Y' ) AS data_add_f,
                DATE_FORMAT( `data_add`, '%d/%m/%Y às %H:%i' ) AS data_add_fc,
                `data_add`,
                `user_up`,
                DATE_FORMAT( `data_up`, '%d/%m/%Y às %H:%i' ) AS data_up_f,
                `data_up`
              FROM
                `obs_radio`
              WHERE
                `cod_radio` = $idradio
              ORDER BY
                `data_add` DESC";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_radio = $model->query( $q_radio );

// fechando a conexao
$model->closeConnection();

if ( !$q_radio ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Falha na consulta ( DETALHES DO RÁDIO ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_radio = $q_radio->num_rows;

if ( $cont_radio < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'A consulta retornou 0 ocorrências ( DETALHES DO RÁDIO ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$d_radio = $q_radio->fetch_assoc();

$iddet = $d_radio['cod_detento'];

$user_add = '';
$user_up = '';

if ( !empty( $d_radio['user_add'] ) ) {
    $user_add = 'ID usuário: ' . $d_radio['user_add'] . ', em ' . $d_radio['data_add'];
}

if ( !empty( $d_radio['user_up'] ) ) {
    $user_up = 'ID usuário: ' . $d_radio['user_up'] . ', em ' . $d_radio['data_up'];
}

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Destalhes do rádio';


$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

require 'cab.php';
$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 4 );
$trail->output();

?>

            <p class="descript_page">DETALHES DO RÁDIO</p>
            <?php if ( $imp_incl == 1 ) {?>
            <p class="link_common"><a href="#" title="Imprimir a autorização deste rádio" onclick="javascript: ow('<?php echo SICOP_ABS_PATH; ?>print/aut_radio.php?idradio=<?php echo $idradio ?>', '600', '600'); return false" >Imprimir autorização</a></p>
            <?php } ?>
            <?php if ( $n_incl >= 3 ) {?>
            <p class="link_common">
                <a href='javascript:void(0)' onclick='envia_radio(<?php echo $d_radio['idradio'];?>)'>Vincular a outr<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L; ?></a>
                | <a href='editradio.php?idradio=<?php echo $idradio?>' title="Alterar dados desta rádio" >Alterar</a>
                <?php if ( $n_incl >= 4 ) {?>
                | <a href='javascript:void(0)' onclick='drop( "idradio", "<?php echo $idradio; ?>", "sendradio", "drop_radio", "2")' title="Excluir este rádio">Excluir</a>
                <?php }?>
            </p>
            <?php } ?>
            <?php
            if ( empty( $iddet ) ) {
                echo '<p class="p_q_no_result">Não há ' . SICOP_DET_DESC_L . ' responsável.</p>';
            } else {
                include 'quali/det_basic.php';
            }
            ?>

            <p class="table_leg">RÁDIO</p>

            <table class="lista_busca">

                <tr bgcolor="#FAFAFA">
                    <td width="179" height="20" align="center" >Marca: <?php echo $d_radio['marca_radio'] ?></td>
                    <td width="180" align="center">Cor: <?php echo $d_radio['cor_radio'] ?></td>
                    <td width="179" align="center"><?php echo SICOP_RAIO ?>: <?php echo $d_radio['raio'] ?> - <?php echo SICOP_CELA ?>: <?php echo $d_radio['cela'] ?></td>
                    <td width="181" align="center">Lacres: <?php echo $d_radio['lacre_1'] ?> / <?php echo $d_radio['lacre_2'] ?></td>
                </tr>

                <tr bgcolor="#FAFAFA">
                    <td height="20" colspan="2" align="center" class="paragrafo10negrito">CADASTRAMENTO</td>
                    <td height="20" colspan="2" align="center" class="paragrafo10negrito">ÚLTIMA ATUALIZAÇÃO</td>
                </tr>

                <tr bgcolor="#FAFAFA">
                    <td height="20" colspan="2" align="center"><?php echo $user_add ?></td>
                    <td height="20" colspan="2" align="center"><?php echo $user_up ?></td>
                </tr>

            </table>

            <div id="obs"></div>
            <div class="linha">
                OBSERVAÇÕES <?php if ( $n_incl >= 3 ) { ?> - <a href="cadobsradio.php?idradio=<?php echo $d_radio['idradio'] ?>" title="Adicionar uma observação para este rádio">Adicionar observação</a> <a href="#" title="Abrir em outra janela" onClick="javascript: ow('cadobsradio.php?idradio=<?php echo $d_radio['idradio']; ?>&targ=1', '800', '450'); return false" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>user_popup.png" alt="Abrir em outra janela" class="icon_popup" /></a><?php }; ?>
                <hr />
            </div>
            <?php

            // instanciando o model
            $model = SicopModel::getInstance();

            // executando a query
            $query_obs = $model->query( $query_obs );

            // fechando a conexao
            $model->closeConnection();

            $cont_obs = 0;

            if( $query_obs ) $cont_obs = $query_obs->num_rows;

            if ( $cont_obs < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não há observações.</p>';
            } else {
            ?>

            <table class="lista_busca">
                <tr >
                    <th class="desc_data">DATA</th>
                    <th class="desc_obs">OBSERVAÇÃO</th>
                    <th class="tb_bt">&nbsp;</th>
                    <th class="tb_bt">&nbsp;</th>
                </tr>
                <?php while ( $dados_obs = $query_obs->fetch_assoc() ) { ?>
                <tr class="even">
                    <td class="desc_data"><?php echo $dados_obs['data_add_f'] ?></td>
                    <td class="desc_obs"><?php echo nl2br( $dados_obs['obs_radio'] ) ?></td>
                    <td class="tb_bt"><?php if ( $n_incl >= 3 ) {  ?><a href="editobsradio.php?idobs=<?php echo $dados_obs['id_obs_radio']; ?>" title="Alterar esta observação" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" alt="" /></a><?php }; ?></td>
                    <td class="tb_bt"><?php if ( $n_incl >= 4 ) {  ?><a href="delobsradio.php?idobs=<?php echo $dados_obs['id_obs_radio']; ?>" title="Excluir esta observação" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="" /></a><?php }; ?></td>
                </tr>
                <tr>
                    <td colspan="4" class="desc_user">Cadastrado em <?php echo $dados_obs['data_add_fc'] ?>, usuário <?php echo $dados_obs['user_add'] ?><?php if ($dados_obs['user_up'] and $dados_obs['data_up_f']) {?> - Atualizado em <?php echo $dados_obs['data_up_f'] ?>, usuário <?php echo $dados_obs['user_up'] ?> <?php }?></td>
                </tr>
                    <?php } // fim do while ?>
            </table>
                <?php } // fim do if que conta o número de ocorrencias ?>

<?php include 'footer.php'; ?>