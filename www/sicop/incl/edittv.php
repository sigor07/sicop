<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_incl   = get_session( 'n_incl', 'int' );
$n_incl_n = 2;

if ( $n_incl < $n_incl_n ) {

    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = 'ALTERAÇÃO DE TV';
    get_msg( $msg, 1 );

    exit;

}

$idtv = get_get( 'idtv', 'int' );

if ( empty( $idtv ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página. Identificador da tv em branco. ( ALTERAÇÃO DE TV )";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$q_tv = "SELECT
            `detentos_tv`.`idtv`,
            `detentos_tv`.`cod_detento`,
            `detentos_tv`.`cod_cela`,
            `detentos_tv`.`marca_tv`,
            `detentos_tv`.`cor_tv`,
            `detentos_tv`.`polegadas`,
            `detentos_tv`.`lacre_1`,
            `detentos_tv`.`lacre_2`,
            `cela`.`cela`,
            `cela`.`cod_raio`
          FROM
            `detentos_tv`
            LEFT JOIN `cela` ON `detentos_tv`.`cod_cela` = `cela`.`idcela`
            LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
          WHERE
            `detentos_tv`.`idtv` = $idtv
          LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_tv = $model->query( $q_tv );

// fechando a conexao
$model->closeConnection();

if ( !$q_tv ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'Falha na consulta ( ALTERAÇÃO DE TV ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$cont_tv = $q_tv->num_rows;

if ( $cont_tv < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = 'A consulta retornou 0 ocorrências ( ALTERAÇÃO DE TV ).';
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$d_tv = $q_tv->fetch_assoc();

$iddet = $d_tv['cod_detento'];

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$desc_pag = 'Alterar TV';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

require 'cab.php';
$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 4 );
$trail->output();
?>

            <p class="descript_page">ALTERAR TV</p>

            <?php
            if ( empty( $iddet ) ) {
                echo '<p class="p_q_no_result">Não há detento responsável.</p>';
            } else {
                include 'quali/det_basic.php';
            }
            ?>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendtv.php" method="post" name="cadtv" id="cadtv" onSubmit="return validacadtv()">
                <table class="edit">
                    <tr >
                        <td width="70" height="20">Data:</td>
                        <td width="164"><input name="data_tv" type="text" class="CaixaTexto" id="data_tv" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" size="12" maxlength="10" /></td>
                    </tr>
                    <tr >
                        <td height="20"><?php echo SICOP_RAIO ?>:</td>
                        <td>
                            <select name="n_raio" class="CaixaTexto" id="n_raio" onChange="$.monta_box_cela();">
                                <option value="" selected="selected">Selecione</option>
                            </select>
                        </td>
                    </tr>
                    <tr >
                        <td height="20"><?php echo SICOP_CELA ?>:</td>
                        <td>
                            <select name="n_cela" class="CaixaTexto" id="n_cela">
                                <option value="" selected="selected">Escolha o raio</option>
                            </select>
                        </td>
                    </tr>
                    <tr >
                        <td height="20">Marca:</td>
                        <td><input name="marca_tv" type="text" class="CaixaTexto" id="marca_tv" onBlur="upperMe(this); remacc(this); " onKeyPress="return blockChars(event, 4);" value="<?php echo $d_tv['marca_tv']; ?>" size="22" maxlength="20" /></td>
                    </tr>
                    <tr >
                        <td height="20">Cor:</td>
                        <td><input name="cor_tv" type="text" class="CaixaTexto" id="cor_tv" onBlur="upperMe(this); remacc(this); " onKeyPress="return blockChars(event, 4);" value="<?php echo $d_tv['cor_tv']; ?>" size="22" maxlength="20" /></td>
                    </tr>
                    <tr >
                        <td height="20">Polegadas:</td>
                        <td><input name="polegadas" type="text" class="CaixaTexto" id="polegadas" onKeyPress="return blockChars(event, 2);" value="<?php echo $d_tv['polegadas']; ?>" size="3" maxlength="2" /></td>
                    </tr>
                    <tr >
                        <td height="20">Lacres:</td>
                        <td>
                            <input name="lacre_1" type="text" class="CaixaTexto" id="lacre_1" onKeyPress="return blockChars(event, 2);" value="<?php echo $d_tv['lacre_1']; ?>" size="6" maxlength="5" /> /
                            <input name="lacre_2" type="text" class="CaixaTexto" id="lacre_2" onKeyPress="return blockChars(event, 2);" value="<?php echo $d_tv['lacre_2']; ?>" size="6" maxlength="5" />
                        </td>
                    </tr>
                </table>

                <input type="hidden" name="idtv" id="idtv" value="<?php echo $idtv;?>" />
                <input type="hidden" name="proced" id="proced" value="1" />

                <input type="hidden" name="old_raio" id="old_raio" value="<?php echo $d_tv['cod_raio'];?>" />
                <input type="hidden" name="old_cela" id="old_cela" value="<?php echo $d_tv['idcela'];?>" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Atualizar" />
                    <input class="form_bt" type="button" name="" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

            <script type="text/javascript">

                $(function() {

                    $.monta_box_raio();

                    $( "#data_tv" ).focus();
                    $( "#data_tv" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });
                });

            </script>

<?php include 'footer.php'; ?>