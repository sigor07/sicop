<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_rol   = get_session( 'n_rol', 'int' );
$n_rol_n = 3;

$motivo_pag = 'CADASTRAMENTO DE VISITANTE';

if ( $n_rol < $n_rol_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 1 );

    exit;

}

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$iddet = get_get( 'iddet', 'int' );
if ( empty( $iddet ) ) {


    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( IDENTIFICADOR EM BRANCO - $motivo_pag ).";
    get_msg( $msg, 1 );


    echo msg_js( '', 1 );


    exit;

}

$q_matr = "SELECT `matricula` FROM `detentos` WHERE `iddetento` = $iddet";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$matricula = $model->fetchOne( $q_matr );

// fechando a conexao
$model->closeConnection();

if ( empty( $matricula ) ) {

    // pegar os dados do preso
    $detento = dados_det( $iddet );

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Tentativa de cadastramento de visitante para " . SICOP_DET_DESC_L . " que não possui matrícula.\n\n $detento";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não pode cadastrar visitantes para ' . SICOP_DET_DESC_L . 's que ainda não possuem matrícula.', 1 );

    exit;

}

$query_vis = "SELECT
                `visitas`.`idvisita`,
                `visitas`.`cod_detento`,
                `visitas`.`nome_visit`,
                `visitas`.`rg_visit`,
                `visitas`.`sexo_visit`,
                `visitas`.`nasc_visit`,
                DATE_FORMAT(`visitas`.`nasc_visit`, '%d/%m/%Y') AS nasc_visit_f,
                FLOOR(DateDiff(CurDate(), `visitas`.`nasc_visit`) / 365.25) AS idade_visit,
                `tipoparentesco`.`parentesco`
              FROM
                `visitas`
                LEFT JOIN `tipoparentesco` ON `visitas`.`cod_parentesco` = `tipoparentesco`.`idparentesco`
              WHERE
                `visitas`.`cod_detento` = $iddet
              ORDER BY
                `visitas`.`nome_visit` ASC";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_vis = $model->query( $query_vis );

// fechando a conexao
$model->closeConnection();

if ( !$query_vis ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta de listagem de visitantes ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );
    exit;

}

$query_parent = "SELECT `idparentesco`, `parentesco` FROM `tipoparentesco` ORDER BY `parentesco` ASC";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_parent = $model->query( $query_parent );

// fechando a conexao
$model->closeConnection();

