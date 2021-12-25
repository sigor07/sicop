<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_inteli   = get_session( 'n_inteli', 'int' );
$n_inteli_n = 3;

$motivo_pag = 'ALTERAÇÃO DE OBSERVAÇÃO - RÁDIO';

if ( $n_inteli < $n_inteli_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
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
                `id_obs_radio`,
                `cod_radio`,
                `obs_radio`
              FROM
                `obs_radio`
              WHERE
                `id_obs_radio` = $idobs
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
    $mensagem = "A consulta retornou 0 ocorrencias ( $motivo_pag ).\n\n Página $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$d_obs = $query_obs->fetch_assoc();

$idradio = $d_obs['cod_radio'];

$q_radio = "SELECT
            `detentos_radio`.`idradio`,
            `detentos_radio`.`cod_detento`,
            `detentos_radio`.`cod_cela`,
            `detentos_radio`.`marca_radio`,
            `detentos_radio`.`cor_radio`,
            `detentos_radio`.`lacre_1`,
            `detentos_radio`.`lacre_2`,
            `cela`.`cela`,
            `raio`.`raio`
          FROM
            `detentos_radio`
            LEFT JOIN `cela` ON `detentos_radio`.`cod_cela` = `cela`.`idcela`
            LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
          WHERE
            `detentos_radio`.`idradio` = $idradio
          LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_radio = $model->query( $q_radio );

// fechando a conexao
$model->closeConnection();

if( !$q_radio ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_radio = $q_radio->num_rows;

if ( $cont_radio != 1 ) {

    $mensagem = "A consulta retornou 0 ocorrencias ( $motivo_pag ).\n\n Página $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$d_radio = $q_radio->fetch_assoc();

$iddet = $d_radio['cod_detento'];

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Alterar observação de rádio';

// adicionando o javascript
$cab_js   = array();
$cab_js[] = 'valida.js';
$cab_js[] = 'ckeditor/ckeditor.js';
set_cab_js( $cab_js );

require 'cab.php';
?>

    <p class="descript_page">ALTERAR OBSERVAÇÃO DE RÁDIO</p>

    <p class="table_leg">RÁDIO</p>

    <table class="lista_busca">
        <tr bgcolor="#FAFAFA">
            <td width="179" height="20" align="center">Marca: <?php echo $d_radio['marca_radio'] ?></td>
            <td width="180" align="center">Cor: <?php echo $d_radio['cor_radio'] ?></td>
            <td width="179" align="center"><?php echo SICOP_RAIO ?>: <?php echo $d_radio['raio'] ?> - <?php echo SICOP_CELA ?>: <?php echo $d_radio['cela'] ?></td>
            <td width="181" align="center">Lacres: <?php echo $d_radio['lacre_1'] ?> / <?php echo $d_radio['lacre_2'] ?></td>
        </tr>
    </table>

    <?php include 'quali/det_basic.php'; ?>

    <p class="table_leg">Observações:</p>

    <form action="<?php echo SICOP_ABS_PATH ?>send/sendradioobs.php" method="post" name="editobs" id="editobs" onSubmit="return validacadobsradio()">

        <div align="center">
            <textarea name="obs_radio" cols="75" rows="5" class="CaixaTexto" id="obs_radio" onkeypress="return blockChars(event, 4);"><?php echo $d_obs['obs_radio'];?></textarea>
        </div>

        <input name="iddet" type="hidden" id="iddet" value="<?php echo $iddet;?>" />
        <input name="idradio" type="hidden" id="idradio" value="<?php echo $idradio;?>" />
        <input name="id_obs_radio" type="hidden" id="id_obs_radio" value="<?php echo $d_obs['id_obs_radio'];?>" />
        <input name="proced" type="hidden" id="proced" value="1" />

        <div class="form_bts">
            <input class="form_bt" type="submit" name="submit" id="submit" value="Alterar" />
            <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
        </div>

    </form>

    <script type="text/javascript"> id('obs_radio').focus(); </script>

<?php include 'footer.php'; ?>