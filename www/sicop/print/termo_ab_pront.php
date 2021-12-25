<?php
if ( !isset( $_SESSION ) ) session_start();

    require '../init/config.php';
require 'incl_print.php';

$pag  = link_pag();
$tipo = '';

$imp_pront     = get_session( 'imp_pront', 'int' );
$diretor_g     = get_session( 'diretor_geral' );
$diretor_p     = get_session( 'diretor_pront' );
$titulo        = get_session( 'titulo' );
$secretaria    = get_session( 'secretaria' );
$coordenadoria = get_session( 'coordenadoria' );
$unidadecurto  = get_session( 'unidadecurto' );
$endereco      = get_session( 'endereco' );
$cidade        = get_session( 'cidade' );
$ip            = $_SERVER['REMOTE_ADDR'];
$maquina       = substr( $ip, strrpos( $ip, '.' ) + 1 );

$motivo_pag = 'IMPRESSÃO DE TERMOS DE ABERTURA DO PROTUÁRIO';

if ($imp_pront < 1) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']     = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 'f' );

    exit;

}

$iduser  = get_session( 'user_id', 'int' );

$iddet_g = get_get( 'iddet', 'int' );

$iddet_s = get_session( 'iddet' );

$iddet = empty( $iddet_g ) ? $iddet_s : $iddet_g;

if ( isset( $_SESSION['iddet'] ) ) unset( $_SESSION['iddet'] );

if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo'] = 'atn';
    $msg['text'] = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 'f' );

    exit;

}

