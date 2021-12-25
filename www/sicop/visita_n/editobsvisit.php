<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_rol   = get_session( 'n_rol', 'int' );
$n_rol_n = 3;

if ($n_rol < $n_rol_n) {
    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
    salvaLog( $mensagem );
    exit;
}

$idobs = get_get( 'idobs', 'int' );

if ( empty( $idobs ) ){
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso direto à página de alteração de observação do visitante.\n\n Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( '', 1 );
    exit;
}

$query_obs = "SELECT
                `id_obs_visit`,
                `cod_visita`,
                `obs_visit`,
                `destacar`
              FROM
                `obs_visit`
              WHERE
                `id_obs_visit` = $idobs
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

    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta da observação retornou 0 ocorrências ( ALTERAÇÃO DE OBSERVAÇÃO - VISITANTES ).\n\n Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$dados_obs = $query_obs->fetch_assoc();

$idvisit = $dados_obs['cod_visita'];

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

        <script type="text/javascript">
            var KEY_TAB = 9;

            function processTab () {
                if ( window.event.keyCode == KEY_TAB ) {
                    var s = document.selection;
                    var tr = s.createRange();

                    if ( tr != null )
                        // escolha o comportamento da tecla "tab"
                        // entre a definição do tab
                        // ou um conjunto de caracteres em branco , para um tab maior .
                        tr.text = "\t";
                        //tr.text = "   ";

                    window.event.returnValue=false;
                }
            }
        </script>


            <p class="descript_page">ALTERAR OBSERVAÇÃO DE VISITANTE</p>

            <?php include 'quali/visit_basic.php'; ?>

            <p class="table_leg">Observação:</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendvisitobs.php" method="post" name="cadobsvisit" id="cadobsvisit">

                <div align="center">
                    <textarea name="obs_visit" id="obs_visit" cols="75" rows="5" class="CaixaTexto" onBlur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 4);" onkeydown="processTab()"><?php echo $dados_obs['obs_visit']; ?></textarea>
                </div>

                <p style="text-align: center; margin-top: 3px;">Destacar: <input name="destacar" type="checkbox" id="destacar" value="1" <?php echo $dados_obs['destacar'] == 1 ? 'checked="checked"' : '' ?> /></p>

                <input type="hidden" name="id_obs_visit" id="id_obs_visit" value="<?php echo $dados_obs['id_obs_visit']; ?>" />
                <input type="hidden" name="proced" id="proced" value="1" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Alterar" />
                    <input class="form_bt" type="button" name="" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

            <script type="text/javascript">

                id( 'obs_visit' ).focus();

                $(function() {
                    $("form").submit(function() {
                        if ( valida_obs( 'obs_visit' ) == true ) {
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