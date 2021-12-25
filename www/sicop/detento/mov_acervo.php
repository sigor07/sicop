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

$n_cadastro = get_session( 'n_cadastro', 'int' );
$n_cad_n    = 3;

if ( $n_cadastro < $n_cad_n ) {

    require 'cab_simp.php';
    $tipo = 0;
    if ( !empty( $targ ) ) $tipo = 3;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'CADASTRAMENTO DE MOVIMENTAÇÕES NO ACERVO';
    get_msg( $msg, 1 );

    exit;

}

$iddet = get_get( 'iddet', 'int' );

if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso direto à página. Identificador d' . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U . ' em branco. ( CADASTRAMENTO DE TV )';
    get_msg( $msg, 1 );

    $ret = 1;
    if ( !empty( $targ ) ) $ret = 'f';
    echo msg_js( '', $ret );
    exit;

}

$query_mov = "SELECT
                `mov_det`.`id_mov`,
                `mov_det`.`cod_tipo_mov`,
                `tipomov`.`sigla_mov`,
                `tipomov`.`tipo_mov`,
                `unidades`.`unidades` AS local_mov,
                `mov_det`.`cod_local_mov`,
                `mov_det`.`data_mov`,
                DATE_FORMAT( `mov_det`.`data_mov`, '%d/%m/%Y' ) As data_mov_f,
                `user_add`
              FROM
                `mov_det`
                LEFT JOIN `tipomov` ON `mov_det`.`cod_tipo_mov` = `tipomov`.`idtipo_mov`
                LEFT JOIN `unidades` ON `mov_det`.`cod_local_mov` = `unidades`.`idunidades`
              WHERE
                `mov_det`.`cod_detento` = $iddet
                AND
                `mov_det`.`cod_tipo_mov` IN( 1, 2, 3 )
              ORDER BY
                `mov_det`.`data_mov` DESC,
                `mov_det`.`data_add` DESC
              LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_mov = $model->query( $query_mov );

// fechando a conexao
$model->closeConnection();

if ( !$query_mov ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Falha na consulta ( ÚLTIMA MOVIMENTAÇÃO - CADASTRAMENTO MOVIMENTAÇÕES D' . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U . " ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    $ret = 1;
    if ( !empty( $targ ) ) $ret = 'f';
    echo msg_js( 'FALHA!!!', $ret );
    exit;

}

$cont_mov = $query_mov->num_rows;

$datault = '';

$query_tipomov = 'SELECT
                    `idtipo_mov`,
                    `sigla_mov`,
                    `tipo_mov`
                  FROM
                    `tipomov`';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_tipomov = $model->query( $query_tipomov );

// fechando a conexao
$model->closeConnection();

if ( !$query_tipomov ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Falha na consulta ( TIPO DE MOVIMENTAÇÃO - CADASTRAMENTO MOVIMENTAÇÕES D' . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U . " ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    $ret = 1;
    if ( !empty( $targ ) ) $ret = 'f';
    echo msg_js( '', $ret );
    exit;

}

$cont_tipomov = $query_tipomov->num_rows;

if ( $cont_tipomov < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'A consulta retornou 0 ocorrências ( TIPO DE MOVIMENTAÇÃO - CADASTRAMENTO MOVIMENTAÇÕES D' . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U . ' ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    $ret = 1;
    if ( !empty( $targ ) ) $ret = 'f';
    echo msg_js( 'FALHA!!!', $ret );
    exit;

}

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Cadastrar movimentação no acervo';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

if ( $targ == 1 ) {

    require 'cab_simp.php';

} else {

    require 'cab.php';

    $pag_atual = $_SERVER['PHP_SELF'];
    $qs = $_SERVER['QUERY_STRING'];

    if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

    $trail = new Breadcrumb();
    $trail->add( $desc_pag, $pag_atual, 5 );
    $trail->output();

}
?>

            <p class="descript_page">NOVA MOVIMENTAÇÃO NO ACERVO</p>

            <?php include 'quali/det_basic.php'; ?>

            <p class="table_leg">Última Movimentação de inclusão</p>

            <?php
            if ( $cont_mov < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não há movimentações.</p>';
            } else {
                $dados_mov = $query_mov->fetch_assoc();
                $datault = $dados_mov['data_mov_f'];
            ?>

            <table class="lista_busca">
                <tr bgcolor="#FAFAFA">
                    <td height="20" width="145">Tipo de Movimentação:</td>
                    <td width="250"><?php echo $dados_mov['sigla_mov'] . ' - ' . $dados_mov['tipo_mov'] ?></td>
                </tr>
                <tr bgcolor="#FAFAFA">
                    <td height="20">Local:</td>
                    <td><?php echo $dados_mov['local_mov'] ?></td>
                </tr>
                <tr bgcolor="#FAFAFA">
                    <td height="20">Data</td>
                    <td><?php echo $datault ?></td>
                </tr>
            </table>

            <?php } ?>

            <p class="table_leg">Nova Movimentação</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/senddetmovacervo.php" method="post" name="cadmovdet" id="cadmovdet" onSubmit="return validacadastramovdet(1)">

                <table class="edit">

                    <tr>
                        <td width="142">Tipo de Movimentação:</td>
                        <td width="316">
                            <select name="tipo_mov" class="CaixaTexto" id="tipo_mov" onChange="$.monta_box_local_mov(); mostraDest();">
                                <option value="" selected="selected">Selecione</option>
                            </select>
                        </td>
                    </tr>

                    <tr id="localmov_field">
                        <td><span id="localmovl">Procedência/Destino:</span></td>
                        <td>
                            <span id="localmov">
                                <select name="local_mov" class="CaixaTexto" id="local_mov">
                                    <option value="" selected="selected">Selecione o tipo de movimentação...</option>
                                </select>
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td>Data da Movimentação:</td>
                        <td>
                            <input name="data_mov" type="text" class="CaixaTexto" id="data_mov" size="12" maxlength="10" onBlur="verifica_data(this, this.value)" onKeyPress="mascara_data(this, this.value); return blockChars(event, 2);" />
                            &nbsp;&nbsp;<a href="#" onClick="javascript: datahoje('data_mov'); return false;" >hoje</a>
                        </td>
                    </tr>

                </table>

                <input type="hidden" name="iddet" id="iddet" value="<?php echo $iddet;?>" />
                <input type="hidden" name="targ" id="targ" value="<?php echo $targ;?>" />
                <input type="hidden" name="datahj" id="datahj" value="<?php echo date('d/m/Y') ?>" />
                <input type="hidden" name="data_ult" id="data_ult" value="<?php echo $datault ?>" />
                <input type="hidden" name="sit_det" id="sit_det" value="998" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Cadastrar" />
                    <input class="form_bt" type="submit" name="cadadd" id="submit" value="Cadastrar e adicionar outro" />
                    <input class="form_bt" type="button" name="" onClick="<?php echo $botao_canc ?>" value="Cancelar" />
                </div>

            </form>

            <script type="text/javascript">

                $(function() {

                    $.monta_box_tipo_mov();

                    $( "#tipo_mov" ).focus();
                    $( "#data_mov" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });
                });

            </script>

<?php include 'footer.php'?>