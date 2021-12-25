<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag         = link_pag();
$tipo        = '';

$n_det_alt   = get_session( 'n_det_alt', 'int' );
$n_det_alias = get_session( 'n_det_alias', 'int' );
$n_cadastro  = get_session( 'n_cadastro', 'int' );
$n_alt_n     = 1;

$motivo_pag = 'ALTERAÇÃO DE DADOS D' . SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U;

if ( $n_det_alt < $n_alt_n ) {

    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    exit;

}


$iddet = get_get( 'iddet', 'int' );
if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( IDENTIFICADOR EM BRANCO - $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );

    exit;

}


$query_det = "SELECT
                `detentos`.`iddetento`,
                `detentos`.`nome_det`,
                `detentos`.`cod_artigo`,
                `detentos`.`matricula`,
                `detentos`.`rg_civil`,
                `detentos`.`execucao`,
                `detentos`.`cpf`,
                `detentos`.`vulgo`,
                `detentos`.`cod_nacionalidade`,
                `detentos`.`cod_cidade`,
                DATE_FORMAT( `detentos`.`nasc_det`, '%d/%m/%Y' ) AS `nasc_det`,
                `detentos`.`profissao`,
                `detentos`.`cod_est_civil`,
                `detentos`.`cod_instrucao`,
                `detentos`.`pai_det`,
                `detentos`.`mae_det`,
                DATE_FORMAT( `detentos`.`data_prisao`, '%d/%m/%Y' ) AS `data_prisao`,
                `detentos`.`cod_local_prisao`,
                `detentos`.`primario`,
                `detentos`.`cod_sit_proc`,
                `detentos`.`prisoes_ant`,
                `detentos`.`fuga`,
                `detentos`.`local_fuga`,
                `detentos`.`cod_cutis`,
                `detentos`.`cod_cabelos`,
                `detentos`.`cod_olhos`,
                `detentos`.`estatura`,
                `detentos`.`peso`,
                `detentos`.`defeito_fisico`,
                `detentos`.`sinal_nasc`,
                `detentos`.`cicatrizes`,
                `detentos`.`tatuagens`,
                `detentos`.`resid_det`,
                `detentos`.`cod_religiao`,
                `detentos`.`possui_adv`,
                `detentos`.`caso_emergencia`,
                `detentos`.`obs_artigos`,
                `detentos`.`data_quali`,
                `detentos`.`monitorado`,
                `detentos`.`dados_prov`,
                `detentos`.`jaleco`,
                `detentos`.`calca`,
				`detentos`.`pl`,
				`detentos`.`guia_local`,
				`detentos`.`guia_numero`,
                `tipoartigo`.`artigo`,
                `tipocutis`.`cutis`,
                `tipocabelos`.`cabelos`,
                `tipoescolaridade`.`escolaridade`,
                `tipoestadocivil`.`est_civil`,
                `tipoolhos`.`olhos`,
                `tiporeligiao`.`religiao`,
                `tiposituacaoprocessual`.`sit_proc`,
                `tiponacionalidade`.`nacionalidade`,
                `cidades`.`nome` AS `cidade`,
                `estados`.`idestado`,
                `estados`.`sigla` AS `estado`
              FROM
                `detentos`
                LEFT JOIN `tipocutis` ON `detentos`.`cod_cutis` = `tipocutis`.`idcutis`
                LEFT JOIN `tipocabelos` ON `detentos`.`cod_cabelos` = `tipocabelos`.`idcabelos`
                LEFT JOIN `tipoescolaridade` ON `detentos`.`cod_instrucao` = `tipoescolaridade`.`idescolaridade`
                LEFT JOIN `tipoestadocivil` ON `detentos`.`cod_est_civil` = `tipoestadocivil`.`idest_civil`
                LEFT JOIN `tipoolhos` ON `detentos`.`cod_olhos` = `tipoolhos`.`idolhos`
                LEFT JOIN `tiporeligiao` ON `detentos`.`cod_religiao` = `tiporeligiao`.`idreligiao`
                LEFT JOIN `tiposituacaoprocessual` ON `detentos`.`cod_sit_proc` = `tiposituacaoprocessual`.`idsit_proc`
                LEFT JOIN `cidades` ON `detentos`.`cod_cidade` = `cidades`.`idcidade`
                LEFT JOIN `estados` ON `cidades`.`cod_uf` = `estados`.`idestado`
                LEFT JOIN `tipoartigo` ON `detentos`.`cod_artigo` = `tipoartigo`.`idartigo`
                LEFT JOIN `tiponacionalidade` ON `detentos`.`cod_nacionalidade` = `tiponacionalidade`.`idnacionalidade`
              WHERE
                `detentos`.`iddetento` = $iddet
              ORDER BY
                `detentos`.`nome_det`
              LIMIT  1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_det = $model->query( $query_det );

