<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_peculio   = get_session( 'n_peculio', 'int' );
$n_peculio_n = 3;

$targ = get_get( 'targ', 'int' );

$botao_canc = 'history.go(-1)';
$botao_value = 'Cancelar';

if ( $targ == 1 ) {
    $botao_canc = 'self.window.close()';
    $botao_value = 'Fechar';
}

$motivo_pag = 'CADASTRAMENTO DE PECÚLIO';

if ( $n_peculio < $n_peculio_n ) {

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

$iddet = get_get( 'iddet', 'int' );

if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso direto à página de cadastramento de pecúlio de detento.';
    get_msg( $msg, 1 );

    $ret = 1;
    if ( !empty ( $targ ) ) $ret = 'f';
    echo msg_js( '', $ret );

    exit;

}

$q_matr = "SELECT `matricula` FROM `detentos` WHERE `iddetento` = $iddet";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$matricula = $model->fetchOne( $q_matr );

// fechando a conexao
$model->closeConnection();

if ( empty( $matricula ) ) {

    // pegar os dados do preso
    $detento = dados_det( $iddet );

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Tentativa de cadastramento de pertence para detento que não possui matrícula.\n\n $detento";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    require 'cab_simp.php';

    $msg = 'Você não pode cadastrar pertences para um detento que ainda não possui matrícula.';
    $saida = msg_js( "$msg", 1 );
    if ( !empty ( $targ ) ) $saida = msg_js( "$msg", 'f' );
    echo $saida;

    exit;

}

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Cadastrar pertence';

$q_tip_pec = 'SELECT `idtipopeculio`, `tipo_peculio` FROM `tipopeculio` ORDER BY `tipo_peculio`';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_tip_pec = $model->query( $q_tip_pec );

// fechando a conexao
$model->closeConnection();

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
    $trail->add( $desc_pag, $pag_atual, 4 );
    $trail->output();

}
?>

            <p class="descript_page">CADASTRAR PERTENCES</p>

            <?php include 'quali/det_basic.php'; ?>

            <p class="table_leg">Pertence</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendpeculio.php" method="post" name="cadpert" id="cadpert" onSubmit="return validacadpert()">

                <table class="edit">
                    <tr >
                        <td class="tbe_legend">Tipo:</td>
                        <td class="tbe_field">
                            <select name="tipo_peculio" class="CaixaTexto" id="tipo_peculio" onchange="javascript: document.getElementById('descr_peculio').focus();">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while( $d_tip_pec = $q_tip_pec->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_tip_pec['idtipopeculio'];?>"><?php echo $d_tip_pec['tipo_peculio'];?></option>
                                <?php };?>
                            </select>
                        </td>
                    </tr>
                    <script type="text/javascript">id("tipo_peculio").focus();</script>
                    <tr >
                        <td class="tbe_legend">Descrição:</td>
                        <td class="tbe_field">
                            <textarea name="descr_peculio" id="descr_peculio" cols="60" rows="3" class="CaixaTexto" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 4);"></textarea>
                        </td>
                    </tr>
                </table>

                <!-- onkeypress="mascara(this, mmonet); return blockChars(event, 2);" -->
                <input type="hidden" name="iddet" id="iddet" value="<?php echo $iddet;?>" />
                <input type="hidden" name="targ" id="targ" value="<?php echo $targ;?>" />
                <input type="hidden" name="proced" id="proced" value="3" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Cadastrar" />
                    <input class="form_bt" type="submit" name="cadadd" id="submit" value="Cadastrar e adicionar outro" />
                    <input class="form_bt" type="button" name="" onclick="<?php echo $botao_canc; ?>" value="<?php echo $botao_value; ?>" />
                </div>

            </form>

    <!--&#13;-->
<?php include 'footer.php'; ?>