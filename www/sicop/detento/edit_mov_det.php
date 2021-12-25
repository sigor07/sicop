<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_det_mov = get_session( 'n_det_mov', 'int' );
$n_mov_n   = 1;

$n_chefia   = get_session( 'n_chefia', 'int' );
$n_chefia_n = 1;

$motivo_pag = 'ALTERAÇÃO DE MOVIMENTAÇÃO DE '  . SICOP_DET_DESC_U;

if ( $n_det_mov < $n_mov_n or $n_chefia < $n_chefia_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$idmov = get_get( 'idmov', 'int' );

if ( empty( $idmov ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página. Identificador da movimentação em branco. ( $motivo_pag )";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$q_mov_atual = "SELECT
                  `mov_det`.`id_mov`,
                  `mov_det`.`cod_detento`,
                  `mov_det`.`cod_tipo_mov`,
                  `mov_det`.`cod_local_mov`,
                  `mov_det`.`data_mov`,
                  DATE_FORMAT( `mov_det`.`data_mov`, '%d/%m/%Y' ) As data_mov_f,
                  `tipomov`.`sigla_mov`,
                  `tipomov`.`tipo_mov`,
                  `unidades`.`unidades` AS local_mov,
                  `user_add`
                FROM
                  `mov_det`
                  LEFT JOIN `tipomov` ON `mov_det`.`cod_tipo_mov` = `tipomov`.`idtipo_mov`
                  LEFT JOIN `unidades` ON `mov_det`.`cod_local_mov` = `unidades`.`idunidades`
                WHERE
                  `mov_det`.`id_mov` = $idmov
                LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_mov_atual = $model->query( $q_mov_atual );

// fechando a conexao
$model->closeConnection();

if ( !$q_mov_atual ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta ( MOVIMENTAÇÃO ATUAL - $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_mov = $q_mov_atual->num_rows;

if( $cont_mov < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "A consulta retornou 0 ocorrências ( MOVIMENTAÇÃO ATUAL - $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$d_mov_atual = $q_mov_atual->fetch_assoc();

$data_mov_atual    = $d_mov_atual['data_mov_f'];
$data_mov_atual_sf = $d_mov_atual['data_mov'];
$tipo_mov_atual    = $d_mov_atual['cod_tipo_mov'];
$id_mov_atual      = $d_mov_atual['id_mov'];
$idlocal_mov       = $d_mov_atual['cod_local_mov'];
$iddet             = $d_mov_atual['cod_detento'];

$q_mov_ant = "SELECT
                `mov_det`.`id_mov`,
                `mov_det`.`cod_detento`,
                `mov_det`.`cod_tipo_mov`,
                `mov_det`.`cod_local_mov`,
                `mov_det`.`data_mov`,
                DATE_FORMAT( `mov_det`.`data_mov`, '%d/%m/%Y' ) As data_mov_f,
                `tipomov`.`sigla_mov`,
                `tipomov`.`tipo_mov`,
                `unidades`.`unidades` AS local_mov,
                `user_add`
              FROM
                `mov_det`
                LEFT JOIN `tipomov` ON `mov_det`.`cod_tipo_mov` = `tipomov`.`idtipo_mov`
                LEFT JOIN `unidades` ON `mov_det`.`cod_local_mov` = `unidades`.`idunidades`
              WHERE
                `mov_det`.`cod_detento` = $iddet
                AND
                `mov_det`.`data_mov` <= '$data_mov_atual_sf'
                AND
                `mov_det`.`id_mov` != $id_mov_atual
              ORDER BY
                `mov_det`.`data_mov` DESC, `mov_det`.`data_add` DESC
              LIMIT 1";


// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_mov_ant = $model->query( $q_mov_ant );

// fechando a conexao
$model->closeConnection();

if ( !$q_mov_ant ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta ( MOVIMENTAÇÃO ANTERIOR - $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_mov_ant = $q_mov_ant->num_rows;

$tipo_mov_ant = '';
$datault      = '';

$desc_pag = 'Alterar movimentção';

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

            <p class="descript_page">ALTERAR MOVIMENTAÇÃO</p>

            <?php include 'quali/det_basic.php'; ?>

            <p class="table_leg">Última Movimentação</p>

            <?php

                $sit_det = manipula_sit_det_id( $tipo_mov_in, $tipo_mov_out, $iddestino );

                if ( $cont_mov_ant < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem

                    echo '<p class="p_q_no_result">Não há movimentações.</p>';

                } else {

                    $d_mov_ant    = $q_mov_ant->fetch_assoc();
                    $tipo_mov_ant = $d_mov_ant['cod_tipo_mov'];
                    $datault      = $d_mov_ant['data_mov_f'];

            ?>
            <table class="lista_busca">
                <tr bgcolor="#FAFAFA">
                    <td height="15" width="145">Tipo de Movimentação:</td>
                    <td width="250"><?php echo $d_mov_ant['sigla_mov'] . ' - ' . $d_mov_ant['tipo_mov'] ?></td>
                </tr>
                <tr bgcolor="#FAFAFA">
                    <td height="15">Local:</td>
                    <td><?php echo $d_mov_ant['local_mov'] ?></td>
                </tr>
                <tr bgcolor="#FAFAFA">
                    <td height="15">Data</td>
                    <td><?php echo $d_mov_ant['data_mov_f'] ?></td>
                </tr>
            </table>
            <?php } ?>
            <p class="table_leg"> Movimentação atual</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/senddetmovup.php" method="post" name="cadmovdet" id="cadmovdet" onSubmit="return validacadastramovdet()">
                <table class="edit">
                    <tr>
                        <td width="140">Tipo de Movimentação:</td>
                        <td width="323">
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
                            <input name="data_mov" type="text" class="CaixaTexto" id="data_mov" onBlur="verifica_data(this, this.value)" onKeyPress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $d_mov_atual['data_mov_f']; ?>" size="12" maxlength="10" />
                            &nbsp;<a href="#" onClick="javascript: datahoje('data_mov'); return false;" >hoje</a>
                        </td>
                    </tr>
                </table>

                <input type="hidden" name="iddet" id="iddet" value="<?php echo $iddet ?>" />
                <input type="hidden" name="idmov" id="idmov" value="<?php echo $idmov; ?>" />
                <input type="hidden" name="datahj" id="datahj" value="<?php echo date('d/m/Y') ?>" />
                <input type="hidden" name="data_ult" id="data_ult" value="<?php echo $datault ?>" />

                <input type="hidden" name="sit_det" id="sit_det" value="<?php echo $sit_det ?>" />
                <input type="hidden" name="tipo_mov_ant" id="tipo_mov_ant" value="<?php echo $tipo_mov_ant ?>" />
                <input type="hidden" name="tipo_mov_atual" id="tipo_mov_atual" value="<?php echo $tipo_mov_atual ?>" />
                <input type="hidden" name="old_local_mov" id="old_local_mov" value="<?php echo $idlocal_mov;?>" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Alterar" />
                    <input class="form_bt" type="button" name="" onClick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

            <script type="text/javascript">

                $(function() {

                    $.monta_box_tipo_mov_ant();

                    mostraDest();

                    $( "#tipo_mov" ).focus();
                    $( "#data_mov" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });

                });



            </script>

<?php include 'footer.php'?>