// fechando a conexao
$model->closeConnection();

if( !$query_det ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$contd = $query_det->num_rows;

if( $contd < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "A consulta retornou 0 ocorrências ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$dados_det = $query_det->fetch_assoc();

$query_art       = 'SELECT `idartigo`, `artigo` FROM `tipoartigo` ORDER BY `artigo` ASC';
$query_nac       = 'SELECT `idnacionalidade`, `nacionalidade` FROM `tiponacionalidade` ORDER BY `nacionalidade` ASC';
$query_cutis     = 'SELECT `idcutis`,`cutis` FROM `tipocutis` ORDER BY `cutis` ASC';
$query_cabelo    = 'SELECT `idcabelos`,`cabelos` FROM `tipocabelos` ORDER BY `cabelos` ASC';
$query_olho      = 'SELECT `idolhos`,`olhos` FROM `tipoolhos` ORDER BY `olhos` ASC';
$query_sit_pr    = 'SELECT `idsit_proc`,`sit_proc` FROM `tiposituacaoprocessual` ORDER BY `sit_proc` ASC';
$query_religiao  = 'SELECT `idreligiao`,`religiao` FROM `tiporeligiao` ORDER BY `religiao` ASC';
$query_inst      = 'SELECT `idescolaridade`,`escolaridade` FROM `tipoescolaridade` ORDER BY `escolaridade` ASC';
$query_est_civil = 'SELECT `idest_civil`,`est_civil` FROM `tipoestadocivil` ORDER BY `est_civil` ASC';
$q_local_prisao  = 'SELECT `unidades`.`idunidades`, `unidades`.`unidades` FROM `unidades` WHERE `in` = TRUE ORDER BY `unidades`.`unidades`';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_art       = $model->query( $query_art );
$query_nac       = $model->query( $query_nac );
$query_cutis     = $model->query( $query_cutis );
$query_cabelo    = $model->query( $query_cabelo );
$query_olho      = $model->query( $query_olho );
$query_sit_pr    = $model->query( $query_sit_pr );
$query_religiao  = $model->query( $query_religiao );
$query_inst      = $model->query( $query_inst );
$query_est_civil = $model->query( $query_est_civil );
$q_local_prisao  = $model->query( $q_local_prisao );

// fechando a conexao
$model->closeConnection();

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Alterar dados d' . SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L;

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

            <p class="descript_page">ALTERAR DADOS D<?php echo SICOP_DET_ART_U . ' ' . SICOP_DET_DESC_U; ?></p>

            <?php if ( $n_det_alias >= 1 ) { ?>
            <p class="link_common"><a href="javascript:void(0)" title="Abrir em outra janela" onClick="javascript: ow('cadaliasdet.php?iddet=<?php echo $dados_det['iddetento']; ?>&targ=1&noreload=1', '830', '600'); return false"> Cadastrar Alias</a></p>
            <?php } ?>

            <form action="<?php echo SICOP_ABS_PATH ?>send/senddetup.php" method="post" name="det_up" id="det_up" onSubmit="return valida_det();">
                <table class="edit">

                    <tr>
                        <td width="108">Nome d<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L; ?>:</td>
                        <td colspan="3"><input name="nome_det" type="text" class="CaixaTexto" id="nome_det" value="<?php echo $dados_det['nome_det'] ?>" onblur="upperMe(this);" onkeypress="return blockChars(event, 1);" size="100" maxlength="80" /></td>
                    </tr>

                    <tr>
                        <td width="108">Matrícula:</td>
                        <td colspan="3">
                            <input name="matricula" type="text" class="CaixaTexto" id="matricula" onblur="checkmatr(this, this.value); $.ck_matr_exist();" onkeypress="mascara(this, mmatr); return blockChars(event, 2);" value="<?php if ( !empty( $dados_det['matricula'] ) ) echo formata_num( $dados_det['matricula'] ) ?>" size="11" maxlength="10" />
                            &nbsp;<a href="#" title="Abrir a calculadora de dígitos de matrícula" onclick="javascript: ow('<?php echo SICOP_ABS_PATH ?>calc_d_matr.php', '600', '300'); return false" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>calc.png" alt="Abrir a calculadora de dígitos de matrícula" width="13" height="13" /></a>&nbsp;&nbsp;
                            RG civil:
                            <input name="rgcivil" type="text" class="CaixaTexto" id="rgcivil" onblur="checkrg(this, this.value);" onkeypress="mascara(this, mrg);" value="<?php if ( !empty( $dados_det['rg_civil'] ) ) echo formata_num( $dados_det['rg_civil'] ) ?>" size="14" maxlength="12" />
                            &nbsp;<a href="#" title="Abrir a calculadora de dígitos de R.G." onclick="javascript: ow('<?php echo SICOP_ABS_PATH ?>calc_d_rg.php', '600', '300'); return false" ><img src="<?php echo SICOP_SYS_IMG_PATH; ?>calc.png" alt="Abrir a calculadora de dígitos de R.G." width="13" height="13" /></a>&nbsp;&nbsp;
                            Execução:
                            <input name="execucao" type="text" class="CaixaTexto" id="execucao" onkeypress="mascara(this, mexec); return blockChars(event, 2);" value="<?php if ( !empty( $dados_det['execucao'] ) ) echo number_format( $dados_det['execucao'], 0, '', '.' ) ?>" size="11" maxlength="9" />
                        </td>
                    </tr>

                    <tr>
                        <td width="108">CPF:</td>
                        <td colspan="3">
                            <input name="cpf" type="text" class="CaixaTexto" id="cpf" onblur="checkcpf(this, this.value); $.ck_cpf_exist();" onkeypress="mascara(this, mcpf); return blockChars(event, 2);" value="<?php if ( !empty( $dados_det['cpf'] ) ) echo formata_num( $dados_det['cpf'], 2 ) ?>" size="16" maxlength="14" />
                        </td>
                    </tr>

                    <tr>
                        <td width="108">Vulgo(s):</td>
                        <td colspan="3"><input name="vulgo" type="text" class="CaixaTexto" id="vulgo" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 4);" value="<?php echo $dados_det['vulgo'] ?>" size="60" maxlength="50" />
                        <?php if ( $n_cadastro >= 3 ) { ?>
                        &nbsp;&nbsp;&nbsp;
                        Dados Provisórios: <input name="dados_prov" type="checkbox" id="dados_prov" value="1"  <?php echo $dados_det['dados_prov'] == 1 ? 'checked="checked"' : '' ?>/>
                        <?php } else { ?>
                        <input name="dados_prov" type="hidden" id="dados_prov" value="<?php echo $dados_det['dados_prov'] == 1 ? '1' : '0' ?>" />
                        <?php }; ?>

                        </td>
                    </tr>
                    <tr>
                        <td width="108">Artigo:</td>
                        <td colspan="3">
                            <select name="artigo" class="CaixaTexto" id="artigo">
                                <option value="" >Selecione...</option>
                                <?php while ( $dados_art = $query_art->fetch_assoc() ) { ?>
                                <option value="<?php echo $dados_art['idartigo']; ?>" <?php echo $dados_art['idartigo'] == $dados_det['cod_artigo'] ? 'selected="selected"' : ''; ?>><?php echo $dados_art['artigo']; ?></option>
                                <?php }; ?>
                            </select>
                            &nbsp;&nbsp;&nbsp;
                            Outros Artigos:
                            <input name="outros_art" type="text" class="CaixaTexto" id="outros_art" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 4);" value="<?php echo $dados_det['obs_artigos']; ?>" size="37" maxlength="27" />
                        </td>
                    </tr>
                    <tr>
                        <td width="108">Nacionalidade:</td>
                        <td width="119">
                            <select name="nacionalidade" class="CaixaTexto" id="nacionalidade">
                                <?php while ( $dados_nac = $query_nac->fetch_assoc() ) { ?>
                                <option value="<?php echo $dados_nac['idnacionalidade']; ?>" <?php echo $dados_nac['idnacionalidade'] == $dados_det['cod_nacionalidade'] ? 'selected="selected"' : ''; ?>><?php echo $dados_nac['nacionalidade']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                        <td colspan="2" align="left">
                            Estado:
                            <select name="uf" class="CaixaTexto" id="uf" onchange="$.monta_box_cidade();">
                                <option value="" selected="selected">Selecione</option>
                            </select>
                            &nbsp;&nbsp;&nbsp;
                            Cidade:
                            <select name="cidade" class="CaixaTexto" id="cidade">
                                <option value="">Selecione o estado</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="108">Nascimento:</td>
                        <td><input name="nasc_det" type="text" class="CaixaTexto" id="nasc_det" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value);return blockChars(event, 2);" value="<?php echo $dados_det['nasc_det'] ?>" size="12" maxlength="10" /></td>
                        <td colspan="2" align="left">Profissão: <input name="profissao" type="text" class="CaixaTexto" id="profissao" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 1);" value="<?php echo $dados_det['profissao'] ?>" size="62" maxlength="50" /></td>
                    </tr>
                    <tr>
                        <td width="108">Estado civil:</td>
                        <td>
                            <select name="est_civil" class="CaixaTexto" id="est_civil">
                                <option value="" >Selecione...</option>
                                <?php while ( $dados_est_civil = $query_est_civil->fetch_assoc() ) { ?>
                                <option value="<?php echo $dados_est_civil['idest_civil']; ?>" <?php echo $dados_est_civil['idest_civil'] == $dados_det['cod_est_civil'] ? 'selected="selected"' : ''; ?>><?php echo $dados_est_civil['est_civil']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                        <td colspan="2" align="left">Instrução:
                            <select name="instrucao" class="CaixaTexto" id="instrucao">
                                <option value="" >Selecione...</option>
                                <?php while ( $dados_inst = $query_inst->fetch_assoc() ) { ?>
                                <option value="<?php echo $dados_inst['idescolaridade']; ?>" <?php echo $dados_inst['idescolaridade'] == $dados_det['cod_instrucao'] ? 'selected="selected"' : ''; ?>><?php echo $dados_inst['escolaridade']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="108">Nome do Pai:</td>
                        <td colspan="3"><input name="nome_pai_det" type="text" class="CaixaTexto" id="nome_pai_det" onblur="upperMe(this); remacc(this); rpcnomepai(this, 2);" onkeypress="return blockChars(event, 1);" value="<?php echo $dados_det['pai_det'] ?>" size="100" maxlength="80" /></td>
                    </tr>
                    <tr>
                        <td width="108">Nome da Mãe:</td>
                        <td colspan="3"><input name="nome_mae_det" type="text" class="CaixaTexto" id="nome_mae_det" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 1);" value="<?php echo $dados_det['mae_det'] ?>" size="100" maxlength="80" /></td>
                    </tr>
                    <tr>
                        <td width="108">Cútis:</td>
                        <td>
                            <select name="cutis" class="CaixaTexto" id="cutis">
                                <option value="" >Selecione...</option>
                                <?php while ( $dados_cutis = $query_cutis->fetch_assoc() ) { ?>
                                <option value="<?php echo $dados_cutis['idcutis']; ?>" <?php echo $dados_cutis['idcutis'] == $dados_det['cod_cutis'] ? 'selected="selected"' : ''; ?>><?php echo $dados_cutis['cutis']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                        <td>Olhos:
                            <select name="olhos" class="CaixaTexto" id="olhos">
                                <option value="" >Selecione...</option>
                                <?php while ( $dados_olho = $query_olho->fetch_assoc() ) { ?>
                                <option value="<?php echo $dados_olho['idolhos']; ?>" <?php echo $dados_olho['idolhos'] == $dados_det['cod_olhos'] ? 'selected="selected"' : ''; ?>><?php echo $dados_olho['olhos']; ?></option>
                                <?php }; ?>
                            </select>
                            &nbsp;&nbsp;&nbsp;
                        </td>
                        <td>
                            Cabelos:
                            <select name="cabelo" class="CaixaTexto" id="cabelo">
                                <option value="" >Selecione...</option>
                                <?php while ( $dados_cabelo = $query_cabelo->fetch_assoc() ) { ?>
                                <option value="<?php echo $dados_cabelo['idcabelos']; ?>" <?php echo $dados_cabelo['idcabelos'] == $dados_det['cod_cabelos'] ? 'selected="selected"' : ''; ?>><?php echo $dados_cabelo['cabelos']; ?></option>
                                <?php }; ?>
                            </select>
                            &nbsp;&nbsp;&nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td width="108">Estatura:</td>
                        <td><input name="estatura" type="text" class="CaixaTexto" id="estatura" onkeypress="mascara(this, mest); return blockChars(event, 2);" value="<?php echo $dados_det['estatura'] = preg_replace( "/([0-9]{1})([0-9]{2})/", "\\1,\\2", $dados_det['estatura'] ) ?>" size="4" maxlength="4" /></td>
                        <td colspan="2">Peso (kg): <input name="peso" type="text" class="CaixaTexto" id="peso" onkeypress="return blockChars(event, 2);" value="<?php echo $dados_det['peso'] ?>" size="4" maxlength="3" /></td>
                    </tr>
                    <tr>
                        <td width="108">Defeitos físicos:</td>
                        <td colspan="3"><input name="defeito_fisico" type="text" class="CaixaTexto" id="defeito_fisico" onblur="upperMe(this); remacc(this); rpcsinais(this);" onkeypress="return blockChars(event, 4);" value="<?php echo $dados_det['defeito_fisico'] ?>" size="100" maxlength="60" /></td>
                    </tr>
                    <tr>
                        <td width="108">Sinal(is) de nascimento:</td>
                        <td colspan="3"><input name="sinal_nasc" type="text" class="CaixaTexto" id="sinal_nasc" onblur="upperMe(this); remacc(this); rpcsinais(this);" onkeypress="return blockChars(event, 4);" value="<?php echo $dados_det['sinal_nasc'] ?>" size="100" maxlength="60" /></td>
                    </tr>
                    <tr>
                        <td width="108">Cicatrizes:</td>
                        <td colspan="3"><input name="cicatrizes" type="text" class="CaixaTexto" id="cicatrizes" onblur="upperMe(this); remacc(this); rpcsinais(this);" onkeypress="return blockChars(event, 4);" value="<?php echo $dados_det['cicatrizes'] ?>" size="100" maxlength="60" /></td>
                    </tr>
                    <tr>
                        <td width="108">Tatuagem(ns): </td>
                        <td colspan="3"><input name="tatuagens" type="text" class="CaixaTexto" id="tatuagens" onblur="upperMe(this); remacc(this); rpcsinais(this);" onkeypress="return blockChars(event, 4);" value="<?php echo $dados_det['tatuagens'] ?>" size="100" maxlength="60" /></td>
                    </tr>
                    <tr>
                        <td width="108">Data da prisão:</td>
                        <td><input name="data_prisao" type="text" class="CaixaTexto" id="data_prisao" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value);return blockChars(event, 2);" value="<?php echo $dados_det['data_prisao'] ?>" size="12" maxlength="10" /></td>
                        <td colspan="2">
                            Primário: <input name="primario" type="radio" id="primario_0" value="1" <?php echo $dados_det['primario'] == "1" || is_null( $dados_det['primario'] ) ? 'checked="checked"' : ''; ?> /> Sim &nbsp;
                                      <input name="primario" type="radio" id="primario_1" value="0" <?php echo $dados_det['primario'] == "0" ? 'checked="checked"' : ''; ?> /> Não
                        </td>
                    </tr>
                    <tr>
                        <td width="108">Local da prisão:</td>
                        <td colspan="3">
                            <select name="local_prisao" class="CaixaTexto" id="local_prisao">
                                <option value="" >Selecione...</option>
                                <?php while ( $d_local_prisao = $q_local_prisao->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_local_prisao['idunidades'];?>" <?php echo $d_local_prisao['idunidades'] == $dados_det['cod_local_prisao'] ? 'selected="selected"' : ''; ?>><?php echo $d_local_prisao['unidades'];?></option>
                                <?php };?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="108">Situação Processual:</td>
                        <td colspan="3">
                            <select name="sit_proc" class="CaixaTexto" id="sit_proc">
                                <option value="" >Selecione...</option>
                                <?php while ( $dados_sit_pr = $query_sit_pr->fetch_assoc() ) { ?>
                                <option value="<?php echo $dados_sit_pr['idsit_proc']; ?>" <?php echo $dados_sit_pr['idsit_proc'] == $dados_det['cod_sit_proc'] ? 'selected="selected"' : ''; ?>><?php echo $dados_sit_pr['sit_proc']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="108">Prisões onde esteve recolhido:</td>
                        <td colspan="3"><textarea name="prisoes_ant" cols="100" rows="2" class="CaixaTexto" id="prisoes_ant" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 4);" onkeydown="textCounter(this, 200);" onkeyup="textCounter(this, 200);"><?php echo $dados_det['prisoes_ant']; ?></textarea></td>
                    </tr>
                    <tr>
                        <td width="108">Fuga:</td>
                        <td colspan="3">
                            <input type="radio" name="fuga" value="1" id="fuga_0" <?php echo $dados_det['fuga'] == 1 ? 'checked="checked"' : ''; ?> onclick="mostraFuga()" /> Sim &nbsp;
                            <input type="radio" name="fuga" value="0" id="fuga_1" <?php echo empty( $dados_det['fuga'] ) ? 'checked="checked"' : ''; ?> onclick="mostraFuga()" /> Não &nbsp;&nbsp;&nbsp;
                            <span id="localfugal">Local:</span> <span id="localfuga"><input name="local_fuga" type="text" class="CaixaTexto" id="local_fuga" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 3);" value="<?php echo $dados_det['local_fuga']; ?>" size="69" maxlength="60" /></span>
                        </td>
                    </tr>
                    <tr>
                        <td width="108">Última residência:</td>
                        <td colspan="3"><textarea name="resid_det" cols="100" rows="2" class="CaixaTexto" id="resid_det" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 4);" onkeydown="textCounter(this, 150);" onkeyup="textCounter(this, 150);"><?php echo $dados_det['resid_det']; ?></textarea></td>
                    </tr>
                    <tr>
                        <td width="108">Em caso de emergência avisar:</td>
                        <td colspan="3"><textarea name="caso_emergencia" cols="100" rows="2" class="CaixaTexto" id="caso_emergencia" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 4);" onkeydown="textCounter(this, 150);" onkeyup="textCounter(this, 150);"><?php echo $dados_det['caso_emergencia']; ?></textarea></td>
                    </tr>
                    <tr>
                        <td width="108">Religião:</td>
                        <td colspan="3">
                            <select name="religiao" class="CaixaTexto" id="religiao">
                                <option value="" >Selecione...</option>
                                <?php while ( $dados_religiao = $query_religiao->fetch_assoc() ) { ?>
                                <option value="<?php echo $dados_religiao['idreligiao']; ?>" <?php echo $dados_religiao['idreligiao'] == $dados_det['cod_religiao'] ? 'selected="selected"' : ''; ?>><?php echo $dados_religiao['religiao']; ?></option>
                                <?php }; ?>
                            </select>
                            &nbsp;&nbsp;&nbsp;
                            Possui advogado particular:
                            <input name="possui_adv" type="radio" id="possui_adv_0" value="1" <?php echo $dados_det['possui_adv'] == 1 ? 'checked="checked"' : ''; ?>  />Sim &nbsp;
                            <input name="possui_adv" type="radio" id="possui_adv_1" value="0" <?php echo $dados_det['possui_adv'] == 0 ? 'checked="checked"' : ''; ?>  />Não
                        </td>
                    </tr>
                    <tr>
                        <td width="108">Uniforme:</td>
                        <td colspan="3">
                            Jaleco: <input type="checkbox" name="jaleco" id="jaleco" value="1"  <?php echo $dados_det['jaleco'] == 1 ? 'checked="checked"' : '' ?>/>&nbsp;&nbsp;&nbsp;
                            Calça: <input type="checkbox" name="calca" id="calca" value="1"  <?php echo $dados_det['calca'] == 1 ? 'checked="checked"' : '' ?>/>
                        </td>
                    </tr>

                    <tr>
                        <td width="108">PL:</td>
                        <td colspan="3"><input name="pl" type="text" class="CaixaTexto" id="pl" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 4);" size="10" maxlength="8" value="<?php echo $dados_det['pl'] ?>" /></td>
                    </tr>
                    <tr>
                        <td width="108">Guia local:</td>
                        <td colspan="3"><input name="guia_local" type="text" class="CaixaTexto" id="guia_local" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 4);" size="10" maxlength="6" value="<?php echo $dados_det['guia_local'] ?>" /></td>
                    </tr>
                    <tr>
                        <td width="108">Guia número:</td>
                        <td colspan="3"><input name="guia_numero" type="text" class="CaixaTexto" id="guia_numero" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 4);" size="10" maxlength="10" value="<?php echo $dados_det['guia_numero'] ?>" /></td>
                    </tr>

                </table>

                </table>

                <input name="old_matr" type="hidden" id="old_matr" value="<?php if ( !empty( $dados_det['matricula'] ) ) echo formata_num( $dados_det['matricula'] ) ?>" />
                <input name="old_cpf" type="hidden" id="old_cpf" value="<?php if ( !empty( $dados_det['cpf'] ) ) echo formata_num( $dados_det['cpf'], 2 ) ?>" />
                <input name="iddet" type="hidden" id="iddet" value="<?php echo $iddet; ?>" />

                <input type="hidden" name="old_uf" id="old_uf" value="<?php echo $dados_det['idestado'];?>" />
                <input type="hidden" name="old_cidade" id="old_cidade" value="<?php echo $dados_det['cod_cidade'];?>" />

                <div class="form_bts">
                    <input class="form_bt" name="atualizar" type="submit" value="Atualizar" />
                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>
            <script type="text/javascript">

                $(function() {

                    $.monta_box_uf();

                    $( "#nome_det" ).focus();
                    $( "#nasc_det, #data_prisao" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });
                });

                mostraFuga();

            </script>

<?php include 'footer.php'?>
