<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_sind   = get_session( 'n_sind', 'int' );
$n_sind_n = 3;

if ( $n_sind < $n_sind_n ) {

    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = 'CADASTRO DE PDA';
    get_msg( $msg, 1 );

    exit;

}

$iddet = get_get( 'iddet' );

if ( $iddet == 'no' ) {

    $iddet = '';

} else {

    $iddet = (int)$_GET['iddet'];

    if ( empty( $iddet ) ) {

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'atn';
        $msg['text']  = 'Tentativa de acesso direto à página de cadastramento de sindicância.';
        get_msg( $msg, 1 );

        echo msg_js( '', 1 );

        exit;

    }

}

if ( !empty( $iddet ) ) {

    $query_det = "SELECT
                    `matricula`
                  FROM
                    `detentos`
                  WHERE
                    `iddetento` = $iddet
                  LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $matricula = $model->fetchOne( $query_det );

    // fechando a conexao
    $model->closeConnection();

    if ( empty( $matricula ) ) {

        // pegar os dados do preso
        $detento = dados_det( $iddet );

        // montar a mensagem q será salva no log
        $msg = array();
        $msg['tipo']  = 'err';
        $msg['text']  = 'Tentativa de cadastramento de sindicância para ' . SICOP_DET_DESC_L . " que não possui matrícula.\n\n $detento";
        $msg['linha'] = __LINE__;
        get_msg( $msg, 1 );

        require 'cab_simp.php';
        echo msg_js( 'Você não pode cadastrar PDAs para ' . SICOP_DET_DESC_L . 's que ainda não possuem matrícula.', 1 );

        exit;

    }

}

$query_sitdet = 'SELECT `idsitdet`, `situacaodet` FROM `tipositdet` ORDER BY `situacaodet` ASC';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_sitdet = $model->query( $query_sitdet );

// fechando a conexao
$model->closeConnection();

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Cadastrar PDA';

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

            <p class="descript_page">CADASTRAR PDA</p>

            <?php if ( empty( $iddet ) ) { ?>

            <p class="table_leg"><?php echo SICOP_DET_DESC_FU; ?></p>

            <div class="detal_var">
                AUTORIA DESCONHECIDA
            </div>

            <?php } else { ?>

            <?php include 'quali/det_basic.php'; ?>

            <?php } ?>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendpda.php" method="post" name="pda_sing" id="pda_sing">

                <table class="edit">

                    <tr>
                        <td width="120" >Número do PDA:</td>
                        <td width="320" >
                            <input name="num_pda" type="text" class="CaixaTexto" id="num_pda" onKeyPress="return blockChars(event, 2);" size="5" maxlength="4" />/<input name="ano_pda" type="text" class="CaixaTexto" id="ano_pda" onKeyPress="return blockChars(event, 2);" size="5" maxlength="4" />
                        </td>
                    </tr>
                    <tr>
                        <td width="120" >Local:</td>
                        <td><input name="local_pda" type="text" class="CaixaTexto" id="local_pda" onBlur="upperMe(this); remacc(this);" onKeyPress="return blockChars(event, 4);" size="60" maxlength="50" /></td>
                    </tr>
                    <tr>
                        <td width="120" >Data da ocorrência:</td>
                        <td><input name="data_ocorrencia" type="text" class="CaixaTexto" id="data_ocorrencia" onBlur="verifica_data(this, this.value)" onKeyPress="mascara_data(this, this.value); return blockChars(event, 2);" size="12" maxlength="10" /></td>
                    </tr>
                    <tr>
                        <td width="120">Situação do PDA:</td>
                        <td>
                            <input name="sit_pda" type="radio" id="sit_pda_0" onClick="mostraPDA();" value="1" checked="CHECKED" /> Em andamento &nbsp;&nbsp;
                            <input name="sit_pda" type="radio" id="sit_pda_1" onClick="mostraPDA();" value="2" /> Concluído &nbsp;&nbsp;
                            <input name="sit_pda" type="radio" id="sit_pda_2" onClick="mostraPDA();" value="3" /> Sobrestado
                        </td>
                    </tr>
                    <tr id="tr_sit_det">
                        <td width="120" >Situação d<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L; ?>:</td>
                        <td>
                            <select name="situacaodet" class="CaixaTexto" id="situacaodet">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while ( $dados_sitdet = $query_sitdet->fetch_assoc() ) { ?>
                                <option value="<?php echo $dados_sitdet['idsitdet']; ?>"><?php echo $dados_sitdet['situacaodet']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                    <tr id="tr_dt_reab">
                        <td width="120" >Data da reabilitação:</td>
                        <td>
                            <input name="data_reabilit" type="text" class="CaixaTexto" id="data_reabilit" onBlur="verifica_data(this, this.value)" onKeyPress="mascara_data(this, this.value); return blockChars(event, 2);" size="12" maxlength="10" />
                        </td>
                    </tr>
                    <tr>
                        <td width="120" valign="top">Descrição:</td>
                        <td><textarea name="descr_pda" id="descr_pda" class="CaixaTexto" cols="59" rows="3" onBlur="upperMe(this); remacc(this);" onKeyPress="return blockChars(event, 4);"></textarea></td>
                    </tr>

                </table>

                <input name="iddet" type="hidden" id="iddet" value="<?php echo $iddet; ?>" />
                <input name="proced" type="hidden" id="proced" value="3" />

                <div class="form_bts">
                    <input class="form_bt" name="atualizar" type="submit" value="Cadastrar" />
                    <input class="form_bt" name="" type="button" onClick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

            <script type="text/javascript">

                mostraPDA();

                $(function() {

                    $( "#num_pda" ).focus();
                    $( "#data_ocorrencia, #data_reabilit" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });

                    $("form").submit(function() {
                        if ( validacadpda() == true ) {
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