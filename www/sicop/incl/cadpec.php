<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$n_incl   = get_session( 'n_incl', 'int' );
$n_incl_n = 3;

$targ = get_get( 'targ', 'int' );

$botao_canc = 'history.go(-1)';
$botao_value = 'Cancelar';

if ( $targ == 1 ) {
    $botao_canc = 'self.window.close()';
    $botao_value = 'Fechar';
}

$motivo_pag = 'CADASTRAMENTO DE PECÚLIO - INCLUSÃO';

if ( $n_incl < $n_incl_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    $ret = 1;
    if ( $targ == 1 ) $ret = 'f';

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', $ret );

    exit;

}

$iddet = get_get( 'iddet', 'int' );

if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    $ret = 1;
    if ( !empty ( $targ ) ) $ret = 'f';
    echo msg_js( '', $ret );

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
    $msg['text']  = "Tentativa de cadastramento de pertence para detento que não possui matrícula.\n\n $detento";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    require 'cab_simp.php';

    $msg = 'Você não pode cadastrar pertences para um detento que ainda não possui matrícula.';
    $ret = 1;
    if ( !empty ( $targ ) ) $ret = 'f';
    echo msg_js( "$msg", $ret );

    exit;

}

$q_pec = "SELECT
            `peculio`.`idpeculio`,
            `peculio`.`cod_detento`,
            `peculio`.`descr_peculio`,
            DATE_FORMAT( `peculio`.`data_add`, '%d/%m/%Y' ) AS data_add_f,
            `tipopeculio`.`tipo_peculio`
          FROM
            `peculio`
            INNER JOIN `tipopeculio` ON `peculio`.`cod_tipo_peculio` = `tipopeculio`.`idtipopeculio`
          WHERE
            `peculio`.`cod_detento` = $iddet
            AND
            `peculio`.`retirado` = FALSE
          ORDER BY
            `peculio`.`data_add`, `tipopeculio`.`tipo_peculio`";

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

// $esp = espécie
$esp = empty( $_GET['esp'] ) ? '' : (int)$_GET['esp'];

$desc_pag = 'Cadastrar pertence';

$q_tip_pec = 'SELECT `idtipopeculio`, `tipo_peculio` FROM `tipopeculio` ORDER BY `tipo_peculio`';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_tip_pec = $model->query( $q_tip_pec );

// fechando a conexao
$model->closeConnection();

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

if ( $targ == 1 ){

    require 'cab_simp.php';

} else {

    require 'cab.php';
    $pag_atual = $_SERVER['PHP_SELF'];
    $qs = $_SERVER['QUERY_STRING'];

    if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

    $trail = new Breadcrumb();
    $trail->add( $desc_pag, $pag_atual, 4 );
    $trail->output();

}
?>

            <p class="descript_page">CADASTRAR PERTENCES</p>

            <?php include 'quali/det_basic.php';?>

            <p class="table_leg">Pertence</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendpeculio.php" method="post" name="cadpert" id="cadpert" onsubmit="return validacadpert()">

                <table class="edit">
                    <tr >
                        <td width="70" height="20" valign="top">Tipo:</td>
                        <td width="325" valign="top">
                            <select name="tipo_peculio" class="CaixaTexto" id="tipo_peculio" onchange="javascript: document.getElementById('descr_peculio').focus();">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while( $d_tip_pec = $q_tip_pec->fetch_assoc() ) { ?>
                                <option value="<?php echo $d_tip_pec['idtipopeculio'];?>"><?php echo $d_tip_pec['tipo_peculio'];?></option>
                                <?php };?>
                            </select>
                            <script type="text/javascript">id('tipo_peculio').focus();</script>
                        </td>
                    </tr>
                    <tr >
                        <td height="20" valign="top">Descrição:</td>
                        <td valign="top"><textarea name="descr_peculio" id="descr_peculio" cols="60" rows="3" class="CaixaTexto" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event, 4);"></textarea></td>
                    </tr>
                </table>

                <input name="iddet" type="hidden" id="iddet" value="<?php echo $iddet;?>" />
                <input name="targ" type="hidden" id="targ" value="<?php echo $targ;?>" />
                <input name="esp" type="hidden" id="esp" value="<?php echo $esp;?>" />

                <input name="proced" type="hidden" id="proced" value="3" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="submit" id="submit" value="Cadastrar" />
                    <input class="form_bt" type="submit" name="cadadd" id="submit" value="Cadastrar e adicionar outro" />
                    <input class="form_bt" type="button" name="" onclick="<?php echo empty( $esp  ) ? $botao_canc : 'history.go(-1)'; ?>" value="<?php echo $botao_value ?>" />
                </div>

            </form>

            <div class="linha">
                PERTENCES
                <hr />
            </div>

            <?php

            // instanciando o model
            $model = SicopModel::getInstance();

            // executando a query
            $q_pec = $model->query( $q_pec );

            // fechando a conexao
            $model->closeConnection();

            $cont_pec = $q_pec->num_rows;
            if( !$q_pec or $cont_pec < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não há pertences cadastrados.</p>';
            } else {
                ?>

            <table class="lista_busca">

                <tr>
                    <th class="desc_data">DATA</th>
                    <th class="tipo_pec">TIPO</th>
                    <th class="desc_pec">DESCRIÇÃO</th>
                </tr>

                <?php while( $d_pec = $q_pec->fetch_assoc() ) { ?>

                <tr class="even">
                    <td class="desc_data"><?php echo $d_pec['data_add_f'] ?></td>
                    <td class="tipo_pec"><?php echo $d_pec['tipo_peculio'] ?></td>
                    <td class="desc_pec"><?php echo nl2br($d_pec['descr_peculio']) ?></td>
                </tr>

                <?php } // fim do while ?>

            </table>
            <?php } // fim do if que conta o número de ocorrencias ?>
            <!--&#13;-->

<?php include 'footer.php';?>