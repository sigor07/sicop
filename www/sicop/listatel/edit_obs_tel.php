<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_admsist = get_session( 'n_admsist', 'int' );

if ( empty( $n_admsist ) or $n_admsist < 3 ) {
    $tipo = 0;
    include '../init/msgnopag.php';
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> -> Tentativa de acesso à página SEM PERMISSÕES. \n\n Página: $pag";
    salvaLog($mensagem);
    exit;
}

$idobs = get_get( 'idobs', 'int' );

if ( empty( $idobs ) ){
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso direto à página de alteração de observação de telefone.\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$query_obs = "SELECT
                `obs_listatel`.`id_obs_listatel`,
                `obs_listatel`.`cod_listatel`,
                `obs_listatel`.`obs_listatel`,
                `listatel`.`tel_local`
              FROM
                `obs_listatel`
                INNER JOIN `listatel` ON `obs_listatel`.`cod_listatel` = `listatel`.`idlistatel`
              WHERE
                `obs_listatel`.`id_obs_listatel` = $idobs
              LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_obs = $model->query( $query_obs );

// fechando a conexao
$model->closeConnection();

$cont_obs = 0;

if( !$query_obs ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_obs = $query_obs->num_rows;

if( $cont_obs < 1 ) {
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ocorrências ( ALTERAÇÃO DE OBSERVAÇÃO - LISTA DE TELEFONES ).\n\n Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$d_obs = $query_obs->fetch_assoc();

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Alterar observação';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) {
    $pag_atual .=  '?' . $qs;
}

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 5 );
$trail->output();
?>

            <p class="descript_page">ALTERAR OBSERVAÇÃO</p>

            <table class="detal_lt">
                <tr>
                    <td class="local_lt_med">Localidade: <?php echo $d_obs['tel_local']; ?></td>
                </tr>
            </table><!-- fim da <table class="detal_lt"> -->

            <p align="center">Observações:</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendlistatelobs.php" method="post" name="obs_tel" id="obs_tel" >
                <div align="center">
                    <textarea name="obs_listatel" cols="75" rows="5" class="CaixaTexto" id="obs_listatel" onkeypress="return blockChars(event, 4);"><?php echo $d_obs['obs_listatel'];?></textarea>
                </div>

                <input name="id_obs_listatel" type="hidden" id="id_obs_listatel" value="<?php echo $d_obs['id_obs_listatel'];?>" />
                <input name="proced" type="hidden" id="proced" value="1" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" onclick="return valida_obs( 'obs_listatel' );" value="Alterar" />
                    <input class="form_bt" type="button" name="" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form><!-- /form ... -->

            <script type="text/javascript">id("obs_listatel").focus()</script>

<?php include 'footer.php'; ?>