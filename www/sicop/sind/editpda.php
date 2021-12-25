<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$n_sind   = get_session( 'n_sind', 'int' );
$n_sind_n = 3;

if ( $n_sind < $n_sind_n ) {
    require ('/cab_simp.php');
    $tipo = 0;
    include '../init/msgnopag.php';
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
    salvaLog( $mensagem );
    exit;
}

$idsind = get_get( 'idsind', 'int' );

if ( empty( $idsind ) ) {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso direto à página de detalhes da sindicância.\n\n Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$query_pda = "SELECT
                `sindicancias`.`idsind`,
                `sindicancias`.`cod_detento`,
                `sindicancias`.`num_pda`,
                `sindicancias`.`ano_pda`,
                `sindicancias`.`local_pda`,
                DATE_FORMAT(`sindicancias`.`data_ocorrencia`, '%d/%m/%Y') AS data_ocorrencia,
                `sindicancias`.`sit_pda`,
                `tipositdet`.`situacaodet`,
                `sindicancias`.`data_reabilit`,
                `sindicancias`.`descr_pda`,
                DATE_FORMAT(`sindicancias`.`data_reabilit`, '%d/%m/%Y') AS data_reab_f
              FROM
                `sindicancias`
                LEFT JOIN `tipositdet` ON `sindicancias`.`cod_sit_detento` = `tipositdet`.`idsitdet`
              WHERE
                `sindicancias`.`idsind` = $idsind
              ORDER BY
                `sindicancias`.`ano_pda`, `sindicancias`.`num_pda`";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_pda = $model->query( $query_pda );

// fechando a conexao
$model->closeConnection();

if( !$query_pda ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$cont_pda = $query_pda->num_rows;

if( $cont_pda < 1 ) {

    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta do PDA retornou 0 ocorrências ( ALTERAÇÃO DE PDA ).\n\n Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$d_pda = $query_pda->fetch_assoc();

$iddet = $d_pda['cod_detento'];

$query_sitdet = 'SELECT `idsitdet`, `situacaodet` FROM `tipositdet` ORDER BY `situacaodet` ASC';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_sitdet = $model->query( $query_sitdet );

// fechando a conexao
$model->closeConnection();

if( !$query_sitdet ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Alterar PDA';

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


            <p class="descript_page">ATUALIZAR PDA</p>

            <?php if (empty($iddet)) { ?>

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
                        <td width="120">Número do PDA:</td>
                        <td width="320"><input name="num_pda" type="text" class="CaixaTexto" id="num_pda" onKeyPress="return blockChars(event, 2);" value="<?php echo $d_pda['num_pda']; ?>" size="5" maxlength="4" />/<input name="ano_pda" type="text" class="CaixaTexto" id="ano_pda" onKeyPress="return blockChars(event, 2);" value="<?php echo $d_pda['ano_pda']; ?>" size="5" maxlength="4" /></td>
                    </tr>
                    <tr>
                        <td width="120">Local:</td>
                        <td><input name="local_pda" type="text" class="CaixaTexto" id="local_pda" onBlur="upperMe(this); remacc(this);" onKeyPress="return blockChars(event, 4);" value="<?php echo $d_pda['local_pda']; ?>" size="60" maxlength="50" /></td>
                    </tr>
                    <tr>
                        <td width="120">Data da ocorrência:</td>
                        <td><input name="data_ocorrencia" type="text" class="CaixaTexto" id="data_ocorrencia" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $d_pda['data_ocorrencia']; ?>" size="12" maxlength="10" /></td>

                    </tr>
                    <tr>
                        <td width="120">Situação do PDA:</td>
                        <td>
                            <input name="sit_pda" type="radio" id="sit_pda_0" onClick="mostraPDA();" value="1" <?php echo $d_pda['sit_pda'] == "1" ? 'checked="checked"' : ''; ?> /> Em andamento &nbsp;&nbsp;
                            <input name="sit_pda" type="radio" id="sit_pda_1" onClick="mostraPDA();" value="2"  <?php echo $d_pda['sit_pda'] == "2" ? 'checked="checked"' : ''; ?>/> Concluído
                            <input name="sit_pda" type="radio" id="sit_pda_2" onClick="mostraPDA();" value="3" <?php echo $d_pda['sit_pda'] == "3" ? 'checked="checked"' : ''; ?> /> Sobrestado
                        </td>
                    </tr>
                    <tr id="tr_sit_det">
                        <td width="120">Situação d<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L; ?>:</td>
                        <td>
                            <select name="situacaodet" class="CaixaTexto" id="situacaodet">
                                <option value="" >Selecione...</option>
                                <?php while ( $dados_sitdet = $query_sitdet->fetch_assoc() ) { ?>
                                <option value="<?php echo $dados_sitdet['idsitdet']; ?>" <?php echo $dados_sitdet['situacaodet'] == $d_pda['situacaodet'] ? 'selected="selected"' : ''; ?>><?php echo $dados_sitdet['situacaodet']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                    <tr id="tr_dt_reab">
                        <td width="120">Data da reabilitação:</td>
                        <td>
                            <input name="data_reabilit" type="text" class="CaixaTexto" id="data_reabilit" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $d_pda['data_reab_f']; ?>" size="12" maxlength="10" />
                        </td>
                    </tr>
                    <tr>
                        <td width="120" valign="top">Descrição:</td>
                        <td><textarea name="descr_pda" id="descr_pda" class="CaixaTexto" cols="58" rows="3" onBlur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 4);"><?php echo $d_pda['descr_pda']; ?></textarea></td>
                    </tr>
                </table>

                <input name="id_pda" type="hidden" id="id_pda" value="<?php echo $d_pda['idsind']; ?>" />
                <input name="iddet" type="hidden" id="iddet" value="<?php echo $iddet; ?>" />
                <input name="proced" type="hidden" id="proced" value="1" />

                <div class="form_bts">
                    <input class="form_bt" name="atualizar" type="submit" value="Atualizar" />
                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

            <script type="text/javascript">

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

                mostraPDA();

            </script>

<?php include 'footer.php'; ?>