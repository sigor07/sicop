<?php
if ( !isset( $_SESSION  ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_pront   = get_session( 'n_pront', 'int' );
$n_pront_n = 3;

$motivo_pag = 'ALTERAÇÃO DE DADOS PROCESSUAIS D' . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U;

if ( $n_pront < $n_pront_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$iddet = get_get( 'iddet', 'int' );

if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( IDENTIFICADOR EM BRANCO - $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$query_art         = 'SELECT idartigo, artigo FROM tipoartigo ORDER BY artigo ASC';
$query_sit_pr      = 'SELECT `idsit_proc`,`sit_proc` FROM `tiposituacaoprocessual` ORDER BY `sit_proc` ASC';
$query_conduta_ant = 'SELECT `idconduta`, `conduta` FROM `tipoconduta` WHERE `idconduta` != 4 ORDER BY `conduta` ASC';
$q_local_prisao    = 'SELECT `unidades`.`idunidades`, `unidades`.`unidades` FROM `unidades` WHERE `in` = TRUE ORDER BY `unidades`.`unidades`';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_art         = $model->query( $query_art );
$query_sit_pr      = $model->query( $query_sit_pr );
$query_conduta_ant = $model->query( $query_conduta_ant );
$q_local_prisao    = $model->query( $q_local_prisao );

// fechando a conexao
$model->closeConnection();

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Alterar dados processuais';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 5 );
$trail->output();
?>

            <p class="descript_page">ALTERAR DADOS PROCESSUAIS D<?php echo SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U; ?></p>

            <?php include 'quali/det_pront.php'; ?>

            <p class="table_leg">Dados processuais</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/senddetprocess.php" method="post" name="detproc_sing" id="detproc_sing">

                <table class="edit">

                    <tr>
                        <td class="detproc_med">
                            Situação processual:
                            <select name="sit_proc" class="CaixaTexto" id="sit_proc">
                                <option value="" >Selecione...</option>
                                <?php while( $dados_sit_pr = $query_sit_pr->fetch_assoc() ) { ?>
                                <option value="<?php echo $dados_sit_pr['idsit_proc'];?>" <?php echo $dados_sit_pr['idsit_proc'] == $d_det['cod_sit_proc'] ? 'selected="selected"' : ''; ?>><?php echo $dados_sit_pr['sit_proc'];?></option>
                                <?php };?>
                             </select>
                        </td>

                        <td class="detproc_med">
                            Artigo:
                            <select name="artigo" class="CaixaTexto" id="artigo">
                                <option value="" >Selecione...</option>
                                <?php while($dados_art = $query_art->fetch_assoc() ) { ?>
                                <option value="<?php echo $dados_art['idartigo'];?>" <?php echo $dados_art['idartigo'] == $d_det['cod_artigo'] ? 'selected="selected"' : ''; ?>><?php echo $dados_art['artigo'];?></option>
                                <?php };?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td class="detproc_med">
                            Primário:
                            <input name="primario" type="radio" id="primario_0" value="1" <?php echo $d_det['primario'] == "1" || is_null( $d_det['primario'] ) ? 'checked="checked"' : ''; ?> /> Sim &nbsp;
                            <input name="primario" type="radio" id="primario_1" value="0" <?php echo $d_det['primario'] == "0" ? 'checked="checked"' : ''; ?> /> Não
                        </td>
                        <td class="detproc_med">Execução: <input name="execucao" type="text" class="CaixaTexto" id="execucao" onkeypress="mascara(this, mexec); return blockChars(event, 2);" value="<?php  if ( !empty( $d_det['execucao'] ) ) echo number_format( $d_det['execucao'], 0, '', '.' ) ?>" size="11" maxlength="9" /></td>
                    </tr>

                    <tr>
                        <td class="detproc_med">
                            Conduta da unidade anterior:
                            <select name="conduta_ant" class="CaixaTexto" id="conduta_ant">
                                <option value="" >Selecione...</option>
                                <?php while($d_conduta_ant = $query_conduta_ant->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_conduta_ant['idconduta'];?>" <?php echo $d_conduta_ant['idconduta'] == $d_det['conduta_ant'] ? 'selected="selected"' : ''; ?>><?php echo $d_conduta_ant['conduta'];?></option>
                                <?php };?>
                            </select>
                        </td>
                        <td class="detproc_med">Data da reabilitação: <input name="data_reab" type="text" class="CaixaTexto" id="data_reab" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $d_det['data_reab_f'];?>" size="12" maxlength="10" /></td>
                    </tr>

                    <tr>
                        <td class="detproc_med">
                            Local da prisão:
                            <select name="local_prisao" class="CaixaTexto" id="local_prisao">
                                <option value="" >Selecione...</option>
                                <?php while ( $d_local_prisao = $q_local_prisao->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_local_prisao['idunidades'];?>" <?php echo $d_local_prisao['idunidades'] == $d_det['cod_local_prisao'] ? 'selected="selected"' : ''; ?>><?php echo $d_local_prisao['unidades'];?></option>
                                <?php };?>
                            </select>
                        </td>
                        <td class="detproc_med">Data da prisão: <input name="data_prisao" type="text" class="CaixaTexto" id="data_prisao" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $d_det['data_prisao'];?>" size="12" maxlength="10" /></td>
                    </tr>

                    <tr >
                        <td colspan="2" class="detproc_grand">MOTIVO DA PRISÃO ATUAL</td>
                    </tr>

                    <tr >
                        <td colspan="2" class="detproc_grand"><textarea name="motivo_prisao" cols="150" rows="3" class="CaixaTexto" id="motivo_prisao" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 4);" onkeydown="textCounter(this, 200);" onkeyup="textCounter(this, 200);"><?php echo $d_det['motivo_prisao'];?></textarea></td>
                    </tr>

                </table>

                <input name="iddet" type="hidden" id="iddet" value="<?php echo $iddet;?>" />

                <div class="form_bts">
                    <input class="form_bt" name="atualizar" type="submit" onclick="return validacaddetproc();" value="Atualizar" />
                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>
            </form>

            <script type="text/javascript">

                $(function() {
                    $( "#sit_proc" ).focus();
                    $( "#data_reab, #data_prisao" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });
                });

            </script>

<?php include 'footer.php'; ?>