$q_det = "SELECT
              `detentos`.`iddetento`,
              `detentos`.`nome_det`,
              `detentos`.`matricula`,
              `detentos`.`rg_civil`,
              DATE_FORMAT(`detentos`.`nasc_det`, '%d/%m/%Y') AS `nasc_det_f`,
              `detentos`.`pai_det`,
              `detentos`.`mae_det`,
              `cidades`.`nome` AS `cidade`,
              `estados`.`sigla` AS `estado`,
              `mov_det_in`.`data_mov` AS data_incl,
              DATE_FORMAT(`mov_det_in`.`data_mov`, '%d/%m/%Y') AS data_incl_f,
              `unidades_in`.`unidades` AS procedencia
            FROM
              `detentos`
              LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
              LEFT JOIN `unidades` `unidades_in` ON `mov_det_in`.`cod_local_mov` = `unidades_in`.`idunidades`
              LEFT JOIN `cidades` ON `detentos`.`cod_cidade` = `cidades`.`idcidade`
              LEFT JOIN `estados` ON `cidades`.`cod_uf` = `estados`.`idestado`
            WHERE
              `detentos`.`iddetento` IN ( $iddet )
            ORDER BY
              `detentos`.`matricula`";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $q_det = $model->query( $q_det );

    // fechando a conexao
    $model->closeConnection();

    if( !$q_det ) {

        echo msg_js( 'FALHA!!!', 'f' );
        exit;

    }

    $cont_prot = $q_det->num_rows;

    if($cont_prot < 1) {
        $mensagem = "[ <span class='desc_erro'>*** ERRO ***</span> ]\n A consulta retornou 0 ocorrências ( TERMO DE ABERTURA DO PROTUÁRIO ).\n\n Página: $pag";
        salvaLog($mensagem);
        echo msg_js( 'FALHA!!!', 'f' );
        exit;
    }

    $query_dp = "SELECT
                     `diretor`,
                     `titulo_diretor`
                    FROM
                      diretores_n
                    WHERE iddiretoresn = $diretor_p
                    LIMIT 1";

    // instanciando o model
    $model = SicopModel::getInstance();

    // executando a query
    $query_dp = $model->query( $query_dp );

    // fechando a conexao
    $model->closeConnection();

    if( !$query_dp ) {

        echo msg_js( 'FALHA!!!', 'f' );
        exit;

    }

    $contdp = $query_dp->num_rows;

    if( $contdp < 1 ) {
        $mensagem = "A consulta retornou 0 ocorrencias (DIRETOR DE PROTUÁRIO).\n\n Página $pag";
        salvaLog( $mensagem );
        echo msg_js( 'FALHA!!!', 'f' );
        exit;
    }

    $d_dp = $query_dp->fetch_assoc();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="pt-br" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" Accept-Language="pt-br"/>
        <link rel="shortcut icon" href="<?php echo SICOP_SYS_IMG_PATH; ?>favicon.ico" type="image/x-icon" />
        <title><?php echo $titulo ?></title>
        <link href="<?php echo SICOP_ABS_PATH ?>css/reset.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SICOP_ABS_PATH ?>css/estilo_cabv.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SICOP_ABS_PATH ?>css/estilo_po.css" rel="stylesheet" type="text/css" />
    </head>

    <body onload="Javascript:window.print();self.window.close()">
    <!-- onload="Javascript:window.print();self.window.close()" -->

    <span class="_Header">
    <?php require 'cabecalho_v.php';?>
    </span>
    <div class="espaco_termo">&nbsp;</div>
    <div class="corpo_termo">

        <?php

        $i = 0;
        while( $d_det = $q_det->fetch_assoc() ) {
        ++$i;

        // pega a data da inclusão do detento, e salva em uma variavel para ser utilizada pela função data_f()
        $data_termo = $d_det['data_incl'];
        $timestamp = strtotime($data_termo);

        ?>
        <?php if ( $i == 1 ) { ?>
        <div class="quebra_pag">&nbsp;</div>
        <?php } ?>
        <?php if ( $i != 1 ) { ?>
        <div class="espaco_termo">&nbsp;</div>
        <?php } ?>
        <p class="par_corpo">&nbsp;</p>
        <p class="par_corpo">&nbsp;</p>
        <p class="par_forte_n" align="center">TERMO DE (RE)ABERTURA</p>
        <p class="par_corpo">&nbsp;</p>
        <p class="par_corpo">&nbsp;</p>
            <table width="620" align="center" cellpadding="1" cellspacing="0" class="detento">
                <tr >
                    <td colspan="3"><b><?php echo SICOP_DET_DESC_FU; ?>:</b> <?php echo $d_det['nome_det'];?></td>
                </tr>
                <tr>
                    <td width="180"><b>RG:</b> <?php echo formata_num($d_det['rg_civil']) ?></td>
                    <td width="180"><b>Matrícula:</b> <?php echo formata_num($d_det['matricula']) ?></td>
                    <td width="252"><b>Nascimento:</b> <?php echo empty($d_det['nasc_det_f']) ? "" : $d_det['nasc_det_f']; ?></td>
                </tr>
                <tr >
                    <td colspan="3"><b>Pai:</b> <?php echo $d_det['pai_det'] ?></td>
                </tr>
                <tr >
                    <td colspan="3"><b>Mãe:</b> <?php echo $d_det['mae_det'] ?></td>
                </tr>
                <tr >
                    <td colspan="3"><b>Cidade:</b> <?php echo $d_det['cidade'].' - '.$d_det['estado'] ?></td>
                </tr>
                <tr >
                    <td colspan="3"><b>Procedência:</b> <?php echo $d_det['procedencia'] ?></td>
                </tr>
            </table>
            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">Na presente data, nesta unidade prisional, declaro (RE)ABERTO o Prontuário Penitenciário d<?php echo SICOP_DET_ART_L . ' ' . SICOP_DET_DESC_L; ?> acima qualificad<?php echo SICOP_DET_ART_L; ?>.</p>
            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">&nbsp;</p>
            <p align="right"><?php echo $cidade;?>, <?php echo data_f($timestamp) ?> </p>
            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">&nbsp;</p>
            <p class="par_corpo">&nbsp;</p>

            <div class="ass_apcc">
                <p class="par_ass"><em><?php echo $d_dp['diretor'];?></em></p>
                <p class="par_ass"><?php echo $d_dp['titulo_diretor'];?></p>
            </div>

            <?php if ( $cont_prot != $i  ) { ?>
            <div class="quebra_pag" style="page-break-before: always;">&nbsp;</div>
            <?php } ?>

            <?php } ?>
            <span class="_Footer">
                  <div class="rodape_termo">
                      <p align="right" class="par_min">Usuário: <?php echo $iduser?>; Computador: <?php echo $maquina; ?>; em <?php echo date( 'd/m/Y \à\s H:i' )?></p>
                      <hr align="center" width="615" size="0" noshade="noshade" color="#000000" />
                      <p align="center"><?php echo $endereco ?></p>
                  </div>
            </span>

        </div>
    </body>
</html>