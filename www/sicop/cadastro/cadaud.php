<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_cadastro = get_session( 'n_cadastro', 'int' );
$n_cad_n    = 3;

if ( $n_cadastro < $n_cad_n ) {

    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = 'CADASTRAMENTO DE AUDIÊNCIA';
    get_msg( $msg, 1 );

    exit;

}

$iddet = get_get( 'iddet', 'int' );

if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = 'Tentativa de acesso direto à página ( IDENTIFICADOR EM BRANCO - CADASTRAMENTO DE AUDIÊNCIA ).';
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$queryaud = "SELECT
               `idaudiencia`,
               `cod_detento`,
               DATE_FORMAT(`data_aud`, '%d/%m/%Y') AS `data_aud_f`,
               DATE_FORMAT(`hora_aud`, '%H:%i') AS `hora_aud_f`,
               `local_aud`,
               `cidade_aud`,
               `tipo_aud`,
               `num_processo`,
               `sit_aud`
             FROM
               `audiencias`
             WHERE
               `cod_detento` = $iddet AND `data_aud` >= DATE(NOW())
             ORDER BY
               `data_aud`, `hora_aud`";

$data_aud_f   = '';
$hora_aud_f   = '';
$local_aud    = '';
$cidade_aud   = '';
$tipo_aud     = '1';
$num_processo = '';
$motivo_justi = '';
$sit_aud      = '11';