$desc_pag = 'Cadastrar visitante';

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .= '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 4 );
$trail->output();
?>

            <p class="descript_page">CADASTRAR VISITANTE</p>

            <?php include 'quali/det_basic.php'; ?>

            <p class="table_leg">Visitantes ja casdastrados:</p>

            <?php

            $contv = $query_vis->num_rows;
            if ( $contv < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não há visitas cadastradas.</p>';
            } else {
            ?>

            <table class="lista_visit">
                <tr >
                    <th class="nome_visit">NOME DO VISITANTE</th>
                    <th class="rg_visit">R.G.</th>
                    <th class="dt_nasc_visit">NASCIMENTO</th>
                    <th class="parent_visit">PARENTESCO</th>
                    <th class="sexo_visit">SEXO</th>
                </tr>
                <?php
                while ( $dadosv = $query_vis->fetch_assoc() ) {

                    $idvisita = $dadosv['idvisita'];

                    $visit = manipula_sit_visia( $idvisita );

                    $suspenso    = false;
                    $visit_class = 'visit_ativa';
                    $sit_v_atual = 'ATIVA';

                    if ( $visit ) {

                        $suspenso    = $visit['suspenso'];
                        $visit_class = $visit['css_class'];
                        $sit_v_atual = $visit['sit_v'];

                    }
                ?>
                <tr class="even_dk">
                    <td class="nome_visit"><a href="detalvisit.php?idvisit=<?php echo $dadosv['idvisita'] ?>"><?php echo $dadosv['nome_visit'] ?></a></td>
                    <td class="rg_visit <?php echo $visit_class; ?>"><?php echo $dadosv['rg_visit'] ?></td>
                    <td class="dt_nasc_visit <?php echo $visit_class; ?>"><?php echo $dadosv['nasc_visit_f'] ?></td>
                    <td class="parent_visit <?php echo $visit_class; ?>"><?php echo $dadosv['parentesco'] ?></td>
                    <td class="sexo_visit" <?php echo $visit_class; ?>><?php echo $dadosv['sexo_visit'] ?></td>
                </tr>
                <?php } // fim do while ?>
            </table>
            <?php } // fim do if que conta o número de ocorrencias ?>

            <p class="link_common">
                <a href="rol_visit.php?iddet=<?php echo $iddet ?>" title="Ir para o rol d<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L; ?>">Ir para o rol</a>
            </p>

            <p class="table_leg">Novo visitante</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendvisit.php" method="post" name="visit_sing" id="visit_sing">

                <table class="edit">
                    <tr>
                        <td width="95">Visitante:</td>
                        <td colspan="3"><input name="nome_visit" type="text" class="CaixaTexto" id="nome_visit" onBlur="upperMe(this); remacc(this);" onKeyPress="return blockChars(event, 1);" size="100" maxlength="80" /></td>
                    </tr>
                    <tr>
                        <td width="95">R.G.:</td>
                        <td width="140"><input name="rg_visit" type="text" class="CaixaTexto" id="rg_visit" onkeypress="return blockChars( event, 7 );" size="14" maxlength="12" /></td>
                        <td colspan="2">Sexo:
                            <input type="radio" name="sexo_visit" value="M" id="sexo_visit_0" />M &nbsp;&nbsp;
                            <input type="radio" name="sexo_visit" value="F" id="sexo_visit_1" />F
                        </td>
                    </tr>
                    <tr>
                        <td width="95">Nome do Pai:</td>
                        <td colspan="3"><input name="pai_visit" type="text" class="CaixaTexto" id="pai_visit" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 1);" size="100" maxlength="80" /></td>
                    </tr>
                    <tr>
                        <td width="95">Nome da Mãe:</td>
                        <td colspan="3"><input name="mae_visit" type="text" class="CaixaTexto" id="mae_visit" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 1);" size="100" maxlength="80" /></td>
                    </tr>
                    <tr>
                        <td width="95">Naturalidade:</td>
                        <td>Estado:
                            <select name="uf" class="CaixaTexto" id="uf" onchange="$.monta_box_cidade();">
                                <option value="" selected="selected">Selecione</option>
                            </select>
                        </td>
                        <td colspan="2">Cidade:
                            <select name="cidade" class="CaixaTexto" id="cidade">
                                <option value="">Selecione o estado</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="95">Nascimento*:</td>
                        <td><input name="nasc_visit" type="text" class="CaixaTexto" id="nasc_visit" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" size="12" maxlength="10" /></td>
                        <td width="181">Parentesco:
                            <select name="idparentesco" class="CaixaTexto" id="idparentesco">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while ( $dados_parent = $query_parent->fetch_assoc() ) { ?>
                                <option value="<?php echo $dados_parent['idparentesco']; ?>" ><?php echo $dados_parent['parentesco']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                        <td width="196">Telefone:
                            <input name="telefone_visit" type="text" class="CaixaTexto" id="telefone_visit" onkeypress="mascara(this, mtel); return blockChars(event, 2);" size="16" maxlength="14" />
                        </td>
                    </tr>
                    <tr>
                        <td width="95">Defeitos físicos:</td>
                        <td colspan="3">
                            <input name="defeito_fisico" type="text" class="CaixaTexto" id="defeito_fisico" onblur="upperMe(this);" onkeypress="return blockChars(event, 1);" size="100" maxlength="80" />
                        </td>
                    </tr>
                    <tr>
                        <td width="95">Sinal(is) de nascimento:</td>
                        <td colspan="3">
                            <input name="sinal_nasc" type="text" class="CaixaTexto" id="sinal_nasc" onblur="upperMe(this);" onkeypress="return blockChars(event, 1);" size="100" maxlength="80" />
                        </td>
                    </tr>
                    <tr>
                        <td width="95">Cicatrizes:</td>
                        <td colspan="3">
                            <input name="cicatrizes" type="text" class="CaixaTexto" id="cicatrizes" onblur="upperMe(this);" onkeypress="return blockChars(event, 1);" size="100" maxlength="80" />
                        </td>
                    </tr>
                    <tr>
                        <td width="95">Tatuagem(ns):</td>
                        <td colspan="3">
                            <input name="tatuagens" type="text" class="CaixaTexto" id="tatuagens" onblur="upperMe(this);" onkeypress="return blockChars(event, 1);" size="100" maxlength="80" />
                        </td>
                    </tr>
                    <tr>
                        <td width="95">Endereço:</td>
                        <td colspan="3"><textarea name="resid_visit" cols="99" rows="2" class="CaixaTexto" id="resid_visit" onblur="upperMe(this);" onkeypress="return blockChars(event, 4);" onkeydown="textCounter(this, 150);" onkeyup="textCounter(this, 150);"></textarea></td>
                    </tr>
                </table>


                <div id="grupo">
                    <fieldset>
                        <p class="table_leg">Documentação:</p>
                        <table width="295" align="center" id="tbl_permissao" class="edit">
                            <tr align="center">
                                <th align="center" width="90">Documento</th>
                                <th width="55">OK</th>
                                <th width="55">FALTA</th>
                                <th width="70">DESNEC.</th>
                            </tr>
                            <tr class="even_gr">
                                <td align="left">Xerox RG</td>
                                <td align="center"><input name="doc_rg" type="radio" id="doc_rg_0" value="1" /></td>
                                <td align="center"><input name="doc_rg" type="radio" id="doc_rg_1" value="0" checked="checked" /></td>
                                <td align="center"><input name="doc_rg" type="radio" id="doc_rg_2" value="2" /></td>
                            </tr>
                            <tr class="even_gr">
                                <td align="left">Foto 3x4</td>
                                <td align="center"><input name="doc_foto34" type="radio" id="doc_foto34_0" value="1" /></td>
                                <td align="center"><input name="doc_foto34" type="radio" id="doc_foto34_1" value="0" checked="checked" /></td>
                                <td align="center"><input name="doc_foto34" type="radio" id="doc_foto34_2" value="2" /></td>
                            </tr>
                            <tr class="even_gr">
                                <td align="left">Comp. resid.</td>
                                <td align="center"><input name="doc_resid" type="radio" id="doc_resid_0" value="1" /></td>
                                <td align="center"><input name="doc_resid" type="radio" id="doc_resid_1" value="0" checked="checked" /></td>
                                <td align="center"><input name="doc_resid" type="radio" id="doc_resid_2" value="2" /></td>
                            </tr>
                            <tr class="even_gr">
                                <td align="left">Antecedentes criminais</td>
                                <td align="center"><input name="doc_ant" type="radio" id="doc_ant_0" value="1" /></td>
                                <td align="center"><input name="doc_ant" type="radio" id="doc_ant_1" value="0" checked="checked" /></td>
                                <td align="center"><input name="doc_ant" type="radio" id="doc_ant_2" value="2" /></td>
                            </tr>
                            <tr class="even_gr">
                                <td align="left">Certidão de nascimento / casamento</td>
                                <td align="center"><input name="doc_cert" type="radio" id="doc_cert_0" value="1" /></td>
                                <td align="center"><input name="doc_cert" type="radio" id="doc_cert_1" value="0" checked="checked" /></td>
                                <td align="center"><input name="doc_cert" type="radio" id="doc_cert_2" value="2" /></td>
                            </tr>
                        </table>
                    </fieldset>
                </div>

                <div id="grupo">
                    <p align="center">* Observação: A data de nascimento não é obrigatória para o cadastramento, mas <b>será exigida</b> para o ingresso do visitante na unidade.</p>
                </div>

                <input type="hidden" name="iddet" id="iddet" value="<?php echo $iddet; ?>" />
                <input type="hidden" name="proced" id="proced" value="3" />

                <div class="form_bts">
                    <input class="form_bt" name="cadastrar" type="submit" value="Cadastrar" />
                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

            <script type="text/javascript">

                $(function() {

                    $.monta_box_uf();

                    $( "#nome_visit" ).focus();
                    $( "#nasc_visit" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });

                    $("form").submit(function() {
                        if ( validacadvisit() == true ) {
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