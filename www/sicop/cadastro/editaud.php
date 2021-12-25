<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_cadastro = get_session( 'n_cadastro', 'int' );
$n_cad_n    = 3;

if ( $n_cadastro < $n_cad_n ) {

    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'ALTERAÇÃO DE AUDIÊNCIA';
    get_msg( $msg, 1 );

    exit;

}

$idaud = get_get( 'idaud', 'int' );

if ( empty( $idaud ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso direto à página ( IDENTIFICADOR DA AUDIÊNCIA EM BRANCO - ALTERAÇÃO DE AUDIÊNCIA ).';
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$query_aud = "SELECT
                `idaudiencia`,
                `cod_detento`,
                `data_aud`,
                DATE_FORMAT(`data_aud`, '%d/%m/%Y') AS `data_aud_f`,
                `hora_aud`,
                DATE_FORMAT(`hora_aud`, '%H:%i') AS `hora_aud_f`,
                `local_aud`,
                `cidade_aud`,
                `tipo_aud`,
                `num_processo`,
                `motivo_justi`,
                `sit_aud`,
                `user_add`,
                DATE_FORMAT(`data_add`, '%d/%m/%Y às %H:%i') AS data_add,
                `user_up`,
                DATE_FORMAT(`data_up`, '%d/%m/%Y às %H:%i') AS data_up
              FROM
                `audiencias`
              WHERE
                `idaudiencia` = $idaud
              LIMIT 1";


// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_aud = $model->query( $query_aud );

// fechando a conexao
$model->closeConnection();

if ( !$query_aud ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Falha na consulta ( ALTERAÇÃO DE AUDIÊNCIA ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$cont_aud = $query_aud->num_rows;

if( $cont_aud < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'A consulta retornou 0 ocorrências ( ALTERAÇÃO DE AUDIÊNCIA ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$d_aud = $query_aud->fetch_assoc();

$iddet = $d_aud['cod_detento'];

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Alterar audiência';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 4 );
$trail->output();
?>

            <p class="descript_page">ALTERAR AUDIÊNCIA</p>

            <?php include 'quali/det_cad.php'; ?>

            <p class="table_leg">Audiência</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendaud.php" method="post" name="aud_sing" id="aud_sing" onSubmit="return validacadaud();">
                <table class="edit">
                    <tr>
                        <td align="center">Tipo de audiência</td>
                    </tr>
                    <tr>
                        <td width="270" rowspan="6">
                            <input name="tipo_aud" type="radio" id="tipo_aud_0" value="1" <?php echo $d_aud['tipo_aud'] == 1 ? 'checked="checked"' : ''; ?> onClick="preenche_campos_aud(); altera_campos_aud(); oculta_campos_aud();" /> JUDICIAL<br />
                            <input name="tipo_aud" type="radio" id="tipo_aud_1" value="2" <?php echo $d_aud['tipo_aud'] == 2 ? 'checked="checked"' : ''; ?> onClick="preenche_campos_aud(); altera_campos_aud(); oculta_campos_aud();"/> MÉDICA <br />
                            <input name="tipo_aud" type="radio" id="tipo_aud_2" value="3" <?php echo $d_aud['tipo_aud'] == 3 ? 'checked="checked"' : ''; ?> onClick="preenche_campos_aud(); altera_campos_aud(); oculta_campos_aud();"/> IML <br />
                            <input name="tipo_aud" type="radio" id="tipo_aud_3" value="4" <?php echo $d_aud['tipo_aud'] == 4 ? 'checked="checked"' : ''; ?> onClick="preenche_campos_aud(); altera_campos_aud(); oculta_campos_aud();"/> EXAME/PERÍCIA JUDICIAL<br />
                            <input name="tipo_aud" type="radio" id="tipo_aud_4" value="5" <?php echo $d_aud['tipo_aud'] == 5 ? 'checked="checked"' : ''; ?> onClick="preenche_campos_aud(); altera_campos_aud(); oculta_campos_aud();"/> DELEGACIA/CADEIA PÚBLICA<br />
                            <input name="tipo_aud" type="radio" id="tipo_aud_5" value="6" <?php echo $d_aud['tipo_aud'] == 6 ? 'checked="checked"' : ''; ?> onClick="preenche_campos_aud(); altera_campos_aud(); oculta_campos_aud();"/> PERÍCIA INSS<br />
                            <input name="tipo_aud" type="radio" id="tipo_aud_6" value="7" <?php echo $d_aud['tipo_aud'] == 7 ? 'checked="checked"' : ''; ?> onClick="preenche_campos_aud(); altera_campos_aud(); oculta_campos_aud();"/> NOTIFICAÇÃO/CITAÇÃO CADEIA PÚBLICA
                            <input name="tipo_aud" type="radio" id="tipo_aud_7" value="8" <?php echo $d_aud['tipo_aud'] == 8 ? 'checked="checked"' : ''; ?> onClick="preenche_campos_aud(); altera_campos_aud(); oculta_campos_aud();"/> SEGURO DESEMPREGO / PIS/PASEP
                        </td>

                    </tr>
                </table>

                <table class="edit">
                    <tr>
                        <td width="95">Data/hora:</td>
                        <td width="575"><input name="data_aud" type="text" class="CaixaTexto" id="data_aud" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $d_aud['data_aud_f']; ?>" size="12" maxlength="10" /> às <input name="hora_aud" type="text" class="CaixaTexto" id="hora_aud" onblur="verifica_hora(this, this.value)" onkeypress="mascara_hora(this, this.value); return blockChars(event, 2);" value="<?php echo $d_aud['hora_aud_f']; ?>" size="5" maxlength="5" /></td>
                    </tr>
                    <tr>
                        <td><span id="local">Local:</span></td>
                        <td><input name="local_aud" type="text" class="CaixaTexto" id="local_aud" onBlur="upperMe(this); remacc(this); rpcvara(this);" onKeyPress="return blockChars(event, 4);" value="<?php echo $d_aud['local_aud']; ?>" size="110" maxlength="100" /></td>
                    </tr>
                    <tr>
                        <td>Cidade:</td>
                        <td><input name="cidade_aud" type="text" class="CaixaTexto" id="cidade_aud" onBlur="upperMe(this); remacc(this); rpccidade(this);" onKeyPress="return blockChars(event, 4);" value="<?php echo $d_aud['cidade_aud']; ?>" size="60" maxlength="50" /></td>
                    </tr>
                    <tr id="num_process_field">
                        <td><span id="num">Nº do processo:</span></td>
                        <td><span id="num_f"><input name="num_processo" type="text" class="CaixaTexto" id="num_processo" onBlur="upperMe(this); remacc(this);" onKeyPress="return blockChars(event, 4);" value="<?php echo $d_aud['num_processo']; ?>" size="60" maxlength="50" /></span></td>
                    </tr>
                    <tr>
                        <td>Situação:</td>
                        <td><input name="sit_aud" type="radio" id="sit_aud_0" value="11" onclick="oculta_motivo_aud()" <?php echo $d_aud['sit_aud'] == "11" ? 'checked="checked"' : ''; ?> /> Ativa &nbsp;&nbsp;<input name="sit_aud" type="radio" id="sit_aud_1" value="12" onclick="oculta_motivo_aud()" <?php echo $d_aud['sit_aud'] == "12" ? 'checked="checked"' : ''; ?> /> Cancelada &nbsp;&nbsp;<input name="sit_aud" type="radio" id="sit_aud_2" value="13" onclick="oculta_motivo_aud()" <?php echo $d_aud['sit_aud'] == "13" ? 'checked="checked"' : ''; ?> /> Justificada</td>
                    </tr>
                    <tr id="mot_aud_field">
                        <td><span id="mot">Motivo:</span></td>
                        <td><span id="mot_f"><textarea name="motivo_justi" id="motivo_justi" class="CaixaTexto" cols="109" rows="3" onBlur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 4);" onKeyDown="textCounter(this, 150);" onKeyUp="textCounter(this, 150);"><?php echo $d_aud['motivo_justi']; ?></textarea></span></td>
                    </tr>
                </table>

                <input name="idaud" type="hidden" id="idaud" value="<?php echo $d_aud['idaudiencia']; ?>" />
                <input name="proced" type="hidden" id="proced" value="1" />

                <div class="form_bts">
                    <input class="form_bt" name="atualizar" type="submit" value="Atualizar" />
                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

            <script type="text/javascript">

                altera_campos_aud();
                oculta_campos_aud();
                oculta_motivo_aud();

                $(function() {
                    $( "#tipo_aud_0" ).focus();
                    $( "#data_aud" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });
                });

            </script>

<?php include 'footer.php';?>