<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';

$imp_peculio = get_session( 'imp_peculio', 'int' );
$n_peculio_n = 1;

$targ = get_get( 'targ', 'int' );

$botao_canc = 'history.go(-1)';
$botao_value = 'Cancelar';

if ( $targ == 1) {
    $botao_canc = 'self.window.close()';
    $botao_value = 'Fechar';
}

$motivo_pag = 'IMPRESSÃO DE PECÚLIO DE DETENTO';

if ( $imp_peculio < $n_peculio_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    $ret = 1;
    if ( !empty ( $targ ) ) $ret = 'f';

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

$desc_pag = 'Imprimir pecúlio';

// adicionando o javascript
$cab_js   = array();
$cab_js[] = 'valida.js';
$cab_js[] = 'jquery.markrows.js';
set_cab_js( $cab_js );

if ( $targ == 1 ) {
    require 'cab_simp.php';
} else {
    require 'cab.php';
    $pag_atual = $_SERVER['PHP_SELF'];
    $qs = $_SERVER['QUERY_STRING'];

    if ( !empty( $qs ) ) {
        $pag_atual .= '?' . $qs;
    }
    $trail = new Breadcrumb();
    $trail->add( $desc_pag, $pag_atual, 5 );
    $trail->output();
}
?>

            <p class="descript_page">IMPRIMIR PECÚLIO</p>

            <?php include 'quali/det_basic.php'; ?>


            <div class="linha_pec">
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

            $cont_pec = 0;

            if( $q_pec ) $cont_pec = $q_pec->num_rows;

            if ( $cont_pec < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não há pertences cadastrados.</p>';

                if ( !empty( $targ ) ) {
                    echo '<p class="link_common"><a href="#" title="Fechar esta janela" onClick="javascript: self.window.close(); return false" >Fechar</a></p>';
                }

                include 'footer.php';

            } else {
                ?>
            <form action="<?php echo SICOP_ABS_PATH ?>send/sendpecprint.php" method="post" name="print_pec" id="print_pec" onSubmit="return validaprintpec();">

                <table class="lista_busca grid">

                    <thead>
                        <tr>
                            <th class="desc_data">DATA</th>
                            <th class="tipo_pec">TIPO</th>
                            <th class="desc_pec">DESCRIÇÃO</th>
                            <th class="tb_ck">&nbsp;</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php while ( $d_pec = $q_pec->fetch_assoc() ) { ?>

                    <tr class="even">
                        <td class="desc_data"><?php echo $d_pec['data_add_f'] ?></td>
                        <td class="tipo_pec"><?php echo $d_pec['tipo_peculio'] ?></td>
                        <td class="desc_pec"><?php echo nl2br( $d_pec['descr_peculio'] ) ?></td>
                        <td class="tb_ck"><input name="idpec[]" type="checkbox" class="mark_row" id="idpec" value="<?php echo $d_pec['idpeculio']; ?>"/></td>
                    </tr>

                    <?php } // fim do while ?>
                    </tbody>

                    <tfoot>
                        <tr>
                            <td  class="tb_ck_leg" colspan="3">todos:</td>
                            <td class="tb_ck"><input type="checkbox" name="todos" value="todos" /></td>
                        </tr>
                    </tfoot>

                </table>

                <div class="form_bts">
                    <input class="form_bt" name="imprimir" type="submit" value="Imprimir marcados" />
                    <input class="form_bt" name="" type="button" onclick="<?php echo $botao_canc; ?>" value="<?php echo $botao_value; ?>" />
                </div>

                <input name="targ" type="hidden" id="targ" value="<?php echo $targ; ?>" />

            </form>

            <?php } // fim do if que conta o número de ocorrencias ?>

<?php include 'footer.php'; ?>