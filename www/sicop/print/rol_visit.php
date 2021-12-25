<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_print.php';

$pag        = link_pag();
$tipo       = '';
$motivo_pag = 'IMPRESSÃO DE ROL DE VISITAS';

$imp_rol = get_session ( 'imp_rol', 'int' );
$n_rol_n = 1;

if ( $imp_rol < $n_rol_n ) {

    require 'cab_simp.php';
    $tipo = 3;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso à página SEM PERMISSÕES ( $motivo_pag ).";
    get_msg( $msg, 1 );

    exit;

}

$iddet = get_get( 'iddet', 'int' );

if ( empty( $iddet ) ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo']  = 'err';
    $msg['text']  = "Identificador do visitante em branco ou inválido ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 'f');

    exit;

}

$querydet = "SELECT
               `detentos`.`iddetento`,
               `detentos`.`nome_det`,
               `detentos`.`matricula`,
               `detentos`.`rg_civil`,
               `detentos`.`execucao`,
               `detentos`.`vulgo`,
               `detentos`.`nasc_det`,
               DATE_FORMAT ( `detentos`.`nasc_det`, '%d/%m/%Y' ) AS `nasc_det`,
               `FLOOR` ( DATEDIFF ( CURDATE(), `detentos`.`nasc_det` ) / 365.25 ) AS `idade_det`,
               `detentos`.`pai_det`,
               `detentos`.`mae_det`,
               `cidades`.`nome` AS `cidade`,
               `estados`.`sigla` AS `estado`,
               `mov_det_in`.`data_mov` AS data_incl,
               DATE_FORMAT(`mov_det_in`.`data_mov`, '%d/%m/%Y') AS data_incl_f,
               `mov_det_in`.`cod_tipo_mov` AS tipo_mov_in,
               `mov_det_out`.`data_mov` AS data_excl,
               DATE_FORMAT(`mov_det_out`.`data_mov`, '%d/%m/%Y') AS data_excl_f,
               `mov_det_out`.`cod_tipo_mov` AS tipo_mov_out,
               `unidades_in`.`unidades` AS procedencia,
               `unidades_out`.`unidades` AS destino,
               `unidades_out`.`idunidades` AS iddestino,
               `cela`.`cela`,
               `raio`.`raio`,
               `det_fotos`.`foto_det_g`,
               `det_fotos`.`foto_det_p`
             FROM
               `detentos`
               LEFT JOIN `mov_det` `mov_det_in` ON `detentos`.`cod_movin` = `mov_det_in`.`id_mov`
               LEFT JOIN `mov_det` `mov_det_out` ON `detentos`.`cod_movout` = `mov_det_out`.`id_mov`
               LEFT JOIN `unidades` `unidades_in` ON `mov_det_in`.`cod_local_mov` = `unidades_in`.`idunidades`
               LEFT JOIN `unidades` `unidades_out` ON `mov_det_out`.`cod_local_mov` = `unidades_out`.`idunidades`
               LEFT JOIN `cidades` ON `detentos`.`cod_cidade` = `cidades`.`idcidade`
               LEFT JOIN `estados` ON `cidades`.`cod_uf` = `estados`.`idestado`
               LEFT JOIN `cela` ON `detentos`.`cod_cela` = `cela`.`idcela`
               LEFT JOIN `raio` ON `cela`.`cod_raio` = `raio`.`idraio`
               LEFT JOIN `det_fotos` ON `det_fotos`.`id_foto` = `detentos`.`cod_foto`
             WHERE
               `detentos`.`iddetento` = $iddet
             LIMIT 1";

