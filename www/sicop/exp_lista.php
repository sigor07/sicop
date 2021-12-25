<?php
if ( !isset( $_SESSION ) ) session_start();

require 'init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$imp_chefia   = get_session( 'imp_chefia', 'int' );
$imp_cadastro = get_session( 'imp_cadastro', 'int' );
$n_imp_n = 1;

$motivo_pag = 'EXPORTÇÃO DA LISTA PARA EXCEL';

if ( $imp_chefia < $n_imp_n and $imp_cadastro < $n_imp_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$data_in  = get_post( 'data_in', 'busca' );
$data_out = get_post( 'data_out', 'busca' );
$op       = get_post( 'op', 'busca' );
$unidade  = get_post( 'unidade', 'int' );
$n_cela   = get_post( 'n_cela', 'int' );
$n_raio   = get_post( 'n_raio', 'int' );
$tipo_sit = get_post( 'tipo_sit', 'int' );



$q_tipo_sit = 'SELECT `idtipo_sit`, `tipo_sit` FROM `tipo_sit_det_busca` ORDER BY `idtipo_sit` ASC';
$q_proced = 'SELECT `idunidades`, `unidades` FROM `unidades` WHERE `in` = TRUE ORDER BY `unidades`';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_tipo_sit = $model->query( $q_tipo_sit );
$q_proced   = $model->query( $q_proced );

// fechando a conexao
$model->closeConnection();

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Gerar lista de ' . SICOP_DET_DESC_L . 's';

require 'cab.php';
$trail = new Breadcrumb();
$trail->add( $desc_pag, $_SERVER['PHP_SELF'], 3 );
$trail->output();
?>



            <p class="descript_page">GERAR LISTA DE <?php echo SICOP_DET_DESC_U; ?>S PARA EXPORTAÇÃO PARA EXCEL</p>

            <form action="" method="post" name="exp_lista_det" id="exp_lista_det">

                <table class="busca_form"><!--remacc(campobusca);-->
                    <tr>
                        <td class="bf_det_legend"><?php echo SICOP_RAIO ?>:</td>
                        <td class="bf_det_field">
                            <select name="n_raio" class="CaixaTexto" id="n_raio" onchange="$.monta_box_cela();">
                                <option value="" selected="selected">Selecione</option>
                            </select>
                            &nbsp; <?php echo SICOP_CELA ?>:
                            <select name="n_cela" class="CaixaTexto" id="n_cela">
                                <option value="" selected="selected">Escolha o raio</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="bf_det_legend">Procedência:</td>
                        <td class="bf_det_field">
                            <select name="unidade" class="CaixaTexto" id="unidade">
                                <option value="" >Selecione...</option>
                                <?php while ( $d_proced = $q_proced->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_proced['idunidades']; ?>" <?php echo $d_proced['idunidades'] == $unidade ? 'selected="selected"' : ''; ?>><?php echo $d_proced['unidades']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="bf_det_legend">Data da inclusão:</td>
                        <td class="bf_det_field">
                            <input name="data_in_ini" type="text" class="CaixaTexto" id="data_in_ini" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_in ?>" size="12" maxlength="10" /> e
                            <input name="data_in_fim" type="text" class="CaixaTexto" id="data_in_fim" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_out ?>" size="12" maxlength="10" />

                        </td>
                    </tr>
                    <tr>
                        <td class="bf_det_legend">Data da exclusão:</td>
                        <td class="bf_det_field">
                            <input name="data_out_ini" type="text" class="CaixaTexto" id="data_out_ini" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_in ?>" size="12" maxlength="10" /> e
                            <input name="data_out_fim" type="text" class="CaixaTexto" id="data_out_fim" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_out ?>" size="12" maxlength="10" />

                        </td>
                    </tr>
                    <tr>
                        <td class="bf_det_legend">Situação do preso:</td>
                        <td class="bf_det_field">
                            <select name="tipo_sit" class="CaixaTexto" id="tipo_sit">
                                <option value="" >Todos</option>
                                <?php while ( $d_tipo_sit = $q_tipo_sit->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_tipo_sit['idtipo_sit']; ?>" <?php echo $d_tipo_sit['idtipo_sit'] == $tipo_sit ? 'selected="selected"' : ''; ?>><?php echo $d_tipo_sit['tipo_sit']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="bf_det_legend">Ordenar por:</td>
                        <td class="bf_det_field">
                            <select name="op" class="CaixaTexto" id="op">
                                <option value="nome" selected="selected">Nome</option>
                                <option value="matr" >Matrícula</option>
                                <option value="raio" ><?php echo SICOP_RAIO ?>/<?php echo SICOP_CELA ?></option>
                                <option value="proc" >Procedência</option>
                                <option value="incl" >Data da inclusão</option>
                                <option value="dest" >Destino</option>
                                <option value="excl" >Data da exclusão</option>
                            </select>
                        </td>
                    </tr>
                </table>

                <p class="sub_title_page"> Além do nome e matrícula, incluir os seguintes campos:</p>

                <table class="export_ckb grid">

                    <tbody>
                        <tr class="even">
                            <td class="legend">R.G.</td>
                            <td class="tb_ck"><input name="rg" type="checkbox" class="mark_row"/></td>
                        </tr>
                        <tr class="even">
                            <td class="legend">Execução</td>
                            <td class="tb_ck"><input name="exec_ck" type="checkbox" class="mark_row"/></td>
                        </tr>
                        <tr class="even">
                            <td class="legend"><?php echo SICOP_RAIO ?> / <?php echo SICOP_CELA ?></td>
                            <td class="tb_ck"><input name="rc_ck" type="checkbox" class="mark_row"/></td>
                        </tr>
                        <tr class="even">
                            <td class="legend">Procedência</td>
                            <td class="tb_ck"><input name="proc_ck" type="checkbox" class="mark_row"/></td>
                        </tr>
                        <tr class="even">
                            <td class="legend">Data da inclusão</td>
                            <td class="tb_ck"><input name="data_in_ck" type="checkbox" class="mark_row"/></td>
                        </tr>
                        <tr class="even">
                            <td class="legend">Destino / Local de trânsito</td>
                            <td class="tb_ck"><input name="dest_ck" type="checkbox" class="mark_row"/></td>
                        </tr>
                        <tr class="even">
                            <td class="legend">Data da exclusão / do trânsito</td>
                            <td class="tb_ck"><input name="data_out_ck" type="checkbox" class="mark_row"/></td>
                        </tr>
                        <tr class="even">
                            <td class="legend">Nome do pai</td>
                            <td class="tb_ck"><input name="pai" type="checkbox" class="mark_row"/></td>
                        </tr>
                        <tr class="even">
                            <td >Nome da mãe</td>
                            <td class="tb_ck"><input name="mae" type="checkbox" class="mark_row"/></td>
                        </tr>
                        <tr class="even">
                            <td class="legend">Data de nascimento</td>
                            <td class="tb_ck"><input name="data_nasc" type="checkbox" class="mark_row"/></td>
                        </tr>
                        <tr class="even">
                            <td class="legend">Naturalidade</td>
                            <td class="tb_ck"><input name="nat" type="checkbox" class="mark_row"/></td>
                        </tr>
                    </tbody>

                    <tfoot>
                        <tr>
                            <td  class="tb_ck_leg">todos:</td>
                            <td class="tb_ck"><input type="checkbox" name="todos" value="todos" /></td>
                        </tr>
                    </tfoot>

                </table>

                <div class="form_bts">
                    <input class="form_bt" type="button" name="gerar" id="exp_lista" value="Gerar lista" />
                </div>

                <input name="gera" type="hidden" id="gera" value="gera" />

            </form>

            <script type="text/javascript">

                $(function() {

                    $("input#exp_lista").live( "click", function(){

                        $.submit_form( 'exp_lista_det', 'export/exp_lista_det.php', '', 1 );
                        return true;

                    });

                    $.monta_box_raio();

                    $( "#n_raio" ).focus();

                    $( "#data_in_ini, #data_in_fim, #data_out_ini, #data_out_fim" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });

                });

            </script>

<?php include 'footer.php';?>