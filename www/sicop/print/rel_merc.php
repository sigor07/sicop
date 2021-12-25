<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_print.php';

$pag        = link_pag();
$tipo       = '';
$motivo_pag = 'IMPRESSÃO DE SEDEX - RELAÇÃO DE MERCADORIAS';
$titulo     = get_session( 'titulo' );
$cidade     = get_session( 'cidade' );

$imp_sedex = get_session( 'imp_sedex', 'int' );
if ( empty( $imp_sedex ) or $imp_sedex < 1 ) {

    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso à página SEM PERMISSÕES ( $motivo_pag ).";
    get_msg( $msg, 1 );

    exit;

}

$is_post = is_post();

if ( !$is_post ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 'f' );

    exit;

}

extract( $_POST, EXTR_OVERWRITE );

$idsedex = empty( $idsedex ) ? '' : $idsedex;
if ( empty( $idsedex ) ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "O usuário não marcou nenhum sedex ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'Você deve marcar pelo menos um sedex!', 1 );

    exit;

}

// monta a variavel para o comparador IN()
$v_sedex = '';
foreach ( $idsedex as $indice => $valor ) {
    if ( (int)$valor == NULL ) continue;
    $v_sedex .= (int)$valor . ',';
}

if ( empty( $v_sedex ) ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "Após validação, o array ficou vazio ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 'f' );

    exit;

}

$v_sedex = substr( $v_sedex, 0, -1 );

$q_sedex = "SELECT
              `sedex`.`idsedex`,
              `sedex`.`cod_sedex`,
              `detentos`.`nome_det`,
              `detentos`.`matricula`,
              `visitas`.`nome_visit`,
              `tipoparentesco`.`parentesco`,
              `cela`.`cela`,
              `raio`.`raio`
            FROM
              `sedex`
              INNER JOIN `detentos` ON `sedex`.`cod_detento` = `detentos`.`iddetento`
              LEFT JOIN `visitas` ON `sedex`.`cod_visita` = `visitas`.`idvisita`
              LEFT JOIN `tipoparentesco` ON `visitas`.`cod_parentesco` = `tipoparentesco`.`idparentesco`
              LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
              LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
            WHERE
              `sedex`.`idsedex` IN($v_sedex)
            ORDER BY
              `detentos`.`nome_det`";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_sedex = $model->query( $q_sedex );

// fechando a conexao
$model->closeConnection();

if( !$q_sedex ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 'f' );

    exit;

}

$cont_sedex = $q_sedex->num_rows;

if( $cont_sedex < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "A consulta de retornou 0 ocorrências ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( '', 'f' );

    exit;

}

// pegar os dados dos sedex
$sedex = dados_sedex( $v_sedex );

$msg             = array( );
$msg['tipo']     = 'desc';
$msg['entre_ch'] = $motivo_pag;
$msg['text']     = "Impressão da relação de mercadorias do sedex. \n\n $sedex";