$queryvis = "SELECT
               `visitas`.`idvisita`,
               `visitas`.`cod_detento`,
               `visitas`.`num_in`,
               `visitas`.`nome_visit`,
               `visitas`.`rg_visit`,
               `visitas`.`sexo_visit`,
               `visitas`.`nasc_visit`,
               DATE_FORMAT ( `visitas`.`nasc_visit`, '%d/%m/%Y' ) AS `nasc_visit_f`,
               FLOOR ( DATEDIFF ( CURDATE(), `visitas`.`nasc_visit` ) / 365.25 ) AS `idade_visit`,
               `tipoparentesco`.`parentesco`,
               `visitas`.`resid_visit`,
               `visitas`.`telefone_visit`,
               `visitas`.`pai_visit`,
               `visitas`.`mae_visit`,
               `visitas`.`doc_rg`,
               `visitas`.`doc_foto34`,
               `visitas`.`doc_resid`,
               `visitas`.`doc_ant`,
               `visitas`.`doc_cert`,
               `visitas`.`user_add`,
               DATE_FORMAT ( `visitas`.`data_add`, '%d/%m/%Y às %H:%i' ) AS `data_add`,
               `visitas`.`user_up`,
               DATE_FORMAT ( `visitas`.`data_up`, '%d/%m/%Y às %H:%i' ) AS `data_up`,
               `cidades`.`nome` AS `cidade_visit`,
               `estados`.`sigla` AS `estado_visit`,
               `visita_fotos`.`foto_visit_g`,
               `visita_fotos`.`foto_visit_p`
             FROM
               `visitas`
               LEFT JOIN `tipoparentesco` ON `visitas`.`cod_parentesco` = `tipoparentesco`.`idparentesco`
               LEFT JOIN `cidades` ON `visitas`.`cod_cidade_v` = `cidades`.`idcidade`
               LEFT JOIN `estados` ON `cidades`.`cod_uf` = `estados`.`idestado`
               LEFT JOIN `visita_fotos` ON `visita_fotos`.`id_foto` = `visitas`.`cod_foto`
             WHERE
               `visitas`.`cod_detento` = $iddet
               AND
               `visitas`.`num_in` = (SELECT `n_p_trans` FROM `detentos` WHERE `iddetento` = $iddet LIMIT 1)
             ORDER BY
               `visitas`.`nome_visit`";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$querydet = $model->query( $querydet );

// fechando a conexao
$model->closeConnection();

if ( !$querydet ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta ( $motivo_pag - DETENTO ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 1 );
    exit;

}

$contd = $querydet->num_rows;

if( $contd < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "A consulta retornou 0 ocorrências ( $motivo_pag - DETENTO ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 'f' );
    exit;

}

$d_det = $querydet->fetch_assoc();

$foto_g   = $d_det['foto_det_g'];
$foto_p   = $d_det['foto_det_p'];

$foto_det = ck_pic( $foto_g, $foto_p, false, 1 );

$tipo_mov_in  = $d_det['tipo_mov_in'];
$procedencia  = $d_det['procedencia'];
$data_incl    = $d_det['data_incl'];
$tipo_mov_out = $d_det['tipo_mov_out'];
$iddestino    = $d_det['iddestino'];
$destino      = $d_det['destino'];
$data_excl    = $d_det['data_excl'];

$det = manipula_sit_det_c( $tipo_mov_in, $procedencia, $data_incl, $tipo_mov_out, $iddestino, $destino, $data_excl );

// pegar os dados do preso
$detento = dados_det( $iddet );

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$queryvis = $model->query( $queryvis );

// fechando a conexao
$model->closeConnection();

if ( !$queryvis ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'err';
    $msg['text']  = "Falha na consulta ( $motivo_pag - VISITANTE ). \n\n $detento";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 1 );
    exit;

}

$mensagem = "[ IMPRESSÃO DE ROL DE VISITAS ]\n Impressão de rol de visitas.\n\n $detento";
salvaLog( $mensagem );

$titulo        = get_session ( 'titulo' );
$secretaria    = get_session ( 'secretaria' );
$coordenadoria = get_session ( 'coordenadoria' );
$unidadecurto  = get_session ( 'unidadecurto' );
$endereco      = get_session ( 'endereco' );