$ant = get_get( 'ant', 'int' );
if ( !empty ( $ant ) ) {

    $idaud = get_session( 'l_id_aud', 'int' );

    if ( !empty ( $idaud ) ) {

        $query_aud_ant = "SELECT
                            DATE_FORMAT(`data_aud`, '%d/%m/%Y') AS `data_aud_f`,
                            DATE_FORMAT(`hora_aud`, '%H:%i') AS `hora_aud_f`,
                            `local_aud`,
                            `cidade_aud`,
                            `tipo_aud`,
                            `num_processo`,
                            `motivo_justi`,
                            `sit_aud`
                          FROM
                            `audiencias`
                          WHERE
                            `idaudiencia` = $idaud
                          LIMIT 1";

        // instanciando o model
        $model = SicopModel::getInstance();

        // executando a query
        $query_aud_ant = $model->query( $query_aud_ant );

        // fechando a conexao
        $model->closeConnection();

        $motivo_pag = 'CADASTRAMENTO DE AUDIÊNCIA - ANTERIOR';
        if ( !$query_aud_ant ) {

            // montar a mensagem q será salva no log
            $msg = array( );
            $msg['tipo'] = 'err';
            $msg['text'] = "Falha na consulta ( $motivo_pag ).";
            $msg['linha'] = __LINE__;
            get_msg( $msg, 1 );

            echo msg_js( 'FALHA!', 1 );
            exit;

        }

        $cont = $query_aud_ant->num_rows;
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

        $d_aud = $query_aud_ant->fetch_assoc();

        extract( $d_aud, EXTR_OVERWRITE );

    }

}

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Cadastrar audiência';

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

            <p class="descript_page">CADASTRAR AUDIÊNCIA</p>

            <?php include 'quali/det_cad.php'; ?>

            <p class="table_leg">Audiências agendadas</p>
            <?php

            // instanciando o model
            $model = SicopModel::getInstance();

            // executando a query
            $queryaud = $model->query( $queryaud );

            // fechando a conexao
            $model->closeConnection();

            $conta = $queryaud->num_rows;
            if ( $conta < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Nenhuma audiência agendada.</p>';
            } else {
                ?>

            <table class="lista_busca">
                <tr>
                    <th class="local_aud_hist">LOCAL DE APRESENTAÇÃO</th>
                    <th class="cidade_aud_hist">CIDADE</th>
                    <th class="data_hora_aud">DATA / HORA</th>
                    <th class="n_process">Nº DO PROCESSO</th>
                </tr>
                <?php
                while ( $dadosa = $queryaud->fetch_assoc() ) {

                    $aud = trata_sit_aud( $dadosa['sit_aud'] );

                    ?>
                <tr class="even_dk" title="Situação da audiência: <?php echo $aud['sitaud']; ?>">
                    <td class="local_aud_hist"><a href="<?php echo SICOP_ABS_PATH ?>cadastro/detalaud.php?idaud=<?php echo $dadosa['idaudiencia'] ?>" ><?php echo $dadosa['local_aud'] ?></a></td>
                    <td class="cidade_aud_hist <?php echo $aud['css_class']; ?>"><?php echo $dadosa['cidade_aud'] ?></td>
                    <td class="data_hora_aud <?php echo $aud['css_class']; ?>"><?php echo $dadosa['data_aud_f'] . ' às ' . $dadosa['hora_aud_f']?></td>
                    <td class="n_process <?php echo $aud['css_class']; ?>"><?php echo $dadosa['num_processo'] ?></td>
                </tr>
                <?php } // fim do while ?>
            </table>
            <?php } // fim do if que conta o número de ocorrencias ?>

            <p class="table_leg">Nova audiência</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendaud.php" method="post" name="aud_sing" id="aud_sing" onSubmit="return validacadaud();">

                <table class="edit" style="margin-bottom: 15px;">
                    <tr>
                        <td align="center">Tipo de audiência</td>
                    </tr>
                    <tr>
                        <td rowspan="6" style="padding: 1px 5px;">
                            <input name="tipo_aud" type="radio" id="tipo_aud_0" value="1" onClick="preenche_campos_aud(); altera_campos_aud(); oculta_campos_aud();" <?php echo ( empty ( $tipo_aud ) or $tipo_aud == '1' ) ? 'checked="checked"' : ''; ?>/> JUDICIAL<br />
                            <input name="tipo_aud" type="radio" id="tipo_aud_1" value="2" onClick="preenche_campos_aud(); altera_campos_aud(); oculta_campos_aud();" <?php echo $tipo_aud == '2' ? 'checked="checked"' : ''; ?>/> MÉDICA <br />
                            <input name="tipo_aud" type="radio" id="tipo_aud_2" value="3" onClick="preenche_campos_aud(); altera_campos_aud(); oculta_campos_aud();" <?php echo $tipo_aud == '3' ? 'checked="checked"' : ''; ?>/> IML <br />
                            <input name="tipo_aud" type="radio" id="tipo_aud_3" value="4" onClick="preenche_campos_aud(); altera_campos_aud(); oculta_campos_aud();" <?php echo $tipo_aud == '4' ? 'checked="checked"' : ''; ?>/> EXAME/PERÍCIA JUDICIAL<br />
                            <input name="tipo_aud" type="radio" id="tipo_aud_4" value="5" onClick="preenche_campos_aud(); altera_campos_aud(); oculta_campos_aud();" <?php echo $tipo_aud == '5' ? 'checked="checked"' : ''; ?>/> DELEGACIA/CADEIA PÚBLICA<br />
                            <input name="tipo_aud" type="radio" id="tipo_aud_5" value="6" onClick="preenche_campos_aud(); altera_campos_aud(); oculta_campos_aud();" <?php echo $tipo_aud == '6' ? 'checked="checked"' : ''; ?>/> PERÍCIA INSS<br />
                            <input name="tipo_aud" type="radio" id="tipo_aud_6" value="7" onClick="preenche_campos_aud(); altera_campos_aud(); oculta_campos_aud();" <?php echo $tipo_aud == '7' ? 'checked="checked"' : ''; ?>/> NOTIFICAÇÃO/CITAÇÃO CADEIA PÚBLICA<br />
                            <input name="tipo_aud" type="radio" id="tipo_aud_7" value="8" onClick="preenche_campos_aud(); altera_campos_aud(); oculta_campos_aud();" <?php echo $tipo_aud == '8' ? 'checked="checked"' : ''; ?>/> SEGURO DESEMPREGO / PIS/PASEP
                        </td>
                    </tr>
                </table>

                <table class="edit">
                    <tr>
                        <td width="95">Data/hora:</td>
                        <td><input name="data_aud" type="text" class="CaixaTexto" id="data_aud" onblur="verifica_data(this, this.value)" onkeypress="mascara_data(this, this.value); return blockChars(event, 2);" value="<?php echo $data_aud_f; ?>" size="12" maxlength="10" /> às <input name="hora_aud" type="text" class="CaixaTexto" id="hora_aud" onblur="verifica_hora(this, this.value)" onkeypress="mascara_hora(this, this.value); return blockChars(event, 2);" value="<?php echo $hora_aud_f; ?>"  size="5" maxlength="5" /></td>
                    </tr>
                    <tr>
                        <td width="95"><span id="local">Local:</span></td>
                        <td><input name="local_aud" type="text" class="CaixaTexto" id="local_aud" onBlur="upperMe(this); rpcvara(this);" onKeyPress="return blockChars(event, 4);" value="<?php echo $local_aud; ?>"  size="110" maxlength="100" /></td>
                    </tr>
                    <tr>
                        <td width="95">Cidade:</td>
                        <td><input name="cidade_aud" type="text" class="CaixaTexto" id="cidade_aud" onBlur="upperMe(this); rpccidade(this);" onKeyPress="return blockChars(event, 4);" value="<?php echo $cidade_aud; ?>"  size="60" maxlength="50" /></td>
                    </tr>
                    <tr id="num_process_field">
                        <td width="95"><span id="num">Nº do processo:</span></td>
                        <td><input name="num_processo" type="text" class="CaixaTexto" id="num_processo" onBlur="upperMe(this); remacc(this);" onKeyPress="return blockChars(event, 4);" value="<?php echo $num_processo; ?>" size="60" maxlength="50" /></td>
                    </tr>
                    <tr>
                        <td width="95">Situação:</td>
                        <td>
                            <input name="sit_aud" type="radio" id="sit_aud_0" value="11" onclick="oculta_motivo_aud()" <?php echo ( empty ( $sit_aud ) or $sit_aud == '11' ) ? 'checked="checked"' : ''; ?> /> Ativa &nbsp;&nbsp;
                            <input name="sit_aud" type="radio" id="sit_aud_1" value="12" onclick="oculta_motivo_aud()" <?php echo $sit_aud == '12' ? 'checked="checked"' : ''; ?> /> Cancelada &nbsp;&nbsp;
                            <input name="sit_aud" type="radio" id="sit_aud_2" value="13" onclick="oculta_motivo_aud()" <?php echo $sit_aud == '13' ? 'checked="checked"' : ''; ?> /> Justificada
                        </td>
                    </tr>
                    <tr id="mot_aud_field">
                        <td>Motivo:</td>
                        <td><textarea name="motivo_justi" id="motivo_justi" class="CaixaTexto" cols="109" rows="3" onBlur="upperMe(this);" onkeypress="return blockChars(event, 4);" onKeyDown="textCounter(this, 150);" onKeyUp="textCounter(this, 150);"><?php echo $motivo_justi; ?></textarea></td>
                    </tr>
                </table>

                <input type="hidden" name="iddet" id="iddet" value="<?php echo $iddet;?>" />
                <input type="hidden" name="proced" id="proced" value="3" />

                <div class="form_bts">
                    <input class="form_bt" name="atualizar" type="submit" value="Cadastrar" />
                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

            <script type="text/javascript">

                altera_campos_aud();
                oculta_campos_aud();
                oculta_motivo_aud();

                $(function() {
                    $( "#tipo_aud_0" ).focus();
                    $( "#data_aud" ).datepicker({
                        showOn: "button",
                        buttonImageOnly: true
                    });
                });

            </script>

<?php include 'footer.php';?>