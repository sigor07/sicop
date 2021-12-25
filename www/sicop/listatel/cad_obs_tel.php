<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$targ = get_get( 'targ', 'int' );

$botao_canc = 'history.go(-1)';
$botao_value = 'Cancelar';

if ( $targ == 1 ) {
    $botao_canc = 'self.window.close()';
    $botao_value = 'Fechar';
}

$n_admsist = get_session( 'n_admsist', 'int' );

$motivo_pag = 'CADASTRAR OBSERVAÇÃO DE TELEFONE';

if ( empty( $n_admsist ) or $n_admsist < 3 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    $ret = 1;
    if ( !empty ( $targ ) ) $ret = 'f';

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', $ret );

    exit;

}


$idlt = get_get( 'idlt', 'int' );

if ( empty( $idlt ) ){

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

$q_local_tel = "SELECT
                  `listatel`.`tel_local`
                FROM
                  `listatel`
                WHERE
                  `listatel`.`idlistatel` = $idlt";


// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_local_tel = $model->query( $q_local_tel );

// fechando a conexao
$model->closeConnection();

if( !$q_local_tel ) {

    echo msg_js( '', 1 );
    exit;

}

$cont_q_local_tel = $q_local_tel->num_rows;

if( $cont_q_local_tel < 1 ) {

    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ocorrências ( CADASTRAMENTO DE OBSERVAÇÃO - LISTA DE TELEFONES ).\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( '', 1 );
    exit;

}

$d_lt = $q_local_tel->fetch_assoc();

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Cadastrar observação';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

if ($targ == 1){

    require 'cab_simp.php';

} else {

    require 'cab.php';
    $pag_atual = $_SERVER['PHP_SELF'];
    $qs = $_SERVER['QUERY_STRING'];

    if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

    $trail = new Breadcrumb();
    $trail->add( $desc_pag, $pag_atual, 5 );
    $trail->output();

}

?>


            <p class="descript_page">CADASTRAR OBSERVAÇÃO</p>

            <table class="detal_lt">
                <tr>
                    <td class="local_lt_med">Localidade: <?php echo $d_lt['tel_local']; ?></td>
                </tr>
            </table><!-- fim da <table class="detal_lt"> -->

            <p class="table_leg">Observação:</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendlistatelobs.php" method="post" name="obs_tel" id="obs_tel">

                <div align="center">
                    <textarea name="obs_listatel" id="obs_listatel" cols="75" rows="5" class="CaixaTexto" onkeypress="return blockChars(event, 4);"></textarea>
                </div>

                <input name="idlt" type="hidden" id="idlt" value="<?php echo $idlt; ?>" />
                <input name="targ" type="hidden" id="targ" value="<?php echo $targ; ?>" />
                <input name="proced" type="hidden" id="proced" value="3" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" onclick="return valida_obs( 'obs_listatel' );" value="Cadastrar" />
                    <input class="form_bt" type="button" name="" onclick="<?php echo $botao_canc; ?>" value="<?php echo $botao_value; ?>" />
                </div>

            </form><!-- fim do <form ... >  -->

            <script type="text/javascript">id("obs_listatel").focus()</script>

<?php include 'footer.php'; ?>