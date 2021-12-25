<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_print.php';

$pag  = link_pag();
$tipo = '';

$n_bonde   = get_session( 'n_bonde', 'int' );
$n_bonde_n = 2;

$motivo_pag = 'IMPRESSÃO DA LISTA DE BONDE';

if ( $n_bonde < $n_bonde_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 'f' );

    exit;

}

$imp_bonde = get_session( 'imp_bonde', 'int' );
$n_imp_n   = 1;

if ( $imp_bonde < $n_imp_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 'f' );

    exit;

}

$n_bonde_fut = get_session( 'n_bonde_fut', 'int' );

$titulo  = get_session( 'titulo' );
$iduser  = get_session( 'user_id' );
$ip      = $_SERVER['REMOTE_ADDR'];
$maquina = substr( $ip, strrpos( $ip, '.' ) + 1 );

$idbonde = get_get( 'idbonde', 'int' );

if ( empty( $idbonde ) ){

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 'f' );

    exit;

}

$q_bonde = "SELECT
              `bonde_det`.`cod_detento`,
              `bonde_det`.`idbd`,
              `bonde_locais`.`idblocal`,
              `detentos`.`nome_det`,
              `detentos`.`matricula`,
              `detentos`.`pai_det`,
              `detentos`.`mae_det`,
              `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
              `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
              `unidades`.`unidades` AS destino,
              `unidades_out`.`idunidades` AS iddestino,
              `cela`.`cela`,
              `raio`.`raio`
            FROM
              `bonde`
              INNER JOIN `bonde_locais` ON `bonde_locais`.`cod_bonde` = `bonde`.`idbonde`
              LEFT JOIN `bonde_det` ON `bonde_det`.`cod_bonde_local` = `bonde_locais`.`idblocal`
              LEFT JOIN `detentos` ON `bonde_det`.`cod_detento` = `detentos`.`iddetento`
              LEFT JOIN `unidades` ON `bonde_locais`.`cod_unidade` = `unidades`.`idunidades`
              LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
              LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
              LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
              LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
              LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
            WHERE
              `bonde`.`idbonde` = $idbonde
            ORDER BY
              `unidades`.`unidades`, `detentos`.`nome_det`";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_bonde = $model->query( $q_bonde );

// fechando a conexao
$model->closeConnection();

if( !$q_bonde ) {

    echo msg_js( 'FALHA!!!', 'f' );
    exit;

}

$cont_q_bonde = $q_bonde->num_rows;

if( $cont_q_bonde < 1 ) {
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ocorrências ( BONDE - IMPRESSÃO ).\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 'f' );
    exit;
}

$q_data_bonde = "SELECT
                   `bonde_data`,
                   DATE_FORMAT( `bonde_data`, '%d/%m/%Y' ) AS bonde_data_f
                 FROM
                   `bonde`
                 WHERE
                   `idbonde` = $idbonde";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_data_bonde = $model->query( $q_data_bonde );

// fechando a conexao
$model->closeConnection();

if( !$q_data_bonde ) {

    echo msg_js( 'FALHA!!!', 'f' );
    exit;

}

$cont_q_data_bonde = $q_data_bonde->num_rows;

if( $cont_q_data_bonde < 1 ) {
    $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ocorrências ( DATA DO BONDE ).\n\n Página: $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 'f' );
    exit;
}

$d_data_bonde = $q_data_bonde->fetch_assoc();

$data_bonde    = $d_data_bonde['bonde_data'];
$data_bonde_ts = strtotime( $data_bonde );

if ( $n_bonde_fut < 1 ) {

    $data_limit = strtotime('+1 day');

    if ( $data_bonde_ts > $data_limit ) {
        $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página de impressão do bonde, sem permiçõs ( BONDE COM DATA FUTURA ).\n\nPágina: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!!!', 'f' );
        exit;
    }

}

$data_bonde_f = $d_data_bonde['bonde_data_f'];

