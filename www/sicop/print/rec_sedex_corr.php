<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_print.php';
require 'classes/ne/numeroExtenso.class.php';

$pag        = link_pag();
$tipo       = '';
$motivo_pag = 'IMPRESSÃO DO RECIBO DO SEDEX DA PORTARIA P/ CORREIO';

$titulo  = get_session( 'titulo' );
$unidade = get_session( 'unidadecurto' );
$cidade  = get_session( 'cidade' );
$iduser  = get_session( 'user_id' );
$ip      = $_SERVER['REMOTE_ADDR'];
$maquina = substr( $ip, strrpos( $ip, '.' ) + 1 );

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
              `sedex`.`cod_sedex`,
              `detentos`.`nome_det`,
              `visitas`.`nome_visit`,
              `sedex_motivo`.`motivo_corr`
            FROM
              `sedex`
              INNER JOIN `detentos` ON `sedex`.`cod_detento` = `detentos`.`iddetento`
              INNER JOIN `sedex_motivo` ON `sedex`.`cod_motivo_dev` = `sedex_motivo`.`idmotivo`
              LEFT JOIN `visitas` ON `sedex`.`cod_visita` = `visitas`.`idvisita`
            WHERE
              `sedex`.`idsedex` IN( $v_sedex )
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

    echo msg_js( 'FALHA!!!', 'f' );

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

    echo msg_js( 'FALHA!!!', 'f' );

    exit;

}

// pegar os dados dos sedex
$sedex = dados_sedex( $v_sedex );

$msg = array( );
$msg['tipo'] = 'desc';
$msg['entre_ch'] = $motivo_pag;
$msg['text'] = "Impressão do recibo do sedex da portaria para o correio. \n\n $sedex";

get_msg( $msg, 1 );

$ne = new numeroExtenso;

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
        <div class="corpo">
            <p align="center" class="par_forte_i">RECIBO DE DEVOLUÇÃO DE SEDEX</p>
            <br />
            <p class="par_corpo">Declaro, para os devidos fins, que recebi do(a) <?php echo $unidade; ?>, em data supra, o(s) sedex relacionado(s), DEVOLVIDO(S) pelo(s) motivo(s) descrito(s):</p>
            <br />
            <p class="par_ass">TOTAL DE <?php echo $cont_sedex ?> ( <?php echo mb_strtoupper( $ne->escrever( $cont_sedex ) ) ?> ) SEDEX</p>
            <br />
            <?php
            $i = 0;
            /**
             * configurações para a quebra de página
             * se $cont_sedex <= 11 não quebra
             * se $cont_sedex == 12 quebra na 11ª ocorrência
             * se $cont_sedex > 12 quebra na 12ª ocorrência
             * e assim com os multiplos
             */

            // número das páginas
            $num_pag = 1;

            // $por_pagina deve-se colocar 13 ao invés de 12 pois somente na primeira página vão 12 itens
            $por_pagina = 13;

            // $multip o multiplicador máximo. também é o limite do for(). precisa ser arrendondado com round()
            $multip = round( $cont_sedex / $por_pagina );

            // $cont_sedex_limit a variavel para ser comparada com $i para saber em qual linha está
            $cont_sedex_limit = $cont_sedex - 1;

            while ( $dados = $q_sedex->fetch_assoc() ) {
                ++$i;
            ?>
            <table width="640" class="bordasimples" border="1" align="center" cellpadding="1" cellspacing="0" id="fixa">
                <tr>
                    <td width="30" rowspan="3" align="center"><?php echo $i; ?></td>
                    <td width="352">Destinatário: <?php echo $dados['nome_det']; ?></td>
                    <td>Código do Sedex: <?php echo $dados['cod_sedex']; ?></td>
                </tr>
                <tr>
                    <td>Rementente: <?php echo !empty( $dados['nome_visit'] ) ? $dados['nome_visit'] : 'N/C'; ?></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2">Motivo da devolução: <?php echo $dados['motivo_corr']; ?></td>
                </tr>
            </table>
            <?php

            $quebra = false;
            for ( $n = 1; $n <= $multip; $n++ ) {

                $por_pagina_limit = $por_pagina * $n - 1;
                if ( ( $cont_sedex == $por_pagina_limit and $i == $cont_sedex_limit )
                     or
                     ( $cont_sedex > $por_pagina_limit and $i == $por_pagina_limit ) ) {
                   $quebra = true;
                }

                if ( $quebra ) break;

            }

            if ( $quebra ) {
            ?>
            <p align="right" class="par_min">Recibo de devolução de sedex p/ correio - Página: <?php echo $num_pag++; ?> - Usuário: <?php echo $iduser?>; Computador: <?php echo $maquina; ?>; em <?php echo date( 'd/m/Y \à\s H:i' )?></p>
            <div class="hRule"><hr /></div>
            <br />
            <p align="center" class="par_fim">*** CONTINUA ***</p>
            <br />
            <div class="hRule"><hr /></div>
            <div style="page-break-before: always;">&nbsp;</div>
            <?php } ?>
            <?php if ( $i != $cont_sedex ) {?>
            <br />
            <?php } ?>
            <?php } ?>
            <p align="right" class="par_min">Recibo de devolução de sedex p/ correio - Página: <?php echo $num_pag; ?> - Usuário: <?php echo $iduser?>; Computador: <?php echo $maquina; ?>; em <?php echo date( 'd/m/Y \à\s H:i' )?></p>
            <div class="hRule"><hr /></div>
            <br />
            <p align="center" class="par_fim">*** FIM DA LISTA ***</p>
            <br />
            <div class="hRule"><hr /></div>
            <br />
            <br />
            <p class="par_data"><?php echo $cidade; ?>, ________/________________/________</p>
            <br />
            <br />
            <p class="par_ass">__________________________________________________________</p>
            <p class="par_ass">Funcionário do Correio recebedor (nome legível)</p>
        </div>
    </body>
</html>