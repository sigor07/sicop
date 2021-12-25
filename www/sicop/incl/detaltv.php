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
    $msg['entre_ch'] = 'DETALHES DA TV';
    get_msg( $msg, 1 );

    exit;

}

$imp_incl = get_session( 'imp_incl', 'int' );

$idtv = get_get( 'idtv', 'int' );

if ( empty( $idtv ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página. Identificador da tv em branco. ( DETALHES DA TV )";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}


$q_tv = "SELECT
           `detentos_tv`.`idtv`,
           `detentos_tv`.`cod_detento`,
           `detentos_tv`.`cod_cela`,
           `detentos_tv`.`marca_tv`,
           `detentos_tv`.`cor_tv`,
           `detentos_tv`.`polegadas`,
           `detentos_tv`.`lacre_1`,
           `detentos_tv`.`lacre_2`,
           `detentos_tv`.`user_add`,
           DATE_FORMAT( `detentos_tv`.`data_add`, '%d/%m/%Y às %H:%i' ) AS data_add,
           `detentos_tv`.`user_up`,
           DATE_FORMAT( `detentos_tv`.`data_up`, '%d/%m/%Y às %H:%i' ) AS data_up,
           `cela`.`cela`,
           `raio`.`raio`
         FROM
           `detentos_tv`
           LEFT JOIN `cela` ON `detentos_tv`.`cod_cela` = `cela`.`idcela`
           LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
         WHERE
           `detentos_tv`.`idtv` = $idtv
         LIMIT 1";

$query_obs = "SELECT
                `id_obs_tv`,
                `cod_tv`,
                `obs_tv`,
                `user_add`,
                DATE_FORMAT(`data_add`, '%d/%m/%Y') AS data_add_f,
                DATE_FORMAT(`data_add`, '%d/%m/%Y às %H:%i') AS data_add_fc,
                `data_add`,
                `user_up`,
                DATE_FORMAT(`data_up`, '%d/%m/%Y às %H:%i') AS data_up_f,
                `data_up`
              FROM
                `obs_tv`
              WHERE
                `cod_tv` = $idtv
              ORDER BY
                `data_add` DESC";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_tv = $model->query( $q_tv );

// fechando a conexao
$model->closeConnection();

if ( !$q_tv ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Falha na consulta ( DETALHES DA TV ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_tv = $q_tv->num_rows;

if ( $cont_tv < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'A consulta retornou 0 ocorrências ( DETALHES DA TV ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$d_tv = $q_tv->fetch_assoc();

$iddet = $d_tv['cod_detento'];

$user_add = '';
$user_up = '';

if ( !empty( $d_tv['user_add'] ) ) {
    $user_add = 'ID usuário: ' . $d_tv['user_add'] . ', em ' . $d_tv['data_add'];
}

if ( !empty( $d_tv['user_up'] ) ) {
    $user_up = 'ID usuário: ' . $d_tv['user_up'] . ', em ' . $d_tv['data_up'];
}

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Destalhes da TV';


$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

require 'cab.php';
$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 4 );
$trail->output();

?>

            <p class="descript_page">DETALHES DA TV</p>

            <?php if ( $imp_incl == 1 ) {?>
            <p class="link_common"><a href="#" title="Imprimir a autorização desta TV" onclick="javascript: ow('<?php echo SICOP_ABS_PATH; ?>print/aut_tv.php?idtv=<?php echo $idtv ?>', '600', '600'); return false" >Imprimir autorização</a></p>
            <?php } ?>

            <?php if ( $n_incl >= 3 ) {?>
            <p class="link_common">
                <a href='javascript:void(0)' onclick='envia_tv(<?php echo $d_tv['idtv'];?>)'>Vincular a outr<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L; ?></a>
                | <a href='edittv.php?idtv=<?php echo $idtv?>' title="Alterar dados desta TV" >Alterar</a>
                <?php if ( $n_incl >= 4 ) {?>
                | <a href='javascript:void(0)' onclick='drop( "idtv", "<?php echo $idtv; ?>", "sendtv", "drop_tv", "2")' title="Excluir esta TV">Excluir</a>
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


            <p class="table_leg">TV</p>

            <table class="lista_busca">

                <tr bgcolor="#FAFAFA">
                    <td width="179" height="20" align="center" >Marca: <?php echo $d_tv['marca_tv'] ?></td>
                    <td width="180" align="center">Cor: <?php echo $d_tv['cor_tv'] ?></td>
                    <td width="179" align="center"><?php echo SICOP_RAIO ?>: <?php echo $d_tv['raio'] ?> - <?php echo SICOP_CELA ?>: <?php echo $d_tv['cela'] ?></td>
                    <td width="181" align="center">Lacres: <?php echo $d_tv['lacre_1'] ?> / <?php echo $d_tv['lacre_2'] ?></td>
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
                OBSERVAÇÕES <?php if ( $n_incl >= 3 ) { ?> - <a href="cadobstv.php?idtv=<?php echo $d_tv['idtv'] ?>" title="Adicionar uma observação para esta TV">Adicionar observação</a> <a href="#" title="Abrir em outra janela" onClick="javascript: ow('cadobstv.php?idtv=<?php echo $d_tv['idtv']; ?>&targ=1', '800', '450'); return false" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>user_popup.png" alt="Abrir em outra janela" class="icon_popup" /></a><?php }; ?>
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

            if($cont_obs < 1) { // se o número de ocorrências for menor do que 1, mostra a mensagem
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
                    <td class="desc_obs"><?php echo nl2br( $dados_obs['obs_tv'] ) ?></td>
                    <td class="tb_bt"><?php if ( $n_incl >= 3 ) { ?><a href="editobstv.php?idobs=<?php echo $dados_obs['id_obs_tv']; ?>" title="Alterar esta observação" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_edit.png" alt="" /></a><?php }; ?></td>
                    <td class="tb_bt"><?php if ( $n_incl >= 4 ) { ?><a href="delobstv.php?idobs=<?php echo $dados_obs['id_obs_tv']; ?>" title="Excluir esta observação" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>b_drop.png" alt="" /></a><?php }; ?></td>
                </tr>
                <tr>
                    <td colspan="4" class="desc_user">Cadastrado em <?php echo $dados_obs['data_add_fc'] ?>, usuário <?php echo $dados_obs['user_add'] ?><?php if ( $dados_obs['user_up'] and $dados_obs['data_up_f'] ) { ?> - Atualizado em <?php echo $dados_obs['data_up_f'] ?>, usuário <?php echo $dados_obs['user_up'] ?> <?php } ?></td>
                </tr>
                <?php } // fim do while ?>
            </table>
            <?php } // fim do if que conta o número de ocorrencias ?>

<?php include 'footer.php'; ?>