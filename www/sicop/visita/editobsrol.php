<?php
if ( !isset( $_SESSION ) ) session_start();

$pag = '';
$tipo = '';

require '../init/config.php';
require 'incl_complete.php';

$n_rol   = get_session( 'n_rol', 'int' );
$n_rol_n = 3;

if ( $n_rol < $n_rol_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'ALTERAÇÃO DE OBSERVAÇÃO - ROL DE VISITAS';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$idobs = get_get( 'idobs', 'int' );

if ( empty( $idobs ) ) {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso direto à página de alteração de observação de rol de visitas.\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( '', 1 );
    exit;
}

$query_obs = "SELECT
                `id_obs_rol`,
                `cod_detento`,
                `obs_rol`
              FROM
                `obs_rol`
              WHERE
                `id_obs_rol` = $idobs
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

    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ocorrências ( ALTERAÇÃO DE OBSERVAÇÃO - ROL DE VISITAS ).\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$dados_obs = $query_obs->fetch_assoc();

$iddet = $dados_obs['cod_detento'];

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Alterar observação';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 5 );
$trail->output();
?>

            <p class="descript_page">ALTERAR OBSERVAÇÃO DE ROL DE VISITAS</p>

            <?php include 'quali/det_basic.php'; ?>

            <p class="table_leg">Observação:</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendrolobs.php" method="post" name="cadobsrol" id="cadobsrol">

                <div align="center">
                    <textarea name="obs_rol" cols="75" rows="5" class="CaixaTexto" id="obs_rol" onkeypress="return blockChars(event, 4);"><?php echo $dados_obs['obs_rol'];?></textarea>
                </div>

                <input type="hidden" name="id_obs_rol" id="id_obs_rol" value="<?php echo $dados_obs['id_obs_rol'];?>" />
                <input type="hidden" name="proced" id="proced" value="1" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Alterar" />
                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

            <script type="text/javascript">

                id( "obs_rol" ).focus()

                $(function() {
                    $("form").submit(function() {
                        if ( valida_obs( 'obs_rol' ) == true ) {
                            // ReadOnly em todos os inputs
                            $("input", this).attr("readonly", true);
                            // Desabilita os submits
                            $("input[type='submit'],input[type='image']", this).attr("disabled", true);
                            return true;
                        } else {
                            return false;
                        }
                    });
                });

            </script>

<?php include 'footer.php'; ?>