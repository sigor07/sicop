<?php

if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$n_inteli   = get_session( 'n_inteli', 'int' );
$n_inteli_n = 3;

$motivo_pag = 'ALTERAÇÃO DE OBSERVAÇÃO - TV';

if ( $n_inteli < $n_inteli_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'ALTERAÇÃO DE OBSERVAÇÃO - TV';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$idobs = get_get( 'idobs', 'int' );
if ( empty( $idobs ) ) {


    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( IDENTIFICADOR EM BRANCO - $motivo_pag ).";
    get_msg( $msg, 1 );


    echo msg_js( '', 1 );


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

if($cont_obs < 1) {
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

$cont_tv = $q_tv->num_rows;

if($cont_tv != 1) {
    $mensagem = "A consulta retornou 0 ocorrencias (TV).\n\n Página $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$d_tv = $q_tv->fetch_assoc();

$iddet = $d_tv['cod_detento'];

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Alterar observação de TV';

// adicionando o javascript
$cab_js   = array();
$cab_js[] = 'valida.js';
$cab_js[] = 'ckeditor/ckeditor.js';
set_cab_js( $cab_js );

require 'cab.php';
?>


    <p class="descript_page">ALTERAR OBSERVAÇÃO DE TV</p>

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

    <p class="table_leg">Observações:</p>

    <form action="<?php echo SICOP_ABS_PATH ?>send/sendtvobs.php" method="post" name="editobs" id="editobs" onSubmit="return validacadobstv()">

        <div align="center">
            <textarea name="obs_tv" cols="75" rows="5" class="CaixaTexto" id="obs_tv" onkeypress="return blockChars(event, 4);"><?php echo $d_obs['obs_tv'];?></textarea>
        </div>

        <input name="iddet" type="hidden" id="iddet" value="<?php echo $iddet;?>"/>
        <input name="idtv" type="hidden" id="idtv" value="<?php echo $idtv;?>"/>
        <input name="id_obs_tv" type="hidden" id="id_obs_tv" value="<?php echo $d_obs['id_obs_tv'];?>"/>
        <input name="proced" type="hidden" id="proced" value="1"/>

        <div class="form_bts">
            <input class="form_bt" type="submit" name="submit" id="submit" value="Alterar" />
            <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
        </div>

    </form>

    <script type="text/javascript"> id('obs_tv').focus(); </script>

<?php include 'footer.php'; ?>