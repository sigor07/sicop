<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_sind   = get_session( 'n_sind', 'int' );
$n_sind_n = 3;

if ( $n_sind < $n_sind_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'ALTERAÇÃO DE OBSERVAÇÃO - PDA';
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$idobs = get_get( 'idobs', 'int' );

if ( empty( $idobs ) ) {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso direto à página de alteração de observação de PDA.\n\n Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$query_obs = "SELECT
                `id_obs_pda`,
                `cod_pda`,
                `obs_pda`
              FROM
                `obs_pda`
              WHERE
                `id_obs_pda` = $idobs
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

    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta da observação retornou 0 ocorrências ( ALTERAÇÃO DE OBSERVAÇÃO - PDA ).\n\n Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$d_obs = $query_obs->fetch_assoc();

$query_pda = "SELECT
                `idsind`,
                `cod_detento`,
                `num_pda`,
                `ano_pda`,
                `local_pda`,
                `sit_pda`,
                `cod_sit_detento` AS sit_det_pda,
                `data_reabilit`
              FROM
                sindicancias
              WHERE
                `idsind` = (SELECT `cod_pda` FROM `obs_pda` WHERE `id_obs_pda` = $idobs LIMIT 1)
              LIMIT 1";


// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_pda = $model->query( $query_pda );

// fechando a conexao
$model->closeConnection();

if( !$query_pda ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_pda = $query_pda->num_rows;

if( $cont_pda < 1 ) {

    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta do PDA retornou 0 ocorrências ( ALTERAÇÃO DE OBSERVAÇÃO - PDA ).\n\n Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$d_pda = $query_pda->fetch_assoc();

$iddet = $d_pda['cod_detento'];

$numpda = empty($d_pda['local_pda']) ? $d_pda['num_pda'] . '/' . $d_pda['ano_pda'] : $d_pda['num_pda'] . '/' . $d_pda['ano_pda'] . '-' . $d_pda['local_pda'];

$corfonts = muda_cor_pda( $d_pda['data_reabilit'], $d_pda['sit_pda'] );

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Alterar observação de PDA';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 6 );
$trail->output();
?>

            <script type="text/javascript">
                var KEY_TAB = 9;

                function processTab () {
                    if ( window.event.keyCode == KEY_TAB )
                    {
                        var s = document.selection;
                        var tr = s.createRange();

                        if ( tr != null )
                        // escolha o comportamento da tecla "tab"
                        // entre a definição do tab
                        // ou um conjunto de caracteres em branco , para um tab maior .
                            tr.text = "\t";
                        //tr.text = "&nbsp;";

                        window.event.returnValue=false;
                    }
                }
            </script>

            <p class="descript_page">ALTERAR OBSERVAÇÃO DE PDA</p>

            <p class="table_leg">PDA</p>

            <div class="detal_var" style="width: 300px;">
                <font color="<?php echo $corfonts; ?>"><?php echo $numpda ?></font>
            </div>

            <?php if ( empty( $iddet ) ) { ?>

            <p class="table_leg"><?php echo SICOP_DET_DESC_FU; ?></p>

            <div class="detal_var">
                AUTORIA DESCONHECIDA
            </div>

            <?php } else { ?>

            <?php include 'quali/det_basic.php'; ?>

            <?php } ?>


            <p class="table_leg">Observação</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendpdaobs.php" method="post" name="cadobspda" id="cadobspda">

                <div align="center">
                    <textarea name="obs_pda" id="obs_pda" cols="75" rows="5" class="CaixaTexto" onBlur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 1);" onkeydown="processTab()"><?php echo $d_obs['obs_pda']; ?></textarea>
                </div>

                <input name="id_obs_pda" type="hidden" id="id_obs_pda" value="<?php echo $d_obs['id_obs_pda']; ?>" />
                <input name="proced" type="hidden" id="proced" value="1" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Alterar" />
                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

            <script type="text/javascript">id( 'obs_pda' ).focus()</script>
            <script type="text/javascript">

                $(function() {
                    $("form").submit(function() {
                        if ( valida_obs( 'obs_pda' ) == true ) {
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