$mensagem = "[ IMPRESSÃO DE LISTA DE BONDE ]\n Impressão da lista de bonde. \n\n <b>ID:</b> $idbonde \n <b>Data do bonde:</b> $data_bonde_f \n";
salvaLog($mensagem);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="pt-br" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" Accept-Language="pt-br"/>
        <link rel="shortcut icon" href="<?php echo SICOP_SYS_IMG_PATH; ?>favicon.ico" type="image/x-icon" />
        <title><?php echo $titulo ?></title>
        <link href="<?php echo SICOP_ABS_PATH ?>css/reset.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SICOP_ABS_PATH ?>css/estilo_print.css" rel="stylesheet" type="text/css" />
    </head>
    <body class="small" onload="Javascript:window.print();self.window.close()">
    <!-- onload="Javascript:window.print();self.window.close()" -->
        <div class="corpo">
            <p class="par_corpo"><?php echo SICOP_DET_DESC_U; ?>S QUE SERÃO TRASFERIDOS</p>
            <p class="par_corpo">DATA: <b><?php echo $data_bonde_f; ?> - <?php echo mb_strtoupper( dia_semana_f( $data_bonde_ts, 1 ) ); ?></b></p>
            <p class="par_corpo">&nbsp;</p>
            <table class="bonde_list">
                <tr class="cab">
                    <th class="num_od">N</th>
                    <th class="nome_det"><?php echo SICOP_DET_DESC_FU; ?></th>
                    <th class="matr_det">Matrícula</th>
                    <th class="raio_det"><?php echo SICOP_RAIO ?></th>
                    <th class="cela_det"><?php echo SICOP_CELA ?></th>
                    <th class="det_obs">&nbsp;</th>
                </tr>

                    <?php
                        $i = 0;

                        $dest_ant = '';

                        while( $d_bonde = $q_bonde->fetch_assoc() ) {

                            $quebra = FALSE;

                            if ( $d_bonde['destino'] != $dest_ant ){
                                $quebra = TRUE;
                            }

                            $tipo_mov_in  = $d_bonde['tipo_mov_in'];
                            $tipo_mov_out = $d_bonde['tipo_mov_out'];
                            $iddestino    = $d_bonde['iddestino'];

                            $sit_det = manipula_sit_det_id($tipo_mov_in, $tipo_mov_out, $iddestino);

                    ?>
                <?php if ( $quebra ){ ?>
                <tr>

                    <td colspan="6" class="dest_det">PARA O(A) <?php echo $d_bonde['destino'] ?></td>

                </tr>
                <?php } ?>
                <?php

                    // se nao tiver o $d_bonde['idbd'] é por que não possui detentos para o local
                    if ( empty( $d_bonde['idbd'] ) ){

                    ?>

                <tr>

                    <td class="noh_det" colspan="6">Não há <?php echo SICOP_DET_DESC_L; ?>s.</td>

                </tr>
                <?php
                    // para iniciar uma nova iteração
                    continue;

                    } ?>
                <tr>
                    <td class="num_od"><?php echo++$i ?></td>
                    <td class="nome_det"><?php echo $d_bonde['nome_det'] ?></td>
                    <td class="matr_det"><?php if ( !empty( $d_bonde['matricula'] ) ) echo formata_num( $d_bonde['matricula'] ); ?></td>
                    <td class="raio_det"><?php echo $d_bonde['raio'] ?></td>
                    <td class="cela_det"><?php echo $d_bonde['cela'] ?></td>
                    <td class="det_obs"><?php if( $sit_det == SICOP_SIT_DET_TRANA ) echo 'TRANSITO'; ?></td>
                </tr>
                <?php
                        $dest_ant = $d_bonde['destino'];
                    }
                ?>
            </table><!-- fim da table."bonde_list" -->

            <p align="right" class="par_user_alto">Usuário: <?php echo $iduser?>; Computador: <?php echo $maquina; ?>; em <?php echo date( 'd/m/Y \à\s H:i' )?></p>

        </div>
    </body>
</html>