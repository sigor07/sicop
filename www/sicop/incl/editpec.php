<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_incl   = get_session( 'n_incl', 'int' );
$n_incl_n = 3;

$targ = get_get( 'targ', 'int' );

$botao_canc = 'history.go(-1)';
$botao_value = 'Cancelar';

if ( $targ == 1 ) {
    $botao_canc = 'self.window.close()';
    $botao_value = 'Fechar';
}

$motivo_pag = 'ALTERAÇÃO DE PECÚLIO - INCLUSÃO';

if ( $n_incl < $n_incl_n ) {

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

$idpec = get_get( 'idpec', 'int' );

if ( empty( $idpec ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    $ret = 1;
    if ( !empty ( $targ ) ) $ret = 'f';
    echo msg_js( 'FALHA!!!', $ret );

    exit;

}

$q_pec = "SELECT
            `peculio`.`idpeculio`,
            `peculio`.`cod_detento`,
            `peculio`.`descr_peculio`,
            `peculio`.`tipo_peculio`,
            DATE_FORMAT( `peculio`.`data_add`, '%d/%m/%Y' ) AS data_add_f
          FROM
            `peculio`
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
    $msg['text']  = "Falha na consulta ( ALTERAÇÃO DE PECÚLIO ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_pec = $q_pec->num_rows;

if( $cont_pec < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'A consulta de retornou 0 ocorrências ( ALTERAÇÃO DE PECÚLIO ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$d_pec = $q_pec->fetch_assoc();

$iddet = $d_pec['cod_detento'];

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

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


            <p class="descript_page">ALTERAR PERTENCES</p>

            <?php include 'quali/det_basic.php';?>

            <p class="table_leg">Pertence</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendpeculio.php" method="post" name="editpert" id="editpert" onsubmit="return validacadpert()">

                <table class="edit">
                    <tr >
                        <td width="70" height="20" valign="top">Tipo:</td>
                        <td width="325" valign="top">
                            <select name="tipo_peculio" class="CaixaTexto" id="tipo_peculio" onchange="javascript: document.getElementById('descr_peculio').focus();">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while( $d_tip_pec = $q_tip_pec->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_tip_pec['idtipopeculio'];?>" <?php echo $d_tip_pec['idtipopeculio'] == $d_pec['tipo_peculio'] ? 'selected="selected"' : ''; ?>><?php echo $d_tip_pec['tipo_peculio'];?></option>
                                <?php };?>
                            </select>
                        </td>
                    </tr>
                    <tr >
                        <td height="20" valign="top">Descrição:</td>
                        <td valign="top"><textarea name="descr_peculio" id="descr_peculio" cols="60" rows="3" class="CaixaTexto" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 4);"><?php echo $d_pec['descr_peculio'];?></textarea></td>
                    </tr>
                </table>

                <input type="hidden" name="idpec" id="idpec" value="<?php echo $idpec;?>" />
                <input type="hidden" name="targ" id="targ" value="<?php echo $targ;?>" />
                <input type="hidden" name="proced" id="proced" value="1" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Atualizar" />
                    <input class="form_bt" type="button" name="" onclick="<?php echo $botao_canc ?>" value="<?php echo $botao_value ?>" />
                </div>

            </form>

            <script type="text/javascript">

                $(function() {

                    $( "#tipo_peculio" ).focus();

                    $("form").submit(function() {
                        if ( validacadpert() == true ) {
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