<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_print.php';

$pag  = link_pag();
$tipo = '';

$imp_pront = get_session( 'imp_pront', 'int' );
$n_pront_n = 1;

$titulo        = get_session ( 'titulo' );
$secretaria    = get_session ( 'secretaria' );
$coordenadoria = get_session ( 'coordenadoria' );
$unidadecurto  = get_session ( 'unidadecurto' );
$iduser        = get_session ( 'user_id', 'int' );
$ip            = $_SERVER['REMOTE_ADDR'];
$maquina       = substr( $ip, strrpos( $ip, '.' ) + 1 );

$motivo_pag = 'IMPRESSÃO DE GRADE';

if ($imp_pront < $n_pront_n) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    require 'cab_simp.php';
    echo msg_js( 'Você não tem permissões para acessar esta página.', 'f' );

    exit;

}

$iddet = get_get( 'iddet', 'int' );

if (empty($iddet)){

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 'f' );

    exit;

}

$querydet = "SELECT
               `nome_det`,
               `matricula`,
               `rg_civil`,
               `execucao`,
               `nasc_det`,
               DATE_FORMAT(`nasc_det`, '%d/%m/%Y') AS nasc_det_f,
               FLOOR(DateDiff(CurDate(), `nasc_det`) / 365.25) AS idade_det,
               `pai_det`,
               `mae_det`,
               `resid_det`,
               `motivo_prisao`
             FROM
               `detentos`
             WHERE
               `iddetento` = $iddet
             LIMIT 1";

$query_grade = "SELECT
                    `idprocesso`,
                    `cod_detento`,
                    `gra_preso`,
                    `gra_num_in`,
                    `gra_num_exec`,
                    `gra_num_inq`,
                    `gra_f_p`,
                    `gra_num_proc`,
                    `gra_campo_x`,
                    `gra_vara`,
                    `gra_comarca`,
                    `gra_artigos`,
                    `gra_data_delito`,
                    DATE_FORMAT(`gra_data_delito`, '%d/%m/%Y') AS gra_data_delito_f,
                    `gra_data_sent`,
                    DATE_FORMAT(`gra_data_sent`, '%d/%m/%Y') AS gra_data_sent_f,
                    `gra_p_ano`,
                    `gra_p_mes`,
                    `gra_p_dia`,
                    `gra_regime`,
                    `gra_sit_atual`
                FROM
                    `grade`
                WHERE
                    `cod_detento` = $iddet AND `gra_preso` = FALSE
                ORDER BY
                    `gra_preso` DESC, `gra_campo_x` ASC, `gra_num_in` DESC, `gra_data_delito` DESC ";

$query_grade_preso = "SELECT
                        `idprocesso`,
                        `cod_detento`,
                        `gra_preso`,
                        `gra_num_in`,
                        `gra_num_exec`,
                        `gra_num_inq`,
                        `gra_f_p`,
                        `gra_num_proc`,
                        `gra_campo_x`,
                        `gra_vara`,
                        `gra_comarca`,
                        `gra_artigos`,
                        `gra_data_delito`,
                        DATE_FORMAT(`gra_data_delito`, '%d/%m/%Y') AS gra_data_delito_f,
                        `gra_data_sent`,
                        DATE_FORMAT(`gra_data_sent`, '%d/%m/%Y') AS gra_data_sent_f,
                        `gra_p_ano`,
                        `gra_p_mes`,
                        `gra_p_dia`,
                        `gra_regime`,
                        `gra_sit_atual`
                    FROM
                        `grade`
                    WHERE
                        `cod_detento` = $iddet AND `gra_preso` = TRUE
                    ORDER BY
                        `gra_preso` DESC, `gra_num_in` DESC, `gra_data_delito` DESC ";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$querydet = $model->query( $querydet );

// fechando a conexao
$model->closeConnection();

if( !$querydet ) {

    echo msg_js( 'FALHA!!!', 'f' );
    exit;

}

$contd = $querydet->num_rows;

if ( $contd < 1 ) {
    $mensagem = "A consulta retornou 0 ocorrencias ( $motivo_pag ).\n\n Página $pag";
    salvaLog($mensagem);
    echo msg_js( 'FALHA!!!', 'f' );
    exit;
}

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);