get_msg( $msg, 1 );

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="pt-br" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="shortcut icon" href="<?php echo SICOP_SYS_IMG_PATH; ?>favicon.ico" type="image/x-icon" />
        <title><?php echo $titulo ?></title>
        <link href="<?php echo SICOP_ABS_PATH ?>css/reset.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SICOP_ABS_PATH ?>css/estilo_p_sedex.css" rel="stylesheet" type="text/css" />
    </head>
    <body onload="Javascript:window.print();self.window.close()">
        <!-- onload="Javascript:window.print();self.window.close()" -->

      <?php
      $db = SicopModel::getInstance();
      $i = 0;
      while( $dados = $q_sedex->fetch_assoc(  ) ) {
      ++$i;
      ?>

        <div class="corpo">
            <?php if ( $i == 1  ) { ?>
            <div>&nbsp;</div>
            <?php } ?>
            <p align="center" class="par_forte_i">EQUIPE DE CONTROLE</p>
            <p align="right"><b><?php echo SICOP_RAIO_AB; ?>: <?php echo $dados['raio']; ?> <?php echo SICOP_CELA_AB; ?>: <?php echo $dados['cela']; ?></b></p>
            <br />
            <p class="par_pessoas"><?php echo SICOP_DET_DESC_FU; ?>: <?php echo $dados['nome_det']; ?></p>
            <p class="par_pessoas">Matrícula: <?php echo formata_num( $dados['matricula'] ) ; ?></p>
            <p >&nbsp;</p>
            <p class="par_pessoas">Rementente: <?php echo $dados['nome_visit']; ?></p>
            <p class="par_pessoas">Parentesco: <?php echo $dados['parentesco']; ?></p>
            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">
                Declaro que recebi, em data supra, o SEDEX Nº <?php echo formata_num_sedex( $dados['cod_sedex'] ); ?>,
                aberto e conferido  em minha presença, e que, caso haja produtos ou mercadorias retidas,
                terei prazo de 15 (quinze) dias para providenciar a retirada.
            </p>
            <br />

            <p align="center" class="par_forte_i">DESCRIÇÃO DO CONTEÚDO</p>

            <?php

            $ids = $dados['idsedex'];

            $q_item_sedex_in = "SELECT
                                  `tipo_un_medida`.`un_medida`,
                                  `sedex_itens`.`id_item`,
                                  `sedex_itens`.`quant`,
                                  `sedex_itens`.`desc`,
                                  `sedex_itens`.`retido`,
                                  DATE_FORMAT( `sedex_itens`.`data_add`, '%d/%m/%Y' ) AS `data_add`
                                FROM
                                  `sedex_itens`
                                  INNER JOIN `tipo_un_medida` ON `sedex_itens`.`cod_um` = `tipo_un_medida`.`idum`
                                WHERE
                                  `sedex_itens`.`cod_sedex` = $ids
                                  AND
                                  `sedex_itens`.`retido` = FALSE
                                ORDER BY
                                  `sedex_itens`.`retido`";


            $q_item_sedex_in = $db->query( $q_item_sedex_in );

            if ( !$q_item_sedex_in ) {

                // montar a mensagem q será salva no log
                $msg = array();
                $msg['tipo']  = 'err';
                $msg['text']  = "Falha na consulta ( ITENS DO SEDEX - $motivo_pag ).";
                $msg['linha'] = __LINE__;
                get_msg( $msg, 1 );

                echo msg_js( '', 1 );
                exit;

            }

            $cont_item_sedex_in = $q_item_sedex_in->num_rows;

            $q_item_sedex_out = "SELECT
                                   `tipo_un_medida`.`un_medida`,
                                   `sedex_itens`.`id_item`,
                                   `sedex_itens`.`quant`,
                                   `sedex_itens`.`desc`,
                                   `sedex_itens`.`retido`,
                                   DATE_FORMAT( `sedex_itens`.`data_add`, '%d/%m/%Y' ) AS `data_add`
                                 FROM
                                   `sedex_itens`
                                   INNER JOIN `tipo_un_medida` ON `sedex_itens`.`cod_um` = `tipo_un_medida`.`idum`
                                 WHERE
                                   `sedex_itens`.`cod_sedex` = $ids
                                   AND
                                   `sedex_itens`.`retido` = TRUE
                                 ORDER BY
                                   `sedex_itens`.`retido`";


            $q_item_sedex_out = $db->query( $q_item_sedex_out );

            if ( !$q_item_sedex_out ) {

                // montar a mensagem q será salva no log
                $msg = array();
                $msg['tipo']  = 'err';
                $msg['text']  = "Falha na consulta ( ITENS DO SEDEX - $motivo_pag ).";
                $msg['linha'] = __LINE__;
                get_msg( $msg, 1 );

                echo msg_js( '', 1 );
                exit;

            }

            $cont_item_sedex_out = $q_item_sedex_out->num_rows;

            if ( $cont_item_sedex_in < 1 and $cont_item_sedex_out < 1 ) {
            ?>

            <div class="linha"><hr /></div>
            <div class="linha"><hr /></div>
            <div class="linha"><hr /></div>
            <div class="linha"><hr /></div>
            <div class="linha"><hr /></div>
            <div class="linha"><hr /></div>
            <div class="linha"><hr /></div>
            <div class="linha"><hr /></div>
            <div class="linha"><hr /></div>
            <div class="linha"><hr /></div>
            <div class="linha"><hr /></div>
            <div class="linha"><hr /></div>
            <div class="linha"><hr /></div>

            <?php } else { ?>

            <table class="tb_itens_sedex">
                <tr>
                    <th> ITENS ENTREGUES </th>
                    <th> ITENS RETIDOS </th>
                </tr>
                <tr>
                    <td>
                    <?php
                    $saida = '';
                    if ( $cont_item_sedex_in < 1 ) {
                        $saida = '<p class="itens_sedex">Não há itens.</p>';
                    } else {

                        $saida = '<p class="itens_sedex">';

                        while( $d_item_sedex_in = $q_item_sedex_in->fetch_object() ) {

                            $quant = str_replace( '.', ',', $d_item_sedex_in->quant );

                            $saida .= $quant . ' ' . $d_item_sedex_in->un_medida . ' ' . $d_item_sedex_in->desc . '<br />';

                        }

                        $saida .= '</p>';

                    }

                    echo $saida;

                    ?>
                    </td>
                    <td>
                    <?php
                    $saida = '';
                    if ( $cont_item_sedex_out < 1 ) {
                        $saida = '<p class="itens_sedex">Não há itens.</p>';
                    } else {

                        $saida = '<p class="itens_sedex">';

                        while( $d_item_sedex_out = $q_item_sedex_out->fetch_object() ) {

                            $quant = str_replace( '.', ',', $d_item_sedex_out->quant );

                            $saida .= $quant . ' ' . $d_item_sedex_out->un_medida . ' ' . $d_item_sedex_out->desc . '<br />';

                        }

                        $saida .= '</p>';

                    }

                    echo $saida;

                    ?>
                    </td>
                </tr>
            </table>



            <?php } ?>

            <p class="par_data"><?php echo $cidade;?>, <?php echo data_f(); ?></p>
            <br />
            <br />
            <p class="par_ass">__________________________________________________________</p>
            <p class="par_ass"><?php echo SICOP_DET_DESC_FU; ?> (nome legível)</p>
            <br />
            <br />
            <br />

            <div class="block_ass">
                <p class="par_ass_func">&nbsp;</p>
                <p class="par_ass_func">&nbsp;</p>
                <p class="par_ass_func">&nbsp;</p>
                <p class="par_ass_func">____________________________________</p>
                <p class="par_ass_func">Visto do funcionário</p>
            </div>

<!--            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">&nbsp;</p>-->

            <div style="clear: none; float: left; width: 45%;">
                <p class="par_ass_visit">Retirei nesta data, os pertences retidos.</p>
                <p class="par_ass_visit">Data ______/______/____________</p>
                <p class="par_ass_visit">&nbsp;</p>
                <p class="par_ass_visit_name">____________________________________</p>
                <p class="par_ass_visit_name">Visitante (nome legível)</p>
            </div>

            <?php if ( $cont_sedex != $i  ) { ?>
            <div style="page-break-before: always;">&nbsp;</div>
            <?php } ?>
        </div>
        <?php
        } // /while( $dados = ...
        $db->closeConnection();
        ?>
    </body>
</html>