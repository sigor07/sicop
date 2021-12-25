<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_pront   = get_session( 'n_pront', 'int' );
$n_pront_n = 3;

$motivo_pag = 'ALTERAR PROCESSO';

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

$idproc = get_get( 'idproc', 'int' );

if ( empty( $idproc ) ){

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( IDENTIFICADOR EM BRANCO - $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$query_grade = "SELECT
                  `idprocesso`,
                  `cod_detento`,
                  `gra_preso`,
                  `gra_num_in`,
                  `gra_num_exec`,
                  `gra_num_inq`,
                  `gra_f_p`,
                  `gra_num_proc`,
                  `gra_campo_x`,
                  `gra_med_seg`,
                  `gra_hediondo`,
                  `gra_fed`,
                  `gra_outro_est`,
                  `gra_consumado`,
                  `gra_vara`,
                  `gra_comarca`,
                  `gra_artigos`,
                  `gra_data_delito`,
                  DATE_FORMAT(`gra_data_delito`, '%d/%m/%Y') AS gra_data_delito_f,
                  `gra_data_sent`,
                  DATE_FORMAT(`gra_data_sent`, '%d/%m/%Y') AS gra_data_sent_f,
                  `gra_p_ano`,
                  `gra_p_mes`,
                  `gra_p_dia`,
                  `gra_regime`,
                  `gra_sit_atual`,
                  `gra_obs`,
                  `user_add`,
                  DATE_FORMAT(`data_add`, '%d/%m/%Y às %H:%i') AS data_add_f,
                  `user_up`,
                  DATE_FORMAT(`data_up`, '%d/%m/%Y às %H:%i') AS data_up_f
                FROM
                  `grade`
                WHERE
                  `idprocesso` = $idproc
                LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_grade = $model->query( $query_grade );

// fechando a conexao
$model->closeConnection();

if( !$query_grade ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_g = $query_grade->num_rows;

if( $cont_g < 1 ) {
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ocorrências ( ALTERAÇÃO DE PROCESSO ).\n\n Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$d_grade = $query_grade->fetch_assoc();

$iddet = $d_grade['cod_detento'];

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Alterar processo';

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


            <p class="descript_page">ALTERAR PROCESSO</p>

            <?php include 'quali/det_basic.php'; ?>

            <p class="table_leg">Processo</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendprocess.php" method="post" name="proc_sing" id="proc_sing" onSubmit="return validacadproc();">

                <table class="edit">

                    <tr >
                        <td class="cp_min">
                            Execução: <input name="gra_num_exec" type="text" class="CaixaTexto" id="gra_num_exec" onkeypress="return blockChars(event,2);" value="<?php echo $d_grade['gra_num_exec']; ?>" size="4" maxlength="3" />
                        </td>
                        <td class="cp_min">Entrada: <input name="gra_num_in" type="text" class="CaixaTexto" id="gra_num_in" onkeypress="return blockChars(event,2);" value="<?php echo $d_grade['gra_num_in']; ?>" size="4" maxlength="3" /></td>
                        <td class="cp_min">ID no sistema: <?php echo $d_grade['idprocesso']; ?></td>
                    </tr>

                    <tr >
                        <td class="cp_min">Nº do inquérito: <input name="gra_num_inq" type="text" class="CaixaTexto" id="gra_num_inq" onkeypress="return blockChars(event,6);" value="<?php echo $d_grade['gra_num_inq']; ?>" size="22" maxlength="20" /></td>
                        <td class="cp_min">F/P: <input name="gra_f_p" type="text" class="CaixaTexto" id="gra_f_p" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event,1);" value="<?php echo $d_grade['gra_f_p']; ?>" size="2" maxlength="2" /></td>
                        <td class="cp_min">
                            Preso:
                            <input name="gra_preso" type="radio" id="gra_preso_0" value="1" <?php echo $d_grade['gra_preso'] == "1" ? 'checked="checked"' : ''; ?>/> Sim  &nbsp;&nbsp;
                            <input name="gra_preso" type="radio" id="gra_preso_1" value="0" <?php echo empty($d_grade['gra_preso']) ? 'checked="checked"' : ''; ?>/> Não
                        </td>
                    </tr>

                    <tr>
                        <td class="cp_min">Nº do processo: <input name="gra_num_proc" type="text" class="CaixaTexto" id="gra_num_proc" onkeypress="return blockChars(event,6);" value="<?php echo $d_grade['gra_num_proc']; ?>" size="22" maxlength="20" /></td>
                        <td class="cp_min">Data do delito: <input name="gra_data_delito" type="text" class="CaixaTexto" id="gra_data_delito" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $d_grade['gra_data_delito_f']; ?>" size="12" maxlength="10" /></td>
                        <td class="cp_min">Data da sentença: <input name="gra_data_sent" type="text" class="CaixaTexto" id="gra_data_sent" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $d_grade['gra_data_sent_f']; ?>" size="12" maxlength="10" /></td>
                    </tr>

                    <tr >
                        <td class="cp_min">Vara: <input name="gra_vara" type="text" class="CaixaTexto" id="gra_vara" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event,4);" value="<?php echo $d_grade['gra_vara']; ?>" size="17" maxlength="15" /></td>
                        <td class="cp_min">Comarca: <input name="gra_comarca" type="text" class="CaixaTexto" id="gra_comarca" onblur="upperMe(this); rpccidade( this, 1 );" onkeypress="return blockChars(event,4);" value="<?php echo $d_grade['gra_comarca']; ?>" size="35" maxlength="30" /></td>
                        <td class="cp_min">
                            Pena:
                            <input name="gra_p_ano" type="text" class="CaixaTexto" id="gra_p_ano" onkeypress="return blockChars(event,2);" value="<?php echo $d_grade['gra_p_ano']; ?>" size="3" maxlength="3" /> anos,
                            <input name="gra_p_mes" type="text" class="CaixaTexto" id="gra_p_mes" onkeypress="return blockChars(event,2);" value="<?php echo $d_grade['gra_p_mes']; ?>" size="1" maxlength="2" /> meses,
                            <input name="gra_p_dia" type="text" class="CaixaTexto" id="gra_p_dia" onkeypress="return blockChars(event,2);" value="<?php echo $d_grade['gra_p_dia']; ?>" size="1" maxlength="2" /> dias
                        </td>
                    </tr>

                    <tr >
                        <td class="cp_min">
                            Medida de segurança:
                            <input name="gra_med_seg" type="radio" id="gra_med_seg_0" value="1" <?php echo $d_grade['gra_med_seg'] == "1" ? 'checked="checked"' : ''; ?>/> Sim  &nbsp;&nbsp;
                            <input name="gra_med_seg" type="radio" id="gra_med_seg_1" value="0" <?php echo empty($d_grade['gra_med_seg']) ? 'checked="checked"' : ''; ?>/> Não
                        </td>
                        <td class="cp_min">
                            Crime hediondo:
                            <input name="gra_hediondo" type="radio" id="gra_hediondo_0" value="1" <?php echo $d_grade['gra_hediondo'] == "1" ? 'checked="checked"' : ''; ?>/> Sim  &nbsp;&nbsp;
                            <input name="gra_hediondo" type="radio" id="gra_hediondo_1" value="0" <?php echo empty($d_grade['gra_hediondo']) ? 'checked="checked"' : ''; ?>/> Não
                        </td>
                        <td class="cp_min">
                            Extinção da punibilidade:
                            <input name="gra_campo_x" type="radio" id="gra_campo_x_0" value="1" <?php echo $d_grade['gra_campo_x'] == "1" ? 'checked="checked"' : ''; ?>/> Sim  &nbsp;&nbsp;
                            <input name="gra_campo_x" type="radio" id="gra_campo_x_1" value="0" <?php echo empty($d_grade['gra_campo_x']) ? 'checked="checked"' : ''; ?>/> Não
                        </td>
                    </tr>

                    <tr >
                        <td class="cp_min">Artigos: <input name="gra_artigos" type="text" class="CaixaTexto" id="gra_artigos" onblur="upperMe(this); rpcartigo(this);" onkeypress="return blockChars(event,4);" value='<?php echo $d_grade['gra_artigos']; ?>' size="35" maxlength="50" /></td>
                        <td class="cp_min">
                            Consumado:
                            <input name="gra_consumado" type="radio" id="gra_consumado_0" value="1" <?php echo $d_grade['gra_consumado'] == "1" ? 'checked="checked"' : ''; ?>/>Sim  &nbsp;&nbsp;
                            <input name="gra_consumado" type="radio" id="gra_consumado_1" value="0" <?php echo empty($d_grade['gra_consumado']) ? 'checked="checked"' : ''; ?>/>Não
                        </td>
                        <td class="cp_min">
                            Federal:
                            <input name="gra_fed" type="radio" id="gra_fed_0" value="1" <?php echo $d_grade['gra_fed'] == "1" ? 'checked="checked"' : ''; ?>/> Sim  &nbsp;&nbsp;
                            <input name="gra_fed" type="radio" id="gra_fed_1" value="0" <?php echo empty($d_grade['gra_fed']) ? 'checked="checked"' : ''; ?>/> Não
                        </td>
                    </tr>

                    <tr >
                        <td class="cp_med" colspan="2">Regime: <input name="gra_regime" type="text" class="CaixaTexto" id="gra_regime" onkeypress="return blockChars(event,4);" value="<?php echo $d_grade['gra_regime']; ?>" size="13" maxlength="10" /></td>
                        <td class="cp_min">
                            Outro estado:
                            <input name="gra_outro_est" type="radio" id="gra_outro_est_0" value="1" <?php echo $d_grade['gra_outro_est'] == "1" ? 'checked="checked"' : ''; ?>/> Sim  &nbsp;&nbsp;
                            <input name="gra_outro_est" type="radio" id="gra_outro_est_1" value="0" <?php echo empty($d_grade['gra_outro_est']) ? 'checked="checked"' : ''; ?>/> Não
                        </td>
                    </tr>

                    <tr >
                        <td class="cp_grd" colspan="3">Situação: <input name="gra_sit_atual" type="text" class="CaixaTexto" id="gra_sit_atual" onblur="upperMe(this);" onkeypress="return blockChars(event,4);" value="<?php echo $d_grade['gra_sit_atual']; ?>" size="35" maxlength="30" /></td>
                    </tr>

                    <tr >
                        <td class="cp_grd" colspan="3" valign="middle">Observação: <input name="gra_obs" type="text" class="CaixaTexto" id="gra_obs" onblur="upperMe(this);" onkeypress="return blockChars(event, 4);" value="<?php echo $d_grade['gra_obs']; ?>" size="80" /></td>
                    </tr>

                </table>

                <input type="hidden" name="iddet" id="iddet" value="<?php echo $iddet; ?>" />
                <input type="hidden" name="idprocesso" id="idprocesso" value="<?php echo $d_grade['idprocesso']; ?>" />
                <input type="hidden" name="proced" id="proced" value="1" />

                <div class="form_bts">
                    <input class="form_bt" name="atualizar" type="submit" value="Atualizar" />
                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

            <script type="text/javascript">

                $(function() {
                    $( "#gra_num_exec" ).focus();
                    $( "#gra_data_delito, #gra_data_sent" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });
                });

            </script>

<?php include 'footer.php'; ?>