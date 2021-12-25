<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_print.php';

$pag  = link_pag();
$tipo = '';
$desc_pag = '';

$imp_chefia   = get_session( 'imp_chefia' , 'int' );
$imp_cadastro = get_session( 'imp_cadastro' , 'int' );
$n_imp_n      = 1;

$motivo_pag = 'IMPRESSÃO DE LISTA DE BUSCA DE ' . SICOP_DET_DESC_U . 'S';

if ( $imp_chefia < $n_imp_n and $imp_cadastro < $n_imp_n ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 'f' );

    exit;

}

$is_post = is_post();
$iddet_s = get_session( 'iddet' );

if ( isset( $_SESSION['iddet'] ) ) unset( $_SESSION['iddet'] );

if ( !$is_post and empty( $iddet_s ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 'f' );

    exit;

}

$titulo  = get_session( 'titulo' );
$iduser  = get_session( 'user_id', 'int' );
$ip      = $_SERVER['REMOTE_ADDR'];
$maquina = substr( $ip, strrpos( $ip, '.' ) + 1 );

$v_det = $iddet_s;
$ordbusca = "`detentos`.`nome_det` ASC";

if ( $is_post ) {

    extract( $_POST, EXTR_OVERWRITE );

    if ( empty( $iddet_p ) ) {
        $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso direto à página de impressão de resultado de consulta ( ARRAY iddet EM BRANCO ).\n\n Página: $pag";
        salvaLog( $mensagem );
        echo msg_js( 'FALHA!!!', 'f' );
        exit;
    }

    // monta a variavel para o comparador IN()
    $v_det = '';
    foreach ( $iddet_p as &$valor ) {
        $valor = (int)$valor;
        if ( empty( $valor ) )
            continue;
        $v_det .= (int)$valor . ',';
    }

    if ( empty( $v_det ) ) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n\n Após validação, o array ficou vazio ( IMPRESSÃO DE RESULTADO DE CONSULTA ).\n\n Página: $pag";
        salvaLog( $mensagem );
        echo msg_js( 'FALHA!!!', 'f' );
        exit;
    }

    $v_det = substr( $v_det, 0, -1 );

    switch ( $op ) {
        default:
        case '':
        case 'nomea':
            $ordbusca = "`detentos`.`nome_det` ASC";
            break;
        case 'nomed':
            $ordbusca = "`detentos`.`nome_det` DESC";
            break;
        case 'matra':
            $ordbusca = "`detentos`.`matricula` ASC";
            break;
        case 'matrd':
            $ordbusca = "`detentos`.`matricula` DESC";
            break;
        case 'proca':
            $ordbusca = "`unidades_in`.`unidades` ASC, `detentos`.`nome_det` ASC";
            break;
        case 'procd':
            $ordbusca = "`unidades_in`.`unidades` DESC, `detentos`.`nome_det` ASC";
            break;
        case 'dataa':
            $ordbusca = "`mov_det_in`.`data_mov` ASC, `detentos`.`nome_det` ASC";
            break;
        case 'datad':
            $ordbusca = "`mov_det_in`.`data_mov` DESC, `detentos`.`nome_det` ASC";
            break;
        case 'raioa':
            $ordbusca = "`detentos`.`cod_cela` ASC, `detentos`.`nome_det` ASC";
            break;
        case 'raiod':
            $ordbusca = "`detentos`.`cod_cela` DESC, `detentos`.`nome_det` ASC";
            break;
    }

}

$q_lista = "SELECT
              `detentos`.`iddetento`,
              `detentos`.`nome_det`,
              `detentos`.`matricula`,
              DATE_FORMAT( `mov_det_in`.`data_mov`, '%d/%m/%Y' ) AS data_incl_f,
              `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
              `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
              `unidades_in`.`unidades` AS procedencia,
              `unidades_out`.`idunidades` AS iddestino,
              `cela`.`cela`,
              `raio`.`raio`
            FROM
              `detentos`
              LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
              LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
              LEFT JOIN `unidades` `unidades_in` ON `mov_det_in`.`cod_local_mov` = `unidades_in`.`idunidades`
              LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
              LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
              LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
            WHERE
              `detentos`.`iddetento` IN( $v_det )
            ORDER BY
              $ordbusca";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_lista = $model->query( $q_lista );

