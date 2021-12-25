<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_det_mov = get_session( 'n_det_mov', 'int' );
$n_mov_n   = 1;

$targ = get_get( 'targ', 'int' );

$botao_canc = 'history.go(-1)';

if ( $targ == 1 ) {
    $botao_canc = 'self.window.close()';
}

$motivo_pag = 'CADASTRAMENTO DE MOVIMENTAÇÃO DE ' . SICOP_DET_DESC_U;

if ( $n_det_mov < $n_mov_n ) {

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
    $msg = array( );
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( IDENTIFICADOR EM BRANCO - $motivo_pag ).";
    get_msg( $msg, 1 );


    echo msg_js( '', 1 );


    exit;

}

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

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
    $msg['text']  = "Falha na consulta ( ÚLTIMA MOVIMENTAÇÃO - $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$cont_mov = $query_mov->num_rows;

$datault = '';

$desc_pag = 'Cadastrar movimentção';

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
    $trail->add( $desc_pag, $pag_atual, 5 );
    $trail->output();

}
?>

            <p class="descript_page">NOVA MOVIMENTAÇÃO</p>

            <?php include 'quali/det_basic.php'; ?>

            <p class="table_leg">Última Movimentação</p>

            <?php

            $sit_det = manipula_sit_det_id( $tipo_mov_in, $tipo_mov_out, $iddestino );

            if ( $sit_det == SICOP_SIT_DET_FALECIDO ) {
                echo msg_js( 'Você não pode movimentar um ' . SICOP_DET_DESC_L . ' falecido.', 1 );
                exit;
            }

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

            <form action="<?php echo SICOP_ABS_PATH ?>send/senddetmov.php" method="post" name="cadmovdet" id="cadmovdet">
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
                                    <option value="" selected="selected">Selecione o tipo de movimentação</option>
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
                <input type="hidden" name="sit_det" id="sit_det" value="<?php echo $sit_det ?>" />
                <input type="hidden" name="old_tipo_mov" id="old_tipo_mov" value="" />
                <input type="hidden" name="old_local_mov" id="old_local_mov" value="" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Cadastrar" />
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

                    $("form").submit(function() {
                        if ( validacadastramovdet() == true ) {
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

<?php include 'footer.php'?>