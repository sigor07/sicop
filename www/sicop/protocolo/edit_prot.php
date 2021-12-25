<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_prot   = get_session( 'n_prot', 'int' );
$n_prot_n = 3;

$motivo_pag = 'ALTERAÇÃO DE PROTOCOLO';

if ($n_prot < $n_prot_n) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$idprot = get_get( 'idprot', 'int' );

if ( empty( $idprot ) ){

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( IDENTIFICADOR EM BRANCO - $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );

    exit;

}

$q_prot = "SELECT
             `protocolo`.`idprot`,
             `protocolo`.`prot_num`,
             `protocolo`.`prot_ano`,
             `protocolo`.`prot_cod_modo_in`,
             `protocolo`.`prot_cod_tipo_doc`,
             `protocolo`.`prot_assunto`,
             `protocolo`.`prot_origem`,
             `protocolo`.`prot_cod_setor`,
             DATE_FORMAT ( `protocolo`.`prot_data_in`, '%d/%m/%Y' ) AS prot_data_in_f,
             DATE_FORMAT ( `protocolo`.`prot_hora_in`, '%H:%i' ) AS prot_hora_in_f,
             `protocolo`.`prot_user_rec`,
             `protocolo`.`prot_canc`
           FROM
             `protocolo`
           WHERE
             `protocolo`.`idprot` = $idprot
           LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_prot = $model->query( $q_prot );

// fechando a conexao
$model->closeConnection();

if( !$q_prot ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_prot = $q_prot->num_rows;

if($cont_prot < 1) {
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ocorrências ( ALTERAR PROTOCOLO ).\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$d_prot = $q_prot->fetch_assoc();

$id_user_rec = $d_prot['prot_user_rec'];
$recebido = false;

// se tiver $id_user_rec é que ja foi recebido
if ( !empty( $id_user_rec ) ) {

    $recebido = true;

}

$q_modo_in = 'SELECT `id_modo_in`, `modo_in` FROM `tipo_prot_modo_in` ORDER BY `modo_in` ASC';
$q_tipo_doc = 'SELECT `id_tipo_doc`, `tipo_doc` FROM `tipo_prot_doc` ORDER BY `tipo_doc` ASC';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_modo_in = $model->query( $q_modo_in );
$q_tipo_doc = $model->query( $q_tipo_doc );

// fechando a conexao
$model->closeConnection();

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Alterar documento';

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

            <p class="descript_page">ALTERAR DOCUMENTO</p>

            <!--onSubmit="return validacadaliasdet()"-->
            <form action="<?php echo SICOP_ABS_PATH ?>send/sendprot.php" method="post" name="cadprot" id="cadprot">
                <table class="edit">
                    <tr>
                        <td class="prot_td_med">
                            Modo de entrada:
                            <select name="modo_in" class="CaixaTexto" id="modo_in">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while ( $d_modo_in = $q_modo_in->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_modo_in['id_modo_in'];?>" <?php echo $d_modo_in['id_modo_in'] == $d_prot['prot_cod_modo_in'] ? 'selected="selected"' : ''; ?>><?php echo $d_modo_in['modo_in'];?></option>
                                <?php };?>
                            </select>
                        </td>
                        <td class="prot_td_med">
                            Tipo:
                            <select name="tipo_doc" class="CaixaTexto" id="tipo_doc">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while ( $d_tipo_doc = $q_tipo_doc->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_tipo_doc['id_tipo_doc'];?>" <?php echo $d_tipo_doc['id_tipo_doc'] == $d_prot['prot_cod_tipo_doc'] ? 'selected="selected"' : ''; ?>><?php echo $d_tipo_doc['tipo_doc'];?></option>
                                <?php };?>
                            </select>
                        </td>
                        <td class="prot_td_med">
                            Protocolo Nº: <b><?php echo number_format( $d_prot['prot_num'], 0, '', '.' ) . '/' . $d_prot['prot_ano']; ?></b> às <input name="prot_hora_in" type="text" class="CaixaTexto" id="prot_hora_in" onblur="verifica_hora(this, this.value)" onkeypress="mascara_hora(this, this.value); return blockChars(event, 2);" value="<?php echo $d_prot['prot_hora_in_f'];?>" size="5" maxlength="5" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div class="prot_leg_m">Assunto:</div> <textarea name="prot_assunto" id="prot_assunto" cols="125" rows="3" class="CaixaTexto" onblur="upperMe(this);" onkeypress="return blockChars(event, 4);"><?php echo $d_prot['prot_assunto'];?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="padding: 0 3px;">

                            <div class="prot_m">

                                <div class="prot_leg">Origem:</div>
                                <input name="prot_origem" type="text" class="CaixaTexto" id="prot_origem" onblur="upperMe(this);" onkeypress="return blockChars(event, 4);" value="<?php echo $d_prot['prot_origem'];?>" size="55" maxlength="150" />

                            </div>

                            <div class="prot_med">
                            <?php
                            if ( !$recebido ) {

                                $q_setor_dest = "SELECT `idsetor`, `desc_prot` FROM `sicop_setor` ORDER BY `desc_prot` ASC";

                                // instanciando o model
                                $model = SicopModel::getInstance();

                                // executando a query
                                $q_setor_dest = $model->query( $q_setor_dest );

                                // fechando a conexao
                                $model->closeConnection();

                            ?>

                                <div class="prot_leg">Setor: </div>
                                <select name="prot_setor" class="CaixaTexto" id="prot_setor">
                                    <option value="" selected="selected">Selecione...</option>
                                    <?php while ( $d_setor_dest = $q_setor_dest->fetch_assoc() ) { ?>
                                    <option value="<?php echo $d_setor_dest['idsetor'];?>" <?php echo $d_setor_dest['idsetor'] == $d_prot['prot_cod_setor'] ? 'selected="selected"' : ''; ?>><?php echo $d_setor_dest['desc_prot'];?></option>
                                    <?php };?>
                                </select>

                            <?php
                            } else {

                                $prot_setor = $d_prot['prot_cod_setor'];

                                $q_setor_dest = "SELECT `desc_prot` FROM `sicop_setor` WHERE `idsetor` = $prot_setor";

                                // instanciando o model
                                $model = SicopModel::getInstance();

                                // executando a query
                                $desc_setor = $model->fetchOne( $q_setor_dest );

                                // fechando a conexao
                                $model->closeConnection();

                                echo //"<div class='prot_med' title='Você só pode alterar o setor de documentos que ainda não foram recebidos.'>
                                      "Setor: $desc_setor

                                      <input name='prot_setor' type='hidden' id='prot_setor' value='$prot_setor'>
                                      ";
                            }
                            ?>

                            </div>

                            <div class="prot_med">

                                <div class="prot_leg">Cancelado:</div> <input name="prot_canc" type="checkbox" id="prot_canc" value="1" <?php echo $d_prot['prot_canc'] == 1 ? 'checked="checked"' : '' ?>/>

                            </div>

                        </td>
                    </tr>
                </table>

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Atualizar" />

                    <!--
                    *** NÃO USAR O CADASTRAR E ADD OUTRO - COLOCAR RETORNO AUTOMÁTICO PARA A TELA DE CADASTRO
                    -->

                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>



                <input name="idprot" type="hidden" id="idprot" value="<?php echo $d_prot['idprot']; ?>"/>
                <input name="proced" type="hidden" id="proced" value="1" />


            </form>

            <script type="text/javascript">

                id( 'modo_in' ).focus();

                $(function() {
                    $("form").submit(function() {
                        if ( valida_prot() == true ) {
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

<?php include 'footer.php';?>