// fechando a conexao
$model->closeConnection();

if( !$q_lista ) {

    echo msg_js( 'FALHA!!!', 'f' );
    exit;

}

$cont_l = $q_lista->num_rows;

if( $cont_l < 1 ) {
    $mensagem = "A consulta retornou 0 ocorrências ( IMPRESSÃO DA LISTA DE BUSCA ).\n\n Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( 'FALHA!!!', 'f' );
    exit;
}

if ( $is_post ) {
    $mensagem = "[ IMPRESSÃO DE RESULTADO DE CONSULTA ]\n Impressão de resultado de consulta.\n";
    salvaLog( $mensagem );
}

$motivo = 'LISTA DE ' . SICOP_DET_DESC_U . 'S';
if ( !empty( $desc_pag ) ) {
    $motivo = $desc_pag;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="pt-br" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="shortcut icon" href="<?php echo SICOP_SYS_IMG_PATH; ?>favicon.ico" type="image/x-icon" />
        <title><?php echo $titulo ?></title>
        <link href="<?php echo SICOP_ABS_PATH ?>css/reset.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SICOP_ABS_PATH ?>css/estilo_p_lista.css" rel="stylesheet" type="text/css" />
    </head>

    <body onload="Javascript:window.print();self.window.close()">
    <!-- onload="Javascript:window.print();self.window.close()" -->

        <div class="corpo">
            <p class="par_corpo"><?php echo $motivo; ?></p>

            <table width="650" align="center" class="bordasimples_f" border="1" cellpadding="1" cellspacing="0">
                <thead>
                    <tr>
                        <th scope="col" width="40" height="15" align="center">N</th>
                        <th scope="col" width="230" align="center"><?php echo SICOP_DET_DESC_FU; ?></th>
                        <th scope="col" width="75" align="center">Matrícula</th>
                        <th scope="col" width="30" align="center"><?php echo SICOP_RAIO_AB; ?></th>
                        <th scope="col" width="20" align="center"><?php echo SICOP_CELA_AB; ?></th>
                        <th scope="col" width="155" align="center">Procedência</th>
                        <th scope="col" width="70" align="center">Inclusão</th>
                    </tr>
                </thead>
                <?php

                    $i = 0;

                    while( $d_det = $q_lista->fetch_assoc() ) {

                        ?>
                <tbody>
                    <tr>
                        <td height="18" align="center"><?php echo ++$i ?></td>
                        <td align="left" class="espaco_td"><?php echo $d_det['nome_det']?></td>
                        <td align="center"><?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] );?></td>
                        <td align="center"><?php echo $d_det['raio']?></td>
                        <td align="center"><?php echo $d_det['cela']?></td>
                        <td align="left" class="espaco_td"><?php echo $d_det['procedencia']?></td>
                        <td align="right" class="espaco_td"><?php echo $d_det['data_incl_f']?></td>
                    </tr>
                </tbody>
            <?php if ( ( $i%45 == 0 and $i != $cont_l ) and $cont_l != 45  ) { ?>
            </table>

            <p align="right" class="par_min">Usuário: <?php echo $iduser?>; Computador: <?php echo $maquina; ?>; em <?php echo date( 'd/m/Y \à\s H:i' )?></p>

            <div style="page-break-before: always;">&nbsp;</div>

            <table width="650" align="center" class="bordasimples_f" border="1" cellpadding="1" cellspacing="0">
                <thead>
                    <tr>
                        <th scope="col" width="40" height="15" align="center">N</th>
                        <th scope="col" width="230" align="center"><?php echo SICOP_DET_DESC_FU; ?></th>
                        <th scope="col" width="75" align="center">Matrícula</th>
                        <th scope="col" width="30" align="center"><?php echo SICOP_RAIO_AB; ?></th>
                        <th scope="col" width="20" align="center"><?php echo SICOP_CELA_AB; ?></th>
                        <th scope="col" width="155" align="center">Procedência</th>
                        <th scope="col" width="70" align="center">Inclusão</th>
                    </tr>
                </thead>
            <?php } ?>
            <?php } ?>

            </table>

            <p align="right" class="par_min">Usuário: <?php echo $iduser?>; Computador: <?php echo $maquina; ?>; em <?php echo date( 'd/m/Y \à\s H:i' )?></p>

        </div>
    </body>
</html>