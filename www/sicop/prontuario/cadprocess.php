<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_pront   = get_session( 'n_pront', 'int' );
$n_pront_n = 3;

if ( $n_pront < $n_pront_n ) {
    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
    salvaLog($mensagem);
    exit;
}

$iddet = get_get( 'iddet', 'int' );

if ( empty( $iddet ) ) {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso direto à página de alteração de processos.\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( '', 1 );
    exit;
}

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Cadastrar processo';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) {
    $pag_atual .= '?' . $qs;
}
$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 4 );
$trail->output();
?>


            <p class="descript_page">CADASTRAR PROCESSO</p>

            <?php include 'quali/det_basic.php'; ?>

            <p class="table_leg">Processo</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendprocess.php" method="post" name="proc_sing" id="proc_sing" onSubmit="return validacadproc();">

                <table class="edit">

                    <tr >
                        <td class="cp_min">
                            Execução: <input name="gra_num_exec" type="text" class="CaixaTexto" id="gra_num_exec" onkeypress="return blockChars(event,2);" size="4" maxlength="3" />
                        </td>
                        <td class="cp_min">Entrada: <input name="gra_num_in" type="text" class="CaixaTexto" id="gra_num_in" onkeypress="return blockChars(event,2);" value="<?php echo $d_det['n_passagem']; ?>" size="4" maxlength="3" /></td>
                        <td class="cp_min">&nbsp;</td>
                    </tr>

                    <tr >
                        <td class="cp_min">Nº do inquérito: <input name="gra_num_inq" type="text" class="CaixaTexto" id="gra_num_inq" onkeypress="return blockChars(event,6);" size="22" maxlength="20" /></td>
                        <td class="cp_min">F/P: <input name="gra_f_p" type="text" class="CaixaTexto" id="gra_f_p" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event,1);" size="2" maxlength="2" /></td>
                        <td class="cp_min">
                            Preso:
                            <input name="gra_preso" type="radio" id="gra_preso_0" value="1" /> Sim  &nbsp;&nbsp;
                            <input name="gra_preso" type="radio" id="gra_preso_1" value="0" checked="checked" /> Não
                        </td>
                    </tr>

                    <tr >
                        <td class="cp_min">Nº do processo: <input name="gra_num_proc" type="text" class="CaixaTexto" id="gra_num_proc" onkeypress="return blockChars(event,6);" size="22" maxlength="20" /></td>
                        <td class="cp_min">Data do delito: <input name="gra_data_delito" type="text" class="CaixaTexto" id="gra_data_delito" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" size="12" maxlength="10" /></td>
                        <td class="cp_min">Data da sentença: <input name="gra_data_sent" type="text" class="CaixaTexto" id="gra_data_sent" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" size="12" maxlength="10" /></td>
                    </tr>

                    <tr >
                        <td class="cp_min">Vara: <input name="gra_vara" type="text" class="CaixaTexto" id="gra_vara" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event,4);" size="17" maxlength="15" /></td>
                        <td class="cp_min">Comarca: <input name="gra_comarca" type="text" class="CaixaTexto" id="gra_comarca" onblur="upperMe(this); rpccidade( this, 1 );" onkeypress="return blockChars(event,4);" size="35" maxlength="30" /></td>
                        <td class="cp_min">
                            Pena:
                            <input name="gra_p_ano" type="text" class="CaixaTexto" id="gra_p_ano" onkeypress="return blockChars(event,2);" size="3" maxlength="3" /> anos,
                            <input name="gra_p_mes" type="text" class="CaixaTexto" id="gra_p_mes" onkeypress="return blockChars(event,2);" size="1" maxlength="2" /> meses,
                            <input name="gra_p_dia" type="text" class="CaixaTexto" id="gra_p_dia" onkeypress="return blockChars(event,2);" size="1" maxlength="2" /> dias
                        </td>
                    </tr>

                    <tr>
                        <td class="cp_min">
                            Medida de segurança:
                            <input name="gra_med_seg" type="radio" id="gra_med_seg_0" value="1" /> Sim  &nbsp;&nbsp;
                            <input name="gra_med_seg" type="radio" id="gra_med_seg_1" value="0" checked="checked" /> Não
                        </td>
                        <td class="cp_min">
                            Crime hediondo:
                            <input name="gra_hediondo" type="radio" id="gra_hediondo_0" value="1" /> Sim  &nbsp;&nbsp;
                            <input name="gra_hediondo" type="radio" id="gra_hediondo_1" value="0" checked="checked" /> Não
                        </td>
                        <td class="cp_min">
                            Extinção da punibilidade:
                            <input name="gra_campo_x" type="radio" id="gra_campo_x_0" value="1" /> Sim  &nbsp;&nbsp;
                            <input name="gra_campo_x" type="radio" id="gra_campo_x_1" value="0" checked="checked" /> Não
                        </td>
                    </tr>

                    <tr>
                        <td class="cp_min">Artigos: <input name="gra_artigos" type="text" class="CaixaTexto" id="gra_artigos" onblur="upperMe(this); rpcartigo(this);" onkeypress="return blockChars(event,4);" size="35" maxlength="50" /></td>
                        <td class="cp_min">
                            Consumado:
                            <input name="gra_consumado" type="radio" id="gra_consumado_0" value="1" checked="checked" />Sim  &nbsp;&nbsp;
                            <input name="gra_consumado" type="radio" id="gra_consumado_1" value="0" />Não
                        </td>
                        <td class="cp_min">
                            Federal:
                            <input name="gra_fed" type="radio" id="gra_fed_0" value="1" /> Sim  &nbsp;&nbsp;
                            <input name="gra_fed" type="radio" id="gra_fed_1" value="0" checked="checked"/> Não
                        </td>
                    </tr>

                    <tr>
                        <td class="cp_med" colspan="2">Regime: <input name="gra_regime" type="text" class="CaixaTexto" id="gra_regime" onkeypress="return blockChars(event,4);" size="13" maxlength="10" /></td>
                        <td class="cp_min">
                            Outro estado:
                            <input name="gra_outro_est" type="radio" id="gra_outro_est_0" value="1" /> Sim  &nbsp;&nbsp;
                            <input name="gra_outro_est" type="radio" id="gra_outro_est_1" value="0" checked="checked" /> Não
                        </td>
                    </tr>

                    <tr>
                        <td class="cp_grd" colspan="3">Situação: <input name="gra_sit_atual" type="text" class="CaixaTexto" id="gra_sit_atual" onblur="upperMe(this);" onkeypress="return blockChars(event,4);" size="35" maxlength="30" /></td>
                    </tr>

                    <tr>
                        <td class="cp_grd" colspan="3" valign="middle">Observação: <input name="gra_obs" type="text" class="CaixaTexto" id="gra_obs" onblur="upperMe(this);" onkeypress="return blockChars(event, 4);" size="80" /></td>
                    </tr>

                </table>

                <input name="iddet" type="hidden" id="iddet" value="<?php echo $iddet; ?>" />
                <input name="proced" type="hidden" id="proced" value="3" />

                <div class="form_bts">
                    <input class="form_bt" name="cadastrar" type="submit" value="Cadastrar" />
                    <input class="form_bt" name="cadadd" type="submit" value="Cadastrar e adicionar outro" />
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