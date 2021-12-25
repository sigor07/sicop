<?php

if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_incl   = get_session( 'n_incl', 'int' );
$n_incl_n = 4;

if ( $n_incl < $n_incl_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'EXCLUSÃO DE OBSERVAÇÃO - TV';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$idobs = empty( $_GET['idobs'] ) ? '' : (int)$_GET['idobs'];

if ( empty( $idobs ) ) {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso direto à página de exclusão de observação de TV.\n\n Página: $pag";
    salvaLog($mensagem);
    echo '<script type="text/javascript">history.go(-1);</script>';
    exit;
}

$query_obs = "SELECT
                `id_obs_tv`,
                `cod_tv`,
                `obs_tv`
              FROM
                `obs_tv`
              WHERE
                `id_obs_tv` = $idobs
              LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_obs = $model->query( $query_obs );

// fechando a conexao
$model->closeConnection();

if( !$query_obs ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_obs = $query_obs->num_rows;

if( $cont_obs < 1 ) {
    $mensagem = "A consulta retornou 0 ocorrencias (observações).\n\n Página $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$d_obs = $query_obs->fetch_assoc();

$idtv = $d_obs['cod_tv'];

$q_tv = "SELECT
            `detentos_tv`.`idtv`,
            `detentos_tv`.`cod_detento`,
            `detentos_tv`.`cod_cela`,
            `detentos_tv`.`marca_tv`,
            `detentos_tv`.`cor_tv`,
            `detentos_tv`.`polegadas`,
            `detentos_tv`.`lacre_1`,
            `detentos_tv`.`lacre_2`,
            `cela`.`cela`,
            `raio`.`raio`
          FROM
            `detentos_tv`
            LEFT JOIN `cela` ON `detentos_tv`.`cod_cela` = `cela`.`idcela`
            LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
          WHERE
            `detentos_tv`.`idtv` = $idtv
          LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_tv = $model->query( $q_tv );

// fechando a conexao
$model->closeConnection();

if( !$q_tv ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_obs = $query_obs->num_rows;

if( $cont_obs < 1 ) {
    $mensagem = "A consulta retornou 0 ocorrencias (TV).\n\n Página $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$d_tv = $q_tv->fetch_assoc();

$iddet = $d_tv['cod_detento'];

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Excluir observação de TV';

require 'cab.php';
?>

            <p class="descript_page">EXCLUIR OBSERVAÇÃO</p>

            <p class="table_leg">TV</p>

            <table class="lista_busca">
                <tr bgcolor="#FAFAFA">
                    <td width="179" height="20" align="center">Marca: <?php echo $d_tv['marca_tv'] ?></td>
                    <td width="180" align="center">Cor: <?php echo $d_tv['cor_tv'] ?></td>
                    <td width="179" align="center"><?php echo SICOP_RAIO ?>: <?php echo $d_tv['raio'] ?> - <?php echo SICOP_CELA ?>: <?php echo $d_tv['cela'] ?></td>
                    <td width="181" align="center">Lacres: <?php echo $d_tv['lacre_1'] ?> / <?php echo $d_tv['lacre_2'] ?></td>
                </tr>
            </table>

            <?php include 'quali/det_basic.php'; ?>

            <p class="table_leg">Observação</p>

            <div class="one_detal"><?php echo nl2br( $d_obs['obs_tv'] ) ?> </div>

            <p class="confirm_ask">Tem certeza de que deseja excluir esta observação?</p>

            <p class="table_leg"><img src="<?php echo SICOP_SYS_IMG_PATH; ?>s_attention.png" alt="Atenção" class="icon_alert" />&nbsp;ATENÇÃO -> Você <b>não poderá</b> desfazer essa operação.</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendtvobs.php" method="post" name="delobs" id="delobs" >

                <input name="idtv" type="hidden" id="idtv" value="<?php echo $idtv;?>">
                <input name="iddet" type="hidden" id="iddet" value="<?php echo $iddet;?>">
                <input name="id_obs_tv" type="hidden" id="id_obs_tv" value="<?php echo $d_obs['id_obs_tv']; ?>" />
                <input name="proced" type="hidden" id="proced" value="2">

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Excluir" />
                    <input class="form_bt" name="" type="button" onClick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

<?php include 'footer.php'; ?>