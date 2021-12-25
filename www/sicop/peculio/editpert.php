<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_peculio       = get_session( 'n_peculio', 'int' );
$n_peculio_baixa = get_session( 'n_peculio_baixa', 'int' );
$n_peculio_n     = 3;

$targ = get_get( 'targ', 'int' );

$botao_canc = 'history.go(-1)';
$botao_value = 'Cancelar';

if ( $targ == 1 ) {
    $botao_canc = 'self.window.close()';
    $botao_value = 'Fechar';
}

$motivo_pag = 'ALTERAÇÃO DE PECÚLIO';

if ( $n_peculio < $n_peculio_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$idpec = get_get( 'idpec', 'int' );

if ( empty( $idpec ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso direto à página de alteração de pecúlio de detento.';
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$q_pec = "SELECT
            `peculio`.`idpeculio`,
            `peculio`.`cod_detento`,
            `peculio`.`cod_tipo_peculio` AS tipopec,
            `peculio`.`descr_peculio`,
            `peculio`.`retirado`,
            `peculio`.`obs_ret`,
            `tipopeculio`.`tipo_peculio`
          FROM
            `peculio`
            INNER JOIN `tipopeculio` ON `peculio`.`cod_tipo_peculio` = `tipopeculio`.`idtipopeculio`
          WHERE
            `peculio`.`idpeculio` = $idpec
          LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_pec = $model->query( $q_pec );

// fechando a conexao
$model->closeConnection();

if( !$q_pec ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta de verificação de matrícula ( ALTERAÇÃO DE PECÚLIO ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$cont_pec = $q_pec->num_rows;

if( $cont_pec < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'A consulta de verificação de matrícula retornou 0 ocorrências ( ALTERAÇÃO DE PECÚLIO ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$d_pec = $q_pec->fetch_assoc();

$iddet = $d_pec['cod_detento'];

// $esp = espécie
$esp = 1;

$desc_pag = 'Alterar pertence';

$q_tip_pec = 'SELECT `idtipopeculio`, `tipo_peculio` FROM `tipopeculio` ORDER BY `tipo_peculio`';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_tip_pec = $model->query( $q_tip_pec );

// fechando a conexao
$model->closeConnection();

if( !$q_tip_pec ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

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

            <p class="descript_page">ALTERAR PERTENCE</p>

            <?php include 'quali/det_basic.php';?>

            <p class="table_leg">Pertence</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendpeculio.php" method="post" name="cadpert" id="cadpert" onSubmit="return validacadpert()">

                <table class="edit">

                    <tr >
                        <td class="tbe_legend">Tipo:</td>
                        <td class="tbe_field">
                            <select name="tipo_peculio" class="CaixaTexto" id="tipo_peculio" onchange="javascript: document.getElementById('descr_peculio').focus();">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while( $d_tip_pec = $q_tip_pec->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_tip_pec['idtipopeculio'];?>" <?php echo $d_tip_pec['idtipopeculio'] == $d_pec['tipopec'] ? 'selected="selected"' : ''; ?>><?php echo $d_tip_pec['tipo_peculio'];?></option>
                                <?php };?>
                            </select>
                        </td>
                    </tr>

                    <tr >
                        <td class="tbe_legend">Descrição:</td>
                        <td class="tbe_field">
                            <textarea name="descr_peculio" id="descr_peculio" cols="60" rows="3" class="CaixaTexto" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 4);"><?php echo $d_pec['descr_peculio'] ?></textarea>
                        </td>
                    </tr>

                    <?php if ($n_peculio_baixa >= 1) { ?>

                    <tr>
                        <td class="tbe_legend">Retirado:</td>
                        <td class="tbe_field">
                            <input name="retirado" type="checkbox" id="retirado" onClick="mostraPERT();" value="1" <?php echo $d_pec['retirado'] == 1 ? 'checked="checked"' : '' ?> />
                        </td>
                    </tr>

                    <tr id="tr_pert_ret">
                        <td class="tbe_legend">Observações:</td>
                        <td class="tbe_field">
                            <textarea name="obs_ret" id="obs_ret" cols="60" rows="3" class="CaixaTexto" onBlur="upperMe(this); remacc(this);" onKeyPress="return blockChars(event, 4);"><?php echo $d_pec['obs_ret'] ?></textarea>
                        </td>
                    </tr>

                    <?php } ?>

                </table>

                <input type="hidden" name="idpec" id="idpec" value="<?php echo $idpec; ?>" />
                <input type="hidden" name="esp" id="esp" value="<?php echo $esp;?>" />
                <input type="hidden" name="proced" id="proced" value="1" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Alterar" />
                    <input class="form_bt" type="button" name="" onclick="<?php echo $botao_canc; ?>" value="<?php echo $botao_value; ?>" />
                </div>

            </form>

            <script type="text/javascript">
                id("tipo_peculio").focus();
                mostraPERT();
            </script>

            <!--&#13;-->
<?php include 'footer.php'; ?>