$d_det = $querydet->fetch_assoc();

$matricula = !empty( $d_det['matricula'] ) ? formata_num( $d_det['matricula'] ) : '';

$execucao =  !empty( $d_det['execucao'] ) ? number_format( $d_det['execucao'], 0, '', '.' ) : 'N/C';

$rg = !empty( $d_det['rg_civil'] ) ? formata_num( $d_det['rg_civil'] ) : '';



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="shortcut icon" href="<?php echo SICOP_SYS_IMG_PATH; ?>favicon.ico" type="image/x-icon" />
        <title><?php echo $titulo ?></title>
        <link href="<?php echo SICOP_ABS_PATH ?>css/reset.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SICOP_ABS_PATH ?>css/estilo_pp.css" rel="stylesheet" type="text/css" />
    </head>

    <body onload="Javascript:window.print();self.window.close()">
        <!-- onload="Javascript:window.print();self.window.close()" -->

        <?php require 'cabecalho_h.php'; ?>

        <script type="text/javascript" src="<?php echo SICOP_ABS_PATH ?>js/funcoes.js"></script>
        <p align="center" class="paragrafo18Italico">EXTRATO DE SITUAÇÃO PROCESSUAL</p>
        <table width="1000" class="bordasimples_grade" border="1" align="center" cellpadding="1" cellspacing="0">
          <tr>
            <td width="585">NOME: <?php echo $d_det['nome_det'];?></td>
            <td width="405" align="center">MOTIVO DA PRISÃO ATUAL</td>
          </tr>
          <tr>
            <td class="nopading"><div id="matricula">Matrícula: <?php echo $matricula;?></div><div id="rg">RG: <?php echo $rg;?></div><div id="execucao">Execução: <?php echo $execucao;?></div><div id="nacimento">Nascimento: <?php echo (empty($d_det['nasc_det']) ? '' : $d_det['nasc_det_f'] . ' - ' . $d_det['idade_det'].' anos');?></div></td>
            <td rowspan="3" valign="top"><b><?php echo nl2br( $d_det['motivo_prisao'] );?></b></td>
          </tr>
          <tr>
            <td>Filiação: <?php echo $d_det['pai_det'];?> e <?php echo $d_det['mae_det'];?></td>
          </tr>
          <tr>
            <td>Última residência: <?php echo $d_det['resid_det'];?></td>
          </tr>
        </table>

        <br />
        <br />

        <?php

            // instanciando o model
            $model = SicopModel::getInstance();

            // executando a query
            $query_grade       = $model->query( $query_grade );
            $query_grade_preso = $model->query( $query_grade_preso );

            // fechando a conexao
            $model->closeConnection();

            $contgra = 0;
            $contgrapreso = 0;

            if( $query_grade ) $contgra = $query_grade->num_rows;
            if( $query_grade_preso ) $contgrapreso = $query_grade_preso->num_rows;


            if( $contgra < 1 && $contgrapreso < 1 ) { // se o número de ocorrências for menor do que 1, mostra a mensagem
                echo '<p class="p_q_no_result">Não há dados.</p>';
            } else {

        ?>
        <div class="letra9">
          <table width="1000" class="bordasimples_grade" border="1" align="center" cellpadding="1" cellspacing="0">
            <thead>
              <tr align="center" class="" >
                <td width="25">Ex.</td>
                <td width="69">Inq.</td>
                <td width="25">F/P</td>
                <td width="100">Processo</td>
                <td width="17">X</td>
                <td width="75">Vara</td>
                <td width="100">Comarca</td>
                <td width="121">Artigos</td>
                <td width="70">Data delito</td>
                <td width="70">Data sentença</td>
                <td width="25">Ano</td>
                <td width="25">Mes</td>
                <td width="25">Dia</td>
                <td width="45">Regime</td>
                <td width="146">Situação Atual</td>
              </tr>
            </thead>
            <tbody>
              <?php

                $i = 0;
                while ( $d_grade = $query_grade_preso->fetch_assoc() ) {

                    $campox = empty($d_grade['gra_campo_x']) ? '' : '<b>X</b>';

                    $neg_i = '';
                    $neg_f = '';

                    if ($d_grade['gra_preso']){
                        $neg_i = '<b>';
                        $neg_f = '</b>';
                    }

        ?>
              <tr>
                <td align="center"><?php echo $neg_i . $d_grade['gra_num_exec'] . $neg_f; ?></td>
                <td><?php echo $neg_i . $d_grade['gra_num_inq'] . $neg_f; ?></td>
                <td align="center"><?php echo $neg_i . $d_grade['gra_f_p'] . $neg_f; ?></td>
                <td ><?php echo $neg_i . $d_grade['gra_num_proc'] . $neg_f; ?></td>
                <td align="center"><?php echo $campox; ?></td>
                <td align="center"><?php echo $neg_i . $d_grade['gra_vara'] . $neg_f; ?></td>
                <td align="center"><?php echo $neg_i . $d_grade['gra_comarca'] . $neg_f; ?></td>
                <td><?php echo $neg_i . $d_grade['gra_artigos'] . $neg_f; ?></td>
                <td align="center"><?php echo $neg_i . $d_grade['gra_data_delito_f'] . $neg_f; ?></td>
                <td align="center"><?php echo $neg_i . $d_grade['gra_data_sent_f'] . $neg_f; ?></td>
                <td align="center"><?php echo $neg_i . $d_grade['gra_p_ano'] . $neg_f; ?></td>
                <td align="center"><?php echo $neg_i . $d_grade['gra_p_mes'] . $neg_f; ?></td>
                <td align="center"><?php echo $neg_i . $d_grade['gra_p_dia'] . $neg_f; ?></td>
                <td><?php echo $neg_i . $d_grade['gra_regime'] . $neg_f; ?></td>
                <td><?php echo $neg_i . $d_grade['gra_sit_atual'] . $neg_f; ?></td>
              </tr>
              <?php }?>
              <?php if($contgra >= 1 && $contgrapreso >= 1) { ?>
              <tr>
                <td colspan="15">&nbsp;</td>
              </tr>
              <?php }?>
              <?php

                $i = 0;
                while ( $d_grade = $query_grade->fetch_assoc() ) {

                    $campox = empty( $d_grade['gra_campo_x'] ) ? '' : '<b>X</b>';

                    $neg_i = '';
                    $neg_f = '';

                    if ( $d_grade['gra_preso'] ) {
                        $neg_i = '<b>';
                        $neg_f = '</b>';
                    }

        ?>
              <tr>
                <td align="center"><?php echo $neg_i . $d_grade['gra_num_exec'] . $neg_f; ?></td>
                <td><?php echo $neg_i . $d_grade['gra_num_inq'] . $neg_f; ?></td>
                <td align="center"><?php echo $neg_i . $d_grade['gra_f_p'] . $neg_f; ?></td>
                <td ><?php echo $neg_i . $d_grade['gra_num_proc'] . $neg_f; ?></td>
                <td align="center"><?php echo $campox; ?></td>
                <td align="center"><?php echo $neg_i . $d_grade['gra_vara'] . $neg_f; ?></td>
                <td align="center"><?php echo $neg_i . $d_grade['gra_comarca'] . $neg_f; ?></td>
                <td><?php echo $neg_i . $d_grade['gra_artigos'] . $neg_f; ?></td>
                <td align="center"><?php echo $neg_i . $d_grade['gra_data_delito_f'] . $neg_f; ?></td>
                <td align="center"><?php echo $neg_i . $d_grade['gra_data_sent_f'] . $neg_f; ?></td>
                <td align="center"><?php echo $neg_i . $d_grade['gra_p_ano'] . $neg_f; ?></td>
                <td align="center"><?php echo $neg_i . $d_grade['gra_p_mes'] . $neg_f; ?></td>
                <td align="center"><?php echo $neg_i . $d_grade['gra_p_dia'] . $neg_f; ?></td>
                <td><?php echo $neg_i . $d_grade['gra_regime'] . $neg_f; ?></td>
                <td><?php echo $neg_i . $d_grade['gra_sit_atual'] . $neg_f; ?></td>
              </tr>
              <?php }?>
            </tbody>
          </table>
          <?php } ?>
          <p align="right" class="par_min">Usuário: <?php echo $iduser?>; Computador: <?php echo $maquina; ?>; em <?php echo date( 'd/m/Y \à\s H:i' )?></p>
        </div>
    </body>
</html>

