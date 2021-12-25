<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_sind   = get_session( 'n_sind', 'int' );
$n_sind_n = 3;

$targ = get_get( 'targ', 'int' );

$botao_canc = 'history.go(-1)';
$botao_value = 'Cancelar';

if ( $targ == 1 ) {
    $botao_canc = 'self.window.close()';
    $botao_value = 'Fechar';
}

$motivo_pag = 'CADASTRO DE OBSERVAÇÃO DE PDA';

if ( $n_sind < $n_sind_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    $ret = 1;
    if ( $targ == 1 ) $ret = 'f';

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', $ret );

    exit;

}

$idpda = get_get( 'idpda', 'int' );

if ( empty( $idpda ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    $ret = 1;
    if ( !empty ( $targ ) ) $ret = 'f';
    echo msg_js( '', $ret );

    exit;

}

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
                `idsind` = $idpda
              LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_pda = $model->query( $query_pda );

// fechando a conexao
$model->closeConnection();

if( !$query_pda ) {

    $ret = 1;
    if ( !empty ( $targ ) ) $ret = 'f';
    echo msg_js( '', $ret );

    exit;

}

$cont_pda = $query_pda->num_rows;

if( $cont_pda < 1 ) {

    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ocorrências ( CADASTRAMENTO DE OBSERVAÇÃO - PDA ).\n\n Página: $pag";
    salvaLog( $mensagem );

    $ret = 1;
    if ( !empty ( $targ ) ) $ret = 'f';
    echo msg_js( '', $ret );

    exit;

}

$d_pda = $query_pda->fetch_assoc();

$iddet = $d_pda['cod_detento'];

$numpda = empty( $d_pda['local_pda'] ) ? $d_pda['num_pda'] . '/' . $d_pda['ano_pda'] : $d_pda['num_pda'] . '/' . $d_pda['ano_pda'] . '-' . $d_pda['local_pda'];

$corfonts = muda_cor_pda( $d_pda['data_reabilit'], $d_pda['sit_pda'] );

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Cadastrar observação de PDA';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

if ( $targ == 1 ){

    require 'cab_simp.php';

} else {

    require 'cab.php';
    $pag_atual = $_SERVER['PHP_SELF'];
    $qs = $_SERVER['QUERY_STRING'];

    if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

    $trail = new Breadcrumb();
    $trail->add( $desc_pag, $pag_atual, 6 );
    $trail->output();

}
?>

            <p class="descript_page">NOVA OBSERVAÇÃO DE PDA</p>

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
                    <textarea name="obs_pda" id="obs_pda" cols="75" rows="5" class="CaixaTexto" onBlur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 1);"></textarea>
                </div>

                <input type="hidden" name="idpda" id="idpda" value="<?php echo $d_pda['idsind']; ?>" />
                <input type="hidden" name="targ" id="targ" value="<?php echo $targ; ?>" />
                <input type="hidden" name="proced" id="proced" value="3" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Cadastrar" />
                    <input class="form_bt" type="button" name="" onclick="<?php echo $botao_canc; ?>" value="<?php echo $botao_value; ?>" />
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