$iduser        = get_session ( 'user_id' );
$ip            = $_SERVER['REMOTE_ADDR'];
$maquina       = substr( $ip, strrpos( $ip, '.' ) + 1 );

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="shortcut icon" href="<?php echo SICOP_SYS_IMG_PATH; ?>favicon.ico" type="image/x-icon" />
        <title><?php echo $titulo ?></title>
        <link href="<?php echo SICOP_ABS_PATH ?>css/reset.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SICOP_ABS_PATH ?>css/estilo_cabv.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SICOP_ABS_PATH ?>css/estilo_print.css" rel="stylesheet" type="text/css" />
    </head>

    <body class="small" onload="javascript:window.print();self.window.close()">
        <!-- onload="Javascript:window.print();self.window.close()" -->
        <?php //require 'cabecalho.php';?>
        <?php require 'cabecalho_v.php'; ?>

        <script type="text/javascript" src="<?php echo SICOP_ABS_PATH ?>js/funcoes.js" ></script>
        <div class="corpo">

            <p align="center" class="par_extra_forte">ROL DE VISITAS</p>

            <!--class="bordasimples" border="1" cellpadding="1" cellspacing="0"-->

            <table class="quali_det_foto">
                <tr >
                    <td class="td_det_med"><span class="destaque_leg"><?php echo SICOP_DET_DESC_FU; ?>:</span> <?php echo $d_det['nome_det']; ?></td>
                    <td class="td_det_min"><span class="destaque_leg">Matrícula:</span> <?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] ); ?></td>
                    <td class="td_det_foto" rowspan="6"><img src="<?php echo $foto_det ?>" alt="" width="100" height="134" /></td>
                </tr>
                <tr>
                    <td class="td_det_med"><span class="destaque_leg">Vulgo(s):</span> <?php echo $d_det['vulgo'] ?></td>
                    <td class="td_det_min"><span class="destaque_leg">Execução:</span> <?php echo!empty( $d_det['execucao'] ) ? number_format( $d_det['execucao'], 0, '', '.' ) : 'N/C' ?></td>
                </tr>
                <tr >
                    <td class="td_det_med"><span class="destaque_leg">Data de Nascimento:</span> <?php echo empty( $d_det['nasc_det'] ) ? '' : $d_det['nasc_det'] . ' - ' . $d_det['idade_det'] . ' anos'; // echo pegaIdade($d_det['data_nasc'])   ?></td>
                    <td class="td_det_min"><span class="destaque_leg">RG Civil:</span> <?php if ( !empty( $d_det['rg_civil'] ) ) echo formata_num( $d_det['rg_civil'] ); ?></td>
                </tr>
                <tr >
                    <td class="td_det_med"><span class="destaque_leg">Cidade:</span> <?php echo $d_det['cidade'] . ' - ' . $d_det['estado'] ?></td>
                    <td class="td_det_min"><span class="destaque_leg">ID no sistema:</span> <?php echo $d_det['iddetento'] ?></td>
                </tr>
                <tr >
                    <td class="td_det_med"><span class="destaque_leg">Pai:</span> <?php echo $d_det['pai_det'] ?></td>
                    <td class="td_det_min">&nbsp;</td>
                </tr>
                <tr >
                    <td class="td_det_med"><span class="destaque_leg">Mãe:</span> <?php echo $d_det['mae_det'] ?></td>
                    <td class="td_det_min">&nbsp;</td>
                </tr>

                <tr >
                    <td class="td_det_med"><span class="destaque_leg">Situação atual:</span> <span class="quali_sit_det <?php echo $det['css_class']; ?>"><?php echo $det['sitat'] ?></span></td>
                    <td class="td_det_raio"><?php echo empty( $d_det['raio'] ) ? '' : SICOP_RAIO . ': ' . $d_det['raio']; ?></td>
                    <td class="td_det_cela"><?php echo empty( $d_det['cela'] ) ? '' : SICOP_CELA . ': ' . $d_det['cela']; ?></td>
                </tr>
                <tr >
                    <td class="td_det_med"><?php echo $det['data_incl'] ?></td>
                    <td class="td_det_med" colspan="2"><?php echo $det['data_excl'] ?></td>
                </tr>
                <tr>
                    <td class="td_det_med"><?php echo $det['procedencia'] ?></td>
                    <td class="td_det_med" colspan="2"><?php echo $det['destino'] ?></td>
                </tr>

            </table><!-- /table class="quali_det_foto" -->

            <?php $contv = $queryvis->num_rows; ?>
            <div class="num_rows_rol">
                <br />
                <br />
                <b>VISITAS CADASTRADAS</b>
                <br />
                <br />
                <?php if ( !empty( $contv ) ) { ?>Total de <?php echo $contv ?> visita(s) cadastrada(s) <?php } ?>
            </div>
            <hr align="center" width="650" size="1" noshade="noshade" color="#000000" />

            <?php

            if ( $contv < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não há visitas cadastradas.</p>';
            } else {

                $i = 0;
                while ( $d_visit = $queryvis->fetch_assoc() ) {

                    $idvisita = $d_visit['idvisita'];

                    $suspenso      = false;
                    $visit_class   = 'visit_ativa';
                    $sit_v_atual   = 'ATIVA';
                    $susp_data_ini = '';
                    $susp_data_fim = '';
                    $susp_motivo   = '';

                    $visit = manipula_sit_visia_cq( $idvisita );

                    if ( $visit ) {

                        $suspenso      = $visit['suspenso'];
                        $visit_class   = $visit['css_class'];
                        $sit_v_atual   = $visit['sit_v'];
                        $susp_data_ini = $visit['data_ini'];
                        $susp_data_fim = $visit['data_fim'];
                        $susp_motivo   = $visit['motivo'];

                    }

                    $foto_g = $d_visit['foto_visit_g'];
                    $foto_p = $d_visit['foto_visit_p'];

                    $foto_visit = ck_pic( $foto_g, $foto_p, false, 2 );

                ?>
            <?php if ( ( $i == 3 ) || ( ( $i - 3 ) % 5 == 0 ) ) { ?>
            <p align="right" class="par_min"><?php echo SICOP_DET_DESC_FU; ?>: <?php echo $d_det['nome_det'];?>; Matrícula: <?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] ) ?> - Usuário: <?php echo $iduser ?>; Computador: <?php echo $maquina; ?>; em <?php echo date( 'd/m/Y \à\s H:i' )?></p>
            <div class="quebra_pag">&nbsp;</div>
            <?php } else { ?>
                <?php if ( $i > 0 ){ ?>
                <br />
                <br />
                <?php } ?>
            <?php } ?>

            <table class="quali_visita">
                <tr >
                    <td class="td_visit_med"><span class="destaque_leg">Visitante:</span> <?php echo $d_visit['nome_visit'] ?></td>
                    <td class="td_visit_min"><span class="destaque_leg">RG:</span> <?php echo $d_visit['rg_visit'] ?></td>
                    <td class="td_visit_foto" rowspan="6"><img src="<?php echo $foto_visit ?>" alt="" width="100" height="134" /></td>
                </tr>
                <tr>
                    <td class="td_visit_med"><span class="destaque_leg">Data de Nascimento:</span> <?php echo empty( $d_visit['nasc_visit_f'] ) ? '' : $d_visit['nasc_visit_f'] . ' - ' . $d_visit['idade_visit'] . ' anos'; // echo pegaIdade($d_visit['data_nasc'])    ?></td>
                    <td class="td_visit_min"><span class="destaque_leg">Sexo:</span> <?php echo $d_visit['sexo_visit'] ?></td>
                </tr>
                <tr>
                    <td class="td_visit_med"><span class="destaque_leg">Endereço:</span> <?php echo $d_visit['resid_visit'] ?></td>
                    <td class="td_visit_min"><span class="destaque_leg">Telefone:</span> <?php echo preg_replace( '/([0-9]{2})([0-9]{4})([0-9]{4})/', '(\\1) \\2-\\3', $d_visit['telefone_visit'] ) ?></td>
                </tr>
                <tr>
                    <td class="td_visit_med"><span class="destaque_leg">Naturalidade:</span> <?php echo $d_visit['cidade_visit'] ?> - <?php echo $d_visit['estado_visit'] ?></td>
                    <td class="td_visit_min"><span class="destaque_leg">Parentesco:</span> <?php echo $d_visit['parentesco']; ?></td>
                </tr>
                <tr>
                    <td class="td_visit_med"><span class="destaque_leg">Pai:</span> <?php echo $d_visit['pai_visit'] ?></td>
                    <td class="td_visit_min"><span class="destaque_leg">ID no sistema:</span> <?php echo $d_visit['idvisita']; ?></td>
                </tr>
                <tr>
                    <td class="td_visit_med"><span class="destaque_leg">Mãe:</span> <?php echo $d_visit['mae_visit'] ?></td>
                    <td class="td_visit_min">&nbsp;</td>
                </tr>
                <tr>
                    <td class="td_visit_med"><span class="destaque_leg">Situação atual do visitante:</span> <font class="par_forte_visit <?php echo $visit_class; ?>" ><?php echo $sit_v_atual; ?></font></td>
                    <td class="td_visit_min" align="center" ><?php if ( $suspenso ) { ?> A partir de <?php echo $susp_data_ini ?> <?php } ?></td>
                    <td class="td_visit_foto" align="center" ><?php if ( !empty( $susp_data_fim ) ) { ?> Até <?php echo $susp_data_fim ?> <?php } ?></td>
                </tr>
                <tr>
                    <td class="td_visit_grd" colspan="3" ><?php if ( $suspenso ) { ?><span class="destaque_leg">Motivo:</span> <?php echo $susp_motivo ?><?php } ?></td>
                </tr>
            </table><!-- /table class="quali_visita" -->

    <?php
              $i++;
                        } // fim do while
            } // fim do if que conta o número de ocorrencias
    ?>

            <p align="right" class="par_min"><?php echo SICOP_DET_DESC_FU; ?>: <?php echo $d_det['nome_det'];?>; Matrícula: <?php if ( !empty( $d_det['matricula'] ) ) echo formata_num( $d_det['matricula'] ) ?> - Usuário: <?php echo $iduser?>; Computador: <?php echo $maquina; ?>; em <?php echo date( 'd/m/Y \à\s H:i' )?></p>
        </div>
    </body>
</html>