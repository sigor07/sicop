<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_pront   = get_session( 'n_pront', 'int' );
$n_pront_n = 3;

if ( $n_pront < $n_pront_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'ALTERAÇÃO DE OBSERVAÇÃO - GRADE';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$idobs = get_get( 'idobs', 'int' );

if ( empty( $idobs ) ){
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso direto à página de alteração de observação de grade.\n\n Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$query_obs = "SELECT
                `id_obs_grade`,
                `cod_detento`,
                `obs_grade`
              FROM
                `obs_grade`
              WHERE
                `id_obs_grade` = $idobs
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

    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ocorrências ( ALTERAÇÃO DE OBSERVAÇÃO - GRADE ).\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$d_obs = $query_obs->fetch_assoc();

$iddet = $d_obs['cod_detento'];

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
    $pag_atual .= '?' . $qs;
}

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 5 );
$trail->output();
?>

            <p class="descript_page">ALTERAR OBSERVAÇÃO</p>

            <?php include 'quali/det_basic.php'; ?>

            <p class="table_leg">Observações:</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendgradeobs.php" method="post" name="editobsgrade" id="editobsgrade">

                <div align="center">
                    <textarea name="obs_grade" cols="75" rows="5" class="CaixaTexto" id="obs_grade" onkeypress="return blockChars(event, 4);"><?php echo $d_obs['obs_grade'];?></textarea>
                </div>

                <script type="text/javascript">id("obs_grade").focus();</script>

                <input type="hidden" name="iddet" id="iddet" value="<?php echo $iddet;?>" />
                <input type="hidden" name="id_obs_grade" id="id_obs_grade" value="<?php echo $d_obs['id_obs_grade'];?>" />
                <input type="hidden" name="proced" id="proced" value="1" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" onclick="return valida_obs( 'obs_grade' );" value="Alterar" />
                    <input class="form_bt" type="button" name="" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

<?php include 'footer.php'; ?>