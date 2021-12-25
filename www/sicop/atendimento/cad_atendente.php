<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag         = link_pag();
$motivo_pag  = 'CADASTRAR ATENDENTE';

$q_cargo = 'SELECT
              `id_ati_user_cargo`,
              `ati_user_cargo`
            FROM
              `ati_users_cargos`
            WHERE
              `portaria` = TRUE
            ORDER BY
              `ati_user_cargo`';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_cargo = $model->query( $q_cargo );

// fechando a conexao
$model->closeConnection();

if ( !$q_cargo ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "Falha na consulta ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );


    echo msg_js( 'FALHA!', 1 );
    exit;

}

$cont = $q_cargo->num_rows;
if ( $cont < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "A consulta retornou 0 ocorrências ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 1 );
    exit;

}

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Cadastrar atendente';

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs        = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3 );
$trail->output();
?>
        <script type="text/javascript" src="<?php echo SICOP_ABS_PATH; ?>js/funcoes.js"></script>
        <script type="text/javascript" src="<?php echo SICOP_ABS_PATH; ?>js/valida.js"></script>
        <script type="text/javascript" src="<?php echo SICOP_ABS_PATH; ?>js/jquery-1.5.1.min.js"></script>
        <div class="no_print">

            <p class="descript_page">CADASTRAR ANTENDENTE</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendati.php" method="post" name="add_atend" id="add_atend" >

                <table class="edit">
                    <tr>
                        <td class="table_form_label">
                            Nome:
                        </td>
                        <td class="form_add_atendente_field">
                            <input name="ati_user_nome" type="text" class="CaixaTexto" id="ati_user_nome" onkeypress="return blockChars(event, 4);" size="60" maxlength="50" />
                        </td>
                    </tr>
                    <tr>
                        <td class="table_form_label">
                            Função:
                        </td>
                        <td class="form_add_atendente_field">
                            <select name="ati_user_cargo" class="CaixaTexto" id="ati_user_cargo">
                                <option value="" >Selecione...</option>
                                <?php while ( $d_cargo = $q_cargo->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_cargo['id_ati_user_cargo']; ?>"><?php echo $d_cargo['ati_user_cargo']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="table_form_label">
                            Documento:
                        </td>
                        <td class="form_add_atendente_field">
                            <input name="ati_user_doc" type="text" class="CaixaTexto" id="ati_user_doc" onkeypress="return blockChars(event, 4);" size="60" maxlength="50" />
                        </td>
                    </tr>
                </table><!-- /table class="edit" -->

                <input type="hidden" name="iddet" id="iddet" value="<?php echo $iddet; ?>" />
                <input type="hidden" name="proced" id="proced" value="3" />

                <div class="form_bts">
                    <input name="atualizar" type="submit" value="Cadastrar" />&nbsp;&nbsp;&nbsp;
                    <input name="" type="button" onClick="history.go(-1)" value="Cancelar" />
                </div>

            </form><!-- /form name="add_atend" -->


            <script type="text/javascript">

                $(function() {
                    $("form").submit(function() {
                        if ( v_cad_ati_user() == true ) {
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