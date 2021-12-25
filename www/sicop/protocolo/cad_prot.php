<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';
$tipo_pag = 'CADASTRAMENTO DE PROTOCOLO';

$n_prot   = get_session( 'n_prot', 'int' );
$n_prot_n = 3;

if ( $n_prot < $n_prot_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $tipo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$q_num_prot   = 'SELECT IFNULL( MAX( `protocolo`.`prot_num` ), 0 ) + 1 AS n_num_prot FROM `protocolo` WHERE `prot_ano` = YEAR( CURDATE() )';
$q_modo_in    = 'SELECT `id_modo_in`, `modo_in` FROM `tipo_prot_modo_in` ORDER BY `modo_in` ASC';
$q_tipo_doc   = 'SELECT `id_tipo_doc`, `tipo_doc` FROM `tipo_prot_doc` ORDER BY `tipo_doc` ASC';
$q_setor_dest = 'SELECT `idsetor`, `desc_prot` FROM `sicop_setor` ORDER BY `desc_prot` ASC';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$n_num_prot   = $model->fetchOne( $q_num_prot );
$q_modo_in    = $model->query( $q_modo_in );
$q_tipo_doc   = $model->query( $q_tipo_doc );
$q_setor_dest = $model->query( $q_setor_dest );

// fechando a conexao
$model->closeConnection();

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Cadastrar documento';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 3);
$trail->output();
?>

            <p class="descript_page">CADASTRAR DOCUMENTO</p>

            <!--onSubmit="return validacadaliasdet()"-->
            <form action="<?php echo SICOP_ABS_PATH ?>send/sendprot.php" method="post" name="cadprot" id="cadprot">

                <table class="edit">
                    <tr>
                        <td class="prot_td_med">
                            Modo de entrada:
                            <select name="modo_in" class="CaixaTexto" id="modo_in">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while ( $d_modo_in = $q_modo_in->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_modo_in['id_modo_in'];?>"><?php echo $d_modo_in['modo_in'];?></option>
                                <?php };?>
                            </select>
                        </td>
                        <td class="prot_td_med">
                            Tipo:
                            <select name="tipo_doc" class="CaixaTexto" id="tipo_doc">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while ( $d_tipo_doc = $q_tipo_doc->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_tipo_doc['id_tipo_doc'];?>"><?php echo $d_tipo_doc['tipo_doc'];?></option>
                                <?php };?>
                            </select>
                        </td>
                        <td class="prot_td_med">
                            Protocolo Nº: <b><?php echo number_format( $n_num_prot, 0, '', '.' ); ?></b> às <input name="prot_hora_in" type="text" class="CaixaTexto" id="prot_hora_in" onblur="verifica_hora(this, this.value)" onkeypress="mascara_hora(this, this.value); return blockChars(event, 2);" size="5" maxlength="5" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div class="prot_leg_m">Assunto:</div> <textarea name="prot_assunto" id="prot_assunto" cols="125" rows="3" class="CaixaTexto" onblur="upperMe(this);" onkeypress="return blockChars(event, 4);"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="padding: 0 3px;">

                            <div class="prot_m">

                                <div class="prot_leg"> Origem:</div>
                                <input name="prot_origem" type="text" class="CaixaTexto" id="prot_origem" onblur="upperMe(this);" onkeypress="return blockChars(event, 4);" size="55" maxlength="150" />

                            </div>

                            <div class="prot_med">

                                <div class="prot_leg">Setor: </div>
                                <select name="prot_setor" class="CaixaTexto" id="prot_setor">
                                    <option value="" selected="selected">Selecione...</option>
                                    <?php while($d_setor_dest = $q_setor_dest->fetch_assoc()) { ?>
                                    <option value="<?php echo $d_setor_dest['idsetor'];?>"><?php echo $d_setor_dest['desc_prot'];?></option>
                                    <?php };?>
                                </select>

                            </div>

                            <div class="prot_med">

                                <div class="prot_leg">Cancelado:</div> <input name="prot_canc" type="checkbox" id="prot_canc" value="1" />

                            </div>

                        </td>
                    </tr>
                </table>

                <input name="prot_num" type="hidden" id="prot_num" value="<?php echo $n_num_prot; ?>">
                <input name="proced" type="hidden" id="proced" value="3" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Cadastrar" />

                    <!--
                    *** NÃO USAR O CADASTRAR E ADD OUTRO - COLOCAR RETORNO AUTOMÁTICO PARA A TELA DE CADASTRO
                    -->

                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

            <script type="text/javascript">

                $(function(){
                    $("#modo_in").focus();
                });


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